<?php

namespace console\workers;

use common\models\Camera;
use common\models\LightAnswer;
use common\models\LightMessage;
use inpassor\daemon\Worker;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use ErrorException;
use Exception;
use yii\db\Expression;

class MtmAmqpWorker extends Worker
{
    const ROUTE_TO_LSERVER = 'routeLServer';
    const EXCHANGE = 'light';

    public $active = true;
    public $maxProcesses = 1;
    public $delay = 60;
    public $run = true;


    /** @var AMQPStreamConnection */
    private $connection;
    /** @var AMQPChannel $channel */
    private $channel;
    private $nodeRoute;
    private $nodeQueueName;
    private $organizationId;
    private $nodeId;

    public function handler($signo)
    {
        $this->log('call handler... ' . $signo);
        switch ($signo) {
            case SIGTERM:
            case SIGINT:
                $this->run = false;
                break;
        }
    }

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/mtm_amqp_worker.log';
        parent::init();

        $params = Yii::$app->params;
        if (!isset($params['amqpServer']['host']) ||
            !isset($params['amqpServer']['port']) ||
            !isset($params['amqpServer']['user']) ||
            !isset($params['amqpServer']['password']) ||
            !isset($params['node']['oid']) ||
            !isset($params['node']['nid'])) {
            $this->log('Не задана конфигурация сервера сообщений и шкафа.');
            $this->run = false;
            return;
        }

        $this->organizationId = $params['node']['oid'];
        $this->nodeId = $params['node']['nid'];

        $this->nodeRoute = 'routeNode-' . $this->organizationId . '-' . $this->nodeId;
        $this->nodeQueueName = 'queryNode-' . $this->organizationId . '-' . $this->nodeId;

        try {
            $this->connection = new AMQPStreamConnection($params['amqpServer']['host'],
                $params['amqpServer']['port'],
                $params['amqpServer']['user'],
                $params['amqpServer']['password']);

            $this->channel = $this->connection->channel();
            $this->channel->exchange_declare(self::EXCHANGE, 'direct', false, true, false);
            $this->channel->queue_declare($this->nodeQueueName, false, true, false, false);
            $this->channel->queue_bind($this->nodeQueueName, self::EXCHANGE, $this->nodeRoute);
            $this->channel->basic_consume($this->nodeQueueName, '', false, false, false, false, [&$this, 'callback']);
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $this->log('init not complete');
            $this->run = false;
            return;
        }

        pcntl_signal(SIGTERM, [&$this, 'handler']);
        pcntl_signal(SIGINT, [&$this, 'handler']);

        $this->log('init complete');
    }


    /**
     * @throws Exception
     */
    public function run()
    {
        $this->log('run...');
        while ($this->run) {
//            $this->log('tick...');
            // TODO: придумать механизм который позволит выбирать все сообщения в очереди, а не по одному с задержкой в секунду
            try {
                if (count($this->channel->callbacks)) {
//                    $this->log('wait for message...');
                    $this->channel->wait(null, true);
//                    $this->log('end wait...');
                }
            } catch (ErrorException $e) {
                $this->log($e->getMessage());
                return;
            } catch (AMQPTimeoutException $e) {
                $this->log($e->getMessage());
                return;
            } catch (Exception $e) {
                $this->log($e->getMessage());
                return;
            }

            $answers = LightAnswer::find()->where(['is', 'dateOut', new Expression('null')])->all();
//            $this->log('answers to send: ' . count($answers));
            foreach ($answers as $answer) {
                $pkt = ['oid' => $this->organizationId, 'nid' => $this->nodeId, 'type' => 'lightstatus', 'address' => $answer->address, 'data' => $answer->data];
                try {
                    $msq = new AMQPMessage(json_encode($pkt), array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
                    $this->channel->basic_publish($msq, self::EXCHANGE, self::ROUTE_TO_LSERVER);
                } catch (Exception $e) {
                    $this->log($e->getMessage());
                    return;
                }

                $answer->dateOut = date('Y-m-d H:i:s');
                $answer->save();
            }

            pcntl_signal_dispatch();
            sleep(1);
        }

        if ($this->connection != null) {
            $this->channel->close();
            $this->connection->close();
        }

        $this->log('finish...');
    }

    /**
     * @param AMQPMessage $msg
     */
    public function callback($msg)
    {
//        $this->log('get msg');
        $content = json_decode($msg->body);
        if (!isset($content->type)) {
            $this->log('Нет поля type.');
            return;
        }

        switch ($content->type) {
            case 'light' :
                $lm = new LightMessage();
                $lm->address = $content->address;
                $lm->data = $content->data;
                $lm->dateIn = date('Y-m-d H:i:s');
                $lm->changedAt = date('Y-m-d H:i:s');
                if ($lm->save()) {
                    /** @var AMQPChannel $channel */
                    $channel = $msg->delivery_info['channel'];
                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
                } else {
                    $this->log('msg not saved');
                }
                break;
            case 'camera' :
                switch ($content->action) {
                    case 'publish' :
                        $params = Yii::$app->params;
                        if (!isset($params['videoServer']['host']) ||
                            !isset($params['videoServer']['port']) ||
                            !isset($params['videoServer']['app'])) {
                            $this->log('Не указана конфигурация сервера публикации видео.');
                            return;
                        }

                        $vHost = $params['videoServer']['host'];
                        $vPort = $params['videoServer']['port'];
                        $vApp = $params['videoServer']['app'];

                        // Запустить процесс на 3 минуты
                        $camera = Camera::find()->where(['uuid' => $content->uuid])->one();
                        if ($camera == null) {
                            $this->log('Камеру не нашли. uuid: ' . $content->uuid);
                            return;
                        }

                        $cmd = '/usr/bin/avconv -i "' . $camera->address . '" -t 180 -codec copy -an -f flv "rtmp://' .
                            $vHost . ':' . $vPort . '/' . $vApp . '/' . $camera->uuid . '" > /dev/null 2>&1 &';
//                        $this->log('cmd: ' . $cmd);
                        exec($cmd);
                        /** @var AMQPChannel $channel */
                        $channel = $msg->delivery_info['channel'];
                        $channel->basic_ack($msg->delivery_info['delivery_tag']);
                        break;
                    default:
                        $this->log('unknown action');
                        break;
                }
                break;
            default:
                $this->log('unknown type of message: ' . $content->type);
                break;
        }
    }

}
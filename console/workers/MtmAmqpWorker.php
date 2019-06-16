<?php

namespace console\workers;

use common\models\LightAnswer;
use common\models\LightMessage;
use inpassor\daemon\Worker;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;

class MtmAmqpWorker extends Worker
{
    const ROUTE_TO_LIGHT = 'toLight';
    const ROUTE_FROM_LIGHT = 'fromLight';

    public $active = true;
    public $maxProcesses = 1;
    public $delay = 60;
    public $run = true;

    private $connection;
    /** @var AMQPChannel $channel */
    private $channel;
    private $boxName;
    private $toBoxQueueName;

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/mtm_amqp_worker.log';
        parent::init();

        $params = Yii::$app->params;
        if (!isset($params['amqpServer']['host']) ||
            !isset($params['amqpServer']['port']) ||
            !isset($params['amqpServer']['user']) ||
            !isset($params['amqpServer']['password']) ||
            !isset($params['box']['oid']) ||
            !isset($params['box']['bid'])) {
            exit(-1);
        }

        $this->boxName = 'box-' . $params['box']['oid'] . '-' . $params['box']['bid'];
        $this->toBoxQueueName = 'toBox-' . $params['box']['oid'] . '-' . $params['box']['bid'];

        $this->connection = new AMQPStreamConnection($params['amqpServer']['host'],
            $params['amqpServer']['port'],
            $params['amqpServer']['user'],
            $params['amqpServer']['password']);

        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->boxName, 'direct', false, true, false);
        $this->channel->queue_declare($this->toBoxQueueName, false, true, false, false);
        $this->channel->queue_bind($this->toBoxQueueName, $this->boxName, self::ROUTE_TO_LIGHT);
        $this->channel->basic_consume($this->toBoxQueueName, '', false, false, false, false, $this->callback);
    }


    public function run()
    {
        while ($this->run) {
            $answers = LightAnswer::findAll(['dateOut' => 'NOT NULL']);
            foreach ($answers as $answer) {
                $pkt = ['address' => $answer->address, 'data' => $answer->data];
                $msq = new AMQPMessage(json_encode($pkt), array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
                $this->channel->basic_publish($msq, $this->boxName, self::ROUTE_FROM_LIGHT);
                $answer->dateOut = date('Y-m-d H:i:s');
                $answer->save();
            }

            sleep(1);
        }
    }

    /**
     * @param AMQPMessage $msg
     */
    private function callback($msg)
    {
        $lm = new LightMessage();
        $content = json_decode($msg->body);
        $lm->address = $content['address'];
        $lm->data = $content['data'];
        $lm->dateIn = date('Y-m-d H:i:s');
        if ($lm->save()) {
            /** @var AMQPChannel $channel */
            $channel = $msg->delivery_info['channel'];
            $channel->basic_ack($msg->delivery_info['delivery_tag']);
        }
    }

}
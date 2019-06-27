<?php

namespace console\workers;

use common\models\Camera;
use common\models\LastUpdate;
use common\models\LightMessage;
use common\models\Measure;
use common\models\SensorChannel;
use common\models\SoundFile;
use yii\httpclient\Client;
use inpassor\daemon\Worker;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use ErrorException;
use Exception;
use yii\httpclient\CurlTransport;

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

    private $apiServer;
    private $fileServer;

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
            !isset($params['node']['nid']) ||
            !isset($params['apiServer']) ||
            !isset($params['fileServer'])) {
            $this->log('Не задана конфигурация сервера сообщений и шкафа.');
            $this->run = false;
            return;
        }

        $this->apiServer = $params['apiServer'];
        $this->fileServer = $params['fileServer'];

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
        $checkSoundFile = 0;
        $checkChannels = 0;
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

//            $answers = LightAnswer::find()->where(['is', 'dateOut', new Expression('null')])->all();
//            $this->log('answers to send: ' . count($answers));
//            foreach ($answers as $answer) {
//                $pkt = ['oid' => $this->organizationId, 'nid' => $this->nodeId, 'type' => 'lightstatus', 'address' => $answer->address, 'data' => $answer->data];
//                try {
//                    $msq = new AMQPMessage(json_encode($pkt), array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
//                    $this->channel->basic_publish($msq, self::EXCHANGE, self::ROUTE_TO_LSERVER);
//                } catch (Exception $e) {
//                    $this->log($e->getMessage());
//                    return;
//                }
//
//                $answer->dateOut = date('Y-m-d H:i:s');
//                $answer->save();
//                try {
//                    $answer->delete();
//                } catch (Throwable $e) {
//                    $this->log($e->getMessage());
//                    $this->log('Не удалось удалить запись _id=' . $answer->_id);
//                }
//            }

            // проверяем наличие новых или обновлённых звуковых файлов на сервере
            if ($checkSoundFile + 10 < time()) {
                $checkSoundFile = time();
//                $this->log('checkSoundFile');
                // где-то нужно хранить дату последней проверки - нужно ли?
                $currentDate = date('Y-m-d H:i:s');
                $lastUpdateModel = LastUpdate::find()->where(['entityName' => 'sound_file'])->one();
                if ($lastUpdateModel == null) {
                    $lastUpdateModel = new LastUpdate();
                    $lastUpdateModel->entityName = 'sound_file';
                    $lastUpdateModel->date = '0000-00-00 00:00:00';
                }

                $lastDate = $lastUpdateModel->date;
                $httpClient = new Client();
                $q = $this->apiServer . '/sound-file?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
                $response = $httpClient->createRequest()
                    ->setMethod('GET')
                    ->setUrl($q)
                    ->send();
                if ($response->isOk) {
                    $lastUpdateModel->date = $currentDate;
                    if (!$lastUpdateModel->save()) {
                        $this->log('Last update date not saved');
                        foreach ($lastUpdateModel->errors as $error) {
                            $this->log($error);
                        }
                    }

                    foreach ($response->data as $f) {
//                        $this->log($f['soundFile']);
                        $model = SoundFile::findOne($f['_id']);
                        if ($model == null) {
                            $model = new SoundFile();
                        }
//                        $soundFile->load($f, 'forma');
                        $model->scenario = 'update';
                        $model->_id = $f['_id'];
                        $model->uuid = $f['uuid'];
                        $model->title = $f['title'];
                        $model->soundFile = $f['soundFile'];
                        $model->nodeUuid = $f['nodeUuid'];
                        $model->deleted = $f['deleted'];
                        $model->createdAt = $f['createdAt'];
                        $model->changedAt = $f['changedAt'];
                        if ($model->save()) {
                            $this->log('sound file model saved: uuid' . $model->uuid);
                            $file = Yii::getAlias('@backend/web/' . $model->getSoundFile());
                            $this->log($file);
                            $fh = fopen($file, 'w');
                            $fileClient = new Client(['transport' => CurlTransport::class]);
                            $url = $this->fileServer . '/files/sound/' . $this->organizationId . '/' . $this->nodeId . '/' . $model->soundFile;
//                            $this->log($url);
                            $response = $fileClient->createRequest()
                                ->setMethod('GET')
                                ->setUrl($url)
                                ->setOutputFile($fh)
                                ->send();
                            fclose($fh);
//                            $this->log('response: ' . $response);
                            unset($response);
                        } else {
                            $this->log('sound file model not saved: uuid' . $model->uuid);
                            foreach ($model->errors as $error) {
                                $this->log($error);
                            }
                        }
                    }
                }
            }

            // проверяем наличие новых данных по сенсорам и измерениям
            if ($checkChannels + 10 < time()) {
                $checkChannels = time();
//                $this->log('checkChannels');
                $currentDate = date('Y-m-d H:i:s');
                $lastUpdateModel = LastUpdate::find()->where(['entityName' => 'channel'])->one();
                if ($lastUpdateModel == null) {
                    $lastUpdateModel = new LastUpdate();
                    $lastUpdateModel->entityName = 'channel';
                    $lastUpdateModel->date = '0000-00-00 00:00:00';
                }

                $lastDate = $lastUpdateModel->date;
                $items = SensorChannel::find()->where(['>=', 'changedAt', $lastDate])->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));

                $httpClient = new Client();
                $q = $this->apiServer . '/sensor-channel/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
                $response = $httpClient->createRequest()
                    ->setMethod('POST')
                    ->setUrl($q)
                    ->setData([
                        'oid' => $this->organizationId,
                        'nid' => $this->nodeId,
                        'items' => $items,
                    ])
                    ->send();
                if ($response->isOk) {
                    $lastUpdateModel->date = $currentDate;
                    if (!$lastUpdateModel->save()) {
                        $this->log('Last update date not saved');
                        foreach ($lastUpdateModel->errors as $error) {
                            $this->log($error);
                        }
                    }
                }

//                $this->log('checkMeasure');
                $currentDate = date('Y-m-d H:i:s');
                $lastUpdateModel = LastUpdate::find()->where(['entityName' => 'measure'])->one();
                if ($lastUpdateModel == null) {
                    $lastUpdateModel = new LastUpdate();
                    $lastUpdateModel->entityName = 'measure';
                    $lastUpdateModel->date = '0000-00-00 00:00:00';
                }

                $lastDate = $lastUpdateModel->date;
                $items = Measure::find()->where(['>=', 'changedAt', $lastDate])->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));

                $httpClient = new Client();
                $q = $this->apiServer . '/measure/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
                $response = $httpClient->createRequest()
                    ->setMethod('POST')
                    ->setUrl($q)
                    ->setData([
                        'oid' => $this->organizationId,
                        'nid' => $this->nodeId,
                        'items' => $items,
                    ])
                    ->send();
                if ($response->isOk) {
                    $lastUpdateModel->date = $currentDate;
                    if (!$lastUpdateModel->save()) {
                        $this->log('Last update date not saved');
                        foreach ($lastUpdateModel->errors as $error) {
                            $this->log($error);
                        }
                    }
                }
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
                            !isset($params['videoServer']['app']) ||
                            !isset($params['videoServer']['publishTime'])) {
                            $this->log('Не указана конфигурация сервера публикации видео.');
                            return;
                        }

                        $vHost = $params['videoServer']['host'];
                        $vPort = $params['videoServer']['port'];
                        $vApp = $params['videoServer']['app'];
                        $publishTime = $params['videoServer']['publishTime'];

                        $camera = Camera::find()->where(['uuid' => $content->uuid])->one();
                        if ($camera == null) {
                            $this->log('Камеру не нашли. uuid: ' . $content->uuid);
                            return;
                        }

                        // проверяем на уже запущенный процес публикации
                        $cmd = 'ps aux | grep avconv | grep ' . $camera->uuid;
                        exec($cmd, $output);

                        if (count($output) <= 1) {
                            $cmd = 'touch /tmp/' . $vApp . '/' . $camera->uuid . '.m3u8';
                            exec($cmd);
                            $cmd = '/usr/bin/avconv -i "' . $camera->address . '" -t ' . $publishTime . ' -codec copy -an -f flv "rtmp://' .
                                $vHost . ':' . $vPort . '/' . $vApp . '/' . $camera->uuid . '" > /dev/null 2>&1 &';
                            exec($cmd);
//                        $this->log('cmd: ' . $cmd);
                        }

                        /** @var AMQPChannel $channel */
                        $channel = $msg->delivery_info['channel'];
                        $channel->basic_ack($msg->delivery_info['delivery_tag']);
                        break;
                    default:
                        $this->log('unknown action');
                        break;
                }
                break;
            case 'sound' :
                switch ($content->action) {
                    case 'play' :
                        $sound = SoundFile::find()->where(['uuid' => $content->uuid])->one();
                        if ($sound == null) {
                            $this->log('Звуковой файл не нашли. uuid: ' . $sound->uuid);
                            return;
                        }

                        // проверяем на уже запущенный процес воспроизведения файла
                        $cmd = 'ps aux | grep mpg123 | grep ' . $sound->uuid;
                        exec($cmd, $output);

                        if (count($output) <= 1) {
                            $cmd = '/usr/bin/mpg123 ' . Yii::getAlias('@backend/web/' . $sound->getSoundFile()) . ' > /dev/null 2>&1 &';
                            exec($cmd);
                            $this->log('cmd: ' . $cmd);
                        }

                        /** @var AMQPChannel $channel */
                        $channel = $msg->delivery_info['channel'];
                        $channel->basic_ack($msg->delivery_info['delivery_tag']);
                        break;
                    default:
                        break;
                }
                break;
            default:
                $this->log('unknown type of message: ' . $content->type);
                break;
        }
    }

}
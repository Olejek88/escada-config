<?php

namespace console\workers;

use backend\controllers\SshController;
use common\components\MtmActiveRecord;
use common\models\Camera;
use common\models\Device;
use common\models\DeviceConfig;
use common\models\DeviceProgram;
use common\models\DeviceRegister;
use common\models\DeviceStatus;
use common\models\DeviceType;
use common\models\Group;
use common\models\GroupControl;
use common\models\LastUpdate;
use common\models\LightMessage;
use common\models\Measure;
use common\models\Node;
use common\models\NodeControl;
use common\models\SensorChannel;
use common\models\SensorConfig;
use common\models\SoundFile;
use common\models\Threads;
use yii\httpclient\Client;
use inpassor\daemon\Worker;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use ErrorException;
use Exception;
use yii\httpclient\StreamTransport;
use yii\base\InvalidConfigException;

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
    private $nodeUuid;

    private $apiServer;
    private $fileServer;

    private $needReconnect;

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
            $this->setupChannel();
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $this->log('init not complete');
            $this->run = false;
            return;
        }

        pcntl_signal(SIGTERM, [&$this, 'handler']);
        pcntl_signal(SIGINT, [&$this, 'handler']);

        $this->needReconnect = false;

        $this->log('init complete');
    }

    private function setupChannel()
    {
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare(self::EXCHANGE, 'direct', false, true, false);
        $this->channel->queue_declare($this->nodeQueueName, false, true, false, false);
        $this->channel->queue_bind($this->nodeQueueName, self::EXCHANGE, $this->nodeRoute);
        $this->channel->basic_consume($this->nodeQueueName, '', false, false, false, false, [&$this, 'callback']);
    }


    /**
     * @throws Exception
     */
    public function run()
    {
        $checkSoundFile = 0;
        $checkChannels = 0;
        $checkData = 0;
        $checkLocalIp = 0;
        $checkLocalIpRate = 60;
        $checkReconnectAmqp = 0;
        $checkReconnectAmqpRate = 300;

        // проверяем наличие информации о шкафе
        $node = Node::find()->where(['oid' => $this->organizationId, '_id' => $this->nodeId])->limit(1)->one();
        if ($node != null) {
            $this->nodeUuid = $node->uuid;
        } else {
            // пробуем получить информацию с сервера
            $node = $this->downloadNode();
            if ($node != null) {
                $this->nodeUuid = $node->uuid;
            } else {
                $this->run = false;
                return;
            }
        }

        $this->log('run...');
        while ($this->run) {

            if ($this->needReconnect || $checkReconnectAmqp + $checkReconnectAmqpRate < time()) {
                $checkReconnectAmqp = time();
                $this->log('Amqp connection lost... try reconnect...');
                try {
                    $this->connection->reconnect();
                    $this->log('reconnect successful');
                    $this->setupChannel();
                    $this->needReconnect = false;
                } catch (Exception $e) {
                    $this->log('Exception (reconnect): ' . $e->getMessage());
                }
            }

//            $this->log('tick...');
            // TODO: придумать механизм который позволит выбирать все сообщения в очереди, а не по одному с задержкой в секунду
            try {
                $count = count($this->channel->callbacks);
//                $this->log('callbacks count = ' . $count);
                if ($count > 0) {
//                    $this->log('wait for message...');
                    $this->channel->wait(null, true);
//                    $this->log('end wait...');
                }
            } catch (ErrorException $e) {
                $this->log('ErrorException: ' . $e->getMessage());
                $this->needReconnect = true;
//                return;
            } catch (AMQPTimeoutException $e) {
                $this->log('AMQPTimeoutException: ' . $e->getMessage());
                $this->needReconnect = true;
//                return;
            } catch (Exception $e) {
                $this->log('Exception: ' . $e->getMessage());
                $this->needReconnect = true;
//                return;
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

            if ($checkLocalIp + $checkLocalIpRate < time()) {
                $checkLocalIp = time();
                // получаем интерфейс через который идёт маршрут по умолчанию
                $command = "/sbin/route -n | grep 0.0.0.0 | head -n 1 | awk '{ print $8}'";
                $netDevice = exec($command);
                // получаем ip адрес найденного интерфейса
                $command = "/sbin/ifconfig $netDevice | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
                $localIP = exec($command);
                // отправляем на сервер свой локальный адрес
                $httpClient = new Client();
                $q = $this->apiServer . '/node/address?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
                $response = $httpClient->createRequest()
                    ->setMethod('POST')
                    ->setUrl($q)
                    ->setData([
                        'oid' => $this->organizationId,
                        'nid' => $this->nodeId,
                        'addr' => $localIP,
                    ])
                    ->send();
                if (!$response->isOk) {
                    // TODO: уведомление о том что не удалось отправить адрес
                }
            }

            // проверяем наличие новых или обновлённых звуковых файлов на сервере
            if ($checkSoundFile + 10 < time()) {
                $checkSoundFile = time();
//                $this->log('checkSoundFile');
                $this->downloadSoundFiles();
            }

            // проверяем наличие новых данных по сенсорам и измерениям которые нужно отправить на сервер
            if ($checkChannels + 10 < time()) {
                $checkChannels = time();
//                $this->log('checkChannels');
                $this->uploadSensorChannel();

//                $this->log('checkSensorConfigs');
                $this->uploadSensorConfig();

//                $this->log('checkThreads');
                $this->uploadThread();

//                $this->log('checkMeasure');
                $this->uploadMeasure();

//                $this->log('checkDeviceRegister');
                $this->uploadDeviceRegister();

//                $this->log('checkCamera');
                $this->uploadCamera();

//                $this->log('checkDevice');
                $this->uploadDevice();

//                $this->log('checkDeviceConfig');
                $this->uploadDeviceConfig();
            }

            // проверяем наличие новых данных по оборудованию, камерам на сервере
            if ($checkData + 10 < time()) {
                $checkData = time();

//                $this->log('checkDeviceStatuses');
                $this->downloadDeviceStatus();

//                $this->log('checkDeviceTypes');
                $this->downloadDeviceType();

//                $this->log('checkDevices');
                $this->downloadDevice();

//                $this->log('checkDevicesConfig');
                $this->downloadDeviceConfig();

//                $this->log('checkDevicesProgram');
                $this->downloadDeviceProgram();

//                $this->log('checkCameras');
                $this->downloadCamera();

//                $this->log('checkSensorChannels');
                $this->downloadSensorChannel();

//                $this->log('checkSensorConfigs');
                $this->downloadSensorConfig();

//                $this->log('checkThreads');
                $this->downloadThread();

//                $this->log('checkNode');
                $this->downloadNode();

//                $this->log('checkNodeControl');
                $this->downloadNodeControl();

//                $this->log('checkGroup');
                $this->downloadGroup();

//                $this->log('checkGroupControl');
                $this->downloadGroupControl();
            }


            pcntl_signal_dispatch();
            sleep(1);
        }

        if ($this->connection != null) {
            try {
                $this->channel->close();
                $this->connection->close();
            } catch (Exception $e) {
                $this->log('Stop worker: ' . $e->getMessage());
            }
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

                        $camera = Camera::find()->where(['uuid' => $content->uuid])->limit(1)->one();
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
                        $sound = SoundFile::find()->where(['uuid' => $content->uuid])->limit(1)->one();
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
            case 'ssh' :
                switch ($content->action) {
                    case 'start' :
                        $cmd = SshController::getSshpassCmd($content->password, $content->localPort, $content->bindIp,
                            $content->remotePort, $content->user, $content->remoteHost);
                        $this->log('cmd: ' . $cmd);
                        exec($cmd);

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


    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadSoundFiles()
    {
        $lastUpdateKey = 'sound_file';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
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
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                        $this->log($f['soundFile']);
                $model = SoundFile::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new SoundFile();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
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
                    $uploadPath = Yii::getAlias('@backend/web/' . $model->getUploadPath());
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $fh = fopen($file, 'w');
                    $fileClient = new Client(['transport' => StreamTransport::class]);
                    $url = $this->fileServer . '/files/sound/' . $this->organizationId . '/' . $this->nodeId . '/' . $model->soundFile;
//                            $this->log($url);
                    $response = $fileClient->createRequest()
                        ->setMethod('GET')
                        ->setUrl($url)
//                                ->setOutputFile($fh)
                        ->send();
                    fwrite($fh, $response);
                    fclose($fh);
//                            $this->log('response: ' . $response);
//                            unset($response);
                } else {
                    $allSave = false;
                    $this->log('sound file model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function uploadSensorChannel()
    {
        $lastUpdateKey = 'channel_upload';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = SensorChannel::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/sensor-channel/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     */
    private function uploadMeasure()
    {
        $lastUpdateKey = 'measure';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = Measure::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего измерения в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/measure/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDevice()
    {
        $lastUpdateKey = 'device_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/device?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = Device::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new Device();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->_id = $f['_id'];
                $model->uuid = $f['uuid'];
                $model->nodeUuid = $f['nodeUuid'];
                $model->address = $f['address'];
                $model->deviceTypeUuid = $f['deviceTypeUuid']; // нужно засасывать? нужно. реализовать
                $model->deviceStatusUuid = $f['deviceStatusUuid'];
                $model->serial = $f['serial'];
                $model->interface = $f['interface'];
                $model->name = $f['name'];
                $model->port = $f['port'];
                $model->object = $f['objectUuid'];
                $model->number = 0;
                $model->linkTimeout = $f['linkTimeout'];;
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('device model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceConfig()
    {
        $lastUpdateKey = 'device_config_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/device-config?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = DeviceConfig::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new DeviceConfig();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->deviceUuid = $f['deviceUuid'];
                $model->parameter = $f['parameter'];
                $model->value = $f['value'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('device model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceProgram()
    {
        $lastUpdateKey = 'device_program_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/device-program?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = DeviceProgram::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new DeviceProgram();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->_id = $f['_id'];
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->period_title1 = $f['period_title1'];
                $model->value1 = $f['value1'];
                $model->period_title2 = $f['period_title2'];
                $model->time2 = $f['time2'];
                $model->value2 = $f['value2'];
                $model->period_title3 = $f['period_title3'];
                $model->time3 = $f['time3'];
                $model->value3 = $f['value3'];
                $model->period_title4 = $f['period_title4'];
                $model->time4 = $f['time4'];
                $model->value4 = $f['value4'];
                $model->period_title5 = $f['period_title5'];
                $model->value5 = $f['value5'];
                $model->changedAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('device_program model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadCamera()
    {
        $lastUpdateKey = 'camera_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/camera?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = Camera::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new Camera();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->_id = $f['_id'];
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->deviceStatusUuid = $f['deviceStatusUuid'];
                $model->nodeUuid = $f['nodeUuid'];
                $model->address = $f['address'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('camera model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadSensorChannel()
    {
        $lastUpdateKey = 'sensor_channel_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/sensor-channel?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
//        $q .= '&XDEBUG_SESSION_START=xdebug';
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            $lastUpdateModel->date = $currentDate;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = SensorChannel::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new SensorChannel();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->register = $f['register'];
                $model->deviceUuid = $f['deviceUuid'];
                $model->measureTypeUuid = $f['measureTypeUuid'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('sensor channel model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadSensorConfig()
    {
        $lastUpdateKey = 'sensor_config_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/sensor-config?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = SensorConfig::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new SensorConfig();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->config = $f['config'];
                $model->sensorChannelUuid = $f['sensorChannelUuid'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('sensor config model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadThread()
    {
        $lastUpdateKey = 'thread_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/thread?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = Threads::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new Threads();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->deviceUuid = $f['deviceUuid'];
                $model->port = $f['port'];
                $model->speed = $f['speed'];
                $model->title = $f['title'];
                $model->status = $f['status'];
                $model->work = $f['work'];
                $model->deviceTypeUuid = $f['deviceTypeUuid'];
                $model->c_time = $f['c_time'];
                $model->message = $f['message'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('thread model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceType()
    {
        $lastUpdateKey = 'deviceType';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/device-type?changedAfter=' . $lastDate;
//        $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            $lastUpdateModel->date = $currentDate;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = DeviceType::findOne($f['_id']);
                if ($model == null) {
                    $model = new DeviceType();
                }

//                $model->scenario = 'update';
                $model->_id = $f['_id'];
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];
                if (!$model->save()) {
                    $allSave = false;
                    $this->log('deviceType model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceStatus()
    {
        $lastUpdateKey = 'deviceStatus';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/device-status?changedAfter=' . $lastDate;
//        $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
//                $this->log($f['device']);
                $model = DeviceStatus::findOne($f['_id']);
                if ($model == null) {
                    $model = new DeviceStatus();
                }

//                $model->scenario = 'update';
                $model->_id = $f['_id'];
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];
                if (!$model->save()) {
                    $allSave = false;
                    $this->log('deviceStatus model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadNode()
    {
        $httpClient = new Client();
        $q = $this->apiServer . '/node?oid=' . $this->organizationId . '&nid=' . $this->nodeId;
//        $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk) {
            $f = $response->data;
            $model = Node::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
            if ($model == null) {
                $model = new Node();
            }

            $model->load($f, '');
            $model->_id = $f['_id'];
            if (!$model->save()) {
                $this->log('node model not saved: uuid' . $model->uuid);
                foreach ($model->errors as $error) {
                    $this->log($error);
                }
                return null;
            } else {
                return $model;
            }
        } else {
            return null;
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function uploadDeviceRegister()
    {
        $lastUpdateKey = 'device_register';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = DeviceRegister::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/device-register/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     */
    private function uploadSensorConfig()
    {
        $lastUpdateKey = 'sensor_config_upload';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = SensorConfig::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/sensor-config/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     */
    private function uploadThread()
    {
        $lastUpdateKey = 'thread_upload';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = Threads::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        $node = Node::find()->where(['oid' => $this->organizationId, '_id' => $this->nodeId])->limit(1)->one();

        foreach ($items as $key => $item) {
            $items[$key]['nodeUuid'] = $node->uuid;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/thread/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     */
    private function uploadCamera()
    {
        $lastUpdateKey = 'camera_upload';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = Camera::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/camera/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     */
    private function uploadDevice()
    {
        $lastUpdateKey = 'device_upload';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = Device::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/device/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     */
    private function uploadDeviceConfig()
    {
        $lastUpdateKey = 'device_config_upload';
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = DeviceConfig::find()->where(['>=', 'changedAt', $lastDate])->orderBy('_id')
            ->limit(500)->asArray()->all();
//                $this->log('date: ' . $lastDate);
//                $this->log('items: ' . count($items));
//                $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < 500) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $httpClient = new Client();
        $q = $this->apiServer . '/device-config/send?XDEBUG_SESSION_START=xdebug';
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
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

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadNodeControl()
    {
        $lastUpdateKey = 'node_control_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/node-control?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
                $model = NodeControl::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new NodeControl();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->nodeUuid = $f['nodeUuid'];
                $model->date = $f['date'];
                $model->type = $f['type'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('group control model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadGroup()
    {
        $lastUpdateKey = 'group_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/group?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
                $model = Group::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new Group();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->title = $f['title'];
                $model->groupId = $f['groupId'];
                $model->deviceProgramUuid = $f['deviceProgramUuid'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('group model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadGroupControl()
    {
        $lastUpdateKey = 'group_control_download';
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $q = $this->apiServer . '/group-control?oid=' . $this->organizationId . '&nid=' . $this->nodeId . '&changedAfter=' . $lastDate;
//                $this->log($q);
        $response = $httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($q)
            ->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
                $model = GroupControl::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new groupControl();
                }

                $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                $model->uuid = $f['uuid'];
                $model->groupUuid = $f['groupUuid'];
                $model->date = $f['date'];
                $model->type = $f['type'];
                $model->deviceProgramUuid = $f['deviceProgramUuid'];
                $model->createdAt = $f['createdAt'];
                $model->changedAt = $f['changedAt'];

                if (!$model->save()) {
                    $allSave = false;
                    $this->log('sensor config model not saved: uuid' . $model->uuid);
                    foreach ($model->errors as $error) {
                        $this->log($error);
                    }
                }
            }

            if ($allSave) {
                $lastUpdateModel->date = $currentDate;
                if (!$lastUpdateModel->save()) {
                    $this->log('Last update date not saved');
                    foreach ($lastUpdateModel->errors as $error) {
                        $this->log($error);
                    }
                }
            }
        }
    }
}

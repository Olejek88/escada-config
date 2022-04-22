<?php

namespace console\workers;

use backend\controllers\SshController;
use common\components\MtmActiveRecord;
use common\models\AvailableDevice;
use common\models\Camera;
use common\models\Device;
use common\models\DeviceConfig;
use common\models\DeviceProgram;
use common\models\DeviceRegister;
use common\models\DeviceStatus;
use common\models\DeviceType;
use common\models\EntityParameter;
use common\models\Group;
use common\models\GroupControl;
use common\models\LastUpdate;
use common\models\LightMessage;
use common\models\LostLight;
use common\models\Measure;
use common\models\Node;
use common\models\NodeControl;
use common\models\SensorChannel;
use common\models\SensorConfig;
use common\models\SoundFile;
use common\models\Threads;
use yii\db\ActiveQuery;
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
        if ($this->logFile == null) {
            $this->logFile = '@runtime/daemon/logs/mtm_amqp_worker.log';
        }

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
        /** @var Node $node */
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

//                $this->log('checkEntityParameter');
                $this->uploadEntityParameter();

//                $this->log('checkLostLight');
                $this->uploadLostLight();

//                $this->log('checkAvailableDevice');
                $this->uploadAvailableDevice();
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

//                $this->log('checkEntityParameter');
                $this->downloadEntityParameter();
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

                        /** @var Camera $camera */
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
                        /** @var SoundFile $sound */
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
//        $this->downloadEntity($lastUpdateKey, new SoundFile(), [
//            $this->apiServer . '/sound-file',
//            'oid' => $this->organizationId,
//            'nid' => $this->nodeId,
//        ]);

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
                    // TODO: !!! Решить вопрос с сохранинием файлов. Например загрузить сами бинари, отдельно
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
     * @throws \yii\httpclient\Exception
     */
    private function uploadSensorChannel()
    {
        $lastUpdateKey = 'channel_upload';
        $this->uploadEntity($lastUpdateKey, SensorChannel::find(), '/sensor-channel/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadMeasure()
    {
        $lastUpdateKey = 'measure';
        $this->uploadEntity($lastUpdateKey, Measure::find(), '/measure/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDevice()
    {
        $lastUpdateKey = 'device_download';
        $this->downloadEntity($lastUpdateKey, new Device(), [
            $this->apiServer . '/device',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceConfig()
    {
        $lastUpdateKey = 'device_config_download';
        $this->downloadEntity($lastUpdateKey, new DeviceConfig(), [
            $this->apiServer . '/device-config',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceProgram()
    {
        $lastUpdateKey = 'device_program_download';
        $this->downloadEntity($lastUpdateKey, new DeviceProgram(), [
            $this->apiServer . '/device-program',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadCamera()
    {
        $lastUpdateKey = 'camera_download';
        $this->downloadEntity($lastUpdateKey, new Camera(), [
            $this->apiServer . '/camera',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadSensorChannel()
    {
        $lastUpdateKey = 'sensor_channel_download';
        $this->downloadEntity($lastUpdateKey, new SensorChannel(), [
            $this->apiServer . '/sensor-channel',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadSensorConfig()
    {
        $lastUpdateKey = 'sensor_config_download';
        $this->downloadEntity($lastUpdateKey, new SensorConfig(), [
            $this->apiServer . '/sensor-config',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadThread()
    {
        $lastUpdateKey = 'thread_download';
        $this->downloadEntity($lastUpdateKey, new Threads(), [
            $this->apiServer . '/thread',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceType()
    {
        $lastUpdateKey = 'deviceType';
        $this->downloadEntity($lastUpdateKey, new DeviceType(), [
            $this->apiServer . '/device-type',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadDeviceStatus()
    {
        $lastUpdateKey = 'deviceStatus';
        $this->downloadEntity($lastUpdateKey, new DeviceStatus(), [
            $this->apiServer . '/device-status',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
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
     * @throws \yii\httpclient\Exception
     */
    private function uploadDeviceRegister()
    {
        $lastUpdateKey = 'device_register';
        $this->uploadEntity($lastUpdateKey, DeviceRegister::find(), '/device-register/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadSensorConfig()
    {
        $lastUpdateKey = 'sensor_config_upload';
        $this->uploadEntity($lastUpdateKey, SensorConfig::find(), '/sensor-config/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadThread()
    {
        $lastUpdateKey = 'thread_upload';
        $this->uploadEntity($lastUpdateKey, Threads::find(), '/thread/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadCamera()
    {
        $lastUpdateKey = 'camera_upload';
        $this->uploadEntity($lastUpdateKey, Camera::find(), '/camera/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadDevice()
    {
        $lastUpdateKey = 'device_upload';
        $this->uploadEntity($lastUpdateKey, Device::find(), '/device/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadDeviceConfig()
    {
        $lastUpdateKey = 'device_config_upload';
        $this->uploadEntity($lastUpdateKey, DeviceConfig::find(), '/device-config/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadEntityParameter()
    {
        $lastUpdateKey = 'entity_parameter_upload';
        $this->uploadEntity($lastUpdateKey, EntityParameter::find(), '/entity-parameter/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadLostLight()
    {
        $lastUpdateKey = 'lost_light_upload';
        $this->uploadEntity($lastUpdateKey, LostLight::find(), '/lost-light/send');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadAvailableDevice()
    {
        $lastUpdateKey = 'available_device_upload';
        $this->uploadEntity($lastUpdateKey, AvailableDevice::find(), '/device/available');
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadNodeControl()
    {
        $lastUpdateKey = 'node_control_download';
        $this->downloadEntity($lastUpdateKey, new NodeControl(), [
            $this->apiServer . '/node-control',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadGroup()
    {
        $lastUpdateKey = 'group_download';
        $this->downloadEntity($lastUpdateKey, new Group(), [
            $this->apiServer . '/group',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadGroupControl()
    {
        $lastUpdateKey = 'group_control_download';
        $this->downloadEntity($lastUpdateKey, new GroupControl(), [
            $this->apiServer . '/group-control',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadEntityParameter()
    {
        $lastUpdateKey = 'entity_parameter_download';
        $this->downloadEntity($lastUpdateKey, new EntityParameter(), [
            $this->apiServer . '/entity-parameter',
            'oid' => $this->organizationId,
            'nid' => $this->nodeId,
        ]);
    }

    /**
     * @param $lastUpdateKey string
     * @param $modelClass string
     * @param $fromUrl array
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function downloadEntity($lastUpdateKey, $modelClass, $fromUrl)
    {
        $currentDate = date('Y-m-d H:i:s');
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $httpClient = new Client();
        $fromUrl['changedAfter'] = $lastDate;
        if (YII_DEBUG) {
            $fromUrl['XDEBUG_SESSION_START'] = 'xdebug';
        }

        $request = $httpClient->createRequest();
        $request->setMethod('GET')
            ->setUrl($fromUrl);
        $response = $request->send();
        if ($response->isOk && count($response->data) > 0) {
            $allSave = true;
            foreach ($response->data as $f) {
                /** @var MtmActiveRecord $class */
                $class = new $modelClass();
                $model = $class::find()->where(['uuid' => $f['uuid']])->limit(1)->one();
                if ($model == null) {
                    $model = new $modelClass();
                }

                if ($model instanceof MtmActiveRecord) {
                    $model->scenario = MtmActiveRecord::SCENARIO_CUSTOM_UPDATE;
                }

                $model->load($f, '');
                $model->_id = $f['_id'];
                if (!$model->save()) {
                    $allSave = false;
                    $this->log($class::tableName() . ' model not saved: uuid' . $model->uuid);
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
     * @param $lastUpdateKey string
     * @param $query ActiveQuery
     * @param $toUrl string
     * @param int $count
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function uploadEntity($lastUpdateKey, $query, $toUrl, $count = 500)
    {
        $lastUpdateModel = LastUpdate::find()->where(['entityName' => $lastUpdateKey])->limit(1)->one();
        if ($lastUpdateModel == null) {
            $lastUpdateModel = new LastUpdate();
            $lastUpdateModel->entityName = $lastUpdateKey;
            $lastUpdateModel->date = '0000-00-00 00:00:00';
        }

        $lastDate = $lastUpdateModel->date;
        $items = $query->where(['>=', 'changedAt', $lastDate])->orderBy('_id')->limit($count)->asArray()->all();
//        $this->log('date: ' . $lastDate);
//        $this->log('items: ' . count($items));
//        $this->log('items: ' . print_r($items, true));
        if (count($items) == 0) {
            return;
        }

        if ($query->modelClass == Threads::class) {
            /** @var Node $node */
            $node = Node::find()->where(['oid' => $this->organizationId, '_id' => $this->nodeId])->limit(1)->one();
            foreach ($items as $key => $item) {
                $items[$key]['nodeUuid'] = $node->uuid;
            }
        }


        // фиксируем дату последнего элемента в текущей выборке
        $lastItem = $items[count($items) - 1];
        if (count($items) < $count) {
            $currentDate = date('Y-m-d H:i:s', strtotime($lastItem['changedAt']) + 1);
        } else {
            $currentDate = $lastItem['changedAt'];
        }

        $urlArray = [$this->apiServer . $toUrl];
        if (YII_DEBUG) {
            $urlArray['XDEBUG_SESSION_START'] = 'xdebug';
        }

        $httpClient = new Client();
        $request = $httpClient->createRequest();
        $request->setMethod('POST')
            ->setUrl($urlArray)
            ->setData([
                'oid' => $this->organizationId,
                'nid' => $this->nodeId,
                'items' => json_encode($items),
            ]);
//        $this->log('url: ' . $request->getFullUrl());
        $response = $request->send();
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
}

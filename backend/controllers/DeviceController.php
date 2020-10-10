<?php

namespace backend\controllers;

use backend\models\DeviceSearch;
use common\components\MainFunctions;
use common\models\Device;
use common\models\DeviceConfig;
use common\models\DeviceRegister;
use common\models\DeviceStatus;
use common\models\DeviceType;
use common\models\Info;
use common\models\Measure;
use common\models\MeasureType;
use common\models\mtm\MtmContactor;
use common\models\mtm\MtmDevLightConfig;
use common\models\mtm\MtmDevLightConfigLight;
use common\models\mtm\MtmPktHeader;
use common\models\mtm\MtmResetCoordinator;
use common\models\Node;
use common\models\Organisation;
use common\models\Protocols;
use common\models\SensorChannel;
use common\models\SensorConfig;
use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * DeviceController implements the CRUD actions for Device model.
 */
class DeviceController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {

        if (Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Device models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Device::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($model == null) {
                return json_encode(new HttpException(404, 'Model not found.'));
            }

            if ($_POST['editableAttribute'] == 'port') {
                $model['port'] = $_POST['Device'][$_POST['editableIndex']]['port'];
            }

            if ($_POST['editableAttribute'] == 'deviceTypeUuid') {
                $model['deviceTypeUuid'] = $_POST['Device'][$_POST['editableIndex']]['deviceTypeUuid'];
            }

            if ($_POST['editableAttribute'] == 'deviceStatusUuid') {
                $model['deviceStatusUuid'] = $_POST['Device'][$_POST['editableIndex']]['deviceStatusUuid'];
            }

            if ($_POST['editableAttribute'] == 'dev_time') {
                $model['dev_time'] = date("Y-m-d H:i:s", $_POST['Device'][$_POST['editableIndex']]['dev_time']);
            }

            if ($_POST['editableAttribute'] == 'address') {
                $model['address'] = $_POST['Device'][$_POST['editableIndex']]['address'];
            }

            if (!$model->save()) {
                return json_encode(new HttpException(500, 'Model not saved.'));
            } else {
                return json_encode('');
            }
        }

        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Device model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new Device model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Device();

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                echo json_encode($model->errors);
                return $this->render('create', ['model' => $model]);
            }
            // сохраняем запись
            if ($model->save(false)) {
                MainFunctions::register("Добавлено новое оборудование " . $model['deviceType']['title']);
                return $this->redirect(['view', 'id' => $model->_id]);
            }
            echo json_encode($model->errors);
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Creates a new Device models.
     *
     * @return mixed
     */
    public function actionNew()
    {
        $devices = array();
        $device_count = 0;
        $objects = Protocols::find()
            ->select('*')
            ->all();
        foreach ($objects as $object) {
            $device = Device::find()
                ->select('*')
                ->where(['objectUuid' => $object['uuid']])
                ->one();
            if ($device == null) {
                $device = new Device();
                $device->uuid = MainFunctions::GUID();
                $device->nodeUuid = $object['uuid'];
                $device->deviceTypeUuid = DeviceType::DEVICE_LIGHT;
                $device->deviceStatusUuid = DeviceStatus::UNKNOWN;
                $device->serial = '222222';
                $device->interface = 1;
                //$device->dev_time = date('Y-m-d H:i:s');
                $device->changedAt = date('Y-m-d H:i:s');
                $device->createdAt = date('Y-m-d H:i:s');
                $device->save();
                $devices[$device_count] = $device;
                $device_count++;
            } else {
                if ($device['deviceTypeUuid'] != DeviceType::DEVICE_LIGHT) {
                    $device['deviceTypeUuid'] = DeviceType::DEVICE_LIGHT;
                    $device['changedAt'] = date('Y-m-d H:i:s');
                    $device->save();
                    echo $device['uuid'] . '<br/>';
                }
            }
        }
        return $this->render('new', ['devices' => $devices]);
    }


    /**
     * Updates an existing Device model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'update',
                    [
                        'model' => $model,
                    ]
                );
            }
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Build tree of device
     *
     * @return mixed
     */
    public function actionTree()
    {
        ini_set('memory_limit', '-1');
        $fullTree = array();
        $nodes = Node::find()->all();
        foreach ($nodes as $node) {
            if ($node['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED) {
                $class = 'critical1';
            } elseif ($node['deviceStatusUuid'] == DeviceStatus::NOT_WORK) {
                $class = 'critical2';
            } else {
                $class = 'critical3';
            }
            $fullTree['children'][] = [
                'status' => '<div class="progress"><div class="' . $class . '">' . $node['deviceStatus']->title . '</div></div>',
                'title' => 'Контроллер [' . $node['address'] . ']',
                'register' => $node['address'],
                'folder' => true
            ];
            $devices = Device::find()->where(['nodeUuid' => $node['uuid']])->all();
            if (isset($_GET['type']))
                $devices = Device::find()->where(['nodeUuid' => $node['uuid']])
                    ->andWhere(['deviceTypeUuid' => $_GET['type']])
                    ->all();
            foreach ($devices as $device) {
                $childIdx = count($fullTree['children']) - 1;
                if ($device['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED) {
                    $class = 'critical1';
                } elseif ($device['deviceStatusUuid'] == DeviceStatus::NOT_WORK) {
                    $class = 'critical2';
                } else {
                    $class = 'critical3';
                }
                $fullTree['children'][$childIdx]['children'][] = [
                    'title' => $device['deviceType']['title'],
                    'status' => '<div class="progress"><div class="'
                        . $class . '">' . $device['deviceStatus']->title . '</div></div>',
                    'register' => $device['port'] . ' [' . $device['address'] . ']',
                    'measure' => '',
                    'date' => $device['last_date'],
                    'folder' => true
                ];
                $channels = SensorChannel::find()->where(['deviceUuid' => $device['uuid']])->all();
                foreach ($channels as $channel) {
                    $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                    $measure = Measure::find()
                        ->where(['sensorChannelUuid' => $channel['uuid']])
                        ->orderBy('date desc')
                        ->one();
                    $date = '-';
                    if (!$measure) {
                        $config = null;
                        $config = SensorConfig::find()->where(['sensorChannelUuid' => $channel['uuid']])->one();
                        if ($config) {
                            $measure = Html::a('конфигурация', ['sensor-config/view', 'id' => $config['_id']]);
                            $date = $config['changedAt'];
                        }
                    } else {
                        $date = $measure['date'];
                        $measure = $measure['value'];
                    }
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
                        'title' => $channel['title'],
                        'register' => $channel['register'],
                        'measure' => $measure,
                        'date' => $date,
                        'folder' => false
                    ];
                }
            }
        }

        return $this->render(
            'tree',
            ['device' => $fullTree]
        );
    }

    /**
     * Build tree of device by user
     *
     * @param integer $id Id
     * @param $date_start
     * @param $date_end
     * @return mixed
     */
    public function actionTable($id, $date_start, $date_end)
    {
        ini_set('memory_limit', '-1');
        return $this->render(
            'tree-user'
        );
    }

    /**
     * Deletes an existing Device model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public
    function actionDelete($id)
    {
        $device = $this->findModel($id);
        $photos = Info::find()
            ->select('*')
            ->where(['deviceUuid' => $device['uuid']])
            ->all();
        foreach ($photos as $photo) {
            $photo->delete();
        }

        $measures = Measure::find()
            ->select('*')
            ->where(['deviceUuid' => $device['uuid']])
            ->all();
        foreach ($measures as $measure) {
            $measure->delete();
        }

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Device model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Device the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Dashboard
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionDashboard()
    {
        if (isset($_GET['uuid'])) {
            $device = Device::find()
                ->where(['uuid' => $_GET['uuid']])
                ->one();
            if ($device && $device['deviceTypeUuid']==DeviceType::DEVICE_ELECTRO)
                return self::actionDashboardElectro($device['uuid']);
        } else
            return self::actionIndex();

        if (isset($_POST['type']) && $_POST['type'] == 'set') {
            if (isset($_POST['device'])) {
                $device = Device::find()->where(['uuid' => $_POST['device']])->one();
                if (isset($_POST['value'])) {
                    $this->set($device, $_POST['value']);
                    self::updateConfig($device['uuid'], DeviceConfig::PARAM_SET_VALUE, $_POST['value']);
                }
            }
        }

        if (isset($_POST['type']) && $_POST['type'] == 'params') {
            if (isset($_POST['device'])) {
                $device = Device::find()->where(['uuid' => $_POST['device']])->one();
                $lightConfig = new MtmDevLightConfig();
                $lightConfig->mode = $_POST['mode'];
                $lightConfig->power = $_POST['power'];
                $lightConfig->group = $_POST['group'];
                $lightConfig->frequency = $_POST['frequency'];

                $lightConfig->type = 2;
                $lightConfig->protoVersion = 0;
                $lightConfig->device = MtmPktHeader::$MTM_DEVICE_LIGHT;

                $pkt = [
                    'type' => 'light',
                    'address' => $device['address'], // 16 байт мак адрес в шестнадцатиричном представлении
                    'data' => $lightConfig->getBase64Data(), // закодированые бинарные данные
                ];
                $org_id = User::ORGANISATION_UUID;
                $node_id = $device['node']['_id'];
                self::sendConfig($pkt, $org_id, $node_id);

                self::updateConfig($device['uuid'], DeviceConfig::PARAM_FREQUENCY, $_POST['frequency']);
                self::updateConfig($device['uuid'], DeviceConfig::PARAM_POWER, $_POST['power']);
                self::updateConfig($device['uuid'], DeviceConfig::PARAM_GROUP, $_POST['group']);
                self::updateConfig($device['uuid'], DeviceConfig::PARAM_REGIME, $_POST['mode']);
            }
        }

        if (isset($_POST['type']) && $_POST['type'] == 'config') {
            if (isset($_POST['device'])) {
                $device = Device::find()->where(['uuid' => $_POST['device']])->one();
                $lightConfig = new MtmDevLightConfigLight();
                if (isset($_POST['device'])) {
                    $device = Device::find()->where(['uuid' => $_POST['device']])->one();
                    if ($device && isset($_POST['time0'])) {
                        $lightConfig->time[0] = $_POST['time0'];
                        $lightConfig->value[0] = $_POST['level0'];
                        $lightConfig->time[1] = $_POST['time1'];
                        $lightConfig->value[1] = $_POST['level1'];
                        $lightConfig->time[2] = $_POST['time2'];
                        $lightConfig->value[2] = $_POST['level2'];
                        $lightConfig->time[3] = $_POST['time3'];
                        $lightConfig->value[3] = $_POST['level3'];

                        $lightConfig->type = 3;
                        $lightConfig->protoVersion = 0;
                        $lightConfig->device = MtmPktHeader::$MTM_DEVICE_LIGHT;

                        $pkt = [
                            'type' => 'light',
                            'address' => $device['address'], // 16 байт мак адрес в шестнадцатиричном представлении
                            'data' => $lightConfig->getBase64Data(), // закодированые бинарные данные
                        ];
                        $org_id = User::ORGANISATION_UUID;
                        $node_id = $device['node']['_id'];
                        self::sendConfig($pkt, $org_id, $node_id);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_TIME0, $_POST['time0']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_LEVEL0, $_POST['level0']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_TIME1, $_POST['time1']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_LEVEL1, $_POST['level1']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_TIME2, $_POST['time2']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_LEVEL2, $_POST['level2']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_TIME3, $_POST['time3']);
                        self::updateConfig($device['uuid'], DeviceConfig::PARAM_LEVEL3, $_POST['level3']);
                    }
                }
            }
        }

        $parameters['mode'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_REGIME);
        $parameters['group'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_GROUP);
        $parameters['power'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_POWER);
        $parameters['frequency'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_FREQUENCY);
        $parameters['value'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_SET_VALUE);

        $parameters['time0'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME0);
        $parameters['level0'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL0);
        $parameters['time1'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME1);
        $parameters['level1'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL1);
        $parameters['time2'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME2);
        $parameters['level2'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL2);
        $parameters['time3'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME3);
        $parameters['level3'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL3);

        return $this->render(
            'dashboard',
            [
                'device' => $device,
                'parameters' => $parameters
            ]
        );
    }

    /**
     * Dashboard
     *
     * @param $uuid
     * @return string
     * @throws InvalidConfigException
     */
    public function actionDashboardElectro($uuid)
    {
        if (isset($_GET['uuid'])) {
            $device = Device::find()
                ->where(['uuid' => $uuid])
                ->one();
        } else {
            return $this->actionIndex();
        }

        // power by days
        $sChannel = SensorChannel::find()->where(['deviceUuid' => $device, 'measureTypeUuid' => MeasureType::POWER])->one();
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->andWhere(['parameter' => 0])
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = 0;
        $data = [];
        $categories = '';
        $values = '';
        foreach (array_reverse($last_measures) as $measure) {
            if ($cnt > 0) {
                $categories .= ',';
                $values .= ',';
            }
            $categories .= "'" . date_format(date_create($measure['date']), 'd H:i') . "'";
            $values .= $measure->value;
            $cnt++;
        }

        // archive days
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->orderBy('date DESC')
            ->limit(200)
            ->all();
        $cnt = -1;
        $data['days'] = [];
        $data['month'] = [];
        $last_date = '';
        foreach (array_reverse($last_measures) as $measure) {
            if ($measure['date'] != $last_date) {
                $last_date = $measure['date'];
                $cnt++;
            }
            $data['days'][$cnt]['date'] = $measure['date'];
            if ($measure['parameter'] == 1)
                $data['days'][$cnt]['w1'] = $measure['value'];
            if ($measure['parameter'] == 2)
                $data['days'][$cnt]['w2'] = $measure['value'];
            if ($measure['parameter'] == 3)
                $data['days'][$cnt]['w3'] = $measure['value'];
            if ($measure['parameter'] == 4)
                $data['days'][$cnt]['w4'] = $measure['value'];
            if ($measure['parameter'] == 0)
                $data['days'][$cnt]['ws'] = $measure['value'];
        }

        // archive month
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = -1;
        $last_date = '';
        foreach ($last_measures as $measure) {
            if ($measure['date'] != $last_date)
                $last_date = $measure['date'];
            $data['month'][$cnt]['date'] = $measure['date'];
            if ($measure['parameter'] == 1)
                $data['month'][$cnt]['w1'] = $measure['value'];
            if ($measure['parameter'] == 2)
                $data['month'][$cnt]['w2'] = $measure['value'];
            if ($measure['parameter'] == 3)
                $data['month'][$cnt]['w3'] = $measure['value'];
            if ($measure['parameter'] == 4)
                $data['month'][$cnt]['w4'] = $measure['value'];
            if ($measure['parameter'] == 0)
                $data['month'][$cnt]['ws'] = $measure['value'];
        }

        // integrate
        $parameters['increment']['w1']['current'] = "-";
        $parameters['increment']['w2']['current'] = "-";
        $parameters['increment']['w3']['current'] = "-";
        $parameters['increment']['w4']['current'] = "-";
        $parameters['increment']['ws']['current'] = "-";

        $integrates = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_TOTAL_CURRENT])
            ->orderBy('date DESC')
            ->limit(500)
            ->all();
        foreach ($integrates as $measure) {
            if ($measure['parameter'] == 1)
                $parameters['increment']['w1']['current'] = $measure['value'];
            if ($measure['parameter'] == 2)
                $parameters['increment']['w2']['current'] = $measure['value'];
            if ($measure['parameter'] == 3)
                $parameters['increment']['w3']['current'] = $measure['value'];
            if ($measure['parameter'] == 4)
                $parameters['increment']['w4']['current'] = $measure['value'];
            if ($measure['parameter'] == 0)
                $parameters['increment']['ws']['current'] = $measure['value'];
        }

        $month = date("Y-m-01 00:00:00", time());
        $current_month = date("Y-m-01 00:00:00", strtotime("-1 months"));
        $prev_month = date("Y-m-01 00:00:00", strtotime("-2 months"));

        $parameters['increment']['w1']['last'] = "-";
        $parameters['increment']['w2']['last'] = "-";
        $parameters['increment']['w3']['last'] = "-";
        $parameters['increment']['w4']['last'] = "-";
        $parameters['increment']['ws']['last'] = "-";
        $parameters['increment']['w1']['prev'] = "-";
        $parameters['increment']['w2']['prev'] = "-";
        $parameters['increment']['w3']['prev'] = "-";
        $parameters['increment']['w4']['prev'] = "-";
        $parameters['increment']['ws']['prev'] = "-";
        $parameters['month']['w1']['last'] = "-";
        $parameters['month']['w2']['last'] = "-";
        $parameters['month']['w3']['last'] = "-";
        $parameters['month']['w4']['last'] = "-";
        $parameters['month']['ws']['last'] = "-";
        $parameters['month']['w1']['prev'] = "-";
        $parameters['month']['w2']['prev'] = "-";
        $parameters['month']['w3']['prev'] = "-";
        $parameters['month']['w4']['prev'] = "-";
        $parameters['month']['ws']['prev'] = "-";
        $parameters['month']['w1']['current'] = "-";
        $parameters['month']['w2']['current'] = "-";
        $parameters['month']['w3']['current'] = "-";
        $parameters['month']['w4']['current'] = "-";
        $parameters['month']['ws']['current'] = "-";

        $parameters['increment']['date']['last'] = date("Y-m-01", strtotime($current_month));
        $parameters['increment']['date']['prev'] = date("Y-m-01", strtotime($prev_month));
        $parameters['month']['date']['last'] = date("Y-m-01", strtotime($current_month));
        $parameters['month']['date']['prev'] = date("Y-m-01", strtotime($prev_month));
        $parameters['month']['date']['current'] = date("Y-m-01", strtotime($month));

        $integrates = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_TOTAL])
            ->orderBy('date DESC')
            ->all();
        foreach ($integrates as $measure) {
            if ($measure['parameter'] == 1) {
                if ($measure['date'] == $current_month)
                    $parameters['increment']['w1']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['increment']['w1']['prev'] = $measure['value'];
            }
            if ($measure['parameter'] == 2) {
                if ($measure['date'] == $current_month)
                    $parameters['increment']['w2']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['increment']['w2']['prev'] = $measure['value'];
            }
            if ($measure['parameter'] == 3) {
                if ($measure['date'] == $current_month)
                    $parameters['increment']['w3']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['increment']['w3']['prev'] = $measure['value'];
            }
            if ($measure['parameter'] == 4) {
                if ($measure['date'] == $current_month)
                    $parameters['increment']['w4']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['increment']['w4']['prev'] = $measure['value'];
            }
            if ($measure['parameter'] == 0) {
                if ($measure['date'] == $current_month)
                    $parameters['increment']['ws']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['increment']['ws']['prev'] = $measure['value'];
            }
        }
        $integrates = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
            ->orderBy('date DESC')
            ->all();
        foreach ($integrates as $measure) {
            if ($measure['parameter'] == 1) {
                if ($measure['date'] == $current_month)
                    $parameters['month']['w1']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['month']['w1']['prev'] = $measure['value'];
                if ($measure['date'] == $month)
                    $parameters['month']['w1']['current'] = $measure['value'];
            }
            if ($measure['parameter'] == 2) {
                if ($measure['date'] == $current_month)
                    $parameters['month']['w2']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['month']['w2']['prev'] = $measure['value'];
                if ($measure['date'] == $month)
                    $parameters['month']['w2']['current'] = $measure['value'];
            }
            if ($measure['parameter'] == 3) {
                if ($measure['date'] == $current_month)
                    $parameters['month']['w3']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['month']['w3']['prev'] = $measure['value'];
                if ($measure['date'] == $month)
                    $parameters['month']['w3']['current'] = $measure['value'];
            }
            if ($measure['parameter'] == 4) {
                if ($measure['date'] == $current_month)
                    $parameters['month']['w4']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['month']['w4']['prev'] = $measure['value'];
                if ($measure['date'] == $month)
                    $parameters['month']['w4']['current'] = $measure['value'];
            }
            if ($measure['parameter'] == 0) {
                if ($measure['date'] == $current_month)
                    $parameters['month']['ws']['last'] = $measure['value'];
                if ($measure['date'] == $prev_month)
                    $parameters['month']['ws']['prev'] = $measure['value'];
                if ($measure['date'] == $month)
                    $parameters['month']['ws']['current'] = $measure['value'];
            }
        }

        $parameters['trends']['title'] = "";
        $measures = [];
        if ($sChannel) {
            $parameters['trends']['title'] = $sChannel['title'];
            $measures = Measure::find()
                ->where(['sensorChannelUuid' => $sChannel['uuid']])
                ->andWhere(['type' => MeasureType::MEASURE_TYPE_INTERVAL])
                ->andWhere(['parameter' => 0])
                ->orderBy('date DESC')
                ->limit(200)
                ->all();
        }

        $cnt = 0;
        $parameters['uuid'] = $sChannel['_id'];
        $parameters['trends']['categories'] = '';
        $parameters['trends']['values'] = '';
        foreach (array_reverse($measures) as $measure) {
            if ($cnt > 0) {
                $parameters['trends']['categories'] .= ',';
                $parameters['trends']['values'] .= ',';
            }
            $parameters['trends']['categories'] .= "'" . date("d H:i", strtotime($measure->date)) . "'";
            $parameters['trends']['values'] .= $measure->value;
            $cnt++;
        }

        $parameters['days']['title'] = "";
        $measures = [];
        if ($sChannel) {
            $parameters['trends']['title'] = $sChannel['title'];
            $measures = Measure::find()
                ->where(['sensorChannelUuid' => $sChannel['uuid']])
                ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
                ->andWhere(['parameter' => 0])
                ->orderBy('date DESC')
                ->limit(200)
                ->all();
        }

        $cnt = 0;
        $parameters['days']['categories'] = '';
        $parameters['days']['values'] = '';
        foreach (array_reverse($measures) as $measure) {
            if ($cnt > 0) {
                $parameters['days']['categories'] .= ',';
                $parameters['days']['values'] .= ',';
            }
            $parameters['days']['categories'] .= "'" . $measure->date . "'";
            $parameters['days']['values'] .= $measure->value;
            $cnt++;
        }

        $deviceRegisters = DeviceRegister::find()
            ->where(['deviceUuid' => $device['uuid']])
            ->orderBy('date DESC')
            ->limit(8);
        $parameters['register']['provider'] = new ActiveDataProvider(
            [
                'query' => $deviceRegisters,
                'sort' => false,
                'pagination' => false
            ]
        );

        $parameters['current']['i1'] = "-";
        $parameters['current']['i2'] = "-";
        $parameters['current']['i3'] = "-";
        $parameters['current']['u1'] = "-";
        $parameters['current']['u2'] = "-";
        $parameters['current']['u3'] = "-";
        $parameters['current']['f1'] = "-";
        $parameters['current']['f2'] = "-";
        $parameters['current']['f3'] = "-";
        $parameters['current']['w1'] = "-";
        $parameters['current']['w2'] = "-";
        $parameters['current']['w3'] = "-";
        $parameters['current']['ws'] = "-";

        $measures = Measure::find()
            ->where(['type' => MeasureType::MEASURE_TYPE_CURRENT])
            ->orderBy('date DESC')->limit(200)
            ->all();
        foreach ($measures as $measure) {
            if ($measure['sensorChannel']['measureTypeUuid'] == MeasureType::CURRENT &&
                $measure['sensorChannel']['deviceUuid'] == $device['uuid']) {
                if ($measure['parameter'] == 1)
                    $parameters['current']['i1'] = $measure['value'];
                if ($measure['parameter'] == 2)
                    $parameters['current']['i2'] = $measure['value'];
                if ($measure['parameter'] == 3)
                    $parameters['current']['i3'] = $measure['value'];
            }
            if ($measure['sensorChannel']['measureTypeUuid'] == MeasureType::VOLTAGE &&
                $measure['sensorChannel']['deviceUuid'] == $device['uuid']) {
                if ($measure['parameter'] == 1)
                    $parameters['current']['u1'] = $measure['value'];
                if ($measure['parameter'] == 2)
                    $parameters['current']['u2'] = $measure['value'];
                if ($measure['parameter'] == 3)
                    $parameters['current']['u3'] = $measure['value'];
            }
            if ($measure['sensorChannel']['measureTypeUuid'] == MeasureType::FREQUENCY &&
                $measure['sensorChannel']['deviceUuid'] == $device['uuid']) {
                if ($measure['parameter'] == 0)
                    $parameters['current']['f1'] = $measure['value'];
            }
            if ($measure['sensorChannel']['measureTypeUuid'] == MeasureType::POWER &&
                $measure['sensorChannel']['deviceUuid'] == $device['uuid']) {
                if ($measure['parameter'] == 0)
                    $parameters['current']['ws'] = $measure['value'];
                if ($measure['parameter'] == 1)
                    $parameters['current']['w1'] = $measure['value'];
                if ($measure['parameter'] == 2)
                    $parameters['current']['w2'] = $measure['value'];
                if ($measure['parameter'] == 3)
                    $parameters['current']['w3'] = $measure['value'];
            }
        }


        $parameters['trends']['title'] = $sChannel['title'];

        return $this->render(
            'dashboard-electro',
            [
                'device' => $device,
                'parameters' => $parameters,
                'dataAll' => $data
            ]
        );
    }

    /**
     * Dashboard
     *
     * @param $uuid
     * @return string
     * @throws InvalidConfigException
     */
    public function actionArchive($uuid)
    {
        if (isset($_GET['uuid'])) {
            $device = Device::find()
                ->where(['uuid' => $uuid])
                ->one();
        } else {
            return $this->actionIndex();
        }

        // power by days
        $sChannel = SensorChannel::find()
            ->where(['deviceUuid' => $device, 'measureTypeUuid' => MeasureType::POWER])
            ->one();
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->andWhere(['parameter' => 0])
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = 0;
        $data = [];
        $data['trends'] = [];
        $data['trends']['days']['categories'] = '';
        $data['trends']['days']['values'] = '';
        foreach (array_reverse($last_measures) as $measure) {
            if ($cnt > 0) {
                $data['trends']['days']['categories'] .= ',';
                $data['trends']['days']['values'] .= ',';
            }
            $data['trends']['days']['categories'] .= "'" . $measure->date . "'";
            $data['trends']['days']['values'] .= $measure->value;
            $cnt++;
        }

        // archive days
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = -1;
        $data['days'] = [];
        $data['month'] = [];

        $last_date = '';
        foreach ($last_measures as $measure) {
            if ($measure['date'] != $last_date) {
                $last_date = $measure['date'];
                $cnt++;
            }
            $data['days'][$cnt]['date'] = $measure['date'];
            if ($measure['parameter'] == 1)
                $data['days'][$cnt]['w1'] = $measure['value'];
            if ($measure['parameter'] == 2)
                $data['days'][$cnt]['w2'] = $measure['value'];
            if ($measure['parameter'] == 3)
                $data['days'][$cnt]['w3'] = $measure['value'];
            if ($measure['parameter'] == 4)
                $data['days'][$cnt]['w4'] = $measure['value'];
            if ($measure['parameter'] == 0)
                $data['days'][$cnt]['ws'] = $measure['value'];
        }

        // power by month
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
            ->andWhere(['parameter' => 0])
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = 0;
        $data['trends']['month']['categories'] = '';
        $data['trends']['month']['values'] = '';
        foreach (array_reverse($last_measures) as $measure) {
            if ($cnt > 0) {
                $data['trends']['month']['categories'] .= ',';
                $data['trends']['month']['values'] .= ',';
            }
            $data['trends']['month']['categories'] .= "'" . $measure->date . "'";
            $data['trends']['month']['values'] .= $measure->value;
            $cnt++;
        }

        // archive month
        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = -1;
        $last_date = '';
        foreach ($last_measures as $measure) {
            if ($measure['date'] != $last_date) {
                $last_date = $measure['date'];
                $cnt++;
            }
            $data['month'][$cnt]['date'] = $measure['date'];
            if ($measure['parameter'] == 1)
                $data['month'][$cnt]['w1'] = $measure['value'];
            if ($measure['parameter'] == 2)
                $data['month'][$cnt]['w2'] = $measure['value'];
            if ($measure['parameter'] == 3)
                $data['month'][$cnt]['w3'] = $measure['value'];
            if ($measure['parameter'] == 4)
                $data['month'][$cnt]['w4'] = $measure['value'];
            if ($measure['parameter'] == 0)
                $data['month'][$cnt]['ws'] = $measure['value'];
        }

        $data['trends']['title'] = $sChannel['title'];

        //echo json_encode($data);
        return $this->render(
            'archive',
            [
                'dataAll' => $data
            ]
        );
    }

    /**
     * Dashboard
     *
     * @param $uuid
     * @return string
     * @throws InvalidConfigException
     */
    public function actionArchiveDays($uuid)
    {
        if (isset($_GET['uuid'])) {
            $device = Device::find()
                ->where(['uuid' => $uuid])
                ->one();
        } else {
            return $this->actionIndex();
        }

        // power by days
        $sChannel = SensorChannel::find()
            ->where(['deviceUuid' => $device, 'measureTypeUuid' => MeasureType::POWER])
            ->one();
        // archive days
        $start_time = '2018-12-31 00:00:00';
        $end_time = '2021-12-31 00:00:00';
        if (isset($_GET['end_time'])) {
            $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
        }
        if (isset($_GET['start_time'])) {
            $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
        }

        $last_measures = Measure::find()
            ->where(['sensorChannelUuid' => $sChannel])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->andWhere('date >= "'.$start_time.'"')
            ->andWhere('date < "'.$end_time.'"')
            ->orderBy('date DESC')
            ->limit(100)
            ->all();
        $cnt = -1;
        $data['days'] = [];
        $data['month'] = [];

        $last_date = '';
        foreach ($last_measures as $measure) {
            if ($measure['date'] != $last_date) {
                $last_date = $measure['date'];
                $cnt++;
            }
            $data['days'][$cnt]['date'] = $measure['date'];
            if ($measure['parameter'] == 1)
                $data['days'][$cnt]['w1'] = $measure['value'];
            if ($measure['parameter'] == 2)
                $data['days'][$cnt]['w2'] = $measure['value'];
            if ($measure['parameter'] == 3)
                $data['days'][$cnt]['w3'] = $measure['value'];
            if ($measure['parameter'] == 4)
                $data['days'][$cnt]['w4'] = $measure['value'];
            if ($measure['parameter'] == 0)
                $data['days'][$cnt]['ws'] = $measure['value'];
        }

        return $this->render(
            'archive-days',
            [
                'dataAll' => $data
            ]
        );
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    public
    function actionSetConfig()
    {
        if (!Yii::$app->user->can(User::PERMISSION_ADMIN)) {
            return 'Нет прав.';
        }

        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;

            if ($uuid) {
                $device = Device::find()->where(['uuid' => $_POST['uuid']])->one();

                $parameters['mode'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_REGIME);
                $parameters['group'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_GROUP);
                $parameters['power'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_POWER);
                $parameters['frequency'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_FREQUENCY);
                $parameters['value'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_SET_VALUE);

                $parameters['time0'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME0);
                $parameters['level0'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL0);
                $parameters['time1'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME1);
                $parameters['level1'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL1);
                $parameters['time2'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME2);
                $parameters['level2'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL2);
                $parameters['time3'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_TIME3);
                $parameters['level3'] = self::getParameter($device['uuid'], DeviceConfig::PARAM_LEVEL3);

                return $this->renderAjax('_edit_light_config', [
                    'device' => $device,
                    'parameters' => $parameters
                ]);
            }
        }
        return 'Нельзя сконфигурировать устройство';
    }

    /**
     * функция отправляет конфигурацию на светильник
     *
     * @param $packet
     * @param $org_id
     * @param $node_id
     */
    static function sendConfig($packet, $org_id, $node_id)
    {

    }

    /**
     * @param $deviceUuid
     * @param $parameter
     * @param $value
     * @throws InvalidConfigException
     */
    function updateConfig($deviceUuid, $parameter, $value)
    {
        $deviceConfig = DeviceConfig::find()->where(['deviceUuid' => $deviceUuid])->andWhere(['parameter' => $parameter])->one();
        if ($deviceConfig) {
            $deviceConfig['value'] = $value;
            $deviceConfig->save();
        } else {
            $deviceConfig = new DeviceConfig();
            $deviceConfig->uuid = MainFunctions::GUID();
            $deviceConfig->deviceUuid = $deviceUuid;
            $deviceConfig->value = $value;
            $deviceConfig->parameter = $parameter;
            $deviceConfig->oid = User::ORGANISATION_UUID;
            $deviceConfig->save();
            //echo json_encode($deviceConfig->errors);
            //exit(0);
        }
    }

    /**
     * @param $deviceUuid
     * @param $parameter
     * @return mixed|null
     * @throws InvalidConfigException
     */
    static function getParameter($deviceUuid, $parameter)
    {
        $deviceConfig = DeviceConfig::find()->where(['deviceUuid' => $deviceUuid])->andWhere(['parameter' => $parameter])->one();
        if ($deviceConfig) {
            return $deviceConfig['value'];
        } else {
            return null;
        }
    }

    /**
     * @param $uuid
     * @return string
     */
    public
    function actionRegister($uuid)
    {
        $deviceRegisters = DeviceRegister::find()->where(['deviceUuid' => $uuid]);
        $provider = new ActiveDataProvider(
            [
                'query' => $deviceRegisters,
                'sort' => false,
            ]
        );
        return $this->render(
            'register',
            [
                'provider' => $provider
            ]
        );
    }

    /**
     * @param $uuid
     * @return string
     */
    public
    function actionTrends($uuid)
    {
        $deviceElectro = Device::find()->where(['uuid' => $uuid])->one();
        $parameters1 = [];
        $parameters1['uuid'] = '';
        $parameters1['trends']['title'] = '';
        $parameters1['trends']['categories'] = '';
        $parameters1['trends']['values'] = '';
        $parameters2 = [];
        $parameters2['uuid'] = '';
        $parameters2['trends']['title'] = '';
        $parameters2['trends']['categories'] = '';
        $parameters2['trends']['values'] = '';
        $parameters3 = [];
        $parameters3['uuid'] = '';
        $parameters3['trends']['title'] = '';
        $parameters3['trends']['categories'] = '';
        $parameters3['trends']['values'] = '';

        if ($deviceElectro) {
            $sensorChannel1 = SensorChannel::find()->where(['deviceUuid' => $deviceElectro['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::POWER])->one();

            if ($sensorChannel1) {
                $measures = Measure::find()
                    ->where(['sensorChannelUuid' => $sensorChannel1['uuid']])
                    ->andWhere(['type' => MeasureType::MEASURE_TYPE_INTERVAL])
                    ->orderBy('date DESC')
                    ->limit(200)->all();

                $cnt = 0;
                $parameters1['uuid'] = $sensorChannel1['_id'];
                $parameters1['trends']['title'] = $sensorChannel1['title'];
                foreach (array_reverse($measures) as $measure) {
                    if ($cnt > 0) {
                        $parameters1['trends']['categories'] .= ',';
                        $parameters1['trends']['values'] .= ',';
                    }
                    $parameters1['trends']['categories'] .= "'" . $measure->date . "'";
                    $parameters1['trends']['values'] .= $measure->value;
                    $cnt++;
                }
            }

            $sensorChannel2 = SensorChannel::find()->where(['deviceUuid' => $deviceElectro['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::VOLTAGE])->one();
            if ($sensorChannel2) {
                $measures = Measure::find()
                    ->where(['sensorChannelUuid' => $sensorChannel2['uuid']])
                    ->andWhere(['type' => MeasureType::MEASURE_TYPE_INTERVAL])
                    ->orderBy('date DESC')
                    ->limit(200)->all();

                $cnt = 0;
                $parameters2['uuid'] = $sensorChannel2['_id'];
                $parameters2['trends']['title'] = $sensorChannel2['title'];
                foreach (array_reverse($measures) as $measure) {
                    if ($cnt > 0) {
                        $parameters2['trends']['categories'] .= ',';
                        $parameters2['trends']['values'] .= ',';
                    }
                    $parameters2['trends']['categories'] .= "'" . $measure->date . "'";
                    $parameters2['trends']['values'] .= $measure->value;
                    $cnt++;
                }
            }

            $sensorChannel3 = SensorChannel::find()->where(['deviceUuid' => $deviceElectro['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::CURRENT])->one();
            if ($sensorChannel3) {
                $measures = Measure::find()
                    ->where(['sensorChannelUuid' => $sensorChannel3['uuid']])
                    ->andWhere(['type' => MeasureType::MEASURE_TYPE_INTERVAL])
                    ->orderBy('date DESC')
                    ->limit(200)->all();

                $cnt = 0;
                $parameters3['uuid'] = $sensorChannel3['_id'];
                $parameters3['trends']['title'] = $sensorChannel3['title'];
                foreach (array_reverse($measures) as $measure) {
                    if ($cnt > 0) {
                        $parameters3['trends']['categories'] .= ',';
                        $parameters3['trends']['values'] .= ',';
                    }
                    $parameters3['trends']['categories'] .= "'" . $measure->date . "'";
                    $parameters3['trends']['values'] .= $measure->value;
                    $cnt++;
                }
            }
        }
        return $this->render(
            'trends',
            [
                'device' => $deviceElectro,
                'parameters1' => $parameters1,
                'parameters2' => $parameters2,
                'parameters3' => $parameters3
            ]
        );
    }
}

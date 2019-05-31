<?php

namespace backend\controllers;

use backend\models\DeviceSearch;
use common\components\MainFunctions;
use common\models\Device;
use common\models\DeviceStatus;
use common\models\DeviceType;
use common\models\House;
use common\models\Measure;
use common\models\Node;
use common\models\Objects;
use common\models\Photo;
use common\models\SensorChannel;
use common\models\SensorConfig;
use common\models\Street;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
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
            if ($_POST['editableAttribute'] == 'port') {
                $model['tag'] = $_POST['Device'][$_POST['editableIndex']]['port'];
            }
            if ($_POST['editableAttribute'] == 'deviceTypeUuid') {
                $model['deviceTypeUuid'] = $_POST['Device'][$_POST['editableIndex']]['deviceTypeUuid'];
            }
            if ($_POST['editableAttribute'] == 'deviceStatusUuid') {
                $model['deviceStatusUuid'] = $_POST['Device'][$_POST['editableIndex']]['deviceStatusUuid'];
            }
            if ($_POST['editableAttribute'] == 'date') {
                $model['date'] = date("Y-m-d H:i:s", $_POST['Device'][$_POST['editableIndex']]['date']);
            }
            $model->save();
            return json_encode('');
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
                MainFunctions::register("Добавлено новое оборудование ".$model['deviceType']['title'].' '.
                    $model['node']['object']->getAddress().' ['.$model['node']['address'].']');
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
        $objects = Objects::find()
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
                $device->deviceTypeUuid = DeviceType::EQUIPMENT_HVS;
                $device->deviceStatusUuid = DeviceStatus::UNKNOWN;
                $device->serial = '222222';
                $device->interface = 1;
                $device->date = date('Y-m-d H:i:s');
                $device->changedAt = date('Y-m-d H:i:s');
                $device->createdAt = date('Y-m-d H:i:s');
                $device->save();
                $devices[$device_count] = $device;
                $device_count++;
            } else {
                if ($device['deviceTypeUuid'] != DeviceType::EQUIPMENT_HVS) {
                    $device['deviceTypeUuid'] = DeviceType::EQUIPMENT_HVS;
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
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        foreach ($streets as $street) {
            $fullTree['children'][] = [
                'title' => $street['title'],
                'folder' => true
            ];
            $houses = House::find()->where(['streetUuid' => $street['uuid']])->
            orderBy('number')->all();
            foreach ($houses as $house) {
                $childIdx = count($fullTree['children']) - 1;
                $fullTree['children'][$childIdx]['children'][] = [
                    'title' => $house->getFullTitle(),
                    'folder' => true
                ];
                $objects = Objects::find()->where(['houseUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
                        'title' => $object['objectType']['title'] . ' ' . $object['title'],
                        'folder' => true
                    ];
                    $nodes = Node::find()->where(['objectUuid' => $object['uuid']])->all();
                    foreach ($nodes as $node) {
                        $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                        if ($node['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED) {
                            $class = 'critical1';
                        } elseif ($node['deviceStatusUuid'] == DeviceStatus::NOT_WORK) {
                            $class = 'critical2';
                        } else {
                            $class = 'critical3';
                        }
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] = [
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
                            $childIdx4 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children']) - 1;
                            if ($device['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED) {
                                $class = 'critical1';
                            } elseif ($device['deviceStatusUuid'] == DeviceStatus::NOT_WORK) {
                                $class = 'critical2';
                            } else {
                                $class = 'critical3';
                            }
                            $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][] = [
                                'title' => $device['deviceType']['title'],
                                'status' => '<div class="progress"><div class="'
                                    . $class . '">' . $device['deviceStatus']->title . '</div></div>',
                                'register' => $device['port'].' ['.$device['address'].']',
                                'measure' => '',
                                'date' => $device['date'],
                                'folder' => true
                            ];
                            $channels = SensorChannel::find()->where(['deviceUuid' => $device['uuid']])->all();
                            foreach ($channels as $channel) {
                                $childIdx5 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children']) - 1;
                                $measure = Measure::find()->where(['sensorChannelUuid' => $channel['uuid']])->one();
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
                                $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][$childIdx5]['children'][] = [
                                    'title' => $channel['title'],
                                    'register' => $channel['register'],
                                    'measure' => $measure,
                                    'date' => $date,
                                    'folder' => false
                                ];
                            }
                        }
                    }
                }
            }
        }
        $deviceTypes = DeviceType::find()->all();
        $items = ArrayHelper::map($deviceTypes, 'uuid', 'title');

        return $this->render(
            'tree',
            ['device' => $fullTree, 'deviceTypes' => $items]
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
        $photos = Photo::find()
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
}

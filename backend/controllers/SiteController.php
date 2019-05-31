<?php
namespace backend\controllers;

use backend\models\UserSearch;
use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\models\City;
use common\models\Defect;
use common\models\EquipmentRegister;
use common\models\ExternalEvent;
use common\models\Journal;
use common\models\Node;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\Organisation;
use common\models\Device;
use common\models\DeviceType;
use common\models\Objects;
use common\models\LoginForm;
use common\models\Measure;
use common\models\SensorChannel;
use common\models\Street;
use common\models\User;
use common\models\UsersAttribute;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['signup', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'dashboard', 'test', 'timeline'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['error']);
        return $actions;
    }

    /**
     * Displays map.
     *
     * @return string
     */
    public function actionIndex()
    {
        /**
         * Работа с картой
         */
        $objectSelect = Objects::find()
            ->select('_id, title, latitude, longitude')
            ->asArray()
            ->all();

        $cnt = 0;
        $objectsGroup = 'var objects=L.layerGroup([';
        $objectsList = '';
        foreach ($objectSelect as $object) {
            $objectsList .= 'var object' . $object["_id"]
                . '= L.marker([' . $object["latitude"]
                . ',' . $object["longitude"] . ']).bindPopup("<b>'
                . $object["title"] . '</b>").openPopup();';
            if ($cnt > 0) {
                $objectsGroup .= ',';
            }

            $objectsGroup .= 'object' . $object["_id"];
            $cnt++;
        }

        $objectsGroup .= ']);' . PHP_EOL;

        $devices = Device::find()->all();

        $cnt = 0;
        $default_coordinates = "[55.54,61.36]";
        $coordinates = $default_coordinates;
        $equipmentsGroup = 'var devices=L.layerGroup([';
        $equipmentsList = '';
        foreach ($devices as $device) {
            if ($device["object"]["latitude"] > 0) {
                $equipmentsList .= 'var device'
                    . $device["_id"]
                    . '= L.marker([' . $device["object"]["latitude"]
                    . ',' . $device["object"]["longitude"]
                    . '], {icon: houseIcon}).bindPopup("<b>'
                    . $device["deviceType"]["title"] . '</b><br/>'
                    . $device["object"]->getAddress() . '").openPopup();';
                $coordinates = "[".$device["object"]["latitude"].",".$device["object"]["longitude"]."]";
                if ($coordinates==$default_coordinates && $device["object"]["latitude"]>0) {
                    $coordinates = "[".$device["object"]["latitude"].",".$device["object"]["longitude"]."]";
                }
                if ($cnt > 0) {
                    $equipmentsGroup .= ',';
                }

                $equipmentsGroup .= 'device' . $device["_id"];
                $cnt++;
            }
        }
        $equipmentsGroup .= ']);' . PHP_EOL;

        return $this->render(
            'index',
            [
                'objectsGroup' => $objectsGroup,
                'objectsList' => $objectsList,
                'devicesGroup' => $equipmentsGroup,
                'devicesList' => $equipmentsList,
                'coordinates' => $coordinates
            ]
        );
    }

    /**
     * Dashboard
     *
     * @return string
     */
    public function actionDashboard()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        $accountUser = Yii::$app->user->identity;
        $currentUser = User::find()
            ->where(['_id' => $accountUser['id']])
            ->asArray()
            ->one();

        $cityCount = City::find()->count();
        $streetCount = Street::find()->count();
        $objectsCount = Objects::find()->count();
        $deviceCount = Device::find()->count();
        $contragentCount = Organisation::find()->count();
        $nodesCount = Node::find()->count();
        $sensorChannelsCount = SensorChannel::find()->count();
        $deviceTypeCount = DeviceType::find()->count();
        $usersCount = User::find()->count();

        $last_measures = Measure::find()
            ->where('createdAt > (NOW()-(4*24*3600000));')
            ->count();
        $complete = 0;

        $measures = Measure::find()
            ->orderBy('date')
            ->all();

        $equipments = Device::find()
            ->orderBy('_id DESC')
            ->limit(20)
            ->all();

        $users = User::find()
            ->all();

        /**
         * Работа с картой
         */
        $deviceData = array();
        $devices = Device::find()->select('*')->all();
        $deviceList[] = $devices;
        $deviceCount = count($devices);
        $cnt = 0;
        $equipmentsGroup = 'var devices=L.layerGroup([';
        $equipmentsList = '';
        $default_coordinates = "[55.54,61.36]";
        $coordinates = $default_coordinates;
        foreach ($devices as $device) {
            if ($device["object"]["latitude"] > 0) {
                $equipmentsList .= 'var device'
                    . $device["_id"]
                    . '= L.marker([' . $device["object"]["latitude"]
                    . ',' . $device["object"]["longitude"]
                    . '], {icon: houseIcon}).bindPopup("<b>'
                    . $device["deviceType"]["title"] . '</b><br/>'
                    . $device["object"]->getAddress() . '").openPopup();';
                $coordinates = "[".$device["object"]["latitude"].",".$device["object"]["longitude"]."]";
                if ($coordinates==$default_coordinates && $device["object"]["latitude"]>0) {
                    $coordinates = "[".$device["object"]["latitude"].",".$device["object"]["longitude"]."]";
                }
                if ($cnt > 0) {
                    $equipmentsGroup .= ',';
                }

                $equipmentsGroup .= 'device' . $device["_id"];
                $cnt++;
            }
        }
        $equipmentsGroup .= ']);' . PHP_EOL;

        return $this->render(
            'dashboard',
            [
                'cityCount' => $cityCount,
                'streetCount' => $streetCount,
                'usersCount' => $usersCount,
                'objectCount' => $objectsCount,
                'nodesCount' => $nodesCount,
                'channelsCount' => $sensorChannelsCount,
                'deviceTypeCount' => $deviceTypeCount,
                'deviceCount' => $deviceCount,
                'measures' => $measures,
                'devices' => $devices,
                'users' => $users,
                'devicesList' => $equipmentsList,
                'devicesGroup' => $equipmentsGroup,
                'last_measures' => $last_measures,
                'complete' => $complete,
                'contragentCount' => $contragentCount,
                'currentUser' => $currentUser,
                'searchModel' => $searchModel,
                'coordinates' => $coordinates,
                'dataProvider' => $dataProvider
            ]
        );
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Action error
     *
     * @return string
     */
    public function actionError()
    {
        if (\Yii::$app->getUser()->isGuest) {
            Yii::$app->getResponse()->redirect("/")->send();
        } else {
            $exception = Yii::$app->errorHandler->exception;
            if ($exception !== null) {
                $statusCode = $exception->statusCode;
                $name = $exception->getName();
                $message = $exception->getMessage();
                return $this->render(
                    'error',
                    [
                        'exception' => $exception,
                        'name' => $name . " " . $statusCode,
                        'message' => $message
                    ]
                );
            }
        }

        return '';
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $id
     * @param $title
     * @param $text
     * @param $user
     *
     * @return string
     */
    public static function formEvent($date, $type, $id, $title, $text, $user)
    {
        $event = '<li>';
        if ($type == 'measure')
            $event .= '<i class="fa fa-wrench bg-red"></i>';
        if ($type == 'alarm')
            $event .= '<i class="fa fa-calendar bg-aqua"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:m", strtotime($date)) . '</span>';
        if ($type == 'measure')
            $event .= '<span class="timeline-header" style="vertical-align: middle">
                        <span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;' .
                Html::a('Снято показание &nbsp;',
                    ['/measure/view', 'id' => Html::encode($id)]) . $title . '</span>';

        if ($type == 'alarm')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                Html::a('Зафиксировано событие &nbsp;',
                    ['/alarm/view', 'id' => Html::encode($id)]) . $title . '</span>';

        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     * Displays a timeline
     *
     * @return mixed
     */
    public function actionTimeline()
    {
        $events = [];
        $journals = Journal::find()
            ->orderBy('date DESC')
            ->limit(10)
            ->all();
        foreach ($journals as $journal) {
            $text = '<i class="fa fa-calendar"></i>&nbsp;' . $journal['description'];
            $events[] = ['date' => $journal['date'], 'event' => self::formEvent($journal['date'], 'journal', 0,
                $journal['description'], $text, $journal['user']->name)];
        }

        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
        $today = date("j-m-Y h:m");

        return $this->render(
            'timeline',
            [
                'events' => $sort_events,
                'today_date' => $today
            ]
        );
    }

}

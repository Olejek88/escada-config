<?php
namespace backend\controllers;

use backend\models\MeasureSearch;
use backend\models\RequestSearch;
use backend\models\ThreadsSearch;
use backend\models\UserSearch;
use backend\models\UsersSearch;
use common\components\MainFunctions;
use common\models\City;
use common\models\Defect;
use common\models\DeviceStatus;
use common\models\EquipmentRegister;
use common\models\ExternalEvent;
use common\models\Info;
use common\models\Journal;
use common\models\MeasureType;
use common\models\Node;
use common\models\Orders;
use common\models\OrderStatus;
use common\models\Config;
use common\models\Device;
use common\models\DeviceType;
use common\models\Protocols;
use common\models\LoginForm;
use common\models\Measure;
use common\models\SensorChannel;
use common\models\SensorConfig;
use common\models\Stat;
use common\models\Threads;
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
                        'actions' => ['logout', 'dashboard', 'test', 'timeline'],
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
     * Dashboard
     *
     * @return string
     */
    public function actionDashboard()
    {
        $categories = "";
        $values = "";
        $stat_categories = "";
        $stat_values = "";
        $stat_values2 = "";
        $last_measures = Measure::find()
            ->where(['measureTypeUuid' => MeasureType::POWER])
            ->orderBy('date')
            ->all();
        $cnt=0;
        foreach ($last_measures as $measure) {
            if ($cnt>0) {
                $categories .= ',';
                $values.=',';
            }
            $categories.= "'".$measure['date']."'";
            $values.=$measure['value'];
            $cnt++;
        }

        $stats = Stat::find()
            ->orderBy('changedAt')
            ->all();
        $cnt=0;
        foreach ($stats as $stat) {
            if ($cnt>0) {
                $stat_categories .= ',';
                $stat_values.=',';
                $stat_values2.=',';
            }
            $stat_categories.= "'".$stat['createdAt']."'";
            $stat_values.= $stat['cpu'];
            $stat_values2.= $stat['mem'];
            $cnt++;
        }

        $measures = Measure::find()
            ->where(['measureTypeUuid' => MeasureType::POWER])
            ->andWhere(['type' => 1])
            ->orderBy('date')
            ->all();

        $measureSearchModel = new MeasureSearch();
        $measureDataProvider = $measureSearchModel->search(Yii::$app->request->queryParams);

        $devices = Device::find()->all();
        $threads = Threads::find()->all();
        $info = Info::find()->all();

        $threadSearchModel = new ThreadsSearch();
        $threadDataProvider = $threadSearchModel->search(Yii::$app->request->queryParams);

        $types = DeviceType::find()->indexBy('_id')->all();
        $tree = array();
        foreach ($types as $type) {
            $expanded = true;
            $tree['children'][] = [
                'title' => $type->title,
                'key' => $type->_id,
                'folder' => true,
                'expanded' => $expanded,
            ];
            $devices = Device::find()->where(['deviceTypeUuid' => $type['uuid']])->all();
            foreach ($devices as $device) {
                $childIdx = count($tree['children']) - 1;
                if ($device['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED) {
                    $class = 'critical1';
                } elseif ($device['deviceStatusUuid'] == DeviceStatus::NOT_WORK) {
                    $class = 'critical2';
                } else {
                    $class = 'critical3';
                }
                $tree['children'][$childIdx]['children'][] = [
                    'title' => $device['deviceType']['title'],
                    'status' => '<div class="progress"><div class="'
                        . $class . '">' . $device['deviceStatus']->title . '</div></div>',
                    'register' => $device['port'].' ['.$device['address'].']',
                    'date' => $device['last_date'],
                    'folder' => true
                ];
                $channels = SensorChannel::find()->where(['deviceUuid' => $device['uuid']])->all();
                foreach ($channels as $channel) {
                    $childIdx2 = count($tree['children'][$childIdx]['children']) - 1;
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
                    $tree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
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
            'dashboard',
            [
                'categories' => $categories,
                'values' => $values,
                'stat_categories' => $stat_categories,
                'stat_values' => $stat_values,
                'stat_values2' => $stat_values2,
                'last_measures' => $last_measures,
                'measures' => $measures,
                'stats' => $stats,
                'devices' => $tree,
                'threads' => $threads,
                'info' => $info,
                'threadDataProvider' => $threadDataProvider,
                'measureDataProvider' => $measureDataProvider
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

<?php
/* @var $searchModel backend\models\DeviceSearch */

use common\models\Measure;
use common\models\MeasureType;
use common\models\SensorChannel;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Отчет по потреблению электроэнергии');

$start_date = '2018-12-31';
$end_date = '2021-12-31';
$start_time = '2018-12-31 00:00:00';
$end_time = '2021-12-31 00:00:00';

$type = '';
if (isset($_GET['type']))
    $type = $_GET['type'];
if (isset($_GET['end_time'])) {
    $end_date = $_GET['end_time'];
    $end_time = date('Y-m-d H:i:s', strtotime($end_date));
}
if (isset($_GET['start_time'])) {
    $start_date = $_GET['start_time'];
    $start_time = date('Y-m-d H:i:s', strtotime($start_date));
}

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 40px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => function ($data) {
            return Html::a($data['node']['object']->getAddress(),
                ['/device/dashboard', 'uuid' => $data['uuid']]);
        },
        'header' => 'Адрес',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '100px',
        'value' => function ($data) {
            return $data['name'];
        },
        'header' => 'Название счетчика',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '100px',
        'value' => function ($data) {
            return $data['serial'];
        },
        'header' => 'Серийный №',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'width' => '100px',
        'pageSummary' => false,
        'value' => function ($data) {
            return $data['node']['phone'];
        },
        'header' => 'Тел.номер',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '100px',
        'pageSummary' => true,
        'value' => function ($data) {
            $start_time = '2018-12-31 00:00:00';
            $end_time = '2021-12-31 00:00:00';
            if (isset($_GET['end_time'])) {
                $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
            }
            if (isset($_GET['start_time'])) {
                $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
            }
            $sensorChannel = SensorChannel::find()
                ->where(['deviceUuid' => $data['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::POWER])
                ->one();
            if ($sensorChannel) {
                $value = Measure::getSumMeasureBetweenDates($sensorChannel, $start_time, $end_time, 1);
                return $value;
            }
            return '-';
        },
        'header' => 'Тариф 1',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '100px',
        'pageSummary' => true,
        'value' => function ($data) {
            $start_time = '2018-12-31 00:00:00';
            $end_time = '2021-12-31 00:00:00';
            if (isset($_GET['end_time'])) {
                $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
            }
            if (isset($_GET['start_time'])) {
                $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
            }
            $sensorChannel = SensorChannel::find()
                ->where(['deviceUuid' => $data['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::POWER])
                ->one();
            if ($sensorChannel) {
                $value = Measure::getSumMeasureBetweenDates($sensorChannel, $start_time, $end_time,2);
                return $value;
            }
            return '-';
        },
        'header' => 'Тариф 2',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '100px',
        'pageSummary' => true,
        'value' => function ($data) {
            $start_time = '2018-12-31 00:00:00';
            $end_time = '2021-12-31 00:00:00';
            if (isset($_GET['end_time'])) {
                $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
            }
            if (isset($_GET['start_time'])) {
                $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
            }
            $sensorChannel = SensorChannel::find()
                ->where(['deviceUuid' => $data['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::POWER])
                ->one();
            if ($sensorChannel) {
                $value = Measure::getSumMeasureBetweenDates($sensorChannel, $start_time, $end_time, 3);
                return $value;
            }
            return '-';
        },
        'header' => 'Тариф 3',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '100px',
        'pageSummary' => true,
        'value' => function ($data) {
            $start_time = '2018-12-31 00:00:00';
            $end_time = '2021-12-31 00:00:00';
            if (isset($_GET['end_time'])) {
                $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
            }
            if (isset($_GET['start_time'])) {
                $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
            }
            $sensorChannel = SensorChannel::find()
                ->where(['deviceUuid' => $data['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::POWER])
                ->one();
            if ($sensorChannel) {
                $value = Measure::getSumMeasureBetweenDates($sensorChannel, $start_time, $end_time, 4);
                return $value;
            }
            return '-';
        },
        'header' => 'Тариф 4',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'width' => '100px',
        'pageSummary' => true,
        'value' => function ($data) {
            $start_time = '2018-12-31 00:00:00';
            $end_time = '2021-12-31 00:00:00';
            if (isset($_GET['end_time'])) {
                $end_time = date('Y-m-d H:i:s', strtotime($_GET['end_time']));
            }
            if (isset($_GET['start_time'])) {
                $start_time = date('Y-m-d H:i:s', strtotime($_GET['start_time']));
            }
            $sensorChannel = SensorChannel::find()
                ->where(['deviceUuid' => $data['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::POWER])
                ->one();
            if ($sensorChannel) {
                $value = Measure::getSumMeasureBetweenDates($sensorChannel, $start_time, $end_time, 0);
                return $value;
            }
            return '-';
        },
        'footer' => '0',
        'header' => 'Сумма',
        'format' => 'raw',
    ]
];

echo GridView::widget([
    'id' => 'equipment-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'showPageSummary' => true,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            '<form action="report"><table style="width: 800px; padding: 3px"><tr><td style="width: 300px">' .
            DatePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]).'</td><td style="width: 300px">'.
            DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]).'</td><td style="width: 100px">'.Html::submitButton(Yii::t('app', 'Выбрать'), [
                'class' => 'btn btn-info']).'</td>
                <td style="width: 100px">{export}</td></tr></table></form>',
            'options' => ['style' => 'width:100%']
            ]
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'equipments'
    ],
    'pjax' => true,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Отчет по потреблению электроэнергии',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

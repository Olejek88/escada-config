<?php
/* @var $searchModel backend\models\DeviceSearch */

use common\models\DeviceType;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Оборудование');

$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Название',
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'deviceTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'value' => 'deviceType.title',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип',
        'format' => 'raw',
    ],
    [
        'attribute' => 'deviceUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'value' => 'device.name',
        'filterType' => GridView::FILTER_SELECT2,
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'work',
        'mergeHeader' => true,
        'header' => 'Работа',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '130px',
        'editableOptions' => function ($model, $key, $index, $widget) {
            $work = [
                '0' => 'Остановлен',
                '1' => 'Запущен'
            ];
            return [
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $work,
                'data' => $work
            ];
        },
        'content' => function ($data) {
            $work = [
                '0' => 'Остановлен',
                '1' => 'Запущен'
            ];
            return $work[$data["work"]];
        }
    ],
    [
        'attribute' => 'status',
        'mergeHeader' => true,
        'header' => 'Статус',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '130px',
        'content' => function ($data) {
            if ($data['status']==0)
                return "<span class='badge' style='background-color:] gray; height: 12px; margin-top: -3px'> </span>&nbsp; Остановлен";
            else
                return "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>&nbsp;  Запущен";
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'port',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Порт',
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'editableOptions' => function ($model, $key, $index, $widget) {
            $ports = [
                '/dev/ttyS0' => '/dev/ttyS0',
                '/dev/ttyS1' => '/dev/ttyS1',
                '/dev/ttyS2' => '/dev/ttyS2',
                '/dev/ttyS3' => '/dev/ttyS3',
                '/dev/ttyUSB0' => '/dev/ttyUSB0',
                '/dev/ttyUSB1' => '/dev/ttyUSB1',
                '/dev/ttyUSB2' => '/dev/ttyUSB2',
            ];
            return [
                'header' => 'Порт',
                'size' => 'sm',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $ports,
                'data' => $ports
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'speed',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Скорость',
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'c_time',
        'vAlign' => 'middle',
        'width' => '120px',
        'mergeHeader' => true,
        'header' => 'Время',
        'format' => 'raw',
    ],
    [
        'attribute' => 'message',
        'vAlign' => 'middle',
        'width' => '180px',
        'mergeHeader' => true,
        'header' => 'Сообщение',
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'equipment-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
        /*            Html::a('Добавить недостающие', ['/equipment/new'], ['class'=>'btn btn-success']),*/
            Html::a('Новое', ['/thread/create'], ['class' => 'btn btn-success']),
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'equipments'
    ],
    'pjax' => true,
    'showPageSummary' => false,
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp;  Потоки',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

<?php
/* @var $searchModel backend\models\DeviceSearch */

use common\models\DeviceStatus;
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
        'attribute' => 'deviceTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => 'deviceType.title',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Тип ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/device-type/create?from=device/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'filter' => ArrayHelper::map(DeviceType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => function ($model, $key, $index, $widget) {
            $models = ArrayHelper::map(DeviceType::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'header' => 'Тип оборудования',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $models,
                'data' => $models
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'deviceStatusUuid',
        'header' => 'Статус ' . Html::a('<span class="glyphicon glyphicon-plus"></span>',
                '/device-status/create?from=device/index',
                ['title' => Yii::t('app', 'Добавить')]),
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'width' => '180px',
        'editableOptions' => function () {
            $status = [];
            $list = [];
            $statuses = DeviceStatus::find()->orderBy('title')->all();
            foreach ($statuses as $stat) {
                $color = 'background-color: white';
                if ($stat['uuid'] == DeviceStatus::UNKNOWN ||
                    $stat['uuid'] == DeviceStatus::NOT_MOUNTED)
                    $color = 'background-color: gray';
                if ($stat['uuid'] == DeviceStatus::NOT_WORK)
                    $color = 'background-color: lightred';
                if ($stat['uuid'] == DeviceStatus::WORK)
                    $color = 'background-color: green';
                $list[$stat['uuid']] = $stat['title'];
                $status[$stat['uuid']] = "<span class='badge' style='" . $color . "; height: 12px; margin-top: -3px'> </span>&nbsp;" .
                    $stat['title'];
            }
            return [
                'header' => 'Статус',
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $status,
                'data' => $list
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'interface',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $interfaces = [
                '0' => 'не указан',
                '1' => 'Последовательный порт',
                '2' => 'Zigbee',
                '3' => 'Ethernet'
            ];
            return $interfaces[$data["interface"]];
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'serial',
        'vAlign' => 'middle',
        'width' => '180px',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Серийный',
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'port',
        'vAlign' => 'middle',
        'width' => '180px',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Порт',
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'address',
        'vAlign' => 'middle',
        'width' => '180px',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => 'Адрес',
        'filterInputOptions' => ['placeholder' => 'Любой'],
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
            Html::a('Новое', ['/device/create'], ['class' => 'btn btn-success']),
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Устройства',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

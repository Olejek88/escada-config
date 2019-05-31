<?php
/* @var $searchModel backend\models\SensorChannelSearch */

use common\models\DeviceStatus;
use common\models\MeasureType;
use common\models\SensorChannel;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Каналы измерения');

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
        'mergeHeader' => true,
        'content' => function ($data) {
            return $data->_id;
        }
    ],
    [
        'attribute' => 'deviceUuid',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => 'Устройство',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['device']->getFullTitle();
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'measureTypeUuid',
        'vAlign' => 'middle',
        'width' => '150px',
        'header' => 'Тип измерения',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => function () {
            $types = ArrayHelper::map(MeasureType::find()->orderBy('title')->all(), 'uuid', 'title');
            return [
                'size' => 'md',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $types,
                'data' => $types
            ];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(MeasureType::find()->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
    ],
    [
        'attribute' => 'register',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Регистр',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'changedAt',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Изменение',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'template'=> '{delete}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'requests-table',
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
            Html::a('Новая', ['/request/create'], ['class' => 'btn btn-success'])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'requests'
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
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Каналы измерения',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

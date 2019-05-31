<?php
/* @var $searchModel backend\models\UserSearch */

use common\models\User;
use common\models\Users;
use kartik\editable\Editable;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Управление пользователями');

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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'name',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'type',
        'vAlign' => 'middle',
        'width' => '180px',
        'header' => 'Тип пользователя',
        'mergeHeader' => true,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $widget) {
            if ($model->type==User::ROLE_ADMIN)
                return 'Администратор';
            if ($model->type==User::ROLE_OPERATOR)
                return 'Оператор';
            return 'Пользователь';
        },
        'editableOptions'=> function ($model, $key, $index, $widget) {
            return [
                'header' => 'Тип',
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => [
                    User::ROLE_ADMIN =>'Администратор',
                    User::ROLE_OPERATOR =>'Оператор'
                ]
            ];
        },
    ],
    [
        'attribute' => 'whoIs',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'Должность',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'users-table',
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
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'users'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary'=>'',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'persistResize' => false,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; Пользователи',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

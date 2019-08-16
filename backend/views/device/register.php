<?php
/* @var $provider
 */

use common\models\DeviceRegister;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
$this->title = Yii::t('app', 'Журнал работы устройств');

?>
<div class="info-box">
    <!-- /.box-header -->
    <div class="box-body">
        <?php
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
                'attribute' => 'date',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '100px',
            ],
            [
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '180px',
                'format' => 'raw',
                'header' => 'Адрес',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'content' => function ($data) {
                    return '['.$data['device']->getFullTitle().']';
                }
            ],
            [
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '180px',
                'header' => 'Контроллер',
                'content' => function ($data) {
                    return '['.$data['device']['node']['phone'].']';
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
            ],
            [
                'attribute' => 'description',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '180px',
            ]
        ];

        echo GridView::widget(
            [
                'dataProvider' => $provider,
                'columns' => $gridColumns,
                'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                'containerOptions' => ['style' => 'overflow: auto'],
                'beforeHeader' => [
                    '{toggleData}'
                ],
                'toolbar' => [
                    []
                ],
                'pjax' => true,
                'showPageSummary' => false,
                'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
                'summary' => '',
                'bordered' => true,
                'striped' => false,
                'condensed' => true,
                'responsive' => false,
                'hover' => true,
                'floatHeader' => false,
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<i class="glyphicon glyphicons-spade"></i>&nbsp; Журнал работы',
                    'headingOptions' => ['style' => 'background: #337ab7']
                ],
            ]
        );
        ?>
    </div>
</div>

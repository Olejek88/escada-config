<?php
/* @var $equipmentsCount
 * @var $threadDataProvider
 */

use kartik\grid\GridView;

?>
<div class="info-box">
    <!-- /.box-header -->
    <div class="box-body">
        <?php
        $gridColumns = [
            [
                'attribute' => 'title',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'device.title',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'headerOptions' => ['class' => 'kv-sticky-column'],
                'contentOptions' => ['class' => 'kv-sticky-column'],
            ],
            [
                'attribute' => 'port',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'speed',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'work',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'status',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'c_time',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'message',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'work',
                'vAlign'=>'middle',
            ],
        ];

        echo GridView::widget([
            'dataProvider' => $threadDataProvider,
            'columns' => $gridColumns,
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'beforeHeader' => [
                '{toggleData}'
            ],
            'pjax' => true,
            'showPageSummary' => false,
            'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
            'summary'=>'',
            'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'export' => false,
            'floatHeader' => false,
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; Потоки',
                'headingOptions' => ['style' => 'background: #337ab7']

            ],
        ]);

        ?>
    </div>
</div>

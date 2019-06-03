<?php

use kartik\grid\GridView;

/* @var $measures
 * @var $measureDataProvider
 */
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Архив</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?php
        $gridColumns = [
            [
                'attribute' => 'date',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'value',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'headerOptions' => ['class' => 'kv-sticky-column'],
                'contentOptions' => ['class' => 'kv-sticky-column'],
            ],
            [
                'attribute' => 'sensorChannel.title',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'measureType.title',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ]
        ];

        echo GridView::widget([
            'dataProvider' => $measureDataProvider,
            'columns' => $gridColumns,
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'pjax' => true,
            'showPageSummary' => false,
            'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
            'summary' => '',
            'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'export' => false,
            'floatHeader' => false,
            'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; Архив данных',
                'headingOptions' => ['style' => 'background: #337ab7'],
            ],
            ]);

        ?>
    </div>
</div>
<!-- /.box -->

<?php

use kartik\grid\GridView;

/* @var $searchModel backend\models\EventSearch */
/* @var $warnings string[] */

/* @var $eventsDataProvider */
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">События по обслуживанию системы</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <ul class="products-list product-list-in-box">
            <?php
            $gridColumns = [
                [
                    'attribute' => 'name',
                    'contentOptions' => [
                        'class' => 'table_class'
                    ],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'next_date',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'headerOptions' => ['class' => 'kv-sticky-column'],
                    'contentOptions' => ['class' => 'kv-sticky-column'],
                ],
                [
                    'attribute' => 'last_date',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'contentOptions' => [
                        'class' => 'table_class'
                    ],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'class'=>'kartik\grid\BooleanColumn',
                    'attribute'=>'active',
                    'vAlign'=>'middle',
                ],
            ];

            foreach ($warnings as $warning) {
                if ($warning!='') {
                    echo '<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-ban"></i> Внимание!</h4>';
                    echo $warning;
                    echo '</div>';
                }
            }
            echo GridView::widget([
                'dataProvider' => $eventsDataProvider,
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
                'floatHeader' => false,
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; Запланированные события',
                    'headingOptions' => ['style' => 'background: #337ab7']

                ],
            ]);
            ?>
        </ul>
    </div>
</div>

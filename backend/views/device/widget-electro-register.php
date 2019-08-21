<?php
/* @var $parameters
 * @var $device
 */

use common\models\Device;
use common\models\DeviceRegister;
use common\models\Node;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Журнал работы</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <div class="btn-group">
                <?php echo Html::a("<button type='button' class='btn btn-box-tool'>
                    <i class='fa fa-link'></i></button>", ['/device/register', 'uuid' => $device['uuid']]); ?>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
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
                'dataProvider' => $parameters['register']['provider'],
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
            ]
        );
        ?>
    </div>
</div>

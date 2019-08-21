<?php
/* @var $node
 */

use common\models\Device;
use common\models\DeviceStatus;
use common\models\DeviceType;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

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
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '180px',
                'value' => 'deviceType.title',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'content' => function ($data) {
                    return Html::a($data['deviceType']['title'],['/device/dashboard', 'uuid' => $data['uuid']]);
                }
            ],
            [
                'attribute' => 'deviceStatus',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '130px',
                'content' => function ($data) {
                    $color = 'background-color: white';
                    if ($data['deviceStatusUuid'] == DeviceStatus::UNKNOWN ||
                        $data['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED)
                        $color = 'background-color: gray';
                    if ($data['deviceStatusUuid'] == DeviceStatus::NOT_WORK)
                        $color = 'background-color: lightred';
                    if ($data['deviceStatusUuid'] == DeviceStatus::WORK)
                        $color = 'background-color: green';
                    $status = "<span class='badge' style='" . $color . "; height: 12px; margin-top: -3px'> </span>&nbsp;" .
                        $data['deviceStatus']['title'];
                    return $status;
                },
            ],
            [
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
                },
            ],
            [
                'attribute' => 'port',
                'vAlign' => 'middle',
                'width' => '110px',
                'header' => 'Порт',
                'format' => 'raw',
            ],
            [
                'attribute' => 'address',
                'vAlign' => 'middle',
                'width' => '70px',
                'filterType' => GridView::FILTER_SELECT2,
                'header' => 'Адрес',
                'format' => 'raw',
            ]
        ];

        $devices = Device::find()
            ->where(['nodeUuid' => $device['nodeUuid']])
            ->andWhere(['deviceTypeUuid' => DeviceType::DEVICE_LIGHT]);
        $provider = new ActiveDataProvider(
            [
                'query' => $devices,
                'sort' =>false,
            ]
        );

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
                'pjax' => false,
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
                    'heading' => '<i class="glyphicon glyphicons-spade"></i>&nbsp; Другие светильники',
                    'headingOptions' => ['style' => 'background: #337ab7']
                ],
                'rowOptions' => function($model) {
                    if (isset($_GET['uuid'])){
                        if ($_GET['uuid'] == $model['uuid'])
                            return ['class' => 'danger'];
                    }
                }
            ]
        );
        ?>
    </div>
</div>

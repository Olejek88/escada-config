<?php

use common\models\Device;
use common\models\SensorChannel;

/* @var $model Device */

$sensorChannels = SensorChannel::find()
    ->where(['deviceUuid' => $model['uuid']])
    ->all();
$models = Device::find()->where(['_id' => $model['_id'], 'deleted' => 0])->limit(1)->one();
?>
<div class="kv-detail-content">
    <h3><?php echo "" ?></h3>
    <div class="row">
        <div class="col-sm-2">
            <table class="table table-bordered table-condensed table-hover small kv-table">
            </table>
        </div>
        <div class="col-sm-4">
            <div class="col-sm-3">
                <table class="table table-bordered table-condensed table-hover small kv-table">
                    <tr style="background-color: #e8e8e8">
                        <th colspan="5" class="text-center text-success">Датчики/каналы измерения</th>
                    </tr>
                    <tr class="active">
                        <th class="text-center">#</th>
                        <th>Канал измерения</th>
                        <th>Регистр</th>
                        <th>Тип измерения</th>
                        <th class="text-right">Дата</th>
                    </tr>
                    <?php
                    foreach ($sensorChannels as $sensorChannel) {
                        echo '<tr>
                                          <td class="text-center">' . $sensorChannel["_id"] . '</td>
                                          <td class="text-center">' . $sensorChannel["title"] . '</td>
                                          <td>' . $sensorChannel['register'] . '</td>
                                          <td>' . $sensorChannel['measureType']['title'] . '</td>
                                          <td class="text-right">' . $sensorChannel["createdAt"] . '</td>
                                          </tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
/* @var $device
 * @var $dataAll
 */

use common\models\Device;
use kartik\widgets\DatePicker;
use yii\helpers\Html;

$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');

$start_date = '2018-12-31';
$end_date = '2021-12-31';
$start_time = '2018-12-31 00:00:00';
$end_time = '2021-12-31 00:00:00';

$type = '';
$this->title = Yii::t('app', 'Дневной архив по устройству');

if (isset($_GET['type']))
    $type = $_GET['type'];
if (isset($_GET['end_time'])) {
    $end_date = $_GET['end_time'];
    $end_time = date('Y-m-d H:i:s', strtotime($end_date));
}
if (isset($_GET['start_time'])) {
    $start_date = $_GET['start_time'];
    $start_time = date('Y-m-d H:i:s', strtotime($start_date));
}
if (isset($_GET['uuid'])) {
    $device = Device::find()->where(['uuid' => $_GET['uuid']])->one();
    if ($device)
        $this->title = Yii::t('app', 'Дневной архив по устройству '.$device->getFullTitle());
}

?>
<div class="row">
    <form action="archive-days">
        <table style="width: 800px; padding: 3px; background-color: white; align-content: center">
            <tr><td style="width: 300px">
                <?php echo DatePicker::widget([
                'name' => 'start_time',
                'value' => $start_date,
                'removeButton' => false,
                'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
                ]
                ]).'</td><td style="width: 300px">'.
                DatePicker::widget([
                'name' => 'end_time',
                'value' => $end_date,
                'removeButton' => false,
                'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
                ]
                ]).Html::hiddenInput("uuid", $_GET['uuid']).'</td><td style="width: 100px">'.
                Html::submitButton(Yii::t('app', 'Выбрать'), ['class' => 'btn btn-info']).'</td></tr>' ?>
        </table>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $this->title ?></h3>
            </div>
            <div class="box-body">
                <div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
                    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
                        <thead>
                        <tr class="kartik-sheet-style" style="height: 20px">
                            <th class="text-center kv-align-middle" data-col-seq="0" style="width: 25%;">Дата</th>
                            <th class="text-center kv-align-middle" data-col-seq="1" style="width: 15%;">Тариф1, кВт*ч</th>
                            <th class="text-center kv-align-center kv-align-middle" data-col-seq="2" style="width: 15%;">Тариф2, кВт*ч</th>
                            <th class="text-center kv-align-middle" data-col-seq="3" style="width: 15%;">Тариф3, кВт*ч</th>
                            <th class="text-center kv-align-center kv-align-middle" data-col-seq="4">Тариф4, кВт*ч</th>
                            <th class="text-center kv-align-center kv-align-middle" data-col-seq="5">Сумма, кВт*ч</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($dataAll['days'] as $data) {
                            echo '<tr data-key="1">';
                            echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="0">'.$data['date'].'</td>';
                            if (isset($data['w1']))
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">'.$data['w1'].'</td>';
                            else
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">-</td>';
                            if (isset($data['w2']))
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">'.$data['w2'].'</td>';
                            else
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">-</td>';
                            if (isset($data['w3']))
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">'.$data['w3'].'</td>';
                            else
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">-</td>';
                            if (isset($data['w4']))
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">'.$data['w4'].'</td>';
                            else
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">-</td>';
                            if (isset($data['ws']))
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">'.$data['ws'].'</td>';
                            else
                                echo '<td class="text-center kv-align-center kv-align-middle" data-col-seq="1">-</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

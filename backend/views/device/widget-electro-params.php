<?php
/* @var $device
 * @var $parameters
 */

use common\models\mtm\MtmDevLightConfig;
use kartik\slider\Slider;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Параметры сети</h3>
    </div>
    <div class="box-body">
        <div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
            <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
                <thead>
                <tr class="kartik-sheet-style" style="height: 20px">
                    <th class="text-center kv-align-middle" data-col-seq="0" style="width: 40%;"></th>
                    <th class="text-center kv-align-middle" data-col-seq="1" style="width: 15%;">Фаза 1</th>
                    <th class="text-center kv-align-center kv-align-middle" data-col-seq="2" style="width: 15%;">Фаза 2</th>
                    <th class="text-center kv-align-middle" data-col-seq="3" style="width: 15%;">Фаза 3</th>
                    <th class="text-center kv-align-center kv-align-middle" data-col-seq="4">Сумма</th>
                </tr>
                </thead>
                <tbody>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">Ток, А</td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="1"><?= $parameters['current']['i1']?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="2"><?= $parameters['current']['i2']?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="3"><?= $parameters['current']['i3']?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="4">-</td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">Напряжение U, В</td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="1"><?= $parameters['current']['u1'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="2"><?= $parameters['current']['u2'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="3"><?= $parameters['current']['u3'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="4">-</td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">Мощность P, Вт</td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="1"><?= $parameters['current']['w1'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="2"><?= $parameters['current']['w2'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="3"><?= $parameters['current']['w3'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="4"><?= $parameters['current']['ws'] ?></td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">Частота F, Гц</td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="1"><?= $parameters['current']['f1'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="2"><?= $parameters['current']['f2'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="3"><?= $parameters['current']['f3'] ?></td>
                    <td class="kv-align-center kv-align-middle" data-col-seq="4">-</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

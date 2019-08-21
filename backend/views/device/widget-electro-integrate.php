<?php
/* @var $device
 * @var $parameters
 */

?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Текущие показания</h3>
    </div>
    <div class="box-body">
        <div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
            <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
                <thead>
                <tr class="kartik-sheet-style" style="height: 20px">
                    <th class="text-center kv-align-middle" data-col-seq="0" style="width: 25%;"></th>
                    <th class="text-center kv-align-middle" data-col-seq="1" style="width: 15%;">Тариф1, кВт*ч</th>
                    <th class="text-center kv-align-center kv-align-middle" data-col-seq="2" style="width: 15%;">Тариф2,
                        кВт*ч
                    </th>
                    <th class="text-center kv-align-middle" data-col-seq="3" style="width: 15%;">Тариф3, кВт*ч</th>
                    <th class="text-center kv-align-center kv-align-middle" data-col-seq="4">Тариф4, кВт*ч</th>
                    <th class="text-center kv-align-center kv-align-middle" data-col-seq="5">Сумма, кВт*ч</th>
                </tr>
                </thead>
                <tbody>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">Текущие значения</td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w1']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w2']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w3']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w4']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['ws']['current'] ?></td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">
                        с <?= $parameters['month']['date']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w1']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w2']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w3']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w4']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['ws']['current'] ?></td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">
                        на <?= $parameters['increment']['date']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w1']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w2']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w3']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w4']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['ws']['last'] ?></td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">
                        c <?= $parameters['month']['date']['last'] ?> по <?= $parameters['month']['date']['current'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w1']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w2']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w3']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w4']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['ws']['last'] ?></td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">
                        на <?= $parameters['increment']['date']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w1']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w2']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w3']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['w4']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['increment']['ws']['prev'] ?></td>
                </tr>
                <tr data-key="1">
                    <td class="kv-align-center kv-align-middle" data-col-seq="0">
                        c <?= $parameters['month']['date']['prev'] ?> по <?= $parameters['month']['date']['last'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w1']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w2']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w3']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['w4']['prev'] ?></td>
                    <td class="kv-align-center kv-align-middle"
                        data-col-seq="1"><?= $parameters['month']['ws']['prev'] ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

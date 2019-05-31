<?php

use yii\helpers\Html;

/* @var $sumOrderStatusCount
 * @var $sumOrderStatusCompleteCount
 * @var $sumTaskStatusCount
 * @var $sumTaskStatusCompleteCount
 * @var $sumStageStatusCount
 * @var $sumStageStatusCompleteCount
 * @var $sumOperationStatusCount
 * @var $sumOperationStatusCompleteCount
 * @var $ordersStatusCount
 * @var $sumOrderStatusCount
 * @var $categories
 * @var $values
 */

$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Статистика нарядов</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                <ul class="dropdown-menu" role="menu">
                    <li><?php echo Html::a("Календарь нарядов", ['/orders/calendar']); ?></li>
                    <li><?php echo Html::a("Наряды", ['/orders']); ?></li>
                    <li class="divider"></li>
                    <li><?php echo Html::a("Анализ выполнения", ['/analytics']); ?></li>
                </ul>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    <strong>Расклад нарядов по месяцам за текущий год</strong>
                </p>
                <div class="chart">
                    <div id="container" style="height: 250px;"></div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", function () {
                            Highcharts.chart('container', {
                                data: {
                                    table: 'datatable'
                                },
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: ''
                                },
                                xAxis: {
                                    categories: [<?php echo $categories; ?>]
                                },
                                legend: {
                                    align: 'right',
                                    x: -300,
                                    verticalAlign: 'top',
                                    y: 0,
                                    floating: true,
                                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                                    borderColor: '#CCC',
                                    borderWidth: 1,
                                    shadow: false
                                },
                                tooltip: {
                                    headerFormat: '<b>{point.x}</b><br/>',
                                    pointFormat: '{series.name}: {point.y}<br/>Всего: {point.stackTotal}'
                                },
                                plotOptions: {
                                    column: {
                                        stacking: 'normal',
                                        dataLabels: {
                                            enabled: true,
                                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                        }
                                    }
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: 'Количество нарядов по месяцам'
                                    }
                                },
                                series: [<?php echo $values; ?>]
                            });
                        });
                    </script>
                </div>
                <!-- /.chart-responsive -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>

    <!-- ./box-body -->
    <div class="box-footer">
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <p class="text-center">
                    <strong>Расклад по статусам</strong>
                </p>
                <div class="progress-group">
                    <span class="progress-text">Новых и в процессе</span>
                    <span class="progress-number"><b><?= $ordersStatusCount[0]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-aqua"
                             style="width: <?= number_format($ordersStatusPercent[0], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                    <span class="progress-text">Не выполнено и отменено</span>
                    <span class="progress-number"><b><?= $ordersStatusCount[1]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-red"
                             style="width: <?= number_format($ordersStatusPercent[1], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                    <span class="progress-text">Всего выполнено</span>
                    <span class="progress-number"><b><?= $ordersStatusCount[2]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-green"
                             style="width: <?= number_format($ordersStatusPercent[2], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                    <span class="progress-text">Не закончено</span>
                    <span class="progress-number"><b><?= $ordersStatusCount[3]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-yellow"
                             style="width: <?= number_format($ordersStatusPercent[3], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
            </div>
            <!-- /.col -->
            <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i
                                class="fa fa-caret-up"></i> <?php if ($sumOrderStatusCount > 0) echo number_format($sumOrderStatusCompleteCount * 100 / $sumOrderStatusCount, 2) . '%' ?> </span>
                    <h5 class="description-header"><?= $sumOrderStatusCount ?>
                        / <?= $sumOrderStatusCompleteCount ?></h5>
                    <span class="description-text">Всего нарядов / Выполнено</span>
                </div>
                <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i
                                class="fa fa-caret-left"></i> <?php if ($sumTaskStatusCount > 0) echo number_format($sumTaskStatusCompleteCount * 100 / $sumTaskStatusCount, 2) . '%' ?></span>
                    <h5 class="description-header"><?= $sumTaskStatusCount ?> / <?= $sumTaskStatusCompleteCount ?></h5>
                    <span class="description-text">Задач / Выполнено</span>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i
                                class="fa fa-caret-up"></i> <?php if ($sumStageStatusCount > 0) echo number_format($sumStageStatusCompleteCount * 100 / $sumStageStatusCount, 2) . '%' ?></span>
                    <h5 class="description-header"><?= $sumStageStatusCount ?>
                        / <?= $sumStageStatusCompleteCount ?></h5>
                    <span class="description-text">Этапов / Выполнено</span>
                </div>
                <div class="description-block">
                    <span class="description-percentage text-red"><i
                                class="fa fa-caret-down"></i> <?php if ($sumOperationStatusCount > 0) echo number_format($sumOperationStatusCompleteCount * 100 / $sumOperationStatusCount, 2) ?></span>
                    <h5 class="description-header"><?= $sumOperationStatusCount ?>
                        / <?= $sumOperationStatusCompleteCount ?></h5>
                    <span class="description-text">Операций / Выполнено</span>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-footer -->
</div>

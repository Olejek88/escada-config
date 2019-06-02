<?php

/* @var $categories
 * @var $values
 */

use yii\helpers\Html;

$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Последние измерения</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                <ul class="dropdown-menu" role="menu">
                    <li><?php echo Html::a("Измерения", ['/measures']); ?></li>
                </ul>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    <strong>Потребляемая мощность</strong>
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
                                        text: 'Потребляемая мощность'
                                    }
                                },
                                series: [{
                                    name: 'Потребляемая мощность (кВт/ч)',
                                    data: [<?php echo $values; ?>]
                                }]
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
</div>



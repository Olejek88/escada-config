<?php

/* @var $categories
 * @var $values
 * @var $values2
 */

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Статистика работы</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    <strong>Статистика</strong>
                </p>
                <div class="chart">
                    <div id="container2" style="height: 250px;"></div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", function () {
                            Highcharts.chart('container2', {
                                data: {
                                    table: 'datatable'
                                },
                                chart: {
                                    type: 'line'
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
                                        text: 'Статистика'
                                    }
                                },
                                series: [{
                                    name: 'CPU (%)',
                                    data: [<?php echo $values; ?>]
                                },{
                                    name: 'Memory (%)',
                                    data: [<?php echo $values2; ?>]
                                }]
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

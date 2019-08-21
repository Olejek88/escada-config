<?php
/* @var $device
 * @var $parameters
 */

use yii\helpers\Html;
$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');

?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $parameters['trends']['title'] ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <div class="btn-group">
                <?php echo Html::a("<button type='button' class='btn btn-box-tool'>
                    <i class='fa fa-link'></i></button>", ['/device/trends', 'uuid' => $device['uuid']]); ?>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="chart">
                    <div id="container<?= $parameters['uuid']?>" style="height: 350px;"></div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", function () {
                            Highcharts.chart('container<?= $parameters['uuid']?>', {
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
                                    categories: [<?php echo $parameters['trends']['categories']; ?>]
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
                                    title: {
                                        text: '<?= $parameters['trends']['title'] ?>'
                                    }
                                },
                                series: [{
                                    name: '<?= $parameters['trends']['title'] ?>',
                                    data: [<?php echo $parameters['trends']['values']; ?>]
                                }]
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

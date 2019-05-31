<?php
/*  @var $values */

/*  @var $name string */

use backend\assets\AdminLteAsset;

AdminLteAsset::register($this);
?>

<div class="measured-value-index" style="height: 700px">
    <div id="container" style="min-width:500px; width:49%; height:90%; float:left"></div>
    <div id="container2" style="min-width:500px; width:49%; height:90%; float:right"></div>
</div>

<script src="/js/vendor/lib/HighCharts/highcharts.js"></script>
<script src="/js/vendor/lib/HighCharts/modules/exporting.js"></script>

<script type="text/javascript">
    Highcharts.chart('container', {
        data: {
            table: 'datatable'
        },
        chart: {
            type: 'column'
        },
        title: {
            text: 'График значения'
        },
        xAxis: {
            categories: [
                <?php
                $first = 0;
                $bar = '';
                foreach ($values as $value) {
                    if ($first > 0)
                        $bar .= "," . PHP_EOL;
                    $bar .= '\'' . $value->date . '\'';
                    $first++;
                }
                echo $bar;
                ?>
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: '<?php echo $name ?>'
            }
        },
        series: [{
            <?php
            $first = 0;
            $bar = "name: '" . $name . "',";
            $bar .= "data: [";
            foreach ($values as $value) {
                if ($first > 0)
                    $bar .= "," . PHP_EOL;
                $bar .= $value->value;
                $first++;
            }
            $bar .= "]";
            echo $bar;
            ?>
        }]
    });
</script>

<div id="container" style="width:99%; height: 400px; margin: 0 auto"></div>
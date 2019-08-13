<script type="text/javascript" src="highcharts/jquery.min.js"></script>
<script type="text/javascript">
<?php
 $today=getdate();
 $mn=$today["month"]=5;
 for ($r=0;$r<4;$r++)
    {
     $datss=sprintf ("%d%02d01000000",$today["year"],$mn-1);
     $elec[$r]=$heat[$r]=$voda[$r]=2;
     $query = 'SELECT SUM(value) FROM prdata WHERE type=4 AND prm=14 AND date='.$datss;
     if ($a = mysql_query ($query,$i))
     if ($uy = mysql_fetch_row ($a)) $elec[$r]=$uy[0]+0;
     $query = 'SELECT SUM(value) FROM prdata WHERE type=4 AND prm=13 AND date='.$datss;
     if ($a = mysql_query ($query,$i))
     if ($uy = mysql_fetch_row ($a)) $heat[$r]=$uy[0]+0;
     $query = 'SELECT SUM(value) FROM prdata WHERE type=4 AND prm=11 AND date='.$datss;
     if ($a = mysql_query ($query,$i))
     if ($uy = mysql_fetch_row ($a)) $voda[$r]=$uy[0]+0;
     if ($mn>1) $mn--;
     else { $mn=12; $today["year"]--; }
    }
?>
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container3'
            },
            title: {
                text: null
            },
            xAxis: {
                categories: ['Газ', 'Тепло', 'Вода', 'Электричество']
            },
            yAxis: {
                title: {
                    text: null
                }
            },
            tooltip: {
                formatter: function() {
                    var s;
                    if (this.point.name) { // the pie chart
                        s = ''+
                            this.point.name +': '+ this.y +' fruits';
                    } else {
                        s = ''+
                            this.x  +': '+ this.y;
                    }
                    return s;
                }
            },
            series: [{
                type: 'column',
                name: 'Газ',
                data: [0, 0, 0, 0]
            }, {
                type: 'column',
                name: 'Тепло',
                data: [<?php for ($r=0; $r<4; $r++) { if ($r>0) print ',';  print $heat[$r]; }?>]
            }, {
                type: 'column',
                name: 'Вода',
                data: [<?php for ($r=0; $r<4; $r++) { if ($r>0) print ',';  print $voda[$r]; }?>]
            }, {
                type: 'spline',
                name: 'Электричество',
                data: [<?php for ($r=0; $r<4; $r++) { if ($r>0) print ',';  print $elec[$r]; }?>]
            }, {
                type: 'pie',
                name: 'Total consumption',
                data: [{
                    name: 'Газ',
                    y: 13,
                    color: '#4572A7'
                }, {
                    name: 'Вода',
                    y: 23,
                    color: '#AA4643'
                }, {
                    name: 'Тепло',
                    y: 19,
                    color: '#89A54E'
                }],
                center: [40, 40],
                size: 80,
                showInLegend: false,
                dataLabels: {
                    enabled: false
                }
            }]
        });
    });
    
});
		</script>

<script src="highcharts/js/highcharts.js"></script>
<script src="highcharts/js/modules/exporting.js"></script>
<div id="container3" style="min-width: 380px; height: 200px; margin: 0 auto"></div>

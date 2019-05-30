<script type="text/javascript" src="highcharts/jquery.js"></script>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'line'
            },
            title: {
		<?php
                 print 'text: \''.$name.'\'';
		?>
            },
            xAxis: {
                categories: [<?php
			if ($cnt>100) $cnt=100;
			for ($tn=$cnt,$cn=0; $tn>=0; $tn--)
			if ($data[$tn])
			{
			 if ($cn>0) print ', ';
			 print $date1[$tn];  $cn++;
			}
		  print '],';
		?>
                labels: {
                    rotation: -45,
                    align: 'right',
                    style: {
                        fontSize: '11px',
                        fontFamily: 'Verdana, sans-serif'
                    }}
            },
            yAxis: {
                title: {
                    text: null
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: false
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'eqwewe',
		data: [<?php
			if ($cnt>100) $cnt=100;
			for ($tn=$cnt,$cn=0; $tn>=0; $tn--)
			if ($data[$tn])
			{
			 if ($cn>0) print ', ';
			 print $data[$tn];  $cn++;
			}
		  print '] }]';
		?>
        });
    });
    
});
</script>

<script src="highcharts/js/highcharts.js"></script>
<script src="highcharts/js/modules/exporting.js"></script>
<div id="container" style="min-width: 940px; height: 250px; margin: 0 auto"></div>

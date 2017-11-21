<?php
$project_id = $_GET["project_id"];
require_once DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_risk_monitoring.php";
require_once DP_BASE_DIR . "/modules/risks/controlling/risks_controlling.php";
$rcontrolling = new RisksControlling();
$options = $rcontrolling->getRisksEARCategories($project_id);
$controllerRisk = new ControlRiskMonitoring();
$totalRisk = $controllerRisk->getRiskAmountByProject($project_id);
$totalMaterialized = $controllerRisk->getRisksHighPriorityByProject($project_id);
;
$risksPerEARCategory = $controllerRisk->getRisksCategories($project_id);
$firstQuarter = (int) ($totalRisk / 3);
$thirdQuarter = (int) ($totalRisk / 2) + 1;
?>

<!-- Shows chart of materialized risk --> 
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#container').highcharts({
	
            chart: {
                type: 'gauge',
                plotBorderWidth: 1,
                plotBackgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF4C6'],
                        [0.3, '#FFFFFF'],
                        [1, '#FFF4C6']
                    ]
                },
                plotBackgroundImage: null,
                height: 200
            },
	
            title: {
                text: '<?php echo $AppUI->_("LBL_MONITORING_RISKS"); ?>'
            },
	    
            pane: [{
                    startAngle: -45,
                    endAngle: 45,
                    background: null,
                    center: ['50%', '125%'],
                    size: 300
                }],	    		        
	
            yAxis: [{
                    min: 0,
                    max: <?php echo $totalRisk ?>,
	       
                    minorTickInterval: 'auto',
                    minorTickWidth: 1,
                    minorTickLength: 1,
                    minorTickPosition: 'inside',
                    minorTickColor: '#666',
	
                    tickPixelInterval: 10,
                    tickWidth: 2,
                    tickPosition: 'inside',
                    tickLength: 1,
                    tickColor: '#666',
                    labels: {
                        step: 4,
                        rotation: 'auto',
                        distance:14
                    },
                    title: {
                        text: 'Riscos'
                    },

		   
                    plotBands: [{
                            from: 0,
                            to: <?php echo $firstQuarter ?>,
                            color: '#55BF3B' // green
                        }, {
                            from: <?php echo $firstQuarter ?>,
                            to: <?php echo $thirdQuarter ?>,
                            color: '#DDDF0D' // yellow
                        }, {
                            from: <?php echo $thirdQuarter ?>,
                            to: <?php echo $totalRisk ?>,
                            color: '#DF5353' // red
                        }],   
                    pane: 0,
                    title: {
                        text: '<span style="font-size:12px"><?php echo $AppUI->_("Riscos com alta prioridade"); ?></span>',
                        y: -20
                    }
                }],
	    
            plotOptions: {
                gauge: {
                    dataLabels: {
                        enabled: true
                    },
                    dial: {
                        radius: '100%'
                    }
                }
            },
            series: [{
                    name: '<?php echo $AppUI->_("Riscos com alta prioridade"); ?>',
                    data: [<?php echo $totalMaterialized; ?>],
                    tooltip: {
                        valueSuffix: ' <?php echo $AppUI->_("Riscos com alta prioridade"); ?>'
                    }
                }]
        },
	
        // Draw the chart
        function(chart) {
            setInterval(function() {
                var left = chart.series[0].points[0];
                leftVal=1;
                left.update(leftVal, false);
                chart.redraw();
            }, 5000);
        });
    });
</script>
<script type="text/javascript">
    $(function () {
        // Radialize the colors
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
            return {
                radialGradient: { cx: 0.3, cy: 0.4, r: 0.4 },
                stops: [
                    [0, color],
                    [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                ]
            };
        });
		
        // Build the chart
        $('#risks_ear_pie').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '<?php echo $AppUI->_("LBL_RISKS_EAR_CAREGORY"); ?>'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>' ;
                        }
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: '<?php echo $AppUI->_("LBL_RISKS_EAR_CAREGORY"); ?>',
                    data: [
    <?php
    $i = 1;
    foreach ($risksPerEARCategory as $item) {
        if ($options[$item[0]] != "") {
            ?>
                ["<?php echo $options[$item[0]] ?>", <?php echo $item[1] ?>]
            <?php
            if ($i < count($risksPerEARCategory)) {
                echo ",";
            }
        }
        $i++;
    }
    ?>
                                ]
                            }]
                    });
                });
</script>

<script src="./modules/timeplanning/js/jsLibraries/Highcharts/js/highcharts.js"></script>
<script src="./modules/timeplanning/js/jsLibraries/Highcharts/js/highcharts-more.js"></script>
<script src="./modules/timeplanning/js/jsLibraries/Highcharts/js/modules/exporting.js"></script>
<div id="risks_ear_pie" style="float:left;width: 400px;position: relative;left:100px;margin: 0 auto"></div>
<div id="container" style="width: 300px; margin: 0 auto;float:right;position: relative;right: 100px"></div>
<!-- Show critical risks -->
<?php require_once DP_BASE_DIR . "/modules/risks/view/vw_criticalrisks.php" ?>
<br />
<!-- Show watchlist -->
<?php require_once DP_BASE_DIR . "/modules/risks/view/vw_watchlist.php" ?>
<br />
<?php require_once DP_BASE_DIR . "/modules/risks/view/vw_expired_risks.php" ?>
<!-- Shows lessons learned panel -->
<!--
<input type="button" class="button" value ="Incluir nova lição aprendida" />

<table class="tbl" style="width:80%">
    <tr>
        <td colspan="4">
            Pesquisar: &nbsp;
            <input type="text" class="text" size="95%" /> &nbsp;
            <input type="button" value="Buscar" class="button" /> 
        </td>
    </tr>
    <tr>
        <th></th>
        <th>Nome</th>
        <th>Tipo</th>
        <th>Palavras chave</th>  
    </tr>
    <tr>
        <td><img src="./modules/risks/images/view_icon.gif"></td>
        <td>Desligamento de membro da equipe</td>
        <td>Organizacional</td>
        <td>demissão, desligamento, tercerização, parceiros</td>
    </tr>
    <tr>
        <td><img src="./modules/risks/images/view_icon.gif"></td>
        <td>Validação de requisitos com protótipos</td>
        <td>Acerto</td>
        <td>prototipação, design, escopo, proposta comercial, validação</td>
    </tr>
</table>

<br />

<input type="button" class="button" value ="Relacionar ação de contingência" />
<table class="tbl" style="width:80%">
    <tr>
        <th>Id</th>
        <th>Risco</th>
        <th>Ação de contingência</th> 
    </tr>
    <tr>
        <td colspan="3">
            Nenhuma ação de contingência foi executada.
        </td>
    </tr>
</table>
-->
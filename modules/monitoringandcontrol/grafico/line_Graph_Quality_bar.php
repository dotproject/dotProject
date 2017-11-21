<?php

global $AppUI;	

 require('../jpgraph/src/jpgraph.php');
 require('../jpgraph/src/jpgraph_bar.php');
 require('../jpgraph/src/jpgraph_line.php');
 require('../jpgraph/src/jpgraph_utils.inc.php');

$arQualidade = unserialize(urldecode($_GET["arQualidade"]));
$arLabelBar = unserialize(urldecode($_GET["arLabelBar"]));
$titGrafico = unserialize(urldecode($_GET["titGrafico"]));


$datay=array(35,160,0,0,0,0);
 
 $graph = new Graph(580,250,'auto');
    
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->img->SetMargin(40,30,40,40);

$graph->xaxis->SetTickLabels($gDateLocale->GetShortMonth());
 
$graph->title->Set($titGrafico);
$graph->title->SetFont(FF_FONT1,FS_BOLD);

$arBarPlot = array(); 
$arLabel = array(); 
$count = count($arQualidade);
for($i=0; $i < $count; ++$i) {
    $bplot = new BarPlot($arQualidade[$i]['quantity']);
    $bplot->SetLegend($arQualidade[$i]['name']);

    array_push($arBarPlot, $bplot);
} 

$gbbplot = new AccBarPlot($arBarPlot);
//$gbarplot = new GroupBarPlot(array($gbbplot));
      
$gbarplot = new GroupBarPlot($arBarPlot);       
$gbarplot->SetWidth(0.15);

$graph->Add($gbarplot);
$graph->xaxis->SetTickLabels($arLabelBar);
 
$graph->Stroke();
?>
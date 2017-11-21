<?php
 // content="text/plain; charset=utf-8"
 require('../jpgraph/src/jpgraph.php');
 require('../jpgraph/src/jpgraph_line.php'); 
 require('../jpgraph/src/jpgraph_utils.inc.php'); 

$vlPlanejado = unserialize(urldecode($_GET["vlPlanejado"]));
$vlAgregado = unserialize(urldecode($_GET["vlAgregado"]));
$dtConsultaArray = unserialize(urldecode($_GET["dtConsultaArray"]));
$titGrafico = unserialize(urldecode($_GET["titGrafico"]));
$titVP = unserialize(urldecode($_GET["titVP"]));
$titVA = unserialize(urldecode($_GET["titVA"]));

// Setup the graph
$width = 650; $height = 280; 


$graph = new Graph($width,$height);

$graph->SetScale("textlin");

$theme_class= new UniversalTheme;
$graph->SetTheme($theme_class);

$graph->title->Set($titGrafico);
$graph->SetBox(false);

$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$graph->xaxis->SetTickLabels($dtConsultaArray);
$graph->xaxis->SetLabelAngle(55);
$graph->ygrid->SetFill(false);
$graph->SetMargin(40,150,40,20);

$p1 = new LinePlot($vlPlanejado);
$graph->Add($p1);

$p2 = new LinePlot($vlAgregado);
$graph->Add($p2);

$p1->value->SetFormatCallback('barValueFormat');
$p1->SetColor("#55bbdd");
$p1->SetLegend($titVP);
$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
$p1->mark->SetColor('#55bbdd');
$p1->mark->SetFillColor('#55bbdd');
$p1->value->show();
$p1->SetCenter();

$p2->value->SetFormatCallback('barValueFormat');
$p2->SetColor("#aaaaaa");
$p2->SetLegend($titVA);
$p2->mark->SetType(MARK_UTRIANGLE,'',1.0);
$p2->mark->SetColor('#aaaaaa');
$p2->mark->SetFillColor('#aaaaaa');
$p2->value->SetMargin(14);
$p2->value->show();
$p2->SetCenter();

$graph->legend->SetFrameWeight(1);
$graph->legend->SetColor('#4E4E4E','#00A78A');
$graph->legend->SetLayout('LEGEND_VERT');
$graph->legend->SetPos(0.01,0.5,'right','center');
$graph->legend->SetMarkAbsSize(8);

// Output line
$graph->Stroke();


function barValueFormat($aLabel) {
     return number_format($aLabel, 2, ',', '.'); 
}

?>
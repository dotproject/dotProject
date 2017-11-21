<?php
require_once ($AppUI->getLibraryClass("jpgraph/src/jpgraph"));
require_once ($AppUI->getLibraryClass("jpgraph/src/jpgraph_gantt"));

$display_option="all";
$showAllGantt=1;
$showLabels=0;
$showInactive =0;
$sortTasksByName=0;
$addPwOiD=0; 
$proFilter=-1;
$startDate=strtotime($projectObj->project_start_date);
$startDateText=date("Y-m-d", $startDate);
if(isset($projectObj->project_end_date)){
    $endDate=strtotime($projectObj->project_end_date);
    $endDateText=date("Y-m-d", $endDate);
}else{
   $endDateText=date("Y-m-d");
}
$urlImage=str_replace ("modules/timeplanning/view/export_project_plan", "index.php" , $baseUrl);
$ganttWidth=970;
$imageName="temp/gantt_" .$projectId.".png";
$localImageURL=$baseUrl. "/".$imageName;
//if($_GET["print"]!="1"){
    if(file_exists($imageName)){
        unlink($imageName);
    }
    require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/gantt_chart_generator.php");
    //rotate image
    if (file_exists($imageName)) {
        $degrees = 270;
        $source = imagecreatefrompng($imageName);
        $rotatedImage = imagerotate($source, $degrees, 0,1);
        imagejpeg($rotatedImage, $imageName);
        ?>
        <img src="<?php echo $imageName;?>" style="max-width: 780px; max-height: 730px;" />
        <?php
    }
//}
?>

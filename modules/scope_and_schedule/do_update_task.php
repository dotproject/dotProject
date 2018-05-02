<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');
GLOBAL $AppUI;
$userDateFormat=strtolower($_SESSION["dateFormat"]); 
$dateSeparator= substr($userDateFormat,2,1);
echo "separator: ".$dateSeparator;
echo "date format: ". $userDateFormat;
$dateParts=explode ($dateSeparator,$userDateFormat);
$yearIndex=array_search("yy",$dateParts);
$monthIndex=array_search("mm",$dateParts);
$dayIndex=array_search("dd",$dateParts);

$id=intval(dPgetParam($_POST, "task_id"));
$startDate=dPgetParam($_POST, "start_date");
$endDate=dPgetParam($_POST, "end_date");
$startDateParts=explode($dateSeparator,$startDate);
$endDateParts=explode($dateSeparator,$endDate);

print_r($startDateParts);
print_r($endDateParts);
echo "ID:".$id;
$obj = new CTask();
$obj->load($id);
$obj->_message = "updated";
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	echo "Error";
}else{
	if(sizeof($startDateParts)==3){
		$obj->task_start_date=$startDateParts[$yearIndex]."-".$startDateParts[$monthIndex]."-".$startDateParts[$dayIndex];
		echo "Start: ". $obj->task_start_date;
	}
	if(sizeof($endDateParts)==3){
		$obj->task_end_date=$endDateParts[$yearIndex]."-".$endDateParts[$monthIndex]."-".$endDateParts[$dayIndex];
		echo "End: ". $obj->task_start_date;
	}
    if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
    }else{
		echo "Saved";
	}
}
die();
//$AppUI->redirect();
?>

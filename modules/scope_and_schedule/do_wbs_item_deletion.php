<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once DP_BASE_DIR . "/modules/scope_and_schedule/wbs_item.class.php";

function deleteChildren($wbsItem){
	$list=$wbsItem->loadWBSItems($wbsItem->project_id,$wbsItem->id);
	foreach($list as $child){
		deleteChildren($child);
	}
	$wbsItem->delete($wbsItem->id);
	
	
	//delete related activities
	$tasks=$wbsItem->loadActivities();
	foreach($tasks as $task){
		$task->delete();
	}
	$q = new DBQuery();
	$q->setDelete("project_wbs_tasks");
	$q->addWhere("wbs_item_id=" . $wbsItem->id);
	$q->exec();
}

$id=intval(dPgetParam($_POST, "id"));
$obj = new WBSItem();
$obj->load($id);
$project_id=$obj->project_id;
deleteChildren($obj);

$AppUI->redirect();//'m=projects&a=view&project_id='.$project_id
?>

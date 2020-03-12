<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once DP_BASE_DIR . "/modules/scope_and_schedule/wbs_item.class.php";


$wbs_item_id=intval(dPgetParam($_POST, "wbs_item_id"));
$task_id=intval(dPgetParam($_POST, "task_id"));
$order=intval(dPgetParam($_POST, "order"));

//fix numbering for integer values
$q = new DBQuery();
$q->addQuery("task_id, wbs_item_id");
$q->addTable("project_wbs_tasks");
$q->addWhere("wbs_item_id = $wbs_item_id order by activity_order asc");
$results = db_loadHashList($q->prepare(true), "task_id");
$i=0;
foreach ($results as $data) {
    $q = new DBQuery();
	$q->addTable('project_wbs_tasks');
	$q->addUpdate('activity_order', $i);
	$q->addWhere("task_id = ". $data[0]);
	$q->exec();
    $i++;
}


//move activity that was previously in that position to one before
$q = new DBQuery();
$q->addTable('project_wbs_tasks');
$q->addUpdate('activity_order', $order +  0.1);
$q->addWhere("wbs_item_id = $wbs_item_id and activity_order = $order");
$q->exec();

//put the selected activity in desired position
$q = new DBQuery();
$q->addTable('project_wbs_tasks');
$q->addUpdate('wbs_item_id', $wbs_item_id);
$q->addUpdate('activity_order', $order);
$q->addWhere("task_id = $task_id");
$q->exec();

	
$AppUI->redirect();//'m=projects&a=view&project_id='.$project_id
?>

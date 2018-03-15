<?php
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');
global $AppUI;
$task_id=-1;
$project_id = dPgetParam($_POST, 'project_id');
$description = '';
$work_package_id = dPgetParam($_POST, 'wbs_item_id');


$q = new DBQuery();
$obj = new CTask();


if( $task_id == -1){
	$obj->task_id=null;
	$obj->task_project = $project_id;
	$obj->task_name = $description;
	$obj->task_start_date = date("Y-m-d");
	$obj->task_end_date = date("Y-m-d");
	$obj->task_creator=$AppUI->user_id;
	db_insertObject('tasks', $obj, 'task_id');
	$task_id=$obj->task_id;
	$obj->load($task_id);
	$obj->task_parent=$task_id;
	$obj->store();
}


$q = new DBQuery();
$q->addQuery('task_id');
$q->addTable('project_wbs_tasks');
$q->addWhere('wbs_item_id =' . $work_package_id);
$sql = $q->prepare();
$tasks = db_loadList($sql);
$order= count($tasks);
$q->clear();


$q = new DBQuery();
$q->addQuery('task_id');
$q->addTable('project_wbs_tasks');
$q->addInsert('wbs_item_id', $work_package_id);
$q->addInsert('task_id', $task_id);
$q->addInsert("activity_order",$order);
$q->exec();
$q->clear();
$AppUI->redirect();
?>
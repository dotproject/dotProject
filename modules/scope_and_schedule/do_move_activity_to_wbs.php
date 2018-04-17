<?php
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');
global $AppUI;
$task_id = dPgetParam($_POST, 'task_id');
$work_package_id = dPgetParam($_POST, 'wbs_item_id');

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
<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');

$id=intval(dPgetParam($_POST, "task_id"));
$obj = new CTask();
$obj->load($id);
$obj->delete();

$q = new DBQuery();
$q->setDelete("project_wbs_tasks");
$q->addWhere("task_id=" . $id);
$q->exec();



$AppUI->redirect();//'m=projects&a=view&project_id='.$project_id
?>

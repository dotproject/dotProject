<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');

$taskLogId = dPgetParam($_POST,"task_log_id");

$taskLog = new CTaskLog();
$taskLog->load($taskLogId);
$taskLog->delete();


$AppUI->setMsg($AppUI->_("LBL_ACTIVITY_TASK_LOG_DELETED",UI_OUTPUT_HTML), UI_MSG_OK, true);

$AppUI->redirect("m=projects&a=view&project_id=" . $_POST["project_id"] . "&tab=" . $_POST["tab"]);
?>
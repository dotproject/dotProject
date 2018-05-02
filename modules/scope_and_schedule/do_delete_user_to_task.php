<?php
require_once DP_BASE_DIR . "/modules/scope_and_schedule/task_user_assigment.class.php";
global $AppUI;
$task_id = dPgetParam($_POST, 'task_id');
$user_id = dPgetParam($_POST, 'user_id');
$taskAssigment= new CTaskAssignement();
$taskAssigment->deleteAssignedUsersToTask($task_id,$user_id);
$AppUI->redirect();
?>
<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$del = dPgetParam($_POST, 'del', 0);
$task_id = dPgetParam($_POST, 'task_id', 0);
$user_id = dPgetParam($_POST, 'user_id', 0);
$obj = new CHumanResourceAllocation();
$msg = '';

if (! $obj->bind($_POST)) {
  $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
  $AppUI->redirect();
}

$AppUI->setMsg('Human Resource Allocation');
if ($del) {
//  if (! $obj->canDelete($msg)) {
//   $AppUI->setMsg($msg, UI_MSG_ERROR);
//    $AppUI->redirect();
//  }
  if (($msg = $obj->delete($task_id, $user_id))) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
    $AppUI->redirect();
  } else {
    $AppUI->setMsg('deleted', UI_MSG_ALERT, true);
    $AppUI->redirect();
  }
} else {
  if (($msg = $obj->store($task_id, $user_id))) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
  } else {
    $AppUI->setMsg($_POST['human_resource_allocation_id'] ? 'updated' : 'added', UI_MSG_OK, true);
  }
  $AppUI->redirect();
}
?>

<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');


$id=intval(dPgetParam($_POST, "task_id"));
echo "ID:".$id;
$obj = new CTask();
$obj->load($id);
$obj->_message = "updated";
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	echo "Error";
}else{
    if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
    }else{
		echo "Saved";
	}
}

$AppUI->redirect();
?>

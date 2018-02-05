<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

$id=intval(dPgetParam($_POST, "id"));
$project_id=intval(dPgetParam($_POST, "project_id"));
$obj = new WBSItem();
if ($id) {
    $obj->_message = "updated";
} else {
    $obj->_message = "added";
}

if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
}else{
    if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
    }else{
        $AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK);	
	}
}
$AppUI->redirect('m=projects&a=view&project_id='.$project_id);
?>

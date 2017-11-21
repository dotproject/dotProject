<?php
require_once (DP_BASE_DIR . "/modules/instructor_admin/class.class.php");
$obj = new CClass();
$obj->bind($_POST);
$result=$obj->delete();

if($result>0){
    $AppUI->setMsg("LBL_CLASS_DELETED", UI_MSG_OK, true);
    $AppUI->redirect("m=instructor_admin");
}

?>

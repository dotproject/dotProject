<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/initiating/authoriziation_workflow.class.php");
$initiating_id = intval(dPgetParam($_POST, 'initiating_id', 0));
if($initiating_id>0){
    $obj = new CInitiating();
    $obj->load($initiating_id);
    $obj->initiating_completed = 0;
    $obj->initiating_approved = 0;
    $obj->initiating_authorized = 0;
    $result=$obj->store();
    if(is_null($result)){
        $AppUI->setMsg($AppUI->_("LBL_PROJECT_CHARTER_INCLUDED"), UI_MSG_OK, true);
    }
}   
$AppUI->redirect();
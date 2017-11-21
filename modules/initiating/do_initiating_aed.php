<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/initiating/authoriziation_workflow.class.php");
$initiating_id = intval(dPgetParam($_POST, 'initiating_id', 0));
$del = intval(dPgetParam($_POST, 'del', 0));
$completed = intval(dPgetParam($_POST, 'initiating_completed', 0));
$approved = intval(dPgetParam($_POST, 'initiating_approved', 0));
$authorized = intval(dPgetParam($_POST, 'initiating_authorized', 0));
global $db, $AppUI;
$not = dPgetParam($_POST, 'notify', '0');
if ($not != '0')
    $not = '1';
$obj = new CInitiating();
// convert dates to SQL format first
if ($obj->initiating_start_date) {
    $date = new CDate($obj->initiating_start_date);
    $obj->initiating_start_date = $date->format(FMT_DATETIME_MYSQL);
}
if ($obj->initiating_end_date) {
    $date = new CDate($obj->initiating_end_date);
    $obj->initiating_end_date = $date->format(FMT_DATETIME_MYSQL);
}
if ($initiating_id) {
    $obj->_message = 'updated'; 
} else {
    $obj->initiating_date_create = str_replace("'", '', $db->DBTimeStamp(time()));
    $obj->initiating_create_by = $AppUI->user_id;
    if ($completed) {
        $obj->initiating_completed = 1;
    }
    if ($approved) {
        $obj->initiating_approved = 1;
    }
    if ($authorized) {
        $obj->initiating_authorized = 1;
    }
    $obj->_message = 'added';
}
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}
// delete the item
if ($del) {
    $obj->load($initiating_id);
    if (($msg = $obj->delete())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
        $AppUI->redirect();
    } else {
        if ($not == '1')
            $obj->notify();
        $AppUI->setMsg("deleted", UI_MSG_ALERT, true);
        $AppUI->redirect("m=initiating");
    }
}

//if autorized then copy values to project
if ($_POST["action_authorized_performed"] == "1") {
    require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
    $projectId=$_POST["project_id"];
    $projectObj = new CProject();
    $projectObj->load($projectId);
    $projectObj->project_start_date=$obj->initiating_start_date;
    $projectObj->project_end_date=$obj->initiating_end_date;
    $projectObj->project_owner=$obj->initiating_manager;
    $projectObj->project_target_budget=$obj->initiating_budget;
    $projectObj->project_status=2;// set in the planning phase
    $projectObj->store();
}

if($initiating_id>0){
//update values of authorization woerkflow
    $approvalWorkflow= new CAuthorizationWorkflow();
    $approvalWorkflow->load($initiating_id);

    
    if( is_null($approvalWorkflow->draft_when)){    
        $approvalWorkflow->draft_when=date("Y-m-d H:i:s");
        $approvalWorkflow->draft_by=$AppUI->user_id;
        //new object - force its insertion, to the store method update that. (necessary because it is an weak entity, using a pre-defined ky from the strong entity)
        $approvalWorkflow->insert();
    }
    
    if($obj->initiating_completed==1 && is_null($approvalWorkflow->completed_when) ){
        $approvalWorkflow->completed_when=date("Y-m-d H:i:s");
        $approvalWorkflow->completed_by=$AppUI->user_id;
    }else if ($obj->initiating_completed!=1){
        $approvalWorkflow->completed_when=null;
        $approvalWorkflow->completed_by= null;
    }
    
    if($obj->initiating_approved==1 && is_null($approvalWorkflow->approved_when) ){
        $approvalWorkflow->approved_when=date("Y-m-d H:i:s");
        $approvalWorkflow->approved_by=$AppUI->user_id;
    }else if($obj->initiating_approved!=1 ){
        $approvalWorkflow->approved_when=null;
        $approvalWorkflow->approved_by=null;
    }
    
    if($obj->initiating_authorized==1 && is_null($approvalWorkflow->authorized_when) ){
        $approvalWorkflow->authorized_when=date("Y-m-d H:i:s");
        $approvalWorkflow->authorized_by=$AppUI->user_id;
    }else if($obj->initiating_authorized!=1){
        $approvalWorkflow->authorized_when=null;
        $approvalWorkflow->authorized_by=null;
    }
    
    $approvalWorkflow->update();
    
}
    
     

if (($msg = $obj->store())) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
    $obj->load($obj->initiating_id);
    if ($not == '1')
        $obj->notify();
    $AppUI->setMsg($AppUI->_("LBL_PROJECT_CHARTER_INCLUDED"), UI_MSG_OK, true);
}
$AppUI->redirect();
<?php
global $AppUI;
require_once (DP_BASE_DIR . "/modules/timeplanning/model/acquisition/acquisition_execution.class.php");
$object=new AcquisitionExecution();
$object->bind($_POST);
$projectId=$object->project_id;
$object->store();
$AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK);
$AppUI->redirect("m=projects&a=view&project_id=".$projectId);
?>

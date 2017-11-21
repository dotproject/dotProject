<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/acquisition/acquisition_execution.class.php");
$id= dPgetParam($_POST,"id");
$tab= dPgetParam($_POST,"tab");
$projectId= dPgetParam($_POST,"project_id");
$object  = new AcquisitionExecution (); 
$object->delete($id) ;
$AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_DELETED"), UI_MSG_OK);
$AppUI->redirect("m=projects&a=view&project_id=".$projectId);
?>

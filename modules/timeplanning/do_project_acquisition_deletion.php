<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/acquisition/controller_acquisition_planning.class.php");
$id= dPgetParam($_POST,"id");
$tab= dPgetParam($_POST,"tab");
$projectId= dPgetParam($_POST,"project_id");
$controller  = new ControllerAcquisitionPlanning (); 
$controller->delete($id); 
$AppUI->setMsg($AppUI->_("LBL_ACQUISITION_ITEM_EXCLUDED",UI_OUTPUT_HTML), UI_MSG_OK);
$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&targetScreenOnProject=/modules/timeplanning/view/acquisition/acquisition_planning.php");
?>

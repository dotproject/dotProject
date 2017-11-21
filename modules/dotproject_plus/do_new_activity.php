<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
$ControllerWBSItemActivityRelationship= new ControllerWBSItemActivityRelationship();
$id=-1;
$project_id = dPgetParam($_POST, 'project_id');
$description = $AppUI->_("LBL_NEW_ACTIVITY");
$work_package = dPgetParam($_POST, 'wbs_item_id');
$id_new_activity=$ControllerWBSItemActivityRelationship->insert($id,$description,$work_package,$project_id);

//$AppUI->setMsg("A atividade/projeto/EAP (nome da atividade) foi salva.", UI_MSG_OK, true);

$AppUI->redirect('m=projects&a=view&project_id='.$project_id."&id_new_activity=".$id_new_activity."&work_package_id=".$work_package);
?>
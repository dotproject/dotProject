<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
$project_id = dPgetParam($_POST, 'project_id');
$activities_ids = dPgetParam($_POST, 'activities_ids');
$activities_ids_to_delete=dPgetParam($_POST, 'activities_ids_to_delete');
$ControllerWBSItemActivityRelationship= new ControllerWBSItemActivityRelationship();
if($activities_ids  != ""){
	$activities_ids = explode(",",$activities_ids);
	for($i=0;$i<sizeof($activities_ids );$i++){
		$id=$activities_ids[$i];
		$description = dPgetParam($_POST, 'description_'.$id,array());
		$work_package = dPgetParam($_POST, 'eap_item_'.$id,array());
		if($description!=""){
			$ControllerWBSItemActivityRelationship->insert($id,$description,$work_package,$project_id);
		}
	}
}

//delete activities
if($activities_ids_to_delete  != ""){
	$activities_ids_to_delete = explode(",",$activities_ids_to_delete);
	for($i=0;$i<sizeof($activities_ids_to_delete);$i++){
		$ControllerWBSItemActivityRelationship->delete($activities_ids_to_delete[$i]);
	}
}
$AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK, true);
$AppUI->redirect('m=projects&a=view&project_id='.$project_id);
?>

<?php
	if (!defined('DP_BASE_DIR')) {
		die('You should not access this file directly.');
	}
	require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
	$controllerActivityMDP= new ControllerActivityMDP();
	$project_id = dPgetParam($_POST, 'project_id');
	$tasks_dependencies_ids = dPgetParam($_POST, 'tasks_dependencies_ids');
	$tasks_positions = dPgetParam($_POST, 'tasks_positions');
	$tasks_data=explode("#",$tasks_dependencies_ids);
	for($i=0;$i<sizeof($tasks_data);$i++){
		if($tasks_data[$i]!=""){
			$task_data=explode(":",$tasks_data[$i]);
			$task_id=$task_data[0];
			$dependencies_ids=$task_data[1];
			$controllerActivityMDP->updateDependencies($task_id,$dependencies_ids);
		}
	}
	$tasks_positions_data=explode("#",$tasks_positions);
	for($i=0;$i<sizeof($tasks_positions_data);$i++){
		if($tasks_positions_data[$i]!=""){
			$task_data=explode(":",$tasks_positions_data[$i]);
			$task_id=$task_data[0];
			$position_xy=explode(",",$task_data[1]);
			$controllerActivityMDP->updatePosition($task_id,$position_xy[0],$position_xy[1]);		
		}
	}
        $AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK, true);
	$AppUI->redirect("m=projects&a=view&project_id=".$project_id."&show_external_page=/modules/timeplanning/view/projects_mdp.php");
?>
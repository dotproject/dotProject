<?php
	if (!defined('DP_BASE_DIR')) {
		die('You should not access this file directly.');
	}
	require_once (DP_BASE_DIR . "/modules/scope_and_schedule/controller_activity_mdp.class.php");
	$activity_id = dPgetParam($_POST, 'activity_id');
	$project_id = dPgetParam($_POST, 'project_id');
	$dependency_id = dPgetParam($_POST, 'dependency_id');
   
   
	if($dependency_id!=-1){
	$controllerActivityMDP= new ControllerActivityMDP();
	
	$activity=$controllerActivityMDP->getProjectActivity($activity_id);
	$dependencies=$activity->getDependencies();
	//print_r($dependencies);
	$dependencies[$dependency_id]=$dependency_id;
	//print_r($dependencies);
	$parameter="";
	foreach($dependencies as $dep_id){
		$parameter.=$dep_id.",";       
	}

	$controllerActivityMDP->updateDependencies($activity_id,$parameter);
	$AppUI->setMsg($AppUI->_("Dependency included",UI_OUTPUT_HTML), UI_MSG_OK, true);
	}else{
		$AppUI->setMsg($AppUI->_("No dependency included",UI_OUTPUT_HTML), UI_MSG_ERROR, true);
	}
	$AppUI->redirect();
?>
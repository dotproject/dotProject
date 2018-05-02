<?php
	if (!defined('DP_BASE_DIR')) {
		die('You should not access this file directly.');
	}
	require_once (DP_BASE_DIR . "/modules/scope_and_schedule/controller_activity_mdp.class.php");
	$activity_id = dPgetParam($_POST, 'activity_id');
        $dependency_id = dPgetParam($_POST, 'dependency_id');
        $project_id = dPgetParam($_POST, 'project_id');
        $controllerActivityMDP= new ControllerActivityMDP();
        
        $activity=$controllerActivityMDP->getProjectActivity($activity_id);
        $dependencies=$activity->getDependencies();
        unset($dependencies[$dependency_id]);//remove =entry for $dependency_id
         $parameter="";
        foreach($dependencies as $dep_id){
            $parameter.=$dep_id.",";       
        }
 
        $controllerActivityMDP->updateDependencies($activity_id,$parameter);
        

        $AppUI->setMsg($AppUI->_("Dependency excluded",UI_OUTPUT_HTML), UI_MSG_OK, true);
		$AppUI->redirect();
?>
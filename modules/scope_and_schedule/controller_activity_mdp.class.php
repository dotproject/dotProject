<?php
require_once (DP_BASE_DIR . "/modules/scope_and_schedule/activities_mdp.class.php");
class ControllerActivityMDP {
	
	function ControllerActivityMDP()	{
	}
	
        
        function addDependecy($taskId, $dependencyId){
            //retrieve depencendies
            //update dependency string
            //replace new string indb
        }
        
        function removeDependency(){
            
        }
        
	function updateDependencies($taskId,$dependencies) {
		$activityMDP= new ActivityMDP();
		$activityMDP->updateDependencies($taskId,$dependencies);
	}
	

	function getProjectActivities($projectId){
		$list=array();
		$q = new DBQuery();
		$q->addQuery("t.task_id, t.task_name");
		$q->addTable("tasks", "t");
		$q->addWhere("t.task_project = $projectId and t.task_milestone<>1");     
        $q->addOrder("task_start_date asc");
                
		$sql = $q->prepare();
		$tasks = db_loadList($sql);
		$list= array();
		foreach($tasks as $task){
			$taskObj= new CTask();
			$taskObj->load($task["task_id"]);
			array_push($list,$taskObj);
		}
		return $list;
	}
        
        function getProjectActivity($activityId){
		$q = new DBQuery();
		$q->addQuery("t.task_id, t.task_name");
		$q->addTable("tasks", "t");
		$q->addWhere("t.task_id = $activityId and t.task_milestone<>1");
		$sql = $q->prepare();
		$tasks = db_loadList($sql);
                $activityMDP= new ActivityMDP();
		foreach($tasks as $task){
			$dependencies=array();
			$q = new DBQuery();
			$q->addQuery("t.task_id");
			$q->addTable("tasks", "t");
			$q->addTable("task_dependencies","td");
			$q->addWhere("td.dependencies_task_id = ".$task[0]." AND t.task_id = td.dependencies_req_task_id");
			$sql = $q->prepare();
			$taskDep = db_loadList($sql);
			foreach ($taskDep  as $dep_ids) {
				foreach($dep_ids as $dep_id){
					if(trim($dep_id)!=""){
						$dependencies[$dep_id]=$dep_id;
					}
				}
			}
			$activityMDP->load($task[0],$task[1],$dependencies);
		}
		return $activityMDP;
	}
}

<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/activities_mdp.class.php");
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
	
	function updatePosition($task_id,$x,$y){
		$activityMDP= new ActivityMDP();
		$activityMDP->updatePosition($task_id,$x,$y);
	}
	
	function getProjectActivities($projectId){
		$list=array();
		$q = new DBQuery();
		$q->addQuery("t.task_id, t.task_name");
		$q->addTable("tasks", "t");
                //filter just activities which are derivated from work packages
                $q->addJoin('tasks_workpackages', 'tw', 't.task_id=tw.task_id');
                $q->addJoin('project_eap_items', 'pei', 'pei.id=tw.eap_item_id');
		$q->addWhere("t.task_project = $projectId and t.task_milestone<>1 and pei.is_leaf=1");     
                $q->addOrder("task_start_date asc");
                
		$sql = $q->prepare();
		$tasks = db_loadList($sql);
		foreach($tasks as $task){
			$q = new DBQuery();
			$q->addQuery("t.pos_x, t.pos_y");
			$q->addTable("tasks_mdp", "t");
			$q->addWhere("task_id = ".$task[0]);
			$sql = $q->prepare();
			$posXY = db_loadList($sql);
			$x=-1;
			$y=-1;
			foreach ($posXY as $xy) {
				$x=$xy[0];
				$y=$xy[1];
			}
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
			$activityMDP= new ActivityMDP();
			$activityMDP->load($task[0],$task[1],$x,$y,$dependencies);
			$list[$task[0]]=$activityMDP;
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
			$q = new DBQuery();
			$q->addQuery("t.pos_x, t.pos_y");
			$q->addTable("tasks_mdp", "t");
			$q->addWhere("task_id = ".$task[0]);
			$sql = $q->prepare();
			$posXY = db_loadList($sql);
			$x=-1;
			$y=-1;
			foreach ($posXY as $xy) {
				$x=$xy[0];
				$y=$xy[1];
			}
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
			$activityMDP->load($task[0],$task[1],$x,$y,$dependencies);
		}
		return $activityMDP;
	}
}

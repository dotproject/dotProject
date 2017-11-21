<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_activity_relationship.class.php");
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
class ControllerWBSItemActivityRelationship{
	
	function ControllerWBSItemActivityRelationship(){
           
	}
	
	function insert($id,$description,$work_package,$project_id) {
		$WBSItemActivityRelationship = new WBSItemActivityRelationship();
                $activity_order=sizeof($this->getActivitiesByWorkPackage($work_package))+1;//always insety new activities in the end
		return $WBSItemActivityRelationship->store($id,$description,$work_package,$project_id,$activity_order);
	}
	
	function delete($id){
		$WBSItemActivityRelationship = new WBSItemActivityRelationship();
		$WBSItemActivityRelationship->delete($id);
	}
	
	function getActivitiesByWorkPackage($WBSItemId){
		$list= array();
		$q = new DBQuery();
		$q->addQuery('t.task_id');
		$q->addTable('tasks_workpackages', 't');
		$q->addTable('tasks', 'pt');
		$q->addWhere('eap_item_id = '.$WBSItemId .' and pt.task_id=t.task_id and pt.task_milestone<>1 order by t.activity_order');
		$sql = $q->prepare();
		$tasks = db_loadList($sql);
		foreach ($tasks as $task) {
                    $obj = new CTask();
                    $obj->load($task['task_id']);
                    $list[$task['task_id']]=$obj;
		}
		return $list;
	}
	
	function getAllActivities($project_id){
		$list= array();
		$q = new DBQuery();
		$q->addQuery('pt.task_id');
		$q->addTable('tasks', 'pt');
		$q->addWhere("pt.task_milestone<>1 and pt.task_project=$project_id");
		$sql = $q->prepare();
		$tasks = db_loadList($sql);
		foreach ($tasks as $task) {
                    $obj = new CTask();
                    $obj->load($task['task_id']);
                    $list[$task['task_id']]=$obj;
		}
		return $list;
	}
	
	
}

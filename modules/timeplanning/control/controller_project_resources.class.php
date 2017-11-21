<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_allocated_role_report.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_allocated_nonhuman_resource_report.php");
class ControllerProjectResources {
	
	function ControllerProjectResources(){
	
	}
	
	function getHumanResources($projectId){
		$list=array();
		$q = new DBQuery();
		$q->addQuery("croles.role_name, count(proles.role_id), sum(est.effort)");
		$q->addTable("project_tasks_estimated_roles", 'proles');	
		$q->leftJoin("company_role", "croles", "proles.role_id=croles.id");
		$q->leftJoin("tasks", "t", "t.task_id=proles.task_id and t.task_project=".$projectId);
		$q->leftJoin("project_tasks_estimations", "est", "est.task_id =  t.task_id group by proles.role_id order by croles.role_name");
		$list=array();
		$sql = $q->prepare();
		$items = db_loadList($sql);
		$i=0;
		foreach ($items as $item) {
			$roleName = $item[0];
			$count = $item[1];
			$sum = $item[2];
			$obj= new ProjectAllocatedRole($roleName,$count,$sum);
			$list[$i++]=$obj;
		}
		return $list;
	}
	
	function getNonHumanResources($projectId){
		$list=array();
		$q = new DBQuery();
		$q->addQuery("r.resource_name, count(r.resource_id), rtype.resource_type_name");
		$q->addTable("resource_tasks", 'rt');	
		$q->leftJoin("resources", "r", "rt.resource_id=r.resource_id");
		$q->leftJoin("tasks", "t", "t.task_id= rt.task_id and t.task_project=".$projectId);
		$q->leftJoin("resource_types", "rtype", "rtype.resource_type_id= r.resource_type group by r.resource_id order by rtype.resource_type_name");
		$list=array();
		$sql = $q->prepare();
		$items = db_loadList($sql);
		$i=0;
		foreach ($items as $item) {
			$name = $item[0];
			$quantity = $item[1];
			$type = $item[2];
			$obj= new ProjectAllocatedNonHumanResource($name,$quantity,$type);
			$list[$i++]=$obj;
		}
		return $list;
	}	
}

<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
require_once (DP_BASE_DIR .'/modules/tasks/tasks.class.php');
class WBSItemActivityRelationship {
	
	function WBSItemActivityRelationship (){
	}
	
	function store($id,$description,$work_package,$project_id,$activity_order) {
			$q = new DBQuery();
			$obj = new CTask();
			$q->addQuery('task_id');
			$q->addTable('tasks');
			$q->addWhere('task_id =' . $id);
			$record = $q->loadResult();
			$obj->task_project = $project_id;
			$obj->task_name = $description;
			
			if (empty($record)){
				$obj->task_start_date = date("Y-m-d");
				$obj->task_end_date = date("Y-m-d");
				$obj->task_creator=1;
				$id=db_insertObject('tasks', $obj, 'task_id');
			}else{
				$obj->load($id);
				$q = new DBQuery();
				$q->addTable('tasks', 't');							
				$q->addUpdate('task_name', $description);	
				$q->addWhere('task_id = '.$id);		
				$q->exec();
			}
			$q = new DBQuery();
			$q->addTable('tasks', 't');							
			$q->addUpdate('task_parent', $obj->task_id);	
			$q->addWhere('task_id = '.$obj->task_id);		
			$q->exec();
			
			$q = new DBQuery();
			$q->addQuery('task_id');
			$q->addTable('tasks_workpackages');
			$q->addWhere('task_id =' . $obj->task_id);
			$record = $q->loadResult();
			$q->clear();
			$q->addTable('tasks_workpackages');
			
			if (empty($record)){
				$q->addInsert('eap_item_id', $work_package);
				$q->addInsert('task_id', $obj->task_id);
                                $q->addInsert("activity_order",$activity_order);
			}else{
				$q->addUpdate('eap_item_id', $work_package);
				$q->addWhere('task_id = ' . $obj->task_id);
			}
			$q->exec();
			$q->clear();
                        return $obj->task_id;
	}

	
	function delete($id){
		$q = new DBQuery();
		$q->setDelete('tasks');
		$q->addWhere('task_id=' . $id);
		$q->exec();
	}
	
}

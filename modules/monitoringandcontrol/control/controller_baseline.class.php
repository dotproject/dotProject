<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

/*
DB filelds - TABLE: 'monitoring_baseline'
 ' quality_id, quality_type_id, quality_description, quality_status_id, quality_date_end, task_id, user_id  '
*/
/*
DB filelds - TABLE: 'monitoring_quality_status'
 '  quality_status_id, quality_status_name  '
*/
/*
DB filelds - TABLE: 'monitoring_quality_type'
 '  quality_type_id, quality_type_name  '
*/
/*
	$task_id, $index, $typpe, $description,	$responsable, $status, $date_end, 
*/

	class ControllerBaseline{
	
	
	function getBaselineRequestById($baseline_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('b.baseline_id, b.baseline_name, b.baseline_version, b.baseline_observation');
		$q->addTable('monitoring_baseline', 'b');	
		$q->addWhere('b.baseline_id='.$baseline_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);
                return $list;	
	}
        
        function getCurrentBaseline($project_id){
		$list = array();
		$q = new DBQuery;
		$q->addQuery('baseline_version');
		$q->addTable('monitoring_baseline');
                $q->addWhere("project_id=".$project_id);
		$q->addOrder("baseline_date desc limit 1");
		$sql = $q->prepare();
                //echo $sql;
		$list = db_loadList($sql);
                $version="1.0";
                if (!empty($list)) {
			foreach($list as $row){			
                               $version= $row['baseline_version'];
                        }
                }
		return $version;
	}

	function countBaseline($project_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('b.baseline_id');
		$q->addTable('monitoring_baseline', 'b');	
		$q->addWhere('b.project_id='.$project_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return count($list);	
	}		
	
	function listBaseline($project_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('b.baseline_id, b.baseline_name, b.baseline_version, b.baseline_date');
		$q->addTable('monitoring_baseline', 'b');	
		$q->addWhere('b.project_id='.$project_id);
		$q->addOrder('baseline_date DESC');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}		
		
	function insertRow($project,$name,$version,$observation,$user){
		$dtAtual = date("Y-m-d h:i:s"); 
		
		
		//Grava o baseline
		$q = new DBQuery();
		$q-> addTable('monitoring_baseline');
		$q->addInsert('project_id', $project);
		$q->addInsert('baseline_name', $name);
		$q->addInsert('baseline_version', $version);
		$q->addInsert('baseline_observation', $observation);
		$q->addInsert('user_id', $user);
		$q->addInsert('baseline_date', $dtAtual);
		$q->exec();

		//Obtem o id
		$q = new DBQuery;
		$q->addQuery('max(baseline_id)');
		$q->addTable('monitoring_baseline', 'b');
		$q->addWhere('b.project_id ='.$project);
		$sql = $q->prepare();
		$list = db_loadList($sql);
		$baselineId = $list[0][0];		

		$controllerBaseline = new ControllerBaseline();
		$controllerBaseline->insertBaselineUserCost($baselineId);		
		//Grava o baseline task
		global $AppUI;	
		$controllerBaseline = new ControllerBaseline();
		
		$q = new DBQuery;
		$q->addQuery('t.task_id, t.task_start_date, t.task_duration, t.task_duration_type, t.task_hours_worked, t.task_end_date, t.task_percent_complete');
		$q->addTable('tasks', 't');
		$q->addWhere('t.task_project ='.$project);
		$sql = $q->prepare();
		$list = db_loadList($sql);
	
		if (!empty($list)) {
			foreach($list as $row){			
				$controllerBaseline->insertBaselineTask($baselineId, 
                                $row['task_id'], 
                                $row['task_start_date'], 
                                $row['task_duration'], 
                                $row['task_duration_type'], 
                                $row['task_hours_worked'], 
                                $row['task_end_date'], 
                                $row['task_percent_complete']);  		
			}
		}	
	}		
		
	function insertBaselineTask($baseline_id,$task_id,$task_start_date,$task_duration,$task_duration_type, $task_hours_worked, $task_end_date, $task_percent_complete ){
		//Grava o baseline
		$q = new DBQuery();
		$q-> addTable('monitoring_baseline_task');
		$q->addInsert('baseline_id', $baseline_id);
		$q->addInsert('task_id', $task_id);
		$q->addInsert('task_start_date', $task_start_date);
		$q->addInsert('task_duration', $task_duration);
		$q->addInsert('task_duration_type', $task_duration_type);
		$q->addInsert('task_hours_worked', $task_hours_worked);
		$q->addInsert('task_end_date', $task_end_date);
		$q->addInsert('task_percent_complete', $task_percent_complete);
		$q->exec();

		//Obtem o id
		$q = new DBQuery;
		$q->addQuery('max(l.baseline_task_id)');
		$q->addTable('monitoring_baseline_task', 'l');
		$q->addWhere('l.baseline_id ='.$baseline_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);
		$baselineTaskId = $list[0][0];	

		//Grava o baseline task
		global $AppUI;	
		$controllerBaseline = new ControllerBaseline();
		
		$q = new DBQuery;
		$q->addQuery('l.task_log_id, l.task_log_creator, l.task_log_hours, l.task_log_date');
		$q->addTable('task_log', 'l');
		$q->addWhere('l.task_log_task ='.$task_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);
		
		if (!empty($list)) {
			foreach($list as $row){			
				$controllerBaseline->insertBaselineTaskLog($baselineTaskId, 
													   $row['task_log_id'], 
													   $row['task_log_creator'], 
													   $row['task_log_hours'], 
													   $row['task_log_date']);  		
			}		
		}
	}
	
	function insertBaselineTaskLog($baseline_task_id,$task_log_id,$task_log_creator,$task_log_hours,$task_log_date){

		//Grava o baseline
		$q = new DBQuery();
		$q-> addTable('monitoring_baseline_task_log');
		$q->addInsert('baseline_task_id', $baseline_task_id);
		$q->addInsert('task_log_id', $task_log_id);
		$q->addInsert('task_log_creator', $task_log_creator);
		$q->addInsert('task_log_hours', $task_log_hours);
		$q->addInsert('task_log_date', $task_log_date);
		$q->exec();
	}
	
	function insertBaselineUserCost($baseline_id){

		$q = new DBQuery;
		$q->addQuery('c.cost_id, c.user_id, c.cost_value, c.cost_per_use, c.cost_dt_begin, c.cost_dt_end');
		$q->addTable('monitoring_user_cost', 'c');
		$sql = $q->prepare();
		$list = db_loadList($sql);
		
		if (!empty($list)) {
			foreach($list as $row){			
				$q = new DBQuery();
				$q-> addTable('monitoring_baseline_user_cost');
				$q->addInsert('baseline_id', $baseline_id);
				$q->addInsert('user_id', $row['user_id']);
				$q->addInsert('cost_id', $row['cost_id']);
				$q->addInsert('cost_value', $row['cost_value']);
				$q->addInsert('cost_per_use', $row['cost_per_use']);
				$q->addInsert('cost_dt_begin', $row['cost_dt_begin']);
				$q->addInsert('cost_dt_end', $row['cost_dt_end']);
				$q->exec();		
			}		
		}		
	}
		
	function deleteRow($id){
	
			//Apagar Baseline User Cost
			$q = new DBQuery();
			$q->setDelete('monitoring_baseline_user_cost');
			$q->addWhere('baseline_id=' . $id);
			$q->exec();
			$q->clear();	

			//Apagar Baseline taskLog
			$qSub = new DBQuery;
			$qSub->addQuery('l.baseline_task_id');
			$qSub->addTable('monitoring_baseline_task', 'l');
			$qSub->addWhere('l.baseline_id=' . $id);
			$sql = $qSub->prepare();

			$q = new DBQuery();
			$q->setDelete('monitoring_baseline_task_log');
			$q->addWhere('baseline_task_id in (' . $sql. ')');
			$q->exec();
			$q->clear();			
			
			//Apagar Baseline Task
			$q = new DBQuery();
			$q->setDelete('monitoring_baseline_task');
			$q->addWhere('baseline_id=' . $id);
			$q->exec();
			$q->clear();				
	
			//Apagar Baseline
			$q = new DBQuery();
			$q->setDelete('monitoring_baseline');
			$q->addWhere('baseline_id=' . $id);
			$q->exec();
			$q->clear();	
	}	
			
	function updateRow($baseline_id, $baseline_name, $baseline_version, $baseline_observation){
			$q = new DBQuery();	
			$q-> addTable('monitoring_baseline');
			$q->addUpdate('baseline_name', $baseline_name);
			$q->addUpdate('baseline_version', $baseline_version);
			$q->addUpdate('baseline_observation', $baseline_observation);
			$q->addWhere('baseline_id = '. $baseline_id);
			$q->exec();	
			$q->clear();	
	}	

}
?>
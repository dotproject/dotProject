<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
/*
DB filelds - TABLE: 'monitoring_change_request ' :
'change_id, change_impact, change_status, change_description, change_cause, change_request, change_date_limit, user_id, task_id, meeting_id'
*/

	class ControllerAcaoCorretiva{    
		
	function insert($index,$task_id,$impact,$status,$description,$cause,$acao_corretiva,$user_id,$date_limit,$id_ata,$project_id){
			$controllerUtil = new ControllerUtil();
			$q = new DBQuery(); 		 
			
			for($i=0;$i<count($index);$i++){  	
				$prazo =  $date_limit[$i];
				$date_timestamp = $controllerUtil->data_to_timestamp ($prazo);
				$data_limite = date('Y-m-d',$date_timestamp);
				
				$q-> addTable('monitoring_change_request');
				$q->addInsert('user_id', $user_id[$i]);
				$q->addInsert('task_id', $task_id);
				$q->addInsert('change_impact', $impact[$i]);
				$q->addInsert('change_status', $status[$i]);
				$q->addInsert('change_description', $description[$i]);
				$q->addInsert('change_cause', $cause[$i]);
				$q->addInsert('change_request', $acao_corretiva[$i]);
				$q->addInsert('change_date_limit',$data_limite);
				$q->addInsert('meeting_id', $id_ata);
				$q->addInsert('project_id', $project_id);
				$q->exec();
			}
	}			
	
	function deleteAcao($change_id){
			$q = new DBQuery();
			$q->setDelete('monitoring_change_request');
			$q->addWhere('change_id=' . $change_id);
			$q->exec();
			$q->clear();	
	}	
	

	function updateRow($task_id,$impact,$status,$description,$cause,$acao_corretiva,$user_id,$date_limit,$id_ata,$change_id,$project_id){	
			$controllerUtil = new ControllerUtil();
			$date_limit=  $controllerUtil -> convert_to_datetime($date_limit);
			$q = new DBQuery();	
			$q-> addTable('monitoring_change_request');
			$q->addUpdate('user_id', $user_id);
			$q->addUpdate('task_id', $task_id);
			$q->addUpdate('change_impact', $impact);
			$q->addUpdate('change_status', $status);
			$q->addUpdate('change_description', $description);
			$q->addUpdate('change_cause', $cause);
			$q->addUpdate('change_request', $acao_corretiva);
			$q->addUpdate('change_date_limit', $date_limit);
			$q->addUpdate('meeting_id', $id_ata);
			$q->addUpdate('project_id', $project_id);
			$q->addWhere('change_id = ' . $change_id);			
			$q->exec();	
		}		
		
	function getTaskRecords($change_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('c.project_id');
		$q->addQuery('t.task_name');
		$q->addTable('monitoring_change_request', 'c');	
		$q->LeftJoin('tasks', 't', 't.task_id = c.task_id');
		
		$q->addWhere('c.change_id='.$change_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}
	
	function getTaskRecordsByProject($task_project){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('task_id');
		$q->addQuery('task_name');
		$q->addTable('tasks', 't');	
		$q->addWhere('task_project='.$task_project);
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}

	function getChangeRequest($task_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('change_id, task_id, change_impact, change_status, change_description, change_cause, change_request, change_date_limit, user_id,meeting_id,project_id');
		$q->addTable('monitoring_change_request', 'c');	
		$q->addWhere('task_id='.$task_id);
		$q->addOrder('change_id ASC');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}
	
	function getChangeRequestByProject($project_id) {
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('change_id, task_id, change_impact, change_status, change_description, change_cause, change_request, change_date_limit, user_id,meeting_id');
		$q->addTable('monitoring_change_request', 'c');	
		$q->addWhere('project_id='.$project_id);
		$q->addOrder('change_id ASC');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}	
	
	function getChangeRequestByMeeting($meeting_id) {
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('c.change_id, c.task_id, c.change_impact, c.change_status, c.change_description, c.change_cause, c.change_request, c.change_date_limit, c.user_id, c.meeting_id, u.user_username');
		$q->addTable('monitoring_change_request', 'c');	
		$q->innerJoin('users', 'u', 'c.user_id = u.user_id');
		$q->addWhere('meeting_id='.$meeting_id);
		$q->addOrder('change_id DESC');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}		
	
	function getChangeRequestById($change_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('c.change_id, c.change_impact, c.change_status, c.change_description, c.change_cause, c.change_request, c.change_date_limit, c.user_id, c.meeting_id, c.project_id');
		$q->addQuery('c.task_id, t.task_name');
		$q->addQuery('c.meeting_id, m.dt_meeting_begin');		
		$q->addTable('monitoring_change_request', 'c');
		$q->leftJoin('tasks', 't', 't.task_id = c.task_id');
		$q->leftJoin('monitoring_meeting', 'm', 'm.meeting_id = c.meeting_id');
		$q->addWhere('change_id ='.$change_id);	
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		
		return $list[0];
	}		
}
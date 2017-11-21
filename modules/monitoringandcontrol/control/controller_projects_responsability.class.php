<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
/*
DB filelds - TABLE: 'monitoring_responsibility_matrix'
 ' responsibility_id, responsibility_description, responsibility_user_id_consultation, responsibility_user_id_execut, responsibility_user_id_support, responsibility_user_id_approve,  project_id '
*/
class ControllerProjectsResponsability{
		
	function getRecords($project_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('responsibility_id, responsibility_description, responsibility_user_id_consultation, responsibility_user_id_execut, responsibility_user_id_support, responsibility_user_id_approve,  project_id');
		$q->addTable('monitoring_responsibility_matriz', 'm');
		$q -> addWhere('project_id='. $project_id );					
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;		
	}
	
	
		function getRecordsById($id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('responsibility_id, responsibility_description, responsibility_user_id_consultation, responsibility_user_id_execut, responsibility_user_id_support, responsibility_user_id_approve,  project_id ');
		$q->addTable('monitoring_responsibility_matriz', 'm');	
		$q->addWhere('responsibility_id=' . $id);
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}
	
}
?>
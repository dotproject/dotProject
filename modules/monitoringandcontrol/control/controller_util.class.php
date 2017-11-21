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

	class ControllerUtil{
		
	function getProjectName($project_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addTable('projects', 'p');
		$q->addQuery('p.project_name');
		$q->addWhere('p.project_id = '.$project_id);	
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}
		
	function getTaskName($task_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('t.task_name');
		$q->addQuery('p.project_name');
		$q->addQuery('p.project_id');
		
		$q->addTable('tasks', 't');
		$q->innerJoin('projects', 'p', 'p.project_id = t.task_project');
		$q->addWhere('t.task_id = '.$task_id);	
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}	
	function date_to_hours($data){
		$data = explode('/',$data);
		$h_dia = $data[0]*24;
		$h_mes=  $data[1]*720;
		$h_ano = $data[2]*8760;
		$horas_data =$h_dia+$h_mes+$h_ano ;	
		return $horas_data;
	}
	function convert_to_datetime($date){
			$date	 = str_replace('/','-',$date);
			$dateObj=new DateTime($date);
			$date=$dateObj->format('Y-m-d H:i:s');
			return $date;		
	}
	function convert_to_date($date){
			$date	 = str_replace('/','-',$date);
			$dateObj=new DateTime($date);
			$date=$dateObj->format('Y-m-d');
			return $date;		
	}		
	
	function formatDate($date){
			$dateObj=new DateTime($date);
			$date=$dateObj->format('d/m/Y');	
			return $date;		
	}
	
	function formatDateTime($date){
			$dateObj=new DateTime($date);
			$date=$dateObj->format('d/m/Y h:i:s');	
			return $date;		
	}		
	
	function data_to_timestamp ($data) { 
	  $dataexplode = explode("/", $data);
	  $d = (int)$dataexplode[0];
	  
	  $timestamp = mktime(0, 0, 0, $dataexplode[1], $d, $dataexplode[2]);
	  return $timestamp;	  
	}		
	
	function getUsers(){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('user_id');
		$q->addQuery('user_username');
		$q->addTable('users', 'u');		
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;
	}
	
	
	function setHours(){
		$key = array();
		for($i=0;$i < 24;$i++){
		if ( $i < 10 ) {
				$key[$i] = "0" . $i;
		} else {
				$key[$i] = $i;
		}
		}
			return  $key ;
	}
	
	function setMinutes(){
		 $key = array();
		for($i=0;$i < 60;$i++){
			if ( $i < 10 ) {
				$key[$i] = "0" . $i;
			} else {
				$key[$i] = $i;
			}
		}
			return  $key ;
	}		

	function getHora($date){
		$convdate = explode(' ',$date);
		$horas= explode(":",$convdate[1]);		
		$hr= $horas[0];
		$min= $horas[1];
		$getHora = array(0=>$hr,1=>$min);
		return $getHora ;
	}
	
	function converteData($data,$hr,$min){			
		$data_explode= explode("/",$data);
		$mes = (int)$data_explode[1];
		$dia =  (int)$data_explode[0];
		$ano =  (int)$data_explode[2];	
		$h = (int)$hr;
		$m = (int)$min;			
		$new_data = mktime($h,$m,0,$mes,$dia,$ano);			
		$dt_final = date("Y-m-d G:i:s",$new_data);		
		return  $dt_final;
	}
		
	function getUsername($num){		
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('user_username');
		$q->addTable('users');	
		$q-> addWhere('user_id='. $num);					
		$sql = $q->prepare();
		$list = db_loadList($sql);	
		$username='';
			if(!empty($list)){
				$username = $list[0][user_username];
			}					
		return $username;	
	}
	
   function inverteData($data,$sep1,$sep2){	   
	   return implode($sep2,array_reverse(explode($sep1,$data)));	  	  
	}
}
?>
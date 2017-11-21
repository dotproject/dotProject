<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
$controllerAcao = new ControllerAcaoCorretiva();  
global $AppUI;
/*
DB filelds - TABLE: 'monitoring_change_request ' :
'change_id, change_impact, change_status, change_description, change_cause, change_request, change_date_limit, user_id, task_id, meeting_id'
*/
	$index = dPgetParam($_POST,'index');
	$task_id = dPgetParam($_POST,'task_id');
	$impact = dPgetParam($_POST,'impact');	
	$status = dPgetParam($_POST,'status');
	$description = dPgetParam($_POST,'description');
	$cause = dPgetParam($_POST,'cause');
	$acao_corretiva = dPgetParam($_POST,'acao_corretiva');	
	$user_id = dPgetParam($_POST,'user');	
	$id_ata = dPgetParam($_POST,'ata');
	$date_limit =  dPgetParam($_POST,'date_limit');
	$change_id =  dPgetParam($_POST,'change_id');
	$project_id = dPgetParam( $_POST, 'project_id', 0 );
	

	if (isset($_POST['acao']) && $_POST['acao']=='insert'){	
		$controllerAcao -> insert($index,$task_id,$impact,$status,$description,$cause,$acao_corretiva,$user_id,$date_limit,$id_ata,$project_id);
	} 
	elseif (isset($_POST['acao']) &&  $_POST['acao'] == 'delete'){	
		$controllerAcao -> deleteAcao($change_id);
	}	
	elseif (isset($_POST['acao']) &&  $_POST['acao'] == 'updateRow'){
		$controllerAcao -> updateRow($task_id,$impact,$status,$description,$cause,$acao_corretiva,$user_id,$date_limit,$id_ata,$change_id,$project_id);
	}	
	

	$AppUI ->redirect('m=projects&a=view&project_id='.$project_id);
	
	
	?>
<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
	global $AppUI;
/*
DB filelds - TABLE: 'monitoring_change_request ' :
'change_id, change_impact, change_status, change_description, change_cause, change_request, change_date_limit, user_id, task_id, meeting_id'
*/
$acao = new ControllerAcaoCorretiva();
	
	$id = dPgetParam($_POST,'change_id');
	$status = dPgetParam($_POST,'status');
	$acao_corretiva = dPgetParam($_POST,'acao_corretiva');
	$date_limit = dPgetParam($_POST,'date_limit');
	$user = dPgetParam($_POST,'user');
	$project_id = dPgetParam($_POST,'project_id');

	
if (isset($_POST['acao']) &&  $_POST['acao'] == 'delete'){		
	$acao -> deleteRow($id);	
	}	
	$AppUI ->redirect('m=monitoringandcontrol&a=addedit_ata&project_id='.$project_id);
?>
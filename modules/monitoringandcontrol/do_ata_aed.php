<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_ata.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
$controllerAcaoCorretiva = new ControllerAcaoCorretiva();  
$controllerAta = new ControllerAta();  
$controllerUtil = new ControllerUtil();  

global $AppUI;

/*
DB filelds - TABLE: 'monitoring_meeting' :
`meeting_id`,`project_id`,`dt_meeting_begin`,`ds_title`,`ds_subject`,`dt_meeting_end`
*/
/*
DB filelds - TABLE: 'monitoring_meeting_item' :
`meeting_item_id`,`meeting_item_description` 
*/
/*
DB filelds - TABLE: 'monitoring_meeting_item_select' :
`meeting_item_select_id`,`meeting_item_id`,`meeting_id`,`status`
*/
	// NOVA ATA
	$meeting_id = dPgetParam($_POST,'meeting_id');
	$project_id = dPgetParam($_POST,'project_id');
	$dt_begin = dPgetParam($_POST,'dt_begin');	
	$hr_begin = dPgetParam($_POST,'hr_begin');
	$min_begin = dPgetParam($_POST,'min_begin');
	$hr_end = dPgetParam($_POST,'hr_end');
	$min_end = dPgetParam($_POST,'min_end');	
	$title = dPgetParam($_POST,'title');	
	$subject = dPgetParam($_POST,'subject');
	$participants =  dPgetParam($_POST,'participants');
	$item_id =  dPgetParam($_POST,'meeting_item_id');
	$item_status =  dPgetParam($_POST,'item_select_status');
	$meeting_type =  dPgetParam($_POST,'meeting_type');
	
	$task_id_entrega = dPgetParam($_POST,'task_id_entrega');
	$item_select_status_entrega = dPgetParam($_POST,'item_select_status_entrega');
		
	$percentual =  dPgetParam($_POST,'percentual');
	$tamanho =  dPgetParam($_POST,'tamanho');
	$idc =  dPgetParam($_POST,'idc');
	$idp =  dPgetParam($_POST,'idp');
	$va =  dPgetParam($_POST,'va');
	$vp =  dPgetParam($_POST,'vp');
	$cr =  dPgetParam($_POST,'cr');
	$baseline =  dPgetParam($_POST,'baseline');

	$index = dPgetParam($_POST,'index');
	$task_id = dPgetParam($_POST,'task_id');
	$impact = dPgetParam($_POST,'impact');	
	$status = dPgetParam($_POST,'status');
	$description = dPgetParam($_POST,'description');
	$cause = dPgetParam($_POST,'cause');
	$acao_corretiva = dPgetParam($_POST,'acao_corretiva');	
	$user_id = dPgetParam($_POST,'user');	
	$date_limit =  dPgetParam($_POST,'date_limit');
	$change_id =  dPgetParam($_POST,'change_id');
	
	if($dt_begin == ""){
		 $dt_end = "";
	}
	$dt_end =  $controllerUtil ->converteData($dt_begin,$hr_end,$min_end);
	$dt_begin =  $controllerUtil ->converteData($dt_begin,$hr_begin,$min_begin);	
	
	if (isset($_POST['acao']) && $_POST['acao']=='insert'){	
	
		$controllerAta -> insert($project_id,$dt_begin,$dt_end,$title,$subject, $meeting_type);
		
		$list = $controllerAta -> getMeetingId($project_id);  // pega o 'meeting_id' anterior 
		$id_ata = $list[0][0];
		
		$controllerAta -> insertParticipants($participants,$id_ata);
		$controllerAcaoCorretiva -> insert($index,$task_id,$impact,$status,$description,$cause,$acao_corretiva,$user_id,$date_limit,$id_ata, $project_id);

		if ($meeting_type == 2) { //Entrega 
			$controllerAta -> insertMeetingTask($task_id_entrega,$item_select_status_entrega,$id_ata);	
		}

		if ($meeting_type == 3 || $meeting_type == 5) { //monitoramento
			$controllerAta -> insertMeetingItens($item_id,$item_status,$id_ata);
		} 
		
		if ($meeting_type == 4 || $meeting_type == 5) {// Gerencia Senior
			$controllerAta -> insertMeetingReport($percentual,$tamanho, $idc, $idp, $va, $vp, $cr, $baseline, $id_ata);	
		}
	} 

	if (isset($_POST['acao']) &&  $_POST['acao'] == 'delete'){	
		$controllerAta -> deleteRow($meeting_id);
	}
	
	if (isset($_POST['acao']) &&  $_POST['acao'] == 'updateRow'){
		
		$controllerAta -> updateMeeting($project_id,$dt_begin,$dt_end,$title,$subject,$meeting_id);
		$controllerAta -> updateParticipants($participants,$meeting_id);
		$controllerAta -> updateMeetingItens($item_id,$item_status,$meeting_id);
	}
	if (isset($_POST['acao']) &&  $_POST['acao'] == 'deletePendencia'){	
		$controllerAcaoCorretiva -> deleteAcao($change_id);
	}

	$AppUI ->redirect('m=projects&a=view&project_id='.$project_id);

?>
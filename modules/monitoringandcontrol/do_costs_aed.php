<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
	global $AppUI;
	require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_resources_costs.class.php");
	$resCost = new ControllerResourcesCosts();  
	
	$id = dPgetParam($_POST,'cost_id');
	$tx_pad = dPgetParam($_POST,'tx_pad');
	$use_cost = dPgetParam($_POST,'use_cost');
	$dt_begin = dPgetParam($_POST,'dt_begin');
	$dt_end = dPgetParam($_POST,'dt_end');
	$user_id = dPgetParam($_POST,'user_id');	
	
	if (isset($_POST['acao']) && $_POST['acao']=='insert'){	
		
	$resCost -> insertCosts($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id);
	
	} 
	elseif (isset($_POST['acao']) &&  $_POST['acao'] == 'delete'){	
	
	$resCost -> deleteRow($id);
	
	}	
	elseif (isset($_POST['acao']) &&  $_POST['acao'] == 'updateRow'){
		
	$resCost -> updateRow($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id, $id);
		
	}	
	
	$AppUI ->redirect();
	?>
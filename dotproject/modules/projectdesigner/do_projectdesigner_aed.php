<?php 
global $AppUI;

//Lets store the panels view options of the user:
$pdo = new CProjectDesignerOptions();
$pdo->pd_option_user = $AppUI->user_id;
$pdo->pd_option_view_project = dPgetParam( $_POST, 'opt_view_project', 0 );
$pdo->pd_option_view_gantt = dPgetParam( $_POST, 'opt_view_gantt', 0 );
$pdo->pd_option_view_tasks = dPgetParam( $_POST, 'opt_view_tasks', 0 );
$pdo->pd_option_view_actions = dPgetParam( $_POST, 'opt_view_actions', 0 );
$pdo->pd_option_view_addtasks = dPgetParam( $_POST, 'opt_view_addtsks', 0 );
$pdo->pd_option_view_files = dPgetParam( $_POST, 'opt_view_files', 0 );
$pdo->store();

$elements = $_POST;
$project_id = $elements['project_id']; 

$AppUI->setMsg('Your workspace has been saved', UI_MSG_OK);
$AppUI->redirect('m=projectdesigner&project_id='.$project_id);
?>

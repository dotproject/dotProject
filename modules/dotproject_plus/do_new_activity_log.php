<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

function formatDate($dateText){
    $date = new DateTime();
    if(strlen($dateText)==10){
        $dateParts = explode("/", $dateText);
        $date->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
        $date->setTime(0, 0, 0);
    }
    return $date->format("Y-m-d H:i:s");
}


require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');

$taskLogDescription = dPgetParam($_POST,"task_log_description");
$taskLogActivityId = dPgetParam($_POST, "activity_id");
$taskLogDate = dPgetParam($_POST, "task_log_date_$taskLogActivityId");
$taskLogHours = dPgetParam($_POST, "task_log_hours");
$taskLogCreator = dPgetParam($_POST, "task_log_creator");
$activityConcluded = dPgetParam($_POST, "activity_concluded");

$taskLog = new CTaskLog();
$taskLog->task_log_creator=$taskLogCreator;
$taskLog->task_log_hours=$taskLogHours;
$taskLog->task_log_task=$taskLogActivityId;
$taskLog->task_log_description=$taskLogDescription;
$taskLog->task_log_name=$taskLogDescription;
$taskLog->task_log_date=formatDate($taskLogDate);
$taskLog->store();

$task_percent_complete=50;
if($activityConcluded=="1"){
    $task_percent_complete=100;
}

$task= new CTask();
$task->load($taskLogActivityId);
$task->task_percent_complete=$task_percent_complete;
$task->store();
//$task_hours_worked=sum das horas da tabela de activity log
                        

//1. Save a new activity log (ok)
//2. Update the activity record (ok)
//3. Show the actual start date and end dates (this one just when task is completed) in execution table (computed not stored)


//SELECT min(task_log_date), max(task_log_date) FROM dotproject_plus.dotp_task_log where task_log_task=119;

////4. Fazer popup para o registro das atividades do projeto
//5. Fazer edição e exclusão de activity log (permitido apenas para quem criou o registro)
// - mostrar lista de activity logs logo abaixo dos dados da activity
//6. Trabalhar o módulo de monitoring e controlling: Análise de valor agregado, tempo e custo

$AppUI->setMsg($AppUI->_("LBL_ACTIVITY_TASK_LOG_REGISTERED"), UI_MSG_OK, true);

$AppUI->redirect("m=projects&a=view&project_id=" . $_POST["project_id"] . "&tab=" . $_POST["tab"]);
?>
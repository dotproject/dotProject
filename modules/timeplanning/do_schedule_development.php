<?php
//echo "START:";
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
$project_id = dPgetParam($_POST, 'project_id');
//echo "Project id:".$project_id."<br/>";
$controllerActivityMDP = new ControllerActivityMDP();
$activities=$controllerActivityMDP->getProjectActivities($project_id);

$activitiesPending=array();
$activitiesUpdated=array();
$inicialActivities=array();
foreach($activities as $activity){
	if( count($activity->getDependencies())>0 ){
		$activitiesPending[$activity->getId()]=$activity;
	}else{
		$inicialActivities[$activity->getId()]=$activity;
	}
}
$project=new CProject();
$project->load($project_id);
//echo "Project Date:".$project->project_start_date;
$date = new DateTime($project->project_start_date);//project start date else now
foreach($inicialActivities as $activity){
	updateActivity($date,$activity->getId());
	$activitiesUpdated[$activity->getId()]=$activity;
}

$anyProcessed=true;
while(count($activitiesPending) > 0 && $anyProcessed){
	$anyProcessed=false;
	//echo "<br/>Pending Activities " . count($activitiesPending)."<br/>" ;
	$activitiesToRemove=array();
	foreach($activitiesPending as $activity){
		$dependencies=$activity->getDependencies();
		$canBeExecuted=true;
		//echo "<br>Activity: ". $activity->getId(). " - ";
		foreach($dependencies as $id){ 
			if( $activitiesPending[$id] != "" ){
				$canBeExecuted=false;
				//echo "Activity can't be executed.";
			}
		}
		
		if($canBeExecuted){
			// echo "Activity can be executed.";
			 updateActivity($date,$activity->getId());
			 $activitiesToRemove[$activity->getId()]=$activity->getId();
			 $activitiesUpdated[$activity->getId()]=$activity;
		}
		//echo "<br/>";
	}
	
	if(count($activitiesToRemove)>0){
            $anyProcessed=true;
	}
	
	foreach($activitiesToRemove as $id){
            unset($activitiesPending[$id]);
	}
}

$AppUI->setMsg($AppUI->_("LBL_SCHEDULE_COMPUTED_SUCCESSFULLY"), UI_MSG_OK, true);
//$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
//$AppUI->setMsg("Project deleted", UI_MSG_ALERT);
$AppUI->redirect('m=projects&a=view&project_id='.$project_id);

function updateActivity($date,$taskId){
	//echo "<br> Start Date:".$date->format('Y-m-d H:i:s')."<br>";
	$estimations= new ProjectTaskEstimation();
	$obj = new CTask();
	$obj->load($taskId);
	$estimations->load($taskId);
	$weekDay=$date->format('l');
	controlHolidays($date);
	$obj->task_start_date=$date->format('Y-m-d H:i:s');
	$unit="day";
	switch ($estimations->getEffortUnit()) {
		case 0:
			$unit="hour";
			break;
		case 1:
			 $unit="minute";
			break;
		case 2:
			$unit= "day";
			break;
	}
	$days=0;
	$minutes=0;
	$hours=0;
	
	if($unit=="hour"){
		if($estimations->getEffort()>8){
			$days=floor($estimations->getEffort()/8);
			$hours=$estimations->getEffort() % 8;
		//	echo "<br>Effort: ".$estimations->getEffort();
		//	echo "<br>Days:".$days;
		//	echo "<br>Hours: ".$hours;
		}else{
			$hours=$estimations->getEffort();
		}
	}
	
	if($unit=="day"){
		$days=$estimations->getEffort();
		$hours=0;
	}
	
	if($unit=="minute"){
            if($estimations->getEffort()>(60*24)){
                $days=floor($estimations->getEffort()/(60*24));
                $minutes=$estimations->getEffort() % (60*24);
                if($minutes>60){
                    $hours=floor($minutes/60);
                    $minutes=$minutes%60;
                }
            }
	}
	for($i=0;$i<$days;$i++){
            $date->modify("+". "1" ." day");
            controlHolidays($date);		
	}
	$date->modify("+". $hours ." hour");
	$date->modify("+". $minutes ." minute");
	//echo "<br> End Date:".$date->format('Y-m-d H:i:s')."<br>";
	$obj->task_duration=($days*8)+$hours +($minutes/60);
	$obj->task_duration_type='1';
	$obj->task_end_date=$date->format('Y-m-d H:i:s');
	$obj->store();
}

function controlHolidays($date){
		$weekDay=$date->format('l');
		if($weekDay=="Saturday"){
			$date->modify("+". "2" ." day");//Goto to monday again
			//echo "<br>Fixed Saturday<br>";
		}else if($weekDay=="Sunday"){
			$date->modify("+". "1" ." day");
			//echo "<br>Fixed Sunday<br>";
		}
}
?>
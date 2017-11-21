<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_minute.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
require_once (DP_BASE_DIR . "/modules/human_resources/configuration_functions.php");
require_once (DP_BASE_DIR . "/modules/human_resources/human_resources.class.php");
set_time_limit(300);
//save
$project_id = $_POST['project_id'];
$task_id=$_POST["activity_id"];
$description=$_POST["activity_description_id_".$task_id];
$owner_id=$_POST["task_owner_".$task_id];
$effort = $_POST["planned_effort_$task_id"];
$effortUnit = $_POST["planned_effort_unit_$task_id"];
$startDate = $_POST["planned_start_date_activity_$task_id"];
$endDate = $_POST["planned_end_date_activity_$task_id"];
$task_percent_complete=$_POST["task_percent_complete"];
$tab=$_POST["tab"];
//update duration and dates
$duration = updateActivity($startDate, $endDate, $task_id,$owner_id,$description,$task_percent_complete);

$rolesIds = array();
$rolesHRs = array();
$rolesQuantity = array();
$excludeRolesIds = array();
$numRoles = intval($_POST["roles_num_$task_id"]);

if ($_POST["estimatedRolesExcludedIds_$task_id"] != "") {
    $excludeRolesIds = explode(",", $_POST["estimatedRolesExcludedIds_$task_id"]);
}

$allRolesIds= explode(";",$_POST["estimatedRolesIds_".$task_id]);
$allRolesHR= explode(";", $_POST["estimatedRolesHR_".$task_id]);

for ($i = 0; $i <= $numRoles; $i++) {
    if (strpos($_POST["estimatedRolesExcluded_$task_id"], $i . "") === false) {
        if($allRolesIds[$i]!=""){
            $rolesIds[$i] = $allRolesIds[$i];
            $rolesHRs[$i] = $allRolesHR[$i];
            $rolesQuantity[$i]=1;
        }
    }
}

//print_r($rolesIds);
//print_r($rolesHRs);

$projectTaskEstimation = new ProjectTaskEstimation();
$projectTaskEstimation->store($task_id, $duration, $effort, $effortUnit, $rolesIds, $rolesQuantity, $excludeRolesIds);

//save HR allocation
$countControl=array();
for ($i = 0; $i < sizeof($rolesIds); $i++) {
    if($rolesHRs[$i]!=""){
        if(!isset($countControl[$rolesIds[$i]])){
            $countControl[$rolesIds[$i]]=0;
        }else{
            $countControl[$rolesIds[$i]]++;
        }
        $user_id=getUserIdByHR($rolesHRs[$i]);
        //Get id of a possible old allocation to delete it
        $q = new DBQuery();
        $q->addTable("human_resource_allocation");
        $q->addQuery("human_resource_allocation_id");
        $q->addWhere("project_tasks_estimated_roles_id=".$rolesIds[$i]." and human_resource_id=".$rolesHRs[$i]);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        foreach ($records as $record) {
            $allocationRoleHR= new CHumanResourceAllocation();
            $allocationRoleHR->load($record[0]);
            $allocationRoleHR->delete($task_id, $user_id);
        }
        
        //Get if of the estimated role for the task
        $task_estimative_id=-1;
        $rolesIds[$i];
        $q = new DBQuery();
        $q->addTable("project_tasks_estimated_roles");
        $q->addQuery("id");
        $q->addWhere("role_id=".$rolesIds[$i]." and task_id=".$task_id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        $task_estimative_id=$records[$countControl[$rolesIds[$i]]][0];
        if($task_estimative_id!=-1){
            //save the new estimative
            $allocationRoleHR= new CHumanResourceAllocation();
            $allocationRoleHR->human_resource_id=$rolesHRs[$i];
            $allocationRoleHR->project_tasks_estimated_roles_id=$task_estimative_id;
            $allocationRoleHR->store($task_id, $user_id);
        }
        
    }

}

$AppUI->setMsg($AppUI->_("LBL_THE_ACTIVITY"). " ($description) ". $AppUI->_("LBL_WAS_SAVED"), UI_MSG_OK, true);
$AppUI->redirect('m=projects&a=view&project_id=' . $project_id);

/**
 * Inputs are informed using dd/mm/yyyy style
 * This method stores the start and end dates and calculates activity duration
 */
function updateActivity($startDateTxt, $endDateTxt, $taskId,$owner,$description, $task_percent_complete) {
    $obj = new CTask();
    $obj->load($taskId);
    $dateStart = null;
    $dateEnd = null;
    $duration = 0;
    $obj->task_percent_complete=$task_percent_complete;
    $calculateDuratation = true;
    if ($startDateTxt != "") {
        $dateStart = new DateTime();
        $dateParts = explode("/", $startDateTxt);
        $dateStart->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
        $dateStart->setTime(0, 0, 0);
        $obj->task_start_date = $dateStart->format("Y-m-d H:i:s");
        $d1 = mktime(0, 0, 0, (int) $dateParts[1], (int) $dateParts[0], (int) $dateParts[2]);
    } else {
        $obj->task_start_date = null;
        $calculateDuratation = false;
    }
    if ($endDateTxt != "") {
        $dateEnd = new DateTime();
        $dateParts = explode("/", $endDateTxt);
        $dateEnd->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
        $dateEnd->setTime(0, 0, 0);
        $obj->task_end_date = $dateEnd->format("Y-m-d H:i:s");
        $d2 = mktime(0, 0, 0, (int) $dateParts[1], (int) $dateParts[0], (int) $dateParts[2]);
    } else {
        $obj->task_end_date = null;
        $calculateDuratation = false;
    }

    if ($calculateDuratation) {
        //$interval = $dateEnd->diff($dateStart);
        //$duration = $interval->format("%d")+1;
        $duration = floor(($d2 - $d1) / 86400);
        $duration++; //add 1 more day to include the start date.
        $obj->task_duration = $duration;
        $obj->task_duration_type = "24"; //This type means the duration is estimated in days
    }
    $obj->store();
    //ensure tasks dates are updated
    $q = new DBQuery();
    $q->addTable("tasks");
    $q->addUpdate("task_name", $description);
    $q->addUpdate("task_owner", $owner);
    $q->addUpdate("task_start_date", $obj->task_start_date);
    $q->addUpdate("task_end_date", $obj->task_end_date);
    $q->addWhere("task_id = " . $taskId);
    $q->exec();
    return $duration;
}
?>
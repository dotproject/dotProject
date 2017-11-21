<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_minute.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');
set_time_limit(300);
//save
$description = $_POST['description'];
if ($description == "") {
    $description = $_POST['description_edit'];
}
$project_id = $_POST['project_id'];
$date = $_POST['date'];
$isEffort = $_POST['isEffort'];
$isDuration = $_POST['isDuration'];
$isResource = $_POST['isResource'];
$isSize = $_POST['isSize'];
$id = $_POST['minute_id'];
$tab = $_POST['tab'];
$members = $_POST['membersIds'];
$pos = strpos($members, ",");
if ($pos === false) {
    $memberId = $members;
    $members = array();
    $members[$memberId] = $memberId;
} else {
    $members = explode(",", $members);
}

$action = $_POST['action_estimation'];
if ($action == "saveEstimationsData") {
    $q = new DBQuery();
    $q->addQuery('t.task_id');
    $q->addTable('tasks', 't');
    $q->addWhere('task_project = ' . $project_id);
    $sql = $q->prepare();
    $tasks = db_loadList($sql);
    foreach ($tasks as $task) {
        $task_id = $task['task_id'];
        $effort = $_POST["planned_effort_$task_id"];
        $effortUnit = $_POST["planned_effort_unit_$task_id"];
        $startDate = $_POST["planned_start_date_activity_$task_id"];
        $endDate = $_POST["planned_end_date_activity_$task_id"];
        //update duration and dates
        $duration = updateActivity($startDate, $endDate, $task_id);
        $rolesIds = array();
        $rolesQuantity = array();
        $excludeRolesIds= array();
        $numRoles = intval($_POST["roles_num_$task_id"]);
        
        if($_POST["estimatedRolesExcludedIds_$task_id"]!=""){
            $excludeRolesIds= explode(",",$_POST["estimatedRolesExcludedIds_$task_id"]);
        }
        for ($i = 0; $i <= $numRoles; $i++) {
            if (strpos($_POST["estimatedRolesExcluded_$task_id"], $i . "") === false) {
                $rolesIds[$i] = $_POST["estimated_role_" . $task_id . "_" . $i];
                $rolesQuantity[$i] = $_POST["estimated_role_quantity_" . $task_id . "_" . $i];
            }
        }
        $projectTaskEstimation = new ProjectTaskEstimation();
        $projectTaskEstimation->store($task_id, $duration, $effort, $effortUnit, $rolesIds, $rolesQuantity, $excludeRolesIds);
    }
 
    $q = new DBQuery();
    $q->addQuery('t.id, t.item_name,t.identation,t.number,is_leaf');
    $q->addTable('project_eap_items', 't');
    $q->addWhere("project_id = $project_id and is_leaf='1' order by sort_order");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    foreach ($items as $item) {
        $eapItemId = $item['id'];
        $size = $_POST["estimated_size_".$eapItemId];
        $sizeUnit = $_POST["estimated_size_unit_".$eapItemId];
        $eapItem = new WBSItemEstimation();
        $eapItem->store($eapItemId, $size, $sizeUnit);
    }
    $AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK);
    $AppUI->redirect('m=projects&a=view&project_id=' . $project_id . "&show_external_page=/modules/timeplanning/view/projects_estimations_minutes.php&tab=" . $tab);
} else {
    if ($action == "read") {
        $AppUI->redirect('m=projects&a=view&project_id=' . $project_id . "&tab=" . $tab . "&minute_id=" . $id . "&action_estimation=read&show_external_page=/modules/timeplanning/view/projects_estimations_minutes.php");
    } else {
        $projectMinute = new ProjectMinute();
        if ($action == "delete") {
            $projectMinute->delete($id);
            $AppUI->setMsg($AppUI->_("Ata de reunião de estimativa excluída.",UI_OUTPUT_HTML), UI_MSG_OK);
        } else {
            $projectMinute->store($description, $date, $project_id, $id, $isEffort, $isDuration, $isResource, $isSize, $members);
            $AppUI->setMsg($AppUI->_("Ata de reunião de estimativa registrada.",UI_OUTPUT_HTML), UI_MSG_OK);
        }
        
        $AppUI->redirect('m=projects&a=view&project_id=' . $project_id . "&show_external_page=/modules/timeplanning/view/projects_estimations_minutes.php&tab=" . $tab);
    }
}

/**
 * Inputs are informed using dd/mm/yyyy style
 * This method stores the start and end dates and calculates activity duration
 */
function updateActivity($startDateTxt, $endDateTxt, $taskId) {
    $obj = new CTask();
    $obj->load($taskId);
    $dateStart = null;
    $dateEnd = null;
    $duration = 0;
    $calculateDuratation = true;
    if ($startDateTxt != "") {
        $dateStart = new DateTime();
        $dateParts = explode("/", $startDateTxt);
        $dateStart->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
        $dateStart->setTime(0, 0, 0);
        $obj->task_start_date = $dateStart->format("Y-m-d H:i:s");
        $d1=mktime(0,0,0,(int)$dateParts[1],(int)$dateParts[0],(int)$dateParts[2]);
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
        $d2=mktime(0,0,0,(int)$dateParts[1],(int)$dateParts[0],(int)$dateParts[2]);
    } else {
        $obj->task_end_date = null;
        $calculateDuratation = false;
    }
      
    if ($calculateDuratation) {
        //$interval = $dateEnd->diff($dateStart);
        //$duration = $interval->format("%d")+1;
        $duration = floor(($d2-$d1)/86400);
        $duration++;//add 1 more day to include the start date.
        $obj->task_duration =  $duration;
        $obj->task_duration_type = "24"; //This type means the duration is estimated in days
    }
    $obj->store();
    //ensure tasks dates are updated
    $q = new DBQuery();
    $q->addTable("tasks");
    $q->addUpdate("task_start_date", $obj->task_start_date );
    $q->addUpdate("task_end_date", $obj->task_end_date);
    $q->addWhere("task_id = ".$taskId);
    $q->exec();
    return $duration;
}
?>


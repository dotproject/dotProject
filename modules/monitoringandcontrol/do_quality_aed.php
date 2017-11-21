<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
global $AppUI;
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_resources_costs.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_quality.class.php");
$resCost = new ControllerResourcesCosts();
$quality = new ControllerQuality();

$id = dPgetParam($_POST, 'quality_id');
$task_id = dPgetParam($_POST, 'task_id');
$index = dPgetParam($_POST, 'index');
$typpe = dPgetParam($_POST, 'typpe');
$description = dPgetParam($_POST, 'description');
$responsable = dPgetParam($_POST, 'responsable');
$status = dPgetParam($_POST, 'status');
$date_end = dPgetParam($_POST, 'date_end');

if (isset($_POST['acao']) && $_POST['acao'] == 'insert') {

    $quality->insert($typpe, $description, $responsable, $status, $date_end, $task_id);
} elseif (isset($_POST['acao']) && $_POST['acao'] == 'delete') {

    $quality->deleteRow($id);
} elseif (isset($_POST['acao']) && $_POST['acao'] == 'updateRow') {

    $quality->updateRecords($typpe, $description, $responsable, $status, $date_end, $task_id, $id);
}
$AppUI->redirect('m=tasks&a=view&task_id=' . $task_id);
?>
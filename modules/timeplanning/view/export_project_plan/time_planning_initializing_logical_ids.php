<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
global $activitiesIdsForDisplay; //variable initializated in export.php
$controllerActivityMDP = new ControllerActivityMDP();
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
$items = $controllerWBSItem->getWorkPackages($projectId);
foreach ($items as $item) {
    $char = 97;
    $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($item->getId());
    foreach ($tasks as $taskDP) {
        $task = $controllerActivityMDP->getProjectActivity($taskDP->task_id);
        $activitiesIdsForDisplay[$task->getId()] = $item->getNumber() . "." . chr($char++);
    }
}
?>
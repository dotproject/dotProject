<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
global $activitiesIdsForDisplay;
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
?>
<table class="printTable">
    <tr>
        <th>
            <?php echo $AppUI->_("LBL_ID",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_ACTIVITY",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_DURATION",UI_OUTPUT_HTML) ?> (<?php echo $AppUI->_("LBL_PROJECT_DAYS",UI_OUTPUT_HTML); ?>)
        </th>
    </tr>
    <?php
    $items = $controllerWBSItem->getWorkPackages($projectId);
    foreach ($items as $item) {
        $id = $item->getId();
        $eapItem = new WBSItemEstimation();
        $eapItem->load($id);
        $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
        foreach ($tasks as $obj) {
            $task_id = $obj->task_id;
            $taskDescription = $obj->task_name;
            $projectTaskEstimation = new ProjectTaskEstimation();
            $projectTaskEstimation->load($task_id);
            //duration and start/end dates.
            $obj = new CTask();
            $obj->load($task_id);
            $startDateTxt = "";
            $endDateTxt = "";
            if (isset($obj->task_start_date) && isset($obj->task_end_date)) {
                $startDateTxt = date("d/m/Y", strtotime($obj->task_start_date));
                $endDateTxt = date("d/m/Y", strtotime($obj->task_end_date));
            }
            $duration = "";
            if ($projectTaskEstimation->getDuration() != "") {
                $duration = $projectTaskEstimation->getDuration() . " ". $AppUI->_("LBL_PROJECT_DAYS_MULT",UI_OUTPUT_HTML);
            }
                ?>
                <tr>
                    <td><?php echo $activitiesIdsForDisplay[$task_id]; ?></td>
                    <td><?php echo $taskDescription ?></td>
                    <td><?php echo $duration ?> </td>            
                </tr>
                <?php
            
        }
    }
    ?>
</table>
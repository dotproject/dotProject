<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
global $activitiesIdsForDisplay; //variable declared in export.php
$controllerActivityMDP = new ControllerActivityMDP();
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
$items = $controllerWBSItem->getWorkPackages($projectId);
?>
<table class="printTable">
    <tr>   
        <th> ID </th>
        <th> <?php echo $AppUI->_("LBL_PROJECT_SEQUENCING_ACTIVITIES",UI_OUTPUT_HTML); ?> </th>
        <th> <?php echo $AppUI->_("LBL_PROJECT_SEQUENCING_DEPENDENCIES",UI_OUTPUT_HTML); ?> </th>
    </tr>
    <?php
    foreach ($items as $item) {
        $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($item->getId());
        ?>
        <tr>
            <td colspan="3">
                <?php
                echo $item->getNumber() . " " . $item->getName();
                ?>
            </td>
        </tr>
        <?php
        foreach ($tasks as $taskDP) {
            $task = $controllerActivityMDP->getProjectActivity($taskDP->task_id);
            ?>

            <tr>
                <td>
                    <?php
                    echo $activitiesIdsForDisplay[$task->getId()];
                    ?>
                </td>
                <td> <?php echo $task->getName(); ?></td>
                <td>
                    <?php
                    foreach ($task->getDependencies() as $dep_id) {
                        $projectActivity = new CTask();
                        $projectActivity->load($dep_id);
                        echo $activitiesIdsForDisplay[$projectActivity->task_id] . ".";
                        echo $projectActivity->task_name;
                        echo "<br/>";
                    }
                }
                ?>
                <?php
            }
            ?>
            &nbsp;
        </td>
    </tr>
</table>
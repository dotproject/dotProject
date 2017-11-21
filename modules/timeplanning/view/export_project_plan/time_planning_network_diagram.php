<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
global $activitiesIdsForDisplay; //variable initialized in export.php
$controllerActivityMDP = new ControllerActivityMDP();
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
?>

<table class="printTable">
    <tr>
        <th>
            <?php echo $AppUI->_("LBL_ID", UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_ACTIVITY", UI_OUTPUT_HTML); ?>
        </th>
        <th >
            <?php echo $AppUI->_("Date Begin", UI_OUTPUT_HTML); ?>
        </th>
        <th >
            <?php echo $AppUI->_("Date End", UI_OUTPUT_HTML) ?>
        </th>
        <!--
        <th >
        <?php //echo $AppUI->_("LBL_PROJECT_SEQUENCING_DEPENDENCIES"); ?>
        </th>
        -->
    </tr>
    <?php
    $q = new DBQuery();
    $q->addQuery("t.task_id, t.task_name");
    $q->addTable("tasks", "t");
    $q->addWhere("t.task_milestone<>1 and t.task_project=" . $projectId. " order by task_start_date asc");
    
    $sql = $q->prepare();
    $tasks = db_loadList($sql);
    foreach ($tasks as $task) {
        $task = $controllerActivityMDP->getProjectActivity($task[0]);
        $taskName = $task->getName();
        $taskId = $task->getId();
        $obj = new CTask();
        $obj->load($taskId);
        $startDateTxt = "";
        $endDateTxt = "";
        if (isset($obj->task_start_date) && isset($obj->task_end_date)) {
            $startDateTxt = date("d/m/Y", strtotime($obj->task_start_date));
            $endDateTxt = date("d/m/Y", strtotime($obj->task_end_date));
        }
        $dependencies = "";
        foreach ($task->getDependencies() as $dep_id) {
            $obj = new CTask();
            $obj->load($dep_id);
            $depTaskId = $obj->task_id;
            $depTaskDescription = $obj->task_name;
            $dependencies.= $depTaskId . ". " . $depTaskDescription . "<br/>";
        }
        if($activitiesIdsForDisplay[$taskId]!=""){ //this is not an orphan activity, and has an WBS item as parent
        ?>
        <tr>
            <td ><?php echo $activitiesIdsForDisplay[$taskId]; ?></td>
            <td ><?php echo $taskName; ?></td>
            <td >
                <?php echo $startDateTxt; ?>
            </td>
            <td>
                <?php echo $endDateTxt; ?>
            </td>
            <!--
            <td>
            <?php // echo $dependencies; ?>&nbsp;
            </td>
            -->
        </tr>
        <?php
        }
    }
   
    ?>
</table>
<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_minute.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_project_minute.class.php");
$controllerProjectMinute = new ControllerProjectMinute();
$minuteId = dPgetParam($_GET, "minute_id", 0);
$date = "";
$description = "";
if ($minuteId != "-1" && $minuteId != "") {
    $projectMinute = new ProjectMinute();
    $projectMinute->load($minuteId);
    $date = $projectMinute->getDate();
    $description = $projectMinute->getDescription();
    $isEffort = $projectMinute->isEffort();
    $isDuration = $projectMinute->isDuration();
    $isResource = $projectMinute->isResource();
    $isSize = $projectMinute->isSize();
    $members = $projectMinute->getMembers();
}
?>

<script src="./modules/timeplanning/js/estimations.js"></script>

<?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/project_mitute_form.php"); ?>
<table class="printTable" >
    <caption> <b><?php echo $AppUI->_("LBL_MINUTES",UI_OUTPUT_HTML); ?></b></caption>
    <tr>
        <th>
            <?php echo $AppUI->_("LBL_ID",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_DATE",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_DESCRIPTION",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <?php
    $minutes = $controllerProjectMinute->getProjectMinutes($projectId);
    foreach ($minutes as $minute) {
        ?>
        <tr>
            <?php
            $date = $minute->getDate();
            $id = $minute->getId();
            $description = $minute->getDescription();
            ?>
            <td><?php echo $id; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo $description; ?></td>
        </tr>
    <?php } ?>
</table>
<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
$controllerCompanyRole = new ControllerCompanyRole();
$items = $controllerWBSItem->getWorkPackages($projectId);
//start: build the roles list
$roles = $controllerCompanyRole->getCompanyRoles($obj->project_company);
?>
<script>
    var roleIds=new Array();
    roleNames=new Array();   
<?php
$i = 0;
foreach ($roles as $role) {
    $roles[$role->getId()] = $role->getDescription();
    ?>
            roleNames[<?php echo $i ?>]="<?php echo $role->getDescription() ?>";
            roleIds[<?php echo $i ?>]="<?php echo $role->getId() ?>";
    <?php
    $i++;
}
//end: build the roles list
?>
</script>
<table class="printTable">
    <caption> <b><?php echo $AppUI->_("LBL_ESTIMATIONS",UI_OUTPUT_HTML); ?></b></caption>
    <tr>
        <th>
            <?php echo $AppUI->_("LBL_WBS",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_ID",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_ACTIVITY",UI_OUTPUT_HTML); ?>
        </th>
        <th >
            <?php echo $AppUI->_("LBL_EFFORT",UI_OUTPUT_HTML); ?>
        </th>
        <th >
            <?php echo $AppUI->_("LBL_DURATION",UI_OUTPUT_HTML) . " " . $AppUI->_("LBL_IN_DAYS",UI_OUTPUT_HTML); ?>
        </th>
        <th colspan="2">
            <?php echo $AppUI->_("LBL_RESOURCES",UI_OUTPUT_HTML); ?>
        </th>
        <th nowrap>
            <?php echo $AppUI->_("LBL_SIZE",UI_OUTPUT_HTML); ?> /  <?php echo $AppUI->_("LBL_METRIC",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <?php
    $items = $controllerWBSItem->getWBSItems($projectId);
    foreach ($items as $item) {
        $id = $item->getId();
        $name = $item->getName();
        $identation = $item->getIdentation();
        $number = $item->getNumber();
        $is_leaf = $item->isLeaf();
        //add decomposed activities
        if ($is_leaf == "1") {
            ?>
            <tr ><td colspan="7"><?php echo $number . " - " . $name ?></td>
                <?php
                //start: add column for size estimation
                $eapItem = new WBSItemEstimation();
                $eapItem->load($id);
                ?>
                <td nowrap>
                    <?php echo $eapItem->getSize() ?> |
                    <?php echo $eapItem->getSizeUnit() ?>
                </td>
                <?php
                //end: add column for size estimation
                //start: code to filter workpakage activities
                $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                $hasActivities = false;
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
                        $duration = "(" . $projectTaskEstimation->getDuration() . " dias)";
                    }

                    if ($taskDescription != "") { //start: build line for task
                        //metric index is db key
                        $effortMetrics = array();
                        $effortMetrics[0] = $AppUI->_("LBL_EFFORT_HOURS",UI_OUTPUT_HTML);
                        $effortMetrics[1] = $AppUI->_("LBL_EFFORT_MINUTES",UI_OUTPUT_HTML);
                        $effortMetrics[2] = $AppUI->_("LBL_EFFORT_DAYS",UI_OUTPUT_HTML);
                        ?>
                    <tr>
                        <td></td>
                        <td ><?php echo $task_id ?></td>
                        <td width="200" ><?php echo $taskDescription ?></td>
                        <td >    
                            <?php echo $projectTaskEstimation->getEffort() ?>
                            <?php
                            $i = 0;
                            foreach ($effortMetrics as $metric) {
                                if ($i == $projectTaskEstimation->getEffortUnit()) {
                                    echo $metric;
                                }
                                $i++;
                            }
                            ?>
                        </td>
                        <td >
                            <?php echo $startDateTxt ?>
                            <br/>
                            <?php echo $endDateTxt ?>
                            <br/>
                            <?php echo $duration ?>
                        </td>
                        <td colspan="2">
                            <?php
                            foreach ($projectTaskEstimation->getRoles() as $role) {
                                echo $roles[ $role->getRoleId() ] . " - " . $role->getQuantity() ."<br/>";
                            }
                            ?>
                        </td>
                        <td>
                        &nbsp;
                        </td>
                    </tr>
                    <?php
                    //end: build line for task
                }
            }
            //end: code to filter workpackages activities
        } else {
            ?>
            <tr>
                <td colspan="8"><?php echo $number . " - " . $name ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
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
$project_id = dPgetParam($_GET, "project_id", 0);
$items = $controllerWBSItem->getWorkPackages($project_id);
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
<a name="estimation_form_anchor"></a>
<table align="center" width="95%">
    <tr>
        <td>
            <input type="button" class="button" value="<?php echo $AppUI->_("LBL_SAVE") . " " . $AppUI->_("LBL_ESTIMATIONS"); ?>" onclick="saveEstimationsData()" />
        </td>
        <td align="center">
            <span id="estimation_form_error_message" style="color: #A00000;display:none"><?php echo $AppUI->_("LBL_ESTIMATION_FORM_ERROR_MESSAGE") ?></span>
        </td>
    </tr>
</table>
<table class="std" align="center" width="95%">
    <caption> <b><?php echo $AppUI->_("LBL_ESTIMATIONS"); ?></b></caption>
    <tr>
        <th>
            <?php echo $AppUI->_("LBL_WBS"); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_ID"); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_ACTIVITY"); ?>
        </th>
        <th nowrap>
            <?php echo $AppUI->_("LBL_EFFORT"); ?>
        </th>
        <th nowrap>
            <?php echo $AppUI->_("LBL_DURATION") . " " . $AppUI->_("LBL_IN_DAYS"); ?>
            &nbsp; | &nbsp; 
            <?php echo $AppUI->_("LBL_DATE_FORMAT"); ?>
        </th>
        <th colspan="2" nowrap>
            <?php echo $AppUI->_("LBL_RESOURCES"); ?>
        </th>
        <th nowrap>
            <?php echo $AppUI->_("LBL_SIZE"); ?> /  <?php echo $AppUI->_("LBL_METRIC"); ?>
        </th>
    </tr>


    <?php
    $items = $controllerWBSItem->getWBSItems($project_id);
    foreach ($items as $item) {
        $id = $item->getId();
        $name = $item->getName();
        $identation = $item->getIdentation();
        $number = $item->getNumber();
        $is_leaf = $item->isLeaf();
        //add decomposed activities
        if ($is_leaf == "1") {
            ?>
            <tr bgcolor="#E8E8E8"><td colspan="7"><?php echo $number . " - " . $name ?></td>
                <?php
                //start: add column for size estimation
                $eapItem = new WBSItemEstimation();
                $eapItem->load($id);
                ?>
                <td nowrap>
                    <input type="text" size="8" class="text" name="estimated_size_<?php echo $id ?>" value="<?php echo $eapItem->getSize() ?>" tabindex="100" />
                    <input type="text" class="text" name="estimated_size_unit_<?php echo $id ?>"  maxlength="30" size="25"  value="<?php echo $eapItem->getSizeUnit() ?>" tabindex="100"/>
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
                        $duration = "(" . $projectTaskEstimation->getDuration() . " days)";
                    }

                    if ($taskDescription != "") { //start: build line for task
                        //metric index is db key
                        $effortMetrics = array();
                        $effortMetrics[0] = $AppUI->_("LBL_EFFORT_HOURS");
                        $effortMetrics[1] = $AppUI->_("LBL_EFFORT_MINUTES");
                        $effortMetrics[2] = $AppUI->_("LBL_EFFORT_DAYS");
                        ?>
                    <tr>
                        <td></td>
                        <td valign="top"><?php echo $task_id ?></td>
                        <td width="200" valign="top"><?php echo $taskDescription ?></td>
                        <td valign="top" nowrap>    
                            <input type="text" class="text" name="planned_effort_<?php echo $task_id ?>" value="<?php echo $projectTaskEstimation->getEffort() ?>" size="8" maxlength="8" tabindex="200"/>
                            <select class="text" name="planned_effort_unit_<?php echo $task_id ?>"  tabindex="200">
                                <?php
                                $i = 0;
                                foreach ($effortMetrics as $metric) {
                                    $selected = $i == $projectTaskEstimation->getEffortUnit() ? "selected" : "";
                                    echo "<option value=\"$i\" $selected>$metric</option>";
                                    $i++;
                                }
                                ?>
                            </select>
                        </td>
                        <td valign="top" nowrap>
                            <input type="text" class="text" name="planned_start_date_activity_<?php echo $task_id ?>" placeholder="dd/mm/yyyy" size="12" maxlength="10" value="<?php echo $startDateTxt ?>" tabindex="300" /> 
                            &nbsp;
                            <input type="text" class="text" name="planned_end_date_activity_<?php echo $task_id ?>" placeholder="dd/mm/yyyy"  size="12" maxlength="10"  value="<?php echo $endDateTxt ?>" tabindex="300" />
                            &nbsp;
                            <?php echo $duration ?>
                        </td>


                        <td valign="top" nowrap>
                            <input type="button" value="+" onclick=addEstimatedRole("<?php echo $task_id ?>","",1) class="button" />
                            <div id="div_res_<?php echo $task_id ?>"></div>
                            <input type="hidden" value="0" name="roles_num_<?php echo $task_id ?>" id="roles_num_<?php echo $task_id ?>" />
                            <input type="hidden" value="" name="estimatedRolesExcluded_<?php echo $task_id ?>" id="estimatedRolesExcluded_<?php echo $task_id ?>" />
                            <input type="hidden" value="" name="estimatedRolesExcludedIds_<?php echo $task_id ?>" id="estimatedRolesExcludedIds_<?php echo $task_id ?>" />
                        </td>
                    </tr>
                    <?php
                    foreach ($projectTaskEstimation->getRoles() as $role) {
                        echo "<script>addEstimatedRole(\"" . $task_id . "\",\"" . $role->getRoleId() . "\"," . $role->getQuantity() . ");</script>";
                    }
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
<br/>

<table align="center" width="95%">
    <tr>
        <td style="font-size: 9px">
            * <?php echo $AppUI->_("LBL_ESTIMATED_ROLE_HELP") ?>
        </td>
    </tr>
    <tr>
        <td>
            <input type="button" class="button" value="<?php echo $AppUI->_("LBL_SAVE") . " " . $AppUI->_("LBL_ESTIMATIONS"); ?>" onclick="saveEstimationsData()" />
        </td>
    </tr>
</table>
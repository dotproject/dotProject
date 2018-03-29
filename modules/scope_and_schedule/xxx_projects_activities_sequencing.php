<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
$projectId = dPgetParam($_GET, 'project_id', 0);
$activitiesIdsForDisplay;//updated by /modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php
require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php");
$controllerActivityMDP = new ControllerActivityMDP();
?>

<script type="text/javascript" src="./modules/timeplanning/js/mdp.js"></script>

<form action="?m=timeplanning&a=view" method="post" name="form_mdp" id="form_mdp">
    <input name="dosql" type="hidden" value="do_projects_mdp_aed" />
    <input name="tasks_ids" id="tasks_ids" type="hidden" value=""/>
    <input name="tasks_dependencies_ids" id="tasks_dependencies_ids" type="hidden" value=""/>
    <input name="tasks_positions" id="tasks_positions" type="hidden" value=""/>
    <input name="project_id" id="project_id" type="hidden" value="<?php echo $projectId; ?>"/>
    <br />
</form>

<table width="100%" class="tbl" style="border: 0px">
    <tr>
        <th>ID</th>
        <th><?php echo $AppUI->_("Task"); ?></th>
        <th><?php echo $AppUI->_("LBL_ACTIVITIES_DECENDENTS");?></th>
        <th><?php echo $AppUI->_("LBL_INCLUDE");?></th>
    </tr>
    <?php
    $tasks = $controllerActivityMDP->getProjectActivities($projectId);
    foreach ($tasks as $task) {
        ?>
        <tr>
            <td valign="top" width="10%">
                A.<?php echo $activitiesIdsForDisplay[$task->getId()] ?>
            </td>
            <td valign="top" width="30%">
                <input type="hidden" value="<?php echo $task->getId() ?>" name="task_id">
                <?php echo $task->getName(); ?>
            </td>
            <td width="30%">
                <table class="std" width="100%" >
                    <?php
                    foreach ($task->getDependencies() as $dep_id) {
                        ?>
                        <tr>  
                            <td>
                                <?php
                                $dep = new CTask();
                                $dep->load($dep_id);
                                echo  $activitiesIdsForDisplay[$dep_id] ." ".$dep->task_name;
                                ?>
                            </td>
                            <td width="15%">
                                <form  action="?m=timeplanning&a=view" method="post" name="activity_dependency_delete_<?php echo $task->getId() . "_" . $dep_id; ?>">
                                    <input name="dosql" type="hidden" value="do_project_activity_exclude_dependency" />
                                    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>"/>
                                    <input type="hidden" name="activity_id" value="<?php echo $task->getId(); ?>" />
                                    <input type="hidden" name="dependency_id" value="<?php echo $dep_id ?>" />
                                    <img style="cursor:pointer" src="./modules/dotproject_plus/images/trash_small.gif" onclick="document.activity_dependency_delete_<?php echo $task->getId() . "_" . $dep_id; ?>.submit();" />
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
            <td valign="top" style="width:30%; max-width: 215px">
                <form  action="?m=timeplanning&a=view" method="post" name="activity_dependency_add_<?php echo $task->getId(); ?>">
                    <input name="dosql" type="hidden" value="do_project_activity_add_dependency" />
                    <input type="hidden" name="activity_id" value="<?php echo $task->getId(); ?>" /> 
                    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>"/>
                    <select name="dependency_id" style="width:200px; max-width: 200px">
                        <option>--<?php echo $AppUI->_("LBL_ADD_PREDECESSOR"); ?>--</option>
                        <?php
                        foreach ($tasks as $task_dependency) {
                            if ($task->getId() != $task_dependency->getId()) {
                                ?>
                                <option value="<?php echo $task_dependency->getId() ?>">
                                    A.<?php echo $activitiesIdsForDisplay[$task_dependency->getId()] ?> &nbsp; <?php echo $task_dependency->getName(); ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <img src="./modules/dotproject_plus/images/mais_verde.png" onclick="document.activity_dependency_add_<?php echo $task->getId(); ?>.submit();" style="cursor:pointer;height: 11px" />
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<br />

<table width="100%" align="center" class="tbl" style="border: 0px">
    <tr>
        <th style="text-align: center">
            <b><?php echo $AppUI->_("LBL_GANTT_SEQUENCING"); ?></b>
        </th>
    </tr>
    <tr>
        <td align="center">
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/gantt_chart.php"); ?>
        </td>
    </tr>
</table>
<br />
<table align="center" width="100%">
    <tr>
        <td align="right">
            <script> var targetScreenOnProject = "/modules/dotproject_plus/projects_tab.planning_and_monitoring.php";</script>
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
        </td>
    </tr>
</table>
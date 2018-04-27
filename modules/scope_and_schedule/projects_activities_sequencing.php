<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/scope_and_schedule/controller_activity_mdp.class.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");

$projectId = dPgetParam($_GET, 'project_id', 0);
$activitiesIdsForDisplay;//updated by /modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php
//require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php");
$controllerActivityMDP = new ControllerActivityMDP();
$project = new CProject();
$project->load($projectId);
?>

<script type="text/javascript" src="./modules/scope_and_schedule/js/mdp.js"></script>

<form action="?m=scope_and_schedule&a=view" method="post" name="form_mdp" id="form_mdp">
    <input name="dosql" type="hidden" value="do_projects_mdp_aed" />
    <input name="tasks_ids" id="tasks_ids" type="hidden" value=""/>
    <input name="tasks_dependencies_ids" id="tasks_dependencies_ids" type="hidden" value=""/>
    <input name="tasks_positions" id="tasks_positions" type="hidden" value=""/>
    <input name="project_id" id="project_id" type="hidden" value="<?php echo $projectId; ?>"/>
    <br />
</form>

<b><?php echo $AppUI->_("Project") ?>: <a href="index.php?m=projects&a=view&project_id=<?php echo $projectId ?>"><?php echo $project->project_name  ?></a></b>
<br />
<br />
<p align="center"><b>Activities Sequencing</b></p>
<br />
<table width="100%" class="tbl" style="border: 0px">
    <tr>
        <th><?php echo $AppUI->_("Task"); ?></th>
        <th><?php echo $AppUI->_("Dependencies");?></th>
        <th><?php echo $AppUI->_("Include");?></th>
    </tr>
    <?php
    $tasks = $controllerActivityMDP->getProjectActivities($projectId);
    foreach ($tasks as $task) {
        ?>
        <tr>
            <td valign="top" width="30%">
                <input type="hidden" value="<?php echo $task->task_id ?>" name="task_id">
                <?php echo $task->task_name; ?>
            </td>
            <td width="30%">
                
                    <?php
					$dependencies=$task->getDependencies();
					if($dependencies != null){
						?>
						<table class="std" width="100%" >
						<?php
	
						$dependencies = explode(',', $dependencies);
						foreach ($dependencies as $dep_id) {
                        ?>
                        <tr>  
                            <td>
                                <?php
                                $dep = new CTask();
                                $dep->load($dep_id);
                                echo  $dep->task_name;
                                ?>
                            </td>
                            <td width="15%">
                                <form  action="?m=scope_and_schedule&a=view" method="post" name="activity_dependency_delete_<?php echo $task->task_id . "_" . $dep_id; ?>">
                                    <input name="dosql" type="hidden" value="do_project_activity_exclude_dependency" />
                                    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>"/>
                                    <input type="hidden" name="activity_id" value="<?php echo $task->task_id; ?>" />
                                    <input type="hidden" name="dependency_id" value="<?php echo $dep_id ?>" />
                                    <img  src="modules/scope_and_schedule/images/trash-icon.png" onclick="document.activity_dependency_delete_<?php echo $task->task_id . "_" . $dep_id; ?>.submit();" style="cursor:pointer;height:13px;width:13px" />
                                </form>
                            </td>
                        </tr>
						
                        <?php
						}
						?>
						</table>
						<?php
                    }
                    ?>
                
            </td>
            <td valign="top" style="width:30%; max-width: 215px">
                <form  action="?m=scope_and_schedule&a=view" method="post" name="activity_dependency_add_<?php echo $task->task_id; ?>">
                    <input name="dosql" type="hidden" value="do_project_activity_add_dependency" />
                    <input type="hidden" name="activity_id" value="<?php echo $task->task_id; ?>" /> 
                    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>"/>
                    <select name="dependency_id" style="width:200px; max-width: 200px">
                        <option value="-1">--<?php echo $AppUI->_("Add dependency"); ?>--</option>
                        <?php
                        foreach ($tasks as $task_dependency) {
                            if ($task->task_id != $task_dependency->task_id) {
                                ?>
                                <option value="<?php echo $task_dependency->task_id ?>">
                                   <?php echo $task_dependency->task_name; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <img src="modules/scope_and_schedule/images/add_button_icon.png" onclick="document.activity_dependency_add_<?php echo $task->task_id; ?>.submit();" style="cursor:pointer;height:18px;width:18px"/>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<br />

<table width="100%" align="center" class="tbl" style="border: 0px">
    <tr>
        <th style="text-align: center">
            <b><?php echo $AppUI->_("Gantt Chart"); ?></b>
        </th>
    </tr>
    <tr>
        <td align="center">
            <?php require_once (DP_BASE_DIR . "/modules/tasks/viewgantt.php"); ?>
        </td>
    </tr>
</table>
<br />
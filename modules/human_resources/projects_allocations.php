<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
global $AppUI;
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";

$project_id = dPgetParam($_GET, "project_id", 0);
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
?>

<form  name="decomposition_form" method="post" action="?m=timeplanning&a=view&project_id=<?php echo $project_id; ?>">

    <table id="table_decomposition" name="table_decomposition" class="tbl"  border="0" align="center" width="95%">
        <caption><b><?php echo $AppUI->_("LBL_PROJECT_ALLOCATIONS"); ?> </b></caption>
        <tr bgcolor="silver">

            <th ><?php echo $AppUI->_("LBL_WBS"); ?> </th>
            <th > <?php echo $AppUI->_("LBL_DESCRIPTION"); ?> </th>
            <th> <?php echo $AppUI->_("LBL_ROLE"); ?> </th>
            <th > <?php echo $AppUI->_("allocations"); ?></th>
        </tr>
        <?php
        $items = $controllerWBSItem->getWBSItems($project_id);
        $activities = array();
        foreach ($items as $item) {
            $id = $item->getId();
            $name = $item->getName();
            $number = $item->getNumber();
            $is_leaf = $item->isLeaf();
            echo "<tr>";
            echo "<td colspan=\"4\"><b>$number - $name</b></td></tr>";

            //add decomposed activities
            if ($is_leaf == "1") {
                //start: code to filter workpakage activities
                $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                $hasActivities = false;
                foreach ($tasks as $obj) {
                    $activities[$obj->task_id] = true; //just inform this activity was used 
                    $hasActivities = true;
                    $task_name = $obj->task_name;
                    $task_id = $obj->task_id;
                    $allocated = areAllTaskRolesAllocated($task_id);
                    $style = $allocated ? "" : "background-color:#ED9A9A; font-weight:bold";
                    ?>
                    <tr style=<?php echo $style; ?>>
                        <td nowrap="nowrap" colspan="2" style=<?php echo $style; ?> >
                            <a href="index.php?m=human_resources&amp;a=view_task_roles&amp;project_id=<?php echo $project_id; ?>&amp;task_id=<?php echo $task_id ?>">
                                <?php echo $task_name ?>
                            </a>
                        </td>
                        <td style=<?php echo $style; ?>>
                            <?php
                            $query = new DBQuery();
                            $query->addTable('project_tasks_estimated_roles', 'e');
                            $query->addQuery('e.id, e.role_id, h.human_resources_role_name, h.human_resources_role_responsability,h.human_resources_role_authority, h.human_resources_role_competence');
                            $query->innerJoin('company_role', 'c', 'c.id = e.role_id');
                            $query->innerJoin('human_resources_role', 'h', 'c.role_name = h.human_resources_role_name');
                            $query->addWhere('e.task_id = ' . $task_id);
                            $res = & $query->exec();
                            for ($res; !$res->EOF; $res->MoveNext()) {
                                echo $res->fields['human_resources_role_name'] . "<br />";
                            }
                            ?>
                        </td>
                        <td nowrap="nowrap" style=<?php echo $style; ?> >
                            <?php
                            $q = new DBQuery();
                            $q->addQuery("u.user_username,tr.task_id,hr_al.human_resource_allocation_id, tr.role_id,hr_al.project_tasks_estimated_roles_id,hr_al.human_resource_id");
                            $q->addTable("project_tasks_estimated_roles", "tr");
                            $q->addJoin("human_resource_allocation", "hr_al ", "hr_al.project_tasks_estimated_roles_id=tr.id");
                            $q->addJoin("human_resource", "hr ", " hr_al.human_resource_id=hr.human_resource_id");
                            $q->addJoin("users", "u", " hr.human_resource_user_id=u.user_id");
                            $q->addWhere("tr.task_id=" . $task_id);
                            $sql = $q->prepare();
                            $records = db_loadList($sql);
                            foreach ($records as $record) {
                                echo $record[0] . "<br />";
                            }
                            ?> 
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>
</form>

<table>
    <tr>
        <td><?php echo $AppUI->_("Key"); ?>:&nbsp;&nbsp;</td>
        <td style="background-color:#FFFFFF; color:#000000" width="10">&nbsp;</td>
        <td>=<?php echo $AppUI->_("Task allocated"); ?>&nbsp;&nbsp;</td>
        <td style="background-color:#ED9A9A; color:#000000" width="10">&nbsp;</td>
        <td>=<?php echo $AppUI->_("Task not allocated"); ?>&nbsp;&nbsp;</td>
    </tr>
</table>

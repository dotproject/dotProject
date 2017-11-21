<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;

$titleBlock = new CTitleBlock('Allocation', 'applet3-48.png', $m, "$m.$a");

$task_id = intval(dPgetParam($_GET, 'task_id', 0));
$project_id = intval(dPgetParam($_GET, 'project_id', 0));

$query = new DBQuery;
$query->addTable('projects', 'p');
$query->addQuery('project_name');
$query->addWhere('p.project_id = ' . $project_id);
$res_project = & $query->exec();
$project_name = $res_project->fields['project_name'];

$query = new DBQuery;
$query->addTable('tasks', 't');
$query->addQuery('task_name');
$query->addWhere('t.task_id = ' . $task_id);
$res_task = & $query->exec();

$titleBlock->addCrumb(('?m=projects&amp;a=view&amp;project_id=' . $project_id), $res_project->fields['project_name']);
$titleBlock->addCrumb(('?m=tasks&amp;a=view&amp;task_id=' . $task_id), $res_task->fields['task_name']);
$titleBlock->show();

$query = new DBQuery;
$query->addTable('project_tasks_estimated_roles', 'e');
$query->addQuery('e.id, e.role_id, h.human_resources_role_name, h.human_resources_role_responsability, 
						h.human_resources_role_authority, h.human_resources_role_competence');
$query->innerJoin('company_role', 'c', 'c.id = e.role_id');
$query->innerJoin('human_resources_role', 'h', 'c.role_name = h.human_resources_role_name');
$query->addWhere('e.task_id = ' . $task_id);
$res = & $query->exec();
?>
<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl'>
    <input type="hidden" name="tab" value="<?php echo dPgetParam($_GET, 'tab', 0); ?>">
    <tr>
        <th nowrap='nowrap' width='25%'>
            <?php echo $AppUI->_('Estimated role'); ?>
        </th>
        <th nowrap='nowrap' width='25%'>
            <?php echo $AppUI->_('Role responsability'); ?>
        </th>
        <th nowrap='nowrap' width='25%'>
            <?php echo $AppUI->_('Role authority'); ?>
        </th>
        <th nowrap='nowrap' width='25%'>
            <?php echo $AppUI->_('Role competence'); ?>
        </th>
    </tr>
    <?php
    require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
    for ($res; !$res->EOF; $res->MoveNext()) {
        $project_tasks_estimated_roles_id = $res->fields['id'];
        $allocated = isRoleAllocated($project_tasks_estimated_roles_id);
        $style = $allocated ? '' : 'background-color:#ED9A9A; font-weight:bold';
        ?>
        <tr>
            <td style=<?php echo $style; ?>>
                <a href="index.php?m=human_resources&amp;a=view_allocation&amp;project_tasks_estimated_roles_id=<?php echo $project_tasks_estimated_roles_id; ?>&amp;task_id=<?php echo $task_id; ?>&amp;project_id=<?php echo $project_id; ?>">
                    <?php echo $res->fields['human_resources_role_name']; ?>
                </a>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res->fields['human_resources_role_responsability']; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res->fields['human_resources_role_authority']; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res->fields['human_resources_role_competence']; ?>
            </td>
        </tr>
        <?php
    }
    $query->clear();
    ?>
</table>
<table>
    <tr>
        <td><?php echo $AppUI->_('Key'); ?>:&nbsp;&nbsp;</td>
        <td style="background-color:#FFFFFF; color:#000000" width="10">&nbsp;</td>
        <td>=<?php echo $AppUI->_('Role allocated'); ?>&nbsp;&nbsp;</td>
        <td style="background-color:#ED9A9A; color:#000000" width="10">&nbsp;</td>
        <td>=<?php echo $AppUI->_('Role not allocated'); ?>&nbsp;&nbsp;</td>
    </tr>
</table>
<br />
<?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>        
          
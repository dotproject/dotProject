<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;

$project_id = intval(dPgetParam($_GET, 'project_id', 0));
$query = new DBQuery;
$query->addTable('projects', 'p');
$query->addQuery('project_name');
$query->addWhere('p.project_id = ' . $project_id);
$res =& $query->exec();

$titleBlock = new CTitleBlock('Human Resources Plan', 'applet3-48.png', $m, "$m.$a");
$titleBlock->addCrumb(('?m=projects&amp;a=view&amp;project_id=' . $project_id), 'project ' . $res->fields['project_name']);
$titleBlock->show();
$query->clear();

$query->addTable('tasks', 't');
$query->addQuery('task_id, task_name');
$query->addWhere('t.task_project = ' . $project_id);
$res =& $query->exec();
?>

<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl' summary="view project tasks">
<tr>
	<th nowrap='nowrap' width='20%'>
    <?php echo $AppUI->_('Task Name'); ?>
	</th>
</tr>
<?php
require_once DP_BASE_DIR."/modules/human_resources/configuration_functions.php";
for ($res; ! $res->EOF; $res->MoveNext()) {
	$task_id = $res->fields['task_id'];
	$allocated = areAllTaskRolesAllocated($task_id);
	$style = $allocated ? '' : 'background-color:#ED9A9A; font-weight:bold';
?>
<tr>
  <td style=<?php echo $style; ?>>
    <a href="index.php?m=human_resources&amp;a=view_task_roles&amp;project_id=<?php echo $project_id;?>&amp;task_id=<?php echo $res->fields['task_id'];?>">
    <?php echo $res->fields['task_name']; ?>
    </a>
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
  <td>=<?php echo $AppUI->_('Task allocated'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#ED9A9A; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Task not allocated'); ?>&nbsp;&nbsp;</td>
</tr>
</table>
<tr>
  <td>
    <input type="button" value="<?php echo $AppUI->_('back');?>"
    class="button" onclick="javascript:history.back(-1);" />
  </td>
</tr>

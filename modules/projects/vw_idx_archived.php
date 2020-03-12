<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $projects, $company_id, $pstatus, $project_types, $currentTabId, $currentTabName;
GLOBAL $priority;

$df = $AppUI->getPref('SHDATEFORMAT');

$editProjectsAllowed = getPermission('projects', 'edit');
foreach ($projects as $row) {
	$editProjectsAllowed = (($editProjectsAllowed) 
	                        || getPermission('projects', 'edit', $row['project_id']));
}

$base_table_cols = 6;
$table_cols = $base_table_cols + (($editProjectsAllowed) ? 1 : 0);
$added_cols = $table_cols - $base_table_cols;
?>

<form action='./index.php' method='get'>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td colspan="<?php echo ($base_table_cols); ?>" nowrap="nowrap">
		<?php echo $AppUI->_('sort by'); ?>:
	</td>
<?php 
if ($added_cols) {
?>
	<td colspan="<?php echo ($added_cols); ?>" nowrap="nowrap">&nbsp;</td>
<?php 
}
?>
</tr>
<tr>
	<th nowrap="nowrap">
		<a href="?m=projects&amp;orderby=project_color_identifier" class="hdr">
		<?php echo $AppUI->_('Color');?>
		</a>
		(<a href="?m=projects&amp;orderby=project_percent_complete" class="hdr">%</a>)
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&amp;orderby=company_name" class="hdr">
		<?php echo $AppUI->_('Company');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&amp;orderby=project_name" class="hdr">
		<?php echo $AppUI->_('Project Name');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&amp;orderby=user_username" class="hdr">
		<?php echo $AppUI->_('Owner');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&amp;orderby=total_tasks" class="hdr">
		<?php echo $AppUI->_('Tasks');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&amp;orderby=project_end_date" class="hdr">
		<?php echo $AppUI->_('Finished');?>
		</a>
	</th>
<?php 
if ($editProjectsAllowed) {
?>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Selection'); ?>
	</th>
<?php 
}
?>
</tr>

<?php 
$CR = "\n";
$CT = "\n\t";
$none = true;
foreach ($projects as $row) {
	if (! getPermission('projects', 'view', $row['project_id'])) {
		continue;
	}
	if ($row['project_status'] == 7) {
		$none = false;
		$end_date = ((intval(@$row['project_actual_end_date'])) 
		             ? new CDate($row['project_actual_end_date']) : null);
?>
<tr>
	<td width="65" align="center" style="border: outset #eeeeee 2px;background-color:<?php 
echo ($row['project_color_identifier']); ?>">
		<span style="color:<?php echo (bestColor($row['project_color_identifier'])); ?>">
		<?php echo(sprintf('%.1f%%', $row['project_percent_complete'])); ?>
		</span>
	</td>
	<td width="30%">
<?php 
		$accessProjComp = getPermission('companies', 'access', $row['project_company']);
		if ($accessProjComp) {
?>
		<a href="?m=companies&amp;a=view&amp;company_id=<?php 
echo htmlspecialchars($row['project_company']); ?>" title="<?php 
echo htmlspecialchars($row['company_description'], ENT_QUOTES); ?>">
<?php 
		}
		echo (htmlspecialchars($row['company_name'], ENT_QUOTES));
		if ($accessProjComp) {
?>
		</a>
<?php 
		}
?>
	</td>
	<td width="100%">
		<a href="?m=projects&amp;a=view&amp;project_id=<?php 
echo htmlspecialchars($row['project_id']); ?>" <?php
if (!empty($row['project_description'])) { ?>onmouseover="return overlib('<?php 
echo(htmlspecialchars(('<div><p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', 
                                                addslashes($row['project_description'])) 
                       . '</p></div>'), ENT_QUOTES)); ?>', CAPTION, '<?php 
echo($AppUI->_('Description')); ?>', CENTER);" onmouseout="nd();"<?php } ?>>
		<?php echo (htmlspecialchars($row['project_name'], ENT_QUOTES)); ?>
		</a>
	</td>
	<td nowrap="nowrap">
		<?php echo (htmlspecialchars($row['user_username'], ENT_QUOTES)); ?>
	</td>
	<td align="center" nowrap="nowrap">
		<?php echo (htmlspecialchars($row['total_tasks'])); ?>
	</td>
	<td align="center" nowrap="nowrap" style="background-color:<?php 
echo ($priority[$row['project_priority']]['color']); ?>">
		<?php echo (($end_date ? $end_date->format($df) : '-')); ?>
	</td>
<?php 
		if ($editProjectsAllowed) {
?>
	<td align="center">
<?php 
			if (getPermission('projects', 'edit', $row['project_id'])) {
?>
		<input type="checkbox" name="project_id[]" value="<?php echo ($row['project_id']); ?>" />
<?php 
			} else {
?>
		&nbsp;
<?php 
			} 
?>
	</td>
<?php 
		}
?>
</tr>
<?php 
	}
}

if ($none) {
?>
<tr>
	<td colspan="<?php echo ($table_cols); ?>"><?php 
echo $AppUI->_('No projects available'); ?></td>
</tr>
<?php 
} else {
?>
<tr>
	<td colspan="<?php echo ($table_cols); ?>" align="right">
		<input type="submit" class="button" value="<?php 
echo $AppUI->_('Update projects status'); ?>" />
		<input type="hidden" name="update_project_status" value="1" />
		<input type="hidden" name="m" value="projects" />
<?php 
	echo arraySelect($pstatus, 'project_status', 'size="1" class="text"', 2, true);
?>
	</td>
</tr>
<?php 
}
?>
</table>
</form>

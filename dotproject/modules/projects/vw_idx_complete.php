<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $projects, $company_id, $pstatus, $project_types, $currentTabId, $currentTabName;

$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');

$base_table_cols = 6;
$table_cols = $base_table_cols + ((($perms->checkModuleItem('projects', 'edit', 
															$row['project_id']))) ? 1 : 0);
$added_cols = $table_cols - $base_table_cols;
?>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
<?php 
	echo ("\t" . '<td colspan="' . $base_table_cols . '" nowrap="nowrap">' 
		  . $AppUI->_('sort by') . ':</td>');
if ($added_cols) {
	echo ("\t" . '<th colspan="' . $added_cols . '" nowrap="nowrap">&nbsp;</td>');
}
?>
</tr>
<tr>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_color_identifier" class="hdr">
		<?php echo $AppUI->_('Color');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=company_name" class="hdr">
		<?php echo $AppUI->_('Company');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_name" class="hdr">
		<?php echo $AppUI->_('Project Name');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=user_username" class="hdr">
		<?php echo $AppUI->_('Owner');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=total_tasks" class="hdr"><?php echo $AppUI->_('Tasks');?></a>
		<a href="?m=projects&orderby=my_tasks" class="hdr">(<?php echo $AppUI->_('My');?>)</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_end_date" class="hdr">
		<?php echo $AppUI->_('Due Date');?>
		</a>
	</th>
<?php 
if ($perms->checkModuleItem('projects', 'edit', $row['project_id'])) {
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
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id']))  {
		continue;
	}
	if ($row['project_status'] == 5) {
		$none = false;
		$end_date = ((intval(@$row['project_end_date'])) 
		             ? new CDate($row['project_end_date']) : null);
		
		$s = '<tr>';
		$s .= ('<td width="65" align="center"' 
		       . ' style="border: outset #eeeeee 2px;background-color:#' 
		       . $row['project_color_identifier'] . '">');
		$s .= ($CT . '<font color="' . bestColor($row['project_color_identifier']) . '">' 
		       . sprintf('%.1f%%', $row['project_percent_complete']) . '</font>');
		$s .= $CR . '</td>';

		$s .= $CR . '<td width="30%">';
		if ($perms->checkModuleItem('companies', 'access', $row['project_company'])) {
			$s .= ($CT . '<a href="?m=companies&a=view&company_id=' . $row['project_company'] 
			       . '" title="' . htmlspecialchars($row['company_description'], ENT_QUOTES) 
			       . '">' . htmlspecialchars($row['company_name'], ENT_QUOTES) . '</a>');
		} else {
			$s .= $CT . htmlspecialchars($row['company_name'], ENT_QUOTES);
		}
		$s .= $CR . '</td>';

		$s .= $CR . '<td width="100%">';
		$s .= ($CT . '<a href="?m=projects&a=view&project_id=' . $row['project_id'] 
		       . '" onmouseover="return overlib(\'' 
		       . htmlspecialchars('<div><p>' 
		                          . str_replace(array("\r\n", "\n", "\r"), '</p><p>'
		                                        , addslashes($row['project_description'])) 
		                          . '</p></div>', ENT_QUOTES) . '\', CAPTION, \'' 
		       . $AppUI->_('Description') . '\', CENTER);" onmouseout="nd();">' 
		       . htmlspecialchars($row['project_name'], ENT_QUOTES) . '</a>');
		$s .= $CR . '</td>';
		$s .= ($CR . '<td nowrap="nowrap">' 
		       . htmlspecialchars($row['user_username'], ENT_QUOTES) . '</td>');
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . $row['total_tasks'] . ($row['my_tasks'] ? ' ('.$row['my_tasks'].')' : '');
		$s .= $CR . '</td>';
		$s .= $CR . '<td align="right" nowrap="nowrap">';
		$s .= $CT . ($end_date ? $end_date->format($df) : '-');
		$s .= $CR . '</td>';
		if ($perms->checkModuleItem('projects', 'edit', $row['project_id'])) {
			$s .= $CR . '<td align="center">';
			if ($perms->checkModuleItem('projects', 'edit', $row['project_id'])) {
				$s .= ($CT . '<input type="checkbox" name="project_id[]" value="' 
					   . $row['project_id'] . '" />');
			} else {
  	    		$s .= $CT . '&nbsp;';
			}
			$s .= $CR . '</td>';
		}
		$s .= $CR . '</tr>';
		echo $s;
	}
}

if ($none) {
	echo $CR . '<tr><td colspan="' . $table_cols . '">' . $AppUI->_('No projects available') . '</td></tr>';
} else {
?>
<tr>
	<td colspan="<?php echo ($table_cols);?>" align="right">
<?php
echo '<input type="submit" class="button" value="'.$AppUI->_('Update projects status').'" />';
echo '<input type="hidden" name="update_project_status" value="1" />';
echo '<input type="hidden" name="m" value="projects" />';
echo arraySelect($pstatus, 'project_status', 'size="1" class="text"', 2, true);
}
?>
	</td>
</tr>
</table>
</form>

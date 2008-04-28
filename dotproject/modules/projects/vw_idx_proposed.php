<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $projects, $company_id, $pstatus, $project_types, $currentTabId, $currentTabName;

$show_all_projects = false;
if ($currentTabId == 500) {
	$show_all_projects = true;
}

$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');

$base_table_cols = 9;
$base_table_cols += (($show_all_projects) ? 1 : 0);

$table_cols = $base_table_cols + ((($perms->checkModuleItem('projects', 'edit', 
															$row['project_id']))) ? 1 : 0);
$added_cols = $table_cols - $base_table_cols;
?>

<form action='./index.php' method='get'>

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
		<a href="?m=projects&orderby=project_start_date" class="hdr">
		<?php echo $AppUI->_('Start');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_end_date" class="hdr">
		<?php echo $AppUI->_('End');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_actual_end_date" class="hdr">
		<?php echo $AppUI->_('Actual');?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=task_log_problem%20DESC,project_priority" class="hdr">
		<?php echo $AppUI->_('P');?>
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
<?php 
if ($show_all_projects) {
?>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=total_tasks" class="hdr"><?php echo $AppUI->_('Status');?></a>
	</th>
<?php
}
?>
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

//Tabbed view
$project_status_filter = $currentTabId;
//Project not defined
//if ($currentTabId == count($project_types)-1)
//	$project_status_filter = 0;

foreach ($projects as $row) {
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id'])) {
		continue;
	}
	if ($show_all_projects || $row['project_status'] == $project_status_filter) {
		$none = false;
		$start_date = ((intval(@$row['project_start_date'])) 
		               ? new CDate($row['project_start_date']) : null);
		$end_date = ((intval(@$row['project_end_date'])) 
		             ? new CDate($row['project_end_date']) : null);
		$actual_end_date = ((intval(@$row['project_actual_end_date'])) 
		                    ? new CDate($row['project_actual_end_date']) : null);
		$style = ((($actual_end_date > $end_date) && !empty($end_date)) 
		          ? 'style="color:red; font-weight:bold"' : '');
		
		$s = '<tr>';
		$s .= ('<td width="65" align="center"' 
		       . ' style="border: outset #eeeeee 2px;background-color:#' 
		       . $row['project_color_identifier'] . '">');
		$s .= ($CT . '<font color="' . bestColor($row['project_color_identifier']) . '">'
		       . sprintf('%.1f%%', $row['project_percent_complete'])
		       . '</font>');
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
		$s .= ($CR . '<td align="center">'. ($start_date ? $start_date->format($df) : '-') 
		       .'</td>');
		$s .= $CR . '<td align="center">'. ($end_date ? $end_date->format($df) : '-') .'</td>';
		$s .= $CR . '<td align="center">';
		$s .= (($actual_end_date) 
		       ? ('<a href="?m=tasks&a=view&task_id=' . $row['critical_task'] . '">') : '');
		$s .= (($actual_end_date) 
		       ? ('<span ' . $style . '>' . $actual_end_date->format($df) . '</span>') 
		       : '-');
		$s .= $actual_end_date ? '</a>' : '';
		$s .= $CR . '</td>';
		$s .= $CR . '<td align="center">';
		
		if ($row['task_log_problem']) {
			$s .= ('<a href="?m=tasks&a=index&f=all&project_id=' . $row['project_id'] . '">' 
				   . dPshowImage('./images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem!') 
				   . '</a>');
		} else if ($row['project_priority'] != 0) {
		  $s .= "\n\t\t" . dPshowImage(('./images/icons/priority' . (($row['project_priority'] > 0) 
																	 ? '+' : '-') 
										. abs($row['project_priority']) . '.gif'), 13, 16, '', '');
		} else {
			$s .= '&nbsp;';
		}
		
		$s .= $CR . '</td>';
		$s .= ($CR . '<td nowrap="nowrap">' 
		       . htmlspecialchars($row['user_username'], ENT_QUOTES) . '</td>');
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . $row['total_tasks'] . ($row['my_tasks'] ? ' ('.$row['my_tasks'].')' : '');
		$s .= $CR . '</td>';
		if ($show_all_projects) {
			$s .= $CR . '<td align="center" nowrap="nowrap">';
			$s .= $CT . (($row['project_status'] == 0) 
			             ? $AppUI->_('Not Defined') : $project_types[$row['project_status']]);
			$s .= $CR . '</td>';
		}
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

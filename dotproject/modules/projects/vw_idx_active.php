<?php /* PROJECTS $Id$ */
global $projects;
global $AppUI, $company_id, $priority;

$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');
?>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td align="right" width="65" nowrap="nowrap">&nbsp;<?php echo $AppUI->_('sort by');?>:&nbsp;</td>
</tr>
<tr>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_color_identifier" class="hdr"><?php echo $AppUI->_('Color');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=company_name" class="hdr"><?php echo $AppUI->_('Company');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_name" class="hdr"><?php echo $AppUI->_('Project Name');?></a>
	</th>
          <th nowrap="nowrap">
		<a href="?m=projects&orderby=project_start_date" class="hdr"><?php echo $AppUI->_('Start');?></a>
	</th>
        <th nowrap="nowrap">
		<a href="?m=projects&orderby=project_end_date" class="hdr"><?php echo $AppUI->_('Due Date');?></a>
	</th>
        <th nowrap="nowrap">
		<a href="?m=projects&orderby=project_actual_end_date" class="hdr"><?php echo $AppUI->_('Actual');?></a>
	</th>
        <th nowrap="nowrap">
		<a href="?m=projects&orderby=task_log_problem" class="hdr"><?php echo $AppUI->_('P');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=user_username" class="hdr"><?php echo $AppUI->_('Owner');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=total_tasks" class="hdr"><?php echo $AppUI->_('Tasks');?></a>
		<a href="?m=projects&orderby=my_tasks" class="hdr">(<?php echo $AppUI->_('My');?>)</a>
	</th>
</tr>

<?php
$CR = "\n";
$CT = "\n\t";
$none = true;
foreach ($projects as $row) {
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id']))
		continue;
	// We dont check the percent_completed == 100 because some projects
	// were being categorized as completed because not all the tasks
	// have been created (for new projects)
	if ($row["project_status"] == 3) {
		$none = false;
                $start_date = intval( @$row["project_start_date"] ) ? new CDate( $row["project_start_date"] ) : null;
		$end_date = intval( @$row["project_end_date"] ) ? new CDate( $row["project_end_date"] ) : null;
                $actual_end_date = intval( @$row["project_actual_end_date"] ) ? new CDate( $row["project_actual_end_date"] ) : null;
                $style = (( $actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

		$s = '<tr>';
		$s .= '<td width="65" align="center" style="border: outset #eeeeee 2px;background-color:#'
			. $row["project_color_identifier"] . '">';
		$s .= $CT . '<font color="' . bestColor( $row["project_color_identifier"] ) . '">'
			. sprintf( "%.1f%%", $row["project_percent_complete"] )
			. '</font>';
		$s .= $CR . '</td>';
		$s .= $CR . '<td width="30%">';
		if ($perms->checkModuleItem('companies', 'access', $row['project_company'])) {
			$s .= $CT . '<a href="?m=companies&a=view&company_id=' . $row["project_company"] . '" title="' . htmlspecialchars( $row["company_description"], ENT_QUOTES ) . '">' . htmlspecialchars( $row["company_name"], ENT_QUOTES ) . '</a>';
		} else {
			$s .= $CT . htmlspecialchars( $row["company_name"], ENT_QUOTES );
		}
		$s .= $CR . '</td>';

		$s .= $CR . '<td width="100%">';$s .= $CT . '<a href="?m=projects&a=view&project_id=' . $row["project_id"] . '" onmouseover="return overlib( \''.htmlspecialchars( '<div><p>'.str_replace(array("\r\n", "\n", "\r"), '</p><p>', $row["project_description"]).'</p></div>', ENT_QUOTES ).'\', STICKY, CAPTION, \''.$AppUI->_('Description').'\', CENTER);" onmouseout="nd();">' . htmlspecialchars( $row["project_name"], ENT_QUOTES ) . '</a>';
		$s .= $CR . '</td>';
                $s .= $CR . '<td align="center">'. ($start_date ? $start_date->format( $df ) : '-') .'</td>';
                $s .= $CR . '<td align="right" nowrap="nowrap" style="background-color:'.$priority[$row['project_priority']]['color'].'">';
		$s .= $CT . ($end_date ? $end_date->format( $df ) : '-');
		$s .= $CR . '</td>';
                $s .= $CR . '<td align="center">';
                $s .= $actual_end_date ? '<a href="?m=tasks&a=view&task_id='.$row["critical_task"].'">' : '';
                $s .= $actual_end_date ? '<span '. $style.'>'.$actual_end_date->format( $df ).'</span>' : '-';
                $s .= $actual_end_date ? '</a>' : '';
		$s .= $CR . '</td>';
                $s .= $CR . '<td align="center">';
                $s .= $row["task_log_problem"] ? '<a href="?m=tasks&a=index&f=all&project_id='.$row["project_id"].'">' : '';
                $s .= $row["task_log_problem"] ? dPshowImage( './images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem' ): '-';
                $s .= $CR . $row["task_log_problem"] ? '</a>' : '';
                $s .= $CR . '</td>';
		$s .= $CR . '<td nowrap="nowrap">' . htmlspecialchars( $row["user_username"], ENT_QUOTES ) . '</td>';
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . $row["total_tasks"] . ($row["my_tasks"] ? ' ('.$row["my_tasks"].')' : '');
		$s .= $CR . '</td>';
		$s .= $CR . '</tr>';
		echo $s;
	}
}
if ($none) {
	echo $CR . '<tr><td colspan="10">' . $AppUI->_( 'No projects available' ) . '</td></tr>';
}
?>
<tr>
	<td colspan="8">&nbsp;</td>
</tr>
</table>

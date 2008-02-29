<?php 
## Active Projects View for Calendar
## based on Companies: View Projects sub-table by gregorerhardt
##
global $AppUI, $company_id, $pstatus, $dPconfig;

$df = $AppUI->getPref('SHDATEFORMAT');

$show_all_projects = true;

// get any records denied from viewing
$obj = new CProject();
$deny = $obj->getDeniedRecords( $AppUI->user_id );

// Task sum table
// by Pablo Roca (pabloroca@mvps.org)
// 16 August 2003

$q = new DBQuery();
$q->createTemp('tasks_sum');
$q->addQuery('task_project, COUNT(DISTINCT task_id) AS total_tasks,
			SUM(task_duration * task_duration_type * task_percent_complete) / SUM(task_duration * task_duration_type) AS project_percent_complete');
$q->addTable('tasks');
$q->addGroup('task_project');
$q->exec();
$q->clear();

// temporary My Tasks
// by Pablo Roca (pabloroca@mvps.org)
// 16 August 2003
$q->createTemp('tasks_summy');
$q->addQuery('task_project, COUNT(DISTINCT task_id) AS my_tasks');
$q->addTable('tasks');
$q->addWhere('task_owner = '.$AppUI->user_id);
$q->addGroup('task_project');
$q->exec();
$q->clear();

$q->addTable('projects');
$q->addQuery('project_id, project_name, project_status, project_color_identifier,
	project_start_date, project_end_date, project_priority,
	ts.total_tasks, tsm.my_tasks, ts.project_percent_complete,
	contact_first_name, contact_last_name, user_username');
$q->addJoin('users', 'u', 'u.user_id = projects.project_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addJoin('tasks_sum', 'ts', 'projects.project_id = ts.task_project');
$q->addJoin('tasks_summy', 'tsm', 'projects.project_id = tsm.task_project');
$q->addWhere('projects.project_status <> 7');
$q->addWhere('projects.project_status <> 5');
$allowed_where = $obj->getAllowedSQL($AppUI->user_id);
if (count($allowed_where) > 1) {
	$q->addWhere(implode('AND', $allowed_where));
} else {
	$q->addWhere($allowed_where[0]);
}
$q->addOrder('project_end_date');

$projects = $q->loadList();
?>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td align="right" width="65" nowrap="nowrap">&nbsp;<?php echo $AppUI->_('sort by');?>:&nbsp;</td>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=project_name" class="hdr"><?php echo $AppUI->_('Name');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=user_username" class="hdr"><?php echo $AppUI->_('Owner');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=projects&orderby=my_tasks%20desc" class="hdr"><?php echo $AppUI->_('My Tasks');?></a>
		<a href="?m=projects&orderby=total_tasks%20desc" class="hdr">(<?php echo $AppUI->_('All');?>)</a>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Selection'); ?>
	</th>
	<?php if ($show_all_projects): ?>
		<th nowrap="nowrap">
			<?php echo $AppUI->_('Status'); ?>
		</th>
	<?php endif; ?>
</tr>

<?php
$CR = "\n";
$CT = "\n\t";
$none = true;

// When in plain view, $AppUI->getState( 'ProjIdxTab' ) doesn't contain the selected index for which we want
// to filter projects, we must get the current box name from the calling file overrides.php variable $v
if ( $tab == -1 ){
	//Plain view
	foreach ($project_types as $project_key => $project_type){
		$project_type = trim($project_type);
		$flip_project_types[$project_type] = $project_key;
	}
	$project_status_filter = $flip_project_types[$v[1]];
} else{
	//Tabbed view
	$project_status_filter = $tab;
	//Project not defined
	if ($tab == count($project_types)-1)
		$project_status_filter = 0;
}

foreach ($projects as $row) {
	if ($show_all_projects || 
	    ($row['project_status'] > 7 && $row['project_status'] == $project_status_filter)) {
		$none = false;
		$end_date = intval( @$row['project_end_date'] ) ? new CDate( $row['project_end_date'] ) : null;

		$s = '<tr>';
		$s .= '<td width="65" align="center" style="border: outset #eeeeee 2px;background-color:#'
			. $row['project_color_identifier'] . '">';
		$s .= $CT . '<font color="' . bestColor( $row['project_color_identifier'] ) . '">'
			. sprintf( "%.1f%%", $row['project_percent_complete'] )
			. '</font>';
		$s .= $CR . '</td>';
		$s .= $CR . '<td width="100%">';
		$s .= $CT . '<a href="?m=projects&a=view&project_id=' . $row['project_id'] . '" title="' . htmlspecialchars( $row['project_description'], ENT_QUOTES ) . '">' . htmlspecialchars( $row['project_name'], ENT_QUOTES ) . '</a>';
		$s .= $CR . '</td>';
		$s .= $CR . '<td nowrap="nowrap">' . htmlspecialchars( $row['user_username'], ENT_QUOTES ) . '</td>';
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . ($row['my_tasks'] ? $row['my_tasks'].' ('.$row['total_tasks'].')' : $row['total_tasks']);
		$s .= $CR . '</td>';
		$s .= $CR . '<td align="center">';
		$s .= $CT . '<input type="checkbox" name="project_id[]" value="'.$row['project_id'].'" />';
		$s .= $CR . '</td>';

		if($show_all_projects){
			$s .= $CR . '<td align="center" nowrap="nowrap">';
			$s .= $CT . $row['project_status'] == 0 ? $AppUI->_('Not Defined') : $AppUI->_($project_types[$row['project_status']]);
			$s .= $CR . '</td>';
		}
		
		$s .= $CR . '</tr>';
		echo $s;
	}
}
if ($none) {
	echo $CR . '<tr><td colspan="6">' . $AppUI->_( 'No projects available' ) . '</td></tr>';
}
?>
</table>

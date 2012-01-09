<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
//TODO: Convert Static Queries

GLOBAL $m, $a, $project_id, $f, $min_view, $query_string, $durnTypes;
GLOBAL $task_sort_item1, $task_sort_type1, $task_sort_order1;
GLOBAL $task_sort_item2, $task_sort_type2, $task_sort_order2;
GLOBAL $user_id, $dPconfig, $currentTabId, $currentTabName, $canEdit, $showEditCheckbox;
global $tasks_opened, $tasks_closed;

if (!(isset($showEditCheckbox))) {
	$showEditCheckbox = $dPconfig['direct_edit_assignment'];
}

/*
 tasks.php
 
 This file contains common task list rendering code used by
 modules/tasks/index.php and modules/projects/vw_tasks.php
 
 in
 
 External used variables:
 
 * $min_view: hide some elements when active (used in the vw_tasks.php)
 * $project_id
 * $f
 * $query_string
*/

if (empty($query_string)) {
	$query_string = "?m=$m&amp;a=$a";
}

// Number of columns (used to calculate how many columns to span things through)
$cols = 13;

$dbprefix = dPgetConfig('dbprefix','');

/*
 * Let's figure out which tasks are selected
 */

$tasks_closed = (($AppUI->getState('tasks_closed')) ? $AppUI->getState('tasks_closed') : array());
$tasks_opened = (($AppUI->getState('tasks_opened')) ? $AppUI->getState('tasks_opened') : array());

$task_id = intval(dPgetParam($_GET, 'task_id', 0));
$pinned_only = intval(dPgetParam($_GET, 'pinned', 0));
if (isset($_GET['pin'])) {
	$pin = intval(dPgetParam($_GET, 'pin', 0));
	
	$msg = '';
	
	// load the record data
	$sql = (($pin)
	        ?"INSERT INTO {$dbprefix}user_task_pin (user_id, task_id) VALUES($AppUI->user_id, $task_id)"
	        :"DELETE FROM {$dbprefix}user_task_pin WHERE user_id=$AppUI->user_id AND task_id=$task_id");
	
	if (!db_exec($sql)) {
		$AppUI->setMsg('ins/del err', UI_MSG_ERROR, true);
	}
	$AppUI->redirect('', -1);
}

if ($task_id > 0) {
	$_GET['open_task_id'] = $task_id;
}

//save place is at end
//$AppUI->savePlace();

// shall all tasks be either opened or opened?
$open_task_all = dPGetParam($_GET, 'open_task_all', 0);
$close_task_all = dPGetParam($_GET, 'close_task_all', 0);
// Closing and opening tasks only applies to dynamic tasks or tasks with children
$open_task_id = dPGetParam($_GET, 'open_task_id', 0);
$close_task_id = dPGetParam($_GET, 'close_task_id', 0);

if ($open_task_all) {
	$tasks_opened = array_unique($tasks_closed);
	$tasks_closed = array();
} else if ($close_task_all) {
	$tasks_closed = array_unique(array_merge($tasks_closed, $tasks_opened));
	$tasks_opened = array();
} else if ($open_task_id) {
	openClosedTask($open_task_id);
} else if ($close_task_id) {
	closeOpenedTask($close_task_id);
}


$durnTypes = dPgetSysVal('TaskDurationType');
$taskPriority = dPgetSysVal('TaskPriority');

$task_project = intval(dPgetParam($_GET, 'task_project', null));

$task_sort_item1 = dPgetCleanParam($_GET, 'task_sort_item1', '');
$task_sort_type1 = dPgetCleanParam($_GET, 'task_sort_type1', 0);
$task_sort_order1 = intval(dPgetParam($_GET, 'task_sort_order1', 0));
$task_sort_item2 = dPgetCleanParam($_GET, 'task_sort_item2', '');
$task_sort_type2 = dPgetCleanParam($_GET, 'task_sort_type2', 0);
$task_sort_order2 = intval(dPgetParam($_GET, 'task_sort_order2', 0));
if (isset($_POST['show_task_options'])) {
	$AppUI->setState('TaskListShowIncomplete', (int)dPgetParam($_POST, 'show_incomplete', 0));
}
$showIncomplete = $AppUI->getState('TaskListShowIncomplete', 0);

require_once $AppUI->getModuleClass('projects');
$project = new CProject;
$allowedProjects = $project->getAllowedSQL($AppUI->user_id);

if (count($allowedProjects)) {
	$where_list = implode(' AND ', $allowedProjects);
}

$working_hours = ($dPconfig['daily_working_hours']?$dPconfig['daily_working_hours']:8);

$q = new DBQuery;
$q->addTable('projects', 'prj');
$q->addQuery('company_name, project_id, project_color_identifier, project_name, '
             . ' SUM(t1.task_duration * t1.task_percent_complete'
             . ' * IF(t1.task_duration_type = 24, ' . $working_hours . ', t1.task_duration_type))'
             . ' / SUM(t1.task_duration * IF(t1.task_duration_type = 24, ' . $working_hours 
             . ', t1.task_duration_type)) AS project_percent_complete ');
$q->addJoin('companies', 'com', 'company_id = project_company');
$q->addJoin('tasks', 't1', 'prj.project_id = t1.task_project');
$q->addWhere($where_list . (($where_list) ? ' AND ' : '') . 't1.task_id = t1.task_parent');
$q->addGroup('project_id');
$q->addOrder('project_name');
$psql = $q->prepare();
$q->clear();


$q->addTable('projects', 'prj');
$q->addQuery('project_id, COUNT(t1.task_id) AS total_tasks');
$q->addJoin('tasks', 't1', 'prj.project_id = t1.task_project');
if ($where_list) {
	$q->addWhere($where_list);
}

$q->addGroup('project_id');
$psql2 = $q->prepare();
$q->clear();


$projects = array();
$canAccessTask = getPermission('tasks', 'access');
if ($canAccessTask) {
	$prc = db_exec($psql);
	echo db_error();
	while ($row = db_fetch_assoc($prc)) {
		$projects[$row['project_id']] = $row;
	}
	
	$prc2 = db_exec($psql2);
	echo db_error();
	while ($row2 = db_fetch_assoc($prc2)) {
		if ($projects[$row2['project_id']]) {
			array_push($projects[$row2['project_id']], $row2);
		}
	}
}

$join = '';
// pull tasks
$select = ('distinct tsk.task_id, task_parent, task_name, task_start_date, task_end_date, ' 
		   . 'task_dynamic, task_pinned, pin.user_id as pin_user, task_priority, ' 
		   . 'task_percent_complete, task_duration, task_duration_type, task_project, '
		   . 'task_description, task_owner, task_status, usernames.user_username, ' 
		   . 'usernames.user_id, task_milestone, assignees.user_username as assignee_username, ' 
		   . 'count(distinct assignees.user_id) as assignee_count, '
		   . 'co.contact_first_name, co.contact_last_name, ' 
		   . 'count(distinct fi.file_task) as file_count, ' 
		   . 'if (tlog.task_log_problem IS NULL, 0, tlog.task_log_problem) AS task_log_problem');
$from = $dbprefix.'tasks as tsk';
$mods = $AppUI->getActiveModules();
if (!empty($mods['history']) && getPermission('history', 'view')) {
	$select .= ', MAX(history_date) as last_update';
	$join = "LEFT JOIN {$dbprefix}history ON history_item = tsk.task_id AND history_table='tasks' ";
}
$join .= 'LEFT JOIN '.$dbprefix.'projects as prj ON project_id = task_project';
$join .= ' LEFT JOIN '.$dbprefix.'users as usernames ON task_owner = usernames.user_id';
// patch 2.12.04 show assignee and count
$join .= ' LEFT JOIN '.$dbprefix.'user_tasks as ut ON ut.task_id = tsk.task_id';
$join .= ' LEFT JOIN '.$dbprefix.'users as assignees ON assignees.user_id = ut.user_id';
$join .= ' LEFT JOIN '.$dbprefix.'contacts as co ON co.contact_id = usernames.user_contact';

// check if there is log report with the problem flag enabled for the task
$join .= (' LEFT JOIN '.$dbprefix.'task_log AS tlog ON tlog.task_log_task = tsk.task_id ' 
		  . 'AND tlog.task_log_problem > 0');

// to figure out if a file is attached to task
$join .= ' LEFT JOIN '.$dbprefix.'files as fi on tsk.task_id = fi.file_task';
$join .= ' LEFT JOIN '.$dbprefix.'user_task_pin as pin ON tsk.task_id = pin.task_id AND pin.user_id = ';
$join .= $user_id ? $user_id : $AppUI->user_id;

$where = $project_id ? ' task_project = '.$project_id : 'project_status <> 7';

if ($pinned_only) {
	$where .= ' AND task_pinned = 1 ';
}

$f = (($f) ? $f : '');
$never_show_with_dots = array(/*'children', */''); //used when displaying tasks
switch ($f) {
	case 'all':
		break;
	case 'myfinished7days':
		$where .= ' AND ut.user_id = '.$user_id;
	case 'allfinished7days':		 // patch 2.12.04 tasks finished in the last 7 days
		$from = $dbprefix.'user_tasks ut2, '.$from;
		$where .= (' AND task_project = prj.project_id AND ut2.task_id = tsk.task_id '
		           . "AND task_percent_complete = 100 AND task_end_date >= '"
		           . date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d')-7, date('Y'))) 
		           . "'");
		break;
	case 'children':
		$task_child_search = new CTask();
		$task_child_search->peek($task_id);
		$childrenlist = $task_child_search->getDeepChildren();
		
		$where .= ' AND tsk.task_id IN (' . implode(',', $childrenlist) . ')';
		break;
	case 'myproj':
		$where .= ' AND project_owner = ' . $user_id;
		break;
	case 'mycomp':
		if (!($AppUI->user_company)) {
			$AppUI->user_company = 0;
		}
		$where .= ' AND project_company = ' . $AppUI->user_company;
		break;
	case 'myunfinished':
		$from = $dbprefix.'user_tasks as ut3, '.$from;
		// This filter checks all tasks that are not already in 100%
		// and the project is not on hold nor completed
		// patch 2.12.04 finish date required to be consider finish
		$where .= (' AND task_project = prj.project_id AND ut3.user_id = ' . $user_id 
		           . ' AND ut3.task_id = tsk.task_id ' 
		           . "AND (task_percent_complete < 100 OR task_end_date = '') "
		           . 'AND prj.project_status <> 7 AND prj.project_status <> 4 ' 
		           . 'AND prj.project_status <> 5');
		break;
	case 'allunfinished':
		// patch 2.12.04 finish date required to be consider finish
		// patch 2.12.04 2, also show unassigned tasks
		$where .= (' AND task_project = prj.project_id ' 
		           . "AND (task_percent_complete < 100 OR task_end_date = '') " 
		           . 'AND prj.project_status <> 7 AND prj.project_status <> 4 ' 
		           . 'AND prj.project_status <> 5');
		break;
	case 'unassigned':
		$join .= ' LEFT JOIN '.$dbprefix.'user_tasks as ut4 ON tsk.task_id = ut4.task_id';
		$where .= ' AND ut4.task_id IS NULL';
		break;
	case 'taskcreated':
		$where .= ' AND tsk.task_owner = ' . $user_id;
		break;
 default:
		$from = $dbprefix.'user_tasks as ut5, '.$from;
		$where .= (' AND tsk.task_project = prj.project_id AND ut5.user_id = ' . $user_id 
		           . ' AND ut5.task_id = tsk.task_id');
		break;
}

if ($project_id && $showIncomplete) {
	$where .= ' AND (task_percent_complete < 100 or task_percent_complete is null)';
}

$task_status = 0;
if ($min_view && isset($_GET['task_status'])) {
	$task_status = intval(dPgetParam($_GET, 'task_status', null));
} else if (!($currentTabName)) {
	// If we aren't tabbed we are in the tasks list.
	$task_status = intval($AppUI->getState('inactive'));
} else if (mb_stristr($currentTabName, 'inactive')) {
	$task_status = '-1';
}

$where .= ' AND task_status = ' . $task_status;

// patch 2.12.04 text search
if ($search_text = $AppUI->getState('searchtext')) {
	$where .= (" AND (task_name LIKE ('%{$search_text}%') " 
	           . "OR task_description LIKE ('%{$search_text}%'))");
}

// filter tasks considering task and project permissions
$projects_filter = '';
$tasks_filter = '';

// TODO: Enable tasks filtering

$allowedProjects = $project->getAllowedSQL($AppUI->user_id, 'task_project');
if (count($allowedProjects)) {
	$where .= ' AND ' . implode(' AND ', $allowedProjects);
}

//
$obj = new CTask;
$allowedTasks = $obj->getAllowedSQL($AppUI->user_id, 'task_id');
if (count($allowedTasks)) {
	$where .= ' AND ' . implode(' AND ', $allowedTasks);
}
$allowedChildrenTasks = $obj->getAllowedSQL($AppUI->user_id, 'task_parent');
if (count($allowedChildrenTasks)) {
	$where .= ' AND ' . implode(' AND ', $allowedChildrenTasks);
}
// echo "<pre>$where</pre>";

// Filter by company
if (! $min_view && $f2 != 'all') {
	$join .= ' LEFT JOIN '.$dbprefix.'companies com ON com.company_id = prj.project_company';
	$where .= ' AND com.company_id = ' . intval($f2);
}

// patch 2.12.04 ADD GROUP BY clause for assignee count
$tsql = ('SELECT ' . $select . ' FROM (' . $from . ') ' . $join 
		 . ' WHERE ' . $where . ' GROUP BY task_id ORDER BY project_id, task_start_date');

//echo "<pre>$tsql</pre>";

if ($canAccessTask) {
	$ptrc = db_exec($tsql);
	if ($ptrc != false) {
		$nums = db_num_rows($ptrc);
	}
	echo db_error();
} 
else {
	$nums = 0;
}

//pull the tasks into an array
for ($x=0; $x < $nums; $x++) {
	$row = db_fetch_assoc($ptrc);
	
	//add information about assigned users into the page output
	$ausql = ('SELECT ut.user_id, u.user_username, contact_email, ut.perc_assignment, ' 
			  . 'SUM(ut.perc_assignment) AS assign_extent, contact_first_name, contact_last_name ' 
			  . 'FROM '.$dbprefix.'user_tasks ut LEFT JOIN '.$dbprefix.'users u ON u.user_id = ut.user_id ' 
			  . 'LEFT JOIN '.$dbprefix.'contacts ON u.user_contact = contact_id ' 
			  . 'WHERE ut.task_id=' . $row['task_id'] . ' GROUP BY ut.user_id ' 
			  . 'ORDER BY ut.perc_assignment desc, u.user_username');
	
	$assigned_users = array ();
	$paurc = db_exec($ausql);
	$nnums = db_num_rows($paurc);
	echo db_error();
	for ($xx=0; $xx < $nnums; $xx++) {
		$row['task_assigned_users'][] = db_fetch_assoc($paurc);
	}
	//pull the final task row into array
	$projects[$row['task_project']]['tasks'][] = $row;
}

?>

<script type="text/javaScript" language="javascript">
function toggle_users(id) {
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "inline" : "none";
}

<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($showEditCheckbox) {
?>
function checkAll(project_id) {
	var f = eval('document.assFrm' + project_id);
	var cFlag = f.master.checked ? false : true;
	
	for (var i=0;i< f.elements.length;i++) {
		var e = f.elements[i];
		// only if it's a checkbox.
		if (e.type == "checkbox" && e.checked == cFlag && e.name != 'master') {
			e.checked = !e.checked;
		}
	}

}

function chAssignment(project_id, rmUser, del) {
	var f = eval('document.assFrm' + project_id);
	var fl = f.add_users.length-1;
	var c = 0;
	var a = 0;
	
	f.hassign.value = "";
	f.htasks.value = "";
	
	// harvest all checked checkboxes (tasks to process)
	for (var i=0;i< f.elements.length;i++) {
		var e = f.elements[i];
		// only if it's a checkbox.
		if (e.type == "checkbox" && e.checked == true && e.name != 'master') {
			c++;
			f.htasks.value = f.htasks.value +", "+ e.value;
		}
	}
	
	// harvest all selected possible User Assignees
	for (fl; fl > -1; fl--) {
		if (f.add_users.options[fl].selected) {
			a++;
			f.hassign.value = ", " + f.hassign.value +", "+ f.add_users.options[fl].value;
		}
	}
	
	if (del == true) {
		if (c == 0) {
			alert ('<?php echo $AppUI->_('Please select at least one Task!', UI_OUTPUT_JS); ?>');
		} 
		else if (a == 0 && rmUser == 1) {
			alert ('<?php echo $AppUI->_('Please select at least one Assignee!', UI_OUTPUT_JS); ?>');
		} 
		else if (confirm('<?php echo $AppUI->_('Are you sure you want to unassign the User from Task(s)?', UI_OUTPUT_JS); ?>')) {
			f.del.value = 1;
			f.rm.value = rmUser;
			f.project_id.value = project_id;
			f.submit();
		}
	}
	else {
		
		if (c == 0) {
			alert ('<?php echo $AppUI->_('Please select at least one Task!', UI_OUTPUT_JS); ?>');
		} 
		else if (a == 0) {
			alert ('<?php echo $AppUI->_('Please select at least one Assignee!', UI_OUTPUT_JS); ?>');
		} 
		else {
			f.rm.value = rmUser;
			f.del.value = del;
			f.project_id.value = project_id;
			f.submit();
		}
	}
}
<?php } ?>
</script>


<?php if ($project_id) { ?>
<table width='100%' border='0' cellpadding='1' cellspacing='0'>
<form name='task_list_options' method='POST' action='<?php echo $query_string; ?>'>
<input type='hidden' name='show_task_options' value='1' />
<tr>
	<?php if ($min_view) { ?>
	<td>
	<a href="<?php echo 'index.php'.$query_string.'&amp;open_task_all=1'; ?>"><?php 
echo $AppUI->_('Expand All'); ?></a> : 
	<a href="<?php echo 'index.php'.$query_string.'&amp;close_task_all=1'; ?>"><?php 
echo $AppUI->_('Collapse All'); ?></a> 
	  &nbsp;&nbsp;
	  </td>
	<?php } 
?>
  <td align='right'>
	<table>
	<tr>
	  <td><?php echo $AppUI->_('Show');?>:</td>
	  <td>
	  <input type="checkbox" name="show_incomplete" id="show_incomplete" onclick="javascript:document.task_list_options.submit();" 
	   <?php echo $showIncomplete ? 'checked="checked"' : ''; ?> />
	  </td>
	  <td><label for="show_incomplete"><?php echo $AppUI->_('Incomplete Tasks Only'); ?></label></td>
	</tr>
	</table>
  </td>
</tr>
</form>
</table>
<?php } ?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
  <th width="10">&nbsp;</th>
  <th width="10"><?php echo $AppUI->_('Pin'); ?></th>
  <th width="10"><?php echo $AppUI->_('New Log'); ?></th>
  <th width="20"><?php echo $AppUI->_('Work');?></th>
  <th align="center"><?php sort_by_item_title('P', 'task_log_problem_priority', SORT_NUMERIC); ?></th>
  <th width="200"><?php sort_by_item_title('Task Name', 'task_name', SORT_STRING);?></th>
  <th nowrap="nowrap"><?php sort_by_item_title('Task Creator', 'user_username', SORT_STRING);?></th>
  <th nowrap="nowrap"><?php echo $AppUI->_('Assigned Users')?></th>
  <th nowrap="nowrap"><?php sort_by_item_title('Start Date', 'task_start_date', SORT_NUMERIC);?></th>
  <th nowrap="nowrap"><?php sort_by_item_title('Duration', 'task_duration', SORT_NUMERIC);?>&nbsp;&nbsp;</th>
  <th nowrap="nowrap"><?php sort_by_item_title('Finish Date', 'task_end_date', SORT_NUMERIC);?></th>
<?php if (!empty($mods['history']) && getPermission('history', 'view')) { ?>
  <th nowrap="nowrap"><?php sort_by_item_title('Last Update', 'last_update', SORT_NUMERIC);?></th>
<?php } else { $cols--; } ?>
  <?php if ($showEditCheckbox) { echo '<th width="1">&nbsp;</th>'; } else { $cols--; } ?>
</tr>
<?php
reset($projects);

foreach ($projects as $k => $p) {
	$tnums = count(@$p['tasks']);
	// don't show project if it has no tasks
	// patch 2.12.04, show project if it is the only project in view
	if ($tnums > 0 || $project_id == $p['project_id']) {
		//echo '<pre>'; print_r($p); echo '</pre>';
		if (!$min_view) {
			// not minimal view
?>
<form name="assFrm<?php echo($p['project_id']) ?>" 
			action="index.php?m=<?php echo($m); ?>&amp;a=<?php echo($a); ?>" method="post">
<input type="hidden" name="del" value="1" />
<input type="hidden" name="rm" value="0" />
<input type="hidden" name="store" value="0" />
<input type="hidden" name="dosql" value="do_task_assign_aed" />
<input type="hidden" name="project_id" value="<?php echo($p['project_id']); ?>" />
<input type="hidden" name="hassign" />
<input type="hidden" name="htasks" />

<tr>
  <td>
  <a href="index.php?m=tasks&amp;f=<?php echo $f;?>&amp;project_id=<?php echo $project_id ? 0 : $k;?>">
  <img src="./images/icons/<?php 
echo (($project_id) ? 'expand.gif' : 'collapse.gif'); 
?>" width="16" height="16" border="0" alt="<?php 
echo (($project_id) ? $AppUI->_('show other projects') : $AppUI->_('show only this project')); ?>" />
  </a>
  </td>
  <td colspan="<?php echo $showEditCheckbox ? $cols-4 : $cols-1; ?>">
  <table width="100%" border="0">
  <tr>
	<!-- patch 2.12.04 display company name next to project name -->
	<td nowrap style="border: outset #eeeeee 2px;background-color:#<?php 
echo @$p['project_color_identifier']; ?>">
	<a href="?m=projects&amp;a=view&amp;project_id=<?php echo $k;?>">
	<span style="color:<?php 
echo bestColor(@$p['project_color_identifier']); ?>;text-decoration:none;">
	<strong><?php echo @$p['company_name'].' :: '.@$p['project_name'];?></strong></span></a>
	</td>
	<td width="<?php echo (101 - intval(@$p['project_percent_complete']));?>%">
	<?php echo (intval(@$p['project_percent_complete']));?>%
	</td>
  </tr>
  </table>
  </td>
  <td colspan="3" align="right" valign="middle">
  <table width="100%" border="0">
  <tr>
	<td align="right">
<?php 
			$hasEditableTask = ((isset($canEdit) && $canEdit) ? true : false);
			if (is_array($p['tasks']) && !($hasEditableTask)) {
				foreach ($p['tasks'] as $i => $t1) {
					$hasEditableTask = (getPermission('tasks', 'edit',  $t1['task_id']) 
					                    || $hasEditableTask);
					if ($hasEditableTask) {
						break;
					}
				}
			}
			if ($showEditCheckbox && ($hasEditableTask)) {
				// get Users with all Allocation info (e.g. their freeCapacity)
				$tempoTask = new CTask();
				$userAlloc = $tempoTask->getAllocation('user_id');
?>
	<select name="add_users" style="width:200px" size="2" multiple="multiple" class="text" 
	 ondblclick="javascript:chAssignment(<?php echo($p['project_id']); ?>, 0, false)">
<?php 
				foreach ($userAlloc as $v => $u) {
					echo ('	  <option value="' . $u['user_id'] . '">' . dPformSafe($u['userFC']) 
					      . "</option>\n");
				}
?>
	</select>
	</td>
	<td align="center">
<?php
				echo (' <a href="javascript:chAssignment(' . $p['project_id'] . ', 0, 0);">' 
				      . dPshowImage(dPfindImage('add.png', 'tasks'), 16, 16, 'Assign Users', 
				                    'Assign selected Users to selected Tasks') . "</a>\n");
				
				echo (' <a href="javascript:chAssignment(' . $p['project_id'] . ', 1, 1);">' 
					  . dPshowImage(dPfindImage('remove.png', 'tasks'), 16, 16, 'Unassign Users', 
								   'Unassign Users from Task') . "</a>\n");
?>
	<br />

	<select class="text" name="percentage_assignment" title="<?php echo ($AppUI->_('Assign with Percentage')); ?>" >
<?php
				for ($i = 0; $i <= 100; $i+=5) {
					echo ("\t<option " . (($i==30) ? 'selected="true"' : '') . ' value="' . $i 
					      . '">' . $i . '%</option>');
				}
?>
	</select>
<?php 
			}
?>
	</td>
  </tr>
  </table>
  </td>
</tr>
<?php
		}
		
		if ($task_sort_item1 != '') {
			if ($task_sort_item2 != '' && $task_sort_item1 != $task_sort_item2) {
				$p['tasks'] = array_csort($p['tasks'], $task_sort_item1, $task_sort_order1, 
										  $task_sort_type1, $task_sort_item2, $task_sort_order2, 
										  $task_sort_type2);
			} else {
				$p['tasks'] = array_csort($p['tasks'], $task_sort_item1, $task_sort_order1, 
										  $task_sort_type1);
			}
		} else {
			/* we have to calculate the end_date via start_date+duration for 
			 ** end='0000-00-00 00:00:00' if array_csort function is not used
			 ** as it is normally done in array_csort function in order to economise
			 ** cpu time as we have to go through the array there anyway
			 */
			if (is_array($p['tasks'])) {
				foreach ($p['tasks'] as $j => $task_change_end_date) {
					if ($task_change_end_date['task_end_date'] == '0000-00-00 00:00:00') {
						 $task_change_end_date['task_end_date'] = calcEndByStartAndDuration($task_change_end_date);
					}
				}
			}
		}
		
		global $tasks_filtered, $children_of;
		//get list of task ids and set-up array of children
		if (is_array($p['tasks'])) {
			foreach ($p['tasks'] as $i => $t) {
				$tasks_filtered[] = $t['task_id']; 
				$children_of[$t['task_parent']] = (($children_of[$t['task_parent']])
												   ?$children_of[$t['task_parent']]:
												   array());
				if ($t['task_parent'] != $t['task_id']) {
					array_push($children_of[$t['task_parent']], $t['task_id']);
				}
			}
		}
		
		//start displaying tasks
		if (is_array($p['tasks'])) {
			$summaries = array('duration' => 0, 'start_date' => date('Y-m-d'), 'end-date' => '');
			foreach ($p['tasks'] as $i => $t1) {
				// Record summaries
				if (!$t1['task_dynamic']) {
					$summaries['duration'] += $t1['task_duration'];
				}
				if ($t1['task_start_date'] < $summaries['start_date']) {
					$summaries['start_date'] = $t1['task_start_date'];
				}
				if ($t1['task_end_date'] > $summaries['end_date']) {
					$summaries['end_date'] = $t1['task_end_date'];
				}
				if ($task_sort_item1) {
					// already user sorted so there is no call for a "task tree" or "open/close" links
					showtask($t1, -1, true, false, true);
				} else {
					if ($t1['task_parent'] == $t1['task_id']) {
						$is_opened = (!(in_array($t1['task_id'], $tasks_closed)));
						
						//check for child
						$no_children = empty($children_of[$t1['task_id']]);
						
						showtask($t1, 0, $is_opened, false, $no_children);
						if ($is_opened && !($no_children)) {
							findchild($p['tasks'], $t1['task_id']);
						}
					} else if (!(in_array($t1['task_parent'], $tasks_filtered))) { 
						/*
						 * don't "mess with" display when showing certain views 
						 * (or similiar filters that don't involve "breaking apart" a task tree 
						 * even though they might not use this page ever)
						 */
						if ((in_array($f, $never_show_with_dots))) {
						  showtask($t1, 1, true, false, true); 
						} else {
							//display as close to "tree-like" as possible
							$is_opened = (!(in_array($t1['task_id'], $tasks_closed)));
							
							//check for child
							$no_children = empty($children_of[$t1['task_id']]);
							
							$my_level = (($task_id && $t1['task_parent'] == $task_id) ? 0 : -1);
							showtask($t1, $my_level, $is_opened, false, $no_children); // indeterminate depth for child task
							if ($is_opened && !($no_children)) {
								findchild($p['tasks'], $t1['task_id']);
							}
						}
					}
					/*
					 * MerlinYoda: Not 100% sure if moving code from below to above always puts orphan trees 
					 * closer to their ancestors. At worst it just displays orphans a little earlier
					 */
				}
			}
		}

$df = $AppUI->getPref('SHDATEFORMAT');
?>
<tr>
	<td colspan="<?php echo $cols - 4 ?>"><?php echo $AppUI->_('Summaries'); ?>: </td>
	<td><?php $summary_date = new CDate($summaries['start_date']); echo $summary_date->format($df); ?></td>
	<td align="center"><?php echo $summaries['duration'] ?> <?php echo $AppUI->_('hours'); ?></td>
	<td><?php $summary_date = new CDate($summaries['end_date']); echo $summary_date->format($df); ?></td>
	<td></td>
</tr>
<?php
		if ($tnums && $dPconfig['enable_gantt_charts'] && !$min_view) { 
?>
<tr>
  <td colspan="<?php echo $cols; ?>" align="right">
	<a href="<?php echo 'index.php'.$query_string.'&amp;open_task_all=1';?>"><?php echo $AppUI->_('Expand All'); ?></a> :
	<a href="<?php echo 'index.php'.$query_string.'&amp;close_task_all=1';?>"><?php echo $AppUI->_('Collapse All'); ?></a>
	&nbsp;(<?php echo $AppUI->_('On Page'); ?>)&nbsp;
<!-- removed project-level report buttons per Mantis Report #2374
  <input type="button" class="button" value="<?php echo $AppUI->_('Reports');?>" 
   onclick="javascript:window.location='index.php?m=projects&a=reports&project_id=<?php echo $k;?>';" />
-->
  <input type="button" class="button" value="<?php echo $AppUI->_('Gantt Chart');?>" 
		onclick="javascript:window.location='index.php?m=tasks&amp;a=viewgantt&amp;project_id=<?php
			echo $k;?>';" />
	</form>
  </td>
</tr>
<?php
		}
   }
}


$AppUI->setState('tasks_opened', $tasks_opened);
$AppUI->setState('tasks_closed', $tasks_closed);

$AppUI->savePlace();

?>
</table>
<table>
<tr>
  <td><?php echo $AppUI->_('Key'); ?>:&nbsp;&nbsp;</td>
  <td style="background-color:#FFFFFF; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Future Task'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#E6EEDD; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Started and on time'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#FFEEBB; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Should have started'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#CC6666; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Overdue'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#AADDAA; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Done'); ?>&nbsp;&nbsp;
	<?php if ($min_view) { ?>
	&nbsp;&nbsp;<a href="<?php echo 'index.php'.$query_string.'&amp;open_task_all=1'; ?>"><?php 
echo $AppUI->_('Expand All'); ?></a> : 
	<a href="<?php echo 'index.php'.$query_string.'&amp;close_task_all=1'; ?>"><?php 
echo $AppUI->_('Collapse All'); ?></a> 
	<?php } 
?>
	</td>
</tr>
</table>
<?php 

//echo '<pre>Opened ::'; print_r($tasks_opened); echo '</pre><br />';
//echo '<pre>Closed ::'; print_r($tasks_closed); echo '</pre><br />';

?>

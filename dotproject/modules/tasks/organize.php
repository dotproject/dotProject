<?php  // $Id$
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}


$project_id = intval(dPgetParam($_GET, 'project_id', 0));
//$date = intval(dPgetParam($_GET, 'date', ''));
$user_id = $AppUI->user_id;
$no_modify = false;

$sort = dPgetParam($_REQUEST, 'sort', 'task_end_date');

if (getPermission('admin', 'view')) { 
	$other_users = true;
	//lets see if the user wants to see anothers user mytodo
	if (($show_uid = dPgetParam($_REQUEST, 'show_user_todo', 0)) != 0) { 
		$user_id = $show_uid;
		$no_modify = true;
		$AppUI->setState('user_id', $user_id);
	} else {
		//$user_id = $AppUI->getState('user_id');
	}
}

// check permissions
$canEdit = getPermission($m, 'edit');

// if task priority set and items selected, do some work
$action = dPgetParam($_POST, 'action', 99);
$selected = dPgetParam($_POST, 'selected', 0);

if ($selected && count($selected)) {
	$new_task = dPgetParam($_POST, 'new_task', -1);
	$new_project = dPgetParam($_POST, 'new_project', $project_id);
	
	foreach ($selected as $key => $val) {
		$t = new CTask();
		$t->load($val);
		if (isset($_POST['include_children']) && $_POST['include_children']) {
			$children = $t->getDeepChildren();
		}
		
		if ($action == 'f') {
			//mark task as completed
			if (getPermission('tasks', 'edit', $t->task_id)) {
				$t->task_percent_complete = 100;
				$t->store();
			}
			if (isset($children)) {
				foreach ($children as $child_id) {
					if (getPermission('tasks', 'edit', $child_t->task_id)) {
						$child_t = new CTask();
						$child_t->load($child_id);
						$child_t->task_percent_complete = 100;
						$child_t->store();
					}
				}
			}
		} else if ($action == 'd') {
			//delete task
			$t->delete();
			//Now task deletion deletes children no matter what.
			/*
			//delete children
			if (isset($children)) {
				foreach ($children as $child) {
					$t->load($child);
					$t->delete();
				}
			}
			*/
		} else if ($action == 'm') {
			//move task
			if (isset($children)) {
				$t->deepMove($new_project, $new_task);
			} else {
				$t->move($new_project, $new_task);
			}
			$t->store();
		} else if ($action == 'c') {
			//copy task
			if (isset($children)) {
				$t2 = $t->deepCopy($new_project, $new_task);
			} else {
				$t2 = $t->copy($new_project, $new_task);
			}
			$t2->store();
		} else if ($action > -2 && $action < 2) {
			//set priority
			$t->task_priority = $action;
			$t->store();
			if (isset($children)) {
				foreach ($children as $child_id) {
					$child_t = new CTask();
					$child_t->load($child_id);
					$child_t->task_priority = $action;
					$child_t->store();
				}
			}
		}
	}
}

$AppUI->savePlace();

$proj = new CProject;
$tobj = new CTask;
$q = new DBQuery;

$allowedProjects = $proj->getAllowedSQL($AppUI->user_id, 'p.project_id');
$allowedTasks = $tobj->getAllowedSQL($AppUI->user_id, 't.task_id');

//query my sub-tasks (ignoring task parents)
$q->addTable('tasks', 't');
$q->innerJoin('projects', 'p', 'p.project_id = t.task_project');
$q->addQuery('t.*, p.project_name, p.project_id, p.project_color_identifier');
if ($project_id) {
	$q->addWhere('p.project_id = ' . $project_id);
}
if (count($allowedTasks)) {
	$q->addWhere($allowedTasks);
}
if (count($allowedProjects)) {
	$q->addWhere($allowedProjects);
}
$q->addGroup('t.task_id');
$q->addOrder($sort . ', t.task_priority DESC');
//echo ('<pre>' . $q->prepare(); . '</pre>');
$tasks = $q->loadList();

$priorities = array('1' => 'high', '0' => 'normal', '-1' => 'low');
$durnTypes = dPgetSysVal('TaskDurationType');

if (!@$min_view) {
	$titleBlock = new CTitleBlock('Organize Tasks', 'applet-48.png', $m, "$m.$a");
	$titleBlock->addCrumb('?m=tasks', 'tasks list');
	if ($project_id) {
		$titleBlock->addCrumb(('?m=projects&amp;a=view&amp;project_id=' . $project_id), 'view project');
	}
	$titleBlock->show();
}

function showchildren($id, $level=1) {
	global $tasks;
	$t = $tasks; //otherwise, $tasks is accessed from a static context and doesn't work.
	foreach ($t as $task) {
		//echo $id . '==> ' . $task['task_parent'] . '==' . $id . '<br>';
		if ($task['task_parent'] == $id && $task['task_parent'] != $task['task_id']) {
			showtask_edit($task, $level);
			showchildren($task['task_id'], $level+1);
		}
	}
}

/*
 * show a task - at a sublevel
 */
function showtask_edit($task, $level=0) {
	global $AppUI, $durnTypes, $now, $df;
	
	$style = '';
	$start = intval(@$task['task_start_date']) ? new CDate($task['task_start_date']) : null;
	$end = intval(@$task['task_end_date']) ? new CDate($task['task_end_date']) : null;
	
	if (!$end && $start) {
		$end = $start;
		$end->addSeconds(@$task['task_duration'] * $task['task_duration_type'] * SEC_HOUR);
	}

	if ($now->after($start) && $task['task_percent_complete'] == 0) {
		$style = 'background-color:#ffeebb';
	} else if ($now->after($start)) {
		$style = 'background-color:#e6eedd';
	}

	if ($now->after($end)) {
		$style = (($end) ? 'background-color:#cc6666;color:#ffffff' 
		          : 'background-color:lightgray;');
	} 
	$days = (($start) ? ($end->dateDiff($now)) : 0);
	
	if ($task['task_percent_complete'] == 100) {
		$days = 'n/a';
		$style = 'background-color:#aaddaa; color:#00000;';
	}
?>
<tr>
	<td>
<?php 
	if (getPermission('tasks', 'edit', $task['task_id'])) { 
?>
		<a href="?m=tasks&amp;a=addedit&amp;task_id=<?php echo $task['task_id']; ?>">
		<img src="./images/icons/pencil.gif" alt="Edit Task" border="0" width="12" height="12" />
		</a>
<?php } ?>
	</td>
	<td align="right"><?php echo intval($task['task_percent_complete']); ?>%</td>
	<td>
<?php 
	if ($task['task_priority'] < 0) {
		echo '<img src="./images/icons/low.gif" width="13" height="16" />';
	} else if ($task['task_priority'] > 0) {
		echo '<img src="./images/icons/' . $task['task_priority'] .'.gif" width="13" height="16" />';
	}
?>
	</td>

	<td width="50%">
		<?php 
		for ($i = 1; $i < $level; $i++) {
			echo '&nbsp;&nbsp;';
		}
		if ($level > 0) {
			echo '<img src="./images/corner-dots.gif" width="16" height="12" border="0" />'; 
		}
?>	
		<a href="?m=tasks&amp;a=view&amp;task_id=<?php echo $task['task_id']; ?>" title="<?php
		echo (((isset($task['parent_name'])) 
		       ? ('*** ' . $AppUI->_('Parent Task') . ' ***' . "\n" 
		          . htmlspecialchars($task['parent_name'], ENT_QUOTES) . "\n\n") : '') 
		      .	'*** ' . $AppUI->_('Description') . ' ***' . "\n" 
		      . htmlspecialchars($task['task_description'], ENT_QUOTES)) ?>">
					<?php echo htmlspecialchars($task['task_name'], ENT_QUOTES); ?>
		</a>
	</td>
	<td style="<?php echo $style; ?>">
<?php
	echo $task['task_duration'] . ' ' . $AppUI->_($durnTypes[$task['task_duration_type']]);
?>
	</td>
	<td nowrap align="right" style="<?php echo $style; ?>"><?php echo $days; ?></td>
	<td><input type="checkbox" name="selected[]" value="<?php echo $task['task_id'] ?>"></td>
</tr>
<?php } // END of displaying tasks function.}}}
?>

<form name="form" method="post" action="index.php?<?php 
echo "m=$m&amp;a=$a&amp;project_id=$project_id"; ?>">
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl" summary="task listing">
<tr>
	<th width="20" colspan="2"><?php echo $AppUI->_('Progress'); ?></th>
	<th width="15" align="center"><?php echo $AppUI->_('P'); ?></th>
	<th>
		<a class="hdr" href="?m=tasks&amp;a=organize&amp;project_id=<?php 
echo $project_id; ?>&amp;sort=task_name">
		<?php echo $AppUI->_('Task'); ?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a class="hdr" href="?m=tasks&amp;a=organize&amp;project_id=<?php 
echo $project_id; ?>&amp;sort=task_duration">
		<?php echo $AppUI->_('Duration'); ?>
		</a>
	</th>
	<th nowrap="nowrap">
		<a class="hdr" href="index.php?m=tasks&a=organize&project_id=<?php 
echo $project_id; ?>&sort=task_end_date">
		<?php echo $AppUI->_('Due In'); ?>
		</a>
	</th>
	<th width="60">
		<input type="checkbox" name="toggleSelects" id="toggleSelects" onclick="toggleTasks();"/>
		<?php echo $AppUI->_('Select'); ?>
	</th>
</tr>

<?php

/*** Tasks listing ***/
$now = new CDate();
$df = $AppUI->getPref('SHDATEFORMAT');

foreach ($tasks as $task) {
	if ($task['task_id'] == $task['task_parent']) {
		showtask_edit($task);
		showchildren($task['task_id']);
	}
}
?>
</table>

<?php
$actions = array();

$actions['c'] = $AppUI->_('Copy', UI_OUTPUT_JS);
if ($canEdit) {
	$actions['m'] = $AppUI->_('Move', UI_OUTPUT_JS);
	$actions['d'] = $AppUI->_('Delete', UI_OUTPUT_JS);
	$actions['f'] = $AppUI->_('Mark as Finished', UI_OUTPUT_JS);
	foreach ($priorities as $k => $v) {
		$actions[$k] = $AppUI->_('set priority to ' . $v, UI_OUTPUT_JS);
	}
}

$deny = $proj->getDeniedRecords($AppUI->user_id);
$q = new DBQuery;
$q->addTable('projects','p');
$q->addQuery('p.project_id, p.project_name');
if ($deny) {
	$q->addWhere('p.project_id NOT IN (' . implode(',', $deny) . ')');
}
$q->addOrder('p.project_name');
$projects = db_loadHashList($q->prepare(true), 'project_id');
$p[0] = $AppUI->_('[none]');
foreach ($projects as $proj) {
	$p[$proj[0]] = $proj[1];
}
if ($project_id) {
	$p[$project_id] = $AppUI->_('[same project]');
}

natsort($p);
$projects =  $p;

$ts[0] = $AppUI->_('[top task]');
foreach ($tasks as $t) {
	$ts[$t['task_id']] = $t['task_name'];
}
?>

<input type="checkbox" name="include_children" id="include_children" value='1' /><label for="include_children"><?php echo $AppUI->_('IncludeChildren'); ?></label><br />
<table summary="action project tasks">
<tr>
	<th><?php echo $AppUI->_('Action'); ?>: </th>
	<th><?php echo $AppUI->_('Project'); ?>: </th>
	<th><?php echo $AppUI->_('Task'); ?>: </th>
</tr>
<tr>
	<td><?php echo arraySelect($actions, 'action', '', '0'); ?></td>
	<td><?php echo arraySelect($projects, 'new_project', ' onchange="javascript:updateTasks();"', '0'); ?></td>
	<td><?php echo ($ts)?arraySelect($ts, 'new_task', '', '0'):''; ?></td>
	<td><input type="submit" class="button" value="<?php 
echo $AppUI->_('update selected tasks'); ?>"></td>
</tr>
</table>
</form>

<table summary="task status">
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
  <td>=<?php echo $AppUI->_('Done'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:lightgray; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Unknown'); ?>&nbsp;&nbsp;</td>
</tr>
</table>

<script language="javascript">
	function updateTasks() {
		var proj = document.forms['form'].new_project.value;
		var tasks = new Array();
		var sel = document.forms['form'].new_task;
		while (sel.options.length)
			sel.options[0] = null;
		sel.options[0] = new Option('loading...', -1);
		frames['thread'].location.href = './index.php?m=tasks&a=listtasks&project=' + proj;
	}
	function toggleTasks() {
		var current_select = document.getElementById('toggleSelects');
		var flag = current_select.checked;
		var selects = document.getElementsByTagName('input');
		for (var i = 0; i < selects.length; i++) {
			if (selects[i].name == 'selected[]') {
				selects[i].checked = flag;
			}
		}
	}
</script>

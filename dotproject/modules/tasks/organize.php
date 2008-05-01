<?php  // $Id$
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

// Project status from sysval, defined as a constant
$perms =& $AppUI->acl();

$project_id = intval(dPgetParam($_GET, 'project_id', 0));
//$date       = intval(dPgetParam($_GET, 'date', ''));
$user_id    = $AppUI->user_id;
$no_modify	= false;

$sort = dPgetParam($_REQUEST, 'sort', 'task_end_date');

if($perms->checkModule("admin","view")){ // let's see if the user has sysadmin access
	$other_users = true;
	if(($show_uid = dPgetParam($_REQUEST, "show_user_todo", 0)) != 0){ // lets see if the user wants to see anothers user mytodo
		$user_id = $show_uid;
		$no_modify = true;
		$AppUI->setState("user_id", $user_id);
	} else {
//		$user_id = $AppUI->getState("user_id");
	}
}

// check permissions
$canEdit = $perms->checkModule($m, 'edit');

// if task priority set and items selected, do some work
$action = dPgetParam($_POST, 'action', 99);
$selected = dPgetParam($_POST, 'selected', 0);

if ($selected && count($selected)) {
	$new_task = dPgetParam($_POST, 'new_task', -1);
	$new_project = dPgetParam($_POST, 'new_project', $project_id);

	foreach ($selected as $key => $val) {
		$t = &new CTask();
		$t->load($val);
		if (isset($_POST['include_children']) && $_POST['include_children']) {
			$children = $t->getDeepChildren();
		}
		if ($action == 'f') { // Mark FINISHED
			// mark task as completed
			$sql = ('UPDATE tasks SET task_percent_complete=100 WHERE task_id' 
			        . ((isset($children)) ? ' IN (' . implode(', ', $children) . ', $val)' 
			           : '=' . $val));
		} else if ($action == 'd') { // DELETE
			// delete task
			$t->delete();
			// Now task deletion deletes children no matter what.
			/*
			// delete children
			if (isset($children)) {
				foreach($children as $child) {
					$t->load($child);
					$t->delete();
				}
			}
			*/
		} else if ($action == 'm') { // MOVE
			if (isset($children)) {
				$t->deepMove($new_project, $new_task);
			} else {
				$t->move($new_project, $new_task);
			}
			$t->store();
		} else if ($action == 'c') { // COPY
			if (isset($children)) {
				$t2 = $t->deepCopy($new_project, $new_task);
			} else {
				$t2 = $t->copy($new_project, $new_task);
			}
			$t2->store();
		} else if ($action > -2 && $action < 2) { // Set PRIORITY
			// set priority
			$sql = ('UPDATE tasks SET task_priority=$action WHERE task_id'
			        . ((isset($children)) ? ' IN (' . implode(',', $children) . ', $val)' 
			           : '=' . $val));
		}
		
		if (isset($sql)) {
			db_exec($sql);
		}
	}
}

$AppUI->savePlace();

$proj =& new CProject;
$tobj =& new CTask;

$allowedProjects = $proj->getAllowedSQL($AppUI->user_id);
$allowedTasks = $tobj->getAllowedSQL($AppUI->user_id, 'task_id');

// query my sub-tasks (ignoring task parents)

$sql = ('SELECT t.*, p.project_name, p.project_id, p.project_color_identifier' 
        . ' FROM tasks AS t INNER JOIN projects AS p ON p.project_id = t.task_project');
$where = array();
if ($project_id) {
	$where[] = 'project_id = ' . $project_id;
}
if (count($allowedTasks)) {
	$where[] = implode(' AND ', $allowedTasks);
}
if (count($allowedProjects)) {
	$where[] = implode(' AND ', $allowedProjects);
}

if ($where) {
  $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .=   ' GROUP BY task_id ORDER BY ' . $sort . ', task_priority DESC';
// echo "<pre>$sql</pre>";
$tasks = db_loadList($sql);

$priorities = array('1' => 'high', '0' => 'normal', '-1' => 'low');

$durnTypes = dPgetSysVal('TaskDurationType');

if (!@$min_view) {
	$titleBlock = new CTitleBlock('Organize Tasks', 'applet-48.png', $m, "$m.$a");
	$titleBlock->addCrumb("?m=tasks", "tasks list");
	if ($project_id) {
		$titleBlock->addCrumb("?m=projects&a=view&project_id=$project_id", "view project");
	}
	$titleBlock->show();
}

function showchildren($id, $level=1) {
	global $tasks;
	$t = $tasks; // otherwise, $tasks is accessed from a static context and doesn't work.
	foreach ($t as $task) {
		//echo $id . '==> ' . $task['task_parent'] . '==' . $id . '<br>';
		if ($task['task_parent'] == $id && $task['task_parent'] != $task['task_id']) {
			showtask_edit($task, $level);
			showchildren($task['task_id'], $level+1);
		}
	}
}

/** show a task - at a sublevel
 * {{{
*/
function showtask_edit($task, $level=0) {
	global $AppUI, $canEdit, $durnTypes, $now, $df;
	
	$style = '';
	$sign = 1;
	$start = intval(@$task["task_start_date"]) ? new CDate($task["task_start_date"]) : null;
	$end = intval(@$task["task_end_date"]) ? new CDate($task["task_end_date"]) : null;
	
	if (!$end && $start) {
		$end = $start;
		$end->addSeconds(@$task["task_duration"]*$task["task_duration_type"]*SEC_HOUR);
	}

	if ($now->after($start) && $task["task_percent_complete"] == 0) {
		$style = 'background-color:#ffeebb';
	} else if ($now->after($start)) {
		$style = 'background-color:#e6eedd';
	}

	if ($now->after($end)) {
		$sign = -1;
		$style = (($end) ? 'background-color:#cc6666;color:#ffffff' 
		          : 'background-color:lightgray;');
	} 
	$days = (($start) ? ($now->dateDiff($end) * $sign) : 0);
	
	if ($task['task_percent_complete'] == 100) {
		$days = 'n/a';
		$style = 'background-color:#aaddaa; color:#00000;';
	}
?>
<tr>
	<td>
<?php 
	if ($canEdit) { ?>
		<a href="./index.php?m=tasks&a=addedit&task_id=<?php echo $task['task_id'];?>"><img src="./images/icons/pencil.gif" alt="Edit Task" border="0" width="12" height="12"></a>
<?php } ?>
	</td>
	<td align="right">
		<?php echo intval($task['task_percent_complete']);?>%
	</td>

	<td>
<?php 
	if ($task['task_priority'] < 0) {
		echo '<img src="./images/icons/low.gif" width="13" height="16">';
	} else if ($task['task_priority'] > 0) {
		echo '<img src="./images/icons/' . $task['task_priority'] .'.gif" width="13" height="16">';
	}
?>
	</td>

	<td width="50%">
	<?php 
		for ($i = 1; $i < $level; $i++) {
			echo '&nbsp;&nbsp;';
		}
		if ($level > 0) {
			echo '<img src="./images/corner-dots.gif" width="16" height="12" border="0">'; 
		}
?>
			
		<a href="./index.php?m=tasks&a=view&task_id=<?php echo $task["task_id"];?>" title="<?php
		echo (((isset($task['parent_name'])) 
		       ? ('*** ' . $AppUI->_('Parent Task') . ' ***' . "\n" 
		          . htmlspecialchars($task['parent_name'], ENT_QUOTES) . "\n\n") : '') 
		      .	'*** ' . $AppUI->_('Description') . ' ***' . "\n" 
		      . htmlspecialchars($task['task_description'], ENT_QUOTES)) ?>">
					<?php echo htmlspecialchars($task["task_name"], ENT_QUOTES); ?>
		</a>
	</td>
	<td style="<?php echo $style;?>">
<?php
	echo $task['task_duration'] . ' ' . $AppUI->_($durnTypes[$task['task_duration_type']]);
?>
	</td>

	<td nowrap align="right" style="<?php echo $style;?>">
		<?php echo $days; ?>
	</td>
	<td>
		<input type="checkbox" name="selected[]" value="<?php echo $task['task_id'] ?>">
	</td>
</tr>
<?php } // END of displaying tasks function.}}}
?>

<form name="form" method="post" action="index.php?<?php echo "m=$m&a=$a&project_id=$project_id";?>"> <!--&date=$date -->
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th width="20" colspan="2"><?php echo $AppUI->_('Progress');?></th>
	<th width="15" align="center"><?php echo $AppUI->_('P');?></th>
	<th>
		<a class="hdr" href="index.php?m=tasks&a=organize&project_id=<?php echo $project_id;?>&sort=task_name">
		<?php echo $AppUI->_('Task');?>
		</a>
	</th>
	<th nowrap>
		<a class="hdr" href="index.php?m=tasks&a=organize&project_id=<?php echo $project_id;?>&sort=task_duration">
		<?php echo $AppUI->_('Duration');?>
		</a>
	</th>
	<th nowrap>
		<a class="hdr" href="index.php?m=tasks&a=organize&project_id=<?php echo $project_id;?>&sort=task_end_date">
		<?php echo $AppUI->_('Due In');?>
		</a>
	</th>
	<th width="60"><input type="checkbox" name="toggleSelects" id="toggleSelects" onclick="toggleTasks();"/>&nbsp;<?php echo $AppUI->_('Select');?></th>
</tr>

<?php

/*** Tasks listing ***/
$now = new CDate();
$df = $AppUI->getPref('SHDATEFORMAT');

foreach ($tasks as $task) 
	if ($task['task_id'] == $task['task_parent'])
	{
		showtask_edit($task);
		showchildren($task['task_id']);
	}
?>
</table>

<?php
  $actions = array();
  $actions['d'] = $AppUI->_('Delete', UI_OUTPUT_JS);
  $actions['f'] = $AppUI->_('Mark as Finished', UI_OUTPUT_JS);
  $actions['m'] = $AppUI->_('Move', UI_OUTPUT_JS);
  $actions['c'] = $AppUI->_('Copy', UI_OUTPUT_JS);
	foreach($priorities as $k => $v)
		$actions[$k] = $AppUI->_('set priority to ' . $v, UI_OUTPUT_JS);

  
  $deny = $proj->getDeniedRecords($AppUI->user_id);
  $sql = 'SELECT project_id, project_name
          FROM projects';
	if ($deny)
		$sql .= "\nWHERE project_id NOT IN (" . implode(',', $deny) . ')';
		$sql .= ' ORDER BY project_name';
  $projects = db_loadHashList($sql, 'project_id');
	$p[0] = $AppUI->_('[none]');
	foreach($projects as $proj)
		$p[$proj[0]] = $proj[1];
	if ($project_id)
		$p[$project_id] = $AppUI->_('[same project]');
		
	natsort($p);
	$projects =  $p;
	
	$ts[0] = $AppUI->_('[top task]');
	foreach($tasks as $t)
		$ts[$t['task_id']] = $t['task_name'];
?>

<input type="checkbox" name="include_children" id="include_children" value='1' /><label for="include_children"><?php echo $AppUI->_('IncludeChildren'); ?></label><br />
<table>
  <tr>
    <th>Action: </th>
    <th>Project: </th>
    <th>Task: </th>
  </tr>
  <tr>
    <td>
      <?php echo arraySelect($actions, 'action', '', '0'); ?>
    </td>
    <td>
      <?php echo arraySelect($projects, 'new_project', ' onChange="updateTasks();"', '0'); ?>
    </td>
    <td>
      <?php echo ($ts)?arraySelect($ts, 'new_task', '', '0'):''; ?>
    </td>
		<td>
			<input type="submit" class="button" value="<?php echo $AppUI->_('update selected tasks');?>">
		</td>
  </tr>
</table>
</form>

<table>
<tr>
	<td><?php echo $AppUI->_('Key');?>:</td>
	<td>&nbsp; &nbsp;</td>
	<td style="background-color:#aaddaa; color:#00000">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Complete');?></td>
	<td bgcolor="#ffffff">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Future Task');?></td>
	<td bgcolor="#e6eedd">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Started and on time');?></td>
	<td>&nbsp; &nbsp;</td>
	<td bgcolor="#ffeebb">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Should have started');?></td>
	<td>&nbsp; &nbsp;</td>
	<td bgcolor="#CC6666">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Overdue');?></td>
	<td bgcolor="lightgray">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Unknown');?></td>
</tr>
</table>

<script language="javascript">
	function updateTasks()
	{
		var proj = document.forms['form'].new_project.value;
		var tasks = new Array();
		var sel = document.forms['form'].new_task;
		while (sel.options.length)
			sel.options[0] = null;
		sel.options[0] = new Option('loading...', -1);
		frames['thread'].location.href = './index.php?m=tasks&a=listtasks&project=' + proj;
	}
	function toggleTasks()
	{
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

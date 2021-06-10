<?php /* TASKS $Id$ */
global $showEditCheckbox, $this_day, $other_users, $dPconfig, $user_id;

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$showEditCheckbox = true;
// Project status from sysval, defined as a constant
$project_on_hold_status = 4;

if (isset($_GET['tab'])) {
	$AppUI->setState('ToDoTab', $_GET['tab']);
}
$tab = $AppUI->getState('ToDoTab') !== NULL ? $AppUI->getState('ToDoTab') : 0;

$project_id = intval(dPgetParam($_GET, 'project_id', 0));
$date = (int)dPgetParam($_GET, 'date', 0);
// Check this as the original logic was flawed
if ($date) {
	$date = $this_day->format(FMT_TIMESTAMP_DATE);
}

$user_id = $AppUI->user_id;
$no_modify = false;
$other_users = false;

if (getPermission('admin','view')) {
	// let's see if the user has sysadmin access
	$other_users = true;
	if (($show_uid = (int)dPgetParam($_REQUEST, 'show_user_todo', 0)) != 0) {
	// lets see if the user wants to see anothers user mytodo
		$user_id = $show_uid;
		$no_modify = true;
		$AppUI->setState('tasks_todo_user_id', $user_id);
	} else if ($AppUI->getState('tasks_todo_user_id')) {
		$user_id = $AppUI->getState('tasks_todo_user_id');
	}
}

// check permissions
$canEdit = getPermission($m, 'edit');

// retrieve any state parameters
if (isset($_POST['show_form'])) {
	$AppUI->setState('TaskDayShowArc', (int)dPgetParam($_POST, 'show_arc_proj', 0));
	$AppUI->setState('TaskDayShowLow', (int)dPgetParam($_POST, 'show_low_task', 0));
	$AppUI->setState('TaskDayShowHold', (int)dPgetParam($_POST, 'show_hold_proj', 0));
	$AppUI->setState('TaskDayShowDyn', (int)dPgetParam($_POST, 'show_dyn_task', 0));
	$AppUI->setState('TaskDayShowPin', (int)dPgetParam($_POST, 'show_pinned', 0));
	$AppUI->setState('TaskDayShowEmptyDate', (int)dPgetParam($_POST, 'show_empty_date', 0));

}
// Required for today view.
global $showArcProjs, $showLowTasks, $showHoldProjs,$showDynTasks,$showPinned, $showEmptyDate;

$showArcProjs = $AppUI->getState('TaskDayShowArc', 0);
$showLowTasks = $AppUI->getState('TaskDayShowLow', 1);
$showHoldProjs = $AppUI->getState('TaskDayShowHold', 0);
$showDynTasks = $AppUI->getState('TaskDayShowDyn', 0);
$showPinned = $AppUI->getState('TaskDayShowPin', 0);
$showEmptyDate = $AppUI->getState('TaskDayShowEmptyDate', 0);

$task_sort_item1 = dPgetCleanParam($_GET, 'task_sort_item1', '');
$task_sort_type1 = dPgetCleanParam($_GET, 'task_sort_type1', 0);
$task_sort_order1 = intval(dPgetParam($_GET, 'task_sort_order1', 0));
$task_sort_item2 = dPgetCleanParam($_GET, 'task_sort_item2', '');
$task_sort_type2 = dPgetCleanParam($_GET, 'task_sort_type2', 0);
$task_sort_order2 = intval(dPgetParam($_GET, 'task_sort_order2', 0));

// if task priority set and items selected, do some work
$task_priority = dPgetCleanParam($_POST, 'task_priority', 99);
$selected = dPgetCleanParam($_POST, 'selected_task', 0);

$q = new DBQuery;
if (is_array($selected) && count($selected)) {
	foreach ($selected as $key => $val) {
		if ($task_priority == 'c') {
			// mark task as completed
			$q->addTable('tasks');
			$q->addUpdate('task_percent_complete', '100');
			$q->addWhere('task_id='.$val);
		} else if ($task_priority == 'd') {
			// delete task
			$q->setDelete('tasks');
			$q->addWhere('task_id='.$val);
		} else if ($task_priority > -2 && $task_priority < 2) {
			// set priority
			$q->addTable('tasks');
			$q->addUpdate('task_priority', $task_priority);
			$q->addWhere('task_id='.$val);
		}
		db_exec($q->prepare(true));
		echo db_error();		
	}
}

$AppUI->savePlace();

$proj = new CProject;
$tobj = new CTask;

$allowedProjects = $proj->getAllowedSQL($AppUI->user_id);
$allowedTasks = $tobj->getAllowedSQL($AppUI->user_id, 'task_id');

// query my sub-tasks (ignoring task parents)

$q = new DBQuery;

$q->addTable('tasks', 'ta');
$q->leftJoin('projects', 'pr', 'pr.project_id = ta.task_project');
$q->innerJoin('user_tasks', 'ut', 'ut.task_id = ta.task_id AND ut.user_id = ' . $user_id);
$q->leftJoin('user_task_pin', 'tp', 'tp.task_id = ta.task_id AND tp.user_id = ' . $user_id);

$q->addQuery('ta.*');
$q->addQuery('pr.project_name, pr.project_id, pr.project_color_identifier');
$q->addQuery('tp.task_pinned');

$q->addWhere('(ta.task_percent_complete < 100 OR ta.task_percent_complete IS NULL)');
$q->addWhere('ta.task_status = 0');
if (!$showArcProjs) {
	$q->addWhere('project_status <> 7');
}
if (!$showLowTasks) {
	$q->addWhere('task_priority >= 0');
}
if (!$showHoldProjs) {
	$q->addWhere('project_status != ' . $project_on_hold_status);
}
if (!$showDynTasks) {
	$q->addWhere('task_dynamic != 1');
}
if ($showPinned) {
	$q->addWhere('task_pinned = 1');
}
if (!$showEmptyDate) {
	$q->addWhere("ta.task_start_date != '' AND ta.task_start_date != '0000-00-00 00:00:00'");
}


if (count($allowedTasks)) {
	$q->addWhere($allowedTasks);
}

if (count($allowedProjects)) {
	$q->addWhere($allowedProjects);
}

$q->addGroup('ta.task_id');
$q->addOrder('ta.task_end_date');
$q->addOrder('task_priority DESC');

$sql = $q->prepare();
//echo "<pre>$sql</pre>";
$q->clear();
global $tasks;
$tasks = db_loadList($sql);

/* we have to calculate the end_date via start_date+duration for 
** end='0000-00-00 00:00:00' 
*/
for ($j=0, $xj=count($tasks); $j < $xj; $j++) {
		
	if ($tasks[$j]['task_end_date'] == '0000-00-00 00:00:00' || $tasks[$j]['task_end_date'] == '') {
		if ($tasks[$j]['task_start_date'] == '0000-00-00 00:00:00' 
		    || $tasks[$j]['task_start_date'] == '') {
			//just to be sure start date is "zeroed"
			$tasks[$j]['task_start_date'] = '0000-00-00 00:00:00';
			$tasks[$j]['task_end_date'] = '0000-00-00 00:00:00';
		} else {
			$tasks[$j]['task_end_date'] = calcEndByStartAndDuration($tasks[$j]);
		}
	}
}

global $priorities;
$priorities = array('1' => 'high', '0' => 'normal', '-1' => 'low');

global $durnTypes;
$durnTypes = dPgetSysVal('TaskDurationType');

if (!@$min_view) {
	$titleBlock = new CTitleBlock('My Tasks To Do', 'applet-48.png', $m, "$m.$a");
	$titleBlock->addCrumb("?m=tasks", "tasks list");
	$titleBlock->show();
}

// If we are called from anywhere but directly, we would end up with
// double rows of tabs that would not work correctly, and since we
// are called from the day view of calendar, we need to prevent this
if ($m == 'tasks' && $a == 'todo') {
?>


<table cellspacing="0" cellpadding="2" border="0" width="100%">
<tr>
	<td width="80%" valign="top">
		<?php
	// Tabbed information boxes
	$tabBox = new CTabBox('?m=tasks&amp;a=todo', DP_BASE_DIR . '/modules/', $tab);
	$tabBox->add('tasks/todo_tasks_sub', 'My Tasks');
	$tabBox->add('tasks/todo_gantt_sub', 'My Gantt');
	// Wouldn't it be better to user $tabBox->loadExtras('tasks', 'todo'); and then
	// add tasks_tab.todo.my_open_requests.php in helpdesk?  
	if ($AppUI->isActiveModule('helpdesk')) { 
		$tabBox->add('helpdesk/vw_idx_my', 'My Open Requests');
	}
	$tabBox->show();
?>
	</td>
</tr>
</table>
<?php
} else {
	include DP_BASE_DIR . '/modules/tasks/todo_tasks_sub.php';
}
?>

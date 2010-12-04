<?php /* TASKS $Id: vw_tasks.php,v 1.2 2007/06/19 11:43:05 pedroix Exp $ */
GLOBAL $m, $a, $project_id, $f, $task_status, $min_view, $query_string, $durnTypes, $tpl;
GLOBAL $task_sort_item1, $task_sort_type1, $task_sort_order1;
GLOBAL $task_sort_item2, $task_sort_type2, $task_sort_order2;
GLOBAL $user_id, $dPconfig, $currentTabId, $currentTabName, $canEdit, $showEditCheckbox;
/*      tasks.php

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

/****
// Let's figure out which tasks are selected
*/

global $tasks_opened;
global $tasks_closed;

$tasks_closed = array();
$tasks_opened = $AppUI->getState('tasks_opened');
if(!$tasks_opened){
    $tasks_opened = array();
}
$q = new DBQuery();
$q->addQuery('task_id');
$q->addTable('tasks');
if ($project_id)
     $q->addWhere('task_project='.$project_id);
$q->addWhere('task_dynamic=1');
$all_tasks = $q->loadList();
foreach ($all_tasks as $key => $open_task) {
        $tasks_opened[] = $open_task['task_id'];
}

$task_id = intval( dPgetParam( $_GET, 'task_id', 0 ) );
$q = new DBQuery;
$pinned_only = intval( dPgetParam( $_GET, 'pinned', 0) );
if (isset($_GET['pin'])) {
    $pin = intval( dPgetParam( $_GET, 'pin', 0 ) );
    $msg = '';

    // load the record data
    if($pin) {
        $q->addTable('user_task_pin');
        $q->addInsert('user_id', $AppUI->user_id);
        $q->addInsert('task_id', $task_id);
    }
    else {
        $q->setDelete('user_task_pin');
        $q->addWhere('user_id = ' . $AppUI->user_id);
        $q->addWhere('task_id = ' . $task_id);
    }

    if ( !$q->exec() ) {
        $AppUI->setMsg( 'ins/del err', UI_MSG_ERROR, true );
    }
    else {
        $q->clear();
    }

    $AppUI->redirect('', -1);
}
else if($task_id > 0) {
    $tasks_opened[] = $task_id;
}

$AppUI->savePlace();

if( ($open_task_id = dPGetParam($_GET, 'open_task_id', 0)) > 0
    && !in_array($_GET['open_task_id'], $tasks_opened)) {
    $tasks_opened[] = $_GET['open_task_id'];
}

// Closing tasks needs also to be within tasks iteration in order to
// close down all child tasks
if(($close_task_id = dPGetParam($_GET, 'close_task_id', 0)) > 0) {
    closeOpenedTask($close_task_id);
}

// We need to save tasks_opened until the end because some tasks are closed within tasks iteration
/// End of tasks_opened routine


$durnTypes = dPgetSysVal( 'TaskDurationType' );
$taskPriority = dPgetSysVal( 'TaskPriority' );

$task_project = $project_id;

$task_sort_item1 = dPgetParam( $_GET, 'task_sort_item1', '' );
$task_sort_type1 = dPgetParam( $_GET, 'task_sort_type1', '' );
$task_sort_item2 = dPgetParam( $_GET, 'task_sort_item2', '' );
$task_sort_type2 = dPgetParam( $_GET, 'task_sort_type2', '' );
$task_sort_order1 = intval( dPgetParam( $_GET, 'task_sort_order1', 0 ) );
$task_sort_order2 = intval( dPgetParam( $_GET, 'task_sort_order2', 0 ) );
if (isset($_POST['show_task_options'])) {
        $AppUI->setState('TaskListShowIncomplete', dPgetParam($_POST, 'show_incomplete', 0));
}
$showIncomplete = $AppUI->getState('TaskListShowIncomplete', 0);

$project =& new CProject;
// $allowedProjects = $project->getAllowedRecords($AppUI->user_id, 'project_id, project_name');
$allowedProjects = $project->getAllowedSQL($AppUI->user_id);
$working_hours = ($dPconfig['daily_working_hours']?$dPconfig['daily_working_hours']:8);

$q->addQuery('project_id, project_color_identifier, project_name');
$q->addQuery('SUM(task_duration * task_percent_complete * IF(task_duration_type = 24, '.$working_hours
             .', task_duration_type)) / SUM(task_duration * IF(task_duration_type = 24, '.$working_hours
             .', task_duration_type)) AS project_percent_complete');
$q->addQuery('company_name');
$q->addTable('projects','pr');
$q->leftJoin('tasks', 't1', 'pr.project_id = t1.task_project');
$q->leftJoin('companies', 'c', 'company_id = project_company');
$q->addWhere('t1.task_id = t1.task_parent');
$q->addWhere('project_id='.$project_id);
if ( count($allowedProjects)) {
  $q->addWhere($allowedProjects);
}
$q->addGroup('project_id');
$q->addOrder('project_name');
$psql = $q->prepare();
$q->addQuery('project_id, COUNT(t1.task_id) as total_tasks');
$psql2 = $q->prepare();
$q->clear();

$perms =& $AppUI->acl();
$projects = array();
if ($canViewTasks) {
    $prc = db_exec( $psql );
    echo db_error();
    while ($row = db_fetch_assoc( $prc )) {
        $projects[$row['project_id']] = $row;
    }

    $prc2 = db_exec( $psql2 );
    echo db_error();
    while ($row2 = db_fetch_assoc( $prc2 )) {
        $projects[$row2["project_id"]] = ((!($projects[$row2["project_id"]]))?array():$projects[$row2["project_id"]]);
        array_push($projects[$row2["project_id"]], $row2);
    }
}

$q->addQuery('t.task_id, task_parent, task_name');
$q->addQuery('task_start_date, task_end_date, task_dynamic');
$q->addQuery('count(t.task_parent) as children');
$q->addQuery('task_pinned, pin.user_id as pin_user');
$q->addQuery('task_priority, task_percent_complete');
$q->addQuery('task_duration, task_duration_type');
$q->addQuery('task_project');
$q->addQuery('task_access, task_type');
$q->addQuery('task_description, task_owner, task_status');
$q->addQuery('usernames.user_username, usernames.user_id');
$q->addQuery('assignees.user_username as assignee_username');
$q->addQuery('count(distinct assignees.user_id) as assignee_count');
$q->addQuery('co.contact_first_name, co.contact_last_name');
$q->addQuery('task_milestone');
$q->addQuery('count(distinct f.file_task) as file_count');
$q->addQuery('tlog.task_log_problem');
$q->addQuery('evtq.queue_id');

$q->addTable('tasks','t');
$mods = $AppUI->getActiveModules();
if (!empty($mods['history']) && !getDenyRead('history')) {
    $q->addQuery('MAX(history_date) as last_update');
    $q->leftJoin('history', 'h', 'history_item = t.task_id AND history_table=\'tasks\'');
}
$q->leftJoin('projects', 'p', 'p.project_id = task_project');
$q->leftJoin('users', 'usernames', 'task_owner = usernames.user_id');
$q->leftJoin('user_tasks', 'ut', 'ut.task_id = t.task_id');
$q->leftJoin('users', 'assignees', 'assignees.user_id = ut.user_id');
$q->leftJoin('contacts', 'co', 'co.contact_id = usernames.user_contact');
$q->leftJoin('task_log', 'tlog', 'tlog.task_log_task = t.task_id AND tlog.task_log_problem > 0');
$q->leftJoin('files', 'f', 't.task_id = f.file_task');
$q->leftJoin('user_task_pin', 'pin', 't.task_id = pin.task_id AND pin.user_id = ' . $AppUI->user_id);
//$user_id = $user_id ? $user_id : $AppUI->user_id;
$q->leftJoin('event_queue', 'evtq', 't.task_id = evtq.queue_origin_id AND evtq.queue_module = "tasks"');

//if ($f != 'children') {
//	$q->addWhere('tasks.task_id = task_parent');
//}

//if ($project_id) {
	$q->addWhere('task_project = ' . $project_id);
//}
$allowedProjects = $project->getAllowedSQL($AppUI->user_id, 'task_project');
if (count($allowedProjects)) {
	$q->addWhere($allowedProjects);
}
$obj =& new CTask;
$allowedTasks = $obj->getAllowedSQL($AppUI->user_id, 't.task_id');
if ( count($allowedTasks)) {
	$q->addWhere($allowedTasks);
}
$q->addGroup('t.task_id');
$q->addOrder('project_id, task_start_date');
if ($canViewTasks) {
	$tasks = $q->loadList();
}
// POST PROCESSING TASKS
foreach ($tasks as $row) {
	//add information about assigned users into the page output
	$q->clear();
	$q->addQuery('ut.user_id,	u.user_username');
	$q->addQuery('contact_email, ut.perc_assignment, SUM(ut.perc_assignment) AS assign_extent');
	$q->addQuery('contact_first_name, contact_last_name');
	$q->addTable('user_tasks', 'ut');
	$q->leftJoin('users', 'u', 'u.user_id = ut.user_id');
	$q->leftJoin('contacts', 'c', 'u.user_contact = c.contact_id');
	$q->addWhere('ut.task_id = ' . $row['task_id']);
	$q->addGroup('ut.user_id');
	$q->addOrder('perc_assignment desc, user_username');

	$assigned_users = array ();
	$row['task_assigned_users'] = $q->loadList();
	$q->addQuery('count(*) as children');
	$q->addTable('tasks');
	$q->addWhere('task_parent = ' . $row['task_id']);
	$q->addWhere('task_id <> task_parent');
	$row['children'] = $q->loadResult();
	$row['style'] = taskstyle_pd($row);
	$row['canEdit'] = !getDenyEdit( 'tasks', $row['task_id'] );
	$row['canViewLog'] = $perms->checkModuleItem('task_log', 'view', $row['task_id']);
	$i = count($projects[$row['task_project']]['tasks']) + 1;
	$row['task_number'] = $i;
	$row['node_id'] = 'node_'.$i.'-' . $row['task_id'];
	if (strpos($row['task_duration'], '.') && $row['task_duration_type'] == 1) {
		$row['task_duration'] = floor($row['task_duration']) . ':'
            . round(60 * ($row['task_duration'] - floor($row['task_duration'])));
    }
	//pull the final task row into array
	$projects[$row['task_project']]['tasks'][] = $row;
}

$showEditCheckbox = isset($canEditTasks) && $canEditTasks;
$AppUI->setState('tasks_opened', $tasks_opened);

foreach($projects as $k => $p) {
	global $done;
	$done = array();
	if ( $task_sort_item1 != '' ) {
		if ( $task_sort_item2 != '' && $task_sort_item1 != $task_sort_item2 ) {
			$p['tasks'] = array_csort($p['tasks'],
                                      $task_sort_item1, $task_sort_order1, $task_sort_type1,
                                      $task_sort_item2, $task_sort_order2, $task_sort_type2 );
        }
		else {
			$p['tasks'] = array_csort($p['tasks'], $task_sort_item1, $task_sort_order1, $task_sort_type1 );
        }
	}
    else {
		/* we have to calculate the end_date via start_date+duration for
		** end='0000-00-00 00:00:00' if array_csort function is not used
		** as it is normally done in array_csort function in order to economise
		** cpu time as we have to go through the array there anyway
		*/
		for ($j=0; $j < count($p['tasks']); $j++) {
			if ( $p['tasks'][$j]['task_end_date'] == '0000-00-00 00:00:00' || $p['tasks'][$j]['task_end_date'] == NULL) {
				 $p['tasks'][$j]['task_end_date'] = calcEndByStartAndDuration($p['tasks'][$j]);
			}
		}
	}

	$p['tasks_count'] = count($p['tasks']);
	$projects[$k] = $p;
}

$durnTypes = dPgetSysVal( 'TaskDurationType' );
$tempoTask = new CTask();
$userAlloc = $tempoTask->getAllocation('user_id');
?>
<form name='frm_tasks'>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
        <th width="10">&nbsp;</th>
        <th width="20"><?php echo $AppUI->_('Work');?></th>
        <th align="center"><?php echo $AppUI->_('P'); ?></th>
        <th align="center"><?php echo $AppUI->_('A'); ?></th>
        <th align="center"><?php echo $AppUI->_('T'); ?></th>
        <th align="center"><?php echo $AppUI->_('R'); ?></th>
        <th align="center"><?php echo $AppUI->_('I'); ?></th>
        <th align="center"><?php echo $AppUI->_('Log');?></th>
        <th width="40%"><?php echo $AppUI->_('Task Name');?></th>
<?php if ($PROJDESIGN_CONFIG['show_task_descriptions']) { ?>
        <th width="200"><?php echo $AppUI->_('Task Description');?></th>
<?php } ?>
        <th nowrap="nowrap"><?php echo $AppUI->_('Task Owner');?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Start');?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Duration');?>&nbsp;&nbsp;</th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Finish');?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Assigned Users')?></th>
        <?php if ($showEditCheckbox) { echo '<th width="1"><input type="checkbox" onclick="mult_sel(this, \'selected_task_\', \'frm_tasks\')" name="multi_check"/></th>'; }?>
</tr>
<?php
reset( $projects );

foreach ($projects as $k => $p) {
        $tnums = count( @$p['tasks'] );
// don't show project if it has no tasks
// patch 2.12.04, show project if it is the only project in view
        if ($tnums > 0 || $project_id == $p['project_id']) {
//echo '<pre>'; print_r($p); echo '</pre>';
                global $done;
                $done = array();

                if ( $task_sort_item1 != "" )
                {
                        if ( $task_sort_item2 != "" && $task_sort_item1 != $task_sort_item2 )
                                $p['tasks'] = array_csort($p['tasks'], $task_sort_item1, $task_sort_order1, $task_sort_type1
                                                                                  , $task_sort_item2, $task_sort_order2, $task_sort_type2 );
                        else $p['tasks'] = array_csort($p['tasks'], $task_sort_item1, $task_sort_order1, $task_sort_type1 );
                } else {

			/* we have to calculate the end_date via start_date+duration for
			** end='0000-00-00 00:00:00' if array_csort function is not used
			** as it is normally done in array_csort function in order to economise
			** cpu time as we have to go through the array there anyway
			*/
			for ($j=0; $j < count($p['tasks']); $j++) {

				if ( $p['tasks'][$j]['task_end_date'] == '0000-00-00 00:00:00' ) {

					 $p['tasks'][$j]['task_end_date'] = calcEndByStartAndDuration($p['tasks'][$j]);
				}
			}

		}

                for ($i=0; $i < $tnums; $i++) {
                        $t = $p['tasks'][$i];

                        if ($t["task_parent"] == $t["task_id"]) {
                            $is_opened = in_array($t["task_id"], $tasks_opened);
                                showtask_pd( $t, 0, $is_opened );
                                if($is_opened || $t["task_dynamic"] == 0){
                                    findchild_pd( $p['tasks'], $t["task_id"] );
                                }
                        }

				if ($search_text) {
                              if (strpos($t['task_name'], $search_text) !== false || strpos($t['task_description'], $search_text) !== false)
      					showtask_pd($t, 1, false);
				}
                }
// check that any 'orphaned' user tasks are also display
                for ($i=0; $i < $tnums; $i++) {
                        if ( !in_array( $p['tasks'][$i]["task_id"], $done ) ) {
                            if($p['tasks'][$i]["task_dynamic"] && in_array( $p['tasks'][$i]["task_parent"], $tasks_closed)) {
                                closeOpenedTask($p['tasks'][$i]["task_id"]);
                            }
                            if(in_array($p['tasks'][$i]["task_parent"], $tasks_opened)){
                                    showtask_pd( $p['tasks'][$i], 1, false);
                            }
                        }
                }
        }
}

$AppUI->setState("tasks_opened", $tasks_opened);
?>
</table>
</form>
<table>
<tr>
        <td><?php echo $AppUI->_('Key');?>:</td>
        <th>&nbsp;P&nbsp;</th>
        <td>=<?php echo $AppUI->_('Priority');?></td>
        <th>&nbsp;A&nbsp;</th>
        <td>=<?php echo $AppUI->_('Access');?></td>
        <th>&nbsp;T&nbsp;</th>
        <td>=<?php echo $AppUI->_('Type');?></td>
        <th>&nbsp;R&nbsp;</th>
        <td>=<?php echo $AppUI->_('Reminder');?></td>
        <th>&nbsp;I&nbsp;</th>
        <td>=<?php echo $AppUI->_('Inactive');?></td>
        <td>&nbsp; &nbsp;</td>
        <td style="border-style:solid;border-width:1px" bgcolor="#ffffff">&nbsp; &nbsp;</td>
        <td>=<?php echo $AppUI->_('Future Task');?></td>
        <td>&nbsp; &nbsp;</td>
        <td style="border-style:solid;border-width:1px" bgcolor="#e6eedd">&nbsp; &nbsp;</td>
        <td>=<?php echo $AppUI->_('Started and on time');?></td>
        <td style="border-style:solid;border-width:1px" bgcolor="#ffeebb">&nbsp; &nbsp;</td>
        <td>=<?php echo $AppUI->_('Should have started');?></td>
        <td>&nbsp; &nbsp;</td>
        <td style="border-style:solid;border-width:1px" bgcolor="#CC6666">&nbsp; &nbsp;</td>
        <td>=<?php echo $AppUI->_('Overdue');?></td>
        <td>&nbsp; &nbsp;</td>
        <td style="border-style:solid;border-width:1px" bgcolor="#aaddaa">&nbsp; &nbsp;</td>
        <td>=<?php echo $AppUI->_('Done');?></td>
</tr>
</table>

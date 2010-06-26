<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $task_id;

$task_id = intval(dPgetParam($_GET, 'task_id', 0));
$task_log_id = intval(dPgetParam($_GET, 'task_log_id', 0));
$reminded = intval(dPgetParam($_GET, 'reminded', 0));

$obj = new CTask();
$obj->peek($task_id); //we need to peek at the task's data to determine its access level
$msg = '';

// check permissions for this record
$canAccess = getPermission($m, 'access', $task_id);
$canRead = getPermission($m, 'view', $task_id);
$canEdit = getPermission($m, 'edit', $task_id);

// check if this record has dependencies to prevent deletion
$canDelete = $obj->canDelete($msg, $task_id);
// check permissions for this record (module level)
$canReadModule = getPermission($m, 'view');

if (!($canRead && $obj->canAccess($AppUI->user_id))) {
	$AppUI->redirect('m=public&a=access_denied');
}

$q = new DBQuery;

$q->addTable('tasks');
$q->leftJoin('users', 'u1', 'u1.user_id = task_owner');
$q->leftJoin('projects', 'p', 'p.project_id = task_project');
$q->leftJoin('task_log', 'tl', 'tl.task_log_task = task_id');
$q->addWhere('task_id = ' . $task_id);
$q->addQuery('tasks.*');
$q->addQuery('project_name, project_color_identifier');
$q->addQuery('u1.user_username as username');
$q->addQuery('ROUND(SUM(task_log_hours),2) as log_hours_worked');
$q->addGroup('task_id');


//$obj = null;
$sql = $q->prepare();
$q->clear();

if (!db_loadObject($sql, $obj, true, false)) {
	$AppUI->setMsg('Task');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// Clear any reminders
if ($reminded) {
	$obj->clearReminder();
}

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('TaskLogVwTab', $_GET['tab']);
}
$tab = $AppUI->getState('TaskLogVwTab') !== NULL ? $AppUI->getState('TaskLogVwTab') : 0;

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');
//Also view the time
$df .= ' ' . $AppUI->getPref('TIMEFORMAT');

$start_date = intval($obj->task_start_date) ? new CDate($obj->task_start_date) : null;
$end_date = intval($obj->task_end_date) ? new CDate($obj->task_end_date) : null;

//check permissions for the associated project
$canReadProject = getPermission('projects', 'view', $obj->task_project);

// get the users on this task
$q->addTable('users', 'u');
$q->addTable('user_tasks', 't');
$q->leftJoin('contacts', 'c' , 'user_contact = contact_id');
$q->addQuery('u.user_id, u.user_username, contact_email');
$q->addWhere('t.task_id = ' . $task_id);
$q->addWhere('t.user_id = u.user_id');
$q->addOrder('u.user_username');

$sql = $q->prepare();
$q->clear();
$users = db_loadList($sql);

$durnTypes = dPgetSysVal('TaskDurationType');

// setup the title block
$titleBlock = new CTitleBlock('View Task', 'applet-48.png', $m, "$m.$a");
$titleBlock->addCell(
);
if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new task').'">', '',
		'<form action="?m=tasks&a=addedit&task_project='.$obj->task_project.'&task_parent=' . $task_id . '" method="post">', '</form>'
	);
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
		'<form action="?m=files&a=addedit&project_id=' . $obj->task_project . '&file_task=' . $obj->task_id . '" method="post">', '</form>'
	);
}
$titleBlock->addCrumb('?m=tasks', 'tasks list');
if ($canReadProject) {
	$titleBlock->addCrumb("?m=projects&a=view&project_id=$obj->task_project", 'view this project');
}
if ($canEdit) {
	$titleBlock->addCrumb("?m=tasks&a=addedit&task_id=$task_id", 'edit this task');
}
if ($canDelete) {
	$titleBlock->addCrumbDelete('delete task', $canDelete, $msg);
}
$titleBlock->show();

$task_types = dPgetSysVal('TaskType');

?>

<script language="JavaScript">
var calendarField = '';

function popCalendar(field) {
	calendarField = field;
	idate = eval('document.editFrm.task_' + field + '.value');
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no, status=no');
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar(idate, fdate) {
	fld_date = eval('document.editFrm.task_' + calendarField);
	fld_fdate = eval('document.editFrm.' + calendarField);
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>

function updateTask() {
	var f = document.editFrm;
	if (f.task_log_description.value.length < 1) {
		alert("<?php echo $AppUI->_('tasksComment', UI_OUTPUT_JS);?>");
		f.task_log_description.focus();
	} else if (isNaN(parseInt(f.task_percent_complete.value+0))) {
		alert("<?php echo $AppUI->_('tasksPercent', UI_OUTPUT_JS);?>");
		f.task_percent_complete.focus();
	} else if (f.task_percent_complete.value  < 0 || f.task_percent_complete.value > 100) {
		alert("<?php echo $AppUI->_('tasksPercentValue', UI_OUTPUT_JS);?>");
		f.task_percent_complete.focus();
	} else {
		f.submit();
	}
}
function delIt() {
	if (confirm("<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Task', UI_OUTPUT_JS).'?';?>")) {
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<form name="frmDelete" action="./index.php?m=tasks" method="post">
	<input type="hidden" name="dosql" value="do_task_aed">
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="task_id" value="<?php echo $task_id;?>" />
</form>

<tr valign="top">
	<td width="50%">
		<table width="100%" cellspacing="1" cellpadding="2">
		<tr>
			<td nowrap="nowrap" colspan="2"><strong><?php echo $AppUI->_('Details');?></strong></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project');?>:</td>
			<td style="background-color:#<?php echo $obj->project_color_identifier;?>">
				<font color="<?php echo bestColor($obj->project_color_identifier); ?>">
					<?php echo @$obj->project_name;?>
				</font>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task');?>:</td>
			<td class="hilite"><strong><?php echo @$obj->task_name;?></strong></td>
		</tr>
		<?php if ($obj->task_parent != $obj->task_id) { 
			$obj_parent = new CTask();
			$obj_parent->load($obj->task_parent);
		?>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task Parent');?>:</td>
			<td class="hilite"><a href="<?php echo './index.php?m=tasks&a=view&task_id=' . @$obj_parent->task_id; ?>"><?php echo @$obj_parent->task_name;?></a></td>
		</tr>
		<?php } ?>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Owner');?>:</td>
			<td class="hilite"> <?php echo @$obj->username;?></td>
		</tr>				<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Priority');?>:</td>
			<td class="hilite">
		<?php
			$task_priotities = dPgetSysVal('TaskPriority');
            echo $AppUI->_($task_priotities[$obj->task_priority]); 
		?>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Web Address');?>:</td>
			<td class="hilite" width="300"><a href="<?php echo @$obj->task_related_url;?>" target="task<?php echo $task_id;?>"><?php echo @$obj->task_related_url;?></a></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Milestone');?>:</td>
			<td class="hilite" width="300"><?php if ($obj->task_milestone) {echo $AppUI->_('Yes');}else {echo $AppUI->_('No');}?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Progress');?>:</td>
			<td class="hilite" width="300"><?php echo @$obj->task_percent_complete;?>%</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Time Worked');?>:</td>
			<td class="hilite" width="300"><?php echo (@$obj->task_hours_worked + @rtrim($obj->log_hours_worked, '0'));?></td>
		</tr>
		<tr>
			<td nowrap="nowrap" colspan="2"><strong><?php echo $AppUI->_('Dates and Targets');?></strong></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Start Date');?>:</td>
			<td class="hilite" width="300"><?php echo $start_date ? $start_date->format($df) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Finish Date');?>:</td>
			<td class="hilite" width="300"><?php echo $end_date ? $end_date->format($df) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap" valign="top"><?php echo $AppUI->_('Expected Duration');?>:</td>
			<td class="hilite" width="300"><?php echo $obj->task_duration.' '.$AppUI->_($durnTypes[$obj->task_duration_type]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Target Budget');?> <?php echo $dPconfig['currency_symbol'] ?>:</td>
			<td class="hilite" width="300"><?php echo $obj->task_target_budget;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task Type');?> :</td>
			<td class="hilite" width="300"><?php echo $AppUI->_($task_types[$obj->task_type]);?></td>
		</tr>

		</table>
	</td>

	<td width="50%">
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td colspan="3"><strong><?php echo $AppUI->_('Assigned Users');?></strong></td>
		</tr>
		<tr>
			<td colspan="3">
			<?php
				$s = '';
				$s = count($users) == 0 ? '<tr><td>'.$AppUI->_('none').'</td></tr>' : '';
				foreach ($users as $row) {
					$s .= '<tr>';
					$s .= '<td class="hilite">'.dPgetUsernameFromID($row['user_id']).'</td>';
					$s .= '<td class="hilite"><a href="mailto:'.$row['contact_email'].'">'.$row['contact_email'].'</a></td>';
					$s .= '</tr>';
				}
				echo '<table width="100%" cellspacing="1">'.$s.'</table>';
			?>
			</td>
		</tr>

		<?php
			// Pull tasks dependencies
			$q->addQuery('td.dependencies_req_task_id, t.task_name');
			$q->addTable('tasks', 't');
			$q->addTable('task_dependencies', 'td');
			$q->addWhere('td.dependencies_req_task_id = t.task_id');
			$q->addWhere('td.dependencies_task_id = ' . $task_id);
			
			$taskDep = $q->loadHashList();
			$q->clear();
		?>
		<tr>
			<td colspan="3"><strong><?php echo $AppUI->_('Dependencies');?></strong></td>
		</tr>
		<tr>
			<td colspan="3">
			<?php 
				$s = count($taskDep) == 0 ? '<tr><td>'.$AppUI->_('none').'</td></tr>' : '';
				foreach ($taskDep as $key => $value) {
					$s .= '<tr><td class="hilite">';
					$s .= '<a href="./index.php?m=tasks&a=view&task_id='.$key.'">'.$value.'</a>';
					$s .= '</td></tr>';
				}
				echo '<table width="100%" cellspacing="1">'.$s.'</table>';
			?>
			</td>
		</tr>
                <?php
			// Pull the tasks depending on this Task 
			$q->addQuery('td.dependencies_task_id, t.task_name');
			$q->addTable('tasks', 't');
			$q->addTable('task_dependencies', 'td');
			$q->addWhere('td.dependencies_task_id = t.task_id');
			$q->addWhere('td.dependencies_req_task_id = ' . $task_id);
			$dependingTasks = $q->loadHashList();
			$q->clear();
		?>
		<tr>
			<td colspan="3"><strong><?php echo $AppUI->_('Tasks depending on this Task');?></strong></td>
		</tr>
		<tr>
			<td colspan="3">
			<?php
				$s = count($dependingTasks) == 0 ? '<tr><td>'.$AppUI->_('none').'</td></tr>' : '';
				foreach ($dependingTasks as $key => $value) {
					$s .= '<tr><td class="hilite">';
					$s .= '<a href="./index.php?m=tasks&a=view&task_id='.$key.'">'.$value.'</a>';
					$s .= '</td></tr>';
				}
				echo '<table width="100%" cellspacing="1">'.$s.'</table>';
			?>
			</td>
		</tr>
		<tr>
		  <td colspan='3' nowrap="nowrap">
		     <strong><?php echo $AppUI->_('Description');?></strong><br />
		  </td>
		 </tr>
		 <tr>
		  <td class='hilite' colspan='3'>
				<?php $newstr = str_replace(chr(10), '<br />', $obj->task_description);echo $newstr;?>
		  </td>
		</tr>
<?php
		$q->addTable('departments', 'd');
		$q->addTable('task_departments', 't');
		$q->addWhere('t.department_id = d.dept_id');
		$q->addWhere('t.task_id = ' . $task_id);
		$q->addQuery('dept_id, dept_name, dept_phone');
		$depts = $q->loadHashList('dept_id');
		$q->clear();
		if (count($depts)) {
			?>
		    <tr>
		    	<td><strong><?php echo $AppUI->_('Departments'); ?></strong></td>
		    </tr>
		    <tr>
		    	<td colspan='3' class="hilite">
		    		<?php
		    			foreach ($depts as $dept_id => $dept_info) {
		    				echo '<div>'.$dept_info['dept_name'];
		    				if ($dept_info['dept_phone'] != '') {
		    					echo '('.$dept_info['dept_phone'].')';
		    				}
		    				echo '</div>';
		    			}
		    		?>
		    	</td>
		    </tr>
	 		<?php
		}
		
		if ($AppUI->isActiveModule('contacts') && getPermission('contacts', 'view')) {
			$q->addTable('contacts', 'c');
			$q->leftJoin('task_contacts', 'tc', 'tc.contact_id = c.contact_id');
			$q->leftJoin('departments', 'd', 'dept_id = contact_department');
			$q->addWhere('tc.task_id = ' . $obj->task_id);
			$q->addQuery('c.contact_id, contact_first_name, contact_last_name, contact_email');
			$q->addQuery('contact_phone, dept_name');
			$q->addWhere("(contact_owner = '$AppUI->user_id' or contact_private = '0')");
			$contacts = $q->loadHashList('contact_id');
			$q->clear();
			if (count($contacts)>0) {
				?>
			    <tr>
			    	<td><strong><?php echo $AppUI->_('Task Contacts'); ?></strong></td>
			    </tr>
			    <tr>
			    	<td colspan='3' class="hilite">
			    		<?php
			    			echo '<table cellspacing="1" cellpadding="2" border="0" width="100%">';
			    			echo '<tr><th>'.$AppUI->_('Name').'</font></th><th>'.$AppUI->_('Email').'</th><th>'.$AppUI->_('Phone').'</th><th>'.$AppUI->_('Department').'</th></tr>';
			    			foreach ($contacts as $contact_id => $contact_data) {
			    				echo '<tr>';
			    				echo '<td class="hilite"><a href="index.php?m=contacts&a=addedit&contact_id=' . $contact_id . '">' . $contact_data['contact_first_name'].' '.$contact_data['contact_last_name'].'</a></td>';
			    				echo '<td class="hilite"><a href="mailto: '.$contact_data['contact_email'].'">'.$contact_data['contact_email'].'</a></td>';
			    				echo '<td class="hilite">'.$contact_data['contact_phone'].'</td>';
			    				echo '<td class="hilite">'.$contact_data['dept_name'].'</td>';
			    				echo '</tr>';
			    			}
			    			echo '</table>';
			    		?>
			    	</td>
			    </tr>
			    <tr>
			    	<td>
		 <?php
			}

			$q->addTable('contacts', 'c');
			$q->leftJoin('project_contacts', 'pc', 'pc.contact_id = c.contact_id');
			$q->leftJoin('departments', 'd', 'd.dept_id = c.contact_department');
			$q->addWhere('pc.project_id = ' . $obj->task_project);
			$q->addQuery('c.contact_id, contact_first_name, contact_last_name, contact_email');
			$q->addQuery('contact_phone, dept_name');
			$q->addWhere("(contact_owner = '$AppUI->user_id' or contact_private = '0')");
			$contacts = $q->loadHashList('contact_id');
			$q->clear();
			if (count($contacts)>0) {
				?>
			    <tr>
			    	<td><strong><?php echo $AppUI->_('Project Contacts'); ?></strong></td>
			    </tr>
			    <tr>
			    	<td colspan='3' class="hilite">
			    		<?php
			    			echo '<table cellspacing="1" cellpadding="2" border="0" width="100%">';
			    			echo '<tr><th color="white">'.$AppUI->_('Name').'</th><th>'.$AppUI->_('Email').'</th><th>'.$AppUI->_('Phone').'</th><th>'.$AppUI->_('Department').'</th></tr>';
			    			foreach ($contacts as $contact_id => $contact_data) {
			    				echo '<tr>';
			    				echo '<td class="hilite"><a href="index.php?m=contacts&a=addedit&contact_id=' . $contact_id . '">' . $contact_data['contact_first_name'].' '.$contact_data['contact_last_name'].'</a></td>';
			    				echo '<td class="hilite"><a href="mailto: '.$contact_data['contact_email'].'">'.$contact_data['contact_email'].'</a></td>';
			    				echo '<td class="hilite">'.$contact_data['contact_phone'].'</td>';
			    				echo '<td class="hilite">'.$contact_data['dept_name'].'</td>';
			    				echo '</tr>';
			    			}
			    			echo '</table>';
			    		?>
			    	</td>
			    </tr>
			    <tr>
			    	<td>
		 <?php
			}

		}
				require_once  $AppUI->getSystemClass('CustomFields');
				$custom_fields = New CustomFields($m, $a, $obj->task_id, 'view');
				$custom_fields->printHTML();
	 ?>
	 		</td>
	 	</tr>
		</table>
	</td>
</tr>
</table>

<?php 
$query_string = '?m=tasks&a=view&task_id=' . $task_id;
$tabBox = new CTabBox('?m=tasks&a=view&task_id=' . $task_id, '', $tab);

$tabBox_show = 0;
if ($obj->task_dynamic != 1) {
	// tabbed information boxes
	if (getPermission('task_log', 'access')) {
		$tabBox_show = 1;
		$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_logs', 'Task Logs');
		// fixed bug that dP automatically jumped to access denied if user does not
		// have read-write permissions on task_id and this tab is opened by default (session_vars)
		// only if user has r-w perms on this task, new or edit log is beign showed
		if (getPermission('tasks', 'edit', $task_id)) {
			if ($task_log_id == 0) {
				if (getPermission('task_log', 'add')) {
					$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_log_update', 'New Log');
				}
			} else if (getPermission('task_log', 'edit')) {
				$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_log_update', 'Edit Log');
			} else if (getPermission('task_log', 'add')) {
				$tabBox_show = 1;
				$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_log_update', 'New Log');
			}
		}
	}
}

if (count($obj->getChildren()) > 0) {
	// Has children
	// settings for tasks
	$f = 'children';
	$min_view = true;
	$tabBox_show = 1;
	// in the tasks file there is an if that checks
	// $_GET[task_status]; this patch is to be able to see
	// child tasks withing an inactive task
	$_GET['task_status'] = $obj->task_status;
	$tabBox->add(DP_BASE_DIR.'/modules/tasks/tasks', 'Child Tasks');
}

if ($tabBox->loadExtras($m, $a)) {
	$tabBox_show = 1;
}

if ($tabBox_show == 1) {
	$tabBox->show();
}
?>

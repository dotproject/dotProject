<?php /* TASKS $Id$ */
/**
* Tasks :: Add/Edit Form
*
*/

$task_id = intval( dPgetParam( $_REQUEST, "task_id", 0 ) );
$perms =& $AppUI->acl();

// load the record data
$obj = new CTask();

// check if we are in a subform
if ($task_id > 0 && !$obj->load( $task_id )) {
	$AppUI->setMsg( 'Task' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

$task_parent = isset($_REQUEST['task_parent'])? $_REQUEST['task_parent'] : $obj->task_parent;

// check for a valid project parent
$task_project = intval( $obj->task_project );
if (!$task_project) {
	$task_project = dPgetParam( $_REQUEST, 'task_project', 0 );
	if (!$task_project) {
		$AppUI->setMsg( "badTaskProject", UI_MSG_ERROR );
		$AppUI->redirect();
	}
}

// check permissions
if ( $task_id ) {
	// we are editing an existing task
	$canEdit = $perms->checkModuleItem( $m, "edit", $task_id );
} else {
	// do we have access on this project?
	$canEdit = $perms->checkModuleItem( 'projects', "view", $task_project );
	// And do we have add permission to tasks?
	if ($canEdit)
	  $canEdit = $perms->checkModule('tasks', 'add');
}

if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied&err=noedit" );
}

//check permissions for the associated project
$canReadProject = $perms->checkModuleItem( 'projects', "view", $obj->task_project);

$durnTypes = dPgetSysVal( 'TaskDurationType' );

// check the document access (public, participant, private)
if (!$obj->canAccess( $AppUI->user_id )) {
	$AppUI->redirect( "m=public&a=access_denied&err=noaccess" );
}

// pull the related project
$project = new CProject();
$project->load( $task_project );

//Pull all users
$q = new DBQuery;
$q->addQuery('user_id, contact_first_name, contact_last_name');
$q->addTable('users');
$q->addTable('contacts');
$q->addWhere('user_contact = contact_id');
$q->addOrder('contact_last_name, contact_first_name');
$q->exec();
$users = array();
while ( $row = $q->fetchRow()) {
  $users[$row['user_id']] = $row['contact_last_name'] . ', ' . $row['contact_first_name'];
}
$q->clear();

function getSpaces($amount){
	if($amount == 0) return "";
	return str_repeat("&nbsp;", $amount);
}

function constructTaskTree($task_data, $depth = 0){
	global $projTasks, $all_tasks, $parents, $task_parent_options, $task_parent, $task_id;

	$projTasks[$task_data['task_id']] = $task_data['task_name'];

	$selected = $task_data['task_id'] == $task_parent ? "selected='selected'" : "";
	$task_data['task_name'] = strlen($task_data[1])>45 ? substr($task_data['task_name'],0, 45)."..." : $task_data['task_name'];

	$task_parent_options .= "<option value='".$task_data['task_id']."' $selected>".getSpaces($depth*3).dPFormSafe($task_data['task_name'])."</option>";

	if (isset($parents[$task_data['task_id']])) {
		foreach ($parents[$task_data['task_id']] as $child_task) {
			if ($child_task != $task_id)
				constructTaskTree($all_tasks[$child_task], ($depth+1));
		}
	}
}

function build_date_list(&$date_array, $row) {
	global $tracked_dynamics, $project;
	// if this task_dynamic is not tracked, set end date to proj start date
	if ( !in_array($row['task_dynamic'], $tracked_dynamics) )
		$date = new CDate( $project->project_start_date );
	elseif ($row['task_milestone'] == 0) {
		$date = new CDate($row['task_end_date']);
	} else {
		$date = new CDate($row['task_start_date']);
	}
	$sdate = $date->format("%d/%m/%Y");
	$shour = $date->format("%H");
	$smin = $date->format("%M");

	$date_array[$row['task_id']] = array($row['task_name'], $sdate, $shour, $smin);
}

// let's get root tasks
$sql = "select task_id, task_name, task_end_date, task_start_date, task_milestone, task_parent, task_dynamic
		from tasks
		where task_project = '$task_project'
			  and task_id  = task_parent
        order by task_start_date";

$root_tasks = db_loadHashList($sql, 'task_id');

$projTasks           = array();
$task_parent_options = "";

// Now lets get non-root tasks, grouped by the task parent
$sql = "select task_id, task_name, task_end_date, task_start_date, task_milestone, task_parent, task_dynamic
	from tasks
	where task_project = '$task_project'
	and task_id != task_parent
	order by task_start_date";

$parents = array();
$projTasksWithEndDates = array( $obj->task_id => $AppUI->_('None') );//arrays contains task end date info for setting new task start date as maximum end date of dependenced tasks
$all_tasks = array();
$sub_tasks = db_exec($sql);
if ($sub_tasks) {
	while ($sub_task = db_fetch_assoc($sub_tasks)) {
		// Build parent/child task list
		$parents[$sub_task['task_parent']][] = $sub_task['task_id'];
		$all_tasks[$sub_task['task_id']] = $sub_task;
		build_date_list($projTasksWithEndDates, $sub_task);
	}
}

// let's iterate root tasks
foreach ($root_tasks as $root_task) {
	build_date_list($projTasksWithEndDates, $root_task);
	if ($root_task['task_id'] != $task_id)
		constructTaskTree($root_task);
}

// setup the title block
$ttl = $task_id > 0 ? "Edit Task" : "Add Task";
$titleBlock = new CTitleBlock( $ttl, 'applet-48.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=tasks", "tasks list" );
if ( $canReadProject ) {
	$titleBlock->addCrumb( "?m=projects&a=view&project_id=$task_project", "view this project" );
}
if ($task_id > 0)
  $titleBlock->addCrumb( "?m=tasks&a=view&task_id=$obj->task_id", "view this task" );
$titleBlock->show();

// Let's gather all the necessary information from the department table
// collect all the departments in the company
$depts = array( 0 => '' );

// ALTER TABLE `tasks` ADD `task_departments` CHAR( 100 ) ;
$company_id                = $project->project_company;
$selected_departments      = $obj->task_departments != "" ? explode(",", $obj->task_departments) : array();
$departments_count         = 0;
$department_selection_list = getDepartmentSelectionList($company_id, $selected_departments);
if($department_selection_list!=""){
	$department_selection_list = "<select name='dept_ids[]' size='$departments_count' multiple class='text'>
								  $department_selection_list
    	                          </select>";
}



function getDepartmentSelectionList($company_id, $checked_array = array(), $dept_parent=0, $spaces = 0){
	global $departments_count;
	$parsed = '';

	if($departments_count < 10) $departments_count++;
	$sql = "select dept_id, dept_name
	        from departments
	        where dept_parent      = '$dept_parent'
	              and dept_company = '$company_id'";
	$depts_list = db_loadHashList($sql, "dept_id");

	foreach($depts_list as $dept_id => $dept_info){
		$selected = in_array($dept_id, $checked_array) ? "selected" : "";

		if(strlen($dept_info["dept_name"]) > 30){
			$dept_info["dept_name"] = substr($dept_info["dept_name"], 0, 28)."...";
		}

		$parsed .= "<option value='$dept_id' $selected>".str_repeat("&nbsp;", $spaces).$dept_info["dept_name"]."</option>";
		$parsed .= getDepartmentSelectionList($company_id, $checked_array, $dept_id, $spaces+5);
	}

	return $parsed;
}

//Dynamic tasks are by default now off because of dangerous behavior if incorrectly used
if ( is_null($obj->task_dynamic) ) $obj->task_dynamic = 0 ;

$can_edit_time_information = $obj->canUserEditTimeInformation();
//get list of projects, for task move drop down list.
//require_once $AppUI->getModuleClass('projects');
//$project =& new CProject;
$pq = new DBQuery;
$pq->addQuery('project_id, project_name');
$pq->addTable('projects');
$pq->addWhere("project_company = '$company_id'");
$pq->addWhere('( project_status <> 7 or project_id = \''. $task_project . '\')');
$pq->addOrder('project_name');
$project->setAllowedSQL($AppUI->user_id, $pq);
$projects = $pq->loadHashList();
?>
<SCRIPT language="JavaScript">
var selected_contacts_id = "<?php echo $obj->task_contacts; ?>";
var task_id = '<?php echo $obj->task_id;?>';

var check_task_dates = <?php
  if (isset($dPconfig['check_task_dates']) && $dPconfig['check_task_dates'])
    echo 'true';
  else
    echo 'false';
?>;
var can_edit_time_information = <?php echo $can_edit_time_information ? 'true' : 'false'; ?>;

var task_name_msg = "<?php echo $AppUI->_('taskName');?>";
var task_start_msg = "<?php echo $AppUI->_('taskValidStartDate');?>";
var task_end_msg = "<?php echo $AppUI->_('taskValidEndDate');?>";

var workHours = <?php echo dPgetConfig( 'daily_working_hours' );?>;
//working days array from config.php
var working_days = new Array(<?php echo dPgetConfig( 'cal_working_days' );?>);
var cal_day_start = <?php echo intval(dPgetConfig( 'cal_day_start' ));?>;
var cal_day_end = <?php echo intval(dPgetConfig( 'cal_day_end' ));?>;
var daily_working_hours = <?php echo intval(dPgetConfig('daily_working_hours')); ?>;


function checkAutoRequiredFields(f) {
	var msg = '';
	<?php 
	/*
	** Automatic required fields generated from System Values
	*/
	$requiredFields = dPgetSysVal( 'TaskRequiredFields' );
	echo dPrequiredFields($requiredFields);
	?>
	return msg;
}

</script>

<table border="1" cellpadding="4" cellspacing="0" width="100%" class="std">
<form name="editFrm" action="?m=tasks&project_id=<?php echo $task_project;?>" method="post">
	<input name="dosql" type="hidden" value="do_task_aed" />
	<input name="task_id" type="hidden" value="<?php echo $task_id;?>" />
	<input name="task_project" type="hidden" value="<?php echo $task_project;?>" />
	<input name='task_contacts' id='task_contacts' type='hidden' value="<?php echo $obj->task_contacts; ?>" />
<tr>
	<td colspan="2" style="border: outset #eeeeee 1px;background-color:#<?php echo $project->project_color_identifier;?>" >
		<font color="<?php echo bestColor( $project->project_color_identifier ); ?>">
			<strong><?php echo $AppUI->_('Project');?>: <?php echo @$project->project_name;?></strong>
		</font>
	</td>
</tr>

<tr valign="top" width="50%">
	<td>
		<?php echo $AppUI->_( 'Task Name' );?> *
		<br /><input type="text" class="text" name="task_name" value="<?php echo dPformSafe( $obj->task_name );?>" size="40" maxlength="255" />
	</td>
	<td>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Status' );?></td>
			<td>
				<?php echo arraySelect( $status, 'task_status', 'size="1" class="text"', $obj->task_status, true );?>
			</td>

			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Priority' );?> *</td>
			<td nowrap>
				<?php echo arraySelect( $priority, 'task_priority', 'size="1" class="text"', $obj->task_priority, true );?>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Progress' );?></td>
			<td>
				<?php echo arraySelect( $percent, 'task_percent_complete', 'size="1" class="text"', $obj->task_percent_complete ) . '%';?>
			</td>

			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Milestone' );?>?</td>
			<td>
				<input type="checkbox" value=1 name="task_milestone" <?php if($obj->task_milestone){?>checked<?php }?> />
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
	<td height="40" width="35%">
		* <?php echo $AppUI->_( 'requiredField' );?>
	</td>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr>
			<td>
				<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel');?>" onClick="if(confirm('<?php echo $AppUI->_('taskCancel', UI_OUTPUT_JS);?>')){location.href = '?<?php echo $AppUI->getPlace();?>';}" />
			</td>
			<td>
				<input class="button" type="button" name="btnFuseAction" value="<?php echo $AppUI->_('save');?>" onClick="submitIt(document.editFrm);" />
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</form>
<?php
	if (isset($_GET['tab']))
	  $AppUI->setState('TaskAeTabIdx', dPgetParam($_GET, 'tab', 0));
	$tab = $AppUI->getState('TaskAeTabIdx', 0);
	$tabBox =& new CTabBox("?m=tasks&a=addedit&task_id=$task_id", "", $tab, "");
	$tabBox->add("{$dPconfig['root_dir']}/modules/tasks/ae_desc", "Details");
        $tabBox->add("{$dPconfig['root_dir']}/modules/tasks/ae_dates", "Dates");
	$tabBox->add("{$dPconfig['root_dir']}/modules/tasks/ae_depend", "Dependencies");
	$tabBox->add("{$dPconfig['root_dir']}/modules/tasks/ae_resource", "Human Resources");
	$tabBox->loadExtras('tasks', 'addedit');
	$tabBox->show('', true);
?>
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
	<td height="40" width="35%">
		* <?php echo $AppUI->_( 'requiredField' );?>
	</td>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr>
			<td>
				<input class="button" type="button" name="cancel2" value="<?php echo $AppUI->_('cancel');?>" onClick="if(confirm('<?php echo $AppUI->_('taskCancel', UI_OUTPUT_JS);?>')){location.href = '?<?php echo $AppUI->getPlace();?>';}" />
			</td>
			<td>
				<input class="button" type="button" name="btnFuseAction2" value="<?php echo $AppUI->_('save');?>" onClick="submitIt(document.editFrm);" />
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

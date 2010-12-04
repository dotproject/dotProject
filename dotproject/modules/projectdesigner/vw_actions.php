<?php
include_once( $AppUI->getModuleClass( 'tasks' ) );
global $task_access, $task_priority, $project_id;

$type = dPgetsysval('TaskType');
$stype = array(''=>'(Type)') + $type;
$priority = dPgetsysval('TaskPriority');
$spriority = array(''=>'(Priority)') + $priority;
$stask_access = array(''=>'(Access)') + $task_access;
$durntype = dPgetSysval('TaskDurationType');
$sdurntype = array( ''=>'(Duration Type)') + $durntype;
$sother = array(''=>'(Other Operations)','1'=>'Mark Tasks as Finished','8'=>'Mark Tasks as Active','9'=>'Mark Tasks as Inactive','2'=>'Mark Tasks as Milestones','3'=>'Mark Tasks as Non Milestone','4'=>'Mark Tasks as Dynamic','5'=>'Mark Tasks as Non Dynamic','6'=>'Add Task Reminder','7'=>'Remove Task Reminder','99'=>'Delete Tasks');

//Pull all users
$q = new DBQuery;
$q->addQuery('user_id, contact_first_name, contact_last_name');
$q->addTable('users');
$q->addTable('contacts');
$q->addWhere('user_contact = contact_id');
$q->addOrder('contact_first_name, contact_last_name');
$q->exec();
$users = array();
while ( $row = $q->fetchRow()) {
	$users[$row['user_id']] = $row['contact_first_name'] . ' ' . $row['contact_last_name'];
}
$q->clear();
$sowners = array(''=>'(Task Owner)') + $users;
$sassign = array(''=>'(Assign User)') + $users;
$sunassign = array(''=>'(Unassign User)') + $users;

$obj =& new CTask;
$allowedTasks = $obj->getAllowedSQL($AppUI->user_id, 't.task_id');

$obj->load($task_id);
$task_project = $project_id ? $project_id : ($obj->task_project ? $obj->task_project : 0);
// let's get root tasks
$q = new DBQuery;
$q->addQuery('task_id, task_name, task_end_date, task_start_date, task_milestone, task_parent, task_dynamic');
$q->addTable('tasks');
$q->addWhere('task_project = '.$task_project);
$q->addWhere('task_id = task_parent');
$q->addOrder('task_start_date');
$sql = $q->prepare();
$q->clear();
$root_tasks = db_loadHashList($sql, 'task_id');
$projTasks           = array();
global $task_parent_options;
$task_parent_options = "";

// Now lets get non-root tasks, grouped by the task parent
$q = new DBQuery;
$q->addQuery('task_id, task_name, task_end_date, task_start_date, task_milestone, task_parent, task_dynamic');
$q->addTable('tasks');
$q->addWhere('task_project = '.$task_project);
$q->addWhere('task_id <> task_parent');
$q->addOrder('task_start_date');
$sql = $q->prepare();
$q->clear();

$parents = array();
$projTasksWithEndDates = array( 0 => $AppUI->_('None') );//arrays contains task end date info for setting new task start date as maximum end date of dependenced tasks
global $all_tasks;
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
	constructTaskTree($root_task,$parents,$all_tasks);
}

$project = new CProject();
$sprojects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, company_name');
$q->addJoin("companies",'co','co.company_id = project_company');
$idx_companies = $q->loadHashList();
$q->clear();
foreach ($sprojects as $prj_id => $prj_name) {
    $sprojects[$prj_id] = $idx_companies[$prj_id].': '.$prj_name;
}
asort($sprojects);
$sprojects = arrayMerge( array( ''=>'('.$AppUI->_('Move to Project', UI_OUTPUT_RAW).')' ), $sprojects );
	  
//lets addthe reference to percent
@include_once( "./functions/tasks_func.php" );
$spercent = arrayMerge( array( ''=>'(Progress)' ), $percent );
?>
            <form name='frm_bulk' method='POST' action='?m=projectdesigner&a=do_task_bulk_aed'>
	      <input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
	      <input type="hidden" name="opt_view_project" value="<?php echo (isset($view_options[0]['pd_option_view_project']) ? $view_options[0]['pd_option_view_project'] : 1);?>" />
	      <input type="hidden" name="opt_view_gantt" value="<?php echo (isset($view_options[0]['pd_option_view_gantt']) ? $view_options[0]['pd_option_view_gantt'] : 1);?>" />
	      <input type="hidden" name="opt_view_tasks" value="<?php echo (isset($view_options[0]['pd_option_view_tasks']) ? $view_options[0]['pd_option_view_tasks'] : 1);?>" />
	      <input type="hidden" name="opt_view_actions" value="<?php echo (isset($view_options[0]['pd_option_view_actions']) ? $view_options[0]['pd_option_view_actions'] : 1);?>" />
	      <input type="hidden" name="opt_view_addtsks" value="<?php echo (isset($view_options[0]['pd_option_view_addtasks']) ? $view_options[0]['pd_option_view_addtasks'] : 1);?>" />
	      <input type="hidden" name="opt_view_files" value="<?php echo (isset($view_options[0]['pd_option_view_files']) ? $view_options[0]['pd_option_view_files'] : 1);?>" />
            <table id="tbl_bulk" name="tbl_bulk" width="100%">
            <tr>
                  <th width="15%"><?php echo $AppUI->_('Start Date');?>&nbsp;</th>
                  <td width="160" nowrap>
                                  <input type='hidden' id='add_task_bulk_start_date' name='add_task_bulk_start_date' value='' />
                                  <input type='text' onChange="setDate('frm_bulk', 'bulk_start_date');" class='text' style='width:120px;' id='bulk_start_date' name='bulk_start_date' value='' />
                                  <a onclick="return showCalendar('bulk_start_date', '<?php echo $cf ?>', 'frm_bulk', '<?php echo (strpos($cf,'%p')!==false ? '12' : '24') ?>', true)" href="#">
                                  <img src='./images/calendar.gif' width='24' height='12' alt='<?php echo $AppUI->_('Calendar');?>' border='0' />
                                  </a>
                  </td>
                  <th width="15%" nowrap="nowrap"><?php echo $AppUI->_('End Date');?>&nbsp;</th>
                  <td width="160" nowrap>
                                  <input type='hidden' id='add_task_bulk_end_date' name='add_task_bulk_end_date' value='' />
                                  <input type='text' onChange="setDate('frm_bulk', 'bulk_end_date');" class='text' style='width:120px;' id='bulk_end_date' name='bulk_end_date' value='' />
                                  <a onclick="return showCalendar('bulk_end_date', '<?php echo $cf ?>', 'frm_bulk', '<?php echo (strpos($cf,'%p')!==false ? '12' : '24') ?>', true)" href="#">
                                  <img src='./images/calendar.gif' width='24' height='12' alt='<?php echo $AppUI->_('Calendar');?>' border='0' />
                                  </a>
                  </td>
                  <th width="15%" nowrap><?php echo $AppUI->_('Duration');?>&nbsp;</th>
                  <td width="130">
                                  <input type='text' class='text' style='width:120px;text-align:right;' id='bulk_task_duration' name='bulk_task_duration' value='' />&nbsp;
								  <?php echo arraySelect( $sdurntype, 'bulk_task_durntype', 'style="width=120px" size="1" class="text"', '', true ); ?>
                  </td>
                  <td width="100%">&nbsp;</td>
            </tr>                                
            <tr>
                  <th width="15%"><?php echo $AppUI->_('Owner');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $sowners, 'bulk_task_owner', 'style="width:130px" class="text"', '' ); ?></td>
                  <th width="15%"><?php echo $AppUI->_('Assign');?>&nbsp;</th>
                  <td width="198" nowrap><?php echo arraySelect( $sassign, 'bulk_task_assign', 'style="width:130px" class="text"', '' ); ?>
      			<select name="bulk_task_assign_perc" class="text">
      			<?php 
      				for ($i = 5; $i <= 100; $i+=5) {
      					echo "<option ".(($i==100)? "selected=\"true\"" : "" )." value=\"".$i."\">".$i."%</option>";
      				}
      			?>
      			</select>
                  </td>
                  <th width="15%"><?php echo $AppUI->_('Unassign');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $sunassign, 'bulk_task_unassign', 'style="width:130px" class="text"', '' ); ?></td>
                  <td width="100%">&nbsp;</td>
            </tr>                                
            <tr>
                  <th width="15%"><?php echo $AppUI->_('Priority');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $spriority, 'bulk_task_priority', 'style="width:80px" class="text"', '' ); ?></td>
                  <th width="15%"><?php echo $AppUI->_('Type');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $stype, 'bulk_task_type', 'style="width:100px" class="text"', '' ); ?></td>
                  <th width="15%"><?php echo $AppUI->_('Parent');?>&nbsp;</th>
                  <td width="130">
                        <select name='bulk_task_parent' style='width:300px' class='text'>
                        	<option value=''>(<?php echo $AppUI->_('Task Parent'); ?>)</option>
                        	<option value='0'>(<?php echo $AppUI->_('Reset to Self Task'); ?>)</option>
                        	<?php echo $task_parent_options; ?>
                        </select>
                  </td>
                  <td width="100%">&nbsp;</td>
            </tr>                                
            <tr>
                  <th width="15%"><?php echo $AppUI->_('Access');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $stask_access, 'bulk_task_access', 'style="width:80px" class="text"', '' ); ?></td>
                  <th width="15%"><?php echo $AppUI->_('Progress');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $spercent, 'bulk_task_percent_complete', 'class="text"', '' ); ?> %</td>                                            
                  <th width="15%"><?php echo $AppUI->_('Dependency');?>&nbsp;</th>
                  <td width="130">
                        <select name='bulk_task_dependency' style='width:300px' class='text'>
                        	<option value=''>(<?php echo $AppUI->_('Task Depend on Completion of...'); ?>)</option>
                        	<option value='0'>(<?php echo $AppUI->_('Remove Dependencies'); ?>)</option>
                        	<?php echo $task_parent_options; ?>
                        </select>
                  </td>
                  <td width="100%">&nbsp;</td>
            </tr>                                
            <tr>
                  <th width="15%" nowrap><?php echo $AppUI->_('Date Move (Days)');?>&nbsp;</th>
                  <td width="130">
                                  <input type='text' class='text' style='width:120px;text-align:right;' id='bulk_move_date' name='bulk_move_date' value='' />
                  </td>
                  <th width="15%"><?php echo $AppUI->_('Other');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $sother, 'bulk_task_other', 'style="width:180px" class="text"', '' ); ?></td>
                  <th width="15%"><?php echo $AppUI->_('Project');?>&nbsp;</th>
                  <td width="130"><?php echo arraySelect( $sprojects, 'bulk_task_project', 'style="width:300px" class="text"', '' ); ?></td>
                  <td width="100%">&nbsp;</td>
            </tr>                                
            <tr>
                  <td colspan="20" align="right"><input type="button" class="button" value="<?php echo $AppUI->_('update');?>" onclick="if (confirm('Are you sure you wish to apply the update(s) to the selected task(s)?')) document.frm_bulk.submit();" /></td>
            </tr>
            </table>
            </form>

<?php
function getSpaces($amount){
	if($amount == 0) return "";
	return str_repeat("&nbsp;", $amount);
}

function constructTaskTree($task_data, $parents, $all_tasks, $depth = 0){
	global $projTasks, $all_tasks, $task_parent_options, $task_parent, $task_id;

	$projTasks[$task_data['task_id']] = $task_data['task_name'];

	$task_data['task_name'] = strlen($task_data[1])>45 ? substr($task_data['task_name'],0, 45)."..." : $task_data['task_name'];

	$task_parent_options .= "<option value='".$task_data['task_id']."' >".getSpaces($depth*3).dPFormSafe($task_data['task_name'])."</option>";

	if (isset($parents[$task_data['task_id']])) {
		foreach ($parents[$task_data['task_id']] as $child_task) {
			if ($child_task != $task_id)
			constructTaskTree($all_tasks[$child_task], $parents, $all_tasks, ($depth+1));
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
?>

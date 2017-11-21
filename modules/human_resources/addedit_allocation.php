<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $AppUI, $dPconfig, $locale_char_se;

$human_resource_allocation_id = intval(dPgetParam($_GET, 'human_resource_allocation_id', 0));
$allocation = new CHumanResourceAllocation();
if ($human_resource_allocation_id && ! $allocation->load($human_resource_allocation_id)) {
	$AppUI->setMsg('Human Resource Allocation');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
}

$project_tasks_estimated_roles_id = intval(dPgetParam($_GET, 'project_tasks_estimated_roles_id', 0));
$allocation->project_tasks_estimated_roles_id = $project_tasks_estimated_roles_id;

$titleBlock = new CTitleBlock((($human_resource_allocation_id) ? 'Edit Human Resource Allocation' : 'Create Human Resource Allocation'),
							   'applet3-48.png', $m, "$m.$a");							   

$task_id = intval(dPgetParam($_GET, 'task_id', 0));								   
$query = new DBQuery;
$query->addTable('tasks', 't');
$query->addQuery('project_name, project_id');
$query->innerJoin('projects', 'p', 't.task_project = p.project_id');
$query->addWhere('t.task_id = ' . $task_id);
$res_project =& $query->exec();
$project_id = $res_project->fields['project_id'];
$project_name = $res_project->fields['project_name'];
$query->clear();
													
require_once DP_BASE_DIR."/modules/tasks/tasks.class.php";
$task = new CTask();
$task->load($task_id);
$titleBlock->addCrumb(('?m=tasks&amp;a=view&amp;task_id=' . $task_id),  $task->task_name);
$titleBlock->addCrumb(('?m=projects&amp;a=view&amp;project_id=' . $project_id),  $project_name);

$query = new DBQuery;
$query->addTable('project_tasks_estimated_roles', 'e');
$query->addQuery('c.role_name');
$query->innerJoin('company_role', 'c', 'c.id = e.role_id');
$query->addWhere('e.id = ' . $project_tasks_estimated_roles_id);
$res =& $query->exec();
$company_role = $res->fields['role_name'];
$query->clear();

$cwd = array();
$cwd[0] = '0';
$cwd[1] = '1';
$cwd[2] = '2';
$cwd[3] = '3';
$cwd[4] = '4';
$cwd[5] = '5';
$cwd[6] = '6';
$cwd_conv = array_map('cal_work_day_conv', $cwd);

function cal_work_day_conv($val) {
	global $locale_char_set;
	setlocale(LC_ALL, 'en_AU'.(($locale_char_set)? ('.' . $locale_char_set) : '.utf8'));
	$wk = Date_Calc::getCalendarWeek(null, null, null, "%a", LOCALE_FIRST_DAY);
	setlocale(LC_ALL, $AppUI->user_lang);
	
	$day_name = $wk[($val - LOCALE_FIRST_DAY)%7];
	if ($locale_char_set == "utf-8" && function_exists("utf8_encode")) {
	    $day_name = utf8_encode($day_name);
	}
	return htmlentities($day_name, ENT_COMPAT, $locale_char_set);
}

$allocated_user_id = intval(dPgetParam($_GET, 'allocated_user_id', 0));
require_once DP_BASE_DIR."/modules/admin/admin.class.php";
if($allocated_user_id) {
	$allocated_user = new CUser();
	$allocated_user->load($allocated_user_id);
	
	$query = new DBQuery;
	$query->addTable('human_resource', 'h');
	$query->addQuery('human_resource_id');
	$query->addWhere('h.human_resource_user_id = ' . $allocated_user->user_id);
	$res =& $query->exec();
	require_once DP_BASE_DIR."/modules/human_resources/human_resources.class.php";
	$allocated_hr = new CHumanResource();
	$allocated_hr->load($res->fields['human_resource_id']);
	$query->clear();
}
else {
	if($human_resource_allocation_id) {
		$allocated_hr = new CHumanResource();
		$allocated_hr->load($allocation->human_resource_id);
		
		$allocated_user = new CUser();
		$allocated_user->load($allocated_hr->human_resource_user_id);
	}
}
$titleBlock->show();
if($allocated_user_id || $human_resource_allocation_id) {
	$allocated_user_username = $allocated_user->user_username;
	
	$query->addTable('users', 'u');
	$query->addQuery('contact_last_name, contact_first_name');
	$query->addJoin('contacts', 'c', 'u.user_contact = c.contact_id');
	$query->addWhere('u.user_id = ' . $allocated_user->user_id);
	$res =& $query->exec();
	$allocated_user_contact = $res->fields['contact_last_name'] . ', ' . $res->fields['contact_first_name'];
	$query->clear();
	
	require_once DP_BASE_DIR."/modules/human_resources/allocation_functions.php";
	$user_roles = getUserRoles($allocated_hr->human_resource_id);
	$roles = array();
	foreach ($user_roles as $role) {
	  $roles[$role['human_resources_role_id']] = $role['human_resources_role_name'];
	}
	$allocated_user_roles = implode(', ', $roles);
	
	$allocated_user_lattes_url = $allocated_hr->human_resource_lattes_url;
	
	$allocated_user_weekday_hours = '';
	$allocated_user_weekday_hours .= $cwd_conv[0] . ': ' . $allocated_hr->human_resource_mon . ', ';
	$allocated_user_weekday_hours .= $cwd_conv[1] . ': ' . $allocated_hr->human_resource_tue . ', ';
	$allocated_user_weekday_hours .= $cwd_conv[2] . ': ' . $allocated_hr->human_resource_wed . ', ';
	$allocated_user_weekday_hours .= $cwd_conv[3] . ': ' . $allocated_hr->human_resource_thu . ', ';
	$allocated_user_weekday_hours .= $cwd_conv[4] . ': ' . $allocated_hr->human_resource_fri . ', ';
	$allocated_user_weekday_hours .= $cwd_conv[5] . ': ' . $allocated_hr->human_resource_sat . ', ';
	$allocated_user_weekday_hours .= $cwd_conv[6] . ': ' . $allocated_hr->human_resource_sun;
	
	$allocation->human_resource_id = $allocated_hr->human_resource_id;
}
?>
<script src="./modules/human_resources/addedit_allocation.js"></script>

<form name="editfrm" action="?m=human_resources" method="post">
<input type="hidden" name="dosql" value="do_allocation_aed" />
<input type="hidden" name="task_id" value="<?php echo dPformSafe($task_id); ?>"/>
<input type="hidden" name="user_id" value="<?php echo dPformSafe($allocated_user->user_id); ?>"/>
<input type="hidden" name="human_resource_allocation_id" value="<?php echo dPformSafe($human_resource_allocation_id);?>" />
<input type="hidden" name="project_tasks_estimated_roles_id" value="<?php echo dPformSafe($allocation->project_tasks_estimated_roles_id);?>" />
<input type="hidden" name="human_resource_id" value="<?php echo dPformSafe($allocation->human_resource_id);?>" />
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std" summary="human_resources">
<tr>
  <td valign="top" width="100%">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task name');?>:</td>
			<td class="hilite" width="100%"><?php echo $task->task_name;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task start date');?>:</td>
			<td class="hilite" width="100%"><?php echo $task->task_start_date;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task end date');?>:</td>
			<td class="hilite" width="100%"><?php echo $task->task_end_date;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task duration hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $task->task_duration;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated role name');?>:</td>
			<td class="hilite" width="100%"><?php echo $company_role;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user name');?>:</td>
			<td class="hilite" width="100%"><?php echo $allocated_user_username;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user contact');?>:</td>
			<td class="hilite" width="100%"><?php echo $allocated_user_contact;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user roles');?>:</td>
			<td class="hilite" width="100%"><?php echo $allocated_user_roles;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user weekday working hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $allocated_user_weekday_hours;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user Lattes URL');?>:</td>
			<td class="hilite" width="100%"><?php echo $allocated_user_lattes_url;?></td>
		</tr>
		</table>
	</td>
</tr>
<td align="right">
    <input type="button" value="<?php echo $AppUI->_('submit');?>"
    class="button" onclick="submitAllocation(document.editfrm);" />
</td>
</table>
</form>

<?php
if($allocated_user_id || $human_resource_allocation_id) {
	require_once DP_BASE_DIR."/modules/human_resources/allocation_functions.php";
	require_once DP_BASE_DIR."/modules/projects/projects.class.php";
	$human_resource_tasks = getHumanResourceTasksExcept($allocated_hr->human_resource_id, $task->task_id, $allocation->human_resource_allocation_id);
	if(count($human_resource_tasks) == 0) {
            ?>
        <br />
            <font color="#319457"> That user is not allocated to any other task. </font>
            <img src="modules/human_resources/images/ok.png" title="Ok" alt="Ok" /> <br /><br />
	<?php
        }
	else {
		echo '<br>';
		?>
		<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl'>
		<strong align='center'><?php echo $AppUI->_('Chosen user tasks');?></strong>
		<tr>
		<th nowrap='nowrap' width='20%'>
		<?php echo $AppUI->_('Task project'); ?>
		</th>
		<th nowrap='nowrap' width='30%'>
		<?php echo $AppUI->_('Task name'); ?>
		</th>
		<th nowrap='nowrap' width='20%'>
		<?php echo $AppUI->_('Active roles'); ?>
		</th>
		<th nowrap='nowrap' width='15%'>
		<?php echo $AppUI->_('Task start date'); ?>
		</th>
		<th nowrap='nowrap' width='15%'>
		<?php echo $AppUI->_('Task end date'); ?>
		</th>
		<th nowrap='nowrap' width='10%'>
		<?php echo $AppUI->_('Task duration hours'); ?>
		</th>
		<th nowrap='nowrap' width='10%'>
		<?php echo $AppUI->_('Date conflict status'); ?>
		</th>
		</tr>
		<?php
		foreach($human_resource_tasks as $human_resource_task) {
			$human_resource_task_project = new CProject();
			$human_resource_task_project->load($human_resource_task['task_project']);
			$tasksInSamePeriod = tasksInSamePeriod($task, $human_resource_task);
			$estimated_role_names_array = getEstimatedRolesByHumanResourceInTask($human_resource_task['task_id'], $allocation->human_resource_id);
			$estimated_role_names = implode(', ', $estimated_role_names_array);
			$conflict_image = '<img src="./modules/human_resources/images/ok.png" title="Ok" alt="Ok"/>';
			if($tasksInSamePeriod) {
				$conflict_image = '<img src="./modules/human_resources/images/not.png" title="Conflict" alt="Conflict"/>';
			}			
			?>		
			<tr>
			<td style=<?php echo $style;?>>
			<?php echo $human_resource_task_project->project_name; ?></a>
			</td>
			<td style=<?php echo $style;?>>
			<?php echo $human_resource_task['task_name']; ?></a>
			</td>
			<td style=<?php echo $style;?>>
			<?php echo $estimated_role_names; ?></a>
			</td>
			<td style=<?php echo $style;?>>
			<?php echo $human_resource_task['task_start_date']; ?></a>
			</td>
			<td style=<?php echo $style;?>>
			<?php echo $human_resource_task['task_end_date']; ?></a>
			</td>
			<td style=<?php echo $style;?>>
			<?php echo $human_resource_task['task_duration']; ?></a>
			</td>
			<td align='center'> <?php echo	$conflict_image; ?>
			</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php	
		echo '<br />';
		echo '<img src="./modules/human_resources/images/not.png" title="Conflict" alt="Conflict"/> :'. $AppUI->_("Tasks with date conflict"). '&nbsp;&nbsp;&nbsp;';
		echo '<img src="./modules/human_resources/images/ok.png" title="Ok" alt="Ok"/> :'. $AppUI->_("Tasks with no date conflict");
		echo '<br /><br />';
	}
}
?>
<?php
$query = new DBQuery;
$query->addTable('projects', 'p');
$query->addQuery('project_company');
$query->addWhere('p.project_id = ' . $project_id);
$res =& $query->exec();
$company_id = $res->fields['project_company'];
$query->clear();

$query->addTable('contacts', 'c');
$query->addQuery('user_id, human_resource_id, contact_id');
$query->innerJoin('users', 'u', 'u.user_contact = c.contact_id');
$query->innerJoin('human_resource', 'h', 'h.human_resource_user_id = u.user_id');
$query->addWhere('c.contact_company = ' . $company_id);
$res =& $query->exec();

$ordered_users = array();
$ordered_hrs = array();
$ordered_contacts = array();
$same_role = array();
$role_names = array();
require_once DP_BASE_DIR."/modules/admin/admin.class.php";
require_once DP_BASE_DIR."/modules/human_resources/human_resources.class.php";
require_once DP_BASE_DIR."/modules/contacts/contacts.class.php";
require_once DP_BASE_DIR."/modules/human_resources/allocation_functions.php";
for ($res; ! $res->EOF; $res->MoveNext()) {
	$user = new CUser();
	$user->load($res->fields['user_id']);
	$hr = new CHumanResource();
	$hr->load($res->fields['human_resource_id']);
	$contact = new CContact();
	$contact->load($res->fields['contact_id']);
	$user_roles = getUserRoles($hr->human_resource_id);
	$roles = array();
	$same_roles = FALSE;
	foreach ($user_roles as $role) {
		
		$roles[$role['human_resources_role_id']] = $role['human_resources_role_name'];
		
		if(strcmp($role['human_resources_role_name'], $company_role) == 0) {
			$same_roles = TRUE;
			break;
		}
	}

	$concat_roles = implode(', ', $roles);
	if($same_roles == TRUE) {
		array_unshift($ordered_users, $user);
		array_unshift($ordered_hrs, $hr);
		array_unshift($ordered_contacts, $contact);
		array_unshift($same_role, 1);
		array_unshift($role_names, $concat_roles);
	}
	else {
		array_push($ordered_users, $user);
		array_push($ordered_hrs, $hr);
		array_push($ordered_contacts, $contact);
		array_push($same_role, 0);
		array_push($role_names, $concat_roles);
	}
}
?>
<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl'>
<strong align='center'><?php echo $AppUI->_('Users in suggested allocation order');?></strong>
<tr>
	<th nowrap='nowrap' width='12%'>
    <?php echo $AppUI->_('User username'); ?>
	</th>
	<th nowrap='nowrap' width='13%'>
    <?php echo $AppUI->_('User contact'); ?>
	</th>
	<th nowrap='nowrap' width='22%'>
    <?php echo $AppUI->_('User roles'); ?>
	</th>
	<th nowrap='nowrap' width='23%'>
    <?php echo $AppUI->_('User weekday working hours'); ?>
	</th>
	<th nowrap='nowrap' width='2%'>
    <?php echo $AppUI->_('User Lattes URL'); ?>
	</th>
</tr>
<?php 
for ($i = 0; $i < count($ordered_hrs); $i++) {
	$user = $ordered_users[$i];
	$user_id = $user->user_id;
	$hr = $ordered_hrs[$i];
	$contact = $ordered_contacts[$i];
	$weekday_working_hours = '';
	$weekday_working_hours .= $cwd_conv[0] . ': ' .  $hr->human_resource_mon . ', ';
	$weekday_working_hours .= $cwd_conv[1] . ': ' .  $hr->human_resource_tue . ', ';
	$weekday_working_hours .= $cwd_conv[2] . ': ' .  $hr->human_resource_wed . ', ';
	$weekday_working_hours .= $cwd_conv[3] . ': ' .  $hr->human_resource_thu . ', ';
	$weekday_working_hours .= $cwd_conv[4] . ': ' .  $hr->human_resource_fri . ', ';
	$weekday_working_hours .= $cwd_conv[5] . ': ' .  $hr->human_resource_sat . ', ';
	$weekday_working_hours .= $cwd_conv[6] . ': ' .  $hr->human_resource_sun;
	$style = ($same_role[$i] == 1) ? 'background-color:#60E564; font-weight:bold' : 'background-color:#F7FC64;';
?>
<tr>
  <td style=<?php echo $style;?>>
   <a href="?m=human_resources&amp;a=addedit_allocation&amp;human_resource_allocation_id=<?php echo $human_resource_allocation_id;?>&amp;project_tasks_estimated_roles_id=<?php echo $project_tasks_estimated_roles_id;?>&amp;task_id=<?php echo $task_id;?>&amp;project_id=<?php echo $project_id;?>&amp;allocated_user_id=<?php echo $user_id;?>">
	<?php echo $user->user_username;?></a>
  </td>
  <td style=<?php echo $style;?>>
   <a href="?m=human_resources&amp;a=addedit_allocation&amp;human_resource_allocation_id=<?php echo $human_resource_allocation_id;?>&amp;project_tasks_estimated_roles_id=<?php echo $project_tasks_estimated_roles_id;?>&amp;task_id=<?php echo $task_id;?>&amp;project_id=<?php echo $project_id;?>&amp;allocated_user_id=<?php echo $user_id;?>">
    <?php echo $contact->contact_last_name . ', ' . $contact->contact_first_name;?></a>
  </td>
  <td style=<?php echo $style;?>>
   <a href="?m=human_resources&amp;a=addedit_allocation&amp;human_resource_allocation_id=<?php echo $human_resource_allocation_id;?>&amp;project_tasks_estimated_roles_id=<?php echo $project_tasks_estimated_roles_id;?>&amp;task_id=<?php echo $task_id;?>&amp;project_id=<?php echo $project_id;?>&amp;allocated_user_id=<?php echo $user_id;?>">
    <?php echo $role_names[$i];?></a>
  </td>
  <td style=<?php echo $style;?>>
   <a href="?m=human_resources&amp;a=addedit_allocation&amp;human_resource_allocation_id=<?php echo $human_resource_allocation_id;?>&amp;project_tasks_estimated_roles_id=<?php echo $project_tasks_estimated_roles_id;?>&amp;task_id=<?php echo $task_id;?>&amp;project_id=<?php echo $project_id;?>&amp;allocated_user_id=<?php echo $user_id;?>">
    <?php echo $weekday_working_hours;?></a>
  </td>
  <?php 
  if($hr->human_resource_lattes_url) {
  ?>
  <td style="<?php echo $style ?>" align="center">
	<a href="#" onclick="window.open('<?php echo $hr->human_resource_lattes_url; ?>')"><?php echo dPshowImage( './modules/human_resources/images/lattes.png', 16, 16, '' ); ?>
	</a>
  </td>
  <?php
  }
  else {
  ?>
  <td style=<?php echo $style;?>>&nbsp;</td>
  <?php
  }
  ?>
</tr>
<?php
}
?>
</table>
<table width="100%">
<tr>
  <td><?php echo $AppUI->_('Key'); ?>:&nbsp;&nbsp;</td>
  <td style="background-color:#60E564; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('User with same role'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#F7FC64; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('User with no same role'); ?>&nbsp;&nbsp;</td>
</tr>
</table>
<br />
* <?php echo $AppUI->_("LBL_HUMAN_RESOURCE_ALLOCATION_HELP"); ?>
<br /><br />
<?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>      


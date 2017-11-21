<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$project_tasks_estimated_roles_id = intval(dPgetParam($_GET, 'project_tasks_estimated_roles_id', 0));
$task_id = intval(dPgetParam($_GET, 'task_id', 0));
$project_id = intval(dPgetParam($_GET, 'project_id', 0));

$query = new DBQuery;
$query->addTable('projects', 'p');
$query->addQuery('project_name');
$query->addWhere('p.project_id = ' . $project_id);
$res_project =& $query->exec();
$project_name = $res_project->fields['project_name'];
$query->clear();

if (! $project_tasks_estimated_roles_id) {
	$AppUI->setMsg("invalid ID", UI_MSG_ERROR);
	$AppUI->redirect();
}
require_once DP_BASE_DIR."/modules/tasks/tasks.class.php";
$task = new CTask();
$task->load($task_id);

$query = new DBQuery;
$query->addTable('human_resource_allocation', 'a');
$query->addQuery('*');
$query->addWhere('a.project_tasks_estimated_roles_id = ' . $project_tasks_estimated_roles_id);
$res =& $query->exec();

$query_company_role = new DBQuery;
$query_company_role->addTable('project_tasks_estimated_roles', 'e');
$query_company_role->addQuery('c.role_name, h.human_resources_role_responsability, 
						h.human_resources_role_authority, h.human_resources_role_competence');
$query_company_role->addJoin('company_role', 'c', 'c.id = e.role_id');
$query_company_role->addJoin('human_resources_role', 'h', 'h.human_resources_role_name = c.role_name');
$query_company_role->addWhere('e.id = ' . $project_tasks_estimated_roles_id);
$res_company_role =& $query_company_role->exec();

if($res->fields['human_resource_id']) {
	$query_hr = new DBQuery;
	$query_hr->addTable('human_resource', 'h');
	$query_hr->addQuery('u.user_username, c.contact_first_name, c.contact_last_name');
	$query_hr->addJoin('users', 'u', 'u.user_id = h.human_resource_user_id');
	$query_hr->addJoin('contacts', 'c', 'u.user_contact = c.contact_id');
	$query_hr->addWhere('h.human_resource_id = ' . $res->fields['human_resource_id']);
	$res_hr =& $query_hr->exec();
	$contact_name = $res_hr->fields['contact_last_name'] . ', ' . $res_hr->fields['contact_first_name'];
	
	$human_resource = new CHumanResource;
	$human_resource->load($res->fields['human_resource_id']);
}

$human_resource_allocation_id = $res->fields['human_resource_allocation_id'];
$obj = new CHumanResourceAllocation;
if($obj->load($human_resource_allocation_id)) {
	$AppUI->savePlace();  
}

$titleBlock = new CTitleBlock('Human Resources Allocation', 'applet3-48.png', $m, "$m.$a");
$titleBlock->addCrumb(('?m=projects&amp;a=view&amp;project_id=' . $project_id),  $project_name);
$titleBlock->addCrumb(('?m=tasks&amp;a=view&amp;task_id=' . $task_id),  $task->task_name);

if($human_resource_allocation_id) {
	$canDelete = true;
	$titleBlock->addCrumb("?m=human_resources&amp;a=addedit_allocation&amp;human_resource_allocation_id=$human_resource_allocation_id&amp;project_tasks_estimated_roles_id=$project_tasks_estimated_roles_id&amp;task_id=$task_id&amp;project_id=$project_id", $AppUI->_("edit this allocation",UI_OUTPUT_JS));
	$titleBlock->addCrumbDelete($AppUI->_('delete allocation',UI_OUTPUT_JS), $canDelete, 'no delete permission');
}
else {
	$titleBlock->addCrumb("?m=human_resources&amp;a=addedit_allocation&amp;human_resource_allocation_id=$human_resource_allocation_id&amp;project_tasks_estimated_roles_id=$project_tasks_estimated_roles_id&amp;task_id=$task_id&amp;project_id=$project_id", "new allocation");
}

$titleBlock->show();

?>
<script type="text/javascript" language="javascript">
	can_delete = true;
	delete_msg = "<?php echo $AppUI->_('doDelete',UI_OUTPUT_JS).' '.$AppUI->_('Allocation',UI_OUTPUT_JS).'?';?>";
</script>
<form name="frmDelete" action="?m=human_resources" method="post">
  <input type="hidden" name="dosql" value="do_allocation_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="task_id" value="<?php echo dPformSafe($task_id); ?>"/>
  <input type="hidden" name="user_id" value="<?php echo dPformSafe($human_resource->human_resource_user_id); ?>"/>
  <input type="hidden" name="human_resource_allocation_id" value="<?php echo $human_resource_allocation_id;?>" />
</form>

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
			<td class="hilite" width="100%"><?php echo $res_company_role->fields['role_name'];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated role responsability');?>:</td>
			<td class="hilite" width="100%"><?php echo $res_company_role->fields['human_resources_role_responsability'];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated role authority');?>:</td>
			<td class="hilite" width="100%"><?php echo $res_company_role->fields['human_resources_role_authority'];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated role competence');?>:</td>
			<td class="hilite" width="100%"><?php echo $res_company_role->fields['human_resources_role_competence'];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user contact');?>:</td>
			<td class="hilite" width="100%"><?php echo $contact_name;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Allocated user name');?>:</td>
			<td class="hilite" width="100%"><?php echo $res_hr->fields['user_username'];?></td>
		</tr>
		</table>
	</td>
</table>
<tr>
  <td>
     <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>       
  </td>
</tr>

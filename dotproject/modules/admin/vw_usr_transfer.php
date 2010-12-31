<?php
/*
 * Allow a user's tasks and projects to be re-assigned to another
 * user.  Only accessible by an admin, used to handle the situation
 * where a user has moved on and another is taking their place.
 */
global $AppUI, $user_id, $canEdit, $canDelete, $tab;

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (! $canEdit) {
	$AppUI->redirect('m=public&a=access_denied');
}


// Get the list of projects associated with this user.
// We need to get all projects that are owned by the user
// or where the user is a project contact.
// We then need to add all projects where a user is assigned a task, is a
// task contact, or is the owner of a task within that project.
// This should cover all situations that we need to correct.
$projects = array();

$q = new DBQuery();
$q->addQuery('user_contact');
$q->addTable('users');
$q->addWhere('user_id = ' . (int)$user_id);
$contact_id = $q->loadResult();

$q->addQuery('distinct project_id, project_name');
$q->addTable('projects');
$q->addWhere('project_owner = ' .(int)$user_id);
$projects += $q->loadHashList();

$q->addQuery('distinct prj.project_id, prj.project_name');
$q->addTable('projects', 'prj');
$q->innerJoin('project_contacts', 'prc',  array('project_id'));
$q->addWhere('prc.contact_id = ' . (int)$contact_id);
$projects += $q->loadHashList();

$q->addQuery('distinct prj.project_id, prj.project_name');
$q->addTable('tasks',  't');
$q->innerJoin('projects', 'prj', 'prj.project_id = t.task_project');
$q->leftJoin('user_tasks', 'ut', 'ut.task_id = t.task_id');
$q->addWhere('t.task_owner = ' . (int)$user_id
	. ' OR ut.user_id = ' . (int)$user_id);
$projects += $q->loadHashList();

$q->addQuery('distinct prj.project_id, prj.project_name');
$q->addTable('tasks', 't');
$q->innerJoin('projects', 'prj', 'prj.project_id = t.task_project');
$q->innerJoin('task_contacts', 'tc', 'tc.task_id = t.task_id');
$q->addWhere('tc.contact_id = ' . (int)$contact_id);
$projects += $q->loadHashList();

$q->addQuery('user_id, concat(u.user_username, \' (\', c.contact_first_name, \' \', c.contact_last_name, \')\') as username');
$q->addTable('users', 'u');
$q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
$q->addWhere('u.user_id != ' . (int)$user_id);
$q->addOrder('u.user_username');
$users = $q->loadHashList();

?>
<script type="text/javascript">
function checkAllProjects()
{
	var sellist = document.getElementById('prjSelectList');
	var l = sellist.options.length;
	for (var i  = 0; i < l; i++) {
		sellist.options[i].selected = true;
	}
}
</script>
<p>Transfer tasks and projects from this user to another</p>
<form name="frmTxfr" method="post" action="?m=admin">
	<input type="hidden" name="dosql" value="do_user_transfer" />
	<input type="hidden" name="from_user" value="<?php echo $user_id; ?>" />
<table cellspacing="0" cellpadding="2" border="0" width="100%">
<tr><td width="50%" valign="top">
<?php
	// Left hand panel shows list of user's projects, allowing
	// each project to be selected, or all.
?>
<table width="100%" cellspacing="1" cellpadding="2" border="0" class="std">
<tr><th><?php echo $AppUI->_('Transfer Projects'); ?></th></tr>
<tr><td><?php echo arraySelect($projects, 'project[]', 'multiple="multiple" size="8" id="prjSelectList"', false); ?></td></tr>
<tr><td><input type="button" name="selectAll" value="<?php echo $AppUI->_('Select All'); ?>" onclick="checkAllProjects();" /></td></tr>
</table>
</td>
<td width="50%" valign="top">
<?php
	// Right hand panel shows list of users who can receive the tasks.
?>
<table width="100%" cellspacing="1" cellpadding="2" border="0" class="std">
<tr><th><?php echo $AppUI->_('To User'); ?></th></tr>
<tr><td><?php echo arraySelect($users, 'user', 'size="8"', false); ?></td></tr>
<tr><td><input type="submit" name="Go" value="Transfer"/></td></tr>
</table>
</td></tr>
</table>
</form>

<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (! $AppUI->acl()->checkModule($m, 'edit')) {
	$AppUI->redirect('m=public&a=access_denied');
}

$user_id = (int)dPgetParam($_POST, 'user');
$projects = dPgetParam($_POST, 'project');
$from_user = (int)dPgetParam($_POST, 'from_user');

if (count($projects) > 1) {
	$project_where = 'IN (';
	$first = true;
	foreach ($projects as $prj) {
		if ($first) {
			$first = false;
		} else {
			$project_where .= ',';
		}
		$project_where .= (int)$prj;
	}
	$project_where .= ')';
} else {
	$project_where = '= ' . (int)$projects[0];
}

// Need to figure out which items to update.  Easiest to do this
// as separate queries.
// Projects:
$q = new DBQuery();
$q->addUpdate('project_owner', $user_id);
$q->addTable('projects');
$q->addWhere('project_owner =  ' . $from_user);
$q->addWhere('project_id' . $project_where);
if (! $q->exec()) {
	$AppUI->setMsg('failed to update project owner', UI_MSG_ERROR);
	return;
}
$q->clear();

$q->addUpdate('contact_id', $user_id);
$q->addTable('project_contacts');
$q->addWhere('contact_id = ' . $from_user);
$q->addWhere('project_id ' . $project_where);
if (!$q->exec()) {
	$AppUI->setMsg('failed to update project contacts', UI_MSG_ERROR);
	return;
}
$q->clear();

// Tasks:
$q->addUpdate('task_owner', $user_id);
$q->addTable('tasks');
$q->addWhere('task_owner =  ' . $from_user);
$q->addWhere('task_project' . $project_where);
if (!$q->exec()) {
	$AppUI->setMsg('failed to update task owner', UI_MSG_ERROR);
	return;
}
$q->clear();

$q->addQuery('task_id');
$q->addTable('tasks');
$q->addWhere('task_project' . $project_where);
$task_sql = $q->prepare();
$q->clear();

$q->addUpdate('contact_id', $user_id);
$q->addTable('task_contacts');
$q->addWhere('contact_id = ' . $from_user);
$q->addWhere('task_id IN (' . $task_sql . ')');
if (!$q->exec()) {
	$AppUI->setMsg('failed to update task contacts', UI_MSG_ERROR);
	return;
}
$q->clear();

$q->addUpdate('user_id', $user_id);
$q->addTable('user_tasks');
$q->addWhere('user_id = ' . $from_user);
$q->addWhere('task_id IN (' . $task_sql . ')');
if (!$q->exec()) {
	$AppUI->setMsg('failed to update task assignments', UI_MSG_ERROR);
	return;
}
$q->clear();
$AppUI->setMsg('updated', UI_MSG_OK);

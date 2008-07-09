<?php  /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

// load the companies class to retrieved denied companies
require_once ($AppUI->getModuleClass('companies'));

// Let's update project status!
if (isset($_GET['update_project_status']) && isset($_GET['project_status']) 
   && isset($_GET['project_id'])) {
	$projects_id = $_GET['project_id']; // This must be an array
	
	foreach ($projects_id as $project_id) {
		$r  = new DBQuery;
		$r->addTable('projects');
		$r->addUpdate('project_status', $_GET['project_status']);
		$r->addWhere('project_id = ' . $project_id);
		$r->exec();
		$r->clear();
	}
}

// End of project status update
// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('ProjIdxTab', $_GET['tab']);
}

$tab = $AppUI->getState('ProjIdxTab') !== NULL ? $AppUI->getState('ProjIdxTab') : 500;
$currentTabId = $tab;
$active = intval(!$AppUI->getState('ProjIdxTab'));

if (isset($_POST['company_id'])) {
	$AppUI->setState('ProjIdxCompany', intval($_POST['company_id']));
}
$company_id = (($AppUI->getState('ProjIdxCompany') !== NULL) 
               ? $AppUI->getState('ProjIdxCompany') 
               : $AppUI->user_company);

$company_prefix = 'company_';

if (isset($_POST['department'])) {
	$AppUI->setState('ProjIdxDepartment', $_POST['department']);
	
	//if department is set, ignore the company_id field
	unset($company_id);
}
$department = (($AppUI->getState('ProjIdxDepartment') !== NULL) 
               ? $AppUI->getState('ProjIdxDepartment') 
               : ($company_prefix . $AppUI->user_company));

//if $department contains the $company_prefix string that it's requesting a company and not a department.  So, clear the 
// $department variable, and populate the $company_id variable.
if (!(strpos($department, $company_prefix)===false)) {
	$company_id = substr($department,strlen($company_prefix));
	$AppUI->setState('ProjIdxCompany', $company_id);
	unset($department);
}

$valid_ordering = array(
	'project_name',
	'user_username',
	'my_tasks desc',
	'total_tasks desc',
	'total_tasks',
	'my_tasks',
	'project_color_identifier',
	'company_name',
	'project_end_date',
	'project_start_date',
	'project_actual_end_date',
	'task_log_problem DESC,project_priority',
	'project_status'
);
$orderdir = $AppUI->getState('ProjIdxOrderDir') ? $AppUI->getState('ProjIdxOrderDir') : 'asc';
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
	$orderdir = (($AppUI->getState('ProjIdxOrderDir') == 'asc') ? 'desc' : 'asc');
	$AppUI->setState('ProjIdxOrderBy', $_GET['orderby']);
}
$orderby = (($AppUI->getState('ProjIdxOrderBy'))
            ? $AppUI->getState('ProjIdxOrderBy') : 'project_end_date');
$AppUI->setState('ProjIdxOrderDir', $orderdir);

// prepare the users filter
if (isset($_POST['show_owner'])) {
	$AppUI->setState('ProjIdxowner', intval($_POST['show_owner']));
}
$owner = $AppUI->getState('ProjIdxowner') !== NULL ? $AppUI->getState('ProjIdxowner') : 0;


$bufferUser = '<select name="show_owner" onchange="document.pickUser.submit()" class="text">';
$bufferUser .= '<option value="0">' . $AppUI->_('All Users') . '</option>';

$usersql = ('SELECT user_id, user_username, contact_first_name, contact_last_name' 
            . ' FROM users, contacts' 
            . ' WHERE user_contact = contact_id' 
            . ' ORDER BY contact_last_name');

if (($rows = db_loadList($usersql, NULL))) {
	foreach ($rows as $row) {
		$bufferUser .= ('<option value="' . $row['user_id'] . '"'
		                . (($owner == $row['user_id']) ? ' selected="selected"' : '') . '>'
		                . $row['contact_last_name'] . ', ' . $row['contact_first_name'] 
		                . ' (' . $row['user_username'] . ') </option>');
	}
}

/* setting this to filter project_list_data function below
 0 = undefined
 3 = active
 5 = completed
 7 = archived

Because these are "magic" numbers, if the values for ProjectStatus change under 'System Admin', 
they'll need to change here as well (sadly).
*/
if ($tab != 7 && $tab != 8) {
	$project_status = $tab;
} elseif ($tab == 0) {
	$project_status = 0;
}
if ($tab == 5 || $tab == 7) {
	$project_active = 0;
}

//for getting permissions for records related to projects
$obj_project = new CProject();
// collect the full (or filtered) projects list data via function in projects.class.php
projects_list_data();

// setup the title block
$titleBlock = new CTitleBlock('Projects', 'applet3-48.png', $m, ($m . '.' . $a));
$titleBlock->addCell($AppUI->_('Owner') . ':');
$titleBlock->addCell($bufferUser, '', '<form action="?m=projects" method="post" name="pickUser">'
                     , '</form>');
$titleBlock->addCell($AppUI->_('Company') . '/' . $AppUI->_('Division') . ':');
$titleBlock->addCell($buffer, '', '<form action="?m=projects" method="post" name="pickCompany">'
                     , '</form>');
$titleBlock->addCell();
if ($canAuthor) {
	$titleBlock->addCell('<input type="submit" class="button" value="' 
	                     . $AppUI->_('new project') . '">', ''
	                     ,'<form action="?m=projects&a=addedit" method="post">', '</form>');
}
$titleBlock->show();

$project_types = dPgetSysVal('ProjectStatus');

// count number of projects per project_status
$q  = new DBQuery();
$q->addTable('projects', 'p');
$q->addQuery('p.project_status, COUNT(p.project_id) as count');
$obj_project->setAllowedSQL($AppUI->user_id, $q);
if ($owner > 0) {
	$q->addWhere('p.project_owner = ' . $owner);
}
if (isset($department)) {
	$q->addJoin('project_departments', 'pd', 'pd.project_id = p.project_id');
	if (!$addPwOiD) {
		$q->addWhere('pd.department_id in (' . implode(',',$dept_ids) . ')');
	}
} else if ($company_id &&!$addPwOiD) {
	$q->addWhere('p.project_company = ' . $company_id);
}
$q->addGroup('project_status');
$statuses = $q->loadHashList('project_status');
$q->clear();
$all_projects = 0;
foreach ($statuses as $k => $v) {
	$project_status_tabs[$v['project_status']] = ($AppUI->_($project_types[$v['project_status']]) 
													  . ' (' . $v['count'] . ')');
	//count all projects
	$all_projects += $v['count'];
}

//set file used per project status title
$fixed_status = array('In Progress' => 'vw_idx_active',
					  'Complete' => 'vw_idx_complete',
					  'Archived' => 'vw_idx_archived');

/**
* Now, we will figure out which vw_idx file are available
* for each project status using the $fixed_status array 
*/
$project_status_file = array();
foreach ($project_types as $status_id => $status_title) {
	//if there is no fixed vw_idx file, we will use vw_idx_proposed
	$project_status_file[$status_id] = ((isset($fixed_status[$status_title])) 
										? $fixed_status[$status_title] : 'vw_idx_proposed');
}

// tabbed information boxes
$tabBox = new CTabBox('?m=projects', DP_BASE_DIR . '/modules/projects/', $tab);

$tabBox->add('vw_idx_proposed', $AppUI->_('All') . ' (' . $all_projects . ')' , true,  500);
foreach ($project_types as $psk => $project_status) {
		$tabBox->add($project_status_file[$psk], 
					 (($project_status_tabs[$psk]) ? $project_status_tabs[$psk] : $AppUI->_($project_status)), true, $psk);
}
$min_view = true;
$tabBox->add('viewgantt', 'Gantt');
$tabBox->show();
?>

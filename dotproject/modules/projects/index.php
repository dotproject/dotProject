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

$orderdir = $AppUI->getState('ProjIdxOrderDir') ? $AppUI->getState('ProjIdxOrderDir') : 'asc';
if (isset($_GET['orderby'])) {
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

// collect the full projects list data via function in projects.class.php
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

$project_types = dPgetSysVal("ProjectStatus");

$active = 0;
$complete = 0;
$archive = 0;
$proposed = 0;

foreach ($project_types as $key=>$value) {
	$counter[$key] = 0;
	if (is_array($projects)) {
		foreach ($projects as $p) {
			if ($p['project_status'] == $key) {
				++$counter[$key];
			}
		}
	}
	$project_types[$key] = ($AppUI->_($project_types[$key], UI_OUTPUT_RAW) 
	                        . ' (' . $counter[$key] . ')');
}


if (is_array($projects)) {
	foreach ($projects as $p) {
		switch ($p['project_status']) {
		case 3:
		  ++$active;
		  break;
		case 5:
		  ++$complete;
		  break;
		default:
		  ++$proposed;
		  break;
		}
	}
}

$fixed_types = array($AppUI->_('In Progress', UI_OUTPUT_RAW) . ' (' . $active . ')' 
                     => 'vw_idx_active',
                     $AppUI->_('Complete', UI_OUTPUT_RAW) . ' (' . $complete . ')' 
                     => 'vw_idx_complete',
                     $AppUI->_('Archived', UI_OUTPUT_RAW) . ' (' . $counter['7'] . ')' 
                     => 'vw_idx_archived');

/**
* Now, we will figure out which vw_idx file are available
* for each project type using the $fixed_types array 
*/
$project_type_file = array();

foreach ($project_types as $project_type) {
	$project_type = trim($project_type);
	// if there is no fixed vw_idx file, we will use vw_idx_proposed
	$project_file_type[$project_type] = ((isset($fixed_types[$project_type])) 
	                                     ? $fixed_types[$project_type] : 'vw_idx_proposed');
}

// tabbed information boxes
$tabBox = new CTabBox('?m=projects', DP_BASE_DIR . '/modules/projects/', $tab);

$tabBox->add('vw_idx_proposed', $AppUI->_('All', UI_OUTPUT_RAW) . ' (' . count($projects) . ')' , true,  500);
foreach ($project_types as $ptk=>$project_type) {
		$tabBox->add($project_file_type[$project_type], $project_type, true, $ptk);
}
$min_view = true;
$tabBox->add('viewgantt', 'Gantt');
$tabBox->show();
?>

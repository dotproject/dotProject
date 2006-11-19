<?php  /* PROJECTS $Id$ */
$AppUI->savePlace();

// load the companies class to retrieved denied companies
require_once( $AppUI->getModuleClass( 'companies' ) );

// Let's update project status!
if(isset($_GET["update_project_status"]) && isset($_GET["project_status"]) && isset($_GET["project_id"]) ){
	$projects_id = $_GET["project_id"]; // This must be an array

	foreach($projects_id as $project_id){
		$r  = new DBQuery;
		$r->addTable('projects');
		$r->addUpdate('project_status', "{$_GET['project_status']}");
		$r->addWhere('project_id   = '.$project_id);
		$r->exec();
		$r->clear();
	}
}
// End of project status update

// retrieve any state parameters
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'ProjIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'ProjIdxTab' ) !== NULL ? $AppUI->getState( 'ProjIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'ProjIdxTab' ) );

if (isset( $_POST['company_id'] )) {
	$AppUI->setState( 'ProjIdxCompany', intval( $_POST['company_id'] ) );
}
$company_id = $AppUI->getState( 'ProjIdxCompany' ) !== NULL ? $AppUI->getState( 'ProjIdxCompany' ) : $AppUI->user_company;

$company_prefix = 'company_';

if (isset( $_POST['department'] )) {
	$AppUI->setState( 'ProjIdxDepartment', $_POST['department'] );
	
	//if department is set, ignore the company_id field
	unset($company_id);
}
$department = $AppUI->getState( 'ProjIdxDepartment' ) !== NULL ? $AppUI->getState( 'ProjIdxDepartment' ) : $company_prefix.$AppUI->user_company;

//if $department contains the $company_prefix string that it's requesting a company and not a department.  So, clear the 
// $department variable, and populate the $company_id variable.
if(!(strpos($department, $company_prefix)===false)){
	$company_id = substr($department,strlen($company_prefix));
	$AppUI->setState( 'ProjIdxCompany', $company_id );
	unset($department);
}

if (isset( $_GET['orderby'] )) {
    $orderdir = $AppUI->getState( 'ProjIdxOrderDir' ) ? ($AppUI->getState( 'ProjIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';    
    $AppUI->setState( 'ProjIdxOrderBy', $_GET['orderby'] );
    $AppUI->setState( 'ProjIdxOrderDir', $orderdir);
}
$orderby  = $AppUI->getState( 'ProjIdxOrderBy' ) ? $AppUI->getState( 'ProjIdxOrderBy' ) : 'project_end_date';
$orderdir = $AppUI->getState( 'ProjIdxOrderDir' ) ? $AppUI->getState( 'ProjIdxOrderDir' ) : 'asc';

// collect the full projects list data via function in projects.class.php
projects_list_data();

// setup the title block
$titleBlock = new CTitleBlock( 'Projects', 'applet3-48.png', $m, "$m.$a" );
$titleBlock->addCell( $AppUI->_('Company') . '/' . $AppUI->_('Division') . ':');
$titleBlock->addCell( $buffer, '', '<form action="?m=projects" method="post" name="pickCompany">', '</form>');
$titleBlock->addCell();
if ($canAuthor) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new project').'">', '',
		'<form action="?m=projects&a=addedit" method="post">', '</form>'
	);
}
$titleBlock->show();

$project_types = dPgetSysVal("ProjectStatus");

$active = 0;
$complete = 0;
$archive = 0;
$proposed = 0;

foreach($project_types as $key=>$value)
{
        $counter[$key] = 0;
	if (is_array($projects)) {
		foreach ($projects as $p)
			if ($p['project_status'] == $key)
				++$counter[$key];
	}
        $project_types[$key] = $AppUI->_($project_types[$key], UI_OUTPUT_RAW) . ' (' . $counter[$key] . ')';
}


if (is_array($projects)) {
        foreach ($projects as $p)
        {
                if ($p['project_status'] == 3)
                        ++$active;
                else if ($p['project_status'] == 5)
                        ++$complete;
                else
                        ++$proposed;
        }
}

$fixed_project_type_file = array(
        $AppUI->_('In Progress', UI_OUTPUT_RAW) . ' (' . $active . ')' => "vw_idx_active",
        $AppUI->_('Complete', UI_OUTPUT_RAW) . ' (' . $complete . ')'    => "vw_idx_complete",
				$AppUI->_('Archived', UI_OUTPUT_RAW). ' (' . $counter['7'] . ')' => 'vw_idx_archived');

/**
* Now, we will figure out which vw_idx file are available
* for each project type using the $fixed_project_type_file array 
*/
$project_type_file = array();

foreach($project_types as $project_type){
	$project_type = trim($project_type);
	if(isset($fixed_project_type_file[$project_type])){
		$project_file_type[$project_type] = $fixed_project_type_file[$project_type];
	} else { // if there is no fixed vw_idx file, we will use vw_idx_proposed
		$project_file_type[$project_type] = "vw_idx_proposed";
	}
}

// tabbed information boxes
$tabBox = new CTabBox( "?m=projects", "{$dPconfig['root_dir']}/modules/projects/", $tab );

$tabBox->add( 'vw_idx_proposed', $AppUI->_('All', UI_OUTPUT_RAW). ' (' . count($projects) . ')' , true,  1000);
foreach($project_types as $ptk=>$project_type) {
		$tabBox->add($project_file_type[$project_type], $project_type, true, $ptk);
}
$min_view = true;
$tabBox->add("viewgantt", "Gantt");
$tabBox->show();
?>

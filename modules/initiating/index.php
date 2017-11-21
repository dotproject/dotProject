<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
$AppUI->savePlace();
// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
	$AppUI->setState('InitiatingIdxProject', intval($_REQUEST['project_id']));
}
$project_id = $AppUI->getState('InitiatingIdxProject') !== NULL ? $AppUI->getState('InitiatingIdxProject') : 0;
if (dPgetParam($_GET, 'tab', -1) != -1) {
	$AppUI->setState('InitiatingIdxTab', intval(dPgetParam($_GET, 'tab')));
}
$tab = $AppUI->getState('InitiatingIdxTab') !== NULL ? $AppUI->getState('InitiatingIdxTab') : 0;
$active = intval(!$AppUI->getState('InitiatingIdxTab'));
require_once($AppUI->getModuleClass('projects'));
//get the list of visible companies
$extra = array(
	//'from' => 'links',
	//'where' => 'project_id = link_project'
);
$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra);
$projects = arrayMerge(array('0'=>$AppUI->_('All', UI_OUTPUT_JS)), $projects);
// setup the title block
$titleBlock = new CTitleBlock('Initiating', 'applet3-48.png', $m, "$m.$a");
$titleBlock->addCell($AppUI->_('Search') . ':');
$titleBlock->addCell(
        '<input type="text" class="text" SIZE="10" name="search" onChange="document.searchfilter.submit();" value=' . "'$search'" .         'title="'. $AppUI->_('Search in text', UI_OUTPUT_JS) . '"/>'
 ,'',       '<form action="?m=initiating" method="post" id="searchfilter">', '</form>'
);
$titleBlock->addCell($AppUI->_('Filter') . ':');
$titleBlock->addCell(
	arraySelect($projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id), '',
	'<form name="pickProject" action="?m=initiating" method="post">', '</form>'
);
//if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('novo termo de abertura').'">', '',
		'<form action="?m=initiating&a=addedit" method="post">', '</form>'
	);
//}
$titleBlock->show();
$tabBox = new CTabBox('?m=initiating', DP_BASE_DIR.'/modules/initiating/', $tab);
$tabBox->add('index_table', 'All');
$tabBox->add('vw_completed', 'Completed');
$tabBox->add('vw_approved', 'Approved');
$tabBox->add('vw_authorized', 'Authorized');
$tabBox->show();
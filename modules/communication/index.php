
<?php

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

//limpar a sessão
unset($_SESSION['receptors']); 
unset($_SESSION['emitters']);

$AppUI->savePlace();

// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
	$AppUI->setState('CommunicationIdxProject', intval($_REQUEST['project_id']));
}

$project_id = $AppUI->getState('CommunicationIdxProject') !== NULL ? $AppUI->getState('CommunicationIdxProject') : 0;

if (dPgetParam($_GET, 'tab', -1) != -1) {
	$AppUI->setState('CommunicationIdxTab', intval(dPgetParam($_GET, 'tab')));
}
$tab = $AppUI->getState('CommunicationIdxTab') !== NULL ? $AppUI->getState('CommunicationIdxTab') : 0;
$active = intval(!$AppUI->getState('CommunicationIdxTab'));

require_once($AppUI->getModuleClass('projects'));

$extra = array();

$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id, project_name', 'project_name', null, $extra);
$projects = arrayMerge(array('0'=>$AppUI->_('All', UI_OUTPUT_JS)), $projects);

// setup the title block
$titleBlock = new CTitleBlock("LBL_COMMUNICATION", 'applet3-48.png', $m, "$m.$a");



$titleBlock->addCell($AppUI->_("LBL_FILTER") . ':');
$titleBlock->addCell(
	arraySelect($projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id), '',
	'<form name="pickProject" action="?m=communication" method="post">', '</form>'
);

$titleBlock->addCell(
		'<input type="submit" class="button" style="font-weight:bold" value="'.$AppUI->_("LBL_NEW_COMMUNICATION").'">', '',
		'<form action="?m=communication&a=addedit" method="post">', '</form>'
	);

$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_("LBL_NEW_CCHANNEL").'">', '',
		'<form action="?m=communication&a=addedit_channel" method="post">', '</form>'
	);

$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_("LBL_NEW_CFREQUENCY").'">', '',
		'<form action="?m=communication&a=addedit_frequency" method="post">', '</form>'
	);

$titleBlock->show();

$tabBox = new CTabBox('?m=communication', DP_BASE_DIR.'/modules/communication/', $tab);
$tabBox->add('index_table', "LBL_ALL");
$tabBox->show();

?>

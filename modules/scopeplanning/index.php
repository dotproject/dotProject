<?php

/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
//limpar a sessão
unset($_SESSION['receptors']); 
unset($_SESSION['emitters']);

$AppUI->savePlace();

// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
    $AppUI->setState('ScopePlanIndexProject', intval($_REQUEST['project_id']));
}
$project_id = $AppUI->getState('ScopePlanIndexProject') !== NULL ? $AppUI->getState('ScopePlanIndexProject') : 0;

if (dPgetParam($_GET, 'tab', -1) != -1) {
    $AppUI->setState('ScopePlanIndexTab', intval(dPgetParam($_GET, 'tab')));
}
$tab = $AppUI->getState('ScopePlanIndexTab') !== NULL ? $AppUI->getState('ScopePlanIndexTab') : 0;
$active = intval(!$AppUI->getState('ScopePlanIndexTab'));

require_once($AppUI->getModuleClass('projects'));
require_once($AppUI->getModuleClass('timeplanning'));

$extra = array();

$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id, project_name', 'project_name', null, $extra);
$projects = arrayMerge(array('0' => $AppUI->_('LBL_SP_ALL', UI_OUTPUT_JS)), $projects);

// setup the title block
$titleBlock = new CTitleBlock('LBL_SP_SCOPEPLANNING', 'scope.png', $m, "$m.$a");
$titleBlock->addCell($AppUI->_('LBL_SP_FILTER') . ':');
$titleBlock->addCell(
        arraySelect($projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id), '', '<form name="pickProject" action="?m=scopeplanning" method="post">', '</form>'
);
$titleBlock->addCell(
        '<input type="submit" class="button" value="' . $AppUI->_('LBL_SP_NEWREQ') . '">', '', '<form action="?m=scopeplanning&a=addedit_requirement" method="post">', '</form>'
);
$titleBlock->show();

$tabBox = new CTabBox('?m=scopeplanning', DP_BASE_DIR . '/modules/scopeplanning/', $tab);
$tabBox->add('vw_req_managplan', 'LBL_SP_REQMANAGPLAN');
$tabBox->add('vw_scope_statement', 'LBL_SP_SCOPESTAT');
$tabBox->add('vw_req_docum', 'LBL_SP_REQDOC');
$tabBox->add('vw_req_tracmatrix', 'LBL_SP_TRACKMATRIX');
$tabBox->add('vw_wbs', 'LBL_SP_WBS');
$tabBox->add('vw_wbs_dictionary', 'LBL_SP_WBS_DICTIONARY');
$tabBox->show();
?>

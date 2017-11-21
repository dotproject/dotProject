<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$AppUI->savePlace();

// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
    $AppUI->setState('RisksIdxProject', intval($_REQUEST['project_id']));
}
$project_id = $AppUI->getState('RisksIdxProject') !== NULL ? $AppUI->getState('RisksIdxProject') : 0;
if (dPgetParam($_GET, 'tab', -1) != -1) {
    $AppUI->setState('RisksIdxTab', intval(dPgetParam($_GET, 'tab')));
}
$tab = $AppUI->getState('RisksIdxTab') !== NULL ? $AppUI->getState('RisksIdxTab') : 0;
$active = intval(!$AppUI->getState('RisksIdxTab'));

require_once($AppUI->getModuleClass('projects'));

$extra = array();
$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra);
$projects = arrayMerge(array('0' => $AppUI->_('LBL_ALL', UI_OUTPUT_JS)), $projects);

// setup the title block
$titleBlock = new CTitleBlock('LBL_RISKS', 'risks.png', $m, "$m.$a");
$titleBlock->show();

$tabBox = new CTabBox('?m=risks', DP_BASE_DIR . '/modules/risks/', $tab);
$tabBox->add('index_table', 'LBL_ALL');
$tabBox->add('vw_watchlist', 'LBL_WATCHLIST');
$tabBox->add('vw_near_term_responses_list', 'LBL_NEARTERM');
$tabBox->add('vw_lessons_learned_list', 'LBL_LESSONS_LIST');
$tabBox->add('vw_strategys_list', 'LBL_STRATEGYS_LIST');
$tabBox->show();
?>
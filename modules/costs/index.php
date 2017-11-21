<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$AppUI->savePlace();

// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
    $AppUI->setState('CostsIdxProject', intval($_REQUEST['project_id']));
}
$project_id = $AppUI->getState('CostsIdxProject') !== NULL ? $AppUI->getState('CostsIdxProject') : 0;
if (dPgetParam($_GET, 'tab', -1) != -1) {
    $AppUI->setState('CostsIdxTab', intval(dPgetParam($_GET, 'tab')));
}
$tab = $AppUI->getState('CostsIdxTab') !== NULL ? $AppUI->getState('CostsIdxTab') : 0;
$active = intval(!$AppUI->getState('CostsIdxTab'));

require_once($AppUI->getModuleClass('projects'));

$extra = array();
$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra);
$projects = arrayMerge(array('0' => $AppUI->_('All', UI_OUTPUT_JS)), $projects);

// setup the title block
$titleBlock = new CTitleBlock('Cost Estimatives and Budget', 'costs.png', $m, "$m.$a");
$titleBlock->show();

$tabBox = new CTabBox('?m=costs', DP_BASE_DIR . '/modules/costs/', $tab);
$tabBox->add('vw_costs', 'Estimative Costs');
$tabBox->add('vw_budget', 'Budget');
$tabBox->show();

?>
 

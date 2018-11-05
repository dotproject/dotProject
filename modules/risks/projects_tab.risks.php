<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig, $cfObj, $m;
require_once($AppUI->getModuleClass('risks'));

global $allowed_folders_ary, $denied_folders_ary, $limited;

$showProject = false;

$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/risks/locale.php");
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
$titleBlock = new CTitleBlock('LBL_RISKS', "../modules/risks/images/risks.png", $m, "$m.$a");
    
$titleBlock->addCell(
        '<input type="submit" class="button" style="font-weight: bold" value="' . $AppUI->_("LBL_RISK_MANAGEMENT_PLAN") . '">', '', 
        '<form action="?m=risks&a=risk_management_plan&project_id='. $project_id .'" method="post">', '</form>'
      );
        
$titleBlock->addCell(
        '<input type="submit" class="button" value="' . $AppUI->_('LBL_CHECKLIST_ANALYSIS') . '">', '', 
        '<form action="?m=risks&a=checklist_risks_model&project_id='. $project_id .'" method="post">', '</form>'
      );
     $titleBlock->addCell(
        '<input type="submit" class="button" value="' . $AppUI->_('LBL_WATCHLIST') . '">', '', 
        '<form action="?m=risks&a=vw_watchlist&project_id=' . $project_id . '&tab='. $tab.'" method="post">', '</form>'
    );
    $titleBlock->addCell(
        '<input type="submit" class="button" value="' . $AppUI->_('LBL_NEARTERM') . '">', '', 
        '<form action="?m=risks&a=vw_near_term_responses_list&project_id=' . $project_id . '&tab='. $tab.'" method="post">', '</form>'
    );  
    $titleBlock->addCell(
        '<input type="submit" class="button" value="' . $AppUI->_('LBL_LESSONS_LIST') . '">', '', 
        '<form action="?m=risks&a=vw_lessons_learned_list&project_id=' . $project_id . '&tab='. $tab.'" method="post">', '</form>'
    ); 
    $titleBlock->addCell(
        '<input type="submit" class="button" value="' . $AppUI->_('LBL_STRATEGYS_LIST') . '">', '', 
        '<form action="?m=risks&a=vw_strategys_list&project_id=' . $project_id . '&tab='. $tab.'" method="post">', '</form>'
    ); 
    $titleBlock->addCell(
        '<input type="submit" class="button" style="font-weight:bold" value="' . $AppUI->_('LBL_NEW') . '">', '', 
        '<form action="?m=risks&a=addedit&project_id=' . $project_id . '&tab='. $tab.'" method="post">', '</form>'
    );   
$titleBlock->show();
include("index_table.php");
?>

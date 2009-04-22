<?php 
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

global $m, $a, $addPwOiD, $AppUI, $buffer, $company_id, $min_view, $priority, $projects, $tab;

$df = $AppUI->getPref('SHDATEFORMAT');

$pstatus =  dPgetSysVal('ProjectStatus');

$projFilter_extra = array('-4' => 'All w/o archived');

// load the companies class to retrieved denied companies
require_once($AppUI->getModuleClass('companies'));

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('DeptProjIdxTab', $_GET['tab']);
}

?>
<?php
$min_view = true;
require(DP_BASE_DIR.'/modules/projects/viewgantt.php');
?>

<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $m, $a, $addPwOiD, $AppUI, $cBuffer, $company_id, $department, $dept_id, $dept_ids;
global $priority, $projects, $tab, $user_id, $min_view;

$df = $AppUI->getPref('SHDATEFORMAT');

$department = isset($_GET['dept_id']) ? $_GET['dept_id'] : (isset($department) ? $department : 0);

$projFilter_extra = array('-4' => 'All w/o archived');

// load the companies class to retrieved denied companies
require_once($AppUI->getModuleClass('companies'));

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('DeptProjIdxTab', $_GET['tab']);
}

if (isset($_POST['show_form'])) {
	$AppUI->setState('addProjWithOwnerInDep',  (int)dPgetParam($_POST, 'add_pwoid', 0));
}
$addPwOiD = $AppUI->getState('addProjWithOwnerInDep') ? $AppUI->getState('addProjWithOwnerInDep') : 0;

$extraGet = '&user_id='.$user_id;
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td align="center" width="100%" nowrap="nowrap" colspan="6">&nbsp;</td>
	<td align="right" nowrap="nowrap">
		<form action="?m=departments&amp;tab=<?php echo $tab; ?>" method="post" name="checkPwOiD">
		<input type="checkbox" name="add_pwoid" id="add_pwoid" onclick="document.checkPwOiD.submit()" <?php echo $addPwOiD ? 'checked="checked"' : '';?> />
		<label for="add_pwoid"><?php echo $AppUI->_('Show Projects whose Owner is Member of the Dep.');?>?</label>
		<input type="hidden" name="show_form" value="1" />
		</form>
	</td>
</tr>
</table>
<?php
$min_view = true;
require(DP_BASE_DIR.'/modules/projects/viewgantt.php');
?>

<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

global $a, $addPwOiD, $AppUI, $buffer, $company_id, $department, $dept_ids, $min_view, $m, $priority, $projects, $tab, $user_id;

$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');

$pstatus =  dPgetSysVal( 'ProjectStatus' );

if (isset(  $_POST['proFilter'] )) {
	$AppUI->setState( 'DeptProjectIdxFilter',  $_POST['proFilter'] );
}
$proFilter = $AppUI->getState( 'DeptProjectIdxFilter' ) !== NULL ? $AppUI->getState( 'DeptProjectIdxFilter' ) : '-3';

$projFilter = arrayMerge( array('-1' => 'All Projects'), $pstatus);
$projFilter = arrayMerge( array( '-2' => 'All w/o in progress'), $projFilter);
$projFilter = arrayMerge( array( '-3' => 'All w/o archived'), $projFilter);
natsort($projFilter);

// load the companies class to retrieved denied companies
require_once( $AppUI->getModuleClass( 'companies' ) );

// retrieve any state parameters
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'DeptProjIdxTab', $_GET['tab'] );
}

if (isset($_POST['show_form'])) {
	$AppUI->setState( 'addProjWithOwnerInDep',  dPgetParam($_POST, 'add_pwoid', 0) );
}
$addPwOiD = $AppUI->getState( 'addProjWithOwnerInDep' ) ? $AppUI->getState( 'addProjWithOwnerInDep' ) : 0;

$extraGet = '&user_id='.$user_id;
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td align="center" width="100%" nowrap="nowrap" colspan="6">&nbsp;</td><td align="right" nowrap="nowrap"><form action="?m=departments&tab=<?php echo $tab; ?>" method="post" name="checkPwOiD"><input type="checkbox" name="add_pwoid" onclick="document.checkPwOiD.submit()" <?php echo $addPwOiD ? 'checked="checked"' : '';?>><?php echo $AppUI->_('Show Projects whose Owner is Member of the Dep.');?>?<input type="hidden" name="show_form" value="1" /></form></td>
</tr>
</table>
<?php
$min_view = true;
require(DP_BASE_DIR.'/modules/projects/viewgantt.php');
?>

<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

$AppUI->savePlace();
$perms =& $AppUI->acl();
$role_id = $_GET['role_id'];
$role = $perms->getRole($role_id);

if (isset($_GET['tab'])) {
	$AppUI->setState('RoleVwTab', $_GET['tab']);
}
$tab = $AppUI->getState('RoleVwTab') !== NULL ? $AppUI->getState('RoleVwTab') : 0;

if (! is_array($role)) {
	$titleBlock = new CTitleBlock('Invalid Role', 'main-settings.png', $m, "$m.$a");
	$titleBlock->addCrumb("?m=system&u=roles", "role list");
	$titleBlcok->show();
} else {
	$titleBlock = new CTitleBlock('View Role', 'main-settings.png', $m, "$m.$a");
	$titleBlock->addCrumb("?m=system&u=roles", "role list");
	$titleBlock->show();
	// Now onto the display of the user.
?>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Role ID');?>:</td>
			<td class="hilite" width="100%"><?php echo $role["value"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Description');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($role["name"]);?></td>
		</tr>
</table>

<?php
	$tabBox = new CTabBox("?m=system&u=roles&a=viewrole&role_id=$role_id", "./modules/system/roles/", $tab );
	$tabBox->add( 'vw_role_perms', 'Permissions');
	$tabBox->show();
} // End of check for valid role
?>
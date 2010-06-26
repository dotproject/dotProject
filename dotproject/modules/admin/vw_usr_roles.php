<?php /* ADMIN $Id$ */
GLOBAL $AppUI, $user_id, $canEdit, $canDelete, $tab;

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

//$roles
// Create the roles class container
require_once DP_BASE_DIR."/modules/system/roles/roles.class.php";

$perms =& $AppUI->acl();
$user_roles = $perms->getUserRoles($user_id);
$crole = new CRole;
$roles = $crole->getRoles();
// Format the roles for use in arraySelect
$roles_arr = array();
foreach ($roles as $role) {
  $roles_arr[$role['id']] = $role['name'];
}

?>

<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function delIt(id) {
	if (confirm('Are you sure you want to delete this role?')) {
		var f = document.frmPerms;
		f.del.value = 1;
		f.role_id.value = id;
		f.submit();
	}
}
<?php
}?>

</script>

<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr><td width="50%" valign="top">

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th width="100%"><?php echo $AppUI->_('Role');?></th>
	<th>&nbsp;</th>
</tr>

<?php
foreach ($user_roles as $row) {
	$buf = '';

	$style = '';
	$buf .= "<td>" . $row['name'] . "</td>";

	$buf .= '<td nowrap>';
	if ($canEdit) {
		$buf .= ('<a href="javascript:delIt(' . $row['id'] . ');" title="' . $AppUI->_('delete') 
				 . '">'. dPshowImage('./images/icons/stock_delete-16.png', 16, 16, '') . '</a>');
	}
	$buf .= '</td>';
	
	echo "<tr>$buf</tr>";
}
?>
</table>

</td><td width="50%" valign="top">

<?php if ($canEdit) {?>

<table cellspacing="1" cellpadding="2" border="0" class="std" width="100%">
<form name="frmPerms" method="post" action="?m=admin">
	<input type="hidden" name="del" value="0">
	<input type="hidden" name="dosql" value="do_userrole_aed">
	<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
	<input type="hidden" name="user_name" value="<?php echo $user_name;?>">
	<input type="hidden" name="role_id" value="">
<tr>
	<th colspan='2'><?php echo $AppUI->_('Add Role');?></th>
</tr>
<tr>
	<td colspan='2' width="100%"><?php echo arraySelect($roles_arr, 'user_role', 'size="1" class="text"','', true);?></td>
</tr>
<tr>
	<td>
		<input type="reset" value="<?php echo $AppUI->_('clear');?>" class="button" name="sqlaction" onClick="clearIt();">
	</td>
	<td align="right">
		<input type="submit" value="<?php echo $AppUI->_('add');?>" class="button" name="sqlaction2">
	</td>
</tr>
</table>
</form>

<?php } ?>

</td>
</tr>
</table>

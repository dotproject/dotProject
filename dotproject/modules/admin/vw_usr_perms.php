<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $user_id, $canEdit, $canDelete, $tab;

$perms =& $AppUI->acl();
$module_list = $perms->getModuleList();
$pgos = array();
$q  = new DBQuery;
$q->addTable('modules', 'm');
$q->addQuery('mod_id, mod_name, permissions_item_table');
$q->addWhere('permissions_item_table is not null');
$q->addWhere("permissions_item_table <> ''");
$pgo_list = $q->loadHashList('mod_name');
$q->clear();

// Build an intersection array for the modules and their listing
$modules = array();
$offset = 0;
foreach ($module_list as $module) {
  $modules[ $module['type'] . "," . $module['id']] = $module['name'];
  if ($module['type'] = 'mod' && isset($pgo_list[$module['name']]))
    $pgos[$offset] = $pgo_list[$module['name']]['permissions_item_table'];
  $offset++;
}
$count = 0;

//Pull User perms
$user_acls = $perms->getUserACLs($user_id);
if (! is_array($user_acls))
  $user_acls = array(); // Stops foreach complaining.
$perm_list = $perms->getPermissionList();

?>

<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function editPerm( id, gon, it, vl, nm ) {
/*
	id = Permission_id
	gon =permission_grant_on
	it =permission_item
	vl =permission_value
	nm = text representation of permission_value
*/
//alert( 'id='+id+'\ngon='+gon+'\nit='+it+'\nvalue='+vl+'\nnm='+nm);
	var f = document.frmPerms;

	f.sqlaction2.value = "<?php echo $AppUI->_('edit'); ?>";
	
	f.permission_id.value = id;
	f.permission_item.value = it;
	f.permission_item_name.value = nm;
	for(var i=0, n=f.permission_grant_on.options.length; i < n; i++) {
		if (f.permission_module.options[i].value == gon) {
			f.permission_module.selectedIndex = i;
			break;
		}
	}
	f.permission_value.selectedIndex = vl+1;
	f.permission_item_name.value = nm;
}

function clearIt(){
	var f = document.frmPerms;
	f.sqlaction2.value = "<?php echo $AppUI->_('add'); ?>";
	f.permission_id.value = 0;
	f.permission_grant_on.selectedIndex = 0;
}

function delIt(id) {
	if (confirm( 'Are you sure you want to delete this permission?' )) {
		var f = document.frmPerms;
		f.del.value = 1;
		f.permission_id.value = id;
		f.submit();
	}
}

var tables = new Array;
<?php
	foreach ($pgos as $key => $value){
		// Find the module id in the modules array
		echo "tables['$key'] = '$value';\n";
	}
?>

function popPermItem() {
	var f = document.frmPerms;
	var pgo = f.permission_module.selectedIndex;

	if (!(pgo in tables)) {
		alert( '<?php echo $AppUI->_('No list associated with this Module.', UI_OUTPUT_JS); ?>' );
		return;
	}
	f.permission_table.value = tables[pgo];
	window.open('./index.php?m=public&a=selector&dialog=1&callback=setPermItem&table=' + tables[pgo], 'selector', 'left=50,top=50,height=250,width=400,resizable')
}

// Callback function for the generic selector
function setPermItem( key, val ) {
	var f = document.frmPerms;
	if (val != '') {
		f.permission_item.value = key;
		f.permission_item_name.value = val;
		f.permission_name.value = val;
	} else {
		f.permission_item.value = '0';
		f.permission_item_name.value = 'all';
		f.permission_table.value = '';
	}
}
<?php } ?>
</script>

<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr><td width="50%" valign="top">

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th width="50%"><?php echo $AppUI->_('Module');?></th>
	<th width="50%"><?php echo $AppUI->_('Item');?></th>
	<th nowrap><?php echo $AppUI->_('Type');?></th>
	<th nowrap><?php echo $AppUI->_('Status');?></th>
	<th>&nbsp;</th>
</tr>

<?php
foreach ($user_acls as $acl){
	$buf = '';
	$permission = $perms->get_acl($acl);

	$style = '';
	// TODO: Do we want to make the colour depend on the allow/deny/inherit flag?
	// Module information.
	if (is_array($permission)) {
		$buf .= "<td $style>";
		$modlist = array();
		$itemlist = array();
		if (is_array($permission['axo_groups'])) {
			foreach ($permission['axo_groups'] as $group_id) {
				$group_data = $perms->get_group_data($group_id, 'axo');
				$modlist[] = $AppUI->_($group_data[3]);
				$itemlist[] = $AppUI->_('ALL');
			}
		}
		if (is_array($permission['axo'])) {
			foreach ($permission['axo'] as $key => $section) {
				// Find the module based on the key
				$mod_info = $perms->get_object_full($key, 'app', 1, 'axo');
				$modlist[] = $AppUI->_($mod_info['name']);
				foreach ($section as $id) {
					$mod_data = $perms->get_object_full($id, $key, 1, 'axo');
					$itemlist[] = $AppUI->_($mod_data['name']);
				}
			}
		}
		$buf .= implode("<br />", $modlist);
		$buf .= "</td><td>";
		$buf .= implode("<br />", $itemlist);
		$buf .= "</td>";
		// Item information TODO:  need to figure this one out.
	// 	$buf .= "<td></td>";
		// Type information.
		$buf .= "<td>";
		$perm_type = array();
		if (is_array($permission['aco'])) {
			foreach ($permission['aco'] as $key => $section) {
				foreach ($section as $value) {
					$perm = $perms->get_object_full($value, $key, 1, 'aco');
					$perm_type[] = $AppUI->_($perm['name']);
				}
			}
		}
		$buf .= implode("<br />", $perm_type);
		$buf .= "</td>";

		// Allow or deny
		$buf .= "<td>" . $AppUI->_( $permission['allow'] ? 'allow' : 'deny' ) . "</td>";
		$buf .= '<td nowrap>';
		if ($canDelete) {
			$buf .= "<a href=\"javascript:delIt({$acl});\" title=\"".$AppUI->_('delete')."\">"
				. dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' )
				. "</a>";
		}
		$buf .= '</td>';
		
		echo "<tr>$buf</tr>";
	}
}
?>
</table>

</td><td width="50%" valign="top">

<?php if ($canEdit) {?>

<table cellspacing="1" cellpadding="2" border="0" class="std" width="100%">
<form name="frmPerms" method="post" action="?m=admin">
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="dosql" value="do_perms_aed" />
	<input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
	<input type="hidden" name="permission_user" value="<?php echo $perms->get_object_id("user", $user_id, "aro");?>" />
	<input type="hidden" name="permission_id" value="0" />
	<input type="hidden" name="permission_item" value="0" />
	<input type="hidden" name="permission_table" value="" />
	<input type="hidden" name="permission_name" value="" />
<tr>
	<th colspan="2"><?php echo $AppUI->_('Add Permissions');?></th>
</tr>
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Module');?>:</td>
	<td width="100%"><?php echo arraySelect($modules, 'permission_module', 'size="1" class="text"', 'grp,all', true);?></td>
</tr>
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Item');?>:</td>
	<td>
		<input type="text" name="permission_item_name" class="text" size="30" value="all" disabled>
		<input type="button" name="" class="text" value="..." onclick="popPermItem();">
	</td>
</tr>
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Access');?>:</td>
	<td>
		<select name="permission_access" class="text">
			<option value='1'><?php echo $AppUI->_('allow');?></option>
			<option value='0'><?php echo $AppUI->_('deny');?></option>
		</select>
	</td>
</tr>
<?php
	foreach ($perm_list as $perm_id => $perm_name) {
?>
<tr>
	<td nowrap align='right'><?php echo $AppUI->_($perm_name);?>:</td>
	<td>
	  <input type='checkbox' name='permission_type[]' value='<?php echo $perm_id;?>'>
	</td>
</tr>
<?php
	}
?>
<tr>
	<td>
		<input type="reset" value="<?php echo $AppUI->_('clear');?>" class="button" name="sqlaction" onClick="clearIt();">
	</td>
	<td align="right">
		<input type="submit" value="<?php echo $AppUI->_('add');?>" class="button" name="sqlaction2">
	</td>
</tr>
</form>
</table>
<?php } ?>

</td>

</tr>



</tr>

</table>

<?php /* SYSTEM $Id$*/
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

$canEdit = getPermission($m, 'edit');
$canRead = getPermission($m, 'view');
if (!$canRead) {
	$AppUI->redirect('m=public&amp;a=access_denied');
}

$hidden_modules = array(
	'public',
	'install',
);
$q = new DBQuery;
$q->addQuery('*');
$q->addTable('modules');
foreach ($hidden_modules as $no_show) {
	$q->addWhere('mod_directory != \'' . $no_show . '\'');
}
$q->addOrder('mod_ui_order');
$modules = db_loadList($q->prepare());
// get the modules actually installed on the file system
$modFiles = $AppUI->readDirs('modules');

$titleBlock = new CTitleBlock('Modules', 'power-management.png', $m, $m . "." . $a);
$titleBlock->addCrumb('?m=system', 'System Admin');
$titleBlock->show();
?>

<table border="0" cellpadding="2" cellspacing="1" width="98%" class="tbl">
<tr>
	<th colspan="2"><?php echo $AppUI->_('Module');?></th>
	<th><?php echo $AppUI->_('Status');?></th>
	<th><?php echo $AppUI->_('Type');?></th>
	<th><?php echo $AppUI->_('Version');?></th>
	<th><?php echo $AppUI->_('Menu Text');?></th>
	<th><?php echo $AppUI->_('Menu Icon');?></th>
	<th><?php echo $AppUI->_('Menu Status');?></th>
	<th><?php echo $AppUI->_('#');?></th>
</tr>
<?php
// do the modules that are installed on the system
foreach ($modules as $row) {
	// clear the file system entry
	if (isset($modFiles[$row['mod_directory']])) {
		$modFiles[$row['mod_directory']] = '';
	}
	$query_string = '?m='.$m.'&amp;a=domodsql&amp;mod_id='.$row['mod_id'];
	$s = '';
	// arrows
	// TODO: sweep this block of code and add line returns to improve View Source readability 
	// [kobudo 14 Feb 2003]
	// Line returns after </td> tags would be a good start [as well as <tr> and </tr> tags]
	$s .= '<td>';
	$s .= ('<img src="./images/icons/updown.gif" width="10" height="15" border="0" usemap="#arrow' 
	       . $row['mod_id'] . '" alt="" />');
	if ($canEdit) {
		$s .= '<map id="arrow'.$row['mod_id'].'" name="arrow'.$row['mod_id'].'">'."\n";
		if ($row['mod_ui_order'] > 0) {
			$s .= '<area coords="0,0,10,7" href="' . $query_string . '&amp;cmd=moveup" alt="" />'."\n";
		}
		$s .= '<area coords="0,8,10,14" href="' . $query_string . '&amp;cmd=movedn" alt="" />'."\n";
		$s .= '</map>'."\n";
	}
	$s .= '</td>'."\n";

	$s .= '<td width="1%" nowrap="nowrap">' . $AppUI->_($row['mod_name']) . '</td>';
	$s .= '<td>';
	$s .= ('<img src="./images/obj/dot' . ($row['mod_active'] ? 'green' : 'yellowanim') 
	       . '.gif" width="12" height="12" alt="" />&nbsp;');
	// John changes Module Terminology to be more descriptive of current Module State... 
	// [14 Feb 2003]
	// Status term "deactivate" changed to "Active"
	// Status term "activate" changed to "Disabled"
	/*
	$s .= ('<a href="' . $query_string . '&cmd=toggle&">' 
	       . ($row['mod_active'] ? $AppUI->_('deactivate') : $AppUI->_('activate')) . '</a>');
	*/
	if ($canEdit) {
		$s .= '<a href="' . $query_string . '&amp;cmd=toggle">';
	}
	$s .= ($row['mod_active'] ? $AppUI->_('active') : $AppUI->_('disabled'));
	if ($canEdit) {
		$s .= '</a>';
	}
	if ($row['mod_type'] != 'core' && $canEdit) {
		$s .= (' | <a href="' . $query_string . '&amp;cmd=remove" onclick="javascript:return window.confirm(' 
		       . "'" . $AppUI->_('This will delete all data associated with the module!') 
		       . '\n\n' . $AppUI->_('Are you sure?') . '\n' . "'" . ');">' 
		       . $AppUI->_('remove') . '</a>');
	}

	// check for upgrades
	$ok = file_exists(DP_BASE_DIR . '/modules/' . $row['mod_directory'] . '/setup.php');
	if ($ok) {
		include_once(DP_BASE_DIR . '/modules/' . $row['mod_directory'] . '/setup.php');
	}
	if ($ok && $config[ 'mod_version' ] != $row['mod_version'] && $canEdit) {
		$s .= (' | <a href="' . $query_string . '&amp;cmd=upgrade" onclick="return window.confirm(' 
		       . "'" . $AppUI->_('Are you sure?') . "'" . ');" >' . $AppUI->_('upgrade') . '</a>');
	}

	// check for configuration
	if ($ok && isset($config['mod_config']) && $config['mod_config'] == true && $canEdit) {
		$s .= ' | <a href="' . $query_string . '&amp;cmd=configure">' . $AppUI->_('configure') . '</a>';
	}
	
	
	$s .= '</td>';
	$s .= '<td>' . $row['mod_type'] . '</td>';
	$s .= '<td>' . $row['mod_version'] . '</td>';
	$s .= '<td>' . $AppUI->_($row['mod_ui_name']) . '</td>';
	$s .= '<td>' . $row['mod_ui_icon'] . '</td>';
	
	$s .= '<td>';
	$s .= ('<img src="./images/obj/' . ($row['mod_ui_active'] ? 'dotgreen.gif' : 'dotredanim.gif') 
	       . '" width="12" height="12" alt="" />&nbsp;');
	/*
	$s .= (($row['mod_ui_active']) 
	       ? ('<span style="color:green">' . $AppUI->_('on')) 
	       : ('<span style="color:red">' . $AppUI->_('off')));
	*/
	// John changes Module Terminology to be more descriptive of current Module State... 
	// [14 Feb 2003]
	// Menu Status term "show" changed to "Visible"
	// Menu Status term "activate" changed to "Disabled"
	/*
	$s .= ('<a href="' . $query_string . '&cmd=toggleMenu">' 
	       . ($row['mod_ui_active'] ? $AppUI->_('hide') : $AppUI->_('show')) . '</a></td>');
	*/
	
	if ($canEdit) {
		$s .= '<a href="' . $query_string . '&amp;cmd=toggleMenu">';
	}
	$s .= (($row['mod_ui_active']) ? $AppUI->_('visible') : $AppUI->_('hidden'));
	if ($canEdit) {
		$s .= '</a>';
	}
	$s .= '</td>';

	$s .= '<td>' . $row['mod_ui_order'] . '</td>';

	echo "<tr>$s</tr>\n";
}

foreach ($modFiles as $v) {
	// clear the file system entry
	if ($v && ! in_array($v, $hidden_modules)) {
		$s = '';
		$s .= '<td></td>';
		$s .= '<td>' . $v . '</td>';
		$s .= '<td>';
		$s .= '<img src="./images/obj/dotgrey.gif" width="12" height="12" alt="" />&nbsp;';
		if ($canEdit) {
			$s .= '<a href="?m=' . $m . '&amp;a=domodsql&amp;cmd=install&amp;mod_directory=' . $v . '">';
		}
		$s .= $AppUI->_('install');
		if ($canEdit) {
			$s .= '</a>';
		}
		$s .= '</td>';
		echo "<tr>$s</tr>\n";
	}

}
?>
</table>

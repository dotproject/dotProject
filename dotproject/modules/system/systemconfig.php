<?php  // $Id$

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

// check permissions
if (!$canEdit) {
    $AppUI->redirect('m=public&a=access_denied');
}

$dPcfg = new CConfig();

// retrieve the system configuration data
$rs = $dPcfg->loadAll('config_group');

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('ConfigIdxTab', $_GET['tab']);
}
$tab = $AppUI->getState('ConfigIdxTab') !== NULL ? $AppUI->getState('ConfigIdxTab') : 0;
$active = intval(!$AppUI->getState('ConfigIdxTab'));

$titleBlock = new CTitleBlock('System Configuration', 'control-center.png', $m);
$titleBlock->addCrumb('?m=system', 'system admin');
$titleBlock->addCrumb('?m=system&a=addeditpref', 'default user preferences');
$titleBlock->show();

if (is_dir(DP_BASE_DIR.'/install')) {
	$AppUI->setMsg('You have not removed your install directory, this is a major security risk!', UI_MSG_ALERT);
	echo '<span class="error">' . $AppUI->getMsg() . '</span>'."\n";
}

echo $AppUI->_('syscfg_intro');
echo '<br />&nbsp;<br />';


// prepare the automated form fields based on db system configuration data
$output  = null;
$last_group = '';
foreach ($rs as $c) {

 	$tooltip = 'title="'.$AppUI->_($c['config_name'].'_tooltip').'"';
	// extraparse the checkboxes and the select lists
	$value = '';
	switch ($c['config_type']) {
		case 'select':
			// Build the select list.
			$entry = '<select class="text" name="dPcfg['.$c['config_name'].']">'."\n";
			// Find the detail relating to this entry.
			$children = $dPcfg->getChildren($c['config_id']);
			foreach ($children as $child) {
				$entry .= '<option value="'.$child['config_list_name'].'"';
				if ($child['config_list_name'] == $c['config_value'])
					$entry  .= ' selected="selected"';
				$entry .= '>' . $AppUI->_($child['config_list_name'] . '_item_title') . '</option>'."\n";
			}
			$entry .= '</select>';
			break;
		case 'checkbox':
			$extra = ($c['config_value'] == 'true') ? 'checked="checked"' : '';
			$value = 'true';
			// allow to fallthrough
		default:
			if (!($value)) {
				$value = $c['config_value'];
			}
			$entry = '<input class="button" type="'.$c['config_type'].'" name="dPcfg['.$c['config_name'].']" value="'.$value.'" '.$tooltip.' '. $extra.'/>';
			break;
	}

	if ($c['config_group'] != $last_group) {
		$output .= '<tr><td colspan="2"><b>' . $AppUI->_($c['config_group'] .'_group_title') . '</b></td></tr>'."\n";
		$last_group = $c['config_group'];
	}
	$output .= '<tr>
			<td class="item" width="20%">'.$AppUI->_($c['config_name'].'_title').'</td>
            		<td align="left">
' .				$entry . '
				<a href="#" onClick="javascript:window.open(\'?m=system&a=systemconfig_help&dialog=1&cn='.$c['config_name'].'\', \'contexthelp\', \'width=400, height=200, left=50, top=50, scrollbars=yes, resizable=yes\')" '.$tooltip.'>(?)</a>
				<input class="button" type="hidden"  name="dPcfgId['.$c['config_name'].']" value="'.$c['config_id'].'" />
			</td>
        </tr>
	';

	}
echo '<form name="cfgFrm" action="index.php?m=system&a=systemconfig" method="post">';
?>
<input type="hidden" name="dosql" value="do_systemconfig_aed" />
<table cellspacing="0" cellpadding="3" border="0" class="std" width="100%" align="center">
	<?php
	echo $output;
	?>
	<tr>
 		<td align="right" colspan="2"><input class="button" type="submit" name="do_save_cfg" value="<?php echo $AppUI->_('Save');?>" /></td>
	</tr>
</table></form>

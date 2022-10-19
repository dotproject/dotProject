<?php /* STYLE/CLASSIC $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}

$dialog = (int)dPgetParam($_GET, 'dialog', 0);

?>

<!DOCTYPE html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta name="Description" content="Classic dotProject Style" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
  <meta http-equiv="Content-Type" content="text/html;charset=<?php echo (isset($locale_char_set) ? $locale_char_set : 'UTF-8'); ?>" />
  <title><?php echo @dPgetConfig('page_title');?></title>
	<link rel="stylesheet" href="./style/<?php echo $uistyle; ?>/css/main.css" media="all" />
  <link rel="shortcut icon" href="./style/<?php echo $uistyle;?>/images/favicon.ico" type="image/ico" />
	<?php $AppUI->loadJS(); ?>
	<script language="javascript" >
	function doBtn(ev) {
		var e = new CommonEvent(ev);

		var oEl = e.target;
		var doit = e.type;

		while (! oEl.className || -1 == oEl.className.indexOf("Btn")) {
			oEl = oEl.parentNode;
			if (!oEl) {
				return;
			}
		}
		var basename = oEl.className.substr(0,6);
		if (doit == "mouseover" || doit == "mouseup") {
			oEl.className = basename + "On";
		} else if (doit == "mousedown") {
			oEl.className = basename + "Down";
		} else {
			oEl.className = basename + "Off";
		}
	}
	document.onmouseover = doBtn;
	document.onmouseout = doBtn;
	document.onmousedown = doBtn;
	document.onmouseup = doBtn;
	</script>

</head>
<body class="mainpage"<?php
if (!$dialog) {
	echo (' style="background: url(style/classic/images/bground.gif);"');
} ?>>
<table class="nav" width="100%" cellpadding="0" cellspacing="2">
<tr>
	<td nowrap width="33%"><?php
	  echo "<a href='" . $dPconfig['base_url'] . "'>" . $dPconfig['company_name'] . "</a>";
		?></td>
<?php if (!$dialog) { ?>
	<td nowrap width="34%"><?php
echo ($AppUI->_('Current user') . ": $AppUI->user_first_name $AppUI->user_last_name"); ?></td>
	<td nowrap width="33%" align="right">
	<table cellpadding="1" cellspacing="1" width="150">
	<tr>
		<td class="topBtnOff" nowrap style="background-color: #cccccc" align="center">
			<a href="./index.php?m=admin&amp;a=viewuser&amp;user_id=<?php echo $AppUI->user_id;?>"><?php
echo $AppUI->_('My Info');?></a>
		</td>
		<td class="topBtnOff" nowrap style="background-color: #cccccc" align="center">
			<a href="?logout=-1"><?php echo $AppUI->_('Logout');?></a>
		</td>
		<td class="topBtnOff" nowrap style="background-color: #cccccc" align="center"><?php
echo dPcontextHelp('Help');?>
		</td>
	</tr>
	</table>
	</td>
	<form name="frm_new" method="get" action="./index.php">
<?php

	$newItemPermCheck = array('companies' => 'Company',
							  'contacts' => 'Contact',
							  'calendar' => 'Event',
							  'files' => 'File',
							  'projects' => 'Project');

	$newItem = array(0=>'- New Item -');
	foreach ($newItemPermCheck as $mod_check => $mod_check_title) {
		if (getPermission($mod_check, 'add')) {
			$newItem[$mod_check] = $mod_check_title;
		}
	}

	echo arraySelect($newItem, 'm', 'style="font-size:10px" onChange="javascript:f=document.frm_new;mod=f.m.options[f.m.selectedIndex].value;if (mod) f.submit();"', '', true);
	echo '</td><input type="hidden" name="a" value="addedit" />';

//build URI string
	if (isset($company_id)) {
		echo '<input type="hidden" name="company_id" value="'.$company_id.'" />';
	}
	if (isset($task_id)) {
		echo '<input type="hidden" name="task_parent" value="'.$task_id.'" />';
	}
	if (isset($file_id)) {
		echo '<input type="hidden" name="file_id" value="'.$file_id.'" />';
	}
?>
	</form></td>
<?php } // END DIALOG BLOCK ?>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top">
<?php if (!$dialog) {
	// left side navigation menu
?>
		<table cellspacing=0 cellpadding=2 border=0 height="600">
		<tr>
			<td><img src="images/shim.gif" width="70" height="3" alt="" /></td>
			<td rowspan="100"><img src="images/shim.gif" width="10" height="100" alt="" /></td>
		</tr>
	<?php
		$nav = $AppUI->getMenuModules();
		$s = '';
		foreach ($nav as $module) {
			if (getPermission($module['mod_directory'], 'access')) {
				$s .= ('<tr><td align="center" valign="middle" class="nav">'
					   . '<a href="?m=' . $module['mod_directory'] . '">'
					   . '<table cellspacing=0 cellpadding=0 border=0><tr><td class="clsBtnOff">'
					   . '<img src="'
					   . dPfindImage($module['mod_ui_icon'], $module['mod_directory'])
					   . '" alt="" border="0" width="30" height="30" /></td></tr></table>'
					   . $AppUI->_($module['mod_ui_name']) ."</a></td></tr>\n");
			}
		}
		echo $s;
		?>
		<tr height="100%">
			<td>&nbsp;<img src="images/shim.gif" width="7" height="10" alt="" /></td>
		</tr>
		</table>
<?php } // END DIALOG ?>
	</td>
<td valign="top" align="left" width="100%">
<?php
	echo $AppUI->getMsg();
?>

<?php /* STYLE/DEFAULT $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}
$dialog = (int)dPgetParam($_GET, 'dialog', 0);
if ($dialog)
	$page_title = '';
else
	$page_title = ($dPconfig['page_title'] == 'dotProject') ? $dPconfig['page_title'] . '&nbsp;' . $AppUI->getVersion() : $dPconfig['page_title'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta name="Description" content="dotProject Default Style" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion() ?? 'unknown'; ?>" />
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $locale_char_set ?? 'UTF-8'; ?>" />
	<title><?php echo @dPgetConfig('page_title');?></title>
	<link rel="stylesheet" href="./style/<?php echo $uistyle;?>/css/main.css" media="all" />
	<link rel="shortcut icon" href="./style/<?php echo $uistyle; ?>/images/favicon.ico" type="image/ico" />
	<?php @$AppUI->loadJS(); // reverted the move to the end, breaks display (gwyneth 20210425) ?>
</head>

<body onload="this.focus();">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td><table width="100%" cellpadding="3" cellspacing="0" border="0"><tr>
	<th style="background: url(style/<?php echo $uistyle;?>/images/titlegrad.jpg);" class="banner" align="left"><strong><?php
		echo "<a style='color: white' href='" . $dPconfig['base_url'] . "'>" . $page_title . "</a>";
	?></strong>
	<?php if (getPermission('smartsearch', 'access')): ?>
	<form name="frmHeaderSearch" action="?m=smartsearch"  method="post">
		<input class="text" type="text" id="keyword1" name="keyword1" value="<?php echo dPgetCleanParam($_POST, 'keyword1', ''); ?>" accesskey="k" />
		<input class="button" type="submit" value="<?php echo $AppUI->_('Search') ?? 'Search'; ?>" />
	</form>
	<?php endif; ?>
	</th>
	<th align="right" width='50'><a href='http://www.dotproject.net/' <?php if (!empty($dialog)) echo "target='_blank'"; ?>><img src="style/<?php echo $uistyle ?? 'default';?>/images/dp_icon.gif" border="0" alt="http://dotproject.net/" /></a></th>
	</tr></table></td>
</tr>
<?php if (empty($dialog)) {
	// top navigation menu
	$nav = $AppUI->getMenuModules() ?? array();
?>
<tr>
	<td class="nav" align="left">
	<table width="100%" cellpadding="3" cellspacing="0">
	<tr>
		<td>
		<?php
		$links = array();
		foreach ($nav as $module) {
			if (getPermission($module['mod_directory'], 'access')) {
				$links[] = '<a href="?m='.$module['mod_directory'].'">'.$AppUI->_($module['mod_ui_name']).'</a>';
			}
		}
		echo implode(' | ', $links);
		echo "\n";
		?>
		</td>
		<td nowrap="nowrap" align="right">
		<form name="frm_new" method="get" action="./index.php">
		<table cellpadding="0" cellspacing="0">
		<tr><td>
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

	echo "        <input type=\"hidden\" name=\"a\" value=\"addedit\" />\n";

//build URI string
	if (!empty($company_id)) {  // !empty(x) means isset(x) and x is _not_ null/''/0/false/[]
		echo '<input type="hidden" name="company_id" value="'.$company_id.'" />';
	}
	if (!empty($task_id)) {
		echo '<input type="hidden" name="task_parent" value="'.$task_id.'" />';
	}
	if (!empty($file_id)) {
		echo '<input type="hidden" name="file_id" value="'.$file_id.'" />';
	}
?>
		</td></tr>
		</table>
		</form>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table cellspacing="0" cellpadding="3" border="0" width="100%">
		<tr>
			<td width="100%"><?php echo $AppUI->_('Welcome').' '.$AppUI->user_first_name.' '.$AppUI->user_last_name; ?></td>
			<td nowrap="nowrap">
				<?php echo dPcontextHelp('Help');?> |
				<a href="./index.php?m=admin&amp;a=viewuser&amp;user_id=<?php echo $AppUI->user_id;?>"><?php echo $AppUI->_('My Info');?></a> |
<?php
	if (getPermission('calendar', 'access')) {
		$now = new CDate();
?>         <b><a href="./index.php?m=tasks&amp;a=todo"><?php echo $AppUI->_('Todo');?></a></b> |
				<a href="./index.php?m=calendar&amp;a=day_view&amp;date=<?php echo $now->format(FMT_TIMESTAMP_DATE);?>"><?php echo $AppUI->_('Today');?></a> |
<?php } ?>
				<a href="?logout=-1"><?php echo $AppUI->_('Logout');?></a>
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php } // END showMenu ?>
</table>

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
<td valign="top" align="left" width="98%">
<?php
	echo $AppUI->getMsg() ?? '';
?>

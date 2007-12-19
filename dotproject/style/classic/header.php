<?php /* STYLE/CLASSIC $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}


$dialog = dPgetParam( $_GET, 'dialog', 0 );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"

	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<meta name="Description" content="Classic dotProject Style" />

	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />

        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />

      	<title><?php echo @dPgetConfig( 'page_title' );?></title>

	<link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle;?>/main.css" media="all" />

	<style type="text/css" media="all">@import "./style/<?php echo $uistyle;?>/main.css";</style>



	<?php $AppUI->loadJS(); ?>

	<script language="JavaScript">

	function doBtn(ev) {



		var e = new CommonEvent(ev);



		var oEl = e.target;

		var doit = e.type;

	

		while (! oEl.className || -1 == oEl.className.indexOf( "Btn" )) {

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
<?php if (!$dialog): ?>
<body class="mainpage" background="style/classic/images/bground.gif">
<?php else: ?>
<body class="mainpage">
<?php endif; ?>

<table class="nav" width="100%" cellpadding="0" cellspacing="2">

<tr>

	<td nowrap width="33%"><?php 

	  echo "<a href='{$dPconfig['base_url']}'>{$dPconfig['company_name']}</a>";

		?></td>

<?php if (!$dialog) { ?>

	<td nowrap width="34%"><?php echo $AppUI->_('Current user').": $AppUI->user_first_name $AppUI->user_last_name"; ?></td>

	<td nowrap width="33%" align="right">

	<table cellpadding="1" cellspacing="1" width="150">

	<tr>

		<td class="topBtnOff" nowrap bgcolor="#cccccc" align="center"><a href="./index.php?m=admin&a=viewuser&user_id=<?php echo $AppUI->user_id;?>"><?php echo $AppUI->_('My Info');?></a></td>

		<td class="topBtnOff" nowrap bgcolor="#cccccc" align="center"><a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a></td>

		<td class="topBtnOff" nowrap bgcolor="#cccccc" align="center"><?php echo dPcontextHelp( 'Help' );?></td>

	</tr>

	</table>

	</td>

	<form name="frm_new" method=GET action="./index.php">

<?php

	echo '<td>';

	$newItem = array( 0=>'- New Item -' );

	if ($perms->checkModule( 'companies', 'add' )) $newItem["companies"] = "Company";
	if ($perms->checkModule( 'contacts', 'add' )) $newItem["contacts"] = "Contact";
	if ($perms->checkModule( 'calendar', 'add' )) $newItem["calendar"] = "Event";
	if ($perms->checkModule( 'files', 'add' )) $newItem["files"] = "File";
	if ($perms->checkModule( 'projects', 'add' )) $newItem["projects"] = "Project";



	echo arraySelect( $newItem, 'm', 'style="font-size:10px" onChange="f=document.frm_new;mod=f.m.options[f.m.selectedIndex].value;if(mod) f.submit();"', '', true);



	echo '</td><input type="hidden" name="a" value="addedit" />';



//build URI string

	if (isset( $company_id )) {

		echo '<input type="hidden" name="company_id" value="'.$company_id.'" />';

	}

	if (isset( $task_id )) {

		echo '<input type="hidden" name="task_parent" value="'.$task_id.'" />';

	}

	if (isset( $file_id )) {

		echo '<input type="hidden" name="file_id" value="'.$file_id.'" />';

	}

?>

	</form>

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

			<td><img src="images/shim.gif" width="70" height="3"></td>

			<td rowspan="100"><img src="images/shim.gif" width="10" height="100"></td>

		</tr>

	<?php

		$nav = $AppUI->getMenuModules();

		$s = '';

		foreach ($nav as $module) {

			if (getPermission($module['mod_directory'], 'view')) {

				$s .= '<tr><td align="center" valign="middle" class="nav">'

					.'<table cellspacing=0 cellpadding=0 border=0><tr><td class="clsBtnOff">'

					.'<a href="?m='.$module['mod_directory'].'">'

					.'<img src="'.dPfindImage( $module['mod_ui_icon'], $module['mod_directory'] ).'" alt="" border="0" width="30" height="30"></a></td></tr></table>'

					.$AppUI->_($module['mod_ui_name'])

					."</td></tr>\n";

			}

		}

		echo $s;

		?>

		<tr height="100%">

			<td>&nbsp;<img src="images/shim.gif" width="7" height="10"></td>

		</tr>

		</table>	

<?php } // END DIALOG ?>

	</td>

<td valign="top" align="left" width="100%">

<?php 

	echo $AppUI->getMsg();

?>


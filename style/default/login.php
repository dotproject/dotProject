<?php /* STYLE/DEFAULT $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $dPconfig['page_title'];?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8';?>" />
       	<title><?php echo $dPconfig['company_name'];?> :: dotProject Login</title>
	<meta http-equiv="Pragma" content="no-cache" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
	<link rel="stylesheet" href="./style/<?php echo $uistyle;?>/main.css" media="all" />
	<style media="all">@import "./style/<?php echo $uistyle;?>/main.css";</style>
	<link rel="shortcut icon" href="./style/<?php echo $uistyle;?>/images/favicon.ico" type="image/ico" />
</head>

<body style="background-color: #f0f0f0" onload="document.loginform.username.focus();">
<br /><br /><br /><br />
<?php //please leave action argument empty ?>
<!--form action="./index.php" method="post" name="loginform"-->
<form method="post" action="<?php echo $loginFromPage; ?>" name="loginform">
<table align="center" border="0" width="250" cellpadding="6" cellspacing="0" class="std">
<input type="hidden" name="login" value="<?php echo time();?>" />
<input type="hidden" name="lostpass" value="0" />
<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
<tr>
	<th colspan="2"><em><?php echo dPgetConfig('company_name');?></em></th>
</tr>
<tr>
	<td align="right" nowrap><?php echo $AppUI->_('Username');?>:</td>
	<td align="left" nowrap><input type="text" size="25" maxlength="255" name="username" class="text" /></td>
</tr>
<tr>
	<td align="right" nowrap><?php echo $AppUI->_('Password');?>:</td>
	<td align="left" nowrap><input type="password" size="25" maxlength="32" name="password" class="text" /></td>
</tr>
<tr>
	<td align="left" nowrap><a href="http://www.dotproject.net/"><img src="./style/default/images/dp_icon.gif" border="0" alt="dotProject logo" /></a></td>
	<td align="right" valign="bottom" nowrap><input type="submit" name="login" value="<?php echo $AppUI->_('login');?>" class="button" /></td>
</tr>
<tr>
	<td colspan="2"><a href="#" onclick="f=document.loginform;f.lostpass.value=1;f.submit();"><?php echo $AppUI->_('forgotPassword');?></a></td>
</tr>
</table>
<?php if (@$AppUI->getVersion()) { ?>
<div align="center">
	<span style="font-size:7pt">Version <?php echo @$AppUI->getVersion();?></span>
</div>
<?php } ?>
</form>
<div align="center">
<?php
	echo '<span class="error">'.$AppUI->getMsg().'</span>';

	$msg = '';
	$msg .=  phpversion() < '4.1' ? '<br /><span class="warning">WARNING: dotproject is NOT SUPPORT for this PHP Version ('.phpversion().')</span>' : '';
	echo $msg;
?>
</div>
<center><?php echo "* ".$AppUI->_("You must have cookies enabled in your browser"); ?></center>
</body>
</html>

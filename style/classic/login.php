<?php /*  STYLE/CLASSIC $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8';?>" />
	<meta http-equiv="Pragma" content="no-cache">
	<link href="./style/<?php echo $uistyle ?? 'default';?>/css/main.css" rel="stylesheet"/>
  <link rel="shortcut icon" href="./style/<?php echo $uistyle ?? 'default';?>/images/favicon.ico" type="image/ico" />
</head>

<body class="loginform" onload="document.loginform.username.focus();">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table align="center" border="0" width="250" cellpadding="4" cellspacing="0" class="bordertable">
<?php //please leave action argument empty ?>
<!--form action="./index.php" method="post" name="loginform"-->
<form method="post" action="<?php echo $loginFromPage; ?>" name="loginform">
<input type="hidden" name="login" value="<?php echo time();?>" />
<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
<tr>
	<td colspan="2" class="companyName">
		<strong><?php echo $dPconfig['company_name'];?></strong>
	</td>
</tr>
<tr>
	<td class="align-right" nowrap width="100">
		<?php echo $AppUI->_('Username');?>:
	</td>
	<td class="align-left" nowrap>
		<input type="text" size="25" maxlength="255" name="username" class="text" />
	</td>
</tr>
<tr>
	<td class="align-right" nowrap>
		<?php echo $AppUI->_('Password');?>:
	</td>
	<td class="align-left" nowrap>
		<input type="password" size="25" maxlength="32" name="password" class="text" />
	</td>
</tr>
<tr>
	<td class="aligncenter" nowrap colspan="2">
		<input type="submit" name="login" value="<?php echo $AppUI->_('login');?>" class="button" /></p>
	</td>
</tr>
</table> <!-- /bordertable -->

<p class="aligncenter"><?php
	echo '<span class="error">'.$AppUI->getMsg().'</span>';
	//echo ini_get('register_globals') ? '' : '<br /><span class="warning">WARNING: dotproject is not fully supported with register_globals=off</span>';
?></p>

<table align="center" border="0" width="250" cellpadding="4" cellspacing="0">
<tr>
	<td>
		<br />
		<ul type="square">
			<li>
				<A href="mailto:<?php echo 'admin@' . $dPconfig['site_domain'];?>"><?php echo $AppUI->_('forgotPassword');?></a>
			</li>
		</ul>
	</td>
</tr>
<tr>
	<td align=center>
		<img src="./images/icons/dp.gif" width="42" height="42" border=0 alt="dotproject" />
		<p>dotproject</p>
		<p><?php echo $AppUI->_('openSource');?></p>
	</td>
</tr>
</form>
</table>
<center><?php echo $AppUI->_('* You must have cookies enabled in your browser');?></center>
</body>
</html>

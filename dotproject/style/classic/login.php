<?php /*  STYLE/CLASSIC $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
	<meta http-equiv="Pragma" content="no-cache">
	<link href="./style/<?php echo $uistyle;?>/main.css" rel="STYLESHEET" type="text/css" />
</head>

<body style="background-color: white" onload="document.loginform.username.focus();">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table align="center" border="0" width="250" cellpadding="4" cellspacing="0" style="background-color: #cccccc" class="bordertable">
<?php //please leave action argument empty ?>
<!--form action="./index.php" method="post" name="loginform"-->
<form method="post" action="<?php echo $loginFromPage; ?>" name="loginform">
<input type="hidden" name="login" value="<?php echo time();?>" />
<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
<tr>
	<td colspan="2" class="headerfontWhite" style="background-color: gray">
		<strong><?php echo $dPconfig['company_name'];?></strong>
	</td>
</tr>
<tr>
	<td style="background-color: #eeeeee" align="right" nowrap width="100">
		<?php echo $AppUI->_('Username');?>:
	</td>
	<td style="background-color: #eeeeee" align="left" class="menufontlight" nowrap>
		<input type="text" size="25" maxlength="255" name="username" class="text" />
	</td>
</tr>
<tr>
	<td style="background-color: #eeeeee" align="right"  nowrap>
		<?php echo $AppUI->_('Password');?>:
	</td>
	<td style="background-color: #eeeeee" align="left" class="menufontlight" nowrap>
		<input type="password" size="25" maxlength="32" name="password" class="text" />
	</td>
</tr>
<tr>
	<td style="background-color: #eeeeee" align="center" class="menufontlight" nowrap colspan="2">
		<input type="submit" name="login" value="<?php echo $AppUI->_('login');?>" class="button" /></p>
	</td>
</tr>
</table>

<p align="center"><?php 
	echo '<span class="error">'.$AppUI->getMsg().'</span>';
	//echo ini_get( 'register_globals') ? '' : '<br /><span class="warning">WARNING: dotproject is not fully supported with register_globals=off</span>';
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

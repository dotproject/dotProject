<?php /* PUBLIC $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

if (! ($user_id = dPgetParam($_REQUEST, 'user_id', 0)) )
        $user_id = @$AppUI->user_id;

// check for a non-zero user id
if ($user_id) {
	$old_pwd = db_escape( trim( dPgetParam( $_POST, 'old_pwd', null ) ) );
	$new_pwd1 = db_escape( trim( dPgetParam( $_POST, 'new_pwd1', null ) ) );
	$new_pwd2 = db_escape( trim( dPgetParam( $_POST, 'new_pwd2', null ) ) );

	// has the change form been posted
	if ($new_pwd1 && $new_pwd2 && $new_pwd1 == $new_pwd2 ) {
		// check that the old password matches
								$old_md5 = md5($old_pwd);
                $sql = "SELECT user_id FROM users WHERE user_password = '$old_md5' AND user_id=$user_id";
                if ($AppUI->user_type == 1 || db_loadResult( $sql ) == $user_id) {
			require_once( $AppUI->getModuleClass( 'admin' ) );
			$user = new CUser();
			$user->user_id = $user_id;
			$user->user_password = $new_pwd1;

			if (($msg = $user->store())) {
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			} else {
				echo $AppUI->_('chgpwUpdated');
			}
		} else {
			echo $AppUI->_('chgpwWrongPW');
		}
	} else {
?>
<script language="javascript">
function submitIt() {
	var f = document.frmEdit;
	var msg = '';

	if (f.new_pwd1.value.length < <?php echo dPgetConfig('password_min_len'); ?>) {
        	msg += "<?php echo $AppUI->_('chgpwValidNew', UI_OUTPUT_JS);?>" + <?php echo dPgetConfig('password_min_len'); ?>;
			f.new_pwd1.focus();
	}
	if (f.new_pwd1.value != f.new_pwd2.value) {
		msg += "\n<?php echo $AppUI->_('chgpwNoMatch', UI_OUTPUT_JS);?>";
		f.new_pwd2.focus();
	}
	if (msg.length < 1) {
		f.submit();
	} else {
		alert(msg);
	}
}
</script>
<h1><?php echo $AppUI->_('Change User Password');?></h1>
<table width="100%" cellspacing="0" cellpadding="4" border="0" class="std">
<form name="frmEdit" method="post" onsubmit="return false">
<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
<?php if ($AppUI->user_type != 1)
{
?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Current Password');?></td>
	<td><input type="password" name="old_pwd" class="text"></td>
</tr>
<?php } ?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('New Password');?></td>
	<td><input type="password" name="new_pwd1" class="text"></td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Repeat New Password');?></td>
	<td><input type="password" name="new_pwd2" class="text"></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="right" nowrap="nowrap"><input type="button" value="<?php echo $AppUI->_('submit');?>" onclick="submitIt()" class="button"></td>
</tr>
<form>
</table>
<?php
	}
} else {
	echo $AppUI->_('chgpwLogin');
}
?>

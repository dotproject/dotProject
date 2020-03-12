<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once $AppUI->getSystemClass('libmail');
$AppUI->savePlace();

$canEdit = getPermission($m, 'edit');
$canRead = getPermission($m, 'view');
if (!$canEdit) {
	$AppUI->redirect('m=public&a=access_denied');
}

$titleBlock = new CTitleBlock('Send Test EMail', '', 'admin', '');
$titleBlock->addCrumb('?m=system', 'system admin');
$titleBlock->show();


$recipient = dPgetCleanParam($_POST, 'recipient');
$test = dPgetCleanParam($_POST,'test');

?>
<form method="post" action="">
<table border="0" cellpadding="2" cellspacing="1" width="600" class="std">
	<tr>
		<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Recipient Email'); ?>:</td>
		<td><input type="text" name="recipient" value="<?php echo $recipient; ?>" size="50" /></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" name="test" value="<?php 
echo $AppUI->_('Test Connection and Send'); ?>" /></td>
	</tr>
</table>
</form>
<pre>
<?php
if (isset($test)) {
  $mail = new Mail();
  $mail->RecordTransaction();
  $mail->To($recipient);
  $mail->Subject('Test Email from dotProject');
  $mail->Body('This is a test email');
  $mail->Send();
  foreach ($mail->GetTransactionLog() as $record) {
    print trim($record) . "\n";
  }
}
?>
</pre>

<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//addfile sql
$file_id = intval(dPgetParam($_POST, 'file_id', 0));
$coReason = dPgetParam($_POST, 'file_co_reason', '');

$co_cancel = intval(dPgetParam($_POST, 'co_cancel', 0));

$not = dPgetParam($_POST, 'notify', '0');
$notcont = dPgetParam($_POST, 'notify_contacts', '0');

$obj = new CFile();
$obj->load($file_id);
$obj->checkout($AppUI->user_id, $file_id, $coReason);

//Notification
$obj->_message = (($co_cancel) ? 'reverted' : 'checked out');
if ($not) {
	$obj->notify();
}
if ($notcont) {
	$obj->notifyContacts();
}

//Checkout Cancellation
if ($co_cancel) {
	$obj->checkout('', $file_id, '');
	$obj->load($file_id);
	$AppUI->setMsg('File checkout reverted', UI_MSG_OK);
} else {
	$AppUI->setMsg('File checkout completed', UI_MSG_OK);
}

$params = 'file_id=' . $file_id;
$session_id = SID;

session_write_close();
// are the params empty
// Fix to handle cookieless sessions
if ($session_id != "") {
	$params .= "&" . $session_id;
}

if ($co_cancel != 1) {
//header("Refresh: 0; URL=fileviewer.php?$params");
	echo ('<script type="text/javascript">fileloader = window.open("fileviewer.php?' . $params 
	      . '", "mywindow","location=1,status=1,scrollbars=0,width=20,height=20");' 
	      . 'fileloader.moveTo(0,0);</script>');
}

?>

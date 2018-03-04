<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//addfile sql
$file_id = intval(dPgetParam($_POST, 'file_id', 0));
$coReason = dPgetCleanParam($_POST, 'file_co_reason', '');

$co_cancel = intval(dPgetParam($_POST, 'co_cancel', 0));

$not = (bool)dPgetParam($_POST, 'notify', '0');
$notcont = (bool)dPgetParam($_POST, 'notify_contacts', '0');

$obj = new CFile();
$obj->load($file_id);

//set checkout messages
if ($msg = $obj->checkout($AppUI->user_id, $coReason)) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
	$AppUI->setMsg('File checkout completed', UI_MSG_OK);
	//Notification
	$obj->_message = (($co_cancel) ? 'reverted' : 'checked out');
	if ($not) {
		$obj->notify();
	}
	if ($notcont) {
		$obj->notifyContacts();
	}
}

//Checkout Cancellation
if ($co_cancel) {
	if ($msg = $obj->checkout('', '')) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$AppUI->setMsg('File checkout reverted', UI_MSG_OK);
	}
}

if (($msg = $obj->store())) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
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
	echo ('<script >fileloader = window.open("fileviewer.php?' . $params 
	      . '", "mywindow","location=1,status=1,scrollbars=0,width=20,height=20");' 
	      . 'fileloader.moveTo(0,0);</script>');
}

?>

<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

$obj = new CEvent();
$msg = '';

$del = dPgetParam( $_POST, 'del', 0 );

// bind the POST parameter to the object record
if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
// configure the date and times to insert into the db table
if ($obj->event_start_date) {
	$start_date = new CDate( $obj->event_start_date.$_POST['start_time'] );
	$obj->event_start_date = $start_date->format( FMT_DATETIME_MYSQL );
}
if ($obj->event_end_date) {
	$end_date = new CDate( $obj->event_end_date.$_POST['end_time'] );
	$obj->event_end_date = $end_date->format( FMT_DATETIME_MYSQL );
}

if (!$del && $start_date->compare ($start_date, $end_date) >= 0)
{
	$AppUI->setMsg( "Start-Date >= End-Date, please correct", UI_MSG_ERROR );
	$AppUI->redirect();
	exit;
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Event' );
$do_redirect = true;
require_once $AppUI->getSystemClass("CustomFields");

if ($del) {
	if (!$obj->canDelete( $msg )) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
	}
	$AppUI->redirect( 'm=calendar' );
} else {
	$isNotNew = @$_POST['event_id'];
	if (!$isNotNew) {
		$obj->event_owner = $AppUI->user_id;
	}
	// Check for existence of clashes.
	if ($_POST['event_assigned'] > '' && ($clash = $obj->checkClash($_POST['event_assigned']))) {
	  $last_a = $a;
	  $GLOBALS['a'] = "clash";
	  $do_redirect = false;
	} else {
	  if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	  } else {
		$custom_fields = New CustomFields( 'calendar', 'addedit', $obj->event_id, "edit" );
		$custom_fields->bind( $_POST );
		$sql = $custom_fields->store( $obj->event_id ); // Store Custom Fields

		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
		if (isset($_POST['event_assigned']))
		      $obj->updateAssigned(explode(",",$_POST['event_assigned']));
		if (isset($_POST['mail_invited'])) {
		      $obj->notify(@$_POST['event_assigned'], $isNotNew);
		}
	  }
	}
}
if ($do_redirect)
  $AppUI->redirect();
?>

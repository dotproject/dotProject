<?php

if ( isset($_REQUEST['clash_action'])) {
  $do_include = false;
  switch ($_REQUEST['clash_action']) {
    case 'suggest':  clash_suggest(); break;
    case 'process':  clash_process(); break;
    case 'cancel' :  clash_cancel(); break;
    case 'mail'   :  clash_mail(); break;
    case 'accept' :  clash_accept(); break;
    default       :  $AppUI->setMsg('Invalid action, event cancelled', UI_MSG_ALERT); break;
  }
  // Why do it here?  Because it is in the global scope and requires
  // less hacking of the included file.
  if ($do_include) {
  	include $do_include;
  }
} else {

?>
<script language="javascript">
  function set_clash_action(action) {
    var f = document.clash_form;
    f.clash_action.value = action;
    f.submit();
  }

</script>
<?php

  $titleBlock =& new CTitleBlock( ($obj->event_id ? "Edit Event" : "Add Event"), "myevo-appointments.png", $m, "$m.$a");
  $titleBlock->show();

  $_SESSION['add_event_post'] = get_object_vars($obj);
  $_SESSION['add_event_clash'] = implode(',', array_keys($clash));
  $_SESSION['add_event_caller'] = $last_a;
  $_SESSION['add_event_attendees'] = $_POST['event_assigned'];
  $_SESSION['add_event_mail'] = isset($_POST['mail_invited']) ? $_POST['mail_invited'] : 'off';

  echo "<table width='100%' class='std'><tr><td><b>".$AppUI->_('clashEvent')."</b></tr></tr>";
  foreach($clash as $user) {
    echo "<tr><td>$user</td></tr>\n";
  }
  echo "</table>\n";
  $calurl = $dPconfig['base_url'] . "/index.php?m=calendar&a=clash&event_id=" . $obj->event_id;
  echo "<a href='#' onclick=\"set_clash_action('suggest');\">" . $AppUI->_('Suggest Alternative') . "</a> : ";
  echo "<a href='#' onclick=\"set_clash_action('cancel');\">" . $AppUI->_('Cancel') . "</a> : ";
  echo "<a href='#' onclick=\"set_clash_action('mail');\">" . $AppUI->_('Mail Request') . "</a> : ";
  echo "<a href='#' onclick=\"set_clash_action('accept');\">" . $AppUI->_('Book Event Despite Conflict') . "</a>\n";
  echo "<form name='clash_form' method='POST' action='$calurl'>";
  echo "<input type='hidden' name='clash_action' value='cancel'>";
  echo "</form>\n";

}

// Clash functions.
/*
 * Cancel the event, simply clear the event details and return to the previous
 * page.
*/
function clash_cancel()
{
  global $AppUI, $a;
  $a = $_SESSION['add_event_caller'];
  clear_clash();
  $AppUI->setMsg('Event Cancelled', UI_MSG_ALERT);
  $AppUI->redirect();
}

/* 
 * display a form
 */
function clash_suggest()
{
  global $AppUI, $dPconfig;
  $obj =& new CEvent;
  $obj->bind($_SESSION['add_event_post']);

  $start_date =& new CDate($obj->event_start_date);
  $end_date =& new CDate($obj->event_end_date);
  $df = $AppUI->getPref('SHDATEFORMAT');
  $start_secs = $start_date->getTime();
  $end_secs = $end_date->getTime();
  $duration = (int) (( $end_secs - $start_secs ) / 60);

  $titleBlock =& new CTitleBlock( "Suggest Alternative Event Time", "myevo-appointments.png", $m, "$m.$a");
  $titleBlock->show();
  $calurl = $dPconfig['base_url'] . "/index.php?m=calendar&a=clash&event_id=" . $obj->event_id;
  $times = array();
  $t = new CDate();
  $t->setTime( 0,0,0 );
  if (!defined('LOCALE_TIME_FORMAT'))
    define('LOCALE_TIME_FORMAT', '%I:%M %p');
  for ($m=0; $m < 60; $m++) {
	  $times[$t->format( "%H%M%S" )] = $t->format( LOCALE_TIME_FORMAT );
	  $t->addSeconds( 1800 );
  }

?>
<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.event_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=250, height=220, scrollbars=no' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.event_' + calendarField );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function set_clash_action(action) {
	document.editFrm.clash_action.value = action;
	document.editFrm.submit();
}

</script>
<form name='editFrm' method='POST' action='<?php echo "$calurl&clash_action=process"; ?>'>
<table width='100%' class='std'>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Earliest Date'); ?>:</td>
  <td width='50%' align='left' nowrap="nowrap">
    <input type="hidden" name="event_start_date" value="<?php echo $start_date->format(FMT_TIMESTAMP_DATE); ?>">
    <input type="text" name="start_date" value="<?php echo $start_date->format($df);?>" class="text" disabled="disabled">
      <a href="#" onClick="popCalendar('start_date')">
	<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
      </a>
  </td>
</tr>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Latest Date'); ?>:</td>
  <td width='50%' align='left' nowrap="nowrap">
    <input type="hidden" name="event_end_date" value="<?php echo $end_date->format(FMT_TIMESTAMP_DATE); ?>">
    <input type="text" name="end_date" value="<?php echo $end_date->format($df);?>" class="text" disabled="disabled">
      <a href="#" onClick="popCalendar('end_date')">
	<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
      </a>
  </td>
</tr>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Earliest Start Time'); ?>:</td>
  <td width='50%' align='left'>
    <?php echo arraySelect( $times, 'start_time', 'size="1" class="text"', $start_date->format("%H%M%S") ); ?>
  </td>
</tr>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Latest Finish Time'); ?>:</td>
  <td width='50%' align='left'>
    <?php echo arraySelect( $times, 'end_time', 'size="1" class="text"', $end_date->format("%H%M%S") ); ?>
  </td>
</tr>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Duration'); ?>:</td>
  <td width='50%' align='left'>
    <input type="text" class="text" size=5 name="duration" value="<?php echo $duration; ?>">
    <?php echo $AppUI->_('minutes'); ?>
  </td>
</tr>
<tr>
  <td><input type="button" value="<?php echo $AppUI->_('cancel'); ?>" class="button" onClick="set_clash_action('cancel');" /></td>
  <td align="right"><input type="button" value="<?php echo $AppUI->_('submit'); ?>" class="button" onClick="set_clash_action('process')" /></td>
</tr>
</table>
<input type='hidden' name='clash_action' value='cancel'>
</form>
<?php
}

/*
 * Build an SQL to determine an appropriate time slot that will meet
 * The requirements for all participants, including the requestor.
 */
function clash_process()
{
  global $AppUI, $do_include, $baseDir;

  $obj =& new CEvent;
  $obj->bind($_SESSION['add_event_post']);
  $attendees = $_SESSION['add_event_attendees'];
  $users = array();
  if (isset($attendees) && $attendees) {
    $users = explode(',', $attendees);
  }
  array_push($users, $obj->event_owner);
  // First remove any duplicates
  $users = array_unique($users);
  // Now remove any null entries, so implode doesn't create a dud SQL
  // Foreach is safer as it works on a copy of the array.
  foreach ($users as $key => $user) {
    if (! $user)
    	unset($users[$key]);
  }

  $start_date =& new CDate($_POST['event_start_date'] . "000000");
  $end_date =& new CDate($_POST['event_end_date']  . "235959");

  // First find any events in the range requested.
  $event_list = $obj->getEventsInWindow($start_date->format(FMT_DATETIME_MYSQL),
  	$end_date->format(FMT_DATETIME_MYSQL),(int)( $_POST['start_time'] / 100),
	(int)($_POST['end_time'] / 100), $users);
  $event_start_date =& new CDate($_POST['event_start_date'] . $_POST['start_time']);
  $event_end_date =& new CDate($_POST['event_end_date'] . $_POST['end_time']);

  if (! $event_list || !count($event_list)) {
    // First available date/time is OK, seed addEdit with the details.
    $obj->event_start_date = $event_start_date->format(FMT_DATETIME_MYSQL);
    $obj->event_end_date = $event_end_date->format(FMT_DATETIME_MYSQL);
    $_SESSION['add_event_post'] = get_object_vars($obj);
    $AppUI->setMsg('No clashes in suggested timespan', UI_MSG_OK);
    $_SESSION['event_is_clash'] = true;
    $_GET['event_id'] = $obj->event_id;
    $do_include = "$baseDir/modules/calendar/addedit.php";
    return;
  }

  // Now we grab the events, in date order, and compare against the
  // required start and end times.
  // Working in 30 minute increments from the start time, and remembering
  // the end time stipulation, find the first hole in the times.
  // Determine the duration in hours/minutes.
  $start_hour = (int)($_POST['start_time'] / 10000);
  $start_minutes = (int)(($_POST['start_time'] % 10000) / 100);
  $start_time = $start_hour * 60 + $start_minutes;
  $end_hour = (int)($_POST['end_time'] / 10000);
  $end_minutes = (int)(($_POST['end_time'] % 10000) / 100);
  $end_time = ($end_hour * 60 + $end_minutes) - $_POST['duration'];

  // First, build a set of "slots" that give us the duration
  // and start/end times we need
  $first_day = $start_date->format('%E');
  $end_day = $end_date->format('%E');
  $days_between = ($end_day + 1) - $first_day;
  $oneday =& new Date_Span(array(1,0,0,0));

  $slots = array();
  $slot_count = 0;
  $first_date = new CDate($start_date);
  for ($i = 0; $i < $days_between; $i++) {
    if ($first_date->isWorkingDay()) {
      $slots[$i] = array();
      for ($j = $start_time; $j <= $end_time; $j += 30) {
	  $slot_count++;
	  $slots[$i][] = array(
	    'date' => $first_date->format('%Y-%m-%d'),
	    'start_time' => $j,
	    'end_time' => $j + $_POST['duration'],
	    'committed' => false
	  );
      }
    } 
    $first_date->addSpan($oneday);
  }
  
  // Now process the events list
  foreach ($event_list as $event) {
    $sdate = new CDate($event['event_start_date']);
    $edate = new CDate($event['event_end_date']);
    $sday = $sdate->format('%E');
    $day_offset = $sday - $first_day;

    // Now find the slots on that day that match
    list($syear, $smonth, $sday, $shour, $sminute, $ssecond)
    	= sscanf($event['event_start_date'], "%4d-%2d-%2d %2d:%2d:%2d");
    list($eyear, $emonth, $eday, $ehour, $eminute, $esecond)
    	= sscanf($event['event_start_date'], "%4d-%2d-%2d %2d:%2d:%2d");
    $start_mins = $shour * 60 + $sminute;
    $end_mins = $ehour * 60 + $eminute;
    if (isset($slots[$day_offset])) {
      foreach ($slots[$day_offset] as $key => $slot) {
	if ($start_mins <= $slot['end_time'] && $end_mins >= $slot['start_time']) {
	  $slots[$day_offset][$key]['committed'] = true;
	}
      }
    } 
  }

  // Third pass through, find the first uncommitted slot;
  foreach ($slots as $day_offset => $day_slot) {
    foreach ($day_slot as $slot) {
      if (! $slot['committed']) {
	$hour = (int)($slot['start_time'] / 60);
	$min = $slot['start_time'] % 60;
	$ehour = (int)($slot['end_time'] / 60);
	$emin = $slot['end_time'] % 60;
	$obj->event_start_date = $slot['date'] . " " . sprintf("%02d:%02d:00", $hour, $min);
	$obj->event_end_date = $slot['date'] . " " . sprintf("%02d:%02d:00", $ehour, $emin);
	$_SESSION['add_event_post'] = get_object_vars($obj);
	$AppUI->setMsg('First available time slot', UI_MSG_OK);
	$_SESSION['event_is_clash'] = true;
	$_GET['event_id'] = $obj->event_id;
	$do_include = "$baseDir/modules/calendar/addedit.php";
	return;
      }
    }
  }
  // If we get here we have found no available slots
  clear_clash();
  $AppUI->setMsg("No times match your parameters", UI_MSG_ALERT);
  $AppUI->redirect();
}

/*
 * Cancel the event, but notify attendees of a possible meeting and request
 * they might like to contact author regarding the date.
 *
 */
function clash_mail()
{
  global $AppUI;
  $obj =& new CEvent;
  if (! $obj->bind ($_SESSION['add_event_post'])) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
  } else {
    $obj->notify(@$_SESSION['add_event_post']['event_assigned'], $_REQUEST['event_id'] ? false : true, true);
    $AppUI->setMsg("Mail sent", UI_MSG_OK);
  }
  clear_clash();
  $AppUI->redirect();
}


/*
 * Even though we end up with a clash, accept the detail.
 */
function clash_accept()
{
  global $AppUI, $do_redirect;

  $AppUI->setMsg('Event');
  $obj =& new CEvent;
  $obj->bind($_SESSION['add_event_post']);
  $GLOBALS['a'] = $_SESSION['add_event_caller'];
  $is_new = ($obj->event_id == 0);
  if (($msg = $obj->store())) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
  } else {
    if (isset($_SESSION['add_event_attendees']) && $_SESSION['add_event_attendees'])
      $obj->updateAssigned(explode(",", $_SESSION['add_event_attendees']));
    if (isset($_SESSION['add_event_mail']) && $_SESSION['add_event_mail'] == 'on')
      $obj->notify($_SESSION['add_event_attendees'], ! $is_new);
    $AppUI->setMsg($is_new ? 'added' : 'updated', UI_MSG_OK, true);
  }
  clear_clash();
  $AppUI->redirect();
}

function clear_clash()
{
  unset($_SESSION['add_event_caller']);
  unset($_SESSION['add_event_post']);
  unset($_SESSION['add_event_clash']);
  unset($_SESSION['add_event_attendees']);
  unset($_SESSION['add_event_mail']);
}

?>

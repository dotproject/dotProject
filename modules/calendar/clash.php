<?php

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (isset($_POST['clash_action'])) {
	$do_include = false;
	switch ($_POST['clash_action']) {
		case 'suggest': clash_suggest(); break;
		case 'process':  clash_process(); break;
		case 'cancel':  clash_cancel(); break;
		case 'mail':  clash_mail(); break;
		case 'accept':  clash_accept(); break;
		default:  $AppUI->setMsg('Invalid action, event cancelled', UI_MSG_ALERT); break;
	}
	// Why do it here?  Because it is in the global scope and requires
	// less hacking of the included file.
	if ($do_include) {
		include $do_include;
	}
} else {

?>
<script  language="javascript">
  function set_clash_action(action) {
    var f = document.clash_form;
    f.clash_action.value = action;
    f.submit();
  }

</script>
<?php

	$titleBlock = new CTitleBlock((($obj->event_id) ? 'Edit Event' : 'Add Event'), 
	                               'myevo-appointments.png', $m, "$m.$a");
	$titleBlock->show();
	
	$_SESSION['add_event_post'] = get_object_vars($obj);
	$_SESSION['add_event_clash'] = implode(',', array_keys($clash));
	$_SESSION['add_event_caller'] = $last_a;
	$_SESSION['add_event_attendees'] = $_POST['event_assigned'];
	$_SESSION['add_event_mail'] = isset($_POST['mail_invited']) ? $_POST['mail_invited'] : 'off';
	
	echo '<table width="100%" summary="Attendee Clash" class="std"><tr><td><b>' . $AppUI->_('clashEvent') . '</b></tr></tr>';
	foreach ($clash as $user) {
		echo '<tr><td>' . $user . "</td></tr>\n";
	}
	echo "</table>\n";
	$calurl = DP_BASE_URL.'/index.php?m=calendar&amp;a=clash&amp;event_id=' . $obj->event_id;
	echo ('<a href="#" onclick="javascript:set_clash_action(\'suggest\');">' 
		  . $AppUI->_('Suggest Alternative') . '</a> : ');
	echo '<a href="#" onclick="javascript:set_clash_action(\'cancel\');">' . $AppUI->_('Cancel') . '</a> : ';
	echo ('<a href="#" onclick="javascript:set_clash_action(\'mail\');">' . $AppUI->_('Mail Request') 
	      . '</a> : ');
	echo ('<a href="#" onclick="javascript:set_clash_action(\'accept\');">' 
		  . $AppUI->_('Book Event Despite Conflict') . "</a>\n");
	echo '<form name="clash_form" method="post" action="' . $calurl . '">';
	echo '<input type="hidden" name="clash_action" value="cancel" />';
	echo "</form>\n";
}

// Clash functions.
/*
 * Cancel the event, simply clear the event details and return to the previous
 * page.
*/
function clash_cancel() {
	global $AppUI, $a;
	$a = $_SESSION['add_event_caller'];
	clear_clash();
	$AppUI->setMsg('Event Cancelled', UI_MSG_ALERT);
	$AppUI->redirect();
}

/* 
 * display a form
 */
function clash_suggest() {
	global $AppUI, $m, $a;
	
	$obj = new CEvent;
	$obj->bind($_SESSION['add_event_post']);
	
	$start_date = new CDate($obj->event_start_date);
	$end_date = new CDate($obj->event_end_date);
	$df = $AppUI->getPref('SHDATEFORMAT');
	$start_secs = $start_date->getTime();
	$end_secs = $end_date->getTime();
	$duration = (int) (($end_secs - $start_secs) / 60);
	
	$titleBlock = new CTitleBlock('Suggest Alternative Event Time', 'myevo-appointments.png', 
	                               $m, "$m.$a");
	$titleBlock->show();
	$calurl = DP_BASE_URL . '/index.php?m=calendar&a=clash&event_id=' . $obj->event_id;
	
	$inc = intval(dPgetConfig('cal_day_increment')) ? intval(dPgetConfig('cal_day_increment')) : 30;
	$times = array();
	$t = new CDate();
	$t->setTime(0,0,0);
	//$m clashes with global $m (module)
	$check = (24 * 60) / $inc;
	$addMins = $inc * 60;
	for ($minutes=0; $minutes < $check; $minutes++) {
		$times[$t->format('%H%M%S')] = $t->format($AppUI->getPref('TIMEFORMAT'));
		$t->addSeconds($addMins);
	}
?>
<script  language="javascript">

function set_clash_action(action) {
	document.editFrm.clash_action.value = action;
	document.editFrm.submit();
}

</script>
<form name='editFrm' method='POST' action='<?php echo "$calurl&clash_action=process"; ?>'>
<table width='100%' class='std'>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Earliest Date'); ?>:</td>
  <td width="50%" align="left" nowrap="nowrap">
    <input type="date" name="event_start_date" value="<?php
echo $start_date->format(FMT_DATE_HTML5); ?>" class="text dpDateField">
  </td>
</tr>
<tr>
  <td width="50%" align="right"><?php echo $AppUI->_('Latest Date'); ?>:</td>
  <td width="50%" align="left" nowrap="nowrap">
    <input type="date" name="event_end_date" value="<?php
echo $end_date->format(FMT_DATE_HTML5); ?>" class="text dpDateField">
  </td>
</tr>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Earliest Start Time'); ?>:</td>
  <td width='50%' align='left'><?php 
echo arraySelect($times, 'start_time', 'size="1" class="text"', $start_date->format("%H%M%S")); ?>
  </td>
</tr>
<tr>
  <td width='50%' align='right'><?php echo $AppUI->_('Latest Finish Time'); ?>:</td>
  <td width='50%' align='left'>
    <?php echo arraySelect($times, 'end_time', 'size="1" class="text"', $end_date->format("%H%M%S")); ?>
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
  <td><input type="button" value="<?php echo $AppUI->_('cancel'); ?>" class="button" onclick="javascript:set_clash_action('cancel');" /></td>
  <td align="right"><input type="button" value="<?php 
echo $AppUI->_('submit'); ?>" class="button" onclick="javascript:set_clash_action('process')" /></td>
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
function clash_process() {
	global $AppUI, $do_include;
	
	$obj = new CEvent;
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
	  if (!($user)) {
      	unset($users[$key]);
	  }
	}
	
	
	// First find any events in the range requested.
	$start_date = new CDate($_POST['event_start_date'] . $_POST['start_time']);
	$end_date = new CDate($_POST['event_start_date'] . $_POST['start_time']);
	$end_date->addSeconds($_POST['duration'] * 60);
	$final_date = new CDate($_POST['event_end_date'] . $_POST['end_time']);
	
	$original_event_start = $obj->event_start_date;
	$original_event_end = $obj->event_end_date;
	
	$user_list = implode(',', $users);
	
	// Now we grab the events, in date order, and compare against the
	// required start and end times.
	// Working in 30 minute increments from the start time, and remembering
	// the end time stipulation, find the first hole in the times.
	
	// Determine the duration in hours/minutes.
	$start_hour = (int)($_POST['start_time'] / 10000);
	$start_minutes = (int)(($_POST['start_time'] % 10000) / 100);
	$start_min_offset = ($start_hour * 60) + $start_minutes;
	$end_hour = (int)($_POST['end_time'] / 10000);
	$end_minutes = (int)(($_POST['end_time'] % 10000) / 100);
	$end_min_offset = (($end_hour * 60) + $end_minutes) - $_POST['duration'];
	
	// First, build a set of "slots" that give us the duration
	// and start/end times we need
	$first_day = $start_date->format('%E');
	$end_day = $final_date->format('%E');
	$oneday = new Date_Span(array(1,0,0,0));
	
	$slots = array();
	$curr_date = new CDate($start_date);
	$curr_date->setTime(0, 0, 0);
	$inc = intval(dPgetConfig('cal_day_increment')) ? intval(dPgetConfig('cal_day_increment')) : 30;
	for ($i = 0; $i <= ($end_day - $first_day); $i++) {
		if ($curr_date->isWorkingDay()) {
			for ($j = $start_min_offset; $j <= $end_min_offset; $j += $inc) {
				$is_committed = false;
				
				$slot_start_date = new CDate($curr_date);
				$slot_start_date->addSeconds($j * 60);
				$slot_end_date = new CDate($slot_start_date);
				$slot_end_date->addSeconds($_POST['duration'] * 60);
				
				$obj->event_start_date = $slot_start_date->format('%Y-%m-%d %T');
				$obj->event_end_date = $slot_end_date->format('%Y-%m-%d %T');
				
				if (!($clash = $obj->checkClash($user_list))) {
					$_SESSION['add_event_post'] = get_object_vars($obj);
					$AppUI->setMsg('First available time slot', UI_MSG_OK);
					$_SESSION['event_is_clash'] = true;
					$_GET['event_id'] = $obj->event_id;
					$do_include = DP_BASE_DIR.'/modules/calendar/addedit.php';
					return;
				}
				
			}
		}
		$curr_date->addSpan($oneday);
		$curr_date->setTime(0, 0, 0);
	}
	
	// If we get here we have found no available slots
	$obj->event_start_date = $original_event_start;
	$obj->event_end_date = $original_event_end;
	clear_clash();
	$AppUI->setMsg('No times match your parameters', UI_MSG_ALERT);
	$AppUI->redirect();
}

/*
 * Cancel the event, but notify attendees of a possible meeting and request
 * they might like to contact author regarding the date.
 *
 */
function clash_mail() {
	global $AppUI;
	$obj = new CEvent;
	if (! $obj->bind ($_SESSION['add_event_post'])) {
		$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	} else {
		$obj->notify(@$_SESSION['add_event_attendees'], $_REQUEST['event_id'] ? false : true, true);
		$AppUI->setMsg("Mail sent", UI_MSG_OK);
	}
	clear_clash();
	$AppUI->redirect();
}


/*
 * Even though we end up with a clash, accept the detail.
 */
function clash_accept() {
	global $AppUI, $do_redirect;
	
	$AppUI->setMsg('Event');
	$obj = new CEvent;
	$obj->bind($_SESSION['add_event_post']);
	$GLOBALS['a'] = $_SESSION['add_event_caller'];
	$is_new = ($obj->event_id == 0);
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		if (isset($_SESSION['add_event_attendees']) && $_SESSION['add_event_attendees']) {
			$obj->updateAssigned(explode(',', $_SESSION['add_event_attendees']));
		}
		if (isset($_SESSION['add_event_mail']) && $_SESSION['add_event_mail'] == 'on') {
			$obj->notify($_SESSION['add_event_attendees'], ! $is_new);
		}
		$AppUI->setMsg($is_new ? 'added' : 'updated', UI_MSG_OK, true);
	}
	clear_clash();
	$AppUI->redirect();
}

function clear_clash() {
  unset($_SESSION['add_event_caller']);
  unset($_SESSION['add_event_post']);
  unset($_SESSION['add_event_clash']);
  unset($_SESSION['add_event_attendees']);
  unset($_SESSION['add_event_mail']);
}

?>

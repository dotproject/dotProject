<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$event_id = intval(dPgetParam($_GET, 'event_id', 0));
$is_clash = isset($_SESSION['event_is_clash']) ? $_SESSION['event_is_clash'] : false;

// check permissions
$canAuthor = getPermission('events', 'add', $event_id);
$canEdit = getPermission('events', 'edit', $event_id);
if (!(($canEdit && $event_id) || ($canAuthor && !($event_id)))) {
	$AppUI->redirect('m=public&a=access_denied');
}

// get the passed timestamp (today if none)
$date = dPgetCleanParam($_GET, 'date', null);

// load the record data
$obj = new CEvent();

if ($is_clash) {
  $obj->bind($_SESSION['add_event_post']);
}
else if (!$obj->load($event_id) && $event_id) {
	$AppUI->setMsg('Event');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
}

// Load the Quill Rich Text Editor
include_once($AppUI->getLibraryClass('quilljs/richedit.class'));

// load the event types
$types = dPgetSysVal('EventType');

// Load the users
$perms =& $AppUI->acl();
$users = $perms->getPermittedUsers('calendar');

// Load the assignees
$assigned = array();
if ($is_clash) {
	$assignee_list = $_SESSION['add_event_attendees'];
	if (isset($assignee_list) && $assignee_list) {
		$q  = new DBQuery;
		$q->addTable('users', 'u');
		$q->addTable('contacts', 'con');
		$q->addQuery('user_id, CONCAT_WS(" " , contact_first_name, contact_last_name)');
		$q->addWhere('user_id IN (' . $assignee_list . ')');
		$q->addWhere("user_contact = contact_id");
		$assigned = $q->loadHashList();
	}
} else if ($event_id == 0) {
	$assigned[$AppUI->user_id] = "$AppUI->user_first_name $AppUI->user_last_name";
} else {
	$assigned = $obj->getAssigned();
}
// Now that we have loaded the possible replacement event,  remove the stored
// details, NOTE: This could cause using a back button to make things break,
// but that is the least of our problems.
if ($is_clash) {
 	unset($_SESSION['add_event_post']);
	unset($_SESSION['add_event_attendees']);
	unset($_SESSION['add_event_mail']);
	unset($_SESSION['add_event_clash']);
	unset($_SESSION['event_is_clash']);
}
if ($_GET['event_project']) {
	$obj->event_project = $_GET['event_project'];
}

// setup the title block
$titleBlock = new CTitleBlock(($event_id ? 'Edit Event' : 'Add Event') ,
                               'myevo-appointments.png', $m, "$m.$a");
$titleBlock->addCrumb('?m=calendar', 'month view');
if ($event_id) {
	$titleBlock->addCrumb('?m=calendar&amp;a=view&event_id=' . $event_id, 'view this event');
}
$titleBlock->show();

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

// pull projects
require_once($AppUI->getModuleClass('projects'));
$q = new DBQuery;
$q->addTable('projects', 'p');
$q->addQuery('p.project_id, p.project_name');

$prj = new CProject;
$allowedProjects = $prj->getAllowedSQL($AppUI->user_id);

if (count($allowedProjects)) {
	$prj->setAllowedSQL($AppUI->user_id, $q);
}
$q->addOrder('project_name');

$all_projects = '(' . $AppUI->_('All', UI_OUTPUT_RAW) . ')';
$projects = arrayMerge(array(0 => $all_projects), $q->loadHashList());

if ($event_id || $is_clash) {
	$start_date = ($obj->event_start_date) ? new CDate($obj->event_start_date) : null;
	$end_date = ($obj->event_end_date) ? new CDate($obj->event_end_date) : $start_date;
} else {
	$start_date = new CDate($date);
	$start_date->setTime(8,0,0);
	$end_date = new CDate($date);
	$end_date->setTime(17,0,0);
}

$inc = intval(dPgetConfig('cal_day_increment')) ? intval(dPgetConfig('cal_day_increment')) : 30;
if (!$event_id && !$is_clash) {
	$seldate = new CDate($date);
	// If date is today, set start time to now + inc
	if ($date == date('Ymd')) {
		$h = date('H');
		// an interval after now.
		$min = intval(date('i') / $inc) + 1;
		$min *= $inc;
		if ($min > 60) {
			$min = 0;
			$h++;
		}
	}
	if (!empty($h) && $h < dPgetConfig('cal_day_end')) {
		$seldate->setTime($h, $min, 0);
	} else {
		$seldate->setTime(dPgetConfig('cal_day_start'),0,0);
	}
		$obj->event_start_date = $seldate->format(FMT_TIMESTAMP);
	if (!empty($h) && $h < dPgetConfig('cal_day_end')) {
		$seldate->addSeconds($inc * 60);
	} else {
		$seldate->setTime(dPgetConfig('cal_day_end'),0,0);
	}
		$obj->event_end_date = $seldate->format(FMT_TIMESTAMP);
}

$recurs =  array ('Never',
	'Hourly',
	'Daily',
	'Weekly',
	'Bi-Weekly',
	'Monthly',
	'Quarterly',
	'Semi-Annually',
                  'Annually');

$remind = array ('900' => '15 mins',
                 '1800' => '30 mins',
                 '3600' => '1 hour',
                 '7200' => '2 hours',
                 '14400' => '4 hours',
                 '28800' => '8 hours',
                 '56600' => '16 hours',
                 '86400' => '1 day',
                 '172800' => '2 days');

// build array of times in preference specified minute increments (default 30)
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
function submitIt() {
	var form = document.editFrm;
	if (form.event_title.value.length < 1) {
		alert('<?php echo $AppUI->_('Please enter a valid event title',  UI_OUTPUT_JS); ?>');
		form.event_title.focus();
		return;
	}
	if (form.event_start_date.value.length < 1) {
		alert('<?php echo $AppUI->_("Please enter a start date", UI_OUTPUT_JS); ?>');
		form.event_start_date.focus();
		return;
	}
	if (form.event_end_date.value.length < 1) {
		alert('<?php echo $AppUI->_("Please enter an end date", UI_OUTPUT_JS); ?>');
		form.event_end_date.focus();
		return;
	}
	if ((!(form.event_times_recuring.value>0))
		&& (form.event_recurs[0].selected!=true)) {
		alert("<?php echo $AppUI->_('Please enter number of recurrences', UI_OUTPUT_JS); ?>");
		form.event_times_recuring.value=1;
		form.event_times_recuring.focus();
		return;
	}
	// Ensure that the assigned values are selected before submitting.
	var assigned = form.assigned;
	var len = assigned.length;
	var users = form.event_assigned;
	users.value = "";
	for (var i = 0; i < len; i++) {
		if (i)
			users.value += ",";
		users.value += assigned.options[i].value;
	}
	form.submit();
}

function addUser() {
	var form = document.editFrm;
	var fl = form.resources.length -1;
	var au = form.assigned.length -1;
	//gets value of percentage assignment of selected resource

	var users = "x";

	//build array of assiged users
	for (au; au > -1; au--) {
		users = users + "," + form.assigned.options[au].value + ","
	}

	//Pull selected resources and add them to list
	for (fl; fl > -1; fl--) {
		if (form.resources.options[fl].selected && users.indexOf("," + form.resources.options[fl].value + ",") == -1) {
			t = form.assigned.length
			opt = new Option(form.resources.options[fl].text, form.resources.options[fl].value);
			form.assigned.options[t] = opt
		}
	}

}

function removeUser() {
	var form = document.editFrm;
	fl = form.assigned.length -1;
	for (fl; fl > -1; fl--) {
		if (form.assigned.options[fl].selected) {
			//remove from hperc_assign
			var selValue = form.assigned.options[fl].value;
			var re = ".*("+selValue+"=[0-9]*;).*";
			form.assigned.options[fl] = null;
		}
	}
}


</script>

<form name="editFrm" action="?m=calendar" method="post">
	<input type="hidden" name="dosql" value="do_event_aed" />
	<input type="hidden" name="event_id" value="<?php echo $event_id;?>" />
	<input type="hidden" name="event_project" value="0" />
	<input type="hidden" name="event_assigned" value="" />

<table cellspacing="1" cellpadding="2" border="0" width="100%" class="std">
<tr>
	<td width="20%" align="right" nowrap="nowrap"><?php echo $AppUI->_('Event Title');?>:</td>
	<td width="20%">
		<input autofocus type="text" class="text" size="25" name="event_title" value="<?php
echo @$obj->event_title;?>" maxlength="255" />
	</td>
	<td align="left" rowspan=4 valign="top" colspan="2" width="40%">
	<?php echo $AppUI->_('Description'); ?> :<br/>
<!--		<textarea class="textarea" name="event_description" rows="5" cols="45"><?php
//echo $obj->event_description;?></textarea> -->
  <?php
    $richedit = new DpRichEdit("event_description", dPsanitiseHTML($obj->event_description));
    $richedit->render();
   ?>
	</td>
</tr>

<tr>
	<td align="right"><?php echo $AppUI->_('Type');?>:</td>
	<td>
<?php
	echo arraySelect($types, 'event_type', 'size="1" class="text"', $obj->event_type, true);
?>
	</td>
</tr>

<tr>
	<td align="right"><?php echo $AppUI->_('Project');?>:</td>
	<td>
<?php
	echo arraySelect($projects, 'event_project', 'size="1" class="text"', $obj->event_project);
?>
	</td>
</tr>


<tr>
	<td align="right" nowrap="nowrap"><label for="event_private"><?php
echo $AppUI->_('Private Entry'); ?>:</label></td>
	<td>
		<input type="checkbox" value="1" name="event_private" id="event_private" <?php
echo (@$obj->event_private ? 'checked="checked"' : '');?> />
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Start Date'); ?>:</td>
	<td nowrap="nowrap">
		<input type="datetime-local" step="<?php echo $inc * 60 ?>" name="event_start_date" value="<?php
echo (($start_date) ? $start_date->format(FMT_DATETIME_HTML5) : ''); ?>" class="dpDateField text">
	</td>
</tr>

<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('End Date'); ?>:</td>
	<td nowrap="nowrap">
		<input type="datetime-local" step="<?php echo $inc * 60 ?>" name="event_end_date" value="<?php
echo (($end_date) ? $end_date->format(FMT_DATETIME_HTML5) : ''); ?>" class="dpDateField text">
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Recurs'); ?>:</td>
	<td><?php
echo arraySelect($recurs, 'event_recurs', 'size="1" class="text"', $obj->event_recurs, true);
?></td>
	<td align="right">x</td>
	<td>
		<input type="text"  name="event_times_recuring" value="<?php
echo ((isset($obj->event_times_recuring)) ? $obj->event_times_recuring : '1');
?>" maxlength="2" size="3" /> <?php echo $AppUI->_('times'); ?>
	</td>
</tr>
<?php /* FUNCTIONALITY NOT YET ENABLED ?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Remind Me'); ?>:</td>
	<td><?php echo arraySelect($remind, 'event_remind', 'size="1" class="text"', $obj['event_remind']); ?> <?php echo $AppUI->_('in advance'); ?></td>
</tr>
<?php */ ?>

<tr>
	<td align="right"><?php echo $AppUI->_('Resources'); ?>:</td>
	<td></td>
	<td align="left"><?php echo $AppUI->_('Invited to Event'); ?>:</td>
	<td></td>
</tr>
<tr>
	<td colspan="2" align="right">
	<?php
echo arraySelect($users, 'resources',
                 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
	</td>
	<td colspan="2" align="left">
	<?php
echo arraySelect($assigned, 'assigned',
                 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
	</td>
</tr>
<tr>
	<td></td>
	<td colspan=2 align="center">
		<table>
			<tr>
				<td align="left"><input type="button" class="button" value="&gt;"
				onclick="javascript:addUser()" /></td>
				<td align="right"><input type="button" class="button" value="&lt;"
				onclick="javascript:removeUser()" /></td>
			</tr>
		</table>
	</td>
	<td align="left"><label for="mail_invited"><?php
echo $AppUI->_('Mail Attendees?'); ?></label>
	<input type="checkbox" name="mail_invited" id="mail_invited" checked="checked" /></td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><label for="event_cwd"><?php
echo $AppUI->_('Show only on Working Days'); ?>:</label></td>
	<td>
		<input type="checkbox" value="1" name="event_cwd" id="event_cwd" <?php
echo (@$obj->event_cwd ? 'checked="checked"' : ''); ?> />
	</td>
</tr>
<tr>
	<td colspan="2" align="right">
			<?php
// $m does not equal 'calendar' here???
require_once $AppUI->getSystemClass("CustomFields");
$custom_fields = New CustomFields('calendar', 'addedit', $obj->event_id, "edit");
$custom_fields->printHTML();
?>
	</td>
<tr>
	<td colspan="2">
		<input type="button" value="<?php
echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back();" />
	</td>
	<td align="right" colspan="2">
		<input type="button" value="<?php
echo $AppUI->_('submit'); ?>" class="button" onClick="javascript:submitIt()" />
	</td>
</tr>
</table>
</form>

<?php /* CALENDAR $Id$ */
$event_id = intval( dPgetParam( $_GET, "event_id", 0 ) );
$is_clash = isset($_SESSION['event_is_clash']) ? $_SESSION['event_is_clash'] : false;

// check permissions
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// get the passed timestamp (today if none)
$date = dPgetParam( $_GET, 'date', null );

// load the record data
$obj = new CEvent();

if ($is_clash) {
  $obj->bind($_SESSION['add_event_post']);
}
else if ( !$obj->load( $event_id ) && $event_id ) {
	$AppUI->setMsg( 'Event' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

// check only owner can edit
// TODO: Should assignee's be allowed to edit?
if ($obj->event_owner != $AppUI->user_id && $event_id != 0) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// load the event types
$types = dPgetSysVal( 'EventType' );

// Load the users
$perms =& $AppUI->acl();
$users = $perms->getPermittedUsers();

// Load the assignees
$assigned = array();
if ($is_clash) {
	$assignee_list = $_SESSION['add_event_attendees'];
	if (isset($assignee_list) && $assignee_list) {
		$q  = new DBQuery;
		$q->addTable('users', 'u');
		$q->addTable('contacts', 'con');
		$q->addQuery('user_id, CONCAT_WS(" " , contact_first_name, contact_last_name)');
		$q->addWhere("user_id in ($assignee_list)");
		$q->addWhere("user_contact = contact_id");
		$assigned = $q->loadHashList();
	} else {
	}
} else if ( $event_id == 0 ) {
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

// setup the title block
$titleBlock = new CTitleBlock( ($event_id ? "Edit Event" : "Add Event") , 'myevo-appointments.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=calendar", "month view" );
if ($event_id) {
	$titleBlock->addCrumb( "?m=calendar&a=view&event_id=$event_id", "view this event" );
}
$titleBlock->show();

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

// pull projects
require_once( $AppUI->getModuleClass( 'projects' ) );
$q =& new DBQuery;
$q->addTable('projects', 'p');
$q->addQuery('p.project_id, p.project_name');

$prj =& new CProject;
$allowedProjects = $prj->getAllowedSQL($AppUI->user_id);

if (count($allowedProjects)) { 
	$prj->setAllowedSQL($AppUI->user_id, $q);
}
$q->addOrder('project_name');

$all_projects = '(' . $AppUI->_('All', UI_OUTPUT_RAW) . ')';
$projects = arrayMerge( array( 0 => $all_projects ), $q->loadHashList() );

if ($event_id || $is_clash) {
	$start_date = intval( $obj->event_start_date ) ? new CDate( $obj->event_start_date ) : null;
	$end_date = intval( $obj->event_end_date ) ? new CDate( $obj->event_end_date ) : $start_date;
} else {
	$start_date = new CDate( $date );
	$start_date->setTime( 8,0,0 );
	$end_date = new CDate( $date );
	$end_date->setTime( 17,0,0 );
}

$recurs =  array (
	'Never',
	'Hourly',
	'Daily',
	'Weekly',
	'Bi-Weekly',
	'Every Month',
	'Quarterly',
	'Every 6 months',
	'Every Year'
);

$remind = array (
	"900" => '15 mins',
	"1800" => '30 mins',
	"3600" => '1 hour',
	"7200" => '2 hours',
	"14400" => '4 hours',
	"28800" => '8 hours',
	"56600" => '16 hours',
	"86400" => '1 day',
	"172800" => '2 days'
);

// build array of times in 30 minute increments
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
function submitIt(){
	var form = document.editFrm;
	if (form.event_title.value.length < 1) {
		alert('<?php echo $AppUI->_('Please enter a valid event title',  UI_OUTPUT_JS); ?>');
		form.event_title.focus();
		return;
	}
	if (form.event_start_date.value.length < 1){
		alert('<?php echo $AppUI->_("Please enter a start date", UI_OUTPUT_JS); ?>');
		form.event_start_date.focus();
		return;
	}
	if (form.event_end_date.value.length < 1){
		alert('<?php echo $AppUI->_("Please enter an end date", UI_OUTPUT_JS); ?>');
		form.event_end_date.focus();
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

var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.event_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=250, height=220, scollbars=false' );
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

	// set end date automatically with start date if start date is after end date
	if (calendarField == 'start_date') {
		if( document.editFrm.end_date.value < idate) {
			document.editFrm.event_end_date.value = idate;
			document.editFrm.end_date.value = fdate;
		}
	}
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
		if (form.resources.options[fl].selected && users.indexOf( "," + form.resources.options[fl].value + "," ) == -1) {
			t = form.assigned.length
			opt = new Option( form.resources.options[fl].text, form.resources.options[fl].value);
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

<table cellspacing="1" cellpadding="2" border="0" width="100%" class="std">
<form name="editFrm" action="?m=calendar" method="post">
	<input type="hidden" name="dosql" value="do_event_aed" />
	<input type="hidden" name="event_id" value="<?php echo $event_id;?>" />
	<input type="hidden" name="event_project" value="0" />
	<input type="hidden" name="event_assigned" value="" />

<tr>
	<td width="20%" align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Event Title' );?>:</td>
	<td width="20%">
		<input type="text" class="text" size="25" name="event_title" value="<?php echo @$obj->event_title;?>" maxlength="255">
	</td>
	<td align="left" rowspan=4 valign="top" colspan="2" width="40%">
	<?php echo $AppUI->_( 'Description' ); ?> :<br/>
		<textarea class="textarea" name="event_description" rows="5" cols="45"><?php echo @$obj->event_description;?></textarea></td>
	</td>
</tr>

<tr>
	<td align="right"><?php echo $AppUI->_('Type');?>:</td>
	<td>
<?php
	echo arraySelect( $types, 'event_type', 'size="1" class="text"', @$obj->event_type, true );
?>
	</td>
</tr>
	
<tr>
	<td align="right"><?php echo $AppUI->_('Project');?>:</td>
	<td>
<?php
	echo arraySelect( $projects, 'event_project', 'size="1" class="text"', @$obj->event_project );
?>
	</td>
</tr>


<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Private Entry' );?>:</td>
	<td>
		<input type="checkbox" value="1" name="event_private" <?php echo (@$obj->event_private ? 'checked' : '');?>>
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Start Date' );?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="event_start_date" value="<?php echo $start_date ? $start_date->format( FMT_TIMESTAMP_DATE ) : '';?>">
		<input type="text" name="start_date" value="<?php echo $start_date ? $start_date->format( $df ) : '';?>" class="text" disabled="disabled">
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Time' );?>:</td>
	<td><?php echo arraySelect( $times, 'start_time', 'size="1" class="text"', $start_date->format( "%H%M%S" ) ); ?></td>
</tr>

<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'End Date' );?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="event_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>">
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled">
		<a href="#" onClick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Time' );?>:</td>
	<td><?php echo arraySelect( $times, 'end_time', 'size="1" class="text"', $end_date->format( "%H%M%S" ) ); ?></td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Recurs' );?>:</td>
	<td><?php echo arraySelect( $recurs, 'event_recurs', 'size="1" class="text"', $obj->event_recurs, true ); ?></td>
	<td align="right">x</td>
	<td>
		<input type="text"  name="event_times_recuring" value="<?php echo @$obj->event_times_recuring;?>" maxlength="2" size=3> <?php echo $AppUI->_( 'times' );?>
	</td>
</tr>
<?php /* FUNCTIONALITY NOT YET ENABLED ?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Remind Me' );?>:</td>
	<td><?php echo arraySelect( $remind, 'event_remind', 'size="1" class="text"', $obj['event_remind'] ); ?> <?php echo $AppUI->_( 'in advance' );?></td>
</tr>
<?php */ ?>

<tr>
	<td align="right"><?php echo $AppUI->_( 'Resources' ); ?>:</td>
	<td></td>
	<td align="left"><?php echo $AppUI->_( 'Invited to Event' ); ?>:</td>
	<td></td>
</tr>
<tr>
	<td colspan="2" align="right">
	<?php echo arraySelect( $users, 'resources', 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
	</td>
	<td colspan="2" align="left">
	<?php echo arraySelect( $assigned, 'assigned', 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
	</td>
</tr>
<tr>
	<td></td>
	<td colspan=2 align="center">
		<table>
			<tr>
				<td align="left"><input type="button" class="button" value="&gt;"
				onClick="addUser()" /></td>
				<td align="right"><input type="button" class="button" value="&lt;"
				onClick="removeUser()" /></td>
			</tr>
		</table>
	</td>
	<td align="left"><?php echo $AppUI->_('Mail Attendees?'); ?> <input type='checkbox' name='mail_invited' checked=true></td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Show only on Working Days' );?>:</td>
	<td>
		<input type="checkbox" value="0" name="event_cwd" <?php echo (@$obj->event_cwd ? 'checked' : '');?>>
	</td>
</tr>
<tr>
	<td colspan="2" align="right">
			<?php
				// $m does not equal 'calendar' here???
				require_once $AppUI->getSystemClass("CustomFields");
				$custom_fields = New CustomFields( 'calendar', 'addedit', $obj->event_id, "edit" );
				$custom_fields->printHTML();
			?>
	</td>
<tr>
	<td colspan="2">
		<input type="button" value="<?php echo $AppUI->_( 'back' );?>" class="button" onclick="javascript:history.back();">
	</td>
	<td align="right" colspan="2">
		<input type="button" value="<?php echo $AppUI->_( 'submit' );?>" class="button" onClick="submitIt()">
	</td>
</tr>
</form>
</table>

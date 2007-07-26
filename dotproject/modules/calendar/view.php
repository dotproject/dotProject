<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$event_id = intval( dPgetParam( $_GET, "event_id", 0 ) );

// check permissions for this record
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem( $m, "edit", $event_id );

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CEvent();
$canDelete = $obj->canDelete( $msg, $event_id );

// load the record data
if (!$obj->load( $event_id )) {
	$AppUI->setMsg( 'Event' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// load the event types
$types = dPgetSysVal( 'EventType' );

// load the event recurs types
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

$assigned = $obj->getAssigned();


if (($obj->event_owner != $AppUI->user_id) && !($perms->checkModule("admin", "view"))) {
 $canEdit = false;
}

$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

$start_date = $obj->event_start_date ? new CDate( $obj->event_start_date ) : null;
$end_date = $obj->event_end_date ? new CDate( $obj->event_end_date ) : null;
$event_project = db_LoadResult('SELECT project_name FROM projects where project_id=' . $obj->event_project);

// setup the title block
$titleBlock = new CTitleBlock( 'View Event', 'myevo-appointments.png', $m, "$m.$a" );
if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell('
<form action="?m=calendar&amp;a=addedit" method="post">
	<input type="submit" class="button" value="'.$AppUI->_('new event').'" />
</form>', '', '', '');
}
$titleBlock->addCrumb( '?m=calendar&amp;date='.$start_date->format( FMT_TIMESTAMP_DATE ), 'month view' );
$titleBlock->addCrumb( '?m=calendar&amp;a=day_view&amp;date='.$start_date->format( FMT_TIMESTAMP_DATE ).'&amp;tab=0', 'day view' );
if ($canEdit) {
	$titleBlock->addCrumb( '?m=calendar&amp;a=addedit&amp;event_id='.$event_id, 'edit this event' );
	if ($canDelete) {
		$titleBlock->addCrumbDelete( 'delete event', $canDelete, $msg );
	}
}
$titleBlock->show();
?>
<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
?>
function delIt() {
	if (confirm( "<?php echo $AppUI->_('eventDelete', UI_OUTPUT_JS);?>" )) {
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<form name="frmDelete" action="./index.php?m=calendar" method="post">
	<input type="hidden" name="dosql" value="do_event_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="event_id" value="<?php echo $event_id;?>" />
</form>

<tr>
	<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Event Title');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->event_title;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Type');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($types[$obj->event_type]);?></td>
		</tr>	
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project');?>:</td>
			<td class="hilite" width="100%"><a href='?m=projects&a=view&project_id=<?php echo $obj->event_project ?>'><?php echo $event_project;?></a></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Starts');?>:</td>
			<td class="hilite"><?php echo $start_date ? $start_date->format( "$df $tf" ) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Ends');?>:</td>
			<td class="hilite"><?php echo $end_date ? $end_date->format( "$df $tf" ) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Recurs');?>:</td>
			<td class="hilite"><?php echo $AppUI->_($recurs[$obj->event_recurs])." (".$obj->event_times_recuring."&nbsp;".$AppUI->_('times').")" ;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Attendees');?>:</td>
			<td class="hilite"><?php
				if (is_array($assigned)) {
					$start = false;
					foreach ($assigned as $user) {
						if ($start)
							echo "<br/>";
						else
							$start = true;
						echo $user;
					}
				}
			?>
		</tr>
		</table>
	</td>
	<td width="50%" valign="top">
		<strong><?php echo $AppUI->_('Description');?></strong>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td class="hilite">
				<?php echo str_replace( chr(10), "<br />", $obj->event_description);?>&nbsp;
			</td>
		</tr>
		</table>
		<?php
				require_once $AppUI->getSystemClass("CustomFields");
				$custom_fields = New CustomFields( $m, $a, $obj->event_id, "view" );
				$custom_fields->printHTML();
		?>

	</td>
</tr>
</table>

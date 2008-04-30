<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig, $start_date, $end_date;
global $this_day, $event_filter, $event_filter_list;
require_once $AppUI->getModuleClass('calendar');

$perms =& $AppUI->acl();
$user_id = $AppUI->user_id;
$other_users = false;
$no_modify = false;

$start_date = isset($start_date) ? $start_date : new CDate('0000-00-00 00:00:00');
$end_date = isset($end_date) ? $end_date : new CDate('9999-12-31 23:59:59');

// assemble the links for the events
$events = CEvent::getEventsForPeriod($start_date, $end_date, 'all', null, $project_id);
//echo '<pre>' . print_r($events, true) .  '</pre>';

$start_hour = dPgetConfig('cal_day_start');
$end_hour   = dPgetConfig('cal_day_end');

$tf = $AppUI->getPref('TIMEFORMAT');
$df = $AppUI->getPref('SHDATEFORMAT');
$types = dPgetSysVal('EventType');
?>
<table cellspacing="1" cellpadding="2" border="0" width="100%" class="tbl">
	<tr>
		<th><?php echo $AppUI->_('Date'); ?></th>
		<th><?php echo $AppUI->_('Type'); ?></th>
		<th><?php echo $AppUI->_('Event'); ?></th>
	</tr>
<?php
foreach ($events as $row) {
	$start = new CDate( $row['event_start_date'] );
	$end = new CDate( $row['event_end_date'] );
?>
	<tr>
		<td width="25%" nowrap="nowrap">
			<?php 
echo $start->format($df . ' ' . $tf); ?> - <?php echo $end->format($df . ' ' . $tf); ?>
		</td>
		<td width="10%" nowrap="nowrap">
			<?php 
echo dPshowImage(dPfindImage(('event' . $row['event_type'] . '.png'), 'calendar' ), 16, 16, ''); ?>
			<b><?php echo $AppUI->_($types[$row['event_type']]); ?></b>
		<td>
			<a href="?m=calendar&a=view&event_id=<?php 
echo $row['event_id'];?>" class="event" title="<?php echo $row['event_description']; ?>">
			<?php echo $row['event_title']; ?>
			</a>
		</td>
	</tr>
<?php 
}
?>
</table>

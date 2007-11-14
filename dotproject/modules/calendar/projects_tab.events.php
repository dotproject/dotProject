<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig, $start_date, $end_date, $this_day, $event_filter, $event_filter_list;
require_once $AppUI->getModuleClass('calendar');

$perms =& $AppUI->acl();
$user_id = $AppUI->user_id;
$other_users = false;
$no_modify = false;

$start_date = isset($start_date) ? $start_date : new CDate('0000-00-00 00:00:00');
$end_date = isset($end_date) ? $end_date : new CDate('9999-12-31 23:59:59');

// assemble the links for the events
$events = CEvent::getEventsForPeriod( $start_date, $end_date, 'all', 0, $project_id );

$start_hour = dPgetConfig('cal_day_start');
$end_hour   = dPgetConfig('cal_day_end');

$tf = $AppUI->getPref('TIMEFORMAT');
$df = $AppUI->getPref('SHDATEFORMAT');
$types = dPgetSysVal('EventType');

$html = '<table cellspacing="1" cellpadding="2" border="0" width="100%" class="tbl">';
$html .= '<tr><th>'. $AppUI->_('Date') . '</th><th>' .$AppUI->_('Type'). '</th><th>'. $AppUI->_('Event') . '</th></tr>';
foreach ($events as $row) {
	$html .= "\n<tr>";
	$start = new CDate( $row['event_start_date'] );
	$end = new CDate( $row['event_end_date'] );
	$html .= "\n\t<td width=\"25%\" nowrap=\"nowrap\">".$start->format($df . ' ' . $tf)."&nbsp;-&nbsp;";
	$html .= $end->format($df . ' ' . $tf)."</td>";

	$href = "?m=calendar&a=view&event_id=".$row['event_id'];
	$alt = $row['event_description'];

	$html .= "\n\t<td width=\"10%\" nowrap=\"nowrap\">";
	$html .= dPshowImage( dPfindImage( 'event'.$row['event_type'].'.png', 'calendar' ), 16, 16, '' );
	$html .= "&nbsp;<b>" . $AppUI->_($types[$row['event_type']]) . "</b>";
	$html .= "\n\t<td>";
	$html .= $href ? "\n\t\t<a href=\"$href\" class=\"event\" title=\"$alt\">" : '';
	$html .= "\n\t\t{$row['event_title']}";
	$html .= $href ? "\n\t\t</a>" : '';
	$html .= "\n\t</td>";
	$html .= "\n</tr>";
}


$html .= '</table>';
echo $html;
?>

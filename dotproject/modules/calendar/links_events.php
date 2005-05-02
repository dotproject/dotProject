<?php /* CALENDAR $Id$ */

/**
* Sub-function to collect events within a period
* @param Date the starting date of the period
* @param Date the ending date of the period
* @param array by-ref an array of links to append new items to
* @param int the length to truncate entries by
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
function getEventLinks( $startPeriod, $endPeriod, &$links, $strMaxLen ) {
	global $event_filter;
	$events = CEvent::getEventsForPeriod( $startPeriod, $endPeriod, $event_filter );

	// assemble the links for the events
	foreach ($events as $row) {
		$start = new CDate( $row['event_start_date'] );
		$end = new CDate( $row['event_end_date'] );
		$date = $start;
		$cwd = explode(",", $GLOBALS["dPconfig"]['cal_working_days']);

		for($i=0; $i <= $start->dateDiff($end); $i++) {
		// the link
			// optionally do not show events on non-working days 
			if ( ( $row['event_cwd'] && in_array($date->getDayOfWeek(), $cwd ) ) || !$row['event_cwd'] ) {
				$url = '?m=calendar&a=view&event_id=' . $row['event_id'];
				$link['href'] = '';
				$link['alt'] = $row['event_description'];
				$link['text'] = '<table cellspacing="0" cellpadding="0" border="0"><tr>'
					. '<td><a href=' . $url . '>' . dPshowImage( dPfindImage( 'event'.$row['event_type'].'.png', 'calendar' ), 16, 16, '' )
					. '</a></td>'
					. '<td><a href="' . $url . '" title="'.$row['event_description'].'"><span class="event">'.$row['event_title'].'</span></a>'
					. '</td></tr></table>';
				$links[$date->format( FMT_TIMESTAMP_DATE )][] = $link;
			 }
				$date = $date->getNextDay();
		}
	}
}
?>

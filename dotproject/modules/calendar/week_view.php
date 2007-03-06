<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$AppUI->savePlace();
global $locale_char_set;

require_once( $AppUI->getModuleClass( 'tasks' ) );

// retrieve any state parameters
if (isset( $_REQUEST['company_id'] )) {
	$AppUI->setState( 'CalIdxCompany', intval( $_REQUEST['company_id'] ) );
}
$company_id = $AppUI->getState( 'CalIdxCompany' ) !== NULL ? $AppUI->getState( 'CalIdxCompany' ) : $AppUI->user_company;

$event_filter = $AppUI->checkPrefState('CalIdxFilter', @$_REQUEST['event_filter'], 'EVENTFILTER', 'my');

// get the passed timestamp (today if none)
$date = dPgetParam( $_GET, 'date', null );

// establish the focus 'date'
$this_week = new CDate( $date );
$dd = $this_week->getDay();
$mm = $this_week->getMonth();
$yy = $this_week->getYear();

// prepare time period for 'events'
$first_time = new CDate( Date_calc::beginOfWeek( $dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY ) );
$first_time->setTime( 0, 0, 0 );
$first_time->subtractSeconds( 1 );
$last_time = new CDate( Date_calc::endOfWeek( $dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY ) );
$last_time->setTime( 23, 59, 59 );

$prev_week = new CDate( Date_calc::beginOfPrevWeek( $dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY ) );
$next_week = new CDate( Date_calc::beginOfNextWeek( $dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY ) );

$tasks = CTask::getTasksForPeriod( $first_time, $last_time, $company_id );
$events = CEvent::getEventsForPeriod( $first_time, $last_time );

$links = array();

// assemble the links for the tasks
require_once( DP_BASE_DIR.'/modules/calendar/links_tasks.php' );
getTaskLinks( $first_time, $last_time, $links, 50, $company_id );

// assemble the links for the events
require_once( DP_BASE_DIR.'/modules/calendar/links_events.php' );
getEventLinks( $first_time, $last_time, $links, 50 );

// setup the title block
$titleBlock = new CTitleBlock( 'Week View', 'myevo-appointments.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=calendar&date=".$this_week->format( FMT_TIMESTAMP_DATE ), "month view" );
$titleBlock->addCell( $AppUI->_('Event Filter') . ':');
$titleBlock->addCell(
	arraySelect($event_filter_list, 'event_filter', 'onChange="document.pickFilter.submit()" class="text"',
	$event_filter, true ), '', "<Form action='{$_SERVER['REQUEST_URI']}' method='post' name='pickFilter'>", '</form>'
);
$titleBlock->show();
?>

<style type="text/css">
TD.weekDay  {
	height:120px;
	vertical-align: top;
	padding: 1px 4px 1px 4px;
	border-bottom: 1px solid #ccc;
	border-right: 1px solid  #ccc;
	text-align: left;
}
</style>

<table border="0" cellspacing="1" cellpadding="2" width="100%" class="motitle">
<tr>
	<td>
		<a href="<?php echo '?m=calendar&a=week_view&date='.$prev_week->format( FMT_TIMESTAMP_DATE ); ?>"><img src="images/prev.gif" width="16" height="16" alt="pre" border="0"></A>
	</td>
	<th width="100%">
		<span style="font-size:12pt"><?php echo $AppUI->_( 'Week' ).' '.htmlentities($first_time->format( "%U - %Y" ), ENT_COMPAT, $locale_char_set); ?></span>
	</th>
	<td>
		<a href="<?php echo '?m=calendar&a=week_view&date='.$next_week->format( FMT_TIMESTAMP_DATE ); ?>"><img src="images/next.gif" width="16" height="16" alt="next" border="0"></A>
	</td>
</tr>
</table>

<table border="0" cellspacing="1" cellpadding="2" width="100%" style="margin-width:4px;background-color:white">
<?php
$column = 0;
$show_day = $this_week;

$today = new CDate();
$today = $today->format( FMT_TIMESTAMP_DATE );

for ($i=0; $i < 7; $i++) {
	$dayStamp = $show_day->format( FMT_TIMESTAMP_DATE );

	$day  = $show_day->getDay();
	$href = "?m=calendar&a=day_view&date=$dayStamp";

	$s = '';
	if ($column == 0) {
		$s .= '<tr>';
	}
	$s .= '<td class="weekDay" style="width:50%;">';

	$s .= '<table style="width:100%;border-spacing:0;">';
	$s .= '<tr><td align="';
	$s .= ($column == 0) ? 'left' : 'right';
	$s .= '"><a href="'.$href.'">';

	$s .= $dayStamp == $today ? '<span style="color:red">' : '';
	$day_string = "<strong>" . htmlentities($show_day->format("%d"), ENT_COMPAT, $locale_char_set) . "</strong>";
	$day_name = htmlentities($show_day->format("%A"), ENT_COMPAT, $locale_char_set);
	$s .= ($column == 0) ? "$day_string $day_name" :  "$day_name $day_string";
	
	$s .= $dayStamp == $today ? '</span>' : '';
	$s .= '</a></td></tr>';

	$s .= '<tr><td>';

	if (isset( $links[$dayStamp] )) {
		foreach ($links[$dayStamp] as $e) {
			$href = isset($e['href']) ? $e['href'] : null;
			$alt = isset($e['alt']) ? $e['alt'] : null;

			$s .= "<br />";
			$s .= $href ? "<a href=\"$href\" class=\"event\" title=\"$alt\">" : '';
			$s .= "{$e['text']}";
			$s .= $href ? '</a>' : '';
		}
	}

	$s .= '</td></tr></table>';

	$s .= '</td>';
	if ($column == 1) {
		$s .= '</tr>';
	}
	$column = 1 - $column;

// select next day
	$show_day->addSeconds( 24*3600 );
	echo $s;
}
?>
<tr>
	<td colspan="2" align="right" bgcolor="#efefe7">
		<a href="./index.php?m=calendar&a=week_view"><?php echo $AppUI->_('today');?></A>
	</td>
</tr>
</table>

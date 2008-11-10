<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$AppUI->savePlace();

dPsetMicroTime();

require_once($AppUI->getModuleClass('companies'));
require_once($AppUI->getModuleClass('tasks'));

// retrieve any state parameters
if (isset($_REQUEST['company_id'])) {
	$AppUI->setState('CalIdxCompany', intval($_REQUEST['company_id']));
}
$company_id = $AppUI->getState('CalIdxCompany', 0);

// Using simplified set/get semantics. Doesn't need as much code in the module.
$event_filter = $AppUI->checkPrefState('CalIdxFilter', @$_REQUEST['event_filter'], 
									   'EVENTFILTER', 'my');

// get the passed timestamp (today if none)
$ctoday = new CDate();
$today = $ctoday->format(FMT_TIMESTAMP_DATE);
$date = dPgetParam($_GET, 'date', $today);

// get the list of visible companies
$company = new CCompany();
$companies = $company->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 
										 'company_name');
$companies = arrayMerge(array('0'=>$AppUI->_('All')), $companies);

#echo '<pre>';print_r($events);echo '</pre>';
// setup the title block
$titleBlock = new CTitleBlock('Monthly Calendar', 'myevo-appointments.png', $m, "$m.$a");
$titleBlock->addCell($AppUI->_('Company').':');
$titleBlock->addCell(arraySelect($companies, 'company_id', 
								 'onChange="document.pickCompany.submit()" class="text"', 
								 $company_id), '',
					 ('<form action="' . $_SERVER['REQUEST_URI'] 
					  . '" method="post" name="pickCompany">'), '</form>');
$titleBlock->addCell($AppUI->_('Event Filter') . ':');
$titleBlock->addCell(arraySelect($event_filter_list, 'event_filter', 
								 'onChange="document.pickFilter.submit()" class="text"',
								 $event_filter, true), '', 
					 ('<Form action="' . $_SERVER['REQUEST_URI'] 
					  . '" method="post" name="pickFilter">'), '</form>');
$titleBlock->show();
?>

<script language="javascript">
function clickDay(uts, fdate) {
	window.location = './index.php?m=calendar&a=day_view&date='+uts+'&tab=0';
}
function clickWeek(uts, fdate) {
	window.location = './index.php?m=calendar&a=week_view&date='+uts;
}
</script>

<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td>
<?php
// establish the focus 'date'
$date = new CDate($date);

// prepare time period for 'events'
$first_time = new CDate($date);
$first_time->setDay(1);
$first_time->setTime(0, 0, 0);
$first_time->subtractSeconds(1);
$last_time = new CDate($date);
$last_time->setDay($date->getDaysInMonth());
$last_time->setTime(23, 59, 59);

$links = array();

// assemble the links for the tasks
require_once(DP_BASE_DIR.'/modules/calendar/links_tasks.php');
getTaskLinks($first_time, $last_time, $links, 20, $company_id);

// assemble the links for the events
require_once(DP_BASE_DIR.'/modules/calendar/links_events.php');
getEventLinks($first_time, $last_time, $links, 20);

// create the main calendar
$cal = new CMonthCalendar($date);
$cal->setStyles('motitle', 'mocal');
$cal->setLinkFunctions('clickDay', 'clickWeek');
$cal->setEvents($links);

echo $cal->show();
//echo '<pre>';print_r($cal);echo '</pre>';

// create the mini previous and next month calendars under
$minical = new CMonthCalendar($cal->prev_month);
$minical->setStyles('minititle', 'minical');
$minical->showArrows = false;
$minical->showWeek = false;
$minical->clickMonth = true;
$minical->setLinkFunctions('clickDay');

echo '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>';
echo ('<td valign="top" align="center" width="200">' . $minical->show() . '</td>');
echo '<td valign="top" align="center" width="100%">&nbsp;</td>';

$minical->setDate($cal->next_month);

echo ('<td valign="top" align="center" width="200">' . $minical->show() . '</td>');
echo '</tr></table>';
?>
</td></tr></table>

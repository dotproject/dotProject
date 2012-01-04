<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $tab, $locale_char_set, $date;
$AppUI->savePlace();

require_once($AppUI->getModuleClass('tasks'));
require (DP_BASE_DIR.'/functions/projects_func.php');


/* Kludge: Backward compatible function to address php5 and php4 date issue */
function php4_clone($object) {
	if (version_compare(phpversion(), '5.0') < 0) {
		return $object;
	} else {
		return @clone($object);
	}
}

// retrieve any state parameters
if (isset($_REQUEST['company_id'])) {
	$AppUI->setState('CalIdxCompany', intval($_REQUEST['company_id']));
}
$company_id = $AppUI->getState('CalIdxCompany', $AppUI->user_company);

$event_filter = $AppUI->checkPrefState('CalIdxFilter', @$_REQUEST['event_filter'], 
                                       'EVENTFILTER', 'my');

$AppUI->setState('CalDayViewTab', dPgetCleanParam($_GET, 'tab', $tab));
$tab = $AppUI->getState('CalDayViewTab' ,'0');

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

// get the passed timestamp (today if none)
$ctoday = new CDate();
$today = $ctoday->format(FMT_TIMESTAMP_DATE);
$date = dPgetCleanParam($_GET, 'date', $today);
// establish the focus 'date'
$this_day = new CDate($date);
$dd = $this_day->getDay();
$mm = $this_day->getMonth();
$yy = $this_day->getYear();

// get current week
$this_week = Date_calc::beginOfWeek ($dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY);

// prepare time period for 'events'
$first_time = php4_clone($this_day);
$first_time->setTime(0, 0, 0);
$first_time->subtractSeconds(1);

$last_time = php4_clone($this_day);
$last_time->setTime(23, 59, 59);

$prev_day = new CDate(Date_calc::prevDay($dd, $mm, $yy, FMT_TIMESTAMP_DATE));
$next_day = new CDate(Date_calc::nextDay($dd, $mm, $yy, FMT_TIMESTAMP_DATE));

// get the list of visible companies
$company = new CCompany();
global $companies;
$companies = $company->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 
                                         'company_name');
$companies = arrayMerge(array('0'=>$AppUI->_('All')), $companies);

// setup the title block
$titleBlock = new CTitleBlock('Day View', 'myevo-appointments.png', $m, "$m.$a");
$titleBlock->addCrumb(('?m=calendar&amp;date=' . $this_day->format(FMT_TIMESTAMP_DATE)), 'month view');
$titleBlock->addCrumb(('?m=calendar&amp;a=week_view&amp;date=' . $this_week), 'week view');
$titleBlock->addCell(($AppUI->_('Company') . ':'));
$titleBlock->addCell(arraySelect($companies, 'company_id', 
                                 'onchange="document.pickCompany.submit()" class="text"', 
                                 $company_id), '', 
                     ('<td><form action="' . htmlspecialchars($_SERVER['REQUEST_URI']) 
                      . '" method="post" name="pickCompany">' 
                      . '<table border="0" cellspacing="0" cellpadding="0"><tr>'), 
                     '</tr></table></form></td>');
$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new event') 
                      . '" />'), '', ('<td><form action="?m=calendar&amp;a=addedit&amp;date=' 
									  . $this_day->format(FMT_TIMESTAMP_DATE)  . '" method="post">' 
                                      . '<table border="0" cellspacing="0" cellpadding="0"><tr>'), 
                     '</tr></table></form></td>');
$titleBlock->show();
?>
<script type="text/javascript" language="javascript">
function clickDay(idate, fdate) {
		window.location = '?m=calendar&amp;a=day_view&amp;date='+idate+'&amp;tab=0';
}
</script>

<table width="100%" cellspacing="0" cellpadding="4">
	<tr>
		<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="motitle">
				<tr>
						<td>
					<a href="<?php 
echo '?m=calendar&amp;a=day_view&amp;date='.$prev_day->format(FMT_TIMESTAMP_DATE); ?>">
					<?php 
echo dPshowImage(dPfindImage('prev.gif'), 16, 16, $AppUI->_('previous day')); ?>
					</a>
						</td>
						<th width="100%">
					<?php 
echo ($AppUI->_($this_day->format('%A')) . ', ' . $this_day->format($df)); ?>
						</th>
						<td>
					<a href="<?php 
echo ('?m=calendar&amp;a=day_view&amp;date=' . $next_day->format(FMT_TIMESTAMP_DATE)); ?>">
					<?php 
echo dPshowImage(dPfindImage('next.gif'), 16, 16, $AppUI->_('next day')); ?>
					</a>
						</td>
				</tr>
				</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?php
// tabbed information boxes
$tabBox = new CTabBox(('?m=calendar&amp;a=day_view&amp;date=' . $this_day->format(FMT_TIMESTAMP_DATE)), '', 
					  $tab);
$tabBox->add($dPconfig['root_dir'] . '/modules/calendar/vw_day_events', 'Events');
$tabBox->add($dPconfig['root_dir'] . '/modules/calendar/vw_day_tasks', 'Tasks');
$tabBox->loadExtras($m);
$tabBox->show();
?>
		</td>
<?php if ($dPconfig['cal_day_view_show_minical']) { ?>
		<td valign="top" width="175">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td align="center">
<?php
$minical = new CMonthCalendar($this_day);
$minical->setStyles('minititle', 'minical');
$minical->showArrows = false;
$minical->showWeek = false;
$minical->clickMonth = true;
$minical->setLinkFunctions('clickDay');

$minical->setDate($minical->prev_month);
echo $minical->show(); 
?>
				</td>
			</tr>
			<tr>
				<td align="center">
<?php 
$minical->setDate($minical->next_month);
echo $minical->show(); 
?>
				</td>
			</tr>
			<tr>
				<td align="center">
<?php 
$minical->setDate($minical->next_month);
echo $minical->show(); 
?>

				</td>
			</tr>
	</table>
	</td>
 <?php } ?>
	</tr>
</table>

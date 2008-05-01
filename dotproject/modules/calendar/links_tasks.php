<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}


/**
* Sub-function to collect tasks within a period
*
* @param Date the starting date of the period
* @param Date the ending date of the period
* @param array by-ref an array of links to append new items to
* @param int the length to truncate entries by
* @param int the company id to filter by
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
function getTaskLinks($startPeriod, $endPeriod, &$links, $strMaxLen, $company_id=0) {
	GLOBAL $a, $AppUI, $dPconfig;
	$tasks = CTask::getTasksForPeriod($startPeriod, $endPeriod, $company_id, true);

	$durnTypes = dPgetSysVal('TaskDurationType');

	$link = array();
	$sid = 3600*24;
	// assemble the links for the tasks

	foreach ($tasks as $row) {
		// the link
		$link['href'] = "?m=tasks&a=view&task_id=".$row['task_id'];
		$link['alt'] = $row['project_name'].":\n".$row['task_name'];
		
		// the link text
		if (strlen($row['task_name']) > $strMaxLen) {
			$row['task_name'] = substr($row['task_name'], 0, $strMaxLen).'...';
		}
		$link['text'] = '<span style="color:'.bestColor($row['color']).';background-color:#'.$row['color'].'">'.$row['task_name'].'</span>';
		
		// determine which day(s) to display the task
		$start = new CDate($row['task_start_date']);
		$end = $row['task_end_date'] ? new CDate($row['task_end_date']) : null;
		$durn = $row['task_duration'];
		$durnType = $row['task_duration_type'];
		
		if (($start->after($startPeriod) || $start->equals($startPeriod)) && ($start->before($endPeriod) || $start->equals($endPeriod))) {
			$temp = $link;
			$temp['alt'] = "START [".$row['task_duration'].' '.$AppUI->_($durnTypes[$row['task_duration_type']])."]\n".$link['alt'];
			if ($a != 'day_view') {
				$temp['text'] = dPshowImage(dPfindImage('block-start-16.png')).$temp['text'];
			}
			$links[$start->format(FMT_TIMESTAMP_DATE)][] = $temp;
		}
		if ($end && $end->after($startPeriod) && $end->before($endPeriod)
				&& $start->before($end)) {
			
			$temp = $link;
			$temp['alt'] = "FINISH\n".$link['alt'];
			if ($a != 'day_view') {
				$temp['text'].= dPshowImage(dPfindImage('block-end-16.png'));
			}
			$links[$end->format(FMT_TIMESTAMP_DATE)][] = $temp;
			
		}
		// convert duration to days
		if ($durnType < 24.0) {
			if ($durn > $dPconfig['daily_working_hours']) {
				$durn /= $dPconfig['daily_working_hours'];
			} else {
				$durn = 0.0;
			}
		} else {
			$durn *= ($durnType / 24.0);
		}
		// fill in between start and finish based on duration
		// notes:
		// start date is not in a future month, must be this or past month
		// start date is counted as one days work
		// business days are not taken into account
		$target = $start;
		$target->addSeconds($durn*$sid);
		
		if (Date::compare($target, $startPeriod) < 0) {
			continue;
		}
		if (Date::compare($start, $startPeriod) > 0) {
			$temp = $start;
			$temp->addSeconds($sid);
		} else {
			$temp = $startPeriod;
		}
		
		// Optimised for speed, AJD.
		while (Date::compare($endPeriod, $temp) > 0 
		       && Date::compare($target, $temp) > 0
		       && ($end == null || $temp->before($end))) {
			$links[$temp->format(FMT_TIMESTAMP_DATE)][] = $link;
			$temp->addSeconds($sid);
		}
	}
}
?>

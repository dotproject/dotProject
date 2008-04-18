<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$do_report = dPgetParam($_POST, 'do_report', 0);
$log_start_date = dPgetParam($_POST, 'log_start_date', 0);
$log_end_date = dPgetParam($_POST, 'log_end_date', 0);
$log_all = dPgetParam($_POST['log_all'], 0);
$use_period	= dPgetParam($_POST,'use_period',0); 
$display_week_hours = dPgetParam($_POST,'display_week_hours',0); 
$max_levels = dPgetParam($_POST,'max_levels', ''); 
$log_userfilter = dPgetParam($_POST,'log_userfilter','');
$log_open = dPgetParam($_POST,'log_open',0);
$pdf_output = dPgetParam($_POST,'pdf_output',0);

$table_header = '';
$table_rows='';

// create Date objects from the datetime fields
$start_date = intval($log_start_date) ? new CDate($log_start_date) : new CDate();
$end_date = intval($log_end_date)	? new CDate($log_end_date) : new CDate();

if (!$log_start_date) {
	$start_date->subtractSpan(new Date_Span('14,0,0,0'));
}
$end_date->setTime(23, 59, 59);


?>

<script language="javascript">
var calendarField = '';

function popCalendar(field){
	calendarField = field;
	idate = eval('document.editFrm.log_' + field + '.value');
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no, status=no');
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar(idate, fdate) {
	fld_date = eval('document.editFrm.log_' + calendarField);
	fld_fdate = eval('document.editFrm.' + calendarField);
	fld_date.value = idate;
	fld_fdate.value = fdate;
}
</script>

<form name="editFrm" action="index.php?m=projects&a=reports" method="post">
<input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type; ?>" />

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">

<tr>
	<td align="right" width="1%" nowrap="nowrap">
		<label for="start_date"><?php echo $AppUI->_('For period'); ?>:</label>
		<input type="hidden" name="log_start_date" value="<?php 
echo $start_date->format(FMT_TIMESTAMP_DATE); ?>" />
		<input type="text" name="start_date" value="<?php 
echo $start_date->format($df); ?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php 
echo $AppUI->_('Calendar'); ?>" border="0" />
		</a>
	</td>
	<td width="1%" nowrap="nowrap">
		<?php
echo arraySelect(dPgetUsers(), 'log_userfilter', 'class="text" style="width: 200px"', 
                 $log_userfilter); 
?>
	</td>
	<td width="1%" nowrap="nowrap">
		<input type="checkbox" name="use_period" id="use_period"<?php 
echo (($use_period) ? ' checked="checked"' : ''); ?> value="use_period" />
		<label for="use_period"><?php echo $AppUI->_('Use the period'); ?></label>
	</td> 
	
	<td align="left" nowrap="nowrap" rowspan="2">
		<input class="button" type="submit" name="do_report" value="<?php 
echo ($AppUI->_('submit')); ?>" />
	</td>
</tr>
<tr>
	<td align="right" width="1%" nowrap="nowrap">
		<label for="end_date"><?php echo $AppUI->_('to'); ?>:</label>
		<input type="hidden" name="log_end_date" value="<?php 
echo (($end_date) ? $end_date->format(FMT_TIMESTAMP_DATE) : ''); ?>" />
		<input type="text" name="end_date" value="<?php 
echo (($end_date) ? $end_date->format($df) : ''); ?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php 
echo ($AppUI->_('Calendar')); ?>" border="0" />
		</a>
	</td>
	<td width="1%">
		<?php echo ($AppUI->_('Levels to display')); ?>
		<input type="text" name="max_levels" size="10" maxlength="3" value="<?php 
echo (($max_levels) ? $max_levels : ''); ?>" />
	</td>
	<td width="1%" nowrap="nowrap">
		<input type="checkbox" name="display_week_hours" id="display_week_hours"<?php 
echo (($display_week_hours) ? ' checked="checked"' : ''); ?> value="display_week_hours" />
		<label for="display_week_hours"><?php echo $AppUI->_('Display allocated hours/week'); ?></label>
	</td>
</tr>

</table>
</form>
<center>
	<table class="std">
<?php
if ($do_report) {

	// Let's figure out which users we have
	$query = new DBQuery;
	$query->addTable('users', 'u');
	$query->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
	$query->addQuery('u.user_id, u.user_username, c.contact_first_name, c.contact_last_name');
	if ($log_userfilter) {
		$query->addWhere('user_id = ' . $log_userfilter);
	}
	$query->addOrder('user_username');
	$user_list = $query->loadHashList('user_id');
	$query->clear();
	
	
	$proj = new CProject;
	$task = new CTask();
	$ss = $start_date->format(FMT_DATETIME_MYSQL);
	$se = $end_date->format(FMT_DATETIME_MYSQL);
	
	$query->addTable('tasks', 't');
	$query->leftJoin('projects', 'p', 'p.project_id = t.task_project');
	$query->addQuery('t.*');
	if ($use_period) {
		$query->addWhere("((task_start_date >= '$ss' AND task_start_date <= '$se') "
		                 . " OR (task_end_date <= '$se' AND task_end_date >= '$ss'))");
	}
	
	if ($project_id) {
		$query->addWhere('t.task_project = '. $project_id);
	}
	// Now add the required restrictions.
	$proj->setAllowedSQL($AppUI->user_id, $query, null, 'p');
	$task->setAllowedSQL($AppUI->user_id, $query, null, 't');
	$query->addOrder('task_end_date');
	$task_list_hash = $query->loadHashList('task_id');
	$query->clear();
	
	$task_list = array();
	$task_assigned_users = array();
	
	foreach ($task_list_hash as $task_id => $task_data) {
		$task = new CTask();
		$task->bind($task_data);
		$task_list[$task_id] = $task;
		$task_assigned_users[$task_id] = $task->getAssignedUsers();
	}
	
	$user_usage = array();
	$task_dates = array();
	
	$actual_date = $start_date;
	$days_header = ''; // we will save days title here
	
	$max_levels = (($max_levels == '' || intval($max_levels) < 0) ? -1 : intval($max_levels));
	$max_levels = (($max_levels == 0) ? 1 : $max_levels);
	
	if (count($task_list) == 0) {
		echo '<p>' . $AppUI->_('No data available') .'</p>';
	} else {
		$sss = $ss;
		$sse = $se;
		if (!$use_period) {	
			$sss = $sse = 0; 
			if ($display_week_hours) {
				foreach($task_list as $t) {
					$sss = ((!($sss) || $t->task_start_date < $sss) ? $t->task_start_date : $sss);
					$sse = ((!($sse) || $t->task_end_date > $sse) ? $t->task_end_date : $sse);
				}
			}
			
		}
		
		//Display Users and thier related Tasks.
?>		
		<tr>
			<td nowrap="nowrap" style="background: #A0A0A0;color: #000000;font-weight: bold;">
				<?php echo $AppUI->_('Task'); ?>
			</td>
<?php
		if ($project_id == 0) {
?>
			<td nowrap="nowrap" style="background: #A0A0A0;color: #000000;font-weight: bold;">
				<?php echo $AppUI->_('Project'); ?>
			</td>
<?php } ?>
			<td nowrap="nowrap" style="background: #A0A0A0;color: #000000;font-weight: bold;">
				<?php echo $AppUI->_('Start Date'); ?>
			</td>
			<td nowrap="nowrap" style="background: #A0A0A0;color: #000000;font-weight: bold;">
				<?php echo $AppUI->_('End Date'); ?>
			</td>
<?php
		if ($display_week_hours) {
			echo (weekDates($sss, $sse));
		}
?>
		</tr>
<?php 
		
		foreach ($user_list as $user_id => $user_data) {
?>
		<tr>
			<td nowrap="nowrap" style="background: #D0D0D0;color: #000000;font-weight: bold;">
				<?php 
			echo ($user_data['contact_first_name'] . ' ' . $user_data['contact_last_name'] . "\n");
?>
			</td>
<?php
			$wx = (2 + (($project_id == 0) ? 1 : 0) 
				+ (($display_week_hours) ? weekCells($sss, $sse) : 0));
			for($w=0; $w < $wx; $w++) {
?>
			  <td nowrap="nowrap" style="background: #D0D0D0">&nbsp;</td>
<?php
			}
?>
		</tr>
<?php
			$actual_date = $start_date;
			foreach($task_list as $task) {
				if ($task->task_id == $task->task_parent 
					&& isMemberOfTask($task_list, $task_assigned_users, $user_id, $task)) {
						echo (displayTask($task_list, $task, 0, $display_week_hours, $sss, $sse, 
								($project_id == 0)));
						// Get children
						echo (doChildren($task_list, $task_assigned_users, $task->task_id, 
								$user_id, 1, $max_levels, $display_week_hours, $sss, $sse, 
								($project_id == 0)));
				}
			}
		}
	}
}

?>
	</table>
</center>


<?php 
function doChildren($list, $Lusers, $id, $uid, $level, $maxlevels, $display_week_hours, $ss, $se, 
                   $log_all_projects = false) {
	$tmp = "";
	if ($maxlevels == -1 || $level < $maxlevels) {
		foreach ($list as $task) {
			if (($task->task_parent == $id) && $task->task_id != $task->task_parent) {
				// we have a child, do we have the user as a member?
				if (isMemberOfTask($list, $Lusers, $uid, $task)) {
					$tmp .= displayTask($list, $task, $level, $display_week_hours, $ss, $se, 
							$log_all_projects);
					$tmp .= doChildren($list, $Lusers, $task->task_id, $uid, $level+1, 
							$maxlevels, $display_week_hours, $ss, $se, $log_all_projects);
				}
			}
		}
	}
	return $tmp;
}

function isMemberOfTask($list, $Lusers, $user_id, $task) {
	//check for given task id
	foreach ($list as $task_id => $my_task) {
		if ($my_task->task_id == $task->task_id) {
			foreach($Lusers[$task_id] as $task_user_id => $user_data) {
				if ($task_user_id == $user_id) {
					return true;
				}
			}
		}
		
		// we have a child task
		if ($my_task->task_parent == $task->task_id 
			&& $my_task->task_id != $task->task_id) {
			if (isMemberOfTask($list, $Lusers, $user_id, $my_task)) {
				return true;
			}
		}
	}
	
	return false;
}

function displayTask($list, $task, $level, $display_week_hours, $fromPeriod, $toPeriod, 
                     $log_all_projects = false) {
	global $df;
	
	$tmp = "\t\t<tr>\n\t\t\t" . '<td nowrap="nowrap">&nbsp;&nbsp;&nbsp;';
	for($i=0; $i < $level; $i++) {
		$tmp .= '&nbsp;&nbsp;&nbsp;';
	}
	switch ($level) {
		case 0:
			$tmp .= '<b>' . $task->task_name . '</b>';
			break;
		case 1:
			$tmp .= '<i>' . $task->task_name . '</i>';
			break;
		default:
			$tmp .= $task->task_name;
			break;
	}
	
	$tmp .= "</td>\n";
	
	if ($log_all_projects) {	
		//Show project name when we are logging all projects
		$project = $task->getProject();
		$tmp .= "\t\t\t" .'<td nowrap="nowrap">' . $project['project_name'] . "</td>\n";
	}
	
	$dt = new CDate($task->task_start_date);
	$tmp .= "\t\t\t" .'<td nowrap="nowrap">' . $dt->format($df) . "</td>\n";
	
	$dt=new CDate($task->task_end_date);
	$tmp .= "\t\t\t" .'<td nowrap="nowrap">' . $dt->format($df) . "</td>\n";
	
	if ($display_week_hours) {
		$tmp .= displayWeeks($list, $task, $level, $fromPeriod, $toPeriod);
	}
	$tmp .="\t\t</tr>\n";
	return $tmp;
}


function weekDates($fromPeriod, $toPeriod) {
	global $df;
	$row = '';
	
	//start of week
	$sd = new CDate($fromPeriod);
	$days_from_start = $sd->getDayOfWeek();
	for ($i=0; $i < $days_from_start; $i++) {
		$stmp = $sd->getPrevDay();
		$sd =  new CDate($stmp->format('%Y-%m-%d 00:00:00'));
	}
	
	//end of week
	$ed = new CDate($toPeriod);
	$days_spent = $ed->getDayOfWeek();
	for ($i = (6 - $days_spent); $i > 0; $i--) {
		$etmp = $ed->getNextDay();
		$ed =  new CDate($etmp->format('%Y-%m-%d 23:59:59'));
	}
	
	$row = "";
	while ($sd->before($ed)) {
		$row.= ("\t\t\t" 
			. '<td nowrap="nowrap" style="background:#A0A0A0;color: #000000;font-weight: bold;">' 
			. $sd->format($df) . "</td>\n");
		$sd->addSeconds(7 * 24 * 3600); //add one week
	}
	return $row;
}

function weekCells($fromPeriod, $toPeriod) {
	//start of week
	$sd = new CDate($fromPeriod);
	$days_from_start = $sd->getDayOfWeek();
	for ($i=0; $i < $days_from_start; $i++) {
		$stmp = $sd->getPrevDay();
		$sd =  new CDate($stmp->format('%Y-%m-%d 00:00:00'));
	}
	
	//end of week
	$ed = new CDate($toPeriod);
	$days_spent = $ed->getDayOfWeek();
	for ($i = (6 - $days_spent); $i > 0; $i--) {
		$etmp = $ed->getNextDay();
		$ed =  new CDate($etmp->format('%Y-%m-%d 23:59:59'));
	}
	
	$weeks = 0;
	while ($sd->before($ed)) {
		$weeks++;
		$sd->addSeconds(7 * 24 * 3600); //add one week
	}
	
	return $weeks;
}

// Look for a user when he/she has been allocated
// to this task and when. Report this in weeks
// This function is called within 'displayTask()'
function displayWeeks($list, $task, $level, $fromPeriod, $toPeriod) {
	
	//start of week
	$sd = new CDate($fromPeriod);
	$days_from_start = $sd->getDayOfWeek();
	for ($i=0; $i < $days_from_start; $i++) {
		$stmp = $sd->getPrevDay();
		$sd =  new CDate($stmp->format('%Y-%m-%d 00:00:00'));
	}
	
	//end of week
	$ed = new CDate($toPeriod);
	$days_spent = $ed->getDayOfWeek();
	for ($i = (6 - $days_spent); $i > 0; $i--) {
		$etmp = $ed->getNextDay();
		$ed =  new CDate($etmp->format('%Y-%m-%d 23:59:59'));
	}
	
	$st = new CDate($task->task_start_date);
	$et = new CDate($task->task_end_date);
	
	$row = '';
	while ($sd->before($ed)) {
		$sd_end = new CDate($sd->format('%Y-%m-%d 00:00:00'));
		$sd_end->addSeconds(7 * 24 * 3600); //add one week
		
		if (($sd->after($st) && $sd_end->before($et)) 
			|| ($st->before($sd_end) && !($st->before($sd))) 
			|| ($et->after($sd) && !($et->after($sd_end)))) {
			/*
			 * generate visually distiguishable colors for up to 12 task levels
			 * Color will just be blue (#0000FF) for levels 12th and up. 
			 */
			$red_key = (12 - (floor($level / 3) * 3));
			$red_key = (($red_key > 15) ? 15 : (($red_key < 0) ? 0 : $red_key));
			
			$green_key_1 = ($red_key + 4 - (($level % 3) * 4));
			$green_key_1 = (($green_key_1 > 15) ? 15 : (($green_key_1 < 0) ? 0 : $green_key_1));
			
			$green_key_2 = (($green_key_1 == $red_key) ? 0 : $green_key_1);
			
			$color_hex = strtoupper('#' .dechex($red_key) . '0' . dechex($green_key_1) 
			                        . dechex($green_key_2) . 'FF');
			$row .= '<td nowrap="nowrap" style="background:' . $color_hex . ';" >';
		}
		else {
			$row .= '<td nowrap="nowrap">';
		}
		$row .= '&nbsp;</td>';
		$sd->addSeconds(7 * 24 * 3600); //add one week
	}
	return $row;
}

?>

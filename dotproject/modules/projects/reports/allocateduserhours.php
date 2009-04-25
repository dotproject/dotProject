<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$coarseness 		 = dPgetParam($_POST, "coarseness", 1);
$do_report 		 = dPgetParam($_POST, "do_report", 0);
$hideNonWd		 = dPgetParam($_POST, "hideNonWd", 0);
$log_start_date          = dPgetParam($_POST, "log_start_date", 0);
$log_end_date 	         = dPgetParam($_POST, "log_end_date", 0);
$use_assigned_percentage = dPgetParam($_POST, "use_assigned_percentage", 0);
$user_id                 = dPgetParam($_POST, "user_id", $AppUI->user_id);

// create Date objects from the datetime fields
$start_date = intval($log_start_date) ? new CDate($log_start_date) : new CDate(date("Y-m-01"));
$end_date   = intval($log_end_date)   ? new CDate($log_end_date) : new CDate();

$end_date->setTime(23, 59, 59);
?>

<script language="javascript">
var calendarField = '';

function popCalendar(field) {
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
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_category" value="<?php echo $report_category;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">


<tr>
	<td nowrap="nowrap"><?php echo $AppUI->_('For period');?>:
		<input type="hidden" name="log_start_date" value="<?php echo $start_date->format(FMT_TIMESTAMP_DATE);?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format($df);?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td nowrap="nowrap"><?php echo $AppUI->_('to');?>
		<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format($df) : '';?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td nowrap='nowrap'>
	   <input type="radio" name="coarseness" value="1" <?php if ($coarseness == 1) echo "checked" ?> />
	   <?php echo $AppUI->_('Days');?>
	   <input type="radio" name="coarseness" value="7" <?php if ($coarseness == 7) echo "checked" ?> />
	   <?php echo $AppUI->_('Weeks');?>
</td>
	<td nowrap='nowrap'>
	   <?php 
	       echo $AppUI->_('Tasks created by');
	       echo " ";
	       echo getUsersCombo($user_id);
	   ?>
	</td>
</tr>
<tr>
	<td nowrap="nowrap" colspan="3" align="center">
		<input type="checkbox" name="log_all_projects" id="log_all_projects" <?php if ($log_all_projects) echo 'checked="checked"' ?> />
		<label for="log_all_projects"><?php echo $AppUI->_('Log All Projects');?></label>
	   <input type="checkbox" name="use_assigned_percentage" id="use_assigned_percentage" <?php if ($use_assigned_percentage) echo 'checked="checked"' ?> />
	   <labe for="use_assigned_percentage"><?php echo $AppUI->_('Use assigned percentage');?></label>
	
	   <input type="checkbox" name="hideNonWd" id="hideNonWd" <?php if ($hideNonWd) echo 'checked="checked"' ?> />
	   <label for="hideNonWd"><?php echo $AppUI->_('Hide non-working days');?></label>
	</td>	
	<td align="left" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>
</table>
</form>

<?php
if ($do_report) {
	
	// Let's figure out which users we have
	$q = new DBQuery;
	$q->addTable('users', 'u');
	$q->addQuery('u.user_id, u.user_username, contact_first_name, contact_last_name');
	$q->addJoin('contacts', 'c', 'u.user_contact = contact_id');
	$user_list = $q->loadHashList('user_id');
	$q->clear();
	
	$q = new DBQuery;
	$q->addTable('tasks', 't');
	$q->addTable('user_tasks', 'ut');
	$q->addTable('projects', 'p');
	$q->addQuery('t.*, ut.*, p.project_name');
	$q->addWhere("(task_start_date
			   BETWEEN \"".$start_date->format(FMT_DATETIME_MYSQL)."\" 
	                AND \"".$end_date->format(FMT_DATETIME_MYSQL)."\" 
	           OR task_end_date	BETWEEN \"".$start_date->format(FMT_DATETIME_MYSQL)."\" 
	                AND \"".$end_date->format(FMT_DATETIME_MYSQL)."\" 
		   OR (task_start_date <= \"".$start_date->format(FMT_DATETIME_MYSQL)."\"
	                AND task_end_date >= \"".$end_date->format(FMT_DATETIME_MYSQL)."\"))");
	$q->addWhere('task_end_date IS NOT NULL');
	$q->addWhere("task_end_date != '0000-00-00 00:00:00'");
	$q->addWhere('task_start_date IS NOT NULL');
	$q->addWhere("task_start_date != '0000-00-00 00:00:00'");
	$q->addWhere("task_dynamic !='1'");
	$q->addWhere("task_milestone = '0'");
	$q->addWhere('task_duration  > 0');
	$q->addWhere('t.task_project = p.project_id');
	$q->addWhere('t.task_id = ut.task_id');
	
	if ($user_id) {
		$q->addWhere("t.task_owner = '$user_id'");
    	}
	if ($project_id != 0) {
		$q->addWhere("t.task_project='$project_id'");
	}

	$proj =& new CProject;
	$proj->setAllowedSQL($AppUI->user_id, $q);

	$obj =& new CTask;
	$obj->setAllowedSQL($AppUI->user_id, $q);
	
	$task_list_hash   = $q->loadHashList('task_id');
	
	$q->clear();
	
	$task_list        = array();
	$fetched_projects = array();
	foreach ($task_list_hash as $task_id => $task_data) {
		$task = new CTask();
		$task->bind($task_data);
		$task_list[] = $task;
		$fetched_projects[$task->task_project] = $task_data["project_name"];
	}
	
	$user_usage            = array();
	$task_dates            = array();
	
	$actual_date = $start_date;
	$days_header = ""; // we will save days title here
	
	$user_tasks_counted_in = array();
	$user_names = array();
	
	if (count($task_list) == 0) {
		echo "<p>" . $AppUI->_('No data available') ."</p>";
	} else {
		foreach ($task_list as $task) {
			$task_start_date  = new CDate($task->task_start_date);
			$task_end_date    = new CDate($task->task_end_date);
			
			$day_difference   = $task_end_date->dateDiff($task_start_date);
			$actual_date      = $task_start_date;
	
			$users            = $task->getAssignedUsers();
			
			if ($coarseness == 1) {
				userUsageDays();
			} else if ($coarseness == 7) {
				userUsageWeeks();
			}
			
		}
	
		if ($coarseness == 1) {
			showDays();
		} else if ($coarseness == 7) {
			showWeeks();
		}
		?>
			<center><table class="std">
			<?php echo $table_header . $table_rows; ?>
			</table>
			<table width="100%"><tr><td align="center">
		<?php
			
	
			echo '<h4>' . $AppUI->_("Total capacity for shown users") . '</h4>';
			echo $AppUI->_("Allocated hours").": ".number_format($allocated_hours_sum,2)."<br />";
			echo $AppUI->_("Total capacity").": ".number_format($total_hours_capacity,2)."<br />";
			echo $AppUI->_("Percentage used").": ". (($total_hours_capacity > 0) ? number_format($allocated_hours_sum/$total_hours_capacity,2)*100 : 0) ."%<br />";
	?>
			</td>
			<td align="center">
		<?php
			
	
			echo '<h4>' . $AppUI->_("Total capacity for all users") . '</h4>';
			echo $AppUI->_("Allocated hours").": ".number_format($allocated_hours_sum,2)."<br />";
			echo $AppUI->_("Total capacity").": ".number_format($total_hours_capacity_all,2)."<br />";
			echo $AppUI->_("Percentage used").": ".(($total_hours_capacity_all > 0) ? number_format($allocated_hours_sum/$total_hours_capacity_all,2)*100 : 0)."%<br />";
	}		
?>
	   </td></tr>
	   </table>
	   </center>
<?php
foreach ($user_tasks_counted_in as $user_id => $project_information) {
	echo "<b>".$user_names[$user_id]."</b><br /><blockquote>";
	echo "<table width='50%' border='1' class='std'>";
	foreach ($project_information as $project_id => $task_information) {
	        echo "<tr><th colspan='3'><span style='font-weight:bold; font-size:110%'>".$fetched_projects[$project_id]."</span></th></tr>";
	        
	        $project_total = 0;
	        foreach ($task_information as $task_id => $hours_assigned) {
	        echo "<tr><td>&nbsp;</td><td>".$task_list_hash[$task_id]["task_name"]."</td><td>".round($hours_assigned,2)." hrs</td></tr>";
	        $project_total += round($hours_assigned,2);
	        }
	        echo "<tr><td colspan='2' align='right'><b>".$AppUI->_("Total assigned")."</b></td><td><b>$project_total hrs</b></td></tr>";
	        
	}
	echo "</table></blockquote>";
	}	
}

function userUsageWeeks() {
GLOBAL $task_start_date, $task_end_date, $day_difference, $hours_added, $actual_date, $users, $user_data, $user_usage,$use_assigned_percentage, $user_tasks_counted_in, $task, $start_date, $end_date;

	$task_duration_per_week = $task->getTaskDurationPerWeek($use_assigned_percentage);
	setlocale(LC_ALL, 'en_AU'.(($locale_char_set)? ('.' . $locale_char_set) : '.utf8'));
	$ted = new CDate(Date_Calc::endOfWeek($task_end_date->day,$task_end_date->month,$task_end_date->year));
	$tsd = new CDate(Date_Calc::beginOfWeek($task_start_date->day,$task_start_date->month,$task_start_date->year));
	$ed = new CDate(Date_Calc::endOfWeek($end_date->day,$end_date->month,$end_date->year));
	$sd = new CDate(Date_Calc::beginOfWeek($start_date->day,$start_date->month,$start_date->year));
	setlocale(LC_ALL, $AppUI->user_lang);

	$week_difference = $end_date->workingDaysInSpan($start_date)/count(explode(",",dPgetConfig("cal_working_days")));

	$actual_date = $start_date;

	for ($i = 0; $i<=$week_difference; $i++) {
		if (!$actual_date->before($tsd) && !$actual_date->after($ted)) {
			setlocale(LC_ALL, 'en_AU'.(($locale_char_set)? ('.' . $locale_char_set) : '.utf8'));
			$awoy = $actual_date->year.Date_Calc::weekOfYear($actual_date->day,$actual_date->month,$actual_date->year);
			setlocale(LC_ALL, $AppUI->user_lang);
			foreach ($users as $user_id => $user_data) {
				if (!isset($user_usage[$user_id][$awoy])) {
					$user_usage[$user_id][$awoy] = 0;
				}
				$percentage_assigned = $use_assigned_percentage ? ($user_data["perc_assignment"]/100) : 1;
				$hours_added = $task_duration_per_week * $percentage_assigned;
				$user_usage[$user_id][$awoy] += $hours_added;
				if ($user_usage[$user_id][$awoy] < 0.005) {
					//We want to show at least 0.01 even when the assigned time is very small so we know
					//that at that time the user has a running task
					$user_usage[$user_id][$awoy] += 0.006;
					$hours_added                 += 0.006;
				}
				
				// Let's register the tasks counted in for calculation
				if (!array_key_exists($user_id, $user_tasks_counted_in)) {
				    $user_tasks_counted_in[$user_id] = array();
				}
				
				if (!array_key_exists($task->task_project, $user_tasks_counted_in[$user_id])) {
				    $user_tasks_counted_in[$user_id][$task->task_project] = array();
				}
				
				if (!array_key_exists($task->task_id, $user_tasks_counted_in[$user_id][$task->task_project])) {
				    $user_tasks_counted_in[$user_id][$task->task_project][$task->task_id] = 0;
				}
				// We add it up
				$user_tasks_counted_in[$user_id][$task->task_project][$task->task_id] += $hours_added;
			}
		}
		$actual_date->addSeconds(168*3600);	// + one week
	}
}

function showWeeks() {
GLOBAL   $allocated_hours_sum, $end_date, $start_date, $AppUI, $user_list, $user_names, $user_usage, $hideNonWd, $table_header, $table_rows, $df, $working_days_count, $total_hours_capacity, $total_hours_capacity_all;

	$working_days_count = 0;
	$allocated_hours_sum = 0;

	setlocale(LC_ALL, 'en_AU'.(($locale_char_set)? ('.' . $locale_char_set) : '.utf8'));
	$ed = new CDate(Date_Calc::endOfWeek($end_date->day,$end_date->month,$end_date->year));
	$sd = new CDate(Date_Calc::beginOfWeek($start_date->day,$start_date->month,$start_date->year));
	setlocale(LC_ALL, $AppUI->user_lang);

	$week_difference = ceil($ed->workingDaysInSpan($sd)/count(explode(",",dPgetConfig("cal_working_days"))));

	$actual_date = $sd;

	$table_header = "<tr><th>".$AppUI->_("User")."</th>";
	for ($i=0; $i<$week_difference; $i++) {
		$actual_date->addSeconds(168*3600);	// + one week
		$working_days_count = $working_days_count + count(explode(",",dPgetConfig("cal_working_days")));
	}
	$table_header .= "<th nowrap='nowrap' colspan='2'>".$AppUI->_("Allocated")."</th></tr>";
	
	$table_rows = "";
	
	foreach ($user_list as $user_id => $user_data) {
	    @$user_names[$user_id] = $user_data["user_username"];
		if (isset($user_usage[$user_id])) {
			$table_rows .= "<tr><td nowrap='nowrap'>(".$user_data["user_username"].") ".$user_data["contact_first_name"]." ".$user_data["contact_last_name"]."</td>";
			$actual_date = $sd;
/*
			for ($i=0; $i<$week_difference; $i++) { 
				setlocale(LC_ALL, 'en_AU'.(($locale_char_set)? ('.' . $locale_char_set) : '.utf8'));
				$awoy = $actual_date->year.Date_Calc::weekOfYear($actual_date->day,$actual_date->month,$actual_date->year);
				setlocale(LC_ALL, $AppUI->user_lang);

				$table_rows .= "<td align='right'>";
				if (isset($user_usage[$user_id][$awoy])) {
					$hours = number_format($user_usage[$user_id][$awoy],2);
					$table_rows .= $hours;
					$percentage_used = round(($hours/(dPgetConfig("daily_working_hours")*count(explode(",",dPgetConfig("cal_working_days")))))*100);
					$bar_color       = "blue";
					if ($percentage_used > 100) {
						$bar_color = "red";
						$percentage_used = 100;
					}
					$table_rows .= "<div style='height:2px;width:$percentage_used%; background-color:$bar_color'>&nbsp;</div>";
				} else {
					$table_rows .= "&nbsp;";
				} 
				$table_rows .= "</td>";

				$actual_date->addSeconds(168*3600);	// + one week
			}
*/
			$array_sum = array_sum($user_usage[$user_id]);

			$average_user_usage = number_format(($array_sum/($week_difference * count(explode(",",dPgetConfig("cal_working_days")))*dPgetConfig("daily_working_hours")))*100, 2);
			$allocated_hours_sum += $array_sum;

			$bar_color = "blue";
			if ($average_user_usage > 100) {
				$bar_color = "red";
				$average_user_usage = 100;
			}
			$table_rows .= "<td ><div align='left'>".round($array_sum, 2)." ".$AppUI->_("hours")."</td> <td align='right'> ".$average_user_usage ;
			$table_rows .= "%</div>";
			$table_rows .= "<div align='left' style='height:2px;width:$average_user_usage%; background-color:$bar_color'>&nbsp;</div></td>";
			$table_rows .= "</tr>";
		}	
	}
/*
	$total_hours_capacity = $week_difference * count(explode(",",dPgetConfig("cal_working_days"))) * dPgetConfig("daily_working_hours") * count($user_usage);		
	$total_hours_capacity_all = $week_difference * count(explode(",",dPgetConfig("cal_working_days"))) *dPgetConfig("daily_working_hours") * count($user_list);
*/
	$total_hours_capacity = $working_days_count/2*dPgetConfig("daily_working_hours")*count($user_usage);
	$total_hours_capacity_all = $working_days_count/2*dPgetConfig("daily_working_hours")*count($user_list);
}

function userUsageDays() {
GLOBAL $task_start_date, $task_end_date, $day_difference, $hours_added, $actual_date, $users, $user_data, $user_usage,$use_assigned_percentage, $user_tasks_counted_in, $task, $start_date, $end_date;

			$task_duration_per_day = $task->getTaskDurationPerDay($use_assigned_percentage);
			
			for ($i = 0; $i<=$day_difference; $i++) {
				if (!$actual_date->before($start_date) && !$actual_date->after($end_date)
				   && $actual_date->isWorkingDay()) {
	
					foreach ($users as $user_id => $user_data) {
						if (!isset($user_usage[$user_id][$actual_date->format("%Y%m%d")])) {
							$user_usage[$user_id][$actual_date->format("%Y%m%d")] = 0;
						}
						$percentage_assigned = $use_assigned_percentage ? ($user_data["perc_assignment"]/100) : 1;
						$hours_added = $task_duration_per_day * $percentage_assigned;
						$user_usage[$user_id][$actual_date->format("%Y%m%d")] += $hours_added;
						if ($user_usage[$user_id][$actual_date->format("%Y%m%d")] < 0.005) {
							//We want to show at least 0.01 even when the assigned time is very small so we know
							//that at that time the user has a running task
							$user_usage[$user_id][$actual_date->format("%Y%m%d")] += 0.006;
							$hours_added                                          += 0.006;
						}
						
						// Let's register the tasks counted in for calculation
						if (!array_key_exists($user_id, $user_tasks_counted_in)) {
						    $user_tasks_counted_in[$user_id] = array();
						}
						
						if (!array_key_exists($task->task_project, $user_tasks_counted_in[$user_id])) {
						    $user_tasks_counted_in[$user_id][$task->task_project] = array();
						}
						
						if (!array_key_exists($task->task_id, $user_tasks_counted_in[$user_id][$task->task_project])) {
						    $user_tasks_counted_in[$user_id][$task->task_project][$task->task_id] = 0;
						}
						// We add it up
						$user_tasks_counted_in[$user_id][$task->task_project][$task->task_id] += $hours_added;
					}
				}
				$actual_date->addDays(1);
			}
}




function showDays() {
	GLOBAL  $allocated_hours_sum, $end_date, $start_date, $AppUI, $user_list, $user_names, $user_usage, $hideNonWd, $table_header, $table_rows, $df, $working_days_count, $total_hours_capacity, $total_hours_capacity_all;

		$days_difference =  $end_date->dateDiff($start_date);

		$actual_date     = $start_date;
		$working_days_count = 0;
		$allocated_hours_sum = 0;
		
		$table_header = "<tr><th>".$AppUI->_("User")."</th>";
		for ($i=0; $i<=$days_difference; $i++) {
			if ($actual_date->isWorkingDay()) {
				$working_days_count++;
			}
			$actual_date->addDays(1);
		}
		$table_header .= "<th nowrap='nowrap' colspan='2'>".$AppUI->_("Allocated")."</th></tr>";
		
		$table_rows = "";
		
		foreach ($user_list as $user_id => $user_data) {
		    @$user_names[$user_id] = $user_data["user_username"];
			if (isset($user_usage[$user_id])) {
				$table_rows .= "<tr><td nowrap='nowrap'>(".$user_data["user_username"].") ".$user_data["contact_first_name"]." ".$user_data["contact_last_name"]."</td>";
				$actual_date = $start_date;
/*
				for ($i=0; $i<=$days_difference; $i++) {	
					if (($actual_date->isWorkingDay()) || (!$actual_date->isWorkingDay() && !$hideNonWd)) {
						$table_rows .= "<td>";
						if (isset($user_usage[$user_id][$actual_date->format("%Y%m%d")])) {
							$hours       = number_format($user_usage[$user_id][$actual_date->format("%Y%m%d")],2);
							$table_rows .= $hours;
							$percentage_used = round($hours/dPgetConfig("daily_working_hours")*100);
							$bar_color       = "blue";
							if ($percentage_used > 100) {
								$bar_color = "red";
								$percentage_used = 100;
							}
							$table_rows .= "<div style='height:2px;width:$percentage_used%; background-color:$bar_color'>&nbsp;</div>";
						} else {
							$table_rows .= "&nbsp;";
						} 
						$table_rows .= "</td>";
					}
					$actual_date->addDays(1);
				}
*/
				$array_sum = array_sum($user_usage[$user_id]);
				$average_user_usage = number_format(($array_sum/($working_days_count*dPgetConfig("daily_working_hours")))*100, 2);
				$allocated_hours_sum += $array_sum;
				
				$bar_color = "blue";
				if ($average_user_usage > 100) {
					$bar_color = "red";
					$average_user_usage = 100;
				}
				$table_rows .= "<td ><div align='left'>".round($array_sum, 2)." ".$AppUI->_("hours")."</td> <td align='right'> ".$average_user_usage ;
				$table_rows .= "%</div>";
				$table_rows .= "<div align='left' style='height:2px;width:$average_user_usage%; background-color:$bar_color'>&nbsp;</div></td>";
				$table_rows .= "</tr>";
				
			}
		}
		$total_hours_capacity = $working_days_count*dPgetConfig("daily_working_hours")*count($user_usage);
		$total_hours_capacity_all = $working_days_count*dPgetConfig("daily_working_hours")*count($user_list);
}
?>		

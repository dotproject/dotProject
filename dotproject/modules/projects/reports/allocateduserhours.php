<?php

$do_report 		         = dPgetParam( $_POST, "do_report", 0 );
$log_start_date          = dPgetParam( $_POST, "log_start_date", 0 );
$log_end_date 	         = dPgetParam( $_POST, "log_end_date", 0 );
$log_all_projects 	     = dPgetParam($_POST, "log_all_projects", 0);
$log_all		         = dPgetParam($_POST, "log_all", 0);
$use_assigned_percentage = dPgetParam($_POST, "use_assigned_percentage", 0);
$user_id                 = dPgetParam($_POST, "user_id", $AppUI->user_id);

// create Date objects from the datetime fields
$start_date = intval( $log_start_date ) ? new CDate( $log_start_date ) : new CDate(date("Y-m-01"));
$end_date   = intval( $log_end_date )   ? new CDate( $log_end_date ) : new CDate();

$end_date->setTime( 23, 59, 59 );
?>

<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scollbars=false' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.log_' + calendarField );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}
</script>

<form name="editFrm" action="index.php?m=projects&a=reports" method="post">
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">


<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_start_date" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format( $df );?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to');?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>

	<td nowrap="nowrap">
		<input type="checkbox" name="log_all_projects" <?php if ($log_all_projects) echo "checked" ?> />
		<?php echo $AppUI->_( 'Log All Projects' );?>
	</td>
	
	<td nowrap='nowrap'>
	   <input type="checkbox" name="use_assigned_percentage" <?php if ($use_assigned_percentage) echo "checked" ?> />
	   <?php echo $AppUI->_( 'Use assigned percentage' );?>
	</td>
	
	<td nowrap='nowrap'>
	   <?php 
	       echo $AppUI->_( 'Tasks created by' );
	       echo " ";
	       echo getUsersCombo($user_id);
	   ?>
	</td>
	
	<td align="right" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>

</table>
</form>

<?php
if($do_report) {
	
	// Let's figure out which users we have
	$sql = "SELECT  u.user_id,
	 				u.user_username, 
					contact_first_name, 
					contact_last_name
	        FROM users AS u
                LEFT JOIN contacts ON user_contact = contact_id";
	
	$user_list = db_loadHashList($sql, "user_id");
	
	$sql = "SELECT t.*, ut.*, p.project_name
			FROM tasks AS t, user_tasks AS ut, projects AS p
			WHERE (task_start_date
			   BETWEEN \"".$start_date->format( FMT_DATETIME_MYSQL )."\" 
	                AND \"".$end_date->format( FMT_DATETIME_MYSQL )."\" 
	           OR task_end_date	BETWEEN \"".$start_date->format( FMT_DATETIME_MYSQL )."\" 
	                AND \"".$end_date->format( FMT_DATETIME_MYSQL )."\")
							OR (task_start_date <= \"".$start_date->format( FMT_DATETIME_MYSQL )."\"
								AND task_end_date >= \"".$end_date->format( FMT_DATETIME_MYSQL )."\")
	        AND !isnull(task_end_date) AND task_end_date != '0000-00-00 00:00:00'
	        AND !isnull(task_start_date) AND task_start_date != '0000-00-00 00:00:00'
	        AND task_dynamic   !='1'
	        AND task_milestone = '0'
	        AND task_duration  > 0
	        AND t.task_project = p.project_id";
    if($user_id){
        $sql .= " AND t.task_owner = '$user_id'";
    }
	if(!$log_all_projects){
		$sql .= " AND t.task_project='$project_id'\n";
	}

	$sql .= " AND t.task_id = ut.task_id";

	$proj =& new CProject;
	$allowedProjects = $proj->getAllowedSQL($AppUI->user_id, 'task_project');
	if (count($allowedProjects)) {
		$sql .= " AND " . implode(" AND ", $allowedProjects);
	}

	$obj =& new CTask;
	$allowedTasks = $obj->getAllowedSQL($AppUI->user_id);
	if (count($allowedTasks)) {
		$sql .= " AND " . implode(" AND ", $allowedTasks);
	}
	
	$task_list_hash   = db_loadHashList($sql, "task_id");
	$task_list        = array();
	$fetched_projects = array();
	foreach($task_list_hash as $task_id => $task_data){
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
	
	if ( count($task_list) == 0 ) {
		echo "<p>" . $AppUI->_( 'No data available' ) ."</p>";
	}else {
		foreach($task_list as $task) {
			$task_start_date  = new CDate($task->task_start_date);
			$task_end_date    = new CDate($task->task_end_date);
			
			$day_difference   = $task_end_date->dateDiff($task_start_date);
			$actual_date      = $task_start_date;
	
			$users                 = $task->getAssignedUsers();
			$task_duration_per_day = $task->getTaskDurationPerDay($use_assigned_percentage);
			
			for($i = 0; $i<=$day_difference; $i++){
				if(!$actual_date->before($start_date) && !$actual_date->after($end_date)
				   && $actual_date->isWorkingDay()) {
	
					foreach($users as $user_id => $user_data){
						if(!isset($user_usage[$user_id][$actual_date->format("%Y%m%d")])){
							$user_usage[$user_id][$actual_date->format("%Y%m%d")] = 0;
						}
						$percentage_assigned = $use_assigned_percentage ? ($user_data["perc_assignment"]/100) : 1;
						$hours_added = $task_duration_per_day * $percentage_assigned;
						$user_usage[$user_id][$actual_date->format("%Y%m%d")] += $hours_added;
						if($user_usage[$user_id][$actual_date->format("%Y%m%d")] < 0.005){
							//We want to show at least 0.01 even when the assigned time is very small so we know
							//that at that time the user has a running task
							$user_usage[$user_id][$actual_date->format("%Y%m%d")] += 0.006;
							$hours_added                                          += 0.006;
						}
						
						// Let's register the tasks counted in for calculation
						if(!array_key_exists($user_id, $user_tasks_counted_in)){
						    $user_tasks_counted_in[$user_id] = array();
						}
						
						if(!array_key_exists($task->task_project, $user_tasks_counted_in[$user_id])) {
						    $user_tasks_counted_in[$user_id][$task->task_project] = array();
						}
						
						if(!array_key_exists($task->task_id, $user_tasks_counted_in[$user_id][$task->task_project])){
						    $user_tasks_counted_in[$user_id][$task->task_project][$task->task_id] = 0;
						}
						// We add it up
						$user_tasks_counted_in[$user_id][$task->task_project][$task->task_id] += $hours_added;
					}
				}
				$actual_date->addDays(1);
			}
		}
	
		$days_difference = $end_date->dateDiff($start_date);
		$actual_date     = $start_date;
		$working_days_count = 0;
		$allocated_hours_sum = 0;
		
		$table_header = "<tr><th>".$AppUI->_("User")."</th>";
		for($i=0; $i<=$days_difference; $i++){
			$table_header .= "<th>".utf8_encode(Date_Calc::getWeekdayAbbrname($actual_date->day, $actual_date->month, $actual_date->year, 3))."<br><table><td style='font-weight:normal; font-size:70%'>".$actual_date->format( $df )."</td></table></th>";
			if($actual_date->isWorkingDay()){
				$working_days_count++;
			}
			$actual_date->addDays(1);
		}
		$table_header .= "<th nowrap='nowrap' colspan='2'>".$AppUI->_("Allocated")."</th></tr>";
		
		$table_rows = "";
		
		foreach($user_list as $user_id => $user_data){
		    @$user_names[$user_id] = $user_data["user_username"];
			if(isset($user_usage[$user_id])) {
				$table_rows .= "<tr><td nowrap='nowrap'>(".$user_data["user_username"].") ".$user_data["contact_first_name"]." ".$user_data["contact_last_name"]."</td>";
				$actual_date = $start_date;
				for($i=0; $i<=$days_difference; $i++){
					$table_rows .= "<td>";
					if(isset($user_usage[$user_id][$actual_date->format("%Y%m%d")])){
						$hours       = number_format($user_usage[$user_id][$actual_date->format("%Y%m%d")],2);
						$table_rows .= $hours;
						$percentage_used = round($hours/dPgetConfig("daily_working_hours")*100);
						$bar_color       = "blue";
						if($percentage_used > 100){
							$bar_color = "red";
							$percentage_used = 100;
						}
						$table_rows .= "<div style='height:2px;width:$percentage_used%; background-color:$bar_color'>&nbsp;</div>";
					} else {
						$table_rows .= "&nbsp;";
					} 
					$table_rows .= "</td>";
					$actual_date->addDays(1);
				}
				
				$array_sum = array_sum($user_usage[$user_id]);
				$average_user_usage = number_format( ($array_sum/($working_days_count*dPgetConfig("daily_working_hours")))*100, 2);
				$allocated_hours_sum += $array_sum;
				
				$bar_color = "blue";
				if($average_user_usage > 100){
					$bar_color = "red";
					$average_user_usage = 100;
				}
				$table_rows .= "<td ><div align='left'>".round($array_sum, 2)." ".$AppUI->_("hours")."</td> <td align='right'> ".$average_user_usage ;
				$table_rows .= "%</div>";
				$table_rows .= "<div align='left' style='height:2px;width:$average_user_usage%; background-color:$bar_color'>&nbsp;</div></td>";
				$table_rows .= "</tr>";
				
			}
		}
		?>
			<center><table class="std">
			<?php echo $table_header . $table_rows; ?>
			</table>
			<table width="100%"><tr><td align="center">
		<?php
			$total_hours_capacity = $working_days_count*dPgetConfig("daily_working_hours")*count($user_usage);
	
			echo '<h4>' . $AppUI->_("Total capacity for shown users") . '</h4>';
			echo $AppUI->_("Allocated hours").": ".number_format($allocated_hours_sum,2)."<br />";
			echo $AppUI->_("Total capacity").": ".number_format($total_hours_capacity,2)."<br />";
			echo $AppUI->_("Percentage used").": ".number_format($allocated_hours_sum/$total_hours_capacity,2)*100 ."%<br />";
	?>
			</td>
			<td align="center">
		<?php
			$total_hours_capacity = $working_days_count*dPgetConfig("daily_working_hours")*count($user_list);
	
			echo '<h4>' . $AppUI->_("Total capacity for all users") . '</h4>';
			echo $AppUI->_("Allocated hours").": ".number_format($allocated_hours_sum,2)."<br />";
			echo $AppUI->_("Total capacity").": ".number_format($total_hours_capacity,2)."<br />";
			echo $AppUI->_("Percentage used").": ".number_format($allocated_hours_sum/$total_hours_capacity,2)*100 ."%<br />";
	}		
	?>
	   </td></tr>
	   </table>
	   </center>
       <?php
           foreach($user_tasks_counted_in as $user_id => $project_information) {
               echo "<b>".$user_names[$user_id]."</b><br /><blockquote>";
               echo "<table width='50%' border='1' class='std'>";
               foreach ($project_information as $project_id => $task_information) {
                   echo "<tr><th colspan='3'><span style='font-weight:bold; font-size:110%'>".$fetched_projects[$project_id]."</span></th></tr>";
                   
                   $project_total = 0;
                   foreach($task_information as $task_id => $hours_assigned){
                       echo "<tr><td>&nbsp;</td><td>".$task_list_hash[$task_id]["task_name"]."</td><td>".round($hours_assigned,2)." hrs</td></tr>";
                       $project_total += round($hours_assigned,2);
                   }
                   echo "<tr><td colspan='2' align='right'><b>".$AppUI->_("Total assigned")."</b></td><td><b>$project_total hrs</b></td></tr>";
                   
               }
               echo "</table></blockquote>";
           }
       ?>
	<?php	
}
			?>		



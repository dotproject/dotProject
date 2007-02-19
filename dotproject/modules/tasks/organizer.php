<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

/*
 * Dynamic Tasks Organizer - by J. Christopher Pereira
 *
 * Consider:
 *	- order by priorities
 *	- other related persons time availability
 *
 * Constraints:
 *	- other tasks
 *	- task dependencies
 *
 */

$errors = false;
$tasks = array();
$actions = false;

$do = isset( $_REQUEST['do'] ) ? $_REQUEST['do'] : 'conf';
$set_duration = isset( $_REQUEST['set_duration'] ) ? $_REQUEST['set_duration'] : null;
$set_dynamic = isset( $_REQUEST['set_dynamic'] ) ? $_REQUEST['set_dynamic'] : null;

$df = $AppUI->getPref('SHDATEFORMAT');

$NO_DATE = "0000-00-00 00:00:00";

function task_link($task) {
	return "<a href='index.php?m=tasks&a=addedit&task_id=" . $task["task_id"] . "'>" . $task["task_name"] . "</a>";
}

function search_task($task_id) {
	global $tasks;
	for($i = 0; $i < count($tasks) ; $i++) {
		if($tasks[$i]["task_id"] == $task_id) return $i;
	}
	return -1;
}

function log_info($msg) {
	global $option_debug;
	if($option_debug) {
		echo "$msg<br />";
	}
}

function log_action($msg) {
	global $action;
	echo "&nbsp;&nbsp;<font color=red size=2>$msg</font><br />";
	$action = true;
}

function log_error($msg, $fields = "") {
	global $action;
	echo "<font color=red size=1>ERROR: $msg</font><br />$fields<hr>";
	$action = true;
}

function log_warning($msg, $fields = "") {
	global $show_warnings;
	echo "WARNING: $msg<br />$fields<hr>";
}

function fixate_task($task_index, $time, $dep_on_task) {

	// WARNING: task_index != task_id !!!

	global $tasks, $do, $option_advance_if_possible, $AppUI, $df;

	// don't fixate tasks before now

	if($time < time()) {
		$time = time();
	}
	
	$start_date = $time;
	$end_date = $start_date;
	$durn = convert2days( $tasks[$task_index]["task_duration"], $tasks[$task_index]["task_duration_type"] );
	$end_date += $durn * SECONDS_PER_DAY;
	
	// Complex SQL explanation:
	//
	// Objective: Check tasks overlapping only when
	// a user is vital for both tasks
	//
	// Definition of "vital for one task": when a task is assigned to user and total_users <= 2
	// (for example: if task is assigned to tree o more users, he is not vital).
	//
	// Thus, a user is vital for both tasks <=>
	//	- total_users <= 2 for both tasks
	//	- and he apears in both tasks
	//
	// Thus, in both tasks (say 4 and 10), a there will be a vital user <=>
	//	- "number of tasks with total_users <= 2"
	//	  = rows("select count(*) as num_users from user_tasks
	//	  where task_id=4 or task_id=10
	//	  group by task_id having num_users <= 2") == 2;
	//
	//	- and "number of users which appears in both tasks"
	//	  = rows("select count(*) as frec
	//	  from user_tasks where task_id=4 or task_id=10
	//	  group by user_id having frec = 2") > 0

	$t1_start = $start_date;
	$t1_end = $end_date;	

	foreach($tasks as $task2) {
		$t2_start = db_dateTime2unix( $task2["task_start_date"] );
		$t2_end = db_dateTime2unix( $task2["task_end_date"] );		
		
		if($task2["fixed"] && (
							   ($t1_start >= $t2_start && $t1_start <= $t2_end)
							   || ($t1_end >= $t2_start && $t1_end <= $t2_end))
		   ) {
			// tasks are overlapping

			if(!$option_advance_if_possible || $task2["task_percent_complete"] != 100) {

				$t1 = $tasks[$task_index]["task_id"];
				$t2 = $task2["task_id"];
				
				if ( $option_check_vital_users ) {					
					$sql1 = "select count(*) as num_users from user_tasks where task_id=$t1 or task_id=$t2 group by task_id having num_users <= 2";
					$sql2 = "select count(*) as frec from user_tasks where task_id=$t1 or task_id=$t2 group by user_id having frec = 2";
					$vital = mysql_num_rows(mysql_query($sql1)) == 2 && mysql_num_rows(mysql_query($sql2)) > 0;
				} else {
					$vital = true;
				}
				
				if($vital) {
					log_info("Task can't be set to [" . formatTime($start_date) . " - ". formatTime($end_date) . "] due to conflicts with task " . task_link($task2) . ".");
					// OBS: I'm asuming the dependent task will start next day
					fixate_task($task_index, $t2_end + SECONDS_PER_DAY, $dep_on_task);
					return;					
				} else {
					log_info("Task conflicts with task " . task_link($task2) . " but there are no vital users.");
				}
			} else {
				log_info("Task " . task_link($task2) . " is complete, I won't check if it is overllaping");
			}
		}
	}
	
	$tasks[$task_index]["fixed"] = true;

	// be quite if nothing changes

	if (db_dateTime2unix( $tasks[$task_index]["task_start_date"] ) == $start_date &&
		db_dateTime2unix( $tasks[$task_index]["task_end_date"] ) == $end_date ) {
		log_info("Nothing changed, still programmed for [" . formatTime($start_date) . " - " . formatTime($end_date) . "]");
		return;
	}	
	
	$tasks[$task_index]["task_start_date"] = db_unix2dateTime( $start_date );
	$tasks[$task_index]["task_end_date"] = db_unix2dateTime( $end_date );	

	if($do == "ask") {
		if($dep_on_task) {
			log_action("I will fixate task " . task_link($tasks[$task_index]) . " to " . formatTime($start_date) . " (depends on " .  task_link($dep_on_task) . ")");
		} else {
			log_action("I will fixate task " . task_link($tasks[$task_index]) . " to " . formatTime($start_date) . " (no dependencies)");
		}
		
		// echo "<input type=hidden name=fixate_task[" . $tasks[$task_index]["task_id"] . "] value=y>";
	} else if($do == "fixate") {
		log_action("Task " . task_link($tasks[$task_index]) . " fixated to " . formatTime($start_date) );
		$sql = "update tasks set task_start_date = '" . db_unix2dateTime($start_date) . "', task_end_date = '" .  db_unix2dateTime($end_date) . "' where task_id = " . $tasks[$task_index]["task_id"];
		mysql_query($sql);
	}
	
}

function get_last_children($task) {
	// returns the last children (leafs) from $task
	$arr = array();

	// query children from task
	$sql = "select * from tasks where task_parent=" . $task["task_id"];
	$query = mysql_query($sql);
	if(mysql_num_rows($query)) {
		// has children
		while($row = mysql_fetch_array($query)) {
			if($row["task_id"] != $task["task_id"]) {
				// add recursively children of children to $arr
				$sub = get_last_children($row);
				array_splice($arr, count($arr), 0, $sub);
			}
		}
	} else {
		// it's a leaf
		array_push($arr, $task);
	}
	return $arr;
}

function process_dependencies($i) {
	global $tasks, $option_advance_if_possible;

	if($tasks[$i]["fixed"]) return;  

	log_info("<div style='padding-left: 1em'>Dependecies for '" . $tasks[$i]["task_name"] . "':<br />");

	// query dependencies for this task

	$query = mysql_query("select tasks.* from tasks,task_dependencies where task_id=dependencies_req_task_id and dependencies_task_id=" . $tasks[$i]["task_id"]);

	if(mysql_num_rows($query) != 0) {
		$all_fixed = true;
		$latest_end_date = null;	   

		// store dependencies in an array (for adding more entries on the fly)

		$dependencies = array();
		while($row = mysql_fetch_array($query)) {
			array_push($dependencies, $row);
		}		

		$d = 0;
		
		while($d < count($dependencies)) {

			$row = $dependencies[$d];
			$index = search_task($row["task_id"]);

			if($index == -1) {
				// task is not listed => it's a task group
				// => $i depends on all its subtasks
				// => add all subtasks to the dependencies array

				log_info("- task '" . $row["task_name"] . "' is a task group (processing subtask's dependencies)");

				$children = get_last_children($row);
				// replace this taskgroup with all its subtasks

				array_splice($dependencies, $d, 1, $children);

				continue;
			}

			log_info(" - '" . $tasks[$index]["task_name"] . ($tasks[$index]["fixed"]?" (FIXED)":"") . "'");

			// TODO: Detect dependencies loops (A->B, B->C, C->A)

			process_dependencies($index);

			if(!$tasks[$index]["fixed"]) {
				$all_fixed = false;
			} else {
				// ignore dependencies of finished tasks if option is enabled
				if(!$option_advance_if_possible || $tasks[$index]["task_percent_complete"] != 100) {
					// get latest end_date
					$end_date = db_dateTime2unix( $tasks[$index]["task_end_date"] );

					if(!$latest_end_date || $end_date > $latest_end_date) {
						$latest_end_date = $end_date;
						$dep_on_task = $row;
					}
				} else {
					log_info("this task is complete => don't check dependency");
				}
				$d++;
			}
		}	   

		if($all_fixed) {
			// this task depends only on fixated tasks
			log_info("all dependencies are fixed");
			fixate_task($i, $latest_end_date, $dep_on_task);
		} else {
			log_error("task has not fixed dependencies");
		}

	} else {
		// task has no dependencies
		log_info("no dependencies => ");
		fixate_task($i, time(), "");
	}
	log_info("</div><br />\n");	
}
?>

<table name="table" cellspacing="1" cellpadding="1" border="0" width="100%">
<tr>
	<td><?php echo dPshowImage( dPfindImage( 'applet-48.png', $m ), 16, 16, '' ); ?></td>
	<td nowrap><h1><?php echo $AppUI->_('Tasks Organizer Wizard'); ?></h1></td>
	<td nowrap><img src="./images/shim.gif" width="16" height="16" alt="" border="0"></td>
	<td valign="top" align="right" width="100%"></td>
</tr>
</table>

<?php

/*** Process updates ***/

// update tasks duration
if($set_duration) {
	foreach($set_duration as $key=>$val) {
		if($val) {
			$sql = "update tasks set task_duration=" . ($val * $dayhour[$key]) . " where task_id=" . $key;
			mysql_query($sql);
		}
	}
	$do = "ask"; // ask again
}

if($set_dynamic) {
	foreach($set_dynamic as $key=>$val) {
		if($val) {
			$sql = "update tasks set task_dynamic=1 where task_id=$key";
			mysql_query($sql);
		}
	}
	$do = "ask";
}

?>

<form name="form" method="post">

<?php
if($do == "conf") {
	echo '<table border="0" cellpadding="4" cellspacing="0" width="100%" class="tbl">';
	echo '<tr>';
	echo '<td>';
}

function checkbox($name, $descr, $default = 0, $show = true) {
	global $AppUI;
	global $$name;
	if(!isset($$name)) $$name=$default;
	if($show) {
		echo "<input type=checkbox name=$name value=1 " . ($$name?"checked":"") . ">".$AppUI->_($descr)."<br />";
	} else {
		echo "<input type=hidden name=$name value=" . ($$name?"1":"") . ">";
	}
}

// pull projects
$sql = "SELECT project_id, project_name FROM projects ORDER BY project_name";
$projects = arrayMerge( array( 0 => '(' . $AppUI->_('All') . ')' ), db_loadHashList( $sql ) );

echo $AppUI->_('Project').": " . arraySelect( $projects, 'project_id', 'class="text"', $project_id ) . "<br>";

checkbox("option_check_delayed_tasks", "Check delays for fixed tasks", 1, $do == "conf");
checkbox("option_fix_task_group_date_ranges", "Fix date ranges for task groups according to subtasks dates", 1, $do == "conf");
checkbox("option_no_end_date_warning", "Warn of fixed tasks without end dates", 0, $do == "conf");
checkbox("option_advance_if_possible", "Begin new tasks if dependencies are finished before expected", 1, $do == "conf");
checkbox("option_check_vital_users", "Allow two concurrent tasks when there are no vital users", 1, $do == "conf");
checkbox("option_debug", "Show debug info", 0, $do == "conf");

if($do == "conf") { ?>
	</td>
</tr>
</table>
<br />
<?php }

if($do != "conf") {
	echo '<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">';
	echo '<tr>';
	echo '<td>';

	/**** Add tasks to an array and check conflicts ****/

	// Select tasks without children (sub tasks)

	$sql = "select a.*, !a.task_dynamic AS fixed FROM tasks AS a " .
	  "LEFT JOIN tasks AS b ON a.task_id = b.task_parent AND a.task_id != b.task_id " .
	  "WHERE (b.task_id IS NULL or b.task_id = b.task_parent) " .
	  "AND (a.task_project = $project_id) " .
	  "ORDER BY a.task_priority desc, a.task_order desc";	

	$dtrc = mysql_query( $sql );

	while ($row = mysql_fetch_array( $dtrc, MYSQL_ASSOC )) {
		
		// check durations
		
		// OBS: agregue if (task_end_date)

		if(!$row["task_duration"] && $row["task_end_date"] == $NO_DATE ) {
			log_error("Task " .task_link($row) . " has no duration.",
				"Please enter the expected duration: "
				."<input class=input type=text name='set_duration[" . $row["task_id"] . "]' size=3>"
				. "<select name='dayhour[" . $row["task_id"] . "]'>"
				. "<option value='1'>hour(s)</option>"
				. "<option value='24'>day(s)</option>"
				. "</select>"
			);
			$errors = true;
		}
		
		// calculate or set blank task_end_date if unset

		if(!$row["task_dynamic"] && $row["task_end_date"] == $NO_DATE ) {
			$end_date = new CDate( $row["task_start_date"] );
			$durn = convert2days( $row["task_duration"], $row["task_duration_type"] );
			$end_date->addDays( $durn );
			$row["task_end_date"] = $end_date->getDate();
			if($do=="ask" && $option_no_end_date_warning) {
				log_warning("Task " . task_link($row) . " has no end date. Using tasks duration instead.",
					"<input type=checkbox name='set_end_date[" . $row["task_id"] . "]' value=1> "
					."Set end date to " . $row["task_end_date"]
				);
			}
		}

		// check delayed tasks
		if($do == "ask") {
			if(!$row["task_dynamic"] && $row["task_percent_complete"] == 0) {
				// nothing has be done yet
				$end_time = new CDate( db_dateTime2unix( $row["task_end_date"] ) );
				if($end_time < time()) {
					if($option_check_delayed_tasks) {
						log_warning("Task " .task_link($row) . " started on " . $row["task_start_date"] . " and ended on " . formatTime($end_time) . "." ,
							"<input type=checkbox name=set_dynamic[" . $row["task_id"] . "] value=1 checked> Set as dynamic task and reorganize<br />" .
							"<input type=checkbox name=set_priority[" . $row["task_id"] . "] value=1 checked> Set priority to high<br />"
						);
					}
				}
			}
		}
		array_push($tasks, $row);
	}
	
	if(!$errors) {
		for($i = 0; $i < count($tasks) ; $i++) {
			process_dependencies($i);
		}
	}	

	if($option_fix_task_group_date_ranges) {
		// query taskgroups
		$sql = "select distinct a.* from tasks as a, tasks as b " .
		  "WHERE (b.task_parent = a.task_id and a.task_id != b.task_id) " .
		  " AND (a.task_project = $project_id AND b.task_project = $project_id)";
		$taskgroups = mysql_query($sql);
		while($tg = mysql_fetch_array($taskgroups)) {
			$children = get_last_children($tg);
			$min_time = null;
			$max_time = null;

			foreach($children as $child) {
				$start_time = db_dateTime2unix($child["task_start_date"]);
				$end_time = db_dateTime2unix($child["task_end_date"]);
				if (!$min_time || $start_time < $min_time) {
					$min_time = $start_time;
				}
				if (!$max_time || $end_time > $max_time) {
					$max_time = $end_time;
				}
			}		 

			if (db_dateTime2unix($tg["task_start_date"]) != $min_time
					|| db_dateTime2unix($tg["task_end_date"]) != $max_time) {
				if ($do == "ask") {
					log_action("I will set date of task group " . task_link($tg) . " to " . formatTime($min_time) . " - " . formatTime($max_time) . ".");
				} else if ($do == "fixate") {
					log_action("Date range of task group " . task_link($tg) . " changed to " . formatTime($min_time) . " - " . formatTime($max_time) . ".");
					mysql_query("update tasks set task_start_date='" .  db_unix2dateTime($min_time) . "', task_end_date='" .  db_unix2dateTime($max_time) . "' where task_id=" . $tg["task_id"]);
				}
			}
		}
	}
	
	if(!$action) {
		echo "<font size=2><strong>".$AppUI->_('Tasks are already organized')."</strong></font><br />";
	}

	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br />';
}

if ($do=="conf" || $action) {
	if(!$errors) {
		echo "<input type=hidden name=do value=" . ($do=="ask"?"fixate":"ask") . ">";
		if($do == "ask") {
			echo "<font size=2><strong>".$AppUI->_('Do you want to accept this changes?')."</strong></font><br />";
			echo "<input type=button value=accept class=button onClick='javascript:document.form.submit()'>";
		} else if ($do == "fixate") {
			echo "<font size=2><strong>".$AppUI->_('Tasks has been reorganized')."</strong></font><br />";
		} else if ($do == "conf") {
				echo "<input type=button value=".$AppUI->_('start')." class=button onClick='javascript:document.form.submit()'>";
		}
	} else {
		echo "<font size=2><strong>".$AppUI->_('Please correct the above errors')."</strong></font><br />";
		echo "<input type=button value=submit class=button onClick='javascript:document.form.submit()'>";
	}
}
?>

</form>

</body>
</html>


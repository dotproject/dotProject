<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$do_report 		    = dPgetParam($_POST, "do_report", 0);
$log_start_date 	= dPgetCleanParam($_POST, "log_start_date", 0);
$log_end_date 	    = dPgetCleanParam($_POST, "log_end_date", 0);
$user_id            = (int)dPgetParam($_POST, "user_id", $AppUI->user_id);

// create Date objects from the datetime fields
$start_date = intval($log_start_date) ? new CDate($log_start_date) : new CDate();
$end_date   = intval($log_end_date)   ? new CDate($log_end_date) : new CDate();

if (!$log_start_date) {
	$start_date->subtractSpan(new Date_Span("14,0,0,0"));
}
$end_date->setTime(23, 59, 59);
?>

<form name="editFrm" action="index.php?m=projects&a=reports" method="post">
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">


<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="date" name="log_start_date" value="<?php echo $start_date->format(FMT_DATE_HTML5);?>" class="text dpDateField">
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to');?></td>
	<td nowrap="nowrap">
		<input type="date" name="log_end_date" value="<?php echo $end_date ? $end_date->format(FMT_DATE_HTML5) : '';?>" class="text dpDateField">
	</td>

	<td nowrap='nowrap'>
	   <?php
	       echo arraySelect(dPgetUsers(), "user_id", "class='text'", $user_id);
	   ?>
	</td>
	
	<td align="right" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>

</table>
</form>

<?php
if ($do_report) {
    $projects_filter = '';
    if ($project_id != 0)
			$projects_filter = " and task_project = $project_id ";

    $user_filter     = "";
    
	$q = new DBQuery;
	$q->addTable('tasks','t');
	$q->addTable('users','u');
	$q->addTable('projects','p');
	$q->addQuery('t.*, p.project_name, u.user_username');
    if ($user_id > 0) {
	$q->addTable('user_tasks', 'ut');
        $user_filter  = " and ut.user_id = $user_id
                         and ut.task_id = t.task_id ";
    }
	$q->addWhere("task_end_date >= '".$start_date->format(FMT_DATETIME_MYSQL)
				."' and task_end_date <= '".$end_date->format(FMT_DATETIME_MYSQL)
				."' and p.project_id = t.task_project"
				." and t.task_dynamic = '0'"
				.' and t.task_owner = u.user_id'
				. $projects_filter
				. $user_filter);
	$q->addOrder('project_name asc, task_end_date asc');

    $tasks = $q->loadHashList("task_id");
    $first_task = current($tasks);
    $actual_project_id = 0;
    $first_task        = true;
    $task_log          = array();
    
    echo "<table class='tbl' width='80%' summary='task log'>";
    echo "<tr><th>".$AppUI->_("Task name")."</th><th>".$AppUI->_("T.Owner")."</th><th>".$AppUI->_("H.Alloc.")."</th><th>".$AppUI->_("Task end date")."</th><th>".$AppUI->_("Last activity date")."</th><th>".$AppUI->_("Done")."?</th></tr>";
    $hrs = $AppUI->_("hrs"); // To avoid calling $AppUI each row
    foreach ($tasks as $task) {
        if ($actual_project_id != $task["task_project"]) {
            echo "<tr><td colspan='6'><b>".$task["project_name"]."</b></td>";
            $actual_project_id = $task["task_project"];
        }
		if (!($q instanceof DBQuery)) {
			//only create if wasn't already present as it may have been created above
			$q = new DBQuery;
		}
		$q->addTable('task_log');
		$q->addQuery('*');
		$q->addWhere('task_log_task = '.$task['task_id']);
		$q->addOrder('task_log_date desc');
		$q->setLimit('1');
		$task_log = $q->loadHash();
        
        $done_img = $task["task_percent_complete"] == 100 ? "Yes" : "No";
        echo "<tr><td>&nbsp;&nbsp;&nbsp;".$task["task_name"]."</td><td>".$task["user_username"]."</td><td>".($task["task_duration"]*$task["task_duration_type"])." $hrs</td><td>".$task["task_end_date"]."</td><td>".$task_log["task_log_date"]."</td><td align='center'>$done_img</td></tr>";
    }
}
?>		


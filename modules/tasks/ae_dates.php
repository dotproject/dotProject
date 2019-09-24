<?php // $Id$
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI, $dPconfig, $task_parent_options, $loadFromTab;
global $can_edit_time_information, $locale_char_set, $obj;
global $durnTypes, $task_project, $task_id, $tab;
global $project, $task_parent;

//Time arrays for selects
$start = intval(dPgetConfig('cal_day_start', 8));
$end   = intval(dPgetConfig('cal_day_end', 17));
$inc   = intval(dPgetConfig('cal_day_increment', 15));
$hours = array();
for ($current = $start; $current <= $end; $current++) {
	
	$current_key = (($current < 10) ? '0' : '') . $current;
	
	if (mb_stristr($AppUI->getPref('TIMEFORMAT'), '%p')) {
		//User time format in 12hr
		$hours[$current_key] = (($current > 12) ? $current-12 : $current);
	} else {
		//User time format in 24hr
		$hours[$current_key] = $current_key;
	}
}

$minutes = array();
for ($current = 0; $current < 60; $current += $inc) {
	$current = (($current < 10) ? '0' : '') . $current;
	$minutes[$current] = $current;
}

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

if ($task_id == 0) {
	$taskParentObj = new CTask();
	if ($task_parent > 0) {
		$taskParentObj->load($task_parent);
	}
}

if (intval($obj->task_start_date)) {
	$start_date = new CDate($obj->task_start_date);
// For a new task, calculate the best start date to suggest
} elseif ($task_id == 0) {
	// Inherit task parent's start date if there is one
	if (intval($taskParentObj->task_start_date)) {
		$start_date = new CDate($taskParentObj->task_start_date);
	// Inherit project's start date if there is one
	} elseif (intval($project->project_start_date)) {
		$start_date = new CDate($project->project_start_date);
		if ($start_date < new CDate()) {
			$start_date = new CDate();
		}
	// Fallback to today's date
	} else {
		$start_date = new CDate();
	}
} else {
	$start_date = null;
}

// Calculate end date
if (intval($obj->task_end_date)) {
	$end_date = new CDate($obj->task_end_date);
// For a new task, calculate the best end date to suggest
} elseif ($task_id == 0) {
	// Inherit task parent's end date if there is one
	if (intval($taskParentObj->task_end_date)) {
		$end_date = new CDate($taskParentObj->task_end_date);
	// Inherit project's end date if there is one
	} elseif (intval($project->project_end_date)) {
		$end_date = new CDate($project->project_end_date);
	} else {
		$end_date = null;
	}
} else {
	$end_date = null;
}

// convert the numeric calendar_working_days config array value to a human readable output format
$cwd = explode(',', $dPconfig['cal_working_days']);

$cwd_conv = array_map('cal_work_day_conv', $cwd);
$cwd_hr = implode(', ', $cwd_conv);

function cal_work_day_conv($val) {
	global $locale_char_set, $AppUI;
	$AppUI->setBaseLocale();
	$wk = Date_Calc::getCalendarWeek(null, null, null, "%a", LOCALE_FIRST_DAY);
	setlocale(LC_ALL, $AppUI->user_lang);
	
	$day_name = $wk[($val - LOCALE_FIRST_DAY)%7];
	if ($locale_char_set == "utf-8" && function_exists("utf8_encode")) {
	    $day_name = utf8_encode($day_name);
	}
	return htmlentities($day_name, ENT_COMPAT, $locale_char_set);
}
?>
<form name="datesFrm" action="?m=tasks&a=addedit&task_project=<?php echo $task_project;?>" method="post">
<input name="dosql" type="hidden" value="do_task_aed" />
<input name="task_id" type="hidden" value="<?php echo $task_id;?>" />
<input name="sub_form" type="hidden" value="1" />
<table width="100%" border="0" cellpadding="4" cellspacing="0" class="std">
<?php
if ($can_edit_time_information) {
?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Start Date');?></td>
	<td nowrap="nowrap">
		<input type="datetime-local" name="task_start_date" id="task_start_date" value="<?php 
	echo (($start_date) ? $start_date->format(FMT_DATETIME_HTML5) : ''); ?>" class="dpDateField text">
		
		
	</td>
	
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Finish Date');?></td>

	<td nowrap="nowrap">
		<input type="datetime-local" name="task_end_date" id="task_end_date" value="<?php 
	echo $end_date ? $end_date->format(FMT_DATETIME_HTML5) : '';?>" class="dpDateField text">
		
		
	</td>
       
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Expected Duration');?>:</td>
	<td nowrap="nowrap">
		<input type="text" class="text" name="task_duration" maxlength="8" size="6" value="<?php echo isset($obj->task_duration) ? $obj->task_duration : 1;?>" />
	<?php
	echo arraySelect($durnTypes, 'task_duration_type', 'class="text"', 
					 $obj->task_duration_type, true);
?>
	</td>
	<td><?php echo $AppUI->_('Daily Working Hours').': '.$dPconfig['daily_working_hours']; ?></td>

</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Calculate');?>:</td>
	<td nowrap="nowrap">
		<input type="button" value="<?php 
	echo $AppUI->_('Duration');?>" onclick="javascript:calcDuration(document.datesFrm)" class="button" />
		<input type="button" value="<?php 
	echo $AppUI->_('Finish Date');?>" onclick="javascript:calcFinish(document.datesFrm)" class="button" />
	</td>
	<td><?php echo $AppUI->_('Working Days').': '.$cwd_hr; ?></td>
</tr>
        <?php
} else {
?>
<tr>
	<td colspan='2'><?php 
	echo $AppUI->_('Only the task owner, project owner, or system administrator' 
				   . ' is able to edit time related information.'); ?>
        </td>
</tr>
<?php
} // end of can_edit_time_information
?>
</table>
</form>
<script language="javascript" >
 subForm.push(new FormDefinition(<?php echo $currentTabId;?>, document.datesFrm, checkDates, saveDates));
</script>

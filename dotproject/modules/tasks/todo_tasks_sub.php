<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $showEditCheckbox, $tasks, $priorities;
global $m, $a, $date, $other_users, $showPinned, $showArcProjs;
global $showHoldProjs, $showDynTasks, $showLowTasks, $showEmptyDate, $user_id;
global $task_sort_item1, $task_sort_type1, $task_sort_order1;
global $task_sort_item2, $task_sort_type2, $task_sort_order2;

$canDelete = getPermission($m, 'delete');
$q = new DBQuery();
?>


<form name="form_buttons" method="post" action="index.php?<?php 
echo "m=$m&a=$a&date=$date"; ?>">
<input type="hidden" name="show_form" value="1" />
<table width="100%" border="0" cellpadding="1" cellspacing="0">

  <tr>
	<td width="50%">
<?php
if ($other_users) {
	echo ($AppUI->_("Show Todo for:") 
	      . '<select name="show_user_todo" onchange="document.form_buttons.submit()">');
	
	$q->addTable('users', 'u');
	$q->innerJoin('contacts', 'c', 'c.contact_id = u.user_contact');
	$q->addQuery('u.user_id, u.user_username, c.contact_first_name, c.contact_last_name');
	$q->addOrder('c.contact_last_name');
	$usersql = $q->prepare(true);
	
	if (($rows = db_loadList($usersql))) {
		foreach ($rows as $row) {
		  echo ('<option value="' . $row['user_id'] . '"' 
				. (($user_id == $row["user_id"]) ? ' selected="selected"' : '') . ' />' 
				. $row['contact_last_name'].', ' . $row["contact_first_name"]) ;
		}
	}
}
	?>
	  </select>
	</td>
	<td align="right" width="50%"><?php echo $AppUI->_('Show'); ?>:</td>
	<td>
	  <input type="checkbox" name="show_pinned" id="show_pinned" onclick="javascript:document.form_buttons.submit()"<?php echo (($showPinned) ? 'checked="checked"' : ''); ?> />
	</td>
	<td nowrap="nowrap">
	  <label for="show_pinned"><?php echo $AppUI->_('Pinned Only'); ?></label>
	</td>
	<td>
	  <input type="checkbox" name="show_arc_proj" id="show_arc_proj" onclick="javascript:document.form_buttons.submit()" <?php echo (($$showArcProjs) ? 'checked="checked"' : ''); ?> />
	</td>
	<td nowrap="nowrap">
	  <label for="show_arc_proj"><?php echo $AppUI->_('Archived Projects'); ?></label>
	</td>
	<td>
	  <input type="checkbox" name="show_hold_proj" id="show_hold_proj" onclick="javascript:document.form_buttons.submit()" <?php echo (($$showHoldProjs) ? 'checked="checked"' : ''); ?> />
	</td>
	<td nowrap="nowrap">
	  <label for="show_hold_proj"><?php echo $AppUI->_('Projects on Hold'); ?></label>
	</td>
	<td>
	  <input type="checkbox" name="show_dyn_task" id="show_dyn_task" onclick="javascript:document.form_buttons.submit()" <?php echo (($$showDynTasks) ? 'checked="checked"' : ''); ?> />
	</td>
	<td nowrap="nowrap">
	  <label for="show_dyn_task"><?php echo $AppUI->_('Dynamic Tasks'); ?></label>
	</td>
	<td>
	  <input type="checkbox" name="show_low_task" id="show_low_task" onclick="javascript:document.form_buttons.submit()" <?php echo (($$showLowTasks) ? 'checked="checked"' : ''); ?> />
	</td>
	<td nowrap="nowrap">
	  <label for="show_low_task"><?php echo $AppUI->_('Low Priority Tasks'); ?></label>
	</td>
	<td>
		<input type="checkbox" name="show_empty_date" id="show_empty_date" onclick="javascript:document.form_buttons.submit()" <?php echo (($$showEmptyDate) ? 'checked="checked"' : ''); ?> />
	</td>
	<td nowrap="nowrap">
		<label for="show_empty_date"><?php echo $AppUI->_('Empty Dates'); ?></label>
	</td>
</tr>
</table>
</form>

<form name="form" method="post" action="index.php?<?php echo "m=$m&amp;a=$a&amp;date=$date"; ?>">
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th width='10'>&nbsp;</th>
	<th width='10'><?php echo $AppUI->_('Pin'); ?></th>
	<th width="20" colspan="2"><?php echo $AppUI->_('Progress'); ?></th>
	<th width="15" align="center"><?php 
sort_by_item_title('P', 'task_priority', SORT_NUMERIC); ?></th>
	<th colspan="2"><?php 
sort_by_item_title('Task / Project', 'task_name', SORT_STRING); ?></th>
	<th nowrap><?php 
sort_by_item_title('Start Date', 'task_start_date', SORT_NUMERIC); ?></th>
	<th nowrap><?php sort_by_item_title('Duration', 'task_duration', SORT_NUMERIC); ?></th>
	<th nowrap><?php 
sort_by_item_title('Finish Date', 'task_end_date', SORT_NUMERIC); ?></th>
	<th nowrap><?php sort_by_item_title('Due In', 'task_due_in', SORT_NUMERIC); ?></th>
	<?php if (dPgetConfig('direct_edit_assignment')) { ?><th width="0">&nbsp;</th><?php } ?>
</tr>

<?php

/*** Tasks listing ***/
$now = new CDate();
$df = $AppUI->getPref('SHDATEFORMAT');

// generate the 'due in' value
foreach ($tasks as $tId=>$task) {
	$start = intval(@$task["task_start_date"]) ? new CDate($task["task_start_date"]) : null;
	$end = intval(@$task["task_end_date"]) ? new CDate($task["task_end_date"]) : null;
	
	if (!$end && $start) {
		$end = $start;
		$end->addSeconds(@$task["task_duration"]*$task["task_duration_type"]*SEC_HOUR);
	}
	
	$days = (($end) ? $end->dateDiff($now) : null);
	$tasks[$tId]['task_due_in'] = $days;

}

// sorting tasks
if ($task_sort_item1 != '') {
	if ($task_sort_item2 != '' && $task_sort_item1 != $task_sort_item2) {
		$tasks = array_csort($tasks, $task_sort_item1, $task_sort_order1, $task_sort_type1, 
							 $task_sort_item2, $task_sort_order2, $task_sort_type2);
	} else {
		$tasks = array_csort($tasks, $task_sort_item1, $task_sort_order1, $task_sort_type1);
	}
}else { // All this appears to already be handled in todo.php ... should consider deleting this else block
	/* we have to calculate the end_date via start_date+duration for 
	 ** end='0000-00-00 00:00:00' if array_csort function is not used
	 ** as it is normally done in array_csort function in order to economise
	 ** cpu time as we have to go through the array there anyway
	 */
	for ($j=0, $xj = count($tasks); $j < $xj; $j++) {	
		if ($tasks[$j]['task_end_date'] == '0000-00-00 00:00:00' 
		    || $tasks[$j]['task_end_date'] == '') {
			if ($tasks[$j]['task_start_date'] == '0000-00-00 00:00:00' 
			    || $tasks[$j]['task_start_date'] == '') {
				//just to be sure start date is "zeroed"
				$tasks[$j]['task_start_date'] = '0000-00-00 00:00:00'; 
				$tasks[$j]['task_end_date'] = '0000-00-00 00:00:00';
			} else {
				$tasks[$j]['task_end_date'] = calcEndByStartAndDuration($tasks[$j]);
			}
		}
	}
}

// showing tasks
foreach ($tasks as $task) {
	showtask($task, 0, false, true);
}
if (dPgetConfig('direct_edit_assignment')) {
?>
<tr>
	<td colspan="9" align="right" height="30">
		<input type="submit" class="button" value="<?php echo $AppUI->_('update task'); ?>" />
	</td>
	<td colspan="3" align="center">
<?php
	foreach ($priorities as $k => $v) {
		$options[$k] = $AppUI->_('set priority to ' . $v, UI_OUTPUT_RAW);
	}
	$options['c'] = $AppUI->_('mark as finished', UI_OUTPUT_RAW);
	if ($canDelete) {
		$options['d'] = $AppUI->_('delete', UI_OUTPUT_RAW);
	}
	echo arraySelect($options, 'task_priority', 'size="1" class="text"', '0');
}
?>
	</td>
</table>
</form>
<table>
<tr>
  <td><?php echo $AppUI->_('Key'); ?>:&nbsp;&nbsp;</td>
  <td style="background-color:#FFFFFF; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Future Task'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#E6EEDD; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Started and on time'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#FFEEBB; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Should have started'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#CC6666; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Overdue'); ?>&nbsp;&nbsp;</td>
</tr>
</table>

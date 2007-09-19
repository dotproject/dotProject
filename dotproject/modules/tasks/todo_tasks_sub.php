<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

global $showEditCheckbox, $tasks, $priorities;
GLOBAL $m, $a, $date, $other_users, $showPinned, $showArcProjs, $showHoldProjs, $showDynTasks, $showLowTasks, $showEmptyDate, $user_id;
GLOBAL $task_sort_item1, $task_sort_type1, $task_sort_order1;
GLOBAL $task_sort_item2, $task_sort_type2, $task_sort_order2;
$perms =& $AppUI->acl();
$canDelete = $perms->checkModuleItem($m, 'delete');
?>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<form name="form_buttons" method="post" action="index.php?<?php echo "m=$m&a=$a&date=$date";?>">
<input type="hidden" name="show_form" value="1" />

<tr>
	<td width="50%">
	<?php
	if ($other_users) {
		echo $AppUI->_("Show Todo for:").'<select name="show_user_todo" onchange="document.form_buttons.submit()">';

                $usersql = "
                SELECT user_id, user_username, contact_first_name, contact_last_name
                FROM users, contacts
                WHERE user_contact = contact_id
		ORDER BY contact_last_name
                ";

		
                if (($rows = db_loadList( $usersql, NULL )))
                {
                        foreach ($rows as $row)
                        {
                                if ( $user_id == $row["user_id"])
                                        echo "<OPTION VALUE='".$row["user_id"]."' SELECTED>".$row["contact_last_name"].', '.$row["contact_first_name"];
                                else
                                        echo "<OPTION VALUE='".$row["user_id"]."'>".$row["contact_last_name"].', '.$row["contact_first_name"];
			                  }
							  }
	}
	?>
		</select>
	</td>
	<td align="right" width="50%">
		<?php echo $AppUI->_('Show'); ?>:
	</td>
	<td>
		<input type="checkbox" name="show_pinned" id="show_pinned" onclick="document.form_buttons.submit()" <?php echo $showPinned ? 'checked="checked"' : ''; ?> />
	</td>
	<td nowrap="nowrap">
		<label for="show_pinned"><?php echo $AppUI->_('Pinned Only'); ?></label>
	</td>
	<td>
		<input type="checkbox" name="show_arc_proj" id="show_arc_proj" onclick="document.form_buttons.submit()" <?php echo $showArcProjs ? 'checked="checked"' : ''; ?> />
	</td>
	<td nowrap="nowrap">
		<label for="show_arc_proj"><?php echo $AppUI->_('Archived Projects'); ?></label>
	</td>
	<td>
		<input type="checkbox" name="show_hold_proj" id="show_hold_proj" onclick="document.form_buttons.submit()" <?php echo $showHoldProjs ? 'checked="checked"' : ''; ?> />
	</td>
    <td nowrap="nowrap">
		<label for="show_hold_proj"><?php echo $AppUI->_('Projects on Hold'); ?></label>
	</td>
	<td>
		<input type="checkbox" name="show_dyn_task" id="show_dyn_task" onclick="document.form_buttons.submit()" <?php echo $showDynTasks ? 'checked="checked"' : ""; ?> />
	</td>
	<td nowrap="nowrap">
		<label for="show_dyn_task"><?php echo $AppUI->_('Dynamic Tasks'); ?></label>
	</td>
	<td>
		<input type="checkbox" name="show_low_task" id="show_low_task" onclick="document.form_buttons.submit()" <?php echo $showLowTasks ? 'checked="checked"' : ''; ?> />
	</td>
	<td nowrap="nowrap">
		<label for="show_low_task"><?php echo $AppUI->_('Low Priority Tasks'); ?></label>
	</td>
	<td>
		<input type="checkbox" name="show_empty_date" id="show_empty_date" onclick="document.form_buttons.submit()" <?php echo $showEmptyDate ? 'checked="checked"' : ''; ?> />
	</td>
	<td nowrap="nowrap">
		<label for="show_empty_date"><?php echo $AppUI->_('Empty Dates'); ?></label>
	</td>
</tr>
</form>
</table>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<form name="form" method="post" action="index.php?<?php echo "m=$m&a=$a&date=$date";?>">
<tr>
	<th width='10'>&nbsp;</th>
	<th width='10'><?php echo $AppUI->_('Pin'); ?></th>
	<th width="20" colspan="2"><?php echo $AppUI->_('Progress');?></th>
	<th width="15" align="center"><?php sort_by_item_title( 'P', 'task_priority', SORT_NUMERIC, '&a=todo' ); ?></th>
	<th colspan="2"><?php sort_by_item_title( 'Task / Project', 'task_name', SORT_STRING, '&a=todo' );?></th>
	<th nowrap><?php sort_by_item_title( 'Start Date', 'task_start_date', SORT_NUMERIC , '&a=todo');?></th>
	<th nowrap><?php sort_by_item_title( 'Duration', 'task_duration', SORT_NUMERIC, '&a=todo' );?></th>
	<th nowrap><?php sort_by_item_title( 'Finish Date', 'task_end_date', SORT_NUMERIC , '&a=todo');?></th>
	<th nowrap><?php sort_by_item_title( 'Due In', 'task_due_in', SORT_NUMERIC , '&a=todo');?></th>
	<?php if (dPgetConfig('direct_edit_assignment')) { ?><th width="0">&nbsp;</th><?php } ?>
</tr>

<?php

/*** Tasks listing ***/
$now = new CDate();
$df = $AppUI->getPref('SHDATEFORMAT');

// generate the 'due in' value
foreach ($tasks as $tId=>$task) {
	$sign = 1;
	$start = intval( @$task["task_start_date"] ) ? new CDate( $task["task_start_date"] ) : null;
	$end = intval( @$task["task_end_date"] ) ? new CDate( $task["task_end_date"] ) : null;
	
	if (!$end && $start) {
		$end = $start;
		$end->addSeconds( @$task["task_duration"]*$task["task_duration_type"]*SEC_HOUR );
	}

	if ($end && $now->after( $end )) {
		$sign = -1;
	} 

	$days = $end ? $now->dateDiff( $end ) * $sign : null;
	$tasks[$tId]['task_due_in'] = $days;

}

// sorting tasks
if ( $task_sort_item1 != "" ) {
    if ( $task_sort_item2 != "" && $task_sort_item1 != $task_sort_item2 )
        $tasks = array_csort($tasks, $task_sort_item1, $task_sort_order1, $task_sort_type1
                                  , $task_sort_item2, $task_sort_order2, $task_sort_type2 );
    else $tasks = array_csort($tasks, $task_sort_item1, $task_sort_order1, $task_sort_type1 );
} 
else {
    /* we have to calculate the end_date via start_date+duration for 
      ** end='0000-00-00 00:00:00' if array_csort function is not used
      ** as it is normally done in array_csort function in order to economise
      ** cpu time as we have to go through the array there anyway
      */
    for ($j=0; $j < count($tasks); $j++) {
        if ( $tasks[$j]['task_end_date'] == '0000-00-00 00:00:00' ) {
            $tasks[$j]['task_end_date'] = calcEndByStartAndDuration($tasks[$j]);
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
		<input type="submit" class="button" value="<?php echo $AppUI->_('update task');?>">
	</td>
	<td colspan="3" align="center">
<?php
	foreach($priorities as $k => $v) {
		$options[$k] = $AppUI->_('set priority to ' . $v, UI_OUTPUT_RAW);
	}
	$options['c'] = $AppUI->_('mark as finished', UI_OUTPUT_RAW);
	if ($canDelete) 
	{
		$options['d'] = $AppUI->_('delete', UI_OUTPUT_RAW);
	}
	echo arraySelect( $options, 'task_priority', 'size="1" class="text"', '0' );
}
?>
	</td>
</form>
</table>
<table>
<tr>
	<td><?php echo $AppUI->_('Key');?>:</td>
	<td>&nbsp; &nbsp;</td>
	<td bgcolor="#ffffff">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Future Task');?></td>
	<td>&nbsp; &nbsp;</td>
	<td bgcolor="#CCECAC">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Started and on time');?></td>
	<td bgcolor="#FFDD88">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Should have started');?></td>
	<td>&nbsp; &nbsp;</td>
	<td bgcolor="#CC6666">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Overdue');?></td>
</tr>
</table>

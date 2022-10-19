<?php

if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

include_once ($AppUI->getLibraryClass('quilljs/richedit.class'));

// $Id$
global $AppUI, $users, $task_id, $task_project, $obj, $projTasksWithEndDates, $tab, $loadFromTab;

// Make sure that we can see users that are allocated to the task.
$q = new DBQuery;
if ($task_id == 0) {
	// Add task creator to assigned users by default
	$assigned_perc = array($AppUI->user_id => array('contact_name' => $users[$AppUI->user_id], 'perc_assignment' => '100'));
} else {
	// Pull users on this task
	$q->addQuery("ut.user_id, perc_assignment, concat_ws(', ', contact_last_name, contact_first_name) as contact_name");
	$q->addTable('user_tasks', 'ut');
	$q->leftJoin('users', 'u', array('user_id'));
	$q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
	$q->addWhere('task_id ='.$task_id.' AND task_id <> 0');
	$assigned_perc = db_loadHashList($q->prepare(true), 'user_id');
}

$initPercAsignment = "";
$assigned = array();
foreach ($assigned_perc as $user_id => $data) {
	$assigned[$user_id] = $data['contact_name'] . " [" . $data['perc_assignment'] . "%]";
	$initPercAsignment .= "$user_id=" . $data['perc_assignment'] . ";";
}

?>
<script language="javascript">
<?php
echo "var projTasksWithEndDates=new Array();\n";  // this is UGLY and breaks JS parsing (gwyneth 20210424)
$keys = array_keys($projTasksWithEndDates);
for ($i = 1, $xi = sizeof($keys); $i < $xi; $i++) {
	//array[task_is] = end_date, end_hour, end_minutes
	echo ('projTasksWithEndDates[' . $keys[$i] . ']=new Array("'
	      . $projTasksWithEndDates[$keys[$i]][1] . '", "'
	      . $projTasksWithEndDates[$keys[$i]][2] . '", "'
	      . $projTasksWithEndDates[$keys[$i]][3] ."\");\n");
}
?>
</script>
<form action="?m=tasks&a=addedit&task_project=<?php echo $task_project; ?>"
  method="post" name="resourceFrm">
<input type="hidden" name="sub_form" value="1" />
<input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
<input type="hidden" name="dosql" value="do_task_aed" />
	<input name="hperc_assign" type="hidden" value="<?php echo
	$initPercAsignment;?>"/>
<table width="100%" border="1" cellpadding="4" cellspacing="0" class="std">
<tr>
	<td valign="top" align="center">
		<table cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td><?php echo $AppUI->_('Human Resources');?>:</td>
				<td><?php echo $AppUI->_('Assigned to Task');?>:</td>
			</tr>
			<tr>
				<td>
					<?php echo arraySelect($users, 'resources', 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
				</td>
				<td>
					<?php echo arraySelect($assigned, 'assigned', 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<table>
					<tr>
						<td align="right"><input type="button" class="button" value="&gt;" onclick="javascript:addUser(document.resourceFrm)" /></td>
						<td>
							<select name="percentage_assignment" class="text">
							<?php
								for ($i = 5; $i <= 100; $i+=5) {
									echo ('<option ' . (($i==100) ? 'selected="true"' : '')
									      . ' value="' . $i . '">' . $i . '%</option>');
								}
							?>
							</select>
						</td>
						<td align="left"><input type="button" class="button" value="&lt;" onclick="javascript:removeUser(document.resourceFrm)" /></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
	<td valign="top" align="center">
		<table><tr><td align="left">
		<?php echo $AppUI->_('Additional Email Comments');?>:
		<br />
		<!-- <textarea name="email_comment" class="textarea" cols="60" rows="10" wrap="virtual"></textarea> -->
    <?php
      $richedit = new DpRichEdit('email_comment', '');  // where is the content?... (gwyneth 20210426)
      $richedit->render();
    ?>
    <br />
		<input type="checkbox" name="task_notify" id="task_notify" value="1"<?php if ($obj->task_notify != 0) { echo ' checked="checked"'; } ?> /> <label for="task_notify"><?php echo $AppUI->_('notifyChange'); ?></label>
		</td></tr></table><br />

	</td>
</tr>
</table>
<input type="hidden" name="hassign" />
</form>
<script language="javascript">
  subForm.push(new FormDefinition(<?php echo $currentTabId; ?>, document.resourceFrm, checkResource, saveResource));
</script>

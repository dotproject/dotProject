<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

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
    $q->addWhere('task_id =' . $task_id . ' AND task_id <> 0');
    $assigned_perc = db_loadHashList($q->prepare(true), 'user_id');
}

$initPercAsignment = "";
$assigned = array();
foreach ($assigned_perc as $user_id => $data) {
    $assigned[$user_id] = $data['contact_name'] . " [" . $data['perc_assignment'] . "%]";
    $initPercAsignment .= "$user_id={$data['perc_assignment']};";
}
?>
<script language="javascript">
<?php
echo "var projTasksWithEndDates=new Array();\n";
$keys = array_keys($projTasksWithEndDates);
for ($i = 1, $xi = sizeof($keys); $i < $xi; $i++) {
    //array[task_is] = end_date, end_hour, end_minutes
    echo ('projTasksWithEndDates[' . $keys[$i] . ']=new Array("'
    . $projTasksWithEndDates[$keys[$i]][1] . '", "'
    . $projTasksWithEndDates[$keys[$i]][2] . '", "'
    . $projTasksWithEndDates[$keys[$i]][3] . "\");\n");
}
?>
</script>
<form name="resourceFrm">
    <input type="hidden" name="sub_form" value="1" />
    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
    <input name="hperc_assign" type="hidden" value="<?php echo $initPercAsignment; ?>"/>

    <table width="100%" border="1" cellpadding="4" cellspacing="0" class="std">
        <tr>
            <td valign="top" align="left">
                <table cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td><?php echo $AppUI->_('Assigned to Task'); ?>:</td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo arraySelect($assigned, 'assigned', 'style="width:220px" size="10" class="text" multiple="multiple" ', null); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
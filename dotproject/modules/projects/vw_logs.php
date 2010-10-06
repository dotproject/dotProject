<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $AppUI, $project_id, $df, $canEdit, $m, $tab;
$q = new DBQuery;

// Lets check which cost codes have been used before
$q->addQuery('project_company');
$q->addTable('projects');
$q->addWhere('project_id = ' . $project_id);
$company_id = $q->loadResult();
$q->clear();

$q->addTable('billingcode');
$q->addQuery('billingcode_id, billingcode_name');
$q->addOrder('billingcode_name');
$q->addWhere('billingcode_status = 0');
$q->addWhere('(company_id = 0 OR company_id = ' . $company_id . ')');
$task_log_costcodes = array(0 => '') + $q->loadHashList();
$q->clear();


// Show deleted codes separately (at the end)
$q->addTable('billingcode');
$q->addQuery('billingcode_id, billingcode_name');
$q->addOrder('billingcode_name');
$q->addWhere('billingcode_status = 1');
$q->addWhere('(company_id = 0 OR company_id = ' . $company_id . ')');
$task_log_costcodes = $task_log_costcodes + $q->loadHashList();
$q->clear();

$q->addTable('users');
$q->addQuery("user_id, concat(contact_first_name,' ',contact_last_name)");
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addOrder('contact_first_name, contact_last_name');
$users = array('-1' => $AppUI->_('All Users')) + $q->loadHashList();
$q->clear();

$cost_code = dPgetParam($_GET, 'cost_code', '0');

if (isset($_GET['user_id'])) {
	$AppUI->setState('ProjectsTaskLogsUserFilter', $_GET['user_id']);
}
$user_id = $AppUI->getState('ProjectsTaskLogsUserFilter') ? $AppUI->getState('ProjectsTaskLogsUserFilter') : $AppUI->user_id;

$AppUI->setState('ProjectsTaskLogsHideArchived', (isset($_GET['hide_inactive']) ? true : false));
$hide_inactive = $AppUI->getState('ProjectsTaskLogsHideArchived');

$AppUI->setState('ProjectsTaskLogsHideComplete', (isset($_GET['hide_complete']) ? true : false));
$hide_complete = $AppUI->getState('ProjectsTaskLogsHideComplete');
	
?>
<script language="JavaScript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function delIt2(id) {
	if (confirm("<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Task Log', UI_OUTPUT_JS).'?';?>")) {
		document.frmDelete2.task_log_id.value = id;
		document.frmDelete2.submit();
	}
}
<?php } ?>
</script>
<table border="0" cellpadding="2" cellspacing="1" width="100%" class="std">
<form name="frmFilter" action="./index.php" method="get">
<tr>
	<td width="98%">&nbsp;</td>
	<td width="1%" nowrap="nowrap"><input type="checkbox" name="hide_inactive" id="hide_inactive" <?php echo $hide_inactive?'checked="checked"':''?> onchange="document.frmFilter.submit()"><label for="hide_inactive"><?php echo $AppUI->_('Hide Inactive')?></label></td>
	<td width="1%" nowrap="nowrap"><input type="checkbox" name="hide_complete" id="hide_complete" <?php echo $hide_complete?'checked="checked"':''?> onchange="document.frmFilter.submit()"><label for="hide_complete"><?php echo $AppUI->_('Hide 100% Complete')?></label></td>
	<td width="1%" nowrap="nowrap"><?php echo $AppUI->_('User Filter')?></td>
	<td width="1%"><?php echo arraySelect($users, 'user_id', 'size="1" class="text" id="medium" onchange="document.frmFilter.submit()"',
                          $user_id)?></td>
	<td width="1%" nowrap="nowrap"><?php echo $AppUI->_('Cost Code Filter')?></td>
	<td width="1%"><?php echo arraySelect($task_log_costcodes, 'cost_code', 'size="1" class="text" onchange="document.frmFilter.submit()"',
                          $cost_code)?></td>
</tr>
<input type="hidden" name="m" value="projects"/>
<input type="hidden" name="a" value="view"/>
<input type="hidden" name="project_id" value="<?php echo $project_id?>"/>
<input type="hidden" name="tab" value="<?php echo $tab?>"/>
</form>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="100%" class="tbl">
<form name="frmDelete2" action="./index.php?m=tasks" method="post">
	<input type="hidden" name="dosql" value="do_updatetask">
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="task_log_id" value="0" />
</form>
<tr>
	<th></th>
	<th><?php echo $AppUI->_('Date');?></th>
	<th width="100"><?php echo $AppUI->_('Summary');?></th>
	<th width="100"><?php echo $AppUI->_('User');?></th>
	<th width="100"><?php echo $AppUI->_('Hours');?></th>
	<th width="100"><?php echo $AppUI->_('Cost Code');?></th>
	<th width="100%"><?php echo $AppUI->_('Comments');?></th>
	<th></th>
</tr>
<?php
// Winnow out the tasks we are not allowed to view.
$project = new CProject;

// Pull the task comments
$q  = new DBQuery;
$q->addTable('task_log','tl');
$q->addQuery('tl.*, user_username, task_id');
$q->addQuery('billingcode_name as task_log_costcode');
$q->addJoin('users', 'u', 'user_id = task_log_creator');
$q->addJoin('tasks', 't', 'task_log_task = t.task_id');
$q->addJoin('billingcode', 'b', 'tl.task_log_costcode = billingcode_id');
//already included bY the setAllowedSQL function
//$q->addJoin('projects', 'p', 'task_project = p.project_id');
$q->addWhere("task_project = $project_id ");
if ($user_id > 0) {
	$q->addWhere("task_log_creator=$user_id");
}
if ($hide_inactive) {
	$q->addWhere('task_status >= 0');
}
if ($hide_complete) {
	$q->addWhere('task_percent_complete < 100');
}
if ($cost_code != 0) {
	$q->addWhere("task_log_costcode = '$cost_code'");
}

$q->addOrder('task_log_date');
$project->setAllowedSQL($AppUI->user_id, $q, 'task_project');
$logs = $q->loadList();

$s = '';
$hrs = 0;
foreach ($logs as $row) {
	$task_log_date = intval($row['task_log_date']) ? new CDate($row['task_log_date']) : null;

	$s .= '<tr bgcolor="white" valign="top">';
	$s .= "\n\t<td>";
	if (getPermission('tasks', 'edit', $row['task_id'])) {
		$s .= ("\n\t\t" . '<a href="?m=tasks&amp;a=view&amp;task_id=' . $row['task_id'] 
		       . "&amp;tab=1&amp;task_log_id=" . @$row['task_log_id'] . '">' 
			   . "\n\t\t\t" . dPshowImage('./images/icons/stock_edit-16.png', 16, 16, '') 
			   . "\n\t\t</a>");
	}
	$s .= "\n\t</td>";
	$s .= '<td nowrap="nowrap">'.($task_log_date ? $task_log_date->format($df) : '-').'</td>';
	$s .= '<td width="30%"><a href="?m=tasks&amp;a=view&amp;task_id='.$row['task_id'].'&amp;tab=0">'.@$row["task_log_name"].'</a></td>';
	$s .= '<td width="100">'.$row["user_username"].'</td>';
	$s .= '<td width="100" align="right">'.sprintf("%.2f", $row["task_log_hours"]) . '</td>';
	$s .= '<td width="100">'.$row["task_log_costcode"].'</td>';
	$s .= '<td>';

// dylan_cuthbert: auto-transation system in-progress, leave these lines
	$transbrk = "\n[translation]\n";
	$descrip = str_replace("\n", "<br />", $row['task_log_description']);
	$tranpos = mb_strpos($descrip, str_replace("\n", "<br />", $transbrk));
	if ($tranpos === false) $s .= $descrip;
	else
	{
		$descrip = mb_substr($descrip, 0, $tranpos);
		$tranpos = mb_strpos($row['task_log_description'], $transbrk);
		$transla = mb_substr($row['task_log_description'], $tranpos + mb_strlen($transbrk));
		$transla = trim(str_replace("'", '"', $transla));
		$s .= $descrip."<div style='font-weight: bold; text-align: right'><a title='$transla' class='hilite'>[".$AppUI->_("translation")."]</a></div>";
	}
// end auto-translation code
			
	$s .= '</td>';
	$s .= "\n\t<td>";
	if ($canDelete) {
		$s .= ("\n\t\t" . '<a href="javascript:delIt2(' . $row['task_log_id'] . ');" title="' 
			   . $AppUI->_('delete log') . '">' . "\n\t\t\t" 
		       . dPshowImage('./images/icons/stock_delete-16.png', 16, 16, ''). "\n\t\t</a>");
	}
	$s .= "\n\t</td>";
	$s .= '</tr>';
	$hrs += (float)$row["task_log_hours"];
}
$s .= '<tr bgcolor="white" valign="top">';
$s .= '<td colspan="3" align="right">' . $AppUI->_('Total Hours') . ' =</td>';
$s .= '<td align="right">' . sprintf("%.2f", $hrs) . '</td>';
$s .= '</tr>';
echo $s;
?>
</table>

<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $AppUI, $task_id, $obj, $percent, $can_edit_time_information;

// check permissions
if (!getPermission('tasks', 'edit', $task_id)) {
	$AppUI->redirect('m=public&a=access_denied');
}

$canEdit = getPermission('task_log', 'edit', $task_id);
$canAdd = getPermission('task_log', 'add', $task_id);

$task_log_id = intval(dPgetParam($_GET, 'task_log_id', 0));
$log = new CTaskLog();
if ($task_log_id) {
	if (!($canEdit)) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	$log->load($task_log_id);
} else {
	if (!($canAdd)) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	$log->task_log_task = $task_id;
	$log->task_log_name = $obj->task_name;
}

// Check that the user is at least assigned to a task
$task = new CTask;
$task->load($task_id);
if (!($task->canAccess($AppUI->user_id))) {
	$AppUI->redirect('m=public&a=access_denied');
}

$q = new DBQuery;
$q->addTable('tasks', 't');
$q->innerJoin('projects', 'p', 'p.project_id = t.task_project');
$q->innerJoin('billingcode', 'b', 'b.company_id = p.project_company OR b.company_id = 0');
$q->addQuery('b.billingcode_id, b.billingcode_name');
$q->addWhere('b.billingcode_status = 0');
$q->addWhere('t.task_id = ' . $task_id);
$q->addOrder('b.billingcode_name');
$task_log_costcodes = $q->loadHashList();
$task_log_costcodes[0] = '';
$q->clear();

$taskLogReference = dPgetSysVal('TaskLogReference');

// Task Update Form
$df = $AppUI->getPref('SHDATEFORMAT');
$log_date = new CDate($log->task_log_date);

//task log e-mail checkboxes
$tl = $AppUI->getPref('TASKLOGEMAIL');
$ta = $tl & 1;
$tt = $tl & 2;
$tp = $tl & 4;

//notify owner checkbox 
$notify_own = $AppUI->getPref('MAILALL'); 

//task log e-mail list
$task_email_title = array();
$q->addTable('task_contacts', 'tc');
$q->leftJoin('contacts', 'c', 'c.contact_id = tc.contact_id');
$q->addWhere('tc.task_id = ' . $task_id);
$q->addQuery('tc.contact_id');
$q->addQuery('c.contact_first_name, c.contact_last_name');
$req =& $q->exec();
$cid = array();
for ($req; !($req->EOF); $req->MoveNext()) {
	$cid[] = $req->fields['contact_id'];
	$task_email_title[] = ($req->fields['contact_first_name'] . ' ' 
	                       . $req->fields['contact_last_name']);
}
$q->clear();

//project contacts
$q->addTable('project_contacts', 'pc');
$q->leftJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
$q->addWhere('pc.project_id = ' . $obj->task_project);
$q->addQuery('pc.contact_id');
$q->addQuery('c.contact_first_name, c.contact_last_name');
$req =& $q->exec();
$cid = array();
$proj_email_title = array();
for ($req; !($req->EOF); $req->MoveNext()) {
	if (! in_array($req->fields['contact_id'], $cid)) {
		$cid[] = $req->fields['contact_id'];
		$proj_email_title[] = ($req->fields['contact_first_name'] . ' ' 
		                       . $req->fields['contact_last_name']);
	}
}

?>

<!-- TIMER RELATED SCRIPTS -->
<script type="text/javascript" language="javascript">
	// please keep these lines on when you copy the source
	// made by: Nicolas - http://www.javascript-page.com
	// adapted by: Juan Carlos Gonzalez jcgonz@users.sourceforge.net
	
	var timerID = 0;
	var tStart = null;
	var total_minutes = -1;
	
	function UpdateTimer() {
		if (timerID) {
			clearTimeout(timerID);
			clockID  = 0;
		}
		
		// One minute has passed
		total_minutes = total_minutes+1;
		
		document.getElementById("timerStatus").innerHTML = "("+total_minutes+" <?php 
echo $AppUI->_('minutes elapsed'); ?>)";

		// Lets round hours to two decimals
		var total_hours   = Math.round((total_minutes / 60) * 100) / 100;
		document.editFrm.task_log_hours.value = total_hours;
		
		timerID = setTimeout("UpdateTimer()", 60000);
	}
	
	function timerStart() {
		if (!timerID) { // this means that it needs to be started
			timerSet();
			document.editFrm.timerStartStopButton.value = "<?php echo $AppUI->_('Stop'); ?>";
			UpdateTimer();
		} else { // timer must be stoped
			document.editFrm.timerStartStopButton.value = "<?php echo $AppUI->_('Start'); ?>";
			document.getElementById("timerStatus").innerHTML = "";
			timerStop();
		}
	}
	
	function timerStop() {
		if (timerID) {
			clearTimeout(timerID);
			timerID  = 0;
			total_minutes = total_minutes-1;
		}
	}
	
	function timerReset() {
		document.editFrm.task_log_hours.value = "0.00";
		total_minutes = -1;
	}
	
	function timerSet() {
		total_minutes = Math.round(document.editFrm.task_log_hours.value * 60) -1;
	}
	<?php
if ($obj->canUserEditTimeInformation()) {
?>
	function popCalendar(field) {
		calendarField = field;
		idate = eval('document.editFrm.task_' + field + '.value');
		window.open('index.php?m=public&'+'a=calendar&'+'dialog=1&'+'callback=setCalendar&'+'date='
					+ idate, 'calwin', 'width=251, height=220, scrollbars=no, status=no');
	}
<?php 
}
?>
</script>
<!-- END OF TIMER RELATED SCRIPTS -->

<a name="log"></a>
<form name="editFrm" action="?m=tasks&amp;a=view&amp;task_id=<?php echo $task_id; ?>" method="post"
  onsubmit='javascript:updateEmailContacts();'>
  <input type="hidden" name="uniqueid" value="<?php echo uniqid(""); ?>" />
  <input type="hidden" name="dosql" value="do_updatetask" />
  <input type="hidden" name="task_log_id" value="<?php echo $log->task_log_id; ?>" />
  <input type="hidden" name="task_log_task" value="<?php echo $log->task_log_task; ?>" />
  <input type="hidden" name="task_log_creator" value="<?php 
echo(($log->task_log_creator == 0) ? $AppUI->user_id : $log->task_log_creator) ?>" />
  <input type="hidden" name="task_log_name" value="Update :<?php echo $AppUI->___($log->task_log_name); ?>" />
<table cellspacing="1" cellpadding="2" border="0" width="100%">
<tr>
  <td width='40%' valign='top' align='center'>
    <table width='100%'>
      <tr>
        <td align="right"><?php echo $AppUI->_('Date'); ?></td>
        <td nowrap="nowrap">
          <input type="hidden" name="task_log_date" value="<?php 
echo $log_date->format(FMT_DATETIME_MYSQL); ?>" />
          <input type="text" name="log_date" value="<?php 
echo $log_date->format($df); ?>" class="text" disabled="disabled" />
		<a href="#" onclick="javascript:popCalendar('log_date')">
          <img src="./images/calendar.gif" width="24" height="12" alt="<?php 
echo $AppUI->_('Calendar'); ?>" border="0" />
          </a>
        </td>
      </tr>
      <tr>
        <td align="right"><?php echo $AppUI->_('Progress'); ?></td>
        <td>
        <table>
          <tr>
            <td>
              <?php
echo arraySelect($percent, 'task_percent_complete', 'size="1" class="text"', 
                 $obj->task_percent_complete) . '%'; 
?>
            </td>
            <td valign="middle"><?php
if ($obj->task_owner != $AppUI->user_id) {
?>
              <input type="checkbox" name="task_log_notify_owner" id="task_log_notify_owner" <?php 
echo (($notify_own) ? ' checked="checked"' : ''); ?> />
            </td>
            <td valign="middle">
              <label for="task_log_notify_owner"><?php echo $AppUI->_('Notify owner'); ?></label>
<?php 
}
?>
            </td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td align="right"><?php echo $AppUI->_('Hours Worked'); ?></td>
        <td>
          <input type="text" class="text" name="task_log_hours" value="<?php 
echo $log->task_log_hours; ?>" maxlength="8" size="6" /> 
          <input type='button' class="button" value='<?php 
echo $AppUI->_('Start'); ?>' onclick='javascript:timerStart()' name='timerStartStopButton' />
          <input type='button' class="button" value='<?php 
echo $AppUI->_('Reset'); ?>' onclick="javascript:timerReset()" name='timerResetButton' /> 
          <span id='timerStatus'></span>
        </td>
      </tr>
      <tr>
        <td align="right"><?php echo $AppUI->_('Cost Code'); ?></td>
        <td>
          <?php
echo arraySelect($task_log_costcodes, 'task_log_costcodes', 
                 ('size="1" class="text" onchange="javascript:task_log_costcode.value' 
                  . ' = this.options[this.selectedIndex].value;"'), $log->task_log_costcode);
?>
           -&gt; <input type="text" class="text" name="task_log_costcode" value="<?php 
echo $log->task_log_costcode; ?>" maxlength="8" size="8" />
        </td>
      </tr>
<?php
if ($obj->canUserEditTimeInformation()) {
	$end_date = ((intval($obj->task_end_date)) ? new CDate($obj->task_end_date) : null);
?>
      <tr>
        <td align='right'><?php echo $AppUI->_("Task end date"); ?></td>
        <td>
          <input type="hidden" name="task_end_date" value="<?php 
	echo (($end_date) ? $end_date->format(FMT_TIMESTAMP) : ''); ?>" />
          <input type="text" name="end_date" value="<?php 
	echo (($end_date) ? $end_date->format($df) : ''); ?>" class="text" disabled="disabled" />
			<a href="#" onclick="javascript:popCalendar('end_date')">
          <img src="./images/calendar.gif" width="24" height="12" alt="<?php 
	echo $AppUI->_('Calendar'); ?>" border="0" />
          </a>
        </td>
      </tr>
<?php
}
?>
    </table>
  </td>
  <td width='60%' valign='top' align='center'>
    <table width='100%'>
      <tr>
        <td align="right"><?php echo $AppUI->_('Summary'); ?>:</td>
        <td valign="middle">
          <table width="100%">
            <tr>
              <td align="left">
                <input type="text" class="text" name="task_log_name" value="<?php 
echo $AppUI->___($log->task_log_name); ?>" maxlength="255" size="30" />
              </td>
              <td align="center">
                <label for="task_log_problem"><?php echo $AppUI->_('Problem'); ?>:</label>
                <input type="checkbox" value="1" name="task_log_problem" id="task_log_problem"<?php 
echo (($log->task_log_problem) ? 'checked="checked"' : ''); ?> />
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="right" valign="middle"><?php echo $AppUI->_('Reference'); ?>:</td>
        <td valign="middle">
          <?php 
echo arraySelect($taskLogReference, 'task_log_reference', 'size="1" class="text"', 
                 $log->task_log_reference, true); 
?>
        </td>
      </tr>
      <tr>
        <td align="right"><?php echo $AppUI->_('URL'); ?>:</td>
        <td><input type="text" class="text" name="task_log_related_url" value="<?php 
echo ($log->task_log_related_url); ?>" size="50" maxlength="255" title="<?php 
echo $AppUI->_('Must in general be entered with protocol name, e.g. http://...'); ?>" /></td>
      </tr>
      <tr>
        <td align="right" valign="top"><?php echo $AppUI->_('Description'); ?>:</td>
        <td><textarea name="task_log_description" class="textarea" cols="50" rows="6"><?php 
echo $log->task_log_description; ?></textarea></td>
      </tr>
      <tr>
        <td align="right" valign="top"><?php echo $AppUI->_('Email Log to'); ?>:</td>
        <td>
          <input type="checkbox" name="email_assignees" id="email_assignees"<?php 
echo (($ta) ? ' checked="checked"' : ''); ?> />
          <label for="email_assignees"><?php echo $AppUI->_('Task Assignees'); ?></label>
          <input type="hidden" name="email_task_list" id="email_task_list" value="<?php 
echo implode(',', $cid); ?>" />
          <input type="checkbox" onmouseover="javascript:window.status = '<?php 
echo addslashes(implode(',',$task_email_title)); 
?>';" onmouseout="javascript:window.status = '';" name="email_task_contacts" id="email_task_contacts"<?php
echo (($tt) ? ' checked="checked"' : ''); ?> />
          <label for="email_task_contacts"><?php echo $AppUI->_('Task Contacts'); ?></label>
          <input type="hidden" name="email_project_list" id="email_project_list" value="<?php 
echo implode(',', $cid); ?>" />
          <input type="checkbox" onmouseover="javascript:window.status = '<?php 
echo addslashes(implode(',', $proj_email_title)); 
?>';" onmouseout="javascript:window.status = '';" name="email_project_contacts" id="email_project_contacts" <?php 
echo (($tp) ? ' checked="checked"' : ''); ?> />
          <label for="email_project_contacts"><?php echo $AppUI->_('Project Contacts'); ?></label>
          <input type='hidden' name='email_others' id='email_others' value='' />
          <?php
if ($AppUI->isActiveModule('contacts') && getPermission('contacts', 'view')) {
?>
          <input type='button' class='button' value='<?php 
	echo $AppUI->_('Other Contacts...'); ?>' onclick='javascript:popEmailContacts();' />
<?php 
}
?>
        </td>
      </tr>
      <tr>
        <td align="right" valign="top"><?php echo $AppUI->_('Extra Recipients'); ?>:</td>
        <td><input type="text" class="text" name="email_extras" maxlength="255" size="30" /></td>
      </tr>
      <tr>
        <td colspan="2" valign="bottom" align="right">
          <input type="button" class="button" value="<?php 
echo $AppUI->_('update task'); ?>" onclick="updateTask()" />
        </td>
      </tr>
    </table>
  </td>
</tr>
</table>
</form>

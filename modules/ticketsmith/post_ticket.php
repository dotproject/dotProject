<?php /* TICKETSMITH $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

##
##	Ticketsmith Post Ticket
##

if (!$canEdit && !$canAuthor) {
	$AppUI->redirect("m=public&a=access_denied");
}

include_once ($AppUI->getLibraryClass('quilljs/richedit.class'));
require_once($AppUI->getModuleClass ('companies'));
require_once($AppUI->getModuleClass ('projects'));

// setup the title block
$titleBlock = new CTitleBlock('Submit Trouble Ticket', 'gconf-app-icon.png', $m, $m . "." . $a);
$titleBlock->addCrumb("?m=ticketsmith", "tickets list");
$titleBlock->show();

?>

<script  language="javascript">
function submitIt() {
	var f = document.ticketform;
	var msg = '';
	if (f.name.value.length < 3) {
		msg += "\n- <?php echo $AppUI->_('a valid name'); ?>"
	}
	if (f.email.value.length < 3) {
		msg += "\n- <?php echo $AppUI->_('a valid email'); ?>";
	}
	if (f.subject.value.length < 3) {
		msg += "\n- <?php echo $AppUI->_('a valid subject'); ?>";
	}
	if (f.description.value.length < 3) {
		msg += "\n- <?php echo $AppUI->_('a valid description'); ?>";
	}
	if (msg.length < 1) {
		f.submit();
	} else {
		alert("<?php echo $AppUI->_('ticketsmithValidDetail', UI_OUTPUT_JS); ?>:" + msg);
	}
}
</script>

<form name="ticketform" action="?m=ticketsmith" method="post">
<input type="hidden" name="dosql" value="do_ticket_aed">

<table width="100%" border=0 cellpadding="0" cellspacing=1 class="std">
<tr height="20">
	<th colspan="2">
		&nbsp;<span class="trouble-details"><?php echo $AppUI->_('Trouble Details'); ?></span>
	</th>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Name'); ?>:</td>
	<td><input type="text" class="text" name="name" value="<?php echo $AppUI->user_first_name . ' ' . $AppUI->user_last_name; ?>" size=50 maxlength="255" /> <span class="smallNorm">(<?php echo $AppUI->_('required'); ?>)</span></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('E-Mail'); ?>:</td>
	<td><input type="text" class="text" name="email" value="<?php echo $AppUI->user_email; ?>" size="50" maxlength="50" /> <span class="smallNorm">(<?php echo $AppUI->_('required'); ?>)</span></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Subject'); ?>:</td>
	<td><input autofocus type="text" class="text" name="subject" value="" size="50" maxlength="50" /> <span class="smallNorm">(<?php echo $AppUI->_('required'); ?>)</span></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Priority'); ?>:</td>
	<td>
		<select name="priority" class="text">
			<option value="0"><?php echo $AppUI->_('Low'); ?>
			<option value="1" selected><?php echo $AppUI->_('Normal'); ?>
			<option value="2"><?php echo $AppUI->_('High'); ?>
			<option value="3"><?php echo $AppUI->_('Highest'); ?>
			<option value="4"><strong><?php echo $AppUI->_('911'); ?> (<?php echo $AppUI->_('Showstopper'); ?>)</strong>
		</select>
	</td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Company'); ?>:</td>
	<td>
	  <?php
		$objCompany = new CCompany();
		$companies = $objCompany->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 'company_name');
		$companies = arrayMerge(array('0'=>''), $companies);
		echo arraySelect($companies, 'ticket_company', 'class="text" size="1"', null);
	  ?></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Project'); ?>:</td>
	<td>
	  <?php
		// Retrieve projects that the user can access
		$objProject = new CProject();
		$allowedProjects = $objProject->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 'project_name');

		$q  = new DBQuery;
		$q->addTable('projects', 'p');
		$q->addTable('tasks', 't');
		$q->addQuery('p.project_id, p.project_name');
		$q->addWhere('t.task_project = p.project_id');
		if (count($allowedProjects) > 0) {
			$q->addWhere('(p.project_id IN (' .
			implode (',', array_keys($allowedProjects)) . '))');
		}
		$q->addOrder('p.project_name');

		$importList = $q->loadHashList ();
		$importList = arrayMerge(array('0'=> $AppUI->_('')), $importList);

		echo arraySelect($importList, 'ticket_project', 'size="1" class="text"', null);
	?></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Description of Problem'); ?>: </td>
	<td><span class="smallNorm">(<?php echo $AppUI->_('required'); ?>)</span></td>
</tr>
<tr>
	<td colspan="2" align="center">
<!--
		<textarea cols="70" rows="10" class="textarea" name="description"><?php //echo @$crow["description"];?></textarea>
-->
  <?php
  $richedit = new DpRichEdit("description", dPsanitiseHTML(@$crow["description"]));
  $richedit->render();
  ?>
	</td>
</tr>
<tr>
	<td><input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" /></td>
	<td align="right"><input type="button" value="<?php echo $AppUI->_('submit'); ?>" class="button" onclick="javascript:submitIt()" /></td>
</tr>
</table>
</form>
&nbsp;<br />&nbsp;<br />&nbsp;

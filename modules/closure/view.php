<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

$obj = new CClosure;
$pma_id = dPgetParam($_GET, 'pma_id', 0);
$perms =& $AppUI->acl();

$canView = $perms->checkModuleItem('closure', 'view', $pma_id);
$canEdit = $perms->checkModuleItem('closure', 'edit', $pma_id);
$canDelete = $perms->checkModuleItem('closure', 'delete', $pma_id);
$canAdd = $perms->checkModule('closure', 'add');

if (! $canView) {
  $AppUI->redirect("m=public&a=access_denied");
}

if (! $pma_id) {
  $AppUI->setMsg('invalid ID', UI_MSG_ERROR);
  $AppUI->redirect();
}
// TODO: tab stuff

$obj = new CClosure;

if (! $obj->load($pma_id)) {
  $AppUI->setMsg('Post Mortem Analysis');
  $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
  $AppUI->redirect();
} else {
  $AppUI->savePlace();
}

$titleBlock = new CTitleBlock('View Post Mortem Analysis', 'closed.png', $m, "$m.$a");
if ($canAdd) {
  $titleBlock->addCell(
    '<input type="submit" class="button" value="'. $AppUI->_('new post mortem analysis').'" />', '',
    '<form action="?m=closure&a=addedit" method="post">', '</form>'
  );
}

$titleBlock->addCrumb('?m=closure', 'post mortem list');
if ($canEdit) {
  $titleBlock->addCrumb("?m=closure&a=addedit&pma_id=$pma_id", "edit this post mortem");
}
if ($canDelete) {
  $titleBlock->addCrumbDelete('delete post mortem', $canDelete, 'no delete permission');
}
$titleBlock->show();

$meeting_date = intval($obj->project_meeting_date) ? new CDate($obj->project_meeting_date) : null;
$start_date = intval($obj->project_start_date) ? new CDate($obj->project_start_date) : null;
$end_date = intval($obj->project_end_date) ? new CDate($obj->project_end_date) : null;
$planned_start_date = intval($obj->project_start_date) ? new CDate($obj->project_planned_start_date) : null;
$planned_end_date = intval($obj->project_end_date) ? new CDate($obj->project_planned_end_date) : null;
$df = $AppUI->getPref('SHDATEFORMAT');

if ($canDelete) {
?>
<script language="javascript">
  can_delete = true;
  delete_msg = "<?php echo $AppUI->_('doDelete').' '.$AppUI->_('Post Mortem').'?';?>";
</script>
<form name="frmDelete" action="./index.php?m=closure" method="post">
  <input type="hidden" name="dosql" value="do_closure_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="pma_id" value="<?php echo $pma_id;?>" />
</form>
<?php
}
?>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">


<tr>

	<td width="100%" valign="top">
	<strong>  <?php echo $AppUI->_('Meeting Settings');?>  </strong><br />
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
 	  <tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Meeting Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $meeting_date ? $meeting_date->format($df) : '';?></td>
		</tr>

		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Meeting Participants');?>:</td>
			<td class="hilite"><?php echo $obj->participants;?></td>
		</tr>

		</table>
	</td>
</tr>



<tr>
  <td valign="top" width="100%">
		<strong><?php echo $AppUI->_('Project Summary');?></strong><br />
		<table cellspacing="1" cellpadding="2" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Post Mortem ID');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->pma_id;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Name');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->project_name;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Start Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $start_date ? $start_date->format($df) : '';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Planned Start Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $planned_start_date ? $planned_start_date->format($df) : '';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project End Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $end_date ? $end_date->format($df) : '';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Planned End Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $planned_end_date ? $planned_end_date->format($df) : '';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Budget');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->budget;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Planned Budget');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->planned_budget;?></td>
		</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td>
		<?php
			require_once(DP_BASE_DIR . '/classes/CustomFields.class.php');
			$custom_fields = New CustomFields( $m, $a, $obj->resource_id, "view" );
			$custom_fields->printHTML();
		?>
	</td>
</tr>
<tr>
	<td width="100%" valign="top">
    <strong><?php echo $AppUI->_('Lessons Learned');?></strong><br /> <br />
    <p align="left"> <?php echo $AppUI->_('Project Strengths');?>      </p>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr align="left">
			<td class="hilite">
				<?php echo str_replace( chr(10), "<br />", $obj->project_strength);?>&nbsp;
			</td>
		</tr>

		</table>
	</td>
</tr>

<tr>

	<td width="100%" valign="top">
	<p align="left">  <?php echo $AppUI->_('Project Weaknesses');?>  </p>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr align="left">
			<td class="hilite">
				<?php echo str_replace( chr(10), "<br />", $obj->project_weaknesses);?>&nbsp;
			</td>
		</tr>

		</table>
	</td>
</tr>

<tr>

	<td width="100%" valign="top">
	<p align="left"> 	<?php echo $AppUI->_('Improvement Suggestions');?>   </p>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr align="left">
			<td class="hilite">
				<?php echo str_replace( chr(10), "<br />", $obj->improvement_suggestions);?>&nbsp;
			</td>
		</tr>

		</table>
	</td>
</tr>

<tr>
			<td width="100%" valign="top">
		<p align="left"> <?php echo $AppUI->_('Conclusions');?>  </p>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr align="left">
			<td class="hilite">
				<?php echo str_replace( chr(10), "<br />", $obj->conclusions);?>&nbsp;
			</td>
		</tr>

		</table>
	</td>
</tr>
</table>

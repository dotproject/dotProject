<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

$AppUI->savePlace();

$perms = $AppUI->acl();
$canEdit = $perms->checkModule($m, "edit");

$titleBlock = new CTitleBlock('Closure', 'closed.png', $m, "$m.$a");
if ($canEdit) {
  $titleBlock->addCell(
    '<input type="submit" class="button" value="'. $AppUI->_('new post mortem analysis').'">', '',
    '<form action="?m=closure&a=addedit" method="post">','</form>'
  );
}

$titleBlock->show();

$q = new DBQuery;
$q->addTable('post_mortem_analysis', 'pma');
$q->addQuery('distinct pma.project_name, pma.pma_id,
pma.project_start_date, pma.project_end_date, pma.project_meeting_date');

$rows = $q->loadList();
$df = $AppUI->getPref('SHDATEFORMAT');

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
  <th width="1%"></th>
  <th width="10"><?php echo $AppUI->_('Project Name'); ?></th>
  <th width="10"><?php echo $AppUI->_('Project Meeting Date'); ?></th>
  <th width="10"><?php echo $AppUI->_('Project Start Date'); ?></th>
  <th width="10"><?php echo $AppUI->_('Project End Date');?></th>
</tr>
<?php foreach ($rows as $p) {
   $meeting_date = intval($p['project_meeting_date']) ? new CDate($p['project_meeting_date']) : null;
   $start_date = intval($p['project_start_date']) ? new CDate($p['project_start_date']) : null;
   $end_date = intval($p['project_end_date']) ? new CDate($p['project_end_date']) : null;
?>
  <tr>
	<td> <a href="?m=closure&amp;a=addedit&amp;pma_id=<?php echo $p['pma_id'];?>"><img src="./images/icons/pencil.gif" alt="Edit post mortem analysis"</a> </td>	

 	<td> <a href="?m=closure&amp;a=view&amp;pma_id=<?php echo $p['pma_id'];?>"><?php echo $p['project_name']; ?></a> </td>
	
	<td> <?php echo $meeting_date ? $meeting_date->format($df) : ''; ?></td>
	
	<td> <?php echo $start_date ? $start_date->format($df) : ''; ?></td>
	
	<td> <?php echo $end_date ? $end_date->format($df) : ''; ?></td>
  </tr>
<?php } ?>
</table>

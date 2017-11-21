<?php /* PROJECTS $Id: companies_tab.view.active_projects.php 4779 2007-02-21 14:53:28Z cyberhorse $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

global $AppUI, $pstatus, $tpl, $m;

require_once($AppUI->getModuleClass('files'));

$pstatus = dPgetSysVal( 'ProjectStatus' );

$project_id = $_GET['project_id'];
$sort = dPgetParam($_GET, 'orderby', 'pma_id');
if ($sort == 'pma_id') {
  $sort .= ' DESC';
}
$df = $AppUI->getPref('SHDATEFORMAT');

$page = isset($_REQUEST['page'])?$_REQUEST['page']:1;

$q  = new DBQuery;
$q->addTable('post_mortem_analysis', 'pma');
$q->addQuery('pma_id, pma.project_name, project_meeting_date, participants');
$q->addJoin('projects', 'p','p.project_name = pma.project_name');
$q->addWhere('p.project_id = '.$project_id);
$q->addOrder($sort);
$rows = $q->loadList();

$q->addTable('projects');
$q->addQuery('count(*)');
$q->addWhere('project_id = '.$project_id);
$count_rows = $q->loadResult();

$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_name');
$q->addWhere('project_id = '.$project_id);
$res =& $q->exec();

$project_name = $res->fields['project_name'];

$df = $AppUI->getPref('SHDATEFORMAT');

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
  <th> </th>
  <th width="50%"><?php echo $AppUI->_('Project Meeting Date'); ?></th>
  <th width="50%"><?php echo $AppUI->_('Participants'); ?></th>
</tr>
<?php foreach ($rows as $p) {
   $meeting_date = intval($p['project_meeting_date']) ? new CDate($p['project_meeting_date']) : null;

?>
  <tr>
	<td><a href="?m=closure&amp;a=addedit&amp;pma_id=<?php echo $p['pma_id'];?>"><img src="./images/icons/pencil.gif" alt="Edit post mortem analysis"></a> </td>
 	<td> <a href="?m=closure&amp;a=view&amp;pma_id=<?php echo $p['pma_id'];?>">
   <?php echo $meeting_date ? $meeting_date->format($df) : ''; ?> </a></td>

	<td> <?php echo $p['participants']; ?></td>

  </tr>
<?php } ?>
<!--<tr>
</tr><br />
	<td></td>
	<td></td>
<td nowrap="nowrap" align="right">-->
</td></tr>
</table>
<div align="right">
<input class="button" type="button" name="new post mortem" value="<?php echo 'new post mortem'; ?>" onclick="location.href = '?m=closure&amp;a=addedit&amp;project_name=<?php echo $project_name;?>';" />
</div>

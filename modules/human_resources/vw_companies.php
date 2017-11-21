<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;

$query = new DBQuery;
//$obj->setAllowedSQL($AppUI->user_id, $query);
$query->addTable('companies');
if (!$tabbed)
	$currentTabId++;
	
if ($currentTabId) {
  $query->addQuery('company_id, company_name');
}
$res =& $query->exec();
?>
<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl' summary="view companies">
<tr>
	<th nowrap='nowrap' width='15%'>
    <?php echo $AppUI->_('Company name'); ?>
	</th>
</tr>
<?php
require_once DP_BASE_DIR."/modules/human_resources/configuration_functions.php";
for ($res; ! $res->EOF; $res->MoveNext()) {
	$company_id = $res->fields['company_id'];
	$configured = allCompanyHumanResourcesConfigured($company_id);
	$style = $configured ? '' : 'background-color:#ED9A9A; font-weight:bold';
?>
<tr>
  <td style=<?php echo $style;?>>
	<a href="index.php?m=human_resources&amp;a=view_company_users&amp;company_id=<?php echo $company_id;?>">
		<?php echo $res->fields['company_name']; ?>
    </a>
  </td>
</tr>
<?php
  }
$query->clear();
?>
</table>
<table>
<tr>
  <td><?php echo $AppUI->_('Key'); ?>:&nbsp;&nbsp;</td>
  <td style="background-color:#FFFFFF; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Company with all human resources configured'); ?>&nbsp;&nbsp;</td>
  <td style="background-color:#ED9A9A; color:#000000" width="10">&nbsp;</td>
  <td>=<?php echo $AppUI->_('Company with not all human resources configured'); ?>&nbsp;&nbsp;</td>
</tr>
</table>


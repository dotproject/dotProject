<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;

$query = new DBQuery;
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
for($res; ! $res->EOF; $res->MoveNext()) {
	$company_id = $res->fields['company_id'];
	$configured = areConfiguredAllRoles($company_id);
	$style = $configured ? '' : 'background-color:#ED9A9A; font-weight:bold';
?>
<tr>
  <td style=<?php echo $style; ?>>
	<a href="index.php?m=human_resources&amp;a=view_company_roles&amp;company_id=<?php echo $company_id; ?>">
		<?php echo $res->fields['company_name']; ?>
    </a>
  </td>
</tr>
<?php
}
?>
</table>
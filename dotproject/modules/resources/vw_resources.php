<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;
$obj = new CResource;

$query = new DBQuery;
$obj->setAllowedSQL($AppUI->user_id, $query);
$query->addTable($obj->_tbl);
if (!$tabbed)
	$currentTabId++;

if ($currentTabId)
  $query->addWhere('resource_type = ' . $_SESSION['resource_type_list'][$currentTabId]['resource_type_id']);
$res =& $query->exec();
?>
<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl'>
<tr>
	<th nowrap='nowrap' width='20%'>
    <?php echo $AppUI->_('ID'); ?>
	</th>
  <th nowrap='nowrap' width='70%'>
    <?php echo $AppUI->_('Resource Name'); ?>
  </th>
  <th nowrap='nowrap' width='10%'>
    <?php echo $AppUI->_('Max Alloc %'); ?>
  </th>
</tr>
<?php
  for ($res; ! $res->EOF; $res->MoveNext()) {
?>
<tr>
  <td>
    <a href="index.php?m=resources&a=view&resource_id=<?php echo $res->fields['resource_id'];?>">
    <?php echo $res->fields['resource_key']; ?>
    </a>
  </td>
  <td>
    <a href="index.php?m=resources&a=view&resource_id=<?php echo $res->fields['resource_id'];?>">
    <?php echo $res->fields['resource_name']; ?>
		</a>
  </td>
  <td>
    <?php echo $res->fields['resource_max_allocation']; ?>
  </td>
</tr>
<?php
  }
$query->clear();
?>
</table>

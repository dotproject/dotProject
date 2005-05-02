<?php /* ADMIN $Id$ */
GLOBAL $AppUI, $user_id;

$q  = new DBQuery;
$q->addTable('projects', 'p');
$q->addQuery('p.*');
$q->addWhere('project_active <> 0');
$q->addWhere("project_owner = $user_id");
$q->addOrder('project_name');
$projects = $q->loadList();

$pstatus = dPgetSysVal( 'ProjectStatus' );
?>
<table width="100%" border=0 cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th><?php echo $AppUI->_('Name');?></th>
	<th><?php echo $AppUI->_('Status');?></th>
</tr>

<?php foreach ($projects as $row) {	?>
<tr>
	<td>
		<a href="?m=projects&a=view&project_id=<?php echo $row["project_id"];?>">
			<?php echo $row["project_name"];?>
		</a>
	<td><?php echo $AppUI->_($pstatus[$row["project_status"]]); ?></td>
</tr>
<?php } ?>
</table>
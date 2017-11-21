<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}


$q = new DBQuery();

$q->addTable('costs', 'c');
$q->addQuery('DISTINCT p.project_id, p.project_name');
$q->addJoin('projects', 'p', 'p.project_id = c.cost_project_id');
$q->addOrder('p.project_id');
$res = $q->loadList();
?>
<table width='100%' border='0' cellpadding='2' cellspacing='1' class='tbl' >

    <tr>
        <th nowrap='nowrap' width='10%'>
            <?php echo $AppUI->_('Project ID'); ?>
        </th>
        <th nowrap='nowrap' width='85%'>
            <?php echo $AppUI->_('Project Name'); ?>
        </th>
    </tr>    

    <?php foreach ($res as $row) {
        ?>
        <tr>
            <td nowrap='nowrap'>
                <?php echo $row['project_id'] ?>
            </td>
            <td nowrap='nowrap'>
                <a href="index.php?m=costs&amp;a=view_costs&amp;project_id=<?php echo $row['project_id']; ?>">
                    <?php echo $row['project_name']; ?>
                </a>
            </td>
        </tr>
    <?php } ?>
    <?php
    $q->clear();
    ?>
</table>



<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$whereProject ='';
if ($projectSelected != null) {
    $t = intval(dPgetParam($_GET, 'tab'));
    // setup the title block
    $titleBlock = new CTitleBlock($AppUI->_('LBL_RISKS').' - '.str_replace("&ccedil;&otilde;", "çõ",$AppUI->_('LBL_LESSONS_LIST')), 'risks.png', $m, "$m.$a");
    $titleBlock->addCrumb("?m=projects&a=view&project_id=".$projectSelected."&tab=".$t."&targetScreenOnProject=/modules/risks/projects_risks.php", "LBL_RETURN_LIST");
    $titleBlock->show();
    $whereProject = ' and risk_project='.$projectSelected;
}
    
$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$whereProject ='';
if ($projectSelected!=null) {
    $whereProject = ' and risk_project='.$projectSelected;
}
$t = intval(dPgetParam($_GET, 'tab'));

$q = new DBQuery(); 
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '0' and NOT risk_lessons_learned='' $whereProject");
$activeList = $q->loadList();

$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '1' and NOT risk_lessons_learned='' $whereProject");
$inactiveList = $q->loadList();
?>

<?php echo $AppUI->_('LBL_ACTIVE_RISKS');?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
    <th nowrap="nowrap"></th>
    <th nowrap="nowrap"><?php echo $AppUI->_('LBL_ID');?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_('LBL_RISK_NAME');?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_('LBL_LESSONS');?></th>
</tr>
<?php foreach ($activeList as $row) {
?>
<tr>
    <td nowrap style="background-color:#<?php echo $bg; ?>" width="30">
        <a href="index.php?m=risks&a=addedit&id=<?php echo($row['risk_id']); if ($projectSelected!=null) {echo('&project_id=' . $projectSelected . '&tab='. $t.'&vw=vw_lessons_learned_list');}?>">
            <img src="./modules/risks/images/stock_edit-16.png" border="0" width="12" height="12">
        </a>
        <a href="index.php?m=risks&a=view&id=<?php echo($row['risk_id']); if ($projectSelected!=null) {echo('&project_id=' . $projectSelected . '&tab='. $t.'&vw=vw_lessons_learned_list');}?>">
           <img src="./modules/risks/images/view_icon.gif" border="0" width="12" height="12">
        </a>
    </td>
    <td width="25"><?php echo $row['risk_id'];?></td>
    <td ><?php echo $row['risk_name'];?></td>
    <td><?php echo $row['risk_lessons_learned'];?></td>
</tr>
<?php } ?>
</table>
</br>
<?php echo $AppUI->_('LBL_INACTIVE_RISKS');?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
    <th nowrap="nowrap"></th>
    <th nowrap="nowrap"><?php echo $AppUI->_('LBL_ID');?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_('LBL_RISK_NAME');?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_('LBL_LESSONS');?></th>
</tr>
<?php foreach ($inactiveList as $row) {
?>
<tr>
    <td nowrap style="background-color:#<?php echo $bg; ?>" width="30">
        <a href="index.php?m=risks&a=addedit&id=<?php echo($row['risk_id']); if ($projectSelected!=null) {echo('&project_id=' . $projectSelected . '&tab='. $t.'&vw=vw_lessons_learned_list');}?>">
            <img src="./modules/risks/images/stock_edit-16.png" border="0" width="12" height="12">
        </a>
        <a href="index.php?m=risks&a=view&id=<?php echo($row['risk_id']); if ($projectSelected!=null) {echo('&project_id=' . $projectSelected . '&tab='. $t.'&vw=vw_lessons_learned_list');}?>">
            <img src="./modules/risks/images/view_icon.gif" border="0" width="12" height="12">
        </a>
    </td>
    <td><?php echo $row['risk_id'];?></td>
    <td ><?php echo $row['risk_name'];?></td>
    <td><?php echo $row['risk_lessons_learned'];?></td>
</tr>
<?php } ?>
</table>
<script src="./modules/risks/risks.js"></script>
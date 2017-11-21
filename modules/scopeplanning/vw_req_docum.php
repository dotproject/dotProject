<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('scope_requirements', 's');
$q->addJoin('projects', 'p', 'p.project_id = s.project_id');
$q->addJoin('scope_requirement_categories', 'c', 's.req_categ_prefix_id = c.req_categ_prefix_id');
$q->addOrder('p.project_id');
$q->addOrder('s.req_idname');
if(isset($_POST['project_id']) && $_POST['project_id'] != '0'){    
    $q->addwhere('s.project_id='.$_POST['project_id']);
    $list = $q->loadList();
}else{
    $list = $q->loadList();
}

?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th nowrap="nowrap" width="70px"> </th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_PROJECT'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_REQ'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_DESCRIPTION'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_SOURCE'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_OWNER'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_CATEGORY'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_PRIORITY'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_STATUS'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_VERSION'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_INCLUSIONDATE'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_CONCLUSIONDATE'); ?></th>
    </tr>
    
<?php foreach ($list as $row) { ?>
        <tr>
            <td>
                <a href="index.php?m=scopeplanning&a=view_requirement&reqid=<?php echo $row['req_id'] ?>"><?php echo $AppUI->_("LBL_SP_VIEW"); ?></a>
                <a>|</a>
                <a href="index.php?m=scopeplanning&a=addedit_requirement&reqid=<?php echo $row['req_id'] ?>&projid=<?php echo $row['project_id'] ?>"><?php echo $AppUI->_("LBL_SP_EDIT"); ?></a>               
            </td>
            <td><?php echo $row['project_name'] ?></td>
            <td><?php echo $row['req_idname'] ?></td>
            <td><?php echo $row['req_description'] ?></td>
            <td><?php echo $row['req_source'] ?></td> 
            <td><?php echo $row['req_owner'] ?></td>
            <td><?php echo $row['req_categ_name'] ?></td>
            <td><?php echo $row['req_priority_id'] ?></td>
            <td><?php echo $row['req_status_id'] ?></td>
            <td><?php echo $row['req_version'] ?></td>
            <td><?php echo $row['req_inclusiondate'] ?></td>
            <td><?php echo $row['req_conclusiondate'] ?></td>
        </tr>
<?php } ?>
</table>
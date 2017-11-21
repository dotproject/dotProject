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
$q->addQuery('req_id, req_idname, req_description, req_source, req_owner, s.req_categ_prefix_id, 
    req_priority_id, req_status_id, req_version, req_inclusiondate, req_conclusiondate, 
    eapitem_id, req_testcase, s.project_id, p.project_id, project_name, req_categ_name, id, number, item_name');
$q->addTable('scope_requirements', 's');
$q->addJoin('projects', 'p', 'p.project_id = s.project_id');
$q->addJoin('scope_requirement_categories', 'c', 's.req_categ_prefix_id = c.req_categ_prefix_id');
$q->leftJoin('project_eap_items', 'e', 'e.id = s.eapitem_id');
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
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_WBSITEM'); ?></th> 
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_TESTECASE'); ?></th> 
    </tr>
    
<?php foreach ($list as $row) { ?>
        <tr>
            <td>
                <a href="index.php?m=scopeplanning&a=view_requirement_tracmatrix&reqid=<?php echo $row['req_id'] ?>&projid=<?php echo $row['project_id'] ?>"><?php echo $AppUI->_("LBL_SP_VIEW"); ?></a>
                <a>|</a>
                <a href="index.php?m=scopeplanning&a=addedit_requirement_tracmatrix&reqid=<?php echo $row['req_id'] ?>&projid=<?php echo $row['project_id'] ?>"><?php echo $AppUI->_("LBL_SP_EDIT"); ?></a>               
            </td>
            <td><?php echo $row['project_name'] ?></td>
            <td><?php echo $row['req_idname'] ?></td>
            <td><?php echo $row['req_description'] ?></td>
            <td><?php echo $row['req_source'] ?></td> 
            <td><?php echo $row['number'] ?><?php echo ' ' ?><?php echo $row['item_name'] ?></td><!-- from table project_eap_items, find it with 'eapitem_id from requirements table' -->
            <td><?php echo $row['req_testcase'] ?></td>
        </tr>
<?php } ?>
</table>

<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once($AppUI->getModuleClass('timeplanning'));

$q = new DBQuery();
$q->addQuery('project_id, project_name');//add a parameter on a query
$q->addTable('projects');//add a table on a query
$q->addOrder('project_id');//add the 'order' clause on a query
if(isset($_POST['project_id']) && $_POST['project_id'] != '0'){    
    $q->addwhere('project_id='.$_POST['project_id']);
    $list = $q->loadList();
}else{
    $list = $q->loadList();
}

?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th nowrap="nowrap" width="150px"> </th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_PROJECT'); ?></th>        
    </tr>
    
<?php foreach ($list as $row) { ?>
        <tr>
            <td>                
                <a href="index.php?m=scopeplanning&a=view_wbs_dic&project_id=<?php echo $row['project_id'] ?>&project_name=<?php echo $row['project_name'] ?>"><?php echo $AppUI->_("LBL_SP_VIEW"); ?></a>
                <a>|</a>
                <a href="index.php?m=scopeplanning&a=addedit_wbs_dic&project_id=<?php echo $row['project_id'] ?>&project_name=<?php echo $row['project_name'] ?>"><?php echo $AppUI->_("LBL_SP_ADDEDIT"); ?></a>               
            </td>
            <td><?php echo $row['project_name'] ?></td>            
        </tr>
<?php } ?>
</table>
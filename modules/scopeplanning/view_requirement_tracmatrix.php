<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

//load requirements management plan's id
$req_id = intval(dPgetParam($_GET, 'reqid', 0)); //'id' is sent by vw_req_managplan.php on clicking 'View'
$project_id = intval(dPgetParam($_GET, 'projid', 0));

$titleBlock = new CTitleBlock('LBL_SP_REQTRAC', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('scope_requirements', 's');
$q->addWhere('req_id =' . $req_id);
$q->addJoin('projects', 'p', 'p.project_id = s.project_id');
$q->addJoin('scope_requirement_categories', 'c', 's.req_categ_prefix_id = c.req_categ_prefix_id');
$q->addJoin('project_eap_items', 'e', 'e.id = s.eapitem_id');
$q->addOrder('p.project_id');

$obj = new CReqManagPlan();

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $req_id > 0) {
    $AppUI->setMsg('LBL_SP_SCOPEPLANNING');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

$eapitemsList = new DBQuery();
$eapitemsList->addQuery('e.number, e.item_name');
$eapitemsList->addTable('project_eap_items', 'e');
$eapitemsList->addWhere('e.project_id = ' . $project_id);
$eapitemsList = $eapitemsList->loadList();
?>


<table align="left" border="0" cellpadding="10" cellspacing="5" style="width: 100%" class="std" name="threads" charset=UTF-8>
    <tr>
        <td valign="top" width="100"><?php echo $AppUI->_('LBL_SP_PROJECT'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->project_name); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_REQ'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_idname); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_DESCRIPTION'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_description); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_SOURCE'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_source); ?></td>
    </tr>       
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_WBSITEM'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->number); ?><?php echo ' ' ?><?php echo dPformSafe(@$obj->item_name); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_TESTECASE'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_testcase); ?></td>
    </tr>    
    <tr>
        <td><input name="btn_back" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_RETURN'); ?>"
                   onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
        <td></td>
    </tr>
</table>
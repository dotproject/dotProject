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
//$project_id = intval(dPgetParam($_GET, 'projectid', 0));

$titleBlock = new CTitleBlock('LBL_SP_REQ', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('scope_requirements', 's');
$q->addWhere('req_id =' . $req_id);
$q->addJoin('projects', 'p', 'p.project_id = s.project_id');
$q->addJoin('scope_requirement_categories', 'c', 's.req_categ_prefix_id = c.req_categ_prefix_id');
$q->addOrder('p.project_id');

$obj = new CReqManagPlan();

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $req_id > 0) {
    $AppUI->setMsg('LBL_SP_SCOPEPLANNING');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

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
        <td valign="top"><?php echo $AppUI->_('LBL_SP_OWNER'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_owner); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_CATEGORY'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_categ_name); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_PRIORITY'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_priority_id); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_STATUS'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_status_id); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_VERSION'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_version); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_INCLUSIONDATE'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_inclusiondate); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_CONCLUSIONDATE'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_conclusiondate); ?></td>
    </tr>
    <tr>
        <td><input name="btn_back" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_RETURN'); ?>"
                   onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
        <td></td>
    </tr>
</table>
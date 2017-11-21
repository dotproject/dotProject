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
$req_managplan_id = intval(dPgetParam($_GET, 'managplanid', 0)); //'id' is sent by vw_req_managplan.php on clicking 'View'
$project_id = intval(dPgetParam($_GET, 'projectid', 0));

$titleBlock = new CTitleBlock('LBL_SP_REQMANAGPLAN', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*'); //select
$q->addTable('scope_requirements_managplan', 's'); //from
$q->addWhere('req_managplan_id = ' . $req_managplan_id); //where
$q->addJoin('projects', 'p', 's.project_id = p.project_id');

$obj = new CReqManagPlan();

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $req_managplan_id > 0) {
    $AppUI->setMsg('LBL_SP_SCOPEPLANNING');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

?>

<table align="left" border="0" cellpadding="10" cellspacing="3" style="width: 100%" class="std" name="threads" charset=UTF-8>      
    <tr><td align="left" width="180px" nowrap="nowrap"><?php echo $AppUI->_("LBL_SP_PROJECT"); ?>:</td>
        <td><?php echo dPformSafe(@$obj->project_name); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_REQCOLLECT'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_managplan_collect_descr); ?></td>                                                
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_CATEGORY'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_managplan_reqcategories); ?></td>            
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_PRIORITY'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_managplan_reqprioritization); ?></td>            
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_TRACEABILITY'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_managplan_trac_descr); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_CONFIGMANAG'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_managplan_config_descr); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_VERIFICATION'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_managplan_verif_descr); ?></td>
    </tr>
    <tr>
        <td><input name="btn_back" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_RETURN'); ?>"
                   onclick="history.back(-1)"/></td>
        <td/></td>
    </tr>
</table>

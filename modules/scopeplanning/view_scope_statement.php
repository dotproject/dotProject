<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

//load requirements scope id
$scope_id = intval(dPgetParam($_GET, 'scopeid', 0)); 
$project_id = intval(dPgetParam($_GET, 'projectid', 0));

$titleBlock = new CTitleBlock('LBL_SP_SCOPESTAT', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*'); //select
$q->addTable('scope_statement', 's'); //from
$q->addWhere('scope_id = ' . $scope_id); //where
$q->addJoin('projects', 'p', 's.project_id = p.project_id');

$obj = new CReqManagPlan();

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $scope_id > 0) {
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
            <td valign="top"><?php echo $AppUI->_('LBL_SP_SCOPE'); ?>:</td>
            <td><?php echo dPformSafe(@$obj->scope_description); ?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_ACCEPTANCECRIT'); ?>:</td>
            <td><?php echo dPformSafe(@$obj->scope_acceptancecriteria); ?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_DELIVERABLES'); ?>:</td>
            <td><?php echo dPformSafe(@$obj->scope_deliverables); ?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_EXCLUSIONS'); ?>:</td>
            <td><?php echo dPformSafe(@$obj->scope_exclusions); ?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_RESTRICTIONS'); ?>:</td>
            <td><?php echo dPformSafe(@$obj->scope_constraints); ?></td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_ASSUMPTIONS'); ?>:</td>
            <td><?php echo dPformSafe(@$obj->scope_assumptions); ?></td>
        </tr>
        <tr>
            <td><input name="btn_back" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_RETURN'); ?>"
                   onclick="history.back(-1)"/></td> 
            <td/></td>
        </tr>
</table>

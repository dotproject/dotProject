<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;
require_once (DP_BASE_DIR . "/modules/human_resources/human_resources.class.php");
$company_id = intval(dPgetParam($_GET, 'company_id', null));
$edit = intval(dPgetParam($_GET, 'edit', null));

$query = new DBQuery();
$query->addTable('company_policies', 'p');
$query->addQuery('company_policies_id');
$query->addWhere('p.company_policies_company_id = ' . $company_id);
$res = & $query->exec();
$company_policies_id = $res->fields['company_policies_id'];
$query->clear();

$policies = new CCompaniesPolicies();
if ($company_policies_id && !$policies->load($company_policies_id)) {
    $AppUI->setMsg('Company policies');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}

if ($company_policies_id && $edit != 1) {
    ?>
    <script>
        function editPolicies(){
            window.location="index.php?m=human_resources&a=vw_policies&company_id=<?php echo $company_id; ?>&edit=1";
        }
    </script>
    <!-- Edit button is commendted because this screen is now always opened in edit mode.
    <input type="button" value="<?php echo $AppUI->_('LBL_EDIT'); ?>" class="button" onclick="editPolicies()" style="margin-bottom: 15px;" />
    -->
    <?php
}
if (/*$edit || !$company_policies_id*/ true) { //Now always display it in edit mode, because it is show in company main form.
    ?>
    <script src="./modules/human_resources/vw_policies.js"></script>
    <link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
    <form name="editfrm" action="?m=human_resources" method="post">
        <input type="hidden" name="dosql" value="do_policies_aed" />
        <input type="hidden" name="company_policies_id" value="<?php echo dPformSafe($company_policies_id); ?>" />
        <input type="hidden" name="company_policies_company_id" value="<?php echo dPformSafe($company_id); ?>" />
        <table cellspacing="1" cellpadding="1" border="0" width="100%" align="center" class="std">
            <tr><th colspan="2"><b><?php echo $AppUI->_("LBL_ORGANIZATIONAL_POLICY"); ?></b></th></tr>
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Rewards and recognition'); ?>:</td>
                <td><textarea name='company_policies_recognition' cols="90" rows="8"><?php echo dPformSafe($policies->company_policies_recognition); ?></textarea></td>
            </tr>
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Regulations, standards, and policy compliance'); ?>:</td>
                <td><textarea name='company_policies_policy' cols="90" rows="8"><?php echo dPformSafe($policies->company_policies_policy); ?></textarea></td>
            </tr>
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Safety'); ?>:</td>
                <td><textarea name='company_policies_safety' cols="90" rows="8"><?php echo dPformSafe($policies->company_policies_safety); ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <input type="button" value="<?php echo ucfirst($AppUI->_('submit')); ?>" class="button" onclick="submitPolicies(document.editfrm);"/>
                    <input class="button" type="button" onclick="confirmGoBack()" value="<?php echo ucwords($AppUI->_('LBL_CANCEL')); ?>" />
                </td>
            </tr>
        </table>
    </form>
    <?php
} else {
    ?>
    <table border="0" width="100%" align="center" cellpadding="4" cellspacing="0" width="100%" class="std" summary="human_resources">
        <tr><th colspan="2"><b>Política Organizacional</b></th></tr>

        <tr>
            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Rewards and recognition'); ?>:</td>
            <td class="hilite" width="100%"><?php echo $policies->company_policies_recognition; ?></td>
        </tr>
        <tr>
            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Regulations, standards, and policy compliance'); ?>:</td>
            <td class="hilite" width="100%"><?php echo $policies->company_policies_policy; ?></td>
        </tr>
        <tr>
            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Safety'); ?>:</td>
            <td class="hilite" width="100%"><?php echo $policies->company_policies_safety; ?></td>
        </tr>

    </table>
    <?php
}
?>

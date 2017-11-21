<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $tabbed, $currentTabName, $currentTabId, $AppUI;

$company_id = intval(dPgetParam($_GET, 'company_id', 0));
?>


<?php
$query = new DBQuery;
$query->addTable('human_resources_role', 'r');
$query->addQuery('r.*');
$query->addWhere('r.human_resources_role_company_id = ' . $company_id);
$res_companies = & $query->exec();
?>

<table width="95%" align="center" border='0' cellpadding='2' cellspacing='1' class='tbl'>
    <caption> <b><?php echo $AppUI->_("LBL_ORGANIZATION_ROLES"); ?></b> </caption>
    <tr>
        <th width='3%'>&nbsp;</th>
        <th nowrap='nowrap' width='10%'>
            <?php echo $AppUI->_('Role name'); ?>
        </th>
        <th nowrap='nowrap' width='29%'>
            <?php echo $AppUI->_('Role responsability'); ?>
        </th>
        <th nowrap='nowrap' width='29%'>
            <?php echo $AppUI->_('Role authority'); ?>
        </th>
        <th nowrap='nowrap' width='29%'>
            <?php echo $AppUI->_('Role competence'); ?>
        </th>
    </tr>

    <?php
    require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
    for ($res_companies; !$res_companies->EOF; $res_companies->MoveNext()) {
        $human_resources_role_id = $res_companies->fields['human_resources_role_id'];
        $configured = isConfiguredRole($human_resources_role_id);
        $style = $configured ? '' : 'background-color:#ED9A9A; font-weight:bold';
        ?>
        <tr>
            <td style=<?php echo $style; ?>>
                <a href="index.php?m=human_resources&a=view_role&human_resources_role_id=<?php echo $human_resources_role_id; ?>&company_id=<?php echo $company_id; ?>&edit=1">
                    <img src="./modules/costs/images/stock_edit-16.png" border="0" width="12" height="12">
                </a>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields['human_resources_role_name']; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields['human_resources_role_responsability']; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields['human_resources_role_authority']; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields['human_resources_role_competence']; ?>
            </td>
        </tr>
        <?php
    }
    $query->clear();
    ?>
</table>
<table width="95%" align="center">
    <tr>
        <td style="text-align: right">
            <form action="?m=human_resources&amp;a=view_role&amp;company_id=<?php echo $company_id; ?>" method="post">
                <input type="submit" class="button" value="<?php echo $AppUI->_('LBL_NEW_ROLE') ?>"  />
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_no_ask.php"); ?>
            </form>
            
        </td>
    </tr>
</table>
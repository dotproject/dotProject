<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$project = new CProject();
$project->load($projectSelected);
$company_id = $project->project_company;
$compartionDateFunction= strtotime($project->project_start_date) != false && strtotime($project->project_end_date) != false;
$compartionEmptyFormat= $project->project_start_date!="0000-00-00 00:00:00" && $project->project_end_date != "0000-00-00 00:00:00";
if ($compartionDateFunction && $compartionEmptyFormat) {
    require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";
    $cost_id = intval(dPgetParam($_GET, 'cost_id', 0));

    $perms = & $AppUI->acl();

    $q = new DBQuery;
    $q->clear();
    $q->addQuery('*');
    $q->addTable('costs');
    $q->addWhere('cost_project_id = ' . $projectSelected);

// check if this record has dependancies to prevent deletion
    $msg = '';
// load the record data
    $obj = null;
    if ((!db_loadObject($q->prepare(), $obj)) && ($cost_id > 0)) {
        $AppUI->setMsg('Estimative Costs');
        $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
        $AppUI->redirect();
    }

    /* Funcao para inserir na tabela de custos  */
    insertCostValues($projectSelected);

    $whereProject = '';
    if ($projectSelected != null) {
        $whereProject = ' and cost_project_id=' . $projectSelected;
    }

    /* transform date to dd/mm/yyyy */
    $date_begin = intval($obj->cost_date_begin) ? new CDate($obj->cost_date_begin) : null;
    $date_end = intval($obj->cost_date_end) ? new CDate($obj->cost_date_end) : null;
    $df = $AppUI->getPref('SHDATEFORMAT');

// Get humans estimatives
    $humanCost = getResources("Human", $whereProject);

// Get non humans estimatives
    $notHumanCost = getResources("Non-Human", $whereProject);
    ?>

    <table width="95%" align="center">
        <tr>
            <td align="right">
                <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/costs/view_budget.php';submitMenuForm();" value="<?php echo $AppUI->_("LBL_PROJECT_BUDGET") ?>" onclick="submitMenuForm()" />
            </td>
        </tr>
    </table>
    <!-- ############################## ESTIMATIVAS CUSTOS HUMANOS ############################################ -->

    <table align="center" width="95%" border="0">
        <tr>
            <td nowrap='nowrap' width='95%' style="color:#000000">
                <?php echo $AppUI->_("LBL_COST_HUMAN_RESOURCE_HELP", UI_OUTPUT_JS); ?>
            </td>
        </tr>
    </table>

    <table align="center" width="95%">
        <tr>
            <td align="right">
                <form action="?m=companies&a=view&company_id=<?php echo $company_id; ?>&rh_config=1&tab=3" method="POST">
                    <input class="button" type="submit" value="<?php echo $AppUI->_("LBL_CONFIG_RH"); ?>" />
                </form>
            </td>
        </tr>
    </table>


    <table align="center"  width="95%" border="0" cellpadding="3" cellspacing="3" class="tbl">

        <tr>
            <th nowrap='nowrap' width='100%' colspan="7">
                <?php echo $AppUI->_('Human Resource Estimative'); ?>
            </th>
        </tr>
        <tr>
            <th nowrap="nowrap" width="1%"></th>
            <th nowrap="nowrap" width="20%"><?php echo $AppUI->_('Name'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('Date Begin'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('Date End'); ?></th>
            <th nowrap="nowrap" width="10%"><?php echo $AppUI->_('Hours/Month'); ?></th>
            <th nowrap="nowrap" width="15%"><?php echo $AppUI->_('Hour Cost'); ?>  &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>)</th>
            <th nowrap="nowrap" ><?php echo $AppUI->_("Total Cost"); ?>&nbsp;(<?php echo dPgetConfig("currency_symbol") ?>)
            </th>
        </tr>
        <?php
        foreach ($humanCost as $row) {
            /* transform date to dd/mm/yyyy */
            $date_begin = intval($row['cost_date_begin']) ? new CDate($row['cost_date_begin']) : null;
            $date_end = intval($row['cost_date_end']) ? new CDate($row['cost_date_end']) : null;
            ?>
            <tr>
                <td nowrap="nowrap" align="center">
                    <a href="index.php?m=costs&a=addedit_costs&cost_id=<?php echo ($row['cost_id']); ?>&project_id=<?php echo $projectSelected ?>">
                        <img src="./modules/costs/images/stock_edit-16.png" border="0" width="12" height="12">
                    </a>
                </td>
                <td nowrap="nowrap"><?php echo $row['cost_description']; ?></td>
                <td nowrap="nowrap"><?php echo $date_begin ? $date_begin->format($df) : ''; ?></td>
                <td nowrap="nowrap"><?php echo $date_end ? $date_end->format($df) : ''; ?></td>
                <td nowrap="nowrap" style="text-align: center"><?php echo $row['cost_quantity']; ?></td>
                <td nowrap="nowrap" style="text-align: right"><?php echo number_format($row['cost_value_unitary'], 2, ',', '.'); ?></td>
                <td nowrap="nowrap" style="text-align: right"><?php echo number_format($row['cost_value_total'], 2, ',', '.'); ?></td>
            </tr>
            <?php
            $sumH = $sumH + $row['cost_value_total'];
        }
        ?>
        <tr>
            <td nowrap="nowrap" align="right" colspan="6" cellpadding="3"> <b><?php echo $AppUI->_("Subtotal Human Estimatives"); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):  </b> </td>
            <td nowrap="nowrap" cellpadding="3" style="text-align: right"><b><?php echo number_format($sumH, 2, ',', '.'); ?></b></td>
        </tr>
    </table>
    <table width="95%" align="center">
        <tr>
            <td>
                <span style='color:red'>*</span>
                <span style='color:black;font-size: 11px'>
                    <?php echo $AppUI->_("LBL_RH_AUTOMATICALLY_ADDED_COST_BASELINE") ?>
                </span>
            </td>
        </tr>
    </table>

    <br />
    <!-- ############################## ESTIMATIVAS CUSTOS NAO HUMANOS ############################################ -->
    <table align="center" width="95%">
        <tr>
            <td align="right">
                <form action="?m=costs&a=addedit_costs_not_human&project_id=<?php echo $projectSelected ?>" method="post">
                    <input type="submit" class="button" value="<?php echo $AppUI->_("LBL_INCLUDE_NON_HUMAN_RESOURCE", UI_OUTPUT_JS) ?>" />
                </form>
            </td>
        </tr>
    </table>

    <table align="center" width="95%" border="0" cellpadding="3" cellspacing="3" class="tbl">
        <tr>
            <th nowrap='nowrap' width='100%' colspan="7">
                <?php echo $AppUI->_('Non-Human Resource Estimative'); ?>
            </th>
        </tr>
        <tr>
            <th nowrap="nowrap" width="1%"></th>
            <th nowrap="nowrap" width="20%"><?php echo $AppUI->_('Description'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('Date Begin'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('Date End'); ?></th>
            <th nowrap="nowrap" width="10%"><?php echo $AppUI->_('Quantity'); ?></th>
            <th nowrap="nowrap" width="15%"><?php echo $AppUI->_('Unitary Cost'); ?>  &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>)</th>
            <th nowrap="nowrap"><?php echo $AppUI->_('Total Cost'); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>)</th>
        </tr>
        <?php
        foreach ($notHumanCost as $row) {
            /* transform date to dd/mm/yyyy */
            $date_begin = intval($row['cost_date_begin']) ? new CDate($row['cost_date_begin']) : null;
            $date_end = intval($row['cost_date_end']) ? new CDate($row['cost_date_end']) : null;
            ?>
            <tr>
                <td nowrap="nowrap" align="center">
                    <a href="index.php?m=costs&a=addedit_costs_not_human&cost_id=<?php echo($row['cost_id']); ?>&project_id=<?php echo $projectSelected ?>">
                        <img src="./modules/costs/images/stock_edit-16.png" border="0" width="12" height="12">
                    </a>
                </td>
                <td nowrap="nowrap"><?php echo $row['cost_description']; ?></td>
                <td nowrap="nowrap"><?php echo $date_begin ? $date_begin->format($df) : ''; ?></td>
                <td nowrap="nowrap"><?php echo $date_end ? $date_end->format($df) : ''; ?></td>
                <td nowrap="nowrap" style="text-align: center"><?php echo $row['cost_quantity']; ?></td>
                <td nowrap="nowrap" style="text-align: right"><?php echo number_format($row['cost_value_unitary'], 2, ',', '.'); ?></td>
                <td nowrap="nowrap" style="text-align: right"><?php echo number_format($row['cost_value_total'], 2, ',', '.'); ?></td>
            </tr>
            <?php
            $sumNH = $sumNH + $row['cost_value_total'];
        }
        ?>
        <tr>
            <td nowrap="nowrap" align="right" colspan="6" cellpadding="3"> <b><?php echo $AppUI->_("Subtotal Not Human Estimatives"); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>): </b> </td>
            <td nowrap="nowrap" cellpadding="3" style="text-align: right"><b><?php echo number_format($sumNH, 2, ',', '.'); ?></b></td>
        </tr>
    </table>
    <?php
} else {
    ?>
    <br />
    <div>
        <span style="color:#F00">*</span>
    <?php echo $AppUI->_("LBL_COST_PLANNING_NEEDS_PROJECT_DATES"); ?>
        </div>
<?php
    }
?>
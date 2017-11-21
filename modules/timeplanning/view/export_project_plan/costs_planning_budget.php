<?php
require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";
global $subTotal;// this variable comes from costs baseline line file that has to be processed before of this.
$q = new DBQuery();
$q->addQuery("*");
$q->addTable("budget");
$q->addWhere("budget_project_id = " . $projectId);
$q->addOrder("budget_id");
$v = $q->exec();
?>
<table class="printTable" >
    <tr>
        <th colspan="3">
            <?php echo $AppUI->_("Budget",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <tr>
        <th ><?php echo $AppUI->_("Managememt Reserve(%)",UI_OUTPUT_HTML); ?></th>
        <th ><?php echo $AppUI->_("Subtotal Budget",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</th>
        <th ><?php echo $AppUI->_("Total Value",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>) </th>
    </tr>
    <tr>
        <td style="text-align:center"><?php echo $v->fields["budget_reserve_management"] ?></td>
        <td style="text-align:right"><?php echo number_format($subTotal, 2, ",", "."); ?> </td>
        <td style="text-align:right"><?php
                $budget = ($subTotal + ($subTotal * ($v->fields["budget_reserve_management"] / 100)));
                echo number_format($budget, 2, ",", ".");
              ?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <b> <?php echo $AppUI->_("Total Budget",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>): <?php echo number_format($budget, 2, ",", "."); ?></b>
        </td>
    </tr>
</table>
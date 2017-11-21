<?php
require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";
$whereProject = "";
if ($projectId != null) {
    $whereProject = " and cost_project_id=" . $projectId;
}
// Get humans estimatives
$humanCost = getResources("Human",$whereProject);
// Get not humans estimatives
$notHumanCost = getResources("Non-Human",$whereProject);
$df = $AppUI->getPref("SHDATEFORMAT");
?>


<!-- ############################## ESTIMATIVAS CUSTOS HUMANOS ############################################ -->
<table class="printTable">
    <tr>
        <th colspan="6">
            <?php echo $AppUI->_("Human Resource Estimative",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <tr>
        <th><?php echo $AppUI->_("Name",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Date Begin",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Date End",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Hours/Month",UI_OUTPUT_HTML); ?></th>
        <th ><?php echo $AppUI->_("Hour Cost",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</th>
        <th><?php echo $AppUI->_("Total Cost",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</th>
    </tr>
    <?php
    foreach ($humanCost as $row) {
        /* transform date to dd/mm/yyyy */
        $date_begin = intval($row["cost_date_begin"]) ? new CDate($row["cost_date_begin"]) : null;
        $date_end = intval($row["cost_date_end"]) ? new CDate($row["cost_date_end"]) : null;
        ?>
        <tr>
            <td><?php echo $row["cost_description"]; ?></td>
            <td><?php echo $date_begin ? $date_begin->format($df) : ""; ?></td>
            <td><?php echo $date_end ? $date_end->format($df) : ""; ?></td>
            <td style="text-align:center"><?php echo $row["cost_quantity"]; ?></td>
            <td style="text-align:right"><?php echo number_format($row["cost_value_unitary"], 2, ",", ".");  ?></td>
            <td style="text-align:right"><?php echo number_format($row["cost_value_total"], 2, ",", "."); ?></td>
        </tr>
        <?php
        $sumH = $sumH + $row["cost_value_total"];
    }
    ?>
    <tr>
        <td align="right" colspan="5" > 
            <b>
                <?php echo $AppUI->_("Subtotal Human Estimatives",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)
            </b> 
        </td>
        <td style="text-align:right"><b><?php echo number_format($sumH, 2, ",", "."); ?></b></td>

    </tr>
    <!-- ############################## ESTIMATIVAS CUSTOS NAO HUMANOS ############################################ -->
    <tr>
        <th colspan="6">
            <?php echo $AppUI->_("Non-Human Resource Estimative",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <tr>
        <th><?php echo $AppUI->_("Description",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Date Begin",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Date End",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Quantity",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("Unitary Cost",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</th>
        <th><?php echo $AppUI->_("Total Cost",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</th>
    </tr>
    <?php
    foreach ($notHumanCost as $row) {
        /* transform date to dd/mm/yyyy */
        $date_begin = intval($row["cost_date_begin"]) ? new CDate($row["cost_date_begin"]) : null;
        $date_end = intval($row["cost_date_end"]) ? new CDate($row["cost_date_end"]) : null;
        ?>
        <tr>
            <td ><?php echo $row["cost_description"]; ?></td>
            <td ><?php echo $date_begin ? $date_begin->format($df) : ""; ?></td>
            <td ><?php echo $date_end ? $date_end->format($df) : ""; ?></td>
            <td style="text-align:center"><?php echo $row["cost_quantity"]; ?></td>
            <td style="text-align:right"><?php echo number_format($row["cost_value_unitary"], 2, ",", "."); ?></td>
            <td style="text-align:right"><?php echo number_format($row["cost_value_total"], 2, ",", "."); ?></td>
        </tr>
        <?php
        $sumNH = $sumNH + $row["cost_value_total"];
    }
    ?>
    <tr>
        <td align="right" colspan="5"> 
            <b> 
                <?php echo $AppUI->_("Subtotal Not Human Estimatives",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)
            </b>
        </td>
        <td style="text-align:right"><b><?php echo number_format($sumNH, 2, ",", "."); ?></b></td>
    </tr>
</table>




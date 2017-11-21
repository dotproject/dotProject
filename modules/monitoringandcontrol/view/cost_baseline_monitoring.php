<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";
require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
require_once DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_earn_value.class.php";
$controllerEarnValue = new ControllerEarnValue();
$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$q = new DBQuery();
$q->clear();
$q->addQuery("*");
$q->addTable("budget");
$q->addWhere("budget_Project_id = " . $projectSelected);
$v = $q->exec();
$budget_id = $v->fields["budget_id"];
$whereProject = ' and cost_project_id=' . $projectSelected;

// Get humans estimatives
$q->clear();
$q->addQuery('*');
$q->addTable('costs');
$q->addWhere("cost_type_id = '0' $whereProject");
$q->addOrder('cost_description');
$humanCost = $q->loadList();

// Get not humans estimatives
$q->clear();
$q->addQuery('*');
$q->addTable('costs');
$q->addWhere("cost_type_id = '1' $whereProject");
$q->addOrder('cost_description');
$notHumanCost = $q->loadList();

//get project start and end dates
$q->clear();
$q->addQuery('project_start_date,project_end_date');
$q->addTable('projects');
$q->addWhere("project_id = '$projectSelected'");
$datesProject = $q->exec();
//caulculates the project planned months among its year(s)
$meses = diferencaMeses(substr($datesProject->fields['project_start_date'], 0, -9), substr($datesProject->fields['project_end_date'], 0, -9));
$monthStartProject = substr($datesProject->fields['project_start_date'], 5, -12);
$monthEndProject = substr($datesProject->fields['project_end_date'], 5, -12);
$monthSProject = substr($datesProject->fields['project_start_date'], 5, -12);
$yearStartProject = substr($datesProject->fields['project_start_date'], 0, -15);
$yearEndProject = substr($datesProject->fields['project_end_date'], 0, -15);
$years = $yearEndProject - $yearStartProject;
$tempYear = $yearStartProject;

//$tempMeses is the quantity of months within a year (the first year in this case). It is u´pdate for each year
//$meses is the absolut quantity of months
if ($years == 0) {
    //$tempMeses = $monthEndProject - $monthStartProject;
    $tempMeses = 0;
    for ($i = 0; $i <= $meses; $i++) {
        $tempMeses++;
    }
} else {
    $tempMeses = (12 - $monthStartProject) + 1;
}

$c = 0;
$counter = 1;
//set an array with the index for each month/year
$monthsYearsIndex = array();
$index_month = $monthStartProject;
$index_year = $yearStartProject;
for ($i = 0; $i <= $meses; $i++) {
    $monthPrefix = strlen($index_month) < 2 ? "0" : "";
    $monthsYearsIndex[$index_year . "_" . $monthPrefix . $index_month] = $i;
    if ($index_month == 12) {
        $index_month = 1;
        $index_year++;
    }
    $index_month++;
}

//define colors
$realCostPositiveColor = "#006400";
$realCostNegativeColor = "#ff0000";
$plannedCostPositiveColor = "limegreen";
$plannedCostNegativeColor = "#d33";

?>

<style>
    .tableComparisionRealPlanned{
        border: 1px solid silver;
        width:100%;

    }
    .tableComparisionRealPlanned td:first-child  {
        border-bottom: 1px solid silver;
    }

    .plannedCostCell{
        color:#564;
    }
</style>



<!-- ############################## ESTIMATIVAS CUSTOS HUMANOS ############################################ -->
<table width="100%" border="0" cellpadding="3" cellspacing="3" class="tbl">
    <caption> 
        <span style="font-weight: bold">
            <?php echo $AppUI->_("LBL_TITLE_MONITORING_COST_BASELINE") ?>
        </span>
        <br />
        <span class="plannedCostCell">
            * <?php echo $AppUI->_("LBL_LEGEND_MONITORING_COST_BASELINE") ?>
        </span>
    </caption>
    <tr>
        <th nowrap='nowrap'><?php echo $AppUI->_("Year"); ?></th>
        <?php
        for ($i = 0; $i <= $years; $i++) {
            ?>
            <th nowrap="nowrap" colspan="<?php echo $tempMeses ?>">
                <?php echo $tempYear; ?>
            </th>
            <?php
            $tempMeses = ($meses - $tempMeses) + 1;
            $ns = $tempMeses - 12;
            if ($ns > 0)
                $tempMeses = 12;
            $tempYear++;
        }
        ?>
        <th>&nbsp;</th> <!-- Last column empty due to it is used to present the totals -->
    </tr>
    <!-- Create a line fot header, a cell for each month/year -->
    <tr>
        <th nowrap="nowrap" width="15%"><?php echo $AppUI->_('Item'); ?></th>
        <?php
        for ($i = 0; $i <= $meses; $i++) {
            $mes = $monthStartProject;
            $monthStartProject++;
            if ($mes == 12)
                $monthStartProject = 1;
            ?>
            <th style="text-align: center; text-wrap: none">
                <?php echo strlen($mes) < 2 ? "0" . $mes : $mes; ?>
            </th>
            <?php
            $counter++;
        }
        ?>
        <th nowrap="nowrap" width="10%"><?php echo $AppUI->_('Total Cost'); ?> (<?php echo dPgetConfig("currency_symbol") ?>)</th>
    </tr>

    <!-- Start with human resource -->
    <tr>
        <td nowrap='nowrap' align="center" colspan="<?php echo $meses + 3 ?>"><!-- + 3 is because of the <td> used for labels and totals that are unecessary for this <tr>   -->
            <b><?php echo $AppUI->_('HUMAN RESOURCE ESTIMATIVE'); ?></b>
        </td>
    </tr>
    <?php
    $sumTotalRealHR = 0;
    foreach ($humanCost as $row) {
        ?>
        <tr>
            <td nowrap="nowrap"><?php echo $row['cost_description']; ?></td>
            <?php
            $user_id = getUserIdByHR($row["cost_human_resource_id"]);
            $totalRealHR = 0; //sum all real costs for one HR.
            $mStartProject = substr($datesProject->fields['project_start_date'], 5, -12);
            $monthStart = substr($row["cost_date_begin"], 5, -12);
            $yearStart = substr($row["cost_date_begin"], 0, -15);
            $monthEnd = substr($row["cost_date_end"], 5, -12);
            $yearEnd = substr($row["cost_date_end"], 0, -15);
            $key = $yearStart . "_" . $monthStart;
            $startIndex = $monthsYearsIndex[$key];
            $diffMonths = diferencaMeses(substr($row["cost_date_begin"], 0, -9), substr($row["cost_date_end"], 0, -9));
            if ($diffMonths < 0) {
                $diffMonths = 0;
            } else if ($diffMonths >= count($monthsYearsIndex)) {
                $diffMonths = count($monthsYearsIndex) - 1; // this case the resource dates are longer than project dates, it will be limited by project dates.
            }
            $monthIterated = (int) (substr($monthStart, 0, 1) == "0" ? str_replace("0", "", $monthStart) : $monthStart);
            $yearIterated = (int) (substr($yearStart, 0, 1) == "0" ? str_replace("0", "", $yearStart) : $yearStart);
            for ($i = 0; $i <= $meses; $i++) {
                $mStartProject++;
                if ($i == $startIndex) {
                    if ($monthStart == $monthEnd && $yearEnd == $yearStart) { //exception for resources which lasts just a month
                        $mtz[$c][$k] = $row["cost_value_total"];
                        $realCostRH = $controllerEarnValue->getCostsByHR($projectSelected, $monthIterated, $yearIterated, $user_id);
                        $mtzRealHR[$c][$k] = $realCostRH;
                        ?>
                        <td>
                            <table class="tableComparisionRealPlanned">

                                <tr>
                                    <td style="text-align:right" class="plannedCostCell">
                                        <?php echo formatCellContent(number_format($mtz[$c][$k], 2, ",", ".")); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right">
                                        <?php
                                        $totalRealHR+=$realCostRH;
                                        echo formatCellContent(number_format($realCostRH, 2, ",", "."));
                                        ?>
                                    </td>
                                </tr>

                            </table>
                        </td>

                        <?php
                    } else {
                        $k = $i;
                        for ($j = 0; $j <= $diffMonths; $j++) {
                            $mtz[$c][$k] = $row["cost_value_total"] / ($diffMonths + 1);
                            $realCostRH = $controllerEarnValue->getCostsByHR($projectSelected, $monthIterated, $yearIterated, $user_id);
                            $mtzRealHR[$c][$k] = $realCostRH;
                            ?>

                            <td>
                                <table class="tableComparisionRealPlanned">
                                    <tr>
                                        <td style="text-align:right" class="plannedCostCell">
                                            <?php echo formatCellContent(number_format($mtz[$c][$k], 2, ",", ".")); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="text-align:right">
                                            <?php
                                            $totalRealHR+=$realCostRH;
                                            echo formatCellContent(number_format($realCostRH, 2, ",", "."));
                                            ?>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <?php
                            //move one month foward
                            if ($monthIterated == 12) {
                                $monthIterated = 1;
                                $yearIterated++;
                            } else {
                                $monthIterated++;
                            }
                            $k++;
                        }
                        $i = $k - 1;
                    }
                } else {
                    ?>
                    <td>
                        <table class="tableComparisionRealPlanned">
                            <tr>
                                <td style="text-align:right" class="plannedCostCell">
                                    <?php echo formatCellContent(number_format(0, 2, ",", ".")); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">
                                    <?php
                                    $realCostRH = $controllerEarnValue->getCostsByHR($projectSelected, $monthIterated, $yearIterated, $user_id);
                                    $mtzRealHR[$c][$k] = $realCostRH;
                                    $totalRealHR+=$realCostRH;
                                    echo formatCellContent(number_format($realCostRH, 2, ",", "."));
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <?php
                }
            }

            $c++;
            ?>
            <td nowrap="nowrap" style="text-align: right">

                <table class="tableComparisionRealPlanned">
                    <?php
                    $realBackground = "color:" . ($totalRealHR > $row["cost_value_total"] ? "$realCostNegativeColor" : "$realCostPositiveColor");
                    $plannedBackground = "color:" . ($totalRealHR > $row["cost_value_total"] ? "$plannedCostNegativeColor" : "$plannedCostPositiveColor");
                    ?>
                    <tr>
                        <td style="text-align:right;<?php echo $plannedBackground ?>">
                            <?php echo formatCellContent(number_format($row['cost_value_total'], 2, ',', '.')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;<?php echo $realBackground ?>">
                            <?php
                            $sumTotalRealHR+=$totalRealHR;
                            echo formatCellContent(number_format($totalRealHR, 2, ",", "."));
                            ?>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <?php
        $sumH = $sumH + $row['cost_value_total'];
    }
    ?>
    <tr>
        <td nowrap="nowrap" width="15%" style="text-align: right"><b><?php echo $AppUI->_("Subtotal Human Estimatives"); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):  </b></td>
        <?php
        for ($i = 0; $i <= $meses; $i++) {
            $sum = 0;
            $sumReal = 0;
            ?>
            <td nowrap="nowrap" cellpadding="3" style="text-align: right">

                <table class="tableComparisionRealPlanned">

                    <tr>
                        <td style="text-align:right;font-weight: bold" class="plannedCostCell">
                            <?php
                            for ($j = 0; $j <= $c; $j++) {
                                $sum = $sum + $mtz[$j][$i];
                            }
                            $sumColumns[0][$i] = $sum;

                            echo formatCellContent(number_format($sum, 2, ",", "."));
                            ?> 
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:right;font-weight: bold">
                            <?php
                            for ($j = 0; $j <= $c; $j++) {
                                $sumReal = $sumReal + $mtzRealHR[$j][$i];
                            }
                            echo formatCellContent(number_format($sumReal, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                </table>
            </td>
            <?php
        }
        ?>
        <td nowrap="nowrap" cellpadding="3" style="text-align: right">


            <?php
            $realBackground = "color:" . ($sumTotalRealHR > $sumH ? "$realCostNegativeColor" : "$realCostPositiveColor");
            $plannedBackground = "color:" . ($sumTotalRealHR > $sumH ? "$plannedCostNegativeColor" : "$plannedCostPositiveColor");
            ?>

            <table class="tableComparisionRealPlanned">

                <tr>
                    <td style="text-align:right;font-weight: bold;<?php echo $plannedBackground ?>">
                        <?php echo formatCellContent(number_format($sumH, 2, ',', '.')); ?>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;font-weight: bold;<?php echo $realBackground ?>">
                        <?php
                        echo formatCellContent(number_format($sumTotalRealHR, 2, ",", "."));
                        ?>
                    </td>
                </tr>

            </table>


        </td>
    </tr>
    <br />
    <!-- ############################## ESTIMATIVAS CUSTOS NAO HUMANOS ############################################ -->
    <?php
    $c = 0;
    ?>

    <!-- Start non-human resouces -->
    <tr>
        <td nowrap='nowrap' align="center" colspan="<?php echo $meses + 3 ?>">
            <b> <?php echo $AppUI->_('NON-HUMAN RESOURCE ESTIMATIVE'); ?></b>
        </td>
    </tr>

    <?php
    $sumTotalRealNHR = 0;
    foreach ($notHumanCost as $row) {
        ?>
        <tr>
            <td nowrap="nowrap" width="15%"><?php echo $row['cost_description']; ?></td>
            <?php
            $resource_id = $row["cost_id"];
            $totalRealNHR = 0; //sum all real costs for one NHR.
            $mStartProject = substr($datesProject->fields['project_start_date'], 5, -12);
            $monthStart = substr($row["cost_date_begin"], 5, -12);
            $yearStart = substr($row["cost_date_begin"], 0, -15);
            $monthEnd = substr($row["cost_date_end"], 5, -12);
            $yearEnd = substr($row["cost_date_end"], 0, -15);
            $key = $yearStart . "_" . $monthStart;
            $startIndex = $monthsYearsIndex[$key];
            $diffMonths = diferencaMeses(substr($row["cost_date_begin"], 0, -9), substr($row["cost_date_end"], 0, -9));
            if ($diffMonths < 0) {
                $diffMonths = 0;
            } else if ($diffMonths >= count($monthsYearsIndex)) {
                $diffMonths = count($monthsYearsIndex) - 1; // this case the resource dates are longer than project dates, it will be limited by project dates.
            }
            $monthIterated = (int) (substr($monthStart, 0, 1) == "0" ? str_replace("0", "", $monthStart) : $monthStart);
            $yearIterated = (int) (substr($yearStart, 0, 1) == "0" ? str_replace("0", "", $yearStart) : $yearStart);
            for ($i = 0; $i <= $meses; $i++) {
                $mStartProject++;
                if ($i == $startIndex) {
                    if ($monthStart == $monthEnd && $yearEnd == $yearStart) { //exception for resources which lasts just a month
                        $mtzNH[$c][$k] = $row["cost_value_total"];
                        $realCostNHR = $controllerEarnValue->getCostsByNHR($projectSelected, $monthIterated, $yearIterated, $resource_id);
                        $mtzNHReal[$c][$k] = $realCostNHR;
                        ?>
                        <td>
                            <table class="tableComparisionRealPlanned">

                                <tr>
                                    <td style="text-align:right" class="plannedCostCell">
                                        <?php echo formatCellContent(number_format($mtzNH[$c][$k], 2, ",", ".")); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right">
                                        <?php
                                        $totalRealNHR+=$realCostNHR;
                                        echo formatCellContent(number_format($realCostNHR, 2, ",", "."));
                                        ?>
                                    </td>
                                </tr>

                            </table>
                        </td>

                        <?php
                    } else {
                        $k = $i;
                        for ($j = 0; $j <= $diffMonths; $j++) {
                            $mtzNH[$c][$k] = $row["cost_value_total"] / ($diffMonths + 1);
                            $realCostNHR = $controllerEarnValue->getCostsByNHR($projectSelected, $monthIterated, $yearIterated, $resource_id);
                            $mtzNHReal[$c][$k] = $realCostNHR;
                            ?>

                            <td>
                                <table class="tableComparisionRealPlanned">

                                    <tr>
                                        <td style="text-align:right" class="plannedCostCell">
                                            <?php echo formatCellContent(number_format($mtzNH[$c][$k], 2, ",", ".")); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="text-align:right">
                                            <?php
                                            $totalRealNHR+=$realCostNHR;
                                            echo formatCellContent(number_format($realCostNHR, 2, ",", "."));
                                            ?>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <?php
                            //move one month foward
                            if ($monthIterated == 12) {
                                $monthIterated = 1;
                                $yearIterated++;
                            } else {
                                $monthIterated++;
                            }
                            $k++;
                        }
                        $i = $k - 1;
                    }
                } else {
                    ?>
                    <td>
                        <table class="tableComparisionRealPlanned">
                            <tr>
                                <td style="text-align:right" class="plannedCostCell">
                                    <?php echo formatCellContent(number_format(0, 2, ",", ".")); ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:right">
                                    <?php
                                    $realCostNHR = $controllerEarnValue->getCostsByNHR($projectSelected, $monthIterated, $yearIterated, $resource_id);
                                    $mtzNHReal[$c][$k] = $realCostNHR;
                                    $totalRealNHR+=$realCostNHR;
                                    $sumTotalRealNHR+=$totalRealNHR;
                                    echo formatCellContent(number_format($realCostNHR, 2, ",", "."));
                                    ?>
                                </td>
                            </tr>

                        </table>
                    </td>
                    <?php
                }
            }
            $c++;
            ?>
            <td>
                <?php
                $realBackground = "color:" . ($totalRealNHR > $row['cost_value_total'] ? "$realCostNegativeColor" : "$realCostPositiveColor");
                $plannedBackground = "color:" . ($totalRealNHR > $row['cost_value_total'] ? "$plannedCostNegativeColor" : "$plannedCostPositiveColor");
                ?>
                <table class="tableComparisionRealPlanned" >
                    <tr>
                        <td style="text-align:right;<?php echo $plannedBackground ?>">
                            <?php echo formatCellContent(number_format($row['cost_value_total'], 2, ",", ".")); ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:right;<?php echo $realBackground ?>">
                            <?php
                            echo formatCellContent(number_format($totalRealNHR, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <?php
        $sumNH = $sumNH + $row['cost_value_total'];
    }
    ?>
    <tr>
        <td nowrap="nowrap"  width="15%" style="text-align: right"> <b><?php echo $AppUI->_("Subtotal Not Human Estimatives"); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</b> </td>
        <?php
        for ($i = 0; $i <= $meses; $i++) {
            $sum = 0;
            $sumReal = 0;
            ?>
            <td nowrap="nowrap" cellpadding="3" style="text-align: right">
                <table class="tableComparisionRealPlanned">

                    <tr>
                        <td style="text-align:right;font-weight: bold" class="plannedCostCell">
                            <?php
                            for ($j = 0; $j <= $c; $j++) {
                                $sum = $sum + $mtzNH[$j][$i];
                            }
                            $sumColumns[0][$i] = $sum;
                            echo formatCellContent(number_format($sum, 2, ",", "."));
                            ?> 
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:right;font-weight: bold">
                            <?php
                            for ($j = 0; $j <= $c; $j++) {
                                $sumReal = $sumReal + $mtzNHReal[$j][$i];
                            }
                            echo formatCellContent(number_format($sumReal, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                </table>
            </td>
            <?php
        }
        ?>
        <td nowrap="nowrap" cellpadding="3" style="text-align: right">

            <?php
            $realBackground = "color:" . ($sumTotalRealNHR > $sumNH ? "$realCostNegativeColor" : "$realCostPositiveColor");
            $plannedBackground = "color:" . ($sumTotalRealNHR > $sumNH ? "$plannedCostNegativeColor" : "$plannedCostPositiveColor");
            ?>
            <table class="tableComparisionRealPlanned">
                <tr>
                    <td style="text-align:right;font-weight: bold;<?php echo $plannedBackground ?>">
                        <?php echo formatCellContent(number_format($sumNH, 2, ",", ".")); ?>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;font-weight: bold;<?php echo $realBackground ?>">
                        <?php
                        echo formatCellContent(number_format($sumTotalRealNHR, 2, ",", "."));
                        ?>
                    </td>
                </tr>

            </table>
        </td>
    </tr>

    <!-- ############################## CONTINGENCY RESERVE  ############################################ -->  
    <?php
    $risks = getContingencyCosts($projectSelected);
    ?>
    <tr>
        <td nowrap='nowrap' width='100%' align="center" colspan="<?php echo $meses + 3 ?>">
            <b><?php echo $AppUI->_('CONTINGENCY RESERVE'); ?></b>
        </td>
    </tr>
    <?php
    $k = 0;
    $c = 0;
    $sumTotalContingency = 0;
    $sumRealTotalContingency = 0;
    foreach ($risks as $row) {
        $sumPlanned = 0;
        $sumReal = 0;
        ?>
        <tr>
            <td nowrap="nowrap">
                <?php echo $row['budget_reserve_description'] ?>
            </td>
            <?php
            //write columns cells for td 
            $mStartProject = $monthSProject;
            $mEndProject = $monthEndProject;
            $monthStart = substr($row["budget_reserve_inicial_month"], 5, -12);
            $yearStart = substr($row["budget_reserve_inicial_month"], 0, -15);
            $monthEnd = substr($row["budget_reserve_final_month"], 5, -12);
            $yearEnd = substr($row["budget_reserve_final_month"], 0, -15);
            $key = $yearStart . "_" . $monthStart;
            $startIndex = $monthsYearsIndex[$key];
            $diffMonths = diferencaMeses(substr($row["budget_reserve_inicial_month"], 0, -9), substr($row["budget_reserve_final_month"], 0, -9));

            if ($diffMonths < 0) {
                $diffMonths = 0;
            } else if ($diffMonths >= count($monthsYearsIndex)) {
                $diffMonths = count($monthsYearsIndex) - 1; // this case the resource dates are longer than project dates, it will be limited by project dates.
            }

            $monthIterated = (int) (substr($monthStart, 0, 1) == "0" ? str_replace("0", "", $monthStart) : $monthStart);
            $yearIterated = (int) (substr($yearStart, 0, 1) == "0" ? str_replace("0", "", $yearStart) : $yearStart);

            for ($i = 0; $i <= $meses; $i++) {
                $mStartProject++;
                if ($i == $startIndex) {
                    if ($monthStart == $monthEnd && $yearEnd == $yearStart) { //exception for resources which lasts just a month
                        $mtzC[$c][$k] = $row["budget_reserve_financial_impact"];
                        $sumPlanned+=$mtzC[$c][$k];
                        $realCostContingency = $controllerEarnValue->getCostsByContingency($projectSelected, $monthIterated, $yearIterated, $row["budget_reserve_id"]);
                        $sumReal+= $realCostContingency;
                        $mtzCReal[$c][$k] = $realCostContingency;
                        ?>
                        <td>                    
                            <table class="tableComparisionRealPlanned">

                                <tr>
                                    <td style="text-align:right;" class="plannedCostCell">
                                        <?php
                                        echo formatCellContent(number_format($mtzC[$c][$k], 2, ",", "."));
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;">
                                        <?php
                                        echo formatCellContent(number_format($realCostContingency, 2, ",", "."));
                                        ?>
                                    </td>
                                </tr>

                            </table>
                        </td>

                        <?php
                    } else {
                        $k = $i;
                        for ($j = 0; $j <= $diffMonths; $j++) {
                            $mtzC[$c][$k] = $row["budget_reserve_financial_impact"] / ($diffMonths + 1);
                            $sumPlanned+=$mtzC[$c][$k];
                            $realCostContingency = $controllerEarnValue->getCostsByContingency($projectSelected, $monthIterated, $yearIterated, $row["budget_reserve_id"]);
                            $sumReal+= $realCostContingency;
                            $mtzCReal[$c][$k] = $realCostContingency;
                            ?>                            
                            <td>                    
                                <table class="tableComparisionRealPlanned">

                                    <tr>
                                        <td style="text-align:right;" class="plannedCostCell">
                                            <?php
                                            echo formatCellContent(number_format($mtzC[$c][$k], 2, ",", "."));
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;">
                                            <?php
                                            echo formatCellContent(number_format($realCostContingency, 2, ",", "."));
                                            ?>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <?php
                            $k++;
                            //move one month foward
                            if ($monthIterated == 12) {
                                $monthIterated = 1;
                                $yearIterated++;
                            } else {
                                $monthIterated++;
                            }
                        }
                        $i = $k - 1;
                    }
                } else {
                    $mtzC[$c][$i] = 0;
                    $realCostContingency = $controllerEarnValue->getCostsByContingency($projectSelected, $monthIterated, $yearIterated, $row["budget_reserve_id"]);
                    $sumReal+= $realCostContingency;
                    $mtzCReal[$c][$k] = $realCostContingency;
                    ?>
                    <td>                    
                        <table class="tableComparisionRealPlanned">
                            <tr>
                                <td style="text-align:right;" class="plannedCostCell">
                                    <?php
                                    echo formatCellContent(number_format($mtzC[$c][$k], 2, ",", "."));
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:right;">
                                    <?php
                                    echo formatCellContent(number_format($realCostContingency, 2, ",", "."));
                                    ?>
                                </td>
                            </tr>

                        </table>
                    </td>
                    <?php
                }
            }
            $c++;
            ?>

            <td>
                <?php
                $realBackground = "color:" . ($sumReal > $sumPlanned ? "$realCostNegativeColor" : "$realCostPositiveColor");
                $plannedBackground = "color:" . ($sumReal > $sumPlanned ? "$plannedCostNegativeColor" : "$plannedCostPositiveColor");
                ?>
                <table class="tableComparisionRealPlanned">
                    <tr>
                        <td style="text-align:right;<?php echo $plannedBackground ?>">
                            <?php
                            echo formatCellContent(number_format($sumPlanned, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:right;<?php echo $realBackground ?>">
                            <?php
                            echo formatCellContent(number_format($sumReal, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <?php
        $k++;
        $sumTotalContingency += $sumPlanned;
        $sumRealTotalContingency +=$sumReal;
    }
    ?>
    <tr>
        <td nowrap="nowrap" width="15%" style="text-align: right"> <b><?php echo $AppUI->_('Subtotal Contingency'); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</b> </td>
        <?php
        for ($i = 0; $i <= $meses; $i++) {
            $sum = 0;
            $sumReal = 0;
            for ($j = 0; $j <= $c; $j++) {
                $sum = $sum + $mtzC[$j][$i];
                $sumReal = $sumReal + $mtzCReal[$j][$i];
            }
            $sumColumnsRealContingency[0][$i] = $sumReal;
            $sumColumns[0][$i] = $sum;
            ?>

            <td nowrap="nowrap" style="text-align: right" cellpadding="3">
                <table class="tableComparisionRealPlanned">
                    <tr>
                        <td style="text-align:right;font-weight: bold" class="plannedCostCell">
                            <?php
                            echo formatCellContent(number_format($sum, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:right;font-weight: bold">
                            <?php
                            echo formatCellContent(number_format($sumReal, 2, ",", "."));
                            ?>
                        </td>
                    </tr>

                </table>
            </td>

            <?php
        }
        ?>

        <td nowrap="nowrap" style="text-align: right" cellpadding="3">

            <?php
            $realBackground = "color:" . ($sumRealTotalContingency > $sumTotalContingency ? "$realCostNegativeColor" : "$realCostPositiveColor");
            $plannedBackground = "color:" . ($sumRealTotalContingency > $sumTotalContingency ? "$plannedCostNegativeColor" : "$plannedCostPositiveColor");
            ?>
            <table class="tableComparisionRealPlanned">

                <tr>
                    <td style="text-align:right;font-weight: bold;<?php echo $plannedBackground; ?>">
                        <?php
                        echo formatCellContent(number_format($sumTotalContingency, 2, ",", "."));
                        ?>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;font-weight: bold;<?php echo $realBackground; ?>">
                        <?php echo formatCellContent(number_format($sumRealTotalContingency, 2, ",", ".")); ?>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <!-- Non planned expenses -->
    <tr>
        <td nowrap="nowrap">
            <?php echo $AppUI->_("LBL_NON_PLANNED_EXPENSES"); ?>
        </td>
        <?php
        //set project data: begin and end months/years
        $monthStart = substr($datesProject->fields['project_start_date'], 5, -12);
        $monthEnd = substr($datesProject->fields['project_end_date'], 5, -12);

        $yearStart = substr($datesProject->fields['project_start_date'], 0, -15);
        $yearEnd = substr($datesProject->fields['project_end_date'], 0, -15);

        $key = $yearStart . "_" . $monthStart;
        $startIndex = $monthsYearsIndex[$key];
        $diffMonths = count($monthsYearsIndex) - 1; // this case the resource dates are longer than project dates, it will be limited by project dates.

        $monthIterated = (int) (substr($monthStart, 0, 1) == "0" ? str_replace("0", "", $monthStart) : $monthStart);
        $yearIterated = (int) (substr($yearStart, 0, 1) == "0" ? str_replace("0", "", $yearStart) : $yearStart);
        $sumNonPlannedCost = 0;
        for ($i = 0; $i <= $meses; $i++) {
            $nonPlannedCost = $controllerEarnValue->getCostsNonPlanned($projectSelected, $monthIterated, $yearIterated);
            $sumNonPlannedCost += $nonPlannedCost;
            ?>
            <td>                    
                <table class="tableComparisionRealPlanned">

                    <tr>
                        <td style="text-align:right;" class="plannedCostCell">
                            <?php echo formatCellContent(number_format(0, 2, ",", ".")); ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:right;">
                            <?php
                            echo formatCellContent(number_format($nonPlannedCost, 2, ",", "."));
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
            <?php
            //move one month foward
            if ($monthIterated == 12) {
                $monthIterated = 1;
                $yearIterated++;
            } else {
                $monthIterated++;
            }
        }
        ?>
        <td style="text-align:right;font-weight: bold">
            <table class="tableComparisionRealPlanned">
                 <?php $realBackground = "color:" . ($sumNonPlannedCost> 0 ? "$realCostNegativeColor" : "$realCostPositiveColor"); ?>
           
                <tr>
                    <td style="text-align:right;" class="plannedCostCell">
                        <?php echo formatCellContent(number_format(0, 2, ",", ".")); ?>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;<?php echo $realBackground; ?>">
                        <?php
                        echo formatCellContent(number_format($sumNonPlannedCost, 2, ",", "."));
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
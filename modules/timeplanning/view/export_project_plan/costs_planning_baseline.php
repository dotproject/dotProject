<?php
require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";

$whereProject = "";
if ($projectId != null) {
    $whereProject = " and cost_project_id=" . $projectId;
}

$q = new DBQuery();
// Get humans estimatives
$q->clear();
$q->addQuery("*");
$q->addTable("costs");
$q->addWhere("cost_type_id = \"0\"" . $whereProject);
$q->addOrder("cost_description");
$humanCost = $q->loadList();

// Get not humans estimatives
$q->clear();
$q->addQuery("*");
$q->addTable("costs");
$q->addWhere("cost_type_id = \"1\"" . $whereProject);
$q->addOrder("cost_description");
$notHumanCost = $q->loadList();

$q->clear();
$q->addQuery("*");
$q->addTable("budget");
$q->addWhere("budget_project_id = " . $projectId);
$q->addOrder("budget_id");
$v = $q->loadList();
?>

<!-- ############################## ESTIMATIVAS CUSTOS HUMANOS ############################################ -->
<table class="printTable" style="font-size:7px !important;">

    <?php
    $q->clear();
    $q->addQuery("project_start_date,project_end_date");
    $q->addTable("projects");
    $q->addWhere("project_id = \"$projectId\"");
    $datesProject = & $q->exec();

    if(!isset($datesProject->fields["project_start_date"])){
        $datesProject->fields["project_start_date"]=date("Y-m-d H:i:s");
        $datesProject->fields["project_end_date"]= date("Y-m-d H:i:s", strtotime("+1 month", time()));
    }
    
    
    $meses = diferencaMeses(substr($datesProject->fields["project_start_date"], 0, -9), substr($datesProject->fields["project_end_date"], 0, -9));
    $monthStartProject = substr($datesProject->fields["project_start_date"], 5, -12);
    $monthEndProject = substr($datesProject->fields["project_end_date"], 5, -12);
    $monthSProject = substr($datesProject->fields["project_start_date"], 5, -12);
    $yearStartProject = substr($datesProject->fields["project_start_date"], 0, -15);
    $yearEndProject = substr($datesProject->fields["project_end_date"], 0, -15);

    $years = $yearEndProject - $yearStartProject;
    $tempYear = $yearStartProject;
    //Calculates how many months exist in the firt year of the project
    $tempMeses =0; (12 - $monthStartProject) + 1;   
    $monthEndFirstYear=$yearStartProject==$yearEndProject?$monthEndProject:12;
    for($i=$monthStartProject;$i<=$monthEndFirstYear;$i++){
        $tempMeses++;
    }
    
    $sumColumns;
    $c = 0;
    $counter = 1;
    $sumH=0;
    $sumNH=0;
     //set an array with the index for each month/year
    $monthsYearsIndex = array();
    $index_month = $monthStartProject;
    $index_year = $yearStartProject;
    for ($i = 0; $i <= $meses; $i++) {
        $monthPrefix=strlen($index_month) < 2 ? "0" : "";
        $monthsYearsIndex[$index_year . "_" .$monthPrefix .$index_month]=$i;
        if ($index_month == 12){
            $index_month = 0;
            $index_year++;
        }
        $index_month++;
    }
    ?>
    <tr>
        <th ><?php echo $AppUI->_("Year"); ?></th>
        <?php
        for ($i = 0; $i <= $years; $i++) {
            echo "<th colspan=\"" . ($tempMeses + 1). "\">";
            echo $tempYear;
            echo"</th>";
            $tempMeses = ($meses - $tempMeses) + 1;
            $ns = $tempMeses - 12;
            if ($ns > 0)
                $tempMeses = 12;
            $tempYear++;
        }
        ?>
        <!-- <th>&nbsp;</th> -->
    </tr>
    <tr>
        <th  ><?php echo $AppUI->_("Item"); ?></th>
        <?php
        for ($i = 0; $i <= $meses; $i++) {
            $mes = $monthStartProject;
            $monthStartProject++;
            if ($mes == 12)
                $monthStartProject = 1;
            ?>

            <th style="text-align:center">
                <?php echo strlen($mes)<2?"0".$mes:$mes; ?>
            </th>
            <?php
            $counter++;
        }
        ?>
        <th  ><?php echo $AppUI->_("Total Cost",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</th>
    </tr>

    <tr>
        <td  align="center" colspan="<?php echo $meses + 3 ?>">
            <b><?php echo $AppUI->_("HUMAN RESOURCE ESTIMATIVE",UI_OUTPUT_HTML); ?></b>
        </td>
    </tr>

<?php foreach ($humanCost as $row) {
    ?>
        <tr>
            <td style="width:170px"><?php echo $row["cost_description"]; ?></td>
            <?php
            $mtz = costsBudget($meses, $c, $row, substr($datesProject->fields["project_start_date"], 5, -12), substr($datesProject->fields["project_end_date"], 5, -12), $mtz,$monthsYearsIndex);
            $c++;
            ?>

            <td style="text-align:right"><?php echo formatCellContent(number_format($row["cost_value_total"], 2, ",", ".")); ?></td>
        </tr>
        <?php
        $sumH = $sumH + $row["cost_value_total"];
    }
    ?>
    <tr>
        <td> <b><?php echo $AppUI->_("Subtotal Human Estimatives",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</b> </td>
        <?php
        $sumColumns = subTotalBudget($meses, $c, $mtz, 0, $sumColumns);
        ?>
        <td style="text-align:right"><b><?php echo formatCellContent(number_format($sumH, 2, ",", ".")); ?></b></td>

    </tr>


    <br>
    <!-- ############################## ESTIMATIVAS CUSTOS NAO HUMANOS ############################################ -->

    <tr>
        <td  align="center" colspan="<?php echo $meses + 3 ?>">
            <b> <?php echo $AppUI->_("Non-Human Resource Estimative",UI_OUTPUT_HTML); ?></b>
        </td>
    </tr>

    <?php
    $c = 0;
    foreach ($notHumanCost as $row) {
        ?>
        <tr>
            <td ><?php echo $row["cost_description"]; ?></td>
            <?php
            $mtzNH = costsBudget($meses, $c, $row, substr($datesProject->fields["project_start_date"], 5, -12), substr($datesProject->fields["project_end_date"], 5, -12), $mtzNH,$monthsYearsIndex);
            $c++;
            ?>

            <td style="text-align:right"><?php echo formatCellContent(number_format($row["cost_value_total"], 2, ",", ".")); ?></td>
        </tr>
        <?php
        $sumNH = $sumNH + $row["cost_value_total"];
    }
    ?>
    <tr>
        <td> <b><?php echo $AppUI->_("Subtotal Not Human Estimatives",UI_OUTPUT_HTML); ?>  (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</b> </td>
        <?php
        $sumColumns = subTotalBudget($meses, $c, $mtzNH, 1, $sumColumns);
        ?>
        <td  cellpadding="3" style="text-align:right"><b><?php echo formatCellContent(number_format($sumNH, 2, ",", ".")); ?></b></td>

    </tr>


    <!-- ############################## CONTINGENCY RESERVE  ############################################ -->  

    <?php
    $q->clear();
    $q->addQuery("*");
    $q->addTable("budget_reserve", "b");
    $q->addWhere("budget_reserve_project_id = " . $projectId);
    $q->addOrder("budget_reserve_risk_id");
    $risks = $q->loadList();
    ?>

    <tr>
        <td  align="center" colspan="<?php echo $meses + 3 ?>">
            <b><?php echo $AppUI->_("CONTINGENCY RESERVE",UI_OUTPUT_HTML); ?></b>
        </td>
    </tr>

    <?php
    $k = 0;
    $c = 0;

    foreach ($risks as $row) {
        ?>
        <tr>
            <td>
            <?php echo $row["budget_reserve_description"] ?>
            </td>

            <?php
            $mtzC = costsContingency($meses, $c, $row, $monthSProject, substr($datesProject->fields["project_end_date"], 5, -12), $mtzC,$monthsYearsIndex);
            $c++;
            ?>

            <td style="text-align:right"><?php
            $sumRowContingency = subTotalBudgetRow($meses, $c, $mtzC, $k);
            echo formatCellContent(number_format($sumRowContingency, 2, ",", "."));
            ?>
            </td>
        </tr>
        <?php
        $k++;
        $sumC = $sumC + $sumRowContingency;
        ;
    }
    ?>
    <tr>
        <td> <b><?php echo $AppUI->_("Subtotal Contingency",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</b> </td>
        <?php
        $sumColumns = subTotalBudget($meses, $c, $mtzC, 0, $sumColumns);
        ?>
        <td style="text-align:right"><b><?php echo formatCellContent(number_format($sumC, 2, ",", ".")); ?></b></td>
    </tr>

    <tr>
        <td   align="center" colspan="<?php echo $meses + 3 ?>"></td>
    </tr>

    <tr>
        <td   align="center">
            <b><?php echo $AppUI->_("TOTAL",UI_OUTPUT_HTML); ?> (<?php echo $AppUI->_("LBL_PROJECT_CURRENCY",UI_OUTPUT_HTML); ?>)</b>
        </td>
        <?php
        totalBudget($meses, $sumColumns);
        ?>
        <td style="text-align:right">
            <b>
                <?php 
                $subTotal = $sumH + $sumNH + $sumC;
                echo formatCellContent(number_format($subTotal, 2, ",", "."));
                ?>
            </b>
        </td>
    </tr>
</table>
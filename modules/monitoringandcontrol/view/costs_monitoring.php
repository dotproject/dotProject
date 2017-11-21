<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
?>
<div style="display: none">
    <?php require_once (DP_BASE_DIR . "/modules/costs/view_budget.php"); //get the baseline values?>
</div>

<?php
global $AppUI;
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_earn_value.class.php");
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_baseline.class.php");
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
require_once(DP_BASE_DIR . "/modules/projects/projects.class.php");

ini_set('max_execution_time', 180);
ini_set('memory_limit', $dPconfig['reset_memory_limit']);

$project_id = dPgetParam($_GET, 'project_id', 0);
$projectObj = new CProject();
$projectObj->load($project_id);
$projectStartDate = new CDate($projectObj->project_start_date);
$projectEndDate = new CDate($projectObj->project_end_date);
$dtAtual = $projectEndDate->format("%d/%m/%Y");
$titGrafico = $AppUI->_('LBL_GRAF_CUSTO');
$titCR = $AppUI->_('LBL_CUSTO_REAL') . " (" . dPgetConfig("currency_symbol") . ")";
$titVA = $AppUI->_('LBL_VALOR_AGREGADO') . " (" . dPgetConfig("currency_symbol") . ")";
$titPC = $AppUI->_('LBL_PLANNED_COST') . " (" . dPgetConfig("currency_symbol") . ")";
$cmbBaseline = dPgetParam($_POST, 'cmbBaseline');
$today = new CDate();
$monthsFromBegin = diffDate(substr($projectObj->project_start_date, 0, -9), $today->format("%Y-%m-%d"), "M");
//calculate costs planned to spent until now
$costsPlannedToSpent = 0;
$i = 0;
while ($i < sizeof($costsBaselinePlannedArray) && $i < $monthsFromBegin) {
    $costsPlannedToSpent+=$costsBaselinePlannedArray[$i];
    $i++;
}
?>

<script type="text/javascript" src="./modules/monitoringandcontrol/js/util.js"> </script>
<div style="background-color: white">
    <table style="position: relative;left: 20px;float: left">
        <tr>
            <td colspan="2">&nbsp;
                <?php
                $controllerUtil = new ControllerUtil();
                $controllerEarnValue = new ControllerEarnValue();

                $vlValorAgregado = $controllerEarnValue->obterValorAgregado($project_id, $dtAtual, $cmbBaseline);
                $vlValorReal = $controllerEarnValue->obterValorReal($project_id, $dtAtual, $cmbBaseline);
                $vlVariacaoCusto = $controllerEarnValue->obterVariacaoCusto($project_id, $dtAtual, $cmbBaseline);
                $vlIndiceDesempenho = $controllerEarnValue->obterIndiceDesempenhoCusto($project_id, $dtAtual, $cmbBaseline);
                $lstDataMinTask = $controllerEarnValue->obterInicioPeriodo($project_id, $cmbBaseline);

                foreach ($lstDataMinTask as $ini) {
                    $dtUtil = new CDate($ini[0]);
                    $dtInicioProjeto = $dtUtil->format('%d/%m/%Y');
                }

                $vlReal = array();
                $vlAgregado = array();
                $dtConsultaArray = array();

                $arDtAtual = explode('/', $dtAtual);
                $diaDtAtual = $arDtAtual[0];
                $mesDtAtual = $arDtAtual[1];
                $anoDtAtual = $arDtAtual[2];

                $arInicioProjeto = explode('/', $dtInicioProjeto);
                $diaInicioProjeto = $arInicioProjeto[0];
                $mesInicioProjeto = $arInicioProjeto[1];
                $anoInicioProjeto = $arInicioProjeto[2];

                $difAno = ($anoDtAtual - $anoInicioProjeto) * 12;
                $difMes = ($mesDtAtual - $mesInicioProjeto) + 1;
                $nPlot = ($difMes + $difAno);

                if ($nPlot <= 12) {
                    array_push($vlReal, 0);
                    array_push($vlAgregado, 0);
                    array_push($dtConsultaArray, $dtInicioProjeto);
                }

                for ($i = 1; $i <= $nPlot; ++$i) {
                    if (($nPlot - $i) > 12) {
                        continue;
                    }

                    $dtConsulta = date('d/m/Y', mktime(0, 0, 0, ($mesInicioProjeto + $i), $diaInicioProjeto, $anoInicioProjeto));

                    if ($controllerUtil->data_to_timestamp($dtConsulta) < $controllerUtil->data_to_timestamp($dtAtual)) {
                        array_push($dtConsultaArray, $dtConsulta);
                        array_push($vlReal, $controllerEarnValue->obterValorReal($project_id, $dtConsulta, $cmbBaseline));
                        array_push($vlAgregado, $controllerEarnValue->obterValorAgregado($project_id, $dtConsulta, $cmbBaseline));
                    } else {
                        if ($dtAtual == $dtInicioProjeto) {
                            continue;
                        }
                        array_push($dtConsultaArray, $dtAtual);
                        array_push($vlReal, $controllerEarnValue->obterValorReal($project_id, $dtAtual, $cmbBaseline));
                        array_push($vlAgregado, $controllerEarnValue->obterValorAgregado($project_id, $dtAtual, $cmbBaseline));
                        break;
                    }
                }
                $realCost = 0;
                if (sizeof($vlReal) > 1) {
                    $realCost = $vlReal[sizeof($vlReal) - 1];
                }
                ?>            
            </td>
        </tr>

        <tr>			  
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_("LBL_MONTHS_FROM_BEGIN"); ?>:
            </td> 
            <td nowrap="nowrap">    
                <?php echo $monthsFromBegin . " " . $AppUI->_("LBL_MONTH_MONTHS"); ?> 
                (<?php echo $projectStartDate->format("%d/%m/%Y") . " - " . $today->format("%d/%m/%Y"); ?>)
            </td>	
        </tr>
        
         <tr>			  
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_('LBL_DATA_ENCERRAMENTO'); ?>:
            </td> 
            <td nowrap="nowrap">   
                <input type="hidden" name="date_edit"  id="date_edit"  value="<?php echo $dtAtual; ?>"/>
                <?php echo $dtAtual; ?>
            </td>
        </tr>

        <tr>			  
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_("LBL_HOW_MUCH_WAS_PLANNED_TO_SPENT"); ?>:
            </td> 
            <td nowrap="nowrap">    
                <?php echo dPgetConfig("currency_symbol") . " " . number_format($costsPlannedToSpent, 2, ',', '.'); ?>
            </td>	
        </tr>

        <tr>			  
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_("LBL_HOW_MUCH_WAS_SPENT"); ?>:
            </td> 
            <td nowrap="nowrap">    
                <?php echo dPgetConfig("currency_symbol") . " " . number_format($realCost, 2, ',', '.'); ?>
            </td>	
        </tr>


<!--
        <tr>			  
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_('LBL_BASELINE'); ?>:
            </td> 
            <td nowrap="nowrap">   
                <input name="cmbBaseline" type="hidden" value="0"  id="cmbBaseline" /> 
                <?php echo $AppUI->_('LBL_POSICAO_ATUAL'); ?>
                <?php
                $controllerBaseline = new ControllerBaseline();
                $lstBaseline = $controllerBaseline->listBaseline($project_id);
                ?>
            </td>	
        </tr>
-->
       <tr>
           <td colspan="2" style="text-align: center; font-weight:bold;color:gray">
               <br />
               <?php echo $AppUI->_("LBL_PROJECT_EARNED_VALUE_ANALYSIS"); ?>
           </td>
       </tr>
        <tr>
            <td style="text-wrap: avoid;font-weight: bold; text-align: right" >
                <?php echo $AppUI->_('LBL_CUSTO_REAL'); ?> (<?php echo $AppUI->_('LBL_CR'); ?>):
            </td>
            <td>
                <?php echo dPgetConfig("currency_symbol") ?>
                <?php
                if (isset($vlValorReal)) {
                    $actualCost = number_format($vlValorReal, 2, ',', '.');
                }else
                    $actualCost = "";
                ?>
                <input type="hidden" name="cr"value="<?php echo $actualCost; ?>" />
                <?php echo $actualCost; ?>
            </td>
        </tr>

        <tr>
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_('LBL_VALOR_AGREGADO'); ?> (<?php echo $AppUI->_('LBL_VA'); ?>):
            </td>
            <td>
                <?php echo dPgetConfig("currency_symbol") ?>
                <?php
                if (isset($vlValorAgregado)) {
                    $valorAgregado = number_format($vlValorAgregado, 2, ',', '.');
                }else
                    $valorAgregado = "";
                ?>
                <input type="hidden" name="va" size="15" value="<?php echo $valorAgregado ?>" />
                <?php echo $valorAgregado; ?>
            </td>
        </tr>

        <tr>
            <?php
            if (isset($vlVariacaoCusto)) {
                $costVariation = number_format($vlVariacaoCusto, 2, ',', '.');
            } else {
                $costVariation = "0,00";
            }
            $statusColor = "";
            if ($vlVariacaoCusto == 0) {
                $statusColor = "green";
            } else if ($vlVariacaoCusto > 0) {
                $statusColor = "blue";
            } else {
                $statusColor = "red";
            }
            ?>
            <td style="text-wrap: avoid;font-weight: bold; text-align: right">
                <?php echo $AppUI->_('LBL_VARIACAO_CUSTO'); ?> (<?php echo $AppUI->_('LBL_VC'); ?>):
            </td>
            <td style="color:<?php echo $statusColor ?>">
                <?php echo dPgetConfig("currency_symbol") ?>
                <input type="hidden" name="vc"  value="<?php echo $costVariation ?>" />
                <?php echo $costVariation ?>
            </td>
        </tr>		        
        <tr>
            <?php
            $idc = round($vlIndiceDesempenho, 2);
            $lblIDC = "";
            $statusColor = "";
            if ($idc == 1) {
                $statusColor = "green";
                $lblIDC = $AppUI->_('LBL_IDC_IGUAL');
            } else if ($idc > 1) {
                $statusColor = "blue";
                $lblIDC = $AppUI->_('LBL_IDC_MAIOR');
            } else {
                $statusColor = "red";
                $lblIDC = $AppUI->_('LBL_IDC_MENOR');
            }
            ?>

            <td style="text-wrap: none;font-weight: bold; text-align: right;">
                <?php echo $AppUI->_('LBL_INDICE_CUSTO'); ?> (<?php echo $AppUI->_('LBL_IDC'); ?>):
            </td>
            <td style="color:<?php echo $statusColor ?>">
                <input type="hidden"  name="idp" value="<?php echo $idc; ?>" />
                <?php echo $idc; ?> (<?php echo $lblIDC;?>)
            </td>
        </tr>

        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>


    <?php
//create an array with culmulative costs for planned costs for baseline graph
    $culmulativePlannedCostsArray = array();
    $culmulativePlannedCostsArray[0] = $costsBaselinePlannedArray[0];
    $i = 1;
    while ($i < sizeof($costsBaselinePlannedArray)) {
        $culmulativePlannedCostsArray[$i] = $culmulativePlannedCostsArray[$i - 1] + $costsBaselinePlannedArray[$i];
        $i++;
    }

//create an array with culmulative costs for actual costs for baseline graph
    $culmulativeActualCostsArray = array();
    $culmulativeActualCostsArray[0] = $vlReal[0];
    $i = 1;
    while ($i < sizeof($vlReal)) {
        $culmulativeActualCostsArray[$i] = $culmulativeActualCostsArray[$i - 1] + $vlReal[$i];
        $i++;
    }
    ?>

    <table  style="position: relative;right: 20px;float: right" >	    
        <tr>
            <td colspan="2">
                <?php
                if ((!empty($vlReal) || !isset($vlReal)) || (!empty($vlAgregado) || !isset($vlAgregado))) {
                    $url = './modules/monitoringandcontrol/grafico/line_Graph_Cost.php?titGrafico=' . urlencode(serialize($titGrafico)) . '&titCR=' . urlencode(serialize($titCR)) . '&titVA=' . urlencode(serialize($titVA)) . '&titPC=' . urlencode(serialize($titPC)) . '&dtConsultaArray=' . urlencode(serialize($dtConsultaArray)) . '&vlReal=' . urlencode(serialize($vlReal)) . '&vlAgregado=' . urlencode(serialize($vlAgregado)) . "&vlPlanned=" . urlencode(serialize($culmulativePlannedCostsArray));
                }else
                    $url = './modules/monitoringandcontrol/grafico/line_Graph_Cost.php';
                ?>
                <img src="<?php echo $url; ?>" />         
            </td> 						
        </tr>  
    </table>
    <br />
    <?php require_once DP_BASE_DIR . "/modules/monitoringandcontrol/view/cost_baseline_monitoring.php"; ?>
</div>
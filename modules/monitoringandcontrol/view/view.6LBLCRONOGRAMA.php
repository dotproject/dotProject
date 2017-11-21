<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_earn_value.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_baseline.class.php");
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$project_id = dPgetParam($_GET, 'project_id', 0);

ini_set('max_execution_time', 180);
ini_set('memory_limit', $dPconfig['reset_memory_limit']);
global $AppUI;

$titGrafico = $AppUI->_('LBL_GRAF_PRAZO');
$titVP = $AppUI->_('LBL_VALOR_PLANEJADO');
$titVA = $AppUI->_('LBL_VALOR_AGREGADO');

// se a data não estiver setada pega atual senão usa a passada
if (isset($_POST['date_edit']) &&
        eregi("^[0-9]{2}/[0-9]{2}/[0-9]{4}$", $_POST['date_edit']) &&
        checkdate(substr($_POST['date_edit'], 3, 2), substr($_POST['date_edit'], 0, 2), substr($_POST['date_edit'], 7, 4))) {
    $dtAtual = $_POST['date_edit'];
} else {
    $dtAtual = date('d/m/Y');
}
$cmbBaseline = dPgetParam($_POST, 'cmbBaseline');
?>

<script>
    var calendarField = "";
    var calwin = null;

    /*
     * @param {type} field: the id of the date field
     * A hidden field with the same name should has an identical id, but that starts with f_ 
     * @returns {undefined}
     */
    function popCalendar(field) {
        calendarField = field;
        idate = document.getElementById("f_" + field).value;
        window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=280, height=250, scrollbars=no');
    }

    /**
     *	@param string Input date in the format YYYYMMDD
     *	@param string Formatted date
     */
    function setCalendar(idate, fdate) {
        fld_date = document.getElementById(calendarField);
        fld_fdate = document.getElementById("f_" + calendarField);
        fld_date.value = fdate;
        fld_fdate.value = idate;
    }
</script>

<script type="text/javascript" src="./modules/monitoringandcontrol/js/util.js"></script>
<!--  calendar  -->
<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>
<!-- end calendar  -->   
<br />
<div>

    <form name="gantt_filter" method="GET" action="">
        <input type="hidden" name="m" value="projects">
        <input type="hidden" name="a" value="view">
        <input type="hidden" name="project_id" value="<?php echo $_GET["project_id"] ?>">
        <div style="display:none" width="90%" align="center" >
            Data Início:
            <input type="hidden" name="f_date_begin" id="f_date_begin"  value="<?php echo $_GET["f_date_begin"]; ?>"/>
            <!-- format(FMT_TIMESTAMP_DATE) -->
            <input type="text" style="width:85px" class="text" name="date_begin" id="date_begin" value="<?php echo ($_GET["date_begin"]); ?>" readonly="true"  />
            <a href="#" onclick="popCalendar('date_begin');">
                <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
            </a>
            &nbsp; à &nbsp;
            Data fim:
            <input type="hidden" name="f_date_end" id="f_date_end"  value="<?php echo $_GET["f_date_end"]; ?>" />
            <!-- format(FMT_TIMESTAMP_DATE) -->
            <input type="text" style="width:85px" class="text" name="date_end" id="date_end" value="<?php echo $_GET["date_end"]; ?>" readonly="true" />
            <a href="#" onclick="popCalendar('date_end');">
                <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
            </a>
            &nbsp;
            <input type="submit" value="Consultar" class="button" />
        </div>
    </form>
    
    <br /><br />
    
    <div align="center">
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/gantt_chart.php"); ?>
    </div>
    <br />
        <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/monitoringandcontrol/view/view.1LBLBASELINE.php';submitMenuForm();" value="<?php echo $AppUI->_("View baselines") ?>" onclick="submitMenuForm()" />
        <!--
        <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/monitoringandcontrol/view/view.2LBLRESPONSABILIDADE.php';submitMenuForm();" value="<?php echo $AppUI->_("View responsabilities") ?>" onclick="submitMenuForm()" />
        <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/monitoringandcontrol/view/view.3LBLACAOCORRETIVA.php';submitMenuForm();" value="<?php echo $AppUI->_("View corrective actions") ?>" onclick="submitMenuForm()" />
        <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/monitoringandcontrol/view/view.4LBLATA.php';submitMenuForm();" value="<?php echo $AppUI->_("View minutes") ?>" onclick="submitMenuForm()" />
        <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/monitoringandcontrol/view/view.5LBLCUSTO.php';submitMenuForm();" value="<?php echo $AppUI->_("View CPI - Costs monitoring") ?>" onclick="submitMenuForm()" />
        <input type="button" onclick="document.gqs_feature_menu.user_choosen_feature.value = '/modules/monitoringandcontrol/view/view.5LBLCUSTO.php';submitMenuForm();" value="<?php echo $AppUI->_("Vingandcontrol/view/view.5LBLCUSTOiew CPI - View quality monitoring") ?>" onclick="submitMenuForm()" />
        -->
    <hr align="center" width="90%" />
    <table  width="40%" align="left" >	    
        <tr>
            <td colspan="2">&nbsp;
                <?php
                $controllerUtil = new ControllerUtil();
                $controllerEarnValue = new ControllerEarnValue();

                $vlValorAgregado = $controllerEarnValue->obterValorAgregado($project_id, $dtAtual, $cmbBaseline);
                $vlValorPlanejado = $controllerEarnValue->obterValorPlanejado($project_id, $dtAtual, $cmbBaseline);
                $vlVariacaoCronograma = $controllerEarnValue->obterVariacaoPrazo($project_id, $dtAtual, $cmbBaseline);
                $vlIndiceDesempenho = $controllerEarnValue->obterIndiceDesempenhoPrazo($project_id, $dtAtual, $cmbBaseline);
                $lstDataMinTask = $controllerEarnValue->obterInicioPeriodo($project_id, $cmbBaseline);

                foreach ($lstDataMinTask as $ini) {
                    $dtUtil = new CDate($ini[0]);
                    $dtInicioProjeto = $dtUtil->format('%d/%m/%Y');
                }
   
                $vlPlanejado = array();
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
                    array_push($vlPlanejado, 0);
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
                        array_push($vlPlanejado, $controllerEarnValue->obterValorPlanejado($project_id, $dtConsulta, $cmbBaseline));
                        array_push($vlAgregado, $controllerEarnValue->obterValorAgregado($project_id, $dtConsulta, $cmbBaseline));
                    } else {
                        if ($dtAtual == $dtInicioProjeto) {
                            continue;
                        }
                        array_push($dtConsultaArray, $dtAtual);
                        array_push($vlPlanejado, $controllerEarnValue->obterValorPlanejado($project_id, $dtAtual, $cmbBaseline));
                        array_push($vlAgregado, $controllerEarnValue->obterValorAgregado($project_id, $dtAtual, $cmbBaseline));
                        break;
                    }
                }
                ?>            
            </td>
        </tr>	
    
        <form name="formdata" id="formdata" method="post"  action=""  enctype="multipart/form-data" >

            <tr>			  
                <td align="right"><?php echo $AppUI->_('LBL_BASELINE'); ?></td> 
                <td nowrap="nowrap">   
                    <select name="cmbBaseline" size="1" id="cmbBaseline" onchange="submit();"> 		
                        <?php
                        $controllerBaseline = new ControllerBaseline();
                        $lstBaseline = $controllerBaseline->listBaseline($project_id);
                        echo "<option value='0'>" . $AppUI->_('LBL_POSICAO_ATUAL') . "</option>";
                        for ($i = 0; $i < count($lstBaseline); $i++) {
                            if ($cmbBaseline == $lstBaseline[$i][baseline_id]) {
                                echo "<option value='" . $lstBaseline[$i][baseline_id] . "' selected>" . $lstBaseline[$i][baseline_version] . "</option>";
                            } else {
                                echo "<option value='" . $lstBaseline[$i][baseline_id] . "' >" . $lstBaseline[$i][baseline_version] . "</option>";
                            }
                        }
                        ?>          
                    </select>		
                </td>	
            </tr>	

            <tr>			  
                <td align="right"><?php echo $AppUI->_('LBL_DATA'); ?></td> 
                <td nowrap="nowrap">   
                    <input type="text" class="text"  name="date_edit"  id="date_edit"  size="15" value="<?php echo $dtAtual; ?>"  onchange="submit();" maxlength="10" onkeyup="formatadata(this, event)"/>
                    <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'), 'dd/mm/yyyy', this)" />   
                </td>
            </tr>
        </form>					
        
        <tr>
            <td width="40%" align="right" ><?php echo $AppUI->_('LBL_VALOR_PLANEJADO'); ?> (<?php echo $AppUI->_('LBL_VP'); ?>)</td>
            <td><input type="text" class="text"  name="vp" size="15" readonly="readonly" value="<?php
                if (isset($vlValorPlanejado)) {
                    echo number_format($vlValorPlanejado, 2, ',', '.');
                }
                else
                    echo "";
                ?>"></td>
        </tr>		
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_VALOR_AGREGADO'); ?> (<?php echo $AppUI->_('LBL_VA'); ?>)</td>
            <td><input type="text"  class="text"  name="va" size="15" readonly="readonly" value="<?php
                if (isset($vlValorAgregado)) {
                    echo number_format($vlValorAgregado, 2, ',', '.');
                }
                else
                    echo "";
                ?>" ></td>
        </tr>		
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_VARIACAO_PRAZO'); ?> (<?php echo $AppUI->_('LBL_VPR'); ?>)</td>
            <td><input type="text" class="text" name="vc" size="15" readonly="readonly" value="<?php
                if (isset($vlVariacaoCronograma)) {
                    echo number_format($vlVariacaoCronograma, 2, ',', '.');
                }
                else
                    echo "";
                ?>"></td>
        </tr>		        
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_INDICE_PRAZO'); ?> (<?php echo $AppUI->_('LBL_IDP'); ?>)</td>
            <td><input type="text" class="text" name="idp" size="15" readonly="readonly" value="<?php echo round($vlIndiceDesempenho, 2); ?>"></td>
        </tr>	
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left:20px" >
                <p><?php echo $AppUI->_('LBL_IDP'); ?> < 1: <?php echo $AppUI->_('LBL_IDP_MENOR'); ?>
                    <br><?php echo $AppUI->_('LBL_IDP'); ?> > 1: <?php echo $AppUI->_('LBL_IDP_MAIOR'); ?>
                    <br><?php echo $AppUI->_('LBL_IDP'); ?> = 1: <?php echo $AppUI->_('LBL_IDP_IGUAL'); ?></p>			
            </td>
        </tr>
    </table>
    
    <table  width="60%" align="left" >	    
        <tr>
            <td colspan="2">
                <?php
                if ((!empty($vlPlanejado) || !isset($vlPlanejado)) || (!empty($vlAgregado) || !isset($vlAgregado))) {
                    $url = './modules/monitoringandcontrol/grafico/line_Graph_Schedule.php?titGrafico=' . urlencode(serialize($titGrafico)) . '&titVP=' . urlencode(serialize($titVP)) . '&titVA=' . urlencode(serialize($titVA)) . '&dtConsultaArray=' . urlencode(serialize($dtConsultaArray)) . '&vlPlanejado=' . urlencode(serialize($vlPlanejado)) . '&vlAgregado=' . urlencode(serialize($vlAgregado));
                }
                else
                    $url = './modules/monitoringandcontrol/grafico/line_Graph_Schedule.php';
                ?>
                <img  src="<?php echo $url; ?>" >         
            </td> 						

        </tr>  
    </table>	
</div>
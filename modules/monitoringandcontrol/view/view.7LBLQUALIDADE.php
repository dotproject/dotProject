<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_earn_value.class.php");
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_quality.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");

$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$project_id = dPgetParam( $_GET, 'project_id', 0 ); 

 ini_set('max_execution_time', 180);
 ini_set('memory_limit', $dPconfig['reset_memory_limit']);

global $AppUI;	

    $qualityControl = new ControllerQuality();
    $controllerUtil = new ControllerUtil();
    
    $user = $_POST['user'];
    //Tratamento para o grafico de pizza
    $arQualidadePie = $qualityControl->obterDadosGraficoPizza($project_id, $user); 
      
    //Tratamento para o grafico de barras
    $arQualidadeBarTotal = $qualityControl->obterDataTarefa($project_id, $user);
    $arLabelBar = array();
  
    for($i=0; $i < count($arQualidadeBarTotal); ++$i) {
        $chave = $arQualidadeBarTotal[$i]['month'] . '/' . $arQualidadeBarTotal[$i]['year'];
        array_push($arLabelBar, $chave);
    }
    
    $arQualidadeBar = $qualityControl->obterDadosGraficoBarra($project_id, $user);
    $titGraficoPizza = $AppUI->_('LBL_GRAF_PIZZA');
    $titGraficoBarra = $AppUI->_('LBL_GRAF_BARRA');
?>
<form name="formdata" id="formdata" method="post"  action=""  enctype="multipart/form-data" >	
	<table  width="100%" align="left" >	    
        <tr>
        
            <td colspan="2">
                <select name="user" size="1"  id="user" onchange="submit();">
                    <option value"0"><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>  
                    <?php		   		
                    $list = array();	
                    $list = $qualityControl -> obterUsuarioDeTarefa($project_id);
                    foreach($list as $row){		
                      if($user ==  $row[user_id]){			
                        echo "<option value='$row[user_id]' selected>$row[user_username]</option>";					 
                      }else {
                        echo "<option value='$row[user_id]'>$row[user_username]</option>";
                      }	                          					
                    }
                     ?>     
                </select>
            </td>
        </tr>
        <tr>
            <td >
            <table  align="left" >	    
                <tr>
                    <td >
                    <?php $urlBar = './modules/monitoringandcontrol/grafico/line_Graph_Quality_pie.php?titGrafico='.urlencode(serialize($titGraficoPizza)).'&arQualidade=' .urlencode(serialize( $arQualidadePie)) ?> 
                            <img  src="<?php echo $urlBar; ?>" >     
                    </td>
                </tr>	
          </table>	
          </td>
          <td>
            <table   align="left" >	    
                <tr>
                    <td >
                    <?php $urlBar = './modules/monitoringandcontrol/grafico/line_Graph_Quality_bar.php?titGrafico='.urlencode(serialize($titGraficoBarra)).'&arQualidade=' .urlencode(serialize( $arQualidadeBar)) . '&arLabelBar=' .urlencode(serialize( $arLabelBar))?> 
                            <img  src="<?php echo $urlBar; ?>" >         
                    </td> 						
               </tr>  
          </table>	     
            </td>
        </tr>        	
  </table>	
</form>  


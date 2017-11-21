<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_resources_costs.class.php");

$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$user_id = dPgetParam($_GET, 'user_id');
$controllerResourceCost = new ControllerResourcesCosts();
$controllerUtil = new ControllerUtil();
?>
<script src="./modules/monitoringandcontrol/js/costs.js" charset="ISO-8859-1"> </script>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>
<script src="./modules/monitoringandcontrol/js/util.js"> </script>
<!--  calendar  -->
<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>
<!-- end calendar  -->   

<!-- *************  LISTA  ************ -->	
<br/>
<table class="tbl" id="tb_costList" width="70%" align="center">	
    <tr>
        <th style="width:32%"><?php echo $AppUI->_('LBL_INICIO_VIGENCIA'); ?> </th>
        <th style="width:32%"><?php echo $AppUI->_('LBL_FIM_VIGENCIA'); ?></th>
        <th style="width:32%"><?php echo $AppUI->_('LBL_TAXA_PADRAO'); ?> (<?php echo dPgetConfig("currency_symbol") ?>) </th>
        <th style="width:2%">&nbsp;</th>
        <th style="width:2%">&nbsp;</th>
    </tr>		

    <?php
    $lista = array();
    $lista = $controllerResourceCost->getRecordsByUser($user_id);
    foreach ($lista as $list) {
        ?>										
        <tr>
            <td><?php echo $controllerUtil->formatDate($list[4]); ?> </td>
            <td> <?php echo $controllerUtil->formatDate($list[5]); ?> </td>
            <td> <?php echo number_format($list[2], 2, ',', '.'); ?> </td>
            <td align="center">
                <form name="form_update" method="post" action="" enctype="multipart/form-data" >  
                    <input  type="hidden" name="acao" value="update"  />
                    <input name="user_id" type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
                    <input name="cost_id" type="hidden" id="cost_id" value="<?php echo $list[0]; ?>" />
                    <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />   
                </form>
            </td>                        <td align="center">
                <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=do_costs_aed&user_id=<?php echo $user_id; ?>" enctype="multipart/form-data" >
                    <?php $id = $list[0]; ?> 
                    <input name="dosql" type="hidden" value="do_costs_aed" />	
                    <input  type="hidden" name="acao" value="delete"  />
                    <input name="user_id" type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
                    <input name="cost_id" type="hidden" id="cost_id" value="<?php echo $id; ?>" />
                    <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow(excluir);"  />
                </form>

            </td>

        </tr>						
    <?php } ?>	
</table>	


<!-- ************ UPDATE ************ -->		

<?php
if (isset($_POST['acao']) && $_POST['acao'] == 'update') {
    $id = $_POST['cost_id'];
    ?>		

    <form action="?m=monitoringandcontrol&a=do_costs_aed&user_id=<?php echo $user_id; ?>" method="post" name="form_updateRow" id="form_updateRow" enctype="multipart/form-data">
        <input name="dosql" type="hidden" value="do_costs_aed" />		
        <input name="user_id" type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
        <input  type="hidden" name="cost_id" value="<?php echo $id; ?>"  />
        <input  type="hidden" name="acao" value="updateRow"  />
        <input type="hidden" name="msg_mandatory_empty" value="<?php echo $AppUI->_("LBL_GENERIC_FORM_VALIDATION"); ?>" />
        <input type="hidden" name="msg_date_incorrect" value="<?php echo $AppUI->_("LBL_DATE_FIELD_INVALID_FILLED"); ?>" />
        <input type="hidden" name="msg_date_begin_before_end_date" value="<?php echo $AppUI->_("LBL_DATE_BEGIN_BEFORE_DATE_END"); ?>" />
        <input type="hidden" name="msg_invalid_range" value="<?php echo $AppUI->_("LBL_DATE_INVALID_RANGE"); ?>" />
        <br />
        <table width="70%" align="center" class="tbl">
            <tr>
                <td>&nbsp;

                    <?php
                    $lstData = $controllerResourceCost->getListDt($user_id, $id);
                    echo "<script>
                            var globalData = new Array();
                            var index = 0; 
			 </script>";
                    foreach ($lstData as $lst) {
                        echo "<script>
                                globalData[index] = new Array();
                                globalData[index][0] = '" . $controllerUtil->formatDate($lst[cost_dt_begin]) . "'; 
                                globalData[index][1] = '" . $controllerUtil->formatDate($lst[cost_dt_end]) . "';
                                index++;
                              </script>";
                    }
                    ?>
                </td>
            </tr>
        </table>	

        <table class="tbl"  id="tb_updateRow" width="70%" align="center">	
            <tr>
                <th style="width:32%"><?php echo $AppUI->_('LBL_INICIO_VIGENCIA'); ?> </th>
                <th style="width:32%"><?php echo $AppUI->_('LBL_FIM_VIGENCIA'); ?></th>
                <th style="width:36%"><?php echo $AppUI->_('LBL_TAXA_PADRAO'); ?> (<?php echo dPgetConfig("currency_symbol") ?>)</th>	
            </tr>		
            <tr>

                <?php
                // pega a lista de registros pelo id 
                $records = array();
                $records = $controllerResourceCost->getListById($id);
                // pega a posição dessa linha na tabela
                $pos = $controllerResourceCost->getPosition($id, $user_id);  // get insert row position on table
                $prevPos = $pos - 1;
                $nextPos = $pos + 1;
                $rows = $controllerResourceCost->getRecordsByUser($user_id);

                if ($rows[$prevPos][5] != "") {
                    $dt_endPrev = $rows[$prevPos][5];   // pega a data end da linha anterior
                }else
                    $dt_endPrev = "";

                if ($rows[$nextPos][4] != "") {
                    $dt_iniNext = $rows[$nextPos][4];   // pega a data ini da proxima linha 
                }else
                    $dt_iniNext = "";

                $dt_endPrev = $controllerUtil->formatDate($dt_endPrev);
                $dt_iniNext = $controllerUtil->formatDate($dt_iniNext);

                foreach ($records as $rec) {
                    $dt_ini = $rec[4];
                    $dt_fim = $rec[5];
                    $dt_ini = $controllerUtil->formatDate($dt_ini);
                    $dt_fim = $controllerUtil->formatDate($dt_fim);
                    ?>
                    <td>  
                        <input type="text" class="text"  name="dt_begin"  id="date_edit2" value="<?php echo $dt_ini; ?>" maxlength="10" onkeyup="formatadata(this,event)"/>
                        <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit2'),'dd/mm/yyyy',this)" />     
                    </td>                 
                    <td>  
                        <input type="text" class="text"  name="dt_end"  id="date_edit3"  value="<?php echo $dt_fim; ?>" maxlength="10" onkeyup="formatadata(this,event)"/>
                        <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit3'),'dd/mm/yyyy',this)" />                 
                    </td>
                    <td>  
                        <input type="text" name="tx_pad" id="tx_pad" size="30" value="<?php echo number_format($rec[2], 2, ',', '.'); ?>" />     
                    </td>         
    <?php } ?> 
            </tr>        
        </table>
        <table width="70%" align="center">
            <tr>
                <td>				
                    <input type="button" class="button" value="Atualizar"  onclick="return upValidate();"   />
                </td>
            </tr>
        </table>
    </form>  <!-- end form_updateRow  -->		


<?php } else { ?>

    <!-- ************ ADD CADASTRO  ************ -->	

    <form action="?m=monitoringandcontrol&a=do_costs_aed&user_id=<?php echo $user_id; ?>" method="post" name="form_costs" id="form_costs" enctype="multipart/form-data">
        <input name="dosql" type="hidden" value="do_costs_aed" />		
        <input name="user_id" type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
        <input type="hidden" name="cost_id" value=""  />
        <input type="hidden" name="acao" value="insert"  />
        <input type="hidden" name="msg_mandatory_empty" value="<?php echo $AppUI->_("LBL_GENERIC_FORM_VALIDATION"); ?>" />
        <input type="hidden" name="msg_date_incorrect" value="<?php echo $AppUI->_("LBL_DATE_FIELD_INVALID_FILLED"); ?>" />
        <input type="hidden" name="msg_date_begin_before_end_date" value="<?php echo $AppUI->_("LBL_DATE_BEGIN_BEFORE_DATE_END"); ?>" />
        <input type="hidden" name="msg_invalid_range" value="<?php echo $AppUI->_("LBL_DATE_INVALID_RANGE"); ?>" />
        
        
        <br />
        <table width="70%" align="center">
            <tr>
                <td>&nbsp;
    <?php
    $lstData = $controllerResourceCost->getListDt($user_id, null);
    echo "<script>
            var globalData = new Array(); 
            var index = 0; 
	</script>";

    foreach ($lstData as $lst) {
        echo "<script>
                globalData[index] = new Array();
                globalData[index][0] = '" . $controllerUtil->formatDate($lst[cost_dt_begin]) . "'; 
                globalData[index][1] = '" . $controllerUtil->formatDate($lst[cost_dt_end]) . "';
                index++;
            </script>";
    }
    ?>			
                </td>
            </tr>
        </table>	

        <table class="tbl" id="tb_addCosts" width="70%" align="center">
            <tr>
                <th style="width:32%"><?php echo $AppUI->_('LBL_INICIO_VIGENCIA'); ?> </th>
                <th style="width:32%"><?php echo $AppUI->_('LBL_FIM_VIGENCIA'); ?></th>
                <th style="width:36%"><?php echo $AppUI->_('LBL_TAXA_PADRAO'); ?> (<?php echo dPgetConfig("currency_symbol") ?>)</th>		
            </tr>		
            <tr id="1000">		
                <td nowrap="nowrap">                 	  
                    <input type="text" class="text"  name="dt_begin"  id="date_edit" maxlength="10" onkeyup="formatadata(this,event)"/>
                    <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)" />     
                </td>

                <td nowrap="nowrap">                 	  
                    <input type="text" class="text"  name="dt_end"  id="date_edit1" maxlength="10" onkeyup="formatadata(this,event)"/>
                    <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit1'),'dd/mm/yyyy',this)" />                 
                </td>
                <td>  
                    <input type="text" name="tx_pad" id="tx_pad" size="30" />     
                </td>         
            </tr>        
        </table>
        <table width="70%" align="center">
            <tr>
                <td>				
                    <input type="button" class="button"  onclick="return valida();" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" />
                </td>
            </tr>
        </table>
    </form>
<?php }
?>
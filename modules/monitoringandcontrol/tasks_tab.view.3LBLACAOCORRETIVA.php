<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$task_id = dPgetParam($_GET, 'task_id', 0);
$controllerAcaoCorretiva = new ControllerAcaoCorretiva();
?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>
<!-- ***************** LISTA A��O CORRETIVA ****************************-->	
<br/>
<p align="right" style="width:85%"> 
    <input type="button" class="button" value="<?php echo $AppUI->_('LBL_NOVA') . ' ' . $AppUI->_('LBL_ACAO_CORRETIVA'); ?>"  onclick="window.location='?m=monitoringandcontrol&a=addedit_acao_corretiva&task_id=<?php echo $task_id; ?>&project_id=<?php echo $project_id; ?>' "/>

</p>
<table class="std"  width="60%" align="center" style="border-radius:6px">	
    <caption> <b> <?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?></b></caption>       
    <tr>
        <th><?php echo $AppUI->_('LBL_DESCRICAO'); ?> </th>
        <th style="width:7%">&nbsp;</th>
        <th style="width:7%">&nbsp;</th>	
    </tr>		
    <?php
    $list = array();
    $list = $controllerAcaoCorretiva->getChangeRequest($task_id);
    foreach ($list as $row) {
        $task_id = $row[1];
        ?>		
        <tr>
            <td style="padding-left:15px"><?php echo $row[4]; ?> </td>
            <td align="center">
                <form name="form_update" method="post" action="?m=monitoringandcontrol&a=addedit_update_acao_corretiva&task_id=<?php echo $task_id; ?>" enctype="multipart/form-data" >  
                    <input  type="hidden" name="acao" value="update"  />
                    <input name="change_id" type="hidden" id="change_id" value="<?php echo $row[0]; ?>"> 
                    <input name="task_id" type="hidden" id="change_id" value="<?php echo $task_id; ?>">  
                    <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />                                 
                </form>
            </td>	
            <td align="center">
                <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=do_acao_corretiva_aed&task_id=<?php echo $task_id; ?>" enctype="multipart/form-data" >
                    <input name="dosql" type="hidden" value="do_acao_corretiva_aed" />                              
                    <input  type="hidden" name="acao" value="delete"  />                           
                    <input name="change_id" type="hidden" id="change_id" value="<?php echo $row[0]; ?>">
                    <input name="task_id" type="hidden" id="change_id" value="<?php echo $task_id; ?>">  
                    <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow(excluir);"  />
                </form>                                
            </td>
        </tr>						
    <?php } ?>	
</table>	


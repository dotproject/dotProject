<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$task_id = dPgetParam($_GET, 'task_id', 0);
$project_id = dPgetParam($_GET, 'project_id', 0);
$controllerAcaoCorretiva = new ControllerAcaoCorretiva();
?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>

<!-- ***************** LISTA AÇÃO CORRETIVA ****************************-->	
<br/>
<p align="right" style="width:95%"> 
    <input type="button" class="button" value="<?php echo $AppUI->_('LBL_NOVA') . ' ' . $AppUI->_('LBL_ACAO_CORRETIVA'); ?>"  onclick="window.location='?m=monitoringandcontrol&a=addedit_acao_corretiva&project_id=<?php echo $project_id; ?>' "/>   
</p>
<table class="tbl"  width="95%" align="center">	
    <caption> <b> <?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?></b></caption>       
    <tr>
        <th><?php echo $AppUI->_('LBL_DESCRICAO'); ?> </th>
        <th style="width:10%"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
        <th style="width:5%">&nbsp;</th>
        <th style="width:5%">&nbsp;</th>	
    </tr>		
    <?php
    $list = $controllerAcaoCorretiva->getChangeRequestByProject($project_id);
    foreach ($list as $row) {
        $task_id = $row[1];
        ?>		
        <tr>
            <td><?php echo $row[change_request]; ?> </td>
            <td>
                <?php
                $select = Array(0 => $AppUI->_('LBL_SELECIONE') . "...", 1 => $AppUI->_('LBL_ABERTO'), 2 => $AppUI->_('LBL_FECHADO'), 3 => $AppUI->_('LBL_DESENVOLVIMENTO'), 4 => $AppUI->_('LBL_CANCELADO'));
                for ($i = 0; $i < count($select); $i++) {
                    if ($row[change_status] == $i) {
                        echo "<option value='" . $row[change_status] . "' selected>" . $select[$i] . "</option>";
                        break;
                    }
                }
                ?>       					
            </td>
            <td align="center">
                <form name="form_update" method="post" action="?m=monitoringandcontrol&a=addedit_update_acao_corretiva&project_id=<?php echo $project_id; ?>" enctype="multipart/form-data" >  
                    <input  type="hidden" name="acao" value="update"  />
                    <input name="change_id" type="hidden" id="change_id" value="<?php echo $row[0]; ?>"> 
                    <input name="task_id" type="hidden" id="task_id" value="<?php echo $task_id; ?>">  
                    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>"> 
                    <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />                                 
                </form>
            </td>	
            <td align="center">
                <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=do_acao_corretiva_aed&project_id=<?php echo $project_id; ?>" enctype="multipart/form-data" >
                    <input name="dosql" type="hidden" value="do_acao_corretiva_aed" />                              
                    <input  type="hidden" name="acao" value="delete"  />                           
                    <input name="change_id" type="hidden" id="change_id" value="<?php echo $row[0]; ?>">
                    <input name="task_id" type="hidden" id="task_id" value="<?php echo $task_id; ?>">  
                    <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow(excluir);"  />
                </form>                                
            </td>
        </tr>						
    <?php } ?>	
</table>	


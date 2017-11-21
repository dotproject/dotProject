<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_baseline.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");

$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$project_id = dPgetParam( $_GET, 'project_id', 0 );
$controllerBaseline= new ControllerBaseline();
$controllerUtil = new ControllerUtil();

?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>
<!-- ***************** LISTA ATAS ****************************-->	
   <br/>
    <p align="right" style="width:95%"> 
            <input type="button" class="button" value="<?php echo $AppUI->_('LBL_NOVA') . ' ' . $AppUI->_('LBL_BASELINE'); ?>"  onclick="window.location='?m=monitoringandcontrol&a=addedit_baseline&project_id=<?php echo $project_id ; ?>';"/>
    </p>

   <table class="tbl"  width="95%" align="center" >	
      <caption> <b> <?php echo $AppUI->_('LBL_BASELINE'); ?>  </b></caption>
        <tr>

      <tr>
         <th><?php echo $AppUI->_('LBL_NOME'); ?> </th>
         <th><?php echo $AppUI->_('LBL_VERSAO'); ?> </th>
         <th><?php echo $AppUI->_('LBL_DATA'); ?></th>
         <th>&nbsp;</th>
         <th>&nbsp;</th>
        </tr>		
      
            <?php 
               $list = array();					
               $list = $controllerBaseline -> listBaseline($project_id);
               foreach($list as $row){   				
            ?>		
               <tr>
                  <td><?php echo $row['baseline_name']; ?> </td>
                  <td><?php echo $row['baseline_version']; ?> </td>
                  <td><?php echo $controllerUtil->formatDateTime($row['baseline_date']);  ?> </td>
                        <td align="center">
                        
                        <form name="form_update" method="post" action="?m=monitoringandcontrol&a=addedit_update_baseline&project_id=<?php echo $project_id; ?>&idBaseline=<?php echo $row['baseline_id'] ; ?>" enctype="multipart/form-data" >  
                           <input  type="hidden" name="acao" value="update"  />
                           <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
                           <input name="idBaseline" type="hidden" id="meeting_id" value="<?php echo $row['baseline_id']; ?>">
                           <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />                                 
                        </form>
                        </td>	
                        <td align="center">
                        <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=addedit_update_baseline&project_id=<?php echo $project_id; ?>" enctype="multipart/form-data" >
                           <input name="dosql" type="hidden" value="do_baseline_aed" />	                              
                           <input  type="hidden" name="acao" value="delete"  />
                           <input name="idBaseline" type="hidden" id="idBaseline" value="<?php echo $row['baseline_id']; ?>">
                           <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
                           <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow(excluir);"  />
                        </form>                                
                        </td>
                   </tr>						
            <?php  } ?>	
   </table>
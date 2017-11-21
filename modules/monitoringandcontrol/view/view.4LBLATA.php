<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_ata.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");

$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$project_id = dPgetParam($_GET, 'project_id', 0);
$controllerAta = new ControllerAta();
$controllerUtil = new ControllerUtil();
?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>
<!-- ***************** LISTA ATAS ****************************-->	
<br/>
<p align="right" style="width:95%">     		
    <input type="button" class="button" value="<?php echo $AppUI->_('LBL_NOVA') . ' ' . $AppUI->_('LBL_ATA'); ?>"  onclick="window.location='?m=monitoringandcontrol&a=addedit_ata&project_id=<?php echo $project_id; ?>' "/>
</p>

<table class="tbl"  width="95%" align="center">	
    <caption> <b> <?php echo $AppUI->_('LBL_ATA'); ?> </b></caption>
    <tr>

    <tr>
        <th> <?php echo $AppUI->_('LBL_DATA'); ?> </th>
        <th> <?php echo $AppUI->_('LBL_TIPO'); ?> </th>
        <th> <?php echo $AppUI->_('LBL_TITULO'); ?> </th>

        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>		

    <?php
    $list = array();
    $list = $controllerAta->getMeeting($project_id);
    foreach ($list as $row) {
        ?>		

        <tr>
            <td><?php echo $controllerUtil->formatDate($row[dt_meeting_begin]); ?> </td>
            <td><?php echo $row[meeting_type_name]; ?> </td>
            <td><?php echo $row[ds_title]; ?> </td>

            <td align="center">
                <form name="form_update" method="post" action="?m=monitoringandcontrol&a=addedit_update_ata&project_id=<?php echo $project_id; ?>" enctype="multipart/form-data" >  
                    <input  type="hidden" name="acao" value="update"  />
                    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
                    <input name="meeting_id" type="hidden" id="meeting_id" value="<?php echo $row[0]; ?>">
                    <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />                                 
                </form>
            </td>	
            <td align="center">
                <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=do_ata_aed&project_id=<?php echo $project_id; ?>" enctype="multipart/form-data" >
                    <input name="dosql" type="hidden" value="do_ata_aed" />	                              
                    <input  type="hidden" name="acao" value="delete"  />
                    <input name="meeting_id" type="hidden" id="meeting_id" value="<?php echo $row[0]; ?>">
                    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
                    <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow(excluir);"  />
                </form>                                
            </td>
        </tr>						
    <?php } ?>	
</table>	


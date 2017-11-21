<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_projects_responsability.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_quality.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");

$AppUI->savePlace();
$task_id = dPgetParam($_GET, 'task_id', 0);
$controllerQuality = new ControllerQuality();
$controllerUtil = new ControllerUtil();

$lstType = $controllerQuality->getType();
$lstStatus = $controllerQuality->getStatus();
$lstUser = $controllerUtil->getUsers();
?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>
<script src="./modules/monitoringandcontrol/js/quality.js"> </script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/util.js"> </script>
<!--  calendar  -->
<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>
<!-- end calendar  -->   

<br/>
<table class="std" id="tb_resp" width="70%" align="center" style="border-radius:6px">	
    <tr>
        <th style="width:13%"><?php echo $AppUI->_('LBL_TIPO'); ?></th>
        <th style="width:45%"><?php echo $AppUI->_('LBL_ATIVIDADE'); ?></th>
        <th  style="width:12%"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
        <th style="width:20%"><?php echo $AppUI->_('LBL_DATA_ENCERRAMENTO'); ?></th>
        <th style="width:5%">&nbsp;</th>
        <th style="width:5%">&nbsp;</th>	
    </tr>		
    <tr>
        <?php
        $q_records = $controllerQuality->getQualityRecords($task_id);

        foreach ($q_records as $q_rec) {
            //   ' quality_id, quality_type_id, quality_description, quality_status_id, quality_date_end, task_id, user_id  '	
            ?>										
        <tr>
            <td><?php echo $controllerQuality->getTypeName($q_rec[quality_type_id]); ?> </td>
            <td> <?php echo $q_rec[quality_description]; ?> </td>

            <td> <?php echo $controllerQuality->getStatusName($q_rec[quality_status_id]); ?> </td>
            <td> <?php echo $controllerUtil->formatDate($q_rec[quality_date_end]); ?> </td>
            <td align="center">
                <form name="form_update" method="post" action="" enctype="multipart/form-data" >
                    <?php $id = $q_rec[0]; ?>                          
                    <input  type="hidden" name="acao" value="update"  />
                    <input  type="hidden" name="quality_id" value="<?php echo $id; ?>"  />
                    <input name="task_id" type="hidden" id="task_id" value="<?php echo $task_id; ?>">
                    <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />   
                </form>
            </td>                       <td align="center">
                <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=do_quality_aed&task_id=<?php echo $task_id; ?>" enctype="multipart/form-data" >
                    <input name="dosql" type="hidden" value="do_quality_aed" />	                              
                    <input  type="hidden" name="acao" value="delete"  />
                    <input  type="hidden" name="quality_id" value="<?php echo $q_rec[0]; ?>"  />
                    <input name="task_id" type="hidden" id="task_id" value="<?php echo $task_id; ?>">
                    <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow();"  />
                </form>                                
            </td>
        </tr>						
    <?php } ?>	
</table>	

<!-- ***************** UPDATE  ****************************-->		
<?php
if (isset($_POST['acao']) && $_POST['acao'] == 'update') {
    $id = $_POST['quality_id'];
    ?>	
    <form action="?m=monitoringandcontrol&a=do_quality_aed&task_id=<?php echo $task_id; ?>" method="post" name="form_updateRow" id="form_updateRow" enctype="multipart/form-data">
        <input name="dosql" type="hidden" value="do_quality_aed" />		
        <input name="task_id" type="hidden" id="task_id" value="<?php echo $task_id; ?>">
        <input  type="hidden" name="quality_id" value="<?php echo $id; ?>"  />
        <input  type="hidden" name="acao" value="updateRow"  />
        <br/>
        <table width="70%" align="center">
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>	

        <table class="std" id="tb_qlist" width="70%" align="center" style="border-radius:6px">	

            <tr>
                <th style="width:13%"><?php echo $AppUI->_('LBL_TIPO'); ?></th>
                <th style="width:45%"><?php echo $AppUI->_('LBL_ATIVIDADE'); ?></th>
                <th  style="width:12%"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
                <th style="width:20%"><?php echo $AppUI->_('LBL_DATA_ENCERRAMENTO'); ?></th>
                <th style="width:10%">&nbsp;</th>

            </tr>		
            <?php
            $q_list = $controllerQuality->getQualityRecordsById($id);


            foreach ($q_list as $q_row) {
                $dt_end = $q_row[5];
                $dt_end = $controllerUtil->formatDate($dt_end);
            }
            ?> 
            <tr>
                <td>  
                    <select name="typpe" size="1" id="typpe">
                        <option value="0" >Selecione...</option>       
                        <?php
                        foreach ($lstType as $t_list) {
                            if ($q_row[1] == $t_list[0]) {
                                echo "<option value=' $q_row[1] ' selected>" . $controllerQuality->getTypeName($q_row[1]) . "</option>";
                            } else {
                                echo "<option value=' $t_list[0]'>$t_list[1]</option>";
                            }
                        }
                        ?>  
                    </select>  

                </td>

                <td>               
                    <input type="text" name="description" id="description" size="50"  value=" <?php echo $q_row[2]; ?>" />
                </td>

                <td>  
                    <select name="status" size="1" id="status">
                        <option value="0" >Selecione...</option>       
                        <?php
                        foreach ($lstStatus as $s_row) {
                            if ($q_row[4] == $s_row[0]) {
                                echo "<option value=' $q_row[4] ' selected>" . $controllerQuality->getStatusName($q_row[4]) . "</option>";
                            } else {
                                echo "<option value=' $s_row[0]'>$s_row[1]</option>";
                            }
                        }
                        ?>  
                    </select>

                </td>      
                <td>  
                    <input type="text" class="text"  name="date_end"  id="date_edit" maxlength="10" value="<?php echo $dt_end; ?>" onkeyup="formatadata(this,event)"/>
                    <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)"  />     
                </td>  
            </tr>        
        </table>
        <table width="70%" align="center">
            <tr>
                <td>				
                    <input type="submit" class="button"  onclick="return validateUpdateFields();" value="Atualizar"   />
                </td>
            </tr>
        </table>
    </form>    

<?php } else { ?>
    <!-- ***************** ADD  ****************************-->	
    <form action="?m=monitoringandcontrol&a=do_quality_aed&task_id=<?php echo $task_id; ?>" method="post" name="form_resp" id="form_resp" enctype="multipart/form-data">
        <input name="dosql" type="hidden" value="do_quality_aed" />		
        <input name="task_id" type="hidden" id="task_id" value="<?php echo $task_id; ?>">
        <input  type="hidden" name="acao" value="insert"  />
        <br/>
        <?php // TESTES //
        ?>
        <table width="70%" align="center">
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>	

        <table class="std" id="tb_qlist" width="70%" align="center" style="border-radius:6px">	

            <tr>
                <th style="width:13%"><?php echo $AppUI->_('LBL_TIPO'); ?></th>
                <th style="width:45%"><?php echo $AppUI->_('LBL_ATIVIDADE'); ?></th>
                <th  style="width:12%"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
                <th style="width:20%"><?php echo $AppUI->_('LBL_DATA_ENCERRAMENTO'); ?></th>
                <th style="width:10%">&nbsp;</th>
            </tr>		
            <tr >
                <td>  
                    <select name="typpe" size="1" id="typpe">
                        <option value="0" ><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>       
    <?php
    foreach ($lstType as $list) {
        echo "<option value='$list[quality_type_id]' >$list[quality_type_name]</option>";
    }
    ?>  
                    </select>  
                </td>

                <td>               
                    <input type="text" name="description" id="description" size="50" />
                </td>

                <td>  
                    <select name="status" size="1" id="status">
                        <option value="0" ><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>       
    <?php
    foreach ($lstStatus as $status) {
        echo "<option value='$status[quality_status_id]' >$status[quality_status_name]</option>";
    }
    ?>
                    </select>

                </td>      
                <td>  
                    <input type="text" class="text"  name="date_end"  id="date_edit" maxlength="10" onkeyup="formatadata(this,event)" />
                    <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)" />     
                </td>  
            </tr>        
        </table>
        <table width="70%" align="center">
            <tr>
                <td>				
                    <input type="submit" class="button" onClick=" return validateQualityFields();" value="Save" />
                </td>
            </tr>
        </table>
    </form>
<?php } ?>

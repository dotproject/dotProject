<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
$task_id = dPgetParam($_GET, 'task_id', 0);
$project_id = dPgetParam($_GET, 'project_id', 0);

require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_ata.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");


$titulo = $AppUI->_('LBL_NOVA');
$titleBlock = new CTitleBlock("$titulo", 'graph-up.png', $m, $m . '.' . $a);
$titleBlock->show();


$controllerAta = new ControllerAta();
$controllerAcaoCorretiva = new ControllerAcaoCorretiva();
$controllerUtil = new ControllerUtil();
?>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/ata.js"   ></script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/validateChangeRequest.js"></script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/util.js"> </script>
<!--  calendar  -->
<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>
<!-- end calendar  -->   <table width="100%" cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td align="left" colspan="8"><b> <?php echo $AppUI->_('LBL_MONITORACAO'); ?> : <?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?></b></td>
    </tr>
</table>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form action="?m=monitoringandcontrol&a=do_acao_corretiva_aed&task_id=<?php echo $task_id; ?>" method="post" name="form_ata" id="form_ata" enctype="multipart/form-data">	    
    <input name="dosql" type="hidden" value="do_acao_corretiva_aed" />    
    <input  type="hidden" name="acao" value="insert"  />
    <br/>
    <table class="std" width="100%" cellspacing="0" cellpadding="4" border="0" name="table_form">
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td align="right" style="width:10%"><?php echo $AppUI->_('LBL_PROJETO'); ?>:</td>
            <td  style="width:90%" >
                <?php
                if (isset($task_id) && $task_id != '') {

                    $task_name = $controllerUtil->getTaskName($task_id);
                    $project_name = $task_name[0][project_name];
                    $project_id = $task_name[0][project_id];
                } else {
                    $project_retorno = $controllerUtil->getProjectName($project_id);
                    $project_name = $project_retorno[0][project_name];
                }
                ?>
                <input type="text" name="projeto" size="20" id="projeto" readonly="readonly" value="<?php echo $project_name; ?> " />
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" /> 			
                <input type="hidden" name="index" value="0" /> 	
            </td>
            <td colspan="5">&nbsp;
                <?php
                ?>
            </td>
        </tr>
        <tr>
            <td align="right" style="width:10%" ><?php echo $AppUI->_('LBL_TAREFA'); ?>:</td>


            <td  style="width:90%">
                <?php
                if (isset($task_id) && $task_id != '') {

                    $task_name = $controllerUtil->getTaskName($task_id);
                    echo ' <input type="text" name="tarefa" size="40" id="tarefa" readonly="readonly" value="' . $task_name[0][task_name] . '" />';
                    echo ' <input type="hidden" name="task_id" size="40" id="task_id"  value="' . $task_id . '" />';
                } else {
                    ?>
                    <select name="task_id" size="1"  >
                        <option value"0"><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>  
                        <?php
                        $task_project = $project_id;
                        $task_records = $controllerAcaoCorretiva->getTaskRecordsByProject($task_project);
                        foreach ($task_records as $t_row) {

                            echo "<option value='$t_row[0]' >" . $t_row[1] . "</option>";
                        }
                        ?>     
                    </select> 
                <?php } ?>

            </td>  
            <td colspan="5">&nbsp;</td>           
        </tr>
        <tr>
            <td align="right" style="width:10%" ><?php echo $AppUI->_('LBL_IMPACTO'); ?> (<?php echo $AppUI->_('LBL_HORA'); ?>):<span class="span_mandatory">*</span></td>
            <td  style="width:90%" >
                <input name="impact[]" type="text" id="impact" size="3" maxlength="3"  />					
            </td>
            <td colspan="5">&nbsp;</td>
        </tr> 
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_STATUS'); ?>:<span class="span_mandatory">*</span></td>
            <td >            
                <select name="status[]" size="1"  id="status" >
                    <option value="0"><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>  
                    <option value="1"><?php echo $AppUI->_('LBL_ABERTO'); ?></option>  
                    <option value="2"><?php echo $AppUI->_('LBL_FECHADO'); ?></option>  
                    <option value="3"><?php echo $AppUI->_('LBL_DESENVOLVIMENTO'); ?></option>  
                    <option value="4"><?php echo $AppUI->_('LBL_CANCELADO'); ?></option>  
                </select>
            </td>
        </tr>          
        <tr>
            <td  align="right"><?php echo $AppUI->_('LBL_DESCRICAO_DESVIO'); ?>:<span class="span_mandatory">*</span></td>
            <td><textarea  name="description[]" id="description"  rows="4" ></textarea></td>
        </tr>

        <tr>
            <td  align="right"><?php echo $AppUI->_('LBL_CAUSA'); ?>:<span class="span_mandatory">*</span></td>
            <td><textarea  name="cause[]"  id="cause" rows="4" ></textarea></td>
        </tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?>:<span class="span_mandatory">*</span></td>
            <td><textarea  name="acao_corretiva[]"  id="acao_corretiva" rows="4" ></textarea></td>
        </tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_RESPONSAVEL'); ?>:<span class="span_mandatory">*</span></td>
            <td >            
                <select name="user[]" size="1"  id="user" >
                    <option value"0"><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>  
                    <?php
                    $list = array();
                    $list = $controllerUtil->getUsers();
                    foreach ($list as $row) {
                        echo "<option value='" . $row[0] . "' >" . $row[1] . "</option>";
                    }
                    ?>     
                </select>
            </td>
        </tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_ATA'); ?>:<span class="span_mandatory">*</span></td>            
            <td >            
                <select name="ata" size="1"  id="ata" >
                    <option value"0"><?php echo $AppUI->_('LBL_SELECIONE'); ?>...</option>  
                    <?php
                    $lista = $controllerAta->getMeeting($project_id);
                    foreach ($lista as $line) {

                        $data = $controllerUtil->formatDate($line[dt_meeting_begin]);
                        echo "<option value='" . $line[meeting_id] . "'>" . $data . " - " . $line[ds_title] . "</option>";
                    }
                    ?>    
                </select>
            </td>
        </tr>
        <tr>
            <td align="right" ><?php echo $AppUI->_('LBL_PRAZO'); ?>:<span class="span_mandatory">*</span></td>
            <td nowrap="nowrap" > 
                <input type="text" class="text"  name="date_limit[]"  id="date_edit" maxlength="10" onkeyup="formatadata(this,event)" />
                <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)" />     
            </td>   
        </tr>
   
    <tr>
        <td>
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
        </td>
        <td align="right">
            <input type="button" value="<?php echo $AppUI->_('Gravar'); ?>" class="button" onclick="validateChangeRequest();"  />
        </td>
    </tr>
     </table>
</form>


<span class="span_mandatory">*</span>&nbsp;<?php echo $AppUI->_("LBL_REQUIRED_FIELD"); ?>
<span style="display: none" id="validation_massage"><?php echo $AppUI->_("LBL_GENERIC_FORM_VALIDATION"); ?></span>
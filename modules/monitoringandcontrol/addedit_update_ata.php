<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_ata.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_report.class.php");

$AppUI->savePlace();
$project_id = dPgetParam($_GET, 'project_id', 0);
$titulo = $AppUI->_('LBL_EDITAR') . ' ' . $AppUI->_('LBL_ATA_SEM_ACENTO');
$titleBlock = new CTitleBlock($titulo, 'graph-up.png', $m, $m . '.' . $a);
$titleBlock->show();

$controllerUtil = new ControllerUtil();
$controllerAta = new ControllerAta();
$controllerReport = new ControllerReport();
$controllerAcaoCorretiva = new ControllerAcaoCorretiva();
$meeting_id = $_POST['meeting_id'];
$meetingData = $controllerAta->getListById($meeting_id);
?>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/ata.js" ></script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jquery.js" ></script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/util.js"> </script>
<style>
    .tdLabel{
        width: fit-content;
    }

    #p1,#p2,#p3{
        width:100%;
    }
</style>
<!--  calendar  -->
<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>

<!-- end calendar  -->  
<table width="100%" cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td align="left" colspan="8"><b> <?php echo $AppUI->_('LBL_MONITORACAO'); ?> : <?php echo $AppUI->_('LBL_ATA'); ?> </b></td>
    </tr>
</table>
<form action="?m=monitoringandcontrol&a=do_ata_aed&project_id=<?php echo $project_id; ?>" method="post" name="form_ata" id="form_ata" enctype="multipart/form-data">
    <input name="dosql" type="hidden" value="do_ata_aed" />
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
    <input type="hidden" name="meeting_id"  id="meeting_id" value="<?php echo $meeting_id; ?>">  
    <input  type="hidden" name="acao" value="updateRow"  />
    <input id="items_ids_to_delete" type="hidden" value="" name="items_ids_to_delete">
    <br />
    
    <table  class="std" width="100%"  border="0">
        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_PROJETO'); ?>:</td>
            <td>
                <?php $project_name = $controllerUtil->getProjectName($project_id); ?>
                <a href="?m=projects&a=view&project_id=<?php echo $project_id; ?>" target="blank"><?php echo $project_name[0][0] ?></a>
            </td>
        </tr>
        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_DATA'); ?>:</td>
            <td nowrap="nowrap">                 	  
                <input type="text" class="text"  name="dt_begin"  id="date_edit"  value="<?php echo $controllerUtil->formatDate($meetingData[dt_meeting_begin]); ?>" maxlength="10" onkeyup="formatadata(this,event)"/>
                <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)" />     
            </td>   
        </tr>
        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_HORA'); ?>:</td>
            <td >
                <select name="hr_begin" size="1" id="hr_begin" class="text"> 		
                    <?php
                    $time = $controllerUtil->getHora($meetingData[dt_meeting_begin]);
                    $hours = $controllerUtil->setHours();

                    for ($i = 0; $i < count($hours); $i++) {
                        if ($time[0] == $hours[$i]) {
                            echo "<option value=' $time[0]' selected>" . $time[0] . "</option>";
                        } else {
                            echo " <option value='$hours[$i]'> $hours[$i]</option>";
                        }
                    }
                    ?>          
                </select>&nbsp;  :

                <select name="min_begin" size="1" id="min_begin" class="text"> 		
                    <?php
                    $time = $controllerUtil->getHora($meetingData[dt_meeting_begin]);
                    $minutes = $controllerUtil->setMinutes();
                    for ($i = 0; $i < count($minutes); $i++) {
                        if ($time[1] == $minutes[$i]) {
                            echo "<option value='$time[1]' selected>" . $time[1] . "</option>";
                        } else {
                            echo " <option value='$minutes[$i]'> $minutes[$i]</option>";
                        }
                    }
                    ?>          
                </select> 			
                <?php echo $AppUI->_('LBL_AS'); ?> 
                <select name="hr_end" size="1" id="hr_end" class="text"> 		
                    <?php
                    $time = $controllerUtil->getHora($meetingData[dt_meeting_end]);
                    $hours = $controllerUtil->setHours();

                    for ($i = 0; $i < count($hours); $i++) {
                        if ($time[0] == $hours[$i]) {
                            echo "<option value=' $time[0]' selected>" . $time[0] . "</option>";
                        } else {
                            echo " <option value='$hours[$i]'> $hours[$i]</option>";
                        }
                    }
                    ?>          
                </select>&nbsp;  :		

                <select name="min_end" size="1" id="min_end" class="text"> 		
                    <?php
                    $time = $controllerUtil->getHora($meetingData[dt_meeting_end]);
                    $minutes = $controllerUtil->setMinutes();
                    for ($i = 0; $i < count($minutes); $i++) {
                        if ($time[1] == $minutes[$i]) {
                            echo "<option value=' $time[1] ' selected>" . $time[1] . "</option>";
                        } else {
                            echo " <option value='$minutes[$i]'> $minutes[$i]</option>";
                        }
                    }
                    ?>          
                </select> 

            </td>
        </tr>

        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_TITULO'); ?>:</td>
            <td>
                <input type="text" class="text" name="title" id="title" size="75%"  value="<?php echo $meetingData[ds_title]; ?>" />
            </td>
        </tr>

        <tr>
            <td class="tdLabel">&nbsp;</td>
            <td>
                <table>
                    <tr>
                        <td><?php echo $AppUI->_('LBL_USUARIO'); ?>:</td>
                        <td> &nbsp;</td>
                        <td><?php echo $AppUI->_('LBL_PARTICIPANTES'); ?>:</td>
                    </tr>
                    <tr>
                        <td >            
                            <select name="users" size="4" multiple="multiple" id="users" style="width:190px" class="text">
                                <?php
                                $list = array();
                                $list = $controllerUtil->getUsers();
                                foreach ($list as $row) {
                                    echo "<option value='$row[0]' >$row[1]</option>";
                                }
                                ?>     
                            </select>
                        </td>
                        <td align="center" valign="middle">
                            <input type="button" class="button" onClick="move(this.form.participants,this.form.users)" value="<<">
                            <input type="button" class="button" onClick="move(this.form.users,this.form.participants)" value=">>">
                        </td>
                        <td >            
                            <select name="participants[]" size="4" multiple="multiple" id="participants" style="width:190px" class="text">
                                <?php
                                $meeting_users = $controllerAta->getUsersById($meeting_id);
                                if (!empty($meeting_users)) {
                                    foreach ($meeting_users as $m_users) {
                                        echo "<option value='$m_users[0]' >" . $controllerUtil->getUsername($m_users[0]) . "</option>";
                                    }
                                }
                                ?>     
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td align="right" class="tdLabel" valign="top">
                <?php echo $AppUI->_('LBL_ASSUNTO'); ?>:
            </td>
            <td>
                <textarea  name="subject" class="text" style="width:75%" rows="4" ><?php echo $meetingData[ds_subject]; ?></textarea>
            </td>
        </tr>

        <tr valign="top">
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_TIPO'); ?>:</td>  
            <td>
                <?php echo $meetingData["meeting_type_name"]; ?>
                <input  type="hidden" name="meeting_type" value="<?php echo $meetingData["meeting_type_id"]; ?>"  />
            </td>
        </tr>    

        <tr>
            <td>
                &nbsp;
            </td>
            <td >
                <?php require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/view/monitoring_checklist.php"); ?>
            </td>
        </tr>

        <tr>
            <td class="tdLabel">
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
            <td align="right">
                <input type="button" value="<?php echo $AppUI->_('Gravar'); ?>" class="button" onclick="validateMeeting();"  />
            </td>
        </tr>        
    </table>


    <br />

    <table>       
        <tr>
            <td colspan="2">	 
                <!-- p1 -->
                <?php require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/view/monitoring_panels.php"); ?>

                <!-- p2 -->
                <table class="tbl" width="80%" id="p2" style="<?php
                if ($meetingData[meeting_type_id] != 2) {
                    echo 'display:none;';
                };
                ?>}">	
                    <tr>
                        <th align="center"><?php echo $AppUI->_('LBL_TAREFA'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_DATA_INICIO'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_DATA_FIM'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_DURACAO'); ?></th>				
                    </tr>	     
                    <?php
                    $tasks = $controllerAta->getTasksFinishedSelected($meeting_id);

                    foreach ($tasks as $task) {
                        ?>
                        <tr>
                            <td  align="center"><?php echo $task[task_name]; ?></td>
                            <td  align="center"><?php echo $controllerUtil->formatDateTime($task[task_start_date]); ?></td>
                            <td  align="center"><?php echo $controllerUtil->formatDateTime($task[task_end_date]); ?></td>
                            <td  align="center"><?php echo $task[task_duration]; ?></td>		
                        </tr>				
                    <?php } ?>	
                </table>                                  

                <!-- p3 -->
                <table class="std"  align="left" border="0"  id="p3" style="<?php
                    if ($meetingData[meeting_type_id] != 4 && $meetingData[meeting_type_id] != 5) {
                        echo 'display:none;';
                    };
                    ?>}">	
                    <tr>
                        <th align="center">%</th>
                        <th align="center"><?php echo $AppUI->_('LBL_TAMANHO'); ?> (<?php echo $AppUI->_('LBL_HORA'); ?>)</th>
                        <th align="center"><?php echo $AppUI->_('LBL_IDC'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_IDP'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_VP'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_VA'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_CR'); ?></th>
                        <th align="center"><?php echo $AppUI->_('LBL_NUMERO_BASELINE'); ?></th>	
                    </tr>		
                    <?php
                    $reports = $controllerAta->getReportSenior($meeting_id);

                    foreach ($reports as $cad) {

                        if ($cad[meeting_idc] < 0.8) {
                            $corIdc = "#FF9FA5";
                        } elseif ($cad[meeting_idc] < 1) {
                            $corIdc = "#FFFFAE";
                        } elseif ($cad[meeting_idc] > 1) {
                            $corIdc = "#B7FFB7";
                        }
                        if ($cad[meeting_idp] < 0.8) {
                            $corIdp = "#FF9FA5";
                        } elseif ($cad[meeting_idp] < 1) {
                            $corIdp = "#FFFFAE";
                        } elseif ($cad[meeting_idp] > 1) {
                            $corIdp = "#B7FFB7";
                        }
                        ?>										
                        <tr>
                            <td  align="center"><?php echo round($cad[meeting_percentual], 2); ?></td>
                            <td  align="center"><?php echo $cad[meeting_size]; ?> </td>
                            <td bgcolor="<?php echo $corIdc; ?>" align="center" ><?php echo number_format($cad[meeting_idc], 2, ',', '.'); ?> </td>
                            <td bgcolor="<?php echo $corIdp; ?>" align="center" ><?php echo number_format($cad[meeting_idp], 2, ',', '.'); ?> </td>							
                            <td align="center" ><?php echo number_format($cad[meeting_vp], 2, ',', '.'); ?> </td>
                            <td align="center" ><?php echo number_format($cad[meeting_va], 2, ',', '.'); ?> </td>
                            <td align="center" ><?php echo number_format($cad[meeting_cr], 2, ',', '.'); ?> </td>
                            <td align="center"><?php echo $cad[meeting_baseline]; ?> </td>								
                        </tr>				
                    <?php } ?>	
                    <tr>
                        <td colspan='8'align="center">
                            <table class="std" width="100%" >
                                <tr><td align="center" width="20" style="border-style:solid;border-width:1px" bgcolor="#FF9FA5"></td>
                                    <td align="left">&lt; 0,8</td>
                                    <td width="20" style="border-style:solid;border-width:1px" bgcolor="#FFFFAE">&nbsp; &nbsp;</td>
                                    <td align="left">&lt; 1</td>
                                    <td width="20" style="border-style:solid;border-width:1px" bgcolor="#B7FFB7">&nbsp; &nbsp;</td>
                                    <td align="left">&gt; 1</td>
                                </tr>
                            </table>
                        </td>   						
                    </tr>	                    
                </table>

            </td>                    
        </tr>
    </table>

    <!--************************** TAB  ***************************************** -->
    <script>
        function alteraTab_0(){	
            document.getElementById("pendencias_ata").style.display = "block";	
            document.getElementById('toptab_0').style.background = "url(style/default/images/tabSelectedBg.png)";
            document.getElementById("pendencias_anteriores").style.display = "none";	
            document.getElementById('toptab_1').style.background = "url(style/default/images/tabBg.png)";	
        }	

    </script>
    <script>
        function popNovaPendencia() {	 															 
            window.open("?m=monitoringandcontrol&a=nova_pendencia&dialog=1&project_id=<?php echo $project_id; ?>", "pendencia", "left=300,top=50,height=790,width=700");
        }
        function deleteRole(rowId){
            var id=rowId;
            var field=document.getElementById("items_ids_to_delete");
            field.value+=field.value==""? id: id;
            var i=document.getElementById(rowId).rowIndex;
            document.getElementById('tbl_pendencias').deleteRow(i);

        }
    </script>
    <script type="text/javascript" src="./modules/monitoringandcontrol/js/pendencias.js"   ></script>


    <table width="100%" cellspacing="0" cellpadding="0" border="0" summary="tabbed view">  
        <tbody> 
            <tr> 

                <td>

                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>                
                            <tr>

                                <td valign="middle">
                                    <img border="0" alt="" id="lefttab_0" src="./style/default/images/tabSelectedLeft.png">
                                </td>
                                <td nowrap="nowrap" valign="middle" style="background: url(style/default/images/tabSelectedBg.png);" id="toptab_0">&nbsp;
                                    <a href=" #" onclick="alteraTab_0();" ><?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?></a>&nbsp;
                                </td>
                                <td valign="middle">
                                    <img border="0" alt="" src="./style/default/images/tabSelectedRight.png" id="righttab_0">
                                </td>
                                <td class="tabsp"><img height="1" width="3" alt="" src="./images/shim.gif">
                                </td>			

                            </tr>       
                        </tbody>
                    </table>        
                </td>
            </tr><!-- tabs-->

            <tr> 
                <td class="tabox" width="100%" colspan="69">

                    <div id="pendencias_ata" style="display:block">  <!--toptab_0-->


                        <p align="left" style="width:85%"> 
                        </p>   


                        <table id="tbl_pendencias" class="std"  width="70%" align="center" style="border-radius:5px">	   
                            <tr>
                                <th align="center"  style="width:25%"><?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?></th>
                                <th align="center" style="width:15%"><?php echo $AppUI->_('LBL_RESPONSAVEL'); ?> </th>
                                <th align="center" style="width:20%"><?php echo $AppUI->_('LBL_PRAZO'); ?> </th>
                                <th align="center" style="width:12%"><?php echo $AppUI->_('LBL_STATUS'); ?> </th> 
                                <th style="width:3%">&nbsp; </th> 
                            </tr>		
                            <?php
                            $records = $controllerAcaoCorretiva->getChangeRequestByMeeting($meeting_id);

                            foreach ($records as $rec) {
                                ?>
                                <tr>  
                                    <td> <?php echo $rec[change_description]; ?></td>
                                    <td align="center"><?php echo $rec[user_username]; ?></td>        
                                    <td align="center"><?php echo $controllerUtil->formatDate($rec[change_date_limit]); ?> </td> 
                                    <td align="center">
                                        <?php
                                        $select = Array(0 => $AppUI->_('LBL_SELECIONE') . "...", 1 => $AppUI->_('LBL_ABERTO'), 2 => $AppUI->_('LBL_FECHADO'), 3 => $AppUI->_('LBL_DESENVOLVIMENTO'), 4 => $AppUI->_('LBL_CANCELADO'));
                                        for ($i = 0; $i < count($select); $i++) {
                                            if ($rec[change_status] == $i) {
                                                echo $select[$i];
                                            }
                                        }
                                        ?>       
                                    </td >                
                                    <td align="center">
                                    </td>
                                </tr>  
                            <?php } ?>          
                        </table>
                </td>
            </tr>
        </tbody>
    </table>
</form>

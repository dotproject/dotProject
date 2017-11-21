<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_ata.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_report.class.php");

$AppUI->savePlace();
$project_id = dPgetParam($_GET, 'project_id', 0);
$titulo = $AppUI->_('LBL_NOVA') . ' ' . $AppUI->_('LBL_ATA_SEM_ACENTO');
$titleBlock = new CTitleBlock($titulo, 'graph-up.png', $m, $m . '.' . $a);
$titleBlock->show();

$controllerUtil = new ControllerUtil();
$controllerAta = new ControllerAta();
$controllerReport = new ControllerReport();
?>
<style>
    .tdLabel{
        width: fit-content;
    }
    #p1,#p2,#p3{
        width:100%;
    }
</style>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/ata.js" ></script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jquery.js" ></script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/util.js"> </script>
<script type="text/javascript">
    $(document).ready(function() {	
        $('#meeting_type').bind('change', function() {
            var optionValue = $("#meeting_type").val();		
            switch (optionValue)
            {
                case '2':
                    $("table#p1").hide();
                    $("table#p1_checklist").hide();
                    $("table#p2").show();
                    $("table#p3").hide();	
                    break;
                case '3':
                    $("table#p1").show();
                     $("table#p1_checklist").show();
                    $("table#p2").hide();
                    $("table#p3").hide();
                    break;
                case '4':
                    $("table#p1").hide();
                    $("table#p1_checklist").hide();
                    $("table#p2").hide();
                    $("table#p3").show();	
                    break;
                case '5':
                    $("table#p1").show();
                     $("table#p1_checklist").show();
                    $("table#p2").hide();
                    $("table#p3").show();	
                    break;				
                default:		       
                    $("table#p1").hide();	
                    $("table#p1_checklist").hide();
                    $("table#p2").hide();
                    $("table#p3").hide();
                    break;
            }
        });
    });
</script>
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
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>" />
    <input  type="hidden" name="acao" value="insert"  />
    <input id="items_ids_to_delete" type="hidden" value="" name="items_ids_to_delete">
    <br />
    <table  class="std" width="100%" cellspacing="0" cellpadding="4" border="0" >
        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_PROJETO'); ?>:</td>
            <td>
                <?php $project_name = $controllerUtil->getProjectName($project_id); ?>
                <a href="?m=projects&a=view&project_id=<?php echo $project_id; ?>" target="blank"><?php echo $project_name[0][0] ?></a>					
            </td>
        </tr>

        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_DATA'); ?>:</td>
            <td nowrap="nowrap" >                 	  
                <input type="text" class="text"  name="dt_begin"  id="date_edit" maxlength="10" onkeyup="formatadata(this,event)"/>
                <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)" />     
            </td>   
        </tr>

        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_HORA'); ?>:</td>
            <td>
                <select class="text" name="hr_begin" size="1" id="hr_begin"> 		
                    <?php
                    $hours = $controllerUtil->setHours();
                    for ($i = 0; $i < count($hours); $i++) {
                        echo " <option value='$hours[$i]'> $hours[$i]</option>";
                    }
                    ?>          
                </select>&nbsp;  :

                <select class="text" name="min_begin" size="1" id="min_begin"> 		
                    <option value="0">00</option>
                    <?php
                    $minutes = $controllerUtil->setMinutes();
                    for ($i = 0; $i < count($minutes); $i++) {
                        echo " <option value='$minutes[$i]'> $minutes[$i]</option>";
                    }
                    ?>          
                </select> 
                <?php echo $AppUI->_('LBL_AS'); ?> 

                <select class="text" name="hr_end" size="1" id="hr_end"> 		
                    <option value="0">00</option>
                    <?php
                    $hours = $controllerUtil->setHours();
                    for ($i = 0; $i < count($hours); $i++) {
                        echo " <option value='$hours[$i]'> $hours[$i]</option>";
                    }
                    ?>          
                </select> &nbsp;:

                <select class="text" name="min_end" size="1" id="min_end"> 		
                    <option value="0">00</option>
                    <?php
                    $minutes = $controllerUtil->setMinutes();
                    for ($i = 0; $i < count($minutes); $i++) {
                        echo " <option value='$minutes[$i]'> $minutes[$i]</option>";
                    }
                    ?>          
                </select> 
            </td>
        </tr>

        <tr>
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_TITULO'); ?></td>
            <td ><input class="text" type="text" name="title" id="title" size="75%"  /></td>
        </tr>

        <tr>
            <td class="tdLabel"> &nbsp;</td>
            <td>
                <table>
                    <tr>
                        <td>&nbsp;</td>
                        <td ><?php echo $AppUI->_('LBL_USUARIO'); ?>:</td>
                        <td>&nbsp;</td>
                        <td ><?php echo $AppUI->_('LBL_PARTICIPANTES'); ?>:</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td >            
                            <select class="text" name="users" size="4" multiple="multiple" id="users" style="width:190px">
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
                            <select class="text" name="participants[]" size="4" multiple="multiple" id="participants" style="width:190px">
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td align="right" class="tdLabel" valign="top" ><?php echo $AppUI->_('LBL_ASSUNTO'); ?>:</td>
            <td>
                <textarea  name="subject" cols="106" rows="4" class="text"></textarea>
            </td>
        </tr>

        <tr valign="top">
            <td align="right" class="tdLabel"><?php echo $AppUI->_('LBL_TIPO'); ?>:</td>  
            <td >
                <select class="text" name="meeting_type" size="1" id="meeting_type">
                    <?php
                    $meetingTypeLabels = array(
                        "Delivery" => $AppUI->_("LBL_REPORT_TYPE_DELIVERY"),
                        "Monitoring" => $AppUI->_("LBL_REPORT_TYPE_MONITORING"),
                        "Standard" => $AppUI->_("LBL_REPORT_TYPE_STANDARD"),
                        "Status Report" => $AppUI->_("LBL_REPORT_TYPE_STATUS_REPORT"),
                        "Monitoring / Status Report" => $AppUI->_("LBL_REPORT_TYPE_MONITORING_STATUS_REPORT"),
                    );

                    $mt_lista = $controllerAta->getMeetingType();
                    foreach ($mt_lista as $mt_row) {
                        echo "<option value='$mt_row[0]' >" . $meetingTypeLabels[$mt_row[1]] . "</option>";
                    }
                    ?>  
                </select> 			
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <?php require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/view/monitoring_checklist.php"); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
            <td align="right"><input type="button" value="<?php echo $AppUI->_('Gravar'); ?>" class="button" onclick="validateMeeting();"  /></td>
        </tr>
    </table>

    <br />      
    <?php require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/view/monitoring_panels.php"); ?>
    <table class="tbl" id="p2" style="display:none;" >	
        <tr>

            <th align="center"><?php echo $AppUI->_('LBL_TAREFA'); ?></th>
            <th align="center"><?php echo $AppUI->_('LBL_DATA_INICIO'); ?></th>
            <th align="center"><?php echo $AppUI->_('LBL_DATA_FIM'); ?></th>
            <th align="center"><?php echo $AppUI->_('LBL_DURACAO'); ?></th>
            <th align="center"><?php echo $AppUI->_('LBL_ENTREGUE'); ?></th>
        </tr>	     
        <?php
        $tasks = $controllerAta->getTasksFinished($project_id);
        foreach ($tasks as $task) {
            ?>
            <tr>
                <td  align="left"><?php echo $task[task_name]; ?></td>
                <td  align="center"><?php echo $controllerUtil->formatDateTime($task[task_start_date]); ?></td>
                <td  align="center"><?php echo $controllerUtil->formatDateTime($task[task_end_date]); ?></td>
                <td  align="center"><?php echo $task[task_duration]; ?></td>		
                <td width="5%">                                         
                    <select class="text" name="item_select_status_entrega[]" size="1">
                        <option value="0" ><?php echo $AppUI->_('LBL_SIM'); ?></option>
                        <option value="1" ><?php echo $AppUI->_('LBL_NAO'); ?></option>
                    </select>
                    <input type="hidden" name="task_id_entrega[]" value="<?php echo $task[task_id]; ?>"   />
                </td>
            </tr>				
        <?php } ?>	
    </table>                                  

    <table class="std"  style="display:none;" border="0"  id="p3" style="background-color: #FFF" >	
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
        $companies = $controllerReport->getProjectCompany($project_id);
        foreach ($companies as $comp) {
            $company_id = $comp[0];
        }

        $cadastrados = $controllerReport->obterDadosRelatorioGerenciaSenior($company_id);
        foreach ($cadastrados as $cad) {

            if ($cad['idc'] < 0.8) {
                $corIdc = "#FF9FA5";
            } elseif ($cad['idc'] < 1) {
                $corIdc = "#FFFFAE";
            } elseif ($cad['idc'] > 1) {
                $corIdc = "#B7FFB7";
            }
            if ($cad['idp'] < 0.8) {
                $corIdp = "#FF9FA5";
            } elseif ($cad['idp'] < 1) {
                $corIdp = "#FFFFAE";
            } elseif ($cad['idp'] > 1) {
                $corIdp = "#B7FFB7";
            }
            ?>										
            <tr>
                <td  align="center"><?php echo number_format($cad[percentual], 2, ',', '.'); ?></td>
                <td  align="center"><?php echo $cad[tamanho]; ?> </td>
                <td bgcolor="<?php echo $corIdc; ?>" align="center" ><?php echo number_format($cad[idc], 2, ',', '.'); ?> </td>
                <td bgcolor="<?php echo $corIdp; ?>" align="center" ><?php echo number_format($cad[idp], 2, ',', '.'); ?> </td>	

                <td align="center" ><?php echo number_format($cad[vp], 2, ',', '.'); ?> </td>
                <td align="center" ><?php echo number_format($cad[va], 2, ',', '.'); ?> </td>
                <td align="center" ><?php echo number_format($cad[cr], 2, ',', '.'); ?> </td>
                <td align="center"><?php echo $cad[baseline]; ?> </td>								

            <input type="hidden" name="percentual" value="<?php echo $cad[percentual]; ?>"   />
            <input type="hidden" name="tamanho" value="<?php echo $cad[tamanho]; ?>"   />
            <input type="hidden" name="idc" value="<?php echo $cad[idc]; ?>"   />
            <input type="hidden" name="idp" value="<?php echo $cad[idp]; ?>"   />
            <input type="hidden" name="va" value="<?php echo $cad[va]; ?>"   />
            <input type="hidden" name="vp" value="<?php echo $cad[vp]; ?>"   />
            <input type="hidden" name="cr" value="<?php echo $cad[cr]; ?>"   />
            <input type="hidden" name="baseline" value="<?php echo $cad[baseline]; ?>"   />
            </tr>				
        <?php } ?>	
        <tr>
            <td align="center">
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

    <br /><br />
    <!--************************** TAB  ***************************************** -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0" summary="tabbed view">  
        <tbody> 
            <tr> <!-- tabs-->     
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

                        <p align="left" style="width:85%"> 
                            <input type="button" class="button" value="<?php echo $AppUI->_('LBL_CADASTRAR'); ?>"  onclick="popNovaPendencia()"  />
                        </p>   


                        <table id="tbl_pendencias" class="std"  width="70%" align="center" style="border-radius:5px">	   
                            <tr>
                                <th align="center"  style="width:25%"><?php echo $AppUI->_('LBL_ACAO_CORRETIVA'); ?></th>
                                <th align="center" style="width:15%"><?php echo $AppUI->_('LBL_RESPONSAVEL'); ?> </th>
                                <th align="center" style="width:20%"><?php echo $AppUI->_('LBL_PRAZO'); ?> </th>
                                <th align="center" style="width:12%"><?php echo $AppUI->_('LBL_STATUS'); ?> </th> 
                                <th   style="width:3%">&nbsp; </th> 
                            </tr>		
                            <tr id="1000">
                                <td ></td> <td ></td> <td ></td> <td ></td> <td ></td>
                            </tr>         
                        </table>
                    </div>  <!--pendencias_ata-->
                </td>
            </tr>
        </tbody>
    </table>
    <script>
        function alteraTab_0(){	
            document.getElementById("pendencias_ata").style.display = "block";	
            document.getElementById('toptab_0').style.background = "url(style/default/images/tabSelectedBg.png)";
            document.getElementById("pendencias_anteriores").style.display = "none";	
            document.getElementById('toptab_1').style.background = "url(style/default/images/tabBg.png)";	
        }	
    </script>   
</form>
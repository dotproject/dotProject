<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_projects_responsability.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$project_id = dPgetParam($_GET, 'project_id', 0);
$controllerResp = new ControllerProjectsResponsability();
$controllerUtil = new ControllerUtil();
?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>

<!-- ***************** LISTA CADASTRADOS  ****************************-->	
<br/>
<table class="tbl" id="tb_resp" width="95%" align="center">	
    <caption> <b> <?php echo $AppUI->_('LBL_MATRIZ'); ?> </b></caption>
    <tr>
        <th style="width:40%"><?php echo $AppUI->_('LBL_MATRIZ'); ?></th>
        <th style="width:10%"><?php echo $AppUI->_('LBL_CONSULTADO'); ?></th>
        <th style="width:10%"><?php echo $AppUI->_('LBL_EXECUTA'); ?></th>
        <th style="width:10%"><?php echo $AppUI->_('LBL_APOIA'); ?> </th>
        <th style="width:10%"><?php echo $AppUI->_('LBL_APROVA'); ?> </th>
        <th style="width:5%">&nbsp;</th>
        <th style="width:5%">&nbsp;</th>	
    </tr>		
    <?php
    $cadastrados = $controllerResp->getRecords($project_id);
    foreach ($cadastrados as $cad) {
        ?>										
        <tr>
            <td><?php echo $cad[1]; ?> </td>
            <td> <?php echo $controllerUtil->getUsername($cad[2]); ?> </td>
            <td> <?php echo $controllerUtil->getUsername($cad[3]); ?> </td>
            <td> <?php echo $controllerUtil->getUsername($cad[4]); ?> </td>
            <td> <?php echo $controllerUtil->getUsername($cad[5]); ?> </td>
            <td align="center">
                <form name="form_update" method="post" action="" enctype="multipart/form-data" >
                    <?php $id = $cad[0]; ?>                          
                    <input  type="hidden" name="acao" value="update"  />
                    <input  type="hidden" name="responsibility_id" value="<?php echo $id; ?>"  />
                    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
                    <input  type="image" alt="./images/icons/pencil.gif" src="./images/icons/pencil.gif" title="Editar" name="editar" value="editar" onclick="updateRow();"  />   
                </form>
            </td>                        
            <td align="center">
                <form name="form_delete" method="post" action="?m=monitoringandcontrol&a=do_respons_aed&project_id=<?php echo $project_id; ?>" enctype="multipart/form-data" >
                    <input name="dosql" type="hidden" value="do_respons_aed" />	                              
                    <input  type="hidden" name="acao" value="delete"  />
                    <input  type="hidden" name="responsibility_id" value="<?php echo $cad[0]; ?>"  />
                    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
                    <input  type="image" alt="./images/icons/stock_delete-16.png" src="./images/icons/stock_delete-16.png" title="Deletar" name="deletar" value="deletar" onclick="deleteRow(excluir);"  />
                </form>

            </td>

        </tr>						
    <?php } ?>	
</table>	


<!-- ***************** UPDATE  ****************************-->		

<?php
if (isset($_POST['acao']) && $_POST['acao'] == 'update') {
    $id = $_POST['responsibility_id'];
    ?>		

    <form action="?m=monitoringandcontrol&a=do_respons_aed&project_id=<?php echo $project_id; ?>" method="post" name="form_updateRow" id="form_updateRow" enctype="multipart/form-data">

        <input name="dosql" type="hidden" value="do_respons_aed" />		
        <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
        <input  type="hidden" name="responsibility_id" value="<?php echo $id; ?>"  />
        <input  type="hidden" name="acao" value="updateRow"  />
        <br/>
        <table width="70%" align="center">
            <tr>
                <td>&nbsp;            

                </td>
            </tr>
        </table>	

        <table class="std" id="tb_updateRow" width="70%" align="center" style="border-radius:6px">	

            <tr>
                <th style="width:40%"><?php echo $AppUI->_('LBL_MATRIZ'); ?></th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_CONSULTADO'); ?></th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_EXECUTA'); ?></th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_APOIA'); ?> </th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_APROVA'); ?> </th>
                <th style="width:10%">&nbsp;</th>
            </tr>		
            <tr>
                <?php
                $list = array();
                $list = $controllerUtil->getUsers();
                $k = 0;
                $records = $controllerResp->getRecordsById($id);
                foreach ($records as $rec) {
                    $k++;
                }
                ?> 

                <td>          
                    <input type="text" name="description[]" id="description" size="32" value="<?php echo $rec[1]; ?>" />
                    <input type="hidden" name="index" value="0" />                  
                </td>
                <td>	                 
                    <select name="consultation[]" size="1" id="consultation"> 		
                        <option value="0">Selecione...</option>
                        <?php
                        $i = 0;
                        foreach ($list as $row) {
                            if ($rec[2] == $row[0]) {
                                echo "<option value=' $rec[2] ' selected>" . $controllerUtil->getUsername($rec[2]) . "</option>";
                            } else {
                                echo "<option value=' $row[0]'>$row[1]</option>";
                            }
                            $i++;
                        }
                        ?>          
                    </select>
                </td>
                <td>  
                    <select name="execut[]" size="1" id="execut" >         		
                        <option value="0">Selecione...</option>
                        <?php
                        $j = 0;
                        foreach ($list as $row) {
                            if ($rec[3] == $row[0]) {
                                echo "<option value=' $rec[3] ' selected='selected'>" . $controllerUtil->getUsername($rec[3]) . "</option>";
                            } else {
                                echo "<option value=' $row[0]'>$row[1]</option>";
                            }
                            $j++;
                        }
                        ?>                       
                    </select>
                </td>               

                <td>  
                    <select name="support[]" size="1" id="support">
                        <option value="0">Selecione...</option>  
                        <?php
                        $m = 0;
                        foreach ($list as $row) {
                            if ($rec[4] == $row[0]) {
                                echo "<option value=' $rec[4] ' selected>" . $controllerUtil->getUsername($rec[4]) . "</option>";
                            } else {
                                echo "<option value=' $row[0]'>$row[1]</option>";
                            }
                            $m++;
                        }
                        ?>                       
                    </select>

                </td>      
                <td>  
                    <select name="approve[]" size="1" id="approve">
                        <option value="0">Selecione...</option>
                        <?php
                        $n = 0;
                        foreach ($list as $row) {
                            if ($rec[5] == $row[0]) {
                                echo "<option value=' $rec[5] ' selected>" . $controllerUtil->getUsername($rec[5]) . "</option>";
                            } else {
                                echo "<option value=' $row[0]'>$row[1]</option>";
                            }
                            $n++;
                        }
                        ?>                       
                    </select>

                </td>  
            </tr>        
        </table>
        <table width="70%" align="center">
            <tr>
                <td>				
                    <input type="button" class="button" onClick="updateRecords();" value="Atualizar" />
                </td>
            </tr>
        </table>
    </form>

<?php } else { ?>

    <!-- ***************** ADD CADASTRO  ****************************-->	
    <form action="?m=monitoringandcontrol&a=do_respons_aed&project_id=<?php echo $project_id; ?>" method="post" name="form_resp" id="form_resp" enctype="multipart/form-data">
        <input name="dosql" type="hidden" value="do_respons_aed" />		
        <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
        <input  type="hidden" name="acao" value="insert"  />
        <br/>
        <table width="95%" align="center">
            <tr>
                <td>            
                    <input type="button" class="button" value="Add" onclick="addRow('');">
                </td>
            </tr>
        </table>	

        <table class="tbl" id="tb_row" width="95%" align="center">	

            <tr>
                <th style="width:40%"><?php echo $AppUI->_('LBL_MATRIZ'); ?></th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_CONSULTADO'); ?></th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_EXECUTA'); ?></th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_APOIA'); ?> </th>
                <th style="width:10%"><?php echo $AppUI->_('LBL_APROVA'); ?> </th>
            </tr>		
            <tr id="1000">
                <?php
                echo "<script>
                        globalUserNameList= new Array();
                        globalUserIdList= new Array();
                     </script>";

                $list = array();
                $list = $controllerUtil->getUsers();
                $i = 0;
                foreach ($list as $row) {
                    echo "
                        <script>	
                                 globalUserNameList[$i] = '$row[1]';
                                 globalUserIdList[$i] = '$row[0]';
                        </script>";
                    $i++;
                }
                ?>
                <td>               
                    <input type="text" name="description[]" id="description" size="95%" maxlength="45" />
                    <input type="hidden" name="index[]" value="0" />                  
                </td>
                <td>  
                    <select name="consultation[]" size="1" id="consultation"></select>
                    <script>	
                        var sel = document.form_resp.consultation.options;
                        sel[0] = new Option("Selecione...","0")
                        var opt ; 
                        var i ;
                        for(i=0; i<globalUserIdList.length; i++){
                            opt=  new Option(globalUserNameList[i],globalUserIdList[i])
                            sel[i+1]=opt;
                        }
                    </script>				    
                </td>
                <td>  
                    <select name="execut[]" size="1" id="execut"></select>
                    <script>	
                        var sel = document.form_resp.execut.options;
                        sel[0] = new Option("Selecione...","0")
                        var opt ; 
                        var i ;
                        for(i=0; i<globalUserIdList.length; i++){
                            opt=  new Option(globalUserNameList[i],globalUserIdList[i])
                            sel[i+1]=opt;
                        }
                    </script>				    
                </td>            
                <td>  
                    <select name="support[]" size="1" id="support"></select>
                    <script>	
                        var sel = document.form_resp.support.options;
                        sel[0] = new Option("Selecione...","0")
                        var opt ; 
                        var i ;
                        for(i=0; i<globalUserIdList.length; i++){
                            opt=  new Option(globalUserNameList[i],globalUserIdList[i])
                            sel[i+1]=opt;
                        }
                    </script>				    
                </td>      
                <td>  
                    <select name="approve[]" size="1" id="approve"></select>
                    <script>	
                        var sel = document.form_resp.approve.options;
                        sel[0] = new Option("Selecione...","0")
                        var opt ; 
                        var i ;
                        for(i=0; i<globalUserIdList.length; i++){
                            opt=  new Option(globalUserNameList[i],globalUserIdList[i])
                            sel[i+1]=opt;
                        }
                    </script>				    
                </td>  
            </tr>        
        </table>
        <table width="95%" align="center">
            <tr>
                <td>				
                    <input type="button" class="button" onClick="saveRecords();" value="Save" />
                </td>
            </tr>
        </table>
    </form>
<?php } ?>


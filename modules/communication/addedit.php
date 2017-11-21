<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

include "functions.php";

############################################################################
// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

// setup the title block

$titleBlock = new CTitleBlock("LBL_COMMUNICATIONS", 'applet3-48.png', $m, "$m.$a");
$titleBlock->addCrumb("index.php?a=view&m=projects&project_id=". $_GET["project_id"]."&tab=1&targetScreenOnProject=/modules/communication/index_project.php", "LBL_LIST");
if ($canDelete && $communication_id) {
    $titleBlock->addCrumbDelete("LBL_DELETE", $canDelete, $msg);
}
$titleBlock->show();
?>

<script language="javascript">
        
    function submitIt(){
        var f = document.uploadFrm;
        f.project.value = document.uploadFrm.project.value;
        f.frequency.value = document.uploadFrm.frequency.value;
        f.channel.value = document.uploadFrm.channel.value;
        f.responsible.value = document.uploadFrm.responsible.value;
        //alert(f.responsible.value);
        f.submit();
    }
        
    function delIt() {
        if (confirm("<?php echo $AppUI->_("LBL_ANSWER_DELETE", UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }
        
    function AddReceptor(a, b) {
        var get = GetForm();
        window.location = ('?m=communication&a=addedit&communication_id='+b+'&radd='+a+get)
    }
    function DelReceptor(a, b) {            
        var get = GetForm();
        window.location = ('?m=communication&a=addedit&communication_id='+b+'&rdel='+a+get)
    }
    function AddIssuing(a, b) {            
        var get = GetForm();
        window.location = ('?m=communication&a=addedit&communication_id='+b+'&raddi='+a+get)
    }
    function DelIssuing(a, b) {
        var get = GetForm();
        window.location = ('?m=communication&a=addedit&communication_id='+b+'&rdeli='+a+get)
    }        
        
    function GetForm() {
        var get = '';
        var project = document.uploadFrm.project.value;
        var title = document.uploadFrm.communication_title.value;            
        var communication = document.uploadFrm.communication_information.value;
        var channel = document.uploadFrm.channel.value;
        var frequency = document.uploadFrm.frequency.value;
        var restrictions = document.uploadFrm.communication_restrictions.value;
        var communication_date = document.uploadFrm.communication_date.value;
        var responsible = document.uploadFrm.responsible.value;
        if (project != '') {
            get += '&project='+project;
        }
        if (title != '') {
            get += '&title='+title;
        }
        if (communication != '') {
            get += '&communication='+communication;
        }
        if (channel != '') {
            get += '&channel='+channel;
        }
        if (frequency != '') {
            get += '&frequency='+frequency;
        }
        if (restrictions != '') {
            get += '&restrictions='+restrictions;
        }
        if (communication_date != '') {
            get += '&communication_date='+communication_date;
        }
         if (responsible != '') {
            get += '&responsible='+responsible;
        }
        return get;
    }
        
    function setdate(id, b){
        var get = GetForm();                
        window.location = '?m=communication&a=addedit&communication_id='+b+'&date='+id+get;
                   
    }
        
</script>


<table width="100%" border="0" cellpadding="3" cellspacing="3" class="std">
    <form name="uploadFrm" action="?m=communication" method="post">
        <input type="hidden" name="dosql" value="do_communication_aed" />
        <input type="hidden" name="del" value="0" />      
        <input type="hidden" name="project" value="<?php echo $_GET["project_id"]!=""?$_GET["project_id"]:$_GET["project"]; ?>" />  
        <input type="hidden" name="project_id" value="<?php echo $_GET["project_id"]!=""?$_GET["project_id"]:$_GET["project"]; ?>" />  
        
        <input type="hidden" name="communication_id" value="<?php echo $communication_id; ?>" />
        <tr>
            <td width="100%" valign="top" align="left" colspan="2">
                <table cellspacing="1" cellpadding="2" width="60%" charset=UTF-8>
                    <!--
                    <tr><td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_PROJECT"); ?>:</td>
                        <td>
                            
                            <select id="project" name="project" style="min-width:150px">
                            <?php
                            $projectId=isset($_GET['project'])?$_GET['project']:@$obj->communication_project_id;
                            foreach ($projects as $registro) {
                                if (isset($_GET['project'])) {
                                    $value = $_GET['project'];
                                } else {
                                    $value = @$obj->communication_project_id;
                                }
                                echo '<option value="' . $registro['project_id'] . '" ' . ($registro['project_id'] == $value ? 'selected="selected"' : '') . '>'. $registro['project_name'] . '</option>';
                            }
                            ;
                            ?>
                            </select>
                           
                        </td>
                    </tr>
                     -->
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_TITLE"); ?>:</td>
                        <td><input type="text" maxlength="45" size="80" name="communication_title" value="<?php echo (isset($_GET['title']) ? $_GET['title'] : @$obj->communication_title); ?>" /></td>                        
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_COMMUNICATION"); ?>:</td>
                        <td align="left"><textarea name="communication_information" cols="100" rows="3" class="textarea"><?php echo (isset($_GET['communication']) ? $_GET['communication'] : @$obj->communication_information); ?></textarea></td>
                    </tr>  
                    <tr>
                        <td style="vertical-align: top"><?php echo $AppUI->_("LBL_ISSUING") ?></td>
                        <td colspan="2">
                            <select id="issuing" name="issuing" style="min-width:150px" onchange="AddIssuing(this.value,<?php echo $communication_id ?>)">
                                <option value=""><?php echo $AppUI->_("LBL_ADD") ?></option>
                                <?php
                                foreach ($rlista as $registro) {
                                    echo '<option value="' . $registro['contact_id'] . '">' . $registro['contact_first_name'] . ' ' . $registro['contact_last_name'] . '</option>';
                                }
                                ?> 
                            </select>
                            <?php
                            if (sizeof($remitters) > 0) {
                                ?>                   
                                <table>
                                    <?php
                                    foreach ($remitters as $registro) {
                                        ?>
                                        <tr>
                                            <td> 
                                                <?php echo $registro['contact_first_name'] . ' ' . $registro['contact_last_name'] ?>  
                                            </td>
                                            <td>

                                                <input type='button' value='x' style='color: #aa0000; font-weight: bold' onclick='DelIssuing( <?php echo $registro['communication_issuing_id'] ?>,<?php echo $communication_id ?>)' />

                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>

                                <?php
                            }
                            ?>   
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            <?php echo $AppUI->_("LBL_RECEPTOR"); ?>
                        </td>
                        <td>
                            <select id="receptor" name="receptor" style="min-width:150px" onchange="AddReceptor(this.value, <?php echo $communication_id ?>)">
                                <option value=""><?php echo $AppUI->_("LBL_ADD")?></option>
                                <?php
                                    foreach ($rlista as $registro) {
                                        echo '<option value="' . $registro['contact_id'] . '">' . $registro['contact_first_name'] . ' ' . $registro['contact_last_name'] . '</option>';
                                        
                                    }
                                ?>
                            </select>
                            <?php
                            if(count($rreceptors)>0){   
                            ?>
                            <table>
                               <?php foreach ($rreceptors as $registro) { ?>
                                <tr>
                                    <td>
                                        <?php echo $registro['contact_first_name'] . ' ' . $registro['contact_last_name']  ?>
                                    </td>
                                    <td>
                                        <input type="button" value="x" style="color: #aa0000; font-weight: bold" onclick="DelReceptor(<?php echo $registro['communication_receptor_id'] ?> , <?php echo $communication_id ?>)" />
                                    </td>
                                </tr>
                               <?php } ?>
                            </table>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                  <tr> 
                  <td><?php echo $AppUI->_("LBL_CHANNEL")?>: </td>
                  <td><select id="channel" name="channel" style="min-width:150px">
                      <option value=""><?php echo $AppUI->_("LBL_SELECT")?></option>
                  <?php
                  foreach ($channels as $registro) {
                    if (isset($_GET['channel'])) {
                        $value = $_GET['channel'];
                    } else {
                        $value = $obj->communication_channel_id;
                    }
                    echo '<option value="' . $registro['communication_channel_id'] . '" ' . ($registro['communication_channel_id'] == $value ? 'selected="selected"' : '') . '>' . $registro['communication_channel'] . '</option>';}
                  ?></select></td>
                  </tr>
                  <tr><td>Frequência: </td>
                      <td><select id="frequency" name="frequency" style="min-width:150px" onchange="setdate(this.value, <?php echo $communication_id ?>)">
                          <option value=""><?php echo $AppUI->_("LBL_SELECT")?></option>
                  <?php
                  if (isset($_GET['frequency'])) {
                      $value = $_GET['frequency'];
                  } else {
                      $value = @$obj->communication_frequency_id;
                  }
                  foreach ($frequency as $registro) {
                    echo '<option value="' . $registro['communication_frequency_id'] . '" ' . ($registro['communication_frequency_id'] == $value ? 'selected="selected"' : '') . '>' . $registro['communication_frequency'] . '</option>';
                  }
                  ?></select>
                    <span style="margin-left:15px; <?php echo ($showdate ? '' : 'display:none;'); ?>"><?php echo $AppUI->_("LBL_DATE")?>: </span>
                    <input type="text" style="<?php echo ($showdate ? '' : 'display:none;'); ?> margin-left: 10px;" value="<?php echo ($communication_id == 0 ? $_GET['communication_date'] : @$obj->communication_date);  ?>" name="communication_date">
                    </td>
                 </tr>
                 <tr><td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_RESTRICTIONS");?>: </td>
                     <td align="left"><textarea name="communication_restrictions" cols="100" rows="3" class="textarea"><?php echo ($communication_id == 0 ? $_GET['restrictions'] : @$obj->communication_restrictions); ?></textarea></td>
                 </tr>
                 <td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_RESPONSIBLE");?>: </td> 
                     <td><select id="responsible" name="responsible" style="min-width:150px">
                         <option value=""><?php echo $AppUI->_("LBL_SELECT")?></option>
                  <?php
                  foreach ($rlista as $registro) {
                    if (isset($_GET['responsible'])) {
                        $value = $_GET['responsible'];
                    } else {
                        $value = $obj->communication_responsible_authorization;
                    }
                    echo '<option value="' . $registro['contact_id'] . '" ' . ($registro['contact_id'] == $value ? 'selected="selected"' : '') . '>' . $registro['contact_first_name'] . ' ' . $registro['contact_last_name'] . '</option>';}
                  ?></select></td>
                  <tr><td><span style="margin-left:0px"><?php echo $AppUI->_("LBL_SEND")?></span></td></tr>
                
            </table>
        </td>
    </tr>
    <tr>
        <td width='70%' style='line-break: strict;word-wrap: break-word'>
            <span style='color:#F00'>*</span> <?php echo $AppUI->_("LBL_EMISSOR_RECIPTORS"); ?>
        </td>
        <td  align="right" width='30%'>
            <input type="button" class="button" value="<?php echo ucfirst($AppUI->_("LBL_SUBMIT")); ?>" onclick="submitIt()" />
            <script> var targetScreenOnProject="/modules/communication/index_project.php";</script>
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>         
                
        </td>
    </tr>
   </form>
</table>

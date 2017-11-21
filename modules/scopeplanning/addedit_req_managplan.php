<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

//load requirements management plan's id
$req_managplan_id = intval(dPgetParam($_GET, 'managplanid', 0));//'id' is sent by vw_req_managplan.php on clicking 'Edit'
$project_id = intval(dPgetParam($_GET, 'projectid', 0));

$titleBlock = new CTitleBlock((($req_managplan_id) ? 'LBL_SP_EDIT_REQMANAGPLAN' : 'LBL_SP_CREATE_REQMANAGPLAN'),
                'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";

$canDelete = getPermission($m, 'delete', $req_managplan_id);
if ($canDelete && $req_managplan_id > 0) {
    $titleBlock->addCrumbDelete('LBL_SP_DELETE', $canDelete, $msg);
}
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*');//select
$q->addTable('scope_requirements_managplan');//from
$q->addWhere('req_managplan_id = ' . $req_managplan_id);//where

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CReqManagPlan();
$canDelete = $obj->canDelete($msg, $req_managplan_id);

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $req_managplan_id > 0) {
	$AppUI->setMsg('LBL_SP_REQMANAGPLAN');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
}

$projects = new DBQuery();
if ($project_id == null) {
    $projects->addQuery('project_id, project_name');
    $projects->addTable('projects');
    $projects = $projects->loadHashList();
} else {
    $projects->addQuery('project_id, project_name');
    $projects->addTable('projects', 'p');
    $projects->addWhere('project_id = ' . $project_id);
    $projects->addOrder('project_id');
    $projects = $projects->loadHashList();
}

?>

<script language="javascript">
    function submitIt() {                
        var f = document.uploadFrm;     
        var msg = '';
        var msg1 = "<?php echo $AppUI->_('LBL_SP_EMPTYFIELDS');?>";
        var msg2 = "<?php echo $AppUI->_('LBL_SP_ALLFIELDSEMPTY'); ?>";
        var msg3 = "<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_REQMANAGPLAN'); ?>";
        
        var a = f.req_managplan_collect_descr.value.length;
        var b = f.req_managplan_reqcategories.value.length;
        var c = f.req_managplan_trac_descr.value.length;
        var d = f.req_managplan_config_descr.value.length;
        var e = f.req_managplan_verif_descr.value.length;
        var g = f.req_managplan_reqprioritization.value.length;
        
          
        if(a<1 || b<1 || c<1 || d<1 || e<1 || g<1){           
            msg = msg1 + msg3;           
        }        
        if(a<1 && b<1 && c<1 && d<1 && e<1 && g<1){                       
            msg = msg2 + msg3
        }        
                
        if(msg.length<1){
            f.submit();
        } else {
            if (confirm(msg)) {   
                f.submit();
            }
        }  
    }  
    
    function delIt() {
        if (confirm("<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_DELETION', UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }
</script>

<table align="left" border="0" cellpadding="10" cellspacing="3" style="width: 100%" class="std" name="threads" charset=UTF-8>
    <form name="uploadFrm" action="?m=scopeplanning" method="post">
        <input type="hidden" name="dosql" value="do_scopeplanning_req_managplan_aed" />	
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="req_managplan_id" value="<?php echo $req_managplan_id; ?>" />
        
        <tr><td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_SP_PROJECT"); ?>:</td>
            <td>
                <?php echo arraySelect($projects, 'project_id', 'size="1" class="text"', 
                        (@$obj->project_id ? dPformSafe(@$obj->project_id) : $project_id));
                ?>
            </td>
        </tr>
        <tr>
            <!-- As tags "textarea" devem abrir e fechar na mesma linha para que nao aparecam espacos em branco no inicio da area de texto <textarea>blablabla</textarea> -->
            <td valign="top"><?php echo $AppUI->_('LBL_SP_REQCOLLECT'); ?>:</td>
            <td><textarea name="req_managplan_collect_descr" cols="100" rows="10" class="textarea" ><?php echo dPformSafe(@$obj->req_managplan_collect_descr);?></textarea>                                           
            </td>                                                
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_CATEGORY'); ?>:</td>
            <td><textarea cols="100" rows="10" name="req_managplan_reqcategories" class="textarea"><?php echo dPformSafe(@$obj->req_managplan_reqcategories);?></textarea>
            </td>            
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_PRIORITY'); ?>:</td>
            <td><textarea cols="100" rows="10" name="req_managplan_reqprioritization" class="textarea"><?php echo dPformSafe(@$obj->req_managplan_reqprioritization);?></textarea>
            </td>            
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_TRACEABILITY'); ?>:</td>
            <td><textarea cols="100" rows="10" name="req_managplan_trac_descr" class="textarea"><?php echo dPformSafe(@$obj->req_managplan_trac_descr);?></textarea>                                          
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_CONFIGMANAG'); ?>:</td>
            <td><textarea cols="100" rows="10" name="req_managplan_config_descr" class="textarea"><?php echo dPformSafe(@$obj->req_managplan_config_descr);?></textarea>         
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_VERIFICATION'); ?>:</td>
            <td><textarea cols="100" rows="10" name="req_managplan_verif_descr" class="textarea"><?php echo dPformSafe(@$obj->req_managplan_verif_descr);?></textarea>                          
            </td>
        </tr>
        <tr>
            <td><input name="btn_cancel" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_CANCEL'); ?>"
                       onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
            <td style="text-align: right"><input name="btn_submit" class="button" type="button" 
                                                 value="<?php echo $AppUI->_('LBL_SP_SAVE'); ?>" onclick="submitIt()"/></td>
        </tr>
    </form>
</table>
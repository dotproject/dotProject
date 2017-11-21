<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

//load scope statement id
$scope_id = intval(dPgetParam($_GET, 'scopeid', 0)); 
$project_id = intval(dPgetParam($_GET, 'projectid', 0));

$titleBlock = new CTitleBlock((($scope_id) ? 'LBL_SP_EDIT_SCOPESTAT' : 'LBL_SP_CREATE_SCOPESTAT'),
                'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";

$canDelete = getPermission($m, 'delete', $scope_id);
if ($canDelete && $scope_id > 0) {
    $titleBlock->addCrumbDelete('LBL_SP_DELETE', $canDelete, $msg);
}
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*'); //select
$q->addTable('scope_statement'); //from
$q->addWhere('scope_id = ' . $scope_id); //where

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CScopeStatement();
$canDelete = $obj->canDelete($msg, $scope_id);

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $scope_id > 0) {
    $AppUI->setMsg('LBL_SP_SCOPESTAT');
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
        
        var msg1 = "<?php echo $AppUI->_('LBL_SP_EMPTYFIELDS')?>";
        var msg2 = "<?php echo $AppUI->_('LBL_SP_ALLFIELDSEMPTY')?>";
        var msg3 = "<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_SCOPESTAT')?>";
        
        var b = f.scope_description.value.length;
        var c = f.scope_acceptancecriteria.value.length;
        var d = f.scope_deliverables.value.length;
        var e = f.scope_exclusions.value.length;
        var g = f.scope_constraints.value.length;
        var h = f.scope_assumptions.value.length;
          
        if(b<1 || c<1 || d<1 || e<1 || g<1 || h<1){                          
            msg = msg1 + msg3;
        }        
        if(b<1 && c<1 && d<1 && e<1 && g<1 && h<1){                       
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
        <input type="hidden" name="dosql" value="do_scopeplanning_scope_statement_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="scope_id" value="<?php echo $scope_id; ?>"/>

        <tr><td align="left" nowrap="nowrap"><?php echo $AppUI->_("LBL_SP_PROJECT"); ?>:</td>
            <td>
                <?php echo arraySelect($projects, 'project_id', 'size="1" class="text"', 
                        (@$obj->project_id ? dPformSafe(@$obj->project_id) : $project_id));
                ?>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_SCOPE'); ?>:</td>
            <td><textarea cols="100" name="scope_description" rows="10" class="textarea"><?php echo dPformSafe(@$obj->scope_description); ?></textarea>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_ACCEPTANCECRIT'); ?>:</td>
            <td><textarea cols="100" name="scope_acceptancecriteria" rows="10" class="textarea"><?php echo dPformSafe(@$obj->scope_acceptancecriteria); ?></textarea>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_DELIVERABLES'); ?>:</td>
            <td><textarea cols="100" name="scope_deliverables" rows="10" class="textarea"><?php echo dPformSafe(@$obj->scope_deliverables); ?></textarea>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_EXCLUSIONS'); ?>:</td>
            <td><textarea cols="100" name="scope_exclusions" rows="10" class="textarea"><?php echo dPformSafe(@$obj->scope_exclusions); ?></textarea>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_RESTRICTIONS'); ?>:</td>
            <td><textarea cols="100" name="scope_constraints" rows="10" class="textarea"><?php echo dPformSafe(@$obj->scope_constraints); ?></textarea>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_ASSUMPTIONS'); ?>:</td>
            <td><textarea cols="100" name="scope_assumptions" rows="10" class="textarea"><?php echo dPformSafe(@$obj->scope_assumptions); ?></textarea>
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



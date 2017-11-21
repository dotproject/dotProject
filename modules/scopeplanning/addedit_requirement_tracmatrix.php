<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$req_id = intval(dPgetParam($_GET, 'reqid', 0)); 
$project_id = intval(dPgetParam($_GET, 'projid', 0));

$titleBlock = new CTitleBlock('LBL_SP_EDIT_REQTRAC', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('scope_requirements', 's');
$q->addWhere('req_id =' . $req_id);
$q->addJoin('projects', 'p', 'p.project_id = s.project_id');
$q->addJoin('scope_requirement_categories', 'c', 's.req_categ_prefix_id = c.req_categ_prefix_id');
$q->addJoin('project_eap_items', 'e', 'e.id = s.eapitem_id');
$q->addOrder('p.project_id');

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CScopeRequirements();
$canDelete = $obj->canDelete($msg, $req_id);

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $req_id > 0) {
    $AppUI->setMsg('LBL_SP_REQTRAC');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

$eapitemsList = new DBQuery();
$eapitemsList->addQuery('id, number, item_name, sort_order');
$eapitemsList->addTable('project_eap_items', 'e');
$eapitemsList->addWhere('e.project_id = ' . $project_id);
$eapitemsList->addOrder('e.sort_order');
$eapitemsList = $eapitemsList->loadList();
?>

<script language="javascript">
    function submitIt() {
        var f = document.uploadFrm;
        f.submit();
    }
</script>

<table align="left" border="0" cellpadding="10" cellspacing="5" style="width: 100%" class="std" name="threads" charset=UTF-8>
    <form name="uploadFrm" action="?m=scopeplanning" method="post">
        <input type="hidden" name="dosql" value="do_scopeplanning_requirement_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="req_id" value="<?php echo $req_id; ?>"/>          
        
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_PROJECT'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->project_name); ?></td>
    </tr>                
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_REQ'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_idname); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_DESCRIPTION'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_description); ?></td>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_SOURCE'); ?>:</td>
        <td><?php echo dPformSafe(@$obj->req_source); ?></td>
    </tr>       
    <tr>        
        <td valign="top"><?php echo $AppUI->_('LBL_SP_WBSITEM'); ?>:</td>
        <td><select name="eapitem_id" class="combo" style="width: 370px">
                <?php
                foreach ($eapitemsList as $reg) {
                    $value = @$obj->number;
                    echo '<option value="' . $reg['id'] . '" ' . ($reg['number'] == $value ? 'selected="selected"' : '') . '>' . $reg['number'] . ' ' . $reg['item_name'] . '</option>';
                }
                ?>                                        
            </select>
        </td>
    </tr>
    </tr>
    <tr>
        <td valign="top"><?php echo $AppUI->_('LBL_SP_TESTECASE'); ?>:</td>
        <td><textarea cols="50" name="req_testcase" class="std" rows="10"><?php echo dPformSafe(@$obj->req_testcase); ?></textarea></td>
    </tr>    
    <tr>
        <td><input name="btn_cancel" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_CANCEL'); ?>"
                   onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
        <td style="text-align: right"><input name="btn_submit" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_SAVE'); ?>" onclick="submitIt()"/></td>
    </tr>
    </form>
</table>
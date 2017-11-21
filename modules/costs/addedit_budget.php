<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$budget_id = intval(dPgetParam($_GET, 'budget_id', 0));
// check permissions for this record
$canEdit = getPermission($m, 'edit', $budget_id);
if (!(($canEdit && $budget_id) || ($canAuthor && !($budget_id)))) {
    $AppUI->redirect('m=public&a=access_denied');
}

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('budget');
$q->addWhere('budget_id = ' . $budget_id);
//$project_id = $q->loadList();
// check if this record has dependancies to prevent deletion
$msg = '';
// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && ($budget_id > 0)) {
    $AppUI->setMsg('Budget');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}
require_once (DP_BASE_DIR . '/modules/timeplanning/view/link_to_project.php');
// setup the title block
$ttl = $budget_id ? "Edit" : "Add";
$titleBlock = new CTitleBlock($ttl, 'costs.png', $m, "$m.$a");
$titleBlock->show();
?>
<script language="javascript">
    function submitIt() {
        
        var f = document.uploadFrm;
       
        var msg = '';        
        var foc=false;
        if (f.budget_reserve_management.value < 0) {
            msg += "\n<?php echo $AppUI->_("LBL_VALIDATION_MANAGEMENT_RESERVE", UI_OUTPUT_JS); ?>";
            if ((foc==false) && (navigator.userAgent.indexOf('MSIE')== -1)) {
                f.budget_reserve_management.focus();
                foc=true;
            }
        }
        
        if (msg.length < 1) {
            f.submit();
        } else {
            alert(msg);
        }
        
        
    }
    
    function delIt() {
        if (confirm("<?php echo $AppUI->_('Delete this budget?', UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }
    
    function budgetTotal(){
        var management = document.getElementById('budget_reserve_management').value; 
        var subtotal = <?php echo $obj->budget_sub_total ?>;
        var total = (management/100) * subtotal;
        total = total + subtotal;
        
        document.getElementById('budget_total').value = total; 
       document.getElementById('text_total').innerHTML = total; 
    }
</script>

<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />

<form name="uploadFrm" action="?m=costs" method="post">
    <input type="hidden" name="dosql" value="do_budget_aed" />
    <input type="hidden" name="project_id" value="<?php echo $_GET["project_id"]; ?>" />
    
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="budget_id" value="<?php echo $budget_id; ?>" />
    <table width="100%" border="0" cellpadding="3" cellspacing="3" class="std" name="table_form" >
          <tr>
            <th colspan="2">
                <?php echo $AppUI->_("LBL_COST_MANAGEMENT_RESERVE",UI_OUTPUT_HTML); ?>
            </th>
        </tr>
        <tr>
            <td  class="td_label"><?php echo $AppUI->_("Management Reserve"); ?> (%)<span class="span_mandatory">*</span>:</td>
            <td>
                <input name="budget_reserve_management" id="budget_reserve_management" value="<?php echo dPformSafe($obj->budget_reserve_management); ?>" />
            </td>
        </tr>
        <tr>
            <td  class="td_label"><?php echo $AppUI->_('SubTotal'); ?>:</td>
            <td>
                <span id="text_subtotal"><?php echo dPformSafe($obj->budget_sub_total); ?></span>
                <input type="hidden" name="budget_sub_total" id="budget_sub_total" value="<?php echo dPformSafe($obj->budget_sub_total); ?>" />
            </td>
        </tr>
        <tr>
            <td  class="td_label"><?php echo $AppUI->_('Total Budget'); ?>&nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</td>
            <td>
                <span id="text_total"><?php echo dPformSafe($obj->budget_total); ?></span>
                <input type="hidden" name="budget_total" id="budget_total" value="<?php echo dPformSafe($obj->budget_total); ?>"  />
            </td>
        </tr>

        <tr>         
            <td align="right" colspan="2">
               <input type="button" class="button" value="<?php echo $AppUI->_("LBL_SUBMIT"); ?>" onclick="budgetTotal();submitIt();" />
               <script> var targetScreenOnProject="/modules/costs/view_budget.php";</script>
               <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
        </tr>
    </table>
</form>
<span class="span_mandatory">*</span> <?php echo $AppUI->_("Required Fields"); ?>



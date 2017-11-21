<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$initiating_id = $_GET["initiating_id"];
$obj = new CStakeholder();
$canDelete=false;
$initiating_stakeholder_id = intval(dPgetParam($_GET, 'initiating_stakeholder_id', 0));
if(isset($initiating_stakeholder_id)){
    require_once (DP_BASE_DIR . "/modules/contacts/contacts.class.php");
    $q = new DBQuery();
    $q->addQuery('*');
    $q->addTable('initiating_stakeholder');
    $q->addWhere('initiating_stakeholder_id = ' . $initiating_stakeholder_id);
    // check if this record has dependancies to prevent deletion
    $msg = '';
    $canDelete = $obj->canDelete($msg, $initiating_stakeholder_id);
}
// load the record data
if (!db_loadObject($q->prepare(), $obj) && $initiating_stakeholder_id > 0) {
    $AppUI->setMsg('Initiating');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}
if(!isset($initiating_id)){
  $initiating_id=  $obj->initiating_id;
} 
// setup the title block
$ttl = isset($initiating_stakeholder_id) ? "Edit" : "Add";
$titleBlock = new CTitleBlock($ttl, 'applet3-48.png', $m, "$m.$a");
//$titleBlock->addCrumb("?m=$m", "list of stakeholders");
$titleBlock->addCrumbDelete('delete stakeholder', $canDelete, $msg);
$titleBlock->show();
// option of select box
$opt = array('', $AppUI->_("LBL_PROJECT_STAKEHOLDER_HIGH"), $AppUI->_("LBL_PROJECT_STAKEHOLDER_LOW"));
?>

<script language="javascript">
    function submitIt() {
        var f = document.uploadFrm;
        f.strategy.value = document.uploadFrm.stakeholder_strategy.value;
        var firstName=document.getElementById("first_name").value;
        var lastName=document.getElementById("last_name").value;
        if(firstName=="" || lastName==""){
            window.alert("<?php echo $AppUI->_('VALIDATE_NEW_USER_FORM'); ?>.");
        }else{
            f.submit();
        }
    }
    function delIt() {
        if (confirm("<?php echo $AppUI->_('stakeholdersDelete', UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }

    function updateStrategy() {
        if (document.uploadFrm.stakeholder_power.value == '0' || document.uploadFrm.stakeholder_interest.value == '0'){
            document.uploadFrm.stakeholder_strategy.value='';
        }
	
        if (document.uploadFrm.stakeholder_power.value == '1'){
            if (document.uploadFrm.stakeholder_interest.value == '2'){
                document.uploadFrm.stakeholder_strategy.value='<?php echo $AppUI->_("LBL_STAKEHOLDER_MAINTAIN_SATISFIED",UI_OUTPUT_JS) ?>';
            }
            if (document.uploadFrm.stakeholder_interest.value == '1'){
                document.uploadFrm.stakeholder_strategy.value='<?php echo $AppUI->_("LBL_STAKEHOLDER_CLOSELY_MANAGE",UI_OUTPUT_JS) ?>';
            }
        }

        if (document.uploadFrm.stakeholder_power.value == '2'){
            if (document.uploadFrm.stakeholder_interest.value == '2'){
                document.uploadFrm.stakeholder_strategy.value='<?php echo $AppUI->_("LBL_STAKEHOLDER_MONITOR",UI_OUTPUT_JS) ?>';
            }
            if (document.uploadFrm.stakeholder_interest.value == '1'){ 
                document.uploadFrm.stakeholder_strategy.value='<?php echo $AppUI->_("LBL_STAKEHOLDER_KEEP_INFORMED",UI_OUTPUT_JS) ?>';
            }
        }
    }
    
</script>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form name="uploadFrm" action="?m=stakeholder" method="post">
    <input type="hidden" name="dosql" value="do_stakeholder_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="strategy" />
    <input type="hidden" name="contact_id" />
    <input type="hidden" name="initiating_stakeholder_id" value="<?php echo $initiating_stakeholder_id; ?>" />
    <input type="hidden" name="initiating_id" value="<?php echo $initiating_id; ?>" />
    <input type="hidden" name="project_id" value="<?php echo $_GET["project_id"]; ?>" />
       
    <?php
    $firstName = "";
    $lastName = "";
    if ($obj->contact_id > 0) {
        $contactObj = new CContact();
        $contactObj->load($obj->contact_id);
        $firstName = $contactObj->contact_first_name;
        $lastName = $contactObj->contact_last_name;
    }
    ?>
    <table width="100%" border="0" cellpadding="3" cellspacing="3" class="std" name="table_form">

        <tr>
            <th colspan="2" c align="center"><?php echo $AppUI->_('Stakeholder'); ?></th>
        </tr>
        <tr>
            <td class="td_label">
                <label><?php echo $AppUI->_("First Name"); ?>:</label>
            </td>
            <td>
                <input type="text" name="first_name" id="first_name" value="<?php echo $firstName ?>" maxlength="100"  />
            </td>
        <tr>
            <td class="td_label">
                <label><?php echo $AppUI->_("Last Name"); ?>:</label></td>
            <td>
                <input type="text" name="last_name" id="last_name" value="<?php echo $lastName ?>" maxlength="100"  />
            </td>
        </tr>
        
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Responsibilities'); ?>:</td>
            <td>
                <textarea name="stakeholder_responsibility" cols="50" rows="3" style="wrap:virtual;" class="textarea"><?php echo dPformSafe(@$obj->stakeholder_responsibility); ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Power'); ?>:</td>
            <td align="left">
                <?php echo arraySelect($opt, 'stakeholder_power', 'size="1" class="text" onchange="javascript:updateStrategy();"', ((@$obj->stakeholder_power) ? $obj->stakeholder_power : '')); ?>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Interest'); ?>:</td>
            <td align="left"><?php echo arraySelect($opt, 'stakeholder_interest', 'size="1" class="text" onchange="javascript:updateStrategy();"', ((@$obj->stakeholder_interest) ? $obj->stakeholder_interest : '')); ?>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Strategy'); ?>:</td>
            <td>
                <textarea name="stakeholder_strategy" cols="50" rows="3" class="textarea"><?php echo dPformSafe($obj->stakeholder_strategy); ?></textarea>
            </td>
        </tr>

    </table>

    <table border="0" width="100%">
        <tr>
            <td align="right" colspan="2">
                <?php print("<a href='?m=stakeholder&amp;a=pdf&amp;id=$initiating_stakeholder_id&amp;suppressHeaders=1'>" . $AppUI->_('Gerar PDF') . "</a>\n"); ?>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?> 
                <input type="button" class="button" value="<?php echo ucfirst($AppUI->_('submit')); ?>" onclick="submitIt()" />
            </td>
        </tr>
    </table>		
</form>

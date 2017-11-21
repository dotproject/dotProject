<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $dPconfig, $locale_char_se;

$titleBlock = new CTitleBlock('View Role', 'applet3-48.png', $m, "$m.$a");

$company_id = intval(dPgetParam($_GET, 'company_id', 0));
$query = new DBQuery;
$query->addTable('companies', 'c');
$query->addQuery('company_name');
$query->addWhere('c.company_id = ' . $company_id);
$res = & $query->exec();
$titleBlock->addCrumb(('?m=companies&amp;a=view&amp;company_id=' . $company_id), $res->fields['company_name']);
$query->clear();

$human_resources_role_id = intval(dPgetParam($_GET, 'human_resources_role_id', 0));
$obj = new CHumanResourcesRole();
$edit = intval(dPgetParam($_GET, 'edit', null));
if ($obj->load($human_resources_role_id) && $edit != 1) {
    $titleBlock->addCrumb("?m=human_resources&amp;a=view_role&amp;company_id=$company_id&amp;human_resources_role_id=$human_resources_role_id&amp;&amp;edit=1", "edit");
}
//Control when can delete

$canDelete = $obj->canDelete($obj);
if ($canDelete && $human_resources_role_id > 0) {
    $titleBlock->addCrumbDelete("LBL_DELETE_ROLE", $canDelete, $msg);
}else if(!$canDelete){
    $titleBlock->addCell('<p style="color: green;">' . $AppUI->_("LBL_ROLE_CANT_BE_DELETED") . '</p>');
}

$titleBlock->show();
?>
<form name = "editfrm" action = "?m=human_resources" method = "post">
    <script src="./modules/human_resources/view_role.js"></script>
    <link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
    <input type="hidden" name="dosql" value="do_role_aed" />
    <input type="hidden" name="del_msg" value="<?php echo $AppUI->_("LBL_DELETE_MSG_ROLE", UI_OUTPUT_JS); ?>" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="human_resources_role_id" value="<?php echo dPformSafe($human_resources_role_id); ?>" />
    <input type="hidden" name="human_resources_role_company_id" value="<?php echo dPformSafe($company_id); ?>" />
    <?php
    if ($edit || !$human_resources_role_id) {
        ?>
        <table cellspacing="1" cellpadding="1" border="0" width="100%" class="std" name="table_form">
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Role name'); ?>:</td>
                <td align='left'>
                    <input type='text' maxlength="100" name="human_resources_role_name" value="<?php echo ($obj->human_resources_role_name); ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Role responsability'); ?>:</td>
                <td><textarea name='human_resources_role_responsability' cols="90" rows="8"><?php echo ($obj->human_resources_role_responsability); ?></textarea></td>
            </tr>
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Role authority'); ?>:</td>
                <td><textarea name='human_resources_role_authority' cols="90" rows="8"><?php echo ($obj->human_resources_role_authority); ?></textarea></td>
            </tr>
            <tr>
                <td class="td_label"><?php echo $AppUI->_('Role competence'); ?>:</td>
                <td><textarea name='human_resources_role_competence' cols="90" rows="8"><?php echo ($obj->human_resources_role_competence); ?></textarea></td>
            </tr>
            <tr>
                <td align="right" colspan="2">
                    <input type="button" value="<?php echo ucfirst($AppUI->_('submit')); ?>" class="button" onclick="submitRole(document.editfrm);" />
                    <script>goCompanyHome=true;</script>
                    <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_company.php"); ?>
                </td>
            </tr>
        </table>

        <?php
    } else {
        ?>
        <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std" summary="human_resources">
            <tr>
                <td valign="top" width="100%">
                    <strong><?php echo $AppUI->_('Details'); ?></strong>
                    <table cellspacing="1" cellpadding="2" width="100%">
                        <tr>
                            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Role name'); ?>:</td>
                            <td class="hilite" width="100%"><?php echo $obj->human_resources_role_name; ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Role responsability'); ?>:</td>
                            <td class="hilite" width="100%"><?php echo $obj->human_resources_role_responsability; ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Role authority'); ?>:</td>
                            <td class="hilite" width="100%"><?php echo $obj->human_resources_role_authority; ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Role competence'); ?>:</td>
                            <td class="hilite" width="100%"><?php echo $obj->human_resources_role_competence; ?></td>
                        </tr>
                        <tr>
                        <td align="right" colspan="2">
                            
                            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_no_ask.php"); ?>
                        </td>
                    </tr>
                    </table>
                </td>
        </table>
       <?php
    }
    ?>
</form>
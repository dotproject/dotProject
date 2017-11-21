<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once DP_BASE_DIR."/modules/human_resources/configuration_functions.php";
global $tabbed, $currentTabName, $currentTabId, $AppUI;
$company_id = intval(dPgetParam($_GET, "company_id", 0));
$res = getDetailedUsersByCompanyId($company_id);
?>
<br />
<table width="95%" align="center" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <caption><b><?php echo $AppUI->_('2LBLHumanResources'); ?></b></caption>
    <tr>
        <th width="5%"></th>
        <th nowrap="nowrap" width="30%">
            <?php echo $AppUI->_("User username"); ?>
        </th>
        <th nowrap="nowrap" width="40%">
            <?php echo $AppUI->_("User roles"); ?>
        </th>
        <th></th>
    </tr>
    <?php
    require_once DP_BASE_DIR . "/modules/human_resources/human_resources.class.php";
    require_once DP_BASE_DIR . "/modules/human_resources/allocation_functions.php";
    for ($res; !$res->EOF; $res->MoveNext()) {
        $user_id = $res->fields["user_id"];
        $human_resource_id = getHumanResourceId($user_id);
        $user_has_human_resource = $human_resource_id != -1; //$human_resource_id equals -1 means does not exist
        $style = $user_has_human_resource ? "" : "background-color:#ED9A9A; font-weight:bold";
        $contact_id = $res->fields["contact_id"];

        $roles = getUserRolesByUserId($user_id);
        $concat_roles_names = "";
        if ($roles != null) {
            $roles_array = array();
            foreach ($roles as $role) {
                array_unshift($roles_array, $role["human_resources_role_name"]);
            }
            $concat_roles_names = implode(", ", $roles_array);
        }
        ?>
        <tr>
            <td style=<?php echo $style; ?>>
                <a style="text-decoration: none" href="index.php?m=human_resources&a=view_hr&user_id=<?php echo $user_id; ?>&contact_id=<?php echo $contact_id; ?>&company_id=<?php echo $company_id; ?>"> 
                    <img src="images/icons/stock_edit-16.png" border="0" />
                </a>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res->fields["contact_first_name"]; ?> <?php echo $res->fields["contact_last_name"]; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $concat_roles_names; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php
                $obj = new CHumanResource();
                $canDelete=false;
                if ($human_resource_id != -1) {
                     $obj->load($human_resource_id);
                     $canDelete=$obj->canDelete();
                }else{
                   $canDelete=true; 
                }
                if ($canDelete) {//user without human resource can be deleted
                    ?>
                
                <script>
                    function confirmHRDeletion(){
                        var answer=false;
                        if(window.confirm("<?php echo $AppUI->_("LBL_ASK_HUMAN_RESOURCE_DELETE",UI_OUTPUT_JS); ?>")){
                            answer=true;
                        }
                        return answer;
                    }
                </script>
                <form name="frmDelete" action="?m=human_resources" method="post" onsubmit="return confirmHRDeletion()">
                    <input type="hidden" name="dosql" value="do_hr_aed" />
                    <input type="hidden" name="del" value="1" />
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                    <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>" />
                    <input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
                    <input type="hidden" name="human_resource_id" value="<?php echo $human_resource_id; ?>" />
                    <button type="submit" style="border:none;background: none;cursor: pointer"><img src="images/trash_small.gif" border="0" /></button>
                </form>
                    
                    <?php
                }else{
                    ?>
                    <span style="color: green;"> <?php echo $AppUI->_("LBL_RH_CANT_BE_DELETED") ?> </span>
                <?php
                    }
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<table width="95%" align="center">
    <tr>
        <td><?php echo $AppUI->_("Key"); ?>:&nbsp;&nbsp;</td>
        <td style="background-color:#FFFFFF; color:#000000" width="10">&nbsp;</td>
        <td>=<?php echo $AppUI->_("User with human resources configured"); ?>&nbsp;&nbsp;</td>
        <td style="background-color:#ED9A9A; color:#000000" width="10">&nbsp;</td>
        <td>=<?php echo $AppUI->_("User with human resources not configured"); ?>&nbsp;&nbsp;</td>
    </tr>
     <tr >
            <td style="text-align: right" colspan="5">
                <script> var targetScreenOnProject="";</script>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_no_ask.php"); ?>
            </td>
        </tr>
</table>
<br /><br />
<!-- New user form -->
<script>
    function validateNewUserForm(){
        var firstName=document.getElementById("first_name").value;
        var lastName=document.getElementById("last_name").value;
        var resultado=true;
        if(firstName=="" || lastName==""){
            window.alert("<?php echo $AppUI->_('VALIDATE_NEW_USER_FORM');  ?>.");
            resultado=false;
        }
        return resultado;
    }
</script>
<form name="new_user" method="post" action="?m=human_resources" onsubmit="return validateNewUserForm()">
    <input type="hidden" name="dosql" value="do_user_contact_creation" />
    <?php
    require_once (DP_BASE_DIR . "/modules/companies/companies.class.php");
    $companyObj = new CCompany();
    $companyObj->load($company_id);
    $companyName = $companyObj->company_name;
    ?>
    <!-- Incluir links para editar contato ou user caso necessário. No form de HR -->
    <link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
    <table width="95%" align="center" border="0" class="std" name="table_form">
        <tr>
            <th colspan="2" align="center">
                <?php echo $AppUI->_("NEW_USER_FORM"); ?>
            </th>
        </tr>
        <tr>
            <th class="td_label">
                <label><?php echo $AppUI->_("Company"); ?>:</label>
            </th>
            <td>
                <input type="hidden" name="company_id" value="<?php echo $company_id ?>">
                <span><?php echo $companyName; ?></span>
            </td>
        </tr>
        <tr>
            <th class="td_label">
                <label><?php echo $AppUI->_("First Name"); ?>:</label>
            </th>
            <td>
                <input type="text" name="first_name" id="first_name" value="<?php echo "" ?>" maxlength="100" />
            </td>
        </tr>
        <tr>
            <th class="td_label">
                <label><?php echo $AppUI->_("Last Name"); ?>:</label>
            </th>
            <td>
                <input type="text" name="last_name" id="last_name" value="<?php echo "" ?>" maxlength="100" />
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">
                <br />
                <input type="submit" class="button" value="<?php echo ucfirst($AppUI->_("submit")); ?>" />
            </td>
        </tr>    
    </table>
</form>
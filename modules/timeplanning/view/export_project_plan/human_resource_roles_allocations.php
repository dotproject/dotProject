<?php
require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
?>
<table class="printTable">
    <tr>
        <th >
            <?php echo $AppUI->_("LBL_ROLE",UI_OUTPUT_HTML); ?>
        </th>
        <th >
            <?php echo $AppUI->_("Allocated user username",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <?php
    $query = new DBQuery();
    $query->addQuery("cr.human_resources_role_name, cr.human_resources_role_id, hrr.human_resource_id, hr.human_resource_user_id, ctc.contact_first_name, ctc.contact_last_name");
    $query->addTable("human_resources_role", "cr");
    $query->leftJoin("human_resource_roles", "hrr", "cr.human_resources_role_id=hrr.human_resources_role_id");
    $query->innerJoin("human_resource", "hr", "hr.human_resource_id=hrr.human_resource_id");
    $query->innerJoin("users", "usr", "usr.user_id=hr.human_resource_user_id");
    $query->innerJoin("contacts", "ctc", "ctc.contact_id=usr.user_contact");
    $query->addWhere("cr.human_resources_role_company_id = " . $companyId);
    $query->addOrder("cr.human_resources_role_name");
    $res = $query->loadList();
    $lastRoleId = null;
    for ($i = 0; $i < count($res); $i++) {
        $roleId = $res[$i]["human_resources_role_id"];
        $contactFirstName = $res[$i]["contact_first_name"];
        $contactLastName = $res[$i]["contact_last_name"];
        $userName = $contactFirstName . " " . $contactLastName;
        if ($lastRoleId != $roleId) {
            if ($lastRoleId != null) {
                ?>
                <tr>
                    <td> <?php echo $roleName ?></td>
                    <td> <?php echo $users; ?></td>
                </tr>

                <?php
            }
            $lastRoleId=$roleId;
            $roleName = $res[$i]["human_resources_role_name"];
            $users = $userName;
        } else {
            $users.=", " . $userName;
        }
        if(count($res)==($i+1)){
            ?>
                <tr>
                    <td> <?php echo $roleName ?></td>
                    <td> <?php echo $users; ?></td>
                </tr>
            <?php
        }
    }
    ?>
</table>
<script language="javascript">
    function deleteRole(roleId, roleName) {
        var option = document.createElement("option");
        option.name = roleName;
        option.value = roleId;
        option.text = roleName;
        try{
            document.getElementById("role_combo").add(option, null);
        }catch(e){
            document.getElementById("role_combo").add(option, 0);
        }
        var rolesTable = document.getElementById("roles_table");
        var rowIndex = document.getElementById(roleId).parentNode.parentNode.rowIndex;
        rolesTable.deleteRow(rowIndex);
    }
    function addRole() {
        var roleCombo = document.getElementById("role_combo");
        var rolesTable = document.getElementById("roles_table");
        var lastRowNumber = rolesTable.rows.length;
        var row = rolesTable.insertRow(lastRowNumber);
        var td1 = row.insertCell(0);
        td1.innerHTML = "<input readonly=\"readonly\" type=\"text\" id=\""+ roleCombo.options[roleCombo.selectedIndex].value +"\" value=\"" + roleCombo.options[roleCombo.selectedIndex].text + "\"/>";
        var td2 = row.insertCell(1);
        var deleteString = "deleteRole(\"" + roleCombo.options[roleCombo.selectedIndex].value + "\",\"" + roleCombo.options[roleCombo.selectedIndex].text + "\")";
        deleteString="<img src=\"images/icons/stock_delete-16.png\" onclick=" +deleteString+ " style=\"cursor:hand\"/>";
        td2.innerHTML = deleteString;
        roleCombo.remove(roleCombo.selectedIndex);
    }
</script>
<?php
$concat_role_names = "";
$roles = array();
if ($human_resource_id) {
    $query = new DBQuery;
    $query->addTable("human_resource_roles", "r");
    $query->addQuery("h.human_resources_role_name, h.human_resources_role_id");
    $query->innerJoin("human_resources_role", "h", "h.human_resources_role_id = r.human_resources_role_id");
    $query->addWhere("r.human_resource_id = " . $human_resource_id);
    $sql = $query->prepare();
    $roles = db_loadList($sql);
}
?>
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
            <td width="50%" valign="top">
                <table id="roles_table" width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
                    <tr>
                        <th width="100%"><?php echo $AppUI->_("Role"); ?></th>
                        <th>&nbsp;</th>
                    </tr>

                    <?php
                    foreach ($roles as $role) {
                        ?>
                        <tr>
                            <td align="left"><input type="text" id="<?php echo $role["human_resources_role_id"]; ?>" value="<?php echo $role["human_resources_role_name"]; ?>" />
                            </td>
                            <td>
                                <a href="javascript:deleteRole('<?php echo $role["human_resources_role_id"]; ?>', '<?php echo $role["human_resources_role_name"]; ?>')" title="<?php echo $AppUI->_("delete"); ?>">
                                    <?php echo dPshowImage("./images/icons/stock_delete-16.png", 16, 16, ""); ?></a>
                            </td>
                        </tr>
                        <?php
                    }

                    $query = new DBQuery;
                    $query->addTable("human_resources_role", "r");
                    $query->addQuery("r.human_resources_role_name, r.human_resources_role_id");
                    $query->addWhere("r.human_resources_role_company_id = " . $company_id);
                    $sql = $query->prepare();
                    $company_roles = db_loadList($sql);
                    $different_roles = $company_roles;
                    $i = 0;
                    foreach ($company_roles as $company_role) {
                        foreach ($roles as $role) {
                            if ($company_role["human_resources_role_id"] ==
                                    $role["human_resources_role_id"]) {
                                unset($different_roles[$i]);
                            }
                        }
                        $i++;
                    }
                    ?>
                </table>
            </td>
            <td width="50%" style="vertical-align: middle;text-align: center">
                <?php echo $AppUI->_("Add Role"); ?>:
                <select id="role_combo">
                    <?php
                    foreach ($different_roles as $role) {
                        ?>
                        <option name="<?php echo $role["human_resources_role_name"]; ?>" value="<?php echo $role["human_resources_role_id"]; ?>">
                            <?php echo $role["human_resources_role_name"]; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
                <input type="button" value="<?php echo $AppUI->_("add"); ?>" class="button" onclick="addRole();">
                     
            </td>
        </tr>
    </table>
<?php
require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
?>
<table class="printTable">
    <tr>
        <th >
            <?php echo $AppUI->_("Task Name",UI_OUTPUT_HTML); ?>
        </th>
        <th >
            <?php echo $AppUI->_("allocations",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <?php
    $query = new DBQuery();
    $query->addTable("tasks", "t");
    $query->addQuery("task_id, task_name");
    $query->addWhere("t.task_project = " . $projectId);
    $res = & $query->exec();
    for ($res; !$res->EOF; $res->MoveNext()) {
        $taskId = $res->fields["task_id"];
        $taskName = $res->fields["task_name"];
        ?>
        <tr>
            <td ><?php echo $taskName ?></td>
            <?php
            $query = new DBQuery();
            $query->addTable("project_tasks_estimated_roles", "e");
            $query->addQuery("e.id, e.role_id, h.human_resources_role_name, h.human_resources_role_responsability, h.human_resources_role_authority, h.human_resources_role_competence");
            $query->innerJoin("company_role", "c", "c.id = e.role_id");
            $query->innerJoin("human_resources_role", "h", "c.role_name = h.human_resources_role_name");
            $query->addWhere("e.task_id = " . $taskId);
            $resRHAllocation = $query->exec();
            ?>
            <td>
                <table style="border: 1px black solid;width:100%" >
                    <tr>
                        <th ><?php echo $AppUI->_("Estimated role"); ?></th>
                        <th ><?php echo $AppUI->_("Allocated role name"); ?>  </th>
                        <th ><?php echo $AppUI->_("Allocated user username"); ?> </th>
                    </tr>
                    <?php
                    for ($resRHAllocation; !$resRHAllocation->EOF; $resRHAllocation->MoveNext()) {
                        $project_tasks_estimated_roles_id = $resRHAllocation->fields["id"];
                        $estimatedRole = $resRHAllocation->fields["human_resources_role_name"];
                        //start
                        $query = new DBQuery();
                        $query->addTable("human_resource_allocation", "a");
                        $query->addQuery("*");
                        $query->addWhere("a.project_tasks_estimated_roles_id = " . $project_tasks_estimated_roles_id);
                        $resRHResource = $query->exec();

                        $query_company_role = new DBQuery();
                        $query_company_role->addTable("project_tasks_estimated_roles", "e");
                        $query_company_role->addQuery("c.role_name, h.human_resources_role_responsability,h.human_resources_role_authority, h.human_resources_role_competence");
                        $query_company_role->addJoin("company_role", "c", "c.id = e.role_id");
                        $query_company_role->addJoin("human_resources_role", "h", "h.human_resources_role_name = c.role_name");
                        $query_company_role->addWhere("e.id = " . $project_tasks_estimated_roles_id);
                        $res_company_role = $query_company_role->exec();

                        if ($resRHResource->fields["human_resource_id"]) {
                            $query_hr = new DBQuery();
                            $query_hr->addTable("human_resource", "h");
                            $query_hr->addQuery("u.user_username, c.contact_first_name, c.contact_last_name");
                            $query_hr->addJoin("users", "u", "u.user_id = h.human_resource_user_id");
                            $query_hr->addJoin("contacts", "c", "u.user_contact = c.contact_id");
                            $query_hr->addWhere("h.human_resource_id = " . $resRHResource->fields["human_resource_id"]);
                            $res_hr = $query_hr->exec();
                            $human_resource = new CHumanResource();
                            $human_resource->load($resRHResource->fields["human_resource_id"]);
                        }
                        $concat_roles_names = "";
                        if ($resRHResource->fields["human_resource_id"] != "") {
                            $query = new DBQuery;
                            $query->addTable("human_resource_roles", "r");
                            $query->addQuery("h.human_resources_role_name, h.human_resources_role_id");
                            $query->innerJoin("human_resources_role", "h", "h.human_resources_role_id = r.human_resources_role_id");
                            $query->addWhere("r.human_resource_id = " . $resRHResource->fields["human_resource_id"]);
                            $sql = $query->prepare();
                            $roles = db_loadList($sql);
                            $roles_array = array();
                            foreach ($roles as $role) {
                                array_unshift($roles_array, $role["human_resources_role_name"]);
                            }
                            $concat_roles_names = implode(", ", $roles_array);
                        }
                        ?>
                        <tr>
                            <td width="33%"><?php echo $estimatedRole; ?>&nbsp;</td>
                            <td width="33%"><?php echo $concat_roles_names; ?>&nbsp;</td>
                            <td width="33%"><?php echo ucwords($res_hr->fields["user_username"]); ?>&nbsp;</td>
                        </tr>
                        <?php
                    }
                    $query->clear();
                    ?>
                </table>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
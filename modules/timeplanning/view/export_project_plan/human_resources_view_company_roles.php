<?php
global $AppUI;

$query = new DBQuery();
$query->addTable("human_resources_role", "r");
$query->addQuery("r.*");
$query->addWhere("r.human_resources_role_company_id = " . $companyId);
$res_companies = & $query->exec();
?>
<table class="printTable" >
    <tr>
        <th width="10%">
            <?php echo $AppUI->_("Role name",UI_OUTPUT_HTML); ?>
        </th>
        <th  width="30%">
            <?php echo $AppUI->_("Role responsability",UI_OUTPUT_HTML); ?>
        </th>
        <th  width="30%">
            <?php echo $AppUI->_("Role authority",UI_OUTPUT_HTML); ?>
        </th>
        <th  width="30%">
            <?php echo $AppUI->_("Role competence",UI_OUTPUT_HTML); ?>
        </th>
    </tr>

    <?php
    require_once DP_BASE_DIR . "/modules/human_resources/configuration_functions.php";
    for ($res_companies; !$res_companies->EOF; $res_companies->MoveNext()) {
        $human_resources_role_id = $res_companies->fields["human_resources_role_id"];
        $configured = isConfiguredRole($human_resources_role_id);
        $style = $configured ? "" : "background-color:#ED9A9A; font-weight:bold";
        ?>
        <tr>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields["human_resources_role_name"]; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields["human_resources_role_responsability"]; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields["human_resources_role_authority"]; ?>
            </td>
            <td style=<?php echo $style; ?>>
                <?php echo $res_companies->fields["human_resources_role_competence"]; ?>
            </td>
        </tr>
        <?php
    }
    $query->clear();
    ?>
</table>

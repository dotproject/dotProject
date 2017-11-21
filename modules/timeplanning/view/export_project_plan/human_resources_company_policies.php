<?php
require_once DP_BASE_DIR . "/modules/human_resources/human_resources.class.php";
$query = new DBQuery();
$query->addTable("company_policies", "p");
$query->addQuery("company_policies_id");
$query->addWhere("p.company_policies_company_id = " . $companyId);
$res = & $query->exec();
$company_policies_id = $res->fields["company_policies_id"];
$query->clear();
$policies = new CCompaniesPolicies();
$policies->load($company_policies_id);
?>
<table class="printTable">
    <tr>
        <th><?php echo $AppUI->_("Rewards and recognition",UI_OUTPUT_HTML); ?>:</th>
    </tr>
    <tr>
        <td><?php echo dPformSafe($policies->company_policies_recognition); ?></td>
    </tr>
    <tr>
        <th><?php echo $AppUI->_("Regulations, standards, and policy compliance",UI_OUTPUT_HTML); ?>:</th>
    </tr>
    <tr>
        <td><?php echo dPformSafe($policies->company_policies_policy); ?></td>
    </tr>
    <tr>
        <th><?php echo $AppUI->_("Safety",UI_OUTPUT_HTML); ?>:</b></th>
    </tr>
    <tr>
        <td><?php echo dPformSafe($policies->company_policies_safety); ?></td>
    </tr>
</table>


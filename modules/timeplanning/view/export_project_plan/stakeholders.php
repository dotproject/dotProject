<?php
//SELECT c.contact_first_name, c.contact_last_name,s.stakeholder_responsibility,s.stakeholder_power, s.stakeholder_interest,s.stakeholder_strategy FROM dotproject_2_1_7.dotp_initiating_stakeholder as s
//inner join dotp_initiating as i on s.initiating_id=i.initiating_id inner join dotp_contacts c on c.contact_id=s.contact_id where i.project_id=2;
$query = new DBQuery();
$query->addTable("initiating_stakeholder", "s");
$query->addQuery("c.contact_first_name, c.contact_last_name,s.stakeholder_responsibility,s.stakeholder_power, s.stakeholder_interest,s.stakeholder_strategy");
$query->innerJoin("initiating", "i", "s.initiating_id=i.initiating_id");
$query->innerJoin("contacts", "c", "c.contact_id=s.contact_id");
$query->addWhere("i.project_id = " . $projectId);
$stakeholders = $query->exec();
?>
<table class="printTable">
    <tr>
        <th>
            <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER_RESPONSABILITIES",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER_POWER",UI_OUTPUT_HTML); ?>

        </th>
        <th>
            <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER_INTEREST",UI_OUTPUT_HTML); ?>
        </th>
        <th>
            <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER_STRATEGY",UI_OUTPUT_HTML); ?>
        </th>
    </tr>
    <?php
    for ($stakeholders; !$stakeholders->EOF; $stakeholders->MoveNext()) {
        $power = $stakeholders->fields["stakeholder_power"] != "1" ? $AppUI->_("LBL_PROJECT_STAKEHOLDER_LOW",UI_OUTPUT_HTML) : $AppUI->_("LBL_PROJECT_STAKEHOLDER_HIGH",UI_OUTPUT_HTML);
        $interest = $stakeholders->fields["stakeholder_interest"] != "1" ? $AppUI->_("LBL_PROJECT_STAKEHOLDER_LOW",UI_OUTPUT_HTML) : $AppUI->_("LBL_PROJECT_STAKEHOLDER_HIGH",UI_OUTPUT_HTML);
        ?>
        <tr>
            <td><?php echo $stakeholders->fields["contact_first_name"] . " " . $stakeholders->fields["contact_last_name"] ?></td>
            <td><?php echo $stakeholders->fields["stakeholder_responsibility"] ?></td>
            <td><?php echo $power; ?></td>
            <td><?php echo $interest; ?></td>
            <td><?php echo $stakeholders->fields["stakeholder_strategy"] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
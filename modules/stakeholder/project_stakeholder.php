<?php
require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
$project_id = dPgetParam($_GET, "project_id", 0);
$initiating = CInitiating::findByProjectId($project_id);
if (is_null($initiating)) {
    $initiating_id = -1;
} else {
    $initiating_id = $initiating->initiating_id;
}

$q = new DBQuery();
$q->addQuery("*");
$q->addTable("initiating_stakeholder", "stk");
$q->addJoin("initiating", "i", "i.initiating_id = stk.initiating_id");
$q->addJoin("contacts", "c", "c.contact_id = stk.contact_id");
$q->addWhere("i.initiating_id=".$initiating_id);
$q->addOrder("stk.initiating_id");
$q->addOrder("stk.contact_id");
$list = $q->loadList();
?>
<table width="95%" align="center">
    <tr>
        <td>
            <form action="?m=stakeholder&a=addedit&project_id=<?php echo $project_id; ?>&initiating_id=<?php echo $initiating_id; ?>" method="post">
                <input type="submit" class="button" value="<?php echo $AppUI->_("new stakeholder") ?>" />
            </form>
        </td>
    </tr>
</table>
<table width="95%" align="center" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th nowrap="nowrap"><?php echo $AppUI->_("Stakeholder"); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_("Responsibilities"); ?></th>
        <th><?php echo $AppUI->_("Interest"); ?></th>
        <th><?php echo $AppUI->_("Power"); ?></th>
        <th><?php echo $AppUI->_('Strategy'); ?></th>
        <th nowrap="nowrap"> &nbsp; </th>
    </tr>
    <?php
    foreach ($list as $row) {
        $power = $row["stakeholder_power"] != "1" ? $AppUI->_("LBL_PROJECT_STAKEHOLDER_LOW") : $AppUI->_("LBL_PROJECT_STAKEHOLDER_HIGH");
        $interest = $row["stakeholder_interest"] != "1" ? $AppUI->_("LBL_PROJECT_STAKEHOLDER_LOW") : $AppUI->_("LBL_PROJECT_STAKEHOLDER_HIGH");
        ?>
        <tr>
            <td><?php echo $row["contact_first_name"] ?> <?php echo $row["contact_last_name"] ?></td>
            <td><?php echo $row["stakeholder_responsibility"] ?> </td>
            <td><?php echo $interest ?></td>
            <td><?php echo $power ?></td>
            <td><?php echo $row["stakeholder_strategy"] ?></td>

            <td>
                <a href="index.php?m=stakeholder&a=addedit&initiating_stakeholder_id=<?php echo $row["initiating_stakeholder_id"] ?>&project_id=<?php echo $project_id; ?>&initiating_id=<?php echo $initiating_id; ?>" style="border: 0px;text-decoration: none">
                    <img src="modules/timeplanning/images/stock_edit-16.png" border="0" alt="<?php echo $AppUI->_("Edit"); ?>"/>
                </a>
            </td>
        </tr>
    <?php } ?>
</table>
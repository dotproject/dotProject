<?php
require_once DP_BASE_DIR . "/modules/admin/admin.class.php";
?>
<?php
$q = new DBQuery();
$q->addQuery("*");
$q->addTable("initiating", "i");
$q->addWhere("i.initiating_completed = 1");
$q->addWhere("i.initiating_approved = 0");
$q->addWhere("i.initiating_authorized = 0");
$q->addOrder("i.initiating_id");
$q->setLimit(100);
$list = $q->loadList();
?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th nowrap="nowrap"><?php echo $AppUI->_("Title"); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_("Project Manager"); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_("Created By"); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_("Creation Date"); ?></th>
        <th nowrap="nowrap"> </th>
    </tr>
    <?php foreach ($list as $row) { ?>
        <tr>
            <?php
            $manager = new CUser();
            $manager->load($row["initiating_manager"]);
            $creator = new CUser();
            $creator->load($row["initiating_create_by"]);
            ?>
            <td><?php echo $row["initiating_title"] ?></td>
            <td><?php echo ucfirst($manager->user_username); ?></td>
            <td><?php echo ucfirst($creator->user_username); ?></td>
            <td><?php echo $row["initiating_date_create"] ?></td>
            <td><a href="index.php?m=initiating&a=addedit&id=<?php echo $row["initiating_id"] ?>">edit</a></td>
        </tr>
    <?php } ?>
</table>

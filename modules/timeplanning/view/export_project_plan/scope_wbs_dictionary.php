<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_dictionary_entry.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
$controllerWBSItem = new ControllerWBSItem();
?>
<table class="printTable">
    <tr>
        <th width="25%"><?php echo $AppUI->_("LBL_WBS_ITEM",UI_OUTPUT_HTML); ?></th>
        <th> <?php echo $AppUI->_("LBL_DESCRIPTION",UI_OUTPUT_HTML); ?> </th>
    </tr>
    <?php
    $items = $controllerWBSItem->getWBSItems($projectId);
    foreach ($items as $item) {
        ?>
        <tr>
            <td style="text-align: left">
                <?php echo $item->getIdentation(); ?>
                <?php echo $item->getNumber(); ?>
                <?php echo $item->getName(); ?>
                <?php echo $item->isLeaf() == 1 ? "*" : ""; ?>
            </td>
            <td>
                <?php
                $obj = new WBSDictionaryEntry();
                $obj->load($item->getId());
                ?>
                <?php echo $obj->getDescription(); ?>
            </td>
        </tr>
    <?php }
    ?>
</table>      
<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_dictionary_entry.class.php");
$controllerWBSItem = new ControllerWBSItem();
?>
<table class="printTable">
    <tr>
        <th class="shortTD"><?php echo $AppUI->_("LBL_ID",UI_OUTPUT_HTML); ?> </th>
        <th style="text-align: left"><?php echo $AppUI->_("LBL_WBS_ITEM",UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("LBL_DESCRIPTION",UI_OUTPUT_HTML); ?>&nbsp;&nbsp;(<?php echo $AppUI->_("LBL_WBS_DICTIONARY",UI_OUTPUT_HTML); ?>)</th>
    </tr>
    <?php
    $items = $controllerWBSItem->getWBSItems($projectId);
    foreach ($items as $item) {
        ?>
        <tr>
            <td><?php echo $item->getNumber() . " " . ($item->isLeaf() == 1 ? "*" : "") ?></td>
            <td colspan="<?php echo $item->isLeaf() == 1?1:2 ?>"><?php echo /*$item->getIdentation() . */ $item->getName() ?></td>
           <?php 
                if($item->isLeaf() == 1){
                    ?>
                <td style="width:50%">
                <?php
                   $dic=new WBSDictionaryEntry();
                   $dic->load($item->getId());
                   echo $dic->getDescription();   
                ?>
                </td>
                <?php
                }
                ?>
            
        </tr>    
        <?php
    }
    ?>
        <tr>
            <td colspan="3">
                <i><?php echo $AppUI->_("LBL_WORK_PACKAGE_SYMBOL", UI_OUTPUT_HTML); ?></i>
            </td>
        </tr>
</table>      
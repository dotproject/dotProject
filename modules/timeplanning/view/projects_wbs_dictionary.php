<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_dictionary_entry.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
$controllerWBSItem = new ControllerWBSItem();
?>
<?php $project_id = dPgetParam($_GET, "project_id", 0); ?>
<form action="?m=timeplanning&a=view&project_id=<?php echo $project_id; ?>" method="post" name="form_wbs_dictionary" id="form_wbs_dictionary">
    <input name="dosql" type="hidden" value="do_project_wbs_dictionary_aed" />
    <input name="eap_items_ids" id="eap_items_ids" type="hidden" />	
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>" />

    <table class="std" id="tb_wbs_dictionary" width="95%" align="center">
        <caption> <b> <?php echo $AppUI->_("LBL_WBS_DICTIONARY"); ?>  </b></caption>
        <tr>
            <th width="15%">Item</th>
            <th> <?php echo $AppUI->_("LBL_DESCRIPTION"); ?> </th>
            <!--
            <th> Tipo de entrega </th>
            <th> Critérios de aceite </th>
            -->
        </tr>
        <?php
        $items = $controllerWBSItem->getWBSItems($project_id);
        foreach ($items as $item) {
            ?>
            <tr>
                <td colspan="<?php echo $item->isLeaf() == 1?1:2 ?>">
                    <span style="color:#FFFFFF"><?php echo $item->getIdentation(); ?></span>
                    <?php echo $item->getNumber(); ?>
                    <?php echo $item->getName(); ?>
                    <?php echo $item->isLeaf()==1?"*":""; ?>
                </td>
                <?php if ($item->isLeaf()==1){ ?>
                <td>
                    <?php
                        $obj=new WBSDictionaryEntry();
                        $obj->load($item->getId());
                    ?>
                    <textarea name="wbs_item_dictionaty_entry_<?php echo $item->getId(); ?>" style="width:95%" rows="3"><?php echo $obj->getDescription(); ?></textarea>
                </td>
                <?php } ?>
                <!--
                <td>
                    <select>
                        <option>Interna</option>
                        <option>Externa</option>
                    </select>
                </td>
                <td>
                    <textarea rows="3"></textarea>                        
                </td>
                -->
            </tr>
        <?php }
        ?>
    </table>      
    <table width="95%" align="center">
        <tr> 
            <td colspan="2" style="text-align: right"> 
                <input type="submit" class="button" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" />
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>  
            </td>
        </tr>
    </table>
</form>
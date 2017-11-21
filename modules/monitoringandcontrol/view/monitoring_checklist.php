<?php
$display = "block";
if ($meetingData[meeting_type_id] != 3 && $meetingData[meeting_type_id] != 5) {
    $display = "none";
};
?>
<table class="tbl" id="p1_checklist" style="display:<?php echo $display ?>;" >
    <tr>
        <th colspan="2"><b><?php echo ($AppUI->_('LBL_ITEM_MONITORACAO')); ?></b></th>                        
    </tr>
    <?php
    $itemsCategory = array(); // the categories (knownledge areas) which contains checklist itens
    $knowledgeAreas = array("scope", "cost", "quality", "human_resource", "comunication", "risk", "acquisitions", "stakeholder");
    $itemsCategoriesLabel = array(
        "scope" => $AppUI->_("LBL_PROJECT_PLAN_SCOPE"),
        "time" => $AppUI->_("LBL_PROJECT_TIME"),
        "cost" => $AppUI->_("LBL_PROJECT_COSTS"),
        "quality" => $AppUI->_("LBL_PROJECT_QUALITY"),
        "human_resource" => $AppUI->_("LBL_PROJECT_PROJECT_HUMAN_RESOURCES"),
        "comunication" => $AppUI->_("LBL_PROJECT_COMMUNICATION"),
        "risk" => $AppUI->_("LBL_PROJECT_RISKS"),
        "acquisitions" => $AppUI->_("LBL_PROJECT_ACQUISITIONS"),
        "stakeholder" => $AppUI->_("LBL_PROJECT_STAKEHOLDER")
    );
    foreach ($knowledgeAreas as $knownledgeArea) {
        $itens = $controllerAta->getMeetingItemByKnowledgeArea($knownledgeArea);

        foreach ($itens as $item) {
            $knownledgeArea = $item[2];
            if (is_null($itemsCategory[$knownledgeArea])) {
                $itemsCategory[$knownledgeArea] = $knownledgeArea;
                ?>
                <tr>
                    <th style="text-align:left" colspan="2">
                        <?php echo $itemsCategoriesLabel[$knownledgeArea]; ?>
                        <a href="#<?php echo $knownledgeArea; ?>_monitoring" style="color:white">[+]</a>
                    </th>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td width="95%">
                    <?php echo ($item[1]); ?>    
                    <input type="hidden" name="meeting_item_id[]" value="<?php echo $item[0]; ?>"   />                                  
                </td>
                <td width="5%">
                    <?php 
                        $answer=isset($meeting_id)?$controllerAta->getMeetingItemResponse($meeting_id,$item[0]):"1";
                    ?>
                    <select name="item_select_status[]" size="1">
                        <option value="0" <?php echo $answer==0?"selected":"" ?> ><?php echo $AppUI->_('LBL_SIM'); ?></option>
                        <option value="1" <?php echo $answer==1?"selected":"" ?>><?php echo $AppUI->_('LBL_NAO'); ?></option>
                    </select>
                </td>
            </tr>
        <?php
        }
    }
    ?>
</table>
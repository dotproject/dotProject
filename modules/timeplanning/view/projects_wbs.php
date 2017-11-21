<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
$controllerWBSItem = new ControllerWBSItem();
?>
<script src="./modules/timeplanning/js/eap.js"></script>
<style>
    /**
    shortTD css class is used to control the expansion of the first cell of wbs table.
    It reserves more space for the description column.
    */
    .shortTD{
        max-width: 28px;
        width: 28px;
    }
    .std caption{
        text-align: center;
    }

</style>


<?php $project_id = dPgetParam($_GET, 'project_id', 0); ?>
<form action="?m=timeplanning&a=view&project_id=<?php echo $project_id; ?>" method="post" name="form_eap" id="form_eap">
    <input name="dosql" type="hidden" value="do_project_eap_aed" />
    <input name="eap_items_ids" id="eap_items_ids" type="hidden" />
    <input type="hidden" name="items_ids_to_delete" id="items_ids_to_delete" value="" />	
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>" />

    <table width="95%" align="center">
        <!--
        <tr>
            
                <td>
                    <fieldset>
                        <legend>Templates</legend>
                        <input type="button" class="button" onclick=saveEAP() value="Criar template a partir desta EAP" />
                        <br /> <br/>
                        Criar EAP com base no template:<select class="text"><option>- Selecione o template -</option></select>
                         <br/>
                    </fieldset>
                </td>
                <td>
                    <fieldset>
                        <legend>Baselines</legend>
                        <input type="button" class="button" onclick=saveEAP() value="Gerar nova baseline" />
                        |
                        <b>Baseline atual:</b> 1.1
                        <br/> <br/>
                        Consultar baseline:<select class="text"><option>- Selecione a baseline -</option></select>            
                    </fieldset>
                </td>
        </tr>
        -->
        <tr> 
            <td align="left"> <input type="button" class="button" value="<?php echo $AppUI->_("LBL_ADD"); ?>" onclick="addItem('', '', '', '')"  /> </td>
            <td align="right"> 
                <input type="button" class="button" onclick=saveEAP() value="<?php echo $AppUI->_('LBL_SAVE'); ?>"   /> 
            </td>
        </tr>
    </table>
    <table class="tbl" id="tb_eap" width="95%" align="center" cellpadding="2" cellspacing="1" border="0">
        <caption> <b> <?php echo $AppUI->_("LBL_WBS_EXTENDED"); ?> </b></caption>
        <tr>
            <th class="shortTD"><?php echo $AppUI->_("LBL_ID"); ?> </th>
            <th width="20"><?php echo $AppUI->_("LBL_ORDER"); ?></th>
            <th width="20"><?php echo $AppUI->_("LBL_IDENTATION"); ?></th>
            <th ><?php echo $AppUI->_("LBL_WBS"); ?> Item</th>
            <th width="20"> &nbsp; </th>
        </tr>
    </table>      
    <table width="95%" align="center">
        <tr> 
            <td align="left"> <input type="button" class="button" value="<?php echo $AppUI->_("LBL_ADD"); ?>" onclick="addItem('', '', '', '')" /> </td>
            <td align="right">
                <input type="button" class="button" onclick=saveEAP() value="<?php echo $AppUI->_("LBL_SAVE"); ?>" />
            </td>
        </tr>
    </table>
</form>
<?php
$items = $controllerWBSItem->getWBSItems($project_id);
foreach ($items as $item) {
    echo '<script>addItem(' . $item->getId() . ',"' . $item->getName() . '",0,"' . $item->getIdentation() . '");</script>';
}
?>
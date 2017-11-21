<?php
require_once DP_BASE_DIR . "/modules/timeplanning/model/acquisition/acquisition_execution.class.php";
require_once DP_BASE_DIR . "/modules/timeplanning/view/dotp_calendar_import.php";
require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";
$acquisitionPlannedResources = getResources("Non-Human", " and cost_project_id=" . $_GET["project_id"]);
$acquisitionPlannedRisks = getContingencyCosts($_GET["project_id"]);
$acquisition = new AcquisitionExecution();
$costLinkColor = "#9C9C9C";
?>
<a name="acquisition_execution"></a>
<style>
    #aquisition_list tr td{
        vertical-align: top;
    }
</style>
<script type="text/javascript" language="javascript">
    /**
     * Avoid form submition when mandatory fields are empty
     */
    function validation(){
        var description=document.acquisition_exe.description.value;
        var value=document.acquisition_exe.value.value;
        var date=document.acquisition_exe.date.value;
        var result=false;
        if(description!="" && value !="" && date !=""){
            result=true;
        }else{
            window.alert("<?php echo $AppUI->_("LBL_GENERIC_FORM_VALIDATION", UI_OUTPUT_JS); ?>");
        }
        if(document.acquisition_exe.reference_id.selectedIndex>=threadShouldResourcesContincency){
            document.acquisition_exe.is_risk_contingency.value=1;
        }else{
            document.acquisition_exe.is_risk_contingency.value=0;
        }
        return result;
    }
    
    function vincularItemPlanejado(){
        var reference_id=document.acquisition_exe.reference_id;
        var id= reference_id.options[reference_id.selectedIndex].value;
        var name="";
        if(id!=""){
            name= reference_id.options[reference_id.selectedIndex].text;
            document.acquisition_exe.description.value=name;
            reference_id.style.backgroundColor="<?php echo $costLinkColor; ?>";
        }
    }
    
    function desvincularItemPlanejado(){
        var reference_id=document.acquisition_exe.reference_id;
        reference_id.selectedIndex=0;
        document.acquisition_exe.description.value="";
        reference_id.style.backgroundColor="";
    }
    
</script>
<table class="tbl" align="center" width="95%" id="aquisition_list">
    <caption><?php echo $AppUI->_("LBL_ACQUISITION_EXECUTION"); ?></caption>
    <tr>
        <th><?php echo $AppUI->_("LBL_EXE_ACQUISITION_DESCRIPTION"); ?></th>
        <th><?php echo $AppUI->_("LBL_EXE_ACQUISITION_VALUE"); ?> (<?php echo dPgetConfig("currency_symbol") ?>)</th>
        <th><?php echo $AppUI->_("LBL_EXE_ACQUISITION_DATE"); ?></th>
        <th><?php echo $AppUI->_("LBL_DELIVERED"); ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    $df = $AppUI->getPref('SHDATEFORMAT');
    $list = $acquisition->loadAll($_GET["project_id"]);
    foreach ($list as $object) {
        ?>
        <tr>
            <td style="<?php echo $attributes; ?>">
				<?php
				if ($object->reference_id != "" && $object->reference_id != 0) {
				?>
				<image src="modules/timeplanning/images/link.png" />
				<?php
				}
				?>
                <?php echo $object->description; ?>
            </td>

            <td align="center">
                <?php
                echo number_format($object->value, 2, ',', '.');
                ?>
            </td>

            <td align="center">
                <?php
                $date = new CDate($object->date);
                echo $date->format($df);
                ?>
            </td>

            <td align="center">
                <?php echo $object->is_delivered == 1 ? $AppUI->_("LBL_YES") : $AppUI->_("LBL_NO"); ?>
            </td>

            <td width="20" valign="top">
                <a href="index.php?m=projects&a=view&acquisition_execution_id=<?php echo $object->id ?>&project_id=<?php echo $_GET["project_id"] ?>#acquisition_execution_edit">
                    <img alt="<?php echo $AppUI->_("LBL_EDIT"); ?>" src="modules/timeplanning/images/stock_edit-16.png" border="0" />
                </a>
            </td>	
            <td width="20" valign="top">
                <form method="post" action="?m=timeplanning">
                    <input name="dosql" type="hidden" value="do_project_acquisition_execution_deletion" />
                    <input name="project_id" type="hidden" value="<?php echo $_GET["project_id"] ?>" />
                    <input name="tab" type="hidden" value="<?php echo $_GET["tab"]; ?>" />
                    <input name="id" type="hidden" value="<?php echo $object->id ?>" />
                    <button type="submit" style="text-decoration: none;border: 0px;cursor: pointer">
                        <img src="modules/timeplanning/images/stock_delete-16.png" border="0" />
                    </button>
                </form>
            </td>	
        </tr>
    <?php } ?>
    <tr>
        <td colspan="6">
            <?php echo $AppUI->_("LBL_LEGEND"); ?>: 
            <image src="modules/timeplanning/images/link.png" />
            <?php echo $AppUI->_("LBL_EXE_ACQUISITION_LINKED_PLANNED"); ?>
            &nbsp;|&nbsp;
            <?php echo $AppUI->_("LBL_DELIVERED"); ?>:&nbsp;<?php echo $AppUI->_("LBL_DELIVERED_HINT"); ?>
        </td>
    </tr>
</table>


<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<?php
$id = $_GET["acquisition_execution_id"];
$object = new AcquisitionExecution();
if ($id > 0) {
    $object->load($id);
}
?>
<a name="acquisition_execution_edit"></a>
<form  method="post" name="acquisition_exe" action="?m=timeplanning" onsubmit="return validation()">
    <input name="dosql" type="hidden" value="do_project_acquisition_execution" />
    <input name="project_id" type="hidden" value="<?php echo $_GET["project_id"]; ?>" />
    <input name="tab" type="hidden" value="<?php echo $_GET["tab"]; ?>" />
    <input name="id" type="hidden" value="<?php echo $object->id ?>" />
    <table width="95%" align="center">
        <tr>
            <td colspan="2" >
                <input type="submit" name="Salvar" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" class="button" />
            </td>
        </tr>
    </table>
    <table class="std" align="center" width="95%" name="table_form" border="0">
        <tr >
            <th colspan="2" align="center"><?php echo $AppUI->_("LBL_ACQUISITION_EXECUTION"); ?></th>
        </tr>

        <tr>
            <td> &nbsp; </td>
            <td>
                <input type="hidden" name="is_risk_contingency" value="<?php echo $object->is_risk_contingency ?>" />
                <select class="text" name="reference_id" id="reference_id">
                    <option value=""><?php echo $AppUI->_("LBL_LINK_COST_BASELINE_ITEM"); ?></option>
                    <optgroup label="<?php echo $AppUI->_("LBL_SELECT_PLANNED_RESOURCE"); ?>">
                        <?php
                        $threadshoudResourcesContincency = 0;
                        foreach ($acquisitionPlannedResources as $record) {
                            $attributes = "";
                            if ($object->is_risk_contingency != 1 && $object->reference_id == $record["cost_id"]) {
                                $attributes = "selected=\"true\" style=\"background-color: $costLinkColor;\"";
                            }
                            ?>
                            <option value="<?php echo $record["cost_id"]; ?>" <?php echo $attributes; ?>>
                                <?php echo $record["cost_description"]; ?>
                            </option>
                            <?php
                            $threadshoudResourcesContincency++; //in the loop ending it contains the last valid index for non HR resources. Index bigger are contingency costs.
                        }
                        ?>
                    </optgroup>

                    <optgroup label="<?php echo $AppUI->_("LBL_SELECT_CONTINGENCY_COST"); ?> " >
                        <?php
                        foreach ($acquisitionPlannedRisks as $record) {
                            $attributes = "";
                            if ($object->is_risk_contingency == 1 && $object->reference_id == $record["budget_reserve_id"]) {
                                $attributes = "selected=\"true\" style=\"background-color: $costLinkColor;\"";
                            }
                            ?>
                            <option value="<?php echo $record["budget_reserve_id"]; ?>" <?php echo $attributes; ?>>
                                <?php echo $record["budget_reserve_description"]; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </optgroup>
                </select> 
                <script>
                    var threadShouldResourcesContincency=<?php echo $threadshoudResourcesContincency; ?>;
                </script>
                <input type="button" class="button" value="Vincular com item planejado" onclick="vincularItemPlanejado()" />
                &nbsp;
                <input type="button" class="button" value="Limpar" onclick="desvincularItemPlanejado()" />
            </td>
        </tr> 
        <tr >
            <td class="td_label"><?php echo $AppUI->_("LBL_EXE_ACQUISITION_DESCRIPTION"); ?><span class="span_mandatory">*</span>:</td>
            <td nowrap>
                <input type="text" class="text" name="description" value="<?php echo $object->description ?>" />
            </td>	
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_EXE_ACQUISITION_VALUE"); ?>
                <?php echo "(" . dPgetConfig("currency_symbol") . ")" ?> <span class="span_mandatory">*</span>:
            </td>
            <td nowrap>
                <input type="text" class="text" name="value" value="<?php echo $object->value ?>" style="width: 150px" />
            </td>	
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_EXE_ACQUISITION_DATE"); ?><span class="span_mandatory">*</span>:</td>
            <td nowrap>
                <?php
                $date = new CDate($object->date);
                ?>
                <input type="hidden" name="date" id="date" value="<?php echo (($object->date) ? $date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" class="text" name="date_view" id="date_view" value="<?php echo (($date) ? $date->format($df) : ''); ?>" disabled="disabled"  style="width:80px" />

                <a href="#" onclick="popCalendar( document.getElementById('date_view'),  document.getElementById('date'));">
                    <img src="./images/calendar.gif" width="24" height="12" border="0" />
                </a>

            </td>	
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_DELIVERED"); ?><span class="span_mandatory">*</span>:</td>
            <td nowrap>
                <input type="checkbox" value="1" name="is_delivered" <?php echo $object->is_delivered == 1 ? "checked" : "" ?> />
            </td>	
        </tr>



    </table>
    <table width="95%" align="center">
        <tr>
            <td colspan="2" >
                <input type="submit" name="Salvar" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" class="button" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="span_mandatory">*</span>&nbsp;<?php echo $AppUI->_("LBL_REQUIRED_FIELD"); ?>
            </td>
        </tr>
    </table>
</form>

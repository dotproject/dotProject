<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
$project_id = intval(dPgetParam($_GET, "project_id", 0));
?>
<style>
    table[name="risk_management_plan_table"] td{
        vertical-align: top;
    }

    table[name="risk_management_plan_table"] caption{
        font-weight: bold;
    }

    table [name="risk_management_plan_table"] td input[type="text"],textarea,select{
        width: 90%;
    }


    .td_label{
        text-align: right;
        text-wrap: none;
        width:18%;
    }
    .span_mandatory{
        color:red;
    }

    .td_section{
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
        background-color: #CCC;
        border: 1px solid #888;
        height: 20px;
        padding-top: 3px;
    }

    .td_icons{
        width:35px;
    }

</style>
<script src="./modules/risks/ear.js"></script>

<?php
require_once DP_BASE_DIR . "/modules/risks/risks_management_plan.class.php";
require_once (DP_BASE_DIR . "/modules/risks/controller_wbs_items.class.php");
$obj = new CRisksManagementPlan();
$q = new DBQuery();
$q->addQuery("*");
$q->addTable("risks_management_plan");
$q->addWhere("project_id = " . $project_id);
if (!db_loadObject($q->prepare(), $obj) && ($project_id >= 0)) {
    $obj = new CRisksManagementPlan();
}
$obj->loadDefaultValues();
?>

<form name="uploadFrm" action="?m=risks" method="post">
    <input type="hidden" name="dosql" value="do_risks_management_plan" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
    <input type="hidden" name="risk_plan_id" value="<?php echo $obj->risk_plan_id; ?>" /> 

    <table  width="100%" border="0" class="std" name="risk_management_plan_table">
        <caption> <?php echo $AppUI->_("LBL_RISK_MANAGEMENT_PLAN"); ?> </caption>
        
        <tr>
            <td colspan="2" class="td_section">
                <?php echo $AppUI->_("LBL_PROBABILITY_IMPACT_CONFIGURATION"); ?>
            </td>
        </tr>
        <!-- Tipos de probabilidade -->
        <tr>
            <td colspan="2">
                <table class="tbl" width="100%">
                    <tr>
                        <th colspan="2">
                            <?php echo $AppUI->_("LBL_PROBABILITY"); ?>
                        </th>
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_SUPER_LOW_F"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="probability_super_low" value="<?php echo $obj->probability_super_low ?>" /></td>
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_LOW_F"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="probability_low" value="<?php echo $obj->probability_low ?>"  /></td>
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_MEDIUM_F"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="probability_medium" value="<?php echo $obj->probability_medium ?>" /></td>
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_HIGH_F"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="probability_high" value="<?php echo $obj->probability_high ?>" /></td>
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_SUPER_HIGH_F"); ?></td>
                        <td><input type="text" class="text" maxlength="100"  name="probability_super_high" value="<?php echo $obj->probability_super_high ?>" /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Tipos de impacto -->
        <tr>
            <td colspan="2">
                <table class="tbl" width="100%">
                    <tr>
                        <th colspan="2">
                            <?php echo $AppUI->_("LBL_IMPACT"); ?>
                        </th>
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_SUPER_LOW_M"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="impact_super_low" value="<?php echo $obj->impact_super_low ?>" /></td>     
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_LOW_M"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="impact_low" value="<?php echo $obj->impact_low ?>" /></td>   
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_MEDIUM_M"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="impact_medium" value="<?php echo $obj->impact_medium ?>" /></td>   
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_HIGH_M"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="impact_high" value="<?php echo $obj->impact_high ?>" /></td>   
                    </tr>
                    <tr>
                        <td class="td_label"><?php echo $AppUI->_("LBL_SUPER_HIGH_M"); ?></td>
                        <td><input type="text" class="text" maxlength="100" name="impact_super_high" value="<?php echo $obj->impact_super_high ?>" /></td>   
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Matriz de probabilidade e impacto -->
        <?php

        //options for the expositon factor     
        function insertMatrixOptions($value) {
            global $AppUI;
            $selectedLow = $value == 0 ? "selected=\"true\"" : "";
            $selectedMedium = $value == 1 ? "selected=\"true\"" : "";
            $selectedHigh = $value == 2 ? "selected=\"true\"" : "";
            echo "<option value=\"0\" $selectedLow style=\"color:#006400\">" . $AppUI->_("LBL_LOW_F") . "</option>";
            echo "<option value=\"1\" $selectedMedium style=\"color:#B8860B\">" . $AppUI->_("LBL_MEDIUM_F") . "</option>";
            echo "<option value=\"2\" $selectedHigh style=\"color:#FF0000\">" . $AppUI->_("LBL_HIGH_F") . "</option>";
        }
        ?>
        <tr>
            <td colspan="2" class="td_section">
                <?php echo $AppUI->_("LBL_RISK_MATRIX"); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="tbl" width="100%" id="risk_matrix">
                    <tr>
                        <th colspan="2" rowspan="2">&nbsp;</th>
                        <th colspan="5"><?php echo $AppUI->_("LBL_PROBABILITY"); ?></th>
                    </tr>             
                    <tr>      
                        <th><?php echo $AppUI->_("LBL_SUPER_LOW_M"); ?></th>
                        <th><?php echo $AppUI->_("LBL_LOW_M"); ?></th>
                        <th><?php echo $AppUI->_("LBL_MEDIUM_M"); ?></th>
                        <th><?php echo $AppUI->_("LBL_HIGH_M"); ?></th>
                        <th><?php echo $AppUI->_("LBL_SUPER_HIGH_M"); ?></th>
                    </tr>
                    <tr>
                        <th rowspan="6" width="15"><?php echo $AppUI->_("LBL_IMPACT"); ?></th>
                    </tr>
                    <?php
                    $superlow_superlow = $obj->matrix_superlow_superlow;
                    $superlow_low = $obj->matrix_superlow_low;
                    $superlow_medium = $obj->matrix_superlow_medium;
                    $superlow_high = $obj->matrix_superlow_high;
                    $superlow_superhigh = $obj->matrix_superlow_superhigh;
                    ?>
                    <tr>
                        <th><?php echo $AppUI->_("LBL_SUPER_LOW_F"); ?></th>
                        <td><select name="matrix_superlow_superlow"><?php insertMatrixOptions($superlow_superlow); ?></select></td>
                        <td><select name="matrix_superlow_low"><?php insertMatrixOptions($superlow_low); ?></select></td>
                        <td><select name="matrix_superlow_medium"><?php insertMatrixOptions($superlow_medium); ?></select></td>
                        <td><select name="matrix_superlow_high"><?php insertMatrixOptions($superlow_high); ?></select></td>
                        <td><select name="matrix_superlow_superhigh"><?php insertMatrixOptions($superlow_superhigh); ?></select></td>
                    </tr>

                    <?php
                    $low_superlow = $obj->matrix_low_superlow;
                    $low_low = $obj->matrix_low_low;
                    $low_medium = $obj->matrix_low_medium;
                    $low_high = $obj->matrix_low_high;
                    $low_superhigh = $obj->matrix_low_superhigh;
                    ?>
                    <tr>
                        <th><?php echo $AppUI->_("LBL_LOW_F"); ?></th>
                        <td><select name="matrix_low_superlow"><?php insertMatrixOptions($low_superlow); ?></select></td>
                        <td><select name="matrix_low_low"><?php insertMatrixOptions($low_low); ?></select></td>
                        <td><select name="matrix_low_medium"><?php insertMatrixOptions($low_medium); ?></select></td>
                        <td><select name="matrix_low_high"><?php insertMatrixOptions($low_high); ?></select></td>
                        <td><select name="matrix_low_superhigh"><?php insertMatrixOptions($low_superhigh); ?></select></td>
                    </tr>
                    <?php
                    $medium_superlow = $obj->matrix_medium_superlow;
                    $medium_low = $obj->matrix_medium_low;
                    $medium_medium = $obj->matrix_medium_medium;
                    $medium_high = $obj->matrix_medium_high;
                    $medium_superhigh = $obj->matrix_medium_superhigh;
                    ?>

                    <tr>
                        <th><?php echo $AppUI->_("LBL_MEDIUM_F"); ?></th>
                        <td><select name="matrix_medium_superlow"><?php insertMatrixOptions($medium_superlow); ?></select></td>
                        <td><select name="matrix_medium_low"><?php insertMatrixOptions($medium_low); ?></select></td>
                        <td><select name="matrix_medium_medium"><?php insertMatrixOptions($medium_medium); ?></select></td>
                        <td><select name="matrix_medium_high"><?php insertMatrixOptions($medium_high); ?></select></td>
                        <td><select name="matrix_medium_superhigh"><?php insertMatrixOptions($medium_superhigh); ?></select></td>
                    </tr>

                    <?php
                    $high_superlow = $obj->matrix_high_superlow;
                    $high_low = $obj->matrix_high_low;
                    $high_medium = $obj->matrix_high_medium;
                    $high_high = $obj->matrix_high_high;
                    $high_superhigh = $obj->matrix_high_superhigh;
                    ?>

                    <tr>
                        <th><?php echo $AppUI->_("LBL_HIGH_F"); ?></th>
                        <td><select name="matrix_high_superlow"><?php insertMatrixOptions($high_superlow); ?></select></td>
                        <td><select name="matrix_high_low"><?php insertMatrixOptions($high_low); ?></select></td>
                        <td><select name="matrix_high_medium"><?php insertMatrixOptions($high_medium); ?></select></td>
                        <td><select name="matrix_high_high"><?php insertMatrixOptions($high_high); ?></select></td>
                        <td><select name="matrix_high_superhigh"><?php insertMatrixOptions($high_superhigh); ?></select></td>
                    </tr>

                    <?php
                    $superhigh_superlow = $obj->matrix_superhigh_superlow;
                    $superhigh_low = $obj->matrix_superhigh_low;
                    $superhigh_medium = $obj->matrix_superhigh_medium;
                    $superhigh_high = $obj->matrix_superhigh_high;
                    $superhigh_superhigh = $obj->matrix_superhigh_superhigh;
                    ?>

                    <tr>
                        <th><?php echo $AppUI->_("LBL_SUPER_HIGH_F"); ?></th>
                        <td><select name="matrix_superhigh_superlow"><?php insertMatrixOptions($superhigh_superlow); ?></select></td>
                        <td><select name="matrix_superhigh_low"><?php insertMatrixOptions($superhigh_low); ?></select></td>
                        <td><select name="matrix_superhigh_medium"><?php insertMatrixOptions($superhigh_medium); ?></select></td>
                        <td><select name="matrix_superhigh_high"><?php insertMatrixOptions($superhigh_high); ?></select></td>
                        <td><select name="matrix_superhigh_superhigh"><?php insertMatrixOptions($superhigh_superhigh); ?></select></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="td_section">
                <?php echo $AppUI->_("LBL_RISKS_MONITORING_AND_CONTROLLING_DEFINITIONS"); ?>
            </td>
        </tr>


        <!-- Contingency reserva usage protocol  -->
        <tr> 
            <td class="td_label">
                <label for="risk_contengency_reserve_protocol"><?php echo $AppUI->_("LBL_CONTINGENCY_RESERVE_PROTOCOL"); ?></label><span class="span_mandatory">*</span>:
            </td>
            <td>
                <textarea name="risk_contengency_reserve_protocol" cols="50" rows="4" style="wrap:virtual;" maxlength="255" class="textarea"><?php echo $obj->risk_contengency_reserve_protocol; ?></textarea>
            </td>
        </tr>
        <!-- Risks revision frequency -->
        <tr> 
            <td class="td_label">
                <label for="risk_revision_frequency"><?php echo $AppUI->_("LBL_RISK_REVISION_FREQUENCY"); ?></label><span class="span_mandatory">*</span>:
            </td>
            <td>
                <input type="text" name="risk_revision_frequency" maxlength="3" value="<?php echo $obj->risk_revision_frequency; ?>" />
            </td>
        </tr>
        <!-- RBS - Risks Breakdown Structure -->
        <tr>
            <td colspan="2" class="td_section">
                <?php echo $AppUI->_("LBL_RISK_BREAKDOWN_STRUCTURE"); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> 
                <style>
                    .shortTD{
                        max-width: 28px;
                        width: 28px;
                    }
                    .std caption{
                        text-align: center;
                    }
                </style>
                <input name="eap_items_ids" id="eap_items_ids" type="hidden" />
                <input type="hidden" name="items_ids_to_delete" id="items_ids_to_delete" value="" />	
                <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>" />
                <input type="button" class="button" value="<?php echo $AppUI->__("LBL_ADD"); ?>" onclick="addItem('', '', '', '');" /> 
                <table class="tbl" id="tb_eap" width="95%" align="center" cellpadding="2" cellspacing="1" border="0">
                    <tr>
                        <th class="shortTD"><?php echo $AppUI->_("LBL_ID"); ?> </th>
                        <th width="20"><?php echo $AppUI->_("LBL_RISK_ORDER"); ?></th>
                        <th width="20"><?php echo $AppUI->_("LBL_RISK_INDENTATION"); ?></th>
                        <th ><?php echo $AppUI->_("LBL_EAR_ITEM"); ?></th>
                        <th width="20"> &nbsp; </th>
                    </tr>
                </table>   
                <?php
                $controllerWBSItem = new ControllerWBSItem();
                $items = $controllerWBSItem->getWBSItems($project_id);
                foreach ($items as $item) {
                    echo '<script>addItem(' . $item->getId() . ',"' . $item->getName() . '",0,"' . $item->getIdentation() . '");</script>';
                }
                ?>

                <!-- Insert deafult values for RBS when it is a new risk managenment plan -->
                <?php
                if (sizeof($items) == 0 && $obj->risk_plan_id == "") {
                    $defaultItems = array(
                        //Label, index, indentation level
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_0"),
                            "index" => 0,
                            "indent" => 0
                        ),
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_1"),
                            "index" => 0,
                            "indent" => 1
                        ),
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_2"),
                            "index" => 0,
                            "indent" => 2
                        ),
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_3"),
                            "index" => 0,
                            "indent" => 2
                        ),
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_4"),
                            "index" => 0,
                            "indent" => 1
                        ),
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_5"),
                            "index" => 0,
                            "indent" => 2
                        ),
                        array(
                            "label" => $AppUI->_("LBL_RISK_DEFAULTRBS_6"),
                            "index" => 0,
                            "indent" => 2
                        )
                    );

                    ?>
                    <script>
                        (function() {
                            //Util function for the indent level
                            var str_repeat = function(str, times) {
                                var strReturn = "";
                                for (var i = 0; i < times; i++) {
                                    strReturn += str;
                                }
                                return strReturn;
                            }

                            //Render all the default items
                            var items = <?php echo json_encode($defaultItems); ?>;
                            console.log(items);
                            for (var i in items) {
                                var item = items[i];
                                addItem(i+1, item.label, item.index, str_repeat('&nbsp;&nbsp;&nbsp;', item.indent));
                            }
                        })();
                    </script>
                    <?php
                }
                ?>

            </td>
        </tr>
        <!-- Actions -->
        <tr>
            <td align="right" colspan="2">
                <input type="submit" class="button" value="<?php echo $AppUI->_("LBL_SUBMIT"); ?>" onclick="saveEAP();submitIt()" />
                <script> var targetScreenOnProject="/modules/risks/projects_risks.php";</script>
                <?php require_once (DP_BASE_DIR . "/modules/risks/backbutton.php"); ?>
            </td>
        </tr>
    </table>
</form>
<span class="span_mandatory">*</span>&nbsp;<?php echo $AppUI->_("LBL_REQUIRED_FIELD"); ?>
<script src="./modules/risks/risks.js"></script>
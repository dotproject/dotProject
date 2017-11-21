<?php
$display = "block";
if ($meetingData[meeting_type_id] != 3 && $meetingData[meeting_type_id] != 5) {
    $display = "none";
};
?>
<!-- Include the painels in the PMBOK knowledge areas order 
"scope" => $AppUI->_("LBL_PROJECT_PLAN_SCOPE"),
"time" => $AppUI->_("LBL_PROJECT_TIME"),
"cost" => $AppUI->_("LBL_PROJECT_COSTS"),
"quality" => $AppUI->_("LBL_PROJECT_QUALITY"),
"human_resource" => $AppUI->_("LBL_PROJECT_PROJECT_HUMAN_RESOURCES"),
"comunication" => $AppUI->_("LBL_PROJECT_COMMUNICATION"),
"risk" => $AppUI->_("LBL_PROJECT_RISKS"),
"acquisitions" => $AppUI->_("LBL_PROJECT_ACQUISITIONS"),
"stakeholder" => $AppUI->_("LBL_PROJECT_STAKEHOLDER")
-->
<table class="tbl" id="p1" style="display:<?php echo $display ?>;" >

    <!-- Includes for costs monitoring -->
    <tr>
        <td colspan="2">
            &nbsp;
        </td>
    </tr>
    <tr >
        <th colspan="2" style="font-weight: bold"><?php echo $AppUI->_("LBL_MONITORING_PANEL") . " : " . $AppUI->_("LBL_PROJECT_COSTS") ?></th>
    </tr>
    <tr>
        <td colspan="2">
            <a name="cost_monitoring">&nbsp;</a>
            <?php require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/view/costs_monitoring.php"); ?>
        </td>
    </tr>

    <!-- Includes for risks monitoring -->
    <tr>
        <td colspan="2">
            &nbsp;
        </td>
    </tr>
    <tr >
        <th colspan="2" style="font-weight: bold"><?php echo $AppUI->_("LBL_MONITORING_PANEL") . " : " . $AppUI->_("LBL_PROJECT_RISKS") ?></th>
    </tr>
    <tr>
        <td colspan="2">
            <a name="risk_monitoring">&nbsp;</a>
            <?php require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/view/risks_monitoring.php"); ?>
            <br />
        </td>
    </tr>


</table>
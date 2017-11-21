<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/acquisition/controller_acquisition_planning.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/acquisition/acquisition_planning.class.php");
$controller = new ControllerAcquisitionPlanning();
$list = $controller->getAcquisitionPlanningsPerProject($projectId);
foreach ($list as $object) {
    ?>

    <table class="printTable" >
        <tr>
            <td width="30%" class="labelCell"><?php echo $AppUI->_("LBL_ITEM_TO_ACQUIRE",UI_OUTPUT_HTML); ?></td>
            <td width="70%"><?php echo $object->getItemsToBeAcquired() ?>&nbsp;</td>
        </tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_CONTRACT_TYPE",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $object->getContractType() ?>&nbsp;</td>
        </tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_DOCUMENTS_TO_ACQUIRE",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $object->getDocumentsToAcquisition() ?>&nbsp;</td>
        </tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_CRITERIA_TO_SUPPLIERS_SELECTION",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $object->getCriteriaForSelection() ?>&nbsp;</td>
        </tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_ACQUISITION_ADDITIONAL_REQUIRIMENTS",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $object->getAdditionalRequirements() ?>&nbsp;</td>
        </tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_SUPPLIERS_PROCESSES_MANAGEMENT",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $object->getSupplierManagementProcess() ?>&nbsp;</td>
        </tr> 
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_ACQUISITION_ROLES_RESPONSABILITIES",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $object->getAcquisitionRoles() ?>&nbsp;</td>
        </tr>
    </table>
    <br/>
<?php } ?>
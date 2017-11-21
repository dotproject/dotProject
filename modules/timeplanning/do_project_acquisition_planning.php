<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/acquisition/controller_acquisition_planning.class.php");
$projectId = dPgetParam($_POST, "project_id");
$itemsToBeAcquired = dPgetParam($_POST,"items_to_be_acquired");
$contractType= dPgetParam($_POST,"contract_type");
$documentsToAcquisition = dPgetParam($_POST,"documents_to_acquisition");
$acquisitionRoles= dPgetParam($_POST,"acquisition_roles");
$additionalRequirements = dPgetParam($_POST,"additional_requirements");
$criteriaForSelection= dPgetParam($_POST,"criteria_for_supplier_selection");
$supplierManagementProcess = dPgetParam($_POST,"supplier_management_process");
$id= dPgetParam($_POST,"acquisition_planning_id");

$criteria_to_save = dPgetParam($_POST,"criteria_to_save");
$requirements_to_save = dPgetParam($_POST,"requirements_to_save");
$roles_to_save = dPgetParam($_POST,"roles_to_save");


$criteria_to_delete = dPgetParam($_POST,"criteria_to_delete");
$requirements_to_delete = dPgetParam($_POST,"requirements_to_delete");
$roles_to_delete = dPgetParam($_POST,"roles_to_delete");
$controller  = new ControllerAcquisitionPlanning (); 
$acquisitionId=$controller->sendDataToBeStored($id, $projectId, $acquisitionRoles, $supplierManagementProcess, $itemsToBeAcquired, $documentsToAcquisition, $criteriaForSelection, $contractType, $additionalRequirements);

$controller->deleteRoles($roles_to_delete);
$controller->deleteCriteria($criteria_to_delete);
$controller->deleteRequirements($requirements_to_delete);
$controller->storeRoles($roles_to_save,$acquisitionId);
$controller->storeCriteria($criteria_to_save,$acquisitionId);
$controller->storeRequirements($requirements_to_save,$acquisitionId);

$AppUI->setMsg($AppUI->_("LBL_ACQUISITION_ITEM_REGISTERED",UI_OUTPUT_HTML), UI_MSG_OK);
$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&targetScreenOnProject=/modules/timeplanning/view/acquisition/acquisition_planning.php");
?>

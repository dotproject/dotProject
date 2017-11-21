<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once DP_BASE_DIR . "/modules/risks/risks_management_plan.class.php";
require_once (DP_BASE_DIR . "/modules/risks/controller_wbs_items.class.php");
//add risks sql
$projectSelected = intval(dPgetParam($_POST, "project_id"));
//$risk_plan_id = intval(dPgetParam($_POST, "risk_plan_id", 0));

$obj = new CRisksManagementPlan();
$q = new DBQuery();
$q->addQuery("*");
$q->addTable("risks_management_plan");
$q->addWhere("project_id = " . $projectSelected);
if (!db_loadObject($q->prepare(), $obj)) {
    $obj = new CRisksManagementPlan();
    $obj->_message = "added";
} else {
    $obj->_message = "updated";
}

if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg("LBL_RISK_MANAGEMENT_PLAN_REGISTERED", UI_MSG_OK, true);
if (($msg = $obj->store())) {
    //set id as same as the project
    $AppUI->setMsg($msg, UI_MSG_ERROR);
}

$q = new DBQuery();
$q->addTable("risks_management_plan");
$q->addUpdate("risk_plan_id", $projectSelected);
$q->addWhere("project_id = " . $projectSelected);
$q->exec();


//store ear 
$controllerWBSItem = new ControllerWBSItem();
$eap_items_ids = dPgetParam($_POST, 'eap_items_ids');
if ($eap_items_ids != "") {
    $eap_items_ids = explode(",", $eap_items_ids);
    for ($i = 0; $i < sizeof($eap_items_ids); $i++) {
        $id = $eap_items_ids[$i];
        $description = dPgetParam($_POST, 'description_' . $id, array());
        echo $description;
        $identation = dPgetParam($_POST, 'identation_field_' . $id, array());
        $number = dPgetParam($_POST, 'number_field_' . $id, array());
        $isLeaf = dPgetParam($_POST, 'leaf_field_' . $id, array());
        $controllerWBSItem->insert($projectSelected, $description, $number, $i, $isLeaf, $identation, $id);
    }
}

//delete items
$items_ids_to_delete = dPgetParam($_POST, 'items_ids_to_delete');
if ($items_ids_to_delete != "") {
    $items_ids_to_delete = explode(",", $items_ids_to_delete);
    for ($i = 0; $i < sizeof($items_ids_to_delete); $i++) {
        $controllerWBSItem->delete($items_ids_to_delete[$i]);
    }
}
if ($projectSelected == "") {
    $AppUI->redirect("m=risks");
} else {
    $AppUI->redirect("m=projects&a=view&project_id=" . $projectSelected."&targetScreenOnProject=/modules/risks/projects_risks.php");
}
?>

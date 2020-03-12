<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
//add risks sql
$projectSelected = intval(dPgetParam($_POST, "risk_project"));
$risk_id = intval(dPgetParam($_POST, "risk_id", 0));
$del = intval(dPgetParam($_POST, "del", 0));

$not = dPgetParam($_POST, "notify", "0");
if ($not != "0") {
    $not = "1";
}
$obj = new CRisks();
if ($risk_id) {
    $obj->_message = "updated";
} else {
    $obj->_message = "added";
}

if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}

// delete the item
if ($del) {
    $obj->load($risk_id);
    if (($msg = $obj->delete())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
    } else {
        if ($not == "1") {
            $obj->notify();
        }
        $projectSelected = intval(dPgetParam($_GET, "project_id"));
        $AppUI->setMsg("LBL_RISK_EXCLUDED", UI_MSG_ALERT, true);
    }
    $AppUI->redirect("m=projects&a=view&project_id=" . $projectSelected."&targetScreenOnProject=/modules/risks/projects_risks.php");
   
} else {
    if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
    } else {
        $obj->load($obj->risk_id);
        if ($not == "1") {
            $obj->notify();
        }
        $AppUI->setMsg($risk_id ? "LBL_RISK_UPDATE" : "LBL_RISK_INCLUDED", UI_MSG_OK, true);
    }
   
    $AppUI->redirect("m=projects&a=view&project_id=" . $projectSelected."&targetScreenOnProject=/modules/risks/projects_risks.php"); 
}
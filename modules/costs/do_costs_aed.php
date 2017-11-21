<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//add costs sql
$cost_id = intval(dPgetParam($_POST, 'cost_id', 0));
$del = intval(dPgetParam($_POST, 'del', 0));
$projectSelected = intval(dPgetParam($_POST, 'cost_project_id', 0));
$not = dPgetParam($_POST, 'notify', '0');
if ($not!='0') {
    $not='1';
}
$obj = new CCosts();
if ($cost_id) { 
	$obj->_message = 'updated';
} else {
	$obj->_message = 'added';
}

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

// delete the item
if ($del) {
	$obj->load($cost_id);
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		if ($not=='1') {
                    $obj->notify();
                }
                
                if ($projectSelected != null) {                    
                    $AppUI->redirect("m=projects&a=view&project_id=" . $projectSelected."&tab=1&targetScreenOnProject=/modules/costs/view_costs.php");
                }
                $AppUI->setMsg("deleted", UI_MSG_ALERT, true);
	}
}

if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
        $obj->load($obj->cost_id);
        if ($not=='1') {
        $obj->notify();
        }
        $AppUI->setMsg("LBL_DATA_SUCCESSFULLY_PROCESSED", UI_MSG_OK, true);
}
$pageReturn=$_POST["cost_type_id"]!="1"?"addedit_costs":"addedit_costs_not_human";
$AppUI->redirect("m=costs&a=".$pageReturn."&cost_id=".$obj->cost_id."&project_id=".$projectSelected);
?>
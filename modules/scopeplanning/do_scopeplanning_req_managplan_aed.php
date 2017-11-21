<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$req_managplan_id = intval(dPgetParam($_POST, 'req_managplan_id', 0));
$del = intval(dPgetParam($_POST, 'del', 0));

$not = dPgetParam($_POST, 'notify', '0');
if ($not!='0') {
    $not='1';
}
$obj = new CReqManagPlan();
if ($req_managplan_id) { 
	$obj->_message = 'updated';
} else {
	$obj->_message = 'added';
}

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('LBL_SP_REQMANAGPLAN');
// delete the item
if ($del) {
	$obj->load($req_managplan_id);
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		if ($not=='1') {
                    $obj->notify();
                }
                $projectSelected = intval(dPgetParam($_GET, 'project_id'));
                if ($projectSelected != null) {                    
                    $AppUI->redirect("m=scopeplanning");
                }
                $AppUI->setMsg("deleted", UI_MSG_ALERT, true);
	}
}

if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
        $obj->load($obj->req_managplan_id);
        if ($not=='1') {
        $obj->notify();
        }
        $AppUI->setMsg($req_managplan_id ? 'LBL_SP_UPDATED' : 'LBL_SP_ADDED1', UI_MSG_OK, true);
}

$AppUI->redirect();
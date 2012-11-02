<?php /* SYSTEM $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$del = dPgetParam($_POST, 'del');
$edit = dPgetCleanParam($_POST,'edit');

$obj = new bcode();
$obj->_billingcode_id = (int)dPgetParam($_POST, 'billingcode_id', 0);

$company_id = (int)dPgetParam($_REQUEST, 'company_id', 0);


// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('Billing Codes');
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
	}
} else {
	if ($edit) {
		$obj->_billingcode_id = $edit;
	}
	
	$obj->billingcode_value = dPgetCleanParam($_REQUEST,'billingcode_value');
	$obj->billingcode_name = dPgetCleanParam($_REQUEST,'billingcode_name');
	$obj->billingcode_desc = dPgetCleanParam($_REQUEST,'billingcode_desc');
	$obj->company_id = $company_id;
	
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$AppUI->setMsg('updated', UI_MSG_OK, true);
	}
}

$AppUI->redirect('m=system&a=billingcode&company_id='.$company_id);

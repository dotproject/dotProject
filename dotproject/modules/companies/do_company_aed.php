<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$del = dPgetParam($_POST, 'del', 0);
$obj = new CCompany();
$msg = '';

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

require_once($AppUI->getSystemClass('CustomFields'));

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('Company');
if ($del) {
	if (!$obj->canDelete($msg)) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	}
	
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
		$AppUI->redirect('m=companies');
	}
} 
else {
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
 		$custom_fields = New CustomFields($m, 'addedit', $obj->company_id, 'edit');
 		$custom_fields->bind($_POST);
 		$sql = $custom_fields->store($obj->company_id); // Store Custom Fields
		$AppUI->setMsg(((@$_POST['company_id']) ? 'updated' : 'added'), UI_MSG_OK, true);
	}
	$AppUI->redirect();
}
?>

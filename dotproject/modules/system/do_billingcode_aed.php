<?php /* SYSTEM $Id$ */

$del = isset($_POST['del']) ? $_POST['del'] : 0;
$edit = isset($_POST['edit']) ? $_POST['edit'] : 0;

$obj = new bcode();
$obj->_billingcode_id = isset($_POST['billingcode_id']) ? $_POST['billingcode_id'] : 0;


// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Billing Codes' );
if ($del) {
	if (($msg = $obj->delete()))
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	else
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
} else {
	if ($edit)
		$obj->_billingcode_id = $edit;
	
	$obj->billingcode_value=$_REQUEST['billingcode_value'];
	$obj->billingcode_name=$_REQUEST['billingcode_name'];
	$obj->billingcode_desc=$_REQUEST['billingcode_desc'];
	$obj->company_id=$_REQUEST['company_id'];
	
	if (($msg = $obj->store()))
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	else
		$AppUI->setMsg('updated', UI_MSG_OK, true);
}

$AppUI->redirect('m=system&a=billingcode&company_id='.$_REQUEST['company_id']);
?>
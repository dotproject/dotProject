<?php /* PROJECTS $Id: do_project_aed.php 4779 2007-02-21 14:53:28Z cyberhorse $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

$obj = new CClosure();
$msg = '';

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

require_once(DP_BASE_DIR . '/classes/CustomFields.class.php');

$del = dPgetParam( $_POST, 'del', 0 );

// prepare (and translate) the module name ready for the suffix
if ($del) {
	$project_id = dPgetParam($_POST, 'pma_id', 0);
	$canDelete = $obj->canDelete($msg, $pma_id);
	if (!$canDelete) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( 'Post Mortem deleted', UI_MSG_ALERT);
		$AppUI->redirect( 'm=closure' );
	}
} else {
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['pma_id'];
 		$custom_fields = New CustomFields( $m, 'addedit', $obj->pma_id, "edit" );
 		$custom_fields->bind( $_POST );
 		$sql = $obj->store( $obj->pma_id ); // Store Custom Fields

		$AppUI->setMsg( $isNotNew ? 'Post Mortem updated' : 'Post Mortem inserted', UI_MSG_OK);
	}
	$AppUI->redirect();
}
?>

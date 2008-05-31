<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$del = isset($_REQUEST['del']) ? $_REQUEST['del'] : FALSE;

$perms =& $AppUI->acl();

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Roles' );

if ($del) {
	if (!$perms->checkModule($m, 'delete')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	if ($perms->deleteUserRole($_REQUEST['role_id'], $_REQUEST['user_id'])) {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "failed to delete role", UI_MSG_ERROR );
		$AppUI->redirect();
	}
	return;
}

if (isset($_REQUEST['user_role']) && $_REQUEST['user_role']) {
	if (! $perms->checkModule($m, 'edit') || ! $perms->checkModule($m, 'add')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	if ( $perms->insertUserRole($_REQUEST['user_role'], $_REQUEST['user_id'])) {
		$AppUI->setMsg( "added", UI_MSG_ALERT, true );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "failed to add role", UI_MSG_ERROR );
		$AppUI->redirect();
	}
}
?>

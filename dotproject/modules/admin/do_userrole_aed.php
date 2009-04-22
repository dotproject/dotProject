<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$del = dPgetParam($_POST, 'del', false);
$role_id = dPgetParam($_POST, 'role_id', 0);
$user_id = dPgetParam($_POST, 'user_id', 0);
$user_role = dPgetParam($_POST, 'user_role', 0);

if (!(getPermission($m, 'edit', $user_id))) {
	$AppUI->redirect('m=public&a=access_denied');
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('Role');
$perms =& $AppUI->acl();
if ($del) {
	if ($perms->deleteUserRole($role_id, $user_id)) {
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
	} else {
		$AppUI->setMsg('failed to delete role', UI_MSG_ERROR);
	}
} else if ($user_role) {
	if ($perms->insertUserRole($user_role, $user_id)) {
		$AppUI->setMsg('added', UI_MSG_OK, true);
	} else {
		$AppUI->setMsg('failed to add role', UI_MSG_ERROR);
	}
}
$AppUI->redirect();

?>

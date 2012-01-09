<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once $AppUI->getModuleClass('contacts');
$del = (bool)dPgetParam($_POST, 'del', false);
$role_id = (int)dPgetParam($_POST, 'role_id', 0);
$user_id = (int)dPgetParam($_POST, 'user_id', 0);
$user_role = (int)dPgetParam($_POST, 'user_role', 0);

if (!(getPermission($m, 'edit', $user_id))) {
	$AppUI->redirect('m=public&a=access_denied');
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('Role');
$perms =& $AppUI->acl();
if ($del) {
	if ($perms->deleteUserRole($role_id, $user_id)) {
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
		if (dPgetConfig('user_contact_inactivate') && ! $perms->checkLogin($user_id)) {
			// Mark contact as private
			$obj = new CUser();
			$contact = new CContact();
			$obj->load($user_id);
			if ($contact->load($obj->user_contact)) {
				$contact->contact_private = 1;
				$contact->store();
			}
		}
	} else {
		$AppUI->setMsg('failed to delete role', UI_MSG_ERROR);
	}
} else if ($user_role) {
	$public_contact = false;
	if (dPgetConfig('user_contact_activate') && ! $perms->checkLogin($user_id)) {
		$public_contact = true;
	}
	if ($perms->insertUserRole($user_role, $user_id)) {
		$AppUI->setMsg('added', UI_MSG_OK, true);
		if ($public_contact) {
			// Mark contact as public
			$obj = new CUser();
			$contact = new CContact();
			$obj->load($user_id);
			if ($contact->load($obj->user_contact)) {
				$contact->contact_private = 0;
				$contact->store();
			}
		}
	} else {
		$AppUI->setMsg('failed to add role', UI_MSG_ERROR);
	}
}
$AppUI->redirect();

?>

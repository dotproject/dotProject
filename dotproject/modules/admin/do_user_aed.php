<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

require_once $AppUI->getSystemClass('libmail');
include $AppUI->getModuleClass('contacts');
$del = dPgetParam($_REQUEST, 'del', false);
$user_id_aed = intval(dPgetParam($_REQUEST, 'user_id', 0));

$obj = new CUser();
$contact = new CContact();

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}
if (!$contact->bind($_POST)) {
	$AppUI->setMsg($contact->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}
$obj->user_username = mb_strtolower($obj->user_username);
$isNewUser = !($user_id_aed);

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('User');

// !User's contact information not deleted - left for history.
if ($del) {
	if (! getPermission('admin', 'delete') 
	    || !(getPermission('users', 'delete', $user_id_aed))) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
	}
	$AppUI->redirect();
	return;
} else if ($isNewUser) {
	if (!(getPermission('admin', 'add') && getPermission('users', 'add'))) {
		$AppUI->redirect('m=admin&a=access_denied');
	}
	
	//pull a list of existing usernames
	$q = new DBQuery;
	$q->addTable('users','u');
	$q->addQuery('user_username');
	$q->addWhere("user_username like '{$obj->user_username}'");
	$userEx = $q->loadResult();
	
	// If userName already exists quit with error and do nothing
	if ($userEx) {
		$AppUI->setMsg('already exists. Try another username.', UI_MSG_ERROR, true);
		$AppUI->redirect();
	}
	
	$contact->contact_owner = $AppUI->user_id;
} else if (! getPermission('admin', 'edit') || ! getPermission('users', 'edit', $user_id_aed)) {
	$AppUI->redirect('m=public&a=access_denied');
}

if (($msg = $contact->store())) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
} else {        
	$obj->user_contact = $contact->contact_id;
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		if ($isNewUser && $_POST['send_user_mail']) {
			notifyNewUser($contact->contact_email, $contact->contact_first_name, 
			              $obj->user_username, $_POST['user_password']);
		}
		if (isset($_POST['user_role']) && $_POST['user_role']) {
			$perms =& $AppUI->acl();
			if ($perms->insertUserRole($_POST['user_role'], $obj->user_id)) {
				$AppUI->setMsg('', UI_MSG_ALERT, true);
			} else {
				$AppUI->setMsg('failed to add role', UI_MSG_ERROR);
			}
		}
		$AppUI->setMsg($isNewUser ? 'added' : 'updated', UI_MSG_OK, true);
	}
	$AppUI->redirect(($isNewUser ? ('m=admin&a=viewuser&user_id=' . $obj->user_id . '&tab=3') : ''));
}

function notifyNewUser($address, $username, $logname, $logpwd) {
	global $AppUI, $dPconfig;
	$mail = new Mail;
	if ($mail->ValidEmail($address)) {
		if ($mail->ValidEmail($AppUI->user_email)) {
			$email = $AppUI->user_email;
		} else {
			$email = 'dotproject@' . $AppUI->cfg['site_domain'];
		}
		
		$name = $AppUI->user_first_name .' ' . $AppUI->user_last_name;
		$body = $username.',
		
An access account has been created for you in our dotProject project management system.

You can access it here at ' . $dPconfig['base_url'] . '

Your username is: ' . $logname . '
Your password is: ' . $logpwd .'

This account will allow you to see and interact with projects. If you have any questions please contact us.';
		
		$mail->From('"'.$name.'" <'.$email.'>');
		$mail->To($address);
		$mail->Subject('New Account Created - dotProject Project Management System');
		$mail->Body($body);
		$mail->Send();
	}
}
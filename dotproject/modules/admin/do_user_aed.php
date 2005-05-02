<?php /* ADMIN $Id$ */
include $AppUI->getModuleClass('contacts');
$del = isset($_REQUEST['del']) ? $_REQUEST['del'] : FALSE;

$obj = new CUser();
$contact = new CContact();

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
if (!$contact->bind( $_POST )) {
	$AppUI->setMsg( $contact->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
        

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'User' );

// !User's contact information not deleted - left for history.
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( '', -1 );
	}
	return;
}
	$isNewUser = !($_REQUEST['user_id']);
	if ( $isNewUser ) {
		// check if a user with the param Username already exists
		$userEx = FALSE;

		function userExistence( $userName ) {
			global $obj, $userEx;
			if ( $userName == $obj->user_username ) {
				$userEx = TRUE;
			}
		}

		//pull a list of existing usernames
		$sql = "SELECT user_username FROM users";
		$q  = new DBQuery;
		$q->addTable('users','u');
		$q->addQuery('user_username');
		$users = $q->loadList();

		// Iterate the above userNameExistenceCheck for each user
		foreach ( $users as $usrs ) {
			$usrLst = array_map( "userExistence", $usrs );
		}
		// If userName already exists quit with error and do nothing
		if ( $userEx == TRUE ) {
			$AppUI->setMsg( "already exists. Try another username.", UI_MSG_ERROR, true );
			$AppUI->redirect( );
		}

		$contact->contact_owner = $AppUI->user_id;
	}

        if (($msg = $contact->store())) {
                $AppUI->setMsg( $msg, UI_MSG_ERROR );
        }
	else {
        
        $obj->user_contact = $contact->contact_id;
        if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( $isNewUser ? 'added - please setup roles and permissions now.  User must have at least one role to log in.' : 'updated', UI_MSG_OK, true );
	}
        }
	($isNewUser)?$AppUI->redirect("m=admin&a=viewuser&user_id=". $obj->user_id . "&tab=3"):$AppUI->redirect();

?>

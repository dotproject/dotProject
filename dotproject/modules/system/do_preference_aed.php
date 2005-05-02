<?php /* SYSTEM $Id$ */
$del = isset($_POST['del']) ? $_POST['del'] : 0;

$obj = new CPreferences();
$obj->pref_user = isset($_POST['pref_user']) ? $_POST['pref_user'] : 0;

foreach ($_POST['pref_name'] as $name => $value) {
	$obj->pref_name = $name;
	$obj->pref_value = $value;

	// prepare (and translate) the module name ready for the suffix
	$AppUI->setMsg( 'Preferences' );
	if ($del) {
		if (($msg = $obj->delete())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
		} else {
			$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		}
	} else {
		if (($msg = $obj->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
		} else {
			if ($obj->pref_user) {
			// if user preferences, reload them now
				$AppUI->loadPrefs( $AppUI->user_id );
				$AppUI->setUserLocale();
				include_once dPRealPath( "./locales/$AppUI->user_locale/locales.php" );
				include dPRealPath( "./locales/core.php" );
				$AppUI->setMsg( 'Preferences' );
			}
			$AppUI->setMsg( "updated", UI_MSG_OK, true );
		}
	}
}
$AppUI->redirect();
?>

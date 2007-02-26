<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

ob_start();
	@readfile( DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/common.inc' );
	
// language files for specific locales and specific modules (for external modules) should be 
// put in modules/[the-module]/locales/[the-locale]/[the-module].inc
// this allows for module specific translations to be distributed with the module
	
	if ( file_exists( DP_BASE_DIR.'/modules/'.$m.'/locales/'.$AppUI->user_locale.'.inc' ) )
	{
		@readfile( DP_BASE_DIR.'/modules/'.$m.'/locales/'.$AppUI->user_locale.'.inc' );
	}
	else
	{
		@readfile( DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/'.$m.'.inc' );
	}
	
	switch ($m) {
	case 'departments':
		@readfile( DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/companies.inc' );
		break;
	case 'system':
		@readfile( DP_BASE_DIR.'/locales/'.$dPconfig['host_locale'].'/styles.inc' );
		break;
	}
	eval( '$GLOBALS[\'translate\']=array('.ob_get_contents()."\n'0');" );
ob_end_clean();
?>

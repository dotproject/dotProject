<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
//Start the output buffer
ob_start();

//Read the locale file
if ( file_exists( DP_BASE_DIR.'/modules/risks/locales/'.$AppUI->user_locale.'.inc' ) )
{
    @readfile( DP_BASE_DIR.'/modules/risks/locales/'.$AppUI->user_locale.'.inc' );
}

//Parse the file
$module_locale = eval('return array('.ob_get_contents()."\n'0');");
ob_end_clean();

//Add the locale to the global array
$GLOBALS['translate'] = array_merge ( $GLOBALS['translate'], $module_locale );
?>
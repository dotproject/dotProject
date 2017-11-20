<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

// user types
$utypes = dPgetSysVal('UserType');

##
##	NOTE: the user_type field in the users table must be changed to a TINYINT
##
?>
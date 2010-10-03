<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
* users Class
*/
class users extends smartsearch 
{
	var $table = 'users';
	var $table_module = 'admin';
	var $table_key = 'user_id';
	var $table_link = '?m=admin&amp;a=viewuser&amp;user_id=';
	var $table_title = 'Users';
	var $table_orderby = 'user_username';
	var $search_fields = array ('user_username', 'user_signature');
	var $display_fields = array ('user_username', 'user_signature');
	
	function cusers () {
		return new users();
	}
}
?>

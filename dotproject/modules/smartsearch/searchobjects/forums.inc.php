<?php /* SMARTSEARCH$Id$ */		
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
* forums Class
*/
class forums extends smartsearch 
{
	var $table = 'forums';
	var $table_module = 'forums';
	var $table_key = 'forum_id';
	var $table_link = 'index.php?m=forums&a=viewer&forum_id=';
	var $table_title = 'Forums';
	var $table_orderby = 'forum_name';
	var $search_fields = array('forum_name', 'forum_description');
	var $display_fields = array('forum_name', 'forum_description');

	function cforums (){
		return new forums();
	}
}
?>

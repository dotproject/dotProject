<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
* tickets Class
*/
class tickets extends smartsearch 
{
	var $table = 'tickets';
	var $table_module = 'ticketsmith';
	var $table_key = 'ticket';
	var $table_link = '?m=ticketsmith&amp;a=view&amp;ticket=';
	var $table_title = 'Tickets';
	var $table_orderby = 'subject';
	var $search_fields = array('author', 'recipient', 'subject', 'type', 'cc', 'body', 'signature');
	var $display_fields = array('author', 'recipient', 'subject', 'type', 'cc', 'body', 
	                            'signature');

	function ctickets () {
		return new tickets();
	}
}
?>

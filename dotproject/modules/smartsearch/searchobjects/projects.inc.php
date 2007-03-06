<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
* projects Class
*/
class projects extends smartsearch {
	var $table = "projects";
	var $table_alias = "p";
	var $table_module	= "projects";
	var $table_key = "p.project_id";
	var $table_link = "index.php?m=projects&a=view&project_id=";
	var $table_title = "Projects";
	var $table_orderby = "p.project_name";
	var $search_fields  = array ('p.project_name','p.project_short_name','p.project_description','p.project_url','p.project_demo_url', 'con.contact_last_name', 'con.contact_first_name', 'con.contact_email', 'con.contact_title', 'con.contact_email2', 'con.contact_phone', 'con.contact_phone2', 'con.contact_address1', 'con.contact_notes');
	var $display_fields = array ('p.project_name','p.project_short_name','p.project_description','p.project_url','p.project_demo_url', 'con.contact_last_name', 'con.contact_first_name', 'con.contact_email', 'con.contact_title', 'con.contact_email2', 'con.contact_phone', 'con.contact_phone2', 'con.contact_address1', 'con.contact_notes');
	var $table_joins = array (
                              array ('table' => 'project_contacts','alias' => 'pc','join' => 'p.project_id = pc.project_id'),
                              array ('table'=>'contacts','alias'=>'con','join'=>'pc.contact_id = con.contact_id')
                        );

	function cprojects (){
		return new projects();
	}
}
?>

<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
 *  Name: Links
 *  Directory: links
 *  Version 1.0
 *  Type: user
 *  UI Name: Links
 *  UI Icon: ?
 */

$config = array();
$config['mod_name'] = 'Links';               // name the module
$config['mod_version'] = '1.0';               // add a version number
$config['mod_directory'] = 'links';          // tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupLinks';  // the name of the PHP setup class (used below)
$config['mod_type'] = 'user';                   // 'core' for modules distributed with dP by standard, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Links';            // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'communicate.gif';     // name of a related icon
$config['mod_description'] = 'Links related to tasks';     // some description of the module
$config['mod_config'] = false;                   // show 'configure' link in viewmods


if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

// TODO: To be completed later as needed.
class CSetupLinks {

  function configure() { return true; }

  function remove() { 
  	$q = new DBQuery();
  	$q->dropTable('links');
  	$q->exec();
  	
  	$q->clear();
  	$q->setDelete('sysvals');
  	$q->addWhere('sysval_title = \'LinkType\'');
  	$q->exec();
 }
  
  function upgrade($old_version) { return true; }

  function install() {
  	$q = new DBQuery();
  	$q->createTable('links');
  	$q->createDefinition("(
`link_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`link_url` varchar( 255 ) NOT NULL default '',
`link_project` int( 11 ) NOT NULL default '0',
`link_task` int( 11 ) NOT NULL default '0',
`link_name` varchar( 255 ) NOT NULL default '',
`link_parent` int( 11 ) default '0',
`link_description` text,
`link_owner` int( 11 ) default '0',
`link_date` datetime default NULL ,
`link_icon` varchar( 20 ) default 'obj/',
`link_category` int( 11 ) NOT NULL default '0',
PRIMARY KEY ( `link_id` ) ,
KEY `idx_link_task` ( `link_task` ) ,
KEY `idx_link_project` ( `link_project` ) ,
KEY `idx_link_parent` ( `link_parent` ) 
) TYPE = MYISAM ");

	$q->exec($sql);

	$q->clear();
	$q->addTable('sysvals');
	$q->addInsert('sysval_key_id', 1);
	$q->addInsert('sysval_title', 'LinkType');
	$q->addInsert('sysval_value', "0|Unknown\n1|Document\n2|Application");
	$q->exec();
	
	return NULL;
 }

}
?>

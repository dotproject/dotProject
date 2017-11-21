<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
 *  Name: Stakeholder
 *  Directory: stakeholder
 *  Version 1.0
 *  Type: user
 *  UI Name: Stakeholder
 *  UI Icon: ?
 */

$config = array();
$config['mod_name'] = 'Stakeholder'; // name the module
$config['mod_version'] = '1.0'; // add a version number
$config['mod_directory'] = 'stakeholder'; // tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupStakeholder'; // the name of the PHP setup class (used below)
$config['mod_type'] = 'user'; //'core' for standard dP modules, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Stakeholder'; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'applet3-48.png'; // name of a related icon
$config['mod_description'] = 'Initiating process group implementation'; // some description of the module
$config['mod_config'] = false; // show 'configure' link in viewmods


if (@$a == 'setup') {
	echo dPshowModuleConfig($config);
}

// TODO: To be completed later as needed.
class CSetupStakeholder {

  function configure() { return true; }

  function remove() { 
  	$q = new DBQuery();
  	$q->dropTable('initiating_stakeholder');
  	$q->exec();
 }
  
  function upgrade($old_version) {
	// Place to put upgrade logic, based on the previously installed version.
	// Usually handled via a switch statement. 
	// Since this is the first version of this module, we have nothing to update.
	return true;
  }

  function install() {
	$q = new DBQuery();
  	$q->createTable('initiating_stakeholder');
  	$q->createDefinition("(
  `initiating_stakeholder_id` int(11) NOT NULL AUTO_INCREMENT,
  `initiating_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `stakeholder_responsibility` varchar(100) DEFAULT NULL,
  `stakeholder_interest` varchar(100) DEFAULT NULL,
  `stakeholder_power` varchar(100) DEFAULT NULL,
  `stakeholder_strategy` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`initiating_stakeholder_id`) 
) ");

	$q->exec($sql);
  	
	return NULL;
 }
}

<?php

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'SmartSearch';
$config['mod_version'] = '1.0';
$config['mod_directory'] = 'smartsearch';
$config['mod_setup_class'] = 'SSearch';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'SmartSearch';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'A module for search keywords';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class SSearch {   

	function install() {
		return null;
	}
	
	function remove() {
		return null;
	}
	
	function upgrade() {
		return null;
	}
}

?>	
	

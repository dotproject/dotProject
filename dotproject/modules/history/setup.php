<?php
/*
 * Name:      History
 * Directory: history
 * Version:   0.1
 * Class:     user
 * UI Name:   History
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'History';
$config['mod_version'] = '0.3';
$config['mod_directory'] = 'history';
$config['mod_setup_class'] = 'CSetupHistory';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'History';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'A module for tracking changes';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupHistory {   

	function install() {
		$sql = " ( " .
		  "history_id int(10) unsigned NOT NULL auto_increment," .
		  "history_user int(10) NOT NULL default '0'," .
                  "history_action varchar(10) NOT NULL default 'modify', " .
                  "history_item int(10) NOT NULL," .
		  "history_table varchar(15) NOT NULL default ''," .
		  "history_project int(10) NOT NULL default '0'," .
		  "history_date datetime NOT NULL default '0000-00-00 00:00:00'," .
		  "history_description text," .
		  "PRIMARY KEY  (history_id)," .
		  "UNIQUE KEY history_id (history_id)" .
		  ") TYPE=MyISAM";
		$q = new DBQuery;
		$q->createTable('history');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	function remove() {
		$q = new DBQuery;
		$q->dropTable('history');
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	function upgrade($old_version) {
		$q = new DBQuery;
		switch ($old_version) {
			case '0.1':
				$q->alterTable('history');
				$q->addField('history_table', 'varchar(15) NOT NULL default \'\'');
				$q->addField('history_action', 'varchar(10) NOT NULL default \'modify\'');
				$q->dropField('history_module');
				$q->exec();
				$q->clear();
			case '0.2':
				$q->alterTable('history');
				$q->addField('history_item', 'int(10) NOT NULL');
				$q->exec();
				$q->clear();
			case '0.3':
				break;
		}
		return db_error();
	}
}

?>

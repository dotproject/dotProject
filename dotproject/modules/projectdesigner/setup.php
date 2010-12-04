<?php

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'ProjectDesigner';
$config['mod_version'] = '1.0';
$config['mod_directory'] = 'projectdesigner';
$config['mod_setup_class'] = 'projectDesigner';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'ProjectDesigner';
$config['mod_ui_icon'] = 'projectdesigner.jpg';
$config['mod_description'] = 'A module to design projects';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class projectDesigner {   

	function install() {
		$dbprefix = dPgetConfig('dbprefix', '');
		$success = 1;
		
		$bulk_sql[] = "
                  CREATE TABLE `{$dbprefix}project_designer_options` (
                    `pd_option_id` INT(11) NOT NULL auto_increment,
                    `pd_option_user` INT(11) NOT NULL default 0 UNIQUE,
                    `pd_option_view_project` INT(1) NOT NULL default 1,
                    `pd_option_view_gantt` INT(1) NOT NULL default 1,
                    `pd_option_view_tasks` INT(1) NOT NULL default 1,
                    `pd_option_view_actions` INT(1) NOT NULL default 1,
                    `pd_option_view_addtasks` INT(1) NOT NULL default 1,
                    `pd_option_view_files` INT(1) NOT NULL default 1,
                    PRIMARY KEY (`pd_option_id`) 
                  );";
            foreach ($bulk_sql as $s) {
                  db_exec($s);
                  
                  if (db_error()) {
                        $success = 0;
                  }
            }      
		return $success;
	}
	
	function remove() {
		$dbprefix = dPgetConfig('dbprefix', '');
		$success = 1;

		$bulk_sql[] = "DROP TABLE `{$dbprefix}project_designer_options`";
		foreach ($bulk_sql as $s) {
			db_exec($s);
			if (db_error())
				$success = 0;
		}
		return $success;
	}
	
	function upgrade() {
		return null;
	}
	
      function configure() {
            global $AppUI;
      
            $AppUI->redirect("m=projectdesigner&a=configure");
      
            return true;
      }
	
}

?>

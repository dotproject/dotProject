<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Scope and Schedule';
$config['mod_version'] = '1.0';
$config['mod_directory'] = 'scope_and_schedule';
$config['mod_setup_class'] = 'CSetup_ScopeSchedule';
$config['mod_type'] = 'user';
$config['mod_config'] = false;
$config['mod_ui_name'] = 'Scope and Schedule';
$config['mod_ui_icon'] = 'applet3-48.png';
$config['mod_description'] = "Supports scope and schedule management.";

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class CSetup_ScopeSchedule {

    function install() {
        $this->createTables();
        //$this->updateTranslationFiles(); 
             
    }

    private function createTables() {	
		//create wbs table
		$q = new DBQuery();
		
        $sql = "(
		  id INT NOT NULL auto_increment,
		  project_id INT default NULL,
		  sort_order FLOAT default 1,
		  item_name text  default '',
		  number text  default '',
		  is_leaf INT  default 0,
		  id_wbs_item_parent INT default 0,
		  wbs_dictionary TEXT default NULL,
		  PRIMARY KEY  (id),
		  CONSTRAINT fk_wbs_item_project FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects (project_id) on delete cascade on update cascade
		)";
		
		
        $q->createTable("project_wbs_items");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

		
		
        //table to store tasks x work packages relationhip
        $sql = "(
		  task_id INT,
		  wbs_item_id INT,
          activity_order FLOAT NULL DEFAULT 0, 
		  PRIMARY KEY  (task_id, wbs_item_id),
          CONSTRAINT fk_task_eap_item FOREIGN KEY (task_id) REFERENCES " . $q->_table_prefix . "tasks (task_id) on delete cascade on update cascade
		)  ";
        $q = new DBQuery();
        $q->createTable("project_wbs_tasks");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
		//die("created table");
		
		/*
        //table for reports estimations (tasks minutes)
        $q = new DBQuery();
        $sql = "(
		  id INT NOT NULL auto_increment,
		  project_id INT default NULL,
		  minute_date datetime default NULL,
		  description text  default '',
		  isEffort INT default 0,
		  isDuration INT default 0,
		  isResource INT  default 0,
		  isSize INT  default 0,
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_minute_project FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects (project_id) on delete cascade on update restrict
		)";

        $q->createTable("project_minutes");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //table for participants members on tasks minutes
        $sql = "(
		  id INT NOT NULL auto_increment,
		  user_id INT default NULL,
		  task_minute_id INT  default NULL,
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_task_minute_partipant_task FOREIGN KEY (task_minute_id) REFERENCES " . $q->_table_prefix . "project_minutes (id) on delete cascade on update restrict,
                  CONSTRAINT fk_task_minute_partipant_user FOREIGN KEY (user_id) REFERENCES " . $q->_table_prefix . "contacts (contact_id) on delete no action on update no action
		) ";

        $q = new DBQuery();
        $q->createTable("task_minute_members");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();


        //table to store company roles
        $sql = "(
		  id INT NOT NULL auto_increment,
		  company_id INT default NULL,
		  sort_order INT default NULL,
		  role_name text  default '',
		  identation text  default '',
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_role_company FOREIGN KEY (company_id) REFERENCES " . $q->_table_prefix . "companies (company_id) on delete cascade on update restrict
		) ";

        $q = new DBQuery();
        $q->createTable('company_role');
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //table to store task  positions on mdp diagrams
        $sql = "(
		  id INT NOT NULL auto_increment,
		  task_id INT default NULL,
		  pos_x INT default NULL,
		  pos_y INT default NULL,
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_mdp_task FOREIGN KEY (task_id) REFERENCES " . $q->_table_prefix . "tasks (task_id) on delete cascade on update restrict
		) ";

        $q = new DBQuery();
        $q->createTable('tasks_mdp');
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //tables for tasks estimations effort/duration
        $sql = "(
		  id INT NOT NULL auto_increment,
		  task_id INT default NULL,
		  effort float default NULL,
		  effort_unit text  default NULL,
		  duration float  default NULL,
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_estimation_task_attributes FOREIGN KEY (task_id) REFERENCES " . $q->_table_prefix . "tasks(task_id) on delete cascade on update restrict
		) ";
        $q = new DBQuery();
        $q->createTable("project_tasks_estimations");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //tables for tasks estimations resources roles
        $sql = "(
		  id INT NOT NULL auto_increment,
		  task_id INT default NULL,
		  role_id INT default NULL,
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_estimation_task_roles FOREIGN KEY (task_id) REFERENCES " . $q->_table_prefix . "tasks (task_id) on delete cascade on update restrict,
                  CONSTRAINT fk_estimation_roles FOREIGN KEY (role_id) REFERENCES " . $q->_table_prefix . "company_role (id) on delete cascade on update restrict
		)  ";
        $q = new DBQuery();
        $q->createTable("project_tasks_estimated_roles");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //tables for eap item estimations for size
        $sql = "(
		  id INT NOT NULL auto_increment,
		  eap_item_id INT default NULL,
		  size float default NULL,
		  size_unit text  default NULL,
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_estimation_eap_item FOREIGN KEY(eap_item_id) REFERENCES " . $q->_table_prefix . "project_eap_items (id) on delete cascade on update restrict
		) ";
        $q = new DBQuery();
        $q->createTable("eap_item_estimations");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
		*/
    }

    private function updateTranslationFiles() {
        //update translations
        //1. Define translations values
        $portuguese = file_get_contents(DP_BASE_DIR . "\\modules\\timeplanning\\locales\\pt_br.inc");
        $english = file_get_contents(DP_BASE_DIR . "\\modules\\timeplanning\\locales\\en_US.inc");

        //2. Define file names
    
        $fileCommonEn = DP_BASE_DIR . '\locales\en\common.inc';
        $fileCommonBr = DP_BASE_DIR . '\locales\pt_br\common.inc';
        //3. Update translations files


        $this->updateFile($fileCommonEn, $english);
        $this->updateFile($fileCommonBr, $portuguese);
    }

    private function updateFile($fileName, $content) {
        if (!file_exists($fileName)) {
            $fileName = str_replace("\\", "/", $fileName);
        }
        $actualContent = file_get_contents($fileName);
        //just append the translation content whether it wasn't appeded before.
        if (!strstr($actualContent, $content)) {
            $fp = fopen($fileName, "a");
            fwrite($fp, $content);
            fclose($fp);
        }
    }

    function remove() {
        //return true; // Isn't necessary delete table data. It can be reused later in a next install.

        $q = new DBQuery();
        $q->dropTable('project_wbs_items');
        $q->exec();
        $q->clear();
		
        return true;
    }

    

    function upgrade($version = 'all') {
        return true;
    }

    function configure() {
        return true;
    }

}

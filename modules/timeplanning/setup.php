<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Time Planning';
$config['mod_version'] = '2.0';
$config['mod_directory'] = 'timeplanning';
$config['mod_setup_class'] = 'CSetup_TimePlanning';
$config['mod_type'] = 'user';
$config['mod_config'] = false;
$config['mod_ui_name'] = 'Time Planning';
$config['mod_ui_icon'] = 'applet3-48.png';
$config['mod_description'] = "Time planning";

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class CSetup_TimePlanning {

    function install() {
        //$this->installQuality();

        //$this->installAcquisitionExecution();
        //$this->installTimePlanning();
        //$this->installWBSDictionary();
        //$this->installNeedForTrainning();
        
        //$this->installAcquisition();
        //$this->installAcquisitionDependentTables();
        //$this->updateTranslationFiles(); 
             
    }

    private function installTimePlanning() {
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

        //table to store eap items
        $sql = "(
		  id INT NOT NULL auto_increment,
		  project_id INT default NULL,
		  sort_order INT default NULL,
		  item_name text  default '',
		  number text  default '',
		  is_leaf text  default '',
		  identation text  default '',
		  PRIMARY KEY  (id),
                  CONSTRAINT fk_eap_item_project FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects (project_id) on delete cascade on update restrict
		)";

        $q = new DBQuery();
        $q->createTable("project_eap_items");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //table to store tasks x work packages relationhip
        $sql = "(
		  task_id INT default NULL,
		  eap_item_id INT default NULL,
                  activity_order INT NULL DEFAULT 0, 
		  PRIMARY KEY  (task_id),
                  CONSTRAINT fk_task_eap_item FOREIGN KEY (task_id) REFERENCES " . $q->_table_prefix . "tasks (task_id) on delete cascade on update restrict
		)  ";
        $q = new DBQuery();
        $q->createTable("tasks_workpackages");
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
    }

    private function updateTranslationFiles() {
        //update translations
        //1. Define translations values
        $portuguese = file_get_contents(DP_BASE_DIR . "\\modules\\timeplanning\\locales\\pt_br.inc");
        $english = file_get_contents(DP_BASE_DIR . "\\modules\\timeplanning\\locales\\en_US.inc");
        /*
          $projectsEn="\n".'"1LBLWBS" => "WBS",'."\n".'"2LBLDERIVATION" => "Derivation",'."\n".'"3LBLMDP" => "PDM",'."\n".'"4LBLESTIMATIONS" => "Estimations"'. "\n\"5LBLRESOURCES\" => \"Resources\",";
          $companiesEn="\n".'"1LBLORGONOGRAM" => "Organizational diagram",';
          $projectTasksEn="\n".'"1LBLESTIMATIONS" => "Estimations",';
          $projectsBr="\n".'"1LBLWBS" => "EAP",'."\n".'"2LBLDERIVATION" => "Derivação",'."\n".'"3LBLMDP" => "MDP",'. "\n" . '"4LBLESTIMATIONS" => "Estimativas"'. "\n\"5LBLRESOURCES\" => \"Resoursos\",";
          $companiesBr="\n".'"1LBLORGONOGRAM" => "Organograma",';
          $projectTasksBr="\n".'"1LBLESTIMATIONS" => "Estimativas",';
         */
        //2. Define file names
        // $fileCompanyEn = DP_BASE_DIR . '\\locales\\en\\companies.inc';
        // $fileCompanyBr = DP_BASE_DIR . '\\locales\\pt_br\\companies.inc';
        // $fileProjectEn = DP_BASE_DIR . '\\locales\\en\\projects.inc';
        // $fileProjectBr = DP_BASE_DIR . '\\locales\\pt_br\\projects.inc';
        // $fileTaskEn = DP_BASE_DIR . '\locales\en\tasks.inc';
        // $fileTaskBr = DP_BASE_DIR . '\locales\pt_br\tasks.inc';
        $fileCommonEn = DP_BASE_DIR . '\locales\en\common.inc';
        $fileCommonBr = DP_BASE_DIR . '\locales\pt_br\common.inc';
        //3. Update translations files
        //   $this->updateFile($fileCompanyEn, $english);
        //  $this->updateFile($fileTaskEn, $english);
        //  $this->updateFile($fileProjectEn, $english);
        //  $this->updateFile($fileCompanyBr, $portuguese);
        //  $this->updateFile($fileTaskBr, $portuguese);
        //  $this->updateFile($fileProjectBr, $portuguese);
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
        return true; // Isn't necessary delete table data. It can be reused later in a next install.

        $q = new DBQuery();
        $q->dropTable('project_minutes');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('task_minute_members');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('project_tasks_estimated_roles');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('company_role');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('tasks_mdp');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('project_eap_items');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('tasks_workpackages');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('project_tasks_estimations');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('eap_item_estimations');
        $q->exec();
        $q->clear();


        $q = new DBQuery();
        $q->dropTable("quality_planning");
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable("acquisition_planning");
        $q->exec();
        $q->clear();
                    
        $q = new DBQuery();
        $q->dropTable("acquisition_planning_roles");
        $q->exec();
        $q->clear();
                    
        $q = new DBQuery();
        $q->dropTable("acquisition_planning_requirements");
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $q->dropTable("acquisition_planning_criteria");
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $q->dropTable("acquisition_execution");
        $q->exec();
        $q->clear();
        
        return true;
    }

    private function installQuality() {
        /*
        $q = new DBQuery();
        $sql = "(
                `id` INT NOT NULL auto_increment,
                `project_id` INT default NULL,
                `quality_controlling` text  default NULL,
                `quality_assurance` text  default NULL,
                `quality_policies` text  default NULL,
                PRIMARY KEY  (`id`),
                CONSTRAINT FK_PROJECT_QUALITY FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects(project_id) on delete cascade on update restrict
            ) ";

        $q->createTable("quality_planning");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        */
        
        $q = new DBQuery();
        $sql = "(
                id INT NOT NULL auto_increment,
                quality_planning_id INT default NULL,
                what text  default NULL,
                who text  default NULL,
                `when` text  default NULL,
                how text  default NULL,
                PRIMARY KEY  (id),
                CONSTRAINT FK_PROJECT_QUALITY_ASSURANCE_ITEM FOREIGN KEY (quality_planning_id) REFERENCES " . $q->_table_prefix . "quality_planning(id) on delete cascade on update restrict
            ) ";

        $q->createTable("quality_assurance_item");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $sql = "(
                id INT NOT NULL auto_increment,
                quality_planning_id INT default NULL,
                requirement text default NULL,
                PRIMARY KEY  (id),
                CONSTRAINT FK_PROJECT_QUALITY_CONTROL_REQUIREMENT FOREIGN KEY (quality_planning_id) REFERENCES " . $q->_table_prefix . "quality_planning(id) on delete cascade on update restrict
            ) ";

        $q->createTable("quality_control_requirement");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $sql = "(
                id INT NOT NULL auto_increment,
                quality_planning_id INT default NULL,
                gqm_goal_object text default NULL,
                gqm_goal_propose text default NULL,
                gqm_goal_respect_to text default NULL,
                gqm_goal_point_of_view text default NULL,                
                gqm_goal_context text default NULL,
                PRIMARY KEY  (id),
                CONSTRAINT FK_PROJECT_QUALITY_CONTROL_GOAL FOREIGN KEY (quality_planning_id) REFERENCES " . $q->_table_prefix . "quality_planning(id) on delete cascade on update restrict
            ) ";

        $q->createTable("quality_control_goal");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $sql = "(
                id INT NOT NULL auto_increment,
                goal_id INT default NULL,
                question text default NULL,
                target text default NULL,
                PRIMARY KEY  (id),
                CONSTRAINT FK_PROJECT_QUALITY_GOAL_QUESTION FOREIGN KEY (goal_id) REFERENCES " . $q->_table_prefix . "quality_control_goal(id) on delete cascade on update restrict
            ) ";

        $q->createTable("quality_control_analiysis_question");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $sql = "(
                id INT NOT NULL auto_increment,
                question_id INT default NULL,
                metric  text default NULL,
                how_to_collect text  default NULL,
                PRIMARY KEY  (id),
                CONSTRAINT FK_PROJECT_QUALITY_METRIC FOREIGN KEY (question_id) REFERENCES " . $q->_table_prefix . "quality_control_analiysis_question(id) on delete cascade on update restrict
            ) ";
        $q->createTable("quality_control_metric");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
    }
    
     private function installWBSDictionary() {
        $q = new DBQuery();
        $sql = "(
                wbs_item_id INT NOT NULL,
                description text  default NULL,
                PRIMARY KEY  (wbs_item_id),
                CONSTRAINT FK_WBS_ITEM_DICTIONARY FOREIGN KEY (wbs_item_id) REFERENCES " . $q->_table_prefix . "project_eap_items(id) on delete cascade on update restrict
            ) ";
        $q->createTable("wbs_dictionary");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
    }
    
     private function installNeedForTrainning() {
        $q = new DBQuery();
        $sql = "(
                project_id INT NOT NULL,
                description text default NULL,
                PRIMARY KEY  (project_id),
                CONSTRAINT FK_PROJECT_NEED_FOR_TRAINING FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects (project_id) on delete cascade on update restrict
            ) ";
        $q->createTable("need_for_training");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
    }
  
        
    private function installAcquisitionExecution() {
        $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            project_id INT default NULL,
            is_delivered INT default 0,
            is_risk_contingency INT default 0,
            value float default NULL,
            date date default NULL,
            description text default NULL,
            reference_id INT default NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_PROJECT_AQC_EXE FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects(project_id) on delete cascade on update restrict
            ) ";

        $q->createTable("acquisition_execution");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
    }     
        
    /**
     * Create three tables: criteria, requirements, and roles.
     */
    private function installAcquisitionDependentTables() {
        //acquisition_planning_criteria
        $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            acquisition_id INT default NULL,
            criteria text default NULL,
            weight  INT default NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_ACQUISITION_CRITERIA FOREIGN KEY (acquisition_id) REFERENCES " . $q->_table_prefix . "acquisition_planning(id) on delete cascade
            ) ";

        $q->createTable("acquisition_planning_criteria");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //acquisition_planning_requirements
        $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            acquisition_id INT default NULL,
            requirement text default NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_AQUISITION_REQUIREMENT FOREIGN KEY (acquisition_id) REFERENCES " . $q->_table_prefix . "acquisition_planning(id) on delete cascade
            ) ";

        $q->createTable("acquisition_planning_requirements");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
        //acquisition_planning_roles
        $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            acquisition_id INT default NULL,
            role text default NULL,
            responsability text default NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_ACQUISITION_ROLE FOREIGN KEY (acquisition_id) REFERENCES " . $q->_table_prefix . "acquisition_planning(id) on delete cascade
            ) ";

        $q->createTable("acquisition_planning_roles");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
    }
    
    private function installAcquisition(){
         $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            project_id INT default NULL,
            items_to_be_acquired text default NULL,
            contract_type text default NULL,
            documents_to_acquisition text default NULL,
            criteria_for_supplier_selection text default NULL,
            additional_requirements text  default NULL,
            supplier_management_process text  default NULL,
            acquisition_roles text  default NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_PROJECT_QUALITY FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects(project_id) on delete cascade
            ) ";

        $q->createTable("acquisition_planning");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
    }
    
     private function installInstructionalFeedback(){
        $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            user_id INT default NULL,
            feedback_id INT default NULL,
            read_on DATETIME default NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_FEEDBACK_READ_USER FOREIGN KEY (user_id) REFERENCES " . $q->_table_prefix . "users(user_id) on delete cascade,
            ) ";
        $q->createTable("feedback_message_read_log");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $sql = "(
            id INT NOT NULL auto_increment,
            user_id INT default NULL,
            feedback_id INT default NULL,
            grade DATETIME INT NULL,
            PRIMARY KEY  (id),
            CONSTRAINT FK_FEEDBACK_EVALUATION_USER FOREIGN KEY (user_id) REFERENCES " . $q->_table_prefix . "users(user_id) on delete cascade,
            ) ";
        $q->createTable("feedback_evaluation");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();
    }
    

    function upgrade($version = 'all') {
        return true;
    }

    function configure() {
        return true;
    }

}

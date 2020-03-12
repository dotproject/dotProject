<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
/**
 *  Name: Risks
 *  Directory: risks
 *  Version 1.0
 *  Type: user
 *  UI Name: Risks
 *  UI Icon: ?
 */
$config = array();
$config['mod_name'] = 'Risks'; // name the module
$config['mod_version'] = '1.0'; // add a version number
$config['mod_directory'] = 'risks'; // tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupRisks'; // the name of the PHP setup class (used below)
$config['mod_type'] = 'user'; //'core' for standard dP modules, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Risks'; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'risks.png'; // name of a related icon //TODO
$config['mod_description'] = 'Risks Plan'; // some description of the module //TODO
$config['mod_config'] = false; // show 'configure' link in viewmods
//$config['permissions_item_table'] = 'risks'; // tell dotProject the database table name
$config['permissions_item_field'] = 'risk_id'; // identify table's primary key (for permissions)
$config['permissions_item_label'] = 'risk_name'; // identify "title" field in table

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class CSetupRisks {

    function configure() {
        return true;
    }

    function remove() {
        return false;
        $q = new DBQuery();
        $q->setDelete('sysvals');
        $q->addWhere("sysval_title IN ('RiskImpact', 'RiskProbability', 'RiskStatus', 'RiskPotential', 'RiskPriority', 'RiskActive', 'RiskStrategy')");
        $q->exec();
       
        
        $q->dropTable('risks_management_plan');
        $q->exec();
        $q->clear();
         
        $q->dropTable('risks');
        $q->exec();
        $q->clear();
        
        $q->dropTable('project_ear_items');
        $q->exec();
        $q->clear();
    }

    function upgrade($old_version) {
        // Place to put upgrade logic, based on the previously installed version.
        // Usually handled via a switch statement.
        // Since this is the first version of this module, we have nothing to update.
        return true;
    }

    function install() {
        //module table
        $q = new DBQuery();
        $q->createTable('risks');
        $q->createDefinition("(
        risk_id int(11) NOT NULL AUTO_INCREMENT,
        risk_name varchar(255) NOT NULL,
        risk_cause varchar(255) NOT NULL,
        risk_consequence varchar(255) NOT NULL,
        risk_responsible int(11) NOT NULL,
        risk_description varchar(2000) DEFAULT NULL,
        risk_probability varchar(15) NOT NULL,
        risk_impact varchar(15) NOT NULL,
        risk_answer_to_risk varchar(2000) DEFAULT NULL,
        risk_project int(11) DEFAULT NULL,
        risk_task int(11) DEFAULT NULL,
        risk_notes varchar(2000) DEFAULT NULL,
        risk_potential_other_projects varchar(2) NOT NULL,
        risk_lessons_learned varchar(2000) DEFAULT NULL,
        risk_priority varchar(15) NOT NULL,
        risk_active int(11) NOT NULL,
        risk_strategy int(11) NOT NULL,
        risk_prevention_actions varchar(2000) DEFAULT NULL,
        risk_contingency_plan varchar(2000) DEFAULT NULL,
        risk_period_start_date date default NULL,
        risk_period_end_date date default NULL,
        risk_status varchar(45) default NULL,
        risk_ear_classification varchar(45) default NULL,
        risk_triggers varchar(2000) DEFAULT NULL,
        risk_is_contingency int(11) DEFAULT NULL,
        PRIMARY KEY (risk_id))");
        $q->exec();
        
        //table to store ear items
        $q = new DBQuery();
        $sql = "(
                id INT NOT NULL auto_increment,
                project_id INT default NULL,
                sort_order INT default NULL,
                item_name text  default '',
                number text  default '',
                is_leaf text  default '',
                identation text  default '',
                PRIMARY KEY  (id),
                CONSTRAINT fk_ear_item_project FOREIGN KEY (project_id) REFERENCES " . $q->_table_prefix . "projects (project_id) on delete cascade on update restrict
              )";
        $q->createTable("project_ear_items");
        $q->createDefinition($sql);
        $q->exec();
        $q->clear();

        //risk management plan table
        $q = new DBQuery();
        $q->createTable("risks_management_plan");
        $q->createDefinition(
        "(risk_plan_id INT(11) NOT NULL AUTO_INCREMENT,
        project_id INT(11) DEFAULT NULL,
        probability_super_low varchar(250) DEFAULT NULL,
        probability_low varchar(250) DEFAULT NULL,
        probability_medium varchar(250) DEFAULT NULL,
        probability_high varchar(250) DEFAULT NULL,
        probability_super_high varchar(250) DEFAULT NULL,
        impact_super_low varchar(250) DEFAULT NULL,
        impact_low varchar(250) DEFAULT NULL,
        impact_medium varchar(250) DEFAULT NULL,
        impact_high varchar(250) DEFAULT NULL,
        impact_super_high varchar(250) DEFAULT NULL,
        matrix_superlow_superlow varchar(30) DEFAULT NULL,
        matrix_superlow_low varchar(30) DEFAULT NULL,
        matrix_superlow_medium varchar(30) DEFAULT NULL,
        matrix_superlow_high varchar(30) DEFAULT NULL,
        matrix_superlow_superhigh varchar(30) DEFAULT NULL,
        matrix_low_superlow varchar(30) DEFAULT NULL,
        matrix_low_low varchar(30) DEFAULT NULL,
        matrix_low_medium varchar(30) DEFAULT NULL,
        matrix_low_high varchar(30) DEFAULT NULL,
        matrix_low_superhigh varchar(30) DEFAULT NULL,
        matrix_medium_superlow varchar(30) DEFAULT NULL,
        matrix_medium_low varchar(30) DEFAULT NULL,
        matrix_medium_medium varchar(30) DEFAULT NULL,
        matrix_medium_high varchar(30) DEFAULT NULL,
        matrix_medium_superhigh varchar(30) DEFAULT NULL,
        matrix_high_superlow varchar(30) DEFAULT NULL,
        matrix_high_low varchar(30) DEFAULT NULL,
        matrix_high_medium varchar(30) DEFAULT NULL,
        matrix_high_high varchar(30) DEFAULT NULL,
        matrix_high_superhigh varchar(30) DEFAULT NULL,
        matrix_superhigh_superlow varchar(30) DEFAULT NULL,
        matrix_superhigh_low varchar(30) DEFAULT NULL,
        matrix_superhigh_medium varchar(30) DEFAULT NULL,
        matrix_superhigh_high varchar(30) DEFAULT NULL,
        matrix_superhigh_superhigh varchar(30) DEFAULT NULL,
        risk_contengency_reserve_protocol varchar(500) DEFAULT NULL,
        risk_revision_frequency varchar(3) DEFAULT NULL,
        PRIMARY KEY (risk_plan_id))");
        $q->exec();
 
        //Insert sysvals
        $q = new DBQuery();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskImpact');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|" . 'LBL_SUPER_LOW_M' . "\n1|" . 'LBL_LOW_M' . "\n2|" . 'LBL_MEDIUM_M' . "\n3|" . 'LBL_HIGH_M' . "\n4|" . 'LBL_SUPER_HIGH_M');
        $q->exec();

        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskProbability');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|" . 'LBL_SUPER_LOW_F' . "\n1|" . 'LBL_LOW_F' . "\n2|" . 'LBL_MEDIUM_F' . "\n3|" . 'LBL_HIGH_F' . "\n4|" . 'LBL_SUPER_HIGH_F');
        $q->exec();

        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskStatus');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|LBL_RISK_STATUS_IDENTIFIED\n1|LBL_RISK_STATUS_MONITORED\n2|LBL_RISK_STATUS_MATERIALIZED\n3|LBL_RISK_STATUS_FINISHED");
        $q->exec();

        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskPotential');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|" . 'LBL_NO' . "\n1|" . 'LBL_YES');
        $q->exec();

        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskPriority');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|" . 'LBL_LOW_F' . "\n1|" . 'LBL_MEDIUM_F' . "\n2|" . 'LBL_HIGH_F');
        $q->exec();

        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskActive');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|" . 'LBL_YES' . "\n1|" . 'LBL_NO');
        $q->exec();

        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RiskStrategy');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "0|" . 'LBL_ACCEPT' . "\n1|" . 'LBL_ELIMINATE' . "\n2|" . 'LBL_MITIGATE' . "\n3|" . 'LBL_TRANSFER');
        $q->exec();
        return NULL;
    }

    private function updateFile($fileName, $content) {
        if (!file_exists($fileName)) {
            $fileName = str_replace("\\", "/", $fileName);
        }
        $fp = fopen($fileName, 'a');
        fwrite($fp, $content);
        fclose($fp);
    }

}

?>
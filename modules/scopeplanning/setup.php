<?php

/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
/**
 *  Name: Scope Planning
 *  Directory: scopeplanning
 *  Version 1.0
 *  Type: user
 *  UI Name: Scope Planning
 *  UI Icon: ?
 */
$config = array();
$config['mod_name'] = 'Scope Planning'; // name the module
$config['mod_version'] = '1.0'; // add a version number
$config['mod_directory'] = 'scopeplanning'; // tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetup_ScopePlanning'; // the name of the PHP setup class (used below)
$config['mod_type'] = 'user'; //'core' for standard dP modules, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Scope'; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'scope.png'; // name of a related icon
$config['mod_description'] = 'Scope Planning module'; // some description of the module
$config['mod_config'] = false; // show 'configure' link in viewmods

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class CSetup_ScopePlanning {

    function configure() {
        return true;
    }

    function remove() {
        //It is not necessary to delete the tables on uninstall
//        $q = new DBQuery;
//        $q->dropTable('scope_requirements');
//        $q->exec();
//        $q->clear();
//        $q->dropTable('scope_requirements_managplan');
//        $q->exec();
//        $q->clear();
//        $q->dropTable('scope_requirement_categories');
//        $q->exec();
//        $q->clear();
//        $q->dropTable('scope_requirement_priorities');
//        $q->exec();
//        $q->clear();
//        $q->dropTable('scope_requirement_status');
//        $q->exec();
//        $q->clear();
//        $q->dropTable('scope_statement');
//        $q->exec();
//        $q->clear();
        return null;
    }

    function upgrade($old_version) {
        // Place to put upgrade logic, based on the previously installed version.
        // Usually handled via a switch statement.
        // Since this is the first version of this module, we have nothing to update.
        return true;
    }

    function install() {
        $this->installProjectsTranslationFile();
        
        $q = new DBQuery();
        $q->createTable('scope_requirement_priorities');
        $q->createDefinition("(
  `req_priority_id` varchar(20) NOT NULL,
  PRIMARY KEY (`req_priority_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->createTable('scope_requirement_status');
        $q->createDefinition("(
  `req_status_id` varchar(20) NOT NULL,
  PRIMARY KEY (`req_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->createTable('scope_requirement_categories');
        $q->createDefinition("(
  `req_categ_prefix_id` varchar(3) NOT NULL,
  `req_categ_description` text,
  `req_categ_name` varchar(20) NOT NULL,
  `req_categ_priority` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`req_categ_prefix_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->createTable('scope_requirements');
        $q->createDefinition("(
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `req_idname` varchar(6) NOT NULL,
  `req_description` text NOT NULL,
  `req_source` varchar(60) NOT NULL,
  `req_owner` varchar(60) NOT NULL,
  `req_categ_prefix_id` varchar(3) NOT NULL,
  `req_priority_id` varchar(20) NOT NULL,
  `req_status_id` varchar(20) NOT NULL,
  `req_version` varchar(20) DEFAULT NULL,
  `req_inclusiondate` date NOT NULL,
  `req_conclusiondate` date DEFAULT NULL,
  `eapitem_id` int(11) DEFAULT NULL,
  `req_testcase` text,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`req_id`),
  KEY `req_categ_prefix_id` (`req_categ_prefix_id`),
  KEY `req_priority_id` (`req_priority_id`),
  KEY `req_status_id` (`req_status_id`),
  KEY `eapitem_id` (`eapitem_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `dotp_scope_requirements_ibfk_1` FOREIGN KEY (`req_categ_prefix_id`) REFERENCES " . $q->_table_prefix . "scope_requirement_categories (req_categ_prefix_id),
  CONSTRAINT `dotp_scope_requirements_ibfk_2` FOREIGN KEY (`req_priority_id`) REFERENCES " . $q->_table_prefix . "scope_requirement_priorities (req_priority_id),
  CONSTRAINT `dotp_scope_requirements_ibfk_3` FOREIGN KEY (`req_status_id`) REFERENCES " . $q->_table_prefix . "scope_requirement_status (req_status_id),
  CONSTRAINT `dotp_scope_requirements_ibfk_4` FOREIGN KEY (`eapitem_id`) REFERENCES " . $q->_table_prefix . "project_eap_items (id),
  CONSTRAINT `dotp_scope_requirements_ibfk_5` FOREIGN KEY (`project_id`) REFERENCES " . $q->_table_prefix . "projects (project_id)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;");
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->createTable('scope_statement');
        $q->createDefinition("(
  `scope_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,  
  `scope_description` text,
  `scope_acceptancecriteria` text,
  `scope_deliverables` text,
  `scope_exclusions` text,
  `scope_constraints` text,
  `scope_assumptions` text,
  PRIMARY KEY (`scope_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `dotp_scope_statement_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES " . $q->_table_prefix . "projects (project_id)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;");
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->createTable('scope_requirements_managplan');
        $q->createDefinition("(
  `req_managplan_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `req_managplan_collect_descr` text,
  `req_managplan_reqcategories` text,
  `req_managplan_reqprioritization` text,
  `req_managplan_trac_descr` text,
  `req_managplan_config_descr` text,
  `req_managplan_verif_descr` text,
  PRIMARY KEY (`req_managplan_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `dotp_scope_requirements_managplan_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES " . $q->_table_prefix . "projects (project_id)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;");
        $q->exec();
        $q->clear();
             
        $q = new DBQuery();
        $q->addTable('scope_requirement_priorities');
        $q->addInsert('req_priority_id', 'Baixa');
        $q->exec();
        $q->clear();        
        $q = new DBQuery();
        $q->addTable('scope_requirement_priorities');       
        $q->addInsert('req_priority_id', 'Normal');
        $q->exec();
        $q->clear();
        $q = new DBQuery();
        $q->addTable('scope_requirement_priorities');
        $q->addInsert('req_priority_id', 'Alta');       
        $q->exec();
        $q->clear();  
        
        $q = new DBQuery();
        $q->addTable('scope_requirement_status');
        $q->addInsert('req_status_id', 'Ativo');        
        $q->exec();
        $q->clear();        
        $q = new DBQuery();
        $q->addTable('scope_requirement_status');       
        $q->addInsert('req_status_id', 'Adicionado');        
        $q->exec();
        $q->clear();     
        $q = new DBQuery();
        $q->addTable('scope_requirement_status');
        $q->addInsert('req_status_id', 'Cancelado');
        $q->exec();
        $q->clear();        
        $q = new DBQuery();
        $q->addTable('scope_requirement_status');        
        $q->addInsert('req_status_id', 'Aprovado');
        $q->exec();
        $q->clear();        
        $q = new DBQuery();
        $q->addTable('scope_requirement_status');
        $q->addInsert('req_status_id', 'Adiado');
        $q->exec();
        $q->clear();
        
        $q = new DBQuery();
        $q->addTable('scope_requirement_categories');
        $q->addInsert('req_categ_prefix_id', 'RF');
        $q->addInsert('req_categ_name', 'Funcional');              
        $q->exec();
        $q->clear();        
        $q = new DBQuery();
        $q->addTable('scope_requirement_categories');       
        $q->addInsert('req_categ_prefix_id', 'RNF');
        $q->addInsert('req_categ_name', 'Não-funcional');        
        $q->exec();
        $q->clear();        
//        $q = new DBQuery();
//        $q->addTable('scope_requirement_categories');        
//        $q->addInsert('req_categ_prefix_id', 'RQS');
//        $q->addInsert('req_categ_name', 'Security');
//        $q->exec();
//        $q->clear();
        
        return null;
    }
    
    private function installProjectsTranslationFile() {
        $translationFileUS = DP_BASE_DIR . "/modules/scopeplanning/locales/en.inc";
        $translationFilePTBR = DP_BASE_DIR . "/modules/scopeplanning/locales/pt_br.inc";
        echo $translationFilePTBR;       
    }
}

?>

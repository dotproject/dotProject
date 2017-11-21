<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$config = array();
$config['mod_name'] = 'Instructor Admin'; // name the module
$config['mod_version'] = '1.0'; // add a version number
$config['mod_directory'] = 'instructor_admin'; // tell dotProject where to find this module
$config['mod_setup_class'] = 'CInstructorAdmin'; // the name of the PHP setup class (used below)
$config['mod_type'] = 'user'; //'core' for standard dP modules, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Instructor Admin'; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'module_icon.jpg'; // name of a related icon //TODO
$config['mod_description'] = 'Module for setup a new class, creating students credentials at the beginning of the semester'; // some description of the module //TODO
$config['mod_config'] = false; // show 'configure' link in viewmods
//$config['permissions_item_table'] = 'risks'; // tell dotProject the database table name
//$config['permissions_item_field'] = 'instructor_admin_id'; // identify table's primary key (for permissions)
//$config['permissions_item_label'] = 'instructor_admin_code'; // identify "title" field in table

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class CInstructorAdmin {

    function configure() {
        return true;
    }

    function remove() {
        return false;
    }

    function upgrade($old_version) {
        return true;
    }

    function install() {    
        $q = new DBQuery();
        $q->createTable('dpp_classes');
        $q->createDefinition("(
        class_id int(11) NOT NULL AUTO_INCREMENT,
        educational_institution varchar(255) NOT NULL,
        course varchar(255) NOT NULL,
        disciplin varchar(255) NOT NULL,
        instructor varchar(255) NOT NULL,
        semester  int(11) default 1,
        year  int(11) NOT NULL,
        number_of_students  int(11) default 0,
        PRIMARY KEY (class_id))");
        $q->exec();
        
        $q = new DBQuery();
        $q->createTable('dpp_classes_users');
        $q->createDefinition("(
        class_id int(11) NOT NULL,
        user_id int(11) NOT NULL,
        user_login varchar(255) NOT NULL,
        user_password varchar(255) NOT NULL,
        user_company int(11) NOT NULL,
        PRIMARY KEY (user_id))");
        $q->exec();
    }
}

?>
<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

/**
 *  Name: Communication
 *  Directory: communication
 *  Version 1.0
 *  Type: user
 *  UI Name: Communication
 *  UI Icon: ?
 */
$config = array();
$config['mod_name'] = 'Communication'; // name the module
$config['mod_version'] = '1.0'; // add a version number
$config['mod_directory'] = 'communication'; // tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupCommunication'; // the name of the PHP setup class (used below)
$config['mod_type'] = 'user'; //'core' for standard dP modules, 'user' for additional modules from dotmods
$config['mod_ui_name'] = "Communication"; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'applet3-48.png'; // name of a related icon
$config['mod_description'] = 'Communications Planning'; // some description of the module
$config['mod_config'] = false; // show 'configure' link in viewmods
$config['permissions_item_table'] = 'communication'; // tell dotProject the database table name
$config['permissions_item_field'] = 'communication_id'; // identify table's primary key (for permissions)
$config['permissions_item_label'] = 'communication_name'; // identify "title" field in table


if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

// TODO: To be completed later as needed.
class CSetupCommunication {

    function configure() {
        return true;
    }

    function remove() {
        $q = new DBQuery();
        $q->dropTable('communication');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('communication_issuing');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('communication_receptor');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('communication_channel');
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->dropTable('communication_frequency');
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

        //criar tabela de eventos de comunicação
        $q = new DBQuery();
        $q->createTable('communication');
        $q->createDefinition("(
  `communication_id` int(11) NOT NULL AUTO_INCREMENT ,
  `communication_title` varchar(255) NOT NULL,
  `communication_information` varchar(2000) NOT NULL,
  `communication_frequency_id` int(11) NOT NULL,  
  `communication_channel_id` int(11) NOT NULL,  
  `communication_project_id` int(11) NOT NULL, 
  `communication_restrictions` varchar(2000) NOT NULL,
  `communication_date` varchar(30) NOT NULL,
  `communication_responsible_authorization` varchar(80) NOT NULL,
   PRIMARY KEY (`communication_id`) 
) ");

        $q->exec($sql);

        $q->clear();

        // criar tabela com emissores da comunicação
        $q = new DBQuery();
        $q->createTable('communication_issuing');
        $definition="(
        communication_issuing_id int(11) NOT NULL AUTO_INCREMENT,
        communication_id int(11) NOT NULL,
        communication_stakeholder_id int(11) NOT NULL,
        PRIMARY KEY (communication_issuing_id),
        CONSTRAINT fk_communication_issuing FOREIGN KEY (communication_stakeholder_id) REFERENCES " . $q->_table_prefix . "contacts (contact_id) on delete cascade on update no action
        )";
        $q->createDefinition($definition);
        $q->exec($sql);
        $q->clear();

        // criar tabela com receptores da comunicação
        $q = new DBQuery();
        $q->createTable('communication_receptor');
        $q->createDefinition("(
        communication_receptor_id int(11) NOT NULL AUTO_INCREMENT,
        communication_id int(11) NOT NULL,
        communication_stakeholder_id int(11) NOT NULL,
        PRIMARY KEY (communication_receptor_id),
        CONSTRAINT fk_communication_receptor FOREIGN KEY (communication_stakeholder_id) REFERENCES " . $q->_table_prefix . "contacts (contact_id) on delete cascade on update no action
        )");
        $q->exec($sql);
        $q->clear();

        // criar tabela para canais de comunicação
        $q = new DBQuery();
        $q->createTable('communication_channel');
        $q->createDefinition("(
  `communication_channel_id` int(11) NOT NULL AUTO_INCREMENT ,
  `communication_channel` varchar(255) NOT NULL,
   PRIMARY KEY (`communication_channel_id`) 
) ");
        $q->exec($sql);
        $q->clear();

        // criar tabela para frequencia de comunicação
        $q = new DBQuery();
        $q->createTable('communication_frequency');
        $q->createDefinition("(
  `communication_frequency_id` int(11) NOT NULL AUTO_INCREMENT ,
  `communication_frequency` varchar(255) NOT NULL,
  `communication_frequency_hasdate` char(3) default 'Nao',
   PRIMARY KEY (`communication_frequency_id`) 
) ");
        $q->exec($sql);
        $q->clear();

        return NULL;
    }

}

?>

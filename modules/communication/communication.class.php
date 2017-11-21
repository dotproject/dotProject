<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once $AppUI->getSystemClass('dp');

/**
 * Communication Class
 */
class CCommunication extends CDpObject {

    var $communication_id = NULL;
    var $communication_title = NULL;
    var $communication_information = NULL;
    var $communication_frequency_id = NULL;
    var $communication_channel_id = NULL;
    var $communication_issuing_id = NULL;
    var $communication_receptor_id = NULL;
    var $communication_project_id = NULL;
    var $communication_restrictions = NULL;
    var $communication_date = NULL;
    var $communication_responsible_authorization = NULL;

    function CCommunication() {

        $this->CDpObject('communication', 'communication_id');
    }

    function check() {
// ensure the integrity of some variables
        $this->communication_id = intval($this->communication_id);
        return NULL; // object is ok
    }

    function delete() {
        global $dPconfig;
        $this->_message = "deleted";
// delete the main table reference
        $q = new DBQuery();
        $q->setDelete('communication');
        $q->addWhere('communication_id = ' . $this->communication_id);
        if (!$q->exec()) {
            return db_error();
        }
        return NULL;
    }

}

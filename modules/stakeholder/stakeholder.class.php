<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

require_once $AppUI->getSystemClass('dp');
/**
 * Stakeholder Class
 */
class CStakeholder extends CDpObject {

  var $initiating_stakeholder_id = NULL;
  var $initiating_id = NULL;
  var $contact_id = NULL;
  var $stakeholder_responsibility = NULL;
  var $stakeholder_interest = NULL;
  var $stakeholder_power = NULL;
  var $stakeholder_strategy = NULL;

	
	function CStakeholder() {
		$this->CDpObject('initiating_stakeholder', 'initiating_stakeholder_id');
	}

	function check() {
	// ensure the integrity of some variables
		$this->initiating_stakeholder_id = intval($this->initiating_stakeholder_id);

		return NULL; // object is ok
	}

	function delete() {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$q = new DBQuery();
		$q->setDelete('initiating_stakeholder');
		$q->addWhere('initiating_stakeholder_id = ' . $this->initiating_stakeholder_id);
		if (!$q->exec()) {
			return db_error();
		}
		return NULL;
	}
}
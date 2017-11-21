<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

//require_once $AppUI->getSystemClass('dp');
require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('projects'));
/**
 * Risk Class
 */
class CRisks extends CDpObject {
	var $risk_id = NULL;
	var $risk_name = NULL;
        var $risk_responsible = NULL;       
        var $risk_description = NULL;
        var $risk_probability = NULL;
        var $risk_impact = NULL;
	var $risk_answer_to_risk = NULL;
        var $risk_status = NULL;
        var $risk_project = NULL;
	var $risk_task = NULL;
        var $risk_notes = NULL;
        var $risk_potential_other_projects = NULL;
	var $risk_lessons_learned = NULL;
        var $risk_priority = NULL;
        var $risk_active = NULL;
        var $risk_strategy = NULL;
        var $risk_prevention_actions = NULL;
        var $risk_contingency_plan = NULL;
        var $risk_period_start_date = NULL;
        var $risk_period_end_date = NULL;
        var $risk_ear_classification = NULL;
        var $risk_triggers = NULL;
        var $risk_is_contingency=NULL;
        var $risk_cause= NULL;
        var $risk_consequence= NULL;
         
	function CRisks() {
            $this->CDpObject('risks', 'risk_id');
	}

	function check() {
	// ensure the integrity of some variables
		$this->risk_id = intval($this->risk_id);
		return NULL; // object is ok
	}

	function delete() {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$q = new DBQuery();
		$q->setDelete('risks');
		$q->addWhere('risk_id = ' . $this->risk_id);
		if (!$q->exec()) {
			return db_error();
		}
		return NULL;
	}
}
?>
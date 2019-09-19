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

	/**
	 * Call the parent constructor for risks
	 */
	function __construct() {
		parent::__construct('risks', 'risk_id');
		$this->_module_directory = 'risks';
	}

	/**
	 * Check the risk object
	 */
	function check() {
		// ensure the integrity of some variables
		$this->risk_id = intval($this->risk_id);
		if ($this->risk_id == 0) {
			return 0;
		}
		return NULL; // object is ok
	}

	/**
	 * Delete a risk
	 */
	function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
		$this->load($this->risk_id);
		addHistory('risks', $this->risk_id, 'delete', $this->risk_name,
		           $this->risk_id);
		$q = new DBQuery;

		$q->setDelete('risks');
		$q->addWhere('risk_id =' . $this->risk_id);

		$result = ((!$q->exec()) ? db_error() : NULL);
		$q->clear();
		return $result;
	}
}
?>
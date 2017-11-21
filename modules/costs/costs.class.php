<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

//require_once $AppUI->getSystemClass('dp');
require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('projects'));
/**
 * Costs Class
 */
class CCosts extends CDpObject {
	var $cost_id = NULL;
        var $cost_type_id = NULL;
	var $cost_description = NULL;
        var $cost_quantity = NULL;       
        var $cost_date_begin = NULL;
        var $cost_date_end = NULL;
        var $cost_value_unitary = 0;
	var $cost_value_total = 0;
        var $cost_project_id = 0;
        
	function CCosts() {
            $this->CDpObject('costs', 'cost_id');
	}

	function check() {
	// ensure the integrity of some variables
		$this->cost_id = intval($this->cost_id);
		return NULL; // object is ok
	}

	function delete() {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$q = new DBQuery();
		$q->setDelete('costs');
		$q->addWhere('cost_id = ' . $this->cost_id);
		if (!$q->exec()) {
			return db_error();
		}
		return NULL;
	}
}

class CBudgetReserve extends CDpObject {
	var $budget_reserve_id = NULL;
	var $budget_reserve_description = NULL;
        var $budget_reserve_financial_impact = 0;       
        var $budget_reserve_inicial_month = NULL;
        var $budget_reserve_final_month = NULL;
        var $budget_reserve_value_total = 0;
        
	function CBudgetReserve() {
            $this->CDpObject('budget_reserve', 'budget_reserve_id');
	}

	function check() {
	// ensure the integrity of some variables
		$this->budget_reserve_id = intval($this->budget_reserve_id);
		return NULL; // object is ok
	}

	function delete() {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$q = new DBQuery();
		$q->setDelete('budget_reserve');
		$q->addWhere('budget_reserve_id = ' . $this->budget_reserve_id);
		if (!$q->exec()) {
			return db_error();
		}
		return NULL;
	}
}

class CBudget extends CDpObject {
	var $budget_id = NULL;
	var $budget_reserve_management = NULL;
        var $budget_sub_total = 0;       
        var $budget_total = 0;
        
        
	function CBudget() {
            $this->CDpObject('budget', 'budget_id');
	}

	function check() {
	// ensure the integrity of some variables
		$this->budget_id = intval($this->budget_id);
		return NULL; // object is ok
	}

	function delete() {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$q = new DBQuery();
		$q->setDelete('budget');
		$q->addWhere('budget_id = ' . $this->budget_id);
		if (!$q->exec()) {
			return db_error();
		}
		return NULL;
	}
}

?>

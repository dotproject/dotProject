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
class CRisksManagementPlan extends CDpObject {

    var $risk_plan_id = NULL;
    var $project_id = NULL;
    var $probability_super_low = NULL;
    var $probability_low = NULL;
    var $probability_medium = NULL;
    var $probability_high = NULL;
    var $probability_super_high = NULL;
    var $impact_super_low = NULL;
    var $impact_low = NULL;
    var $impact_medium = NULL;
    var $impact_high = NULL;
    var $impact_super_high = NULL;
    var $matrix_superlow_superlow = NULL;
    var $matrix_superlow_low = NULL;
    var $matrix_superlow_medium = NULL;
    var $matrix_superlow_high = NULL;
    var $matrix_superlow_superhigh = NULL;
    var $matrix_low_superlow = NULL;
    var $matrix_low_low = NULL;
    var $matrix_low_medium = NULL;
    var $matrix_low_high = NULL;
    var $matrix_low_superhigh = NULL;
    var $matrix_medium_superlow = NULL;
    var $matrix_medium_low = NULL;
    var $matrix_medium_medium = NULL;
    var $matrix_medium_high = NULL;
    var $matrix_medium_superhigh = NULL;
    var $matrix_high_superlow = NULL;
    var $matrix_high_low = NULL;
    var $matrix_high_medium = NULL;
    var $matrix_high_high = NULL;
    var $matrix_high_superhigh = NULL;
    var $matrix_superhigh_superlow = NULL;
    var $matrix_superhigh_low = NULL;
    var $matrix_superhigh_medium = NULL;
    var $matrix_superhigh_high = NULL;
    var $matrix_superhigh_superhigh = NULL;
    var $risk_contengency_reserve_protocol = NULL;
    var $risk_revision_frequency = NULL;

    /**
	 * Call the parent constructor for risks
	 */
	function __construct() {
		parent::__construct('risks_management_plan', 'risk_plan_id');
        $this->_module_directory = 'risks';     
	}

    function check() {
        // ensure the integrity of some variables
        $this->risk_plan_id = intval($this->risk_plan_id);
        return NULL; // object is ok
    }

    function loadDefaultValues() {
        global $AppUI;
        if ($this->risk_plan_id == "") {
            $this->probability_super_low = $AppUI->_('LBL_RISK_PROBABILITY_SUPERLOW');
            $this->probability_low = $AppUI->_('LBL_RISK_PROBABILITY_LOW');
            $this->probability_medium = $AppUI->_('LBL_RISK_PROBABILITY_MEDIUM');
            $this->probability_high = $AppUI->_('LBL_RISK_PROBABILITY_HIGH');
            $this->probability_super_high = $AppUI->_('LBL_RISK_PROBABILITY_SUPERHIGH');
            $this->impact_super_low = $AppUI->_('LBL_RISK_IMPACT_SUPERLOW');
            $this->impact_low = $AppUI->_('LBL_RISK_IMPACT_LOW');
            $this->impact_medium = $AppUI->_('LBL_RISK_IMPACT_MEDIUM');
            $this->impact_high = $AppUI->_('LBL_RISK_IMPACT_HIGH');
            $this->impact_super_high = $AppUI->_('LBL_RISK_IMPACT_SUPERHIGH');
            $this->matrix_superlow_superlow = 0;
            $this->matrix_superlow_low = 0;
            $this->matrix_superlow_medium = 0;
            $this->matrix_superlow_high = 1;
            $this->matrix_superlow_superhigh = 1;
            $this->matrix_low_superlow = 0;
            $this->matrix_low_low = 0;
            $this->matrix_low_medium = 1;
            $this->matrix_low_high = 1;
            $this->matrix_low_superhigh = 1;
            $this->matrix_medium_superlow = 0;
            $this->matrix_medium_low = 0;
            $this->matrix_medium_medium = 1;
            $this->matrix_medium_high = 1;
            $this->matrix_medium_superhigh = 2;
            $this->matrix_high_superlow = 1;
            $this->matrix_high_low = 1;
            $this->matrix_high_medium = 1;
            $this->matrix_high_high = 2;
            $this->matrix_high_superhigh = 2;
            $this->matrix_superhigh_superlow = 1;
            $this->matrix_superhigh_low = 1;
            $this->matrix_superhigh_medium = 2;
            $this->matrix_superhigh_high = 2;
            $this->matrix_superhigh_superhigh = 2;
            $this->risk_contengency_reserve_protocol = $AppUI->_('LBL_RISK_CONTINGENCY_RESERVE_PROTOCOL');
            $this->risk_revision_frequency = 15;
        }
    }

    /**
	 * Delete a risk management plan
	 */
	function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
		$this->load($this->risk_id);
		addHistory('risks', $this->risk_id, 'delete', $this->risk_name,
		           $this->risk_id);
		$q = new DBQuery;

		$q->setDelete('risks_management_plan');
		$q->addWhere('risk_plan_id =' . $this->risk_plan_id);

		$result = ((!$q->exec()) ? db_error() : NULL);
		$q->clear();
		return $result;
	}

}

?>
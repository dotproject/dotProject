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

    function CRisksManagementPlan() {
        $this->CDpObject('risks_management_plan', 'risk_plan_id');
    }

    function check() {
        // ensure the integrity of some variables
        $this->risk_plan_id = intval($this->risk_plan_id);
        return NULL; // object is ok
    }

    function loadDefaultValues() {
        if ($this->risk_plan_id == "") {
            $this->probability_super_low = "Um evento similar ocorreu uma única vez em outra organização";
            $this->probability_low = "Um evento similar ocorreu em uma organização similar.";
            $this->probability_medium = "Um evento similar já ocorreu nesta organização.";
            $this->probability_high = "Um evento similar já ocorreu diversas vezes nesta organização.";
            $this->probability_super_high = "Um evento similar já ocorreu muitas vezes na mesa atividade ou operação.";
            $this->impact_super_low = "O impacto pode ser ignorado.";
            $this->impact_low = "Impacto mínimo que pode ser contornado por procedimentos padrão.";
            $this->impact_medium = "Impacto maior, que pode ser contornado com algum esforço, utilizando procedimentos padrão.";
            $this->impact_high = "Evento crítico, que pode gerar algum custo ou atrazo ao projeto, ou produtos não apropriados.";
            $this->impact_super_high = "Evento extremo, que pode gerar custos ou atrasos, ou até deteriorar a imagem da organização.";
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
            $this->risk_contengency_reserve_protocol = "A liberação dos recursos da reserva de contingência devem ser solicitados a administração com a comprovação da ocorrência do mesmo.";
            $this->risk_revision_frequency = 15;
        }
    }

    function delete() {
        global $dPconfig;
        $this->_message = "deleted";

        // delete the main table reference
        $q = new DBQuery();
        $q->setDelete('risks_management_plan');
        $q->addWhere('risk_plan_id = ' . $this->risk_plan_id);
        if (!$q->exec()) {
            return db_error();
        }
        return NULL;
    }

}

?>
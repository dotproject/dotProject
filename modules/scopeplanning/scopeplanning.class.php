<?php

/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('projects'));

/*
 * Requirements
 */
class CScopeRequirements extends CDpObject {

    var $req_id = NULL;
    var $req_idname = NULL;
    var $req_description = NULL;
    var $req_source = NULL;
    var $req_owner = NULL;
    var $req_categ_prefix_id = NULL;
    var $req_priority_id = NULL;
    var $req_status_id = NULL;
    var $req_version = NULL;
    var $req_inclusiondate = NULL;
    var $req_conclusiondate = NULL;
    var $eapitem_id = NULL;
    var $req_testcase = NULL;
    var $project_id = NULL;

    function CScopeRequirements() {
        $this->CDpObject('scope_requirements', 'req_id');
    }

    function delete() {
        global $dPconfig;
        $this->_message = "deleted";

        $q = new DBQuery();
        $q->setDelete('scope_requirements');
        $q->addWhere('req_id = ' . $this->req_id);
        if (!$q->exec()) {
            return db_error();
        }
        return NULL;
    }
}

/*
 * Scope statement
 */
class CScopeStatement extends CDpObject {

    var $scope_id = NULL;
    var $project_id = NULL;
    var $scope_description = NULL;
    var $scope_acceptancecriteria = NULL;
    var $scope_deliverables = NULL;
    var $scope_exclusions = NULL;
    var $scope_constraints = NULL;
    var $scope_assumptions = NULL;

    function CScopeStatement() {
        $this->CDpObject('scope_statement', 'scope_id');
    }

    function delete() {
        global $dPconfig;
        $this->_message = "deleted";

        $q = new DBQuery();
        $q->setDelete('scope_statement');
        $q->addWhere('scope_id = ' . $this->scope_id);
        if (!$q->exec()) {
            return db_error();
        }
        return NULL;
    }
}

/*
 * Scope management plan
 */
class CReqManagPlan extends CDpObject {

    var $req_managplan_id = NULL;
    var $project_id = NULL;
    var $req_managplan_collect_descr = NULL;
    var $req_managplan_reqcategories = NULL;
    var $req_managplan_reqprioritization = NULL;
    var $req_managplan_trac_descr = NULL;
    var $req_managplan_config_descr = NULL;
    var $req_managplan_verif_descr = NULL;

    function CReqManagPlan() {
        $this->CDpObject('scope_requirements_managplan', 'req_managplan_id');
    }

    function delete() {
        global $dPconfig;
        $this->_message = "deleted";

        $q = new DBQuery();
        $q->setDelete('scope_requirements_managplan');
        $q->addWhere('req_managplan_id = ' . $this->req_managplan_id);
        if (!$q->exec()) {
            return db_error();
        }
        return NULL;
    }
}

//Foi retirada a opcao de adicionar novas categorias
///*
// * Requirement category kind
// */
//class CScopeRequirementCategories extends CDpObject {
//
//    var $req_categ_prefix_id = NULL;
//    var $req_categ_description = NULL;
//    var $req_categ_name = NULL;    
//
//    //table name and primary key name
//    function CScopeRequirementCategories() {
//        $this->CDpObject('scope_requirement_categories', 'req_categ_prefix_id');
//    }   
//
//    function delete() {
//        global $dPconfig;
//        $this->_message = "deleted";
//
//        $q = new DBQuery();
//        $q->setDelete('scope_requirement_categories');
//        $q->addWhere('req_categ_prefix_id = ' . $this->req_categ_prefix_id);
//        if (!$q->exec()) {
//            return db_error();
//        }
//        return NULL;
//    }
//}

//Nao havera opcao para adicionar prioridades
///*
// * Requirement priority kind
// */
//class CScopeRequirementPriorities extends CDpObject {
//
//    var $req_priority_id = NULL;
//
//    function CScopeRequirementPriorities() {
//        $this->CDpObject('scope_requirement_priorities', 'req_priority_id');
//    }
//}

//Nao havera opcao para adicionar tipos de status
///*
// * Requirement status kind
// */
//class CScopeRequirementStatus extends CDpObject {
//
//    var $req_status_id = NULL;
//
//    function CScopeRequirementStatus() {
//        $this->CDpObject('scope_requirement_status', 'req_status_id');
//    }
//}

////A indicacao de categorias de requisitos no projeto será feita manualmente
////no plano de gerenciamento de requisitos
///*
// * REVER! Isto pode dar problema pois a tabela só possui duas chaves estrangeiras.
// */
//class CScopeProjectReqCategories extends CDpObject {
//
//    var $project_id = NULL;
//    var $req_categ_prefix_id = NULL;
//
//    function CScopeProjectReqCategories() {
//        $this->CDpObject('scope_project_req_categories', '$project_id');
//    }
//
//    function delete() {
//        global $dPconfig;
//        $this->_message = "deleted";
//
//        $q = new DBQuery();
//        $q->setDelete('scope_project_req_categories');
//        $q->addWhere('$project_id = ' . $this->$project_id);
//        if (!$q->exec()) {
//            return db_error();
//        }
//        return NULL;
//    }    
//}

?>

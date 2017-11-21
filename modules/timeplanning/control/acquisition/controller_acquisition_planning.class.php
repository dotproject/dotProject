<?php

require_once (DP_BASE_DIR . "/modules/timeplanning/model/acquisition/acquisition_planning.class.php");

/**
 * Class to provides acquisition plannig data.
 * @author Rafael Queiroz Gonçalves
 */
class ControllerAcquisitionPlanning {

    function ControllerAcquisitionPlanning() {
        
    }

    public function sendDataToBeStored($id, $projectId, $acquisitionRoles, $supplierManagementProcess, $itemsToBeAcquired, $documentsToAcquisition, $criteriaForSelection, $contractType, $additionalRequirements) {
        $acquisition = new AcquisitionPlanning();
        $acquisition->setId($id);
        $acquisition->setProjectId($projectId);
        $acquisition->setItemsToBeAcquired($itemsToBeAcquired);
        $acquisition->setContractType($contractType);
        $acquisition->setDocumentsToAcquisition($documentsToAcquisition);
        $acquisition->setCriteriaForSelection($criteriaForSelection);
        $acquisition->setAdditionalRequirements($additionalRequirements);
        $acquisition->setSupplierManagementProcess($supplierManagementProcess);
        $acquisition->setAcquisitionRoles($acquisitionRoles);
        return $acquisition->store();
    }

    public function getAcquisitionPlanningsPerProject($projectId) {
        $object = new AcquisitionPlanning();
        $list = $object->loadAll($projectId);
        return $list;
    }

    public function getAcquisitionPlanning($id) {
        $object = new AcquisitionPlanning();
        $object->load($id);
        return $object;
    }

    public function delete($id) {
        $object = new AcquisitionPlanning();
        $object->delete($id);
    }

    //function to handle crud of dependent classes
    function deleteRoles($roles) {
        $ids = explode("#$", $roles);
        for ($i = 0; $i < sizeof($ids); $i++) {
            if ($ids[$i] != "") {
                $q = new DBQuery();
                $q->setDelete("acquisition_planning_roles");
                $q->addWhere("id=" . $ids[$i]);
                $q->exec();
                $q->clear();
            }
        }
    }

    function deleteCriteria($criteria) {
        $ids = explode("#$", $criteria);
        for ($i = 0; $i < sizeof($ids); $i++) {
            if ($ids[$i] != "") {
                $q = new DBQuery();
                $q->setDelete("acquisition_planning_criteria");
                $q->addWhere("id=" . $ids[$i]);
                $q->exec();
                $q->clear();
            }
        }
    }

    function deleteRequirements($requirement) {
        $ids = explode("#$", $requirement);
        for ($i = 0; $i < sizeof($ids); $i++) {
            if ($ids[$i] != "") {
                $q = new DBQuery();
                $q->setDelete("acquisition_planning_requirements");
                $q->addWhere("id=" . $ids[$i]);
                $q->exec();
                $q->clear();
            }
        }
    }

    function storeRoles($roles, $acquisitionId) {
        $records = explode("#$", $roles);
        for ($i = 0; $i < sizeof($records); $i++) {
            $fields = explode("#!", $records[$i]);
            $id = $fields[0];
            if ($id != "") {
                $role = $fields[1];
                $responsability = $fields[2];
                $q = new DBQuery();
                $q->addQuery("id");
                $q->addTable("acquisition_planning_roles");
                $q->addWhere("id =" . $id);
                $record = $q->loadResult();
                $q->clear();
                $q->addTable("acquisition_planning_roles");
                if (empty($record)) {
                    $q->addInsert("acquisition_id", $acquisitionId);
                    $q->addInsert("responsability", $responsability);
                    $q->addInsert("role", $role);
                } else {
                    $q->addUpdate("acquisition_id", $acquisitionId);
                    $q->addUpdate("responsability", $responsability);
                    $q->addUpdate("role", $role);
                    $q->addWhere("id = " . $id);
                }
                $q->exec();
                $q->clear();
            }
        }
    }

    function storeCriteria($criteria, $acquisitionId) {
       
        $records = explode("#$", $criteria);
        for ($i = 0; $i < sizeof($records); $i++) {
            $fields = explode("#!", $records[$i]);
            $id = $fields[0];
            if ($id != "") {
                $criteria = $fields[1];
                $weight = $fields[2];
                $q = new DBQuery();
                $q->addQuery("id");
                $q->addTable("acquisition_planning_criteria");
                $q->addWhere("id =" . $id);
                $record = $q->loadResult();
                $q->clear();
                $q->addTable("acquisition_planning_criteria");
                if (empty($record)) {
                    $q->addInsert("acquisition_id", $acquisitionId);
                    $q->addInsert("criteria", $criteria);
                    $q->addInsert("weight", $weight);
                } else {
                    $q->addUpdate("acquisition_id", $acquisitionId);
                    $q->addUpdate("criteria", $criteria);
                    $q->addUpdate("weight", $weight);
                    $q->addWhere("id = " . $id);
                }
                $q->exec();
                $q->clear();
            }
        }
    }

    function storeRequirements($requirements, $acquisitionId) {
        $records = explode("#$", $requirements);
        for ($i = 0; $i < sizeof($records); $i++) {
            $fields = explode("#!", $records[$i]);
            $id = $fields[0];
            if ($id != "") {
                $requirement = $fields[1];
                $q = new DBQuery();
                $q->addQuery("id");
                $q->addTable("acquisition_planning_requirements");
                $q->addWhere("id =" . $id);
                $record = $q->loadResult();
                $q->clear();
                $q->addTable("acquisition_planning_requirements");
                if (empty($record)) {
                    $q->addInsert("acquisition_id", $acquisitionId);
                    $q->addInsert("requirement", $requirement);
                } else {
                    $q->addUpdate("acquisition_id", $acquisitionId);
                    $q->addUpdate("requirement", $requirement);
                    $q->addWhere("id = " . $id);
                }
                $q->exec();
                $q->clear();
            }
        }
    }
    
    
      public function loadCriteria($acquisitionId){
        $q = new DBQuery();
        $q->addQuery("c.id, c.criteria, c.weight");
        $q->addTable("acquisition_planning_criteria", "c");
        $q->addWhere("acquisition_id =" . $acquisitionId);
        $results = db_loadHashList($q->prepare(true), "id");
        $list= array();
        $i=0;
        foreach ($results as $data) {
           $list[$i]=$data[0]."#!".$data[1]."#!".$data[2];
           $i++;
        }
        return $list;
    }
    
     public function loadRoles($acquisitionId){
        $q = new DBQuery();
        $q->addQuery("r.id, r.role, r.responsability");
        $q->addTable("acquisition_planning_roles", "r");
        $q->addWhere("acquisition_id =" . $acquisitionId);
        $results = db_loadHashList($q->prepare(true), "id");
        $list= array();
        $i=0;
        foreach ($results as $data) {
           $list[$i]=$data[0]."#!".$data[1]."#!".$data[2];
           $i++;
        }
        return $list;
    }
    
    public function loadRequirements($acquisitionId){
        $q = new DBQuery();
        $q->addQuery("r.id, r.requirement");
        $q->addTable("acquisition_planning_requirements", "r");
        $q->addWhere("acquisition_id =" . $acquisitionId);
        $results = db_loadHashList($q->prepare(true), "id");
        $list= array();
        $i=0;
        foreach ($results as $data) {
           $list[$i]=$data[0]."#!".$data[1];
           $i++;
        }
        return $list;
    }
    

}

?>

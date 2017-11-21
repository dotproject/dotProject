<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly");
}

class ProjectTaskEstimation {

    private $id = NULL;
    private $effort = NULL;
    private $effortUnit = NULL;
    private $duration = NULL;
    private $roles = NULL;

    public function getId() {
        return $this->id;
    }

    public function getEffort() {
        return $this->effort;
    }

    public function getEffortUnit() {
        return $this->effortUnit;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function getRoles() {
        return $this->roles;
    }

    function ProjectTaskEstimation() {
        
    }

    function store($idValue, $durationValue, $effortValue, $effortUnitValue, $rolesIdsValues, $rolesQuantityValues, $excludedRolesIds) {
        $q = new DBQuery();
        $q->addQuery("t.id");
        $q->addTable("project_tasks_estimations", "t");
        $q->addWhere("t.task_id= " . $idValue);
        $sql = $q->prepare();
        $tasksEstimations = db_loadList($sql);
        $q = new DBQuery();
        $q->addTable("project_tasks_estimations");
        if (sizeof($tasksEstimations) > 0) {
            $q->addUpdate("effort", $effortValue);
            $q->addUpdate("effort_unit", $effortUnitValue);
            $q->addUpdate("duration", $durationValue);
            $q->addWhere("task_id =" . $idValue);
        } else {
            $q->addInsert("effort", $effortValue);
            $q->addInsert("effort_unit", $effortUnitValue);
            $q->addInsert("task_id", $idValue);
            $q->addInsert("duration", $durationValue);
        }
        $q->exec();


         for ($i = 0; $i < sizeof($excludedRolesIds); $i++) {
            $roleId = $excludedRolesIds[$i];
            $q = new DBQuery();
            $q->setDelete("project_tasks_estimated_roles");
            $q->addWhere("task_id =" . $idValue. " and role_id=" . $roleId);
            $q->exec();
         }
        //remove old estimations
        $q = new DBQuery();
        $q->setDelete("project_tasks_estimated_roles");
        $q->addWhere("task_id =" . $idValue);
        $q->exec();
        //include new estimations
        for ($i = 0; $i < sizeof($rolesIdsValues); $i++) {
            $roleId = $rolesIdsValues[$i];
            $roleQuantity = $rolesQuantityValues[$i];
            if ($roleId != "") {
                $q = new DBQuery();
                $q->addQuery("role_id,count(role_id)");
                $q->addTable("project_tasks_estimated_roles");
                $q->addWhere("task_id =" . $idValue . " and role_id=" . $roleId);
                $q->addGroup("role_id");
                $q->exec();
                $results = $q->loadList();
                //if ($roleQuantity != $results[0]["count(role_id)"]) { //check whether had some change in the quantity of an estimated role, to avoid delete elements related to HR allocations
                    for ($j = 0; $j < intval($roleQuantity); $j++) {
                        $q = new DBQuery();
                        $q->addTable("project_tasks_estimated_roles");
                        $q->addInsert("task_id", $idValue);
                        $q->addInsert("role_id", $roleId);
                        $q->exec();
                    }
                //}
            }
        }
    }

    function load($idValue) {
        $q = new DBQuery();
        $q->addQuery("t.task_id,t.effort,t.effort_unit,t.duration");
        $q->addTable("project_tasks_estimations", "t");
        $q->addWhere("t.task_id= " . $idValue);
        $sql = $q->prepare();
        $tasksEstimations = db_loadList($sql);
        foreach ($tasksEstimations as $tasksEstimation) {
            $this->id = $tasksEstimation["task_id"];
            $this->effort = $tasksEstimation["effort"];
            $this->effortUnit = $tasksEstimation["effort_unit"];
            $this->duration = $tasksEstimation["duration"];
        }
        $this->roles = array();
        $q = new DBQuery();
        $q->addQuery("t.role_id, COUNT(*)");
        $q->addTable("project_tasks_estimated_roles", "t");
        $q->addWhere("t.task_id= " . $idValue . " group by t.task_id,t.role_id");
        $sql = $q->prepare();
        $tasksEstimations = db_loadList($sql);
        $i = 0;
        foreach ($tasksEstimations as $tasksEstimation) {
            $this->roles[$i] = new EstimatedRole($tasksEstimation["role_id"], $tasksEstimation[1]);
            $i++;
        }
    }
    
    function getRolesNonGrouped($task_id){
        $rolesNonGrouped = array();
        $q = new DBQuery();
        $q->addQuery("t.role_id, t.id");
        $q->addTable("project_tasks_estimated_roles", "t");
        $q->addWhere("t.task_id= " . $task_id );
        $sql = $q->prepare();
        $tasksEstimations = db_loadList($sql);
        $i = 0;
        foreach ($tasksEstimations as $tasksEstimation) {
            $rolesNonGrouped[$i] = new EstimatedRole($tasksEstimation["role_id"], $tasksEstimation["id"]);
            $i++;
        }
        return $rolesNonGrouped;
    }

}

class EstimatedRole {
    var $roleId = NULL;
    var $quantity = NULL;

    public function getRoleId() {
        return $this->roleId;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    function EstimatedRole($roleIdValue, $quantityValue) {
        $this->roleId = $roleIdValue;
        $this->quantity = $quantityValue;
    }

}

<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly");
}

class QualityPlanning {
    private $id = -1;
    private $projectId = null;
    private $qualityControlling = null;
    private $qualityAssurance= null;
    private $qualityPolicies=null;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    public function setProjectId($projectId) {
        $this->projectId = $projectId;
    }

    public function getQualityControlling() {
        return $this->qualityControlling;
    }

    public function setQualityControlling($qualityControlling) {
        $this->qualityControlling = $qualityControlling;
    }

    public function getQualityAssurance() {
        return $this->qualityAssurance;
    }

    public function setQualityAssurance($qualityAssurance) {
        $this->qualityAssurance = $qualityAssurance;
    }

    public function getQualityPolicies() {
        return $this->qualityPolicies;
    }

    public function setQualityPolicies($qualityPolicies) {
        $this->qualityPolicies = $qualityPolicies;
    }

    
    function QualityPlanning() {
        
    }

    function store() {
        $q = new DBQuery();
        $q->addQuery("id");
        $q->addTable("quality_planning");
        $q->addWhere("id =" . $this->getId());
        $record = $q->loadResult();
        $q->clear();
        $q->addTable("quality_planning");
        if (empty($record)) {
            $q->addInsert("project_id", $this->getProjectId());
            $q->addInsert("quality_assurance", $this->getQualityAssurance());
            $q->addInsert("quality_controlling", $this->getQualityControlling());
            $q->addInsert("quality_policies", $this->getQualityPolicies());
        } else {
            $q->addUpdate("project_id", $this->getProjectId());
            $q->addUpdate("quality_assurance", $this->getQualityAssurance());
            $q->addUpdate("quality_controlling", $this->getQualityControlling());
            $q->addUpdate("quality_policies", $this->getQualityPolicies());
            $q->addWhere("id = " . $this->getId());
        }
        $q->exec();
        $q->clear();
    }

    function load($projectId) {
        $q = new DBQuery();
        $q->addQuery("q.id, q.quality_assurance, q.quality_controlling,q.quality_policies");
        $q->addTable("quality_planning", "q");
        $q->addWhere("project_id =" . $projectId);
        $results = db_loadHashList($q->prepare(true), "q.id");
        foreach ($results as $id => $data) {
            $id = $data[0];
            $qualityAssurance = $data[1];
            $qualityControlling = $data[2];
            $qualityPolicies = $data[3];
            $this->setId($id);
            $this->setQualityAssurance($qualityAssurance);
            $this->setQualityControlling($qualityControlling);
            $this->setQualityPolicies($qualityPolicies);
            $this->setProjectId($projectId);
        }
    }

    function delete($id) {
        $q = new DBQuery();
        $q->setDelete("quality_planning");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

}

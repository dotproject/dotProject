<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly");
}

class ControlRiskMonitoring {

    function getRiskAmountByProject($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addQuery("r.risk_id");
        $q->addTable("risks", "r");
        $q->addWhere("r.risk_project=" . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return count($list);
    }

    function getMaterialzedRisksByProject($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addQuery("r.risk_id");
        $q->addTable("risks", "r");
        $q->addWhere("r.risk_project=" . $project_id . " and r.risk_status=2");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return count($list);
    }
    
   function getRisksHighPriorityByProject($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addQuery("r.risk_id");
        $q->addTable("risks", "r");
        $q->addWhere("r.risk_project=" . $project_id . " and r.risk_priority=2");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return count($list);
    }
    
    function getRisksCategories($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addQuery("r.risk_ear_classification, count(r.risk_ear_classification)");
        $q->addTable("risks", "r");
        $q->addWhere("r.risk_project=" . $project_id . " and not(r.risk_ear_classification is null)");
        $q->addGroup("r.risk_ear_classification");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

}

?>
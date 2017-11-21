<?php

require_once (DP_BASE_DIR . "/modules/timeplanning/model/quality/quality_planning.class.php");

/**
 * Class to provide quality plannig data per project.
 * @author Rafael Queiroz Gonçalves
 */
class ControllerQualityPlanning {

    function ControllerQualityPlanning() {
        
    }

    public function sendDataToBeStored($id, $projectId, $qualityAssurance, $qualityPolicies, $qualityControlling) {
        $object = new QualityPlanning();
        $object->setId($id);
        $object->setProjectId($projectId);
        $object->setQualityAssurance($qualityAssurance);
        $object->setQualityControlling($qualityControlling);
        $object->setQualityPolicies($qualityPolicies);
        $object->store();
    }

    public function getQualityPlanningPerProject($projectId) {
        $object = new QualityPlanning();
        $object->load($projectId);
        return $object;
    }

    public function saveAssuranceItem($quality_planning_id, $what, $who, $when, $how, $id) {
        $q = new DBQuery();
        $q->addTable("quality_assurance_item");
        if ($id == "") {
            $q->addInsert("quality_planning_id", $quality_planning_id);
            $q->addInsert("what", $what);
            $q->addInsert("who", $who);
            $q->addInsert("when", $when);
            $q->addInsert("how", $how);
        } else {
            $q->addUpdate("quality_planning_id", $quality_planning_id);
            $q->addUpdate("what", $what);
            $q->addUpdate("who", $who);
            $q->addUpdate("when", $when);
            $q->addUpdate("how", $how);
            $q->addWhere("id = " . $id);
        }
        $q->exec();
        $q->clear();
    }

    public function saveControlGoal($quality_planning_id, $gqm_goal_object,  $gqm_goal_propose, $gqm_goal_respect_to, $gqm_goal_point_of_view, $gqm_goal_context, $id) {
        $q = new DBQuery();
        $q->addTable("quality_control_goal");
        if ($id == "") {
            $q->addInsert("quality_planning_id", $quality_planning_id);
            $q->addInsert("gqm_goal_propose", $gqm_goal_propose);
            $q->addInsert("gqm_goal_object", $gqm_goal_object);
            $q->addInsert("gqm_goal_respect_to", $gqm_goal_respect_to);
            $q->addInsert("gqm_goal_point_of_view", $gqm_goal_point_of_view);
            $q->addInsert("gqm_goal_context", $gqm_goal_context);
        } else {
            $q->addUpdate("gqm_goal_propose", $gqm_goal_propose);
            $q->addUpdate("gqm_goal_object", $gqm_goal_object);
            $q->addUpdate("gqm_goal_respect_to", $gqm_goal_respect_to);
            $q->addUpdate("gqm_goal_point_of_view", $gqm_goal_point_of_view);
            $q->addUpdate("gqm_goal_context", $gqm_goal_context);
            $q->addWhere("id = " . $id);
        }
        $q->exec();
        $q->clear();
    }

    public function saveControlRequirement($quality_planning_id, $requirement, $id) {
        $q = new DBQuery();
        $q->addTable("quality_control_requirement");
        if ($id == "") {
            $q->addInsert("quality_planning_id", $quality_planning_id);
            $q->addInsert("requirement", $requirement);
        } else {
            $q->addUpdate("requirement", $requirement);
            $q->addWhere("id = " . $id);
        }
        $q->exec();
        $q->clear();
    }

    public function saveQuestion($goal_id, $question, $target, $id) {
        $q = new DBQuery();
        $q->addTable("quality_control_analiysis_question");
        if ($id == "") {
            $q->addInsert("goal_id", $goal_id);
            $q->addInsert("question", $question);
            $q->addInsert("target", $target);
        } else {
            //$q->addUpdate("goal_id", $goal_id);
            $q->addUpdate("question", $question);
            $q->addUpdate("target", $target);
            $q->addWhere("id = " . $id);
        }
        $q->exec();
        $q->clear();
    }

    public function saveMetric($question_id, $metric, $how_to_collect, $id) {
        $q = new DBQuery();
        $q->addTable("quality_control_metric");
        if ($id == "") {
            $q->addInsert("question_id", $question_id);
            $q->addInsert("metric", $metric);
            $q->addInsert("how_to_collect", $how_to_collect);
        } else {
            //$q->addUpdate("question_id", $question_id);
            $q->addUpdate("metric", $metric);
            $q->addUpdate("how_to_collect", $how_to_collect);
            $q->addWhere("id = " . $id);
        }
        //echo $q->prepare();
        //die();
        $q->exec();
        $q->clear();
    }

    public function deleteAssuranceItem($id) {
        $q = new DBQuery();
        $q->setDelete("quality_assurance_item");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

    public function deleteMetric($id) {
        $q = new DBQuery();
        $q->setDelete("quality_control_metric");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

    public function deleteQuestion($id) {
        $q = new DBQuery();
        $q->setDelete("quality_control_analiysis_question");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

    public function deleteControlRequirement($id) {
        $q = new DBQuery();
        $q->setDelete("quality_control_requirement");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

    public function deleteControlGoal($id) {
        $q = new DBQuery();
        $q->setDelete("quality_control_goal");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }
    
    
    public function loadAssuranceItems($quality_planning_id) {
        $q = new DBQuery();
        $q->addQuery("id, what, who, `when`, how");
        $q->addTable("quality_assurance_item", "q");
        $q->addWhere("quality_planning_id =" . $quality_planning_id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        return $records; 
    }
    
  public function loadControlRequirements($quality_planning_id) {    
        $q = new DBQuery();
        $q->addQuery("id, requirement");
        $q->addTable("quality_control_requirement", "q");
        $q->addWhere("quality_planning_id =" . $quality_planning_id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        return $records;     
    }

    public function loadControlGoals($quality_planning_id) {
        $q = new DBQuery();
        $q->addQuery("id, gqm_goal_propose, gqm_goal_object, gqm_goal_respect_to, gqm_goal_point_of_view, gqm_goal_context");
        $q->addTable("quality_control_goal");
        $q->addWhere("quality_planning_id = " . $quality_planning_id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        return $records; 
    }
    
      public function loadQuestions($goal_id) {
        $q = new DBQuery();
        $q->addQuery("id, question, target");
        $q->addTable("quality_control_analiysis_question", "q");
        $q->addWhere("goal_id =" . $goal_id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        return $records;  
    }
    
    public function loadMetrics($question_id) {
        $q = new DBQuery();
        $q->addQuery("id, metric, how_to_collect");
        $q->addTable("quality_control_metric", "q");
        $q->addWhere("question_id =" . $question_id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        return $records;  
    } 

}

?>

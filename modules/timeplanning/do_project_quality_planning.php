<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/quality/controller_quality_planning.class.php");
$projectId = dPgetParam($_POST, "project_id");
$qualityPolicies = dPgetParam($_POST,"quality_policies");
$qualityAssurance= dPgetParam($_POST,"quality_assurance");
$qualityControlling= dPgetParam($_POST,"quality_controlling");
$id= dPgetParam($_POST,"quality_planning_id");
$controller  = new ControllerQualityPlanning (); 
$controller->sendDataToBeStored($id, $projectId, $qualityAssurance, $qualityPolicies, $qualityControlling);

//1. get audit items - Cost: n - number of audit itens

$n=$_POST["number_audit_items"];
for($i=0;$i<$n;$i++){
    $controller->saveAssuranceItem($id, $_POST["what_audit_$i"], $_POST["who_audit_$i"], $_POST["when_audit_$i"], $_POST["how_audit_$i"], $_POST["audit_item_id_$i"]);
}

//2. get requirements - Cost: n - number of requirements
$n = $_POST["number_requirements"];
for ($i = 0; $i < $n; $i++) {
    $controller->saveControlRequirement($id, $_POST["requirement_$i"], $_POST["requirement_id_$i"]);
}

//3. get goals - Cost: n - number of goals
$n=$_POST["number_goals"];
for($i=0;$i<$n;$i++){
   $controller->saveControlGoal($id, $_POST["gqm_goal_object_$i"],$_POST["gqm_goal_propose_$i"], $_POST["gqm_goal_respect_to_$i"], $_POST["gqm_goal_point_of_view_$i"], $_POST["gqm_goal_context_$i"], $_POST["goal_id_$i"]);
   //3.1. Get questions
   $goal_id=$_POST["goal_id_$i"];
   $q=$_POST["number_questions_$goal_id"];
   
   for($j=0;$j<$q;$j++){
       $controller->saveQuestion($goal_id, $_POST["analysis_question_".$i."_".$j], $_POST["analysis_question_".$i."_benchmark_".$j], $_POST["question_".$i."_id_".$j]);
       //3.1.1 get metrics - cost m - number or metrics
       $question_id=$_POST["question_".$i."_id_".$j];
       $m=$_POST["number_metrics_$question_id"];
        for($k=0;$k<$m;$k++){
           $controller->saveMetric($question_id, $_POST["metric_".$k."_qoa_".$question_id], $_POST["metric_".$k."_qoa_".$question_id."_data_collection"], $_POST["metric_".$k."_qoa_".$question_id."_id"]);
        }
   }
}
 //Total cost: n(audit items) + n (requirements) + n (goals)*q(questions)*k(metrics)
$action=$_POST["form_action"];
if($action==1){
    $controller->saveAssuranceItem($id, "", "", "", "", "");
    $msg="LBL_AUDIT_ITEM_INCLUDED";
}else if($action==2){
    $controller->saveControlRequirement($id, "", "");
     $msg="LBL_REQUIREMENT_INCLUDED";
}else if ($action==3){
    $controller->saveControlGoal($id, "","", "", "", "", "");
    $msg="LBL_GOAL_INCLUDED";
}else if ($action==4){
    $goal_id=$_POST["goal_id_new_question"];
    $controller->saveQuestion($goal_id, "", "", "");
    $msg="LBL_ANALYSIS_QUESTION_INCLUDED";
}else if ($action==5){
    $question_id=$_POST["question_id_new_metric"];
    $controller->saveMetric($question_id,"", "", "");
    $msg="LBL_METRIC_INCLUDED";
}else if ($action==6){
    $delete_id=$_POST["id_for_delete"];
    $controller->deleteMetric($delete_id);
    $msg="LBL_METRIC_EXCLUDED";
}else if ($action==7){
    $delete_id=$_POST["id_for_delete"];
    $controller->deleteQuestion($delete_id);
    $msg="LBL_ANALYSIS_QUESTION_EXCLUDED";
}else if ($action==8){
    $delete_id=$_POST["id_for_delete"];
    $controller->deleteControlGoal($delete_id);
    $msg="LBL_GOAL_EXCLUDED";
}else if ($action==9){
    $delete_id=$_POST["id_for_delete"];
    $controller->deleteControlRequirement($delete_id);
    $msg="LBL_REQUIREMENT_EXCLUDED";
}else if ($action==10){
    $delete_id=$_POST["id_for_delete"];
    $controller->deleteAssuranceItem($delete_id);
    $msg="LBL_AUDIT_ITEM_EXCLUDED";
}else {
    $msg="LBL_QUALITY_PLAN_REGISTERED";
}
$AppUI->setMsg($AppUI->_($msg,UI_OUTPUT_HTML), UI_MSG_OK);
$AppUI->redirect("m=projects&a=view&project_id=".$projectId."&targetScreenOnProject=/modules/timeplanning/view/quality/project_quality_planning.php");
?>

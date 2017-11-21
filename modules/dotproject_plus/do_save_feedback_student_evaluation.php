<?php
require_once (DP_BASE_DIR . "/modules/dotproject_plus/feedback/user_feedback_evaluation/feedback_evaluation.class.php");
$evaluation=new CFeedbackEvaluation();
$evaluation->feedback_id=dPgetParam($_GET, 'feedback_id');
$evaluation->user_id=dPgetParam($_GET, 'user_id');
$evaluation->grade=dPgetParam($_GET, 'grade');
$evaluation->store();
?>
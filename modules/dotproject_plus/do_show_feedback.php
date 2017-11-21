<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/dotproject_plus/feedback/feedback.php");
require_once (DP_BASE_DIR . "/modules/dotproject_plus/feedback/feedback_controller.php");
$url = $_POST['url'];
$feedback_id=$_POST["feedback_id"];

//$AppUI->setMsg($feedback_list[$feedback_id]->getDescription(), '',true);
//remove feedback from user feedback list
session_start();
unset($_SESSION["user_feedback"][$feedback_id]);
$_SESSION["user_feedback_read"][$feedback_id]=$feedback_id;
$feedbackManager=new InstructionalFeebackManager();
$feedbackManager->logFeedbackRead($AppUI->user_id, $feedback_id);
$img="<div style='text-align:left'><img src='./style/default/images/icon_info.png' />&nbsp;&nbsp;&nbsp;";
$img.="<img src='./style/dotproject_plus/img/feedback/". InstructionalFeebackManager::getIconByKnowledgeArea($feedback_list[$feedback_id]->getKnowledgeArea()) .".png' style='width:25px; height: 25px' />&nbsp;&nbsp;&nbsp;";
if(!$feedback_list[$feedback_id]->getGeneric()){
    $img.="<img src='./style/dotproject_plus/img/feedback/TCC_icon.png' style='width:25px; height: 25px' />";
}
$img.="</div><br />";
$title="<b>:::: ". $AppUI->_("LBL_FEEDBACK_FOR_THE KNOWLEDGEAREA_OF") ." ". $feedback_list[$feedback_id]->getKnowledgeArea() ." :::: </b> <br /> <br />";

$rating= $AppUI->_("LBL_FEEDBACK_USEFUL")."<br />
<script>
function saveRatingFeedbackEvaluation(){
    
    var grade=$('input[name=star]:checked').val();
    var url='index.php?m=dotproject_plus&dosql=do_save_feedback_student_evaluation&feedback_id=$feedback_id&user_id=$AppUI->user_id&grade='+grade;
    //window.open(url);
    $.get(url);
    
}
</script>
<link rel='stylesheet' href='./modules/dotproject_plus/feedback/user_feedback_evaluation/start_rating.css' />
<form id='ratingsForm'>
	<div class='stars'>
		<input type='radio' name='star' class='star-1' value='1' id='star-1' onchange=saveRatingFeedbackEvaluation() />
		<label class='star-1' for='star-1'>1</label>
		<input type='radio' name='star' class='star-2' value='2' id='star-2' onchange=saveRatingFeedbackEvaluation() />
		<label class='star-2' for='star-2'>2</label>
		<input type='radio' name='star' class='star-3' value='3' id='star-3' onchange=saveRatingFeedbackEvaluation() />
		<label class='star-3' for='star-3'>3</label>
		<input type='radio' name='star' class='star-4' value='4' id='star-4' onchange=saveRatingFeedbackEvaluation() />
		<label class='star-4' for='star-4'>4</label>
		<input type='radio' name='star' class='star-5' value='5' id='star-5' onchange=saveRatingFeedbackEvaluation() />
		<label class='star-5' for='star-5'>5</label>
		<span></span>
	</div>
  
</form>
";

$_SESSION["user_feedback_display_message"]=$img . $title . $feedback_list[$feedback_id]->getDescription(). "<br /><br />". $rating;
$AppUI->redirect($url);

?>

<pre>
<?php //print_r($_SESSION["user_feedback"]); ?>    
</pre>
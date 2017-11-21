<?php
//header("Content-Type: text/html; charset=utf-8",true);
//header('Content-Type: application/vnd.ms-word');
require_once DP_BASE_DIR . "/modules/instructor_admin/PHPWord_0.6.2_Beta/PHPWord.php";
require_once DP_BASE_DIR . "/modules/dotproject_plus/feedback/feedback_controller.php";
require_once DP_BASE_DIR."/modules/human_resources/configuration_functions.php";



$user_id=$_GET["user_id"];
$project=new CProject();
$project->load($_GET["project_id"]);
// New Word Document
$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate(DP_BASE_DIR . "/modules/instructor_admin/template_avaliacao.docx"); //deve ser salvo mantendo compatibilidade com versões anteriores
//integrantes dos grupos
$document->setValue('value00', $user_id);
$res = getDetailedUsersByCompanyId($project->project_company);
$members="";
for ($res; !$res->EOF; $res->MoveNext()) {
    $members.= $res->fields["contact_first_name"]. " ". $res->fields["contact_last_name"] . "; ";
}
$document->setValue('value01', utf8_decode($members));

//Termo de abertura do projeto

$document->setValue('value11', ' ');	

$document->setValue('value12', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_INTEGRATION")));

//Planejamento do escopo
$document->setValue('value21', ' ');	
$document->setValue('value22', ($feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_SCOPE"))));

//Planejamento de tempo 
$document->setValue('value31', ' ');	
$document->setValue('value32', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_TIME")));
//Planejamento  de custo 
$document->setValue('value41', ' ');	
$document->setValue('value42', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_COST")));
//Planejamento de qualidade 
$document->setValue('value51', ' ');	
$document->setValue('value52', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_QUALITY")));
//Planejamento de RH e comunicações
$document->setValue('value61', ' ');	
$document->setValue('value62', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_HR")) . "; " . $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_COMUNICATION"))); 
//Planejamento de aquisições 
$document->setValue('value71', ' ');	
$document->setValue('value72', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_ACQUISITIONS")));
//Planejamento de riscos 
$document->setValue('value81', ' ');	
$document->setValue('value82', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("LBL_FEEDBACK_RISK")));
//Planejamento de stakeholders
$document->setValue('value91', ' ');	
$document->setValue('value92', $feedbackManager->getEvaluationMessagesPerKA($AppUI->_("Stakeholder")));

//nota do trabalho escrito
$document->setValue('value100', '0.00');

//Preparação
$document->setValue('value111', ' ');
//Organização 
$document->setValue('value121', ' ');
//Uso de linguagem
$document->setValue('value131', ' ');
//Perguntas
$document->setValue('value141', ' ');
//Duração da apresentação
$document->setValue('value151', ' ');

//nota da apresentação
$document->setValue('value200', '0.00');

//nota geral
$document->setValue('value300', '0.00');

// Save File
$fileName="modules/timeplanning/view/export_project_plan/temp/avaliacao_grupo_". $user_id .".docx";
unlink ($fileName);
$document->save($fileName);
header("location:".$fileName);
?>

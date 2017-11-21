<?php
require_once (DP_BASE_DIR . "/modules/dotproject_plus/copy_project/ProjectTemplate.php");
$projectTemplate= new ProjectTemplate();
$sourceProjectId=$_POST["project_to_copy"];
$targetProjectId=$_POST["target_project_id"];
$result=$projectTemplate->closeWBS($sourceProjectId, $targetProjectId);
if($result===0){
    $AppUI->setMsg( $AppUI->_("LBL_COPY_FROM_TEMPLATE_SUCCESS" ,UI_OUTPUT_HTML), UI_MSG_OK, true);
}else if ($result===1){
    $AppUI->setMsg( $AppUI->_("LBL_COPY_FROM_TEMPLATE_ERROR_1",UI_OUTPUT_HTML), UI_MSG_ERROR, true);
}
$AppUI->redirect('m=projects&a=view&project_id='.$targetProjectId);
?>
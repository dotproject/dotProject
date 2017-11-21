<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/need_for_training.class.php");
$projectId = dPgetParam($_GET, "project_id", 0);
$description = $_POST["need_for_training"];
$obj = new NeedForTraining();
$obj->setId($projectId);
$obj->setDescription($description);
$obj->store();
$AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK, true);
$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&show_external_page=/modules/timeplanning/view/need_for_training.php#gqs_anchor");
//$AppUI->redirect("m=projects&a=view&project_id=" . $projectId);
?>

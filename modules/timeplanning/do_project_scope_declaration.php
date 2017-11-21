<?php
require_once (DP_BASE_DIR . '/modules/projects/projects.class.php');
$projectId = dPgetParam($_POST, "project_id", 0);
$description = $_POST["scope_declaration"];
$obj = new CProject();
$obj->load($projectId);
$obj->project_description = $description;
$obj->store();
$AppUI->setMsg($AppUI->_("Declaração do escopo registrada!",UI_OUTPUT_HTML), UI_MSG_OK, true);
$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&show_external_page=/modules/timeplanning/view/scope_declaration.php#gqs_anchor");
//$AppUI->redirect("m=projects&a=view&project_id=" . $projectId);
?>

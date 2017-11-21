<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
global $AppUI;
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_respons.class.php");
$respons = new ControllerRespons();
$id = dPgetParam($_POST, 'responsibility_id');
$index = dPgetParam($_POST, 'index');
$description = dPgetParam($_POST, 'description');
$consultation = dPgetParam($_POST, 'consultation');
$execut = dPgetParam($_POST, 'execut');
$support = dPgetParam($_POST, 'support');
$approve = dPgetParam($_POST, 'approve');
$project_id = dPgetParam($_POST, 'project_id');


if (isset($_POST['acao']) && $_POST['acao'] == 'insert') {

    $respons->insert($index, $description, $consultation, $execut, $support, $approve, $project_id);
} elseif (isset($_POST['acao']) && $_POST['acao'] == 'delete') {

    $respons->deleteRow($id);
} elseif (isset($_POST['acao']) && $_POST['acao'] == 'updateRow') {

    $respons->updateRecords($index, $description, $consultation, $execut, $support, $approve, $project_id, $id);
}
$AppUI->redirect('m=projects&a=view&project_id=' . $project_id);
?>

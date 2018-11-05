<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once DP_BASE_DIR . "/modules/scope_and_schedule/wbs_item.class.php";

$id=intval(dPgetParam($_POST, "id"));
$obj = new WBSItem();
$obj->load($id);
$obj->wbs_dictionary = dPgetParam($_POST, "dictionary");
$obj->store();
$AppUI->redirect();
?>

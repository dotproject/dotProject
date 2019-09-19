<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
session_start();
$AppUI->savePlace();
?>

<?php
require_once DP_BASE_DIR . "/modules/scope_and_schedule/view_project_wbs.php";
?>
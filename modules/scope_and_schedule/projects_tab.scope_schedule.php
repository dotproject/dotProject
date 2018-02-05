<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
session_start();
$AppUI->savePlace();
?>
<?php $project_id = dPgetParam($_GET, "project_id", 0); ?>




<?php
echo $project_id;
?>
<?php
require_once (DP_BASE_DIR . '/modules/projects/projects.class.php');
if (isset($_GET["project_id"])) {
    $projectId = $_GET["project_id"];
    if ($projectId != "") {
        $objProjectLink = new CProject();
        $objProjectLink->load($projectId);
        ?>
        <br />
        <?php echo $AppUI->_("Project"); ?>: <a href="index.php?m=projects&a=view&project_id=<?php echo $projectId ?>&tab=1"><?php echo $objProjectLink->project_name ?></a>
        <br />
        <?php
    }
}
?>

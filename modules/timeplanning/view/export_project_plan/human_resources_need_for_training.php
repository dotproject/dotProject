<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/need_for_training.class.php");
$obj = new NeedForTraining();
$obj->load($projectId);
?>
<p class="print_p">
    <?php echo str_ireplace("\n", "<br />", $obj->getDescription()); ?>
</p>

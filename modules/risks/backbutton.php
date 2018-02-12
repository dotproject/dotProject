<?php
    if (isset($_GET['project_id'])) {?>
        <input type="button" onClick="riskModule.backToProject(<?php echo $_GET['project_id'] ?>)" class="button" style="font-weight: bold" value="<?php echo $AppUI->_('LBL_CANCEL') ?>">
        <?php
    }
?>
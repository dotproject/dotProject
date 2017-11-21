<?php require_once (DP_BASE_DIR . "/modules/dotproject_plus/copy_project/CopyProjectViewHelper.php"); ?>
<div id="dialog-form" style="display: none" title="<?php echo $AppUI->_("LBL_COPY_FROM_TEMPLATE") ?>">
    <p><?php echo $AppUI->_("LBL_COPY_FROM_TEMPLATE_HELP") ?></p>
    <form id="copy_project_form" method="post"  action="?m=dotproject_plus">
        <input name="dosql" type="hidden" value="do_copy_project" />
        <input name="target_project_id" type="hidden" value="<?php echo $project_id ?>" />
        <label for="project_to_copy"><?php echo $AppUI->_("LBL_PROJECT") ?></label>:
        <?php
            $copyProjectViewHelper = new CopyProjectViewHelper();
            echo $copyProjectViewHelper->getProjectsCombo();
        ?>
    </form>
</div>
<script>
    function copyProject(){
        $("#copy_project_form").submit();
    }
    var dialogCopyProject = $("#dialog-form").dialog({
        autoOpen: false,
        height: 200,
        width: 300,
        modal: true,
        buttons: {
            "<?php echo $AppUI->_("LBL_COPY") ?>": copyProject,
            Cancel: function () {
                dialogCopyProject.dialog("close");
            }
        },
        close: function () {

        }
    });
</script>
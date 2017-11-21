<?php require_once (DP_BASE_DIR . "/modules/timeplanning/model/need_for_training.class.php"); ?>
<!--
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        width : "95%",
        height: "340",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "hr,removeformat,sub,sup,charmap"
    });
</script>
-->
<style>
    textarea{
        width:950%;
        height: 300px;
        text-align: left;
    }
</style>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<?php $projectId = dPgetParam($_GET, "project_id", 0); ?>
<form action="?m=timeplanning&a=view&project_id=<?php echo $project_id; ?>" method="post" name="form_wbs_dictionary" id="form_wbs_dictionary">
    <input name="dosql" type="hidden" value="do_project_scope_declaration" />
    <input name="eap_items_ids" id="eap_items_ids" type="hidden" />	
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $projectId; ?>" />
    <?php
    $obj = new CProject();
    $obj->load($projectId);
    ?>
    <br />
    <table class="std" width="95%" align="center">   
        <tr>
            <th align="center" style="font-weight: bold">
                <?php echo $AppUI->_("LBL_PROJECT_SCOPE_DECLARATION"); ?>
            </th>
        </tr>
        <tr>
            <td align="center">
                <textarea name="scope_declaration"><?php echo $obj->project_description ?></textarea>
            </td>
        </tr>
        <tr>
            <td align="right">
                <input type="submit" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" class="button" />
                <script> var targetScreenOnProject="/modules/dotproject_plus/projects_tab.planning_and_monitoring.php";</script>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
        </tr>
    </table>
</form>
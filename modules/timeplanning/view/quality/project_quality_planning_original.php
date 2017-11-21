<?php
    require_once (DP_BASE_DIR . "/modules/timeplanning/control/quality/controller_quality_planning.class.php");
    $controller= new ControllerQualityPlanning();
    $object=$controller->getQualityPlanningPerProject($_GET["project_id"]);
?>
<a name="project_quality_planning"></a>
<style>
    textarea{
        width:80%;
        height: 150px;
        text-align: left;
    }
</style>
<!--
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        width : "90%",
        height: "240",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "hr,removeformat,sub,sup,charmap"
    });
</script>
-->
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form name="decomposition_form" method="post" action="?m=timeplanning">
    <input name="dosql" type="hidden" value="do_project_quality_planning" />
    <input name="project_id" type="hidden" value="<?php echo $_GET["project_id"]; ?>" />
    <input name="quality_planning_id" type="hidden" value="<?php echo $object->getId() ?>" />
    <br/>
    <table class="std" align="center" width="95%" style="border: 1px solid black">
        <tr>
            <th colspan="2" align="center"><?php echo $AppUI->_("LBL_QUALITY_PLANNING"); ?></th>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_QUALITY_POLICIES"); ?>:</td>
            <td nowrap>
                <textarea name="quality_policies"><?php echo $object->getQualityPolicies() ?></textarea>
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_QUALITY_ASSURANCE"); ?>:</td>
            <td nowrap>
                <textarea name="quality_assurance"><?php echo $object->getQualityAssurance() ?></textarea>
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_QUALITY_CONTROLLING"); ?>:</td>
            <td nowrap>
                <textarea name="quality_controlling"><?php echo $object->getQualityControlling() ?></textarea>
            </td>	
        </tr>
    </table>
    <table width="95%" align="center">
        <tr>
            <td colspan="2" align="right">
                <input type="submit" name="Salvar" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" class="button" />
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
        </tr>
    </table>
</form>
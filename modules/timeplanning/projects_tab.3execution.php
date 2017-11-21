<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
session_start();
$AppUI->savePlace();
?>
<?php $project_id = dPgetParam($_GET, "project_id", 0); ?>
<?php
$tab = dPgetParam($_GET, "tab", 0);
if ($tab != "") {
    $_SESSION["gqs_tab"] = $tab;
}
?>

<style>
    .tdLabel{
        width:80px;
    }
</style>

<script>
    function submitMenuForm(){
        document.gqs_feature_menu.submit();
    }
</script>

<form action="index.php?a=view&m=projects&project_id=<?php echo $project_id ?>&tab=<?php echo $_SESSION["gqs_tab"] ?>#gqs_anchor" method="post" name="gqs_feature_menu">    
    <input type="radio" name="user_choosen_feature_execution" value="<?php echo $_SESSION["user_choosen_feature_execution"] ?>" checked="true" style="display:none"  />
    <?php require "modules/timeplanning/view/buttons_over_menu.php"; ?>
    <table class="std" width="95%" align="center" >   
        <tr>
            <th colspan="2" align="center">
                <?php require "modules/timeplanning/view/generic_menu_title.php" ?>
            </th>
        </tr>
        <tr>
            <th class="tdLabel" valign="top" >
                <?php echo $AppUI->_("3execution") ?>
            </th>

            <td style="height: 90px;vertical-align: text-top">
                <a href="index.php?m=calendar&a=day_view" />
                    <?php echo $AppUI->_("LBL_PROJECT_MY_ACTIVITIES"); ?>
                </a>            
                <br />
                <input type="radio" name="user_choosen_feature_execution" value="/modules/timeplanning/view/project_activities_execution.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_ACTIVITIES_MONITORING"); ?>
                
                <input type="radio" name="user_choosen_feature_execution" value="/modules/timeplanning/view/acquisition/acquisition_execution.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_ACQUISITION_EXECUTION"); ?>
                
                
                
            </td>
        </tr>
    </table>
</form>
<br />
<a name="gqs_anchor"></a>
<?php
$path = $_POST["user_choosen_feature_execution"];
if ($path != "") {
    $_SESSION["user_choosen_feature_execution"] = $path;
} else {
    $path = $_SESSION["user_choosen_feature_execution"];
}
if (file_exists(DP_BASE_DIR . $path) && $path != "") {
    include (DP_BASE_DIR . $path);
}
?>
<!-- Create little spacing before page finishes: layout only -->
<div style="margin-top: 40px"></div>
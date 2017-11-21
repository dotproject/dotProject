<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

session_start();
$AppUI->savePlace();
?>
<?php $company_id = dPgetParam($_GET, "company_id", 0); ?>
<?php
$tab = dPgetParam($_GET, "tab", 0);
if ($tab != "") {
    $_SESSION["gqs_tab"] = $tab;
}
?>

<script>
    function submitMenuForm(){
        document.gqs_feature_menu.submit();
    }
</script>

<form action="index.php?a=view&m=companies&company_id=<?php echo $company_id ?>&tab=<?php echo $_SESSION["gqs_tab"] ?>#gqs_anchor" method="post" name="gqs_feature_menu">    
    <input type="radio" name="user_choosen_feature_company" value="<?php echo $_SESSION["user_choosen_feature_company"] ?>" checked="true" style="display:none"  />
    <?php require "modules/timeplanning/view/buttons_over_menu.php"; ?>
    <table class="std" width="95%" align="center">   
        <tr>
            <th colspan="2">
                <?php require "modules/timeplanning/view/generic_menu_title.php" ?>
            </th>
        </tr>
        <tr>
            <th ><?php echo $AppUI->_("Configuration") ?></th>
            <td>
                <input type="radio" name="user_choosen_feature_company" value="/modules/human_resources/vw_policies.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("Companies policies") ?>

                <input type="radio" name="user_choosen_feature_company" value="/modules/human_resources/view_company_roles.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("Roles") ?>

                <input type="radio" name="user_choosen_feature_company" value="/modules/timeplanning/companies_organizational_diagram.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_ORGANIZATIONAL_CHART") ?>

                <input type="radio" name="user_choosen_feature_company" value="/modules/human_resources/view_company_users.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("HR Configurations") ?>
            </td>
        </tr>
        <tr>
            <th ><?php echo $AppUI->_("1LBLMONITORAMENTO") ?></th>
            <td>
                <input type="radio" name="user_choosen_feature_company" value="/modules/monitoringandcontrol/companies_monitoring.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("1LBLMONITORAMENTO") ?>
                <br />
            </td>
        </tr>
        <tr>
            <th ><?php echo $AppUI->_("LBL_EXPERIENCE_FACTORY") ?></th>
            <td>
                <input type="radio" name="user_choosen_feature_company" value="/modules/closure/companies_experience_factory.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_EXPERIENCE_FACTORY") ?>
                <br />
            </td>
        </tr>
    </table>
</form>
<br />
<a name="gqs_anchor"></a>
<?php
$path = $_POST["user_choosen_feature_company"];
if ($path != "") {
    $_SESSION["user_choosen_feature_company"] = $path;
} else {
    $path = $_SESSION["user_choosen_feature_company"];
}
if (file_exists(DP_BASE_DIR . $path) && $path != "") {
    include (DP_BASE_DIR . $path);
}
?>
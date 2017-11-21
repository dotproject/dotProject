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
    <input type="radio" name="user_choosen_feature_mc" value="<?php echo $_SESSION["user_choosen_feature_mc"] ?>" checked="true" style="display:none"  />
    
    <?php require "modules/timeplanning/view/buttons_over_menu.php"; ?>
    <table class="std" width="95%" align="center" >   
        <tr>
            <th colspan="2" align="center">
                    <?php require "modules/timeplanning/view/generic_menu_title.php" ?>
            </th>

        </tr>
        <tr>
            <th class="tdLabel" valign="top" >
                <?php echo $AppUI->_("LBL_MC_RECORDS") ?>
            </th>
            <th class="tdLabel" valign="top" >
                <?php echo $AppUI->_("LBL_MC_REPORTS") ?>
            </th>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td>
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.1LBLBASELINE.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("1LBLBASELINE") ?>
                        </td>
                    </tr>

                    <tr>

                        <td >
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.2LBLRESPONSABILIDADE.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("2LBLRESPONSABILIDADE") ?>
                        </td>
                    </tr>

                    <tr>

                        <td>
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.3LBLACAOCORRETIVA.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("3LBLACAOCORRETIVA") ?>
                        </td>
                    </tr>

                    <tr>

                        <td>
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.4LBLATA.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("4LBLATA") ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>

                        <td>
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.5LBLCUSTO.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("5LBLCUSTO") ?>
                        </td>
                    </tr>

                    <tr>

                        <td>
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.6LBLCRONOGRAMA.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("6LBLCRONOGRAMA") ?>
                        </td>
                    </tr>

                    <tr>

                        <td>
                            <input type="radio" name="user_choosen_feature_mc" value="/modules/monitoringandcontrol/view/view.7LBLQUALIDADE.php" onclick="submitMenuForm()" />
                            <?php echo $AppUI->_("7LBLQUALIDADE") ?>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                </table>
            </td>
        </tr>
    </table>
</form>

<a name="gqs_anchor"></a>
<?php
$path = $_POST["user_choosen_feature_mc"];
if ($path != "") {
    $_SESSION["user_choosen_feature_mc"] = $path;
} else {
    $path = $_SESSION["user_choosen_feature_mc"];
}
if (file_exists(DP_BASE_DIR . $path) && $path != "") {
    include (DP_BASE_DIR . $path);
}
?>
<!-- Create little spacing before page finishes: layout only -->
<div style="margin-top: 40px"></div>
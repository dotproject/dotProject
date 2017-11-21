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
}else{
    $_SESSION["gqs_tab"] = 0;
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

<form action="index.php?a=view&m=projects&project_id=<?php echo $project_id ?>&tab=<?php echo $_SESSION["gqs_tab"] ?>" method="post" name="gqs_feature_menu">    
    <input type="radio" name="user_choosen_feature_initiating" value="<?php echo $_SESSION["user_choosen_feature_initiating"] ?>" checked="true" style="display:none"  />
    <div style="width:100%; height: 22px;vertical-align: middle;">
        <div style="width:100%;height: 5px">&nbsp;</div><!-- div just to create a space before menu links -->
        <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature_initiating.value='/modules/initiating/addedit.php';submitMenuForm();" style="<?php echo $_POST["user_choosen_feature_initiating"]=="/modules/initiating/addedit.php" ? "font-weight: bold;font-size:14px": "" ?>" >
            <?php echo $AppUI->_("LBL_OPEN_PROJECT_CHARTER",UI_OUTPUT_HTML) ?>
        </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature_initiating.value='/modules/stakeholder/project_stakeholder.php';submitMenuForm();" style="<?php echo $_POST["user_choosen_feature_initiating"]=="/modules/stakeholder/project_stakeholder.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER",UI_OUTPUT_HTML); ?> 
         </a>
    </div>
        
    <?php //require "modules/timeplanning/view/buttons_over_menu.php"; ?>
    <!--
    <table class="std" width="95%" align="center" >   
        <tr>
            <th colspan="2" align="center">
               <?php require "modules/timeplanning/view/generic_menu_title.php" ?>
            </th>
        </tr>
        <tr>
            <th class="tdLabel" valign="top" >
                <?php echo $AppUI->_("1initiation") ?>
            </th>
           
            <td style="height: 90px;vertical-align: text-top">        
                <input type="radio" name="user_choosen_feature_initiating" value="/modules/initiating/addedit.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_OPEN_PROJECT_CHARTER") ?>
                <span style="margin-left: 20px"></span>
                <input type="radio" name="user_choosen_feature_initiating" value="/modules/stakeholder/project_stakeholder.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER"); ?>             
            </td>
        </tr>
    </table>
    -->
</form>
<br />
<a name="gqs_anchor"></a>
<?php
$path = $_POST["user_choosen_feature_initiating"];
if ($path != "") {
    $_SESSION["user_choosen_feature_initiating"] = $path;
} else {
    $path = $_SESSION["user_choosen_feature_initiating"];
}

if($path==""){
    $path="/modules/initiating/addedit.php";//deafult page 
}

if (file_exists(DP_BASE_DIR . $path) && $path != "") {
    include (DP_BASE_DIR . $path);
}
?>
<!-- Create little spacing before page finishes: layout only -->
<div style="margin-top: 40px"></div>
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
    $_SESSION["gqs_tab"]=1;
}
?>

<script>
    function submitMenuForm(){
        document.gqs_feature_menu.action+="&targetScreenOnProject="+document.gqs_feature_menu.user_choosen_feature.value;
        document.gqs_feature_menu.submit();
    }
</script>

<form action="index.php?a=view&m=projects&project_id=<?php echo $project_id ?>&tab=<?php echo $_SESSION["gqs_tab"] ?>" method="post" name="gqs_feature_menu">    
    <input type="radio" name="user_choosen_feature" value="<?php echo $_SESSION["user_choosen_feature"] ?>" checked="true" style="display:none"  />
 

  <div style="width:100%; height: 22px;vertical-align: middle;">
        <div style="width:100%;height: 5px">&nbsp;</div><!-- div just to create a space before menu links -->
        <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/dotproject_plus/projects_tab.planning_and_monitoring.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/dotproject_plus/projects_tab.planning_and_monitoring.php" ? "font-weight: bold;font-size:14px": "" ?>" >
            <?php echo $AppUI->_("LBL_ATIVIDADE",UI_OUTPUT_HTML) ?>
        </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/monitoringandcontrol/view/view.6LBLCRONOGRAMA.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/monitoringandcontrol/view/view.6LBLCRONOGRAMA.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("6LBLCRONOGRAMA",UI_OUTPUT_HTML); ?> 
         </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/costs/view_costs.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/costs/view_costs.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("5LBLCUSTO",UI_OUTPUT_HTML); ?> 
         </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/risks/projects_risks.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/risks/projects_risks.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("LBL_PROJECT_RISKS",UI_OUTPUT_HTML); ?> 
         </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/timeplanning/view/quality/project_quality_planning.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/timeplanning/view/quality/project_quality_planning.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("7LBLQUALIDADE",UI_OUTPUT_HTML); ?> 
         </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/communication/index_project.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/communication/index_project.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION",UI_OUTPUT_HTML); ?> 
         </a>
         &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/timeplanning/view/acquisition/acquisition_planning.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/timeplanning/view/acquisition/acquisition_planning.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("LBL_PROJECT_ACQUISITIONS",UI_OUTPUT_HTML); ?> 
         </a>
          &nbsp;|&nbsp;
         <a href="#" onclick="document.gqs_feature_menu.user_choosen_feature.value='/modules/stakeholder/project_stakeholder.php';submitMenuForm();" style="<?php echo $_SESSION["user_choosen_feature"]=="/modules/stakeholder/project_stakeholder.php" ? "font-weight: bold;font-size:14px": "" ?>">
            <?php echo $AppUI->_("Stakeholder",UI_OUTPUT_HTML); ?> 
         </a>
          &nbsp;|&nbsp;      
          <a href = "modules/timeplanning/view/export_project_plan/export.php?project_id=<?php echo $project_id; ?>&print=0" target = "_blank" style="font-weight: bold">
                    <?php echo $AppUI->_("LBL_PROJECT_PLAN") ?>
         </a>
    </div>
   <?php// require "modules/timeplanning/view/buttons_over_menu.php"; ?>
    
    <!--
    
                    <?php require "modules/timeplanning/view/generic_menu_title.php" ?>
    
            <th><?php echo $AppUI->_("LBL_PLANNING") ?></th>

        </tr>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_INTEGRATION") ?>
            </th>
            <td>
                <?php
                require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
                $initiating = CInitiating::findByProjectId($project_id);
                if (is_null($initiating)) {
                    ?>
                    <div style="color:red">
                        <?php echo $AppUI->_("LBL_NO_PROJECT_CHARTER") ?>
                    </div>
                <?php } ?>

                <a href = "modules/timeplanning/view/export_project_plan/export.php?project_id=<?php echo $project_id; ?>&print=0" target = "_blank">
                    <?php echo $AppUI->_("LBL_PROJECT_PLAN")
                    ?>
                </a>

            </td>
        </tr>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_SCOPE") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/projects_wbs.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("1LBLWBS") ?>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/projects_wbs_dictionary.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_WBS_DICTIONARY") ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_TIME") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/projects_derivation.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("2LBLDERIVATION") ?>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/projects_mdp.php" onclick="submitMenuForm()"  />
                <?php echo $AppUI->_("3LBLMDP") ?> 
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/projects_estimations.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("4LBLESTIMATIONS") ?>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo $AppUI->_("LBL_PROJECT_PROJECT_HUMAN_RESOURCES"); ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/human_resources/projects_allocations.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("allocations") ?>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/need_for_training.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_NEED_FOR_TRAINING") ?>

            </td>
        </tr>

        <tr>
            <th>
                <?php echo $AppUI->_("LBL_PROJECT_COSTS") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/costs/view_costs.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_COST_ESTIMATION") ?>
                <input type="radio" name="user_choosen_feature" value="/modules/costs/view_budget.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_BUDGET") ?>
                &nbsp;&nbsp;&nbsp;


            </td>
        </tr>


        <tr>
            <th>
                <?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/communication/index_project.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION") ?>
            </td>
        </tr>


        <tr>
            <th>
                <?php echo $AppUI->_("LBL_QUALITY") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/quality/project_quality_planning.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_QUALITY_PLANNING") ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_PROJECT_RISKS") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/risks/risk_management_plan.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_RISK_MANAGEMENT_PLAN") ?>
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="user_choosen_feature" value="/modules/risks/projects_risks.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_RISKS") ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_ACQUISITION") ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/timeplanning/view/acquisition/acquisition_planning.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_ACQUISITION_PLANNING") ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER"); ?>
            </th>
            <td>
                <input type="radio" name="user_choosen_feature" value="/modules/stakeholder/project_stakeholder.php" onclick="submitMenuForm()" />
                <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER"); ?>
            </td>
        </tr>
    </table>
    -->
</form> 
<a name="gqs_anchor"></a>
<?php
$path = $_POST["user_choosen_feature"];
if ($path != "") {
    $_SESSION["user_choosen_feature"] = $path;
} else {
    $path = $_SESSION["user_choosen_feature"];
}
//Allow user to define page it wants to see.
if($_GET["targetScreenOnProject"]!=""){
    $path=$_GET["targetScreenOnProject"];
}
if($path==""){
    $path="/modules/dotproject_plus/projects_tab.planning_and_monitoring.php";//deafult page 
}
if (file_exists(DP_BASE_DIR . $path) && $path != "") {
    include (DP_BASE_DIR . $path);
}
?>
<?php
require_once ("../../../../base.php");
require_once DP_BASE_DIR . ("/includes/config.php");
require_once (DP_BASE_DIR . "/classes/csscolor.class.php"); // Required before main_functions
require_once (DP_BASE_DIR . "/includes/main_functions.php");
require_once (DP_BASE_DIR . "/includes/db_adodb.php");
require_once (DP_BASE_DIR . "/includes/db_connect.php");
require_once (DP_BASE_DIR . "/classes/ui.class.php");
require_once (DP_BASE_DIR . "/classes/permissions.class.php");
require_once (DP_BASE_DIR . "/includes/session.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_baseline.class.php");
dPsessionStart(array("AppUI"));
if (!isset($_SESSION["AppUI"])) {
    $_SESSION["AppUI"] = new CAppUI();
}

$AppUI = $_SESSION["AppUI"];
$AppUI->checkStyle();
$AppUI->loadPrefs($AppUI->user_id);

$AppUI->setUserLocale();
setlocale(LC_TIME, $AppUI->user_lang);
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
require_once (DP_BASE_DIR . "/modules/companies/companies.class.php");
require_once (DP_BASE_DIR . "/modules/contacts/contacts.class.php");
require_once (DP_BASE_DIR . "/modules/admin/admin.class.php");
include_once (DP_BASE_DIR . "/locales/core.php");
$activitiesIdsForDisplay = array(); //updated in time_planning_activities_sequencing.php
$projectId = $_GET["project_id"];
$projectObj = new CProject();
$projectObj->load($projectId);
$companyId = $projectObj->project_company;
$projectName = $projectObj->project_name;
$companyObj = new CCompany();
$companyObj->load($companyId);
$companyName = $companyObj->company_name;
$userObj = new CUser();
$userObj->load($projectObj->project_owner);
$projectManager = $userObj->user_username;
?>
<html>
    <head>
        <meta charset="UTF-8" content="text/html" http-equiv="Content-Type" />
        <style>
            
            body{
                color:#000000;
                font-family:  arial, verdana, sans-serif;
                font-size:11px;
                margin: 1.0cm 0;   
                line-height: 140%;
            }
            
            h1{
                font-size: 13px;
            }
            
            h2{
                background-color: silver /*#f2f2f2*/;
                width: 656px;
                height: 25px;
                vertical-align: middle;
                padding-left: 2px;
                padding-top: 5px;
                margin-top: 0.5px;
                margin-bottom: 11px;    
                font-size:12px;
                page-break-before: always;
                
            }
            
            h3{
                font-size:11px;
                margin-bottom: 11px;
                height: 25px;
                background-color: silver/*#f2f2f2*/;
                width: 656px;
                vertical-align: middle;
                padding-left: 2px;
                padding-top: 5px;               
            }
           
                        
            br,ol,ul,p{
                margin:0.5px 0px;
            }

          

            .labelCell{
                text-align: right;
                font-weight: bold;
            }

            .printTable{              
                border: 1px black solid;
                width: 650px;
                max-width: 650px;
                margin-bottom: 11px;
               
            }
            
            .print_p{
                
                width: 650px;
                max-width: 650px;
                word-wrap: break-word;
                text-align: justify;
                line-height: 140%;
                padding-left: 2px;
                padding-right: 2px;
            }

            table, .printTable{
                font-family: arial, verdana, sans-serif;
                margin-right: -0.25cm;
                border-spacing: 0px;
                padding: 0px;
                color:#000000;
                border-collapse: collapse;
            }
                     

            .printTable th, .printTable td {
                color:#000000;     
                font-size:11px; 
                font-family:  arial, verdana, sans-serif;  
                border-color: #000000;
                border-style: solid;
                border-left-width: 1px;
                border-bottom-width: 1px;
                border-top-width: 0px;/*0px*/
                border-right-width: 0px; /*0px*/
                border-spacing: 0px;
                padding-left: 2px;
                padding-right: 2px;
            }
            
            .printTable th{
                text-align: left; 
                background-color: silver /*LightBlue*/;
            }
                        
            .printTable  td{
                line-height: 140%;
                word-break: keep-all;
                word-wrap:break-word;
                vertical-align: top;
                text-align: justify;
            }
            

            .LandscapeDiv{
                PAGE-BREAK-BEFORE: always;
                width:"100%";
                height:"100%";
                filter: progid:DXImageTransform.Microsoft.BasicImage(Rotation=3);
            }
            
            .summaryMenu{
                list-style-type: none;   
            }
            .summaryMenu li{
                margin-top: 8px;
            }
            .summaryMenu li a{
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <?php
        if ($_GET["print"] == "1") {
            require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/print_pdf_header_footer.php");
        }
        ?>
        <!-- Project planning exportation -->
        <p class="print_p" style="font-size: 60pt; text-align: center;margin-top: 6cm;"> 
            <?php echo $AppUI->_("LBL_PROJECT_PLAN",UI_OUTPUT_HTML); ?>
        </p>
        <br/><br/><br/><br/><br/><br/><br/><br />
        <div class="print_p" style="text-align:center;font-size: 2em">
            <span style="font-weight: bold"><?php echo $AppUI->_("LBL_PROJECT_PROJECT",UI_OUTPUT_HTML); ?></span>
            <br />
            <?php echo $projectName ?> <br/>
            <br />
            <span style="font-weight: bold"><?php echo $AppUI->_("LBL_PROJECT_PROJECT_MANAGER",UI_OUTPUT_HTML); ?></span>
            <br />
            <?php echo ucwords($projectManager) ?> 
            <br/>
            <br />
            <span style="font-weight: bold"><?php echo $AppUI->_("LBL_BASELINE",UI_OUTPUT_HTML); ?></span>
            <?php
            $baselineController= new ControllerBaseline();
            $version=$baselineController->getCurrentBaseline($projectId);
            ?>
            <br />
            <span style="line-height: 135%"><?php echo $version; ?></span>
            <br />
            <br />
            <span style="font-weight: bold"><?php echo $AppUI->_("LBL_DATE",UI_OUTPUT_HTML); ?></span>
            <br />
            <span style="line-height: 135%"><?php echo date("d/m/Y"); ?></span>            
            
        </div>

        <h2><?php echo $AppUI->_("LBL_PROJECT_SUMMARY",UI_OUTPUT_HTML); ?></h2>
        <br/>
        <ul class="summaryMenu">
            <li><a href="#scope">1. <?php echo $AppUI->_("LBL_PROJECT_PLAN_SCOPE",UI_OUTPUT_HTML); ?> </a></li>
            <li><a href="#time">2. <?php echo $AppUI->_("LBL_PROJECT_TIME",UI_OUTPUT_HTML); ?> </a></li>
            <li><a href="#cost">3.<?php echo $AppUI->_("LBL_PROJECT_COSTS",UI_OUTPUT_HTML); ?> </a></li>
            <li><a href="#quality">4.<?php echo $AppUI->_("LBL_PROJECT_QUALITY",UI_OUTPUT_HTML); ?> </a></li>
            <li><a href="#human_resource">5. <?php echo $AppUI->_("LBL_PROJECT_PROJECT_HUMAN_RESOURCES",UI_OUTPUT_HTML); ?></a></li>
            <li><a href="#communication">6. <?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION",UI_OUTPUT_HTML); ?>  </a></li>
            <li><a href="#acquisition">7. <?php echo $AppUI->_("LBL_PROJECT_ACQUISITIONS",UI_OUTPUT_HTML); ?> </a></li>
            <li><a href="#risk">8. <?php echo $AppUI->_("LBL_PROJECT_RISKS",UI_OUTPUT_HTML); ?> </a></li>
            <li><a href="#stakeholder">9. <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER",UI_OUTPUT_HTML); ?> </a></li>
        </ul>
        <br/>
        <h2><a name="scope">1. <?php echo $AppUI->_("LBL_PROJECT_PLAN_SCOPE",UI_OUTPUT_HTML); ?> </a></h2>

        <h3> 1.1. <?php echo $AppUI->_("LBL_PROJECT_SCOPE_DECLARATION",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/scope_statement.php"); ?>
        
        <h3> 1.2. <?php echo $AppUI->_("LBL_PROJECT_WBS",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/scope_planning.php"); ?>
        
        <!-- <h3>1.3.  <?php //echo $AppUI->_("LBL_WBS_DICTIONARY",UI_OUTPUT_HTML); ?> </h3> -->
        <?php //require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/scope_wbs_dictionary.php"); ?>
        <h2><a name="time">2. <?php echo $AppUI->_("LBL_PROJECT_TIME",UI_OUTPUT_HTML); ?> </a></h2>   
        <h3>2.1. <?php echo $AppUI->_("LBL_PROJECT_PROJECT_ACTIVITIES",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_activities_sequencing.php"); ?>
        <?php // require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_activities.php"); ?>
        
        <h3>2.2. <?php echo $AppUI->_("LBL_PROJECT_WORKPACKAGE_ESTIMATION",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_workpackage_estimations.php"); ?>                     
        
        <h3>2.3. <?php echo $AppUI->_("LBL_PROJECT_ACTIVITIES_DURATION",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_activities_duration_estimation.php"); ?>
        
        <h3>2.4. <?php echo $AppUI->_("LBL_PROJECT_NETWORK_DIAGRAM",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_network_diagram.php"); ?>
        
        <h3 style="page-break-before:always">2.5. <?php echo $AppUI->_("LBL_PROJECT_PROJECT_SCHEDULE",UI_OUTPUT_HTML); ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_gantt_chart.php"); ?>
        <h2><a name="cost">3. <?php echo $AppUI->_("LBL_PROJECT_COSTS",UI_OUTPUT_HTML); ?> </a></h2>
        <h3>3.1. <?php echo $AppUI->_("LBL_COST_ESTIMATION",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/costs_planning_estimation.php"); ?>
        
        <h3>3.2. <?php echo $AppUI->_("LBL_PROJECT_COSTS_BASELINE",UI_OUTPUT_HTML); ?> </h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/costs_planning_baseline.php"); ?>
        
        <h3>3.3. <?php echo $AppUI->_("LBL_PROJECT_BUDGET",UI_OUTPUT_HTML); ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/costs_planning_budget.php"); ?>
        <h2><a name="quality">4. <?php echo $AppUI->_("LBL_PROJECT_QUALITY",UI_OUTPUT_HTML); ?> </a></h2>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/quality_planning.php"); ?>              
        <h2><a name="human_resource">5. <?php echo $AppUI->_("LBL_PROJECT_PROJECT_HUMAN_RESOURCES",UI_OUTPUT_HTML); ?> </a> </h2>
        <h3>5.1. <?php echo $AppUI->_("LBL_PROJECT_ORGANIZATIONAL_CHART",UI_OUTPUT_HTML); ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/human_resource_planning_organizational_diagram.php"); ?>
        <h3>5.2. <?php echo $AppUI->_("LBL_PROJECT_ROLES_AUTHORITIES_RESPONSABILITIES_COMPETENCE",UI_OUTPUT_HTML); ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/human_resources_view_company_roles.php"); ?>
        <h3>5.3. <?php echo $AppUI->_("LBL_PROJECT_HUMAN_RESOURCES_CAPABILITIES_AVAILABILITIES",UI_OUTPUT_HTML); ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/human_resources_capabilities_availabilities.php"); ?>
        <h3>5.4.<?php echo $AppUI->_("LBL_PROJECT_ALLOCATIONS",UI_OUTPUT_HTML); ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/human_resource_roles_allocations.php"); ?>
        <!--
        <h3>5.5. <?php //echo $AppUI->_("LBL_ORGANIZATIONAL_POLICIES");  ?></h3>
        <?php //require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/human_resources_company_policies.php"); ?>
        -->
        <h3>5.5. <?php echo $AppUI->_("LBL_NEED_FOR_TRAINING",UI_OUTPUT_HTML) ?></h3>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/human_resources_need_for_training.php"); ?>
        <h2><a name="communication">6. <?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION",UI_OUTPUT_HTML); ?>  </a></h2>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/communication_planning.php"); ?>
        <h2><a name="acquisition">7. <?php echo $AppUI->_("LBL_PROJECT_ACQUISITIONS",UI_OUTPUT_HTML); ?> </a></h2>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/acquisition_planning.php"); ?>       
        <h2><a name="risk">8. <?php echo $AppUI->_("LBL_PROJECT_RISKS",UI_OUTPUT_HTML); ?> </a></h2>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/risks_planning.php"); ?>
        <h2><a name="stakeholder">9. <?php echo $AppUI->_("LBL_PROJECT_STAKEHOLDER",UI_OUTPUT_HTML); ?> </a></h2>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/stakeholders.php"); ?><br/>
        <?php if ($_GET["print"] != "1") { ?>
            <p align="center">
                <a href="<?php echo $baseUrl . "/print_pdf.php?project_id=" . $projectId . "&print=1"; ?>"> Print PDF </a>
            </p>
        <?php } ?>
    </body>
</html>
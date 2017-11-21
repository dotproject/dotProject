<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_activity_mdp.class.php");
$controllerActivityMDP = new ControllerActivityMDP();
$projectId = dPgetParam($_GET, 'project_id', 0);
?>

<!-- YUI -->
<link rel="stylesheet" type="text/css" href="./modules/timeplanning/js/jsLibraries/wireit/lib/yui/fonts/fonts-min.css" /> 
<link rel="stylesheet" type="text/css" href="./modules/timeplanning/js/jsLibraries/wireit/lib/yui/reset/reset-min.css" />
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/lib/yui/utilities/utilities.js"></script>
<!-- Excanvas FOR IE -->
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/lib/excanvas.js"></script>
<!-- WireIt -->
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/js/WireIt.js"></script>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/js/CanvasElement.js"></script>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/js/Wire.js"></script>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/js/Terminal.js"></script>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/js/util/Anim.js"></script>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/wireit/js/util/DD.js"></script>
<link rel="stylesheet" type="text/css" href="./modules/timeplanning/js/jsLibraries/wireit/css/WireIt.css" />
<script type="text/javascript" src="./modules/timeplanning/js/mdp.js"></script>
<style>
    div.blockBox {
        /* WireIt */
        position: absolute;
        z-index: 1;
        /*opacity: 0.8;*/
        cursor: move;
        font-size: 7pt;
        border: 1px black solid;
        /* Others */
        background-color: #E8E8E8; /*rgb(255,200,200);*/
        text-wrap: normal;
        word-break: keep-all;
    }

    #graph_panel{
        position:relative; /* This make the activities into div*/
        background-color:white;
        /*float:left;*/
        border: 1px black solid;
        overflow: auto; 
        width:100%;
        height:650px;
    }
</style>


<form action="?m=timeplanning&a=view" method="post" name="form_mdp" id="form_mdp">
    <input name="dosql" type="hidden" value="do_projects_mdp_aed" />
    <input name="tasks_ids" id="tasks_ids" type="hidden" value=""/>
    <input name="tasks_dependencies_ids" id="tasks_dependencies_ids" type="hidden" value=""/>
    <input name="tasks_positions" id="tasks_positions" type="hidden" value=""/>
    <input name="project_id" id="project_id" type="hidden" value="<?php echo $projectId; ?>"/>
    <!--
    <input type="button" class="button" value="Zoom (+)" onclick=zoom(1) />
    <input type="button" class="button" value="Zoom (-)" onclick=zoom(-1) />
    -->
    <br />
    <table align="center" class="tbl" style="width:100%;height:650px;">
        <th><p style="text-align:center;font-weight: bold"> <?php echo $AppUI->_("LBL_MDP_EXTENDED"); ?></p></th>
        <tr>
            <td align="center">
                <div id="graph_panel" >
              </div>
            </td>
        </tr>
    </table>
    <br />

    <table align="center" width="100%">
        <tr>
            <td align="right">
                <input type="button" class="button" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" onclick=save_mdp() />
                <script> var targetScreenOnProject="/modules/dotproject_plus/projects_tab.planning_and_monitoring.php";</script>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
            
        </tr>
    </table>

    <?php
    $tasks = $controllerActivityMDP->getProjectActivities($projectId);
    foreach ($tasks as $task) {
        $x=$task->getX();
        $y=$task->getY();
        $x=$x<0?1:$x;
        $y=$y<0?1:$y;
        echo "\n<script>addNew('" . $task->getName() . "','" . $task->getId() . "'," .  $x . "," . $y . ");</script>\n";
    }
    foreach ($tasks as $task) {
        foreach ($task->getDependencies() as $dep_id) {
            echo "\n<script>addDependency(" . $task->getId() . ",$dep_id);</script>\n";
        }
    }
    echo "<script>setTimeout('wiresUpdate()',1000);</script>";
    ?>
</form>

<br />

<table width="95%" align="center" class="tbl">
    <tr>
        <th style="text-align: center">
            <b><?php echo $AppUI->_("LBL_GANTT_SEQUENCING"); ?>:</b>
        </th>
    </tr>
    <tr>
        <td align="center">
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/gantt_chart.php"); ?>
        </td>
    </tr>
</table>
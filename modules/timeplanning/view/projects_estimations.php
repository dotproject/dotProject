<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
$AppUI->savePlace();
global $task_id, $obj;
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_minute.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_project_minute.class.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
$controllerProjectMinute = new ControllerProjectMinute();
$minuteId = dPgetParam($_GET, "minute_id", 0);
$date = "";
$description = "";
if ($minuteId != "-1" && $minuteId != "") {
    $projectMinute = new ProjectMinute();
    $projectMinute->load($minuteId);
    $date = $projectMinute->getDate();
    $description = $projectMinute->getDescription();
    $isEffort = $projectMinute->isEffort();
    $isDuration = $projectMinute->isDuration();
    $isResource = $projectMinute->isResource();
    $isSize = $projectMinute->isSize();
    $members = $projectMinute->getMembers();
}
?>

<script src="./modules/timeplanning/js/estimations.js"></script>
<form action="?m=timeplanning&a=view&project_id=<?php echo dPgetParam($_GET, "project_id", 0); ?>#minute_estimation_form"  method="POST" name="minute_form" id="minute_form" >
    <input name="dosql" type="hidden" value="do_projects_estimations_aed" />
    <input type="hidden" name="minute_id" id="minute_id" value="<?php echo $minuteId ?>">
    <input type="hidden" name="project_id" value="<?php echo dPgetParam($_GET, "project_id", 0); ?>">
    <input type="hidden" name="tab" value="<?php echo dPgetParam($_GET, "tab", 0); ?>">
    <input type="hidden" name="membersIds" id="membersIds">
    <input type="hidden" name="action_estimation" id="action_estimation" value="">

    <div>
        <table align="center" width="95%">
            <tr>
                <td>
                    <input type="button" class="button" value="<?php echo $AppUI->_("LBL_CREATE_MINUTE"); ?>" onclick=openEstimationReport() />
                </td>
            </tr>
        </table>
        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/project_mitute_form.php"); ?>
        <table class="tbl" align="center" width="95%" border="0" cellpadding="2" cellspacing="1">
            <caption> <b><?php echo $AppUI->_("LBL_MINUTES"); ?></b></caption>
            <tr>
                <th>
                    <?php echo $AppUI->_("LBL_ID"); ?>
                </th>
                <th>
                    <?php echo $AppUI->_("LBL_DATE"); ?>
                </th>
                <th>
                    <?php echo $AppUI->_("LBL_DESCRIPTION"); ?>
                </th>
                <th>
                    &nbsp;
                </th>
                <th>
                    &nbsp;
                </th>
            </tr>

            <?php
            $minutes = $controllerProjectMinute->getProjectMinutes($_GET["project_id"]);
            foreach ($minutes as $minute) {
                ?>
                <tr>
                    <?php
                    $date = $minute->getDate();
                    $id = $minute->getId();
                    $description = $minute->getDescription();
                    $description = explode("</p>", $description);
                    $description = $description[0];
                    ?>
                    <td valign="top"><?php echo $id; ?></td>
                    <td valign="top"><?php echo $date; ?></td>
                    <td valign="top"><?php echo $description; ?></td>
                    <td valign="top"> 
                        <img src="./modules/timeplanning/images/view_icon.gif" style="cursor:pointer" onclick="openReport(<?php echo $id; ?>)">
                    </td>
                    <td valign="top">
                        <img src="modules/timeplanning/images/stock_delete-16.png" border="0" style="cursor:pointer" onclick=deleteReport("<?php echo $id; ?>") />
                    </td>
                </tr>
            <?php } ?>
        </table>
        <br /><br />
        <?php require (DP_BASE_DIR . "/modules/timeplanning/view/project_tasks_estimation_form.php"); ?>
    </div>
</form>

<br />
<table width="95%" align="center" class="tbl">
    <tr>
        <th>
            <b><?php echo $AppUI->_("GANTT"); ?></b>
        </th>
    </tr>
    <tr>
        <td align="center">
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/gantt_chart.php"); ?>
        </td>
    </tr>
</table>

<!--
<br/>
<form action="?m=timeplanning&a=view" method="post" name="form_cpm" id="form_cpm">
    <span id="schedule_development_prompt_message" style="display:none">
        <?php echo $AppUI->_("LBL_SCHEDULE_DEVELOPMENT_PROMPT_MESSAGE"); ?>
    </span>
    <table class="std" align="center" width="95%" >
        <tr><th><?php echo $AppUI->_("LBL_DEV_SCHEDULE"); ?></th></tr>
        <tr>
            <td>
                <input name="dosql" type="hidden" value="do_schedule_development" />
                <input type="hidden" value="<?php echo dPgetParam($_GET, "project_id", 0); ?>" name="project_id" />
                <?php echo $AppUI->_("LBL_ACTION_SCHEDULE_DEV"); ?>
                <br /><br />            
                <?php
                $obj = new CProject();
                $obj->load(dPgetParam($_GET, "project_id", 0));
                ?>
                <a href="index.php?m=projects&a=view&project_id=<?php echo dPgetParam($_GET, "project_id", 0); ?>&tab=3&start_date=<?php echo $obj->project_start_date; ?>&end_date=<?php echo $obj->project_actual_end_date ?>" > <?php echo $AppUI->_("LBL_OPEN_GANTT_CHART"); ?></a>
                
            </td>
        </tr>
    </table>
    <table align="center" width="95%">
        <tr>
            <td>
                <input type="button" class="button" onclick="scheduleDevelopment()" value="<?php echo $AppUI->_("LBL_EXECUTE"); ?>" />
            </td>
        </tr>
    </table>
</form>

-->
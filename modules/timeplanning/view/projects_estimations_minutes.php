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
    <input type="hidden" name="minute_id" id="minute_id" value="<?php echo $minuteId ?>" />
    <input type="hidden" name="project_id" value="<?php echo dPgetParam($_GET, "project_id", 0); ?>" />
    <input type="hidden" name="tab" value="<?php echo dPgetParam($_GET, "tab", 0); ?>" />
    <input type="hidden" name="membersIds" id="membersIds" />
    <input type="hidden" name="action_estimation" id="action_estimation" value="" />
    <br />
    <div>

        <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/project_mitute_form.php"); ?>
        <br />
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
                    <td valign="top" align="center"><?php echo $id; ?></td>
                    <td valign="top" align="center"><?php echo $date; ?></td>
                    <td valign="top"><?php echo $description; ?></td>
                    <td valign="top" align="center"> 
                        <img src="./modules/timeplanning/images/view_icon.gif" style="cursor:pointer" onclick="openReport(<?php echo $id; ?>)">
                    </td>
                    <td valign="top" align="center">
                        <img src="modules/timeplanning/images/stock_delete-16.png" border="0" style="cursor:pointer" onclick=deleteReport("<?php echo $id; ?>") />
                    </td>
                </tr>
            <?php } ?>

        </table>
        <table width="95%" align="center">
            <tr>
                <td align="right">

                    <input type="button" class="button" value="<?php echo $AppUI->_("LBL_CREATE_MINUTE"); ?>" onclick=openEstimationReport() />
                    <script> var targetScreenOnProject="/modules/dotproject_plus/projects_tab.planning_and_monitoring.php";</script>
                    <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>

                </td>
            </tr>
        </table>
    </div>
</form>
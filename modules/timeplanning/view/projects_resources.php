<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
$AppUI->savePlace();
global $task_id, $obj;
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_allocated_role_report.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_allocated_nonhuman_resource_report.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_project_resources.class.php");

$controllerProjectResources = new ControllerProjectResources();
$projectId = dPgetParam($_GET, "project_id", 0);
if (isset($projectId)) {
    $humanResources = $controllerProjectResources->getHumanResources($projectId);
    $nonHumanResources = $controllerProjectResources->getNonHumanResources($projectId);
}
?>
<div>
    <table class="std" align="center" width="95%" >
        <caption> <b><?php echo $AppUI->_("LBL_PROJECT_RESOURCES"); ?></b></caption>
        <tr>
            <th>
                <?php echo $AppUI->_("LBL_ITEM"); ?>
            </th>
            <th>
                <?php echo $AppUI->_("LBL_QUANTITY"); ?>
            </th>
        </tr>
        <tr bgcolor="#C0C0C0"> 
            <td  colspan="2" align="center"> 
                <b><?php echo $AppUI->_("LBL_PEOPLE"); ?> </b>
            </td>
            <?php
            foreach ($humanResources as $obj) {
                ?>
            <tr>
                <td valign="top"><?php echo $obj->getRoleName(); ?></td>
                <td valign="top"><?php echo $obj->getCount(); ?> (<?php echo $obj->getSum() ?> <?php echo $AppUI->_("LBL_HOURS"); ?>)</td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2" align="center" bgcolor="#C0C0C0">
                <b><?php echo $AppUI->_("LBL_NON_HUMAN_RESOURCES"); ?></b>
            </td>
        </tr>
        <?php
        $lastType = "";
        foreach ($nonHumanResources as $obj) {

            if ($lastType != $obj->getType()) {
                $lastType = $obj->getType();
                ?>
                <tr bgcolor="#C0C0C0"> 
                    <td colspan="2" align="center">
                        <?php echo $AppUI->_($obj->getType()); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td valign="top"><?php echo $obj->getName(); ?></td>
                <td valign="top"><?php echo $obj->getQuantity(); ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
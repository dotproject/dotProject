<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
?>
<?php
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
?>
<table class="printTable">
    <tr bgcolor="silver">
        <th ><?php echo $AppUI->_("LBL_WBS",UI_OUTPUT_HTML); ?> </th>
        <th > <?php echo $AppUI->_("LBL_DESCRIPTION",UI_OUTPUT_HTML); ?> </th>
    </tr>
    <?php
    $items = $controllerWBSItem->getWBSItems($projectId);
    $activities = array();
    foreach ($items as $item) {
        $id = $item->getId();
        $name = $item->getName();
        $identation = $item->getIdentation();
        $number = $item->getNumber();
        $is_leaf = $item->isLeaf();
        if ($is_leaf == "1") {
            ?>
            <tr id="id_<?php echo $id ?>" bgcolor="#C0C0C0" title="is_wbs_item">
                <td colspan="2"><b><?php echo $number ." - ". $name ?></b></td>
                <?php
            } else {
                ?>
            <tr id="id_<?php echo $id ?>"  title="is_wbs_item">
                <td colspan="2"><?php echo $number ." - ". $name ?></td>
                <?php
            }
            echo "</td>";
            echo "</tr>";
            //add decomposed activities
            if ($is_leaf == "1") {
                //start: code to filter workpakage activities
                $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                $hasActivities = false;
                foreach ($tasks as $obj) {
                    $activities[$obj->task_id] = true; //just inform this activity was used
                    $hasActivities = true;
                    $task_name = $obj->task_name;
                    ?>
                <tr>
                    <td colspan="2"><?php echo $obj->task_id . " - " . $task_name; ?></td>
                </tr>
                <?php
            }
            //end: code to filter workpackages activities
        }
    }
    //add tasks without workpackage
    $tasks = $ControllerWBSItemActivityRelationship->getAllActivities($projectId);
    foreach ($tasks as $task) {
        if ($activities[$task->task_id] == null) {
            $task_name = $task->task_name;
            ?>
            <tr>
                <td colspan="2"><?php echo $task->task_id . " - " .$task_name; ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
?>
<script src="./modules/timeplanning/js/decomposition.js"></script>
<style>
    /**
    shortTD css class is used to control the expansion of the first cell of wbs table.
    It reserves more space for the description column.
    */
    .shortTD{
        max-width: 15px;
        min-width: 15px;
        width: 15px;
    }
</style>
<?php $project_id = dPgetParam($_GET, 'project_id', 0); ?>
<form  name="decomposition_form" method="post" action="?m=timeplanning&a=view&project_id=<?php echo $project_id; ?>">
    <input type="hidden" name="activities_ids" id="activities_ids">
    <input type="hidden" name="activities_ids_to_delete" id="activities_ids_to_delete" value="">
    <input name="dosql" type="hidden" value="do_project_activities_aed">
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">

    <?php
    $project_id = dPgetParam($_GET, 'project_id', 0);
    //start: set workpackages
    $controllerWBSItem = new ControllerWBSItem();
    $ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
    $items = $controllerWBSItem->getWorkPackages($project_id);
    $workpackages_combo = "<option value='-1'>-- " . $AppUI->_('LBL_MOVE') . "--</option>";
    foreach ($items as $item) {
        $workpackages_combo.="<option value='" . $item->getId() . "'> " . $item->getNumber() ."</option>";
    }
    echo "<div id='work_packages_combo' style='display:none'>$workpackages_combo</div>";
    //end: set workpackages
    ?>
    <table border="0" align="center" width="95%">
        <tr>
            <td align="left">
                <input type="button" value="<?php echo $AppUI->_('LBL_SAVE'); ?>" class="button"  onclick="salvarCronograma()" />
            </td>
        </tr>
    </table>

        <table id="table_decomposition" name="table_decomposition" class="std"  border="0" align="center" width="95%">
            <caption><b><?php echo $AppUI->_('LBL_ACTIVITIES_DEFINITION'); ?> </b></caption>
            <tr bgcolor="silver">
                <th class="shortTD"><?php echo $AppUI->_('LBL_WBS'); ?> </th>
                <th style="width:75%"> <?php echo $AppUI->_('LBL_DESCRIPTION'); ?> </th>
                <th style="width:5%">&nbsp;</th>
                <th style="width:10%"> <?php echo $AppUI->_('LBL_MOVE'); ?></th>
            </tr>
            <?php
            $items = $controllerWBSItem->getWBSItems($project_id);
            $activities = array();
            foreach ($items as $item) {
                $id = $item->getId();
                $name = $item->getName();
                $identation = $item->getIdentation();
                $number = $item->getNumber();
                $is_leaf = $item->isLeaf();
                if ($is_leaf == "1") {
                    echo "<tr id='id_$id' bgcolor='#E8E8E8' title='is_wbs_item'>";
                    echo "<td colspan='4' title='+'><b>$number - $name</b><br /><img src='modules/timeplanning/images/stock_new_small.png'  style='cursor:pointer' onclick=addLine('','','','','','','','','','','',$id); /></td>";
                } else {
                    echo "<tr id='id_$id' title='is_wbs_item'>";
                    echo "<td colspan='4'>$number - $name</td>";
                }
                echo "</td>";
                echo '</tr>';
                //add decomposed activities
                if ($is_leaf == "1") {
                    //start: code to filter workpakage activities
                    $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                    $hasActivities = false;
                    foreach ($tasks as $obj) {
                        $activities[$obj->task_id] = true; //just inform this activity was used 
                        $hasActivities = true;
                        $task_name = $obj->task_name;
                        echo "<script>addLine(\"$task_name\",'','','','','','" . $obj->task_id . "','','','','',$id);</script>";
                    }
                    //end: code to filter workpackages activities		
                    if (!$hasActivities) {
                        //add a first activity when there is no related activity for this eap item
                        echo "<script>addLine('','','','','','','','','','','',$id);</script>";
                    }
                }
            }
            ?>
        </table>
    <br/>
    <table border="0" align="center" width="95%">
        <tr>
            <td align="left">
                <input type="button" value="<?php echo $AppUI->_('LBL_SAVE'); ?>" class="button"  onclick="salvarCronograma()" />
            </td>
        </tr>
    </table>
</form>
<?php
//add tasks without workpackage 
$tasks = $ControllerWBSItemActivityRelationship->getAllActivities($project_id);
foreach ($tasks as $task) {
    if ($activities[$task->task_id] == null) {
        $taskDescription = $task->task_name;
        $taskId = $task->task_id;
        echo "<script>addLine(\"$taskDescription\",'','','','','',$taskId,'','','','',$id);</script>";
    }
}
?>
<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
global $AppUI;
$AppUI->savePlace();
$dbprefix = dPgetConfig('dbprefix','');
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
$project_id = dPgetParam($_GET, "project_id", 0);
$controllerWBSItem = new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
?>

<form  name="decomposition_form" method="post" action="?m=timeplanning&a=view&project_id=<?php echo $project_id; ?>">

    <table id="table_decomposition" name="table_decomposition" class="tbl"  border="0" align="center" width="95%">
        <caption><b><?php echo $AppUI->_("LBL_PROJECT_ACTIVITIES_MONITORING"); ?> </b></caption>
        <tr bgcolor="silver">
            <th> <?php echo $AppUI->_("LBL_WBS"); ?> </th>
            <th> <?php echo $AppUI->_("LBL_DESCRIPTION"); ?> </th>            
            <th> <?php echo $AppUI->_("Date Begin"); ?> </th>
            <th> <?php echo $AppUI->_("Date End"); ?> </th>
            <th> <?php echo  $AppUI->_("allocations"); ?></th>
            <th>  <?php echo $AppUI->_("LBL_WORK_CONCLUDED"); ?> &nbsp;(%)</th>
            <th> &nbsp;</th>
        </tr>
        <?php
        $items = $controllerWBSItem->getWBSItems($project_id);
        $activities = array();
        foreach ($items as $item) {
            $id = $item->getId();
            $name = $item->getName();
            $number = $item->getNumber();
            $is_leaf = $item->isLeaf();
            echo "<tr>";
            echo "<td colspan=\"7\"><b>$number - $name</b></td></tr>";

            //add decomposed activities
            if ($is_leaf == "1") {
                //start: code to filter workpakage activities
                $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                $hasActivities = false;
                foreach ($tasks as $obj) {
                    $activities[$obj->task_id] = true; //just inform this activity was used 
                    $hasActivities = true;
                    $task_name = $obj->task_name;
                    $task_id = $obj->task_id;
                    $obj = new CTask();
                    $obj->load($task_id);
                    $df = $AppUI->getPref('SHDATEFORMAT');
                    ?>
                    <tr>
                        <td nowrap="nowrap" colspan="2" >
                             <?php echo $task_name ?>
                        </td>
                        <td align="center">
                           <?php
                            $date=new CDate($obj->task_start_date);
                            echo $date->format($df);
                           ?>
                        </td>
                        
                        <td align="center">
                            <?php
                            $date=new CDate($obj->task_end_date);
                            echo $date->format($df);
                           ?>
                        </td>
                        
                        <td align="center">
                            <?php
                            $ausql = ('SELECT ut.user_id, u.user_username, contact_email, ut.perc_assignment, ' 
			  . 'SUM(ut.perc_assignment) AS assign_extent, contact_first_name, contact_last_name ' 
			  . 'FROM '.$dbprefix.'user_tasks ut LEFT JOIN '.$dbprefix.'users u ON u.user_id = ut.user_id ' 
			  . 'LEFT JOIN '.$dbprefix.'contacts ON u.user_contact = contact_id ' 
			  . 'WHERE ut.task_id=' . $task_id . ' GROUP BY ut.user_id ' 
			  . 'ORDER BY ut.perc_assignment desc, u.user_username');
                            $paurc = db_exec($ausql);
                            $nnums = db_num_rows($paurc);
                            for ($xx=0; $xx < $nnums; $xx++) {
                                    $row=db_fetch_assoc($paurc);
                                    echo $row[1];
                                    if($nnums >($xx+1)  ){
                                        echo ", ";
                                    }
                            }
                            ?>
                        </td>
                        
                        <td align="center">
                          <?php echo $obj->task_percent_complete ?> %
                        </td>
                        <td nowrap="nowrap">
                            <a href="?m=tasks&a=view&task_id=<?php echo $task_id ?>&tab=1">
                                <?php echo $AppUI->_("LBL_INSERT_LOG"); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>
</form>
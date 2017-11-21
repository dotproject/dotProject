<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');
set_time_limit(300);
//save
$project_id = $_POST['project_id'];
$wbs_item_id=$_POST["wbs_item_id"];
$description=$_POST["wbs_item_description_".$wbs_item_id];
$estimated_size=$_POST["estimated_size_".$wbs_item_id];
$estimated_size_unit=$_POST["estimated_size_unit_".$wbs_item_id];
$identation=$_POST["identation_field_".$wbs_item_id];
$number=$_POST["number_field_".$wbs_item_id];
$isLeaf= $_POST["leaf_field_".$wbs_item_id];
$wbs_item_order=$_POST["wbs_item_order_".$wbs_item_id];

$controllerWBSItem= new ControllerWBSItem();
$controllerWBSItem->insert($project_id,$description,$number,$wbs_item_order,$isLeaf,$identation,$wbs_item_id);
	
$eapItem = new WBSItemEstimation();
$eapItem->store($wbs_item_id, $estimated_size, $estimated_size_unit);
$AppUI->setMsg($AppUI->_("LBL_THE_WBS_ITEM") ." ($description) " . $AppUI->_("LBL_WAS_SAVED_M")  , UI_MSG_OK, true);
$AppUI->redirect('m=projects&a=view&project_id='.$project_id);
?>
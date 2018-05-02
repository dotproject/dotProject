<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
require_once DP_BASE_DIR . "/modules/scope_and_schedule/wbs_item.class.php";

$id=intval(dPgetParam($_POST, "id"));
$project_id=intval(dPgetParam($_POST, "project_id"));
$wbs_id_position=intval(dPgetParam($_POST, "wbs_id_position"));
$wbsPosition = new WBSItem();
$wbsPosition->load($wbs_id_position);
$obj = new WBSItem();
$obj->load($id);

$order=floatval(dPgetParam($_POST, "order"));
	
if( substr( $wbsPosition->number, 0, strlen ($obj->number) ) !== $obj->number ){
	$obj->id_wbs_item_parent=$wbsPosition->id_wbs_item_parent;
	$obj->sort_order=$wbsPosition->sort_order + $order;
	$obj->store();
}else{
	$AppUI->setMsg($AppUI->_("A parent can not move to within its sublevel."), UI_MSG_ERROR);
}
	
$AppUI->redirect();//'m=projects&a=view&project_id='.$project_id
?>

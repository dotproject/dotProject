<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_dictionary_entry.class.php");
$controllerWBSItem = new ControllerWBSItem();
$projectId = dPgetParam($_GET, "project_id", 0);
$items = $controllerWBSItem->getWBSItems($projectId);
foreach ($items as $item) {
    $id=$item->getId();
    $dictionaryEntry=$_POST["wbs_item_dictionaty_entry_".$id];
    $obj=new WBSDictionaryEntry();
    $obj->setId($id);
    $obj->setDescription($dictionaryEntry);
    $obj->store();
}
$AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK, true);
$AppUI->redirect("m=projects&a=view&project_id=".$projectId);
?>

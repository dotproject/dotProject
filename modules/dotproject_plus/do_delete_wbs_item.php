<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
$controllerWBSItem= new ControllerWBSItem();
$ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
$project_id=dPgetParam($_POST, 'project_id');
$wbs_item_id=dPgetParam($_POST, 'wbs_item_id');
$wbs_item_name=dPgetParam($_POST, 'wbs_item_name');

$wbsItem=$controllerWBSItem->getWBSItemById($wbs_item_id);
//delete the selected WBS item and its activities
$controllerWBSItem->delete($wbs_item_id);
 $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($wbs_item_id);
 foreach ($tasks as $obj) {
     $obj->delete();
 }
 //find all child WBS items
$q = new DBQuery;
$q->addQuery("eap.id");
$q->addTable('project_eap_items', 'eap');
$q->addWhere("project_id =" . $project_id. " and number like '".$wbsItem->getNumber()."%'" );
$results = db_loadHashList($q->prepare(true), 'id');
//delete the child WBS items and its activities
foreach ($results as $id => $data) {
    $id=$data['id'];
    $controllerWBSItem->delete($id);
    $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
    foreach ($tasks as $obj) {
        $obj->delete();
    }
}
 

$AppUI->setMsg($AppUI->_("LBL_THE_WORK_PACKAGE") . " ($wbs_item_name) " . $AppUI->_("LBL_WAS_EXCLUDED",UI_OUTPUT_HTML) .".", UI_MSG_OK, true);
$AppUI->redirect('m=projects&a=view&project_id='.$project_id);
?>

<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
$project_id = dPgetParam($_POST, 'project_id');
$controllerWBSItem= new ControllerWBSItem();
$id=-1;
$description = $AppUI->_("LBL_NEW_WBS_ITEM");

$number_parent = $_POST["number_parent"];
$identation = $_POST["identation_field"];
$number = "";
$isLeaf= 1;
$sortOrder=$_POST["sort_order"];

//sortOrder is the value of its parent wbs item.
//It is needed to identify how many children this parent has, to them assign a new order value for this new item
//This check increases the complexity in n
$items = $controllerWBSItem->getWBSItems($project_id);
$foundParent=false;
foreach ($items as $item) {
    //echo "i number:". $item->getNumber() ." Parent number: " . $number_parent; 
    if($item->getNumber() == $number_parent){ //found parent
        $foundParent=true;
    }else if( $foundParent && (strlen ($item->getNumber())>strlen($number_parent)) ){
        //$description.="child ". $item->getNumber()." ";
        $sortOrder=$item->getSortOrder();//the new sort order will be equals the current last children
        //$identation=$item->getIdentation();
    }else{//starts a new parent in the same level or return to parent of the current parent.
        $foundParent=false;
    }
}

//$description .= " ".$sortOrder. " - " . $number_parent ." found parent: ". ($foundParent?"1":"0");
//die($description);
$controllerWBSItem->insert($project_id,$description,$number,$sortOrder,$isLeaf,$identation,$id);
//$AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED"), UI_MSG_OK, true);
$id_new_eap_item=mysql_insert_id();
$AppUI->redirect('m=projects&a=view&project_id='.$project_id."&id_new_eap_item=".$id_new_eap_item);
?>
<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");

//save
$project_id = $_POST["project_id"];
$wbs_id = $_POST["wbs_id"];
$wbs_order = $_POST["wbs_order"];
$direction = $_POST["direction"];

$WBScontroller = new ControllerWBSItem();
$records = $WBScontroller->getWBSItems($project_id);

//Get record order
$indexMovedWBSItem = -1;
$wbsItem = NULL;
$i = 0;
foreach ($records as $record) {
    if ($record->getId() == $wbs_id) {
        $wbsItem = $record;
        break;
    }
    $i++;
}

//get item to be rearranged
if (!is_null($wbsItem)) {
    if ($direction > 0) {
        for ($j = $i + 1; $j < sizeof($records); $j++) {
            if (strlen($records[$j]->getIdentation()) == strlen($wbsItem->getIdentation())) {
                $indexMovedWBSItem = $j;
                break;
            }
            if (strlen($records[$j]->getIdentation()) < strlen($wbsItem->getIdentation())) {
                break;//not allowed, the requested item already is the last of its group
            }
        }
    } else {
        for ($j = $i - 1; $j >= 0; $j--) {
            if (strlen($records[$j]->getIdentation()) == strlen($wbsItem->getIdentation())) {
                $indexMovedWBSItem = $j;
                break;
            }
            if (strlen($records[$j]->getIdentation()) < strlen($wbsItem->getIdentation())) {
                break;//not allowed, the requested item already is the fisrt of its group
            }
        }
    }
    
    //get children
    $childrenFirst=array();
    $k=$i+1;
    while($k <sizeof($records) && strlen($records[$k]->getIdentation()) > strlen($records[$i]->getIdentation())){
        $childrenFirst[sizeof($childrenFirst)]=$records[$k];
        $k++;
    }
    $childrenSecond=array();
    $k=$j+1;
    while($k <sizeof($records) && strlen($records[$k]->getIdentation()) > strlen($records[$j]->getIdentation())){
        $childrenSecond[sizeof($childrenSecond)]=$records[$k];
        $k++;
    }
    
    //Switch orders
    if ($indexMovedWBSItem >= 0 && $indexMovedWBSItem < sizeof($records)) {
        $movedWBS = $records[$indexMovedWBSItem];
                
        //Ensure always will start from above to bellow
        $nextOrder=null;
     
        if($direction==-1){
            $nextOrder=$movedWBS->getSortOrder();
        }else{
            $nextOrder=$wbsItem->getSortOrder();
            $wbsItem= $records[$j];
            $movedWBS=$records[$i];
            $temp=$childrenFirst;
            $childrenFirst=$childrenSecond;
            $childrenSecond=$temp;
            
        }
                
        //perform the reorder, from above to below
        $wbsItem->store($project_id, $wbsItem->getName(), $wbsItem->getNumber(), $nextOrder, $wbsItem->isLeaf(), $wbsItem->getIdentation(), $wbsItem->getId());
        $childIndex=null;
        for($k=0;$k<sizeof($childrenFirst);$k++){
            $childIndex=$movedWBS->getSortOrder()+1+$k;//new index
            $childItem=$childrenFirst[$k];
            $childItem->store($project_id, $childItem->getName(), $childItem->getNumber(), $childIndex, $childItem->isLeaf(), $childItem->getIdentation(), $childItem->getId());
        }
                
        $nextOrder=$nextOrder+sizeof($childrenFirst)+1;
        
         $movedWBS->store($project_id, $movedWBS->getName(), $movedWBS->getNumber(), $nextOrder, $movedWBS->isLeaf(), $movedWBS->getIdentation(), $movedWBS->getId());
         $childIndex=null;
         for($k=0;$k<sizeof($childrenSecond);$k++){
            $childIndex=$nextOrder+1+$k;//new index
            echo "Index child second item: ".$childIndex;
            $childItem=$childrenSecond[$k];
            $childItem->store($project_id, $childItem->getName(), $childItem->getNumber(), $childIndex, $childItem->isLeaf(), $childItem->getIdentation(), $childItem->getId());
        }
        $AppUI->setMsg("O item da EAP foi reordenado!", UI_MSG_OK, true);
    } else {
        $AppUI->setMsg("A reordenação solicitada não é permitida. Apenas pacotes de trabalho de mesmo nível podem ser reordenados.", UI_MSG_ALERT, true);
    }
}

$AppUI->redirect('m=projects&a=view&project_id=' . $project_id);
?>
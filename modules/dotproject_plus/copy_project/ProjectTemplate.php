<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProjectTemplate
 *
 * @author rafael
 */
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
class ProjectTemplate {
    
    /**
     * 
     * @param type $sourceProjectId
     * @param type $targetProjectId
     * @return type int
     * Return: 0 success; 1 - project WBS not empty
     */
    public function closeWBS($sourceProjectId,$targetProjectId){
        return $this->cloneWBSItems($sourceProjectId,$targetProjectId);
    }
    
    private function cloneWBSItems($sourceProjectId,$targetProjectId){
        $wbsController= new ControllerWBSItem(); 
        $taskController= new ControllerWBSItemActivityRelationship();
        $wbsItems=$wbsController->getWBSItems($sourceProjectId);
        $wbsItemsTargetProject=$wbsController->getWBSItems($targetProjectId);
        if(sizeof($wbsItemsTargetProject)===0){
            foreach ($wbsItems as $wbsItem){
                $clonedItem= new WBSItem();
                $id = $clonedItem->store($targetProjectId, $wbsItem->getName(), $wbsItem->getNumber(), $wbsItem->getSortOrder(), $wbsItem->isLeaf(), htmlentities($wbsItem->getIdentation()), -1);
                if ($wbsItem->isLeaf()) {
                    $tasks = $taskController->getActivitiesByWorkPackage($wbsItem->getId()); //get activities of original WBS item to then copy them to the new one
               
                    $o=0;
                    foreach ($tasks as $task) {
                        $newTask = $task->copy();
     
                        $q = new DBQuery();
                        $q->addTable("tasks", "t");
                        $q->addUpdate('task_project', $targetProjectId);
                        $q->addUpdate('task_start_date', date('Y-m-d H:i:s'));
                        $q->addUpdate('task_end_date', date('Y-m-d H:i:s'));
                        $q->addWhere("task_id=".$newTask->task_id);
                        $q->exec();
                        $q->clear();

                        $q = new DBQuery();
                        $q->addTable("tasks_workpackages", "tw");
                        $q->addInsert('eap_item_id', $id);
                        $q->addInsert('task_id', $newTask->task_id);
                        $q->addInsert("activity_order",  $o++);
                        $q->exec();
                        $q->clear();
              
                    }
                }
            }
            $result=0;
        }else{
            $result=1;
        }

        return $result;
    }            
}

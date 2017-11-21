<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_minute.class.php");
class ControllerProjectMinute {
	
	function ControllerProjectMinute()	{
	}
	
	function getProjectMinutes($projectId){
		$list=array();
		$q = new DBQuery;
		$q->addQuery("tm.id");
		$q->addTable('project_minutes', 'tm');
		$q->addWhere('project_id ='. $projectId .' AND project_id <> 0');
		$results = db_loadHashList($q->prepare(true), 'id');
		foreach ($results as $id => $data) {
			$id=$data['id'];
			$projectMinute= new ProjectMinute();
			$projectMinute->load($id);
			$list[$id]=$projectMinute;
		}		
		return $list;		
	}
	
     	function getAllProjectStakeholders($projectId){    
        $q = new DBQuery();
        $q->addQuery("c.contact_first_name, c.contact_last_name, c.contact_id");
        $q->addTable("initiating_stakeholder", "stk");
        $q->addJoin("initiating", "i", "i.initiating_id = stk.initiating_id");
        $q->addJoin("contacts", "c", "c.contact_id = stk.contact_id");
        $q->addWhere("i.project_id=" . $projectId);
        $q->addOrder("stk.initiating_id");
        $q->addOrder("stk.contact_id");
        $results = db_loadHashList($q->prepare(true), 'contact_id');
        $all_users=array();
        foreach ($results as $id => $data) {
            $all_users[$data[2]]=$data[0] . " ". $data[1];
        }
        return $all_users;
        //global $AppUI, $users, $task_id, $task_project, $obj, $projTasksWithEndDates, $tab, $loadFromTab;
        //make list with all users
        /**
          $q = new DBQuery;
          $q->addQuery("c.contact_first_name, c.contact_last_name, u.user_id");
          $q->addTable('users', 'u');
          $q->addTable('contacts', 'c');
          $q->addWhere('u.user_contact=c.contact_id');
          $results = db_loadHashList($q->prepare(true), 'user_id');
          $all_users=array();
          foreach ($results as $id => $data) {
          $all_users[$data[2]]=$data[1].",".$data[0];
          }
          return $all_users;
         */
    }
	
}

<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}


class CTaskAssignement  {
	
	
	 public function getAllUsersFromCompany($companyId){
      	// get the users on this task
		$q = new DBQuery();
		$q->addTable('users', 'u');
		$q->leftJoin('contacts', 'c' , 'user_contact = contact_id');
		$q->addQuery('u.user_id, u.user_username, contact_email, c.contact_first_name, c.contact_last_name');
		$q->addWhere('c.contact_company = ' . $companyId);
		$q->addOrder('c.contact_first_name');
		$sql = $q->prepare();
		$users = db_loadList($sql);
		return $users;
    }
	
	public function getAssignedUsersToTask($taskId){
		// get the users on this task
		$q = new DBQuery();
		$q->addTable('users', 'u');
		$q->addTable('user_tasks', 't');
		$q->leftJoin('contacts', 'c' , 'user_contact = contact_id');
		$q->addQuery('u.user_id, u.user_username, contact_email, c.contact_first_name, c.contact_last_name');
		$q->addWhere('t.task_id = ' . $taskId);
		$q->addWhere('t.user_id = u.user_id');
		$q->addOrder('u.user_username');
		$sql = $q->prepare();
		$users = db_loadList($sql);
		return $users;
	}
	
	public function addAssignedUsersToTask($taskId,$userId){
		// get the users on this task
		$q = new DBQuery();
		$q->addTable('user_tasks');
		$q->addInsert('task_id' , $taskId);
		$q->addInsert('user_id', $userId);
		$q->addInsert('user_type',0);
		$q->addInsert('perc_assignment',100);
		$q->addInsert('user_task_priority', 0);
		$sql = $q->prepare();
		$q->exec();
	}
	
	public function deleteAssignedUsersToTask($taskId,$userId){
		$q = new DBQuery();
        $q->setDelete('user_tasks');
		$q->addWhere('task_id = ' . $taskId);
		$q->addWhere('user_id = ' . $userId);
        $q->exec();
	}
}
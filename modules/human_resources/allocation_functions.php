<?php
function getAllTasks() {
	$query = new DBQuery;
	$query->addTable('tasks');
	$query->addQuery('*');
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function getEstimativesByTaskId($task_id) {
	$query = new DBQuery;
	$query->addTable('project_tasks_estimated_roles', 'e');
	$query->addQuery('e.id, c.role_name');
	$query->addJoin('company_role', 'c', 'c.id = e.role_id');
	$query->addWhere('e.task_id = ' . $task_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function getAllocationByEstimativeId($estimative_id) {
	$query = new DBQuery;
	$query->addTable('human_resource_allocation', 'a');
	$query->addQuery('a.human_resource_id, a.human_resource_allocation_id');
	$query->addWhere('a.project_tasks_estimated_roles_id = ' . $estimative_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function getEstimatedRolesByHumanResourceInTask($task_id, $human_resource_id) {
	$role_names = array();
	$estimatives = getEstimativesByTaskId($task_id);
	foreach($estimatives as $estimative) {
		$allocations = getAllocationByEstimativeId($estimative['id']);
		foreach($allocations as $allocation) {
			if($allocation['human_resource_id'] == $human_resource_id) {
				array_unshift($role_names, $estimative['role_name']);
			}
		}			
	}
	return $role_names;
}

function getHumanResourceTasksExcept($human_resource_id, $task_id, $human_resource_allocation_id) {
	$human_resource_tasks = array();
	$tasks = getAllTasks();
	foreach($tasks as $task) {
		$estimatives = getEstimativesByTaskId($task['task_id']);
		foreach($estimatives as $estimative) {
			$allocations = getAllocationByEstimativeId($estimative['id']);
			foreach($allocations as $allocation) {
				if(($allocation['human_resource_id'] == $human_resource_id && $task->task_id != $task_id) ||
					($allocation['human_resource_id'] == $human_resource_id && $task->task_id == $task_id &&
					$allocation['human_resource_allocation_id'] != $human_resource_allocation_id)) {
					array_unshift($human_resource_tasks, $task);
				}
			}			
		}
	}
	return $human_resource_tasks;
}

function tasksInSamePeriod($this_task, $that_task) {
	$this_start_date = strtotime($this_task->task_start_date);
	$this_end_date = strtotime($this_task->task_end_date);
	$that_start_date = strtotime($that_task['task_start_date']);
	$that_end_date = strtotime($that_task['task_end_date']);
	$this_start_date_conflict = $that_start_date >= $this_start_date &&
									$that_start_date <= $this_end_date;
	$this_end_date_conflict = $that_end_date >= $this_start_date &&
								$that_end_date <= $this_end_date;
	$that_start_date_conflict = $this_start_date >= $that_start_date &&
								$this_start_date <= $that_end_date;
	$that_end_date_conflict = $this_end_date >= $that_start_date &&
								$this_end_date <= $that_end_date;

	$same_period = false;
	if(($this_start_date_conflict || $this_end_date_conflict) ||
		($that_start_date_conflict || $that_end_date_conflict)) {
		$same_period = true;
	}
	return $same_period;
}

function getUserRolesByUserId($user_id) {
	$query = new DBQuery;
	$query->addTable('users', 'u');
	$query->addQuery('h.human_resource_id');
	$query->innerJoin('human_resource', 'h', 'h.human_resource_user_id = ' . $user_id);
	$query->addWhere('u.user_id = ' . $user_id);
	$res =& $query->exec();
	$human_resource_id =$res->fields['human_resource_id'];
	$roles = null;
	if($human_resource_id) {
		$roles = getUserRoles($human_resource_id);
	}
	return $roles;
}

function getUserRoles($human_resource_id) {
	$query = new DBQuery;
	$query->addTable('human_resource_roles', 'r');
	$query->addQuery('h.human_resources_role_name, h.human_resources_role_id');
	$query->innerJoin('human_resources_role', 'h', 'h.human_resources_role_id = r.human_resources_role_id');
	$query->addWhere('r.human_resource_id = ' . $human_resource_id);
	$sql = $query->prepare();
	return db_loadList($sql);
}
?>
<?php
function getRolesByCompanyId($company_id) {
	$query = new DBQuery;
	$query->addTable('human_resources_role', 'r');
	$query->addQuery('r.human_resources_role_id');
	$query->addWhere('r.human_resources_role_company_id = ' . $company_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function isConfiguredRole($human_resources_role_id) {
	$query = new DBQuery;
	$query->addTable('human_resources_role', 'r');
	$query->addQuery('r.human_resources_role_name, r.human_resources_role_authority, r.human_resources_role_responsability, r.human_resources_role_competence');
	$query->addWhere('r.human_resources_role_id = ' . $human_resources_role_id);
	$res =& $query->exec();
	$configured = false;
	if($res->fields['human_resources_role_name']) {
		if($res->fields['human_resources_role_authority'] && $res->fields['human_resources_role_responsability'] && $res->fields['human_resources_role_competence']) {
			$configured = true;
		}
	}
	$query->clear();
	return $configured;
}

function areConfiguredAllRoles($company_id) {		
	$company_roles = getRolesByCompanyId($company_id);
	$configured = true;
	foreach($company_roles as $company_role) {
		$human_resources_role_id = $company_role['human_resources_role_id'];
		if( ! isConfiguredRole($human_resources_role_id)) {
			$configured = false;
			break;
		}
	}
	return $configured;
}

function isRoleAllocated($project_tasks_estimated_roles_id) {
	$query = new DBQuery;
	$query->addTable('human_resource_allocation', 'a');
	$query->addQuery('human_resource_allocation_id');
	$query->addWhere('a.project_tasks_estimated_roles_id = ' . $project_tasks_estimated_roles_id);
	$sql = $query->prepare();
	$query->clear();
	return count(db_loadList($sql)) > 0;
}

function getProjectTasksEstimatedRolesByTaskId($task_id) {
	$query = new DBQuery;
	$query->addTable('project_tasks_estimated_roles', 'e');
	$query->addQuery('e.id');
	$query->addWhere('e.task_id = ' . $task_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function areAllTaskRolesAllocated($task_id) {
	$project_tasks_estimated_roles = getProjectTasksEstimatedRolesByTaskId($task_id);
	$allocated = true;
	foreach($project_tasks_estimated_roles as $project_tasks_estimated_role) {
		$project_tasks_estimated_roles_id = $project_tasks_estimated_role['id'];
		if( ! isRoleAllocated($project_tasks_estimated_roles_id)) {
			$allocated = false;
			break;
		}
	}
	return $allocated;
}

function getTasksByProjectId($project_id) {
	$query = new DBQuery;
	$query->addTable('tasks', 't');
	$query->addQuery('task_id');
	$query->addWhere('t.task_project = ' . $project_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function areAllProjectTasksAllocated($project_id) {
	$tasks = getTasksByProjectId($project_id);
	$allocated = true;
	foreach($tasks as $task) {
		$task_id = $task['task_id'];
		if( ! areAllTaskRolesAllocated($task_id)) {
			$allocated = false;
			break;
		}
	}
	return $allocated;
}

function getProjectsByCompanyId($company_id) {
	$query = new DBQuery;
	$query->addTable('projects', 'p');
	$query->addQuery('project_id');
	$query->addWhere('p.project_company = ' . $company_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function companyHasPolicies($company_id) {
	$query = new DBQuery;
	$query->addTable('company_policies', 'p');
	$query->addQuery('company_policies_id');
	$query->addWhere('p.company_policies_company_id = ' . $company_id);
	$sql = $query->prepare();
	$query->clear();
	return count(db_loadList($sql)) > 0;
}

function allCompanyHumanResourcesConfigured($company_id) {
	$company_users = getUsersByCompanyId($company_id);	
	$configured = true;
	foreach($company_users as $company_user) {
		if( ! userHasHumanResource($company_user['user_id'])) {
			$configured = false;
			break;
		}
	}
	return $configured;
}

function userHasHumanResource($user_id) {
	$query = new DBQuery;
	$query->addTable('human_resource', 'h');
	$query->addQuery('human_resource_id');
	$query->addWhere('h.human_resource_user_id = ' . $user_id);
	$sql = $query->prepare();
	$query->clear();
	return count(db_loadList($sql)) > 0;
}

function getHumanResourceId($user_id) {
	$query = new DBQuery;
	$query->addTable('human_resource', 'h');
	$query->addQuery('human_resource_id');
	$query->addWhere('h.human_resource_user_id = ' . $user_id);
	$sql = $query->prepare();
        $records = db_loadList($sql);
        $id=-1;
        foreach($records as $record){
            $id=$record[0];
        }
	return $id;
}

function getUserIdByHR($hr_id) {
	$query = new DBQuery;
	$query->addTable('human_resource', 'h');
	$query->addQuery('human_resource_user_id');
	$query->addWhere('h.human_resource_id = ' . $hr_id);
	$sql = $query->prepare();
        $records = db_loadList($sql);
        $id=-1;
        foreach($records as $record){
            $id=$record[0];
        }
	return $id;
}

function getUsersByCompanyId($company_id) {
	$query = new DBQuery;
	$query->addTable('users', 'u');
	$query->addQuery('user_id');
	$query->innerJoin('contacts', 'c', 'u.user_contact = c.contact_id');
	$query->addWhere('c.contact_company = ' . $company_id);
	$sql = $query->prepare();
	$query->clear();
	return db_loadList($sql);
}

function getDetailedUsersByCompanyId($company_id){
    $query = new DBQuery();
    $query->addTable("users", "u");
    $query->addQuery("user_id, user_username, contact_last_name, contact_first_name, contact_id");
    $query->addJoin("contacts", "c", "u.user_contact = c.contact_id");
    $query->addWhere("c.contact_company = " . $company_id ." and user_username not like 'Grupo%'");
    $query->addOrder("contact_last_name");
    $res = & $query->exec();
    return $res;
}
?>
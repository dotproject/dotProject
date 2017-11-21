<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/admin/admin.class.php"); //for user creation
require_once $AppUI->getSystemClass('dp');
require_once $AppUI->getSystemClass('query');

class CHumanResource extends CDpObject {
  var $human_resource_id = null;
  var $human_resource_user_id = null;
  var $human_resource_lattes_url = null;
  var $human_resource_company_role = null;
  var $human_resource_mon = null;
  var $human_resource_tue = null;
  var $human_resource_wed = null;
  var $human_resource_thu = null;
  var $human_resource_fri = null;
  var $human_resource_sat = null;
  var $human_resource_sun = null;
  var $eventual = 0;

  function CHumanResource() {    
	 parent::CDpObject('human_resource', 'human_resource_id');
	 $initial_url = substr($human_resource_lattes_url, 0, 6);
	 $http = 'http://';
	 if(strcmp($initial_url, $http) != 0) {
		$human_resource_lattes_url = $http . $human_resource_lattes_url;
	 }
  }
  
  function canDelete() {
	$query = new DBQuery();
        //Verify user allocation
	$query->addTable("human_resource_allocation", "a");
	$query->addQuery("human_resource_allocation_id");
	$query->addWhere("a.human_resource_id = " . $this->human_resource_id);
	$sql = $query->prepare();
	$query->clear();
        $userAlocation= count(db_loadList($sql)) == 0;
        //Verify user project manager
        $query->addTable("projects", "p");
	$query->addQuery("project_id");
	$query->addWhere("p.project_owner = " . $this->human_resource_user_id);
	$sql = $query->prepare();
	$query->clear();
        $userProjectManager = count(db_loadList($sql)) == 0;
        //verify risk responsible
        $query->addTable("risks", "r");
	$query->addQuery("risk_id");
	$query->addWhere("r.risk_responsible = " . $this->human_resource_user_id);
	$sql = $query->prepare();
	$query->clear();
        $userRiskResposible= count(db_loadList($sql)) == 0;
        
        //verify stakeholder
        
        $userObj = new CUser();
        $userObj->load($this->human_resource_user_id);
     
        $query->addTable("initiating_stakeholder");
	$query->addQuery("contact_id");
	$query->addWhere("contact_id = " . $userObj->user_contact);
	$sql = $query->prepare();
	$query->clear();
        $contactStakeholder= count(db_loadList($sql)) == 0;
        
	return $userProjectManager && $userAlocation && $userRiskResposible && $contactStakeholder;
  }
}

class CHumanResourceAllocation extends CDpObject {
	var $human_resource_allocation_id = null;
	var $project_tasks_estimated_roles_id = null;
	var $human_resource_id = null;
	
	function CHumanResourceAllocation() {
		parent::CDpObject('human_resource_allocation', 'human_resource_allocation_id');
	}
	
	function canDelete(&$msg, $oid=null, $joins=null) {
		return true;
	}
	
	function store($task_id, $user_id) {
		
		$q = new DBQuery;		
		$q->addTable('user_tasks');
		$q->addQuery('user_id');
		$q->addWhere('task_id = ' . $task_id . ' and user_id = ' . $user_id);
		$sql = $q->prepare();
		$user = db_loadList($sql);
		$q->clear();
		
		if(count($user) == 0) {
			$q->addTable('user_tasks');
			$q->addInsert('user_id', $user_id);
			$q->addInsert('task_id', $task_id);
			$q->addInsert('perc_assignment', '100');
			$q->exec();
			$q->clear();
		}		
		return parent::store();
	}
	
	function delete($task_id, $user_id) {
		$q = new DBQuery;
		$q->setDelete('user_tasks');
		$q->addWhere('task_id = ' . $task_id . ' AND user_id = ' . $user_id);
		$q->exec();
		$q->clear();
		
		return parent::delete();
	}
}

class CCompaniesPolicies extends CDpObject {
	var $company_policies_id = null;
	var $company_policies_recognition = null;
	var $company_policies_policy = null;
	var $company_policies_safety = null;
	var $company_policies_company_id = null;
	
	function CCompaniesPolicies() {
		parent::CDpObject('company_policies', 'company_policies_id');
	}
	
	function canDelete(&$msg, $oid=null, $joins=null) {
		return true;
	}
}

class CHumanResourcesRole extends CDpObject {

    var $human_resources_role_id = null;
    var $human_resources_role_name = null;
    var $human_resources_role_company_id = null;
    var $human_resources_role_responsability = null;
    var $human_resources_role_authority = null;
    var $human_resources_role_competence = null;

    function CHumanResourcesRole() {
        parent::CDpObject('human_resources_role', 'human_resources_role_id');
    }

    function canDelete(&$msg, $oid = null, $joins = null) {
        $query = new DBQuery();
        $query->addTable("company_role", "c");
        $query->addQuery("c.id");
        $query->addWhere('c.role_name = "' . $this->human_resources_role_name . '"');
        $sql = $query->prepare();
        return count(db_loadList($sql)) == 0 ? true : false;
    }

}

class CHumanResourceRoles extends CDpObject {
	var $human_resource_roles_id = null;
	var $human_resources_role_id = null;
	var $human_resource_id = null;
		  
	function CHumanResourceRoles() {
		parent::CDpObject('human_resource_roles', 'human_resource_roles_id');
	}
	
	function deleteAll($human_resource_id) {
		$q = new DBQuery;
		$q->setDelete('human_resource_roles');
		$q->addWhere('human_resource_id = ' . $human_resource_id);
		$q->exec();
		$q->clear();
	}
	
	function store($role_id, $human_resource_id) {
		$q = new DBQuery;		
		$q->addTable('human_resource_roles');
		$q->addInsert('human_resources_role_id', $role_id);
		$q->addInsert('human_resource_id', $human_resource_id);
		$q->exec();
		$q->clear();
	}
		  
	function canDelete(&$msg, $oid=null, $joins=null) {
		return true;
	}
}
?>

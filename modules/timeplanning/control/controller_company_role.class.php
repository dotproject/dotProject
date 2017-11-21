<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/company_role.class.php");
class ControllerCompanyRole {
	
	function ControllerCompanyRole()	{
	}
	
	function insert($company_id,$description,$identation,$id,$sort_order) {
		$companyRole = new CompanyRole(); 
		$companyRole->store($company_id,$description,$identation,$id,$sort_order) ;
	}
	

	function delete($id){
		$companyRole = new CompanyRole(); 
		$companyRole->delete($id);
	}
        
        function canDelete($id){
            $query = new DBQuery();
            $query->addTable("project_tasks_estimated_roles", "c");
            $query->addQuery("c.id");
            $query->addWhere("c.role_id = " . $id );
            $sql = $query->prepare();
            return count(db_loadList($sql)) == 0 ? true : false;
	}
	
	function getCompanyRoles($company_id){
		$list=array();
		$q = new DBQuery();
		$q->addQuery('t.id, t.role_name,t.identation');
		$q->addTable('company_role', 't');
		if($company_id!=""){
			$q->addWhere('company_id = '.$company_id .' order by sort_order');
		}
		$sql = $q->prepare();
		$roles = db_loadList($sql);
		foreach ($roles as $role) {
			$id = $role['id'];
			$name = $role['role_name'];
			$identation= $role['identation'];
			$companyRole = new CompanyRole();
			$companyRole->load($id,$name,$identation,$company_id);
			$list[$id]=$companyRole;
		}
		return $list;
	}
	
}

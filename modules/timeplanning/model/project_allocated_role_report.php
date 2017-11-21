<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

class ProjectAllocatedRole {
	
	private $roleName=NULL; 
    private $count=NULL; 
    private $sum=NULL; 
	
	function ProjectAllocatedRole($roleName,$count,$sum){
		$this->roleName=$roleName;
		$this->count=$count;
		$this->sum=$sum;
	}
	
	public function getRoleName(){
		return $this->roleName;
	}
	
	public function getCount(){
		return $this->count;
	}
	
	public function getSum(){
		return $this->sum;
	}				
}

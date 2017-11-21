<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

class ProjectAllocatedNonHumanResource {
	
	private $name=NULL; 
    private $quantity=NULL; 
    private $type=NULL; 
	
	function ProjectAllocatedNonHumanResource($name,$quantity,$type){
		$this->name=$name;
		$this->quantity=$quantity;
		$this->type=$type;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getQuantity(){
		return $this->quantity;
	}
	
	public function getType(){
		return $this->type;
	}				
}

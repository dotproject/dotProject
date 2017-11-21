<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
//  $description,$consultation,$execut,$support,$approve,
class Respons{
	private $respons_id = NULL; 
    private $description = NULL; 
    private $consultation = NULL; 
    private $execut = NULL; 
	private $support = NULL; 
	private $approve = NULL; 
	
	
	public function getRespons_Id(){
		return $this->respons_id;
	}
	public function getDescription(){
		return $this->description;
	}
	public function getConsultation(){
		return $this->consultation;
	}	
	public function getExecut(){
		return $this->execut;
	}		
	public function getSupport(){
		return $this->support;
	}	
	public function getApprove(){
		return $this->approve;
	}		
}
?>
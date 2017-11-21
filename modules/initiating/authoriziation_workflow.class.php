<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

require_once $AppUI->getSystemClass('dp');
/**
 * Initiating Class
 */
class CAuthorizationWorkflow extends CDpObject {

  var $initiating_id = NULL;
  
  var $draft_by= NULL;
  var $draft_when= NULL;
  
  var $completed_by= NULL;
  var $completed_when= NULL;

  var $approved_by= NULL;
  var $approved_when= NULL;
  
  var $authorized_by= NULL;
  var $authorized_when= NULL;


    function CAuthorizationWorkflow() {
        $this->CDpObject('authorization_workflow', 'initiating_id');
    }
    

    public function insert(){
        $query = new DBQuery();
        $query->addTable("authorization_workflow");
        $query->addInsert("initiating_id", $this->initiating_id);
        $query->exec();
    }
    
    
public function update(){
    $query = new DBQuery();
    $query->addTable("authorization_workflow");
    $query->addUpdate("draft_when", $this->draft_when);
    $query->addUpdate("draft_by",  $this->draft_by);
    $query->addUpdate("completed_when",  $this->completed_when);
    $query->addUpdate("completed_by",  $this->completed_by);
    $query->addUpdate("approved_when",  $this->approved_when);
    $query->addUpdate("approved_by",  $this->approved_by);
    $query->addUpdate("authorized_when",  $this->authorized_when);
    $query->addUpdate("authorized_by",   $this->authorized_by);
    $query->addWhere("initiating_id=".$this->initiating_id);
    $query->exec();
}
    
    
}
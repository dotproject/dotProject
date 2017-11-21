<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

//require_once $AppUI->getSystemClass('dp');
require_once($AppUI->getSystemClass('dp'));
/**
 * FeedbackEvaluation Class
 */
class CFeedbackEvaluation extends CDpObject {
	var $id = NULL;
	var $user_id = NULL;
        var $feedback_id = NULL;
        var $grade=NULL;
         
	function __construct() {
            $this->CDpObject('feedback_evaluation', 'id');
	}
        
        public function store(){
            $q = new DBQuery();
            $q->addQuery("id");
            $q->addTable("feedback_evaluation");
            $q->addWhere("user_id=". $this->user_id ." and feedback_id=" . $this->feedback_id);
            $list = $q->loadList();
            $q->clear();
            $q->addTable("feedback_evaluation");
            if (sizeof($list)==0){
                $q->addInsert("user_id", $this->user_id);
                $q->addInsert("feedback_id", $this->feedback_id);
                $q->addInsert("grade", $this->grade);
            }else{
                $q->addUpdate("grade", $this->grade);
                $q->addWhere("user_id=". $this->user_id ." and feedback_id=" . $this->feedback_id);
            }
            $q->exec();
        }
}
?>
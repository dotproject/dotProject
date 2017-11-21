<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

class FeedbackEvaluationStatistic  {
	var $average = 0;
	var $stdv = 0;
        var $total = 0;
         
	function __construct($feedbackId) {
            //SELECT avg(grade),stddev(grade),count(grade) FROM dotproject_plus.dotp_feedback_evaluation where feedback_id=4;
            $q = new DBQuery();
            $q->addQuery("avg(grade),stddev(grade),count(grade) ");
            $q->addTable("feedback_evaluation");
            $q->addWhere("feedback_id=" . $feedbackId);
            //$list = $q->loadList();
            $sql = $q->prepare();
            $list=db_loadList($sql);
            foreach ($list as $record){
                $this->average = $record[0];
                $this->stdv = $record[1];
                $this->total=$record[2];
            }
        }
        
        function getAverage() {
            return $this->average;
        }

        function getStdv() {
            return $this->stdv;
        }

        function getTotal() {
            return $this->total;
        }       
}
?>
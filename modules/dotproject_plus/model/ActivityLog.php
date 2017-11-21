<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ActivityLog
 *
 * @author rafael
 */
class ActivityLog {
    var $result=array();
    
    public function getActivitiesActualDates($activityId){
        $q = new DBQuery();
        $q->addTable("task_log");
        $q->addQuery("min(task_log_date), max(task_log_date)");
        $q->addWhere("task_log_task=" . $activityId);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        $this->result=array();
        foreach ($records as $record) {
            $this->result[0]= $record[0];
            $this->result[1]= $record[1];
        }
        return $this->result;
        //SELECT min(task_log_date), max(task_log_date) FROM dotproject_plus.dotp_task_log where task_log_task=119;
    }
    /**
     * Method have to be called just after getActivitiesActualDates.
     */
    public function getActivityActualDuration(){
        $duration="";
        if(sizeof($this->result)==2){
            $startDateTxt=$this->result[0];
            $endDateTxt=$this->result[1];
             $calculateDuratation = true;
            if ($startDateTxt != "") {
                $dateStart = new DateTime();
                $startDateTxt  = date("d/m/Y", strtotime($startDateTxt));
                $dateParts = explode("/", $startDateTxt);
                $dateStart->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
                $dateStart->setTime(0, 0, 0);
                $d1=mktime(0,0,0,(int)$dateParts[1],(int)$dateParts[0],(int)$dateParts[2]);
            } else {
                $calculateDuratation = false;
            }
            if ($endDateTxt != "") {
                $dateEnd = new DateTime();
                $endDateTxt  = date("d/m/Y", strtotime($endDateTxt));
                $dateParts = explode("/", $endDateTxt);
                $dateEnd->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
                $dateEnd->setTime(0, 0, 0);
                $d2=mktime(0,0,0,(int)$dateParts[1],(int)$dateParts[0],(int)$dateParts[2]);
            } else {
                $calculateDuratation = false;
            }

            if ($calculateDuratation) {
                $duration = floor(($d2-$d1)/86400);
                $duration++;//add 1 more day to include the start date.
            }
        }
        return $duration;
    }
    
    public function getActivityLogs($activityId){
        $q = new DBQuery();
        $q->addQuery("task_log_id,task_log_name,task_log_date, task_log_creator, c.contact_first_name,c.contact_last_name");
        $q->addTable("task_log","tl");
        $q->addJoin("users","u","tl.task_log_creator = u.user_id","inner");
        $q->addJoin("contacts","c","u.user_contact=c.contact_id","inner");        
        $q->addWhere("task_log_task=" . $activityId);
        $q->addOrder("task_log_date asc");
        $sql = $q->prepare();
        //echo $sql;
        $records = db_loadList($sql);
        $resultSet=array();
        $i=0;
        foreach ($records as $record) {
            $fields=array();
            $fields[0]= $record[0];
            $fields[1]= $record[1];
            $fields[2]=  date("d/m/Y", strtotime($record[2]));
            $fields[3]=  $record[3];
            $fields[4]=  $record[4];
            $fields[5]=  $record[5];
            $resultSet[$i]=$fields;
            $i++;
        }
        return $resultSet;
        
        
        //SELECT task_log_id,task_log_name,task_log_date,task_log_creator,c.contact_first_name,c.contact_last_name FROM dotp_task_log tl inner join dotp_users u on tl.task_log_creator = u.user_id inner join dotp_contacts c on u.user_contact=c.contact_id where  task_log_task=119
    }
}

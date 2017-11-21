<?php

if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly");
}

/*
  DB filelds - TABLE: "monitoring_meeting" :
  `meeting_id`,`project_id`,`dt_meeting_begin`,`ds_title`,`ds_subject`,`dt_meeting_end`
 */
/*
  DB filelds - TABLE: "monitoring_meeting_item" :
  `meeting_item_id`,`meeting_item_description`
 */
/*
  DB filelds - TABLE: "monitoring_meeting_item_select" :
  `meeting_item_select_id`,`meeting_item_id`,`meeting_id`,`status`
 */

class ControllerAta {

//////////////////  UPDATE 	///////////////////////////////

    function updateMeeting($project_id, $dt_begin, $dt_end, $title, $subject, $meeting_id) {
        $q = new DBQuery();
        $q->addTable("monitoring_meeting");
        $q->addUpdate("dt_meeting_begin", $dt_begin);
        $q->addUpdate("dt_meeting_end", $dt_end);
        $q->addUpdate("ds_title", $title);
        $q->addUpdate("ds_subject", $subject);
        $q->addUpdate("project_id", $project_id);
        $q->addWhere("meeting_id = " . $meeting_id);
        $q->exec();
    }

    function updateParticipants($participants, $meeting_id) {
        $q = new DBQuery();
        $q->setDelete("monitoring_meeting_user");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $count = count($participants);
        for ($i = 0; $i < $count; $i++) {
            $q->addTable("monitoring_meeting_user");
            $q->addInsert("meeting_id", $meeting_id);
            $q->addInsert("user_id", $participants[$i]);
            $q->exec();
        }
    }

    function updateMeetingItens($item_id, $item_status, $meeting_id) {
        $q = new DBQuery();
        $q->setDelete("monitoring_meeting_item_select");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $count = count($item_id);
        for ($i = 0; $i < $count; $i++) {
            $q->addTable("monitoring_meeting_item_select");
            $q->addInsert("meeting_id", $meeting_id);
            $q->addInsert("meeting_item_id", $item_id[$i]);
            $q->addInsert("status", $item_status[$i]);
            $q->exec();
        }
    }
    
    

//////////////////  INSERT 	///////////////////////////////

    function insert($project_id, $dt_begin, $dt_end, $title, $subject, $meeting_type_id) {
        $q = new DBQuery();
        $q->addTable("monitoring_meeting");
        $q->addInsert("project_id", $project_id);
        $q->addInsert("dt_meeting_begin", $dt_begin);
        $q->addInsert("dt_meeting_end", $dt_end);
        $q->addInsert("ds_title", $title);
        $q->addInsert("ds_subject", $subject);
        $q->addInsert("meeting_type_id", $meeting_type_id);

        $q->exec();
    }

    function insertParticipants($participants, $last_meeting_id) {
        $q = new DBQuery();
        $count = count($participants);
        for ($i = 0; $i < $count; $i++) {
            $q->addTable("monitoring_meeting_user");
            $q->addInsert("meeting_id", $last_meeting_id);
            $q->addInsert("user_id", $participants[$i]);
            $q->exec();
        }
    }

    function insertMeetingItens($item_id, $status, $meeting_id) {
        $q = new DBQuery();
        $q->setDelete("monitoring_meeting_item_select");
        $q->addWhere("meeting_id= $meeting_id");
        $q->exec();
        $q->clear();
        $count = count($item_id);
        for ($i = 0; $i < $count; $i++) {
            $q->addTable("monitoring_meeting_item_select");
            $q->addInsert("meeting_id", $meeting_id);
            $q->addInsert("meeting_item_id", $item_id[$i]);
            $q->addInsert("status", $status[$i]);
            $q->exec();
        }
    }

    function insertMeetingTask($task_id_entrega, $status, $last_id) {
        $q = new DBQuery();
        $count = count($task_id_entrega);

        for ($i = 0; $i < $count; $i++) {
            if ($status[$i] == 0) {
                $q->addTable("monitoring_meeting_item_tasks_delivered");
                $q->addInsert("meeting_id", $last_id);
                $q->addInsert("task_id", $task_id_entrega[$i]);
                $q->exec();
            }
        }
    }

    function insertMeetingReport($percentual, $tamanho, $idc, $idp, $va, $vp, $cr, $baseline, $last_id) {
        $q = new DBQuery();

        $q->addTable("monitoring_meeting_item_senior");
        $q->addInsert("meeting_percentual", $percentual);
        $q->addInsert("meeting_size", $tamanho);
        $q->addInsert("meeting_idc", $idc);
        $q->addInsert("meeting_idp", $idp);
        $q->addInsert("meeting_vp", $va);
        $q->addInsert("meeting_va", $vp);
        $q->addInsert("meeting_cr", $cr);
        $q->addInsert("meeting_baseline", $baseline);
        $q->addInsert("meeting_id", $last_id);
        $q->exec();
    }

///////////////////////////////////////////////////////////////////

    function deleteRow($meeting_id) {
        $q = new DBQuery();
        $q->setDelete("monitoring_change_request");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $q->clear();

        $q = new DBQuery();
        $q->setDelete("monitoring_meeting_user");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $q->clear();

        $q->setDelete("monitoring_meeting_item_tasks_delivered");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $q->clear();

        $q->setDelete("monitoring_meeting_item_senior");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $q->clear();

        $q->setDelete("monitoring_meeting_item_select");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $q->clear();

        $q->setDelete("monitoring_meeting");
        $q->addWhere("meeting_id=" . $meeting_id);
        $q->exec();
        $q->clear();
    }

///////////////////////////////////////////////////////////////
    //items selected by meeting
    function getItemSelect($meeting_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addQuery("s.status");
        $q->addQuery("i.meeting_item_id");
        $q->addQuery("i.meeting_item_description");
        $q->addQuery("i.knownledge_area");
        $q->addTable("monitoring_meeting_item_select", "s");
        $q->innerJoin("monitoring_meeting_item", "i", "i.meeting_item_id = s.meeting_item_id");
        $q->addWhere("meeting_id =" . $meeting_id);
        $q->addOrder("i.knownledge_area, i.item_order ASC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getMeeting($project_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addQuery("meeting_id, project_id, dt_meeting_begin, ds_title,ds_subject, t.meeting_type_name, t.meeting_type_id");
        $q->addTable("monitoring_meeting", "m");
        $q->innerJoin("monitoring_meeting_type", "t", "t.meeting_type_id = m.meeting_type_id");

        $q->addWhere("project_id=" . $project_id);
        $q->addOrder("meeting_id ASC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getListById($meeting_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addQuery("meeting_id, project_id, dt_meeting_begin, ds_title,ds_subject,dt_meeting_end, t.meeting_type_id, t.meeting_type_name");
        $q->addTable("monitoring_meeting", "m");
        $q->innerJoin("monitoring_meeting_type", "t", "t.meeting_type_id = m.meeting_type_id");
        $q->addWhere("meeting_id =" . $meeting_id);
        $q->addOrder("meeting_id ASC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list[0];
    }

    function getMeetingItem() {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addTable("monitoring_meeting_item", "i");
        $q->addQuery("meeting_item_id");
        $q->addQuery("meeting_item_description");
        $q->addQuery("knownledge_area");
        $language = $AppUI->user_locale;
        $q->addWhere("language like '%" . $language . "%'");
        $q->addOrder("knownledge_area, item_order ASC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }
    
  function getMeetingItemResponse($meeting_id,$item_id) {
        $q = new DBQuery();
        $q->addTable("monitoring_meeting_item_select");
        $q->addQuery("status");
        $q->addWhere("meeting_item_id=$item_id and meeting_id=$meeting_id");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $answer=0;
        foreach($list as $record){
            $answer=$record["status"];
        }
        return $answer;
    }

    function getMeetingItemByKnowledgeArea($knownledge_area) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addTable("monitoring_meeting_item", "i");
        $q->addQuery("meeting_item_id");
        $q->addQuery("meeting_item_description");
        $q->addQuery("knownledge_area");
        $language = $AppUI->user_locale;
        $q->addWhere("language like '%" . $language . "%' and knownledge_area='$knownledge_area'");
        $q->addOrder("item_order ASC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getMeetingType() {
        $list = array();
        $q = new DBQuery;
        $q->addTable("monitoring_meeting_type");
        $q->addQuery("meeting_type_id");
        $q->addQuery("meeting_type_name");
        $q->addOrder("meeting_type_id ASC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getMeetingId($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addTable("monitoring_meeting");
        $q->addQuery("meeting_id");
        $q->addWhere("project_id =" . $project_id);
        $q->addOrder("meeting_id DESC");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getUsersById($meeting_id) {
        global $AppUI;
        $users = array();
        $q = new DBQuery;
        $q->addQuery("user_id");
        $q->addTable("monitoring_meeting_user", "u");
        $q->addWhere("meeting_id =" . $meeting_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getTasksFinished($project_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addTable("tasks", "t");
        $q->addQuery("t.task_name, t.task_duration, t.task_start_date, t.task_end_date, t.task_id ");
        $q->addWhere("t.task_project =" . $project_id);

        $qSub = new DBQuery;
        $qSub->addQuery("tSub.task_id ");
        $qSub->addTable("monitoring_meeting_item_tasks_delivered", "dSub");
        $qSub->innerJoin("tasks", "tSub", "tSub.task_id = dSub.task_id");
        $qSub->addWhere("tSub.task_project =" . $project_id);
        $sqlSub = $qSub->prepare();
        $q->addWhere("t.task_id not in (" . $sqlSub . ")");
        $q->addWhere("t.task_percent_complete=100");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getTasksFinishedSelected($meeting_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addQuery("t.task_name, t.task_duration, t.task_start_date, t.task_end_date, t.task_id");
        $q->addTable("monitoring_meeting_item_tasks_delivered", "d");
        $q->innerJoin("tasks", "t", "t.task_id = d.task_id");
        $q->addWhere("d.meeting_id=" . $meeting_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getReportSenior($meeting_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addQuery("s.meeting_percentual, s.meeting_size, s.meeting_idc, s.meeting_idp, s.meeting_vp, s.meeting_va, s.meeting_cr, s.meeting_baseline");
        $q->addTable("monitoring_meeting_item_senior", "s");
        $q->addWhere("s.meeting_id=" . $meeting_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function getUsersSelected($meeting_id) {
        global $AppUI;
        $list = array();
        $q = new DBQuery;
        $q->addQuery("u.user_id");
        $q->addQuery("u.user_username");

        $q->addTable("monitoring_meeting_user", "m");
        $q->innerJoin("users", "u", "m.user_id = u.user_id");
        $q->addWhere("m.meeting_id =" . $meeting_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

}

?>
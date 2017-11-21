<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

class ProjectMinute {

    private $id = NULL;
    private $description = NULL;
    private $isResource = NULL;
    private $isEffort = NULL;
    private $isDuration = NULL;
    private $isSize = NULL;
    private $date = NULL;
    private $members = NULL;

    public function getId() {
        return $this->id;
    }

    public function getMembers() {
        return $this->members;
    }

    public function getDate() {
        return $this->date;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isEffort() {
        return $this->isEffort;
    }

    public function isDuration() {
        return $this->isDuration;
    }

    public function isResource() {
        return $this->isResource;
    }

    public function isSize() {
        return $this->isSize;
    }

    function ProjectMinute() {
        
    }

    function store($description, $date, $project_id, $id, $isEffort, $isDuration, $isResource, $isSize, $membersIds) {
        $q = new DBQuery();
        $q->addTable('project_minutes');
        if ($id != "-1") {
            $q->addUpdate('description', $description);
            $q->addUpdate('minute_date', $date);
            $q->addUpdate('isEffort', $isEffort);
            $q->addUpdate('isDuration', $isDuration);
            $q->addUpdate('isResource', $isResource);
            $q->addUpdate('isSize', $isSize);
            $q->addWhere('id =' . $id);
        } else {
            $q->addInsert('description', $description);
            $q->addInsert('minute_date', $date);
            $q->addInsert('project_id', $project_id);
            $q->addInsert('isEffort', $isEffort);
            $q->addInsert('isDuration', $isDuration);
            $q->addInsert('isResource', $isResource);
            $q->addInsert('isSize', $isSize);
        }
        $q->exec();
        if ($id == -1) {
            $qId = new DBQuery();
            $qId->addQuery('max(id)');
            $qId->addTable('project_minutes');
            $results = db_loadHashList($qId->prepare(true), 'id');
            foreach ($results as $id => $data) {
                $id = $data[0];
            }
        }
        $this->setMembers($membersIds, $id);
    }

    function setMembers($membersIds, $id) {
        $this->deleteMembersRelations($id);
        foreach ($membersIds as $memberId) {
            if ($memberId != "") {
                $q = new DBQuery();
                $q->addTable('task_minute_members');
                $q->addInsert('task_minute_id', $id);
                $q->addInsert('user_id', $memberId);
                $q->exec();
            }
        }
    }

    function deleteMembersRelations($id) {
        $q = new DBQuery();
        $q->setDelete('task_minute_members');
        $q->addWhere('task_minute_id =' . $id);
        $result = true;
        if (!$q->exec()) {
            $result = false;
        }
        return $result;
    }

    function load($minute_id) {
        $q = new DBQuery;
        $q->addQuery("tm.description, tm.minute_date,tm.id,tm.isEffort,tm.isSize,tm.isDuration,tm.isResource");
        $q->addTable('project_minutes', 'tm');
        $q->addWhere('id =' . $minute_id);
        $results = db_loadHashList($q->prepare(true), 'id');
        $hasMinute = false;
        foreach ($results as $id => $data) {
            $description = $data[0];
            $date = $data[1];
            $id = $data[2];
            $dateObj = new DateTime($date);
            $date = $dateObj->format('d/m/Y');
            $isEffort = $data[3];
            $isSize = $data[4];
            $isDuration = $data[5];
            $isResource = $data[6];
        }
        $this->id = $id;
        $this->description = $description;
        $this->date = $date;
        $this->isResource = $isResource;
        $this->isDuration = $isDuration;
        $this->isSize = $isSize;
        $this->isEffort = $isEffort;

        $q = new DBQuery();
        $q->addTable('task_minute_members', 'tm');
        $q->addQuery("tm.user_id");
        $q->addWhere('task_minute_id =' . $minute_id);
        $results = db_loadHashList($q->prepare(true), 'user_id');
        $this->members = array();
        foreach ($results as $user_id => $data) {
            $key = $data[0] . "";
            $this->members[$key] = $key;
        }
    }

    function delete($id) {
        $q = new DBQuery();
        $q->setDelete('project_minutes');
        $q->addWhere('id =' . $id);
        $result = true;
        if (!$q->exec()) {
            $result = false;
        }
        return $result;
    }

}

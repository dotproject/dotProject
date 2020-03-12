<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

class WBSItem {

    private $id = NULL;
    private $name = NULL;
    private $projectId = NULL;
    private $number = NULL;
    private $isLeaf = NULL;
    private $identation = NULL;
    private $sortOrder = NULL;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    public function getNumber() {
        return $this->number;
    }

    public function isLeaf() {
        return $this->isLeaf;
    }

    public function getIdentation() {
        return $this->identation;
    }

    public function getSortOrder() {
        return $this->sortOrder;
    }

    function store($projectId, $description, $number, $sortOrder, $isLeaf, $identation, $id) {
        $q = new DBQuery();
        $q->addQuery('id');
        $q->addTable('project_ear_items');
        $q->addWhere('id =' . $id);
        $record = $q->loadResult();
        $q->clear();
        $q->addTable('project_ear_items');
        if (empty($record)) {
            $q->addInsert('project_id', $projectId);
            $q->addInsert('item_name', $description);
            $q->addInsert('sort_order', $sortOrder);
            $q->addInsert('number', $number);
            $q->addInsert('is_leaf', $isLeaf);
            $q->addInsert('identation', $identation);
        } else {
            $q->addUpdate('identation', $identation);
            $q->addUpdate('sort_order', $sortOrder);
            $q->addUpdate('number', $number);
            $q->addUpdate('is_leaf', $isLeaf);
            $q->addUpdate('item_name', $description);
            $q->addWhere('id = ' . $id);
        }
        $q->exec();
        $q->clear();
    }

    function load($idValue, $name, $identation, $number, $is_leaf) {
        $this->id = $idValue;
        $this->name = $name;
        $this->identation = $identation;
        $this->number = $number;
        $this->isLeaf = $is_leaf;
    }

    function delete($id) {
        $q = new DBQuery();
        $q->setDelete('project_ear_items');
        $q->addWhere('id=' . $id);
        $q->exec();
        $q->clear();
    }

}

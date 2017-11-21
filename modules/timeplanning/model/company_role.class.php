<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

class CompanyRole {

    private $id = NULL;
    private $description = NULL;
    private $identation = NULL;
    private $companyId = NULL;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getIdentation() {
        return $this->identation;
    }

    public function setIdentation($identation) {
        $this->identation = $identation;
    }

    public function getCompanyId() {
        return $this->companyId;
    }

    public function setCompanyId($companyId) {
        $this->companyId = $companyId;
    }

    function CompanyRole() {
        
    }

    function store($company_id, $description, $identation, $id, $sort_order) {
        $q = new DBQuery();
        $q->addQuery('id');
        $q->addTable('company_role');
        $q->addWhere('id =' . $id);
        $record = $q->loadResult();
        $q->clear();
        $q->addTable('company_role');
        if (empty($record)) {
            $q->addInsert('company_id', $company_id);
            $q->addInsert('role_name', $description);
            $q->addInsert('identation', $identation);
            $q->addInsert('sort_order', $sort_order);
        } else {
            $q->addUpdate('identation', $identation);
            $q->addUpdate('role_name', $description);
            $q->addUpdate('sort_order', $sort_order);
            $q->addWhere('id = ' . $id);
        }
        $q->exec();
    }

    function load($id, $description, $identation, $companyId) {
        $this->id = $id;
        $this->description = $description;
        $this->identation = $identation;
        $this->companyId = $companyId;
    }

    function delete($id) {
        $q = new DBQuery();
        $q->setDelete('company_role');
        $q->addWhere('id =' . $id);
        $q->exec();
    }

}

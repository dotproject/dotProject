<?php
/**
 * This class represents acquisition planning entity. 
 */
class AcquisitionPlanning {
    private $id = -1;
    private $projectId = null;
    private $acquisitionRoles = null;
    private $additionalRequirements= null;
    private $contractType=null;
    private $criteriaForSelection=null;
    private $documentsToAcquisition=null;
    private $itemsToBeAcquired=null;
    private $supplierManagementProcess=null;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    public function setProjectId($projectId) {
        $this->projectId = $projectId;
    }

    public function getAcquisitionRoles() {
        return $this->acquisitionRoles;
    }

    public function setAcquisitionRoles($acquisitionRoles) {
        $this->acquisitionRoles = $acquisitionRoles;
    }

    public function getAdditionalRequirements() {
        return $this->additionalRequirements;
    }

    public function setAdditionalRequirements($additionalRequirements) {
        $this->additionalRequirements = $additionalRequirements;
    }

    public function getContractType() {
        return $this->contractType;
    }

    public function setContractType($contractType) {
        $this->contractType = $contractType;
    }

    public function getCriteriaForSelection() {
        return $this->criteriaForSelection;
    }

    public function setCriteriaForSelection($criteriaForSelection) {
        $this->criteriaForSelection = $criteriaForSelection;
    }

    public function getDocumentsToAcquisition() {
        return $this->documentsToAcquisition;
    }

    public function setDocumentsToAcquisition($documentsToAcquisition) {
        $this->documentsToAcquisition = $documentsToAcquisition;
    }

    public function getItemsToBeAcquired() {
        return $this->itemsToBeAcquired;
    }

    public function setItemsToBeAcquired($itemsToBeAcquired) {
        $this->itemsToBeAcquired = $itemsToBeAcquired;
    }

    public function getSupplierManagementProcess() {
        return $this->supplierManagementProcess;
    }

    public function setSupplierManagementProcess($supplierManagementProcess) {
        $this->supplierManagementProcess = $supplierManagementProcess;
    }

    function AcquisitionPlanning() {
        
    }

    public function store() {
        $returnId=$this->getId();
        $q = new DBQuery();
        $q->addQuery("id");
        $q->addTable("acquisition_planning");
        $q->addWhere("id =" . $this->getId());
        $record = $q->loadResult();
        $q->clear();
        $q->addTable("acquisition_planning");
        if (empty($record)) {
            $q->addInsert("project_id", $this->getProjectId());
            $q->addInsert("items_to_be_acquired", $this->getItemsToBeAcquired());
            $q->addInsert("documents_to_acquisition", $this->getDocumentsToAcquisition());
            $q->addInsert("criteria_for_supplier_selection", $this->getCriteriaForSelection());
            $q->addInsert("additional_requirements", $this->getAdditionalRequirements());
            $q->addInsert("supplier_management_process", $this->getSupplierManagementProcess());
            $q->addInsert("acquisition_roles", $this->getAcquisitionRoles());
            $q->addInsert("contract_type", $this->getContractType());
            
        } else {
            $q->addUpdate("project_id", $this->getProjectId());
            $q->addUpdate("items_to_be_acquired", $this->getItemsToBeAcquired());
            $q->addUpdate("documents_to_acquisition", $this->getDocumentsToAcquisition());
            $q->addUpdate("criteria_for_supplier_selection", $this->getCriteriaForSelection());
            $q->addUpdate("additional_requirements", $this->getAdditionalRequirements());
            $q->addUpdate("supplier_management_process", $this->getSupplierManagementProcess());
            $q->addUpdate("acquisition_roles", $this->getAcquisitionRoles());
            $q->addUpdate("contract_type", $this->getContractType());
            $q->addWhere("id = " . $this->getId());
        }
        $q->exec();
        $q->clear();
        if (empty($record)) {
            $returnId=mysql_insert_id();
        }
        return $returnId;
    }

    /**
     * This method loads a acquisition planning object based in its identifier.
     * @param type $id 
     */
    public function load($id) {
        $q = new DBQuery();
        $q->addQuery("a.id, a.project_id, a.items_to_be_acquired, a.contract_type, a.documents_to_acquisition, a.criteria_for_supplier_selection, a.additional_requirements, a.supplier_management_process, a.acquisition_roles");
        $q->addTable("acquisition_planning", "a");
        $q->addWhere("id =" . $id);
        $results = db_loadHashList($q->prepare(true), "a.id");
        foreach ($results as $id => $data) {
           $this->setId($data[0]);
           $this->setProjectId($data[1]);
           $this->setItemsToBeAcquired($data[2]);
           $this->setContractType($data[3]);
           $this->setDocumentsToAcquisition($data[4]);
           $this->setCriteriaForSelection($data[5]);
           $this->setAdditionalRequirements($data[6]);
           $this->setSupplierManagementProcess($data[7]);
           $this->setAcquisitionRoles($data[8]);
        }
    }
    /**
     *This function returns all acquisitions planning for a project.
     *The return value is an array type composed on AcquisitionPlanning objects
     */
    public function loadAll($projectId){
        $q = new DBQuery();
        $q->addQuery("a.id, a.project_id, a.items_to_be_acquired, a.contract_type, a.documents_to_acquisition, a.criteria_for_supplier_selection, a.additional_requirements, a.supplier_management_process, a.acquisition_roles");
        $q->addTable("acquisition_planning", "a");
        $q->addWhere("project_id =" . $projectId);
        $results = db_loadHashList($q->prepare(true), "id");
        $list= array();
        $i=0;
        foreach ($results as $data) {
           $acquisition= new AcquisitionPlanning();
           $acquisition->setId($data[0]);
           $acquisition->setProjectId($data[1]);
           $acquisition->setItemsToBeAcquired($data[2]);
           $acquisition->setContractType($data[3]);
           $acquisition->setDocumentsToAcquisition($data[4]);
           $acquisition->setCriteriaForSelection($data[5]);
           $acquisition->setAdditionalRequirements($data[6]);
           $acquisition->setSupplierManagementProcess($data[7]);
           $acquisition->setAcquisitionRoles($data[8]);
           $list[$i]=$acquisition;
           $i++;
        }
        return $list;
    }

    /**
     * This function deleted a record from acquisition planning table
     * @param type $id 
     */
    function delete($id) {
        $q = new DBQuery();
        $q->setDelete("acquisition_planning");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

}
?>

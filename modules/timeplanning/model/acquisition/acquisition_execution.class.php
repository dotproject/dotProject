<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
require_once $AppUI->getSystemClass('dp');
class AcquisitionExecution extends CDpObject  {
    var $id = null;
    var $project_id = null;
    var $is_delivered =0;
    var $is_risk_contingency=0;
    var $value = null;
    var $date=null;
    var $description=null;
    var $reference_id=null;
   

    function AcquisitionExecution() {
	$this->CDpObject("acquisition_execution", "id");
    }
    
    
    /**
     * This function deleted a record from acquisition planning table
     * @param type $id 
     */
    function delete($id) {
        $q = new DBQuery();
        $q->setDelete("acquisition_execution");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

    /**
     *This function returns all acquisitions planning for a project.
     *The return value is an array type composed on AcquisitionPlanning objects
     */
    public function loadAll($project_id){
        $q = new DBQuery();
        $q->addQuery("a.id");
        $q->addTable("acquisition_execution", "a");
        $q->addWhere("project_id =" . $project_id);
        $q->addOrder("a.date");
        $results = db_loadHashList($q->prepare(true), "id");
        $list= array();
        $i=0;
        foreach ($results as $data) {
           $acquisition= new AcquisitionExecution();
           $acquisition->load($data[0]);
           $list[$i]=$acquisition;
           $i++;
        }
        return $list;
    }
}
?>

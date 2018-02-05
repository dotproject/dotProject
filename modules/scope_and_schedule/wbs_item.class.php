<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
require_once $AppUI->getSystemClass('dp');

class WBSItem extends CDpObject  {
	
	 var $id = null;
	 var $project_id = null;
	 var $sort_order = null;
	 var $item_name  = null;
	 var $number  = null;
	 var $is_leaf = null;
	 var $id_wbs_item_parent = null;
		  

    function WBSItem() {
	$this->CDpObject("project_wbs_items", "id");
    }
    
    
    /**
     * This function deleted a record from project_wbs_items planning table
     * @param type $id 
     */
    function delete($id) {
        $q = new DBQuery();
        $q->setDelete("project_wbs_items");
        $q->addWhere("id=" . $id);
        $q->exec();
        $q->clear();
    }

    /**
     *This function returns all project_wbs_items for a project.
     *The return value is an array type composed of WBSItem objects
     */
    public function loadRoot($project_id){
        $q = new DBQuery();
        $q->addQuery("wbs.id");
        $q->addTable("project_wbs_item", "wbs");
        $q->addWhere("project_id =" . $project_id " and id_wbs_item_parent is null");
        $results = db_loadHashList($q->prepare(true), "id");
        $list= array();
        $i=0;
        foreach ($results as $data) {
           $wbsItem = new WBSItem();
           $wbsItem->load($data[0]);
           $list[$i]=$wbsItem;
           $i++;
        }
        return $list;
    }
}
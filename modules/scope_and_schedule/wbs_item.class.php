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
		  
	function __construct() {
		parent::__construct("project_wbs_items", "id");
	}

    
    /**
     * This function deleted a record from project_wbs_items planning table
     * @param type $id 
     */
    function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
        $q = new DBQuery();
        $q->setDelete("project_wbs_items");
        $q->addWhere("id=" . $oid);
        $q->exec();
        $q->clear();
    }

    /**
     *This function returns all project_wbs_items for a project.
     *The return value is an array type composed of WBSItem objects
     */
    public function loadWBSItems($project_id,$parent_id){
        $q = new DBQuery();
        $q->addQuery("wbs.id");
        $q->addTable("project_wbs_items", "wbs");
        $q->addWhere("project_id = $project_id and id_wbs_item_parent = $parent_id order by sort_order asc");
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
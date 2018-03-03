<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

require_once (DP_BASE_DIR . "/modules/risks/wbs_item.class.php");
class ControllerWBSItem {
	
	function ControllerWBSItem(){
	}
	
	function insert($projectId,$description,$number,$sortOrder,$isLeaf,$identation,$id) {
		$WBSItem= new WBSItem();
		$WBSItem->store($projectId,$description,$number,$sortOrder,$isLeaf,$identation,$id);
	}
	
	function delete($id){
		$WBSItem= new WBSItem(); 
		$WBSItem->delete($id);
	}
	
	function getWBSItems($projectId){
		$list=array();
		$q = new DBQuery();
		$q->addQuery('t.id, t.item_name,t.identation,t.number,t.is_leaf');
		$q->addTable('project_ear_items', 't');
		$q->addWhere('project_id = '.$projectId .' order by sort_order');
		$sql = $q->prepare();
		$items = db_loadList($sql);
		foreach ($items as $item) {
			$id = $item['id'];
			$name = $item['item_name'];
			$identation= $item['identation'];
			$number = $item['number'];
			$is_leaf= $item['is_leaf']; 
			$WBSItem= new WBSItem();
			$WBSItem->load($id,$name,$identation,$number,$is_leaf);
			$list[$id]=$WBSItem;
		}
		return $list;
	}
	
	function getWorkPackages($projectId){
		$list=array();
		$q = new DBQuery();
		$q->addQuery('t.id, t.item_name,t.identation,t.number,t.is_leaf');
		$q->addTable('project_ear_items', 't');
		$q->addWhere("project_id = $projectId and is_leaf='1' order by sort_order");
		$sql = $q->prepare();
		$items = db_loadList($sql);
		foreach ($items as $item) {
			$id = $item['id'];
			$name = $item['item_name'];
			$identation= $item['identation'];
			$number = $item['number'];
			$is_leaf= $item['is_leaf']; 
			$WBSItem= new WBSItem();
			$WBSItem->load($id,$name,$identation,$number,$is_leaf);
			$list[$id]=$WBSItem;
		}
		return $list;
	}
	
	function getWBSItemByTask($task_id){
		$q = new DBQuery();
		$q->addQuery('t.id, t.item_name,t.identation,t.number,t.is_leaf');
		$q->addTable('project_ear_items', 't');
		$q->addTable('tasks_workpackages', 'tw');
		$q->addWhere("tw.task_id=$task_id and t.id= tw.eap_item_id order by sort_order");
		$sql = $q->prepare();
		$items = db_loadList($sql);
		$activities=array();
		$WBSItem= new WBSItem();
		foreach ($items as $item) {
			$id = $item['id'];
			$name = $item['item_name'];
			$identation= $item['identation'];
			$number = $item['number'];
			$is_leaf= $item['is_leaf']; 
			$WBSItem->load($id,$name,$identation,$number,$is_leaf);
		}
		return $WBSItem;
	}
	
}

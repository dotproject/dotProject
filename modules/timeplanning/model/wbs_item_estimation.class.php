<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

class WBSItemEstimation {
	
	private $id=NULL; 
    private $size=NULL; 
    private $sizeUnit=NULL; 
	
	public function getId(){
		return $this->id;
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function getSizeUnit(){
		return $this->sizeUnit;
	}
	
	function WBSItemEstimation (){
	}
	
	function store($idValue,$sizeValue,$sizeUnitValue) {
            $q = new DBQuery();
            $q->addQuery('t.id');
            $q->addTable('eap_item_estimations', 't');
            $q->addWhere('t.eap_item_id= '.$idValue);
            $sql = $q->prepare();
            $eapItemsEstimations = db_loadList($sql);
            $q = new DBQuery();
            $q->addTable('eap_item_estimations');
            if(sizeof($eapItemsEstimations)>0){
                $q->addUpdate('size', $sizeValue);
                $q->addUpdate('size_unit', $sizeUnitValue);
                $q->addWhere('eap_item_id =' . $idValue);
            }else{
                $q->addInsert('size', $sizeValue);
                $q->addInsert('size_unit', $sizeUnitValue);
                $q->addInsert('eap_item_id', $idValue);
            }
            $q->exec();
	}
	
	function load($idValue) {
            $q = new DBQuery();
            $q->addQuery('t.eap_item_id,t.size,t.size_unit');
            $q->addTable('eap_item_estimations', 't');
            $q->addWhere('t.eap_item_id= '.$idValue);
            $sql = $q->prepare();
            $records = db_loadList($sql);
            foreach($records as $record){
                    $this->id=$record['eap_item_id'];
                    $this->size=$record['size'];
                    $this->sizeUnit=$record['size_unit'];
            }
		
	}
	
}

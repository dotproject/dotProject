<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/model/respons.class.php");
/*
DB filelds - TABLE: 'monitoring_responsibility_matrix'
 ' responsibility_id, responsibility_description, responsibility_user_id_consultation, responsibility_user_id_execut, responsibility_user_id_support, responsibility_user_id_approve,  project_id '
*/

class ControllerRespons{

	function insert($index,$description,$consultation,$execut,$support,$approve,$project_id){
		$q = new DBQuery();
		for($i=0;$i<count($index);$i++){
		$q-> addTable('monitoring_responsibility_matriz');
		$q->addInsert('responsibility_description', $description[$i]);
		$q->addInsert('responsibility_user_id_consultation', $consultation[$i]);
		$q->addInsert('responsibility_user_id_execut', $execut[$i]);
		$q->addInsert('responsibility_user_id_support', $support[$i]);
		$q->addInsert('responsibility_user_id_approve', $approve[$i]);
		$q->addInsert('project_id', $project_id);
		$q->exec();
		}
	}
	
	function deleteRow($id){
		$q = new DBQuery();
		$q->setDelete('monitoring_responsibility_matriz');
		$q->addWhere('responsibility_id=' . $id);
		$q->exec();
		$q->clear();	
	}
	
	function  updateRecords($index,$description,$consultation,$execut,$support,$approve,$project_id,$id){	
			$q = new DBQuery();	
		for($j=0;$j<count($index);$j++){
			$q-> addTable('monitoring_responsibility_matriz');
			$q->addUpdate('responsibility_description', $description[$j]);
			$q->addUpdate('responsibility_user_id_consultation', $consultation[$j]);
			$q->addUpdate('responsibility_user_id_execut', $execut[$j]);
			$q->addUpdate('responsibility_user_id_support', $support[$j]);
			$q->addUpdate('responsibility_user_id_approve', $approve[$j]);
			$q->addUpdate('project_id', $project_id);
			$q->addWhere('responsibility_id = ' . $id);
			$q->exec();	
		}
	}	

}
?>
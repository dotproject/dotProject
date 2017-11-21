<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
/*
DB filelds - TABLE: 'monitoring_user_cost' :
'cost_id, user_id, cost_value, cost_per_use, cost_dt_begin, cost_dt_end'
*/
class ControllerResourcesCosts{
	
	/* Verifica se os dados são válidos e efetua a inserção  */
	
	function checkInsert($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id){
			global $AppUI;	
			$resCost = new ControllerResourcesCosts(); 			
			$dt_begin=  $resCost -> convert_to_datetime($dt_begin);
			$dt_end =  $resCost -> convert_to_datetime($dt_end);
			$resCost -> insertCosts($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id);
	}  
	
	/* Verifica se os dados são válidos e efetua a alteração  */
	function checkUpdate($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id,$id){
			global $AppUI;	
			$controllerUtil = new ControllerUtil(); 
			$controllerResourceCosts = new ControllerResourcesCosts();
			$data_i =  $controllerUtil->date_to_hours($dt_begin);				// convert date to hours
			$data_f =  $controllerUtil->date_to_hours($dt_end);				
			if($dt_end != null || $dt_end !=""){    
				if($data_i > $data_f){   // compara 
					$msg = "<script> alert('A data Início Vigencia deve ser inferior a data Fim Vigencia.');</script>";
					$AppUI ->redirect('m=admin&a=viewuser&user_id='.$user_id);
				}
			}
			
			$pos = $controllerResourceCosts ->getPosition($id,$user_id);		// get inserted row position on table
			$prevPos = $pos-1;
			$nextPos = $pos+1;			
			$rows = $controllerUtil -> getRecordsByUser($user_id);	
			$dt_endPrev = $rows[$prevPos][5];		// get previous dt_end
			$dt_iniNext = $rows[$nextPos][4];		// get next dt_begin
				
			if(!empty($dt_endPrev )){
				$dt_endPrev =  $controllerUtil -> formatDate($dt_endPrev);
				$dt_endPrev =  $controllerUtil-> date_to_hours($dt_endPrev);			
					if($data_i < $dt_endPrev){  
						$msg = "<script> alert('Alteração inválida.A data Início de Vigencia atual deve ser posterior a data Fim de Vigencia anterior.');</script>";
						$AppUI ->redirect('m=admin&a=viewuser&user_id='.$user_id.'&msg='.$msg);
					}					
			}
		
			if(!empty($dt_iniNext)){
				$dt_iniNext =  $controllerUtil -> formatDate($dt_iniNext);
				$dt_iniNext =  $controllerUtil-> date_to_hours($dt_iniNext);
					if($data_f > $dt_iniNext){   
						$msg = "<script>alert('Alteração inválida.A data Fim de Vigencia atual deve ser anterior ao próximo Início de Vigencia .');</script>";
						$AppUI ->redirect('m=admin&a=viewuser&user_id='.$user_id.'&msg='.$msg);
					}			
			}
			
			$resCost -> updateRow($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id, $id);

	}

	function insertCosts($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id){
			$controllerUtil = new ControllerUtil(); 			
			$dt_begin=  $controllerUtil -> convert_to_datetime($dt_begin);
			$dt_end =  $controllerUtil -> convert_to_datetime($dt_end);
			$q = new DBQuery();
			$q-> addTable('monitoring_user_cost');
			$q->addInsert('user_id', $user_id);
			$q->addInsert('cost_value', $tx_pad);
			$q->addInsert('cost_per_use', $use_cost);
			$q->addInsert('cost_dt_begin', $dt_begin);
			$q->addInsert('cost_dt_end', $dt_end);
			$q->exec();
			
	}
	
	function deleteRow($id){
			$q = new DBQuery();
			$q->setDelete('monitoring_user_cost');
			$q->addWhere('cost_id=' . $id);
			$q->exec();
			$q->clear();	
	}	
	

	function updateRow($tx_pad,$use_cost,$dt_begin,$dt_end,$user_id, $id){	
			$controllerUtil = new ControllerUtil();  
			$dt_begin=  $controllerUtil -> convert_to_datetime($dt_begin);
			$dt_end =  $controllerUtil -> convert_to_datetime($dt_end);
			$q = new DBQuery();	
			$q-> addTable('monitoring_user_cost');
			$q->addUpdate('cost_dt_begin', $dt_begin);
			$q->addUpdate('cost_dt_end', $dt_end);
			$q->addUpdate('cost_value', $tx_pad);
			$q->addUpdate('cost_per_use', $use_cost);
			$q->addUpdate('user_id', $user_id);
			$q->addWhere('cost_id = ' . $id);
			$q->exec();	
		}

	function getPosition($id,$user_id){
			$list = array();
		    $array_id = array();			
			$q = new DBQuery();	
			$q-> addTable('monitoring_user_cost');
			$q-> addQuery('cost_id');
			$q->addWhere('user_id =' .$user_id);	
			$q->addOrder('cost_id ASC');
			$sql = $q->prepare();
			$list = db_loadList($sql);	
			$i=0;
			foreach($list as $list_id){
				$array_id[$i] = $list_id[0];
				$i++;				
			}
			$pos = array_search($id, $array_id );
			
			return $pos ;	
	}
	
	function isValidDtBegin($dt_begin,$user_id){		
			global $AppUI;	
			$controllerUtil = new ControllerAdminViewUserCustos(); 
			$result = false ;
			$data_i =  $controllerUtil->date_to_hours($dt_begin);				
			$list_dtEnd = $controllerUtil->getListDtEnd($user_id);	
			if(!empty($list_dtEnd)){
				if($list_dtEnd[0][0] != null){
						$prev_dt = $list_dtEnd[0][0];											
						$prev_dt =  $controllerUtil-> formatDate($prev_dt);																											
						$prev_dt =  $controllerUtil-> date_to_hours($prev_dt);
							if($prev_dt >$data_i){
								$msg = "<script> alert('A data início de vigência deve ser posterior ao fim da vigência anterior.');</script>";
								$AppUI ->redirect('m=admin&a=viewuser&user_id='.$user_id.'&msg='.$msg);
							}else
								$result = true ;
				}
			}
			return $result ;
	}
	
	function getResourcesCosts(){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('cost_id, user_id, cost_value, cost_per_use, cost_dt_begin, cost_dt_end');
		$q->addTable('monitoring_user_cost', 'c');	
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}	
	
	function getListById($id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('cost_id, user_id, cost_value, cost_per_use, cost_dt_begin, cost_dt_end');
		$q->addTable('monitoring_user_cost', 'c');
		$q->addWhere('cost_id =' .$id);	
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}
	
	function getRecordsByUser($user_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('cost_id, user_id, cost_value, cost_per_use, cost_dt_begin, cost_dt_end');
		$q->addTable('monitoring_user_cost', 'c');
		$q->addWhere('user_id =' .$user_id);	
		$q->addOrder('cost_id ASC');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}
		
/* retorna as datas de fim de vigencia de um usuario  */
	function getListDt($user_id, $id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('cost_dt_begin, cost_dt_end');
		$q->addTable('monitoring_user_cost', 'c');
		$q->addWhere('user_id =' .$user_id);
		
		if (isset($id)){
			$q->addWhere('cost_id <>' .$id);
		}
				
		$q->addOrder('cost_id DESC');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;			
	}		
	
 }
?>
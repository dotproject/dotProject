<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");


	class ControllerQuality{
		
	function insert($typpe,$description,$responsable,$status,$date_end,$task_id){
		$controllerUtil = new ControllerUtil();
		$date_end=  $controllerUtil -> convert_to_datetime($date_end);
		$q = new DBQuery();
		$q-> addTable('monitoring_quality');
		$q->addInsert('quality_type_id', $typpe);
		$q->addInsert('quality_description', $description);
		//$q->addInsert('user_id', $responsable);
		$q->addInsert('quality_status_id', $status);
		$q->addInsert('quality_date_end', $date_end);
		$q->addInsert('task_id', $task_id);
		$q->exec();
	}
	
	function deleteRow($id){
		$q = new DBQuery();
		$q->setDelete('monitoring_quality');
		$q->addWhere('quality_id=' . $id);
		$q->exec();
		$q->clear();	
	}	
	
	function  updateRecords($typpe,$description,$responsable,$status,$date_end,$task_id,$id){
		$controllerUtil = new ControllerUtil();
		$date_end=  $controllerUtil -> convert_to_datetime($date_end);
		$q = new DBQuery();	
		$q-> addTable('monitoring_quality');
		$q->addUpdate('quality_type_id', $typpe);
		$q->addUpdate('quality_description', $description);
		//$q->addUpdate('user_id', $responsable);
		$q->addUpdate('quality_status_id', $status);
		$q->addUpdate('quality_date_end', $date_end);
		$q->addUpdate('task_id', $task_id);
		$q->addWhere('quality_id = ' . $id);
		$q->exec();	
	}
	
	function  obterQuantidadeTarefaComErro($project_id, $user){
	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('distinct qt.quality_type_id, qt.quality_type_name');
		$q->addTable('monitoring_quality_type', 'qt');
		$sql = $q->prepare();
		$list = db_loadList($sql);
		$indice = 0;

		foreach($list as $row){
			$qAux = new DBQuery;
			$qAux->addQuery(' DISTINCT q.task_id as tarefa');
			$qAux->addTable('monitoring_quality', 'q');
			$qAux->innerJoin('tasks', 't', 't.task_id = q.task_id');
			
			$qAux->addWhere('t.task_project ='.$project_id);
			$qAux->addWhere('q.quality_status_id <> 3');//Cancelado
			$qAux->addWhere('q.quality_type_id='.$row[quality_type_id]);
			
			if(isset($user) && $user > 0) {
				$qAux->innerJoin('user_tasks', 'ut', 't.task_id = ut.task_id');
				$qAux->addWhere('ut.user_id='.$user);
			}
			
			$sql = $qAux->prepare();
			$listAux = db_loadList($sql);
		
			if (count($listAux) > 0) {
				$arQualidadeBarComErro[$indice][quantity] = count($listAux);
				$arQualidadeBarComErro[$indice][name] = $row[quality_type_name];
				$indice += 1;			
			}	
		}	
	
		return $arQualidadeBarComErro;
	}
	
	function  obterQuantidadeTarefaComErroPorTipo($project_id, $idTipoErro, $user){
		$list = array();
		$q = new DBQuery;
		$q->addQuery('COUNT(t.task_id) as quantity');
		$q->addQuery('qt.quality_type_name as name');
		$q->addQuery('concat(year(t.task_start_date), month(t.task_start_date)) as date');
		
		$q->addTable('monitoring_quality', 'q');
		
		$q->innerJoin('tasks', 't', 't.task_id = q.task_id');
		$q->innerJoin('monitoring_quality_type', 'qt', 'qt.quality_type_id = q.quality_type_id');
		
		if(isset($user) && $user > 0) {
			$q->innerJoin('user_tasks', 'ut', 't.task_id = ut.task_id');
			$q->addWhere('ut.user_id='.$user);
		}		
		
		$q->addWhere('t.task_project ='.$project_id);
		$q->addWhere('q.quality_status_id <> 3');//Cancelado
		$q->addWhere('q.quality_type_id ='.$idTipoErro);
		
		$q->addGroup('qt.quality_type_name');
		$q->addGroup('year(t.task_start_date)');
		$q->addGroup('month(t.task_start_date)');
		
		$q->addOrder('year(t.task_start_date)');
		$q->addOrder('month(t.task_start_date)');		
		$q->addOrder('qt.quality_type_name');			
			
		$sql = $q->prepare();
		$list = db_loadList($sql);
		return $list;
	}	
		
	function  obterQuantidadeTarefaSemErro($project_id, $user, $flIncluirQuebra){
		$list = array();
		$q = new DBQuery;
		$q->addQuery('COUNT(t.task_id) as quantity');
		$q->addTable('tasks', 't');
		$q->addWhere('t.task_project ='.$project_id);

		if(isset($user) && $user > 0) {
			$q->innerJoin('user_tasks', 'ut', 't.task_id = ut.task_id');
			$q->addWhere('ut.user_id='.$user);
		}

		$qSub = new DBQuery;
		$qSub->addQuery('q.task_id');
		$qSub->addTable('monitoring_quality', 'q');
		$qSub->addWhere('q.quality_status_id <> 3');//Cancelado

		$q->addWhere('t.task_id not in ('. $qSub->prepareSelect() .')');

		if($flIncluirQuebra){
			$q->addQuery('concat(year(t.task_start_date), month(t.task_start_date)) as date');
			$q->addGroup('year(t.task_start_date)');
			$q->addGroup('month(t.task_start_date)');
			$q->addOrder('year(t.task_start_date)');
			$q->addOrder('month(t.task_start_date)');
		}

		$sql = $q->prepare();
		$list = db_loadList($sql);
		 
		return $list;
	}	

	function  obterUsuarioDeTarefa($project_id){
		$list = array();
		$q = new DBQuery;
		$q->addQuery('distinct u.user_id, u.user_username');
		$q->addTable('tasks', 't');
		$q->innerJoin('user_tasks', 'ut', 't.task_id = ut.task_id');
		$q->innerJoin('users', 'u', 'u.user_id = ut.user_id');
		$q->addWhere('t.task_project ='.$project_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);
		 
		return $list;
	}
	
	function  obterDataTarefa($project_id){
		$list = array();
		$q = new DBQuery;
		$q->addQuery('distinct concat(year(t.task_start_date), month(t.task_start_date)) as date');
		$q->addQuery('         month(t.task_start_date) as month');
		$q->addQuery('         year(t.task_start_date) as year');
		$q->addTable('tasks', 't');
		$q->addWhere('t.task_project ='.$project_id);
		$sql = $q->prepare();
		$list = db_loadList($sql);
		 
		return $list;
	}		

	function  obterDadosGraficoPizza($project_id, $user){
		$qualityControl = new ControllerQuality();
	
		$arQualidadePie =  array();		
		$arQualidadePieSemErro = $qualityControl->obterQuantidadeTarefaSemErro($project_id, $user, false);
		
		if ($arQualidadePieSemErro[0]['quantity'] > 0){
			$arQualidadePieSemErro[0]['name'] = 'Sem Erros';
			$arQualidadePie = array_merge($arQualidadePie, $arQualidadePieSemErro);		
		}

	    $arQualidadePieComErro = $qualityControl->obterQuantidadeTarefaComErro($project_id, $user);
		if (count($arQualidadePieComErro) > 0){
			$arQualidadePie = array_merge($arQualidadePie, $arQualidadePieComErro);
		}			
		
		if (count($arQualidadePie) == 0) {
			$arQualidadePie[0]['name'] = 'Sem Informação';	
			$arQualidadePie[0]['quantity'] = 1;   
		}
		
		return $arQualidadePie ;
	}
	
	function  obterDadosGraficoBarra($project_id, $user){
		$list = array();
		$q = new DBQuery;
		$q->addQuery('distinct qt.quality_type_id, qt.quality_type_name');
		$q->addTable('monitoring_quality_type', 'qt');
		$sql = $q->prepare();
		$list = db_loadList($sql);

		$qualityControl = new ControllerQuality();
		$indice = 0;
		foreach($list as $row){
			$arQualidadeBarComErro[$indice][name] = $row[quality_type_name];
			$arQualidadeBarComErro[$indice][index] = $row[quality_type_id];
			$arQualidadeBarComErro[$indice][quantity] = $qualityControl->obterQuantidadeTarefaComErroPorTipo($project_id, $row[quality_type_id], $user);	
			$indice += 1; 	
		}			 
		$arQualidadeBarSemErro = $qualityControl->obterQuantidadeTarefaSemErro($project_id, $user,  true);
		$arQualidadeBarTotal = $qualityControl->obterDataTarefa($project_id);
		for($i=0; $i < count($arQualidadeBarTotal); ++$i) {
		    $date = $arQualidadeBarTotal[$i]['date'];
			$dataSemErro[$i] = $qualityControl->buscaValorRefencia($arQualidadeBarSemErro, $date);
			
			for($j=0; $j < $indice; ++$j) {
				$dataComErro[$j][$i] = $qualityControl->buscaValorRefencia($arQualidadeBarComErro[$j][quantity], $date);	
			}				
		}
	
		$arQualidadeBar = array();
		
		$arQualidadeBar[0]['name'] = 'Sem Erro';
		$arQualidadeBar[0]['quantity'] = $dataSemErro;		
		for($j=0; $j < $indice; ++$j) {
		
			$arQualidadeBar[$j+1]['name'] = $arQualidadeBarComErro[$j][name];
			$arQualidadeBar[$j+1]['quantity'] = $dataComErro[$j];	

		}
		 
		return $arQualidadeBar;
	}	

						 
    function buscaValorRefencia($lstBanco, $dateParam) {
		$retorno = 0;
	   
		for($i=0; $i < count($lstBanco); ++$i) {
		  if ($lstBanco[$i]['date'] == $dateParam){
			 $retorno = $lstBanco[$i]['quantity'];
		  }
		}
	
		return $retorno;
	}

///////////////////////////////////////////////////////////////////////////////////

		function getQualityRecords($task_id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery( ' quality_id, quality_type_id, quality_description, user_id ,quality_status_id, quality_date_end, task_id  ');
		$q->addTable('monitoring_quality', 'q');	
		$q->addWhere('task_id='.$task_id);
		$q-> addOrder('quality_id');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
	}	

	function getTypeByIndex($index){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('quality_type_id');
		$q->addQuery('quality_type_name');
		$q->addTable('monitoring_quality_type', 'q');	
		$q->addOrder('order by quality_type_id DESC');		
		$q->setLimit($index, $start = -1);
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;
	}
	
	function getType(){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('quality_type_id');
		$q->addQuery('quality_type_name');
		$q->addTable('monitoring_quality_type', 'q');	
		$q -> addOrder('quality_type_id');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;
	}
	
	function getStatus(){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('quality_status_id');
		$q->addQuery('quality_status_name');
		$q->addTable('monitoring_quality_status', 's');	
		$q -> addOrder('quality_status_id');
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;
	}

		function getStatusName($num){		
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('quality_status_name');
		$q->addTable('monitoring_quality_status','s');	
		$q-> addWhere('quality_status_id='. $num);					
		$sql = $q->prepare();
		$list = db_loadList($sql);	
		$statusname='';
			if(!empty($list)){
				$statusname = $list[0][quality_status_name];
			}					
		return $statusname;	
	}	


		function getTypeName($num){		
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery('quality_type_name');
		$q->addTable('monitoring_quality_type','t');	
		$q-> addWhere('quality_type_id='. $num);					
		$sql = $q->prepare();
		$list = db_loadList($sql);	
		$typename='';
			if(!empty($list)){
				$typename = $list[0][quality_type_name];
			}					
		return $typename;	
	}	
	
	function getQualityRecordsById($id){
		global $AppUI;	
		$list = array();
		$q = new DBQuery;
		$q->addQuery( ' quality_id, quality_type_id, quality_description, user_id ,quality_status_id, quality_date_end, task_id  ');
		$q->addTable('monitoring_quality', 'q');	
		$q->addWhere('quality_id='.$id);
		$sql = $q->prepare();
		$list = db_loadList($sql);		
		return $list;	
		
	}
}
?>
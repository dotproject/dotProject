<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_earn_value.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_baseline.class.php");

	class ControllerReport{
		
	function obterDadosRelatorioGerenciaSenior($company){
		$q = new DBQuery;
		$q->addQuery('p.project_name, p.project_id ');
		$q->addTable('projects', 'p');
		$q->addWhere('p.project_company ='.$company);
		
		$sql = $q->prepare();
		$list = db_loadList($sql);	
		$result = array();
		$percentual = 0;
		$tamanho = 0;
		$projeto = '';
		$idc = 0;
		$idp = 0;
		$baseline = 0;
		$dtAtual = date('d/m/Y');
		$controllerEarnValue = new ControllerEarnValue();    
		$controllerBaseline= new ControllerBaseline();
		
		foreach($list as $row){		
			$linha = array();
	
			$linha[percentual] = $controllerEarnValue->obterPercentualTotal($row['project_id']);
			$linha[tamanho] = $controllerEarnValue->obterTamanhoTotal($row['project_id']);
			$linha[id] = $row['project_id'];
			$linha[projeto] = $row['project_name'];
			$linha[idc] = $controllerEarnValue->obterAtualIndiceDesempenhoCusto($row['project_id'], $dtAtual);
			$linha[idp] = $controllerEarnValue->obterAtualIndiceDesempenhoPrazo($row['project_id'], $dtAtual);
			$linha[baseline] = $controllerBaseline->countBaseline($row['project_id']);
			
			$linha[cr] = $controllerEarnValue->obterAtualValorReal($row['project_id'], $dtAtual);
			$linha[vp] = $controllerEarnValue->obterAtualValorPlanejado($row['project_id'], $dtAtual);
			$linha[va] = $controllerEarnValue->obterAtualValorAgregado($row['project_id'], $dtAtual);
		
			array_push($result, $linha);
		}		
		return $result;
	}	
	
	function getProjectCompany($project_id){
		$q = new DBQuery;
		$q->addQuery('p.project_company ');
		$q->addTable('projects', 'p');
		$q->addWhere('p.project_id ='.$project_id);		
		$sql = $q->prepare();
		$list = db_loadList($sql);	
		return $list;		
	}
}
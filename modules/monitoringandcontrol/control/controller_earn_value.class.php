<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}
require_once(DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
/*
  VA - Valor Agregado
  VP - Valor Planejado
  CR - Custo Real
  IDC - Indice de Desempenho de Custo
  IDP - Indice de Desempenho de Prazo
  VP - Variação do Prazo
  VC - Variação do Custo
 */

class ControllerEarnValue {

    function obterValorPlanejado($project_id, $date, $baseline) {
        global $AppUI;
        $result = 0;
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineValorPlanejado($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualValorPlanejado($project_id, $date);
        }
        return $result;
    }

    function obterValorReal($project_id, $date, $baseline) {
        global $AppUI;
        $result = 0;
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineValorReal($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualValorReal($project_id, $date);
        }
        return $result;
    }

    function obterValorAgregado($project_id, $date, $baseline) {
        global $AppUI;
        $result = 0;
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineValorAgregado($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualValorAgregado($project_id, $date);
        }
        return $result;
    }

    function obterVariacaoCusto($project_id, $date, $baseline) {
        global $AppUI;
        $result = 0;
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineVariacaoCusto($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualVariacaoCusto($project_id, $date);
        }
        return $result;
    }

    function obterVariacaoPrazo($project_id, $date, $baseline) {
        global $AppUI;
        $result = 0;
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineVariacaoPrazo($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualVariacaoPrazo($project_id, $date);
        }
        return $result;
    }

    function obterIndiceDesempenhoCusto($project_id, $date, $baseline) {
        global $AppUI;
        $result = 0;
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineIndiceDesempenhoCusto($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualIndiceDesempenhoCusto($project_id, $date);
        }
        return $result;
    }

    function obterIndiceDesempenhoPrazo($project_id, $date, $baseline) {
        global $AppUI;
        $result = "";
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineIndiceDesempenhoPrazo($baseline, $date);
        } else {
            $result = $controllerEarnValue->obterAtualIndiceDesempenhoPrazo($project_id, $date);
        }
        return $result;
    }

    function obterInicioPeriodo($project_id) {
        global $AppUI;
        $result = "";
        $controllerEarnValue = new ControllerEarnValue();
        if (isset($baseline) && $baseline != 0) {
            $result = $controllerEarnValue->obterBaselineInicioPeriodo($baseline);
        } else {
            $result = $controllerEarnValue->obterAtualInicioPeriodo($project_id);
        }
        return $result;
    }

    //Atual
    function obterAtualValorPlanejado($project_id, $date) {
        global $AppUI;
        $controllerUtil = new ControllerUtil();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        $list = array();
        $q = new DBQuery();
        $q->addTable('tasks', 't');
        $q->addQuery('distinct t.task_duration, c.cost_value, t.task_start_date,  t.task_end_date, t.task_id');
        $q->innerJoin('user_tasks', 'u', 't.task_id = u.task_id');
        $q->innerJoin('monitoring_user_cost', 'c', 'c.user_id=u.user_id');
        $q->addWhere(' t.task_start_date <= "' . $date . '" ');
        $q->addWhere(' t.task_start_date between cost_dt_begin and cost_dt_end ');
        $q->addWhere(' t.task_project =' . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = 0;

        $controllerEarnValue = new ControllerEarnValue();
        foreach ($list as $row) {
            $vp = ($row[task_duration] * $row[cost_value]) * $controllerEarnValue->calculaPercentualAvanco($row[task_start_date], $row[task_end_date], $date);
            $result+= $vp;
        }
        return $result;
    }

    /**
     * Obtem o apontamento de horas realizadas pelos membros da equipe equipe.
     * Multiplica cada apontamento de horas pelo respectivo custo do recurso humano
     * O valor é o total do início do projeto até uma data específica. 
     * 
     * Também faz o somatório de todos os registros de despesas até a data parametrizada.
     * 
     * Soma os totais com RH e com RNH.
     * @global type $AppUI
     * @param type $project_id
     * @param type $date
     * @return type: valor absoluto com o montante total do projeto.
     */
    function obterAtualValorReal($project_id, $date) {
        global $AppUI;
        $controllerUtil = new ControllerUtil();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        //get costs with RH
        $list = array();
        $q = new DBQuery();
        $q->addQuery('tl.task_log_hours, c.cost_value');
        $q->addTable('task_log', 'tl');
        $q->innerJoin('tasks', 't', 't.task_id = tl.task_log_task');
        $q->innerJoin('user_tasks', 'u', 'tl.task_log_task = u.task_id');
        $q->innerJoin('monitoring_user_cost', 'c', 'c.user_id=u.user_id');
        $q->addWhere(' tl.task_log_date <= " ' . $date . ' " ');
        $q->addWhere(' tl.task_log_date between cost_dt_begin and cost_dt_end');
        $q->addWhere(' t.task_project =' . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = 0;
        foreach ($list as $row) {
            $vp = $row[0] * $row[1];
            $result+= $vp;
        }
        //get costs with NHR
        $q = new DBQuery();
        $q->addQuery("sum(value)");
        $q->addTable("acquisition_execution");
        $q->addWhere("date <='$date'");
        $q->addWhere("project_id = $project_id");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $totalNHR = 0;
        foreach ($list as $row) {
            $totalNHR = $row[0];
        }
        return $result + $totalNHR;
    }

    /**
     * Get the costs with a HR in a certain month
     * @param type $project_id
     * @param type $month
     * @param type $year
     * @param type $user_id
     * @return type
     */
    
    function getCostsByHR($project_id, $month,$year,$user_id) {
        $controllerUtil = new ControllerUtil();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        //get costs with RH
        $q = new DBQuery();
        $q->addQuery("tl.task_log_hours, c.cost_value");
        $q->addTable("task_log", "tl");
        $q->innerJoin("tasks", "t", "t.task_id = tl.task_log_task");
        $q->innerJoin("user_tasks", "u", "tl.task_log_task = u.task_id");
        $q->innerJoin("monitoring_user_cost", "c", "c.user_id=u.user_id");
        $q->addWhere(" month(tl.task_log_date) =  $month   and year(tl.task_log_date)=$year");
        $q->addWhere(" tl.task_log_creator =  $user_id  ");
        $q->addWhere(" tl.task_log_date between cost_dt_begin and cost_dt_end");
        $q->addWhere(" t.task_project =" . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = 0;
        foreach ($list as $row) {
            $vp = $row[0] * $row[1];
            $result+= $vp;
        }
        return $result;
    }
    
     function getCostsByNHR($project_id, $month,$year,$resource_id) {
         //get costs with NHR
        $q = new DBQuery();
        $q->addQuery("sum(value)");
        $q->addTable("acquisition_execution");
        $q->addWhere("month(date) =  $month   and year(date)=$year");
        $q->addWhere("project_id = $project_id and reference_id=$resource_id and is_risk_contingency!=1");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $totalNHR = 0;
        foreach ($list as $row) {
            $totalNHR = $row[0];
        }
        return $totalNHR;
    }
    
    function getCostsNonPlanned($project_id, $month,$year) {
         //get costs with NHR
        $q = new DBQuery();
        $q->addQuery("sum(value)");
        $q->addTable("acquisition_execution");
        $q->addWhere("month(date) =  $month   and year(date)=$year");
        $q->addWhere("project_id = $project_id and reference_id=0");
        $sql = $q->prepare();
        //echo $sql;
        $list = db_loadList($sql);
        $totalNHR = 0;
        foreach ($list as $row) {
            $totalNHR = $row[0];
        }
        return $totalNHR;
    }
    
     function getCostsByContingency($project_id, $month,$year,$resource_id) {
         //get costs with NHR
        $q = new DBQuery();
        $q->addQuery("sum(value)");
        $q->addTable("acquisition_execution");
        $q->addWhere("month(date) =  $month   and year(date)=$year");
        $q->addWhere("project_id = $project_id and reference_id=$resource_id and is_risk_contingency=1");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $totalNHR = 0;
        foreach ($list as $row) {
            $totalNHR = $row[0];
        }
        return $totalNHR;
    }
    
    //Obtem o percentual de completude de todas as tarefas que tiveram o seu desenvolvimento iniciado
    /**
     * Multiplica a duração da atividade pelo custo hora do recurso humano e pelo percentual de completude
     * @global type $AppUI
     * @param type $project_id
     * @param type $date
     * @return type
     */
    function obterAtualValorAgregado($project_id, $date) {
        global $AppUI;
        $controllerUtil = new ControllerUtil();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        $list = array();
        $q = new DBQuery;
        $q->addTable('tasks', 't');
        $q->addQuery('distinct t.task_duration, c.cost_value, t.task_percent_complete, t.task_id,t.task_duration_type ');
        $q->innerJoin('user_tasks', 'u', 't.task_id = u.task_id');
        $q->innerJoin('monitoring_user_cost', 'c', 'c.user_id=u.user_id');
        $q->innerJoin('task_log', 'tl', 't.task_id = tl.task_log_task');
        $q->addWhere(' tl.task_log_date <= "' . $date . '" ');
        $q->addWhere(' t.task_start_date between cost_dt_begin and cost_dt_end ');

        $q->addWhere(' t.task_project =' . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = 0;
        foreach ($list as $row) {
            if ($row[2] > 0) {
                $duration = $row[0]; //consider the duration was estimated in hours
                if ($row[4] == 24) { //verify if its duration is estimated in days, if yes, then multiply by daily working hours
                    $daily_working_hours = dPgetConfig("daily_working_hours");
                    if (!is_numeric($daily_working_hours)) {
                        $daily_working_hours = 8; //8 by default in case no configuration
                    }
                    $duration = $daily_working_hours * $duration;
                }
                $va = $duration * $row[1] * ($row[2] / 100);
                $result+= $va;
            }
        }
        //get costs with NHR and Contingency
        $q = new DBQuery();
        $q->addQuery("sum(value)");
        $q->addTable("acquisition_execution");
        $q->addWhere("date <='$date'");
        $q->addWhere("project_id = $project_id and is_delivered=1");
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $totalNHR = 0;
        foreach ($list as $row) {
            $totalNHR = $row[0];
        }
        return $result + $totalNHR;
    }

    function obterAtualVariacaoCusto($project_id, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterAtualValorAgregado($project_id, $date);
        $vr = $controllerEarnValue->obterAtualValorReal($project_id, $date);
        $variacaoCusto = $va - $vr;
        return $variacaoCusto;
    }

    function obterAtualVariacaoPrazo($project_id, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterAtualValorAgregado($project_id, $date);
        $vp = $controllerEarnValue->obterAtualValorPlanejado($project_id, $date);
        $variacaoPrazo = $va - $vp;
        return $variacaoPrazo;
    }

    function obterAtualIndiceDesempenhoCusto($project_id, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterAtualValorAgregado($project_id, $date);
        $vr = $controllerEarnValue->obterAtualValorReal($project_id, $date);
        $indiceDesempenhoCusto = "0";
        if ($vr != 0) {
            $indiceDesempenhoCusto = $va / $vr;
        }
        return $indiceDesempenhoCusto;
    }

    function obterAtualIndiceDesempenhoPrazo($project_id, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterAtualValorAgregado($project_id, $date);
        $vp = $controllerEarnValue->obterAtualValorPlanejado($project_id, $date);
        $indiceDesempenhoPrazo = "0";

        if ($vp != 0) {
            $indiceDesempenhoPrazo = $va / $vp;
        }
        return $indiceDesempenhoPrazo;
    }

    function obterPercentualTotal($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addTable('tasks', 't');
        $q->addQuery('task_duration, task_percent_complete');
        $q->addWhere(' t.task_project =' . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);

        $duracaoTotal = 0;
        foreach ($list as $row) {
            $duracaoTotal += $row[task_duration];
            $duracaoCalculada += $row[task_duration] * $row[task_percent_complete] / 100;
        }
        if ($duracaoTotal == 0) {
            return 0;
        } else {
            return $duracaoCalculada * 100 / $duracaoTotal;
        }
    }

    function obterTamanhoTotal($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addTable('tasks', 't');
        $q->addQuery('task_duration');
        $q->addWhere('t.task_project =' . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $duracaoTotal = 0;
        foreach ($list as $row) {
            $duracaoTotal += $row[task_duration];
        }
        return $duracaoTotal;
    }

    function obterAtualInicioPeriodo($project_id) {
        $list = array();
        $q = new DBQuery;
        $q->addTable('tasks', 't');
        $q->addQuery('min(task_start_date)');
        $q->addWhere(' t.task_project =' . $project_id);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    //BASELINE
    function obterBaselineValorPlanejado($baselineId, $date) {
        global $AppUI;
        $controllerUtil = new ControllerUtil();
        $controllerEarnValue = new ControllerEarnValue();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        $list = array();
        $q = new DBQuery;
        $q->addTable('monitoring_baseline_task', 't');
        $q->addQuery('distinct t.task_duration, c.cost_value, t.task_start_date,  t.task_end_date, t.task_id');
        $q->innerJoin('user_tasks', 'u', 't.task_id = u.task_id');
        $q->innerJoin('monitoring_baseline_user_cost', 'c', 'c.user_id=u.user_id');
        $q->addWhere(' t.task_end_date <= "' . $date . '" ');
        $q->addWhere(' t.task_start_date between cost_dt_begin and cost_dt_end ');
        $q->addWhere(' t.baseline_id =' . $baselineId);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = "0";
        foreach ($list as $row) {
            $vp = ($row[task_duration] * $row[cost_value]) * $controllerEarnValue->calculaPercentualAvanco($row[task_start_date], $row[task_end_date], $date);
            $result+= $vp;
        }
        return $result;
    }

    function obterBaselineValorReal($baselineId, $date) {
        global $AppUI;
        $controllerUtil = new ControllerUtil();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        $list = array();
        $q = new DBQuery;
        $q->addQuery('tl.task_log_hours, c.cost_value ');

        $q->addTable('monitoring_baseline_task_log', 'tl');
        $q->innerJoin('monitoring_baseline_task', 't', 't.baseline_task_id = tl.baseline_task_id');
        $q->innerJoin('user_tasks', 'u', 'tl.baseline_task_id = u.task_id');
        $q->innerJoin('monitoring_baseline_user_cost', 'c', 'c.user_id=u.user_id');
        $q->addWhere(' tl.task_log_date <= "' . $date . '" ');
        $q->addWhere(' tl.task_log_date between cost_dt_begin and cost_dt_end ');
        $q->addWhere(' t.baseline_id =' . $baselineId);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = "0";
        foreach ($list as $row) {
            $vp = $row[0] * $row[1];
            $result+= $vp;
        }
        return $result;
    }

    function obterBaselineValorAgregado($baselineId, $date) {
        global $AppUI;
        $controllerUtil = new ControllerUtil();
        $date = $controllerUtil->convert_to_datetime($date . ' 23:59:59');
        $list = array();
        $q = new DBQuery;
        $q->addTable('monitoring_baseline_task', 't');
        $q->addQuery('distinct t.task_duration, c.cost_value, t.task_percent_complete ');
        $q->innerJoin('user_tasks', 'u', 't.task_id = u.task_id');
        $q->innerJoin('monitoring_baseline_user_cost', 'c', 'c.user_id=u.user_id');

        $q->innerJoin('monitoring_baseline_task_log', 'tl', 't.task_id = tl.baseline_task_id');
        $q->addWhere(' tl.task_log_date <= "' . $date . '" ');

        $q->addWhere(' t.task_start_date between cost_dt_begin and cost_dt_end ');
        $q->addWhere(' t.baseline_id =' . $baselineId);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        $result = "0";
        foreach ($list as $row) {
            if ($row[2] > 0) {
                $va = $row[0] * $row[1] * ($row[2] / 100);
                $result+= $va;
            }
        }
        return $result;
    }

    function obterBaselineVariacaoCusto($baselineId, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterBaselineValorAgregado($baselineId, $date);
        $vr = $controllerEarnValue->obterBaselineValorReal($baselineId, $date);
        $variacaoCusto = $va - $vr;
        return $variacaoCusto;
    }

    function obterBaselineVariacaoPrazo($baselineId, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterBaselineValorAgregado($baselineId, $date);
        $vp = $controllerEarnValue->obterBaselineValorPlanejado($baselineId, $date);
        $variacaoPrazo = $va - $vp;
        return $variacaoPrazo;
    }

    function obterBaselineIndiceDesempenhoCusto($baselineId, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterBaselineValorAgregado($baselineId, $date);
        $vr = $controllerEarnValue->obterBaselineValorReal($baselineId, $date);
        $indiceDesempenhoCusto = "";
        if ($vr != 0) {
            $indiceDesempenhoCusto = $va / $vr;
        }
        return $indiceDesempenhoCusto;
    }

    function obterBaselineIndiceDesempenhoPrazo($baselineId, $date) {
        global $AppUI;
        $controllerEarnValue = new ControllerEarnValue();
        $va = $controllerEarnValue->obterBaselineValorAgregado($baselineId, $date);
        $vp = $controllerEarnValue->obterBaselineValorPlanejado($baselineId, $date);
        $indiceDesempenhoPrazo = "";
        if ($vp != 0) {
            $indiceDesempenhoPrazo = $va / $vp;
        }
        return $indiceDesempenhoPrazo;
    }

    function obterBaselineInicioPeriodo($baselineId) {
        $list = array();
        $q = new DBQuery;
        $q->addTable('monitoring_baseline_task', 't');
        $q->addQuery('min(task_start_date)');
        $q->addWhere(' t.baseline_id =' . $baselineId);
        $sql = $q->prepare();
        $list = db_loadList($sql);
        return $list;
    }

    function calculaPercentualAvanco($dataInicial, $dataFinal, $dataAtual) {
        $controllerEarnValue = new ControllerEarnValue();
        $dataIni = $controllerEarnValue->geraTimestamp($dataInicial);
        $dataFim = $controllerEarnValue->geraTimestamp($dataFinal);
        $dataAtu = $controllerEarnValue->geraTimestamp($dataAtual);

        if ($dataAtu >= $dataFim) {
            return 1;
        }

        $difDataTotal = $dataIni - $dataFim;
        $difDataAtual = $dataIni - $dataAtu;

        return $difDataAtual / $difDataTotal;
    }

    function geraTimestamp($data) {
        $partes = explode('-', $data);
        return mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
    }

}
?>
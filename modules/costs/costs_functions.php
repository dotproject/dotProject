<?php

function getCostValueTotal($id) {
    $query = new DBQuery();
    $query->addTable("human_resource");
    $query->addQuery("*");
    $sql = $query->prepare();
    $query->clear();
    return db_loadList($sql);
}

function getContingencyCosts($project_id){
    $q = new DBQuery();
    $q->addQuery('*');
    $q->addTable('budget_reserve', 'b');
    $q->addWhere("budget_reserve_project_id = " . $project_id);
    $q->addOrder('budget_reserve_risk_id');
    return $q->loadList();
}

function getResources($cond, $project) {
    $q = new DBQuery();
    if ($cond == "Human") {
        $q->clear();
        $q->addQuery("*");
        $q->addTable("costs");
        $q->addWhere("cost_type_id = \"0\" $project");
        $q->addOrder("cost_description");
        $humanCost = $q->loadList();
        return $humanCost;
    } else if ($cond == "Non-Human") {
        $q->clear();
        $q->addQuery("*");
        $q->addTable("costs");
        $q->addWhere("cost_type_id = \"1\" $project");
        $q->addOrder("cost_description");
        $notHumanCost = $q->loadList();
        return $notHumanCost;
    }
}

function diasemana($data) {
    $ano = substr("$data", 0, 4);
    $mes = substr("$data", 5, -3);
    $dia = substr("$data", 8, 9);

    $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));

    switch ($diasemana) {
        case"0": $diasemana = "Domingo";
            break;
        case"1": $diasemana = "Segunda-Feira";
            break;
        case"2": $diasemana = "Terça-Feira";
            break;
        case"3": $diasemana = "Quarta-Feira";
            break;
        case"4": $diasemana = "Quinta-Feira";
            break;
        case"5": $diasemana = "Sexta-Feira";
            break;
        case"6": $diasemana = "Sábado";
            break;
    }

    echo "$diasemana";
}

function diferencaMeses($d1, $d2) {
    //forçar sempre do dia 1 do mes, pois o que importa é a diferença dos meses, e não dos dias
    $d1 = substr($d1,0,7)."-01";
    $d2 = substr($d2,0,7)."-02";//do dia 1 ao dia 2, assim sempre fecha o mês
    return diffDate($d1, $d2, "M");
}

function existsResource($projectId, $cost_human_resource_id, $cost_human_resource_role_id) {
    $q = new DBQuery();
    $q->addQuery("c.cost_id");
    $q->addTable("costs", "c");
    $q->addWhere("c.cost_project_id = " . $projectId . " and c.cost_human_resource_id =" . $cost_human_resource_id . " and c.cost_human_resource_role_id=" . $cost_human_resource_role_id);
    $res = $q->loadList();
    return count($res) > 0 ? true : false;
}

/**
 * Funtion insert and update human and non-human resources in cost table
 * @param type $project
 */
function insertCostValues($project) {
    $q = new DBQuery();
    $q->addQuery("DISTINCT usr.user_username,hs.human_resources_role_name, h.human_resource_id, hs.human_resources_role_id, usr.user_id, mc.cost_value,cts.contact_first_name,cts.contact_last_name");
    $q->addTable("tasks", "t");
    $q->innerJoin("user_tasks", "dp", "dp.task_id = t.task_id");
    $q->innerJoin("human_resource", "h", "dp.user_id = h.human_resource_user_id");
    $q->innerJoin("users", "usr", "usr.user_id = h.human_resource_user_id");
    $q->innerJoin("contacts", "cts", "usr.user_contact = cts.contact_id");
    $q->innerJoin("human_resource_roles", "hr", "hr.human_resource_id = h.human_resource_id");
    $q->innerJoin("human_resources_role", "hs", "hr.human_resources_role_id = hs.human_resources_role_id");
    $q->innerJoin("monitoring_user_cost", "mc", "usr.user_id = mc.user_id");
    $q->addWhere("t.task_project = " . $project);
    $q->addOrder("usr.user_id ASC");
    $res = $q->loadList();
    $whereProject = " and cost_project_id=" . $project;
    $humanCost = getResources("Human", $whereProject);
    $date1 = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $date2 = mktime(0, 0, 0, date("m") + 1, date("d"), date("Y"));
    if ($humanCost == null || count($humanCost) == 0) {
        /* Still there is no cost entry for human resources in this project.
         * This code will insert new records for every human resource x role alocated.
         */
        foreach ($res as $row) {
            $cost_human_resource_id = $row["human_resource_id"];
            $cost_human_resource_role_id = $row["human_resources_role_id"];
            $description = $row["contact_first_name"] . " " . $row["contact_last_name"] . " - " . $row["human_resources_role_name"];
            if (!existsResource($project, $cost_human_resource_id, $cost_human_resource_role_id)) {//condition to avoid double values
                $q->clear();
                $q->addTable("costs");
                $q->addInsert("cost_type_id", 0);
                $q->addInsert("cost_project_id", $project);
                $q->addInsert("cost_description", $description);
                $q->addInsert("cost_date_begin", date("Y-m-d H:i:s", $date1));
                $q->addInsert("cost_date_end", date("Y-m-d H:i:s", $date2));
                $q->addInsert("cost_quantity", 1);
                $q->addInsert("cost_value_unitary", $row["cost_value"]);
                $q->addInsert("cost_value_total", $row["cost_value"]);
                $q->addInsert("cost_human_resource_id", $cost_human_resource_id);
                $q->addInsert("cost_human_resource_role_id", $cost_human_resource_role_id);
                $q->exec();
            }
        }
    } else {
        /* Already there is some human resources registered in cost table.
         * This code will update the existing ones with their hour value, name and role name.
         * New human resources also will be included.
         */
        $array = array();
        $arrayName = array();
        //buffer in $array all the costs per human resource. $res is the fresh list with updated costs
        foreach ($res as $row) {
            $key = $row["human_resource_id"] . " - " . $row["human_resources_role_id"];
            $name = $row["contact_first_name"] . " " . $row["contact_last_name"] . " - " . $row["human_resources_role_name"];
            $array[$key] = $row["cost_value"];
            $arrayName[$key] = $name;
        }

        //update all cost entries per human resource
        foreach ($humanCost as $row) {
            $key = $row["cost_human_resource_id"] . " - " . $row["cost_human_resource_role_id"];
            $value = ($array[$key] * $row["cost_quantity"]) * diferencaMeses(substr($row["cost_date_begin"], 0, -9), substr($row["cost_date_end"], 0, -9));
            $q->clear();
            $q->addTable("costs");
            $q->addUpdate("cost_value_unitary", $array[$key]);
            $q->addUpdate("cost_value_total", $value);
            $q->addUpdate("cost_description", $arrayName[$key]);
            $q->addWhere("cost_human_resource_id=" . $row["cost_human_resource_id"] .
                    " and cost_human_resource_role_id=" . $row["cost_human_resource_role_id"] .
                    " and cost_project_id=" . $project); //this filter gets all entries in costs table related to the same human resource and role per project 
            $q->exec();
        }
        //insert new cost entries in case it still not is registered
        foreach ($res as $row) {
            $cost_human_resource_id = $row["human_resource_id"];
            $cost_human_resource_role_id = $row["human_resources_role_id"];
            $name = $row["contact_first_name"] . " " . $row["contact_last_name"] . " - " . $row["human_resources_role_name"];
            $bool = true;
            foreach ($humanCost as $column) {
                if ($cost_human_resource_id == $column["cost_human_resource_id"] && $cost_human_resource_role_id == $column["cost_human_resource_role_id"]) {
                    $bool = false;
                }
            }
            if ($bool == true) {
                $q->clear();
                $q->addTable("costs");
                $q->addInsert("cost_type_id", 0);
                $q->addInsert("cost_project_id", $project);
                $q->addInsert("cost_description", $name);
                $q->addInsert("cost_quantity", 1);
                $q->addInsert("cost_date_begin", date("Y-m-d H:i:s"));
                $q->addInsert("cost_date_end", date("Y-m-d H:i:s", $date2));
                $q->addInsert("cost_value_unitary", $row["cost_value"]);
                $q->addInsert("cost_value_total", $row["cost_value"]);
                $q->addInsert("cost_human_resource_id", $cost_human_resource_id);
                $q->addInsert("cost_human_resource_role_id", $cost_human_resource_role_id);
                $q->exec();
            }
        }
    }

    //bug fix: after delete all alocations of a HR a line with blank description still appers on costs estimations. Those lines are deleted here
     $q->clear();
     $q->setDelete("costs");
     $q->addWhere("cost_description = ''");
     $q->exec();          
    //Comment all part related to non-human resources beecause now they are registered directly on cost module
    /*
    $notHumanCost = getResources("Non-Human", $whereProject); //get the resources from the costs table   
    $q->clear();
    $q->addQuery("r.resource_name,t.task_id,COUNT(r.resource_name) as qntd,r.resource_id");
    $q->addTable("tasks", "t");
    $q->innerJoin("resource_tasks", "rt", "rt.task_id = t.task_id");
    $q->innerJoin("resources", "r", "r.resource_id = rt.resource_id");
    $q->addWhere("t.task_project = " . $project);
    $q->addWhere("rt.percent_allocated = 100");
    $q->addGroup("r.resource_name");
    $q->addOrder("r.resource_name ASC");
    $resNH = $q->loadList();
    if ($notHumanCost == null) {
        foreach ($resNH as $row) {
            $q->addTable("costs");
            $q->addInsert("cost_type_id", 1);
            $q->addInsert("cost_project_id", $project);
            $q->addInsert("cost_description", $row["resource_name"]);
            $q->addInsert("cost_quantity", $row["qntd"]);
            $q->addInsert("cost_date_begin", date("Y-m-d H:i:s"));
            $q->addInsert("cost_date_end", date("Y-m-d H:i:s", $date2));
            $q->addInsert("cost_value_unitary", 0);
            $q->addInsert("cost_value_total", 0);
            $q->addInsert("cost_human_resource_id", $row["resource_id"]);
            $q->exec();
        }
    } else {
        // ################### UPDATE OR INSERTE NON-HUMAN RESOURCES ######################## 
        foreach ($resNH as $row) {
            $array[$row["resource_id"]][0] = $row["qntd"];
            $array[$row["resource_id"]][1] = $row["resource_name"];
        }

        foreach ($notHumanCost as $row) {
            $res_id = $row["cost_human_resource_id"];
            $value = $array[$res_id][0] * $row["cost_value_unitary"];
            $q->clear();
            $q->addTable("costs");
            $q->addUpdate("cost_quantity", $array[$res_id][0]);
            $q->addUpdate("cost_description", $array[$res_id][1]);
            $q->addUpdate("cost_value_total", $value);
            $q->addWhere("cost_human_resource_id=" . $res_id . " and cost_type_id= 1");
            $q->exec();
        }

        foreach ($resNH as $row) {
            $bool = true;
            foreach ($notHumanCost as $column) {
                if ($row["resource_name"] == $column["cost_description"]) {
                    $bool = false;
                }
            }
            if ($bool == true) {
                $q->clear();
                $q->addTable("costs");
                $q->addInsert("cost_type_id", 1);
                $q->addInsert("cost_project_id", $project);
                $q->addInsert("cost_description", $row["resource_name"]);
                $q->addInsert("cost_quantity", $row["qntd"]);
                $q->addInsert("cost_date_begin", date("Y-m-d H:i:s"));
                $q->addInsert("cost_date_end", date("Y-m-d H:i:s", $date2));
                $q->addInsert("cost_value_unitary", 0);
                $q->addInsert("cost_value_total", 0);
                $q->addInsert("cost_human_resource_id", $row["resource_id"]);
                $q->exec();
            }
        } 
    }
    */
}

function insertReserveBudget($project) {

    $q = new DBQuery();

    $q->clear();
    $q->addQuery("r.risk_id,r.risk_name");
    $q->addTable("risks", "r");
    //$q->addWhere("risk_project = " . $project . " and risk_probability = \"3\" or risk_probability = \"4\"");//this line is for manual contigency risk insertion
    $q->addWhere("risk_is_contingency=1 and risk_project = " . $project ); //this line is for manual contingency risk insertion
    $q->addOrder("risk_id");
    $risk = $q->loadList();


    $q->clear();
    $q->addQuery("*");
    $q->addTable("budget_reserve", "b");
    $q->addWhere("budget_reserve_project_id = " . $project);
    $q->addOrder("budget_reserve_risk_id");
    $budgets = $q->loadList();


    if ($budgets == null) {
        foreach ($risk as $row) {

            $q->addTable("budget_reserve");
            $q->addInsert("budget_reserve_project_id", $project);
            $q->addInsert("budget_reserve_risk_id", $row["risk_id"]);
            $q->addInsert("budget_reserve_description", $row["risk_name"]);
            $q->addInsert("budget_reserve_financial_impact", 0);
            $q->addInsert("budget_reserve_inicial_month", date("Y-m-d H:i:s"));
            $q->addInsert("budget_reserve_final_month", date("Y-m-d H:i:s"));
            $q->addInsert("budget_reserve_value_total", 0);
            $q->exec();
        }
    } else {
        foreach ($risk as $row) {
            $q->addTable("budget_reserve");
            $q->addUpdate("budget_reserve_description", $row["risk_name"]);
            $q->addWhere("budget_reserve_project_id=" . $project . " and budget_reserve_risk_id=" . $row[risk_id]);
            $q->exec();
        }
        foreach ($risk as $row) {
            $bool = true;
            foreach ($budgets as $column) {
                if ($row["risk_id"] == $column["budget_reserve_risk_id"]) {            
                    $bool = false;
                    //in case risk already exist, update its name, in case ir have been updated after created
                    $q->clear();
                    $q->addTable("budget_reserve");
                    $q->addUpdate("budget_reserve_description", $row["risk_name"]);
                    $q->addWhere("budget_reserve_project_id=" . $project . " and budget_reserve_risk_id=" . $row["risk_id"]);
                    $q->exec();
                }
            }
            if ($bool == true) {
                $q->clear();
                $q->addTable("budget_reserve");
                $q->addInsert("budget_reserve_project_id", $project);
                $q->addInsert("budget_reserve_risk_id", $row["risk_id"]);
                $q->addInsert("budget_reserve_description", $row["risk_name"]);
                $q->addInsert("budget_reserve_financial_impact", 0);
                $q->addInsert("budget_reserve_inicial_month", date("Y-m-d H:i:s"));
                $q->addInsert("budget_reserve_final_month", date("Y-m-d H:i:s"));
                $q->addInsert("budget_reserve_value_total", 0);
                $q->exec();
            }
        }
        //delete budget for non existing risks
        //update risk list with all project risk
        $q = new DBQuery();
        $q->addQuery("risk_id");
        $q->addTable("risks");
        $q->addWhere("risk_project = " . $project );
        $risk = $q->loadList();
        
        foreach ($budgets as $column) {
            $hasRisk = false;
            foreach ($risk as $row) {
                if ($row["risk_id"] == $column["budget_reserve_risk_id"]) {
                    $hasRisk = true;
                }
            }
            if (!$hasRisk) {
                $q = new DBQuery();
                $q->setDelete("budget_reserve");
                $q->addWhere("budget_reserve_id =" . $column["budget_reserve_id"]);
                $q->exec();
            }
        }
    }
    
    
}

function insertBudget($project, $subTotal) {

    $q = new DBQuery();
    $q->clear();
    $q->addQuery("*");
    $q->addTable("budget");
    $q->addWhere("budget_project_id = " . $project);
    $q->addOrder("budget_id");
    $res = $q->loadList();
    $resul = $q->exec();

    if ($res == null) {

        $q->addTable("budget");
        $q->addInsert("budget_id", $project);
        $q->addInsert("budget_project_id", $project);
        $q->addInsert("budget_reserve_management", 0);
        $q->addInsert("budget_sub_total", $subTotal);
        $q->addInsert("budget_total", $subTotal);
        $q->exec();
    } else {
        $total = (($res[0]["budget_reserve_management"] / 100) * $subTotal);
        $total += $subTotal;

        $q->addTable("budget");
        $q->addUpdate("budget_sub_total", $subTotal);
        $q->addUpdate("budget_total", $total);
        $q->addWhere("budget_id=" . $project);
        $q->exec();
    }
}

function diffDate($d1, $d2, $type = "", $sep = "-") {
    $d1 = explode($sep, $d1);
    $d2 = explode($sep, $d2);
    switch ($type) {
        case "A":
            $X = 31536000;
            break;
        case "M":
            $X = 2592000;
            break;
        case "D":
            $X = 86400;
            break;
        case "H":
            $X = 3600;
            break;
        case "MI":
            $X = 60;
            break;
        default:
            $X = 1;
    }
    $dif = floor(((mktime(0, 0, 0, $d2[1], $d2[2], (int) $d2[0])) - (mktime(0, 0, 0, $d1[1], $d1[2], (int) $d1[0]))) / $X);
    //$dif = ceil(((mktime(0, 0, 0, $d2[1], $d2[2], (int) $d2[0])) - (mktime(0, 0, 0, $d1[1], $d1[2], (int) $d1[0]))) / $X); //using ceil to calc month amonths    
    $result = $dif == 0 ? 1 : $dif;
    return $result;
}

function subTotalBudget($meses, $c, $mtz, $control, $sumColumns) {
    for ($i = 0; $i <= $meses; $i++) {

        echo "<td style=\"text-align:right\"><b>";

        for ($j = 0; $j <= $c; $j++) {
            $sum = $sum + $mtz[$j][$i];
        }
        $sumColumns[$control][$i] = $sum;

        echo formatCellContent(number_format($sum, 2, ",", "."));

        echo " </b>   </td>";


        $sum = 0;
    }
    return $sumColumns;
}

function subTotalBudgetRow($meses, $c, $mtz, $control) {
    for ($i = 0; $i <= $meses; $i++) {
        $sum = $sum + $mtz[$control][$i];
    }
    return $sum;
}

function formatCellContent($content) {
    if (isset($_GET["print"])) {
        $output = "";
        for ($i = 0; $i < strlen($content); $i++) {
            $output.=substr($content, $i, 1);
            if ($i == 8 || $i == 16 || $i == 24 || $i == 32) {
                $output.="<br/>";
            }
        }
        $content = "<span style=\"font-size:7px;\">" . $output . "</span>";
    }
    return $content;
}

function costsBudget($meses, $c, $row, $mStartProject, $mEndProject, $mtz, $monthsYearsIndex) {
    $monthStart = substr($row["cost_date_begin"], 5, -12);
    $yearStart = substr($row["cost_date_begin"], 0, -15);
    $monthEnd = substr($row["cost_date_end"], 5, -12);
    $yearEnd = substr($row["cost_date_end"], 0, -15);
    $key = $yearStart . "_" . $monthStart;
    $startIndex = $monthsYearsIndex[$key];
    $diffMonths = diferencaMeses(substr($row["cost_date_begin"], 0, -9), substr($row["cost_date_end"], 0, -9));
  
    if ($diffMonths < 0) {
        $diffMonths = 0;
    } else if ($diffMonths >= count($monthsYearsIndex)) {
        $diffMonths = count($monthsYearsIndex) - 1; // this case the resource dates are longer than project dates, it will be limited by project dates.
    }
    for ($i = 0; $i <= $meses; $i++) {
        $mStartProject++;
        if ($i == $startIndex) {
            if ($monthStart == $monthEnd && $yearEnd == $yearStart) { //exception for resources which lasts just a month
                echo "<td style=\"text-align:right;width:1%;\">";
                $mtz[$c][$k] = $row["cost_value_total"];
                echo formatCellContent(number_format($mtz[$c][$k], 2, ",", "."));
                echo "</td>";
            } else {
                $k = $i;
                for ($j = 0; $j <= $diffMonths; $j++) {
                    echo "<td style=\"text-align:right;width:1%;\">";
                    $mtz[$c][$k] = $row["cost_value_total"] / ($diffMonths + 1);
                    echo formatCellContent(number_format($mtz[$c][$k], 2, ",", "."));
                    echo "</td>";
                    $k++;
                }
                $i = $k - 1;
            }
        } else {
            echo "<td style=\"text-align:right;width:1%;\">";
            $mtz[$c][$i] = 0;
            echo formatCellContent(number_format(0, 2, ",", "."));
            echo "</td>";
        }
    }
    return $mtz;
}

function costsContingency($meses, $c, $row, $mStartProject, $mEndProject, $mtz, $monthsYearsIndex) {
    $monthStart = substr($row["budget_reserve_inicial_month"], 5, -12);
    $yearStart = substr($row["budget_reserve_inicial_month"], 0, -15);
    $monthEnd = substr($row["budget_reserve_final_month"], 5, -12);
    $yearEnd = substr($row["budget_reserve_final_month"], 0, -15);
    $key = $yearStart . "_" . $monthStart;
    $startIndex = $monthsYearsIndex[$key];
    $d1=substr($row["budget_reserve_inicial_month"], 0, -9);    
    $diffMonths = diferencaMeses(substr($row["budget_reserve_inicial_month"], 0, -9), substr($row["budget_reserve_final_month"], 0, -9));
    
    if ($diffMonths < 0) {
        $diffMonths = 0;
    } else if ($diffMonths >= count($monthsYearsIndex)) {
        $diffMonths = count($monthsYearsIndex) - 1; // this case the resource dates are longer than project dates, it will be limited by project dates.
    }
    
    for ($i = 0; $i <= $meses; $i++) {
        $mStartProject++;
        if ($i == $startIndex) {
            if ($monthStart == $monthEnd && $yearEnd == $yearStart) { //exception for resources which lasts just a month
                echo "<td style=\"text-align:right;width:1%;\">";
		$k = $i;
                $mtz[$c][$k] = $row["budget_reserve_financial_impact"];
                echo formatCellContent(number_format($mtz[$c][$k], 2, ",", "."));
                echo "</td>";
            } else {
                $k = $i;
                for ($j = 0; $j <= $diffMonths; $j++) {
                    echo "<td style=\"text-align:right;width:1%;\">";
                    $mtz[$c][$k] = $row["budget_reserve_financial_impact"] / ($diffMonths + 1);
                    echo formatCellContent(number_format($mtz[$c][$k], 2, ",", "."));
                    echo "</td>";
                    $k++;
                }
                $i = $k - 1;
            }
        } else {
            echo "<td style=\"text-align:right;width:1%;\">";
            $mtz[$c][$i] = 0;
            echo formatCellContent(number_format(0, 2, ",", "."));
            echo "</td>";
        }
    }

    return $mtz;
}
/**
 * 
 * @param type $meses: the quantity of project months
 * @param type $sumColumns: all costs values calculated for the project
 * @return array: containg for each month of costs baseline the total planned value.
 */
function totalBudget($meses, $sumColumns) {
    $costsBaselinePlannedArray=array();
    for ($i = 0; $i <= $meses; $i++) {
        
        for ($j = 0; $j <= 2; $j++) {
            $result += $sumColumns[$j][$i];
        }     
        $costsBaselinePlannedArray[$i]= $result;
        echo "<td width=\"10%\" style=\"text-align:right\">";
        echo "<b>";
        echo formatCellContent(number_format($result, 2, ",", "."));

        echo "</b>";
        echo "</td>";
        $result = 0;
    }
    return $costsBaselinePlannedArray;
}
?>
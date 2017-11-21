<?php
$q = new DBQuery();
$q->addQuery("*");
$q->addTable("risks");
$q->addOrder("risk_id");
$q->setLimit(100);
$list1 = $q->loadList();

foreach ($list1 as $line) {
    $risk_id = $line["risk_id"];
    $Priority;
    $risk_probability = intval($line["risk_probability"]);
    $risk_impact = intval($line["risk_impact"]);
    if (($risk_impact == 0) || ($risk_probability == 2 && $risk_impact == 1) || ($risk_probability == 1 && $risk_impact == 1) || ($risk_probability == 0 && $risk_impact < 4)) {
        $Priority = 0;
    } else {
        if (($risk_probability == 4 && $risk_impact == 1) || ($risk_probability == 3 && $risk_impact == 1) || ($risk_probability == 3 && $risk_impact == 2) || ($risk_probability == 2 && $risk_impact == 2) || ($risk_probability == 1 && $risk_impact == 2) || ($risk_probability == 1 && $risk_impact == 3) || ($risk_probability == 0 && $risk_impact == 4)) {
            $Priority = 1;
        } else {
            if (($risk_impact == 4 && $risk_probability > 0) || ($risk_impact == 3 && $risk_probability > 1) || ($risk_probability == 4 && $risk_impact == 2)) {
                $Priority = 2;
            }
        }
    }
    $dbprefix = dPgetConfig("dbprefix", "");
    $consulta = "UPDATE {$dbprefix}risks SET risk_priority = \"$Priority\" WHERE risk_id = \"$risk_id\"";
    $resultado = mysql_query($consulta) or die($AppUI->_("LBL_QUERY_FAIL"));
}

$q->clear();
$q->addQuery("user_id");
$q->addQuery("CONCAT( contact_first_name, \" \", contact_last_name)");
$q->addTable("users");
$q->leftJoin("contacts", "c", "user_contact = contact_id");
$q->addOrder("contact_first_name, contact_last_name");
$users = $q->loadHashList();

$q->clear();
$q->addQuery("project_id, project_name");
$q->addTable("projects");
$q->addOrder("project_name");
$projects = $q->loadHashList();

$q->clear();
$q->addQuery("task_id, task_name");
$q->addTable("tasks");
$q->addOrder("task_name");
$tasks = $q->loadHashList();

$riskProbability = dPgetSysVal("RiskProbability");
foreach ($riskProbability as $key => $value) {
    $riskProbability[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}
$riskStatus = dPgetSysVal("RiskStatus");
foreach ($riskStatus as $key => $value) {
    $riskStatus[$key] = str_replace("&amp;atilde;", "ã", htmlspecialchars($AppUI->_($value)));
}
$riskImpact = dPgetSysVal("RiskImpact");
foreach ($riskImpact as $key => $value) {
    $riskImpact[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}
$riskPotential = dPgetSysVal("RiskPotential");
foreach ($riskPotential as $key => $value) {
    $riskPotential[$key] = str_replace("&amp;atilde;", "ã", htmlspecialchars($AppUI->_($value)));
}
$riskActive = dPgetSysVal("RiskActive");
foreach ($riskActive as $key => $value) {
    $riskActive[$key] = str_replace("&amp;atilde;", "ã", htmlspecialchars($AppUI->_($value)));
}
$riskStrategy = dPgetSysVal("RiskStrategy");
foreach ($riskStrategy as $key => $value) {
    $riskStrategy[$key] = $AppUI->_($value);
}
$riskPriority = dPgetSysVal("RiskPriority");
foreach ($riskPriority as $key => $value) {
    $riskPriority[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}

$bgRed = "FF6666";
$bgYellow = "FFFF66";
$bgGreen = "33CC66";
$valid_ordering = array(
    "risk_id",
    "risk_name",
    "risk_description",
    "risk_probability",
    "risk_impact",
    "risk_priority",
    "risk_answer_to_risk",
    "risk_status",
    "risk_responsible",
    "risk_project",
    "risk_task",
    "risk_notes",
    "risk_potential_other_projects",
    "risk_lessons_learned",
    "risk_strategy",
    "risk_prevention_action",
    "risk_contingency_plan",
);
$orderdire = "desc";
$orderbyy = "risk_priority";
$whereProject = "";
$whereProject = " and risk_project=" . $projectId;
$q->clear();
$q->addQuery("*");
$q->addTable("risks");
$q->addWhere("risk_active = \"0\" $whereProject");
$q->addOrder($orderbyy . " " . $orderdire);
$activeList = $q->loadList();

$q->clear();
$q->addQuery("*");
$q->addTable("risks");
$q->addWhere("risk_active = \"1\" $whereProject");
$q->addOrder($orderbyy . " " . $orderdire);
$inactiveList = $q->loadList();
?>
<table class="printTable" width="100%" >
    <tr style="vertical-align: top">
       <!--  <th><?php // echo $AppUI->_("LBL_ID");  ?></th> -->
        <th style="width: 20%"><?php echo $AppUI->_("LBL_NAME",UI_OUTPUT_HTML); ?></th>
        <!-- <th><?php //echo $AppUI->_("LBL_DESCRIPTION");  ?></th> -->
        <th style="width: 6%;word-break: break-all;word-wrap: break-word;"><?php echo $AppUI->_("LBL_PROBABILITY",UI_OUTPUT_HTML); ?></th>
        <th style="width: 8%;word-break: break-all;word-wrap: break-word;"><?php echo $AppUI->_("LBL_IMPACT",UI_OUTPUT_HTML); ?></th>
        <th style="width: 8%;word-break: break-all;word-wrap: break-word;"><?php echo $AppUI->_("LBL_PRIORITY",UI_OUTPUT_HTML); ?></th>
        <!-- <th><?php //echo $AppUI->_("LBL_STATUS");  ?></th> -->
        <!-- <th><?php //echo $AppUI->_("LBL_OWNER");  ?></th> -->
        <!--
        <th><?php //echo $AppUI->_("LBL_TASK");  ?></th>
        <th><?php //echo $AppUI->_("LBL_POTENTIAL");  ?></th>
        -->
        <th style="width: 8%;word-break: break-all;word-wrap: break-word;"><?php echo $AppUI->_("LBL_STRATEGY",UI_OUTPUT_HTML); ?></th>
        <th style="width: 25%;"><?php echo $AppUI->_('LBL_PREVENTION_ACTIONS',UI_OUTPUT_HTML); ?></th>
        <th style="width: 25%;"><?php echo $AppUI->_('LBL_CONTINGENCY_PLAN',UI_OUTPUT_HTML); ?></th>

    </tr>
    <?php foreach ($activeList as $row) {
        ?>
        <tr>
           <!-- <td><?php // echo $row["risk_id"]  ?></td> -->
            <td><?php echo $row["risk_name"] ?></td>
            <!--<td><?php //echo $row["risk_description"]  ?></td>-->
            <td><?php echo $riskProbability[$row["risk_probability"]] ?></td>
            <td><?php echo $riskImpact[$row["risk_impact"]] ?></td>
            <?php
            $color="#228B22";//ForestGreen
                    
            if ($row["risk_priority"]==1){
                $color="#DAA520";//GoldenRod
            }else if($row["risk_priority"]==2){
                $color="#B22222";//FireBrick
            }
            
            ?>
            <td style="color:<?php echo $color ?>"><?php echo $riskPriority[$row["risk_priority"]] ?></td>
            <!--<td><?php //echo $riskStatus[$row["risk_status"]]  ?></td>-->
            <?php
            /*
              $ResponsibleDefined = "no";
              foreach ($users as $k => $v) {
              if ($k == $row["risk_responsible"]) {
              $row["risk_responsible"] = $v;
              $ResponsibleDefined = "yes";
              }
              }
              if ($ResponsibleDefined != "yes") {
              $row["risk_responsible"] = $AppUI->_("LBL_NOT_DEFINED");
              }
             */
            ?>
            <!--<td><?php //echo $row["risk_responsible"]  ?></td>-->
            <?php
            /*
              $ProjectDefined = "no";
              foreach ($projects as $k => $v) {
              if ($k == $row["risk_project"]) {
              $row["risk_project"] = $v;
              $ProjectDefined = "yes";
              }
              }
              if ($ProjectDefined != "yes") {
              $row["risk_project"] = $AppUI->_("LBL_NOT_DEFINED");
              }
             */
            ?>
            <?php
            /*
              foreach ($tasks as $k => $v) {
              if ($k == $row["risk_task"]) {
              $row["risk_task"] = $v;
              }
              }
              if ($row["risk_task"] == "0") {
              $row["risk_task"] = $AppUI->_("LBL_NOT_DEFINED");
              } else {
              if ($row["risk_task"] == "-1") {
              $row["risk_task"] = $AppUI->_("LBL_ALL_TASKS");
              }
              }
             */
            ?>
            <!--
            <td><?php //echo $row["risk_task"]  ?></td>
            <td><?php //echo $riskPotential[$row["risk_potential_other_projects"]]  ?></td>
            -->
            <td><?php echo $riskStrategy[$row["risk_strategy"]] ?></td>
            <td><?php echo $row["risk_prevention_actions"]; ?>&nbsp;</td>
            <td><?php echo $row["risk_contingency_plan"]; ?>&nbsp;</td>
        </tr>
        <!--
        <tr>
            <th colspan="2"  style="text-align: right;"><?php //echo $AppUI->_('LBL_LESSONS');  ?>:</th>
            <td colspan="7"><?php // echo $row["risk_lessons_learned"];  ?>&nbsp;</td>
        </tr>
        -->
    <?php } ?>
</table>
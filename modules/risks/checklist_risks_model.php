<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('risks');
$q->addOrder('risk_id');
$q->setLimit(100);
$list1 = $q->loadList();
foreach ($list1 as $line) {
    $risk_id = $line['risk_id'];
    $Priority;
    $risk_probability = intval($line['risk_probability']);
    $risk_impact = intval($line['risk_impact']);
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
    $q->addUpdate('risk_priority', $Priority);
    $q->addWhere('risk_id = '.$risk_id);
    $q->addTable('risks');
    if (! $q->exec()) {
        die($AppUI->_("LBL_QUERY_FAIL"));
    }
    $q->clear();
}

$q->clear();
$q->addQuery('user_id');
$q->addQuery('CONCAT( contact_first_name, \' \', contact_last_name)');
$q->addTable('users');
$q->leftJoin('contacts', 'c', 'user_contact = contact_id');
$q->addOrder('contact_first_name, contact_last_name');
$users = $q->loadHashList();

$q->clear();
$q->addQuery('project_id, project_name');
$q->addTable('projects');
$q->addOrder('project_name');
$projects = $q->loadHashList();

$q->clear();
$q->addQuery('task_id, task_name');
$q->addTable('tasks');
$q->addOrder('task_name');
$tasks = $q->loadHashList();

$riskProbability = dPgetSysVal('RiskProbability');
foreach ($riskProbability as $key => $value) {
    $riskProbability[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}
$riskStatus = dPgetSysVal('RiskStatus');
foreach ($riskStatus as $key => $value) {
    $riskStatus[$key] = str_replace("&amp;atilde;", "ã", htmlspecialchars($AppUI->_($value)));
}
$riskImpact = dPgetSysVal('RiskImpact');
foreach ($riskImpact as $key => $value) {
    $riskImpact[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}
$riskPotential = dPgetSysVal('RiskPotential');
foreach ($riskPotential as $key => $value) {
    $riskPotential[$key] = str_replace("&amp;atilde;", "ã", htmlspecialchars($AppUI->_($value)));
}
$riskActive = dPgetSysVal('RiskActive');
foreach ($riskActive as $key => $value) {
    $riskActive[$key] = str_replace("&amp;atilde;", "ã", htmlspecialchars($AppUI->_($value)));
}
$riskStrategy = dPgetSysVal('RiskStrategy');
foreach ($riskStrategy as $key => $value) {
    $riskStrategy[$key] = $AppUI->_($value);
}
$riskPriority = dPgetSysVal('RiskPriority');
foreach ($riskPriority as $key => $value) {
    $riskPriority[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}

$bgRed = "FF6666";
$bgYellow = "FFFF66";
$bgGreen = "33CC66";
$valid_ordering = array(
    'risk_id',
    'risk_name',
    'risk_description',
    'risk_probability',
    'risk_impact',
    'risk_priority',
    'risk_answer_to_risk',
    'risk_status',
    'risk_responsible',
    'risk_project',
    'risk_task',
    'risk_notes',
    'risk_potential_other_projects',
    'risk_lessons_learned',
    `risk_strategy`,
    `risk_prevention_action`,
    `risk_contingency_plan`,
);


$orderBy = 'risk_ear_classification';
$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$whereProject = '';
if ($projectSelected != null) {
    $whereProject = " not(risk_project=" . $projectSelected . ")";
}
$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere($whereProject);
$q->addOrder($orderBy . " asc");
$activeList = $q->loadList();
?>
<form name="checklist_analysis" action="?m=risks" method="post">
    <input type="hidden" name="dosql" value="do_checklist_analysis" />
    <input type="hidden" name="project_id" value="<?php echo $projectSelected; ?>" />
    <table width="95%" align="center"  border="0" cellpadding="2" cellspacing="1" class="tbl">
        <tr><th align="center" colspan="6"><b><?php echo $AppUI->_('LBL_CHECKLIST_ANALYSIS'); ?></b></th></tr>
        <tr>
            <th nowrap="nowrap"width=""></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('LBL_ID'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('LBL_RISK_NAME'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('LBL_DESCRIPTION'); ?></th>
            <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PRIORITY'); ?></th>      
            <th nowrap="nowrap"><?php echo $AppUI->_('LBL_STRATEGY'); ?></th>
        </tr>
        <?php
        require_once DP_BASE_DIR . "/modules/risks/risks_controlling.php";
        $rcontrolling = new RisksControlling($projectSelected);
        $earClassification = "";
        foreach ($activeList as $row) {
            ?>
            <?php if ($row["risk_ear_classification"] != $earClassification) { ?>
                <tr><th colspan="6"><?php echo $rcontrolling->earOptions[$row["risk_ear_classification"]] ?></th></tr>
                <?php
            }
            $earClassification = $row["risk_ear_classification"];
            ?>
            <tr>
                <td>
                    <input type="checkbox" value="<?php echo $row['risk_id'] ?>" name="risks[]" />
                </td>
                <td><?php echo $row['risk_id'] ?></td>
                <td><?php echo $row['risk_name'] ?></td>
                <td><?php echo $row['risk_description'] ?></td>
                <td style="background-color:#<?php
        if ($row['risk_priority'] == 0) {
            echo $bgGreen;
        } else {
            if ($row['risk_priority'] == 1) {
                echo $bgYellow;
            } else {
                if ($row['risk_priority']) {
                    echo $bgRed;
                }
            }
        }
            ?>"><?php echo $riskPriority[$row['risk_priority']] ?></td>
                <td><?php echo $riskStrategy[$row['risk_strategy']] ?></td>
            </tr>

        <?php } ?>
        <tr>
            <td>
                <?php require_once (DP_BASE_DIR . "/modules/risks/backbutton.php"); ?>       
            </td>
            <td colspan="5" align="right">
                <input type="submit" value="<?php echo $AppUI->_("LBL_RISKS_CHECKLIST_ACTION"); ?>" class="button" />
            </td>
        </tr>
    </table>
</form>
<script src="./modules/risks/risks.js"></script>
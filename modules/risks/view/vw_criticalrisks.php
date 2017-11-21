<?php
$q = new DBQuery();
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
$riskPriority = dPgetSysVal('RiskPriority');
foreach ($riskPriority as $key => $value) {
    $riskPriority[$key] = str_replace("&amp;eacute;", "é", htmlspecialchars($AppUI->_($value)));
}
$riskStrategy = dPgetSysVal('RiskStrategy');
foreach ($riskStrategy as $key => $value) {
    $riskStrategy[$key] = $AppUI->_($value);
}

$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$whereProject = ' and risk_project=' . $projectSelected;

$q->clear();

$bgYellow = "#FFFF66";
$bgGreen = "#33CC66";
$bgRed = "#DF5353";
$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '0' and (risk_probability > 2 and risk_impact > 2) $whereProject");
$activeList = $q->loadList();
?>


<table width="95%" align="center" border="0" cellpadding="2" cellspacing="1" class="tbl">
     <tr>
        <th colspan="7" style="font-weight: bold"> <?php echo $AppUI->_('LBL_CRITICAL_RISKS'); ?> </th>
    </tr>
    <tr>
        <th nowrap="nowrap" style="width:5%"><?php echo $AppUI->_('Id'); ?></th>
        <th nowrap="nowrap" style="width:20%"><?php echo $AppUI->_("LBL_RISK_NAME"); ?></th>
        <th nowrap="nowrap" style="width:15%"><?php echo $AppUI->_('LBL_PRIORITY'); ?></th>
        <th nowrap="nowrap" style="width:15%"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
        <th nowrap="nowrap" style="width:15%"><?php echo $AppUI->_('LBL_OWNER'); ?></th>
        <th nowrap="nowrap" style="width:15%"><?php echo $AppUI->_('LBL_STRATEGY'); ?></th>
    </tr>
    <?php foreach ($activeList as $row) { ?>
        <tr>
            <td ><?php echo $row['risk_id'] ?></td>
            <td><?php echo $row['risk_name'] ?></td>
            
            <!-- 
            <td><?php echo $row['risk_description'] ?></td> 
            <td><?php echo $riskProbability[$row['risk_probability']] ?></td>            
            <td><?php echo $riskImpact[$row['risk_impact']] ?></td>
            -->
            
            <td style="background-color:
                <?php
                if ($row['risk_priority'] == 0) {
                    echo $bgGreen;
                } else if ($row['risk_priority'] == 1) {
                    echo $bgYellow;
                } else if ($row['risk_priority'] == 2) {
                    echo $bgRed;
                }
                ?>"><?php echo $riskPriority[$row['risk_priority']] ?></td>
            <td><?php echo $riskStatus[$row['risk_status']] ?></td>
            <?php
            foreach ($users as $k => $v) {
                if ($k == $row['risk_responsible']) {
                    $row['risk_responsible'] = $v;
                }
            }
            ?>
            <td><?php echo $row['risk_responsible'] ?></td>
            <?php
            foreach ($projects as $k => $v) {
                if ($k == $row['risk_project']) {
                    $row['risk_project'] = $v;
                }
            }
            if ($row['risk_project'] == '0') {
                $row['risk_project'] = $AppUI->_('LBL_NOT_DEFINED');
            }
            ?>
            <!--
            <?php
            foreach ($tasks as $k => $v) {
                if ($k == $row['risk_task']) {
                    $row['risk_task'] = $v;
                }
            }
            if ($row['risk_task'] == '0') {
                $row['risk_task'] = $AppUI->_('LBL_NOT_DEFINED');
            } else {
                if ($row['risk_task'] == '-1') {
                    $row['risk_task'] = $AppUI->_('LBL_ALL_TASKS');
                }
            }
            ?>
            <td><?php echo $row['risk_task'] ?></td>
            -->
            <td><?php echo $riskStrategy[$row['risk_strategy']] ?></td>
        </tr>
    <?php } ?>
</table>
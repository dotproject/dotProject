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
$q->addWhere("risk_active = '0' and (risk_period_end_date < now() and risk_status<>3) $whereProject");
$activeList = $q->loadList();
?>


<table width="95%" align="center" border="0" cellpadding="2" cellspacing="1" class="tbl">
     <tr>
        <th colspan="7" style="font-weight: bold"> <?php echo $AppUI->_("LBL_TRIGGERS_WITH_HIGH_PROBABILITY"); ?> </th>
    </tr>
    <tr>
        <th nowrap="nowrap" ><?php echo $AppUI->_('Id'); ?></th>
        <th nowrap="nowrap" ><?php echo $AppUI->_("LBL_RISK_NAME"); ?></th>
        <th nowrap="nowrap" ><?php echo $AppUI->_("LBL_TRIGGER"); ?></th>
    </tr>
    <?php foreach ($activeList as $row) { ?>
        <tr>
            <td ><?php echo $row['risk_id'] ?></td>
            <td><?php echo $row['risk_name'] ?></td>           
            <td ><?php echo $row['risk_triggers'] ?></td>
        </tr>
    <?php } ?>
</table>
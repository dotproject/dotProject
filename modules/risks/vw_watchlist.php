<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}
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
$whereProject = '';
if ($projectSelected != null) {
    $t = intval(dPgetParam($_GET, 'tab'));
    // setup the title block
    $titleBlock = new CTitleBlock($AppUI->_('LBL_RISKS') . ' - ' . str_replace("&ccedil;&atilde;", "çã",$AppUI->_('LBL_WATCHLIST')), 'risks.png', $m, "$m.$a");
    $titleBlock->addCrumb("?m=projects&a=view&project_id=" . $projectSelected . "&tab=" . $t."&targetScreenOnProject=/modules/risks/projects_risks.php", "LBL_RETURN_LIST");
    $titleBlock->show();
    $whereProject = ' and risk_project=' . $projectSelected;
}
$q->clear();

$bgYellow = "FFFF66";
$bgGreen = "33CC66";

$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '0' and (risk_priority = 0 or risk_priority = 1) $whereProject");
$activeList = $q->loadList();

$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '1' and (risk_priority = 0 or risk_priority = 1) $whereProject");
$inactiveList = $q->loadList();
?>

<?php echo $AppUI->_('LBL_ACTIVE_RISKS'); ?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th nowrap="nowrap"></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Id'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_RISK_NAME'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_DESCRIPTION'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PROBABILITY'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_IMPACT'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PRIORITY'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
        <!--
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_OWNER'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PROJECT'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_TASK'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_POTENTIAL'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_STRATEGY'); ?></th>
        -->
    </tr>
    <?php foreach ($activeList as $row) {
        ?>
        <tr>
            <td nowrap style="background-color:#<?php echo $bg; ?>" width="30">
                <a href="index.php?m=risks&a=addedit&id=<?php
    echo($row['risk_id']);
    if ($projectSelected != null) {
        echo('&project_id=' . $projectSelected . '&tab=' . $t . '&vw=vw_watchlist');
    }
        ?>">
                    <img src="./modules/risks/images/stock_edit-16.png" border="0" width="12" height="12">
                </a>
                <a href="index.php?m=risks&a=view&id=<?php
               echo($row['risk_id']);
               if ($projectSelected != null) {
                   echo('&project_id=' . $projectSelected . '&tab=' . $t . '&vw=vw_watchlist');
               }
        ?>">
                    <img src="./modules/risks/images/view_icon.gif" border="0" width="12" height="12">
                </a>
            </td>
            <td width="25"><?php echo $row['risk_id'] ?></td>
            <td><?php echo $row['risk_name'] ?></td>
            <td><?php echo $row['risk_description'] ?></td>
            <td><?php echo $riskProbability[$row['risk_probability']] ?></td>
            <td><?php echo $riskImpact[$row['risk_impact']] ?></td>
            <td style="background-color:#<?php
               if ($row['risk_priority'] == 0) {
                   echo $bgGreen;
               } else {
                   if ($row['risk_priority'] == 1)
                       echo $bgYellow;
               }
        ?>"><?php echo $riskPriority[$row['risk_priority']] ?></td>
            <td><?php echo $riskStatus[$row['risk_status']] ?></td>
            <!--
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
            <td><?php echo $row['risk_project'] ?></td>
            <?php
            foreach ($tasks as $k => $v ) {
        if ($k==$row['risk_task']) {
            $row['risk_task'] = $v;
        }
    }
    if ($row['risk_task']=='0') {
        $row['risk_task'] = $AppUI->_('LBL_NOT_DEFINED');
    } else {
        if ($row['risk_task']=='-1') {
            $row['risk_task'] = $AppUI->_('LBL_ALL_TASKS');
        }
    }        
            ?>
            <td><?php echo $row['risk_task'] ?></td>
            <td><?php echo $riskPotential[$row['risk_potential_other_projects']] ?></td>
            <td><?php echo $riskStrategy[$row['risk_strategy']] ?></td>
            -->
        </tr>
    <?php } ?>
</table>
</br>
<?php echo $AppUI->_('LBL_INACTIVE_RISKS'); ?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th nowrap="nowrap"></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Id'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_RISK_NAME'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_DESCRIPTION'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PROBABILITY'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_IMPACT'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PRIORITY'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_STATUS'); ?></th>
        <!--
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_OWNER'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_PROJECT'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_TASK'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_POTENTIAL'); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('LBL_STRATEGY'); ?></th>
        -->
    </tr>
    <?php foreach ($inactiveList as $row) { ?>
        <tr>
            <td nowrap style="background-color:#<?php echo $bg; ?>" width="30">
                <a href="index.php?m=risks&a=addedit&id=<?php
    echo($row['risk_id']);
    if ($projectSelected != null) {
        echo('&project_id=' . $projectSelected . '&tab=' . $t . '&vw=vw_watchlist');
    }
        ?>">
                    <img src="./modules/risks/images/stock_edit-16.png" border="0" width="12" height="12">
                </a>
                <a href="index.php?m=risks&a=view&id=<?php
               echo($row['risk_id']);
               if ($projectSelected != null) {
                   echo('&project_id=' . $projectSelected . '&tab=' . $t . '&vw=vw_watchlist');
               }
        ?>">
                    <img src="./modules/risks/images/view_icon.gif" border="0" width="12" height="12">
                </a>
            </td>
            <td width="25"><?php echo $row['risk_id'] ?></td>
            <td><?php echo $row['risk_name'] ?></td>
            <td><?php echo $row['risk_description'] ?></td>
            <td><?php echo $riskProbability[$row['risk_probability']] ?></td>
            <td><?php echo $riskImpact[$row['risk_impact']] ?></td>
            <td style="background-color:#<?php
               if ($row['risk_priority'] == 0) {
                   echo $bgGreen;
               } else {
                   if ($row['risk_priority'] == 1)
                       echo $bgYellow;
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
            <td><?php echo $row['risk_project'] ?></td>
            <?php
            foreach ($tasks as $k => $v ) {
                if ($k==$row['risk_task']) {
                    $row['risk_task'] = $v;
                }
            }
            if ($row['risk_task']=='0') {
                $row['risk_task'] = $AppUI->_('LBL_NOT_DEFINED');
            } else {
                if ($row['risk_task']=='-1') {
                    $row['risk_task'] = $AppUI->_('LBL_ALL_TASKS');
                }
            }        
            ?>
            <td><?php echo $row['risk_task'] ?></td>
            <td><?php echo $riskPotential[$row['risk_potential_other_projects']] ?></td>
            <td><?php echo $riskStrategy[$row['risk_strategy']] ?></td>
        -->
        </tr>
    <?php } ?>
</table>
<script src="./modules/risks/risks.js"></script>
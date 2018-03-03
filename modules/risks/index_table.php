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
$q->clear();
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
    'risk_strategy',
    'risk_prevention_actions',
    'risk_contingency_plan'
);

$orderdire = $AppUI->getState('RisksIdxOrderDir') ? $AppUI->getState('RisksIdxOrderDir') : 'desc';
if ((isset($_GET['orderbyy'])) && (in_array($_GET['orderbyy'], $valid_ordering))) {
    $orderdire = (($AppUI->getState('RisksIdxOrderDir') == 'asc') ? 'desc' : 'asc');
    $AppUI->setState('RisksIdxOrderBy', $_GET['orderbyy']);
}
$orderbyy = (($AppUI->getState('RisksIdxOrderBy')) ? $AppUI->getState('RisksIdxOrderBy') : 'risk_priority');
$AppUI->setState('RisksIdxOrderDir', $orderdire);


$projectSelected = intval(dPgetParam($_GET, 'project_id'));
$whereProject = '';
if ($projectSelected != null) {
    $whereProject = ' and risk_project=' . $projectSelected;
}
$t = intval(dPgetParam($_GET, 'tab'));
$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '0' $whereProject");
$q->addOrder($orderbyy . ' ' . $orderdire);
$activeList = $q->loadList();

$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere("risk_active = '1' $whereProject");
$q->addOrder($orderbyy . ' ' . $orderdire);
$inactiveList = $q->loadList();
?>

<table width="95%" align="center"  border="0" cellpadding="2" cellspacing="1" class="tbl">
    <summary><?php echo $AppUI->_('LBL_ACTIVE_RISKS'); ?></summary>
    <tr>
        <th nowrap="nowrap"width="25"></th>
        <th nowrap="nowrap"><a href="<?php
if ($projectSelected != null) {
    echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
} else {
    echo '?m=risks';
}
?>&orderbyy=risk_id" class="hdr"><?php echo $AppUI->_('LBL_ID'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
            if ($projectSelected != null) {
                echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
            } else {
                echo '?m=risks';
            }
?>&orderbyy=risk_name" class="hdr"><?php echo $AppUI->_('LBL_RISK_NAME'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
            if ($projectSelected != null) {
                echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
            } else {
                echo '?m=risks';
            }
?>&orderbyy=risk_description" class="hdr"><?php echo $AppUI->_('LBL_DESCRIPTION'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
                               if ($projectSelected != null) {
                                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
                               } else {
                                   echo '?m=risks';
                               }
?>&orderbyy=risk_probability" class="hdr"><?php echo $AppUI->_('LBL_PROBABILITY'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
            if ($projectSelected != null) {
                echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
            } else {
                echo '?m=risks';
            }
?>&orderbyy=risk_impact" class="hdr"><?php echo $AppUI->_('LBL_IMPACT'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
                               if ($projectSelected != null) {
                                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
                               } else {
                                   echo '?m=risks';
                               }
?>&orderbyy=risk_priority" class="hdr"><?php echo $AppUI->_('LBL_PRIORITY'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
?>&orderbyy=risk_status" class="hdr"><?php echo $AppUI->_('LBL_STATUS'); ?></a></th>
        <!--
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
?>&orderbyy=risk_responsible" class="hdr"><?php echo $AppUI->_('LBL_OWNER'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
?>&orderbyy=risk_project" class="hdr"><?php echo $AppUI->_('LBL_PROJECT'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
    if ($projectSelected != null) {
        echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
    } else {
        echo '?m=risks';
    }
?>&orderbyy=risk_task" class="hdr"><?php echo $AppUI->_('LBL_TASK'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
               if ($projectSelected != null) {
                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
               } else {
                   echo '?m=risks';
               }
?>&orderbyy=risk_potential_other_projects" class="hdr"><?php echo $AppUI->_('LBL_POTENTIAL'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
               if ($projectSelected != null) {
                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
               } else {
                   echo '?m=risks';
               }
?>&orderbyy=risk_strategy" class="hdr"><?php echo $AppUI->_('LBL_STRATEGY'); ?></a></th>
        -->
    </tr>
        <?php foreach ($activeList as $row) {
            ?>
        <tr>
            <td nowrap style="background-color:#<?php echo $bg; ?>">
                <a href="index.php?m=risks&a=addedit&id=<?php
        echo($row['risk_id']);
        if ($projectSelected != null) {
            echo('&project_id=' . $projectSelected . '&tab=' . $t);
        }
            ?>">
                    <img src="./modules/risks/images/stock_edit-16.png" border="0" width="12" height="12">
                </a>
                <a href="index.php?m=risks&a=view&id=<?php
            echo($row['risk_id']);
            if ($projectSelected != null) {
                echo('&project_id=' . $projectSelected . '&tab=' . $t);
            }
            ?>">
                    <img src="./modules/risks/images/view_icon.gif" border="0" width="12" height="12">
                </a>
            </td>
            <td><?php echo $row['risk_id'] ?></td>
            <td><?php echo $row['risk_name'] ?></td>
            <td><?php echo $row['risk_description'] ?></td>
            <td><?php echo $riskProbability[$row['risk_probability']] ?></td>
            <td><?php echo $riskImpact[$row['risk_impact']] ?></td>
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
            <td><?php echo $riskStatus[$row['risk_status']] ?></td>
            <!--
            <?php
            $ResponsibleDefined;
            foreach ($users as $k => $v) {
                if ($k == $row['risk_responsible']) {
                    $row['risk_responsible'] = $v;
                    $ResponsibleDefined = 'yes';
                }
            }
            if ($ResponsibleDefined != 'yes') {
                $row['risk_responsible'] = $AppUI->_('LBL_NOT_DEFINED');
            }
            $ResponsibleDefined = 'no';
            ?>
            <td><?php echo $row['risk_responsible'] ?></td>
    <?php
    $ProjectDefined;
    foreach ($projects as $k => $v) {
        if ($k == $row['risk_project']) {
            $row['risk_project'] = $v;
            $ProjectDefined = 'yes';
        }
    }
    if ($ProjectDefined != 'yes') {
        $row['risk_project'] = $AppUI->_('LBL_NOT_DEFINED');
    }
    $ProjectDefined = 'no';
    ?>
            <td><?php echo $row['risk_project'] ?></td>
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
            <td><?php echo $riskPotential[$row['risk_potential_other_projects']] ?></td>
            <td><?php echo $riskStrategy[$row['risk_strategy']] ?></td>
            -->
        </tr>
            <?php } ?>
</table>

<table width="95%" align="center"  border="0" cellpadding="2" cellspacing="1" class="tbl">
    <summary> <?php echo $AppUI->_('LBL_INACTIVE_RISKS'); ?> </summary>
    <tr>
        <th nowrap="nowrap"width="25"></th>
        <th nowrap="nowrap"><a href="<?php
            if ($projectSelected != null) {
                echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
            } else {
                echo '?m=risks';
            }
            ?>&orderbyy=risk_id" class="hdr"><?php echo $AppUI->_('LBL_ID'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
            if ($projectSelected != null) {
                echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
            } else {
                echo '?m=risks';
            }
            ?>&orderbyy=risk_name" class="hdr"><?php echo $AppUI->_('LBL_RISK_NAME'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
            ?>&orderbyy=risk_description" class="hdr"><?php echo $AppUI->_('LBL_DESCRIPTION'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
            ?>&orderbyy=risk_probability" class="hdr"><?php echo $AppUI->_('LBL_PROBABILITY'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
            ?>&orderbyy=risk_impact" class="hdr"><?php echo $AppUI->_('LBL_IMPACT'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
    if ($projectSelected != null) {
        echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
    } else {
        echo '?m=risks';
    }
            ?>&orderbyy=risk_priority" class="hdr"><?php echo $AppUI->_('LBL_PRIORITY'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
               if ($projectSelected != null) {
                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
               } else {
                   echo '?m=risks';
               }
            ?>&orderbyy=risk_status" class="hdr"><?php echo $AppUI->_('LBL_STATUS'); ?></a></th>
        <!--
        <th nowrap="nowrap"><a href="<?php
               if ($projectSelected != null) {
                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
               } else {
                   echo '?m=risks';
               }
            ?>&orderbyy=risk_responsible" class="hdr"><?php echo $AppUI->_('LBL_OWNER'); ?></a></th>
        
        <th nowrap="nowrap"><a href="<?php
               if ($projectSelected != null) {
                   echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
               } else {
                   echo '?m=risks';
               }
            ?>&orderbyy=risk_project" class="hdr"><?php echo $AppUI->_('LBL_PROJECT'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
            if ($projectSelected != null) {
                echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
            } else {
                echo '?m=risks';
            }
            ?>&orderbyy=risk_task" class="hdr"><?php echo $AppUI->_('LBL_TASK'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
            ?>&orderbyy=risk_potential_other_projects" class="hdr"><?php echo $AppUI->_('LBL_POTENTIAL'); ?></a></th>
        <th nowrap="nowrap"><a href="<?php
        if ($projectSelected != null) {
            echo '?m=projects&a=view&project_id=' . $projectSelected . 'tab=' . $t;
        } else {
            echo '?m=risks';
        }
            ?>&orderbyy=risk_strategy" class="hdr"><?php echo $AppUI->_('LBL_STRATEGY'); ?></a></th>
        -->
    </tr>
        <?php foreach ($inactiveList as $row) {
            ?>
        <tr>
            <td nowrap style="background-color:#<?php echo $bg; ?>">
                <a href="index.php?m=risks&a=addedit&id=<?php
        echo($row['risk_id']);
        if ($projectSelected != null) {
            echo('&project_id=' . $projectSelected . '&tab=' . $t);
        }
            ?>">
                    <img src="./modules/risks/images/stock_edit-16.png" border="0" width="12" height="12">
                </a>

                <a href="index.php?m=risks&a=view&id=<?php
        echo($row['risk_id']);
        if ($projectSelected != null) {
            echo('&project_id=' . $projectSelected . '&tab=' . $t);
        }
        ?>">
                    <img src="./modules/risks/images/view_icon.gif" border="0" width="12" height="12">
                </a>

            </td>
            <td><?php echo $row['risk_id'] ?></td>
            <td><?php echo $row['risk_name'] ?></td>
            <td><?php echo $row['risk_description'] ?></td>
            <td><?php echo $riskProbability[$row['risk_probability']] ?></td>
            <td><?php echo $riskImpact[$row['risk_impact']] ?></td>
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
                    <td><?php echo $riskPotential[$row['risk_potential_other_projects']] ?></td>
                    <td><?php echo $riskStrategy[$row['risk_strategy']] ?></td>
            -->
        </tr>
<?php } ?>
</table>
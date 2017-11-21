<?php
/* PROJECTS $Id: companies_tab.view.active_projects.php 4779 2007-02-21 14:53:28Z cyberhorse $ */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

/**
 * Companies: View Active Projects sub-table
 */
global $AppUI, $company_id, $pstatus, $m;

$pstatus = dPgetSysVal('ProjectStatus');

if ($sort == 'project_priority') {
    $sort .= ' DESC';
}
$df = $AppUI->getPref('SHDATEFORMAT');

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

$q = new DBQuery;
$q->addTable('post_mortem_analysis', 'pma');
$q->addQuery('distinct pma.project_name, p.project_id, pma_id, project_status,
pma.project_start_date, pma.project_end_date, project_meeting_date');
$q->addJoin('projects', 'p', 'p.project_name = pma.project_name');
$q->addWhere('p.project_company = ' . $company_id);
$q->addOrder($sort);
$rows = $q->loadList();

$df = $AppUI->getPref('SHDATEFORMAT');
?>
<table width="95%" align="center" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <th width="12" />
        <th width="30%"><?php echo $AppUI->_("LBL_CLOSURE_PROJECT_NAME"); ?></th>
        <th width="20%"><?php echo $AppUI->_("LBL_CLOSURE_MEETING_DATE"); ?></th>
        <th width="20%"><?php echo $AppUI->_("LBL_CLOSURE_ACTUAL_START_DATE"); ?></th>
        <th width="20%"><?php echo $AppUI->_("LBL_CLOSURE_ACTUAL_END_DATE"); ?></th>
    </tr>
    <?php
    foreach ($rows as $p) {
        $meeting_date = intval($p['project_meeting_date']) ? new CDate($p['project_meeting_date']) : null;
        $start_date = intval($p['project_start_date']) ? new CDate($p['project_start_date']) : null;
        $end_date = intval($p['project_end_date']) ? new CDate($p['project_end_date']) : null;
        ?>
        <tr>
            <td width="12">
                <a style="text-decoration:none" href="?m=closure&a=addedit&pma_id=<?php echo $p['pma_id']; ?>&project_id=<?php echo $p['project_id']; ?>">
                    <img border="0" src="./images/icons/pencil.gif" alt="Edit post mortem record">
                </a>
            </td>
            <td> 
                    <?php echo $p['project_name']; ?> 
            </td>

            <td> <?php echo $meeting_date ? $meeting_date->format($df) : ''; ?></td>

            <td> <?php echo $start_date ? $start_date->format($df) : ''; ?></td>

            <td> <?php echo $end_date ? $end_date->format($df) : ''; ?></td>
        </tr>
    <?php } ?>
</table>
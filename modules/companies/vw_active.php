<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

##
##	Companies: View Projects sub-table
##
global $AppUI, $company_id, $pstatus, $dPconfig;

$sort = dPgetCleanParam($_GET, 'sort', 'project_name');
if ($sort == 'project_priority') {
        $sort .= ' DESC';
}

$df = $AppUI->getPref('SHDATEFORMAT');

$q  = new DBQuery;
$q->addTable('projects', 'prj');
$q->addQuery('project_id, project_name, project_start_date, project_status, project_target_budget' 
			 . ', project_start_date, project_priority, contact_first_name, contact_last_name');
$q->addJoin('users', 'u', 'u.user_id = prj.project_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('prj.project_company = ' . $company_id);

include_once ($AppUI->getModuleClass('projects'));
$projObj = new CProject();
$projList = $projObj->getDeniedRecords($AppUI->user_id);
if (count($projList)) {
$q->addWhere('NOT (project_id IN (' . implode(',',$projList) .  '))') ;
}

$q->addWhere('prj.project_status <> 7');
$q->addOrder($sort);
$s = '';

if (!($rows = $q->loadList())) {
	$s .= '<tr><td>'.$AppUI->_('No data available').'<br />'.$AppUI->getMsg().'</td></tr>';
} else {
	$s .= '<tr>';
	$s .= '<th><a style="color:white" href="?m=companies&amp;a=view&amp;company_id='.$company_id.'&amp;sort=project_priority">'
			.$AppUI->_('P').'</a></th>'
			.'<th><a style="color:white" href="?m=companies&amp;a=view&amp;company_id='.$company_id.'&amp;sort=project_name">'
			.$AppUI->_('Name').'</a></th>'
		.'<th>'.$AppUI->_('Owner').'</th>'
		.'<th>'.$AppUI->_('Started').'</th>'
		.'<th>'.$AppUI->_('Status').'</th>'
		.'<th>'.$AppUI->_('Budget').'</th>'
		.'</tr>';
	foreach ($rows as $row) {
		$start_date = new CDate($row['project_start_date']);
		$s .= '<tr><td>';
		if ($row['project_priority'] < 0) {
			$s .= dPshowImage('./images/icons/low.gif', 13, 16, '');
		} else if ($row['project_priority'] > 0) {
			$s .= dPshowImage(('./images/icons/' . $row['project_priority']) . '.gif', 13, 16, '');
		}
		
		$s .= '</td>';
		$s .= '<td width="100%">';
		$s .= ('<a href="?m=projects&amp;a=view&amp;project_id=' . dPformSafe($row['project_id']) . '">' 
		       . htmlspecialchars($row['project_name']) . '</a></td>');
		$s .= ('<td nowrap="nowrap">' . htmlspecialchars($row['contact_first_name']) . '&nbsp;' 
		       . htmlspecialchars($row['contact_last_name']) . '</td>');
		$s .= '<td nowrap="nowrap">' . $start_date->format($df) . '</td>';
		$s .= '<td nowrap="nowrap">' . $AppUI->_($pstatus[$row['project_status']]) . '</td>';
		$s .= ('<td nowrap="nowrap" align="right">' 
		       . htmlspecialchars($dPconfig['currency_symbol'] . $row['project_target_budget']) 
		       . '</td>');
		$s .= '</tr>';
	}
}
echo ('<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tbl">' 
      . $s . '</table>');
?>

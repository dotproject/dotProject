<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

##
##	Companies: View Archived Projects sub-table
##

global $AppUI, $company_id;

$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, project_name, project_start_date, project_status, project_target_budget' 
             . ', project_start_date, project_priority, contact_first_name, contact_last_name');
$q->addJoin('users', 'u', 'u.user_id = projects.project_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('projects.project_company = ' . $company_id);

include_once ($AppUI->getModuleClass('projects'));
$projObj = new CProject();
$projList = $projObj->getDeniedRecords($AppUI->user_id);
if (count($projList)) {
	$q->addWhere('NOT (project_id IN (' . implode(',', $projList) . '))') ;
}
$q->addWhere('projects.project_status = 7');
$q->addOrder('project_name');

$s = '';
if (!($rows = $q->loadList())) {
	$s .= '<tr><td>' . $AppUI->_('No data available') . '<br />' . $AppUI->getMsg() . '</td></tr>';
} else {
	$s .= '<tr><th>' . $AppUI->_('Name') . '</th><th>' . $AppUI->_('Owner') . '</th></tr>';
	
	foreach ($rows as $row) {
		$s .= '<tr><td>';
		$s .= ('<a href="?m=projects&amp;a=view&amp;project_id=' . dPformSafe($row['project_id']) . '">' 
		       . htmlspecialchars($row['project_name']) . '</a>');
		$s .= ('<td>' . htmlspecialchars($row['contact_first_name']) . '&nbsp;' 
		       . htmlspecialchars($row['contact_last_name']) . '</td>');
		$s .= '</tr>';
	}
}
echo ('<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tbl">' 
      . $s . '</table>');

?>

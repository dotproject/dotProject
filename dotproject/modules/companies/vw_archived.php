<?php /* COMPANIES $Id$ */
##
##	Companies: View Archived Projects sub-table
##
GLOBAL $AppUI, $company_id; 

$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, project_name, project_start_date, project_status, project_target_budget,
	project_start_date,
        project_priority,
	contact_first_name, contact_last_name');
$q->addJoin('users', 'u', 'u.user_id = projects.project_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('projects.project_company = '.$company_id);
$q->addWhere('projects.project_status = 7');
$q->addOrder('project_name');
$s = '';

if (!($rows = $q->loadList())) {
	$s .= $AppUI->_( 'No data available' ).'<br />'.$AppUI->getMsg();
} else {
	$s .= '<tr>'
		.'<th>'.$AppUI->_( 'Name' ).'</td>'
		.'<th>'.$AppUI->_( 'Owner' ).'</td>'
		.'</tr>';

	foreach ($rows as $row){
		$s .= '<tr><td>';
		$s .= '<a href="?m=projects&a=view&project_id='.$row["project_id"].'">'.$row["project_name"].'</a>';
		$s .= '<td>'.$row["contact_first_name"].'&nbsp;'.$row["contact_last_name"].'</td>';
		$s .= '</tr>';
	}
}
echo '<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tbl">' . $s . '</table>';

?>

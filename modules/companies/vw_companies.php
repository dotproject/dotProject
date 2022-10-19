<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $search_string, $owner_filter_id, $type_filter, $orderby, $orderdir;
global $currentTabId, $currentTabName, $tabbed;

// load the company types
$types = dPgetSysVal('CompanyType');
// get any records denied from viewing

$obj = new CCompany();
$allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name');

$company_type_filter = $currentTabId;
//Not Defined
$companiesType = true;
if ($currentTabName == 'All Companies') {
	$companiesType = false;
}
if ($currentTabName == 'Not Applicable') {
	$company_type_filter = 0;
}

// retrieve list of records
$q  = new DBQuery;
$q->addTable('companies', 'c');
$q->addQuery('c.company_id, c.company_name, c.company_type, c.company_description'
             . ', count(distinct p.project_id) as countp'
			 . ', count(distinct p2.project_id) as inactive'
             . ', con.contact_first_name, con.contact_last_name');
$q->addJoin('projects', 'p', 'c.company_id = p.project_company AND p.project_status <> 7');
$q->addJoin('users', 'u', 'c.company_owner = u.user_id');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addJoin('projects', 'p2', 'c.company_id = p2.project_company AND p2.project_status = 7');
if (count($allowedCompanies) > 0) {
	$q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')');
}
if ($companiesType) {
	$q->addWhere('c.company_type = ' . $company_type_filter);
}
if ($search_string != '') {
	$q->addWhere("c.company_name LIKE " . $q->quote_sanitised('%' . $search_string . '%') );
}
if ($owner_filter_id > 0) {
	$q->addWhere('c.company_owner = ' . $owner_filter_id);
}
$q->addGroup('c.company_id');
$q->addOrder($orderby . ' ' . $orderdir);
$rows = $q->loadList();
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl" summary="company list">
<tr>
	<td nowrap="nowrap" width="60" align="right">&nbsp;<?php
echo $AppUI->_('sort by'); ?>:&nbsp;</td>
	<th nowrap="nowrap">
		<a href="?m=companies&amp;orderby=company_name" class="hdr"><?php
echo $AppUI->_('Company Name'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&amp;orderby=countp" class="hdr"><?php
echo $AppUI->_('Active Projects'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&amp;orderby=inactive" class="hdr"><?php
echo $AppUI->_('Archived Projects'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&amp;orderby=company_type" class="hdr"><?php echo $AppUI->_('Type'); ?></a>
	</th>
</tr>
<?php
$s = '';

$none = true;
foreach ($rows as $row) {
	$none = false;
	$s .= PHP_EOL . '<tr>';
	$s .= PHP_EOL . '<td>&nbsp;</td>';
	// $s .= ("\n" . '<td><a href="?m=companies&amp;a=view&amp;company_id='
	//        . dPformSafe($row['company_id']) . '" title="'
  //        . striptags(dPformSafe($row['company_description']))  // needed because now we allowed tags in description fields (gwyneth 20210504)
	//        .'">' . htmlspecialchars($row['company_name']) .'</a></td>');
  $s .= (PHP_EOL . '<td><a href="?m=companies&amp;a=view&amp;company_id='
          . dPformSafe($row['company_id'] ?? 0) . '" onmouseover="return overlib(\''
          . dPformSafe($row['company_description'] ?? 'No description available') . '\', CAPTION, \'' . $AppUI->_('Description')
          . '\', CENTER' . dPgetConfig('overlib_extra_parameters', ', DONOTHING')
          . ');" onmouseout="nd();">' . htmlspecialchars($row['company_name'] ?? '[NO COMPANY]') . '</a></td>');

  dprint(__FILE__, __LINE__, 2, "[DEBUG] " . __FUNCTION__ . ": Extra parameters for overLib: '" . dPgetConfig('overlib_extra_parameters', '[empty]') . "'");

	$s .= (PHP_EOL . '<td width="125" align="center" nowrap="nowrap">' . $row['countp'] . '</td>');
	$s .= (PHP_EOL . '<td width="125" align="center" nowrap="nowrap">' . ($row['inactive'] ?? '') . '</td>');
	$s .= (PHP_EOL . '<td width="125" align="center" nowrap="nowrap">'
	       . $AppUI->_($types[@$row['company_type']]) . '</td>');
	$s .= PHP_EOL . '</tr>';
}
echo $s . PHP_EOL;
if ($none) {
	echo PHP_EOL . '<tr><td colspan="5">' . $AppUI->_('No companies available') . '</td></tr>';
}
?>
</table>

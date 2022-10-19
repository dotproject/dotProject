<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

// First order check if we are allowed to view
if (!$canAccess) {
	$AppUI->redirect('m=public&a=access_denied');
}
$AppUI->savePlace();

$valid_ordering = array('company_name', 'countp', 'inactive', 'company_type');

// retrieve any state parameters
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
	$orderdir = (($AppUI->getState('CompIdxOrderDir')
				  ? (($AppUI->getState('CompIdxOrderDir') == 'asc') ? 'desc' : 'asc') : 'desc'));
	$AppUI->setState('CompIdxOrderBy', $_GET['orderby']);
    $AppUI->setState('CompIdxOrderDir', $orderdir);
}
$orderby = (($AppUI->getState('CompIdxOrderBy'))
            ? $AppUI->getState('CompIdxOrderBy') : 'company_name');
$orderdir = (($AppUI->getState('CompIdxOrderDir')) ? $AppUI->getState('CompIdxOrderDir') : 'asc');

$owner_filter_id = intval(dPgetParam($_REQUEST, 'owner_filter_id', 0));
if (!empty($owner_filter_id) && $owner_filter_id !== 0) {
	$AppUI->setState('owner_filter_id', $owner_filter_id);  // was $owner_filter_id_pre, which isn't defined anywhere! (gwyneth 20210430)
} else {
	$owner_filter_id = $AppUI->getState('owner_filter_id', $AppUI->user_id);
}
// load the company types
$types = dPgetSysVal('CompanyType');

// get any records denied from viewing
$obj = new CCompany();
$deny = $obj->getDeniedRecords($AppUI->user_id);

// Company search by Kist
$search_string = dPgetCleanParam($_REQUEST, 'search_string', '');
if ($search_string != '') {
	$search_string = (($search_string == '-1') ? '' : $search_string);
	$AppUI->setState('search_string', $search_string);
} else {
	$search_string = $AppUI->getState('search_string');
}

//$canEdit = getPermission($m, 'edit');
// retrieve list of records
$search_string = $AppUI->___($search_string);

$perms =& $AppUI->acl();
$owner_list = array(-1 => $AppUI->_('All', UI_OUTPUT_RAW)) + $perms->getPermittedUsers('companies');
//db_loadHashList($sql);
$owner_combo = arraySelect($owner_list, 'owner_filter_id',
                           'class="text" onchange="javascript:document.searchform.submit()"',
                           $owner_filter_id, false);

// setup the title block
$titleBlock = new CTitleBlock('Companies', 'handshake.png', $m, $m . "." . $a);
$titleBlock->addCell(('<form name="searchform" action="?m=companies&amp;search_string='
                      . dPformSafe($search_string) . '" method="post">' . PHP_EOL
                      . '<table><tr><td><strong>' . $AppUI->_('Search')
                      . '</strong><input autofocus class="text" type="search" name="search_string" value="'
                      .  dPformSafe($search_string) . '" /><br />'
                      . '<a href="index.php?m=companies&amp;search_string=-1">'
                      . $AppUI->_('Reset search') . '</a></td><td valign="top"><strong>'
                      . $AppUI->_('Owner filter') . '</strong> ' . $owner_combo
                      . ' </td></tr></table></form>'));

$search_string = addslashes($search_string);

if ($canEdit) {
	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new company')
	                      . '">'), '', '<form action="?m=companies&amp;a=addedit" method="post">',
	                     '</form>');
}
$titleBlock->show();

if (isset($_GET['tab'])) {
	$AppUI->setState('CompaniesIdxTab', $_GET['tab']);
}
$companiesTypeTab = defVal($AppUI->getState('CompaniesIdxTab'),  0);

//$tabTypes = array(getCompanyTypeID('Client'), getCompanyTypeID('Supplier'), 0);
$companiesType = $companiesTypeTab;

$tabBox = new CTabBox('?m=companies', (DP_BASE_DIR . '/modules/companies/'), $companiesTypeTab);
if ($tabbed = $tabBox->isTabbed()) {
	$add_na = true;
	if (isset($types[0])) { // They have a Not Applicable entry.
		$add_na = false;
		$types[] = $types[0];
	}
	$types[0] = 'All Companies';
	if ($add_na) {
		$types[] = 'Not Applicable';
	}
}
$type_filter = array();
foreach ($types as $type => $type_name) {
	$type_filter[] = $type;
	$tabBox->add('vw_companies', $type_name);
}

$tabBox->show();
?>

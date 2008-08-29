<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

require_once( $AppUI->getModuleClass( 'companies' ) );
GLOBAL $dPconfig, $canEdit, $stub, $where, $orderby;

$q = new DBQuery;
$q->addTable('users', 'u');
$q->addQuery('DISTINCT(user_id), user_username, contact_last_name, contact_first_name,
	permission_user, contact_email, company_name, contact_company');
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addJoin('permissions', 'per', 'user_id = permission_user');

$obj = new CCompany();
$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
if (count($companies) > 0) {
    $companyList = '0';
    foreach($companies as $k => $v) {
    	$companyList .= ', '.$k;
    }
    $q->addWhere('user_company in (' . $companyList . ')'); 
}

if ($stub) {
	$q->addWhere("(UPPER(user_username) LIKE '$stub%'" 
				 . " OR UPPER(contact_first_name) LIKE '$stub%'" 
				 . " OR UPPER(contact_last_name) LIKE '$stub%')");
} else if ($where) {
	$where = $q->quote("%$where%");
	$q->addWhere("(UPPER(user_username) LIKE $where" 
				 . " OR UPPER(contact_first_name) LIKE $where" 
				 . " OR UPPER(contact_last_name) LIKE $where)");
}

$q->addOrder($orderby);
$users = $q->loadList();
$canLogin = false;

require DP_BASE_DIR . '/modules/admin/vw_usr.php';
?>

<?php

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly. Instead, run the Installer in install/index.php.');
}
/**
* This script iterates all contacts and verify if the contact_company 
* field has a text value; if it does, it searches of the company in the 
* companies table, if it finds it then the contact is related to it by its id. 
* If it doesn't find it, the it creates the company (only the name) and then it 
* relates it to the contact using the new company's id.
*/

dPmsg('Fetching companies list');
$q = new DBQuery;
$q->addTable('contacts');
$q->addQuery('*');
$sql = $q->prepare(true);
foreach (db_loadList($sql) as $contact) {
    $contact_company = $contact['contact_company'];
    if (is_numeric($contact_company)) {
        if (!checkCompanyId($contact_company)) {
            dPmsg('Error found in contact_company in the contact '.getContactGeneralInformation($contact));
        }
    } else if ($contact_company != "") {
        $company_id = fetchCompanyId($contact_company);
        // If we find company_id
        
        if (!$company_id) {
            // We need to create the new company
            $company_id = insertCompany($contact_company);
        }
        
        if ($company_id) {
            updateContactCompany($contact, $company_id);
            dPmsg("Contact's company updated - ".getContactGeneralInformation($contact)." - ($company_id) $contact_company");
        } else {
            dPmsg("Unable to update contact's company - ".getContactGeneralInformation($contact));
        }
    }
}


function updateContactCompany($contact_array, $company_id) {
	$q = new DBQuery;
	$q->addTable('contacts');
	$q->addUpdate('contact_company = ' . $company_id);
	$q->addWhere('contact_id = '.$contact_array['contact_id']);
    db_exec($q->prepareUpdate());
}

function getContactGeneralInformation($contact_array) {
    $contact_info  = '('.$contact_array['contact_id'].') ';
    $contact_info .= $contact_array['contact_first_name'].' '.$contact_array['contact_last_name'];
    return $contact_info;
}

function fetchCompanyId($company_name) {
	$q = new DBQuery;
	$q->addTable('companies');
	$q->addQuery('company_id');
	$q->addWhere("company_name = '$company_name'");
    return db_loadResult( $q->prepare() );
}

function checkCompanyId($company_id) {
	$q = new DBQuery;
	$q->addTable('companies');
	$q->addQuery('count(*)');
	$q->addWhere("company_id = '$company_id'");
    return db_loadResult( $q->prepare() );
}

function insertCompany($company_name) {
	$q = new DBQuery;
	$q->addTable("companies");
	$q->addInsert('company_name',$company_name);
    db_exec( $q->prepareInsert() );
    return db_insert_id();
}

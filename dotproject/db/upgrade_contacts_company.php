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
foreach(db_loadList('SELECT * FROM contacts') as $contact) {
    $contact_company = $contact['contact_company'];
    if (is_numeric($contact_company)){
        if(!checkCompanyId($contact_company)){
            dPmsg('Error found in contact_company in the contact '.getContactGeneralInformation($contact));
        }
    } else if ($contact_company != "") {
        $company_id = fetchCompanyId($contact_company);
        // If we find company_id
        
        if (!$company_id) {
            // We need to create the new company
            $company_id = insertCompany($contact_company);
        }
        
        if($company_id){
            updateContactCompany($contact, $company_id);
            dPmsg("Contact's company updated - ".getContactGeneralInformation($contact)." - ($company_id) $contact_company");
        } else {
            dPmsg("Unable to update contact's company - ".getContactGeneralInformation($contact));
        }
    }
}


function updateContactCompany($contact_array, $company_id) {
    $sql = 'UPDATE contacts SET contact_company = ' . $company_id 
	  . ' WHERE contact_id = '.$contact_array['contact_id'];
    db_exec($sql);
}

function getContactGeneralInformation($contact_array) {
    $contact_info  = '('.$contact_array['contact_id'].') ';
    $contact_info .= $contact_array['contact_first_name'].' '.$contact_array['contact_last_name';
    return $contact_info;
}

function fetchCompanyId($company_name) {
    return db_loadResult("SELECT company_id FROM companies WHERE company_name = '$company_name'");
}

function checkCompanyId($company_id) {
    return db_loadResult("SELECT count(*) FROM companies WHERE company_id = '$company_id'");
}

function insertCompany($company_name) {
    $sql = "INSERT INTO companies (company_name) VALUES ('$company_name')";
    db_exec($sql);
    return db_insert_id();
}

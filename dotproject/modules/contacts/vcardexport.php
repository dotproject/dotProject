<?php
// get GETPARAMETER for contact_id
$contact_id = intval( $_GET['contact_id']);

$canRead = !getDenyRead( 'contacts' );
if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

if ( isset($_GET['contact_id']) && !($_GET['contact_id']=='') ) {

	//pull data for this contact
	$q  = new DBQuery;
	$q->addTable('contacts');
	$q->addJoin('companies', 'cp', 'cp.company_id = contact_id');
	$q->addWhere("contact_id = $contact_id");
	$contacts = $q->loadList();

	//foreach ($contacts as $row) {
	//echo $row['contact_id'];
	//}


	// include PEAR vCard class
	require_once( $AppUI->getLibraryClass( 'PEAR/Contact_Vcard_Build' ) );

	// instantiate a builder object
	// (defaults to version 3.0)
	$vcard = new Contact_Vcard_Build();

	// set a formatted name
	$vcard->setFormattedName($contacts[0]['contact_first_name'].' '.$contacts[0]['contact_last_name']);

	// set the structured name parts
	$vcard->setName($contacts[0]['contact_last_name'], $contacts[0]['contact_first_name'], $contacts[0]['contact_type'],
		$contacts[0]['contact_title'], '');

	// set the source of the vCard
	$vcard->setSource($dPconfig['company_name'].' '.$dPconfig['page_title'].': '.$dPconfig['site_domain']);

	// set the birthday of the contact
	$vcard->setBirthday($contacts[0]['contact_birthday']);

	// set a note of the contact
	$contacts[0]['contact_notes'] = str_replace("\r", " ", $contacts[0]['contact_notes'] );
	$vcard->setNote($contacts[0]['contact_notes']);

	// add an organization
	$vcard->addOrganization($contacts[0]['company_name']);

	// add dp company id
	$vcard->setUniqueID($contacts[0]['contact_company']);

	// add a phone number
	$vcard->addTelephone($contacts[0]['contact_phone']);
	$vcard->addParam('TYPE', 'PF');

	// add a phone number
	$vcard->addTelephone($contacts[0]['contact_phone2']);

	// add a mobile phone number
	$vcard->addTelephone($contacts[0]['contact_mobile']);
	$vcard->addParam('TYPE', 'car');

	// add a work email.  note that we add the value
	// first and the param after -- Contact_Vcard_Build
	// is smart enough to add the param in the correct
	// place.
	$vcard->addEmail($contacts[0]['contact_email']);
	//$vcard->addParam('TYPE', 'WORK');
	$vcard->addParam('TYPE', 'PF');

	// add a home/preferred email
	$vcard->addEmail($contacts[0]['contact_email2']);
	//$vcard->addParam('TYPE', 'HOME');

	// add an address
	$vcard->addAddress('', $contacts[0]['contact_address2'], $contacts[0]['contact_address1'],
		$contacts[0]['contact_city'], $contacts[0]['contact_state'], $contacts[0]['contact_zip'], $contacts[0]['contact_country']);
	//$vcard->addParam('TYPE', 'WORK');


	// get back the vCard
	$text = $vcard->fetch();

	//send http-output with this vCard

	// BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
	// [http://bugs.php.net/bug.php?id=16173]
		header("Pragma: ");
		header("Cache-Control: ");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
	// END extra headers to resolve IE caching bug

	header("MIME-Version: 1.0");
	header("Content-Type: text/x-vcard");
	header("Content-Disposition: attachment; filename={$contacts[0]['contact_last_name']}{$contacts[0]['contact_first_name']}.vcf");
	print_r($text);
} else {
$AppUI->setMsg( "contactIdError", UI_MSG_ERROR );
	$AppUI->redirect();
}
?>

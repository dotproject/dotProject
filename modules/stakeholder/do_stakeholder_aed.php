<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//addinitiating sql
$initiating_id = intval(dPgetParam($_POST, 'initiating_stakeholder_id', 0));
$projectId=$_POST["project_id"];
$del = intval(dPgetParam($_POST, 'del', 0));
global $db;

$not = dPgetParam($_POST, 'notify', '0');
if ($not!='0') $not='1';

$obj = new CStakeholder();
$obj->stakeholder_strategy = dPgetParam($_POST, 'strategy');

if ($initiating_stakeholder_id) { 
	$obj->_message = 'updated';
} else {
	$obj->_message = 'added';
}

//get contact id
require_once (DP_BASE_DIR . "/modules/contacts/contacts.class.php"); //for contact creation
$userFirstName = dPgetParam($_POST, "first_name", 0);
$userLastName = dPgetParam($_POST, "last_name", 0);
//pull a list of existing usernames
$q = new DBQuery();
$q->addTable('contacts', 'c');
$q->addQuery('contact_id');
$q->addWhere("contact_first_name= '{$userFirstName}' and contact_last_name='{$userLastName}'");
$sql = $q->prepare();
$contactId = -1;
$contacts = db_loadList($sql);
foreach($contacts as $contact){
    $contactId = $contact[0];
}
// If userName already exists quit with error and do nothing


if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&targetScreenOnProject=/modules/stakeholder/project_stakeholder.php");
}

// delete the item
if ($del) {
	$obj->load($initiating_stakeholder_id);
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		if ($not=='1') $obj->notify();
		$AppUI->setMsg($AppUI->_("LBL_STAKEHOLDER_EXCLUDED"), UI_MSG_ALERT, true);
		$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&targetScreenOnProject=/modules/stakeholder/project_stakeholder.php");
	}
}

if ($contactId<=1) {
    $contactObj = new CContact();
    $contactObj->contact_last_name = $userLastName;
    $contactObj->contact_first_name = $userFirstName;
    $contactObj->contact_id = 0;
    $contactObj->store();
    $contactId = $contactObj->contact_id;
}else{
    //update the stakeholder name when it is edited after created
    $contactObj = new CContact();
    $contactObj->load($contactId);
    $contactObj->contact_last_name = $userLastName;
    $contactObj->contact_first_name = $userFirstName;
    $contactObj->store();
} 
$obj->contact_id=$contactId;
if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
        $obj->load($obj->initiating_stakeholder_id);
        if ($not=='1') $obj->notify();
        $AppUI->setMsg($AppUI->_("LBL_STAKEHOLDER_INCLUDED"), UI_MSG_OK, true);
}

$AppUI->redirect("a=view&m=projects&project_id=".$projectId."&tab=1&targetScreenOnProject=/modules/stakeholder/project_stakeholder.php");
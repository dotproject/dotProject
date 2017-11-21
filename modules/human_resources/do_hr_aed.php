<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

$del = dPgetParam($_POST, "del", 0);
$obj = new CHumanResource();
$msg = "";
$roles_ids = dPgetParam($_POST, "roles_ids", 0);
$companyId = dPgetParam($_POST, "company_id", 0);
$redirectPath = "m=companies&a=view&company_id=" . $companyId;

if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect($redirectPath);
}

if ($del) {
    require_once (DP_BASE_DIR . "/modules/contacts/contacts.class.php"); //for contact
    require_once (DP_BASE_DIR . "/modules/admin/admin.class.php"); //for user
    $contactObj = new CContact();
    $contactObj->load(dPgetParam($_POST, "contact_id", 0));
    $userObj = new CUser();
    $userObj->load(dPgetParam($_POST, "user_id", 0));
    $userObj->delete();
    $contactObj->delete();
    //delete roles estimated
    $human_resource_roles = new CHumanResourceRoles();
    $human_resource_roles->deleteAll($obj->human_resource_id);
    //delete human resource object
    $obj->delete();
    $msg = $AppUI->_("Human Resource") . " " . $AppUI->_("deleted");
    $AppUI->setMsg($msg, UI_MSG_OK, false);
    $AppUI->redirect($redirectPath);
} else {
    if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
    } else {
        if ($roles_ids || $roles_ids == "") {
            $human_resource_roles = new CHumanResourceRoles();
            $roles_ids_array = explode(",", $roles_ids);
            $human_resource_roles->deleteAll($obj->human_resource_id);
            foreach ($roles_ids_array as $role_id) {
                $human_resource_roles->store($role_id, $obj->human_resource_id);
            }
        }
        $msg = $AppUI->_("Human Resource") . " " . $AppUI->_($_POST["human_resource_id"] ? "updated" : "added");
        $AppUI->setMsg($msg, UI_MSG_OK, true);
    }
    $AppUI->redirect($redirectPath);
}
?>

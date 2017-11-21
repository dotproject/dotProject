<?php

/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$req_categ_name = dPgetParam($_POST, 'req_categ_name', 0);
$req_categ_prefix_id = dPgetParam($_POST, 'req_categ_prefix_id', 0);
$req_categ_description = dPgetParam($_POST, 'req_categ_description', 0);
$obj = new CScopeRequirementCategories();
$msg = '';

if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
    //$AppUI->redirect('m=scopeplanning&a=addedit_requirement');
}

//message's suffix showed on the top os the window after the ssbmit
$AppUI->setMsg('LBL_SP_REQCATEGORY');

if (($msg = $obj->store($req_categ_prefix_id, $req_categ_name, $req_categ_description))) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
    //message showed on the top os the window after the ssbmit
    $AppUI->setMsg($_POST['req_categ_prefix_id'] ? 'LBL_SP_UPDATED' : 'LBL_SP_ADDED1', UI_MSG_OK, true);
}
$AppUI->redirect();
?>

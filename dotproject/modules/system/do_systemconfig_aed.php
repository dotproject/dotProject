<?php /* SYSTEM $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$obj = new CConfig();

// set all checkboxes to false
// overwrite the true/enabled/checked checkboxes later
$sql = "UPDATE config SET config_value='false' WHERE config_type='checkbox'";
$rs = db_loadResult($sql);

foreach ($_POST['dPcfg'] as $name => $value) {
	$obj->config_name = $name;
	$obj->config_value = $value;

	// grab the appropriate id for the object in order to ensure
	// that the db is updated well (config_name must be unique)
	$obj->config_id = $_POST['dPcfgId'][$name];

	// prepare (and translate) the module name ready for the suffix
	$AppUI->setMsg('System Configuration');
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$AppUI->setMsg('updated', UI_MSG_OK, true);
	}
}
$AppUI->redirect();
?>
<?php /* SYSTEM $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

##
## Activate or move a module entry
##
$cmd = dPgetCleanParam($_GET, 'cmd', '0');
$mod_id = intval(dPgetParam($_GET, 'mod_id', '0'));
$mod_directory = dPgetCleanParam($_GET, 'mod_directory', '0');

$obj = new CModule();
if ($mod_id) {
	$obj->load($mod_id);
} else {
	$obj->mod_directory = $mod_directory;
}

$ok = @include_once(DP_BASE_DIR.'/modules/'.$obj->mod_directory.'/setup.php');

if (!$ok) {
	if ($obj->mod_type != 'core') {
		$AppUI->setMsg('Module setup file could not be found', UI_MSG_ERROR);
                if ($cmd == 'remove')
                {
			$q = new DBQuery;
			$q->setDelete('modules');
			$q->addWhere('mod_id='.(int)$mod_id);
                        $q->exec();
                        echo db_error();
                        $AppUI->setMsg('Module has been removed from the modules list - please check your database for additional tables that may need to be removed', UI_MSG_ERROR);
                }       
		$AppUI->redirect();
	}
}
$setupclass = $config['mod_setup_class'];
if (! $setupclass) {
  if ($obj->mod_type != 'core') {
    $AppUI->setMsg('Module does not have a valid setup class defined', UI_MSG_ERROR);
    $AppUI->redirect();
  }
}
else
  $setup = new $setupclass();

switch ($cmd) {
	case 'moveup':
	case 'movedn':
		$obj->move($cmd);
		$AppUI->setMsg('Module re-ordered', UI_MSG_OK);
		break;
	case 'toggle':
	// just toggle the active state of the table entry
		$obj->mod_active = 1 - $obj->mod_active;
		$obj->store();
		$AppUI->setMsg('Module state changed', UI_MSG_OK);
		break;
	case 'toggleMenu':
	// just toggle the active state of the table entry
		$obj->mod_ui_active = 1 - $obj->mod_ui_active;
		$obj->store();
		$AppUI->setMsg('Module menu state changed', UI_MSG_OK);
		break;
	case 'install':
	// do the module specific stuff
		$AppUI->setMsg($setup->install());
		$obj->bind($config);
	// add to the installed modules table
		$obj->install();
		$AppUI->setMsg('Module installed', UI_MSG_OK, true);
		break;
	case 'remove':
	// do the module specific stuff
		$AppUI->setMsg($setup->remove());
	// remove from the installed modules table
		$obj->remove();
		$AppUI->setMsg('Module removed', UI_MSG_ALERT, true);
		break;
	case 'upgrade':
		if ($setup->upgrade($obj->mod_version))	// returns true if upgrade succeeded
		{
			$obj->bind($config);
			$obj->store();
			$AppUI->setMsg('Module upgraded', UI_MSG_OK);
		}
		else
		{
			$AppUI->setMsg('Module not upgraded (upgrade failed from '.$obj->mod_version.' to '.$config['mod_version'].')', UI_MSG_ERROR);
		}
		break;
	case 'configure':
		if ($setup->configure()) 	//returns true if configure succeeded
		{
		}
		else {
			$AppUI->setMsg('Module configuration failed', UI_MSG_ERROR);
		}
		break;
	default:
		$AppUI->setMsg('Unknown Command', UI_MSG_ERROR);
		break;
}
$AppUI->redirect();
?>

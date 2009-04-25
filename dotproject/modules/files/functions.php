<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/*
// This functions is now obsolete (obsoleted by getFolderSelectList() in files.class.php)
function check_perm(&$var) {
	global $m;
	if ($var[0] == 0) {
		return true;	
	}
	// if folder can be edited, keep in array
	if (getPermission($m, 'edit', $var['file_folder_id'])) {
		if (!(getPermission($m, 'edit', $var['file_folder_parent']))) {
			$var[2] = 0;
			$var['file_folder_parent'] = 0;
		}
		return true;
	} else {
		return false;	
	}
}

// This functions is now obsolete (obsoleted by getFolderSelectList() in files.class.php)
function getFolderSelectList() {
	global $AppUI;
	$folders = array(0 => '');
      $q = new DBQuery();
	$q->addTable('file_folders');
	$q->addQuery('file_folder_id, file_folder_name, file_folder_parent');
	$q->addOrder('file_folder_name');
	$sql = $q->prepare();
//	$sql = "SELECT file_folder_id, file_folder_name, file_folder_parent FROM file_folders";
	$vfolders = arrayMerge(array('0'=>array(0, $AppUI->_('Root'), -1)), db_loadHashList($sql, 'file_folder_id'));
	$folders = array_filter($vfolders, "check_perm");
	return $folders;
}
*/
?>

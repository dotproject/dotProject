<?php // check access to files module
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI, $m, $obj, $task_id, $dPconfig;
if (getPermission('files', 'view')) {
	if (getPermission('files', 'edit')) { 
		echo ('<a href="./index.php?m=files&a=addedit&project_id=' . $obj->task_project 
		      . '&file_task=' . $task_id . '">' . $AppUI->_('Attach a file') . '</a>');
	}
	echo dPshowImage(dPfindImage('stock_attach-16.png', $m), 16, 16, ''); 
	$showProject=false;
	$project_id = $obj->task_project;
	include(DP_BASE_DIR . '/modules/files/index_table.php');
}
?>

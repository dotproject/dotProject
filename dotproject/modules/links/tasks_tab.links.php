<?php // check access to files module
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}

global $AppUI, $m, $obj, $task_id;
if (getPermission('links', 'view')) {
	if (getPermission('links', 'edit')) { 
		echo ('<a href="./index.php?m=links&amp;a=addedit&amp;project_id=' . $obj->task_project 
		      . '&amp;link_task=' . $task_id . '">' . $AppUI->_('Attach a link') . '</a>');
	}
	echo dPshowImage(dPfindImage('stock_attach-16.png', $m), 16, 16, ''); 
	$showProject=false;
	$project_id = $obj->task_project;
	include(DP_BASE_DIR . '/modules/links/index_table.php');
}
?>

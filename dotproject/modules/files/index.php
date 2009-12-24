<?php /* FILES $Id$ */

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

// "File" filters info
$AppUI->setState('FileIdxTab', dPgetParam($_GET, 'tab'));
$tab = $AppUI->getState('FileIdxTab', 0);
$active = intval(!$AppUI->getState('FileIdxTab'));

// to pass to "new file" button
$folder = intval(dPgetParam($_GET, 'folder', 0));


// "Project" filters info
require_once ($AppUI->getModuleClass('projects'));
// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
	$AppUI->setState('FileIdxProject', $_REQUEST['project_id']);
}
$project_id = $AppUI->getState('FileIdxProject', 0);

/*
 * get "Allowed" projects for filter list 
 * ("All" is always allowed when basing permission on projects)
 */
$project = new CProject();
$extra = array('from' => 'files',
               'where' => 'project_id = file_project');
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 
                                        'project_name', null, $extra);
$projects = arrayMerge(array('0'=>$AppUI->_('All', UI_OUTPUT_RAW)), $projects);

// get SQL for allowed projects/tasks and folders
$task = new CTask();
$allowedProjects = $project->getAllowedSQL($AppUI->user_id, 'file_project');
$allowedTasks = $task->getAllowedSQL($AppUI->user_id, 'file_task');

$cfObj = new CFileFolder();
$allowedFolders = $cfObj->getAllowedSQL($AppUI->user_id, 'file_folder');

//get permissions for folder tab
$canAccess_folders = getPermission('file_folders', 'access');


// setup the title block
$titleBlock = new CTitleBlock('Files', 'folder5.png', $m, $m . '.' . $a);
$titleBlock->addCell($AppUI->_('Filter') . ':');
$titleBlock->addCell(arraySelect($projects, 'project_id', 
                                 'onChange="document.pickProject.submit()" size="1" class="text"', 
                                 $project_id), 
                     '', '<form name="pickProject" action="?m=files" method="post">', '</form>');

/*
 * override the file module's $canEdit variable passed from the main index.php 
 * in order to check on file folder permissions
 */
$canAuthor_folders = getPermission('file_folders', 'add');

if ($canAuthor) {
	$titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new file') 
	                     . '">', '', '<form action="?m=files&a=addedit&folder=' . $folder 
	                     . '" method="post">', '</form>');
}
if ($canAuthor_folders) {
	$titleBlock->addCell('<input type="submit" class="button" value="' 
	                     . $AppUI->_('new folder').'">', '', 
	                     '<form action="?m=files&a=addedit_folder" method="post">', '</form>');
}
$titleBlock->show();

$file_types = dPgetSysVal('FileType');

$fts = $file_types;

if ($tab != -1) {
	array_unshift($file_types, 'All Files');
}


$tabBox = new CTabBox('?m=files', DP_BASE_DIR . '/modules/files/', $tab);
$tabbed = $tabBox->isTabbed();
$i = 0;
foreach ($file_types as $file_type) {
	$tabBox->add('index_table', $file_type);
	++$i;
}
if ($canAccess_folders) {
	$tabBox->add('folders_table', 'Folder Explorer');
}
$tabBox->show();

?>

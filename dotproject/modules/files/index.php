<?php /* FILES $Id$ */

if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

$AppUI->savePlace();

// "File" filters info
$AppUI->setState('FileIdxTab', dPgetParam($_GET, 'tab'));
$tab = $AppUI->getState( 'FileIdxTab', 0 );
$active = intval(!$AppUI->getState( 'FileIdxTab' ));

// to pass to "new file" button
$folder = dPgetParam($_GET, 'folder', 0);


// "Project" filters info
require_once ($AppUI->getModuleClass( 'projects' ));
// retrieve any state parameters
if (isset($_REQUEST['project_id'])) {
	$AppUI->setState('FileIdxProject', $_REQUEST['project_id']);
}
$project_id = $AppUI->getState( 'FileIdxProject', 0 );

/*
 * get "Allowed" projects for filter list 
 * ("All" is always allowed when basing permission on projects)
 */
$project = new CProject();
$extra = array('from' => 'files',
               'where' => 'project_id = file_project');
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 
                                        'project_name', null, $extra );
$projects = arrayMerge( array( '0'=>$AppUI->_('All', UI_OUTPUT_RAW) ), $projects );

// get SQL for allowed projects/tasks and folders
$task = new CTask();
$allowedProjects = $project->getAllowedSQL($AppUI->user_id, 'file_project');
$allowedTasks = $task->getAllowedSQL($AppUI->user_id, 'file_task');

$cfObj = new CFileFolder();
$allowedFolders = $cfObj->getAllowedSQL($AppUI->user_id, 'file_folder');

//get permissions for folder tab
$canAccess_folders = getPermission('file_folders', 'access');


// setup the title block
$titleBlock = new CTitleBlock( 'Files', 'folder5.png', $m, $m . '.' . $a );
$titleBlock->addCell($AppUI->_('Filter') . ':' );
$titleBlock->addCell(arraySelect($projects, 'project_id', 
                                 'onChange="document.pickProject.submit()" size="1" class="text"', 
                                 $project_id ), 
                     '', '<form name="pickProject" action="?m=files" method="post">', '</form>');

/*
 * override the file module's $canEdit variable passed from the main index.php 
 * in order to check on file folder permissions
 */
$canAccess_folders = getPermission('file_folders', 'access');

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

if ( $tab != -1 ) {
	array_unshift($file_types, 'All Files');
}


$tabBox = new CTabBox( '?m=files', DP_BASE_DIR . '/modules/files/', $tab );
$tabbed = $tabBox->isTabbed();
$i = 0;
foreach($file_types as $file_type) {
	$q = new DBQuery;
	$q->addQuery('count(file_id)');
	$q->addTable('files', 'f');
	$q->addJoin('projects', 'p', 'p.project_id = file_project');
	$q->addJoin('tasks', 't', 't.task_id = file_task');
	if (count ($allowedProjects)) {
		$q->addWhere('( ( ' . implode(' AND ', $allowedProjects) . ') OR file_project = 0 )');
	}
	if (count ($allowedTasks)) {
		$q->addWhere('( ( ' . implode(' AND ', $allowedTasks) . ') OR file_task = 0 )');
	}
	if (count($allowedFolders)) {
		$q->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR file_folder = 0)');
	}
	if ($catsql) {
		$q->addWhere($catsql);
	}
	if ($company_id) {
		$q->addWhere("project_company = $company_id");
	}
	if ($project_id) {
		$q->addWhere("file_project = $project_id");
	}
	if ($task_id) {
		$q->addWhere("file_task = $task_id");
	}
	$key = array_search($file_type, $fts);
	if ($i>0 || !$tabbed) {
		$q->addWhere('file_category = '.$key);
	}
	if ($project_id>0) {
		$q->addWhere('file_project = '.$project_id);
	}
	$tabBox->add('index_table', $file_type . ' (' . $q->loadResult() .')');
	++$i;
}
if ($canAccess_folders) {
	$tabBox->add('folders_table', 'Folder Explorer');
}
$tabBox->show();

?>
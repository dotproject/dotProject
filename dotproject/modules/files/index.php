<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

$AppUI->savePlace();

// retrieve any state parameters
if (isset( $_REQUEST['project_id'] )) {
	$AppUI->setState( 'FileIdxProject', $_REQUEST['project_id'] );
}

$project_id = $AppUI->getState( 'FileIdxProject', 0 );

$AppUI->setState( 'FileIdxTab', dPgetParam($_GET, 'tab'));
$tab = $AppUI->getState( 'FileIdxTab', 0 );
$active = intval( !$AppUI->getState( 'FileIdxTab' ) );

$view_temp = dPgetParam( $_GET, "view" );
if (isset( $view_temp )) {
	$view = dPgetParam( $_GET, "view");	// folders or categories
	$AppUI->setState( 'FileIdxView', $view );
} else {
	$view = $AppUI->getState( 'FileIdxView' );
	if ($view == "") { $view = "folders"; }	
}
$folder = dPgetParam( $_GET, "folder", 0);		// to pass to "new file" button

require_once( $AppUI->getModuleClass( 'projects' ) );

// get the list of visible companies
$extra = array(
	'from' => 'files',
	'where' => 'project_id = file_project'
);

//get "Allowed" projects
$project = new CProject();
$projects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$allowedProjects = array_keys($projects);
if (count($allowedProjects)) {
  array_push($allowedProjects, 0); //add "All" to allowed projects
}
$projects = arrayMerge( array( '0'=>$AppUI->_('All', UI_OUTPUT_RAW) ), $projects );

// setup the title block
$titleBlock = new CTitleBlock( 'Files', 'folder5.png', $m, "$m.$a" );
$titleBlock->addCell( $AppUI->_('Filter') . ':' );
$titleBlock->addCell(
	arraySelect( $projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id ), '',
	'<form name="pickProject" action="?m=files" method="post">', '</form>'
);

// override the $canEdit variable passed from the main index.php in order to check folder permissions
/** get permitted folders **/
   $cfObj = new CFileFolder();
   $allowed_folders_ary = $cfObj->getAllowedRecords($AppUI->user_id);
   $denied_folders_ary = $cfObj->getDeniedRecords($AppUI->user_id);

   if ( count( $allowed_folders_ary ) < $cfObj->countFolders() ) {
	$limited = true;
   }
   if (!$limited) {
	$canEdit = true;
   } elseif ($limited AND array_key_exists($folder, $allowed_folders_ary)) {
	$canEdit = true;
   } else {
	$canEdit = false;
   }

if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
		'<form action="?m=files&a=addedit&folder=' . $folder . '" method="post">', '</form>'
	);
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new folder').'">', '',
		'<form action="?m=files&a=addedit_folder" method="post">', '</form>'
	);
}
$titleBlock->show();

$file_types = dPgetSysVal("FileType");

$fts = $file_types;

if ( $tab != -1 ) {
        array_unshift($file_types, "All Files");
}

//if ($view == "folders") {
	//include('folders_table.php');	
//} else {
	$tabBox = new CTabBox( "?m=files", DP_BASE_DIR.'/modules/files/', $tab );
	$tabbed = $tabBox->isTabbed();
	$i = 0;
	foreach($file_types as $file_type) {
        $q = new DBQuery;      
        $q->addQuery('count(file_id)');      
        $q->addTable('files', 'f');        
        $key = array_search($file_type, $fts);              
        if ($i>0 || !$tabbed) $q->addWhere('file_category = '.$key);        
        if ($project_id>0) $q->addWhere('file_project = '.$project_id);        
        $tabBox->add("index_table", $file_type . ' (' . $q->loadResult() .')');
        ++$i;
	}
    $tabBox->add("folders_table", 'Folder Explorer');
	$tabBox->show();
//}
?>

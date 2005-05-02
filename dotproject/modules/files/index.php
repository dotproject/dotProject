<?php /* FILES $Id$ */
$AppUI->savePlace();

// retrieve any state parameters
if (isset( $_REQUEST['project_id'] )) {
	$AppUI->setState( 'FileIdxProject', $_REQUEST['project_id'] );
}

$project_id = $AppUI->getState( 'FileIdxProject', 0 );

if (dPgetParam($_GET, 'tab', -1) != -1 ) {
        $AppUI->setState( 'FileIdxTab', dPgetParam($_GET, 'tab'));
}
$tab = $AppUI->getState( 'FileIdxTab', 0 );
$active = intval( !$AppUI->getState( 'FileIdxTab' ) );

require_once( $AppUI->getModuleClass( 'projects' ) );

// get the list of visible companies
$extra = array(
	'from' => 'files',
	'where' => 'project_id = file_project'
);

$project = new CProject();
$projects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$allowedProjects = array_keys($projects);
$projects = arrayMerge( array( '0'=>$AppUI->_('All', UI_OUTPUT_RAW) ), $projects );

// setup the title block
$titleBlock = new CTitleBlock( 'Files', 'folder5.png', $m, "$m.$a" );
$titleBlock->addCell( $AppUI->_('Filter') . ':' );
$titleBlock->addCell(
	arraySelect( $projects, 'project_id', 'onChange="document.pickProject.submit()" size="1" class="text"', $project_id ), '',
	'<form name="pickProject" action="?m=files" method="post">', '</form>'
);
if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
		'<form action="?m=files&a=addedit" method="post">', '</form>'
	);
}
$titleBlock->show();

$file_types = dPgetSysVal("FileType");
if ( $tab != -1 ) {
        array_unshift($file_types, "All Files");
}

$tabBox = new CTabBox( "?m=files", "{$dPconfig['root_dir']}/modules/files/", $tab );

$i = 0;

foreach($file_types as $file_type)
{
        $tabBox->add("index_table", $file_type);
        ++$i;
}
                                                                                
$tabBox->show();

?>

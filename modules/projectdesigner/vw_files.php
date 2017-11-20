<?php /* PROJECTS $Id: vw_files.php,v 1.1 2007/03/15 18:16:42 pedroix Exp $ */
GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig;
require_once( $AppUI->getModuleClass( 'files' ) );
   
$showProject = false;
require( dPgetConfig('root_dir') . '/modules/files/index_table.php' );
?>

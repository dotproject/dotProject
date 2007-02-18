<?php // check access to files module
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

global $AppUI, $m, $obj, $task_id;
if (!getDenyRead( 'links' )) {
        if (!getDenyEdit( 'links' )) { 
                echo '<a href="./index.php?m=links&a=addedit&project_id=' . $obj->task_project . '&link_task=' . $task_id . '">' . $AppUI->_('Attach a link') . '</a>';
                    
        }
        echo dPshowImage( dPfindImage( 'stock_attach-16.png', $m ), 16, 16, '' ); 
        $showProject=false;
        $project_id = $obj->task_project;
        include($dPconfig['root_dir'] . '/modules/links/index_table.php');
}
?>

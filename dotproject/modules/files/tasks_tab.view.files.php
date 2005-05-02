<?php // check access to files module
global $AppUI, $m, $obj, $task_id, $dPconfig;
if (!getDenyRead( 'files' )) {
        if (!getDenyEdit( 'files' )) { 
                echo '<a href="./index.php?m=files&a=addedit&project_id=' . $obj->task_project . '&file_task=' . $task_id . '">' . $AppUI->_('Attach a file') . '</a>';
                    
        }
        echo dPshowImage( dPfindImage( 'stock_attach-16.png', $m ), 16, 16, '' ); 
        $showProject=false;
        $project_id = $obj->task_project;
        include($dPconfig['root_dir'] . '/modules/files/index_table.php');
}
?>

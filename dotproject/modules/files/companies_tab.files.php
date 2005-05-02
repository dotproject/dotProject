<?php // check access to files module
global $AppUI, $m, $company_id, $dPconfig;
if (!getDenyRead( 'files' )) {
        if (!getDenyEdit( 'files' )) { 
                echo '<a href="./index.php?m=files&a=addedit">' . $AppUI->_('Attach a file') . '</a>';
                    
        }
        echo dPshowImage( dPfindImage( 'stock_attach-16.png', $m ), 16, 16, '' ); 
        $showProject=true;
        include($dPconfig['root_dir'] . '/modules/files/index_table.php');
}
?>

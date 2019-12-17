<?php
// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Projects';
$config['mod_version'] = '1.0.0';
$config['mod_directory'] = 'projects';
$config['mod_setup_class'] = 'Projects';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Projects';
$config['mod_ui_icon'] = 'applet3-48.png';
$config['mod_description'] = 'A module for Project management';
$config['mod_config'] = true;
if (@$a == 'setup')
    echo dPshowModuleConfig($config);
class Projects {
    function configure() {
        global $AppUI;
        $AppUI->redirect('m=projects&a=configure');
        return true;
    }
}
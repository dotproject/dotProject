<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dotProject+';
$config['mod_version'] = '1.0';
$config['mod_directory'] = 'dotproject_plus';
$config['mod_setup_class'] = 'CSetup_dotProjectPlus';
$config['mod_type'] = 'user';
$config['mod_config'] = false;
$config['mod_ui_name'] = 'dotProject+';
$config['mod_ui_icon'] = 'applet3-48.png';
$config['mod_description'] = "dotProject+";

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class CSetup_dotProjectPlus {

    function install() {

    }

    function upgrade($version = 'all') {
        return true;
    }

    function configure() {
        return true;
    }

}

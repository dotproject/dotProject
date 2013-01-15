<?php
/* $Id$ */

/* {{{ Copyright (c) 2003-2005 The dotProject Development Team <core-developers@dotproject.net>

    This file is part of dotProject.

    dotProject is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    dotProject is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with dotProject; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
}}} */

ini_set('display_errors', 1);

if (defined('E_DEPRECATED')) {
	error_reporting(E_ALL & ~(E_DEPRECATED|E_NOTICE|E_STRICT));
} else {
	error_reporting(E_ALL & ~E_NOTICE);
}

if (function_exists('date_default_timezone_set')) {
  # this is a bit of a hack in that it will guess from the system what
  # the timezone is.
  date_default_timezone_set(date_default_timezone_get());
}

global $baseDir;
global $baseUrl;

$baseDir = dirname(__FILE__);
//Make sure directoy seperator is at the end so that paths are well formed
//$baseDir .= ((substr_compare($baseDir, DIRECTORY_SEPERATOR, -1 , 1) == 0) ? '' : DIRECTORY_SEPERATOR);
// Define to deprecate the global baseDir
define('DP_BASE_DIR', $baseDir);

require_once ($baseDir . '/includes/dP_compat.php');

// only rely on env variables if not using a apache handler
function safe_get_env($name) 
{
	if (isset($_SERVER[$name])) {
		return $_SERVER[$name];
	} else if (mb_strpos(php_sapi_name(), 'apache') === false) {
		getenv($name);
	} else {
		return '';
	}
}

// automatically define the base url
$baseUrl = (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') 
             || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') 
             ? 'https://' : 'http://');
$baseUrl .= safe_get_env('HTTP_HOST');
$pathInfo = safe_get_env('PATH_INFO');
if (@$pathInfo) {
  $baseUrl .= str_replace('\\','/',dirname($pathInfo));
} else {
  $baseUrl .= str_replace('\\','/', dirname(safe_get_env('SCRIPT_NAME')));
}

$baseUrl = preg_replace('#/$#D', '', $baseUrl);

// Define to deprecate the global baseUrl
define('DP_BASE_URL', $baseUrl);
// And now we need to ensure we have a valid path for included pear libraries.
// This dependency sucks, but if we don't do it we have no idea what versions we have
$dpLib = DP_BASE_DIR.DIRECTORY_SEPARATOR.'lib';
$pear = $dpLib . DIRECTORY_SEPARATOR. 'PEAR';
// If you want a really secure path, comment the second one out and uncomment this
// set_include_path('.'.PATH_SEPARATOR.$dpLib.PATH_SEPARATOR.$pear);
set_include_path('.'.PATH_SEPARATOR.$dpLib.PATH_SEPARATOR.$pear.PATH_SEPARATOR.get_include_path());

// required includes for start-up
global $dPconfig;
$dPconfig = array();


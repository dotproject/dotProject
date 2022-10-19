<?php /* $Id$ */

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
/*
Copyright (c) 2003-2005 The dotProject Development Team <core-developers@dotproject.net>

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

The full text of the GPL is in the COPYING file.
*/

/*
	* * * INSTALLATION INSTRUCTIONS * * *

  Point your browser to install/index.php and follow the prompts.
	It is no longer necessary to manually create this file unless
	the web server cannot write to the includes directory.

*/

// DATABASE ACCESS INFORMATION [DEFAULT example]
// Modify these values to suit your local settings

$dPconfig['dbtype'] = 'mysqli';      // ONLY MySQL is supported at present
$dPconfig['dbhost'] = 'localhost';
$dPconfig['dbname'] = 'dotproject';  // Change to match your DotProject Database Name
$dPconfig['dbprefix'] = 'dotp_';		// Change to match your DotProject Database Table-Name Prefix
$dPconfig['dbuser'] = 'dp_user';  // Change to match your MySQL Username
$dPconfig['dbpass'] = 'dp_pass';  // Change to match your MySQL Password

// set this value to true to use persistent database connections
$dPconfig['dbpersist'] = false;

/*************  XSS FILTERING ****************************
* Don't include these or change the defaults unless you  *
* undestand what this is likely to do. It would be a big *
* mistake, for instance, to allow script tags            *
*********************************************************/
$dPconfig['filter_allowed_tags'] =  array('a', 'em', 'strong', 'cite', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd');
$dPconfig['filter_allowed_protocols'] = array('http', 'https', 'ftp', 'news', 'nntp', 'tel', 'telnet', 'mailto', 'irc', 'ssh', 'sftp', 'webcal', 'rtsp');

/***************** Configuration for DEVELOPERS use only! ******/
// Root directory is now automatically set to avoid
// getting it wrong. It is also deprecated as $baseDir
// is now set in top-level files index.php and fileviewer.php.
// All code should start to use $baseDir instead of root_dir.
$dPconfig['root_dir'] = $baseDir;

// Base Url is now automatically set to avoid
// getting it wrong. It is also deprecated as $baseUrl
// is now set in top-level files index.php and fileviewer.php.
// All code should start to use $baseUrl instead of base_url.
$dPconfig['base_url'] = $baseUrl;

// Extra parameters for overLib - allows styles to override the defaults
// @author Gwyneth Llewelyn (20210430)
$dPconfig['overlib_extra_parameters'] = "";

// Optionally log to a user-defined location
// Take into account that this file needs to be accessible by the web server process, or else
//  all logs may be lost! Note: the destination should also be allowed via `open_basedir` in `php.ini`
// Created because some system configurations (e.g. nginx + PHP-FPM) do weird filtering of messages and
//  buffer them before sending them to syslog; this creates confusing logs
// @author Gwyneth Llewelyn (20210504)
$dPconfig['error_log_file'] = '';
?>

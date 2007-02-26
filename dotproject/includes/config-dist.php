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

$dPconfig['dbtype'] = 'mysql';      // ONLY MySQL is supported at present
$dPconfig['dbhost'] = 'localhost';
$dPconfig['dbname'] = 'dotproject';  // Change to match your DotProject Database Name
$dPconfig['dbuser'] = 'dp_user';  // Change to match your MySQL Username
$dPconfig['dbpass'] = 'dp_pass';  // Change to match your MySQL Password

// set this value to true to use persistent database connections
$dPconfig['dbpersist'] = false;

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
?>

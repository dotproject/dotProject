<?php
/*
All files in this work, except the modules/ticketsmith directory, are now
covered by the following copyright notice.  The ticketsmith module is
under the Voxel Public License.  See modules/ticketsmith/LICENSE
for details.  Please note that included libraries in the lib directory
may have their own license.

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

// Check for upgrade or install.  This is a little tricky as there can be a few
// variations on the theme.  In an ideal situation if there is no config.php then
// it is an install, otherwise it is an upgrade, however nothing is ever that
// simple.  The possibilities are:
// 1. A simple install as describe above, no config.php, no database
// 2. Someone has read that it needs a writable config.php so creates an empty one
// 3. Someone has uploaded the dotproject.sql file and then run the installer
// 4. Combination of 2 and 3
// 5. It is an upgrade - there must be a config.php and a database.

$baseDir = dirname(dirname(__FILE__));
define('DP_BASE_DIR', $baseDir);

require_once 'install.inc.php';
require_once DP_BASE_DIR.'/lib/adodb/adodb.inc.php';

function dPcheckExistingDB($conf) {
	global $AppUI, $ADODB_FETCH_MODE;
	$AppUI = new InstallerUI;
	
	$ado = @NewADOConnection($conf['dbtype'] ? $conf['dbtype'] : 'mysql');
	if (empty($ado))
		return false;
	$db = @$ado->Connect($conf['dbhost'], $conf['dbuser'], $conf['dbpass']);
	if (! $db)
		return false;
	$exists = @$ado->SelectDB($conf['dbname']);
	if (! $exists)
		return false;

	// Find the tables in the database, if there are none, or if the
	// basic tables of project and task are missing, we are doing an
	// install.
	$table_list = $ado->MetaTables('TABLE');
	if (count($table_list) < 10 ) {
		// There are now more than 60 tables in a standard dP
		// install, but this will at least cover the basics.
		return false;
	}

	// Check the table list for the standard tables.  Firstly
	// we check for sysvals and tasks, and see if there is a common
	// prefix.
	$found = false;
	foreach ($table_list as $tbl) {
		if (substr($tbl, -7) == 'sysvals') {
			$prefix = str_replace('sysvals', '', $tbl);
			$found = true;
			break;
		}
	}
	if (! $found) {
		return false; //Couldn't even find the projects table!
	}
	if (!in_array($prefix . 'tasks', $table_list)) {
		return false; // Must have both tasks and projects.
		// we could go further but it is likely that if these
		// exist then we can safely upgrade.
	}


	// Now we make a check to see if the dotproject.sql has been loaded
	// prior to the installer being run.  This needs to rely on the
	// fact that the GACL tables will exist but will be unpopulated.
	// The install procedure populates them - If this situation changes
	// then this code must be modified to suit.

	$q1 = 'SELECT count(*) from gacl_phpgacl'; // Should be 2
	$q2 = 'SELECT count(*) from gacl_axo'; // Should be greater than the count of modules

	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

	// Note the sense of this test.  If the tables don't exist, it is by default an
	// upgrade (mainly because these only exist in the 2.x and later series). 
	// If one exists and is populated (the version information is seeded by the sql file)
	// but the other either doesn't exist or is unpopulated, then it is an install as
	// the SQL file has been loaded manually.
	if (($qid = @$ado->Execute($q1) ) && ($q1Data = @$qid->fetchRow() ) && ! empty($q1Data[0]) ) {
		@$qid->Close();
		if ( ! ($qid2 = @$ado->Execute($q2) ) || ! ($q2Data = @$qid2->fetchRow() ) || empty($q2Data[0]) ) {
			return false;
		}
		@$qid2->Close();
	}

	return true;
}

function dPcheckUpgrade() {
	$mode = 'install';
	if (is_file('../includes/config.php')) {
		include_once '../includes/config.php';
		if (isset($dPconfig['dbhost'])) {
			if (dPcheckExistingDB($dPconfig)) {
				$mode = 'upgrade';
			}
		}
	}
	return $mode;
}
?>

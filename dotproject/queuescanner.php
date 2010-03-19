<?php
	// $Id$

/*
All files in this work, except the modules/ticketsmith directory, are now
covered by the following copyright notice.  The ticketsmith module is
under the Voxel Public License.  See modules/ticketsmith/LICENSE
for details. Please note that included libraries in lib may have their
own license.

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

	// Function to scan the event queue and execute any functions required.

	require_once 'base.php';
	require_once DP_BASE_DIR.'/includes/config.php';
	require_once DP_BASE_DIR.'/includes/main_functions.php';
	require_once DP_BASE_DIR.'/includes/db_connect.php';
	require_once DP_BASE_DIR.'/classes/ui.class.php';
	require_once DP_BASE_DIR.'/classes/event_queue.class.php';
	require_once DP_BASE_DIR.'/classes/query.class.php';

	$AppUI = new CAppUI;
	$AppUI->setUserLocale();
	$perms =& $AppUI->acl();

	echo "Scanning Queue ...\n";
	$queue = new EventQueue;
	$queue->scan();
	echo 'Done, '.$queue->event_count.' events processed'."\n";

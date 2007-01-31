<?php
if (! defined('DP_BASE_DIR')) {
	die("You must not call this file directly, it is run automatically on install/upgrade");
}
include_once DP_BASE_DIR."/includes/config.php";
include_once DP_BASE_DIR."/includes/main_functions.php";
require_once DP_BASE_DIR."/includes/db_adodb.php";
include_once DP_BASE_DIR."/includes/db_connect.php";
include_once DP_BASE_DIR."/install/install.inc.php";
require_once DP_BASE_DIR."/classes/permissions.class.php";

/**
 * DEVELOPERS PLEASE NOTE:
 *
 * For the new upgrader/installer to work, this code must be structured
 * correctly.  In general if there is a difference between the from
 * version and the to version, then all updates should be performed.
 * If the $last_udpated is set, then a partial update is required as this
 * is a CVS update.  Make sure you create a new case block for any updates
 * that you require, and set $latest_update to the date of the change.
 *
 * Each case statement should fall through to the next, so that the
 * complete update is run if the last_updated is not set.
 */
function dPupgrade($from_version, $to_version, $last_updated)
{

	$latest_update = '20060421'; // Set to the latest upgrade date.

	if (! $last_updated)
		$last_updated = '00000000';
	
	// Place the upgrade code here, depending on the last_updated date.
	return $latest_update;
}

?>

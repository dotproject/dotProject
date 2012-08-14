<?php

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly. Instead, run the Installer in install/index.php.');
}
include_once DP_BASE_DIR.'/includes/config.php';
include_once DP_BASE_DIR.'/includes/main_functions.php';
require_once DP_BASE_DIR.'/includes/db_adodb.php';
include_once DP_BASE_DIR.'/includes/db_connect.php';
include_once DP_BASE_DIR.'/install/install.inc.php';
require_once DP_BASE_DIR.'/classes/permissions.class.php';

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
 *
 * FURTHER NOTE:  As of 2.1, individual upgrade*.php files are deprecated
 * as they are not necessary.  ALL UPDATE CODE needs to be in upgrade_latest.php.
 * The individual SQL files are still required, however.
 */
function dPupgrade($from_version, $to_version, $last_updated) {

	$latest_update = '20110106'; // Set to the latest upgrade date.

	if (empty($last_updated) || empty($from_version)) {
		$last_updated = '00000000';
	}

	$dbprefix = dPgetConfig('dbprefix','');
	$perms = new dPacl;
	
	// Place the upgrade code here, depending on the last_updated date.
	// DO NOT REMOVE PREVIOUS VERSION CODE!!!
	switch ($last_updated) {
		case '00000000':
			$sql = 'SELECT project_id, project_departments, project_contacts FROM ' . $dbprefix.'projects';
			$projects = db_loadList($sql);

			//split out related departments and store them seperatly.
			$sql = 'DELETE FROM '.$dbprefix . 'project_departments';
			db_exec($sql);
			//split out related contacts and store them seperatly.
			$sql = 'DELETE FROM '.$dbprefix .'project_contacts';
			db_exec($sql);

			foreach ($projects as $project) {
                $p_id = (($project['project_id'])?$project['project_id']:'0');
				$departments = explode(',',$project['project_departments']);
				foreach ($departments as $department) {
					$sql = 'INSERT INTO '.$dbprefix.'project_departments (project_id, department_id) values ('.$p_id.', '.$department.')';
                    if ($p_id && $department) {
                        db_exec($sql);
                    }
				}

				$contacts = explode(',',$project['project_contacts']);
				foreach ($contacts as $contact) {
					$sql = 'INSERT INTO '.$dbprefix.'project_contacts (project_id, contact_id) values ('.$p_id.', '.$contact.')';
                    if ($p_id && $contact) {
                        db_exec($sql);
                    }
				}
			}

			/**
			 *  This segment will extract all the task/department and task/contact relational info and populate the task_departments and task_contacts tables.
			 **/

			$sql = 'SELECT task_id, task_departments, task_contacts FROM '.$dbprefix.'tasks';
			$tasks = db_loadList($sql);

			//split out related departments and store them seperatly.
			$sql = 'DELETE FROM '.$dbprefix.'task_departments';
			db_exec($sql);
			//split out related contacts and store them seperatly.
			$sql = 'DELETE FROM '.$dbprefix.'task_contacts';
			db_exec($sql);

			foreach ($tasks as $task) {
				$departments = explode(',',$task['task_departments']);
				foreach ($departments as $department) {
					if ($department) {
						$sql = 'INSERT INTO '.$dbprefix.'task_departments (task_id, department_id) values ('.$task['task_id'].', '.$department.')';
						db_exec($sql);
					}
				}

				$contacts = explode(',',$task['task_contacts']);
				foreach ($contacts as $contact) {
					if ($contact) {
						$sql = 'INSERT INTO '.$dbprefix.'task_contacts (task_id, contact_id) values ('.$task['task_id'].', '.$contact.')';
						db_exec($sql);
					}
				}
			}
            
            $sql = 'ALTER TABLE `'.$dbprefix.'projects` ADD `project_active` TINYINT(4) DEFAULT 1';
            db_exec($sql);
            
			if (strcmp($from_version, '2') < 0) {
				include DP_BASE_DIR.'/db/upgrade_contacts.php';
			}
			include DP_BASE_DIR.'/db/upgrade_permissions.php';

			// Fallthrough
		case '20050314':
			// Add the permissions for task_log
			dPmsg('Adding Task Log permissions');
			$perms->add_object('app', 'Task Logs', 'task_log', 11, 0, 'axo');
			$all_mods = $perms->get_group_id('all', null, 'axo');
			$nonadmin = $perms->get_group_id('non_admin', null, 'axo');
			$perms->add_group_object($all_mods, 'app', 'task_log', 'axo');
			$perms->add_group_object($nonadmin, 'app', 'task_log', 'axo');
		case '20050316':
			include DP_BASE_DIR.'/db/upgrade_contacts_company.php';
		case '20070521': // DP2.1RC2
		case '20071014': // DP2.1
			// Add view of users table to guest and project worker roles
			$guest = $perms->get_group_id('guest', null, 'aro');
			$worker = $perms->get_group_id('normal', null, 'aro');
			$perms->add_acl(array('application' => array('view')), null, array($worker, $guest), array('app' => array('users')), null, 1, 1, null, null, 'user');

		case '20071104': // Last changed date.
			// Add the permissions for task_log
			dPmsg('Adding File Folder permissions');
			$perms->add_object('app', 'File Folders', 'file_folders', 6, 0, 'axo');
			$all_mods = $perms->get_group_id('all', null, 'axo');
			$nonadmin = $perms->get_group_id('non_admin', null, 'axo');
			$perms->add_group_object($all_mods, 'app', 'file_folders', 'axo');
			$perms->add_group_object($nonadmin, 'app', 'file_folders', 'axo');

		case '20071114':
		case '20071204':
		case '20071218':
		case '20080728':
			// Seeing as we had a bug in installation/upgrade logic in previous
			// upgrades, we need to make sure we check that the latest permissions
			// are there.  Luckily the permissions system won't double-add objects
			dPmsg('Checking/Updating permissions');
			$guest = $perms->get_group_id('guest', null, 'aro');
			$worker = $perms->get_group_id('normal', null, 'aro');
			$perms->add_acl(array('application' => array('view')), null, array($worker, $guest), array('app' => array('users')), null, 1, 1, null, null, 'user');
			$perms->add_object('app', 'File Folders', 'file_folders', 6, 0, 'axo');
			$all_mods = $perms->get_group_id('all', null, 'axo');
			$nonadmin = $perms->get_group_id('non_admin', null, 'axo');
			$perms->add_group_object($all_mods, 'app', 'file_folders', 'axo');
			$perms->add_group_object($nonadmin, 'app', 'file_folders', 'axo');

		case '20090427':
			// The SQL should have created our dotpermissions cache table, and populated it,
			// So we really don't need to do anything here.

		case '20101014':
		case '20101117':
		case '20110106':
		case '20120814':
		// TODO:  Add new versions here.  Keep this message above the default label.
		default:
			break;
	}

	return $latest_update;
}

?>

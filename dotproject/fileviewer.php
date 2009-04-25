<?php /* $Id$ */

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

//file viewer
require_once 'base.php';
require_once DP_BASE_DIR.'/includes/config.php';
require_once DP_BASE_DIR.'/includes/main_functions.php';
require_once DP_BASE_DIR.'/classes/ui.class.php';
require_once DP_BASE_DIR.'/includes/db_adodb.php';
require_once DP_BASE_DIR.'/includes/db_connect.php';
require_once DP_BASE_DIR.'/includes/session.php';

$loginFromPage = 'fileviewer.php';

dPsessionStart();

// check if session has previously been initialised
// if no ask for logging and do redirect
if (!isset($_SESSION['AppUI']) || isset($_GET['logout'])) {
    $_SESSION['AppUI'] = new CAppUI();
	$AppUI =& $_SESSION['AppUI'];
	$AppUI->setConfig($dPconfig);
	$AppUI->checkStyle();
	 
	require_once($AppUI->getSystemClass('dp'));
	require_once(DP_BASE_DIR.'/misc/debug.php');

	if ($AppUI->doLogin()) $AppUI->loadPrefs(0);
	// check if the user is trying to log in
	if (isset($_REQUEST['login'])) {
		$username = dPgetParam($_POST, 'username', '');
		$password = dPgetParam($_POST, 'password', '');
		$redirect = dPgetParam($_REQUEST, 'redirect', '');
		$ok = $AppUI->login($username, $password);
		if (!$ok) {
			//display login failed message 
			$uistyle = $AppUI->getPref('UISTYLE') ? $AppUI->getPref('UISTYLE') : $dPconfig['host_style'];
			$AppUI->setMsg('Login Failed');
			require DP_BASE_DIR.'/style/'.$uistyle.'/login.php';
			session_unset();
			exit;
		}
		header ('Location: fileviewer.php?'.$redirect);
		exit;
	}	

	$uistyle = $AppUI->getPref('UISTYLE') ? $AppUI->getPref('UISTYLE') : $dPconfig['host_style'];
	// check if we are logged in
	if ($AppUI->doLogin()) {
	    $AppUI->setUserLocale();
		@include_once(DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php');
		@include_once(DP_BASE_DIR.'/locales/core.php');
		setlocale(LC_TIME, $AppUI->user_locale);
		
		$redirect = @$_SERVER['QUERY_STRING'];
		if (mb_strpos($redirect, 'logout') !== false) $redirect = '';	
		if (isset($locale_char_set)) header('Content-type: text/html;charset='.$locale_char_set);
		require DP_BASE_DIR.'/style/'.$uistyle.'/login.php';
		session_unset();
		session_destroy();
		exit;
	}	
}
$AppUI =& $_SESSION['AppUI'];

require_once DP_BASE_DIR.'/includes/permissions.php';

$perms =& $AppUI->acl();

$canRead = $perms->checkModule('files' , 'view');
if (!$canRead) {
	$AppUI->redirect('m=public&a=access_denied');
}

$file_id = isset($_GET['file_id']) ? $_GET['file_id'] : 0;

if ($file_id) {
	// projects tat are denied access
	require_once($AppUI->getModuleClass('projects'));
	require_once($AppUI->getModuleClass('files'));
	$project =& new CProject;
	$allowedProjects = $project->getAllowedRecords($AppUI->user_id, 'project_id, project_name');
	$fileclass =& new CFile;
	$fileclass->load($file_id);
	$allowedFiles = $fileclass->getAllowedRecords($AppUI->user_id, 'file_id, file_name');
	
	if (count($allowedFiles) && ! array_key_exists($file_id, $allowedFiles)) {
		$AppUI->redirect('m=public&a=access_denied');
	}

	$q = new DBQuery;
	$q->addTable('files');
	if ($fileclass->file_project) {
		$project->setAllowedSQL($AppUI->user_id, $q, 'file_project');
	}
	$q->addWhere('file_id = '.$file_id);
	
	$sql = $q->prepare();

	if (!db_loadHash($sql, $file)) {
		$AppUI->redirect('m=public&a=access_denied');
	};

	/*
	 * DISABLED LINES TO FIX A NEWER BUG 914075 WITH IE 6 (GREGORERHARDT 20040612)

	// BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
	// [http://bugs.php.net/bug.php?id=16173]
	header('Pragma: ');
	header('Cache-Control: ');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');  //HTTP/1.1
	header('Cache-Control: post-check=0, pre-check=0', false);
	// END extra headers to resolve IE caching bug
	*/

	$fname = DP_BASE_DIR.'/files/'.$file['file_project'].'/'.$file['file_real_filename'];
	if (! file_exists($fname)) {
		$AppUI->setMsg('fileIdError', UI_MSG_ERROR);
		$AppUI->redirect();
	}
    
    /*
     * MerlinYoda> 
     * some added lines from: 
     * http://www.dotproject.net/vbulletin/showpost.php?p=11975&postcount=13
     * along with "Pragma" header as suggested in: 
     * http://www.dotproject.net/vbulletin/showpost.php?p=14928&postcount=1. 
     * to fix the IE download issue for all for http and https
     * 
     */ 
     ob_end_clean();
	header('MIME-Version: 1.0');
    header('Pragma: ');
    header('Cache-Control: public');
	header('Content-length: '.$file['file_size']);
	header('Content-type: '.$file['file_type']);
	header('Content-transfer-encoding: 8bit');
	header('Content-disposition: attachment; filename="'.$file['file_name'].'"');

	// read and output the file in chunks to bypass limiting settings in php.ini
	$handle = fopen(DP_BASE_DIR . '/files/'.$file['file_project'].'/'.$file['file_real_filename'], 'rb');
	if ($handle)
	{
		while (!feof($handle)) {
			print fread($handle, 8192);
		}
		fclose($handle);
	}
	flush();
} else {
	$AppUI->setMsg('fileIdError', UI_MSG_ERROR);
	$AppUI->redirect();
}
?>

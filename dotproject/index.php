<?php /* $Id$ */

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

// If you experience a 'white screen of death' or other problems,
// uncomment the following line of code:
//error_reporting(E_ALL);

$loginFromPage = 'index.php';
require_once 'base.php';

clearstatcache();
if (is_file(DP_BASE_DIR . '/includes/config.php')) {
	require_once DP_BASE_DIR . '/includes/config.php';

} else {
	echo ('<html><head><meta http-equiv="refresh" content="5; URL=' . DP_BASE_URL 
	      . '/install/index.php"></head><body>' 
	      . 'Fatal Error. You haven\'t created a config file yet.<br/>' 
	      . '<a href="./install/index.php">Click Here To Start Installation and Create One!</a>' 
	      . ' (forwarded in 5 sec.)</body></html>');
	exit();
}

if (!(isset($GLOBALS['OS_WIN']))) {
	$GLOBALS['OS_WIN'] = (mb_stristr(PHP_OS, 'WIN') !== false);
}

// tweak for pathname consistence on windows machines
require_once (DP_BASE_DIR . '/classes/csscolor.class.php'); // Required before main_functions
require_once (DP_BASE_DIR . '/includes/main_functions.php');
require_once (DP_BASE_DIR . '/includes/db_adodb.php');
require_once (DP_BASE_DIR . '/includes/db_connect.php');

require_once (DP_BASE_DIR . '/classes/ui.class.php');
require_once (DP_BASE_DIR . '/classes/permissions.class.php');
require_once (DP_BASE_DIR . '/includes/session.php');

// don't output anything. Usefull for fileviewer.php, gantt.php, etc.
$suppressHeaders = (bool)dPgetParam($_GET, 'suppressHeaders', false);

// manage the session variable(s)
dPsessionStart(array('AppUI'));

// write the HTML headers

header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); //Date in the past
header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); //always modified
header ('Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0'); //HTTP/1.1
header ('Pragma: no-cache');	// HTTP/1.0

// check if session has previously been initialised
if (!(isset($_SESSION['AppUI'])) || isset($_GET['logout'])) {
    if (isset($_GET['logout']) && isset($_SESSION['AppUI']->user_id)) {
        $AppUI =& $_SESSION['AppUI'];
        $AppUI->registerLogout($AppUI->user_id);
        addHistory('login', $AppUI->user_id, 'logout', 
		           ($AppUI->user_first_name . ' ' . $AppUI->user_last_name));
    }
	$_SESSION['AppUI'] = new CAppUI;
}
$AppUI =& $_SESSION['AppUI'];
$last_insert_id =$AppUI->last_insert_id;

$AppUI->checkStyle();

// load the commonly used classes
require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getSystemClass('query'));

require_once DP_BASE_DIR.'/misc/debug.php';

//Function for update lost action in user_access_log
$AppUI->updateLastAction($last_insert_id);
// load default preferences if not logged in
if ($AppUI->doLogin()) {
	$AppUI->loadPrefs(0);
}

// check is the user needs a new password
if (dPgetParam($_POST, 'lostpass', 0)) {
	$uistyle = dPgetConfig('host_style');
	$AppUI->setUserLocale();
	@include_once (DP_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php');
	@include_once (DP_BASE_DIR . '/locales/core.php');
	setlocale(LC_TIME, $AppUI->user_lang);
	if (dPgetParam($_REQUEST, 'sendpass', 0)) {
		require (DP_BASE_DIR . '/includes/sendpass.php');
		sendNewPass();
	} else {
		require (DP_BASE_DIR . '/style/' . $uistyle . '/lostpass.php');
	}
	exit();
}

// check if the user is trying to log in
// Note the change to REQUEST instead of POST.  This is so that we can
// support alternative authentication methods such as the PostNuke
// and HTTP auth methods now supported.
if (isset($_REQUEST['login'])) {
	$username = dPgetCleanParam($_POST, 'username', '');
	$password = dPgetParam($_POST, 'password', '');
	$redirect = dPgetCleanParam($_REQUEST, 'redirect', '');
	$AppUI->setUserLocale();
	@include_once(DP_BASE_DIR . '/locales/' . $AppUI->user_locale . '/locales.php');
	@include_once DP_BASE_DIR . '/locales/core.php';
	$ok = $AppUI->login($username, $password);
	if (!$ok) {
		$AppUI->setMsg('Login Failed');
	} else {
		//Register login in user_acces_log
		$AppUI->registerLogin();
	}
	addHistory('login', $AppUI->user_id, 'login', 
	           ($AppUI->user_first_name . ' ' . $AppUI->user_last_name));
	$AppUI->redirect($redirect);
}

// supported since PHP 4.2
// writeDebug(var_export($AppUI, true), 'AppUI', __FILE__, __LINE__);

// set the default ui style
$uistyle = (($AppUI->getPref('UISTYLE')) ? $AppUI->getPref('UISTYLE') : dPgetConfig('host_style'));

// clear out main url parameters
$m = '';
$a = '';
$u = '';

// check if we are logged in
if ($AppUI->doLogin()) {
	// load basic locale settings
	$AppUI->setUserLocale();
	@include_once('./locales/' . $AppUI->user_locale . '/locales.php');
	@include_once('./locales/core.php');
	setlocale(LC_TIME, $AppUI->user_lang);
	$redirect = (($_SERVER['QUERY_STRING']) ? strip_tags($_SERVER['QUERY_STRING']) : '');
	if (mb_strpos($redirect, 'logout') !== false) {
		$redirect = '';
	}

	if (isset($locale_char_set)) {
		header('Content-type: text/html;charset=' . $locale_char_set);
	}

	require (DP_BASE_DIR . '/style/' . $uistyle . '/login.php');
	// destroy the current session and output login page
	session_unset();
	session_destroy();
	exit;
}
$AppUI->setUserLocale();
@include_once('./locales/' . $AppUI->user_locale . '/locales.php');

/* date class sets the default start day which comes from the locale */
require_once($AppUI->getSystemClass('date'));


// bring in the rest of the support and localisation files
require_once (DP_BASE_DIR . '/includes/permissions.php');


$def_a = 'index';
if (!(isset($_GET['m']) || empty($dPconfig['default_view_m']))) {
  	$m = $dPconfig['default_view_m'];
	$def_a = ((!empty($dPconfig['default_view_a'])) ? $dPconfig['default_view_a'] : $def_a);
	$tab = $dPconfig['default_view_tab'];
} else {
	// set the module from the url
	$m = $AppUI->checkFileName(dPgetCleanParam($_GET, 'm', getReadableModule()));
}
// set the action from the url
$a = $AppUI->checkFileName(dPgetCleanParam($_GET, 'a', $def_a));

/* This check for $u implies that a file located in a subdirectory of higher depth than 1
 * in relation to the module base can't be executed. So it would'nt be possible to
 * run for example the file module/directory1/directory2/file.php
 * Also it won't be possible to run modules/module/abc.zyz.class.php for that dots are
 * not allowed in the request parameters.
*/

$u = $AppUI->checkFileName(dPgetCleanParam($_GET, 'u', ''));

// load module based locale settings
@include_once (DP_BASE_DIR . '/locales/core.php');

setlocale(LC_TIME, $AppUI->user_lang);
$m_config = dPgetConfig($m);
@include_once (DP_BASE_DIR.'/functions/' . $m . '_func.php');

// TODO: canRead/Edit assignements should be moved into each file

// check overall module permissions
// these can be further modified by the included action files
$canAccess = getPermission($m, 'access');
$canRead = getPermission($m, 'view');
$canEdit = getPermission($m, 'edit');
$canAuthor = getPermission($m, 'add');
$canDelete = getPermission($m, 'delete');

if (!$suppressHeaders) {
	// output the character set header
	if (isset($locale_char_set)) {
		header('Content-type: text/html;charset='.$locale_char_set);
	}
}

// include the module class file - we use file_exists instead of @ so
// that any parse errors in the file are reported, rather than errors
// further down the track.
$modclass = $AppUI->getModuleClass($m);
if (file_exists($modclass)) {
	include_once($modclass);
}
if ($u && file_exists(DP_BASE_DIR . '/modules/' . $m . '/' . $u . '/' . $u . '.class.php')) {
	include_once (DP_BASE_DIR . '/modules/' . $m . '/' . $u . '/' . $u . '.class.php');
}

// do some db work if dosql is set
// TODO - MUST MOVE THESE INTO THE MODULE DIRECTORY
if (isset($_REQUEST['dosql'])) {
	//require('./dosql/' . $_REQUEST['dosql'] . '.php');
	require (DP_BASE_DIR . '/modules/' . $m . '/' . ($u ? ($u.'/') : '') 
	         . $AppUI->checkFileName($_REQUEST['dosql']) . '.php');
}

// start output proper
include  (DP_BASE_DIR . '/style/' . $uistyle . '/overrides.php');
ob_start();
if (!$suppressHeaders) {
	require (DP_BASE_DIR . '/style/' . $uistyle . '/header.php');
}

if (!(isset($_SESSION['all_tabs'][$m]))) {
	// For some reason on some systems if you don't set this up
	// first you get recursive pointers to the all_tabs array, creating
	// phantom tabs.
	if (! isset($_SESSION['all_tabs'])) {
		$_SESSION['all_tabs'] = array();
	}
	$_SESSION['all_tabs'][$m] = array();
	$all_tabs =& $_SESSION['all_tabs'][$m];
	foreach ($AppUI->getActiveModules() as $dir => $module)	{
		if (!(getPermission($dir, 'access'))) {
			continue;
		}
		$modules_tabs = $AppUI->readFiles((DP_BASE_DIR . '/modules/' . $dir . '/'), 
		                                  ('^' . $m . '_tab.*\.php'));
		foreach ($modules_tabs as $mod_tab) {
			// Get the name as the subextension
			// cut the module_tab. and the .php parts of the filename 
			// (begining and end)
			$nameparts = explode('.', $mod_tab);
			$filename = mb_substr($mod_tab, 0, -4);
			if (count($nameparts) > 3) {
				$file = $nameparts[1];
				if (!(isset($all_tabs[$file]))) {
					$all_tabs[$file] = array();
				}
				$arr =& $all_tabs[$file];
				$name = $nameparts[2];
			} else {
				$arr =& $all_tabs;
				$name = $nameparts[1];
			}
			$arr[] = array('name' => ucfirst(str_replace('_', ' ', $name)),
			               'file' => (DP_BASE_DIR . '/modules/' . $dir . '/' . $filename),
			               'module' => $dir);
			
			/* 
			 * Don't forget to unset $arr again! $arr is likely to be used in the sequel declaring
			 * any temporary array. This may lead to strange bugs with disappearing tabs(cf. #1767).
			 * @author: gregorerhardt @date: 20070203
			 */
			unset($arr); 
		}
	}
} else {
	$all_tabs =& $_SESSION['all_tabs'][$m];
}

$module_file = (DP_BASE_DIR . '/modules/' . $m . '/' . (($u) ? ($u.'/') : '') . $a . '.php');
if (file_exists($module_file)) {
	require $module_file;
} else {
	//TODO: make this part of the public module? 
	//TODO: internationalise the string.
	$titleBlock = new CTitleBlock('Warning', 'log-error.gif');
	$titleBlock->show();
	
	echo $AppUI->_('Missing file. Possible Module "' . $m . '" missing!');
}
// wtf??  why?
if (!$suppressHeaders) {
	echo ('<iframe name="thread" src="' . DP_BASE_URL 
	      . '/modules/index.html" width="0" height="0" frameborder="0"></iframe>');
	require (DP_BASE_DIR . '/style/' . $uistyle . '/footer.php');
}
ob_end_flush();

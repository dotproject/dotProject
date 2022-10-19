<?php /* CLASSES $Id$ */
/**
* @package dotproject
* @subpackage core
* @license http://opensource.org/licenses/gpl-license.php GPL License Version 2
*/

if (! defined('DP_BASE_DIR')) {
	die('This file should not be called directly.');
}

// Message No Constants
define('UI_MSG_OK', 1);
define('UI_MSG_ALERT', 2);
define('UI_MSG_WARNING', 3);
define('UI_MSG_ERROR', 4);

// global variable holding the translation array
$GLOBALS['translate'] = array();

define ('UI_CASE_MASK', 0x0F);
define ('UI_CASE_UPPER', 1);
define ('UI_CASE_LOWER', 2);
define ('UI_CASE_UPPERFIRST', 4);

define ('UI_OUTPUT_MASK', 0xFF0);
define ('UI_OUTPUT_TEXT', 0);
define ('UI_OUTPUT_JS', 0x10);
define ('UI_OUTPUT_RAW', 0x20);
define ('UI_OUTPUT_URI', 0x40);
define ('UI_OUTPUT_HTML', 0x80);
define ('UI_OUTPUT_FORM', 0x100);

// DP_BASE_DIR is set in base.php and fileviewer.php and is the base directory
// of the dotproject installation.
require_once DP_BASE_DIR.'/classes/permissions.class.php';
require_once DP_BASE_DIR.'/includes/filter.php';
/**
* The Application User Interface Class.
*
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
* @version $Revision$
*/
class CAppUI {
/** @var array generic array for holding the state of anything */
	var $state=null;
/** @var int */
	var $user_id=null;
/** @var string */
	var $user_first_name=null;
/** @var string */
	var $user_last_name=null;
/** @var string */
	var $user_company=null;
/** @var int */
	var $user_department=null;
/** @var string */
	var $user_email=null;
/** @var int */
	var $user_type=null;
/** @var array */
	var $user_prefs=null;
/** @var int Unix time stamp */
	var $day_selected=null;
/** @var array */
	var $system_prefs = array();

	// localisation
/** @var string */
	var $user_locale=null;
/** @var string */
	var $user_lang=null;
/** @var string */
	var $base_locale = 'en'; // do not change - the base 'keys' will always be in english

/** @var string */
	var $base_date_locale = null;

/** @var string Message string*/
	var $msg = '';
/** @var string */
	var $msgNo = '';
/** @var string Default page for a redirect call*/
	var $defaultRedirect = '';

/** @var array Configuration variable array*/
	var $cfg=null;

/** @var integer Version major */
	var $version_major = null;

/** @var integer Version minor */
	var $version_minor = null;

/** @var integer Version patch level */
	var $version_patch = null;

/** @var string Version string */
	var $version_string = null;

/** @var integer for register log ID */
	var $last_insert_id = null;

/** @var array list of external JS libraries */
	var $_js = [];

/** @var array list of external CSS libraries */
	var $_css = [];

/**
* CAppUI Constructor (deprecated call)
*/
	function CAppUI() {
		$this->state = array();

		$this->user_id = -1;
		$this->user_first_name = '';
		$this->user_last_name = '';
		$this->user_company = 0;
		$this->user_department = 0;
		$this->user_type = 0;

		// cfg['locale_warn'] is the only cfgVariable stored in session data (for security reasons)
		// this guarants the functionality of this->setWarning
		$this->cfg['locale_warn'] = dPgetConfig('locale_warn');

		$this->project_id = 0;

		$this->defaultRedirect = '';
		// set up the default preferences
		$this->setUserLocale($this->base_locale);
		$this->user_prefs = array();
	}
  // Modern-style constructor
  function __construct() {
    self::CAppUI();
  }
/**
* Used to load a php class file from the system classes directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
	function getSystemClass($name=null) {
		if ($name) {
			return DP_BASE_DIR . '/classes/' . $name . '.class.php';
		}
	}

/**
* Used to load a php class file from the lib directory
*
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
*/
	function getLibraryClass($name=null) {
		if ($name) {
			return DP_BASE_DIR . '/lib/' . $name. '.php';
		}
	}

/**
* Used to load a php class file from the module directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
	function getModuleClass($name=null) {
		if ($name) {
			return (DP_BASE_DIR . '/modules/' . $name . '/' . $name . '.class.php');
		}
	}

/**
* Determines the version.
* @return String value indicating the current dotproject version
*/
	function getVersion() {
		global $dPconfig;
		if (!(isset($this->version_major))) {
			include_once (DP_BASE_DIR . '/includes/version.php');
			$this->version_major = $dp_version_major;
			$this->version_minor = $dp_version_minor;
			$this->version_patch = $dp_version_patch;
			$this->version_string = ($this->version_major . '.' . $this->version_minor);
			if (isset($this->version_patch)) {
				$this->version_string .= '.' . $this->version_patch;
			}
			if (isset($dp_version_prepatch)) {
				$this->version_string .= '-' . $dp_version_prepatch;
			}
		}
		return $this->version_string;
	}

/**
* Checks that the current user preferred style is valid/exists.
*/
	function checkStyle() {
		// check if default user's uistyle is installed
		$uistyle = $this->getPref('UISTYLE');

		if ($uistyle && !is_dir(DP_BASE_DIR . '/style/' . $uistyle)) {
			// fall back to host_style if user style is not installed
			$this->setPref('UISTYLE', dPgetConfig('host_style'));
		}
	}

/**
* Utility function to read the 'directories' under 'path'
*
* This function is used to read the modules or locales installed on the file system.
* @param string The path to read.
* @return array A named array of the directories (the key and value are identical).
*/
	function readDirs($path) {
		$dirs = array();
		$d = dir(DP_BASE_DIR . '/'  . $path);
		while (false !== ($name = $d->read())) {
			if (is_dir(DP_BASE_DIR . '/' . $path . '/' . $name) && $name != '.' && $name != '..'
			    && $name != 'CVS' && $name != '.svn') {
				$dirs[$name] = $name;
			}
		}
		$d->close();
		return $dirs;
	}

/**
* Utility function to read the 'files' under 'path'
* @param string The path to read.
* @param string A regular expression to filter by.
* @return array A named array of the files (the key and value are identical).
*/
	function readFiles($path, $filter='.') {
		$files = array();

		if (is_dir($path) && ($handle = opendir($path))) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..' && preg_match(('/' . $filter . '/'), $file)) {
					$files[$file] = $file;
				}
			}
			closedir($handle);
		}
		return $files;
	}


/**
* Utility function to check whether a file name is 'safe'
*
* Prevents from access to relative directories (eg ../../dealyfile.php);
* @param string The file name.
* @return array A named array of the files (the key and value are identical).
*/
	function checkFileName($file) {
		global $AppUI;

		// define bad characters and their replacement
		$bad_chars = ';/\\\'()"$';
		$bad_replace = '.........'; // Needs the same number of chars as $bad_chars

		// check whether the filename contained bad characters
		if (mb_strpos(strtr($file, $bad_chars, $bad_replace), '.') !== false) {
			$AppUI->redirect('m=public&a=access_denied');
			return $file;
		}
		else {
			return $file;
		}
	}



/**
* Utility function to make a file name 'safe'
*
* Strips out mallicious insertion of relative directories (eg ../../dealyfile.php);
* @param string The file name.
* @return array A named array of the files (the key and value are identical).
*/
	function makeFileNameSafe($file) {
		$file = str_replace('../', '', $file);
		$file = str_replace('..\\', '', $file);
		return $file;
	}

/**
* Sets the user locale.
*
* Looks in the user preferences first.
* If this value has not been set by the user it uses the system default set in config.php.
* @param string Locale abbreviation corresponding to the sub-directory name in the locales
* directory (usually the abbreviated language code).
*/
	function setUserLocale($loc='', $set = true) {
		global $locale_char_set;

		$LANGUAGES = $this->loadLanguages();

		if (! $loc) {
			$loc = ((@$this->user_prefs['LOCALE']) ? $this->user_prefs['LOCALE']
			        : dPgetConfig('host_locale'));
		}

		if (isset($LANGUAGES[$loc])) {
			$lang = $LANGUAGES[$loc];
		} else {
			// Need to try and find the language the user is using, find the first one
			// that has this as the language part
			if (mb_strlen($loc) > 2) {
				list ($l, $c) = explode('_', $loc);
				$loc = $this->findLanguage($l, $c);
			} else {
				$loc = $this->findLanguage($loc);
			}
			$lang = $LANGUAGES[$loc];
		}
    if (!empty($lang)) {  // in this case, all assignments made below will also be empty (gwyneth 20210414)
//    if (version_compare(phpversion(), '7.0.0', 'ge')) {
//      dprint(__FILE__, __LINE__, "DEBUG: [" . __FUNCTION__ . "] here goes lang: «" . print_r($lang, true) . "»");
		  // list($base_locale, $english_string, $native_string, $default_language, $lcs) = $lang;

      // The code below is more 'wordy', but at least it catches the empty cases so much better (gwyneth 20210415)
      $base_locale      = $lang[0];
      $english_string   = $lang[1];  // not used? (gwyneth 20210415)
      $native_string    = $lang[2];  // not used? (gwyneth 20210415)
      $default_language = $lang[3];
      if (!empty($lang[4])) { $lcs = $lang[4]; }  // often comes out empty, which is dealt with below (gwyneth 20210415)
//    } else {
      // The assignment order in PHP 7.0 and greater is now the reverse of what it was in 5+
      // So we do a reverse assignment (gwyneth 20210414)
//      list($lcs, $default_language, $native_string, $english_string, $base_locale) = $lang;
    }
		if (! isset($lcs)) {
			$lcs = (isset($locale_char_set)) ? $locale_char_set : 'utf-8';
		}

		if (version_compare(phpversion(), '4.3.0', 'ge')) {
			$user_lang = array(($loc ?? '???') . '.' . $lcs, $default_language, $loc, $base_locale);
		}
		else {
			$user_lang = ((strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') ? $default_language
						  : (($loc ?? '???') . '.' . $lcs));
		}

		if ($set) {
			$this->user_locale = $base_locale;
			$this->user_lang = $user_lang;
			$locale_char_set = $lcs;
			//mb_internal_encoding($locale_char_set);
		} else {
			return $user_lang;
		}
	}

	function findLanguage($language, $country = false) {
		$LANGUAGES = $this->loadLanguages();
		$language = mb_strtolower($language);
		if ($country) {
			$country = mb_strtoupper($country);
			// Try constructing the code again
			$code = $language . '_' . $country;
			if (isset($LANGUAGES[$code])) {
				return $code;
			}
		}

		// Just use the country code and try and find it in the
		// languages list.
		$first_entry = null;
		foreach ($LANGUAGES as $lang => $info) {
			list($l, $c) = explode('_', $lang);
			if ($l == $language) {
				if (!($first_entry)) {
					$first_entry = $lang;
				}
				if ($country && $c == $country) {
					return $lang;
				}
			}
		}
		return $first_entry;
	}

/**
 * Set the base locale, used for getting English date strings so they can be
 * translated by the translation functions.
 */
	function setBaseLocale($context = LC_ALL)
	{
		global $locale_char_set;

		$LANGUAGES = $this->loadLanguages();

//		list($locale, $en_name, $local_name, $win_locale, $lcs) = $LANGUAGES['en_AU'];
// See comments for setUserLocale (gwyneth 20210415)
    $locale     = $LANGUAGES['en_AU'][0];
    $en_name    = $LANGUAGES['en_AU'][1];
    $local_name = $LANGUAGES['en_AU'][2];
    $win_locale = $LANGUAGES['en_AU'][3];
    if (!empty($LANGUAGES['en_AU'][4])) { $lcs = $LANGUAGES['en_AU'][4]; }  // if empty, deal with it below (gwyneth 20210415)
		$real_locale = 'en_AU';
		if (strtoupper(substr(PHP_OS,0,3)) == 'WIN') {
			$real_locale = $win_locale;
		} else {
			if (! isset($lcs)) {
				$lcs = (isset($locale_char_set)) ? $locale_char_set : 'utf-8';
			}
			$real_locale .= '.' . $lcs;
		}
		setlocale($context, $real_locale);
	}

/**
 * Load the known language codes for loaded locales
 *
 */
	function loadLanguages() {
		if (isset($_SESSION['LANGUAGES'])) {
			$LANGUAGES =& $_SESSION['LANGUAGES'];
		} else {
			$LANGUAGES = array();
			$langs = $this->readDirs('locales');
			foreach ($langs as $lang) {
				if (file_exists(DP_BASE_DIR . '/locales/' . $lang . '/lang.php')) {
					include_once DP_BASE_DIR . '/locales/' . $lang . '/lang.php';
				}
			}
			@$_SESSION['LANGUAGES'] =& $LANGUAGES;
		}
		return $LANGUAGES;
	}

	/**
	 * Translate string to the local language [same form as the gettext abbreviation]
	 *
	 * This is the order of precedence:
	 * <ul>
	 * <li>If the key exists in the lang array, return the value of the key
	 * <li>If no key exists and the base lang is the same as the local lang, just return the string
	 * <li>If this is not the base lang, then return string with a red star appended to show
	 * that a translation is required.
	 * </ul>
	 * @param string The string to translate
	 * @param int Option flags, can be case handling or'd with output styles
	 * @return string
	 */

	//Translation function to handle arrays or single string variables
	function _($str, $flags= 0) {
		if (is_array($str)) {
			$translated = array();
			foreach ($str as $s) {
				$translated[] = $this->__($s, $flags);
			}
			return implode(' ', $translated);
		} else {
			return $this->__($str, $flags);
		}
	}

	//Main translation function
	function __($str, $flags = 0) {
		$str = trim($str);
		if (empty($str)) {
			return '';
		}
		$x = @$GLOBALS['translate'][$str];

		if ($x) {
			$str = $x;
		} else if (dPgetConfig('locale_warn') && !($this->base_locale == $this->user_locale
		                                           && in_array($str, @$GLOBALS['translate']))) {
			$str .= dPgetConfig('locale_alert');
		}

		return $this->___($str, $flags);
	}

	//Output formatting function
	function ___($str, $flags = 0) {
		global $locale_char_set;

		if (! $locale_char_set) {
			$locale_char_set = 'utf-8';
		}

		switch ($flags & UI_CASE_MASK) {
			case UI_CASE_UPPER:
				$str = mb_strtoupper($str, $locale_char_set);
				break;
			case UI_CASE_LOWER:
				$str = mb_strtolower($str, $locale_char_set);
				break;
			case UI_CASE_UPPERFIRST:
				$str = mb_convert_case($str, MB_CASE_TITLE, $locale_char_set);
				break;
		}
		/* Altered to support multiple styles of output, to fix
		 * bugs where the same output style cannot be used succesfully
		 * for both javascript and HTML output.
		 *
		 * Default is currently UI_OUTPUT_TEXT.
		 */
		switch ($flags & UI_OUTPUT_MASK) {
			case UI_OUTPUT_URI:
				$str = str_replace(' ', '%20', $str);
				break;
			case UI_OUTPUT_TEXT:  // $flags == 0 : this is the default! (gwyneth 20260426)
				$str = htmlentities(stripslashes($str), ENT_COMPAT, $locale_char_set);
//				$str = filter_xss($str);
        $str = dPsanitiseHTML($str);
				$str = nl2br($str);
				break;
			case UI_OUTPUT_FORM:
				$str = htmlentities(stripslashes($str), ENT_COMPAT, $locale_char_set);
//				$str = filter_xss($str);
        $str = dPsanitiseHTML($str);
				break;
			case UI_OUTPUT_HTML:
				#$str = htmlentities(stripslashes($str), ENT_COMPAT, $locale_char_set);
				#$str = str_replace('&#039;', '&apos;', $str);
//				$str = filter_xss($str);
        $str = dPsanitiseHTML($str);
				break;
			case UI_OUTPUT_JS:
				$str = addslashes(stripslashes($str));
				break;
			case UI_OUTPUT_RAW:
				$str = stripslashes($str);
				break;
		}
		return $str;
	}

	function showHTML($text) {
		return $this->___($text, UI_OUTPUT_HTML);
	}

	function showRaw($text) {
		return $this->___($text, UI_OUTPUT_RAW);
	}

	function showJS($text) {
		return $this->___($text, UI_OUTPUT_JS);
	}

/**
* Set the display of warning for untranslated strings
* @param string
*/
	function setWarning($state=true) {
		$temp = @$this->cfg['locale_warn'];
		$this->cfg['locale_warn'] = $state;
		return $temp;
	}
/**
* Save the url query string
*
* Also saves one level of history.  This is useful for returning from a delete
* operation where the record more not now exist.  Returning to a view page
* would be a nonsense in this case.
* @param string If not set then the current url query string is used
*/
	function savePlace($query='') {
		if (!$query) {
			$query = @$_SERVER['QUERY_STRING'];
		}
		if ($query != @$this->state['SAVEDPLACE']) {
			$this->state['SAVEDPLACE-1'] = @$this->state['SAVEDPLACE'];
			$this->state['SAVEDPLACE'] = $query;
		}
	}
/**
* Resets the internal variable
*/
	function resetPlace() {
		$this->state['SAVEDPLACE'] = '';
	}
/**
* Get the saved place (usually one that could contain an edit button)
* @return string
*/
	function getPlace() {
		return @$this->state['SAVEDPLACE'];
	}
/**
* Redirects the browser to a new page.
*
* Mostly used in conjunction with the savePlace method. It is generally used
* to prevent nasties from doing a browser refresh after a db update.  The
* method deliberately does not use javascript to effect the redirect.
*
* @param string The URL query string to append to the URL
* @param string A marker for a historic 'place, only -1 or an empty string is valid.
*/
	function redirect($params='', $hist='') {
		$session_id = SID;

		session_write_close();
		// are the params empty
		if (!$params) {
			// has a place been saved
			$params = ((!(empty($this->state["SAVEDPLACE$hist"])))
			           ? $this->state["SAVEDPLACE$hist"] : $this->defaultRedirect);
		}
		// Fix to handle cookieless sessions
		if ($session_id != '') {
			//appending $session_id parameter to $params
			$params .= (($params) ? '&' : '')  . $session_id;
		}
		ob_implicit_flush(); // Ensure any buffering is disabled.
		header('Location: index.php?' . $params);
		exit();	// stop the PHP execution
	}
/**
* Set the page message.
*
* The page message is displayed above the title block and then again
* at the end of the page.
*
* IMPORTANT: Please note that append should not be used, since for some
* languagues atomic-wise translation doesn't work. Append should be
* deprecated.
*
* @param mixed The (untranslated) message
* @param int The type of message
* @param boolean If true, $msg is appended to the current string otherwise
* the existing message is overwritten with $msg.
*/
	function setMsg($msg, $msgNo=0, $append=false) {
		$msg = $this->_($msg);
		$this->msg = $append ? $this->msg.' '.$msg : $msg;
		$this->msgNo = $msgNo;
	}
/**
* Display the formatted message and icon
* @param boolean If true the current message state is cleared.
*/
	function getMsg($reset=true) {
		$img = '';
		$class = '';
		$msg = $this->msg ?? null;  // better safe than sorry! (gwyneth 20210425)

    if (empty($this->msgNo)) {  // short-circuit and return if message information is missing or not even set — saves us a few warnings under PHP 8 (gwyneth 20210425)
      return '';
    }

		switch($this->msgNo) {
		case UI_MSG_OK:
			$img = dPshowImage(dPfindImage('stock_ok-16.png'), 16, 16, '');
			$class = 'message';
			break;
		case UI_MSG_ALERT:
			$img = dPshowImage(dPfindImage('rc-gui-status-downgr.png'), 16, 16, '');
			$class = 'message';
			break;
		case UI_MSG_WARNING:
			$img = dPshowImage(dPfindImage('rc-gui-status-downgr.png'), 16, 16, '');
			$class = 'warning';
			break;
		case UI_MSG_ERROR:
			$img = dPshowImage(dPfindImage('stock_cancel-16.png'), 16, 16, '');
			$class = 'error';
			break;
		default:
			$class = 'message';
			break;
		}
		if ($reset) {
			$this->msg = '';
			$this->msgNo = 0;
		}
		return ((!empty($msg)) ? ('<table cellspacing="0" cellpadding="1" border="0"><tr>'
		                  . '<td>' . $img . '</td><td class="' . $class . '">' . $msg . '</td>'
		                  . '</tr></table>')
		        : '');
	}
/**
* Set the value of a temporary state variable.
*
* The state is only held for the duration of a session.  It is not stored in the database.
* Also do not set the value if it is unset.
* @param string The label or key of the state variable
* @param mixed Value to assign to the label/key
*/
	function setState($label, $value = null) {
		if (isset($value)) {
			$this->state[$label] = $value;
		}
	}
/**
* Get the value of a temporary state variable.
* If a default value is supplied and no value is found, set the default.
* @return mixed
*/
	function getState($label, $default_value = null) {
		if (!empty($this->state) && is_array($this->state) && array_key_exists($label, $this->state)) {
			return $this->state[$label];
		} else if (isset($default_value)) {
			$this->setState($label, $default_value);
			return $default_value;
		}
		return NULL;  // to make sure we return _something_, and that _something_ really is NULL (gwyneth 20210414)
	}

	function checkPrefState($label, $value, $prefname, $default_value = null) {
		// Check if we currently have it set
		if (isset($value)) {
			$result = $value;
			$this->state[$label] = $value;
		} else if (!empty($this->state) && is_array($this->state) && array_key_exists($label, $this->state)) {
			$result = $this->state[$label];
		} else if (($pref = $this->getPref($prefname)) !== null) {
			$this->state[$label] = $pref;
			$result = $pref;
		} else if (isset($default_value)) {
			$this->state[$label] = $default_value;
			$result = $default_value;
		} else {
			$result = null;
		}
		return $result;
	}
/**
* Login function
*
* A number of things are done in this method to prevent illegal entry:
* <ul>
* <li>The username and password are trimmed and escaped to prevent malicious
*     SQL being executed
* </ul>
* The schema previously used the MySQL PASSWORD function for encryption.  This
* Method has been deprecated in favour of PHP's MD5() function for database independance.
* The check_legacy_password option is no longer valid
*
* Upon a successful username and password match, several fields from the user
* table are loaded in this object for convenient reference.  The style, localces
* and preferences are also loaded at this time.
*
* @param string The user login name
* @param string The user password
* @return boolean True if successful, false if not
*/
	function login($username, $password) {
		require_once DP_BASE_DIR.'/classes/authenticator.class.php';

		$auth_method = dPgetConfig('auth_method', 'sql');
		if (@$_POST['login'] != 'login'
		    && @$_POST['login'] != $this->_('login', UI_OUTPUT_RAW)
		    && $_REQUEST['login'] != $auth_method) {
			die('You have chosen to log in using an unsupported or disabled login method');
		}
		$auth =& getauth($auth_method);

		$username = trim(db_escape($username));
		$password = trim($password);

		if (!$auth->authenticate($username, $password)) {
			return false;
		}

		$user_id = $auth->userId($username);
		// Some authentication schemes may collect username in various ways.
		$username = $auth->username;

		// Now that the password has been checked, see if they are allowed to
		// access the system
		if (!(isset($GLOBALS['acl']))) {
			$GLOBALS['acl'] = new dPacl;
		}
		if (!($GLOBALS['acl']->checkLogin($user_id))) {
			dprint(__FILE__, __LINE__, 1, 'Permission check failed');
			return false;
		}

		$q = new DBQuery;
		$q->addTable('users');
		$q->addQuery('user_id, contact_first_name as user_first_name, '
		             . 'contact_last_name as user_last_name, contact_company as user_company, '
		             . 'contact_department as user_department, contact_email as user_email, '
		             . 'user_type');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');
		$q->addWhere("user_id = $user_id AND user_username = '$username'");
		$sql = $q->prepare();
		$q->clear();
		dprint(__FILE__, __LINE__, 7, ('Login SQL: ' . $sql));

		if (!db_loadObject($sql, $this)) {
			dprint(__FILE__, __LINE__, 1, 'Failed to load user information');
			return false;
		}

// load the user preferences
		$this->loadPrefs($this->user_id);
		$this->setUserLocale();
		$this->checkStyle();
		return true;
	}
/************************************************************************************************************************
/**
*@Function for regiser log in dotprojet table "user_access_log"
*/
	function registerLogin() {
		$q = new DBQuery;
		$q->addTable('user_access_log');
		$q->addInsert('user_id', $this->user_id);
		$q->addInsert('date_time_in', 'now()', false, true);
		$q->addInsert('user_ip', $_SERVER['REMOTE_ADDR']);
		$q->exec();
		$this->last_insert_id = db_insert_id();
		$q->clear();
	}

/**
*@Function for register log out in dotproject table "user_acces_log"
*/
	function registerLogout($user_id) {
		$q = new DBQuery;
		$q->addTable('user_access_log');
		$q->addUpdate('date_time_out', date('Y-m-d H:i:s'));
		$q->addWhere('user_id = ' . $user_id);
		$q->addWhere("(date_time_out='0000-00-00 00:00:00' OR date_time_out IS NULL)");
		$q->addWhere('user_access_log_id = ' . $this->last_insert_id);
		if ($user_id > 0) {
			$q->exec();
			$q->clear();
		}
	}

/**
*@Function for update table user_acces_log in field date_time_lost_action
*/
	function updateLastAction($last_insert_id) {
		$q  = new DBQuery;
		$q->addTable('user_access_log');
		$q->addUpdate('date_time_last_action', date('Y-m-d H:i:s'));
		$q->addWhere('user_access_log_id = ' . $last_insert_id);
		if ($last_insert_id > 0) {
			$q->exec();
			$q->clear();
		}
	}
/************************************************************************************************************************
/**
* @deprecated
*/
	function logout() {
	}
/**
* Checks whether there is any user logged in.
*/
	function doLogin() {
		return ($this->user_id < 0) ? true : false;
	}
/**
* Gets the value of the specified user preference
*
* @param string Name of the preference
* @return mixed Value of the preference, or NULL if it doesn't exist
*/
	function getPref($name) {
    if (isset($this->user_prefs[$name])) {  // check first if it exists ar all
		  return @$this->user_prefs[$name];
    }
    return null;
	}

/**
 * Gets the value of the specified system preference
 *
 * @param string Name of the preference
 * @return mixed Value of the preference, or NULL if it doesn't exist
 */
	function getSystemPref($name) {
		return ((!empty($this->system_prefs[$name])) ? $this->system_prefs[$name] : NULL);
	}
/**
* Sets the value of a user preference specified by name
*
* @param string Name of the preference
* @param mixed The value of the preference
*/
	function setPref($name, $val) {
		$this->user_prefs[$name] = $val;
	}
/**
* Loads the stored user preferences from the database into the internal
* preferences variable.
*
* Note, this is using a "feature" of loadHashList which means that repeated
* data later in the query will overwrite earlier data with the same key.  This
* means we don't need to copy default preferences across to each user, but the
* defaults become true defaults.
*
* @param int User id number
*/
  function isSystemPref($val) {
		return !empty($val) && $val['pref_user'] == 0;
        }
  function isUserPref($val) {
		return !empty($val) && $val['pref_user'] != 0;
        }
  function flattenPrefs($vals) {
		$result = [];
    if (empty($vals)) {  // better safe than sorry
      return null;
    }
		foreach ($vals as $elem) {
		  $result[$elem['pref_name']] = $elem['pref_value'];
		}
		return $result;
  }
	function loadPrefs($uid=0) {
		$q  = new DBQuery;
		$q->addTable('user_preferences');
		$q->addQuery('pref_user, pref_name, pref_value');
		if (!empty($uid)) {
			$q->addWhere("pref_user in (0, $uid)");
			$q->addOrder('pref_user');
		} else {
			$q->addWhere('pref_user = 0');
		}
		$prefs = $q->loadList();
                // Separate out system preferences from user preferences, but merge them together
		$this->system_prefs = $this->flattenPrefs(array_filter($prefs, [ $this, 'isSystemPref' ]));
		$user_prefs = [];
		if (!empty($uid)) {
			$user_prefs = $this->flattenPrefs(array_filter($prefs, [ $this, 'isUserPref' ]));
		}
		$this->user_prefs = array_merge($this->system_prefs, $this->user_prefs, $user_prefs ?? array());
	}

// --- Module connectors

/**
* Gets a list of the installed modules
* @return array Named array list in the form 'module directory'=>'module name'
*/
	function getInstalledModules() {
		$q = new DBQuery;
		$q->addTable('modules');
		$q->addQuery('mod_directory, mod_ui_name');
		$q->addOrder('mod_directory');
		return ($q->loadHashList());
	}
/**
* Gets a list of the active modules
* @return array Named array list in the form 'module directory'=>'module name'
*/
	function getActiveModules() {
		static $modlist = null;
		if (! isset($modlist)) {
			$q = new DBQuery;
			$q->addTable('modules');
			$q->addQuery('mod_directory, mod_ui_name');
			$q->addWhere('mod_active > 0');
			$q->addOrder('mod_directory');
			$modlist = $q->loadHashList();
		}
		return $modlist;
	}
/**
* Gets a list of the modules that should appear in the menu
* @return array Named array list in the form
* ['module directory', 'module name', 'module_icon']
*/
	function getMenuModules() {
		$q = new DBQuery;
		$q->addTable('modules');
		$q->addQuery('mod_directory, mod_ui_name, mod_ui_icon');
		$q->addWhere('mod_active > 0 AND mod_ui_active > 0 AND mod_directory <> \'public\'');
		$q->addWhere("mod_type != 'utility'");
		$q->addOrder('mod_ui_order');
		return ($q->loadList());
	}

	function isActiveModule($module) {
		$modlist = $this->getActiveModules();
		return !empty($modlist[$module]);
	}

/**
 * Returns the global dpACL class or creates it as neccessary.
 * @return object dPacl
 *
 * @note Temporarily removing & (discouraged under PHP 8 — gwyneth 20210425)
 */
//	function &acl() {
  function acl() {
		if (empty($GLOBALS['acl'])) {
			$GLOBALS['acl'] = new dPacl;
	  	}
	  	return $GLOBALS['acl'];
	}

/**
 * Find and add to output the file tags required to load module-specific
 * javascript.
 */
	function loadJS() {
		global $m, $a;
		// Search for the javascript files to load.
		if (empty($m)) {
			return;
		}
		$root = DP_BASE_DIR;
		if (mb_substr($root, -1) != '/') {
			$root .= '/';
		}

		$base = dPgetConfig('base_url');
		if (mb_substr($base, -1) != '/') {
			$base .= '/';
		}

		// Load the basic javascript used by all modules.
		$jsdir = dir($root . "js");

		$js_files = array();
		while (($entry = $jsdir->read()) !== false) {
			if (mb_substr($entry, -3) == '.js') {
				$js_files[] = $entry;
			}
		}
		asort($js_files);
		// while (list(,$js_file_name) = each($js_files)) {  // each() is obsolete in PHP 8 (gwyneth 20210414)
    foreach ($js_files as $js_file_name) {  // $key will be discarded
			echo ('<script src="' . $base . 'js/'
				  . $this->___($js_file_name) . '"></script>'."\n");
		}

		// additionally load overlib
		echo ('<script src="' . $base . 'lib/overlib/overlib.js"></script>'
		      . "\n");

		$this->getModuleJS($m, $a, true);

		// Finally add any external URLs
		foreach ($this->_js as $href) {
			echo '<script src="' . $href . '"></script>' . "\n";
		}
		// And any CSS scripts
		foreach ($this->_css as $href) {
			echo '<link rel="stylesheet" type="text/css" href="' . $href . '">' . "\n";
		}
	}

	function loadCSS() {

	}

	function getModuleJS($module, $file=null, $load_all = false) {
		$root = DP_BASE_DIR;
		if (mb_substr($root, -1) != '/') {
			$root .= '/';
		}
		$base = DP_BASE_URL;
		if (mb_substr($base, -1) != '/') {
			$base .= '/';
		}
		$module = $this->___($module);

		if ($load_all || !($file)) {
			if (file_exists($root . 'modules/' . $module . '/' . $module . '.module.js')) {
				echo ('<script  src="' . $base . 'modules/' . $module . '/'
				      . $module . '.module.js"></script>' . "\n");
			}
		}
		if (isset($file)) {
			$file = $this->___($file);
			if (file_exists($root . 'modules/' . $module . '/' . $file . '.js')) {
				echo ('<script  src="' . $base . 'modules/' . $module . '/'
				      . $file . '.js"></script>' . "\n");
			}
		}
	}

	/**
	 * Register loadable JS scripts
	 */
	function addJS($href) {
//		$sanitised = filter_xss($href);
    $sanitised = dPsanitiseHTML($href);
		if ($sanitised) {
			array_push($sanitised, $this->_js);
		}
	}

	/**
	 * Register loadable CSS scripts
	 */
	function addCSS($href) {
//		$sanitised = filter_xss($href);
    $sanitised = dPsanitiseHTML($href);
		if ($sanitised) {
			array_push($sanitised, $this->_css);
		}
	}
}


/**
* Tabbed box abstract class
*/
class CTabBox_core {
	/** @var array */
	var $tabs = null;
	/** @var int The active tab */
	var $active = null;
	/** @var string The base URL query string to prefix tab links */
	var $baseHRef = null;
	/** @var string The base path to prefix the include file */
	var $baseInc;
	/** @var string A javascript function that accepts two arguments,
	 the active tab, and the selected tab **/
	var $javascript = null;

	/**
	 * Constructor
	 * @param string The base URL query string to prefix tab links
	 * @param string The base path to prefix the include file
	 * @param int The active tab
	 * @param string Optional javascript method to be used to execute tabs.
	 *	Must support 2 arguments, currently active tab, new tab to activate.
   * @note Deprecated style of call
	 */
	function CTabBox_core($baseHRef='', $baseInc='', $active=0, $javascript = null) {
		$baseHRef = str_replace('&amp;', '&', $baseHRef);
		$baseHRef = htmlspecialchars($baseHRef);

		$this->tabs = array();
		$this->active = $active;
		$this->baseHRef = ($baseHRef ? ($baseHRef . '&amp;') : '?');
		$this->javascript = $javascript;
		$this->baseInc = $baseInc;
	}
  function __construct($baseHRef='', $baseInc='', $active=0, $javascript = null) {
    self::CTabBox_core($baseHRef, $baseInc, $active, $javascript);
  }

	/**
	 * Gets the name of a tab
   *
   * @param integer Number of tab to retrieve name from (I guess it's an integer — gwyneth 20210430
	 * @return string
	 */
	function getTabName($idx) {
		return $this->tabs[$idx][1];
	}

	/**
	 * Adds a tab to the object
   *
	 * @param string File to include
	 * @param string The display title/name of the tab
   * @param boolean if this already translated?
   * @key mixed
   * @return void
	 **/
	function add($file, $title, $translated = false, $key= NULL) {
		$t = array($file, $title, $translated);
		if (isset($key)) {
			$this->tabs[$key] = $t;
		} else {
 			$this->tabs[] = $t;
		}
	}

	function isTabbed() {
		global $AppUI;
		return (($this->active < 0 || @$AppUI->getPref('TABVIEW') == 2) ? false : true);
	}

	/**
	 * Displays the tabbed box
	 * This function may be overridden
	 * @param string Can't remember whether this was useful
	 */
	function show($extra='', $js_tabs = false) {
		GLOBAL $AppUI, $currentTabId, $currentTabName;
		reset($this->tabs);
		$s = '';
		// tabbed / flat view options
		if (@$AppUI->getPref('TABVIEW') == 0) {
			$s .= '<table border="0" cellpadding="2" cellspacing="0" width="100%">';
			$s .= '<tr><td nowrap="nowrap">';
			$s .= ('<a href="' . $this->baseHRef . 'tab=0">' . $AppUI->_('tabbed') . '</a> : ');
			$s .= ('<a href="' . $this->baseHRef . 'tab=-1">' . $AppUI->_('flat') . '</a>');
			$s .= ('</td>' .$extra . '</tr></table>');
			echo $s;
		} else if ($extra) {
			echo ('<table border="0" cellpadding="2" cellspacing="0" width="100%"><tr>'
				  . $extra . '</tr></table>');
		} else {
			echo '<img src="./images/shim.gif" height="10" width="1" alt="" />';
		}

		if ($this->active < 0 || @$AppUI->getPref('TABVIEW') == 2) {
			// flat view, active = -1
			echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';
			foreach ($this->tabs as $k => $v) {
				echo ('<tr><td><strong>' . ($v[2] ? $AppUI->___($v[1]) : $AppUI->_($v[1]))
					  . '</strong></td></tr>');
				echo '<tr><td>';
				$currentTabId = $k;
				$currentTabName = $v[1];
				include $this->baseInc.$v[0].'.php';
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
			// tabbed view
			$s = '<table width="100%" border="0" cellpadding="3" cellspacing="0">'."\n".'<tr>';
			if (count($this->tabs)-1 < $this->active) {
				//Last selected tab is not available in this view. eg. Child tasks
				// Breaks classic view.
				// $this->active = 0;
			}
			foreach ($this->tabs as $k => $v) {
				$class = ($k == $this->active) ? 'tabon' : 'taboff';
				$s .= "\n\t" . '<td width="1%" nowrap="nowrap" class="tabsp">';
				$s .= "\n\t\t" . '<img src="./images/shim.gif" height="1" width="1" alt="" />';
				$s .= "\n\t" . '</td>';
				$s .= "\n\t" . '<td id="toptab_' . $k . '" width="1%" nowrap="nowrap"';
				if ($js_tabs) {
					$s .= ' class="' . $class . '"';
				}
				$s .= '>';
				$s .= "\n\t\t" . '<a href="';
				if ($this->javascript) {
					$s .= 'javascript:' . $this->javascript . '(' . $this->active . ', ' . $k . ')';
				} else if ($js_tabs) {
					$s .= 'javascript:show_tab(' . $k . ')';
				} else {
					$s .= $this->baseHRef . 'tab=' . $k;
				}
				$s .= '">'. ($v[2] ? $v[1] : $AppUI->_($v[1])). '</a>';
				$s .= "\n\t" . '</td>';
			}
			$s .= "\n\t" . '<td nowrap="nowrap" class="tabsp">&nbsp;</td>';
			$s .= "\n</tr>";
			$s .= "\n<tr>";
			$s .= '<td width="100%" colspan="' . (count($this->tabs)*2 + 1) . '" class="tabox">';
			echo $s;

			//Will be null if the previous selection tab is not available in the new window
			// eg. Children tasks
			if ($this->baseInc . $this->tabs[$this->active][0] != "") {
				$currentTabId = $this->active;
				$currentTabName = $this->tabs[$this->active][1];
				if (!$js_tabs) {
					require $this->baseInc.$this->tabs[$this->active][0] . '.php';
				}
			}
			if ($js_tabs) {
				foreach ($this->tabs as $k => $v) {
					echo '<div class="tab" id="tab_' . $k . '">';
					require $this->baseInc.$v[0] . '.php';
					echo '</div>';
				}
			}
			echo "\n</td>\n</tr>\n</table>";
		}
	}

	function loadExtras($module, $file = null) {
		global $AppUI, $acl;
		if (! (isset($_SESSION['all_tabs']) && isset($_SESSION['all_tabs'][$module]))) {
			return false;
		}

		if ($file) {
			if (isset($_SESSION['all_tabs'][$module][$file])
			    && is_array($_SESSION['all_tabs'][$module][$file])) {
				$tab_array =& $_SESSION['all_tabs'][$module][$file];
			} else {
				return false;
			}
		} else {
			$tab_array =& $_SESSION['all_tabs'][$module];
		}
		$tab_count = 0;
		foreach ($tab_array as $tab_elem) {
			if (isset($tab_elem['module']) && $AppUI->isActiveModule($tab_elem['module'])) {
				$tab_count++;
				$this->add($tab_elem['file'], $tab_elem['name']);
			}
		}
		return $tab_count;
	}

	function findTabModule($tab) {
		global $AppUI, $m, $a;

		if (! (isset($_SESSION['all_tabs']) && isset($_SESSION['all_tabs'][$m]))) {
			return false;
		}
		if (isset($a)) {
			if (isset($_SESSION['all_tabs'][$m][$a]) && is_array($_SESSION['all_tabs'][$m][$a])) {
				$tab_array =& $_SESSION['all_tabs'][$m][$a];
			} else {
				$tab_array =& $_SESSION['all_tabs'][$m];
			}
		} else {
			$tab_array =& $_SESSION['all_tabs'][$m];
		}

		list($file, $name) = $this->tabs[$tab];
		foreach ($tab_array as $tab_elem) {
			if (isset($tab_elem['name']) && $tab_elem['name'] == $name
			    && $tab_elem['file'] == $file) {
				return $tab_elem['module'];
			}
		}
		return false;
	}
}


/**
* Title box abstract class
*/
class CTitleBlock_core {
	/** @var string The main title of the page */
	var $title='';
	/** @var string The name of the icon used to the left of the title */
	var $icon='';
	/** @var string The name of the module that this title block is displaying in */
	var $module='';
	/** @var array An array of the table 'cells' to the right of the title block
	 and for bread-crumbs */
	var $cells=null;
	/** @var string The reference for the context help system */
	var $helpref='';

	/**
	 * The constructor
	 *
	 * Assigns the title, icon, module and help reference.  If the user does not
	 * have permission to view the help module, then the context help icon is
	 * not displayed.
   *
   * @note Deprecated constructor style
	 */
	function CTitleBlock_core($title, $icon='', $module='', $helpref='') {
		$this->title = $title;
		$this->icon = $icon;
		$this->module = $module;
		$this->helpref = $helpref;
		$this->cells1 = array();
		$this->cells2 = array();
		$this->crumbs = array();
		$this->showhelp = getPermission('help', 'view');
	}
  // Modern style of calling
  function __construct($title, $icon='', $module='', $helpref='') {
    self::CTitleBlock_core($title, $icon, $module, $helpref);
  }


	/**
	 * Adds a table 'cell' beside the Title string
	 *
	 * Cells are added from left to right.
	 */
	function addCell($data='', $attribs='', $prefix='', $suffix='') {
		$this->cells1[] = array($attribs, $data, $prefix, $suffix);
	}
	/**
	 * Adds a table 'cell' to left-aligned bread-crumbs
	 *
	 * Cells are added from left to right.
	 */
	function addCrumb($link, $label, $icon='') {
		//$link = str_replace('&amp;', '&', $link);
		//$link = htmlspecialchars($link);
		$link = dPsanitiseHTML($link);
		$this->crumbs[$link] = array($label, $icon);
	}
	/**
	 * Adds a table 'cell' to the right-aligned bread-crumbs
	 *
	 * Cells are added from left to right.
	 */
	function addCrumbRight($data='', $attribs='', $prefix='', $suffix='') {
		$this->cells2[] = array($attribs, $data, $prefix, $suffix);
	}
	/**
	 * Creates a standarised, right-aligned delete bread-crumb and icon.
	 */
	function addCrumbDelete($title, $canDelete='', $msg='') {
		global $AppUI;
		$this->addCrumbRight('<table cellspacing="0" cellpadding="0" border="0"><tr><td>'
		                     . '<a href="javascript:delIt()" title="'
		                     . $AppUI->_($canDelete ? '' : $msg).'">'
		                     . dPshowImage('./images/icons/'
		                                   . (($canDelete) ? 'stock_delete-16.png'
		                                      : 'stock_trash_full-16.png'), '16', '16', '')
		                     . '</a></td><td>&nbsp;'
		                     . '<a href="javascript:delIt()" title="'
		                     . $AppUI->_(($canDelete) ? '' : $msg)
		                     . '">' . $AppUI->_($title) . '</a></td></tr></table>');
	}
	/**
	 * The drawing function
	 */
	function show() {
		global $AppUI;
		$CR = "\n";
		$CT = "\n\t";
		$s = "\n" . '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
		$s .= "\n" . '<tr>';
		if ($this->icon) {
			$s .= "\n" . '<td width="42">';
			$s .= dPshowImage(dPFindImage($this->icon, $this->module));
			$s .= '</td>';
		}
		$s .= ("\n" . '<td align="left" width="100%" nowrap="nowrap"><h1>'
		       . $AppUI->_($this->title) . '</h1></td>');
    if (!empty($this->cells1)) {
  		foreach ($this->cells1 as $c) {
  			$s .= "\n" . '<td align="right" nowrap="nowrap"' . ($c[0] ? (' '.$c[0]): '') . '>';
  			$s .= $c[2] ? "\n" . $c[2] : '';
  			$s .= $c[1] ? "\n\t" . $c[1] : '&nbsp;';
  			$s .= $c[3] ? "\n" . $c[3] : '';
  			$s .= "\n" . '</td>';
  		}
    }
		if (!empty($this->showhelp)) {
			$s .= '<td nowrap="nowrap" width="20" align="right">';
			/*
			$s .= ("\n\t"
				   . dPcontextHelp(('<img src="./images/obj/help.gif" width="14" height="16" '
			                        . 'border="0" alt="'.$AppUI->_('Help').'" />'),
			                       $this->helpref));
			*/
			$s .= ("\n\t" . '<a href="#' . $this->helpref
			       . '" onClick="javascript:window.open(\'?m=help&dialog=1&hid='
				   . $this->helpref
			       . "', 'contexthelp', 'width=400,height=400,left=50,top=50,scrollbars=yes,"
			       . 'resizable=yes\')" title="' . $AppUI->_('Help') . '">');
			$s .= "\n\t\t" . dPshowImage('./images/icons/stock_help-16.png', '16', '16',
			                             $AppUI->_('Help'));
			$s .= "\n\t" . '</a>';
			$s .= "\n</td>";
		}
		$s .= "\n</tr>";
		$s .= "\n</table>";

		if ((!empty($this->crumbs) && count($this->crumbs)) || (!empty($this->cells2) && count($this->cells2))) {
			$crumbs = array();
			foreach ($this->crumbs as $k => $v) {
				$t = (($v[1]) ? ('<img src="' . dPfindImage($v[1], $this->module)
				                 . '" border="" alt="" />&nbsp;') : '');
				$t .= $AppUI->_($v[0]);
				$crumbs[] = ('<a href="'. $k .'">' . $t . '</a>');
			}
			$s .= "\n" . '<table border="0" cellpadding="4" cellspacing="0" width="100%">';
			$s .= "\n<tr>";
			$s .= "\n\t" . '<td nowrap="nowrap">';
			$s .= "\n\t\t" . '<strong>' . implode(' : ', $crumbs) . '</strong>';
			$s .= "\n\t" . '</td>';

      if (!empty($this->cells2)) {
  			foreach ($this->cells2 as $c) {
  				$s .= $c[2] ? "\n$c[2]" : '';
  				$s .= "\n\t" . '<td align="right" nowrap="nowrap"' . ($c[0] ? " $c[0]" : '') . '>';
  				$s .= $c[1] ? "\n\t$c[1]" : '&nbsp;';
  				$s .= "\n\t" . '</td>';
  				$s .= $c[3] ? "\n\t".$c[3] : '';
  			}
      }
			$s .= "\n</tr>\n</table>";
		}
		echo "$s";
	}
}


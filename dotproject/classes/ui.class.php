<?php /* CLASSES $Id$ */
/**
* @package dotproject
* @subpackage core
* @license http://opensource.org/licenses/gpl-license.php GPL License Version 2
*/

if (! defined('DP_BASE_DIR')) {
	die('This file should not be called directly');
}

// Message No Constants
define( 'UI_MSG_OK', 1 );
define( 'UI_MSG_ALERT', 2 );
define( 'UI_MSG_WARNING', 3 );
define( 'UI_MSG_ERROR', 4 );

// global variable holding the translation array
$GLOBALS['translate'] = array();

define( "UI_CASE_MASK", 0x0F );
define( "UI_CASE_UPPER", 1 );
define( "UI_CASE_LOWER", 2 );
define( "UI_CASE_UPPERFIRST", 3 );

define ("UI_OUTPUT_MASK", 0xF0);
define ("UI_OUTPUT_HTML", 0);
define ("UI_OUTPUT_JS", 0x10);
define ("UI_OUTPUT_RAW", 0x20);

// DP_BASE_DIR is set in base.php and fileviewer.php and is the base directory
// of the dotproject installation.
require_once DP_BASE_DIR."/classes/permissions.class.php";
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

// localisation
/** @var string */
	var $user_locale=null;
/** @var string */
	var $user_lang=null;
/** @var string */
	var $base_locale = 'en'; // do not change - the base 'keys' will always be in english

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
/**
* CAppUI Constructor
*/
	function CAppUI() {
		global $dPconfig;

		$this->state = array();

		$this->user_id = -1;
		$this->user_first_name = '';
		$this->user_last_name = '';
		$this->user_company = 0;
		$this->user_department = 0;
		$this->user_type = 0;

		// cfg['locale_warn'] is the only cfgVariable stored in session data (for security reasons)
		// this guarants the functionality of this->setWarning
		$this->cfg['locale_warn'] = $dPconfig['locale_warn'];
		
		$this->project_id = 0;

		$this->defaultRedirect = "";
// set up the default preferences
		$this->setUserLocale($this->base_locale);
		$this->user_prefs = array();
	}
/**
* Used to load a php class file from the system classes directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
	function getSystemClass( $name=null ) {
		if ($name) {
			return DP_BASE_DIR."/classes/$name.class.php";
		}
	}

/**
* Used to load a php class file from the lib directory
*
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
*/
	function getLibraryClass( $name=null ) {
		if ($name) {
			return DP_BASE_DIR."/lib/$name.php";
		}
	}

/**
* Used to load a php class file from the module directory
* @param string $name The class root file name (excluding .class.php)
* @return string The path to the include file
 */
	function getModuleClass( $name=null ) {
		if ($name) {
			return DP_BASE_DIR."/modules/$name/$name.class.php";
		}
	}

/**
* Determines the version.
* @return String value indicating the current dotproject version
*/
	function getVersion() {
		global $dPconfig;
		if ( ! isset($this->version_major)) {
			include_once DP_BASE_DIR . '/includes/version.php';
			$this->version_major = $dp_version_major;
			$this->version_minor = $dp_version_minor;
			$this->version_patch = $dp_version_patch;
			$this->version_string = $this->version_major . "." . $this->version_minor;
			if (isset($this->version_patch))
			  $this->version_string .= "." . $this->version_patch;
			if (isset($dp_version_prepatch))
			  $this->version_string .= "-" . $dp_version_prepatch;
		}
		return $this->version_string;
	}

/**
* Checks that the current user preferred style is valid/exists.
*/
	function checkStyle() {
		global $dPconfig;
		// check if default user's uistyle is installed
		$uistyle = $this->getPref("UISTYLE");

		if ($uistyle && !is_dir(DP_BASE_DIR."/style/$uistyle")) {
			// fall back to host_style if user style is not installed
			$this->setPref( 'UISTYLE', $dPconfig['host_style'] );
		}
	}

/**
* Utility function to read the 'directories' under 'path'
*
* This function is used to read the modules or locales installed on the file system.
* @param string The path to read.
* @return array A named array of the directories (the key and value are identical).
*/
	function readDirs( $path ) {
		$dirs = array();
		$d = dir( DP_BASE_DIR."/$path" );
		while (false !== ($name = $d->read())) {
			if(is_dir( DP_BASE_DIR."/$path/$name" ) && $name != "." && $name != ".." && $name != "CVS") {
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
	function readFiles( $path, $filter='.' ) {
		$files = array();

		if (is_dir($path) && ($handle = opendir( $path )) ) {
			while (false !== ($file = readdir( $handle ))) {
				if ($file != "." && $file != ".." && preg_match( "/$filter/", $file )) { 
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
	function checkFileName( $file ) {
		global $AppUI;

		// define bad characters and their replacement
		$bad_chars = ";/\\";
		$bad_replace = "...."; // Needs the same number of chars as $bad_chars

		// check whether the filename contained bad characters
		if ( strpos( strtr( $file, $bad_chars, $bad_replace), '.') !== false ) {
			$AppUI->redirect( "m=public&a=access_denied" );
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
	function makeFileNameSafe( $file ) {
		$file = str_replace( '../', '', $file );
		$file = str_replace( '..\\', '', $file );
		return $file;
	}

/**
* Sets the user locale.
*
* Looks in the user preferences first.  If this value has not been set by the user it uses the system default set in config.php.
* @param string Locale abbreviation corresponding to the sub-directory name in the locales directory (usually the abbreviated language code).
*/
	function setUserLocale( $loc='', $set = true ) {
		global $dPconfig, $locale_char_set;

		$LANGUAGES = $this->loadLanguages();

		if (! $loc) {
			$loc = @$this->user_prefs['LOCALE'] ? $this->user_prefs['LOCALE'] : $dPconfig['host_locale'];
		}

		if (isset($LANGUAGES[$loc]))
			$lang = $LANGUAGES[$loc];
		else {
			// Need to try and find the language the user is using, find the first one
			// that has this as the language part
			if (strlen($loc) > 2) {
				list ($l, $c) = explode('_', $loc);
				$loc = $this->findLanguage($l, $c);
			} else {
				$loc = $this->findLanguage($loc);
			}
			$lang = $LANGUAGES[$loc];
		}
		list($base_locale, $english_string, $native_string, $default_language, $lcs) = $lang;
		if (! isset($lcs))
			$lcs = (isset($locale_char_set)) ? $locale_char_set : 'utf-8';

		if (version_compare(phpversion(), '4.3.0', 'ge'))
			$user_lang = array( $loc . '.' . $lcs, $default_language, $loc, $base_locale);
		else {
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$user_lang = $default_language;
			} else {
				$user_lang = $loc . '.' . $lcs;
			}
		}
		if ($set) {
			$this->user_locale = $base_locale;
			$this->user_lang = $user_lang;
			$locale_char_set = $lcs;
		} else {
			return $user_lang;
		}
	}

	function findLanguage($language, $country = false)
	{
		$LANGUAGES = $this->loadLanguages();
		$language = strtolower($language);
		if ($country) {
			$country = strtoupper($country);
			// Try constructing the code again
			$code = $language . '_' . $country;
			if (isset($LANGUAGES[$code]))
				return $code;
		}

		// Just use the country code and try and find it in the
		// languages list.
		$first_entry = null;
		foreach ($LANGUAGES as $lang => $info) {
			list($l, $c) = explode('_', $lang);
			if ($l == $language) {
				if (! $first_entry)
					$first_entry = $lang;
				if ($country && $c == $country)
					return $lang;
			}
		}
		return $first_entry;
	}

/**
 * Load the known language codes for loaded locales
 *
 */
	function loadLanguages() {

		if ( isset($_SESSION['LANGUAGES'])) {
			$LANGUAGES =& $_SESSION['LANGUAGES'];
		} else {
			$LANGUAGES = array();
			$langs = $this->readDirs('locales');
			foreach ($langs as $lang) {
				if (file_exists(DP_BASE_DIR."/locales/$lang/lang.php")) {
					include_once DP_BASE_DIR."/locales/$lang/lang.php";
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
	function _( $str, $flags= 0 ) {
		if (is_array($str)) {
			$translated = array();
			foreach ($str as $s)
				$translated[] = $this->__($s, $flags);
			return implode(' ', $translated);
		} else {
			return $this->__($str, $flags);
		}
	}

	function __( $str, $flags = 0) {
		global $dPconfig;
		$str = trim($str);
		if (empty( $str )) {
			return '';
		}
		$x = @$GLOBALS['translate'][$str];
		
		if ($x) {
			$str = $x;
		} else if (@$dPconfig['locale_warn']) {
			if ($this->base_locale != $this->user_locale ||
				($this->base_locale == $this->user_locale && !in_array( $str, @$GLOBALS['translate'] )) ) {
				$str .= @$dPconfig['locale_alert'];
			}
		}
		switch ($flags & UI_CASE_MASK) {
			case UI_CASE_UPPER:
				$str = strtoupper( $str );
				break;
			case UI_CASE_LOWER:
				$str = strtolower( $str );
				break;
			case UI_CASE_UPPERFIRST:
				$str = ucwords( $str );
				break;
		}
		/* Altered to support multiple styles of output, to fix
		 * bugs where the same output style cannot be used succesfully
		 * for both javascript and HTML output.
		 * PLEASE NOTE: The default is currently UI_OUTPUT_HTML,
		 * which is different to the previous version (which was
		 * effectively UI_OUTPUT_RAW).  If this causes problems,
		 * and they are localised, then use UI_OUTPUT_RAW in the
		 * offending call.  If they are widespread, change the
		 * default to UI_OUTPUT_RAW and use the other options
		 * where appropriate.
		 * AJD - 2004-12-10
		 */
                global $locale_char_set;

		if (! $locale_char_set) {
			$locale_char_set = 'utf-8';
		}
                
		switch ($flags & UI_OUTPUT_MASK) {
			case UI_OUTPUT_HTML:
				$str = htmlentities(stripslashes($str), ENT_COMPAT, $locale_char_set);
				break;
			case UI_OUTPUT_JS:
				$str = addslashes(stripslashes($str)); //, ENT_COMPAT, $locale_char_set);
				break;
			case UI_OUTPUT_RAW: 
				$str = stripslashes($str);
				break;
		}
		return $str;
	}
/**
* Set the display of warning for untranslated strings
* @param string
*/
	function setWarning( $state=true ) {
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
	function savePlace( $query='' ) {
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
	function redirect( $params='', $hist='' ) {
		$session_id = SID;

		session_write_close();
	// are the params empty
		if (!$params) {
		// has a place been saved
			$params = !empty($this->state["SAVEDPLACE$hist"]) ? $this->state["SAVEDPLACE$hist"] : $this->defaultRedirect;
		}
		// Fix to handle cookieless sessions
		if ($session_id != "") {
		  if (!$params)
		    $params = $session_id;
		  else
		    $params .= "&" . $session_id;
		}
		ob_implicit_flush(); // Ensure any buffering is disabled.
		header( "Location: index.php?$params" );
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
	function setMsg( $msg, $msgNo=0, $append=false ) {
		$msg = $this->_( $msg );
		$this->msg = $append ? $this->msg.' '.$msg : $msg;
		$this->msgNo = $msgNo;
	}
/**
* Display the formatted message and icon
* @param boolean If true the current message state is cleared.
*/
	function getMsg( $reset=true ) {
		$img = '';
		$class = '';
		$msg = $this->msg;

		switch( $this->msgNo ) {
		case UI_MSG_OK:
			$img = dPshowImage( dPfindImage( 'stock_ok-16.png' ), 16, 16, '' );
			$class = "message";
			break;
		case UI_MSG_ALERT:
			$img = dPshowImage( dPfindImage( 'rc-gui-status-downgr.png' ), 16, 16, '' );
			$class = "message";
			break;
		case UI_MSG_WARNING:
			$img = dPshowImage( dPfindImage( 'rc-gui-status-downgr.png' ), 16, 16, '' );
			$class = "warning";
			break;
		case UI_MSG_ERROR:
			$img = dPshowImage( dPfindImage( 'stock_cancel-16.png' ), 16, 16, '' );
			$class = "error";
			break;
		default:
			$class = "message";
			break;
		}
		if ($reset) {
			$this->msg = '';
			$this->msgNo = 0;
		}
		return $msg ? '<table cellspacing="0" cellpadding="1" border="0"><tr>'
			. "<td>$img</td>"
			. "<td class=\"$class\">$msg</td>"
			. '</tr></table>'
			: '';
	}
/**
* Set the value of a temporary state variable.
*
* The state is only held for the duration of a session.  It is not stored in the database.
* Also do not set the value if it is unset.
* @param string The label or key of the state variable
* @param mixed Value to assign to the label/key
*/
	function setState( $label, $value = null) {
		if (isset($value))
			$this->state[$label] = $value;
	}
/**
* Get the value of a temporary state variable.
* If a default value is supplied and no value is found, set the default.
* @return mixed
*/
	function getState( $label, $default_value = null ) {
		if (array_key_exists( $label, $this->state)) {
			return $this->state[$label];
		} else if (isset($default_value)) {
			$this->setState($label, $default_value);
			return $default_value;
		} else  {
			return NULL;
		}
	}

	function checkPrefState($label, $value, $prefname, $default_value = null) {
		// Check if we currently have it set
		if (isset($value)) {
			$result = $value;
			$this->state[$label] = $value;
		} else if (array_key_exists($label, $this->state)) {
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
	function login( $username, $password ) {
		global $dPconfig;

		require_once DP_BASE_DIR."/classes/authenticator.class.php";

		$auth_method = isset($dPconfig['auth_method']) ? $dPconfig['auth_method'] : 'sql';
		if (@$_POST['login'] != 'login' && @$_POST['login'] != $this->_('login') && $_REQUEST['login'] != $auth_method)
			die("You have chosen to log in using an unsupported or disabled login method");
		$auth =& getauth($auth_method);
		
		$username = trim( db_escape( $username ) );
		$password = trim($password);

		if (!$auth->authenticate($username, $password)) {
			return false;
		}
	
		$user_id = $auth->userId($username);
		$username = $auth->username; // Some authentication schemes may collect username in various ways.
		// Now that the password has been checked, see if they are allowed to
		// access the system
		if (! isset($GLOBALS['acl']))
		  $GLOBALS['acl'] =& new dPacl;
		if ( ! $GLOBALS['acl']->checkLogin($user_id)) {
		  dprint(__FILE__, __LINE__, 1, "Permission check failed");
		  return false;
		}

		$q  = new DBQuery;
		$q->addTable('users');
		$q->addQuery('user_id, contact_first_name as user_first_name, contact_last_name as user_last_name, contact_company as user_company, contact_department as user_department, contact_email as user_email, user_type');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');
		$q->addWhere("user_id = $user_id AND user_username = '$username'");
		$sql = $q->prepare();
		$q->clear();
		dprint(__FILE__, __LINE__, 7, "Login SQL: $sql");

		if( !db_loadObject( $sql, $this ) ) {
			dprint(__FILE__, __LINE__, 1, "Failed to load user information");
			return false;
		}

// load the user preferences
		$this->loadPrefs( $this->user_id );
		$this->setUserLocale();
		$this->checkStyle();
		return true;
	}
/************************************************************************************************************************	
/**
*@Function for regiser log in dotprojet table "user_access_log"
*/
	   function registerLogin(){
		$q  = new DBQuery;
		$q->addTable('user_access_log');
		$q->addInsert('user_id', "$this->user_id");
		$q->addInsert('date_time_in', 'now()', false, true);
		$q->addInsert('user_ip', $_SERVER['REMOTE_ADDR']);
                $q->exec();
                $this->last_insert_id = db_insert_id();
								$q->clear();
           }

/**
*@Function for register log out in dotproject table "user_acces_log"
*/
          function registerLogout($user_id){
		$q  = new DBQuery;
		$q->addTable('user_access_log');
		$q->addUpdate('date_time_out', date("Y-m-d H:i:s"));
		$q->addWhere("user_id = '$user_id' and (date_time_out='0000-00-00 00:00:00' or isnull(date_time_out)) ");
		if ($user_id > 0){
			$q->exec();
			$q->clear();
		}
          }
          
/**
*@Function for update table user_acces_log in field date_time_lost_action
*/
          function updateLastAction($last_insert_id){
		$q  = new DBQuery;
		$q->addTable('user_access_log');
		$q->addUpdate('date_time_last_action', date("Y-m-d H:i:s"));
		$q->addWhere("user_access_log_id = $last_insert_id");
                if ($last_insert_id > 0){
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
* @param string Name of the preference
*/
	function getPref( $name ) {
		return @$this->user_prefs[$name];
	}
/**
* Sets the value of a user preference specified by name
* @param string Name of the preference
* @param mixed The value of the preference
*/
	function setPref( $name, $val ) {
		$this->user_prefs[$name] = $val;
	}
/**
* Loads the stored user preferences from the database into the internal
* preferences variable.
* @param int User id number
*/
	function loadPrefs( $uid=0 ) {
		$q  = new DBQuery;
		$q->addTable('user_preferences');
		$q->addQuery('pref_name, pref_value');
		$q->addWhere("pref_user = $uid");
		$prefs = $q->loadHashList();
		$this->user_prefs = array_merge( $this->user_prefs, $prefs );
	}

// --- Module connectors

/**
* Gets a list of the installed modules
* @return array Named array list in the form 'module directory'=>'module name'
*/
	function getInstalledModules() {
		$q  = new DBQuery;
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
		$q  = new DBQuery;
		$q->addTable('modules');
		$q->addQuery('mod_directory, mod_ui_name');
		$q->addWhere('mod_active > 0');
		$q->addOrder('mod_directory');
		return ($q->loadHashList());
	}
/**
* Gets a list of the modules that should appear in the menu
* @return array Named array list in the form
* ['module directory', 'module name', 'module_icon']
*/
	function getMenuModules() {
		$q  = new DBQuery;
		$q->addTable('modules');
		$q->addQuery('mod_directory, mod_ui_name, mod_ui_icon');
		$q->addWhere("mod_active > 0 AND mod_ui_active > 0 AND mod_directory <> 'public'");
		$q->addOrder('mod_ui_order');
		return ($q->loadList());
	}

	function isActiveModule($module) {
		$q  = new DBQuery;
		$q->addTable('modules');
		$q->addQuery('mod_active');
		$q->addWhere("mod_directory = '$module'");
		$sql = $q->prepare();
		$q->clear();
		return db_loadResult($sql);
	}

/**
 * Returns the global dpACL class or creates it as neccessary.
 * @return object dPacl
 */
	function &acl() {
		if (! isset($GLOBALS['acl'])) {
			$GLOBALS['acl'] =& new dPacl;
	  	}
	  	return $GLOBALS['acl'];
	}

/**
 * Find and add to output the file tags required to load module-specific
 * javascript.
 */
	function loadJS() {
	  global $m, $a, $dPconfig;
	  // Search for the javascript files to load.
	  if (! isset($m))
	    return;
	  $root = DP_BASE_DIR;
	  if (substr($root, -1) != '/')
	    $root .= '/';

	  $base = $dPconfig['base_url'];
	  if ( substr($base, -1) != '/')
	    $base .= '/';
	  // Load the basic javascript used by all modules.
	  $jsdir = dir("{$root}js");

	  $js_files = array();
	  while (($entry = $jsdir->read()) !== false) {
	    if (substr($entry, -3) == '.js'){
		    $js_files[] = $entry;
	    }
	  }
	  asort($js_files);
	  while(list(,$js_file_name) = each($js_files)){
		  echo "<script type=\"text/javascript\" src=\"{$base}js/$js_file_name\"></script>\n";
		  }

		// additionally load overlib
			echo "<script type=\"text/javascript\" src=\"{$base}lib/overlib/overlib.js\"></script>\n";

		$this->getModuleJS($m, $a, true);
	}

	function getModuleJS($module, $file=null, $load_all = false) {
		global $dPconfig;
		$root = DP_BASE_DIR;
		if (substr($root, -1) != '/');
			$root .= '/';
		$base = $dPconfig['base_url'];
		if (substr($base, -1) != '/') 
			$base .= '/';
		if ($load_all || ! $file) {
			if (file_exists("{$root}modules/$module/$module.module.js"))
				echo "<script type=\"text/javascript\" src=\"{$base}modules/$module/$module.module.js\"></script>\n";
		}
	  if (isset($file) && file_exists("{$root}modules/$module/$file.js"))
	    echo "<script type=\"text/javascript\" src=\"{$base}modules/$module/$file.js\"></script>\n";
	}

}

/**
* Tabbed box abstract class
*/
class CTabBox_core {
/** @var array */
	var $tabs=NULL;
/** @var int The active tab */
	var $active=NULL;
/** @var string The base URL query string to prefix tab links */
	var $baseHRef=NULL;
/** @var string The base path to prefix the include file */
	var $baseInc;
/** @var string A javascript function that accepts two arguments,
the active tab, and the selected tab **/
	var $javascript = NULL;

/**
* Constructor
* @param string The base URL query string to prefix tab links
* @param string The base path to prefix the include file
* @param int The active tab
* @param string Optional javascript method to be used to execute tabs.
*	Must support 2 arguments, currently active tab, new tab to activate.
*/
	function CTabBox_core( $baseHRef='', $baseInc='', $active=0, $javascript = null ) {
		$this->tabs = array();
		$this->active = $active;
		$this->baseHRef = ($baseHRef ? "$baseHRef&" : "?");
		$this->javascript = $javascript;
		$this->baseInc = $baseInc;
	}
/**
* Gets the name of a tab
* @return string
*/
	function getTabName( $idx ) {
		return $this->tabs[$idx][1];
	}
/**
* Adds a tab to the object
* @param string File to include
* @param The display title/name of the tab
*/
	function add( $file, $title, $translated = false, $key= NULL ) {
		$t = array( $file, $title, $translated);
		if (isset($key))
			$this->tabs[$key] = $t;
		else
 			$this->tabs[] = $t;
	}

	function isTabbed() {
		global $AppUI;
		if ($this->active < 0 || @$AppUI->getPref( 'TABVIEW' ) == 2 )
			return false;
		return true;
	}

/**
* Displays the tabbed box
*
* This function may be overridden
*
* @param string Can't remember whether this was useful
*/
	function show( $extra='', $js_tabs = false ) {
		GLOBAL $AppUI, $currentTabId, $currentTabName;
		reset( $this->tabs );
		$s = '';
	// tabbed / flat view options
		if (@$AppUI->getPref( 'TABVIEW' ) == 0) {
			$s .= '<table border="0" cellpadding="2" cellspacing="0" width="100%"><tr><td nowrap="nowrap">';
			$s .= '<a href="'.$this->baseHRef.'tab=0">'.$AppUI->_('tabbed').'</a> : ';
			$s .= '<a href="'.$this->baseHRef.'tab=-1">'.$AppUI->_('flat').'</a>';
			$s .= '</td>'.$extra.'</tr></table>';
			echo $s;
		} else {
			if ($extra) {
				echo '<table border="0" cellpadding="2" cellspacing="0" width="100%"><tr>'.$extra.'</tr></table>';
			} else {
				echo '<img src="./images/shim.gif" height="10" width="1" />';
			}
		}

		if ($this->active < 0 || @$AppUI->getPref( 'TABVIEW' ) == 2 ) {
		// flat view, active = -1
			echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';
			foreach ($this->tabs as $k => $v) {
				echo '<tr><td><strong>'.($v[2] ? $v[1] : $AppUI->_($v[1])).'</strong></td></tr>';
				echo '<tr><td>';
				$currentTabId = $k;
				$currentTabName = $v[1];
				include $this->baseInc.$v[0].".php";
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
		// tabbed view
			$s = "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n<tr>";
			if ( count($this->tabs)-1 < $this->active ) {
				//Last selected tab is not available in this view. eg. Child tasks
				$this->active = 0;
			}
			foreach( $this->tabs as $k => $v ) {
				$class = ($k == $this->active) ? 'tabon' : 'taboff';
				$s .= "\n\t<td width=\"1%\" nowrap=\"nowrap\" class=\"tabsp\">";
				$s .= "\n\t\t<img src=\"./images/shim.gif\" height=\"1\" width=\"1\" alt=\"\" />";
				$s .= "\n\t</td>";
				$s .= "\n\t<td id=\"toptab_" . $k . "\" width=\"1%\" nowrap=\"nowrap\"";
				if ($js_tabs)
					$s .= " class=\"$class\"";
				$s .= ">";
				$s .= "\n\t\t<a href=\"";
				if ($this->javascript)
					$s .= "javascript:" . $this->javascript . "({$this->active}, $k)";
				else if ($js_tabs)
					$s .= 'javascript:show_tab(' . $k . ')';
				else
					$s .= $this->baseHRef . "tab=$k";
				$s .= "\">". ($v[2] ? $v[1] : $AppUI->_($v[1])). "</a>";
				$s .= "\n\t</td>";
			}
			$s .= "\n\t<td nowrap=\"nowrap\" class=\"tabsp\">&nbsp;</td>";
			$s .= "\n</tr>";
			$s .= "\n<tr>";
			$s .= '<td width="100%" colspan="'.(count($this->tabs)*2 + 1).'" class="tabox">';
			echo $s;
			//Will be null if the previous selection tab is not available in the new window eg. Children tasks
			if ( $this->baseInc.$this->tabs[$this->active][0] != "" ) {
				$currentTabId = $this->active;
				$currentTabName = $this->tabs[$this->active][1];
				if (!$js_tabs)
					require $this->baseInc.$this->tabs[$this->active][0].'.php';
			}
			if ($js_tabs)
			{
				foreach( $this->tabs as $k => $v ) 
				{
					echo '<div class="tab" id="tab_'.$k.'">';
					require $this->baseInc.$v[0].'.php';
					echo '</div>';
				}
			}
			echo "\n</td>\n</tr>\n</table>";
		}
	}

	function loadExtras($module, $file = null) {
		global $AppUI;
		if (! isset($_SESSION['all_tabs']) || ! isset($_SESSION['all_tabs'][$module]))
			return false;

		if ($file) {
			if (isset($_SESSION['all_tabs'][$module][$file]) && is_array($_SESSION['all_tabs'][$module][$file])) {
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

		if (! isset($_SESSION['all_tabs']) || ! isset($_SESSION['all_tabs'][$m]))
			return false;

		if (isset($a)) {
			if (isset($_SESSION['all_tabs'][$m][$a]) && is_array($_SESSION['all_tabs'][$m][$a]))
				$tab_array =& $_SESSION['all_tabs'][$m][$a];
			else
				$tab_array =& $_SESSION['all_tabs'][$m];
		} else {
			$tab_array =& $_SESSION['all_tabs'][$m];
		}

		list($file, $name) = $this->tabs[$tab];
		foreach ($tab_array as $tab_elem) {
			if (isset($tab_elem['name']) && $tab_elem['name'] == $name && $tab_elem['file'] == $file)
				return $tab_elem['module'];
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
/** @var array An array of the table 'cells' to the right of the title block and for bread-crumbs */
	var $cells=null;
/** @var string The reference for the context help system */
	var $helpref='';
/**
* The constructor
*
* Assigns the title, icon, module and help reference.  If the user does not
* have permission to view the help module, then the context help icon is
* not displayed.
*/
	function CTitleBlock_core( $title, $icon='', $module='', $helpref='' ) {
		$this->title = $title;
		$this->icon = $icon;
		$this->module = $module;
		$this->helpref = $helpref;
		$this->cells1 = array();
		$this->cells2 = array();
		$this->crumbs = array();
		$this->showhelp = !getDenyRead( 'help' );
	}
/**
* Adds a table 'cell' beside the Title string
*
* Cells are added from left to right.
*/
	function addCell( $data='', $attribs='', $prefix='', $suffix='' ) {
		$this->cells1[] = array( $attribs, $data, $prefix, $suffix );
	}
/**
* Adds a table 'cell' to left-aligned bread-crumbs
*
* Cells are added from left to right.
*/
	function addCrumb( $link, $label, $icon='' ) {
		$this->crumbs[$link] = array( $label, $icon );
	}
/**
* Adds a table 'cell' to the right-aligned bread-crumbs
*
* Cells are added from left to right.
*/
	function addCrumbRight( $data='', $attribs='', $prefix='', $suffix='' ) {
		$this->cells2[] = array( $attribs, $data, $prefix, $suffix );
	}
/**
* Creates a standarised, right-aligned delete bread-crumb and icon.
*/
	function addCrumbDelete( $title, $canDelete='', $msg='' ) {
		global $AppUI;
		$this->addCrumbRight(
			'<table cellspacing="0" cellpadding="0" border="0"?<tr><td>'
			. '<a href="javascript:delIt()" title="'.($canDelete?'':$msg).'">'
			. dPshowImage( './images/icons/'.($canDelete?'stock_delete-16.png':'stock_trash_full-16.png'), '16', '16',  '' )
			. '</a>'
			. '</td><td>&nbsp;'
			. '<a href="javascript:delIt()" title="'.($canDelete?'':$msg).'">' . $AppUI->_( $title ) . '</a>'
			. '</td></tr></table>'
		);
	}
/**
* The drawing function
*/
	function show() {
		global $AppUI;
		$CR = "\n";
		$CT = "\n\t";
		$s = $CR . '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
		$s .= $CR . '<tr>';
		if ($this->icon) {
			$s .= $CR . '<td width="42">';
			$s .= dPshowImage( dPFindImage( $this->icon, $this->module ));
			$s .= '</td>';
		}
		$s .= $CR . '<td align="left" width="100%" nowrap="nowrap"><h1>' . $AppUI->_($this->title) . '</h1></td>';
		foreach ($this->cells1 as $c) {
			$s .= $c[2] ? $CR . $c[2] : '';
			$s .= $CR . '<td align="right" nowrap="nowrap"' . ($c[0] ? " $c[0]" : '') . '>';
			$s .= $c[1] ? $CT . $c[1] : '&nbsp;';
			$s .= $CR . '</td>';
			$s .= $c[3] ? $CR . $c[3] : '';
		}
		if ($this->showhelp) {
			$s .= '<td nowrap="nowrap" width="20" align="right">';
			//$s .= $CT . contextHelp( '<img src="./images/obj/help.gif" width="14" height="16" border="0" alt="'.$AppUI->_( 'Help' ).'" />', $this->helpref );

			$s .= "\n\t<a href=\"#$this->helpref\" onClick=\"javascript:window.open('?m=help&dialog=1&hid=$this->helpref', 'contexthelp', 'width=400, height=400, left=50, top=50, scrollbars=yes, resizable=yes')\" title=\"".$AppUI->_( 'Help' )."\">";
			$s .= "\n\t\t" . dPshowImage( './images/icons/stock_help-16.png', '16', '16', $AppUI->_( 'Help' ) );
			$s .= "\n\t</a>";
			$s .= "\n</td>";
		}
		$s .= "\n</tr>";
		$s .= "\n</table>";

		if (count( $this->crumbs ) || count( $this->cells2 )) {
			$crumbs = array();
			foreach ($this->crumbs as $k => $v) {
				$t = $v[1] ? '<img src="' . dPfindImage( $v[1], $this->module ) . '" border="" alt="" />&nbsp;' : '';
				$t .= $AppUI->_( $v[0] );
				$crumbs[] = "<a href=\"$k\">$t</a>";
			}
			$s .= "\n<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" width=\"100%\">";
			$s .= "\n<tr>";
			$s .= "\n\t<td nowrap=\"nowrap\">";
			$s .= "\n\t\t" . implode( ' <strong>:</strong> ', $crumbs );
			$s .= "\n\t</td>";

			foreach ($this->cells2 as $c) {
				$s .= $c[2] ? "\n$c[2]" : '';
				$s .= "\n\t<td align=\"right\" nowrap=\"nowrap\"" . ($c[0] ? " $c[0]" : '') . '>';
				$s .= $c[1] ? "\n\t$c[1]" : '&nbsp;';
				$s .= "\n\t</td>";
				$s .= $c[3] ? "\n\t$c[3]" : '';
			}

			$s .= "\n</tr>\n</table>";
		}
		echo "$s";
	}
}
// !! Ensure there is no white space after this close php tag.
?>

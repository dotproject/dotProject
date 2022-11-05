<?php /* CLASSES $Id$ */

/**
 *	@package dotproject
 *	@subpackage modules
 *	@version $Revision$
 */

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (!defined('UNIT_TEST')) {
	require_once $AppUI->getSystemClass('query');
	require_once $AppUI->getModuleClass('system');
}

/**
 *	CDpObject Abstract Class.
 *
 *	Parent class to all database table derived objects
 *	@author Andrew Eddie <eddieajau@users.sourceforge.net>
 *	@abstract
 */
class CDpObject {
	/**
	 *	@var string Name of the table in the db schema relating to child class
	 */
	protected $_tbl = '';
	/**
	 *	@var string Name of the primary key field in the table
	 */
	protected $_tbl_key = '';
	/**
	 *	@var string Permission module name relating to child class
	 */
	protected $_permission_name = '';
	/**
	 *	@var string Error message
	 */
	protected $_error = '';
	/**
	 *	@var string generic message
	 */
	public $_message;
	/**
	 * @var object Query Handler
	 */
	protected $_query;

	
	/**
	 *	Object constructor to set table and key field
	 *
	 *	Can be overloaded/supplemented by the child class
	 *	@param string $table name of the table in the db schema relating to child class
	 *	@param string $key name of the primary key field in the table
	 *	@param string $perm_name permission module name relating to child class (default $table)
	 */
	public function __construct($table, $key, $perm_name='', $mod_dir='') {
		$this->_tbl = $table;
		$this->_tbl_key = $key;
		$this->_permission_name = (($perm_name) ? $perm_name : $table);
		$this->_module_directory = $mod_dir;
		$this->_query = new DBQuery;
	}

    /**
     * Let people know that this is not a good idea.
     * @param $table
     * @param $key
     * @param string $perm_name
     */
	public function CDpObject($table, $key, $perm_name='') {
		$this->_tbl = $table;
		$this->_tbl_key = $key;
		$this->_permission_name = (($perm_name) ? $perm_name : $table);
		$this->_query = new DBQuery;
		trigger_error('Calling CDpObject directly is no longer supported. Please update your module to support PHP5 style inheritance', E_USER_NOTICE);
	}

	/**
	 *	@return string Returns the error message
	 */
	public function getError() {
		return $this->_error;
	}
	/**
	 *	Clears any existing data in the current object
	 */
	public function clear() {
		foreach (array_keys(array_diff_key(get_object_vars($this), get_class_vars(get_class($this)))) as $k) {
			unset($this->$k);
		}
	}

	/**
	 *	Binds a named array/hash to this object
	 *
	 *	can be overloaded/supplemented by the child class
	 *	@param array $hash named array
	 *	@return null|string	null is operation was satisfactory, otherwise returns an error
	 */
	public function bind($hash) {
		if (!is_array($hash)) {
			$this->_error = (get_class($this) . '::bind failed.');
			return false;
		} 
		else {
			/*
			 * We need to filter out any object values from the array/hash so the bindHashToObject()
			 * doesn't die. We also avoid issues such as passing objects to non-object functions 
			 * and copying object references instead of cloning objects. Object cloning (if needed) 
			 * should be handled seperatly anyway.
			 */
			foreach ($hash as $k => $v) {
				if (!(is_object($hash[$k]))) {
					$filtered_hash[$k] = $v;
				}
			}
			
			bindHashToObject($filtered_hash, $this);
			return true;
		}
	}
	
	/**
	 *	Binds an array/hash to this object
	 *	@param int $oid optional argument, if not specifed then the value of current key is used
	 *	@return any result from the database operation
	 */
	public function load($oid=null , $strip = true) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval($oid);
		}
		$oid = $this->$k;
		if ($oid === null) {
			return false;
		}
		$this->_query->clear();
		$this->_query->addTable($this->_tbl);
		$this->_query->addWhere($this->_tbl_key . ' = ' . $oid);
		$sql = $this->_query->prepare();
		$this->_query->clear();
		$this->clear();
		return db_loadObject($sql, $this, false, $strip);
	}
	
	/**
	 *	Returns an array, keyed by the key field, of all elements that meet
	 *	the where clause provided. Ordered by $order key.
	 */
	public function loadAll($order = null, $where = null) {
		$this->_query->clear();
		$this->_query->addTable($this->_tbl);
		if ($order) {
			$this->_query->addOrder($order);
		}
		if ($where) {
			$this->_query->addWhere($where);
		}
		$sql = $this->_query->prepare();
		$this->_query->clear();
		return db_loadHashList($sql, $this->_tbl_key);
	}
	
	/**
	 *	Return a DBQuery object seeded with the table name.
	 *	@param string $alias optional alias for table queries.
	 *	@return DBQuery object
	 */
	public function &getQuery($alias = null) {
		$this->_query->clear();
		$this->_query->addTable($this->_tbl, $alias);
		return $this->_query;
	}
	
	/**
	 *	Generic check method
	 *
	 *	Can be overloaded/supplemented by the child class
	 *	@return null if the object is ok
	 */
	public function check() {
		return NULL;
	}
	
	/**
	 *	Clone the current record
	 *
	 *	@author	handco <handco@users.sourceforge.net>
	 *	@return	object	The new record object or null if error
	 **/
	public function duplicate() {
		$_key = $this->_tbl_key;
		
		// In php4 assignment does a shallow copy
		// in php5 clone is required
		if (version_compare(phpversion(), '5') >= 0) {
			$newObj = clone ($this);
		} else {
			$newObj = $this;
		}
		// blanking the primary key to ensure that's a new record
		$newObj->$_key = '';
		
		return $newObj;
	}
	
	/**
	 *	Default trimming method for class variables of type string
	 *
	 *	@param object Object to trim class variables for
	 *	Can be overloaded/supplemented by the child class
	 *	@return none
	 */
	public function dPTrimAll() {
		$trim_arr = get_object_vars($this);
		foreach ($trim_arr as $trim_key => $trim_val) {
			if (!(strcasecmp(gettype($trim_val), 'string'))) {
				$this->{$trim_key} = trim($trim_val);
			}
		}
	}
	
	/**
	 *	Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 *	Can be overloaded/supplemented by the child class
	 *	@return null|string null if successful otherwise returns and error message
	 */
	public function store($updateNulls = false) {
		
		$this->dPTrimAll();
		
		$msg = $this->check();
		if ($msg) {
			return (get_class($this) . '::store-check failed<br />' . $msg);
		}
		$k = $this->_tbl_key;
		if ($this->$k) {
			$store_type = 'update';
			$ret = db_updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		} 
		else {
			$store_type = 'add';
			$ret = db_insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		
		if ($ret) {
			// only record history if an update or insert actually occurs.
			addHistory($this->_tbl, $this->$k, $store_type, 
			           ($this->_tbl . '_' . $store_type . '(' . $this->$k . ')'));
		}
		return ((!$ret) ? (get_class($this) . '::store failed <br />' . db_error()) : NULL) ;
	}
	
	/**
	 *	Generic check for whether dependencies exist for this object in the db schema
	 *
	 *	Can be overloaded/supplemented by the child class
	 *	@param string $msg Error message returned
	 *	@param int Optional key index
	 *	@param array Optional array to compiles standard joins: 
	 *    format [label => 'Label', name => 'table name', idfield => 'field', joinfield => 'field']
	 *	@return true|false
	 */
	public function canDelete(&$msg, $oid=null, $joins=null) {
		global $AppUI;
		
		// First things first.  Are we allowed to delete?
		$acl =& $AppUI->acl();
		if (!($acl->checkModuleItem($this->_permission_name, 'delete', $oid))) {
			$msg = $AppUI->_('noDeletePermission');
			return false;
		}
		
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval($oid);
		}
		if (is_array($joins)) {
			$q = new DBQuery;
			$q->addTable($this->_tbl, 'k');
			$q->addQuery($k);
			$i = 0;
			foreach ($joins as $table) {
				$table_alias = 't' . $i++;
				$q->addJoin($table['name'], $table_alias, 
				            ($table_alias . '.' . $table['joinfield'] . ' = ' . 'k' . '.' . $k));
				$q->addQuery('COUNT(DISTINCT ' . $table_alias . '.' . $table['idfield'] . ') AS ' 
							 . $table['idfield'] . $table_alias);
			}
			$q->addWhere($k . " = '" . $this->$k . "'");
			$q->addGroup($k);
			$sql = $q->prepare(true);
			
			$obj = null;
			if (!db_loadObject($sql, $obj)) {
				$msg = db_error();
				return false;
			}
			$msg = array();
			$i = 0;
			foreach ($joins as $table) {
				$table_alias = 't' . $i++;
				$k = $table['idfield'] . $table_alias;
				if ($obj->$k) {
					$msg[] = $table_alias . '.' . $AppUI->_($table['label']);
				}
			}
			
			if (count($msg)) {
				$msg = ($AppUI->_('noDeleteRecord') . ': ' . implode(', ', $msg));
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 *	Default delete method
	 *
	 *	Can be overloaded/supplemented by the child class
	 *	@return null|string null if successful otherwise returns and error message
	 */
	public function delete($oid=null, $history_desc = '',  $history_proj = 0) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval($oid);
		}
		if (!$this->canDelete($msg)) {
			return $msg;
		}
		
		$q = new DBQuery;
		$q->setDelete($this->_tbl);
		$q->addWhere($this->_tbl_key . " = '" . $this->$k . "'");
		$result = ((!$q->exec())?db_error():null);
		if (!$result) {
			// only record history if deletion actually occurred
			addHistory($this->_tbl, $this->$k, 'delete', $history_desc, $history_proj);
		}
		$q->clear();
		return $result;
	}
	
	/**
	 *	Get specifically denied records from a table/module based on a user
	 *	@param int User id number
	 *	@return array
	 */
	public function getDeniedRecords($uid) {
		global $AppUI;
		$perms =& $AppUI->acl();
		
		$uid = intval($uid);
		$uid || exit ('FATAL ERROR<br />' . get_class($this) 
		              . '::getDeniedRecords failed, user id = 0');
		
		return $perms->getDeniedItems($this->_tbl, $uid);
	}
	
	/**
	 *	Returns a list of records exposed to the user
	 *	@param int User id number
	 *	@param string Optional fields to be returned by the query, default is all
	 *	@param string Optional sort order for the query
	 *	@param string Optional name of field to index the returned array
	 *	@param array Optional array of additional sql parameters (from and where supported)
	 *	@return array
	 */
	// returns a list of records exposed to the user
	public function getAllowedRecords($uid, $fields='*', $orderby='', $index=null, $extra=null) {
		global $AppUI;
		$perms = $AppUI->acl();
		
		$uid = intval($uid);
		$uid || exit ('FATAL ERROR<br />' . get_class($this) . '::getAllowedRecords failed');
		$deny = $perms->getDeniedItems($this->_tbl, $uid);
		$allow = $perms->getAllowedItems($this->_tbl, $uid);
		if (!($perms->checkModule($this->_tbl, 'view', $uid))) {
			if (!(count($allow))) {
				return array();	// No access, and no allow overrides, so nothing to show.
			}
		} 
		else {
			$allow = array();	// Full access, allow overrides don't mean anything.
		}
		$this->_query->clear();
		$this->_query->addQuery($fields);
		$this->_query->addTable($this->_tbl);
		
		if (@$extra['from']) {
			$this->_query->addTable($extra['from']);
		}
		
		if (count($allow)) {
			$this->_query->addWhere($this->_tbl_key . ' IN (' . implode(',', $allow) . ')');
		}
		if (count($deny)) {
			$this->_query->addWhere($this->_tbl_key . ' NOT IN (' . implode(',', $deny) . ')');
		}
		if (isset($extra['where'])) {
			$this->_query->addWhere($extra['where']);
		}
		
		if ($orderby) {
			$this->_query->addOrder($orderby);
		}
		
		return $this->_query->loadHashList($index);
	}
	
	public function getAllowedSQL($uid, $index = null, $alt_mod = null) {
		global $AppUI;
		$perms =& $AppUI->acl();
		$mod = ((isset($alt_mod)) ? $alt_mod : $this->_tbl);
		
		$uid = intval($uid);
		$uid || exit ('FATAL ERROR<br />' . get_class($this) . '::getAllowedSQL failed');
		$deny =& $perms->getDeniedItems($mod, $uid);
		$allow =& $perms->getAllowedItems($mod, $uid);
		if (!($perms->checkModule($mod, 'view', $uid))) {
			if (!(count($allow))) {
				return array('1=0');	// No access, and no allow overrides, so nothing to show.
			}
		} 
		else {
			$allow = array();	// Full access, allow overrides don't mean anything.
		}
		
		if (!(isset($index))) {
			$index = $this->_tbl_key;
		}
		$where = array();
		if (count($allow)) {
			$where[] = ($index . ' IN (' . implode(',', $allow) . ')');
		}
		if (count($deny)) {
			$where[] = ($index .' NOT IN (' . implode(',', $deny) . ')');
		}
		return $where;
	}
	
	public function setAllowedSQL($uid, &$query, $index = null, $key = null, $alt_mod = null) {
		global $AppUI;
		$perms =& $AppUI->acl();
		$mod = ((isset($alt_mod)) ? $alt_mod : $this->_tbl);
		
		$uid = intval($uid);
		$uid || exit ('FATAL ERROR<br />' . get_class($this) . '::getAllowedSQL failed');
		$deny =& $perms->getDeniedItems($mod, $uid);
		$allow =& $perms->getAllowedItems($mod, $uid);
		// Make sure that we add the table otherwise dependencies break
		if (isset($index)) {
			if (!($key)) {
				$key = mb_substr($this->_tbl, 0, 2);
			}
			$query->leftJoin($this->_tbl, $key, ($key . '.' . $this->_tbl_key . ' = ' . $index));
		}
		if (!($perms->checkModule($mod, 'view', $uid))) {
			if (!(count($allow))) {
				// We need to ensure that we don't just break complex SQLs, but
				// instead limit to a nonsensical value.  This assumes that the
				// key is auto-incremented.
				$query->addWhere((($key) ? ($key . '.') : '') . $this->_tbl_key . ' = 0');
				return;
			}
		} else {
			$allow = array();	// Full access, allow overrides don't mean anything.
		}
		
		if (count($allow)) {
			$query->addWhere((($key) ? ($key . '.') : '') . $this->_tbl_key 
			                 . ' IN (' . implode(',', $allow) . ')');
		}
		if (count($deny)) {
			$query->addWhere((($key) ? ($key . '.') : '') . $this->_tbl_key 
			                 . ' NOT IN (' . implode(',', $deny) . ')');
 		}
 	}

	/*
	* Decode HTML entities in object vars
	*/
	public function htmlDecode() {
		foreach (get_object_vars($this) as $k => $v) {
			if (is_array($v) or is_object($v) or $v == NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$this->$k = htmlspecialchars_decode($v);
		}
	}

	/**
	 * Utility function to return the current module name
	 * It first tries to get the name based on the table name,
	 * and infer the name from the class name.  If neither of these
	 * are appropriate, children should implement this function themselves.
	 * or set _module_directory after construction.
	 */
	public function getModuleName() {
		/* If we've already done this, or our sub-class has set this,
		   we are on a winner */
		if (!empty($this->_module_directory)) {
			return $this->_module_directory;
		}
		/* Now the guessing game begins */
		$mods = $this->getCModule();
		if (!empty($this->_permission_name)) {
			if (($mod_name = $mods->getModuleByName($this->_permission_name))) {
				$this->_module_directory = $mod_name;
				return $mod_name;
			}
		}
		$class = get_class($this);
		// Class usually includes an initial C and is camel case
		// And never is multibyte, so we are safe to use the non mb_ functions.
		$class = strtolower(substr($class, 1));
		if (($mod_name = $mods->getModuleByName($class))) {
			$this->_module_directory = $mod_name;
			return $mod_name;
		}
		return 'unknown';
	}

	/**
	 * Temp fix for the purpose of unit testing classes
	 * that originally initialized this object within the
	 * method being tested.
	 *
	 * @return object
	 */
	private function getCModule() {
		return new CModule;
	}

 }

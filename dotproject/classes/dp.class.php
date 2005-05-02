<?php /* CLASSES $Id$ */

/**
 *	@package dotproject
 *	@subpackage modules
 *	@version $Revision$
 */

require_once $AppUI->getSystemClass('query');

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
	var $_tbl = '';
/**
 *	@var string Name of the primary key field in the table
 */
	var $_tbl_key = '';
/**
 *	@var string Error message
 */
	var $_error = '';

/**
 * @var object Query Handler
 */
 var $_query;

/**
 *	Object constructor to set table and key field
 *
 *	Can be overloaded/supplemented by the child class
 *	@param string $table name of the table in the db schema relating to child class
 *	@param string $key name of the primary key field in the table
 */
	function CDpObject( $table, $key ) {
		global $dPconfig;
		$this->_tbl = $table;
		$this->_tbl_key = $key;
		if (isset($dPconfig['dbprefix']))
			$this->_prefix = $dPconfig['dbprefix'];
		else
			$this->_prefix = '';
		$this->_query =& new DBQuery;
	}
/**
 *	@return string Returns the error message
 */
	function getError() {
		return $this->_error;
	}
/**
 *	Binds a named array/hash to this object
 *
 *	can be overloaded/supplemented by the child class
 *	@param array $hash named array
 *	@return null|string	null is operation was satisfactory, otherwise returns an error
 */
	function bind( $hash ) {
		if (!is_array( $hash )) {
			$this->_error = get_class( $this )."::bind failed.";
			return false;
		} else {
			bindHashToObject( $hash, $this );
			return true;
		}
	}

/**
 *	Binds an array/hash to this object
 *	@param int $oid optional argument, if not specifed then the value of current key is used
 *	@return any result from the database operation
 */
	function load( $oid=null , $strip = true) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		$oid = $this->$k;
		if ($oid === null) {
			return false;
		}
		$this->_query->clear();
		$this->_query->addTable($this->_tbl);
		$this->_query->addWhere("$this->_tbl_key = $oid");
		$sql = $this->_query->prepare();
		$this->_query->clear();
		return db_loadObject( $sql, $this, false, $strip );
	}

/**
 *	Returns an array, keyed by the key field, of all elements that meet
 *	the where clause provided. Ordered by $order key.
 */
	function loadAll($order = null, $where = null) {
		$this->_query->clear();
		$this->_query->addTable($this->_tbl);
		if ($order)
		  $this->_query->addOrder($order);
		if ($where)
		  $this->_query->addWhere($where);
		$sql = $this->_query->prepare();
		$this->_query->clear();
		return db_loadHashList($sql, $this->_tbl_key);
	}

/**
 *	Return a DBQuery object seeded with the table name.
 *	@param string $alias optional alias for table queries.
 *	@return DBQuery object
 */
	function &getQuery($alias = null) {
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
	function check() {
		return NULL;
	}
	
/**
*	Clone the current record
*
*	@author	handco <handco@users.sourceforge.net>
*	@return	object	The new record object or null if error
**/
	function duplicate() {
		$_key = $this->_tbl_key;
		
		$newObj = $this;
		// blanking the primary key to ensure that's a new record
		$newObj->$_key = '';
		
		return $newObj;
	}


/**
 *	Inserts a new row if id is zero or updates an existing row in the database table
 *
 *	Can be overloaded/supplemented by the child class
 *	@return null|string null if successful otherwise returns and error message
 */
	function store( $updateNulls = false ) {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed<br />$msg";
		}
		$k = $this->_tbl_key;
		if( $this->$k ) {
                        addHistory($this->_tbl . '_update(' . $this->$k . ')', 0, $this->_tbl);
			$ret = db_updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		} else {
			$ret = db_insertObject( $this->_tbl, $this, $this->_tbl_key );
                        addHistory($this->_tbl . '_add(' . $this->$k . ')', 0, $this->_tbl);
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

/**
 *	Generic check for whether dependencies exist for this object in the db schema
 *
 *	Can be overloaded/supplemented by the child class
 *	@param string $msg Error message returned
 *	@param int Optional key index
 *	@param array Optional array to compiles standard joins: format [label=>'Label',name=>'table name',idfield=>'field',joinfield=>'field']
 *	@return true|false
 */
	function canDelete( &$msg, $oid=null, $joins=null ) {
		global $AppUI;

		// First things first.  Are we allowed to delete?
		$acl =& $AppUI->acl();
		if ( ! $acl->checkModuleItem($this->_tbl, "delete", $oid)) {
		  $msg = $AppUI->_( "noDeletePermission" );
		  return false;
		}

		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		if (is_array( $joins )) {
			$select = "$k";
			$join = "";
			
			$q  = new DBQuery;
			$q->addTable($this->_tbl);
			$q->addWhere("$k = '".$this->$k."'");
			$q->addGroup($k);
			foreach( $joins as $table ) {
				$q->addQuery("COUNT(DISTINCT {$table['idfield']}) AS {$table['idfield']}");
				$q->addJoin($table['name'], $table['name'], "{$table['joinfield']} = $k");
			}
			$sql = $q->prepare();
			$q->clear();

			$obj = null;
			if (!db_loadObject( $sql, $obj )) {
				$msg = db_error();
				return false;
			}
			$msg = array();
			foreach( $joins as $table ) {
				$k = $table['idfield'];
				if ($obj->$k) {
					$msg[] = $AppUI->_( $table['label'] );
				}
			}

			if (count( $msg )) {
				$msg = $AppUI->_( "noDeleteRecord" ) . ": " . implode( ', ', $msg );
				return false;
			} else {
				return true;
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
	function delete( $oid=null ) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		if (!$this->canDelete( $msg )) {
			return $msg;
		}
                
                addHistory($this->_tbl, $this->$k, 'delete');
		$q  = new DBQuery;
		$q->setDelete($this->_tbl);
		$q->addWhere("$this->_tbl_key = '".$this->$k."'");
		$result = null;
		if (!$q->exec()) {
			$result = db_error();
		}
		$q->clear();
		return $result;
	}

/**
 *	Get specifically denied records from a table/module based on a user
 *	@param int User id number
 *	@return array
 */
	function getDeniedRecords( $uid ) {
		$uid = intval( $uid );
		$uid || exit ("FATAL ERROR<br />" . get_class( $this ) . "::getDeniedRecords failed, user id = 0" );

		$perms =& $GLOBALS['AppUI']->acl();
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
	function getAllowedRecords( $uid, $fields='*', $orderby='', $index=null, $extra=null ) {
		$perms =& $GLOBALS['AppUI']->acl();
		$uid = intval( $uid );
		$uid || exit ("FATAL ERROR<br />" . get_class( $this ) . "::getAllowedRecords failed" );
		$deny =& $perms->getDeniedItems( $this->_tbl, $uid );
		$allow =& $perms->getAllowedItems($this->_tbl, $uid);
		if (! $perms->checkModule($this->_tbl, "view" )) {
		  if (! count($allow))
		    return array();	// No access, and no allow overrides, so nothing to show.
		} else {
		  $allow = array();	// Full access, allow overrides don't mean anything.
		}
		$this->_query->clear();
		$this->_query->addQuery($fields);
		$this->_query->addTable($this->_tbl);

		if (@$extra['from']) {
			$this->_query->addTable($extra['from']);
		}
		
		if (count($allow)) {
		  $this->_query->addWhere("$this->_tbl_key IN (" . implode(',', $allow) . ")");
		}
		if (count($deny)) {
		  $this->_query->addWhere("$this->_tbl_key NOT IN (" . implode(",", $deny) . ")");
		}
		if (isset($extra['where'])) {
		  $this->_query->addWhere($extra['where']);
		}

		if ($orderby)
		  $this->_query->addOrder($orderby);

		return $this->_query->loadHashList( $index );
	}

	function getAllowedSQL( $uid, $index = null ) {
		$perms =& $GLOBALS['AppUI']->acl();
		$uid = intval( $uid );
		$uid || exit ("FATAL ERROR<br />" . get_class( $this ) . "::getAllowedSQL failed" );
		$deny =& $perms->getDeniedItems( $this->_tbl, $uid );
		$allow =& $perms->getAllowedItems($this->_tbl, $uid);
		if (! $perms->checkModule($this->_tbl, "view" )) {
		  if (! count($allow))
		    return array("1=0");	// No access, and no allow overrides, so nothing to show.
		} else {
		  $allow = array();	// Full access, allow overrides don't mean anything.
		}

		if (! isset($index))
		   $index = $this->_tbl_key;
		$where = array();
		if (count($allow)) {
		  $where[] = "$index IN (" . implode(',', $allow) . ")";
		}
		if (count($deny)) {
		  $where[] = "$index NOT IN (" . implode(",", $deny) . ")";
		}
		return $where;
	}

	function setAllowedSQL($uid, &$query, $index = null, $key = null) {
		$perms =& $GLOBALS['AppUI']->acl();
		$uid = intval( $uid );
		$uid || exit ("FATAL ERROR<br />" . get_class( $this ) . "::getAllowedSQL failed" );
		$deny =& $perms->getDeniedItems($this->_tbl, $uid );
		$allow =& $perms->getAllowedItems($this->_tbl, $uid);
		// Make sure that we add the table otherwise dependencies break
		if (isset($index)) {
			if (! $key)
				$key = substr($this->_tbl, 0, 2);
			$query->leftJoin($this->_tbl, $key, "$key.$this->_tbl_key = $index");
		}
		if (! $perms->checkModule($this->_tbl, "view" )) {
		  if (! count($allow)) {
				// We need to ensure that we don't just break complex SQLs, but
				// instead limit to a nonsensical value.  This assumes that the
				// key is auto-incremented.
		    $query->addWhere("$this->_tbl_key = 0");
		    return;
			}
		} else {
		  $allow = array();	// Full access, allow overrides don't mean anything.
		}

		if (count($allow)) {
		  $query->addWhere("$this->_tbl_key IN (" . implode(',', $allow) . ")");
		}
		if (count($deny)) {
		  $query->addWhere("$this->_tbl_key NOT IN (" . implode(",", $deny) . ")");
		}
	}
}
?>

<?php /* INCLUDES $Id$ */
/**
* Generic functions based on library function (that is, non-db specific)
*
* @todo Encapsulate into a database object
*/

if (!(defined('DP_BASE_DIR'))) {
	die('You should not access this file directly.');
}

// load the db specific handlers
//require_once(DP_BASE_DIR."/includes/db_" . $dPconfig['dbtype'] . ".php");
//require_once("./includes/db_adodb.php");
require_once DP_BASE_DIR . '/includes/db_adodb.php';

// make the connection to the db
db_connect(dPgetConfig('dbhost'), dPgetConfig('dbname'),
dPgetConfig('dbuser'), dPgetConfig('dbpass'), dPgetConfig('dbpersist'));


// Quick hack to ensure MySQL behaves itself (#2323)
$db->Execute("SET sql_mode := ''");

/*
* Having successfully established the database connection now,
* we will hurry up to load the system configuration details from the database.
*/

$sql = 'SELECT config_name, config_value, config_type FROM '.dPgetConfig('dbprefix','').'config';
$rs = $db->Execute($sql);

if ($rs) { // Won't work in install mode.
	$rsArr = $rs->GetArray();

	foreach ($rsArr as $c) {
		if ($c['config_type'] == 'checkbox') {
			$c['config_value'] = ($c['config_value'] == 'true') ? true : false;
		}
		$dPconfig[$c['config_name']] = $c['config_value'];
	}
}


/**
* This global function loads the first field of the first row returned by the query.
*
* @param string The SQL query
* @return The value returned in the query or null if the query failed.
*/
function db_loadResult($sql) {
	$cur = db_exec($sql);
	if (!($cur)) {
		exit(db_error());
	}
	$ret = null;
	if ($row = db_fetch_row($cur)) {
		$ret = $row[0];
	}
	db_free_result($cur);
	return $ret;
}

/**
* This global function loads the first row of a query into an object
*
* If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
* If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
* @param string The SQL query
* @param object The address of variable
* @return bool  Object loaded correctly? true
*/
function db_loadObject($sql, &$object, $bindAll=false , $strip = true) {
	if (!empty($object)) {
		$hash = array();
		if (empty(db_loadHash($sql, $hash))) {
			return false;
		}
		bindHashToObject($hash, $object, null, $strip, $bindAll);
		return true;
	} else {
		$cur = db_exec($sql);
		if (empty($cur)) {
			exit(db_error());  // a bit drastic, isn't it? (gwyneth 20210427)
		}
		$object = db_fetch_object($cur);
		if (!empty($object)) {
			db_free_result($cur);
		} else {
			$object = null;
		}
		return !empty($object);
	}
}

/**
* This global function return a result row as an associative array
*
* @param string The SQL query
* @param array An array for the result to be return in
* @return <b>True</b> is the query was successful, <b>False</b> otherwise
*/
function db_loadHash($sql, &$hash) {
	$cur = db_exec($sql);
	if (!($cur)) {
		exit(db_error());
	}
	$hash = db_fetch_assoc($cur);
	db_free_result($cur);
	return ((!($hash)) ? false : true);
}

/**
* Document::db_loadHashList()
*
* { Description }
*
* @param string $index
*/
function db_loadHashList($sql, $index='') {
	$cur = db_exec($sql);
	if (!($cur)) {
		exit(db_error());
	}
	$hashlist = array();
	while ($hash = db_fetch_array($cur)) {
		$hashlist[$hash[(($index) ? $index : 0)]] = $index ? $hash : $hash[1];
	}
	db_free_result($cur);
	return $hashlist;
}

/**
* Document::db_loadList()
*
* { Description }
*
* @param [type] $maxrows
*/
function db_loadList($sql, $maxrows=NULL) {
	GLOBAL $AppUI;
	if (!($cur = db_exec($sql))) {;
		$AppUI->setMsg(db_error(), UI_MSG_ERROR);
		return false;
	}
	$list = array();
	$cnt = 0;
	while ($hash = db_fetch_assoc($cur)) {
		$list[] = $hash;
		if ($maxrows && $maxrows == $cnt++) {
			break;
		}
	}
	db_free_result($cur);
	return $list;
}

/**
* Document::db_loadColumn()
*
* { Description }
*
* @param [type] $maxrows
*/
function db_loadColumn($sql, $maxrows=NULL) {
	GLOBAL $AppUI;
	if (!($cur = db_exec($sql))) {;
		$AppUI->setMsg(db_error(), UI_MSG_ERROR);
		return false;
	}
	$list = array();
	$cnt = 0;
	$row_index = null;
	while ($row = db_fetch_row($cur)) {
		if (!(isset($row_index))) {
			if (isset($row[0])) {
				$row_index = 0;
			} else {
				$row_indices = array_keys($row);
				$row_index = $row_indices[0];
			}
		}
		$list[] = $row[$row_index];
		if ($maxrows && $maxrows == $cnt++) {
			break;
		}
	}
	db_free_result($cur);
	return $list;
}

/* return an array of objects from a SQL SELECT query
 * class must implement the Load() factory, see examples in Webo classes
 * @note to optimize request, only select object oids in $sql
 */
function db_loadObjectList($sql, $object, $maxrows = NULL) {
	$cur = db_exec($sql);
	if (!($cur)) {
		die('db_loadObjectList : ' . db_error());
	}
	$list = array();
	$cnt = 0;
	$row_index = null;
	while ($row = db_fetch_array($cur)) {
		if (!(isset($row_index))) {
			if (isset($row[0]))
				$row_index = 0;
			else {
				$row_indices = array_keys($row);
				$row_index = $row_indices[0];
			}
		}
		$object->load($row[$row_index]);
		$list[] = $object;
		if ($maxrows && $maxrows == $cnt++) {
			break;
		}
	}
	db_free_result($cur);
	return $list;
}


/**
* Document::db_insertArray()
*
* { Description }
*
* @param [type] $verbose
*/
function db_insertArray($table, &$hash, $verbose=false) {
	//TODO: if DBQuery class available, use it
	$dbprefix = dPgetConfig('dbprefix','');
	if ( ($dbprefix != '') && (strstr($table,$dbprefix) === false) ) {
		//have prefix and table does not have it prepended
		$fmtsql = "INSERT INTO `$dbprefix$table` (%s) VALUES (%s) ";
	} else {
		// table has prefix already prepended (or no prefix)
		$fmtsql = "INSERT INTO `$table` (%s) VALUES (%s) ";
	}
	foreach ($hash as $k => $v) {
		if (is_array($v) || is_object($v) || $v == NULL) {
			continue;
		}
		$fields[] = $k;
		$values[] = "'" . db_escape($v) . "'";
	}
	$sql = sprintf($fmtsql, implode(',', $fields) ,  implode(',', $values));

	if ($verbose) {
		print "$sql<br />\n";
	}

	if (!(db_exec($sql))) {
		return false;
	}
	$id = db_insert_id();
	return true;
}

/**
* Document::db_updateArray()
*
* { Description }
*
* @param [type] $verbose
*/
function db_updateArray($table, &$hash, $keyName, $verbose=false) {
	//TODO: If DBQuery available, use it
	$dbprefix = dPgetConfig('dbprefix','');
	if ( ($dbprefix != '') && (strstr($table,$dbprefix) === false) ) {
		$fmtsql = "UPDATE `$dbprefix$table` SET %s WHERE %s ";
	} else {
		$fmtsql = "UPDATE `$table` SET %s WHERE %s ";
	}
	foreach ($hash as $k => $v) {
		if (is_array($v) || is_object($v) || $k[0] == '_') { // internal or NA field
			continue;
		}

		if ($k == $keyName) { // PK not to be updated
			$where = "$keyName='" . db_escape($v) . "'";
			continue;
		}
		$val = (($v == '') ? 'NULL' : ("'" . db_escape($v) . "'"));
		$tmp[] = "$k=$val";
	}
	$sql = sprintf($fmtsql, implode(',', $tmp) , $where);
	if ($verbose) {
		print "$sql<br />\n";
	}
	$ret = db_exec($sql);
	return $ret;
}

/**
* Document::db_delete()
*
* { Description }
*
*/
function db_delete($table, $keyName, $keyValue) {
	//TODO: If DBQuery available use it
	$dbprefix = dPgetConfig('dbprefix','');
	if ( ($dbprefix != '') && (strstr($table,$dbprefix) === false) ) {
		$sql = "DELETE FROM $dbprefix$table WHERE $keyName='$keyValue'";
	} else {
		$sql = "DELETE FROM $table WHERE $keyName='$keyValue'";
	}
	$keyName = db_escape($keyName);
	$keyValue = db_escape($keyValue);
	$ret = db_exec( $sql );
	return $ret;
}


/**
* Document::db_insertObject()
*
* { Description }
*
* @param [type] $keyName
* @param [type] $verbose
*/
function db_insertObject($table, &$object, $keyName = NULL, $verbose=false) {
	//TODO: If DBQuery available, use it
	$dbprefix = dPgetConfig('dbprefix','');
	if ( ($dbprefix != '') && (strstr($table,$dbprefix) === false) ) {
		$fmtsql = "INSERT INTO `$dbprefix$table` (%s) VALUES (%s) ";
	} else {
		$fmtsql = "INSERT INTO `$table` (%s) VALUES (%s) ";
	}
	foreach (get_object_vars($object) as $k => $v) {
		if (is_array($v) || is_object($v) || $v == NULL) {
			continue;
		}
		if ($k[0] == '_') { // internal field
			continue;
		}
		$fields[] = $k;
		$values[] = "'" . db_escape($v) . "'";
	}
	$sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));
	if ($verbose) {
		print "$sql<br />\n";
	}
	if (!(db_exec($sql))) {
		return false;
	}
	$id = db_insert_id();
	if ($verbose) {
		print "id=[$id]<br />\n";
	}
	if ($keyName && $id) {
		$object->$keyName = $id;
	}
	return true;
}

/**
* Document::db_updateObject()
*
* { Description }
*
* @param [type] $updateNulls
*/
function db_updateObject($table, &$object, $keyName, $updateNulls=true, $descriptionField = NULL) {
	global $AppUI;
	$perms =& $AppUI->acl();

	//TODO: If DBQuery exists use it
	$dbprefix = dPgetConfig('dbprefix','');
	if ( ($dbprefix != '') && (strstr($table,$dbprefix) === false) ) {
		$fmtsql = "UPDATE `$dbprefix$table` SET %s WHERE %s";
	} else {
		$fmtsql = "UPDATE `$table` SET %s WHERE %s";
	}
	$obj_vars_arr = get_object_vars($object);
	foreach ($obj_vars_arr as $k => $v) {
		if (is_array($v) || is_object($v) || $k[0] == '_') { // internal or NA field
			continue;
		}
		if ($k == $keyName) { // PK not to be updated
			$where = "$keyName='" . db_escape($v) . "'";
			continue;
		}
		if ($v === NULL && !($updateNulls)) {
			continue;
		}
		$val = (($v === '') ? "''" : ("'" . db_escape($v) . "'"));
		$tmp[] = "$k=$val";
	}
	if (count ($tmp)) {
		$sql = sprintf($fmtsql, implode(",", $tmp) , $where);
		$retval = db_exec($sql);
		if ($retval) {
			$perm_item_id = $perms->get_object_id($table, $obj_vars_arr[$keyName], 'axo');
			if ($perm_item_id) {
				if ($descriptionField) {
					$keyDesc = $descriptionField;
				} else {
					//try to get a valid label field from module table by default
					//If the passed $table has our dbprefix, we have to strip it
					//TODO: If DBQuery available, use it
					if ($dbprefix != '') {
						if (!(strstr($table,$dbprefix) === false)) substr_replace($table,'',0,strlen($dbprefix));
						$keyDesc = db_loadResult('SELECT permissions_item_label FROM '.$dbprefix.'modules'
						                         . " WHERE permissions_item_table = '" . $table . "'");
					} else {
					$keyDesc = db_loadResult('SELECT permissions_item_label FROM modules'
					                         . " WHERE permissions_item_table = '" . $table . "'");
					}
				}

				if ($keyDesc) {
					$perms->edit_object($perm_item_id, $table, $obj_vars_arr[$keyDesc],
					                    $obj_vars_arr[$keyName], 0, 0, 'axo');
				}
			}
		}
	} else {
		$retval = true;
	}

	return $retval;
}

/**
* Document::db_dateConvert()
*
* { Description }
*
*/
function db_dateConvert($src, &$dest, $srcFmt) {
	$result = strtotime($src);
	$dest = $result;
	return ($result != 0);
}

/**
* Document::db_datetime()
*
* { Description }
*
* @param [type] $timestamp
*/
function db_datetime($timestamp = NULL) {
	if (!($timestamp)) {
		return NULL;
	}
	$datetime_str = ((is_object($timestamp)) ? $timestamp->toString('%Y-%m-%d %H:%M:%S')
	                 : strftime('%Y-%m-%d %H:%M:%S', $timestamp));
	return $datetime_str;
}

/**
* Document::db_dateTime2locale()
*
* { Description }
*
*/
function db_dateTime2locale($dateTime, $format) {
	$dateTime = null;
	if (intval($dateTime)) {
		$date = new CDate($dateTime);
		$dateTime = $date->format($format);
	}
	return $dateTime;
}

/*
* copy the hash array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* only non-object hash values accepted or function dies
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
* @param boolean
*/
function bindHashToObject($hash, &$obj, $prefix=NULL, $checkSlashes=true, $bindAll=false) {
	if (!(is_array($hash))) {
		die('bindHashToObject : hash expected');
	} else if (!(is_object($obj))) {
		die('bindHashToObject : object expected');
	}
	/*
	 * checking that all hash values are non-objects so that stripslashes() and other such
	 * functions are correctly used as well as making sure that we actually create new values and
	 * not just copy a reference to an object. bind() already filters non-objects but we still need
	 * to check on this should the funtion be called independently of bind()
	 */

	foreach ($hash as $k => $v) {
		if (is_object($hash[$k])) {
			$error_str .= ('bindHashToObject : non-object expected for hash value with key '
			               . $k . "\n");
			die ($error_str);
		}
	}
	// get_magic_quotes_gpc() has been REMOVED in PHP 8; calling it throws a fatal error (gwyneth 20210413)
  $check_magic_quotes = function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc();

	if ($bindAll) {
		foreach ($hash as $k => $v) {
			$obj->$k = (($checkSlashes && $check_magic_quotes) ? stripslashes($hash[$k])
			            : $hash[$k]);
		}
	} else if ($prefix) {
		foreach (get_object_vars($obj) as $k => $v) {
			if (isset($hash[$prefix . $k ])) {
				$obj->$k = (($checkSlashes && $check_magic_quotes) ? stripslashes($hash[$k])
				            : $hash[$k]);
			}
		}
	} else {
		foreach (get_object_vars($obj) as $k => $v) {
			if (isset($hash[$k])) {
				$obj->$k = (($checkSlashes && $check_magic_quotes) ? stripslashes($hash[$k])
				            : $hash[$k]);
			}
		}
	}
	//echo "obj="; print_r($obj); exit;
}
?>

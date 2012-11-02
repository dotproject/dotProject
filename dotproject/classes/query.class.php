<?php

/*{{{ Copyright 2003,2004 Adam Donnison <adam@saki.com.au>

    This file is part of the collected works of Adam Donnison.

    This is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
}}}*/

if (!(defined('DP_BASE_DIR'))) {
	die('This file should not be called directly.');
}

require_once DP_BASE_DIR."/lib/adodb/adodb.inc.php";

define('QUERY_STYLE_ASSOC', ADODB_FETCH_ASSOC);
define('QUERY_STYLE_NUM' , ADODB_FETCH_NUM);
define('QUERY_STYLE_BOTH', ADODB_FETCH_BOTH);

/** {{{1 class DBQuery
 * Container for creating prefix-safe queries.  Allows build up of
 * a select statement by adding components one at a time.
 *
 * @version	$Id$
 * @package	dotProject
 * @access	public
 * @author	Adam Donnison <adam@saki.com.au>
 * @license	GPL version 2 or later.
 * @copyright	(c) 2003 Adam Donnison
 */
class DBQuery {
	var $query;
	var $table_list;
	var $where;
	var $order_by;
	var $group_by;
	var $limit;
	var $offset;
	var $join;
	var $type;
	var $update_list;
	var $value_list;
	var $create_table;
	var $create_definition;
	var $include_count;
	var $_table_prefix;
	var $_query_id = null;
	var $_old_style = null;
	
	function DBQuery($prefix = null) {
		$this->_table_prefix = ((isset($prefix)) ? $prefix : dPgetConfig('dbprefix', ''));
		$this->include_count = false;
		$this->clear();
	}
	
	
	function clear() {
		global $ADODB_FETCH_MODE;
		if (isset($this->_old_style)) {
			$ADODB_FETCH_MODE = $this->_old_style;
			$this->_old_style = null;
		}
		$this->type = 'select';
		$this->query = null;
		$this->table_list = null;
		$this->where = null;
		$this->order_by = null;
		$this->group_by = null;
		$this->limit = null;
		$this->offset = -1;
		$this->join = null;
		$this->value_list = null;
		$this->update_list = null;
		$this->create_table = null;
		$this->create_definition = null;
		if ($this->_query_id) {
			$this->_query_id->Close();
		}
		$this->_query_id = null;
	}

	function clearQuery() {
		if ($this->_query_id) {
			$this->_query_id->Close();
		}
		$this->_query_id = null;
	}
	
	/**
	 * Add a hash item to an array.
	 *
	 * @access	private
	 * @param	string	$varname	Name of variable to add/create
	 * @param	mixed	$name	Data to add
	 * @param	string 	$id	Index to use in array.
	 */
	function addMap($varname, $name, $id) {
		if (!isset($this->$varname)) {
		  $this->$varname = array();
		}
		
		if (isset($id)) {
			$this->{$varname}[$id] = $name;
		} else {
			$this->{$varname}[] = $name;
		}
	}
	
	/**
	 * Adds a table to the query.  A table is normally addressed by an
	 * alias.  If you don't supply the alias chances are your code will
	 * break.  You can add as many tables as are needed for the query.
	 * E.g. addTable('something', 'a') will result in an SQL statement
	 * of {PREFIX}table as a.
	 * Where {PREFIX} is the system defined table prefix.
	 *
	 * @param	string	$name	Name of table, without prefix.
	 * @parem	string	$id	Alias for use in query/where/group clauses.
	 */
	function addTable($name, $id = null) {
	  $this->addMap('table_list', $name, $id);
	}
	
	/**
	 * Add a clause to an array.  Checks to see variable exists first.
	 * then pushes the new data onto the end of the array.
	 */
	function addClause($clause, $value, $check_array = true) {
		dprint(__FILE__, __LINE__, 8, "Adding '$value' to $clause clause");
		if (!isset($this->$clause)) {
			$this->$clause = array();
		}
		if ($check_array && is_array($value)) {
			foreach ($value as $v) {
				array_push($this->$clause, $v);
			}
		} else {
		  array_push($this->$clause, $value);
		}
	}
	
	/**
	 * Add the actual select part of the query.  E.g. '*', or 'a.*'
	 * or 'a.field, b.field', etc.  You can call this multiple times
	 * and it will correctly format a combined query.
	 *
	 * @param	string	$query	Query string to use.
	 */
	function addQuery($query) {
		$this->addClause('query', $query);
	}
	
	function addInsert($field, $value, $set = false, $func = false) {
		if ($set) {
			$fields = ((is_array($field)) ? $field : explode(',', $field));
			$values = ((is_array($value)) ? $value : explode(',', $value));
			
			for ($i = 0, $fc=count($fields); $i < $fc; $i++) {
				$this->addMap('value_list', $this->quote($values[$i]), $fields[$i]);
			}
		} else if (!$func) {
    		$this->addMap('value_list', $this->quote($value), $field);
		} else {
    		$this->addMap('value_list', $value, $field);
		}
		$this->type = 'insert';
	}
	
	// implemented addReplace() on top of addInsert()
	
	function addReplace($field, $value, $set = false, $func = false) {
		$this->addInsert($field, $value, $set, $func);
		$this->type = 'replace';
	}
	
	
	function addUpdate($field, $value, $set = false) {
		if ($set) {
			$fields = ((is_array($field)) ? $field : explode(',', $field));
			$values = ((is_array($value)) ? $value : explode(',', $value));

			for ($i = 0, $fc=count($fields); $i < $fc; $i++) {
				$this->addMap('update_list', $values[$i], $fields[$i]);
			}
		} else {
    		$this->addMap('update_list', $value, $field);
		}
		$this->type = 'update';
	}
	
	function createTable($table) {
		$this->type = 'createPermanent';
		$this->create_table = $table;
	}
	
	function createTemp($table) {
		$this->type = 'create';
		$this->create_table = $table;
	}
	
	function dropTable($table) {
		$this->type = 'drop';
		$this->create_table = $table;
	}
	
	function dropTemp($table) {
		$this->type = 'drop';
		$this->create_table = $table;
	}
	
	function alterTable($table) {
		$this->create_table = $table;
		$this->type = 'alter';
	}
	
	function addField($name, $type) {
		if (! is_array($this->create_definition))
			$this->create_definition = array();
		$this->create_definition[] = array('action' => 'ADD',
			'type' => '',
			'spec' => $name . ' ' . $type);
	}
	
	function alterField($name, $type) {
		if (! is_array($this->create_definition)) {
			$this->create_definition = array();
		}
		$this->create_definition[] = array('action' => 'CHANGE',
		                                   'type' => '',
		                                   'spec' => $name . ' ' . $name . ' ' . $type);
	}
	
	function dropField($name) {
		if (! is_array($this->create_definition)) {
			$this->create_definition = array();
		}
		$this->create_definition[] = array('action' => 'DROP',
		                                   'type' => '',
		                                   'spec' => $name);
	}
	
	function addIndex($name, $type)	{
		if (! is_array($this->create_definition)) {
			$this->create_definition = array();
		}
		$this->create_definition[] = array('action' => 'ADD',
		                                   'type' => 'INDEX',
		                                   'spec' => $name . ' ' . $type);
	}
	
	function dropIndex($name) {
		if (! is_array($this->create_definition)) {
			$this->create_definition = array();
		}
		$this->create_definition[] = array('action' => 'DROP',
		                                   'type' => 'INDEX',
		                                   'spec' => $name);
	}
	
	function dropPrimary() {
		if (! is_array($this->create_definition)) {
			$this->create_definition = array();
		}
		$this->create_definition[] = array('action' => 'DROP',
		                                   'type' => 'PRIMARY KEY',
		                                   'spec' => '');
	}
	
	function createDefinition($def) {
		$this->create_definition = $def;
	}
	
	function setDelete($table) {
		$this->type = 'delete';
		$this->addMap('table_list', $table, null);
	}
	
	/** 
	 * Add where sub-clauses.  The where clause can be built up one
	 * part at a time and the resultant query will put in the 'and'
	 * between each component.
	 *
	 * Make sure you use table aliases.
	 *
	 * @param	string 	$query	Where subclause to use
	 */
	function addWhere($query) {
		if (isset($query)) {
			$this->addClause('where', $query);
		}
	}
	
	/**
	 * Add a join condition to the query.  This only implements
	 * left join, however most other joins are either synonymns or
	 * can be emulated with where clauses.
	 *
	 * @param	string	$table	Name of table (without prefix)
	 * @param	string	$alias	Alias to use instead of table name (required).
	 * @param	mixed	$join	Join condition (e.g. 'a.id = b.other_id')
	 *				or array of join fieldnames, e.g. array('id', 'name);
	 *				Both are correctly converted into a join clause.
	 */
	function addJoin($table, $alias, $join, $type = 'left') {
		$var = array ('table' => $table,
		              'alias' => $alias,
		              'condition' => $join,
		              'type' => $type);
		
		$this->addClause('join', $var, false);
	}
	
	function leftJoin($table, $alias, $join) {
		$this->addJoin($table, $alias, $join, 'left');
	}
	
	function rightJoin($table, $alias, $join) {
		$this->addJoin($table, $alias, $join, 'right');
	}
	
	function innerJoin($table, $alias, $join) {
		$this->addJoin($table, $alias, $join, 'inner');
	}
	
	/**
	 * Add an order by clause.  Again, only the fieldname is required, and
	 * it should include an alias if a table has been added.
	 * May be called multiple times.
	 *
	 * @param	string	$order	Order by field.
	 */
	function addOrder($order) {
		if (isset($order)) {
			$this->addClause('order_by', $order);
		}
	}
	
	/**
	 * Add a group by clause.  Only the fieldname is required.
	 * May be called multiple times.  Use table aliases as required.
	 *
	 * @param	string	$group	Field name to group by.
	 */
	function addGroup($group) {
		$this->addClause('group_by', $group);
	}

	/**
	 * Set a limit on the query.  This is done in a database-independent
	 * fashion.
	 *
	 * @param	integer	$limit	Number of rows to limit.
	 * @param	integer	$start	First row to start extraction.
	 */
	function setLimit($limit, $start = -1) {
		$this->limit = $limit;
		$this->offset = $start;
	}
	
	/**
	 * Set include count feature, grabs the count of rows that
	 * would have been returned had no limit been set.
	 */
	function includeCount() {
		$this->include_count = true;
	}
	
	/**
	 * Prepare a query for execution via db_exec.
	 *
	 */
	function prepare($clear = false) {
		switch ($this->type) {
		case 'select':
			$q = $this->prepareSelect();
			break;
		case 'update':
			$q = $this->prepareUpdate();
			break;
		case 'insert':
	        $q = $this->prepareInsert();
			break;
		case 'replace':
	        $q = $this->prepareReplace();
			break;
		case 'delete':
			$q = $this->prepareDelete();
			break;
		case 'create':	// Create a temporary table
	        $s = $this->prepareSelect();
			$q = 'CREATE TEMPORARY TABLE ' . $this->_table_prefix . $this->create_table;
			if (!empty($this->create_definition)) {
				$q .= ' ' . $this->create_definition;
			}
			$q .= ' ' . $s;
			break;
		case 'alter':
			$q = $this->prepareAlter();
			break;
		case 'createPermanent':	// Create a temporary table
			$s = $this->prepareSelect();
			$q = 'CREATE TABLE ' . $this->_table_prefix . $this->create_table;
			if (!empty($this->create_definition)) {
				$q .= ' ' . $this->create_definition;
			}
			$q .= ' ' . $s;
			break;
		case 'drop':
			$q = 'DROP TABLE IF EXISTS ' ;
			if (is_array($this->create_table)) {
				$q .= $this->_table_prefix . implode(','.$this->_table_prefix, $this->create_table);
			} else {
				$q .= $this->_table_prefix . $this->create_table;
			}
			break;
		}
		if ($clear) {
			$this->clear();
		}
		return $q;
		dprint(__FILE__, __LINE__, 2, $q);
	}
	
	function prepareSelect() {
		$q = 'SELECT ';
		if ($this->include_count) {
			$q .= 'SQL_CALC_FOUND_ROWS ';
		}
		if (isset($this->query)) {
			if (is_array($this->query)) {
				$inselect = false;
				$q .= implode(',', $this->query);
			} else {
				$q .= $this->query;
			}
		} else {
			$q .= '*';
		}
		$q .= ' FROM ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				$q .= '(';	// Required for MySQL 5 compatability.
				$intable = false;
				foreach ($this->table_list as $table_id => $table) {
					if ($intable) {
						$q .= ",";
					} else {
						$intable = true;
					}
					$q .= '`' . $this->_table_prefix . $table . '`';
					if (! is_numeric($table_id)) {
						$q .= " as $table_id";
					}
				}
				$q .= ')'; // MySQL 5 compat.
			} else {
				$q .= $this->_table_prefix . $this->table_list;
			}
		} else {
    		return false;
		}
		$q .= $this->make_join($this->join);
		$q .= $this->make_where_clause($this->where);
		$q .= $this->make_group_clause($this->group_by);
		$q .= $this->make_order_clause($this->order_by);
		return $q;
	}
	
	function prepareUpdate() {
		// You can only update one table, so we get the table detail
		$q = 'UPDATE ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				reset($this->table_list);
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= '`' . $this->_table_prefix . $table . '`';
		
		$q .= ' SET ';
		$sets = '';
		foreach ($this->update_list as $field => $value) {
		  $sets .= (($sets) ? ', ' : '') . "`$field` = " . $this->quote($value);
		}
		$q .= $sets;
		$q .= $this->make_where_clause($this->where);
		return $q;
	}
	
	function prepareInsert() {
		$q = 'INSERT INTO ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				reset($this->table_list);
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= '`' . $this->_table_prefix . $table . '`';
		
		$fieldlist = '';
		$valuelist = '';
		foreach ($this->value_list as $field => $value) {
			$fieldlist .= (($fieldlist) ? ',' : '') . '`' . trim($field) . '`';
			$valuelist .= (($valuelist) ? ',' : '') . $value;
		}
		$q .= "($fieldlist) values ($valuelist)";
		return $q;
	}
	
	function prepareReplace() {
		$q = 'REPLACE INTO ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				reset($this->table_list);
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= '`' . $this->_table_prefix . $table . '`';
		
		$fieldlist = '';
		$valuelist = '';
		
		foreach ($this->value_list as $field => $value) {
			$fieldlist .= (($fieldlist) ? ',' : '') . '`' . trim($field) . '`';
			$valuelist .= (($valuelist) ? ',' : '') . $value;
		}
		$q .= "($fieldlist) values ($valuelist)";
		return $q;
	}
	
	function prepareDelete() {
		$q = 'DELETE FROM ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= '`' . $this->_table_prefix . $table . '`';
		$q .= $this->make_where_clause($this->where);
		return $q;
	}

	//TODO: add ALTER DROP/CHANGE/MODIFY/IMPORT/DISCARD/...
	//definitions: http://dev.mysql.com/doc/mysql/en/alter-table.html
	function prepareAlter() {
		$q = 'ALTER TABLE `' . $this->_table_prefix . $this->create_table . '` ';
		if (isset($this->create_definition)) {
			$alters = '';
			if (is_array($this->create_definition)) {
				foreach ($this->create_definition as $def) {
					$alters .= ((($alters) ? ', ' : ' ') . $def['action'] . ' ' . $def['type'] 
					            . ' ' . $def['spec']);
				}
			} else {
				$alters .= ' ADD ' . $this->create_definition;
			}
			$q .= $alters;
		}
		
		return $q; 
	}
	
	/**
	 * Execute the query and return a handle.  Supplants the db_exec query
	 */
	function &exec($style = ADODB_FETCH_BOTH, $debug = false) {
		global $db;
		global $ADODB_FETCH_MODE;
		
		if (! isset($this->_old_style)) {
			$this->_old_style = $ADODB_FETCH_MODE;
		}
		
		$ADODB_FETCH_MODE = $style;
		$this->clearQuery();
		if ($q = $this->prepare()) {
        	dprint(__FILE__, __LINE__, 7, "executing query($q)");
			if ($debug) {
				// Before running the query, explain the query and return the details.
				$qid = $db->Execute('EXPLAIN ' . $q);
				if ($qid) {
					$res = array();
					while ($row = $this->fetchRow()) {
						$res[] = $row;
					}
					dprint(__FILE__, __LINE__, 0, "QUERY DEBUG: " . var_export($res, true));
					$qid->Close();
				}
			}
			$this->_query_id = ((isset($this->limit)) 
			                    ? $db->SelectLimit($q, $this->limit, $this->offset)
			                    : $db->Execute($q));
			
			if (! $this->_query_id) {
				$error = $db->ErrorMsg();
				dprint(__FILE__, __LINE__, 0, "query failed($q) - error was: " . $error);
				return $this->_query_id;
			}
		}
		
		return $this->_query_id;
	}

	function fetchRow() {
		if (! $this->_query_id) {
			return false;
		}
		return $this->_query_id->FetchRow();
	}
	
	/**
	 * loadList - replaces dbLoadList on 
	 */
	function loadList($maxrows = null) {
		global $db;
		global $AppUI;

		if (! $this->exec(ADODB_FETCH_ASSOC)) {
			$AppUI->setMsg($db->ErrorMsg(), UI_MSG_ERROR);
			$this->clear();
			return false;
		}

		$list = array();
		$cnt = 0;
		while ($hash = $this->fetchRow()) {
			$list[] = $hash;
			if ($maxrows && $maxrows == $cnt++) {
				break;
			}
		}
		$this->clear();
		return $list;
	}
	
	function loadHashList($index = null) {
		global $db;

		if (! $this->exec(ADODB_FETCH_ASSOC)) {
			exit ($db->ErrorMsg());
		}
		$hashlist = array();
		$keys = null;
		while ($hash = $this->fetchRow()) {
			if ($index) {
				$hashlist[$hash[$index]] = $hash;
			} else {
				// If we are using fetch mode of ASSOC, then we don't
				// have an array index we can use, so we need to get one
				if (! $keys) {
					$keys = array_keys($hash);
				}
				$hashlist[$hash[$keys[0]]] = $hash[$keys[1]];
			}
		}
		$this->clear();
		return $hashlist;
	}

	function loadHash() {
		global $db;
		if (! $this->exec(ADODB_FETCH_ASSOC)) {
			exit ($db->ErrorMsg());
		}
		$hash = $this->fetchRow();
		$this->clear();
		return $hash;
	}

	function loadArrayList($index = 0) {
		global $db;

		if (! $this->exec(ADODB_FETCH_NUM)) {
			exit ($db->ErrorMsg());
		}
		$hashlist = array();
		$keys = null;
		while ($hash = $this->fetchRow()) {
			$hashlist[$hash[$index]] = $hash;
		}
		$this->clear();
		return $hashlist;
	}

	function loadColumn() {
		global $db;
		if (! $this->exec(ADODB_FETCH_NUM)) {
		  die ($db->ErrorMsg());
		}
		$result = array();
		while ($row = $this->fetchRow()) {
		  $result[] = $row[0];
		}
		$this->clear();
		return $result;
	}

	function loadObject(&$object, $bindAll=false , $strip = true) {
		global $db;
		if (! $this->exec(ADODB_FETCH_NUM)) {
			die ($db->ErrorMsg());
		}
		if ($object != null) {
			$hash = $this->fetchRow();
			$this->clear();
			if (!$hash) {
				return false;
			}
			$this->bindHashToObject($hash, $object, null, $strip, $bindAll);
			return true;
		} else if ($object = $this->_query_id->FetchNextObject(false)) {
			$this->clear();
			return true;
		} else {
			$object = null;
			return false;
		}
	}
	
	/**
	 * Using an XML string, build or update a table.
	 */
	function execXML($xml, $mode = 'REPLACE') {
		global $db, $AppUI;

		include_once DP_BASE_DIR.'/lib/adodb/adodb-xmlschema.inc.php';
		$schema = new adoSchema($db);
		$schema->setUpgradeMode($mode);
		if (isset($this->_table_prefix) && $this->_table_prefix) {
			$schema->setPrefix($this->_table_prefix, false);
		}
		$schema->ContinueOnError(true);
		if (($sql = $scheme->ParseSchemaString($xml)) == false) {
			$AppUI->setMsg(array('Error in XML Schema', 'Error', $db->ErrorMsg()), UI_MSG_ERR);
			return false;
		}
		
		return (($schema->ExecuteSchema($sql, true)) ? true : false);
	}

	/** {{{2 function loadResult
	 * Load a single column result from a single row
	 */
	function loadResult() {
		global $AppUI, $db;
		
		$result = false;
		
		if (! $this->exec(ADODB_FETCH_NUM)) {
			$AppUI->setMsg($db->ErrorMsg(), UI_MSG_ERROR);
		} else if ($data = $this->fetchRow()) {
			$result =  $data[0];
		}
		$this->clear();
		return $result;
	}
	//2}}}

	/** {{{2 function make_where_clause
	 * Create a where clause based upon supplied field.
	 *
	 * @param	mixed	$clause	Either string or array of subclauses.
	 * @return	string
	 */
	function make_where_clause($where_clause) {
		$result = '';
		if (! isset($where_clause)) {
			return $result;
		}
		if (is_array($where_clause)) {
			if (count($where_clause)) {
				$started = false;
				$result = ' WHERE ' . implode(' AND ', $where_clause);
			}
		} else if (mb_strlen($where_clause) > 0) {
			$result = " where $where_clause";
		}
		return $result;
	}
	//2}}}
	
	/** {{{2 function make_order_clause
	 * Create an order by clause based upon supplied field.
	 *
	 * @param	mixed	$clause	Either string or array of subclauses.
	 * @return	string
	 */
	function make_order_clause($order_clause) {
		$result = "";
		if (! isset($order_clause)) {
			return $result;
		}

		if (is_array($order_clause)) {
			$started = false;
			$result = ' ORDER BY ' . implode(',', $order_clause);
		} else if (mb_strlen($order_clause) > 0) {
			$result = " ORDER BY $order_clause";	
		}
		return $result;
	}
	//2}}}
	
	//{{{2 function make_group_clause
	function make_group_clause($group_clause) {
		$result = "";
		if (! isset($group_clause)) {
			return $result;
		}
		
		if (is_array($group_clause)) {
			$started = false;
			$result = ' GROUP BY ' . implode(',', $group_clause);
		} else if (mb_strlen($group_clause) > 0) {
			$result = " GROUP BY $group_clause";
		}
		return $result;
	}
	//2}}}
	
	//{{{2 function make_join
	function make_join($join_clause) {
		$result = "";
		if (! isset($join_clause)) {
			return $result;
		}
		if (is_array($join_clause)) {
			foreach ($join_clause as $join) {
				$result .= (' ' . mb_strtoupper($join['type']) . ' JOIN `' 
				            . $this->_table_prefix . $join['table'] . '`');
				if ($join['alias']) {
					$result .= ' AS ' . $join['alias'];
				}
				$result .= ((is_array($join['condition'])) 
				            ? ' USING (' . implode(',', $join['condition']) . ')' 
				            : ' ON ' . $join['condition']);
			}
		} else {
			$result .= ' LEFT JOIN `' . $this->_table_prefix . $join_clause . '`';
		}
		return $result;
	}
	//2}}}
	
	function foundRows() {
		global $db;
		$result = false;
		if ($this->include_count) {
			if ($qid = $db->Execute('SELECT FOUND_ROWS() as rc')) {
				$data = $qid->FetchRow();
				$result = isset($data['rc']) ? $data['rc'] : $data[0];
			}
		}
		return $result;
	}
	
	function quote($string) {
		global $db;
		return $db->qstr($string, get_magic_quotes_runtime());
	}

	/**
	 * We need to ensure we don't get queries that can cause SQL injections.
	 * For this we need to remove quotes, semicolons, and various other components
	 * that could cause us concern.  This is not exhaustive, but covers the most
	 * common problems.
	 */
	function sanitise($string) {
		return str_replace(array("'", '"', ')', '(', ';', '--'), '', $string);
	}

	function quote_sanitised($string) {
		return $this->quote($this->sanitise($string));
	}
}
//1}}}

// vim600: fdm=marker sw=2 ts=8 ai:
?>

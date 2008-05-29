<?php
// $Id$

/**
 * Copyright 2005, the dotProject Team.
 *
 * This file is part of dotProject and is released under the same license.
 * Check the file index.php in the top level dotproject directory for license
 * details.  If you cannot find this file, or a LICENSE or COPYING file,
 * please email the author for details.
 */

/*
 * Permissions system extends the phpgacl class.  Very few changes have
 * been made, however the main one is to provide the database details from
 * the main dP environment.
 */

if (!(defined('DP_BASE_DIR'))) {
	die('This file should not be called directly.');
}

//Set the ADODB directory
if (!(defined('ADODB_DIR'))) {
	define('ADODB_DIR', DP_BASE_DIR . '/lib/adodb');
}

//Include the PHPGACL library
require_once DP_BASE_DIR . '/lib/phpgacl/gacl.class.php';
require_once DP_BASE_DIR . '/lib/phpgacl/gacl_api.class.php';
//Include the db_connections 

//Now extend the class
/**
 * Extend the gacl_api class.	There is an argument to separate this
 * into a gacl and gacl_api class on the premise that normal activity
 * only needs the functions in gacl, but it would appear that this is
 * not so for dP, which tends to require reverse lookups rather than
 * just forward ones (i.e. looking up who is allowed to do x, rather
 * than is x allowed to do y).
 */
class dPacl extends gacl_api {
	
	function dPacl($opts = null) {
		global $db;
		
		if (!(is_array($opts))) {
			$opts = array();
		}
		$opts['db_type'] = dPgetConfig('dbtype');
		$opts['db_host'] = dPgetConfig('dbhost');
		$opts['db_user'] = dPgetConfig('dbuser');
		$opts['db_password'] = dPgetConfig('dbpass');
		$opts['db_name'] = dPgetConfig('dbname');
		$opts['db'] = $db;
		/*
		 * We can add an ADODB instance instead of the database connection details. 
		 * This might be worth looking at in the future.
		 */
		if (dPgetConfig('debug', 0) > 10) {
			$this->_debug = true;
		}
		parent::gacl_api($opts);
	}
	
	function checkLogin($login) {
		//Simple ARO<->ACO check, no AXO's required.
		return $this->acl_check('system', 'login', 'user', $login);
	}
	
	function checkModule($module, $op, $userid = null) {
		if (!($userid)) {
			$userid = $GLOBALS['AppUI']->user_id;
		}
		$module = (($module == 'sysvals') ? 'system' : $module);
		$result = $this->acl_check('application', $op, 'user', $userid, 'app', $module);
		dprint(__FILE__, __LINE__, 2, "checkModule($module, $op, $userid) returned $result");
		return $result;
	}
	
	function checkModuleItem($module, $op, $item = null, $userid = null) {
		if (!($userid)) {
			$userid = $GLOBALS['AppUI']->user_id;
		}
		if (!($item)) {
			return $this->checkModule($module, $op, $userid);
		}
		
		$result = $this->acl_query('application', $op, 'user', $userid, $module, $item, null);
		//If there is no acl_id then we default back to the parent lookup
		if (!($result && $result['acl_id'])) {
			dprint(__FILE__, __LINE__, 2, 
			       "checkModuleItem($module, $op, $userid) did not return a record");
			return $this->checkModule($module, $op, $userid);
		}
		dprint(__FILE__, __LINE__, 2, 
		       "checkModuleItem($module, $op, $userid) returned " . $result['allow']);
		return $result['allow'];
	}
	
	/**
	 * This gets tricky and is there mainly for the compatibility layer
	 * for getDeny functions.
	 * If we get an ACL ID, and we get allow = false, then the item is
	 * actively denied.	Any other combination is a soft-deny (i.e. not
	 * strictly allowed, but not actively denied.
	 */
	function checkModuleItemDenied($module, $op, $item, $user_id = null) {
		if (!$user_id) {
			$user_id = $GLOBALS['AppUI']->user_id;
		}
		$result = $this->acl_query('application', $op, 'user', $user_id, $module, $item);
		return (($result && $result['acl_id'] && ! $result['allow']) ? true : false);
	}
	
	function addLogin($login, $username) {
		$res = $this->add_object('user', $username, $login, 1, 0, 'aro');
		if (!($res)) {
			dprint(__FILE__, __LINE__, 0, 'Failed to add user permission object');
		}
		return $res;
	}
	
	function updateLogin($login, $username) {
		$id = $this->get_object_id('user', $login, 'aro');
		if (!($id)) {
			return $this->addLogin($login, $username);
		}
		//Check if the details have changed.
		list ($osec, $val, $oord, $oname, $ohid) = $this->get_object_data($id, 'aro');
		if ($oname != $username) {
			$res = $this->edit_object( $id, 'user', $username, $login, 1, 0, 'aro');
			if (!($res)) {
				dprint(__FILE__, __LINE__, 0, 'Failed to change user permission object');
			}
		}
		return $res;
	}
	
	function deleteLogin($login) {
		$id = $this->get_object_id('user', $login, 'aro');
		if ($id) {
			$id = $this->del_object($id, 'aro', true);
			$id = $this->get_object_id('user', $login, 'aro');
			if ($id) {
				dprint(__FILE__, __LINE__, 0, 'Failed to remove user permission object');
			}			
		}
		return $id;
	}
	
	function addModule($mod, $modname) {
		$res = $this->add_object('app', $modname, $mod, 1, 0, 'axo');
		if ($res) {
			 $res = $this->addGroupItem($mod);
		}
		if (!($res)) {
			dprint(__FILE__, __LINE__, 0, 'Failed to add module permission object');
		}
		return $res;
	}
	
	function addModuleSection($mod) {
		$res = $this->add_object_section(ucfirst($mod) . ' Record', $mod, 0, 0, 'axo');
		if (!($res)) {
			dprint(__FILE__, __LINE__, 0, 'Failed to add module permission section');
		}
		return $res;
	}
	
	function addModuleItem($mod, $itemid, $itemdesc) {
		//Verify that description is properly escaped (no effect if already escaped)
		$itemdesc = addslashes(stripslashes($itemdesc));
		$res = $this->add_object($mod, $itemdesc, $itemid, 0, 0, 'axo');
		return $res;
	}
	
	function addGroupItem($item, $group = 'all', $section = 'app', $type = 'axo') {
		if ($gid = $this->get_group_id($group, null, $type)) {
			return $this->add_group_object($gid, $section, $item, $type);
		}
		return false;
	}
	
	function deleteModule($mod) {
		$id = $this->get_object_id('app', $mod, 'axo');
		if ($id) {
			$this->deleteGroupItem($mod);
			$id = $this->del_object($id, 'axo', true);
		}
		if (!($id)) {
			dprint(__FILE__, __LINE__, 0, 'Failed to remove module permission object');
		}
		return $id;
	}
	
	function deleteModuleSection($mod) {
		$id = $this->get_object_section_section_id(null, $mod, 'axo');
		if ($id) {
			$id = $this->del_object_section($id, 'axo', true);
		}
		if (!($id)) {
			dprint(__FILE__, __LINE__, 0, 'Failed to remove module permission section');
		}
		return $id;
	}
	
	/*
	** Deleting all module-associyted entries from the phpgacl tables
	** such as gacl_aco_maps, gacl_acl and gacl_aro_map
	**
	** @author 	gregorerhardt	
	** @date		20070927
	** @cause		#2140
	**
	** @access 	public
	** @param		string	module (directory) name
	** @return
	*/
	function deleteModuleItems($mod) {
		//Declaring the return string
		$ret = null;
		$q = new DBQuery;
		
		//Fetching module-associated ACL ID's
		$q->addTable('gacl_axo_map');
		$q->addQuery('acl_id');
		$q->addWhere("value = '" . $mod ."'");
		$acls = $q->loadHashList('acl_id');
		$q->clear();
		
		$tables = array('gacl_aco_map' => 'acl_id', 'gacl_aro_map' => 'acl_id', 'gacl_acl' => 'id');
		foreach ($acls as $acl => $k) {
			//Deleting gacl_aco_map, gacl_aro_map, and gacl_aco_map entries
			foreach ($tables as $acl_table => $acl_tab_key) {
				$q->setDelete($acl_table);
				$q->addWhere($acl_tab_key . ' = ' . $acl);
				if (!($q->exec())) {
					$ret .= (is_null($ret) ? "\n\t" : '') . db_error();
				}
				$q->clear();
			}
		}
		
		//Returning null (no error) or database error message (error)
		return $ret;
	}
	
	function deleteGroupItem($item, $group = 'all', $section = 'app', $type = 'axo') {
		if ($gid = $this->get_group_id($group, null, $type)) {
			return $this->del_group_object($gid, $section, $item, $type);
		}
		return false;
	}
	
	function isUserPermitted($userid, $module = null) {
		return (($module) ? $this->checkModule($module, 'view', $userid) 
		        : $this->checkLogin($userid));
	}
	
	function getPermittedUsers($module = null) {
		/*
		 * Not as pretty as I'd like, but we can do it reasonably well.
		 * Check to see if we are allowed to see other users.
		 * If not we can only see ourselves.
		 */
		global $AppUI;
		$canViewUsers = $this->checkModule('users', 'view');
		$q	= new DBQuery;
		$q->addTable('users');
		$q->addQuery('user_id,' 
		             . ' concat_ws(", ", contact_last_name, contact_first_name) as contact_name');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');
		$q->addOrder('contact_last_name');
		$res = $q->exec();
		$userlist = array();
		while ($row = $q->fetchRow()) {
			if ($row['user_id'] == $AppUI->user_id || 
			    ($canViewUsers && $this->isUserPermitted($row['user_id'], $module))) {
				$userlist[$row['user_id']] = $row['contact_name'];
			}
		}
		$q->clear();
		//	Now format the userlist as an assoc array.
		return $userlist;
	}
	
	function getItemACLs($module, $uid = null) {
		if (!($uid)) {
			$uid = $GLOBALS['AppUI']->user_id;
		}
		//Grab a list of all acls that match the user/module, for which Deny permission is set.
		return $this->search_acl('application', 'view', 'user', $uid, false, $module, 
		                         false, false, false);
	}
	
	function getUserACLs($uid = null) {
		if (!($uid)) {
			$uid = $GLOBALS['AppUI']->user_id;
		}
		return $this->search_acl('application', false, 'user', $uid, null, false, 
		                         false, false, false);
	}
	
	function getRoleACLs($role_id) {
		$role = $this->getRole($role_id);
		return $this->search_acl('application', false, false, false, $role['name'], false, 
		                         false, false, false);
	}
	
	function getRole($role_id) {
		$data = $this->get_group_data($role_id);
		return (($data) 
		        ? array('id' => $data[0], 'parent_id' => $data[1], 'value' => $data[2], 
		                'name' => $data[3], 'lft' => $data[4], 'rgt' => $data[5]) 
		        : false);
	}
	
	function & getDeniedItems($module, $uid = null) {
		$items = array();
		if (!($uid)) {
			$uid = $GLOBALS['AppUI']->user_id;
		}
		//first get role items
		$roles = $this->getUserRoles($uid);
		foreach ($roles as $role_arr) {
			$acls = $this->getRoleACLs($role_arr['id']);
			if (is_array($acls)) {
				foreach ($acls as $acl) {
					$acl_entry = $this->get_acl($acl);
					if (in_array('view', $acl_entry['aco']['application']) 
					    && $acl_entry['allow'] == false && $acl_entry['enabled'] == true 
					    && isset($acl_entry['axo'][$module])) {
						foreach ($acl_entry['axo'][$module] as $id) {
							$items[] = $id;
						}
					}
				}
			} else {
				dprint(__FILE__, __LINE__, 2, 
				       "getDeniedItems($module, $uid) - no role ACL's match");
			}
		}
		
		
		//now get use specific items
		$acls = $this->getItemACLs($module, $uid);
		if (is_array($acls)) {
			foreach ($acls as $acl) {
				$acl_entry = $this->get_acl($acl);
				if ($acl_entry['allow'] == false && $acl_entry['enabled'] == true 
				    && isset($acl_entry['axo'][$module])) {
					foreach ($acl_entry['axo'][$module] as $id) {
						$items[] = $id;
					}
				}
			}
		} else {
			dprint(__FILE__, __LINE__, 2, "getDeniedItems($module, $uid) - no user ACL's match");
		}
		dprint(__FILE__,__LINE__, 2, 
		       "getDeniedItems($module, $uid) returning " . count($items) . ' items');
		return $items;
	}
	
	//This is probably redundant.
	function & getAllowedItems($module, $uid = null) {
		$items = array();
		if (!($uid)) {
			$uid = $GLOBALS['AppUI']->user_id;
		}
		
		//first get role items
		$roles = $this->getUserRoles($uid);
		foreach ($roles as $role_arr) {
			$acls = $this->getRoleACLs($role_arr['id']);
			if (is_array($acls)) {
				foreach ($acls as $acl) {
					$acl_entry = $this->get_acl($acl);
					if (in_array('view', $acl_entry['aco']['application']) 
					    && $acl_entry['allow'] == true && $acl_entry['enabled'] == true 
					    && isset($acl_entry['axo'][$module])) {
						foreach ($acl_entry['axo'][$module] as $id) {
							$items[] = $id;
						}
					}
				}
			} else {
				dprint(__FILE__, __LINE__, 2, 
				       "getAllowedItems($module, $uid) - no role ACL's match");
			}
		}
		
		//now get use specific items
		$acls = $this->getItemACLs($module, $uid);
		if (is_array($acls)) {
			foreach ($acls as $acl) {
				$acl_entry = $this->get_acl($acl);
				if ($acl_entry['allow'] == true && $acl_entry['enabled'] == true 
				    && isset($acl_entry['axo'][$module])) {
					foreach ($acl_entry['axo'][$module] as $id) {
						$items[] = $id;
					}
				}
			}
		} else {
			dprint(__FILE__, __LINE__, 2, "getAllowedItems($module, $uid) - no user ACL's match");
		}
		dprint(__FILE__,__LINE__, 2, 
		       "getAllowedItems($module, $uid) returning " . count($items) . ' items');
		return $items;
	}
	/*
	 * Copied from get_group_children in the parent class, this version returns
	 * all of the fields, rather than just the group ids. This makes it a bit
	 * more efficient as it doesn't need the get_group_data call for each row.
	 */
	function getChildren($group_id, $group_type = 'ARO', $recurse = 'NO_RECURSE') {
		$this->debug_text(('get_group_children(): Group_ID: ' . $group_id . ' Group Type: ' 
						   . $group_type . ' Recurse: ' . $recurse));
		
		switch (strtolower(trim($group_type))) {
			case 'axo':
				$group_type = 'axo';
				$table = $this->_db_table_prefix .'axo_groups';
				break;
			default:
				$group_type = 'aro';
				$table = $this->_db_table_prefix .'aro_groups';
				break;
		}
		
		if (empty($group_id)) {
			$this->debug_text(('get_group_children(): ID (' . $group_id 
			                   . ') is empty, this is required'));
			return FALSE;
		}
		
		$q = new DBQuery;
		$q->addTable($table, 'g1');
		$q->addQuery('g1.id, g1.name, g1.value, g1.parent_id');
		$q->addOrder('g1.value');
	
		switch (strtoupper($recurse)) {
			case 'RECURSE':
				$q->addJoin($table, 'g2', 'g2.lft<g1.lft AND g2.rgt>g1.rgt');
				$q->addWhere('g2.id='. $group_id);
				break;
			default:
				$q->addWhere('g1.parent_id='. $group_id);
				break;
		}
		
		$result = array();
		$q->exec();
		while ($row = $q->fetchRow()) {
			$result[] = array('id' => $row[0], 'name' => $row[1], 
			                  'value' => $row[2], 'parent_id' => $row[3]);
		}
		$q->clear();
		return $result;
	}
	
	function insertRole($value, $name) {
		$role_parent = $this->get_group_id('role');
		$value = str_replace(' ', '_', $value);
		return $this->add_group($value, $name, $role_parent);
	}
	
	function updateRole($id, $value, $name) {
		return $this->edit_group($id, $value, $name);
	}
	
	function deleteRole($id) {
		//Delete all of the group assignments before deleting group.
		$objs = $this->get_group_objects($id);
		foreach ($objs as $section => $value) {
			$this->del_group_object($id, $section, $value);
		}
		return $this->del_group($id, false);
	}
	
	function insertUserRole($role, $user) {
		//Check to see if the user ACL exists first.
		$id = $this->get_object_id('user', $user, 'aro');
		if (!($id)) {
			$q = new DBQuery;
			$q->addTable('users');
			$q->addQuery('user_username');
			$q->addWhere('user_id = ' . $user);
			$rq = $q->exec();
			if (!($rq)) {
				dprint(__FILE__, __LINE__, 0, 
					   ('Cannot add role, user ' . $user . ' does not exist!<br />' . db_error()));
				$q->clear();
				return false;
			}
			$row = $q->fetchRow();
			if ($row) {
				$this->addLogin($user, $row['user_username']);
			}
			$q->clear();
		}
		return $this->add_group_object($role, 'user', $user);
	}
	
	function deleteUserRole($role, $user) {
		return $this->del_group_object($role, 'user', $user);
	}
	
	/*
	 * Returns the group ids of all groups this user is mapped to. 
	 * Not provided in original phpGacl, but useful.
	 */
	function getUserRoles($user) {
		$id = $this->get_object_id('user', $user, 'aro');
		$result = $this->get_group_map($id);
		if (!(is_array($result))) {
			$result = array();
		}
		return $result;
	}
	
	// Return a list of module groups and modules that a user can be permitted access to.
	function getModuleList() {
		$result = array();
		// First grab all the module groups.
		$parent_id = $this->get_group_id('mod', null, 'axo');
		if (!($parent_id)) {
			dprint(__FILE__, __LINE__, 0, 'failed to get parent for module groups');
		}
		$groups = $this->getChildren($parent_id, 'axo');
		if (is_array($groups)) {
			foreach ($groups as $group) {
				$result[] = array('id' => $group['id'], 'type' => 'grp', 
				                  'name' => $group['name'], 'value' => $group['value']);
			}
		} else {
			dprint(__FILE__, __LINE__, 1, "No groups available for $parent_id");
		}
		//Now the individual modules.
		$modlist = $this->get_objects_full('app', 0, 'axo');
		if (is_array($modlist)) {
			foreach ($modlist as $mod) {
				$result[] = array('id' => $mod['id'], 'type' => 'mod', 
				                  'name' => $mod['name'], 'value' => $mod['value']);
			}
		}
		return $result;
	}
	/*
	 * An assignable module is one where there is a module sub-group
	 * Effectivly we just list those module in the section "modname"
	 */
	function getAssignableModules() {
		return $this->get_object_sections(null, 0, 'axo', 'value not in ("sys", "app")');
	}
	
	function getPermissionList() {
		$result = array();
		$list = $this->get_objects_full('application', 0, 'aco');
		if (is_array($list)) {
			//We only need the id and the name
			foreach ($list as $perm) {
				$result[$perm['id']] = $perm['name'];
			}
		}
		return $result;
	}
	
	function get_group_map($id, $group_type = 'ARO') {
		$this->debug_text('get_group_map(): Assigned ID: ' . $id . ' Group Type: ' . $group_type);
		$grp_type_mod = strtolower(trim($group_type));
		switch ($grp_type_mod) {
			case 'axo':
				$group_type = $grp_type_mod;
				break;
			default:
				$group_type = 'aro';
				break;
		}
		$table = $this->_db_table_prefix . $group_type . '_groups';
		$map_table = $this->_db_table_prefix . 'groups_' . $group_type . '_map';
		$map_field = $group_type . '_id';
		
		if (empty($id)) {
			$this->debug_text('get_group_map(): ID (' . $id . ') is empty, this is required');
			return FALSE;
		}
		
		$q = new DBQuery;
		$q->addTable($table, 'g1');
		$q->innerJoin($map_table, 'g2', 'g2.group_id = g1.id');
		$q->addQuery('g1.id, g1.name, g1.value, g1.parent_id');
		$q->addWhere("g2.$map_field = $id");
		$q->addOrder('g1.value');
		
		$result = array();
		$q->exec();
		while ($row = $q->fetchRow()) {
			$result[] = array('id' => $row[0], 'name' => $row[1], 
			                  'value' => $row[2], 'parent_id' => $row[3]);
		}
		$q->clear();
		return $result;
	}
	
	/*======================================================================* \
		Function:	get_object()
	\*======================================================================*/
	function get_object_full($value = null , $section_value = null, $return_hidden = 1, 
	                         $object_type = null) {
		$obj_type_mod = strtolower(trim($object_type));
		switch($obj_type_mod) {
			case 'aco':
			case 'aro':
			case 'axo':
			case 'acl':
				$object_type = $obj_type_mod;
				break;
			default:
				$this->debug_text('get_object(): Invalid Object Type: '. $object_type);
				return FALSE;
				break;
		}
		
		$table = $this->_db_table_prefix . $object_type;
		$this->debug_text(('get_object(): Section Value: ' . $section_value . ' Object Type: ' 
		                   . $object_type));
		
		$q = new DBQuery;
		$q->addTable($table);
		$q->addQuery('id, section_value, name, value, order_value, hidden');
		if (!(empty($value))) {
			$q->addWhere('value=' . $this->db->quote($value));
		}
		if (!(empty($section_value))) {
			$q->addWhere('section_value='. $this->db->quote($section_value));
		}
		if ($return_hidden == 0 && $object_type != 'acl') {
			$q->addWhere('hidden=0');
		}
		$q->exec();
		$row = $q->fetchRow();
		$q->clear();
		
		if (!(is_array($row))) {
			$this->debug_db('get_object');
			return false;
		}
		
		//Return Object info.
		return array('id' => $row[0], 'section_value' => $row[1], 'name' => $row[2], 
		             'value' => $row[3], 'order_value' => $row[4], 'hidden' => $row[5]);
	}
	
	/*======================================================================*\
		Function:	get_objects ()
		Purpose:	Grabs all Objects in the database, or specific to a section_value
					returns format suitable for add_acl and is_conflicting_acl
	\*======================================================================*/
	function get_objects_full($section_value = null, $return_hidden = 1, $object_type = null, 
	                          $limit_clause = null) {
		$obj_type_mod = strtolower(trim($object_type));
		switch ($obj_type_mod) {
			case 'aco':
			case 'aro':
			case 'axo':
				$object_type = $obj_type_mod;
				break;
			default:
				$this->debug_text('get_objects(): Invalid Object Type: '. $object_type);
				return FALSE;
				break;
		}
		
		$table = $this->_db_table_prefix . $obj_type_mod;
		$this->debug_text(('get_objects(): Section Value: ' . $section_value . ' Object Type:  ' 
		                   . $object_type));
		
		$q = new DBQuery;
		$q->addTable($table);
		$q->addQuery('id, section_value, name, value, order_value, hidden');
		if (!(empty($section_value))) {
			$q->addWhere('section_value=' . $this->db->quote($section_value));
		}
		if ($return_hidden == 0) {
			$q->addWhere('hidden=0');
		}
		if (!(empty($limit_clause))) {
			$q->addWhere($limit_clause);
		}
		$q->addOrder('order_value');
		
		/*
		$rs = $q->exec();
		if (!(is_object($rs))) {
			$this->debug_db('get_objects');
			return FALSE;
		}
		*/
		
		$retarr = array();
		$q->exec();
		while ($row = $q->fetchRow()) {
			$retarr[] = array('id' => $row[0], 'section_value' => $row[1], 'name' => $row[2], 
			                  'value' => $row[3], 'order_value' => $row[4], 'hidden' => $row[5]);
		}
		$q->clear();
		
		//Return objects
		return $retarr;
	}
	
	function get_object_sections($section_value = null, $return_hidden = 1, $object_type = null, 
	                             $limit_clause = null) {
		$obj_type_mod = strtolower(trim($object_type));
		switch ($obj_type_mod) {
			case 'aco':
			case 'aro':
			case 'axo':
				$object_type = $obj_type_mod;
				break;
			default:
				$this->debug_text('get_object_sections(): Invalid Object Type: '. $object_type);
				return FALSE;
				break;
		}
		
		$table = $this->_db_table_prefix . $object_type . '_sections';
		$this->debug_text(('get_objects(): Section Value: ' . $section_value . ' Object Type: ' 
		                   . $object_type));
		
		//$query = 'SELECT id, value, name, order_value, hidden FROM '. $table;
		$q = new DBQuery;
		$q->addTable($table);
		$q->addQuery('id, value, name, order_value, hidden');
		if (!(empty($section_value))) {
			$q->addWhere('value=' . $this->db->quote($section_value));
		}
		if ($return_hidden == 0) {
			$q->addWhere('hidden=0');
		}
		if (!(empty($limit_clause))) {
			$q->addWhere($limit_clause);
		}
		$q->addOrder('order_value');
		$rs = $q->exec();
		/*
		if (!is_object($rs)) {
			$this->debug_db('get_object_sections');
			return FALSE;
		}
		*/
		
		$retarr = array();
		while ($row = $q->fetchRow()) {
			$retarr[] = array('id' => $row[0], 'value' => $row[1], 'name' => $row[2], 
			                  'order_value' => $row[3], 'hidden' => $row[4]);
		}
		$q->clear();
		
		//Return objects
		return $retarr;
	}
	
	//Called from do_perms_aed, allows us to add a new ACL 
	function addUserPermission() {
		//Need to have a user id, parse the permissions array
		if (!(is_array($_POST['permission_type']))) {
			$this->debug_text('you must select at least one permission');
			return false;
		}
		
		$mod_type = substr($_POST['permission_module'],0,4);
		$mod_id = substr($_POST['permission_module'],4);
		$mod_group = null;
		$mod_mod = null;
		if ($mod_type == 'grp,') {
			$mod_group = array($mod_id);
		} else if (isset($_POST['permission_item']) && $_POST['permission_item']) {
			$mod_mod = array();
			$mod_mod[$_POST['permission_table']][] =	$_POST['permission_item'];
			//First need to check if the section exists.
			if (!($this->get_object_section_section_id(null, $_POST['permission_table'], 'axo'))) {
					$this->addModuleSection($_POST['permission_table']);
			}
			//check if the item already exists, if not create it.
			if (!($this->get_object_id($_POST['permission_table'], $_POST['permission_item'], 
			                           'axo'))) {
				$this->addModuleItem($_POST['permission_table'], $_POST['permission_item'], 
									 $_POST['permission_name']);
			}
		} else {
			//Get the module information
			$mod_info = $this->get_object_data($mod_id, 'axo');
			$mod_mod = array();
			$mod_mod[$mod_info[0][0]][] = $mod_info[0][1];
		}
		
		if (!(empty($_POST['role_id']))) {
			$user_map = null;
			$role_map = array($_POST['role_id']);
		} else {
			$role_map = null;
			$aro_info = $this->get_object_data($_POST['permission_user'], 'aro');
			$user_map = array();
			$user_map[$aro_info[0][0]][] = $aro_info[0][1];
		}
		//Build the permissions info
		$type_map = array();
		foreach ($_POST['permission_type'] as $tid) {
			$type = $this->get_object_data($tid, 'aco');
			foreach ($type as $t) {
				$type_map[$t[0]][] = $t[1];
			}
		}
		return $this->add_acl($type_map, $user_map, $role_map, $mod_mod, $mod_group, 
		                      $_POST['permission_access'], 1, null, null, 'user');
	}
	
	//Deprecated, now just calls addUserPermission
	function addRolePermission() {
			return $this->addUserPermission();
	}
	
	//Some function overrides.
	function debug_text($text) {
		$this->_debug_msg = $text;
		dprint(__FILE__, __LINE__, 9, $text);
	}
	
	function msg() {
		return $this->_debug_msg;
	}
	
}
?>

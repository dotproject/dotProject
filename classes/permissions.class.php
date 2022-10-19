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

	function __construct($opts = null) {
		global $db;

		if (empty($opts) || !(is_array($opts))) {
			$opts = array();
		}
		$opts['db_type'] = dPgetConfig('dbtype');
		$opts['db_host'] = dPgetConfig('dbhost');
		$opts['db_user'] = dPgetConfig('dbuser');
		$opts['db_password'] = dPgetConfig('dbpass');
		$opts['db_name'] = dPgetConfig('dbname');
		$opts['caching'] = dPgetConfig('gacl_cache', false);
		// With dotP prefixing, we can end up with doubles if we perform queries ourself.
		$this->_original_db_prefix = $this->_db_table_prefix;
		$opts['db_table_prefix'] = dPgetConfig('dbprefix','').$this->_db_table_prefix;
		$opts['force_cache_expire'] = dPgetConfig('gacl_expire', true);
		$opts['cache_dir'] = dPgetConfig('gacl_cache_dir', '/tmp');
		$opts['cache_expire_time'] = dPgetConfig('gacl_timeout', 600);
		$opts['db'] = $db;
		/*
		 * We can add an ADODB instance instead of the database connection details.
		 * This might be worth looking at in the future.
		 */
		if (dPgetConfig('debug', 0) > 10) {
			$this->_debug = true;
		}
		if (method_exists(get_parent_class(), 'gacl_api') && is_callable(get_parent_class(), 'gacl_api')) {
      parent::gacl_api($opts);
    } else if (method_exists(get_parent_class(), 'gacl') && is_callable(get_parent_class(), 'gacl')) {
      parent::gacl($opts);
    }
    // else do nothing, don't call anything (gwyneth 20210414)
	}

  /**
   * Simple function to check if the user belongs to a group
   *
   * @param integer $login is the user ID; set to 0 by default (which will always fail)
   + @return integer is one of: (PERM_DENY, PERM_EDIT, PERM_READ, PERM_ALL)
   */
	function checkLogin($login = 0) {
		//Simple ARO<->ACO check, no AXO's required.
		//return $this->acl_check('system', 'login', 'user', $login);
    // For dotproject, this is equivalent to check if the user belongs to a group.
    // checkLogin will be done in that way, instead checking for the "login" aco in the "system" section,
    // because that should involve to check for nested ACOs when building the dotpermissions table, which
    // would make it a bit more complex.
    //
    $q = new DBQuery;
    $q->addQuery('aro.value,aro.name, gr_aro.group_id');
    $q->addTable('gacl_aro', 'aro');
    $q->innerJoin('gacl_groups_aro_map', 'gr_aro', 'aro.id=gr_aro.aro_id');
    $q->addWhere('aro.value=' . (int)$login);
    $q->setLimit(1);
    $arr=$q->loadHash();
//    return !empty($arr) ? 1 : 0;
    return (int)($arr ?? PERM_DENY);  // this is a bit tricky, but we were missing the -1 permission...
  }

	function checkModule($module, $op, $userid = null) {
		if (empty($userid)) {
			$userid = $GLOBALS['AppUI']->user_id ?? 0; // zero seems to be "safer"... (gwyneth 20210416)
		}

    $q = new DBQuery;
    $q->addQuery('allow');
    $q->addTable('dotpermissions', 'dp');
    $q->addWhere("permission='" . $op . "' AND axo='" . $module . "' AND user_id='" . $userid . "' and section='app'");
    $q->addOrder('priority ASC, acl_id DESC');
    $q->setLimit(1);
    $arr = $q->loadHash();

    if (!empty($arr) && is_array($arr) && isset($arr['allow'])) {  // extra check for null! (gwyneth 20210416)
      $result = (int) $arr['allow'];
    } else {
      $result = PERM_DENY;
    }
    //echo $result;
    if ($module == "projects") {
      dprint(__FILE__, __LINE__, 2,  "[DEBUG]: " . __FUNCTION__ . "(" . $module . "," . $op . "," . ($userid ?? '[nobody]') . ") returned " . $result . " (should be either -1, 0, or 1).");
    }
    return $result;
	/*
		$module = (($module == 'sysvals') ? 'system' : $module);
		$result = $this->acl_check('application', $op, 'user', $userid, 'app', $module);
		dprint(__FILE__, __LINE__, 2, "checkModule($module, $op, $userid) returned $result");
		return $result;
	*/
	}

	function checkModuleItem($module, $op, $item = null, $userid = null) {
		if (!($userid)) {
			$userid = $GLOBALS['AppUI']->user_id;
		}
		if (!($item)) {
			return $this->checkModule($module, $op, $userid);
		}

		$q = new DBQuery;
		$q->addQuery('allow');
		$q->addTable('dotpermissions');
		$q->addWhere("permission='" . $op . "' AND axo='" . $item . "' AND user_id='" . $userid . "' and section='" . $module . "'");
		$q->addOrder('priority ASC,acl_id DESC');
		$q->setLimit(1);
		$arr = $q->loadHash();
    if (!empty($arr) && !empty($arr['allow'])) {  // safer using `empty()` (gwyneth 20210504)
      $result = (int) $arr['allow'];
    } else {
      $result = null;  // no record returned, so it will be called below (gwyneth 20210415)
    }
		if (empty($result)) {  // it's better to check for empty() since it catches a lot more things (gwyneth 20210415)
			dprint(__FILE__, __LINE__, 2,
			  "[WARN]: " . __FUNCTION__ . "(" . $module . "," . $op . "," . ($userid ?? '[nobody]') . ") did not return a record");
			return $this->checkModule($module, $op, $userid);
		}
		dprint(__FILE__, __LINE__, 2,
      "[DEBUG]: " . __FUNCTION__ . "(" . $module . "," . $op . "," . ($userid ?? '[nobody]') . ") returned " . $result . ".");
		return $result;
	}

	/**
	 * This gets tricky and is there mainly for the compatibility layer
	 * for getDeny functions.
	 * If we get an ACL ID, and we get allow = false, then the item is
	 * actively denied.	Any other combination is a soft-deny (i.e. not
	 * strictly allowed, but not actively denied.
   *
   * @note I _might_ have broken this with too much error checking... (gwyneth 20210504)
	 */
	function checkModuleItemDenied($module, $op, $item, $user_id = null) {
		if (!$user_id) {
			$user_id = $GLOBALS['AppUI']->user_id ?? 0;  // `?? 0` ought to do the trick (gwyneth 20210504)
		}
		$q = new DBQuery;
		$q->addQuery('allow');
		$q->addTable('dotpermissions');
		$q->addWhere("permission='" . $op . "' AND axo='" . $item . "' AND user_id='" . $user_id . "' and section='" . $module . "'");
		$q->addOrder('priority ASC, acl_id DESC');
		$q->setLimit(1);
		$arr = $q->loadHash();
		if(!empty($arr) && !empty($arr['allow'])) {  // it's safer to use `empty()` (gwyneth 20210504)
			return true;
		} else {
			return false;
		}
	}

	function addLogin($login, $username) {
		$res = $this->add_object('user', $username, $login, 1, 0, 'aro');
		if (!($res)) {
			dprint(__FILE__, __LINE__, 0, 'Failed to add user permission object');
			$this->regeneratePermissions();
		}
		return $res;
	}

	function updateLogin($login, $username) {
		$id = $this->get_object_id('user', $login, 'aro');
		if (!($id)) {
			return $this->addLogin($login, $username);
		}
		// Check if the details have changed.
    // Note: this _can_ return false, so we check first: (gwyneth 20210427)
    $oPermissions = $this->get_object_data($id, 'aro');
    // As result, we get either an object, or false, so we'll check for false first: (gwyneth 20210427)
    if ($oPermissions === false) {
      return false;  // I have no idea if this is correct (gwyneth 20210427)
    }
		// list ($osec, $val, $oord, $oname, $ohid) = $oPermissions;
    //  I personally hate list (...) because it's hard to debug; I'm getting errors on some fields
    //  but have no idea why. Allegedly, we want to check if the username is to be changed? (gwyneth 20210427)
		if (!empty($oPermissions["name"]) && $oPermissions["name"] != $username) {
			$res = $this->edit_object( $id, 'user', $username, $login, 1, 0, 'aro');  // It seems that this always returns true or false (gwyneth 202210427)
			if (!($res)) {
				dprint(__FILE__, __LINE__, 0, 'Failed to change user permission object');
			}
			$this->regeneratePermissions(); //this line was outside of this if (after the next bracket
		}
		return $res ?? false;
	}

	function deleteLogin($login) {
		$id = $this->get_object_id('user', $login, 'aro');
		if ($id) {
			$id = $this->del_object($id, 'aro', true);
			$id = $this->get_object_id('user', $login, 'aro');
			if ($id) {
				dprint(__FILE__, __LINE__, 0, 'Failed to remove user permission object');
			} else {
				$this->regeneratePermissions();
			}
		}
		return $id;
	}

	function addModule($mod, $modname) {
		$res = $this->add_object('app', $modname, $mod, 1, 0, 'axo');
		if ($res) {
			 $res = $this->addGroupItem($mod);
			 $this->regeneratePermissions();
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
		} else {
			$this->regeneratePermissions();
		}
		return $res;
	}

	function addModuleItem($mod, $itemid, $itemdesc) {
		//Verify that description is properly escaped (no effect if already escaped)
		$itemdesc = addslashes(stripslashes($itemdesc));
		$res = $this->add_object($mod, $itemdesc, $itemid, 0, 0, 'axo');
		$this->regeneratePermissions();
		return $res;
	}

	function addGroupItem($item, $group = 'all', $section = 'app', $type = 'axo') {
		if ($gid = $this->get_group_id($group, null, $type)) {
			$res=$this->add_group_object($gid, $section, $item, $type);
			$this->regeneratePermissions();
			return $res;
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
		} else {
			$this->regeneratePermissions();
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
		} else {
			$this->regeneratePermissions();
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
		$res=$this->del_group_object($gid, $section, $item, $type);
		$this->regeneratePermissions();
		return $res;
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

	function getDeniedItems($module, $uid = null) {
      // Checking in dotpermissions..
      // Is getDeniedItems operation-independent???
		$items = array();
		if (!($uid)) {
			$uid = $GLOBALS['AppUI']->user_id;
		}
		//first get role items
		$q = new DBQuery;
		$q->addQuery('distinct axo');
		$q->addTable('dotpermissions');
		$q->addWhere("allow=0 AND user_id=$uid AND section='$module' AND enabled=1");
		$items = $q->loadColumn();

		dprint(__FILE__,__LINE__, 8,
		       "getDeniedItems($module, $uid) returning " . count($items) . ' items');
		return $items;
	}

	//This is probably redundant.
	function getAllowedItems($module, $uid = null) {
    // Checking in dotpermissions..
      // Is getAllowedItems operation-independent???
		$items = array();
		if (!($uid)) {
			$uid = $GLOBALS['AppUI']->user_id;
		}

		// Bug found by Anne (dotproject forum) -- Changed from "allow=0" to "allow!=0"
		$q = new DBQuery;
		$q->addQuery('distinct axo');
		$q->addTable('dotpermissions');
		$q->addWhere("allow!=0 AND user_id=$uid AND section='$module' AND enabled=1");
		$items = $q->loadColumn();
		dprint(__FILE__,__LINE__, 8,
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
				$table = 'gacl_axo_groups';
				break;
			default:
				$group_type = 'aro';
				$table = 'gacl_aro_groups';
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
		$res=$this->add_group($value, $name, $role_parent);
		$this->regeneratePermissions();
		return $res;
	}

	function updateRole($id, $value, $name) {
		$res=$this->edit_group($id, $value, $name);
		$this->regeneratePermissions();
		return $res;
	}

	function deleteRole($id) {
		//Delete all of the group assignments before deleting group.
		$objs = $this->get_group_objects($id);
		foreach ($objs as $section => $value) {
			$this->del_group_object($id, $section, $value);
		}
		$res=$this->del_group($id, false);
		$this->regeneratePermissions();
		return $res;
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
		$res=$this->add_group_object($role, "user", $user);
		$this->regeneratePermissions();
		return $res;
	}

	function deleteUserRole($role, $user) {
	$res=$this->del_group_object($role, 'user', $user);
	$this->regeneratePermissions();
	return $res;
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
		$table = $this->_original_db_prefix . $group_type . '_groups';
		$map_table = $this->_original_db_prefix . 'groups_' . $group_type . '_map';
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

		$table = $this->_original_db_prefix . $object_type;
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

		$table = $this->_original_db_prefix . $obj_type_mod;
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

		$table = $this->_original_db_prefix . $object_type . '_sections';
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
		$res = $this->add_acl($type_map, $user_map, $role_map, $mod_mod, $mod_group,
								$_POST['permission_access'], 1, null, null, 'user');
		$this->regeneratePermissions();
	    return $res;
	}

	//Deprecated, now just calls addUserPermission
	function addRolePermission() {
		$this->regeneratePermissions();
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

	function del_acl($id) {
		gacl_api::del_acl($id);
	    $this->regeneratePermissions();
	}

  /**
   * dotpermissions table generation (Jose Maria Rodriguez Millan,28/05/2008)
   * Generates a single table for permissions lookup.
   * This should be regenerated every time an axo,aro or aco is added/modified/deleted.
   *
   * This is very rough, as it regenerates the whole table independently of what was actually
   * modified, but will do.
   *
   * Maybe it'd be locking the table?
   *
   */
  function regeneratePermissions() {
      $dbprefix = dPgetConfig('dbprefix','');
      $query2[]="TRUNCATE TABLE " . $dbprefix . "dotpermissions";

      // Direct "aro" - "axos" assignments
      $query2[]="INSERT INTO " . $dbprefix . "dotpermissions (acl_id,user_id,section,axo,permission,allow,priority,enabled) ".
       "SELECT  acl.id,aro.value,axo_m.section_value,axo_m.value,aco_m.value,acl.allow,1,acl.enabled
		from " . $dbprefix . "gacl_acl acl
		LEFT JOIN " . $dbprefix . "gacl_aco_map aco_m ON acl.id=aco_m.acl_id
		LEFT JOIN " . $dbprefix . "gacl_aro_map aro_m ON acl.id=aro_m.acl_id
		LEFT JOIN " . $dbprefix . "gacl_aro aro ON aro_m.value=aro.value
		LEFT JOIN " . $dbprefix . "gacl_axo_map axo_m on axo_m.acl_id=acl.id
		WHERE aro.name IS NOT NULL AND axo_m.value IS NOT NULL";

      // aro to axo groups
      $query2[]="INSERT INTO " . $dbprefix . "dotpermissions (acl_id,user_id,section,axo,permission,allow,priority,enabled) ".
       "SELECT acl.id,aro.value,axo.section_value,axo.value,aco_m.value,acl.allow,2,acl.enabled
	   from " . $dbprefix . "gacl_acl acl
	   LEFT JOIN " . $dbprefix . "gacl_aco_map aco_m ON acl.id=aco_m.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro_map aro_m ON acl.id=aro_m.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro aro ON aro_m.value=aro.value
	   LEFT JOIN " . $dbprefix . "gacl_axo_groups_map axo_gm on axo_gm.acl_id=acl.id
	   LEFT JOIN " . $dbprefix . "gacl_axo_groups axo_g on axo_gm.group_id=axo_g.id
	   LEFT JOIN " . $dbprefix . "gacl_groups_axo_map g_axo_m ON axo_g.id=g_axo_m.group_id
	   LEFT JOIN " . $dbprefix . "gacl_axo axo ON g_axo_m.axo_id=axo.id
	   WHERE aro.value IS NOT NULL AND axo_g.value IS NOT NULL";

      // Aro groups to axos
      $query2[]="INSERT into " . $dbprefix . "dotpermissions (acl_id,user_id,section,axo,permission,allow,priority,enabled) ".
       "SELECT  acl.id,aro.value,axo_m.section_value,axo_m.value,aco_m.value,acl.allow,3,acl.enabled
	   from " . $dbprefix . "gacl_acl acl
	   LEFT JOIN " . $dbprefix . "gacl_aco_map aco_m ON acl.id=aco_m.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro_groups_map aro_gm ON acl.id=aro_gm.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro_groups aro_g ON aro_gm.group_id=aro_g.id
	   LEFT JOIN " . $dbprefix . "gacl_axo_map axo_m on axo_m.acl_id=acl.id
	   LEFT JOIN " . $dbprefix . "gacl_groups_aro_map g_aro_m ON aro_g.id=g_aro_m.group_id
	   LEFT JOIN " . $dbprefix . "gacl_aro aro ON g_aro_m.aro_id=aro.id
	   WHERE axo_m.value IS NOT NULL AND aro.name IS NOT NULL";

      // Aro groups to axo groups
      $query2[]="INSERT into " . $dbprefix . "dotpermissions (acl_id,user_id, section,axo,permission,allow,priority,enabled) ".
       "SELECT acl.id,aro.value,axo.section_value,axo.value,aco_m.value,acl.allow,4,acl.enabled
	   from " . $dbprefix . "gacl_acl acl
	   LEFT JOIN " . $dbprefix . "gacl_aco_map aco_m ON acl.id=aco_m.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro_map aro_m ON acl.id=aro_m.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro_groups_map aro_gm ON acl.id=aro_gm.acl_id
	   LEFT JOIN " . $dbprefix . "gacl_aro_groups aro_g ON aro_gm.group_id=aro_g.id
	   LEFT JOIN " . $dbprefix . "gacl_axo_groups_map axo_gm on axo_gm.acl_id=acl.id
	   LEFT JOIN " . $dbprefix . "gacl_axo_groups axo_g on axo_gm.group_id=axo_g.id
	   LEFT JOIN " . $dbprefix . "gacl_groups_aro_map g_aro_m ON aro_g.id=g_aro_m.group_id
	   LEFT JOIN " . $dbprefix . "gacl_aro aro ON g_aro_m.aro_id=aro.id
	   LEFT JOIN " . $dbprefix . "gacl_groups_axo_map g_axo_m ON axo_g.id=g_axo_m.group_id
	   LEFT JOIN " . $dbprefix . "gacl_axo axo ON g_axo_m.axo_id=axo.id
	   WHERE axo_g.value IS NOT NULL and aro.value IS NOT NULL";

      foreach ($query2 as $query) {
          $GLOBALS['db']->Execute($query);
      }
  }

}
?>

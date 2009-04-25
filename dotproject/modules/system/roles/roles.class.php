<?php /* ROLES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
 * This class abstracts the concept of a user Role, which is, in effect, an ARO
 * group in phpGACL speak.  phpGACL has a few constraints, e.g. having only a
 * single parent group, from which all other groups must be determined.  The
 * parent for Roles is 'role'.  You can create parent trees, however a role
 * cannot be its own parent.  For the first pass of this, we limit to a single
 * depth role structure.
 *
 * Once a Role is created, users can be assigned to one or more roles, by adding
 * their user ARO id to the group. All users are given an ARO id which is separate
 * from their user id, but maps it between the dP database and the phpGacl database.
 *
 * Roles, like individual users, can be assigned permissions, and it is expected
 * that most permissions will be assigned at role level, leaving user level for
 * just those exceptions warranting it.  Permissions are added as ACLs.
 *
 * If a role is deleted, then all of the ACLs associated with the role must also
 * be deleted, and then the user id mappings.  Note that the user ARO is _never_
 * deleted, unless the user is.
 */
class CRole {
	var $role_id = NULL;
	var $role_name = NULL;
	var $role_description = NULL;
	var $perms = null;

	function CRole($name='', $description='') {
		$this->role_name = $name;
		$this->role_description = $description;
		$this->perms =& $GLOBALS['AppUI']->acl();
	}

	function bind($hash) {
		if (!is_array($hash)) {
			return get_class($this)."::bind failed";
		} else {
			bindHashToObject($hash, $this);
			return NULL;
		}
	}

	function check() {
		// Not really much to check, just return OK for this iteration.
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if ($msg) {
			return get_class($this)."::store-check failed<br />$msg";
		}
		if ($this->role_id) {
			$ret = $this->perms->updateRole($this->role_id, $this->role_name, $this->role_description);
		} else {
			$ret = $this->perms->insertRole($this->role_name, $this->role_description);
		}
		if (!$ret) {
			return get_class($this)."::store failed";
		} else {
			return NULL;
		}
	}

	function delete() {
		// Delete a role requires deleting all of the ACLs associated
		// with this role, and all of the group data for the role.
		if ($this->perms->checkModuleItem('roles', "delete", $this->role_id)) {
			// Delete all the children from this group
			$this->perms->deleteRole($this->role_id);
			return null;
		} else {
			return get_class($this)."::delete failed <br/>You do not have permission to delete this role";
		}
	}

	function __sleep()
	{
		return array('role_id', 'role_name', 'role_description');
	}

	function __wakeup()
	{
		$this->perms =& $GLOBALS['AppUI']->acl();
	}

	/**
	 * Return a list of known roles.
	 */
	function getRoles()
	{
		$role_parent = $this->perms->get_group_id("role");
		$roles = $this->perms->getChildren($role_parent);
		return $roles;
	}
	

	function rename_array(&$roles, $from, $to) {
		if (count($from) != count($to)) {
			return false;
		}
		foreach ($roles as $key => $val) {
			// 4.2 and before return NULL on fail, later returns false.
			if (($k = array_search($k, $from)) !== false && $k !== null) {
				unset($roles[$key]);
				$roles[$to[$k]] = $val;
			}
		}
		return true;
	}
}
// vim:ai sw=8 ts=8:
?>

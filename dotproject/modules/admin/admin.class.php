<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
* User Class
*/
class CUser extends CDpObject {
	var $user_id = NULL;
	var $user_username = NULL;
	var $user_password = NULL;
	var $user_parent = NULL;
	var $user_type = NULL;
	var $user_contact = NULL;
	var $user_signature = NULL;
/*	var $user_first_name = NULL;
	var $user_last_name = NULL;
	var $user_company = NULL;
	var $user_department = NULL;
	var $user_email = NULL;
	var $user_phone = NULL;
	var $user_home_phone = NULL;
	var $user_mobile = NULL;
	var $user_address1 = NULL;
	var $user_address2 = NULL;
	var $user_city = NULL;
	var $user_state = NULL;
	var $user_zip = NULL;
	var $user_country = NULL;
	var $user_icq = NULL;
	var $user_aol = NULL;
	var $user_birthday = NULL;
	var $user_pic = NULL;
	var $user_owner = NULL; */

	function CUser() {
		$this->CDpObject('users', 'user_id');
	}

	function check() {
		if ($this->user_id === NULL) {
			return 'user id is NULL';
		}
		if ($this->user_password !== NULL) {
			$this->user_password = db_escape(trim($this->user_password));
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if ($msg) {
			return get_class($this)."::store-check failed";
		}
		$q  = new DBQuery;
		if ($this->user_id) {
		// save the old password
			$perm_func = "updateLogin";
			if ($this->user_password) {
				$this->user_password = md5($this->user_password);
				addHistory($this->_tbl, $this->user_id, 'password changed', 
						'Password changed from IP ' . $_SERVER['REMOTE_ADDR']);
			} else {
				$this->user_password = null;
			}

			$ret = db_updateObject('users', $this, 'user_id', false);
		} else {
			$perm_func = "addLogin";
			$this->user_password = md5($this->user_password);
			$ret = db_insertObject('users', $this, 'user_id');
		}
		if (!$ret) {
			return get_class($this)."::store failed <br />" . db_error();
		} else {
			$acl =& $GLOBALS['AppUI']->acl();
			$acl->$perm_func($this->user_id, $this->user_username);
			return NULL;
		}
	}

	function delete($oid = NULL) {
		$id = $this->user_id;
		$result = parent::delete($oid);
		if (! $result) {
			$acl =& $GLOBALS['AppUI']->acl();
			$acl->deleteLogin($id);
			$q = new DBQuery;
			$q->setDelete('user_preferences');
			$q->addWhere('pref_user = '.$this->user_id);
			$q->exec();
			$q->clear();
		}
		return $result;
 	}
}

?>

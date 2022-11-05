<?php

namespace Classes;

/**
 * Authenticate user against database
 */
class SQLAuthenticator
{
	var $user_id;
	var $username;

	/**
	 * Query database and get user to compare
	 *
	 * @param [type] $username
	 * @param [type] $password
	 * @return void
	 */
	function authenticate($username, $password)
	{
		GLOBAL $db, $AppUI;

		$this->username = $username;

		$q  = $this->getDBQueryObj();
		$q->addTable('users');
		$q->addQuery('user_id, user_password');
		$q->addWhere("user_username = '$username'");
		if (!$rs = $q->exec()) {
			$q->clear();
			return false;
		}
		if (!$row = $q->fetchRow()) {
			$q->clear();
			return false;
		}

		$this->user_id = $row["user_id"];
		$q->clear();

		return $this->comparePasswords($password, $row["user_password"]);
	}

	/**
	 * Verifies that passwords are the same
	 *
	 * @param [string] $fPassword ex: from form
	 * @param [string] $dbPassword ex: from database
	 * @return boolean
	 */
	function comparePasswords($fPassword, $dbPassword) {
		if (MD5($fPassword) == $dbPassword) return true;
		return false;
	}

	/**
	 * separated out so that authenticate() can be unit tested
	 */
	function getDBQueryObj() {
		return new DBQuery;
	}

	function userId($username)
	{
		// We ignore the username provided
		return $this->user_id;
	}
}

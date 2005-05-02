<?php
// $Id$

	/*
	 *	Authenticator Class
	 *
	 */


	function &getAuth($auth_mode)
	{
		switch($auth_mode)
		{
			case "ldap":
				$auth = new LDAPAuthenticator();
				return $auth;
				break;
			case "pn":
				$auth = new PostNukeAuthenticator();
				return $auth;
				break;
			default:
				$auth = new SQLAuthenticator();
				return $auth;
				break;
		}
	}

	/**
	 * PostNuke authentication has encoded information
	 * passed in on the login request.  This needs to 
	 * be extracted and verified.
	 */
	class PostNukeAuthenticator extends SQLAuthenticator
	{

		function PostNukeAuthenticator()
		{
			global $dPconfig;
			$this->fallback = isset($dPconfig['postnuke_allow_login']) ? $dPconfig['postnuke_allow_login'] : false;
		}

		function authenticate($username, $password)
		{
			global $db, $AppUI;
			if (!isset($_REQUEST['userdata'])) { // fallback to SQL Authentication if PostNuke fails.
				if ($this->fallback)
					return parent::authenticate($username, $password);
				else {
					die($AppUI->_('You have not configured your PostNuke site correctly'));
				}
			}

			if (! $compressed_data = base64_decode(urldecode($_REQUEST['userdata']))) {
				die($AppUI->_('The credentials supplied were missing or corrupted') . ' (1)');
			}
			if (! $userdata = gzuncompress($compressed_data)) {
				die($AppUI->_('The credentials supplied were missing or corrupted') . ' (2)');
			}
			if (! $_REQUEST['check'] = md5($userdata)) {
				die ($AppUI->_('The credentials supplied were issing or corrupted') . ' (3)');
			}
			$user_data = unserialize($userdata);

			// Now we need to check if the user already exists, if so we just
			// update.  If not we need to create a new user and add a default
			// role.
			$username = trim($user_data['login']);
			$this->username = $username;
			$names = explode(' ', trim($user_data['name']));
			$last_name = array_pop($names);
			$first_name = implode(' ', $names);
			$passwd = trim($user_data['passwd']);
			$email = trim($user_data['email']);
			
			$q  = new DBQuery;
			$q->addTable('users');
			$q->addQuery('user_id, user_password, user_contact');
			$q->addWhere("user_username = '$username'");
			if (! $rs = $q->exec()) {
				die($AppUI->_('Failed to get user details') . ' - error was ' . $db->ErrorMsg());
			}
			if ( $rs->RecordCount() < 1) {
				$q->clear();
				$this->createsqluser($username, $passwd, $email, $first_name, $last_name);
			} else {
				if (! $row = $rs->FetchRow())
					die($AppUI->_('Failed to retrieve user detail'));
				// User exists, update the user details.
				$this->user_id = $row['user_id'];
				$q->clear();
				$q->addTable('users');
				$q->addUpdate('user_password', $passwd);
				$q->addWhere("user_id = {$this->user_id}");
				if (! $q->exec()) {
					die($AppUI->_('Could not update user credentials'));
				}
				$q->clear();
				$q->addTable('contacts');
				$q->addUpdate('contact_first_name', $first_name);
				$q->addUpdate('contact_last_name', $last_name);
				$q->addUpdate('contact_email', $email);
				$q->addWhere("contact_id = {$row['user_contact']}");
				if (! $q->exec()) {
					die($AppUI->_('Could not update user details'));
				}
				$q->clear();
			}
			return true;
		}

		function createsqluser($username, $password, $email, $first, $last)
		{
			GLOBAL $db, $AppUI;

			require_once($AppUI->getModuleClass("contacts"));
	
			$c = New CContact();
			$c->contact_first_name = $first;
			$c->contact_last_name = $last;
			$c->contact_email = $email;
			$c->contact_order_by = "$last, $first";

			db_insertObject('contacts', $c, 'contact_id');
			$contact_id = ($c->contact_id == NULL) ? "NULL" : $c->contact_id;
			if (! $c->contact_id)
				die($AppUI->_('Failed to create user details'));

			$q  = new DBQuery;
			$q->addTable('users');
			$q->addInsert('user_username',$username );
			$q->addInsert('user_password', $password);
			$q->addInsert('user_type', '1');
			$q->addInsert('user_contact', $c->contact_id);
			if (! $q->exec())
				die($AppUI->_('Failed to create user credentials'));
			$user_id = $db->Insert_ID();
			$this->user_id = $user_id;
			$q->clear();

			$acl =& $AppUI->acl();
			$acl->insertUserRole($acl->get_group_id('anon'), $this->user_id);
		}
	}

	class SQLAuthenticator
	{
		var $user_id;
		var $username;

		function authenticate($username, $password)
		{
			GLOBAL $db, $AppUI;

			$this->username = $username;

			$q  = new DBQuery;
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
			if (MD5($password) == $row["user_password"]) return true;
			return false;
		}

		function userId()
		{
			return $this->user_id;
		}
	}	

	class LDAPAuthenticator extends SQLAuthenticator
	{
		var $ldap_host;
		var $ldap_port;
		var $ldap_version;
		var $base_dn;
		var $ldap_search_user;
		var $ldap_search_pass;	
		var $filter;

		var $user_id;
		var $username;

		function LDAPAuthenticator()
		{
			GLOBAL $dPconfig;

			$this->fallback = isset($dPconfig['ldap_allow_login']) ? $dPconfig['ldap_allow_login'] : false;

			$this->ldap_host = $dPconfig["ldap_host"];
			$this->ldap_port = $dPconfig["ldap_port"];
			$this->ldap_version = $dPconfig["ldap_version"];
			$this->base_dn = $dPconfig["ldap_base_dn"];
			$this->ldap_search_user = $dPconfig["ldap_search_user"];
			$this->ldap_search_pass = $dPconfig["ldap_search_pass"];
			$this->filter = $dPconfig["ldap_user_filter"];
		}

		function authenticate($username, $password)
		{
			GLOBAL $dPconfig;
			$this->username = $username;

			if (strlen($password) == 0) return false; // LDAP will succeed binding with no password on AD (defaults to anon bind)
			if ($this->fallback == true)
			{
				if (parent::authenticate($username, $password)) return true;	
			}
			// Fallback SQL authentication fails, proceed with LDAP

			if (!$rs = @ldap_connect($this->ldap_host, $this->ldap_port))
			{
				return false;
			}
			@ldap_set_option($rs, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
			@ldap_set_option($rs, LDAP_OPT_REFERRALS, 0);

			//$ldap_bind_dn = "cn=".$this->ldap_search_user.",".$this->base_dn;
			$ldap_bind_dn = $this->ldap_search_user;	

			if (!$bindok = @ldap_bind($rs, $ldap_bind_dn, $this->ldap_search_pass))
			{
				// Uncomment for LDAP debugging
				/*	
				$error_msg = ldap_error($rs);
				die("Couldnt Bind Using ".$ldap_bind_dn."@".$this->ldap_host.":".$this->ldap_port." Because:".$error_msg);
				*/
				return false;
			}
			else
			{
				$filter_r = str_replace("%USERNAME%", $username, $this->filter);
				$result = @ldap_search($rs, $this->base_dn, $filter_r);
				if (!$result) return false; // ldap search returned nothing or error
				
				$result_user = ldap_get_entries($rs, $result);
				if ($result_user["count"] == 0) return false; // No users match the filter

				$first_user = $result_user[0];
				$ldap_user_dn = $first_user["dn"];

				// Bind with the dn of the user that matched our filter (only one user should match sAMAccountName or uid etc..)

				if (!$bind_user = @ldap_bind($rs, $ldap_user_dn, $password))
				{
					/*
					$error_msg = ldap_error($rs);
					die("Couldnt Bind Using ".$ldap_user_dn."@".$this->ldap_host.":".$this->ldap_port." Because:".$error_msg);
					*/
					return false;
				}
				else
				{
					if ($this->userExists($username))
					{
						return true;
					}
					else
					{
						$this->createsqluser($username, $password, $first_user); 
					}
					return true;
				} 
			}
		}

		function userExists($username)
		{
			GLOBAL $db;
			$q  = new DBQuery;
			$result = false;
			$q->addTable('users');
			$q->addWhere("user_username = '$username'");
			$rs = $q->exec();
			if ($rs->RecordCount() > 0) 
			  $result = true;
			$q->clear();
			return $result;
		}

		function userId($username)
		{
			GLOBAL $db;
			$q  = new DBQuery;
			$q->addTable('users');
			$q->addWhere("user_username = '$username'");
			$rs = $q->exec();
			$row = $rs->FetchRow();
			$q->clear();
			return $row["user_id"];	
		}

		function createsqluser($username, $password, $ldap_attribs = Array())
		{
			GLOBAL $db, $AppUI;
			$hash_pass = MD5($password);

			require_once($AppUI->getModuleClass("contacts"));
	
			if (!count($ldap_attribs) == 0)
			{
				// Contact information based on the inetOrgPerson class schema
				$c = New CContact();
				$c->contact_first_name = $ldap_attribs["givenname"][0];
				$c->contact_last_name = $ldap_attribs["sn"][0];
				$c->contact_email = $ldap_attribs["mail"][0];
				$c->contact_phone = $ldap_attribs["telephonenumber"][0];
				$c->contact_mobile = $ldap_attribs["mobile"][0];
				$c->contact_city = $ldap_attribs["l"][0];
				$c->contact_country = $ldap_attribs["country"][0];
				$c->contact_state = $ldap_attribs["st"][0];
				$c->contact_zip = $ldap_attribs["postalcode"][0];
				$c->contact_job = $ldap_attribs["title"][0];

				//print_r($c); die();
				db_insertObject('contacts', $c, 'contact_id');
			}
			$contact_id = ($c->contact_id == NULL) ? "NULL" : $c->contact_id;

			$q  = new DBQuery;
			$q->addTable('users');
			$q->addInsert('user_username',$username );
			$q->addInsert('user_password', $hash_pass);
			$q->addInsert('user_type', '1');
			$q->addInsert('user_contact', $c->contact_id);
			$q->exec();
			$user_id = $db->Insert_ID();
			$this->user_id = $user_id;
			$q->clear();

			$acl =& $AppUI->acl();
			$acl->insertUserRole($acl->get_group_id('anon'), $this->user_id);
		}

	}


?>

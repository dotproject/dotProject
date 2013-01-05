<?php /* CONTACTS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision$
 */

require_once($AppUI->getSystemClass ('dp'));

/**
 * Contacts class
 */
class CContact extends CDpObject{
	var $contact_id = NULL;
	var $contact_first_name = NULL;
	var $contact_last_name = NULL;
	var $contact_order_by = NULL;
	var $contact_title = NULL;
	var $contact_job = NULL;
	var $contact_birthday = NULL;
	var $contact_company = NULL;
	var $contact_department = NULL;
	var $contact_type = NULL;
	var $contact_email = NULL;
	var $contact_email2 = NULL;
	var $contact_phone = NULL;
	var $contact_phone2 = NULL;
	var $contact_fax = NULL;
	var $contact_mobile = NULL;
	var $contact_address1 = NULL;
	var $contact_address2 = NULL;
	var $contact_city = NULL;
	var $contact_state = NULL;
	var $contact_zip = NULL;
	var $contact_url = NULL;
	var $contact_icq = NULL;
	var $contact_aol = NULL;
	var $contact_yahoo = NULL;
	var $contact_msn = NULL;
	var $contact_jabber = NULL;
	var $contact_notes = NULL;
	var $contact_project = NULL;
	var $contact_country = NULL;
	var $contact_icon = NULL;
	var $contact_owner = NULL;
	var $contact_private = NULL;
	
	public function __construct() {
		parent::__construct('contacts', 'contact_id');
	}
	
	function check() {
		if ($this->contact_id === NULL) {
			return 'contact id is NULL';
		}
		//ensure changes of state in checkboxes is captured
		$this->contact_private = intval($this->contact_private);
		$this->contact_owner = intval($this->contact_owner);
		return NULL; // object is ok
	}
	
	function canDelete(&$msg, $oid=null, $joins=null) {
		global $AppUI;
		if ($oid) {
			//Check to see if there is a user
			$q = new DBQuery;
			$q->addTable('users');
			$q->addQuery('count(*) as user_count');
			$q->addWhere('user_contact = ' . (int) $oid);
			$user_count = $q->loadResult();
			if ($user_count > 0) {
				$msg =  $AppUI->_('contactsDeleteUserError');
				return false;
			}
		}
		return parent::canDelete($msg, $oid, $joins);
	}
	
	function getCompanyName() {
		$q = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_name');
		$q->addWhere('company_id = ' . (int) $this->contact_company);
		return $q->loadResult();
 	}
	
	function getCompanyDetails() {
		$result = array('company_id' => 0, 'company_name' => '');
		if (! $this->contact_company) {
			return $result;
		}
		
		$q = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_id, company_name');
		$q->addWhere('company_id = ' . (int) $this->contact_company);
		return $q->loadHash();
	}
	
	function getDepartmentDetails() {
		$result = array('dept_id' => 0, 'dept_name' => '');
		if (!($this->contact_department)) {
			return $result;
		}
		
		$q  = new DBQuery;
		$q->addTable('departments');
		$q->addQuery('dept_id, dept_name');
		$q->addWhere('dept_id = ' . (int) $this->contact_department);
		return $q->loadHash();
	}
	
}
?>

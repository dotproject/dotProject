<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('tasks'));
require_once($AppUI->getModuleClass('projects'));
/**
* Link Class
*/
class CLink extends CDpObject {

	public $link_id = NULL;
	public $link_project = NULL;
	public $link_url = NULL;
	public $link_task = NULL;
	public $link_name = NULL;
	public $link_parent = NULL;
	public $link_description = NULL;
	public $link_owner = NULL;
	public $link_date = NULL;
	public $link_category = NULL;

	
	function __construct() {
		parent::__construct('links', 'link_id');
	}

	function check() {
	// ensure the integrity of some variables
		$this->link_id = intval($this->link_id);
		$this->link_parent = intval($this->link_parent);
                $this->link_category = intval($this->link_category);
		$this->link_task = intval($this->link_task);
		$this->link_project = intval($this->link_project);

		return NULL; // object is ok
	}

	function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
		global $dPconfig;
		$this->_message = "deleted";

	// delete the main table reference
		$q = new DBQuery();
		$q->setDelete('links');
		$q->addWhere('link_id = ' . $this->link_id);
		if (!$q->exec()) {
			return db_error();
		}
		return NULL;
	}
}

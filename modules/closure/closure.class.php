<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

require_once $AppUI->getSystemClass('dp');
require_once $AppUI->getSystemClass('query');

class CClosure extends CDpObject {
		var $pma_id = null;
    var $project_name = null;
		var $project_start_date = null;
	  var $project_end_date = null;
 		var $project_planned_start_date = null;
	  var $project_planned_end_date = null;
		var $project_meeting_date = null;
		var $planned_budget = 0;
		var $budget = 0;
    var $participants = null;
		var $project_strength = null;
		var $project_weaknesses = null;
		var $improvement_suggestions = null;
		var $conclusions = null;

  function CClosure() {
    parent::CDpObject('post_mortem_analysis', 'pma_id');
  }
  
 	function load($oid=null , $strip = true) {
		$result = parent::load($oid, $strip);
		if ($result && $oid) {
			$q = new DBQuery;
			$q->addTable('post_mortem_analysis', 'pma');
			$q->addQuery('project_name');
			$q->addJoin('projects', 'p1', 'p1.project_name = pma.project_name');
      if($oid != null)
              $q->addWhere(" project_id = $oid");
		}
		return $result;
	}
	
		function store() {
		$this->dPTrimAll();

		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed - $msg";
		}

		$details['project_name'] = $this->project_name;
		$details['pma_id'] = $this->pma_id;
		if( $this->pma_id ) {
			$ret = db_updateObject( 'post_mortem_analysis', $this, 'pma_id', true );
			$details['changes'] = $ret;
			addHistory('post_mortem_analysis', $this->pma_id, 'update', $details);
		}
		else {
			$ret = db_insertObject( 'post_mortem_analysis', $this, 'pma_id' );
			addHistory('post_mortem_analysis', $this->pma_id, 'add', $details);
		}

		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		}
    else {
			return NULL;
		}
	}
	
	function canDelete( &$msg, $oid=null ) {
		// TODO: check if user permissions are considered when deleting a project
		global $AppUI;
		$perms =& $AppUI->acl();

		return $perms->checkModuleItem('closure', 'delete', $oid);
	}

	function delete() {
		$this->load($this->project_id);
		$details['name'] = $this->project_name;
		addHistory('post_mortem_analysis', $this->project_name, 'delete', $details);

    $q = new DBQuery;
		$q->setDelete('post_mortem_analysis');
		$q->addWhere('pma_id ='.$this->pma_id);

    $result = ((!$q->exec())?db_error():NULL);
		$q->clear();
		return $result;
	}
	
}

?>

<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

// Copyright 2004, Adam Donnison <adam@saki.com.au>
// Released under GNU General Public License version 2 or later

require_once $AppUI->getSystemClass('dp');
require_once $AppUI->getSystemClass('query');


class CResource extends CDpObject {
  var $resource_id = null;
  var $resource_key = null;
  var $resource_name = null;
  var $resource_type = null;
  var $resource_max_allocation = null;
  var $resource_note = null;

  function CResource() {
    parent::CDpObject('resources', 'resource_id');
  }

  function &loadTypes() {
    // If we have loaded the resource types before then we don't need to
    // load them again.
    if (isset($_SESSION['resource_type_list'])) {
      $typelist =& $_SESSION['resource_type_list'];
    } else {
      $this->_query->clear();
      $this->_query->addTable('resource_types');
      $this->_query->addQuery('resource_type_id, resource_type_name');
      $this->_query->addOrder('resource_type_name');

      $res =& $this->_query->exec();
      $typelist = array();
      $typelist[0] = array (
	'resource_type_id' => 0,
	'resource_type_name' => 'All Resources'
     );
      while ($row = db_fetch_assoc($res)) {
	  $typelist[] = $row;
      }
      $this->_query->clear();
      $_SESSION['resource_type_list'] =& $typelist;
    }
    return $typelist;
  }

  function typeSelect()
  {
    $typelist =& $this->loadTypes();
    $result = array();
    foreach ($typelist as $type) {
      $result[$type['resource_type_id']] = $type['resource_type_name'];
    }
    return $result;
  }

  function getTypeName()
  {
    $result = "All Resources";
    $this->_query->clear();
    $this->_query->addTable('resource_types');
    $this->_query->addWhere('resource_type_id = ' . $this->resource_type);
    $res =& $this->_query->exec();
    if ($row = db_fetch_assoc($res)) {
      $result = $row['resource_type_name'];
    }
    $this->_query->clear();
    return $result;
  }
}


// vim600: sw=2 ts=8 ai fdm=marker:
?>

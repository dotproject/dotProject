<?php /* SYSKEYS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

include_once($AppUI->getSystemClass ('dp'));

##
## CSysKey Class
##

class CSysKey extends CDpObject {
	var $syskey_id = NULL;
	var $syskey_name = NULL;
	var $syskey_label = NULL;
	var $syskey_type = NULL;
	var $syskey_sep1 = NULL;
	var $syskey_sep2 = NULL;

	function CSysKey($name=null, $label=null, $type='0', $sep1="\n", $sep2 = '|') {
		$this->CDpObject('syskeys', 'syskey_id');
		$this->syskey_name = $name;
		$this->syskey_label = $label;
		$this->syskey_type = $type;
		$this->syskey_sep1 = $sep1;
		$this->syskey_sep2 = $sep2;
	}
}

##
## CSysVal Class
##

class CSysVal extends CDpObject {
	var $sysval_id = NULL;
	var $sysval_key_id = NULL;
	var $sysval_title = NULL;
	var $sysval_value = NULL;

	function check() {
		if ($this->sysval_key_id == 0)
			return 'Key Type cannot be empty';
		
		return null;
	}

	function CSysVal($key=null, $title=null, $value=null) {
		$this->CDpObject('sysvals', 'sysval_id');
		$this->sysval_key_id = $key;
		$this->sysval_title = $title;
		$this->sysval_value = $value;
	}
}

?>
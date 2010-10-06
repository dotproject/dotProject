<?php
// $Id$
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

// For the CustomField type FileLink the files class is needed
require_once ($AppUI->getModuleClass('files'));

/*
 *	CustomField Classes
 */

class CustomField
{
	var $field_id;
	// TODO - Implement Field Order - some people like to change the order of fields
	var $field_order;
	var $field_name;
	var $field_description;
	var $field_htmltype;
	// TODO - data type, meant for validation if you just want numeric data in a text input
	// but not yet implemented
	var $field_datatype;
	
	var $field_extratags;
	
	var $object_id = NULL;
	
	var $value_id = 0;
	
	var $value_charvalue;
	var $value_intvalue;
	
	function CustomField($field_id, $field_name, $field_order, $field_description, 
	                     $field_extratags) {
		$this->field_id = $field_id;
		$this->field_name = $field_name;
		$this->field_order = $field_order;
		$this->field_description = stripslashes($field_description);
		$this->field_extratags = stripslashes($field_extratags);
	}

	function load($object_id) {
		// Override Load Method for List type Classes
		GLOBAL $db;
		$q = new DBQuery;
		$q->addTable('custom_fields_values');
		$q->addWhere('value_field_id = ' . $this->field_id);
		$q->addWhere('value_object_id = ' . $object_id);
		$rs = $q->exec();
		$row = $q->fetchRow();
		$q->clear();
		
		$value_id = $row['value_id'];
		$value_charvalue = $row['value_charvalue'];
		$value_intvalue = $row['value_intvalue'];
		
		if ($value_id != NULL) {
			$this->value_id = $value_id;
			$this->value_charvalue = $value_charvalue;
			$this->value_intvalue = $value_intvalue;
		}
	}

	function store($object_id) {
		GLOBAL $db;
		if ($object_id == NULL) {
			return 'Error: Cannot store field ('.$this->field_name.'), associated id not supplied.';
		} else { 
			$ins_intvalue = (($this->value_intvalue == NULL) ? 0 : $this->value_intvalue);
			$ins_charvalue = (($this->value_charvalue == NULL) ? '' 
			                  : stripslashes($this->value_charvalue));
			
			$q = new DBQuery;
			$q->addTable('custom_fields_values');
			
			if ($this->value_id > 0) {
				$q->addUpdate('value_charvalue', $ins_charvalue);
				$q->addUpdate('value_intvalue', $ins_intvalue);
				$q->addWhere('value_id = '.$this->value_id);
			} else {
				$new_value_id = $db->GenID('custom_fields_values_id', 1);
				
				$q->addInsert('value_id', $new_value_id);
				$q->addInsert('value_module', '');
				$q->addInsert('value_field_id', $this->field_id);
				$q->addInsert('value_object_id', $object_id);
				$q->addInsert('value_charvalue', $ins_charvalue);
				$q->addInsert('value_intvalue', $ins_intvalue);
			}
			$rs = $q->exec();
            
			$q->clear();
			if (!$rs) { 
			  return $db->ErrorMsg() . ' | SQL: ';
			}
		}
	}
	
	function setIntValue($v) {	
		$this->value_intvalue = $v;
	}
	
	function intValue() {
		return $this->value_intvalue;
	}
	
	function setValue($v) {
		$this->value_charvalue = $v;
	}
	
	function charValue() {
		return $this->value_charvalue;
	}

	function charValueHTML() {
		return htmlspecialchars($this->value_charvalue);
	}
	
	function value() {
		return charValue();
	}
	
	function setValueId($v) {
		$this->value_id = $v;
	}
	
	function valueId() {
		return $this->value_id;
	}
	
	function fieldName() {
		return $this->field_name;
	}
	
	function fieldDescription() {
		return $this->field_description;
	}
		
	function fieldDescriptionHTML() {
		return htmlspecialchars($this->field_description);
	}
	
	function fieldId() {
		return $this->field_id;
	}
	
	function fieldHtmlType() {	
		return $this->field_htmltype;
	}
	
	function fieldExtraTags() {
		return $this->field_extratags;
	}
	
}

// CustomFieldCheckBox - Produces an INPUT Element of the CheckBox type in edit mode, view mode indicates 'Yes' or 'No'
class CustomFieldCheckBox extends CustomField
{
	function CustomFieldCheckBox($field_id, $field_name, $field_order, $field_description, 
	                             $field_extratags) {
		$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'checkbox';
	}
	
	function getHTML($mode) {
		$html = '<td nowrap="nowrap">';
		switch($mode) {
			case 'edit':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td><input type="checkbox" name="' 
				          . $this->field_name . '" value="1"' 
				          . (($this->intValue()) ? ' checked="checked" ' : ' ') 
				          . $this->field_extratags . ' />');
				break;
			case 'view':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td class="hilite" width="100%">' 
						  . (($this->intValue()) ? 'Yes' : 'No'));
				break;
		}
		$html .= '</td>';
		return $html;
	}
	
	function setValue($v) {
		$this->value_intvalue = $v;
	}
}

// CustomFieldText - Produces an INPUT Element of the TEXT type in edit mode
class CustomFieldText extends CustomField
{
	function CustomFieldText($field_id, $field_name, $field_order, $field_description, 
	                         $field_extratags) {
		$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'textinput';
	}
	
	function getHTML($mode) {
		$html = '<td nowrap="nowrap">';
		switch($mode) {
			case 'edit':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td><input type="text" name="' 
				          . $this->field_name . '" value="' . $this->charValueHTML() . '" ' 
				          . $this->field_extratags . ' />');
				break;
			case 'view':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td class="hilite" width="100%">' 
				          . $this->charValueHTML());
				break;
		}
		$html .= '</td>';
		return $html;
	}
}

// CustomFieldTextArea - Produces a TEXTAREA Element in edit mode
class CustomFieldTextArea extends CustomField
{
	function CustomFieldTextArea($field_id, $field_name, $field_order, $field_description, 
	                             $field_extratags) {
		$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'textarea';
	}
	
	function getHTML($mode) {
		$html = '<td nowrap="nowrap">';
		switch($mode) {
			case 'edit':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td><textarea name="' 
				          . $this->field_name . '" ' . $this->field_extratags . '>' 
				          . $this->charValueHTML() . '</textarea>');
				break;
			case 'view':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td class="hilite" width="100%">' 
						  . nl2br($this->charValueHTML()));
				break;
		}
		$html .= '</td>';
		return $html;
	}
}

// CustomFieldLabel - Produces just a non editable label
class CustomFieldLabel extends CustomField 
{
    function CustomFieldLabel($field_id, $field_name, $field_order, $field_description, 
	                          $field_extratags) {
    	$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'label';
    }
    
    function getHTML($mode) {
		// We don't really care about its mode
		$html = '<td nowrap="nowrap">';
		$html .= ('<span' . (($this->field_extratags) ? (' ' . $this->field_extratags) : '') . '>' 
		          . $this->fieldDescriptionHTML() . '</span>');
		
		$html .= '</td>';
		return $html;
    }
}

// CustomFieldSeparator - Produces just an horizontal line
class CustomFieldSeparator extends CustomField 
{
    function CustomFieldSeparator($field_id, $field_name, $field_order, $field_description, 
	                              $field_extratags) {
		$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'separator';
    }
    
    function getHTML($mode) {
    	// We don't really care about its mode
		$html = '<td nowrap="nowrap">';
    	$html .= '<hr' . (($this->field_extratags) ? (' ' . $this->field_extratags) : '') . ' />';
		$html .= '</td>';
		return $html;
    }
}

// CustomFieldSelect - Produces a SELECT list, extends the load method so that the option list can be loaded from a seperate table
class CustomFieldSelect extends CustomField
{
	var $options;
	
	function CustomFieldSelect($field_id, $field_name, $field_order, $field_description, 
	                           $field_extratags) {
		$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'select';
		$this->options = New CustomOptionList($field_id);		
		$this->options->load();
	}
	
	function getHTML($mode) {
		$html = '<td nowrap="nowrap">';
		switch($mode) {
			case 'edit':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td>' 
				          . $this->options->getHTML($this->field_name, $this->intValue()));
				break;
			case 'view':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td class="hilite" width="100%">' 
				          . htmlspecialchars($this->options->itemAtIndex($this->intValue())));
				break;
		}
		$html .= '</td>';
		return $html;
	}

	function setValue($v) {
		$this->value_intvalue = $v;
	}

	function value() {
		return $this->value_intvalue;
	}
}

/* CustomFieldWeblink
** Produces an INPUT Element of the TEXT type in edit mode 
** and a <a href> </a> weblink in display mode
*/

class CustomFieldWeblink extends CustomField
{
	function CustomFieldWeblink ($field_id, $field_name, $field_order, $field_description, 
	                             $field_extratags) {
		$this->CustomField($field_id, $field_name, $field_order, $field_description, 
		                   $field_extratags);
		$this->field_htmltype = 'href';
	}
	
	function getHTML($mode) {
		$html .= '<td nowrap="nowrap">';
		switch($mode) {
			case 'edit':
				$html .= ($this->fieldDescriptionHTML() . ': </td><td><input type="text" name="' 
				          . $this->field_name . '" value="' . $this->charValueHTML(). '" ' 
				          . $this->field_extratags . ' />');
				break;
			case 'view':
				$html .= ($this->fieldDescriptionHTML() 
				          . ': </td><td class="hilite" width="100%"><a href="' 
				          . $this->charValueHTML(). '">' . $this->charValueHTML(). '</a>');
				break;
		}
		$html .= '</td>';
		return $html;
	}
}

/* CustomFieldFilelink
** Produces a FILE Upload Element of the FILE type in edit mode 
** and a <a href> </a> weblink in display/view mode
**
** Make sure that the target form where the cf are included
** is of the following type:  
**
** enctype="multipart/form-data"
*/

class CustomFieldFilelink extends CustomField {

	function CustomFieldFilelink ($field_id, $field_name, $field_order, $field_description, $field_extratags)	{
		$this->CustomField($field_id, $field_name, $field_order, $field_description, $field_extratags);
		$this->field_htmltype = 'file';
	}

	function getHTML($mode)	{
		// load the file object 
		$cv = $this->charValue();
		if (!empty($cv) && $cv>0) {
			$obj = new CFile();
			$obj->load($cv);
		}

		$html = '<td nowrap="nowrap">';
		switch($mode) {
			case "edit":
				/* additionally add the hidden field	$this->field_name.'_id' 
				** to track the file id for file replacements/updates (cf. store() method).
				**
				** The <a href...> </a> link is needed since browsers do not support
				** prevalues in the <input type="file" .../> fields.
				*/
				$html .= $this->fieldDescriptionHTML().': </td><td>'.(($cv > 0) ? '<a href="./fileviewer.php?file_id='.$this->charValue().'">'.$obj->file_name.'</a>&nbsp;' : '') .'<input type="file" name="'.$this->field_name.'" '.$this->field_extratags.' /> <input type="hidden" name="'.$this->field_name.'_id" value="'.(!empty($cv) ? $cv : 0).'"/>';
				break;
			case "view":
				$html .= $this->fieldDescriptionHTML().': </td><td class="hilite" width="100%"><a href="./fileviewer.php?file_id='.$this->charValue().'">'.$obj->file_name.'</a>';
				break;
		}
		return $html;
	}

	/*
	** Extending the parent::store function
	** - in order to store files
	*/
	function store($object_id) {
		global $AppUI, $db, $_FILES, $m, $_POST;
						
		$file_uploaded = false;
		
		// instantiate the file object and eventually load exsiting file data
		$obj = new CFile();
		if ($_POST[$this->field_name.'_id']) {
			$obj->load($_POST[$this->field_name.'_id']);

			// create an old object for the case that
			// the file must be replaced
			if ($_POST[$this->field_name.'_id']>0) {
				$oldObj = new CFile();
				$oldObj->load($_POST[$this->field_name.'_id']);
			}
		}
		
		// if the cf lives in the projects module
		// affiliate the file to the suitable project
		if ($m == 'projects' && !empty($_POST['project_id'])) {
			$obj->file_project = $_POST['project_id'];
		} 
		// todo: implement task affiliation here, too

		$upload = null;
		if (isset($_FILES[$this->field_name])) {
			$upload = $_FILES[$this->field_name];

			if ($upload['size'] > 0) {
				// store file with a unique name
				$obj->file_name = $upload['name'];
				$obj->file_type = $upload['type'];
				$obj->file_size = $upload['size'];
				$obj->file_date = str_replace("'", '', $db->DBTimeStamp(time()));
				$obj->file_real_filename = uniqid(rand());
				$obj->file_owner = $AppUI->user_id;
				$obj->file_version++;
				$obj->file_version_id = $obj->file_id;
				
				$res = $obj->moveTemp($upload);
				if ($res) {
					$file_uploaded = true;
				}

				if (($msg = $obj->store())) {
					$AppUI->setMsg($msg, UI_MSG_ERROR);
				} else {
					// reset the cf field_name to the file_id
					$this->setValue($obj->file_id);
				}
			}
		}

		// Delete the existing (old) file in case of file replacement 
		// (through addedit not through c/o-versions)
		if (($_POST[$this->field_name.'_id']) && ($upload['size'] > 0) && $file_uploaded) {
			$oldObj->deleteFile();
		}

		if (($upload['size'] > 0) && $file_uploaded) {
			return parent::store($object_id);
		} else if (($upload['size'] > 1) && !$file_uploaded) {
			$AppUI->setMsg('File could not be stored!', UI_MSG_ERROR, true);
			return true;
		}
	}
}



// CustomFields class - loads all custom fields related to a module, produces a html table of all custom fields
// Also loads values automatically if the obj_id parameter is supplied. The obj_id parameter is the ID of the module object 
// eg. company_id for companies module
class CustomFields
{
	var $m;
	var $a;
	var $mode;
	var $obj_id;
	
	var $fields;
	
	function CustomFields($m, $a, $obj_id = NULL, $mode = 'edit') {
		$this->m = $m;
		$this->a = 'addedit'; // only addedit pages can carry the custom field for now
		$this->obj_id = $obj_id;
		$this->mode = $mode;
		
		// Get Custom Fields for this Module
		$q = new DBQuery;
		$q->addTable('custom_fields_struct');
		$q->addWhere("field_module = '" . $this->m . "' AND field_page = '" . $this->a . "'");
		//$q->addOrder('field_order DESC');
		$rows = $q->loadList();						
		if ($rows != NULL) {
			foreach ($rows as $row) {
				switch ($row['field_htmltype']) {
					case 'checkbox':
						$new_method = 'CustomFieldCheckbox';
						break;
					case 'file':
						$new_method = 'CustomFieldFilelink';
						break;
					case 'href':
						$new_method = 'CustomFieldWeblink';
						break;
					case 'textarea':
						$new_method = 'CustomFieldTextArea';
						break;
					case 'select':
						$new_method = 'CustomFieldSelect';
						break;
				    case 'label':
						$new_method = 'CustomFieldLabel';
				        break;
				    case 'separator':
						$new_method = 'CustomFieldSeparator';
				        break;    
					default:
						$new_method = 'CustomFieldText';
						break; 
				}
				$field_name = $row['field_name'];
				$this->fields[$field_name] = new $new_method($row['field_id'], $field_name, 
				                                        $row['field_order'], 
				                                        stripslashes($row['field_description']), 
				                                        stripslashes($row['field_extratags']));
			}

			if ($obj_id > 0) {
				//Load Values
				foreach ($this->fields as $key => $cfield) {
					$this->fields[$key]->load($this->obj_id);
				}
			}
		}
	}
	
	function add($field_name, $field_description, $field_htmltype, $field_datatype, 
	            $field_extratags, &$error_msg) {
		GLOBAL $db;
		$next_id = $db->GenID('custom_fields_struct_id', 1);
		
		$field_order = 1;
		$field_a = 'addedit';
		
		// TODO - module pages other than addedit
		// TODO - validation that field_name doesnt already exist
		$q = new DBQuery;
		$q->addTable('custom_fields_struct');
		$q->addInsert('field_id', $next_id);
		$q->addInsert('field_module', $this->m);
		$q->addInsert('field_page', $field_a);
		$q->addInsert('field_htmltype', $field_htmltype);
		$q->addInsert('field_datatype', $field_datatype);
		$q->addInsert('field_order', $field_order);
		$q->addInsert('field_name', $field_name);
		$q->addInsert('field_description', $field_description);
		$q->addInsert('field_extratags', $field_extratags);
		
		if (!$q->exec()) {
			$error_msg = $db->ErrorMsg();
			$q->clear();
			return 0;
		} else {
			$q->clear();
			return $next_id;
		}
	}
	
	function update($field_id, $field_name, $field_description, $field_htmltype, $field_datatype, 
	                $field_extratags, &$error_msg) {
		GLOBAL $db;
		
		$q = new DBQuery;
		$q->addTable('custom_fields_struct');
		$q->addUpdate('field_name', $field_name);
		$q->addUpdate('field_description', $field_description);
		$q->addUpdate('field_htmltype', $field_htmltype);
		$q->addUpdate('field_datatype', $field_datatype);
		$q->addUpdate('field_extratags', $field_extratags);
		$q->addWhere('field_id = '.$field_id);
		if (!$q->exec()) {
			$error_msg = $db->ErrorMsg();
			$q->clear();
			return 0;
		} else {
			$q->clear();
			return $field_id;
		}
	}
	
	function fieldWithId($field_id) {
		foreach ($this->fields as $k => $v) {
		 	if ($this->fields[$k]->field_id == $field_id) {
				return $this->fields[$k];
			}
		}
	}
	
	function bind(&$formvars) {
		if (!count($this->fields) == 0) {
			foreach ($this->fields as $k => $v) {
					$this->fields[$k]->setValue(@$formvars[$k]);
			}
		}
	}
	
	function store($object_id) {
		if (!count($this->fields) == 0) {
			$store_errors = '';
            foreach ($this->fields as $k => $cf) 	{
				$result = $this->fields[$k]->store($object_id);
				if ($result) {
					$store_errors .= 'Error storing custom field ' . $k . ':' . $result;
				}
			}
			
			if ($store_errors) { 
				echo $store_errors;
			}
		}
	}
	
	function deleteField($field_id) {
		GLOBAL $db;
		$q = new DBQuery;
		$q->setDelete('custom_fields_struct');
		$q->addWhere('field_id = ' . $field_id);
		if (!$q->exec()) {
			$q->clear();
			return $db->ErrorMsg();
		}
	}
	
	function count() {
		return count($this->fields);
	}
	
	function getHTML() {
		if ($this->count() == 0) {
			return '';
		} else {
			$html = '<table width="100%">' . "\n";
			
			foreach ($this->fields as $cfield) {
				$html .= ("\t" . '<tr>' . $cfield->getHTML($this->mode) . "</tr>\n");
			}
			$html .= "</table>\n";
			
			return $html;
		}
	}
	
	function printHTML() {
		$html = $this->getHTML();
		echo $html;
	}
	
	function search($moduleTable, $moduleTableId, $moduleTableName, $keyword) {
		$q = new DBQuery;
		$q->addTable('custom_fields_values', 'cfv');
		$q->addQuery('m.' . $moduleTableId);
		$q->addQuery('m.' . $moduleTableName);
		$q->addQuery('cfv.value_charvalue');
		$q->addJoin('custom_fields_struct', 'cfs', 'cfs.field_id = cfv.value_field_id');
		$q->addJoin($moduleTable, 'm', 'm.' . $moduleTableId . ' = cfv.value_object_id');
		$q->addWhere('cfs.field_module = "' . $this->m . '"');
		$q->addWhere('cfv.value_charvalue LIKE "%' . $keyword . '%"');
		return $q->loadList();
	}
}

class CustomOptionList
{
	var $field_id;
	var $options;
	
	function CustomOptionList($field_id) {
		$this->field_id = $field_id;
		$this->options = array();
	}
	
	function load() {
		GLOBAL $db;
		
		$q = new DBQuery;
		$q->addTable('custom_fields_lists');
		$q->addWhere('field_id = ' . $this->field_id);
		$q->addOrder('list_value');
		if (!$rs = $q->exec()) {
			$q->clear();
			return $db->ErrorMsg();
		}
		
		$this->options = Array();
		while ($opt_row = $q->fetchRow()) {
			$this->options[$opt_row['list_option_id']] = $opt_row['list_value'];
		}
		
		$q->clear();
	}
	
	function store() {
		GLOBAL $db;
		
		if (! is_array($this->options)) {
			$this->options = array();
		}
		
		//load the dbs options and compare them with the options
		$q = new DBQuery;
		$q->addTable('custom_fields_lists');
		$q->addWhere('field_id = ' . $this->field_id);
		$q->addOrder('list_value');
		if (!$rs = $q->exec()) {
			$q->clear();
		  	return $db->ErrorMsg();		
		}
		
		$dboptions = Array();
		while ($opt_row = $q->fetchRow()) {
			$dboptions[$opt_row['list_option_id']] = $opt_row['list_value'];
		}
		$q->clear();
		
		$newoptions = Array();
		$newoptions = array_diff($this->options, $dboptions);
		$deleteoptions = array_diff($dboptions, $this->options);
		//insert the new options
		foreach ($newoptions as $opt) {
			$optid = $db->GenID('custom_fields_option_id', 1);
			
			$q = new DBQuery;
			$q->addTable('custom_fields_lists');
			$q->addInsert('field_id', $this->field_id);
			$q->addInsert('list_option_id', $optid);
			$q->addInsert('list_value', db_escape($opt));
			
			if (!$q->exec()) {
				$insert_error = $db->ErrorMsg();
			}
			$q->clear();
		}
		//delete the deleted options
		foreach ($deleteoptions as $opt => $value) {
			$q = new DBQuery;
			$q->setDelete('custom_fields_lists');
			$q->addWhere('list_option_id = ' . $opt);
			
			if (!$q->exec()) {
				$delete_error = $db->ErrorMsg();
			}
			$q->clear();
		}
		
		return $insert_error.' '.$delete_error;
	}
	
	function delete() {
		$q = new DBQuery;
		$q->setDelete('custom_fields_lists');
		$q->addWhere('field_id = ' . $this->field_id);
		$q->exec();
		$q->clear();
	}
	
	function setOptions($option_array) {
		$this->options = $option_array;
	} 
	
	function getOptions() {
		return $this->options;
	}
	
	function itemAtIndex($i) {
		return $this->options[$i];
	}
	
	function getHTML($field_name, $selected) {
		$html = '<select name="' . $field_name . '">' . "\n";
		foreach ($this->options as $i => $opt) {
		 	$html .= ("\t" . '<option value=' . $i . '"' 
			          . (($i == $selected) ?' selected="selected"' : '') . '>' 
			          . htmlspecialchars($opt) . '</option>');
		}	
		$html .= "</select>\n";
		return $html;
	}
}
?>

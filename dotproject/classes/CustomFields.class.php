<?php
	// $Id$
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

		function CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags )
		{
			$this->field_id = $field_id;
			$this->field_name = $field_name;
			$this->field_order = $field_order;
			$this->field_description = $field_description;
			$this->field_extratags = $field_extratags;
		}

		function load( $object_id )
		{
			// Override Load Method for List type Classes
			GLOBAL $db;
			$q  = new DBQuery;
			$q->addTable('custom_fields_values');
			$q->addWhere("value_field_id = ".$this->field_id);
			$q->addWhere("value_object_id = ".$object_id);
			$rs = $q->exec();
			$row = $q->fetchRow();
			$q->clear();

			$value_id = $row["value_id"];
			$value_charvalue = $row["value_charvalue"];
			$value_intvalue = $row["value_intvalue"];

			if ($value_id != NULL)
			{
				$this->value_id = $value_id;
				$this->value_charvalue = $value_charvalue;
				$this->value_intvalue = $value_intvalue;
			}
		}

		function store( $object_id )
		{
			GLOBAL $db;
			if ($object_id == NULL)
			{
				return 'Error: Cannot store field ('.$this->field_name.'), associated id not supplied.';
			}
			else
			{ 
				$ins_intvalue = $this->value_intvalue == NULL ? 'NULL' : $this->value_intvalue;

				if ($this->value_id > 0)
				{

						$q  = new DBQuery;
						$q->addTable('custom_fields_values');
						$q->addUpdate('value_charvalue', $this->value_charvalue );
						$q->addUpdate('value_intvalue', $ins_intvalue);
						$q->addWhere("value_id = ".$this->value_id);
				}
				else
				{
						$new_value_id = $db->GenID('custom_fields_values_id', 1 );
		
						$q  = new DBQuery;
						$q->addTable('custom_fields_values');
						$q->addInsert('value_id', $new_value_id);
						$q->addInsert('value_module', '');
						$q->addInsert('value_field_id', $this->field_id);
						$q->addInsert('value_object_id', $object_id);

						$q->addInsert('value_charvalue', $this->value_charvalue );
						$q->addInsert('value_intvalue', $ins_intvalue);
				}
//				if ($sql != NULL) $rs = $q->exec();
                // No $sql var defined
                $rs = $q->exec();
                
				$q->clear();
				if (!$rs) return $db->ErrorMsg()." | SQL: ";
			}
		}

		function setIntValue( $v )
		{	
			$this->value_intvalue = $v;
		}

		function intValue()
		{
			return $this->value_intvalue;
		}

		function setValue( $v )
		{
			$this->value_charvalue = $v;
		}

		function value()
		{
			return $this->value_charvalue;
		}

		function charValue()
		{
			return $this->value_charvalue;
		}

		function setValueId( $v )
		{
			$this->value_id = $v;
		}

		function valueId()
		{
			return $this->value_id;
		}

		function fieldName()
		{
			return $this->field_name;
		}

		function fieldDescription()
		{
			return $this->field_description;
		}
			
		function fieldId()
		{
			return $this->field_id;
		}

		function fieldHtmlType()
		{	
			return $this->field_htmltype;
		}

		function fieldExtraTags()
		{
			return $this->field_extratags;
		}

	}

	// CustomFieldCheckBox - Produces an INPUT Element of the CheckBox type in edit mode, view mode indicates 'Yes' or 'No'
	class CustomFieldCheckBox extends CustomField
	{
		function CustomFieldCheckBox( $field_id, $field_name, $field_order, $field_description, $field_extratags )
		{
			$this->CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags );
			$this->field_htmltype = 'checkbox';
		}

		function getHTML($mode)
		{
			switch($mode)
			{
				case "edit":
					$bool_tag = ($this->intValue()) ? "checked " : "";
					$html = $this->field_description.": </td><td><input type=\"checkbox\" name=\"".$this->field_name."\" value=\"1\" ".$bool_tag.$this->field_extratags."/>";
					break;
				case "view":
					$bool_text = ($this->intValue()) ? "Yes" : "No";
					$html = $this->field_description.": </td><td class=\"hilite\" width=\"100%\">".$bool_text;
					break;
			}
			return $html;
		}

		function setValue( $v )
		{
			$this->value_intvalue = $v;
		}
	}
	
	// CustomFieldText - Produces an INPUT Element of the TEXT type in edit mode
	class CustomFieldText extends CustomField
	{
		function CustomFieldText( $field_id, $field_name, $field_order, $field_description, $field_extratags )
		{
			$this->CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags );
			$this->field_htmltype = 'textinput';
		}

		function getHTML($mode)
		{
			switch($mode)
			{
				case "edit":
					$html = $this->field_description.": </td><td><input type=\"text\" name=\"".$this->field_name."\" value=\"".$this->charValue()."\" ".$this->field_extratags." />";
					break;
				case "view":
					$html = $this->field_description.": </td><td class=\"hilite\" width=\"100%\">".$this->charValue();
					break;
			}
			return $html;
		}
	}

	// CustomFieldTextArea - Produces a TEXTAREA Element in edit mode
	class CustomFieldTextArea extends CustomField
	{
		function CustomFieldTextArea( $field_id, $field_name, $field_order, $field_description, $field_extratags )
		{
			$this->CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags );
			$this->field_htmltype = 'textarea';
		}

		function getHTML($mode)
		{
			switch($mode)
			{
				case "edit":
					$html = $this->field_description.": </td><td><textarea name=\"".$this->field_name."\" ".$this->field_extratags.">".$this->charValue()."</textarea>";
					break;
				case "view":
					$html = $this->field_description.": </td><td class=\"hilite\" width=\"100%\">".nl2br($this->charValue());
					break;
			}
			return $html;
		}
	}
	
	// CustomFieldLabel - Produces just a non editable label
	class CustomFieldLabel extends CustomField 
	{
	    function CustomFieldLabel($field_id, $field_name, $field_order, $field_description, $field_extratags) {
    	    $this->CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags );
            $this->field_htmltype = 'label';
	    }
	    
	    function getHTML($mode) {
	    	// We don't really care about its mode
	    	return "<span $this->field_extratags>$this->field_description</span>";
	    }
	}
	
	// CustomFieldSeparator - Produces just an horizontal line
	class CustomFieldSeparator extends CustomField 
	{
	    function CustomFieldSeparator($field_id, $field_name, $field_order, $field_description, $field_extratags) {
    	    $this->CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags );
            $this->field_htmltype = 'separator';
	    }
	    
	    function getHTML($mode) {
	    	// We don't really care about its mode
	    	return "<hr $this->field_extratags />";
	    }
	}

	// CustomFieldSelect - Produces a SELECT list, extends the load method so that the option list can be loaded from a seperate table
	class CustomFieldSelect extends CustomField
	{
		var $options;

		function CustomFieldSelect( $field_id, $field_name, $field_order, $field_description, $field_extratags )
		{
			$this->CustomField( $field_id, $field_name, $field_order, $field_description, $field_extratags );
			$this->field_htmltype = 'select';
			$this->options = New CustomOptionList( $field_id );		
			$this->options->load();
		}

		function getHTML($mode)
		{
			switch($mode)
			{
				case "edit":
					$html = $this->field_description.": </td><td>";
					$html.= $this->options->getHTML( $this->field_name, $this->intValue() );
					break;
				case "view":
					$html = $this->field_description.": </td><td class=\"hilite\" width=\"100%\">".$this->options->itemAtIndex($this->intValue());
					break;
			}
			return $html;
		}

		function setValue( $v )
		{
			$this->value_intvalue = $v;
		}

		function value()
		{
			return $this->value_intvalue;
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

		function CustomFields($m, $a, $obj_id = NULL, $mode = "edit")
		{
			$this->m = $m;
			$this->a = 'addedit'; // only addedit pages can carry the custom field for now
			$this->obj_id = $obj_id;
			$this->mode = $mode;

			// Get Custom Fields for this Module
			$q  = new DBQuery;
			$q->addTable('custom_fields_struct');
			$q->addWhere("field_module = '".$this->m."' AND	field_page = '".$this->a."'");
			$q->addOrder('field_order ASC');
			$rows = $q->loadList();						
			if ($rows == NULL)
			{
				// No Custom Fields Available
			}
			else
			{
				foreach($rows as $row)
				{
					switch ($row["field_htmltype"])
					{
						case "checkbox":
							$this->fields[$row["field_name"]] = New CustomFieldCheckbox( $row["field_id"], $row["field_name"], $row["field_order"], stripslashes($row["field_description"]), stripslashes($row["field_extratags"]) );
							break;
						case "textarea":
							$this->fields[$row["field_name"]] = New CustomFieldTextArea( $row["field_id"], $row["field_name"], $row["field_order"], stripslashes($row["field_description"]), stripslashes($row["field_extratags"]) );
							break;
						case "select":
							$this->fields[$row["field_name"]] = New CustomFieldSelect( $row["field_id"], $row["field_name"], $row["field_order"], stripslashes($row["field_description"]), stripslashes($row["field_extratags"]) );
							break;
					    case "label":
					        $this->fields[$row["field_name"]] = new CustomFieldLabel( $row["field_id"], $row["field_name"], $row["field_order"], stripslashes($row["field_description"]), stripslashes($row["field_extratags"]) );
					        break;
					    case "separator":
					        $this->fields[$row["field_name"]] = new CustomFieldSeparator( $row["field_id"], $row["field_name"], $row["field_order"], stripslashes($row["field_description"]), stripslashes($row["field_extratags"]) );
					        break;    
						default:
							$this->fields[$row["field_name"]] = New CustomFieldText( $row["field_id"], $row["field_name"], $row["field_order"], stripslashes($row["field_description"]), stripslashes($row["field_extratags"]) );
							break; 
					}
				
				}
	
				if ($obj_id > 0)
				{
					//Load Values
					foreach ($this->fields as $key => $cfield)
					{
						$this->fields[$key]->load( $this->obj_id );
					}
				}
			}
		

		}

		function add( $field_name, $field_description, $field_htmltype, $field_datatype, $field_extratags, &$error_msg )
		{
			GLOBAL $db;
			$next_id = $db->GenID( 'custom_fields_struct_id', 1 );

			$field_order = 1;
			$field_a = 'addedit';

			// TODO - module pages other than addedit
			// TODO - validation that field_name doesnt already exist
			$q  = new DBQuery;
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


			if (!$q->exec())
			{
				//return "<pre>".$sql."</pre>";
				$error_msg = $db->ErrorMsg();
				$q->clear();
				return 0;
			}
			else
			{
				$q->clear();
				return $next_id;
			}
		} 

		function update( $field_id, $field_name, $field_description, $field_htmltype, $field_datatype, $field_extratags, &$error_msg )
		{
			GLOBAL $db;
			
			$q  = new DBQuery;
			$q->addTable('custom_fields_struct');
			$q->addUpdate('field_name', $field_name);
			$q->addUpdate('field_description', $field_description);
			$q->addUpdate('field_htmltype', $field_htmltype);
			$q->addUpdate('field_datatype', $field_datatype);
			$q->addUpdate('field_extratags', $field_extratags);
			$q->addWhere("field_id = ".$field_id);
			if (!$q->exec())
			{
				$error_msg = $db->ErrorMsg();
				$q->clear();
				return 0;
			}
			else
			{
				$q->clear();
				return $field_id;
			}
		}

		function fieldWithId( $field_id )
		{
			foreach ($this->fields as $k => $v)
			{
				if ($this->fields[$k]->field_id == $field_id) 
					return $this->fields[$k];	
			}
		}

		function bind( &$formvars )
		{
			if (!count($this->fields) == 0)
			{
				foreach ($this->fields as $k => $v)
				{
					if ($formvars[$k] != NULL)
					{
						$this->fields[$k]->setValue($formvars[$k]);
					}
				}
			}
		}

		function store( $object_id )
		{
			if (!count($this->fields) == 0)
			{
				foreach ($this->fields as $k => $cf)
				{
					$result = $this->fields[$k]->store( $object_id );
					if ($result)
					{
						$store_errors .= "Error storing custom field ".$k.":".$result;
					}
				}

				//if ($store_errors) return $store_errors;
				if ($store_errors) echo $store_errors;
			}
		}

		function deleteField( $field_id )
		{
			GLOBAL $db;
			$q  = new DBQuery;
			$q->setDelete('custom_fields_struct');
			$q->addWhere("field_id = $field_id");
			if (!$q->exec())
			{
				$q->clear();
				return $db->ErrorMsg();
			}	
		}

		function count()
		{
			return count($this->fields);
		}

		function getHTML()
		{
			if ($this->count() == 0)
			{
				return "";
			}
			else
			{
				$html = "<table width=\"100%\">\n";
	
				foreach ($this->fields as $cfield)
				{
					$html .= "\t<tr><td nowrap=\"nowrap\">".$cfield->getHTML($this->mode)."</td></tr>\n";
				}
				$html .= "</table>\n";

				return $html;
			}
		}


		function printHTML()
		{
			$html = $this->getHTML();
			echo $html;
		}
		
	}

	class CustomOptionList
	{
		var $field_id;
		var $options;

		function CustomOptionList( $field_id )
		{
			$this->field_id = $field_id;
			$this->options = array();
		}

		function load()
		{
			GLOBAL $db;
	
			$q  = new DBQuery;
			$q->addTable('custom_fields_lists');
			$q->addWhere("field_id = {$this->field_id}");
			if (!$rs = $q->exec()) {
				$q->clear();
			  return $db->ErrorMsg();		
			}

			$this->options = Array();

			while ($opt_row = $q->fetchRow())
			{
				$this->options[] = $opt_row["list_value"];
			}
			$q->clear();
		}

		function store()
		{
			GLOBAL $db;

			if (! is_array($this->options))
				$this->options = array();

			foreach($this->options as $opt)
			{
				$optid = $db->GenID('custom_fields_option_id', 1 );

				$q  = new DBQuery;
				$q->addTable('custom_fields_lists');
				$q->addInsert('field_id', $this->field_id);
				$q->addInsert('list_option_id', $optid);
				$q->addInsert('list_value', db_escape(strip_tags($opt)));

				if (!$q->exec()) $insert_error = $db->ErrorMsg();  	
				$q->clear();
			}	

			return $insert_error;
		}

		function delete()
		{
			$q  = new DBQuery;
			$q->setDelete('custom_fields_lists');
			$q->addWhere("field_id = {$this->field_id}");
			$q->exec();
			$q->clear();
		}

		function setOptions( $option_array )
		{
			$this->options = $option_array;
		} 

		function getOptions()
		{
			return $this->options;
		}

		function itemAtIndex( $i )
		{
			return $this->options[$i];
		}

		function getHTML( $field_name, $selected )
		{
			$html = "<select name=\"".$field_name."\">\n";
			foreach ($this->options as $i => $opt)
			{
				$html .= "\t<option value=\"".$i."\"";
				if ($i == $selected) $html .= " selected ";
				$html .= ">".$opt."</option>";
			}	
			$html .= "</select>\n";
			return $html;
		}		
	}
?>

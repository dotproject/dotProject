<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

	/*
	 *	do_custom_field_aed.php
	 *
	 */
	require_once("./classes/CustomFields.class.php");

	$edit_field_id = dpGetParam( $_POST, "field_id", NULL );

	if ($edit_field_id != NULL)
	{
		$edit_module = dpGetParam( $_POST, "module", NULL );
		$field_name = dpGetParam( $_POST, "field_name", NULL );
		$field_description = db_escape(strip_tags(dpGetParam( $_POST, "field_description", NULL )));
		$field_htmltype = dpGetParam( $_POST, "field_htmltype", NULL );
		$field_datatype = dpGetParam( $_POST, "field_datatype", "alpha" );
		$field_extratags = db_escape(dpGetParam( $_POST, "field_extratags", NULL ));

		$list_select_items = dpGetParam( $_POST, "select_items", NULL );

		$custom_fields = New CustomFields( strtolower($edit_module), 'addedit', null, null );


		if ($edit_field_id == 0)
		{
			$fid = $custom_fields->add( $field_name, $field_description, $field_htmltype, $field_datatype, $field_extratags, $msg );
		}
		else
		{
			$fid = $custom_fields->update( $edit_field_id, $field_name, $field_description, $field_htmltype, $field_datatype, $field_extratags, $msg );
		}
	
		// Add or Update a Custom Field
		if ($msg)
		{
			$AppUI->setMsg($AppUI->_('Error adding custom field:').$msg, UI_MSG_ALERT, true);
		}
		else
		{
			if ($field_htmltype == "select")
			{
				$opts = New CustomOptionList( $fid );
				$opts->setOptions( $list_select_items );

				if ($edit_field_id == 0)
				{
					$o_msg = $opts->store();
				}
				else
				{
					// To update each list would be a lot more complex than rewriting it
					// So it is, but it is needed in order for it to work properly. (Pedro A. Bug 1163)
					$o_msg = $opts->store();
				}

				if ($o_msg)
				{
					// Select List Failed - Delete CustomField also
				}
	
			}	

			$AppUI->setMsg($AppUI->_('Custom field added successfully'), UI_MSG_OK, true);
		}
	}
?>

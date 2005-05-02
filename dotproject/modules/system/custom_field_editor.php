<?php
	/*
	 *	Custom Field Editor (NEW)
	 *
	 */

	$AppUI->savePlace();

	require_once("./classes/CustomFields.class.php");

	$titleBlock = new CTitleBlock('Custom field editor', "customfields.png", "admin", "admin.custom_field_editor");
	$titleBlock->addCrumb( "?m=system", "system admin" );

	$edit_field_id = dpGetParam( $_POST, "field_id", NULL );

	$titleBlock->show();

	$sql = "SELECT * FROM modules WHERE mod_name IN ('Companies', 'Projects', 'Tasks', 'Calendar') ORDER BY mod_ui_order";
	$modules = db_loadList( $sql );

	echo "<table cellpadding=\"2\">";

	foreach ($modules as $module)
	{
		echo "<tr><td colspan=\"4\">";
		echo "<h3>".$AppUI->_($module["mod_name"])."</h3>";
		echo "</td></tr>";

		echo "<tr><td colspan=\"4\">";
		echo "<a href=\"?m=system&a=custom_field_addedit&module=".$module["mod_name"]."\"><img src='./images/icons/stock_new.png' align='center' width='16' height='16' border='0'>".$AppUI->_('Add a new Custom Field to this Module')."</a><br /><br />";
		echo "</td></tr>";

		$sql = "SELECT * FROM custom_fields_struct WHERE field_module = '".strtolower($module["mod_name"])."'";
		$custom_fields = db_loadList( $sql );

		foreach ($custom_fields as $f)
		{
			echo "<tr><td class=\"hilite\">";
			echo "<a href=\"?m=system&a=custom_field_addedit&module=".$module["mod_name"]."&field_id=".$f["field_id"]."\"><img src='./images/icons/stock_edit-16.png' align='center' width='16' height='16' border='0'>Edit</a>";
			echo "</td><td class=\"hilite\">";
			echo "<a href=\"?m=system&a=custom_field_addedit&field_id=".$f["field_id"]."&delete=1\"><img src='./images/icons/stock_delete-16.png' align='center' width='16' height='16' border='0'>Delete</a> ";
			echo "</td><td class=\"hilite\">";
			echo stripslashes($f["field_description"])."\n";
			echo "</td></tr>";
		}
	}
?>
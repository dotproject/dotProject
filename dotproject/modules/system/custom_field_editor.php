<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$AppUI->savePlace();

require_once($AppUI->getSystemClass('CustomFields'));

$titleBlock = new CTitleBlock('Custom field editor', 'customfields.png', 'admin', 
                              'admin.custom_field_editor');
$titleBlock->addCrumb('?m=system', 'system admin');
$edit_field_id = dpGetParam($_POST, 'field_id', NULL);

$titleBlock->show();

$sql = ('SELECT * FROM modules' 
        . ' ORDER BY mod_ui_order');
$q = new DBQuery;
$q->addTable('modules');
$q->addWhere('mod_name IN (\'Companies\', \'Projects\', \'Tasks\', \'Calendar\')');
$modules = $q->loadList();

echo '<table cellpadding="2" summary="module list">';

foreach ($modules as $module) {
	echo '<tr><td colspan="4">';
	echo '<h3>'.$AppUI->_($module['mod_name']).'</h3>';
	echo '</td></tr>';
	
	echo '<tr><td colspan="4">';
	echo ('<a href="?m=system&amp;a=custom_field_addedit&amp;module=' . $module['mod_name'] 
		  . '"><img src="./images/icons/stock_new.png" align="center" width="16" height="16" border="0" alt="" />' 
		  . $AppUI->_('Add a new Custom Field to this Module') . '</a><br /><br />');
	echo '</td></tr>';
	
	$q->clear();
	$q->addTable('custom_fields_struct');
	$q->addWhere('field_module = \''.mb_strtolower($module['mod_name'])."'");
	$custom_fields = $q->loadList();
	
	foreach ($custom_fields as $f) {
		echo '<tr><td class="hilite">';
		echo ('<a href="?m=system&amp;a=custom_field_addedit&amp;module=' . $module['mod_name'] 
		      . '&amp;field_id=' . $f['field_id'] 
			  . '"><img src="./images/icons/stock_edit-16.png" align="center" width="16" height="16" border="0" alt="" />Edit</a>');
		echo '</td><td class="hilite">';
		echo ('<a href="?m=system&amp;a=custom_field_addedit&amp;field_id=' . $f['field_id'] 
			  . '&amp;delete=1"><img src="./images/icons/stock_delete-16.png" align="center" width="16" height="16" border="0" alt="" />Delete</a>');
		echo '</td><td class="hilite">';
		echo htmlspecialchars($f['field_description']) . "\n";
		echo '</td></tr>';
	}
}
?>

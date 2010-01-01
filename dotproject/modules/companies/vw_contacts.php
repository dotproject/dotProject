<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

##
##	Companies: View User sub-table
##

global $AppUI, $company_id, $obj;

require_once $AppUI->getModuleClass('contacts');

$q = new DBQuery;
$q->addTable('contacts');
$q->addWhere("contact_company = '" . addslashes($obj->company_name) 
             . "' OR contact_company = '" . $obj->company_id . "'");
$q->addOrder('contact_last_name'); 

$s = '';
if (!($rows = $q->loadList())) {
	echo $AppUI->_('No data available') . '<br />' . $AppUI->getMsg();
} else {
?>
<table width="100%" border=0 cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th><?php echo $AppUI->_('Name');?></td>
	<th><?php echo $AppUI->_('e-mail');?></td>
	<th><?php echo $AppUI->_('Department');?></td>
</tr>
<?php
	foreach ($rows as $row) {
		$contact =& new CContact;
		$contact->bind($row);
		$dept_detail = $contact->getDepartmentDetails();
		
		$s .= '<tr><td>';
		$s .= ('<a href="./index.php?m=contacts&a=view&contact_id=' 
		       . dPformSafe($row['contact_id']) . '">' 
		       . htmlspecialchars($row['contact_last_name'] . ', ' . $row['contact_first_name']) 
		       .'</a>');
		$s .= ('<td><a href="mailto:' . dPformSafe($row['contact_email'], DP_FORM_URI) . '">' 
		       . htmlspecialchars($row['contact_email']) . '</a></td>');
		$s .= '<td>' . htmlspecialchars($dept_detail['dept_name']) . '</td>';
		$s .= '</tr>';
	}
}

$s .= '<tr><td colspan="3" align="right" valign="top" style="background-color:#ffffff">';
$s .= ('<input type="button" class=button value="' . $AppUI->_('new contact') 
	   . '" onClick="javascript:window.location=\'./index.php?m=contacts&a=addedit&company_id=' 
	   . dPformSafe($company_id) . '&company_name=' . dPformSafe($obj->company_name) . '\'">');
$s .= '</td></tr>';
echo $s;
	
?>
</table>
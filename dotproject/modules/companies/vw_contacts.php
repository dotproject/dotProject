<?php /* COMPANIES $Id$ */
##
##	Companies: View User sub-table
##
GLOBAL $AppUI, $company_id, $obj;

require_once $AppUI->getModuleClass('contacts');
$q  = new DBQuery;
$q->addTable('contacts');
$q->addWhere("contact_company = '$obj->company_name' OR contact_company = '$obj->company_id'");
$q->addOrder('contact_last_name'); 
$s = '';
if (!($rows = $q->loadList())) {
	echo $AppUI->_('No data available').'<br />'.$AppUI->getMsg();
} else {
?>
<table width="100%" border=0 cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th><?php echo $AppUI->_( 'Name' );?></td>
	<th><?php echo $AppUI->_( 'e-mail' );?></td>
	<th><?php echo $AppUI->_( 'Department' );?></td>
</tr>
<?php
	foreach ($rows as $row){
		$contact =& new CContact;
		$contact->bind($row);
		$dept_detail = $contact->getDepartmentDetails();

		$s .= '<tr><td>';
		$s .= '<a href="./index.php?m=contacts&a=addedit&contact_id='.$row["contact_id"].'">'. $row["contact_last_name"].", ".$row["contact_first_name"] .'</a>';
		$s .= '<td><a href="mailto:'.$row["contact_email"] .'">' .$row["contact_email"] .'</a></td>';
		$s .= '<td>'.$dept_detail['dept_name'] .'</td>';
		$s .= '</tr>';
	}
}

	$s .= '<tr><td colspan="3" align="right" valign="top" style="background-color:#ffffff">';
	$s .= '<input type="button" class=button value="'.$AppUI->_( 'new contact' ).'" onClick="javascript:window.location=\'./index.php?m=contacts&a=addedit&company_id='.$company_id.'&company_name='.$obj->company_name.'\'">';
	$s .= '</td></tr>';
	echo $s;
	
?>
</table>
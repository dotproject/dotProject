<?php /* DEPARTMENTS $Id$ */
// Add / Edit Company
$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : 0;
$company_id = isset($_GET['company_id']) ? $_GET['company_id'] : 0;

// check permissions for this department
$canEdit = !getDenyEdit( $m, $dept_id );
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// pull data for this department
$q  = new DBQuery;
$q->addTable('departments','dep');
$q->addQuery('dep.*, company_name');
$q->addJoin('companies', 'com', 'com.company_id = dep.dept_company');
$q->addWhere('dep.dept_id = '.$dept_id);
$sql = $q->prepare();
$q->clear();
if (!db_loadHash( $sql, $drow ) && $dept_id > 0) {
	$titleBlock = new CTitleBlock( 'Invalid Department ID', 'users.gif', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=companies", "companies list" );
	if ($company_id) {
		$titleBlock->addCrumb( "?m=companies&a=view&company_id=$company_id", "view this company" );
	}
	$titleBlock->show();
} else {
	##echo $sql.db_error();##
	$company_id = $dept_id ? $drow['dept_company'] : $company_id;

	// check if valid company
	$q  = new DBQuery;
	$q->addTable('companies','com');
	$q->addQuery('company_name');
	$q->addWhere('com.company_id = '.$company_id);
	$sql = $q->prepare();
	$q->clear();
	$company_name = db_loadResult( $sql );
	if (!$dept_id && $company_name === null) {
		$AppUI->setMsg( 'badCompany', UI_MSG_ERROR );
		$AppUI->redirect();
	}

	// collect all the departments in the company
	$depts = array( 0 => '' );
	if ($company_id) {
		$q  = new DBQuery;
		$q->addTable('departments','dep');
		$q->addQuery('dept_id, dept_name, dept_parent');
		$q->addWhere('dep.dept_company = '.$company_id);
		$q->addWhere('dep.dept_id != '.$dept_id);
		$depts = $q->loadArrayList();
		$depts['0']  = array( 0, '- '.$AppUI->_('Select Unit').' -', -1 );
	}

	// collect all the users for the department owner list
	$q  = new DBQuery;
	$q->addTable('users','u');
	$q->addTable('contacts','con');
	$q->addQuery('user_id');
	$q->addQuery('CONCAT_WS(", ",contact_last_name, contact_first_name)'); 
	$q->addOrder('contact_first_name');
	$q->addWhere('u.user_contact = con.contact_id');
	$q->addOrder('contact_last_name, contact_first_name');
	$owners = arrayMerge( array( '0'=>'' ), $q->loadHashList() );

// setup the title block
	$ttl = $company_id > 0 ? "Edit Department" : "Add Department";
	$titleBlock = new CTitleBlock( $ttl, 'users.gif', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=companies", "companies list" );
	$titleBlock->addCrumb( "?m=companies&a=view&company_id=$company_id", "view this company" );
	$titleBlock->show();
?>
<script language="javascript">
function testURL( x ) {
	var test = "document.editFrm.dept_url.value";
	test = eval(test);
	if (test.length > 6) {
		newwin = window.open( "http://" + test, 'newwin', '' );
	}
}

function submitIt() {
	var form = document.editFrm;
	if (form.dept_name.value.length < 2) {
		alert( '<?php echo $AppUI->_( 'deptValidName', UI_OUTPUT_JS );?>' );
		form.dept_name.focus();
	} else {
		form.submit();
	}
}
</script>

<table cellspacing="0" cellpadding="4" border="0" width="98%" class="std">
<form name="editFrm" action="?m=departments" method="post">
	<input type="hidden" name="dosql" value="do_dept_aed" />
	<input type="hidden" name="dept_id" value="<?php echo $dept_id;?>" />
	<input type="hidden" name="dept_company" value="<?php echo $company_id;?>" />

<tr>
	<td align="right" nowrap><?php echo $AppUI->_( 'Department Company' );?>:</td>
	<td><strong><?php echo $company_name;?></strong></td>
</tr>
<tr>
	<td align="right" nowrap><?php echo $AppUI->_( 'Department Name' );?>:</td>
	<td>
		<input type="text" class="text" name="dept_name" value="<?php echo @$drow["dept_name"];?>" size="50" maxlength="255" />
		<span class="smallNorm">(<?php echo $AppUI->_( 'required' );?>)</span>
	</td>
</tr>
<tr>
	<td align="right" nowrap><?php echo $AppUI->_( 'Phone' );?>:</td>
	<td>
		<input type="text" class="text" name="dept_phone" value="<?php echo @$drow["dept_phone"];?>" maxlength="30" />
	</td>
</tr>
<tr>
	<td align="right" nowrap><?php echo $AppUI->_( 'Fax' );?>:</td>
	<td>
		<input type="text" class="text" name="dept_fax" value="<?php echo @$drow["dept_fax"];?>" maxlength="30" />
	</td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_( 'Address' );?>1:</td>
	<td><input type="text" class="text" name="dept_address1" value="<?php echo @$drow["dept_address1"];?>" size="50" maxlength="255" /></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_( 'Address' );?>2:</td>
	<td><input type="text" class="text" name="dept_address2" value="<?php echo @$drow["dept_address2"];?>" size="50" maxlength="255"></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_( 'City' );?>:</td>
	<td><input type="text" class="text" name="dept_city" value="<?php echo @$drow["dept_city"];?>" size="50" maxlength="50"></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_( 'State' );?>:</td>
	<td><input type="text" class="text" name="dept_state" value="<?php echo @$drow["dept_state"];?>" maxlength="50"></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_( 'Zip' );?>:</td>
	<td><input type="text" class="text" name="dept_zip" value="<?php echo @$drow["dept_zip"];?>" maxlength="15"></td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_( 'URL' );?><A name="x"></a></td>
	<td>
		<input type="text" class="text" value="<?php echo @$drow["dept_url"];?>" name="dept_url" size="50" maxlength="255">
		<a href="#x" onClick="testURL('dept_url')">[<?php echo $AppUI->_( 'test' );?>]</a>
	</td>
</tr>

<?php
if (count( $depts )) {
?>
<tr>
	<td align="right" nowrap><?php echo $AppUI->_( 'Department Parent' );?>:</td>
	<td>
<?php
	echo arraySelectTree( $depts, 'dept_parent', 'class=text size=1', @$drow["dept_parent"] );
?>
	</td>
</tr>
<?php } else {
	echo '<input type="hidden" name="dept_parent" value="0">';
}
?>
<tr>
	<td align="right"><?php echo $AppUI->_( 'Owner' );?>:</td>
	<td>
<?php
	echo arraySelect( $owners, 'dept_owner', 'size="1" class="text"', $drow["dept_owner"] );
?>
	</td>
</tr>
<tr>
	<td align="right" valign="top" nowrap><?php echo $AppUI->_( 'Description' );?>:</td>
	<td align="left">
		<textarea cols="70" rows="10" class="textarea" name="dept_desc"><?php echo @$drow["dept_desc"];?></textarea>
	</td>
</tr>

<tr>
	<td>
		<input type="button" value="<?php echo $AppUI->_( 'back' );?>" class="button" onClick="javascript:history.back(-1);" />
	</td>
	<td align="right">
		<input type="button" value="<?php echo $AppUI->_( 'submit' );?>" class="button" onClick="submitIt()" />
	</td>
</tr>
</form>
</table>
<?php } ?>

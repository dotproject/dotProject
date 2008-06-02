<?php /* ADMIN $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

GLOBAL $addPwT,$company_id, $dept_ids, $department, $min_view, $m, $a;
$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;

if ($user_id != $AppUI->user_id 
&& ( ! $perms->checkModuleItem('admin', 'view', $user_id) 
|| ! $perms->checkModuleItem('users', 'view', $user_id) ) )
	$AppUI->redirect('m=public&a=access_denied');

$AppUI->savePlace();

if (isset( $_POST['show_form'] )) {
	$add_pwt = dPgetParam($_POST, 'add_pwt', 0 );
	$AppUI->setState( 'addProjWithTasks', $add_pwt);
} else {
	$AppUI->setState( 'addProjWithTasks', false);
}

$addPwT = $AppUI->getState( 'addProjWithTasks' ) ? $AppUI->getState( 'addProjWithTasks' ) : 0;

$company_id = $AppUI->getState( 'UsrProjIdxCompany' ) !== NULL ? $AppUI->getState( 'UsrProjIdxCompany' ) : $AppUI->user_company;

$company_prefix = 'company_';

if (isset( $_POST['department'] )) {
	$AppUI->setState( 'UsrProjIdxDepartment', $_POST['department'] );
	
	//if department is set, ignore the company_id field
	unset($company_id);
}
$department = $AppUI->getState( 'UsrProjIdxDepartment' ) !== NULL ? $AppUI->getState( 'UsrProjIdxDepartment' ) : $company_prefix.$AppUI->user_company;

//if $department contains the $company_prefix string that it's requesting a company and not a department.  So, clear the 
// $department variable, and populate the $company_id variable.
if(!(strpos($department, $company_prefix)===false)){
	$company_id = substr($department,strlen($company_prefix));
	$AppUI->setState( 'UsrProjIdxCompany', $company_id );
	unset($department);
}

if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'UserVwTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'UserVwTab' ) !== NULL ? $AppUI->getState( 'UserVwTab' ) : 0;

// pull data
$q  = new DBQuery;
$q->addTable('users', 'u');
$q->addQuery('u.*');
$q->addQuery('con.*, company_id, company_name, dept_name, dept_id');
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addJoin('departments', 'dep', 'dept_id = contact_department');
$q->addWhere('u.user_id = '.$user_id);
$sql = $q->prepare();
$q->clear();

if (!db_loadHash( $sql, $user )) {
	$titleBlock = new CTitleBlock( 'Invalid User ID', 'helix-setup-user.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=admin", "users list" );
	$titleBlock->show();
} else {

// setup the title block
	$titleBlock = new CTitleBlock( 'View User', 'helix-setup-user.png', $m, "$m.$a" );
	if ($canRead) {
	  $titleBlock->addCrumb( "?m=admin", "users list" );
	}
	if ($canEdit || $user_id == $AppUI->user_id) {
	      $titleBlock->addCrumb( "?m=admin&a=addedituser&user_id=$user_id", "edit this user" );
	      $titleBlock->addCrumb( "?m=system&a=addeditpref&user_id=$user_id", "edit preferences" );
	      $titleBlock->addCrumbRight(
			'<a href="#" onclick="popChgPwd();return false">' . $AppUI->_('change password') . '</a>'
	      );
	      $titleBlock->addCell('<td align="right" width="100%"><input type="button" class=button value="'.$AppUI->_('add user').'" onClick="javascript:window.location=\'./index.php?m=admin&a=addedituser\';" /></td>');
	}
	$titleBlock->show();
?>
<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit || $user_id == $AppUI->user_id) {
?>
function popChgPwd() {
	window.open( './index.php?m=public&a=chpwd&dialog=1&user_id=<?php echo $user['user_id']; ?>', 'chpwd', 'top=250,left=250,width=350, height=220, scrollbars=no' );
}
<?php } ?>
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr valign="top">
	<td width="50%">
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Login Name');?>:</td>
			<td class="hilite" width="100%"><?php echo $user["user_username"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('User Type');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($utypes[$user["user_type"]]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Real Name');?>:</td>
			<td class="hilite" width="100%"><?php echo $user["contact_first_name"].' '.$user["contact_last_name"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Company');?>:</td>
			<td class="hilite" width="100%">
				<?php
				if ($perms->checkModuleItem('companies', 'access', $user['contact_company'])) {
					echo '<a href="?m=companies&a=view&company_id=' . $user['contact_company'] . '" title="' . htmlspecialchars($user['company_name'], ENT_QUOTES) . '">' . htmlspecialchars($user['company_name'], ENT_QUOTES) . '</a>';
				} else {
					echo htmlspecialchars($user['company_name'], ENT_QUOTES);
				}
				?>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Department');?>:</td>
			<td class="hilite" width="100%">
				<a href="?m=departments&a=view&dept_id=<?php echo @$user["contact_department"];?>"><?php echo $user["dept_name"];?></a>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Phone');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_phone"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Home Phone');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_phone2"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Mobile');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_mobile"];?></td>
		</tr>
		<tr valign=top>
			<td align="right" nowrap><?php echo $AppUI->_('Address');?>:</td>
			<td class="hilite" width="100%"><?php
				echo @$user["contact_address1"]
					.( ($user["contact_address2"]) ? '<br />'.$user["contact_address2"] : '' )
					.'<br />'.$user["contact_city"]
					.'&nbsp;&nbsp;'.$user["contact_state"]
					.'&nbsp;&nbsp;'.$user["contact_zip"]
					.'<br />'.$user["contact_country"]
					;
			?></td>
		</tr>
		</table>

	</td>
	<td width="50%">
		<table width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Birthday');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_birthday"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap>ICQ#:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_icq"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap>AOL Nick:</td>
			<td class="hilite" width="100%"><a href="aim:<?php echo @$user["contact_aol"];?>"><?php echo @$user["contact_aol"];?></a></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Email');?>:</td>
			<td class="hilite" width="100%"><?php echo '<a href="mailto:'.@$user["contact_email"].'">'.@$user["contact_email"].'</a>';?></td>
		</tr>
		<tr>
			<td colspan="2"><strong><?php echo $AppUI->_('Signature');?>:</strong></td>
		</tr>
		<tr>
			<td class="hilite" width="100%" colspan="2">
				<?php echo str_replace( chr(10), "<br />", $user["user_signature"]);?>&nbsp;
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<?php
	// tabbed information boxes
	$min_view = true;
	$tabBox = new CTabBox( "?m=admin&a=viewuser&user_id=$user_id", '', $tab );
	$tabBox->loadExtras('admin', 'viewuser'); 
	$tabBox->add( DP_BASE_DIR.'/modules/admin/vw_usr_log', 'User Log');
	$tabBox->add( DP_BASE_DIR.'/modules/admin/vw_usr_perms', 'Permissions' );
	$tabBox->add( DP_BASE_DIR.'/modules/admin/vw_usr_roles', 'Roles' );
	$tabBox->show();
}
?>

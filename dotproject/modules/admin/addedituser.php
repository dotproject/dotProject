<?php /* ADMIN $Id$ */
//add or edit a system user

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

if ($user_id == 0)
	$canEdit = $canAuthor;

if ($canEdit)
	$canEdit = $perms->checkModuleItem('users', ($user_id ? 'edit' : 'add'), $user_id);

// check permissions
if (!$canEdit && $user_id != $AppUI->user_id) {
    $AppUI->redirect( "m=public&a=access_denied" );
}

//$roles
// Create the roles class container
require_once DP_BASE_DIR."/modules/system/roles/roles.class.php";
$perms =& $AppUI->acl();
$crole =& new CRole;
$roles = $crole->getRoles();
// Format the roles for use in arraySelect
$roles_arr = array();
foreach ($roles as $role) {
  $roles_arr[$role['id']] = $role['name'];
}
$roles_arr = arrayMerge( array( 0 => '' ), $roles_arr );


$q  = new DBQuery;
$q->addTable('users', 'u');
$q->addQuery('u.*');
$q->addQuery('con.*, company_id, company_name, dept_name');
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addJoin('departments', 'dep', 'dept_id = contact_department');
$q->addWhere('u.user_id = '.$user_id);
$sql = $q->prepare();
$q->clear();

if (!db_loadHash( $sql, $user ) && $user_id > 0) {
	$titleBlock = new CTitleBlock( 'Invalid User ID', 'helix-setup-user.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=admin", "users list" );
	$titleBlock->show();
} else {
	 if ( $user_id == 0)
        $user['contact_id'] = 0;
// pull companies
	$q = new DBQuery;
	$q->addTable('companies');
	$q->addQuery('company_id, company_name');
	$q->addOrder('company_name');
	$companies = arrayMerge( array( 0 => '' ), $q->loadHashList() );

// setup the title block
	$ttl = $user_id > 0 ? "Edit User" : "Add User";
	$titleBlock = new CTitleBlock( $ttl, 'helix-setup-user.png', $m, "$m.$a" );
	if ($perms->checkModule('admin', 'view') && $perms->checkModule('users', 'view'))
		$titleBlock->addCrumb( "?m=admin", "users list" );
	if ($user_id > 0) {
		$titleBlock->addCrumb( "?m=admin&a=viewuser&user_id=$user_id", "view this user" );
		if ($canEdit || $user_id == $AppUI->user_id) {
		$titleBlock->addCrumb( "?m=system&a=addeditpref&user_id=$user_id", "edit preferences" );
		}
	}
	$titleBlock->show();
?>
<SCRIPT language="javascript">
function submitIt(){
    var form = document.editFrm;
   if (form.user_username.value.length < <?php echo dPgetConfig('username_min_len'); ?> && form.user_username.value != '<?php echo dPgetConfig('admin_username'); ?>') {
        alert("<?php echo $AppUI->_('adminValidUserName', UI_OUTPUT_JS)  ;?>"  + <?php echo dPgetConfig('username_min_len'); ?>);
        form.user_username.focus();
      <?php if ($canEdit && !$user_id) { ?>
    } else if (form.user_role.value <=0 ) {
        alert("<?php echo $AppUI->_('adminValidRole', UI_OUTPUT_JS);?>");
        form.user_role.focus();     <?php } ?>
    } else if (form.user_password.value.length < <?php echo dPgetConfig('password_min_len'); ?>) {
        alert("<?php echo $AppUI->_('adminValidPassword', UI_OUTPUT_JS);?>" + <?php echo dPgetConfig('password_min_len'); ?>);
        form.user_password.focus();
    } else if (form.user_password.value !=  form.password_check.value) {
        alert("<?php echo $AppUI->_('adminPasswordsDiffer', UI_OUTPUT_JS);?>");
        form.user_password.focus();
    } else if (form.contact_first_name.value.length < 1) {
        alert("<?php echo $AppUI->_('adminValidFirstName', UI_OUTPUT_JS);?>");
        form.contact_first_name.focus();
    } else if (form.contact_last_name.value.length < 1) {
        alert("<?php echo $AppUI->_('adminValidLastName', UI_OUTPUT_JS);?>");
        form.contact_last_name.focus();
    } else if (form.contact_email.value.length < 4) {
        alert("<?php echo $AppUI->_('adminInvalidEmail', UI_OUTPUT_JS);?>");
        form.contact_email.focus();
    } else if (form.contact_birthday && form.contact_birthday.value.length > 0) {
        dar = form.contact_birthday.value.split("-");
        if (dar.length < 3) {
            alert("<?php echo $AppUI->_('adminInvalidBirthday', UI_OUTPUT_JS);?>");
            form.contact_birthday.focus();
        } else if (isNaN(parseInt(dar[0],10)) || isNaN(parseInt(dar[1],10)) || isNaN(parseInt(dar[2],10))) {
            alert("<?php echo $AppUI->_('adminInvalidBirthday', UI_OUTPUT_JS);?>");
            form.contact_birthday.focus();
        } else if (parseInt(dar[1],10) < 1 || parseInt(dar[1],10) > 12) {
            alert("<?php echo $AppUI->_('adminInvalidMonth', UI_OUTPUT_JS).' '.$AppUI->_('adminInvalidBirthday', UI_OUTPUT_JS);?>");
            form.contact_birthday.focus();
        } else if (parseInt(dar[2],10) < 1 || parseInt(dar[2],10) > 31) {
            alert("<?php echo $AppUI->_('adminInvalidDay', UI_OUTPUT_JS).' '.$AppUI->_('adminInvalidBirthday', UI_OUTPUT_JS);?>");
            form.contact_birthday.focus();
        } else if(parseInt(dar[0],10) < 1900 || parseInt(dar[0],10) > 2020) {
            alert("<?php echo $AppUI->_('adminInvalidYear', UI_OUTPUT_JS).' '.$AppUI->_('adminInvalidBirthday', UI_OUTPUT_JS);?>");
            form.contact_birthday.focus();
        } else {
            form.submit();
        }
    } else {
        form.submit();
    }
}

function popDept() {
    var f = document.editFrm;
    if (f.selectedIndex == 0) {
        alert('<?php echo $AppUI->_( 'Please select a company first!', UI_OUTPUT_JS ); ?>');
    } else {
        window.open('./index.php?m=public&a=selector&dialog=1&callback=setDept&table=departments&company_id='
            + f.contact_company.options[f.contact_company.selectedIndex].value
            + '&dept_id='+f.contact_department.value,'dept','left=50,top=50,height=250,width=400,resizable')
    }
}

// Callback function for the generic selector
function setDept( key, val ) {
    var f = document.editFrm;
    if (val != '') {
        f.contact_department.value = key;
        f.dept_name.value = val;
    } else {
        f.contact_department.value = '0';
        f.dept_name.value = '';
    }
}
</script>

<table width="100%" border="0" cellpadding="0" cellspacing="1" height="400" class="std">
<form name="editFrm" action="./index.php?m=admin" method="post">
	<input type="hidden" name="user_id" value="<?php echo intval($user["user_id"]);?>" />
	<input type="hidden" name="contact_id" value="<?php echo intval($user["contact_id"]);?>" />
	<input type="hidden" name="dosql" value="do_user_aed" />
	<input type="hidden" name="username_min_len" value="<?php echo dPgetConfig('username_min_len'); ?>)" />
	<input type="hidden" name="password_min_len" value="<?php echo dPgetConfig('password_min_len'); ?>)" />
	

<tr>
    <td align="right" width="230">* <?php echo $AppUI->_('Login Name');?>:</td>
    <td>
<?php
	if (@$user["user_username"]){
		echo '<input type="hidden" class="text" name="user_username" value="' . $user["user_username"] . '" />';
		echo '<strong>' . $user["user_username"] . '</strong>';
    } else {
        echo '<input type="text" class="text" name="user_username" value="' . $user["user_username"] . '" maxlength="255" size="40" />';
    }
?>
	</td></tr>
<?php if ($canEdit) { // prevent users without read-write permissions from seeing and editing user type
?>
<tr>
    <td align="right"> <?php echo $AppUI->_('User Type');?>:</td>
    <td>
<?php
    echo arraySelect( $utypes, 'user_type', 'class=text size=1', $user["user_type"], true );
?>
    </td>
</tr>
<?php } // End of security
?>
<?php if ($canEdit && !$user_id) { ?>
<tr>
    <td align="right">* <?php echo $AppUI->_('User Role');?>:</td>
    <td><?php echo arraySelect($roles_arr, 'user_role', 'size="1" class="text"','', true);?></td>
</tr>
<?php }
?>
<tr>
    <td align="right">* <?php echo $AppUI->_('Password');?>:</td>
    <td><input type="password" class="text" name="user_password" value="<?php echo $user["user_password"];?>" maxlength="32" size="32" /> </td>
</tr>
<tr>
    <td align="right">* <?php echo $AppUI->_('Confirm Password');?>:</td>
    <td><input type="password" class="text" name="password_check" value="<?php echo $user["user_password"];?>" maxlength="32" size="32" /> </td>
</tr>
<tr>
    <td align="right">* <?php echo $AppUI->_('Name');?>:</td>
    <td><input type="text" class="text" name="contact_first_name" value="<?php echo $user["contact_first_name"];?>" maxlength="50" /> <input type="text" class="text" name="contact_last_name" value="<?php echo $user["contact_last_name"];?>" maxlength="50" /></td>
</tr>
<?php if ($canEdit) { ?>
<tr>
    <td align="right"> <?php echo $AppUI->_('Company');?>:</td>
    <td>
<?php
    echo arraySelect( $companies, 'contact_company', 'class=text size=1', $user["contact_company"] );
?>
    </td>
</tr>
<?php } ?>
<tr>
    <td align="right"><?php echo $AppUI->_('Department');?>:</td>
    <td>
        <input type="hidden" name="contact_department" value="<?php echo @$user["contact_department"];?>" />
        <input type="text" class="text" name="dept_name" value="<?php echo @$user["dept_name"];?>" size="40" disabled />
        <input type="button" class="button" value="<?php echo $AppUI->_('select dept');?>..." onclick="popDept()" />
    </td>
</tr>
<tr>
    <td align="right">* <?php echo $AppUI->_('Email');?>:</td>
    <td><input type="text" class="text" name="contact_email" value="<?php echo $user["contact_email"];?>" maxlength="255" size="40" /> </td>
</tr>
<tr>
    <td align="right" valign=top><?php echo $AppUI->_('Email').' '.$AppUI->_('Signature');?>:</td>
    <td><textarea class="text" cols=50 name="user_signature" style="height: 50px"><?php echo @$user["user_signature"];?></textarea></td>
</tr>
<tr>
	<td align="right"><a href="?m=contacts&a=addedit&contact_id=<?php echo $user['user_contact']; ?>"><?php echo $AppUI->_(array('edit', 'contact info')); ?></a></td>
	<td>&nbsp;</td>
</tr>
<tr>
    <td align="right">* <?php echo $AppUI->_('Required Fields'); ?></td>
    <td></td>
<tr>
    <td align="left">
        <input type="button" value="<?php echo $AppUI->_('back');?>" onClick="javascript:history.back(-1);" class="button" />
    </td>
    <td align="right">
    <?php if ($canEdit && !$user_id) { ?>
	<?php echo $AppUI->_('Inform new user of his account details?'); ?> <input type='checkbox' value='1' name='send_user_mail' >&nbsp;&nbsp;&nbsp;<?php } ?>
	<input type="button" value="<?php echo $AppUI->_('submit');?>" onClick="submitIt()" class="button" />
    </td>
</tr>
</table>
<?php } ?>

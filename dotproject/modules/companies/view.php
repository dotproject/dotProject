<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

$company_id = intval(dPgetParam($_GET, 'company_id', 0));

// check permissions for this record
$perms =& $AppUI->acl();
$canRead = $perms->checkModuleItem($m, 'view', $company_id);
$canEdit = $perms->checkModuleItem($m, 'edit', $company_id);


if (!$canRead) {
	$AppUI->redirect("m=public&a=access_denied");
}

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('CompVwTab', $_GET['tab']);
}
$tab = $AppUI->getState('CompVwTab') !== NULL ? $AppUI->getState('CompVwTab') : 0;

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CCompany();
$canDelete = $obj->canDelete($msg, $company_id);

// load the record data
$q  = new DBQuery;
$q->addTable('companies');
$q->addQuery('companies.*');
$q->addQuery('con.contact_first_name');
$q->addQuery('con.contact_last_name');
$q->addJoin('users', 'u', 'u.user_id = companies.company_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('companies.company_id = '.$company_id);
$sql = $q->prepare();
$q->clear();

$obj = null;
if (!db_loadObject($sql, $obj)) {
	$AppUI->setMsg('Company');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// load the list of project statii and company types
$pstatus = dPgetSysVal('ProjectStatus');
$types = dPgetSysVal('CompanyType');

// setup the title block
$titleBlock = new CTitleBlock('View Company', 'handshake.png', $m, "$m.$a");
if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new company').'" />', '',
		'<form action="?m=companies&a=addedit" method="post">', '</form>'
	);
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new project').'" />', '',
		'<form action="?m=projects&a=addedit&company_id='.$company_id.'" method="post">', '</form>'
	);
}
$titleBlock->addCrumb("?m=companies", "company list");
if ($canEdit) {
	$titleBlock->addCrumb("?m=companies&a=addedit&company_id=$company_id", "edit this company");
	
	if ($canDelete) {
		$titleBlock->addCrumbDelete('delete company', $canDelete, $msg);
	}
}
$titleBlock->show();
?>
<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
?>
function delIt() {
	if (confirm("<?php echo $AppUI->_('doDelete').' '.$AppUI->_('Company').'?';?>")) {
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<?php if ($canDelete) {
?>
<form name="frmDelete" action="./index.php?m=companies" method="post">
	<input type="hidden" name="dosql" value="do_company_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="company_id" value="<?php echo $company_id;?>" />
</form>
<?php } ?>

<tr>
	<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Company');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->company_name;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Owner');?>:</td>
			<td class="hilite" width="100%"><?php echo "{$obj->contact_first_name} {$obj->contact_last_name}";?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Email');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->company_email;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Phone');?>:</td>
			<td class="hilite"><?php echo @$obj->company_phone1;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Phone');?>2:</td>
			<td class="hilite"><?php echo @$obj->company_phone2;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Fax');?>:</td>
			<td class="hilite"><?php echo @$obj->company_fax;?></td>
		</tr>
		<tr valign=top>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Address');?>:</td>
			<td class="hilite">
			<a href='http://maps.google.com/maps?q=<?php echo @$obj->company_address1;?>+<?php echo @$obj->company_address2;?>+<?php echo @$obj->company_city;?>+<?php echo @$obj->company_state;?>+<?php echo @$obj->company_zip;?>+<?php echo @$obj->company_country;?>' target='_blank'><image align="right" border="0" src="./images/googlemaps.gif" width="55" height="22" alt="Find It on Google" /></a>
			<?php
						echo @$obj->company_address1
							.(($obj->company_address2) ? '<br />'.$obj->company_address2 : '')
							.(($obj->company_city) ? '<br />'.$obj->company_city : '')
							.(($obj->company_state) ? '<br />'.$obj->company_state : '')
							.(($obj->company_zip) ? '<br />'.$obj->company_zip : '')
							;
			?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('URL');?>:</td>
			<td class="hilite">
				<a href="http://<?php echo @$obj->company_primary_url;?>" target="Company"><?php echo @$obj->company_primary_url;?></a>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Type');?>:</td>
			<td class="hilite"><?php echo $AppUI->_($types[@$obj->company_type]);?></td>
		</tr>
		</table>

	</td>
	<td width="50%" valign="top">
		<strong><?php echo $AppUI->_('Description');?></strong>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td class="hilite">
				<?php echo str_replace(chr(10), "<br />", $obj->company_description);?>&nbsp;
			</td>
		</tr>
		
		</table>
		<?php
			require_once($AppUI->getSystemClass('CustomFields'));
			$custom_fields = New CustomFields($m, $a, $obj->company_id, "view");
			$custom_fields->printHTML();
		?>
	</td>
</tr>
</table>

<?php
// tabbed information boxes
$moddir = DP_BASE_DIR . '/modules/companies/';
$tabBox = new CTabBox("?m=companies&a=view&company_id=$company_id", "", $tab);
$tabBox->add($moddir . 'vw_active', 'Active Projects');
$tabBox->add($moddir . 'vw_archived', 'Archived Projects');
$tabBox->add($moddir . 'vw_depts', 'Departments');
$tabBox->add($moddir . 'vw_users', 'Users');
$tabBox->add($moddir . 'vw_contacts', 'Contacts');
$tabBox->loadExtras($m);
$tabBox->loadExtras($m, 'view');
$tabBox->show();

?>

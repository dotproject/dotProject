<?php
/* COMPANIES $Id: view.php 6080 2010-12-04 08:39:35Z ajdonnison $ */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$company_id = intval(dPgetParam($_GET, 'company_id', 0));

// check permissions for this record
$canRead = getPermission($m, 'view', $company_id);
$canEdit = getPermission($m, 'edit', $company_id);


if (!$canRead) {
    $AppUI->redirect('m=public&a=access_denied');
}

// retrieve any state parameters
if (isset($_GET['tab'])) {
    $AppUI->setState('CompVwTab', $_GET['tab']);
}
$tab = (($AppUI->getState('CompVwTab') !== NULL) ? $AppUI->getState('CompVwTab') : 0);

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CCompany();
$canDelete = $obj->canDelete($msg, $company_id);

// load the record data
$q = new DBQuery;
$q->addTable('companies', 'co');
$q->addQuery('co.*');
$q->addQuery('con.contact_first_name');
$q->addQuery('con.contact_last_name');
$q->addJoin('users', 'u', 'u.user_id = co.company_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('co.company_id = ' . $company_id);
$sql = $q->prepare();
$q->clear();

$obj = null;
if (!db_loadObject($sql, $obj)) {
    $AppUI->setMsg('Company');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
} else {
    $AppUI->savePlace();
}

// load the list of project statii and company types
$pstatus = dPgetSysVal('ProjectStatus');
$types = dPgetSysVal('CompanyType');

// setup the title block
/*
  $titleBlock = new CTitleBlock('View Company', 'handshake.png', $m, "$m.$a");
  if ($canEdit) {
  $titleBlock->addCell();
  $titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new company')
  . '" />'), '', '<form action="?m=companies&amp;a=addedit" method="post">',
  '</form>');
  $titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new project')
  . '" />'), '',
  ('<form action="?m=projects&amp;a=addedit&amp;company_id='
  . dPformSafe($company_id) . '" method="post">'), '</form>');
  }
  $titleBlock->addCrumb('?m=companies', 'company list');
  if ($canEdit) {
  $titleBlock->addCrumb(('?m=companies&amp;a=addedit&amp;company_id=' . $company_id),
  'edit this company');
  if ($canDelete) {
  $titleBlock->addCrumbDelete('delete company', $canDelete, $msg);
  }
  }
  $titleBlock->show();
 */
?>
<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side
// in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
    ?>
        function delIt() {
            if (confirm("<?php echo ($AppUI->_('doDelete') . ' ' . $AppUI->_('Company') . '?'); ?>")) {
                document.frmDelete.submit();
            }
        }
<?php } ?>
</script>

<?php if ($canDelete) {
    ?>
    <form name="frmDelete" action="./index.php?m=companies" method="post">
        <input type="hidden" name="dosql" value="do_company_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="company_id" value="<?php echo dPformSafe($company_id); ?>" />
    </form>
<?php } ?>




<!-- New form -->
<?php
require_once (DP_BASE_DIR . "/modules/human_resources/human_resources.class.php");
$query = new DBQuery();
$query->addTable('company_policies', 'p');
$query->addQuery('company_policies_id');
$query->addWhere('p.company_policies_company_id = ' . $company_id);
$res = & $query->exec();
$company_policies_id = $res->fields['company_policies_id'];
$query->clear();

$policies = new CCompaniesPolicies();
if ($company_policies_id != "") {
    $policies->load($company_policies_id);
}
?>
<script>
    function showCompanyForm(){
        var table=document.getElementById("table_company_form");
        if(table.style.display=="none"){
            table.style.display="table";
        }else{
            table.style.display="none";
        }
    }
</script>

<div style="font-size: 14px;font-weight: bold;cursor:pointer;" onclick="showCompanyForm()" >
<?php echo htmlspecialchars($obj->company_name); ?>  &nbsp;<img src='./modules/dotproject_plus/images/icone_seta.png' />
</div>
<div style="float:right;text-align: right">
    <input type="button" onclick="window.location='index.php?m=companies&a=addedit&company_id=<?php echo $company_id ?>';" value="Editar" class="button" />
    <input type="button" onclick="document.getElementById('rh_div').style.display='block';" value="RH" class="button" />
    <input type="button" onclick="window.location='index.php?m=admin';" value="Usuários" class="button" />
    <input type="button" onclick="window.location='index.php?m=contacts';" value="Contatos" class="button" />
</div>
<br /><br />
<table  border="0" cellpadding="4" cellspacing="0" width="100%" class="std" id="table_company_form">
    <tr>
        <td class="label_dpp">Nome<span style="color:red">*</span>:</td>
        <td> <?php echo htmlspecialchars($obj->company_name); ?> </td>
        <td class="label_dpp"> Dono: </td>
        <td> <?php echo (htmlspecialchars($obj->contact_first_name) . '&nbsp;' . htmlspecialchars($obj->contact_last_name)); ?> </td>
    </tr>
    <tr>
        <th colspan="4"><b>Contato</b></th>
    </tr>
    <tr>
        <td class="label_dpp"><?php echo $AppUI->_('Phone'); ?>:</td>
        <td colspan="3"><?php echo htmlspecialchars(@$obj->company_phone1); ?></td>
    </tr>
    <tr>
        <td class="label_dpp"><?php echo $AppUI->_('Email'); ?>: </td>
        <td colspan="3"><?php echo htmlspecialchars($obj->company_email); ?></td>
    </tr>
    <tr>
        <td class="label_dpp"> <?php echo $AppUI->_('Address'); ?>: </td>
        <td colspan="3"><?php echo $obj->company_address1; ?></td>
    </tr>
    <tr>
        <td class="label_dpp"> Cidade: </td>
        <td><?php echo htmlspecialchars($obj->company_city); ?>  </td>
        <td class="label_dpp">
            Estado:
        </td>
        <td> 
            <?php echo htmlspecialchars($obj->company_state); ?>&nbsp;&nbsp;
            <b>CEP:</b>&nbsp;&nbsp; <?php echo htmlspecialchars($obj->company_zip); ?>
        </td>
        </tr>
        <tr>
            <th colspan="4"><b>Política Organizacional</b></th>
        </tr> 
        <tr>
            <td class="label_dpp"><?php echo $AppUI->_('Recompensas'); ?>:</td>
            <td colspan="3"><?php echo $policies->company_policies_recognition; ?></td>
        </tr>
        <tr>
            <td class="label_dpp"><?php echo $AppUI->_('Regulamentos'); ?>:</td>
            <td colspan="3"><?php echo $policies->company_policies_policy; ?></td>
        </tr>
        <tr>
            <td class="label_dpp"><?php echo $AppUI->_('Safety'); ?>:</td>
            <td colspan="3"><?php echo $policies->company_policies_safety; ?></td>
        </tr>
</table>
<!-- old form -->
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std" style="display:none">
    <tr>
        <td valign="top" width="50%">
            <strong><?php echo $AppUI->_('Details'); ?></strong>
            <table cellspacing="1" cellpadding="2" width="100%">
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Company'); ?>:</td>
                    <td class="hilite" width="100%"><?php echo htmlspecialchars($obj->company_name); ?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Owner'); ?>:</td>
                    <td class="hilite" width="100%"><?php
echo (htmlspecialchars($obj->contact_first_name) . '&nbsp;'
 . htmlspecialchars($obj->contact_last_name));
?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Email'); ?>:</td>
                    <td class="hilite" width="100%"><?php echo htmlspecialchars($obj->company_email); ?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Phone'); ?>:</td>
                    <td class="hilite"><?php echo htmlspecialchars(@$obj->company_phone1); ?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Phone'); ?>2:</td>
                    <td class="hilite"><?php echo htmlspecialchars(@$obj->company_phone2); ?></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Fax'); ?>:</td>
                    <td class="hilite"><?php echo htmlspecialchars(@$obj->company_fax); ?></td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Address'); ?>:</td>
                    <td class="hilite">
                                                      <?php if (!empty($obj->company_country)) { ?>
                            <span style="float: right"><a href="http://maps.google.com/maps?q=<?php echo dPformSafe(@$obj->company_address1, DP_FORM_URI); ?>+<?php echo dPformSafe(@$obj->company_address2, DP_FORM_URI); ?>+<?php echo dPformSafe(@$obj->company_city, DP_FORM_URI); ?>+<?php echo dPformSafe(@$obj->company_state, DP_FORM_URI); ?>+<?php echo dPformSafe(@$obj->company_zip, DP_FORM_URI); ?>+<?php echo dPformSafe(@$obj->company_country, DP_FORM_URI); ?>" target="_blank">
                                                          <?php
                                                          echo dPshowImage('./images/googlemaps.gif', 55, 22, 'Find It on Google');
                                                          ?>
                                                      <?php } ?>
                            </a></span>
                                                          <?php
                                                          echo (htmlspecialchars(@$obj->company_address1)
                                                          . (($obj->company_address2) ? '<br />' : '') . htmlspecialchars($obj->company_address2)
                                                          . (($obj->company_city) ? '<br />' : '') . htmlspecialchars($obj->company_city)
                                                          . (($obj->company_state) ? ', ' : '') . htmlspecialchars($obj->company_state)
                                                          . (($obj->company_zip) ? ' ' : '') . htmlspecialchars($obj->company_zip));
                                                          ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('URL'); ?>:</td>
                    <td class="hilite">
                        <a href="http://<?php echo dPformSafe(@$obj->company_primary_url, DP_FORM_URI); ?>" target="Company"><?php echo htmlspecialchars(@$obj->company_primary_url); ?></a>
                    </td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Type'); ?>:</td>
                    <td class="hilite"><?php echo $AppUI->_($types[@$obj->company_type]); ?></td>
                </tr>
            </table>

        </td>
        <td width="50%" valign="top">
            <strong><?php echo $AppUI->_('Description'); ?></strong>
            <table cellspacing="0" cellpadding="2" border="0" width="100%" summary="company description">
                <tr>
                    <td class="hilite">
<?php echo str_replace(chr(10), '<br />', htmlspecialchars($obj->company_description)); ?>&nbsp;
                    </td>
                </tr>

            </table>
<?php
require_once($AppUI->getSystemClass('CustomFields'));
$custom_fields = New CustomFields($m, $a, $obj->company_id, 'view');
$custom_fields->printHTML();
?>
        </td>
    </tr>
</table>

<br />

<hr />
<div id="rh_div" style="display:<?php echo $_GET["rh_config"]=="1"?"block":"none" ?>">
<?php require_once (DP_BASE_DIR . "/modules/human_resources/view_company_users.php"); ?>
<?php require_once (DP_BASE_DIR . "/modules/timeplanning/companies_organizational_diagram.php"); ?> 
<?php require_once (DP_BASE_DIR . "/modules/human_resources/view_company_roles.php"); ?> 
</div>
<br />
<?php require_once (DP_BASE_DIR . "/modules/dotproject_plus/companies_tab.Projects.php"); ?>
<?php
// tabbed information boxes
/*
  $moddir = DP_BASE_DIR . '/modules/companies/';
  $tabBox = new CTabBox(('?m=companies&amp;a=view&amp;company_id=' . $company_id), '', $tab);
  $tabBox->add($moddir . 'vw_active', 'Active Projects');
  $tabBox->add($moddir . 'vw_archived', 'Archived Projects');
  $tabBox->add($moddir . 'vw_depts', 'Departments');
  $tabBox->add($moddir . 'vw_users', 'Users');
  $tabBox->add($moddir . 'vw_contacts', 'Contacts');
  $tabBox->loadExtras($m);
  $tabBox->loadExtras($m, 'view');
  $tabBox->show();
 */
?>

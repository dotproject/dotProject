<?php /* SYSTEM $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $AppUI;

##
## add or edit a user preferences
##
$company_id=0;
$company_id = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
// Check permissions
if (!$canEdit) {
  $AppUI->redirect('m=public&a=access_denied');
}

$q = new DBQuery;
$q->addTable('billingcode','bc');
$q->addQuery('*');
$q->addOrder('billingcode_name ASC');
$q->addWhere('company_id = ' . $company_id);
$billingcodes = $q->loadList();
$q->clear();

$q->addTable('companies','c');
$q->addQuery('company_id, company_name');
$q->addOrder('company_name ASC');
$company_list = $q->loadHashList();
$company_list['0'] = $AppUI->_('Select Company');
$q->clear();

$company_name = $company_list[$company_id];

$titleBlock = new CTitleBlock('Edit Billing Codes', 'myevo-weather.png', $m, "$m.$a");
$titleBlock->addCrumb('?m=system', 'system admin');
$titleBlock->show();
?>
<script type="text/javascript" language="javascript">
<!--
function submitIt() {
	var form = document.changecode;
	form.submit();
}

function changeIt() {
	var f=document.changeMe;
	var msg = '';
	f.submit();
}


function delIt2(id) {
	document.frmDel.billingcode_id.value = id;
	document.frmDel.submit();
}
-->
</script>

<form name="changeMe" action="./index.php?m=system&amp;a=billingcode" method="post">
	<?php echo arraySelect($company_list, 'company_id', 'size="1" class="text" onchange="changeIt();"', $company_id, false);?>
</form>

<form name="frmDel" action="./index.php?m=system" method="post">
  <input type="hidden" name="dosql" value="do_billingcode_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
  <input type="hidden" name="billingcode_id" value="" />
</form>

<form name="changecode" action="./index.php?m=system" method="post">
  <input type="hidden" name="dosql" value="do_billingcode_aed" />
  <input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
  <input type="hidden" name="billingcode_status" value="0" />

<table border="0" cellpadding="1" cellspacing="1" class="std">
<tr>
  <th>&nbsp;</th>
  <th><?php echo $AppUI->_('Billing Code');?></th>
  <th><?php echo $AppUI->_('Value');?></th>
  <th><?php echo $AppUI->_('Description');?></th>
</tr>

<?php

foreach ($billingcodes as $code) {
	$code_id = $code['billingcode_id'];
	$code_name = htmlspecialchars($code['billingcode_name']);
	$code_value = htmlspecialchars($code['billingcode_value']);
	$code_desc = htmlspecialchars($code['billingcode_desc']);
	if ($code['billingcode_id'] == $_GET['billingcode_id']) {
?>
<tr>
  <td><input type="hidden" name="billingcode_id" value="<?php echo $code_id; ?>" /></td>
  <td><input type="text" name="billingcode_name" value="<?php echo $code_name; ?>" /></td>
  <td><input type="text" name="billingcode_value" value="<?php echo $code_value; ?>" /></td>
  <td><input type="text" name="billingcode_desc" value="<?php echo $code_desc; ?>" /></td>
</tr>
<?php
	} else {
?>
<tr>
  <td>
	<a href="?m=system&amp;a=billingcode&amp;company_id=<?php 
		echo $company_id; ?>&amp;billingcode_id=<?php echo $code_id; ?>" title="<?php 
		echo $AppUI->_('edit'); ?>">
	<img src="./images/icons/stock_edit-16.png" border="0" alt="Edit" />
	</a>
	<?php		
		if ($code['billingcode_status'] == 0) {
?>
	<a href="javascript:delIt2(<?php echo $code_id; ?>);" title="<?php echo $AppUI->_('delete'); 
?>">
	<img src="./images/icons/stock_delete-16.png" border="0" alt="Delete" />
	</a>
<?php 
		} 
?>
  </td>
  <td nowrap="nowrap"><?php 
		echo ($code_name . (($code['billingcode_status'] == 1) ? ' (deleted)':'')); ?></td>
  <td nowrap="nowrap"><?php echo $code_value; ?></td>
  <td nowrap="nowrap"><?php echo $code_desc; ?></td>
</tr>
<?php
	}
}

if (!(isset($_GET['billingcode_id']))) {
?>
<tr>
  <td><input type="hidden" name="billingcode_id" value="" /></td>
  <td><input type="text" name="billingcode_name" value="" /></td>
  <td><input type="text" name="billingcode_value" value="" /></td>
  <td><input type="text" name="billingcode_desc" value="" /></td>
</tr>
<?php 
}
?>
<tr>
  <td align="left" colspan="2">
	<input class="button"  type="button" value="<?php 
echo $AppUI->_('back');?>" onclick="javascript:history.back(-1);" />
  </td>
  <td align="right" colspan="4">
	<input class="button" type="button" value="<?php 
echo $AppUI->_('submit');?>" onclick="submitIt()" />
  </td>
</tr>
</table>
</form>

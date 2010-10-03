<?php /* SYSTEM $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$AppUI->savePlace();

$titleBlock = new CTitleBlock('System Administration', '48_my_computer.png', $m, "$m.$a");
$titleBlock->show();
?>
<div>
<table width="50%" border="0" cellpadding="0" cellspacing="5" align="left">
<tr>
	<td width="42">
		<?php echo dPshowImage(dPfindImage('rdf2.png', $m), 42, 42, ''); ?>
	</td>
	<td align="left" class="subtitle">
		<?php echo $AppUI->_('Language Support');?>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=system&amp;a=translate"><?php echo $AppUI->_('Translation Management');?></a>
	</td>
</tr>

<tr>
	<td>
		<?php echo dPshowImage(dPfindImage('myevo-weather.png', $m), 42, 42, ''); ?>
	</td>
	<td align="left" class="subtitle">
		<?php echo $AppUI->_('Preferences');?>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=system&amp;a=systemconfig"><?php echo $AppUI->_('System Configuration');?></a>
		<br /><a href="?m=system&amp;a=addeditpref"><?php echo $AppUI->_('Default User Preferences');?></a>
		<br /><a href="?m=system&amp;u=syskeys&amp;a=keys"><?php echo $AppUI->_('System Lookup Keys');?></a>
		<br /><a href="?m=system&amp;u=syskeys"><?php echo $AppUI->_('System Lookup Values');?></a>
		<br /><a href="?m=system&amp;a=custom_field_editor"><?php echo $AppUI->_('Custom Field Editor');?></a>
                <br /><a href="?m=system&amp;a=billingcode"><?php echo $AppUI->_('Billing Code Table');?></a>
	</td>
</tr>

<tr>
	<td>
		<?php echo dPshowImage(dPfindImage('power-management.png', $m), 42, 42, ''); ?>
	</td>
	<td align="left" class="subtitle">
		<?php echo $AppUI->_('Modules');?>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=system&amp;a=viewmods"><?php echo $AppUI->_('View Modules');?></a>
	</td>
</tr>

<tr>
	<td>
		<?php echo dPshowImage(dPfindImage('main-settings.png', $m), 42, 42, ''); ?>
	</td>
	<td align="left" class="subtitle">
		<?php echo $AppUI->_('Administration');?>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=system&amp;u=roles"><?php echo $AppUI->_('User Roles');?></a>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=system&amp;a=contacts_ldap"><?php echo $AppUI->_('Import Contacts');?></a>
	</td>
</tr>

</table>
</div>

<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

// Copyright 2004 Adam Donnison <adam@saki.com.au>
$resource_id = intval(dPgetParam($_GET, 'resource_id', null));

$canDelete = getPermission('resources', 'delete', $resource_id);
$canView = getPermission('resources', 'view', $resource_id);
if ((! $resource_id && ! getPermission('resources', 'add')) || ! $canView || ! $canEdit) {
	$AppUI->redirect('m=public&a=access_denied');
}

$obj =& new CResource;
if ($resource_id && ! $obj->load($resource_id)) {
	$AppUI->setMsg('Resource');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
}

$titleBlock =& new CTitleBlock((($resource_id) ? 'Edit Resource' : 'Add Resource'), 
							   'helpdesk.png', $m, "$m.$a"
);
$titleBlock->addCrumb('?m=resources', 'resource list');
if ($resource_id) {
    $titleBlock->addCrumb("?m=resources&a=view&resource_id=$resource_id", 'view this resource');
}
$titleBlock->show();

$typelist = $obj->typeSelect();
?>
<form name="editfrm" action="?m=resources" method="post">
<input type="hidden" name="dosql" value="do_resource_aed" />
<input type="hidden" name="resource_id" value="<?php echo dPformSafe($resource_id);?>" />
<table cellspacing="1" cellpadding="1" border="0" width="100%" class="std">
<tr>
<td align='center' >
  <table>
	<tr><td align='right'><?php echo $AppUI->_('Resource ID'); ?></td>
  <td align='left'><input type='text' size=15 maxlength=64 name=resource_key
    value="<?php echo dPformSafe($obj->resource_key);?>" /></td></tr>
  <tr><td align='right'><?php echo $AppUI->_('Resource Name'); ?></td>
  <td align='left'><input type='text' size=30 maxlength=255 name=resource_name
    value="<?php echo dPformSafe($obj->resource_name);?>" /></td></tr>
  <tr><td align='right'><?php echo $AppUI->_('Type'); ?></td>
  <td align='left'><?php echo arraySelect($typelist, 'resource_type', 'class=select', $obj->resource_type, true);?>
  </td></tr>
  <tr><td align='right'><?php echo $AppUI->_('Maximum Allocation Percentage'); ?></td>
  <td><input type='text' size=5 maxlength=5 value='<?php 
    if ($obj->resource_max_allocation)
      echo dPformSafe($obj->resource_max_allocation);
    else
      echo '100'; ?>'
    name='resource_max_allocation'></td></tr>
  <tr><td align='right'><?php echo $AppUI->_('Notes'); ?></td>
  <td><textarea name='resource_note' cols=40 rows=5 ><?php echo dPformSafe($obj->resource_note);?></textarea>
  </table>
</td>
</tr>
<tr>
  <td>
    <input type="button" value="<?php echo $AppUI->_('back');?>" 
    class="button" onclick="javascript:history.back(-1);" />
  </td>
  <td align="right">
    <input type="button" value="<?php echo $AppUI->_('submit');?>" 
    class="button" onclick="submitIt(document.editfrm);" />
  </td>
</tr>
</table>
</form>

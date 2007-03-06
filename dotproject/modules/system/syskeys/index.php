<?php /* SYSKEYS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$AppUI->savePlace();

// pull all the key types
$sql = "SELECT syskey_id,syskey_name FROM syskeys ORDER BY syskey_name";
$keys = arrayMerge( array( 0 => '- Select Type -' ), db_loadHashList( $sql ) );

$sql = "SELECT * FROM syskeys, sysvals WHERE sysval_key_id = syskey_id ORDER BY sysval_title";
$values = db_loadList( $sql );

$sysval_id = isset( $_GET['sysval_id'] ) ? $_GET['sysval_id'] : 0;

$titleBlock = new CTitleBlock( 'System Lookup Values', 'myevo-weather.png', $m, "$m.$u.$a" );
$titleBlock->addCrumb( "?m=system", "System Admin" );
$titleBlock->show();
?>
<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function delIt(id) {
	if (confirm( 'Are you sure you want to delete this?' )) {
		f = document.sysValFrm;
		f.del.value = 1;
		f.sysval_id.value = id;
		f.submit();
	}
}
<?php } ?>
</script>

<table border="0" cellpadding="2" cellspacing="1" width="100%" class="tbl">
<tr>
	<th>&nbsp;</th>
	<th><?php echo $AppUI->_('Key Type');?></th>
	<th><?php echo $AppUI->_('Title');?></th>
	<th colspan="2"><?php echo $AppUI->_('Values');?></th>
	<th>&nbsp;</th>
</tr>
<?php

function showRow($id=0, $key=0, $title='', $value='') {
	GLOBAL $canEdit, $sysval_id, $CR, $AppUI, $keys;
	$s = '<tr>'.$CR;
	if ($sysval_id == $id && $canEdit) {
	// edit form
		$s .= '<form name="sysValFrm" method="post" action="?m=system&u=syskeys&a=do_sysval_aed">'.$CR;
		$s .= '<input type="hidden" name="del" value="0" />'.$CR;
		$s .= '<input type="hidden" name="sysval_id" value="'.$id.'" />'.$CR;

		$s .= '<td>&nbsp;</td>';
		$s .= '<td valign="top">'.arraySelect( $keys, 'sysval_key_id', 'size="1" class="text"', $key).'</td>';
		$s .= '<td valign="top"><input type="text" name="sysval_title" value="'.dPformSafe($title).'" class="text" /></td>';
		$s .= '<td valign="top"><textarea name="sysval_value" class="small" rows="5" cols="40">'.$value.'</textarea></td>';
		$s .= '<td><input type="submit" value="'.$AppUI->_($id ? 'edit' : 'add').'" class="button" /></td>';
		$s .= '<td>&nbsp;</td>';
	} else {
		$s .= '<td width="12" valign="top">';
		if ($canEdit) {
			$s .= '<a href="?m=system&u=syskeys&sysval_id='.$id.'" title="'.$AppUI->_('edit').'">'
				. dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' )
				. '</a>';
			$s .= '</td>'.$CR;
		}
		$s .= '<td valign="top">'.$keys[$key].'</td>'.$CR;
		$s .= '<td valign="top">'.dPformSafe($title).'</td>'.$CR;
		$s .= '<td valign="top" colspan="2">'.$value.'</td>'.$CR;
		$s .= '<td valign="top" width="16">';
		if ($canEdit) {
			$s .= '<a href="javascript:delIt('.$id.')" title="'.$AppUI->_('delete').'">'
				. dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' )
				. '</a>';
		}
		$s .= '</td>'.$CR;
	}
	$s .= '</tr>'.$CR;
	return $s;
}

// do the modules that are installed on the system
$s = '';
foreach ($values as $row) {
	echo showRow( $row['sysval_id'], $row['sysval_key_id'], $row['sysval_title'], $row['sysval_value'] );
}
// add in the new key row:
if ($sysval_id == 0) {
	echo showRow();
}
?>
</table>

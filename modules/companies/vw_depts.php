<?php /* COMPANIES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

##
##	Companies: View Projects sub-table
##

GLOBAL $AppUI, $company_id, $canEdit;

$q  = new DBQuery;
$q->addTable('departments','dept');
$q->addQuery('dept.*, COUNT(c.contact_department) as dept_users');
$q->addJoin('contacts', 'c', 'c.contact_department = dept.dept_id');
$q->addWhere('dept.dept_company = '.$company_id);
$q->addGroup('dept.dept_id');
$q->addOrder('dept.dept_parent, dept.dept_name');
$sql = $q->prepare();
$q->clear();

// function renamed to avoid naming clash
function showchilddept_comp(&$a, $level=0) {
	global $AppUI;
	$s = ('<td><a href="./index.php?m=departments&amp;a=addedit&amp;dept_id='
	      . $a['dept_id'] . '" title="'.$AppUI->_('edit').'">'
	      . dPshowImage('./images/icons/stock_edit-16.png', 16, 16, '') . '<a></td><td>');

	for ($y=0; $y < $level; $y++) {
		$s .= dPshowImage(('./images/' . (($y+1 == $level) ? 'corner-dots.gif' : 'shim.gif')),
		                  16, 12, '');
	}

	$s .= ('<a href="?m=departments&amp;a=view&amp;dept_id=' . $a['dept_id'] . '">'
	       . $a['dept_name'] . '</a>');
	$s .= '</td>';
	$s .= '<td align="center">' . (($a['dept_users']) ? $a['dept_users'] : '') . '</td>';

	echo '<tr>' . $s . '</tr>';
}

// function renamed to avoid naming clash
function findchilddept_comp(&$tarr, $parent, $level=0) {
	$level = $level+1;
	$n = count($tarr);
	for ($x=0; $x < $n; $x++) {
		if ($tarr[$x]['dept_parent'] == $parent
		    && $tarr[$x]['dept_parent'] != $tarr[$x]['dept_id']) {
			showchilddept_comp($tarr[$x], $level);
			findchilddept_comp($tarr, $tarr[$x]['dept_id'], $level);
		}
	}
}
/* main HTML starts below this */
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl" summary="view departments">
<thead>
  <tr>
<?php
$rows = db_loadList($sql, NULL);
if (count($rows)) {
	$s = '<th>&nbsp;</th>' . PHP_EOL;
	$s .= '<th width="100%">'.$AppUI->_('Name').'</th>' . PHP_EOL;
	$s .= '<th>'.$AppUI->_('Users').'</th>' . PHP_EOL;
} else {
	$s .= '<td>' . $AppUI->_('No data available') . '</td>' . PHP_EOL;
}
echo $s;
?>
  </tr>
</thead>
<tbody>
<?php
foreach ($rows as $row) {
	if ($row['dept_parent'] == 0) {
		showchilddept_comp($row);
		findchilddept_comp($rows, $row['dept_id']);
	}
}
?>
</tbody>
<tfoot>
  <tr>
    <td colspan="3" nowrap="nowrap" rowspan="99" valign="top">
<?php
if ($canEdit) {
	echo ('<input type="button" class="button" value="' . $AppUI->_('new department')
	      . '" onclick="javascript:window.location='
		  . '\'./index.php?m=departments&amp;a=addedit&amp;company_id=' . $company_id . '\';" />');
} ?>
    </td>
  </tr>
</tfoot>
</table>

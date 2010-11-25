<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (!(getPermission($m, 'view'))) {
	$AppUI->redirect('m=public&a=access_denied');
}
if (!(getPermission('users', 'view'))) {
	$AppUI->redirect('m=public&a=access_denied');
}

$AppUI->savePlace();

if (isset($_GET['tab'])) {
    $AppUI->setState('UserIdxTab', $_GET['tab']);
}
$tab = (($AppUI->getState('UserIdxTab') !== NULL) ? $AppUI->getState('UserIdxTab') : 0);

if (isset($_GET['stub'])) {
    $AppUI->setState('UserIdxStub', $_GET['stub']);
    $AppUI->setState('UserIdxWhere', '');
} else if (isset($_POST['where'])) { 
    $AppUI->setState('UserIdxWhere', $_POST['where']);
    $AppUI->setState('UserIdxStub', '');
}
$stub = $AppUI->getState('UserIdxStub');
$where = $AppUI->getState('UserIdxWhere');

$valid_ordering = array(
	'user_username',
	'contact_last_name',
	'contact_company',
	'date_time_in',
	'user_ip',
);
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
    $AppUI->setState('UserIdxOrderby', $_GET['orderby']);
}
$orderby = (($AppUI->getState('UserIdxOrderby')) ? $AppUI->getState('UserIdxOrderby') 
            : 'user_username');
$orderby = (($tab == 3 || ($orderby != 'date_time_in' && $orderby != 'user_ip')) 
            ? $orderby : 'user_username');

$q = new DBQuery;

// Pull First Letters
$let = ":";

$q->addTable('users','u');
$q->addJoin('contacts', 'con', 'con.contact_id = u.user_contact');
$q->addQuery('DISTINCT UPPER(SUBSTRING(u.user_username, 1, 1)) AS L' 
			 . ', UPPER(SUBSTRING(con.contact_first_name, 1, 1)) AS CF' 
			 . ', UPPER(SUBSTRING(con.contact_last_name, 1, 1)) AS CL');
$arr = $q->loadList();
foreach ($arr as $L) {
	foreach ($L as $v) {
		if (empty ($let)) {
			$let .= $v;
		} else {
			$let .= (mb_strpos($let, $v) === false) ? $v : '';
		}
	}
}
$q->clear();

$a2z = "\n" . '<table cellpadding="2" cellspacing="1" border="0">';
$a2z .= "\n<tr>";
$a2z .= '<td width="100%" align="right">' . $AppUI->_('Show'). ': </td>';
$a2z .= '<td><a href="?m=admin&amp;stub=0">' . $AppUI->_('All') . '</a></td>';
for ($c=65; $c < 91; $c++) {
	$cu = chr($c);
	$cell = ((mb_strpos($let, $cu) > 0) 
	         ? '<a href="?m=admin&amp;stub=' . $cu . '">' . $cu . '</a>' 
	         : '<font color="#999999">' . $cu . '</font>');
	$a2z .= "\n\t<td>" . $cell . '</td>';
}
$a2z .= "\n</tr>\n</table>";

// setup the title block
$titleBlock = new CTitleBlock('User Management', 'helix-setup-users.png', $m, "$m.$a");

$where = dPformSafe($where);

$titleBlock->addCell(('<form action="index.php?m=admin" method="post">' 
                      . '<input type="text" name="where" class="text" size="10" value="' . $where 
                      . '" /> <input type="submit" value="' . $AppUI->_('search') 
                      . '" class="button" /></form>'),	'',	'', '');

$titleBlock->addCell($a2z);
$titleBlock->show();

?>
<script language="javascript" type="text/javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
?>
function delMe(x, y) {
	if (confirm("<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('User', UI_OUTPUT_JS);?> " + y + "?")) {
		document.frmDelete.user_id.value = x;
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<?php
$extra = '<td align="right" width="100%"><input type="button" class=button value="'.$AppUI->_('add user').'" onclick="javascript:window.location=\'./index.php?m=admin&amp;a=addedituser\';" /></td>';

// tabbed information boxes
$tabBox = new CTabBox('?m=admin', (DP_BASE_DIR . '/modules/admin/'), $tab);
$tabBox->add('vw_active_usr', 'Active Users');
$tabBox->add('vw_inactive_usr', 'Inactive Users');
$tabBox->add('vw_usr_log', 'User Log');
if ($canEdit && $canDelete) {
	$tabBox->add('vw_usr_sessions', 'Active Sessions');
}
$tabBox->show($extra);

?>

<form name="frmDelete" action="./index.php?m=admin" method="post">
	<input type="hidden" name="dosql" value="do_user_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="user_id" value="0" />
</form>

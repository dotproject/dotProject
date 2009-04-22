<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

global $showEditCheckbox, $tasks, $priorities;
global $m, $a, $date, $min_view, $other_users, $showPinned, $showArcProjs, $showHoldProjs;
global $showDynTasks, $showLowTasks, $showEmptyDate, $user_id;

$q = new DBQuery;
$canDelete = getPermission($m, 'delete');
?>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<form name="form_buttons" method="post" action="index.php?<?php echo "m=$m&a=$a&date=$date";?>">
<input type="hidden" name="show_form" value="1" />
<tr>
	<td width="50%">
		<?php
	if ($other_users) {
		$q->addTable('users', 'u');
		$q->innerJoin('contacts', 'c', 'c.contact_id = u.user_contact');
		$q->addQuery('u.user_id, u.user_username, c.contact_first_name, c.contact_last_name');
		$q->addOrder('contact_last_name');
		$usersql = $q->prepare();
		$q->clear();
		echo $AppUI->_('Show Todo for:'); 
?>
		<select name="show_user_todo" onchange="document.form_buttons.submit()">
		<?php
		if ($rows = db_loadList( $usersql, NULL )) {
			foreach ($rows as $row) {
				$selected = (($user_id == $row['user_id']) ? ' selected="selected"' : '');
				echo ('<option value="' . $row['user_id'] . '"' . $selected . '>' 
				      . $row['contact_last_name'] . ', ' . $row['contact_first_name'] 
				      . '</option>' . "\n");
				
			}
		}
	}
	?>
		</select>
	</td>
</tr>
</form>
</table>
<?php 
$min_view = true;
include DP_BASE_DIR . '/modules/tasks/viewgantt.php';
?>

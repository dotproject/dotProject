<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

global $showEditCheckbox, $tasks, $priorities;
GLOBAL $m, $a, $date, $min_view, $other_users, $showPinned, $showArcProjs, $showHoldProjs, $showDynTasks, $showLowTasks, $showEmptyDate, $user_id;
$perms =& $AppUI->acl();
$canDelete = $perms->checkModuleItem($m, 'delete');
?>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<form name="form_buttons" method="post" action="index.php?<?php echo "m=$m&a=$a&date=$date";?>">
<input type="hidden" name="show_form" value="1" />
<tr>
	<td width="50%">
	<?php
	if ($other_users) {
		echo $AppUI->_("Show Todo for:").'<select name="show_user_todo" onchange="document.form_buttons.submit()">';

                $usersql = "
                SELECT user_id, user_username, contact_first_name, contact_last_name
                FROM users, contacts
                WHERE user_contact = contact_id
		ORDER BY contact_last_name
                ";

		
                if (($rows = db_loadList( $usersql, NULL )))
                {
                        foreach ($rows as $row)
                        {
                                if ( $user_id == $row["user_id"])
                                        echo "<OPTION VALUE='".$row["user_id"]."' SELECTED>".$row["contact_last_name"].', '.$row["contact_first_name"];
                                else
                                        echo "<OPTION VALUE='".$row["user_id"]."'>".$row["contact_last_name"].', '.$row["contact_first_name"];
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

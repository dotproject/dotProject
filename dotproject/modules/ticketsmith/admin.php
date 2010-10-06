<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/* $Id$ */

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'modules/ticketsmith/common.inc.php');

$db = new DBQuery;

/* determine page function */
switch ($action) {
    case "create":
        $fields = array("name", "email", "username", "password", "signature");
        for ($loop = 0; $loop < count($fields); $loop++) {
            $values[] = "'" . escape_string($$fields[$loop]) . "'";
        }
        $fields_string = join(", ", $fields);
        $values_string = join(", ", $values);
		$db->addTable('users');
		$db->addInsert($fields_string, $values_string);
		$db->exec();
        $title = "User Created";
        $content = "<tr><td align=\"center\" colspan=\"3\">The user has been created.</td></tr>\n";
        break;
    case "Apply changes":
        $fields = array("name", "email", "password", "signature");
        for ($loop = 0; $loop < count($fields); $loop++) {
            $update_fields[] = $fields[$loop] . " = '" . escape_string($$fields[$loop]) . "'";
        }
        $update_string = join(", ", $update_fields);
		$db->addTable('users');
		$db->addUpdate($fields, $update_fields, true);
		$db->addWhere("id='" . $id . "'");
		$db->exec();
        $title = "User Updated";
        $content = "<tr><td align=\"center\" colspan=\"3\">The user profile has been updated.</td></tr>\n";
        break;
    case "Delete this user":
		$db->addTable('users');
		$db->addWhere("id='" . $id . "'");
		$db->setDelete();
		$db->exec();
        $title = "User Deleted";
        $content = "<tr><td align=\"center\" colspan=\"3\">The user has been deleted.</td></tr>\n";
        break;
    case "new":
        $title = "Create User";
		$content = "<tr><td><strong>Name</strong></td><td colspan=\"2\"><font size=\"-1\"><input type=\"text\" name=\"name\" size=\"20\" /></font></td></tr>\n";
		$content .= "<tr><td><strong>Email</strong> </td><td colspan=\"2\"><font size=\"-1\"><input type=\"text\" name=\"email\" size=\"20\" /></font></td></tr>\n";
		$content .= "<tr><td><strong>Username</strong></td><td colspan=\"2\"><font size=\"-1\"><input type=\"text\" name=\"username\" size=\"20\" /></font></td></tr>\n";
		$content .= "<tr><td><strong>Password</strong></td><td colspan=\"2\"><font size=\"-1\"><input type=\"password\" name=\"password\" size=\"20\" /></font></td></tr>\n";
        $content .= "<tr><td><strong>Signature</strong></td><td colspan=\"2\"><tt><textarea name=\"signature\" cols=\"72\" rows=\"3\"></textarea></tt></td></tr>\n";
		$content .= "<tr><td><br /></td><td colspan=\"2\"><font size=\"-1\"><input type=\"submit\" value=\"Create user\" /></font></td></tr>\n";
		$content .= "<input type=\"hidden\" name=\"action\" value=\"create\" />\n";
        break;
    case "edit":
        $title = "Edit User";
        $user_info = query2hash("SELECT * FROM ".dPgetConfig('dbprefix','')."users WHERE id = '$id'");
		$content = "<tr><td><strong>Name</strong></td><td colspan=\"2\"><font size=\"-1\"><input type=\"text\" name=\"name\" size=\"20\" value=\"" . $user_info["name"] . "\" /></font></td></tr>\n";
		$content .= "<tr><td><strong>Email</strong> </td><td colspan=\"2\"><font size=\"-1\"><input type=\"text\" name=\"email\" size=\"20\" value=\"" . $user_info["email"] . "\" /></font></td></tr>\n";
		$content .= "<tr><td><strong>Password</strong></td><td colspan=\"2\"><font size=\"-1\"><input type=\"password\" name=\"password\" size=\"20\" value=\"" . $user_info["password"] . "\" /></font></td></tr>\n";
        $content .= "<tr><td><strong>Signature</strong></td><td colspan=\"2\"><tt><textarea name=\"signature\" cols=\"72\" rows=\"3\">" . $user_info["signature"] . "</textarea></tt></td></tr>\n";
		$content .= "<tr><td><br /></td><td colspan=\"2\"><font size=\"-1\"><input type=\"submit\" name=\"action\" value=\"Apply changes\" /> <input type=\"submit\" name=\"action\" value=\"Delete this user\" /></font></td></tr>\n";
		$content .= "<input type=\"hidden\" name=\"id\" value=\"" . $user_info["id"] . "\" />\n";
		$content .= "<input type=\"hidden\" name=\"old_password\" value=\"" . $user_info["password"] . "\" />\n";
        break;
    default:
        $title = "User Administration";
        $content = "<tr><td align=\"center\"><strong>Edit</strong></td><td><strong>User</strong></td><td><strong>Email</strong></td></tr>\n";
        $result = do_query("SELECT id, name, email FROM ".dPgetConfig('dbprefix','')."users WHERE username != 'admin' ORDER BY id");
        while ($row = result2hash($result)) {
            $content .= "<tr>";
			$content .= "<td align=\"center\"><a href=\"?a=ticketsmith&amp;a=edit&amp;id=" . $row["id"] . "\"><img src='modules/ticketsmith/images/posticon.gif' border=\"0\" /></a></td>";
            $content .= "<td>" . $row["name"] . "</td>";
            $content .= "<td>" . $row["email"] . "</td>";
            $content .= "</tr>\n";
        }
}

/* start form */
print("<form name='ticketform' action=\"$PHP_SELF\" method=\"post\">\n");

/* start table */
print("<table class='maintable' bgcolor='#eeeeee'>\n");
print("<tr>\n");
print("<td colspan=\"3\" align=\"center\" bgcolor=\"" . $CONFIG["heading_color"] . "\">\n");
print("<div class=\"heading\">$title</div>\n");
print("</td>\n");
print("</tr>\n");

/* output content */
print($content);

/* output actions */
print("<tr>\n");
print("<td align=\"left\" valign=\"top\"><br /></td>");
print("<td align=\"left\" valign=\"top\" colspan=\"2\">\n");
print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
print("<tr><td align=\"left\">");
if ($action) {
    print("<a href=\"$PHP_SELF\">Return to user list</a>");
}
else {
    print("<a href=\"$PHP_SELF?action=new\">Create new user</a>");
}
print("</td>\n");
print("<td align=\"right\"><a href=\"$PHP_SELF?logout=1\">Logout</a></td></tr>\n");
print("</table>\n");
print("</td>");
print("</tr>\n");

/* end table */
print("</table>\n");

/* end form */
print("</form>\n");
?>

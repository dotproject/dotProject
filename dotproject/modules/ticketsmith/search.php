<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

if (!$canRead) {
	$AppUI->redirect("m=public&a=access_denied");
}


require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'/modules/ticketsmith/common.inc.php');
if (empty($search_pattern)) $search_pattern = "";
if (empty($search_field)) $search_field = "";
if (empty($search_depth)) $search_depth = "";
if (empty($sort_column)) $sort_column = "";
/* set title */
$title = "Search Tickets";

/* start form */
print("<form name='ticketform' action='?m=ticketsmith&amp;a=search' method='post'>\n");

/* start table */
print("<table class='maintable' bgcolor='#eeeeee' width='95%'>\n");
print("<tr>\n");
print("<td colspan='2' align='center' bgcolor='#878676' width='100%'>\n");
print("<div class=\"heading\">".$AppUI->_($title)."</div>\n");
print("</td>\n</tr>\n");

/* field select */
print("<tr>\n");
print("<td align=\"right\"><strong>".$AppUI->_('Field')."</strong></td>\n");
print("<td>");
$field_choices = array("author"  => $AppUI->_("Author"),
                       "body"    => $AppUI->_("Body"),
                       "subject" => $AppUI->_("Subject"));


$field_selectbox = create_selectbox("search_field", $field_choices, $search_field);
print("$field_selectbox\n");
print("</td>\n");
print("</tr>\n");

/* pattern select */
$search_pattern = dPformSafe($search_pattern);
print("<tr>\n");
print("<td align=\"right\"><strong>".$AppUI->_('Pattern')."</strong></td>\n");
print("<td><input type=\"text\" name=\"search_pattern\" value=\"$search_pattern\"></td>\n");
print("</tr>\n");

/* depth select */
print("<tr>\n");
print("<td align=\"right\"><strong>".$AppUI->_('Depth')."</strong></td>\n");
print("<td>");
$depth_choices = array("All"     => $AppUI->_("All Tickets"),
                       "Open"    => $AppUI->_("Open Parents"),
                       "Closed"  => $AppUI->_("Closed Parents"),
                       "Deleted" => $AppUI->_("Deleted Parents"),
                       "Child"   => $AppUI->_("Followups")." &amp; ".$AppUI->_("Comments"));

$depth_selectbox = create_selectbox("search_depth", $depth_choices, $search_depth);
print("$depth_selectbox\n");
print("</td>\n");
print("</tr>\n");

/* sort select */
print("<tr>\n");
print("<td align=\"right\"><strong>".$AppUI->_('Sort By')."</strong></td>\n");
print("<td>");
$sort_choices = array("ticket"     => $AppUI->_("Ticket"),
                      "author"     => $AppUI->_("Author"),
                      "subject"    => $AppUI->_("Subject"),
                      "timestamp"  => $AppUI->_("Date"),
                      "activity"   => $AppUI->_("Activity"),
                      "type"       => $AppUI->_("Type"),
                      "priority"   => $AppUI->_("Priority"),
                      "assignment" => $AppUI->_("Owner"));

$sort_selectbox = create_selectbox("sort_column", $sort_choices, $sort_column);
print($sort_selectbox);
print(" <input type=\"radio\" name=\"sort_direction\" value=\"ASC\" /> ".$AppUI->_('Ascending'));
print(" <input type=\"radio\" name=\"sort_direction\" value=\"DESC\" checked='checked' /> ".$AppUI->_('Descending'));
print("</td>\n");
print("</tr>\n");

/* submit button */
print("<tr>\n");
print("<td><br /></td>\n");
print('<td><input type="submit" value="'.$AppUI->_('Search').'" /></td>');
print("</tr>\n");

/* output footer */
print("<tr>\n");
print("<td><br /></td>\n");
print("<td><a href='?m=ticketsmith'>".$AppUI->_('Return to ticket list')."</a></td>\n");
print("</tr>\n");

/* end table */
print("</table>\n");

if ($search_pattern) {

    /* set fields */
    $fields = array("columns"  => array("ticket", "author", "subject", "timestamp", "type"),
                    "types"    => array("view", "email", "normal", "elapsed_date", "normal"),
                    "aligns"   => array("center", "left", "left", "left", "center"));
    
    /* start results table */
    print("<p>\n");
    print("<table width=\"95%\" border=\"1\" cellspacing=\"5\" cellpadding=\"5\">\n");

    /* form search query */
    $select_columns = join(", ", $fields["columns"]);
    $search_pattern = "%" . escape_string($search_pattern) . "%";
    $query = "SELECT $select_columns FROM ".dPgetConfig('dbprefix','')."tickets WHERE $search_field LIKE '$search_pattern'";
    if ($search_depth == "Child") {
        $query .= " AND parent != 0";
    }
    else if ($search_depth != "All") {
        $query .= " AND type = '$search_depth'";
    }
    $query .= " ORDER BY $sort_column $sort_direction";
    
    /* perform search */
    $result = do_query($query);
    
    /* display results */
    $result_count = number_rows($result);
    if ($result_count) {
        print("<tr><td colspan=\"5\">".$AppUI->_('There were')." ".$result_count." ".$AppUI->_('results')." ".$AppUI->_('in the given search').".</td></tr>\n");
        while ($row = result2hash($result)) {
            print("<tr>");
            for ($loop = 0; $loop < count($fields["columns"]); $loop++) {
                print("<td align=\"" . $fields["aligns"][$loop] . "\">");
		if ($loop==4) {
                print(format_field($AppUI->_($row[$fields["columns"][$loop]]), $fields["types"][$loop]));
		}
		else {
		print(format_field($row[$fields["columns"][$loop]], $fields["types"][$loop]));
		}
                print("</td>");
            }
            print("</tr>\n");
        }
    }
    else {
        print("<tr><td>".$AppUI->_('There were')." ".$AppUI->_('no results')." ".$AppUI->_('in the given search').".</td></tr>\n");
    }
    
    /* end results table */
    print("</table>\n");

}

/* end form */
print("</form>\n");
?>

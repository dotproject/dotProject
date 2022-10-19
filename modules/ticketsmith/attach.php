<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

if (!$canEdit) {
	$AppUI->redirect("m=public&a=access_denied");
}

$ticket = (int)dPgetParam($_GET, 'ticket', '');
$dbprefix = dPgetConfig('dbprefix', '');

$titleBlock = new CTitleBlock('Link Ticket', 'gconf-app-icon.php', $m, "$m.$a");
$titleBlock->addCrumb("?m=ticketsmith", "tickets list");
$titleBlock->show();

require(DP_BASE_DIR."/modules/ticketsmith/config.inc.php");
require(DP_BASE_DIR."/modules/ticketsmith/common.inc.php");

/* setup table & database field stuff */
$fields = array("headings" => array("Link", "Author", "Subject", "Date",
                                    "Followup", "Status", "Priority", "Owner"),

                "columns"  => array("ticket", "author", "subject", "timestamp",
                                    "activity", "type", "priority", "assignment"),

                "types"    => array("doattach", "email", "normal", "open_date",
                                    "activity_date", "normal", "priority_view", "user"),

                "aligns"   => array("center", "left", "left", "left", "left",
                                    "center", "center", "center"));

/* set up defaults for viewing */
$type = @$type ? $type : "Open";
$column = @$column ? $column : "priority";
$direction = @$direction ? $direction : "DESC";
$offset = @$offset ? $offset : 0;
$limit = @$limit ? $limit : $CONFIG["view_rows"];


/* count tickets */
$q = new DBQuery();
$q->addTable('tickets');
$q->addQuery('COUNT(*) as rowcount');
$q->addWhere("parent = '0'");
$q->addWhere("ticket != " . $ticket);
if ($type != 'All') {
  $q->addWhere("type = '" . $type . "'");
}
$ticket_count = $q->loadResult();

/* paging controls */
if (($offset + $limit) < $ticket_count) {
    $page_string = ($offset + 1) . " to " . ($offset + $limit) . " of $ticket_count";
}
else {
    $page_string = ($offset + 1) . " to $ticket_count of $ticket_count";
}

/* start table */
$title = "Assign ticket to parent";
?>
<table class="tbl" width="100%">
<tr>
	<td colspan="<?php echo count($fields["headings"]);?>" align="center">
		<table width="100%" border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td width="33%"></td>
			<td width="34%" align="center"><strong><?php echo $AppUI->_($title);?></strong></td>
			<td width="33%" align="right" valign="middle">
<?php

if ($ticket_count > $limit) {
    if ($offset - $limit >= 0) {
        print("<a href='?m=ticketsmith&amp;type=$type&amp;column=$column&amp;direction=$direction&amp;offset="
				. ($offset - $limit) . "'><img src='modules/ticketsmith/images/ltwt.gif' border='0' /></a> | \n");
    }
    print("$page_string\n");
    if ($offset + $limit < $ticket_count) {
        print(" | <a href='?m=ticketsmith&amp;type=$type&amp;column=$column&amp;direction=$direction&amp;offset="
				. ($offset + $limit) . "'><img src='modules/ticketsmith/images/rtwt.gif' border='0' /></a>\n");
    }
}
?>

			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
/* form query */
$q->clear();
$q->addTable('tickets');
$q->addQuery($fields['columns']);
if ($type == "My") {
    $query .= "type = 'Open' AND (assignment = '" . $user_cookie . "' OR assignment = '0') AND ";
    $q->addWhere("type = 'Open'");
    $q->addWhere("(assignment = '" . $user_cookie . "' OR assignment = '0')");
}
else if ($type != "All") {
    $q->addWhere("type = '" . $type . "'");
}
$q->addWhere("ticket != " . $ticket);
$q->addWhere("parent = '0'");
$q->addOrder(urlencode($column) . " " . $direction);
$q->setLimit($limit, $offset);
$q->includeCount();
$result = $q->loadList();
$parent_count = $q->foundRows();

/* output tickets */
if ($parent_count) {
    print("<tr>\n");
    for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
        print("<td  align=" . $fields["aligns"][$loop] . ">");
        print("<a href='?m=ticketsmith&amp;type=$type");
        print("&amp;column=" . $fields["columns"][$loop]);
        if ($column != $fields["columns"][$loop]) {
            $new_direction = "ASC";
        }
        else {
            if ($direction == "ASC") {
                $new_direction = "DESC";
            }
            else {
                $new_direction == "ASC";
            }
        }
        print("&amp;direction=$new_direction");
        print("'><b>" . $AppUI->_($fields["headings"][$loop]) . "</b></a></td>\n");
    }
    print("</tr>\n");
    foreach ($result as $row) {
        print("<tr height='25'>\n");
        for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
            print("<td  bgcolor='white' align='" . $fields["aligns"][$loop] . "'>\n");
	        print(format_field($row[$fields["columns"][$loop]], $fields["types"][$loop], $ticket) . "\n");
            print("</td>\n");
        }
        print("</tr>\n");
    }
}
 else {
    print("<tr style='height:25px;'>\n");
    print("<td align='center' colspan='" . count($fields["headings"]) . "'>\n");
    print($AppUI->_('There are no')." ");
    print($type == "All" ? "" : mb_strtolower($AppUI->_($type)) . " ");
    print($AppUI->_('tickets').".\n");
    print("</td>\n");
    print("</tr>\n");
}

/* output action links */
print("<tr>\n");
print("<td><br></td>\n");
print("<td colspan='" . (count($fields["headings"]) - 1) . "' align='right'>\n");
print("<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n");
print("<tr style='height:25px;'><td align='left'>");
$types = array("My","Open","Closed","Deleted","All");
for ($loop = 0; $loop < count($types); $loop++) {
    $toggles[] = "<a href='?m=ticketsmith&amp;type=" . $types[$loop] . "'>"
				. $AppUI->_($types[$loop]) . "</a>";
}
print(join(" | ", $toggles));
print(" ".$AppUI->_('Tickets')."</td>\n");
if ($type == "Deleted" && $parent_count) {
    print("<td align='center'><a href='?m=ticketsmith&amp;type=Deleted&amp;action=expunge'>"
			.$AppUI->_('Expunge Deleted')."</a></td>");
}
print("<td align='right'><a href='?m=ticketsmith&amp;a=search'>".$AppUI->_('Search')."</a> |
<a href='?m=ticketsmith&amp;type=$type'>".$AppUI->_('Back to top')."</a></td></tr>\n");
print("</table>\n");
print("</td>\n");
print("</tr>\n");

/* end table */
print("</table>\n");

/* end page */
?>

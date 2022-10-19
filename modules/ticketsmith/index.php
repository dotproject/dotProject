<?php  /* TICKETSMITH $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (!$canAccess) {
	$AppUI->redirect("m=public&a=access_denied");
}

// setup the title block
$titleBlock = new CTitleBlock('Trouble Ticket Management', 'gconf-app-icon.png', $m, "$m.$a");
if ($canAuthor) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new ticket').'">', '',
		'<form name="ticketform" action="?m=ticketsmith&amp;a=post_ticket" method="post">', '</form>'
	);
}
$titleBlock->show();

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'/modules/ticketsmith/common.inc.php');

$column = $CONFIG["order_by"];
$direction = $CONFIG["message_order"];
$offset = 0;
$limit = $CONFIG["view_rows"];

$type = dPgetCleanParam($_GET, 'type', '');
$column = dPgetCleanParam($_GET, 'column', $column);
$direction = dPgetCleanParam($_GET, 'direction', $direction);
$offset = dPgetCleanParam($_GET, 'offset', $offset);
$action = dPgetCleanParam($_REQUEST, 'action', null);

dprint(__FILE__, __LINE__, 11, "[DEBUG] Column is `$column`");

if (empty($type) || $type == '') {  // better safe than sorry (gwyneth 20210420)
	if ($AppUI->getState("ticket_type")) {
		$type = $AppUI->getState("ticket_type");
	} else {
		$type = "Open";
	}
} else {
	$AppUI->setState("ticket_type", $type);
}

/* expunge deleted tickets */
$q = new DBQuery();
if (@$action == "expunge") {
    $q->clear();
    $q->addTable('tickets');
    $q->addQuery('ticket');
    $q->addWhere("type = 'Deleted'");
    $deleted_parents = $q->loadColumn();
    for ($loop = 0; $loop < count($deleted_parents); $loop++) {
        $q->clear();
        $q->setDelete('tickets');
        $q->addWhere("ticket = '" . $deleted_parents[$loop] . "' OR parent = '" . $deleted_parents[$loop] . "'");
        $q->exec();
    }
}

/* setup table & database field stuff */
if ($dPconfig['link_tickets_kludge']) {
	$fields = array("headings" => array("View", "Author", "Subject", "Date",
                                    "Followup", "Status", "Priority", "Owner", "Link"),

                "columns"  => array("ticket", "author", "subject", "timestamp",
                                    "activity", "type", "priority", "assignment", "ticket"),

                "types"    => array("view", "email", "normal", "open_date",
                                    "activity_date", "normal", "priority_view", "user", "attach"),

                "aligns"   => array("center", "left", "left", "left", "left",
                                    "center", "center", "center", "center"));
} else {
	$fields = array("headings" => array("View", "Author", "Subject", "Date",
                                    "Followup", "Status", "Priority", "Owner"),

                "columns"  => array("ticket", "author", "subject", "timestamp",
                                    "activity", "type", "priority", "assignment"),

                "types"    => array("view", "email", "normal", "open_date",
                                    "activity_date", "normal", "priority_view", "user"),

                "aligns"   => array("center", "left", "left", "left", "left",
                                    "center", "center", "center"));
}

/* set up defaults for viewing */
if ($type == "my") {
	$title = "My Tickets";
} else {
	$title = $type . " Tickets";
}

/* count tickets */
$q->clear();
$q->addTable('tickets');
$q->addQuery('COUNT(*) as rowcount');
$q->addWhere("parent = '0'");

if ($type != 'All') {
    $q->addWhere("type = '$type'");
}
$ticket_count = $q->loadResult();
dprint(__FILE__, __LINE__, 11, "[DEBUG] Ticket count: #" . $ticket_count . ".");
/* paging controls */
if (($offset + $limit) < $ticket_count) {
    $page_string = ($offset + 1) . " to " . ($offset + $limit) . " of " . $ticket_count;
}
else {
    $page_string = ($offset + 1) . " to " . $ticket_count . " of " . $ticket_count;
}

/* start table */
?>

<table class="tbl" width="100%">
<tr>
	<td colspan="<?php echo count($fields["headings"]) ?? 1;?>" align="center">
		<table width="100%" border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td width="33%"></td>
			<td width="34%" align="center"><strong><?php echo $AppUI->_($title);?></strong></td>
			<td width="33%" align="right" valign="middle">
<?php
if ($ticket_count > $limit) {
    if ($offset - $limit >= 0) {
        print("<a href='?m=ticketsmith&amp;type=" . $type . "&amp;column=" . $column . "&amp;direction=" . $direction . "&amp;offset="
				. ($offset - $limit) . "'><img src='images/navleft.gif' border='0' alt='Navigate to left' /></a> | \n");
    }
    print($AppUI->_("$page_string")."\n");
    if ($offset + $limit < $ticket_count) {
        print(" | <a href='?m=ticketsmith&amp;type=" . $type . "&amp;column=" . $column . "&amp;direction=" . $direction . "&amp;offset="
				. ($offset + $limit) . "'><img src='images/navright.gif' border='0' alt='Navigate to right' /></a>\n");
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
    $q->addWhere("type = 'Open'");
    $q->addWhere("(assignment = '" . $AppUI->user_id . "' OR assignment = '0')");
}
else if ($type != "All") {
    $q->addWhere("type = '" . $type . "'");
}
$q->addWhere("parent = '0'");
if (!empty($column)) {
  $q->addOrder(urlencode($column) . " " . $direction);
}
$q->setLimit($limit, $offset);
$q->includeCount();

/* do query */
$result = $q->loadList();
$parent_count = $q->foundRows();

/* output tickets */
if ($parent_count) {
    print("<tr>\n");
    for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
        print("<th align=" . $fields["aligns"][$loop] . ">");
        print("<a href=\"?m=ticketsmith&amp;type=" . $type);
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
        print("&amp;direction=" . $new_direction);
        print('" class="hdr">' . $AppUI->_($fields["headings"][$loop]) . "</a></th>\n");
    }
    print("</tr>\n");
    if (!empty($result)) {
      foreach ($result as $row) {
        print("<tr style='height:25px;'>\n");
        // make sure that $fields["headings"] exists and is not empty before counting it! (gwyneth 20210419)
        $total_fields = !empty($fields["headings"]) ? count($fields["headings"]) : 0;
        for ($loop = 0; $loop < $total_fields; $loop++) {
          print("<td bgcolor='white' align=" . $fields["aligns"][$loop] . ">\n");

  	    	//translate some information, some not
  	    	if ($fields["headings"][$loop] == "Status") {
      			print($AppUI->_(format_field($row[$fields["columns"][$loop]],
              $fields["types"][$loop],
              $row[$fields["columns"][0]])) . "\n");
      		}
      		else {
      	    print(format_field($row[$fields["columns"][$loop]],
              $fields["types"][$loop],
              $row[$fields["columns"][0]]) . "\n");
      		}
          print("</td>\n");
        }
        print("</tr>\n");
      }
    }
    else {
      print("<tr style='height:25px;' align='center' colspan='"
        . (!empty($fields["headings"]) ? count($fields["headings"]) : 1) // see comment above (gwyneth 20210501)
        . "'><td>Nothing to show!</td></tr>\n");
    }
}
else {
    print("<tr style='height:25px;'>\n");
    print("<td align='center' colspan='" . (!empty($fields["headings"]) ? count($fields["headings"]) : 1) . "'>\n");  // again: see my comment above (gwyneth 20210501)
    print($AppUI->_('There are no')." ");
    print($type == "All" ? "" : mb_strtolower($AppUI->_($type)) . " ");
    print($AppUI->_('tickets').".\n");
    print("</td>\n");
    print("</tr>\n");
}

/* output action links */
print("<tr>\n");
print("<td><br /></td>\n");
print("<td colspan='" . (!empty($fields["headings"]) ? (count($fields["headings"]) - 1) : 2) . "' align='right'>\n");
print("<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n");
print("<tr style='height:25px;'><td align='left'>");
$types = array("My","Open","Processing","Closed","Deleted","All");
for ($loop = 0; $loop < count($types); $loop++) {
    $toggles[] = "<a href='?m=ticketsmith&amp;type=" . $types[$loop] . "'>" . $AppUI->_($types[$loop]) . "</a>";
}
print(join(" | ", $toggles));
print(" ".$AppUI->_('Tickets')."</td>\n");
if ($type == "Deleted" && $parent_count) {
    print("<td align='center'><a href='?m=ticketsmith&amp;type=Deleted&amp;action=expunge'>".$AppUI->_('Expunge Deleted')."</a></td>");
}
print("<td align='right'>
<a href='?m=ticketsmith&amp;a=pdf&amp;type=" . $type . "&suppressHeaders=1'>" . $AppUI->_('Report as PDF') . "</a> |
<a href='?m=ticketsmith&amp;a=search'>" . $AppUI->_('Search') . "</a> |
<a href='?m=ticketsmith&amp;type=" . $type . "'>" .$AppUI->_('Back to top') . "</a></td></tr>\n");
print("</table>\n");
print("</td>\n");
print("</tr>\n");

/* end table */
print("</table>\n");

?>

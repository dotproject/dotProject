<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

if (!$canRead) {
	$AppUI->redirect("m=public&a=access_denied");
}

$ticket = dPgetParam($_GET, 'ticket', '');
$ticket_type = dPgetParam($_GET, 'ticket_type', '');

$type_toggle = dPgetParam($_POST, 'type_toggle', '');
$priority_toggle = dPgetParam($_POST, 'priority_toggle', '');
$assignment_toggle = dPgetParam($_POST, 'assignment_toggle', '');

// setup the title block
$titleBlock = new CTitleBlock('View Ticket', 'gconf-app-icon.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=ticketsmith", "tickets list");
$titleBlock->addCrumb("?m=ticketsmith&type=My", "my tickets");
$titleBlock->show();

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'/modules/ticketsmith/common.inc.php');


/* initialize fields */
if ($ticket_type == "Staff Followup" || $ticket_type == "Client Followup") {

    $title = $AppUI->_($ticket_type)." ".$AppUI->_('to Ticket')." #$ticket_parent";

    $fields = array("headings" => array("From", "To", "Subject", "Date", "Cc", "<br />"),
                    "columns"  => array("author", "recipient", "subject", "timestamp", "cc", "body"),
                    "types"    => array("email", "original_author", "normal", "elapsed_date", "email", "body"));

}
else if ($ticket_type == "Staff Comment") {

    $title = $AppUI->_($ticket_type)." ".$AppUI->_('to Ticket')." #$ticket_parent";

    $fields = array("headings" => array("From", "Date", "<br />"),
                    "columns"  => array("author", "timestamp", "body"),
                    "types"    => array("email", "elapsed_date", "body"));

}
else {

    $title = $AppUI->_('Ticket')." #$ticket";

    $fields = array('headings' => array('From', 'Subject', 'Date', 'Cc', 'Status',
                                        'Priority', 'Owner', 'Company', 'Project', '<br />'),

                    'columns'  => array('author', 'subject', 'timestamp', 'cc',
                                        'type', 'priority', 'assignment', 'ticket_company', 'ticket_project', 'body'),

                    'types'    => array('email', 'normal', 'elapsed_date', 'email',
                                        'status', 'priority_select', 'assignment', 'ticket_company', 'ticket_project', 'body'));
}

/* perform updates */
$orig_assignment = dPgetParam($_POST, 'orig_assignment', '');
$author = dPgetParam($_POST, 'author', '');
$priority = dPgetParam($_POST, 'priority', '');
$subject = dPgetParam($_POST, 'subject', '');

if (@$type_toggle || @$priority_toggle || @$assignment_toggle) {
    do_query("UPDATE tickets SET type = '$type_toggle', priority = '$priority_toggle', assignment = '$assignment_toggle' WHERE ticket = '$ticket'");

	//Emailing notifications.
	$change = ' ';
	if ($type_toggle)
		$change .= $AppUI->_('Status changed') . ' ';
	if ($priority_toggle)
		$change .= $AppUI->_('Priority changed') . ' ';
	if ($assignment_toggle)
		$change .= $AppUI->_('Assignment changed') . ' ';
		
	$boundary = "_lkqwkASDHASK89271893712893";
	$message = "--$boundary\n";
	$message .= "Content-disposition: inline\n";
	$message .= "Content-type: text/plain\n\n";
	$message .= $AppUI->_('Ticket Updated - ')  . $change . ".\n\n";
	$message .= "Ticket ID: $ticket\n";
	$message .= "Author   : $author\n";
	$message .= "Subject  : $subject\n";
	$message .= "View     : ".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket\n";
	$message .= "\n--$boundary\n";
	$message .= "Content-disposition: inline\n";
	$message .= "Content-type: text/html\n\n";
	$message .= "<html>\n";
	$message .= "<head>\n";
	$message .= "<style>\n";
	$message .= ".title {\n";
	$message .= "	FONT-SIZE: 18pt; SIZE: 18pt;\n";
	$message .= "}\n";
	$message .= "</style>\n";
	$message .= "<title>".$AppUI->_('Ticket Updated - ') . $change ."</title>\n";
	$message .= "</head>\n";
	$message .= "<body>\n";
	$message .= "\n";
	$message .= "<TABLE border=0 cellpadding=4 cellspacing=1>\n";
	$message .= "	<TR>\n";
	$message .= "	<TD valign=top><img src=".DP_BASE_URL."/images/icons/ticketsmith.gif alt= border=0 width=42 height=42></td>\n";
	$message .= "		<TD nowrap><span class=title>".$AppUI->_('Trouble Ticket Management -')  . $change ."</span></td>\n";
	$message .= "		<TD valign=top align=right width=100%>&nbsp;</td>\n";
	$message .= "	</tr>\n";
	$message .= "</TABLE>\n";
	$message .= "<TABLE width=600 border=0 cellpadding=4 cellspacing=1 bgcolor=#878676>\n";
	$message .= "	<TR>\n";
	$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>".$AppUI->_('Ticket ID').":</font></TD>\n";
	$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>$ticket</font></TD>\n";
	$message .= "	</tr>\n";
	$message .= "	<TR>\n";
	$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>".$AppUI->_('Author').":</font></TD>\n";
	$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>" . str_replace(">", "&gt;", str_replace("<", "&lt;", str_replace('"', '', $author))) . "</font></TD>\n";
	$message .= "	</tr>\n";
	$message .= "	<TR>\n";
	$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>".$AppUI->_('Subject').":</font></TD>\n";
	$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>$subject</font></TD>\n";
	$message .= "	</tr>\n";
	$message .= "	<TR>\n";
	$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>".$AppUI->_('View').":</font></TD>\n";
	$message .= "		<TD bgcolor=white nowrap><a href=\"".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket\"><font face=arial,sans-serif size=2>".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket</font></a></TD>\n";
	$message .= "	</tr>\n";
	$message .= "</TABLE>\n";
	$message .= "</body>\n";
	$message .= "</html>\n";
	$message .= "\n--$boundary--\n";

	$ticketNotification = dPgetSysVal('TicketNotify');
	if (count($ticketNotification) > 0) {
		mail($ticketNotification[$priority], $AppUI->_('Trouble ticket')." #$ticket ", $message, "From: " . $CONFIG['reply_to'] . "\nContent-type: multipart/alternative; boundary=\"$boundary\"\nMime-Version: 1.0");
	}

	if (@$assignment_toggle != @$orig_assignment)
	{
		$mailinfo = query2hash("SELECT contact_first_name, contact_last_name, contact_email from users u LEFT JOIN contacts ON u.user_contact = contact_id WHERE user_id = $assignment_toggle");

		if (@$mailinfo['contact_email']) {
			$boundary = "_lkqwkASDHASK89271893712893";
			$message = "--$boundary\n";
			$message .= "Content-disposition: inline\n";
			$message .= "Content-type: text/plain\n\n";
			$message .= $AppUI->_('Trouble ticket assigned to you') . ".\n\n";
			$message .= "Ticket ID: $ticket\n";
			$message .= "Author   : $author\n";
			$message .= "Subject  : $subject\n";
			$message .= "View     : ".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket\n";
			$message .= "\n--$boundary\n";
			$message .= "Content-disposition: inline\n";
			$message .= "Content-type: text/html\n\n";
			$message .= "<html>\n";
			$message .= "<head>\n";
			$message .= "<style>\n";
			$message .= ".title {\n";
			$message .= "	FONT-SIZE: 18pt; SIZE: 18pt;\n";
			$message .= "}\n";
			$message .= "</style>\n";
			$message .= "<title>".$AppUI->_('Trouble ticket assigned to you')."</title>\n";
			$message .= "</head>\n";
			$message .= "<body>\n";
			$message .= "\n";
			$message .= "<TABLE border=0 cellpadding=4 cellspacing=1>\n";
			$message .= "	<TR>\n";
			$message .= "	<TD valign=top><img src=".DP_BASE_URL."/images/icons/ticketsmith.gif alt= border=0 width=42 height=42></td>\n";
			$message .= "		<TD nowrap><span class=title>".$AppUI->_('Trouble Ticket Management')."</span></td>\n";
			$message .= "		<TD valign=top align=right width=100%>&nbsp;</td>\n";
			$message .= "	</tr>\n";
			$message .= "</TABLE>\n";
			$message .= "<TABLE width=600 border=0 cellpadding=4 cellspacing=1 bgcolor=#878676>\n";
			$message .= "	<TR>\n";
			$message .= "		<TD colspan=2><font face=arial,san-serif size=2 color=white>".$AppUI->_('Ticket assigned to you')."</font></TD>\n";
			$message .= "	</tr>\n";
			$message .= "	<TR>\n";
			$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>".$AppUI->_('Ticket ID').":</font></TD>\n";
			$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>$ticket</font></TD>\n";
			$message .= "	</tr>\n";
			$message .= "	<TR>\n";
			$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>".$AppUI->_('Author').":</font></TD>\n";
			$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>" . str_replace(">", "&gt;", str_replace("<", "&lt;", str_replace('"', '', $author))) . "</font></TD>\n";
			$message .= "	</tr>\n";
			$message .= "	<TR>\n";
			$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>".$AppUI->_('Subject').":</font></TD>\n";
			$message .= "		<TD bgcolor=white><font face=arial,san-serif size=2>$subject</font></TD>\n";
			$message .= "	</tr>\n";
			$message .= "	<TR>\n";
			$message .= "		<TD bgcolor=white nowrap><font face=arial,san-serif size=2>".$AppUI->_('View').":</font></TD>\n";
			$message .= "		<TD bgcolor=white nowrap><a href=\"".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket\"><font face=arial,sans-serif size=2>".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket</font></a></TD>\n";
			$message .= "	</tr>\n";
			$message .= "</TABLE>\n";
			$message .= "</body>\n";
			$message .= "</html>\n";
			$message .= "\n--$boundary--\n";

			mail($mailinfo["contact_email"], $AppUI->_('Trouble ticket')." #$ticket ".$AppUI->_('has been assigned to you'), $message, "From: " . $CONFIG['reply_to'] . "\nContent-type: multipart/alternative; boundary=\"$boundary\"\nMime-Version: 1.0");
		} // End of check for valid email
	} // End of check for toggle of assignee

} // End of check for change in header fields

/* start table */
?>

<table class="std" cellspacing="2" cellpadding="3" border="0" width="100%">
<tr>
	<th colspan="2" align="center"><?php echo $title;?></th>
</tr>

<form name="ticketform" action="index.php?m=ticketsmith&a=view&ticket=<?php echo $ticket;?>" method="post">
<input type="hidden" name="ticket" value="$ticket" />

<?php
/* start form */

/* get ticket */
$ticket_info = query2hash("SELECT * FROM tickets WHERE ticket = $ticket");

print("<input type=\"hidden\" name=\"orig_assignment\" value='" . $ticket_info["assignment"] . "' />\n");
print("<input type=\"hidden\" name=\"author\" value='" . $ticket_info["author"] . "' />\n");
print("<input type=\"hidden\" name=\"priority\" value='" . $ticket_info["priority"] . "' />\n");
print("<input type=\"hidden\" name=\"subject\" value='" . $ticket_info["subject"] . "' />\n");

/* output ticket */
for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
    print("<tr>\n");
    if ($fields["headings"][$loop] !== "<br />") {
	$fields["headings"][$loop] = $AppUI->_($fields["headings"][$loop]);
    }
    print("<td align=\"right\">" . $fields["headings"][$loop] . "</td>");
    print("<td align=\"left\" class=\"hilite\">" . format_field($ticket_info[$fields["columns"][$loop]], $fields["types"][$loop]) . "</td>\n");
    print("</tr>\n");
}
$ticket_info["assignment"];

/* output attachment indicator */
$attach_count = query2result("SELECT attachment FROM tickets WHERE ticket = '$ticket'");

if ($attach_count == 1) {
    print("<tr>\n");
    print("<td align=\"left\"><strong>Attachments</strong></td>");
    print("<td align=\"left\">This email had attachments which were removed.</td>\n");
    print("</tr>\n");
} else if ($attach_count == 2) {
  $result = do_query("SELECT file_id, file_name from files, tickets where ticket = '$ticket'
  and file_task = ticket and file_project = 0");
  if (number_rows($result)) {
       print("<tr>\n");
      print("<td align=\"left\"><b>Attachments</b></td>");
      print("<td align=\"left\">");
		  while ($row = result2hash($result)) {
			  echo "<a href='fileviewer.php?file_id=" . $row["file_id"] . "'>";
			  echo $row["file_name"];
			echo "</a><br>\n";
		}
		print("</td>\n");
		print("</tr>\n");
	}
}

/* output followup navigation */
if ($ticket_type != "Staff Followup" && $ticket_type != "Client Followup" && $ticket_type != "Staff Comment") {

    /* output followups */
    print("<tr>\n");
    print("<td align=\"left\" valign=\"top\"><strong>".$AppUI->_('Followups')."</strong></td>\n");
    print("<td align=\"left\" valign=\"top\">\n");

    /* grab followups */
    $query = "SELECT ticket, type, timestamp, author FROM tickets WHERE parent = '$ticket' ORDER BY ticket " . $CONFIG["followup_order"];
    $result = do_query($query);

    if (number_rows($result)) {

        /* print followups */
        print("<table width=\"100%\" border=\"1\" cellspacing=\"5\" cellpadding=\"5\">\n");
        while ($row = result2hash($result)) {

            /* determine row color */
            $color = (@$number++ % 2 == 0) ? "#d3dce3" : "#dddddd";

            /* start row */
            print("<tr>\n");

            /* do number/author */
            print("<td bgcolor=\"$color\">\n");
            print("<strong>$number</strong> : \n");
            $row["author"] = ereg_replace("\"", "", $row["author"]);
            $row["author"] = htmlspecialchars($row["author"]);
            print($row["author"] . "\n");
            print("</td>\n");

            /* do type */
            print("<td bgcolor=\"$color\"><a href=\"index.php?m=ticketsmith&a=view&ticket=" . $row["ticket"] . "\">" . $AppUI->_($row["type"]) . "</a></td>\n");

            /* do timestamp */
            print("<td bgcolor=\"$color\">\n");
            print(get_time_ago($row["timestamp"]));
            print("</td>\n");

            /* end row */
            print("</tr>\n");

        }
        print("</table>\n");

    }
    else {
        print("<em>".$AppUI->_('none')."</em>\n");
    }

    print("</td>\n</tr>\n");

}

else {

    /* get peer followups */
    $results = do_query("SELECT ticket, type FROM tickets WHERE parent = '$ticket_parent' ORDER BY ticket " . $CONFIG["followup_order"]);

    /* parse followups */
    while ($row = result2hash($results)) {
        $peer_tickets[] = $row["ticket"];
    }

    /* count peers */
    $peer_count = count($peer_tickets);

    if ($peer_count > 1) {

        /* start row */
        print("<tr>\n");
        print("<td><strong>Followups</strong></td>\n");

        /* start cell */
        print("<td valign=\"middle\">");

        /* form peer links */
        for ($loop = 0; $loop < $peer_count; $loop++) {
            if ($peer_tickets[$loop] == $ticket) {
                $viewed_peer = $loop;
                $peer_strings[$loop] = "<strong>" . ($loop + 1) . "</strong>";
            }
            else {
                $peer_strings[$loop] = "<a href=\"index.php?m=ticketsmith&a=view&ticket=$peer_tickets[$loop]\">" . ($loop + 1) . "</a>";
            }
        }

        /* previous navigator */
        if ($viewed_peer > 0) {
            print("<a href=\"index.php?m=ticketsmith&a=view&ticket=" . $peer_tickets[$viewed_peer - 1] . "\">");
            print($CONFIG["followup_order"] == "ASC" ?  $AppUI->_("older") : $AppUI->_("newer"));
            print("</a> | ");
        }

        /* ticket list */
        print(join(" | ", $peer_strings));

        /* next navigator */
        if ($peer_count - $viewed_peer > 1) {
            print(" | <a href=\"index.php?m=ticketsmith&a=view&ticket=" . $peer_tickets[$viewed_peer + 1] . "\">");
            print($CONFIG["followup_order"] == "ASC" ?  "newer" : "older");
            print("</a>");
        }

        /* end cell */
        print("</td>\n");

        /* end row */
        print("</tr>\n");

    }

}

/* output action links */
print("<tr>\n");
print("<td><br /></td>\n");
print("<td>\n");
print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
if ($ticket_type == "Staff Followup" || $ticket_type == "Client Followup" || $ticket_type == "Staff Comment") {
	if ($canEdit) {
		print("<tr><td align=\"left\"><a href=index.php?m=ticketsmith&a=followup&ticket=$ticket>".$AppUI->_("Post followup (emails client)")."</a> | ");
		print("<a href=index.php?m=ticketsmith&a=comment&ticket=$ticket>".$AppUI->_('Post internal comment')."</a> | ");
		print("<a href=index.php?m=ticketsmith&a=view&ticket=$ticket_parent>".$AppUI->_('Return to parent')."</a> | ");
	}
	else {
	print("<tr><td align=\"left\"><a href=index.php?m=ticketsmith&a=view&ticket=$ticket_parent>".$AppUI->_('Return to parent')."</a>");
	}

}
else {
	if ($canEdit) {
		print("<tr><td align=\"left\"><a href=index.php?m=ticketsmith&a=followup&ticket=$ticket>".$AppUI->_("Post followup (emails client)")."</a> | ");
		print("<a href=index.php?m=ticketsmith&a=comment&ticket=$ticket>".$AppUI->_('Post internal comment')."</a> | ");
	}
}
print("</td>");
print('<td align="right"><a href="index.php?m=ticketsmith&a=view&ticket='.$ticket.'">'.$AppUI->_('Back to top').'</a></td></tr>');
print("</table>\n");
print("</td>");
print("</tr>\n");

/* end table */
print("</table>\n");

/* end form */
print("</form>\n");
?>

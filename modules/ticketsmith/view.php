<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

if (!$canRead) {
	$AppUI->redirect("m=public&a=access_denied");
}

$ticket = (int)dPgetParam($_GET, 'ticket', '');
$ticket_type = dPgetCleanParam($_GET, 'ticket_type', '');

$type_toggle = dPgetCleanParam($_POST, 'type_toggle', '');
$priority_toggle = (int)dPgetParam($_POST, 'priority_toggle', '');
$assignment_toggle = (int)dPgetParam($_POST, 'assignment_toggle', '');

// setup the title block
$titleBlock = new CTitleBlock('View Ticket', 'gconf-app-icon.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=ticketsmith", "tickets list");
$titleBlock->addCrumb("?m=ticketsmith&amp;type=My", "my tickets");
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
$orig_assignment = dPgetCleanParam($_POST, 'orig_assignment', '');
$author = dPgetCleanParam($_POST, 'author', '');
$priority = dPgetCleanParam($_POST, 'priority', '');
$subject = dPgetCleanParam($_POST, 'subject', '');

$q = new DBQuery();

if (@$type_toggle || @$priority_toggle || @$assignment_toggle) {
    $q->clear();
    $q->addTable('tickets');
    $q->addUpdate('type,priority,assignment', array($type_toggle, $priority_toggle, $assignment_toggle), true);
    $q->addWhere("ticket = '{$ticket}'");
    $q->exec();

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
	$message .= "View     : ".DP_BASE_URL."/?m=ticketsmith&amp;a=view&amp;ticket=$ticket\n";
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
	$message .= "<table border='0' cellpadding='4' cellspacing='1'>\n";
	$message .= "	<tr>\n";
	$message .= "	<td valign=top><img src=".DP_BASE_URL."/images/icons/ticketsmith.gif alt= border=0 width=42 height=42></td>\n";
	$message .= "		<td nowrap='nowrap'><span class=title>".$AppUI->_('Trouble Ticket Management -')  . $change ."</span></td>\n";
	$message .= "		<td valign=top align=right width=100%>&nbsp;</td>\n";
	$message .= "	</tr>\n";
	$message .= "</table>\n";
	$message .= "<table width='600' border='0' cellpadding='4' cellspacing='1' bgcolor='#878676'>\n";
	$message .= "	<tr>\n";
	$message .= "		<td bgcolor='white' nowrap='nowrap'><font face='arial,san-serif' size='2'>".$AppUI->_('Ticket ID').":</font></td>\n";
	$message .= "		<td bgcolor='white' nowrap='nowrap'><font face='arial,san-serif' size='2'>$ticket</font></td>\n";
	$message .= "	</tr>\n";
	$message .= "	<tr>\n";
	$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>".$AppUI->_('Author').":</font></td>\n";
	$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>" . str_replace(">", "&gt;", str_replace("<", "&lt;", str_replace('"', '', $author))) . "</font></td>\n";
	$message .= "	</tr>\n";
	$message .= "	<tr>\n";
	$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>".$AppUI->_('Subject').":</font></td>\n";
	$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>$subject</font></td>\n";
	$message .= "	</tr>\n";
	$message .= "	<tr>\n";
	$message .= "		<td bgcolor='white' nowrap='nowrap'><font face='arial,san-serif' size='2'>".$AppUI->_('View').":</font></td>\n";
	$message .= "		<td bgcolor='white' nowrap='nowrap'><a href=\"".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket\"><font face=arial,sans-serif size='2'>".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket</font></a></td>\n";
	$message .= "	</tr>\n";
	$message .= "</table>\n";
	$message .= "</body>\n";
	$message .= "</html>\n";
	$message .= "\n--$boundary--\n";

	$ticketNotification = dPgetSysVal('TicketNotify');
	if (count($ticketNotification) > 0) {
		mail($ticketNotification[$priority], $AppUI->_('Trouble ticket')." #$ticket ", $message, "From: " . $CONFIG['reply_to'] . "\nContent-type: multipart/alternative; boundary=\"$boundary\"\nMime-Version: 1.0");
	}

	if (@$assignment_toggle != @$orig_assignment)
	{
		
                $q->clear();
                $q->addQuery('contact_first_name, contact_last_name, contact_email');
                $q->addTable('users', 'u');
                $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
                $q->addWhere("user_id = {$assignment_toggle}");
		$mailinfo = $q->loadHash();

		if (@$mailinfo['contact_email']) {
			$boundary = "_lkqwkASDHASK89271893712893";
			$message = "--$boundary\n";
			$message .= "Content-disposition: inline\n";
			$message .= "Content-type: text/plain\n\n";
			$message .= $AppUI->_('Trouble ticket assigned to you') . ".\n\n";
			$message .= "Ticket ID: $ticket\n";
			$message .= "Author   : $author\n";
			$message .= "Subject  : $subject\n";
			$message .= "View     : ".DP_BASE_URL."/index.php?m=ticketsmith&amp;a=view&amp;ticket=$ticket\n";
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
			$message .= "<table border=0 cellpadding=4 cellspacing=1>\n";
			$message .= "	<tr>\n";
			$message .= "	<td valign=top><img src=".DP_BASE_URL."/images/icons/ticketsmith.gif alt= border=0 width=42 height=42></td>\n";
			$message .= "		<td nowrap='nowrap'><span class=title>".$AppUI->_('Trouble Ticket Management')."</span></td>\n";
			$message .= "		<td valign=top align=right width=100%>&nbsp;</td>\n";
			$message .= "	</tr>\n";
			$message .= "</table>\n";
			$message .= "<table width=600 border=0 cellpadding=4 cellspacing=1 bgcolor=#878676>\n";
			$message .= "	<tr>\n";
			$message .= "		<td colspan=2><font face='arial,san-serif' size='2' color='white'>".$AppUI->_('Ticket assigned to you')."</font></td>\n";
			$message .= "	</tr>\n";
			$message .= "	<tr>\n";
			$message .= "		<td bgcolor='white' nowrap='nowrap'><font face='arial,san-serif' size='2'>".$AppUI->_('Ticket ID').":</font></td>\n";
			$message .= "		<td bgcolor='white' nowrap='nowrap'><font face='arial,san-serif' size='2'>$ticket</font></td>\n";
			$message .= "	</tr>\n";
			$message .= "	<tr>\n";
			$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>".$AppUI->_('Author').":</font></td>\n";
			$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>" . str_replace(">", "&gt;", str_replace("<", "&lt;", str_replace('"', '', $author))) . "</font></td>\n";
			$message .= "	</tr>\n";
			$message .= "	<tr>\n";
			$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>".$AppUI->_('Subject').":</font></td>\n";
			$message .= "		<td bgcolor='white'><font face='arial,san-serif' size='2'>$subject</font></td>\n";
			$message .= "	</tr>\n";
			$message .= "	<tr>\n";
			$message .= "		<td bgcolor='white' nowrap='nowrap'><font face='arial,san-serif' size='2'>".$AppUI->_('View').":</font></td>\n";
			$message .= "		<td bgcolor='white' nowrap='nowrap'><a href=\"".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket\"><font face=arial,sans-serif size='2'>".DP_BASE_URL."/index.php?m=ticketsmith&a=view&ticket=$ticket</font></a></td>\n";
			$message .= "	</tr>\n";
			$message .= "</table>\n";
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

<form name="ticketform" action="index.php?m=ticketsmith&amp;a=view&amp;ticket=<?php echo $ticket;?>" method="post">
<input type="hidden" name="ticket" value="$ticket" />

<?php
/* start form */

/* get ticket */
$q->clear();
$q->addTable('tickets');
$q->addWhere("ticket = '${ticket}'");
$ticket_info = $q->loadHash();

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
$q->clear();
$q->addTable('tickets');
$q->addQuery('attachment');
$q->addWhere("ticket = '{$ticket}'");
$attach_count = $q->loadResult();

if ($attach_count == 1) {
    print("<tr>\n");
    print("<td align=\"left\"><strong>Attachments</strong></td>");
    print("<td align=\"left\">This email had attachments which were removed.</td>\n");
    print("</tr>\n");
} else if ($attach_count == 2) {
  $q->clear();
  $q->addQuery('file_id, file_name');
  $q->addTable('files', 'f');
  $q->innerJoin('tickets','t', 't.ticket = f.file_task');
  $q->addWhere("t.ticket = '{$ticket}'");
  $q->addWhere("f.project = 0");
  $q->includeCount();

  $result = $q->loadList();
  if ($q->foundRows()) {
       print("<tr>\n");
      print("<td align=\"left\"><b>Attachments</b></td>");
      print("<td align=\"left\">");
		  foreach ($result as $row) {
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
    $q->clear();
    $q->addQuery('ticket,type,timestamp,author');
    $q->addTable('tickets');
    $q->addWhere("parent = '{$ticket}'");
    $q->addOrder("ticket {$CONFIG['followup_order']}");
    $q->includeCount();
    $result = $q->loadList();

    if ($q->foundRows()) {

        /* print followups */
        print("<table width=\"100%\" border=\"1\" cellspacing=\"5\" cellpadding=\"5\">\n");
        foreach ($result as $row) {

            /* determine row color */
            $color = (@$number++ % 2 == 0) ? "#d3dce3" : "#dddddd";

            /* start row */
            print("<tr>\n");

            /* do number/author */
            print("<td bgcolor=\"$color\">\n");
            print("<strong>$number</strong> : \n");
            $row["author"] = preg_replace('/\"/', '', $row["author"]);
            $row["author"] = htmlspecialchars($row["author"]);
            print($row["author"] . "\n");
            print("</td>\n");

            /* do type */
            print("<td bgcolor=\"$color\"><a href=\"index.php?m=ticketsmith&amp;a=view&amp;ticket=" . $row["ticket"] . "\">" . $AppUI->_($row["type"]) . "</a></td>\n");

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
    $q->clear();
    $q->addQuery('ticket, type');
    $q->addTable('tickets');
    $q->addWhere("parent = '{$ticket_parent}'");
    $q->addOrder("ticket {$CONFIG['followup_order']}");
    $results = $q->loadList();

    /* parse followups */
    if ($results) {
      foreach ($results as $row) {
        $peer_tickets[] = $row["ticket"];
      }
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
                $peer_strings[$loop] = "<a href=\"?m=ticketsmith&amp;a=view&amp;ticket=$peer_tickets[$loop]\">" . ($loop + 1) . "</a>";
            }
        }

        /* previous navigator */
        if ($viewed_peer > 0) {
            print("<a href=\"?m=ticketsmith&amp;a=view&amp;ticket=" . $peer_tickets[$viewed_peer - 1] . "\">");
            print($CONFIG["followup_order"] == "ASC" ?  $AppUI->_("older") : $AppUI->_("newer"));
            print("</a> | ");
        }

        /* ticket list */
        print(join(" | ", $peer_strings));

        /* next navigator */
        if ($peer_count - $viewed_peer > 1) {
            print(" | <a href=\"?m=ticketsmith&amp;a=view&amp;ticket=" . $peer_tickets[$viewed_peer + 1] . "\">");
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
		print("<tr><td align=\"left\"><a href='?m=ticketsmith&amp;a=followup&amp;ticket=$ticket'>".$AppUI->_("Post followup (emails client)")."</a> | ");
		print("<a href='?m=ticketsmith&amp;a=comment&amp;ticket=$ticket'>".$AppUI->_('Post internal comment')."</a> | ");
		print("<a href='?m=ticketsmith&amp;a=view&amp;ticket=$ticket_parent'>".$AppUI->_('Return to parent')."</a> | ");
	}
	else {
	print("<tr><td align=\"left\"><a href='?m=ticketsmith&amp;a=view&amp;ticket=$ticket_parent'>".$AppUI->_('Return to parent')."</a>");
	}

}
else {
	if ($canEdit) {
		print("<tr><td align=\"left\"><a href='?m=ticketsmith&amp;a=followup&amp;ticket=$ticket'>".$AppUI->_("Post followup (emails client)")."</a> | ");
		print("<a href='?m=ticketsmith&amp;a=comment&amp;ticket=$ticket'>".$AppUI->_('Post internal comment')."</a> | ");
	}
}
print("</td>");
print('<td align="right"><a href="?m=ticketsmith&amp;a=view&amp;ticket='.$ticket.'">'.$AppUI->_('Back to top').'</a></td></tr>');
print("</table>\n");
print("</td>");
print("</tr>\n");

/* end table */
print("</table>\n");

/* end form */
print("</form>\n");
?>

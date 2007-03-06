<?php /* TICKETSMITH $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$ticket = dPgetParam( $_GET, 'ticket', '' );
$ticket_type = dPgetParam( $_GET, 'ticket_type', '' );

// setup the title block
$titleBlock = new CTitleBlock( 'Post Followup', 'gconf-app-icon.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=ticketsmith", "tickets list" );
$titleBlock->addCrumb( "?m=ticketsmith&a=view&ticket=$ticket", "view this ticket" );
$titleBlock->show();

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'/modules/ticketsmith/common.inc.php');

require_once( $AppUI->getSystemClass( 'libmail' ) );

/* set title */
$title = "Post Followup";

/* setup fields */
$fields = array("headings" => array("Subject", "Cc", "<br />"),
                "columns"  => array("subject", "cc", "body"),
                "types"    => array("subject", "cc", "followup"));

/* prepare ticket parent */
if (!$ticket_parent) {
    $ticket_parent = $ticket;
}

//echo '<pre>';print_r($_POST);echo '</pre>';die;
$recipient = dPgetParam( $_POST, 'recipient', '' );
$subject = dPgetParam( $_POST, 'subject', '' );
$cc = dPgetParam( $_POST, 'cc', '' );
$followup = dPgetParam( $_POST, 'followup', '' );

if (@$followup) {

    /* prepare fields */
    $timestamp = time();
    list($from_name, $from_email) = query2array("SELECT CONCAT_WS(' ',contact_first_name,contact_last_name) as name, contact_email as email FROM users u LEFT JOIN contacts ON u.user_contact = contact_id WHERE user_id = '$AppUI->user_id'");
    $author = "$from_name <$from_email>";
    if (!$recipient) {
        $recipient = query2result("SELECT author FROM tickets WHERE ticket = '$ticket_parent'");
    }

    /* prepare posted stuff */
    $recipient = stripslashes($recipient);
    $subject = stripslashes($subject);
    $followup = stripslashes($followup);
    $cc = stripslashes($cc);

	$mail = new Mail;
	if (isset($CONFIG['reply_name']) && $CONFIG["reply_name"] != "") {
		$mail->From($CONFIG["reply_name"] . " <" . $CONFIG["reply_to"] . ">");
	} else {
		$mail->From( $author );
		$mail->ReplyTo( $CONFIG["reply_to"] );
	}
	$mail->To( $recipient );
	if ($cc) {
		$mail->Cc( $cc );
	}
	$mail->Subject( "[#$ticket_parent] " . trim( $subject ) );
	$mail->Body( $followup );
    $mail->Send() || fatal_error("Unable to mail followup.  Quit without recording followup to database.");

    /* escape special characters */
    $author = db_escape( $author );
    $recipient = db_escape( $recipient );
    $subject = db_escape( $subject );
    $followup = db_escape( $followup );
    $cc = db_escape( $cc );

    /* do database insert */
    $query = "INSERT INTO tickets (author, subject, recipient, body, cc, timestamp, type, assignment, parent) ";
    $query .= "VALUES ('$author','$subject','$recipient','$followup','$cc','$timestamp','Staff Followup','9999','$ticket_parent')";
    do_query($query);

    /* update parent activity */
    do_query("UPDATE tickets SET activity = '$timestamp' WHERE ticket = '$ticket_parent'");

    /* redirect to parent */
    echo("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=index.php?m=ticketsmith&a=view&ticket=$ticket_parent\">");

    exit();

} else {

    /* start table */
	print("<table class=std width=100%>\n");
    print("<tr>\n");
    print("<th colspan=2 align=center>\n");
    print("<div class=heading> ".$AppUI->_($title)."</div>\n");
    print("</th>\n");
    print("</tr>\n");

    /* start form */
    print("<form name='ticketform' action=\"index.php?m=ticketsmith&a=followup&ticket=$ticket\" method=post>\n");

    /* get ticket */
    $ticket_info = query2hash("SELECT * FROM tickets WHERE ticket = $ticket");

    /* output From: line */
    print("<tr>\n");
    print("<td align=left><strong>".$AppUI->_('From')."</strong></td>");
    list($from_name, $from_email) = query2array("SELECT CONCAT_WS(' ',contact_first_name,contact_last_name) as name, contact_email as email FROM users u LEFT JOIN contacts ON u.user_contact = contact_id WHERE user_id = '$AppUI->user_id'");
    print("<td align=left>" . $from_name . " &lt;" . $from_email . "&gt;</td>\n");
    print("</tr>\n");

    /* output To: line */
    print("<tr>\n");
    print("<td align=left><strong>".$AppUI->_('To')."</strong></td>");
    $recipient = query2result("SELECT author FROM tickets WHERE ticket = '$ticket_parent'");
    print("<td align=left>" . format_field($recipient, "recipient") . "</td>\n");
    print("</tr>\n");

    /* output ticket */
    for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
        print("<tr>\n");
	// do not translate if heading is "<br />"
	if ( $fields["headings"][$loop] == "<br />") {
	}
	else {
		$fields["headings"][$loop] = $AppUI->_($fields["headings"][$loop]);
	}
        print("<td align=left><strong>" . $fields["headings"][$loop] . "</strong></td>");
        print("<td align=left>" . format_field($ticket_info[$fields["columns"][$loop]], $fields["types"][$loop]) . "</td>\n");
        print("</tr>\n");
    }

    /* output submit button */
    print('<tr><td><br /></td><td><font size=-1><input class=button type=submit value="'.$AppUI->_('Post Followup').'"></font></td></tr>');

    /* output actions */
    print("<tr>\n");
    print("<td align=left valign=top><br /></td>");
    print("<td align=left valign=top>&nbsp;</td>\n");
    print("</tr>\n");

    /* end table */
    print("</table>\n");

    /* end form */
    print("</form>\n");
}

?>

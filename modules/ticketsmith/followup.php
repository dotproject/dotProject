<?php /* TICKETSMITH $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

if (!$canRead) {
	$AppUI->redirect("m=public&a=access_denied");
}

$ticket = (int)dPgetParam($_GET, 'ticket', '');
$ticket_type = dPgetCleanParam($_GET, 'ticket_type', '');

// setup the title block
$titleBlock = new CTitleBlock('Post Followup', 'gconf-app-icon.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=ticketsmith", "tickets list");
$titleBlock->addCrumb("?m=ticketsmith&amp;a=view&amp;ticket=$ticket", "view this ticket");
$titleBlock->show();

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'/modules/ticketsmith/common.inc.php');

require_once($AppUI->getSystemClass('libmail'));

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

// echo '<pre>';print_r($_POST);echo '</pre>';die;

$recipient = dPgetEmailParam($_POST, 'recipient', '');
$subject = dPgetCleanParam($_POST, 'subject', '');
$cc = dPgetCleanParam($_POST, 'cc', '');
$followup = dPgetCleanParam($_POST, 'followup', '');

$q = new DBQuery();

if (@$followup) {

    /* prepare fields */
    $timestamp = time();
    $q->addTable('users', 'u');
    $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
    $q->addQuery("CONCAT_WS(' ',contact_first_name,contact_last_name) as name");
    $q->addQuery('contact_email as email');
    $q->addWhere("user_id = '{$AppUI->user_id}'");
    $q->exec();

    list($from_name, $from_email) = $q->fetchRow();

    $author = "$from_name <$from_email>";
    if (!$recipient) {
        $q->clear();
        $q->addTable('tickets');
        $q->addQuery('author');
        $q->addWhere("ticket = '{$ticket_parent}'");

        $recipient = $q->loadResult();
    }

    /* prepare posted stuff */
    $recipient = stripslashes($recipient);
    $subject = stripslashes($subject);
    $followup = stripslashes($followup);
    $cc = stripslashes($cc);

	$mail = new Mail;
	echo "</pre>\n";
	if (isset($CONFIG['reply_name']) && $CONFIG["reply_name"] != "") {
		$mail->From($CONFIG["reply_name"] . " <" . $CONFIG["reply_to"] . ">");
	} else {
		$mail->From($author);
		$mail->ReplyTo($CONFIG["reply_to"]);
	}
	$mail->To($recipient);
	if ($cc) {
		$mail->Cc($cc);
	}
	$mail->Subject("[#$ticket_parent] " . trim($subject));
	$mail->Body($followup);
    $mail->Send() || fatal_error("Unable to mail followup.  Quit without recording followup to database.");

    /* do database insert */
    $q->clear();
    $q->addTable('tickets');
    $q->addInsert('author,subject,recipient,body,cc,timestamp,type,assignment,parent',
      array($author, $subject, $recipient, $followup, $cc, $timestamp, 'Staff Followup', '9999', $ticket_parent), true);
    $q->exec();

    /* update parent activity */
    $q->clear();
    $q->addTable('tickets');
    $q->addUpdate('activity', $timestamp);
    $q->addWhere("ticket = '{$ticket_parent}'");
    $q->exec();

    /* redirect to parent */
    echo("<meta http-equiv=\"Refresh\" CONTENT=\"0;URL=?m=ticketsmith&amp;a=view&amp;ticket=$ticket_parent\">");

    exit();

} else {

    /* start table */
	print("<table class='std' width='100%'>\n");
    print("<tr>\n");
    print("<th colspan='2' align='center'>\n");
    print("<div class='heading'> ".$AppUI->_($title)."</div>\n");
    print("</th>\n");
    print("</tr>\n");

    /* start form */
    print("<form name='ticketform' action='?m=ticketsmith&amp;a=followup&amp;ticket=$ticket' method='post'>\n");

    $q->clear();
    $q->addTable('tickets');
    $q->addWhere("ticket = {$ticket}");
    $ticket_info = $q->loadHash();

    /* output From: line */
    print("<tr>\n");
    print("<td align='left'><strong>".$AppUI->_('From')."</strong></td>");
    $q->clear();
    $q->addTable('users', 'u');
    $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
    $q->addWhere("user_id = '{$AppUI->user_id}'");
    $q->addQuery("CONCAT_WS(' ',contact_first_name,contact_last_name) as name, contact_email as email");
    $q->exec();
    list($from_name, $from_email) = $q->fetchRow();

    print("<td align='left'>" . $from_name . " &lt;" . $from_email . "&gt;</td>\n");
    print("</tr>\n");

    /* output To: line */
    print("<tr>\n");
    print("<td align='left'><strong>".$AppUI->_('To')."</strong></td>");
    $q->clear();
    $q->addTable('tickets');
    $q->addQuery('author');
    $q->addWhere("ticket = '{$ticket_parent}'");

    $recipient = $q->loadResult();

    print("<td align='left'>" . format_field($recipient, "recipient") . "</td>\n");
    print("</tr>\n");

    /* output ticket */
    for ($loop = 0; $loop < count($fields["headings"]); $loop++) {
        print("<tr>\n");
	// do not translate if heading is "<br />"
	if ($fields["headings"][$loop] == "<br />") {
	}
	else {
		$fields["headings"][$loop] = $AppUI->_($fields["headings"][$loop]);
	}
        print("<td align='left'><strong>" . $fields["headings"][$loop] . "</strong></td>");
        print("<td align='left'>" . format_field($ticket_info[$fields["columns"][$loop]], $fields["types"][$loop]) . "</td>\n");
        print("</tr>\n");
    }

    /* output submit button */
	print('<tr><td><br /></td><td><font size="-1"><input class="button" type="submit" value="'.$AppUI->_('Post Followup').'" /></font></td></tr>');

    /* output actions */
    print("<tr>\n");
    print("<td align='left' valign='top'><br /></td>");
    print("<td align='left' valign='top'>&nbsp;</td>\n");
    print("</tr>\n");

    /* end table */
    print("</table>\n");

    /* end form */
    print("</form>\n");
}

?>

<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');

##
##	Ticketsmith sql handler
##

$name = dPgetParam($_POST, 'name', '');
$email = dPgetParam($_POST, 'email', '');
$subject = dPgetParam($_POST, 'subject', '');
$priority = dPgetParam($_POST, 'priority', '');
$description = dPgetParam($_POST, 'description', '');
$ticket_company = dPgetParam($_POST, 'ticket_company', 0);
$ticket_project = dPgetParam($_POST, 'ticket_project', 0);

$author = $name . " <" . $email . ">";
$tsql =
"INSERT INTO tickets (author,subject,priority,body,timestamp,type, ticket_company, ticket_project) ".
"VALUES('$author', '".addslashes($subject)."','$priority','".addslashes($description)."',UNIX_TIMESTAMP(),'Open', $ticket_company, $ticket_project)";

$rc = mysql_query($tsql);


if (mysql_errno()) {
	$AppUI->setMsg(mysql_error());
} else {
	$AppUI->setMsg("Ticket added");
	
	$ticket = mysql_insert_id();
	//Emailing notifications.
	$boundary = "_lkqwkASDHASK89271893712893";
	$message = "--$boundary\n";
	$message .= "Content-disposition: inline\n";
	$message .= "Content-type: text/plain\n\n";
	$message .= $AppUI->_('New Ticket') . ".\n\n";
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
	$message .= "<title>".$AppUI->_('New Ticket')."</title>\n";
	$message .= "</head>\n";
	$message .= "<body>\n";
	$message .= "\n";
	$message .= "<table border='0' cellpadding='4' cellspacing='1'>\n";
	$message .= "	<tr>\n";
	$message .= "	<td valign='top'><img src=".DP_BASE_URL."/images/icons/ticketsmith.gif alt='' border='0' width='42' height='42'></td>\n";
	$message .= "		<td nowrap='nowrap'><span class='title'>".$AppUI->_('Trouble Ticket Management - New Ticket')."</span></td>\n";
	$message .= "		<td valign='top' align='right' width='100%'>&nbsp;</td>\n";
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
	$message .= "		<td bgcolor='white' nowrap='nowrap'><a href=\"".DP_BASE_URL."/?m=ticketsmith&amp;a=view&amp;ticket=$ticket\"><font face='arial,sans-serif' size='2'>".DP_BASE_URL."/?m=ticketsmith&amp;a=view&amp;ticket=$ticket</font></a></td>\n";
	$message .= "	</tr>\n";
	$message .= "</table>\n";
	$message .= "</body>\n";
	$message .= "</html>\n";
	$message .= "\n--$boundary--\n";

	$ticketNotification = dPgetSysVal('TicketNotify');
	if (count($ticketNotification) > 0) {
		mail($ticketNotification[$priority], $AppUI->_('Trouble ticket')." #$ticket ", $message, "From: " . $CONFIG['reply_to'] . "\nContent-type: multipart/alternative; boundary=\"$boundary\"\nMime-Version: 1.0");
	}
}
$AppUI->redirect("m=ticketsmith");
?>

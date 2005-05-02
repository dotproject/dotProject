<?php
##
##	Ticketsmith sql handler
##

$name = isset($HTTP_POST_VARS['name']) ? $HTTP_POST_VARS['name'] : '';
$email = isset($HTTP_POST_VARS['email']) ? $HTTP_POST_VARS['email'] : '';
$subject = isset($HTTP_POST_VARS['subject']) ? $HTTP_POST_VARS['subject'] : '';
$priority = isset($HTTP_POST_VARS['priority']) ? $HTTP_POST_VARS['priority'] : '';
$description = isset($HTTP_POST_VARS['description']) ? $HTTP_POST_VARS['description'] : '';
//$description = db_escape($description);

$author = $name . " <" . $email . ">";
$tsql =
"INSERT INTO tickets (author,subject,priority,body,timestamp,type) ".
"VALUES('$author','$subject','$priority','$description',UNIX_TIMESTAMP(),'Open')";

$rc = mysql_query($tsql);

if (!mysql_errno()) {
	$AppUI->setMsg( mysql_error() );
	// add code to mail to ticket master
} else {
	$AppUI->setMsg( "Ticket added" );
}
$AppUI->redirect( "m=ticketsmith" );
?>

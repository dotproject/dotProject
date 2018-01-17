<?php /* TICKETSMITH $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

if (!$canEdit) {
	$AppUI->redirect("m=public&a=access_denied");
}

$ticket = (int)dPgetParam($_GET, 'ticket', '');
$ticket_type = dPgetCleanParam($_GET, 'ticket_type', '');

// setup the title block
$titleBlock = new CTitleBlock('Post Comment', 'gconf-app-icon.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=ticketsmith", "tickets list");
$titleBlock->addCrumb("?m=ticketsmith&amp;a=view&amp;ticket=$ticket", "view this ticket");
$titleBlock->show();

require(DP_BASE_DIR.'/modules/ticketsmith/config.inc.php');
require(DP_BASE_DIR.'/modules/ticketsmith/common.inc.php');

/* set title */
$title = "Post Comment";

/* prepare ticket parent */
if (!$ticket_parent) {
    $ticket_parent = $ticket;
}

$author_name = dPgetCleanParam($_POST, 'author_name', '');
$author_email = dPgetCleanParam($_POST, 'author_email', '');
$comment = dPgetCleanParam($_POST, 'comment', '');
$body = dPgetCleanParam($_POST, 'body', '');

$q = new DBQuery();

if (@$comment) {

    /* prepare fields */
    $q->addTable('users', 'u');
    $q->addQuery("CONTACT_WS(' ',contact_first_name,contact_last_name) as name");
    $q->addQuery("contact_email as email");
    $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
    $q->addWhere("user_id = '{$AppUI->user_id}'");

    list($author_name, $author_email) = $q->fetchRow();

    $q->clear();
    $q->addTable('tickets');
    $q->addQuery('subject');
    $q->addWhere("ticket = '{$ticket_parent}'");
    $subject = $q->loadResult();

    $author = $author_name . " <" . $author_email . ">";
    $timestamp = time();

    /* prepare query */
    
    $q->clear();
    $q->addTable('tickets');
    $q->addInsert('author,subject,body,timestamp,type,parent,assignment',
      array($author, $subject, $comment, $timestamp, 'Staff Comment', $ticket_parent, '9999'), true);

    $q->exec();
    $q->clear();

    $q->addTable('tickets');
    $q->addUpdate('activity', $timestamp);
    $q->addWhere("ticket = '{$ticket_parent}'");
    $q->exec();
    $q->clear();

    /* return to ticket view */
    echo("<meta http-equiv=\"Refresh\" CONTENT=\"0;URL=?m=ticketsmith&amp;a=view&amp;ticket=$ticket_parent\">");

    exit();

} else {

    /* start table */
	print('<table class="std" bgcolor="#eeeeee" width="100%">'."\n");
    print("<tr>\n");
	print("<th colspan=\"2\" align=\"center\" >\n");
    print("<div class=\"heading\">".$AppUI->_($title)."</div>\n");
    print("</th>\n");
    print("</tr>\n");

    /* start form */
    print('<form name="ticketform" action="?m=ticketsmith&amp;a=comment&amp;ticket='.$ticket.'" method="post">' . "\n");

    /* determine poster */
    print("<tr>\n");
    print("<td align=\"left\"><strong>".$AppUI->_('From')."</strong></td>");
    $q->clear();
    $q->addTable('users', 'u');
    $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
    $q->addQuery("CONTACT_WS(' ',contact_first_name,contact_last_name) as name");
    $q->addQuery("contact_email as email");
    $q->addWhere("user_id = '{$AppUI->user_id}'");

    list($author_name, $author_email) = $q->fetchRow();
    print("<td align=\"left\">" . $author_name . " &lt;" . $author_email . "&gt;</td>\n");
    print("</tr>");

    /* output textarea */
    print("<tr>\n");
    print("<td align=\"left\"><br /></td>");
    print("<td align=\"left\">");
    print("<tt>\n");
    print("<textarea name=\"comment\" wrap=\"hard\" cols=\"72\" rows=\"20\">\n");
    print("</textarea>\n");
    print("</tt>\n");
    print("</td>\n");

    /* output submit button */
	print('<tr><td><br /></td><td><font size=\"-1\"><input type="submit" class=button value="'.$AppUI->_('Post Comment').'" /></font></td></tr>');

    /* footer links */
    print("<tr>\n");
    print("<td><br /></td>");
    print("<td>&nbsp;</td>");
    print("</tr>\n");

    /* end table */
    print("</table>\n");

    /* end form */
    print("</form>\n");
}

?>

<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//update task
$newparent = (int)dPgetParam($_GET, 'newparent', 0);
$ticket = (int)dPgetParam($_GET, 'ticket', 0);

$q = new DBQuery();
$q->addTable('tickets');
$q->addUpdate('parent,assignment,type',
  array($newparent, 9999, 'Client Followup'), true);
$q->addWhere("ticket = {$ticket}");
$q->exec();

header("Location: index.php?m=ticketsmith");
if (isset($newparent) && isset($ticket) && $newparent != 0 && $ticket != 0) {
  // error_log("Updating ticket - $sql1");
  mysql_query($sql1);
  // error_log(mysql_error());
  $sql2 = "update {$dbprefix}tickets set activity = '" . time() . "' where ticket = $newparent";
  // error_log($sql2);
  mysql_query($sql2);
  // error_log($mysql_error());
}
// else error_log("Ticket has not been reassigned");
?>

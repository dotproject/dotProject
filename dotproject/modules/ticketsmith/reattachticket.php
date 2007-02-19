<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

//update task
$newparent = dpGetParam( $_GET, 'newparent', 0);
$ticket = dpgetparam( $_GET, 'ticket', 0 );

$sql1 = "update tickets set parent = $newparent,
  assignment = 9999,
  type = 'Client Followup'
  where ticket = $ticket";

header("Location: index.php?m=ticketsmith");
if (isset($newparent) && isset($ticket) && $newparent != 0 && $ticket != 0) {
  // error_log("Updating ticket - $sql1");
  mysql_query($sql1);
  // error_log( mysql_error());
  $sql2 = "update tickets set activity = '" . time() . "' where ticket = $newparent";
  // error_log($sql2);
  mysql_query($sql2);
  // error_log($mysql_error());
}
// else error_log( "Ticket has not been reassigned");
?>

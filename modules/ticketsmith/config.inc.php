<?php
/* $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

// reply-to address for staff followups
// i.e. the address the gateway receives
if (! isset($dPconfig['site_domain'])) {
  $dPconfig['site_domain'] = "dotproject.net";
}

// Email to be notified for all new/changed tickets
$CONFIG['notify'] = '';
// Email to be notified for all new/changed tickets with high priority
$CONFIG['notify_911'] = '';

$CONFIG["reply_to"] = "support@" . $dPconfig['site_domain'];
// If you want to hide real addresses behind a bogus 
// generic email, uncomment the following:
// $CONFIG["reply_name"] = "Help Desk";

// page color preferences
$CONFIG["background_color"] = "#ffffff";
$CONFIG["heading_color"] = "#cc0000";
$CONFIG["ticket_color"] = "#ffffee";

// date format
$CONFIG["date_format"] = "D M j Y g:ia";

// visual warnings for old tickets
$CONFIG["warning_active"]= 1; // 0 = inactive, 1 = active
$CONFIG["warning_color"] = "#ff0000";
$CONFIG["warning_age"] = "0.5"; // in hours

// priority names (low to high)
$CONFIG["priority_names"] = array_map(array($AppUI,'_'), (array)dPgetSysVal('TicketPriority'));

// priority colors (low to high)
$CONFIG["priority_colors"] = array("#006600","#000000","#ff0000","#ff0000","#ff0000");

$CONFIG["type_names"] = dPgetSysVal('TicketStatus');
// number of tickets to see at once
$CONFIG["view_rows"] = 40;

// wordwrap badly-formatted messages (PHP >= 4.0.2 only)
$CONFIG["wordwrap"] = 1; // 0 = inactive, 1 = active

// column to order messages by
$CONFIG["order_by"] = "timestamp"; // "author", "subject", "timestamp", "activity", "type", "priority", "assignment"

// order in which to display messages
$CONFIG["message_order"] = "ASC"; // "ASC" or "DESC"

// order in which to display followups
$CONFIG["followup_order"] = "ASC"; // "ASC" or "DESC"

// go to parent or latest followup from index?
// note that latest followup is slightly slower
$CONFIG["index_link"] = "parent"; // "parent" or "latest"

?>

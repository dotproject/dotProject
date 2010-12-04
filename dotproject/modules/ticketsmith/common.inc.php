<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/* $Id$ */

/* program info */
$program = "Dotproject";
$version = "0.6.3";
$xmailer = "dotproject (http://dotproject.net/)";

/* error handler */
function fatal_error ($reason) {

    die($reason);

}


/* do a MySQL query */
function do_query ($query) {
	$result = @mysql_query($query);
	if (!$result) {
		fatal_error("A database query error has occurred!<br>".mysql_error());
	} else {
		return($result);
	}
	
}

/* get single result value */
function query2result ($query) {

	$result = do_query($query);
	$row = @mysql_result($result, 0);
	return($row);

}

/* get result in numeric array */
function query2array ($query) {

	$result = do_query($query);
	$row = @mysql_fetch_row($result);
	return($row);

}

/* get result in associative array */
function query2hash ($query) {

	$result = do_query($query);
	$row = @mysql_fetch_array($result);
	return($row);
	
}

/* get row of result */
function result2row ($result) {

    $row = @mysql_fetch_row($result);
    return($row);

}

/* get row of result in hash */
function result2hash ($result) {

    $row = @mysql_fetch_array($result);
    return($row);

}

/* find number of rows in query result */
function number_rows ($result) {

    $number_rows = @mysql_num_rows($result);
    return($number_rows);

}

/* put rows from a column into an array */
function column2array ($query) {

    $result = do_query($query);
    while ($row = @mysql_fetch_array($result)) {
        $array[] = $row[0];
    }
    return($array);

}

/* create read-only output of list values */
function chooseSelectedValue ($name, $options, $selected) {
	while (list($key, $val) = each($options)) {
			if ($key == $selected) {
				$output = "$val\n";
			}
		}
 return($output);

}

/* create drop-down box */
function create_selectbox ($name, $options, $selected) {

	$output= "";
	$output .= '<select name="'.$name.'" onchange="javascript:document.ticketform.submit()" class="text">'."\n";

	while (list($key, $val) = each($options)) {
		$output .= "<option value=\"$key\"";

		if ($key == $selected) {
			$output .= " selected";
		}

		$output .= ">$val\n";
		//$loop++;
	}

	$output .= "</select>\n";


    return($output);

}

/* escape special characters */
function escape_string ($string) {
    
    if (!get_magic_quotes_gpc()) {
        $string = addslashes($string);
    }
    return($string);

}

/* format "time ago" date string */
function get_time_ago ($timestamp) {
	global $AppUI;

    $elapsed_seconds = time() - $timestamp;

    if ($elapsed_seconds < 60) { // seconds ago
        if ($elapsed_seconds) {
            $interval = $elapsed_seconds;
        }
        else {
            $interval = 1;
        }
        $output = "second";
    }
    else if ($elapsed_seconds < 3600) { // minutes ago
        $interval = round($elapsed_seconds / 60);
        $output = "minute";
    }
    else if ($elapsed_seconds < 86400) { // hours ago
        $interval = round($elapsed_seconds / 3600);
        $output = "hour";
    }
    else if ($elapsed_seconds < 604800) { // days ago
        $interval = round($elapsed_seconds / 86400);
        $output = "day";
    }
    else if ($elapsed_seconds < 2419200) { // weeks ago
        $interval = round($elapsed_seconds / 604800);
        $output = "week";
    }
    else if ($elapsed_seconds < 29030400) { // months ago
        $interval = round($elapsed_seconds / 2419200);
        $output = " month";
    }
    else { // years ago
        $interval = round($elapsed_seconds / 29030400);
        $output = "year";
    }
    
    if ($interval > 1) {
        $output .= "s";
    }

    $output = " ".$AppUI->_($output);

    $output .= " ".$AppUI->_('ago');

    $output = $interval.$output;
    return($output);

}

/* smart word wrapping */
function smart_wrap ($text, $width) {

    if (function_exists("wordwrap")) {
        if (preg_match("/[^\\n]{100,}/", $text)) {
            $text = wordwrap($text, $width);
        }
    }
    else {
        $text = "Wordwrap unsupported in PHP " . phpversion() . "\n\n";;
        $text .= "Please adjust your Ticketsmith configuration and/or upgrade PHP\n";
    }

    return($text);

}

/* word_wrap($string, $cols, $quote_old, $prefix, $nice_prefix)
 *
 * Takes $string, and wraps it on a per-word boundary (does not clip
 * words UNLESS the word is more than $cols long), no more than $cols per
 * line. Allows for optional prefix string for each line. (Was written to
 * easily format replies to e-mails, prefixing each line with "> ".
 *
 * Puts words that do not fit at the end of the line to the 
 * beginning of the next line (keeping in mind prefixes) and not on a line
 * by themselves.
 *
 * parameter $string 		-- text to be wrapped
 * parameter $cols  		-- maximum width of text
 * parameter $quote_old 	-- adds the prefix for each line if true
 * parameter $prefix 		-- prefix used in message (nice_prefix but w/o spaces)
 * parameter $nice_prefix	-- prefix that is easier to read (i.e. "> " instead of ">").
 *
 * $nice_prefix is actually written to the message (if $quote_old), but $prefix is used for 
 * backward compatibility
 * 
 * Please note that though quote_old may be false, prefix and nice_prefix are still used to maintain
 * the existing structure of a message (in case the message is a reply or etc...)
 *
 * Original by Dominic J. Eidson.
 * Copyright 1999 Dominic J. Eidson, use as you wish, but give credit
 * where credit due.
 * Modified by Daniel Kigelman in 2003.
 *
 */

function word_wrap ($string, $cols = 78, $quote_old = false, $prefix = ">", $nice_prefix = "> ") {

if (preg_match("/^.*\r\n/", $string)) {
	$t_lines = mb_split("\r\n", $string);
} else if (preg_match("/^.*\n/", $string)) {
	$t_lines = mb_split("\n", $string);
} else {
	$t_lines = mb_split("\r", $string);
}

$outlines = "";
$leftover = "";

// Loop through each line of message
while (list(, $thisline) = each($t_lines)) {
	// Process Leftover
	if (mb_strlen($leftover) > 0) {
		$counter = 0;

		// Subtract all prefixes from the beginning of this line.
		while (mb_substr($thisline, 0, mb_strlen($prefix)) == $prefix) {
			$counter++;
			
			if (mb_substr($thisline, 0, mb_strlen($nice_prefix)) == $nice_prefix) {
				$thisline = mb_substr($thisline, mb_strlen($nice_prefix));
			} else {
				$thisline = mb_substr($thisline, mb_strlen($prefix));
			}
		}
		
		// Add the leftover to the beginning of this line.
		$thisline = $leftover . $thisline;
		
		// Add all the prefixes back on to the beginning of the line.
		for ($i = 0; $i < $counter; $i++) {
			$thisline = $nice_prefix . $thisline;
		}
	}

	if (mb_strlen($thisline) + mb_strlen($nice_prefix) > $cols) {
		$newline = '';
		$t_l_lines = mb_split(' ', $thisline);
		// This line is too big.  Break it up into words and add them one by one.
		while (list(, $thisword) = each($t_l_lines)) {
			// Process words that are longer than $cols
			while ((mb_strlen($thisword) + mb_strlen($nice_prefix)) > $cols) {
				$cur_pos = 0;
				$outlines .= $nice_prefix;
				for ($num=0; $num < $cols-1; $num++) {
					$outlines .= $thisword[$num];
					$cur_pos++;
				}
				$outlines .= "\n";
				$thisword = mb_substr($thisword, $cur_pos, (mb_strlen($thisword)-$cur_pos));
			}

			// Check that the line is within $cols.  If not, don't add the word; start a new line.
			if ((mb_strlen($newline) + mb_strlen($thisword) + mb_strlen($nice_prefix) + 1) > $cols) {
				if ($quote_old) $outlines .= $nice_prefix.$newline."\n";
				else $outlines .= $newline."\n";
				$newline = $thisword." ";
			} else {
				$newline .= $thisword." ";
			}
		}
		// Whatever is leftover from processing the line, assign to $leftover
		$leftover = $newline;
	} else {
		if ($quote_old) $outlines .= $nice_prefix . $thisline."\n";
		else $outlines .= $thisline."\n";
		$leftover = "";
	}
	
	// If we're processing the last line and there's leftover text, add a blank line to hold the leftover
	if (key($t_lines) == count($t_lines) - 1 && mb_strlen($leftover) > 0) {
		$t_lines[] = "";
	}
}
return $outlines;
}


/* format display field */
function format_field ($value, $type, $ticket = NULL) {

    global $CONFIG;
    global $AppUI;
    global $canEdit;
    $dbprefix = dPgetConfig('dbprefix','');

    switch ($type) {
        case "user":
            if ($value) {
	    	$output = query2result("SELECT CONCAT_WS(' ',contact_first_name,contact_last_name) as name FROM {$dbprefix}users u LEFT JOIN {$dbprefix}contacts ON u.user_contact = contact_id WHERE user_id = '$value'");
            } else {
                $output = "-";
            }
            break;
        case "status":
	    if ($canEdit) {
            	$output = create_selectbox("type_toggle", array("Open" =>$AppUI->_("Open"), "Processing" => $AppUI->_("Processing"), "Closed" => $AppUI->_("Closed"), "Deleted" => $AppUI->_("Deleted")), $value);
	    }
	    else {
		$output = chooseSelectedValue("type_toggle", array("Open" =>$AppUI->_("Open"), "Processing" => $AppUI->_("Processing"), "Closed" => $AppUI->_("Closed"), "Deleted" => $AppUI->_("Deleted")), $value);
	    }
            break;
        case "priority_view":
            $priority = $CONFIG["priority_names"][$value];
            $color = $CONFIG["priority_colors"][$value];
            if ($value == 3) {
                $priority = "<strong>$priority</strong>";
            }
            if ($value == 4) {
                $priority = "<blink><strong>$priority</strong></blink>";
            }

            $output = "<font color=\"$color\">$priority</font>";
            break;
        case "priority_select":
	    if ($canEdit) {
            	$output = create_selectbox("priority_toggle", $CONFIG["priority_names"], $value);
	    }
	    else {
	    	$output = chooseSelectedValue("priority_toggle", $CONFIG["priority_names"], $value);
	    }
            break;
        case "assignment":
            $options[0] = "-";
	    $query = "SELECT user_id as id, CONCAT_WS(' ',contact_first_name,contact_last_name) as name FROM {$dbprefix}users u LEFT JOIN {$dbprefix}contacts c ON u.user_contact = c.contact_id ORDER BY name";
            $result = do_query($query);
            while ($row = result2hash($result)) {
                $options[$row["id"]] = $row["name"];
            }
	    if ($canEdit) {
            	$output = create_selectbox("assignment_toggle", $options, $value);
	    }
	    else {
	    	$output = chooseSelectedValue("assignment_toggle", $options, $value);
	    }
            break;
        case "view":
            if ($CONFIG["index_link"] == "latest") {
                $latest_value = query2result("SELECT ticket FROM {$dbprefix}tickets WHERE parent = '$value' ORDER BY ticket DESC LIMIT 1");
                if ($latest_value) {
                    $value = $latest_value;
                }
            }
            $output = "<a href='?m=ticketsmith&amp;a=view&amp;ticket=$value'>$value&nbsp;";
			$output .= "<img src='images/icons/pencil.gif' border='0' alt='' /></a>";
            break;
	case "attach":
	    $output = "<a href='?m=ticketsmith&amp;a=attach&amp;ticket=$value'>";
	    $output .= "Link</a>";
	    break;
	case "doattach":
	    $output = "<a href='?m=ticketsmith&amp;a=attach&amp;newparent=$value&amp;dosql=reattachticket&amp;ticket=$ticket'>";
	    $output .= "Link</a>";
	    break;
        case "open_date":
            $output = get_time_ago($value);
            if ($CONFIG["warning_active"]) {
                if (time() - $value > $CONFIG["warning_age"] * 3600) {
                    $output = "<font color=\"" . $CONFIG["warning_color"] . "\"><xb>" . $output . "</strong></font>";
                }
            }
            break;
        case "activity_date":
            if (!$value) {
                $output = "<em>".$AppUI->_('none')."</em>";
            }
            else {
                $output = get_time_ago($value);
            }
            $latest_followup_type = query2result("SELECT type FROM {$dbprefix}tickets WHERE parent = '$ticket' ORDER BY timestamp DESC LIMIT 1");
            if ($latest_followup_type) {
                $latest_followup_type = preg_replace("/(\w+)\s.*/", "\\1", $latest_followup_type);
                $output .= " [$latest_followup_type]";
            }
            break;
        case "elapsed_date":
            $output = date($CONFIG["date_format"], $value);
            $time_ago = get_time_ago($value);
            $output .= " <em>($time_ago)</em>";
            break;
        case "body":
	    if ($CONFIG["wordwrap"]) {
	    	$value = word_wrap($value, 78);
	    }
            $value = htmlspecialchars($value);
            $output = "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\">\n";
            $output .= "<tr><td bgcolor=\"" . $CONFIG["ticket_color"] . "\">\n<tt><pre>\n";
            $url_find = "/(http|https|ftp|news|telnet|finger)(:\/\/[^ \">\\t\\r\\n]*)/";
            $url_replace = "<a href=\"\\1\\2\" target=\"new\">";
            $url_replace .= "<span style=\"font-size: 10pt;\">\\1\\2</span></a>";
            $value = preg_replace($url_find, $url_replace, $value);
	    $output .= stripslashes($value);
            $output .= "\n</pre></tt>\n</td></tr>\n</table>\n";
            break;
        case "followup":
            $output = "\n<tt>\n";
            $output .= "<textarea style='font-family: monospace;' name=\"followup\" wrap=\"hard\" cols=\"72\" rows=\"20\">\n";
            $signature = query2result("SELECT user_signature FROM {$dbprefix}users WHERE user_id = '$AppUI->user_id'");
            if ($signature) {
                $output .= "\n";
                $output .= "-- \n";
                $output .= $signature;
            }
            $output .= "\n\n";
            $output .= "---- ".$AppUI->_('Original message')." ----\n\n";
            if ($CONFIG["wordwrap"]) {
                $value = word_wrap($value, 70, true);
            }
            $value = htmlspecialchars($value);
            $output .= $value;
            $output .= "\n</textarea>\n";
            $output .= "</tt>\n";
            break;
        case "subject":
            $value = preg_replace("/\s*Re:\s*/i", "", $value);
            $value = preg_replace("/(\[\#\d+\])(\w+)/", "\\2", $value);
            $value = "Re: " . $value;
            $value = htmlspecialchars($value);
			@$output .= "<input type=\"text\" name=\"subject\" value=\"$value\" size=\"70\" />\n";
            break;
        case "cc":
            $value = htmlspecialchars($value);
			$output = "<input type=\"text\" name=\"cc\" value=\"$value\" size=\"70\" />";
            break;
        case "recipient":
            $value = htmlspecialchars($value);
			$output = "<input type=\"text\" name=\"recipient\" value=\"$value\" size=\"70\" />";
            break;
        case "original_author":
            if ($value) {
                $value = preg_replace('/\"/', '', $value);
                $output = htmlspecialchars($value);
            }
            else {
                $output = "<em>(".$AppUI->_('original ticket author').")</em>";
            }
            break;
        case "email":
            if ($value) {
                $value = preg_replace('/\"/', '', $value);
                $output = htmlspecialchars($value);
            }
            else {
                $output = "<em>".$AppUI->_('none')."</em>";
            }
            break;
		case 'ticket_company':
		    $q  = new DBQuery;
			$q->addTable('companies','co');
			$q->addQuery('co.*');
			$q->addWhere('co.company_id = '.$value);
			$sql = $q->prepare();
			if (!db_loadObject($sql, $obj)) {
				// it all dies!
			}
			$output = '<a href="?m=companies&amp;a=view&amp;company_id='.$value.'">'.$obj->company_name.'</a>';
			break;
		case 'ticket_project':
		    $q  = new DBQuery;
			$q->addTable('projects', 'pr');
			$q->addQuery('pr.*');
			$q->addWhere('pr.project_id = '.$value);
			$sql = $q->prepare();
			if (!db_loadObject($sql, $obj)) {
				// it all dies!
			}
			$output = '<a href="?m=projects&amp;a=view&amp;project_id='.$value.'">'.$obj->project_name.'</a>';
			break;
        default:
            $output = $value ? htmlspecialchars($value) : "<em>".$AppUI->_('none')."</em>";
    }
    return($output);

}

/* figure out parent & type */
if (isset($ticket)) {
    $dbprefix = dPgetConfig('dbprefix','');
    list($ticket_type, $ticket_parent) = query2array("SELECT type, parent FROM {$dbprefix}tickets WHERE ticket = '$ticket'");
}


?>

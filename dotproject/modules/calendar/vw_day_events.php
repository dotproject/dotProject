<?php /* CALENDAR $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $this_day, $first_time, $last_time, $company_id, $event_filter, $event_filter_list, $AppUI;

// load the event types
$types = dPgetSysVal('EventType');
$links = array();

$user_id = $AppUI->user_id;
$other_users = false;
$no_modify = false;

if (getPermission('admin', 'view')) {
	$other_users = true;
	if (($show_uid = (int)dPgetParam($_REQUEST, 'show_user_events', 0)) != 0) {
		$user_id = $show_uid;
		$no_modify = true;
		$AppUI->setState('event_user_id', $user_id);
	}
}

// assemble the links for the events
$events = CEvent::getEventsForPeriod($first_time, $last_time, $event_filter, $user_id);

$start_hour = dPgetConfig('cal_day_start');
$end_hour   = dPgetConfig('cal_day_end');

foreach ($events as $row) {
	$start_date = new CDate($row['event_start_date']);
	$end_date = new CDate($row['event_end_date']);
	
	//Adjust display start hour (as necessary)
	if ($start_hour > $start_date->format('%H')) {
		$start_hour = $start_date->format('%H');
	}
	if ($start_hour >  $end_date->format('%H') 
	    && $end_date->format('%Y%m%d') == $this_day->format('%Y%m%d')) {
		$start_hour = $end_date->format('%H');
	}
	
	//Adjust display end hour (as necessary)
	if ($end_hour < $end_date->format('%H')) {
		$end_hour = $end_date->format('%H');
	}
	if ($end_hour < $start_date->format('%H') 
	    && $end_date->format('%Y%m%d') == $this_day->format('%Y%m%d')) {
		$end_hour = $start_date->format('%H') + 1;
	}
}

$tf = $AppUI->getPref('TIMEFORMAT');

$dayStamp = $this_day->format(FMT_TIMESTAMP_DATE);

$start = $start_hour;
$end = $end_hour;
$inc = dPgetConfig('cal_day_increment');

if ($start === null) $start = 8;
if ($end === null) $end = 17;
if ($inc === null) $inc = 15;

//display adjusted events
$this_day->setTime($start, 0, 0);
$events2 = array();
foreach ($events as $row) {
	$start_date = new CDate($row['event_start_date']);
	$end_date = new CDate($row['event_end_date']);
	if ($start_date->before($this_day)) {
		$events2[$this_day->format('%H%M%S')][] = $row;
	} else {
		$events2[$start_date->format('%H%M%S')][] = $row;
	}
	
}


// calculate colums per each time row
$this_day->setTime($start, 0, 0);
$disp_columns = array();
for ($i=0, $n=($end-$start)*60/$inc; $i < $n; $i++) {
	$disp_columns[$i] = 0;
}
for ($i=0, $n=($end-$start)*60/$inc; $i < $n; $i++) {
	$timeStamp = $this_day->format('%H%M%S');
	if (@$events2[$timeStamp]) {
		$count = count($events2[$timeStamp]);
		for ($j = 0; $j < $count; $j++) {
			$row = $events2[$timeStamp][$j];
			
			$et = new CDate($row['event_end_date']);
			$et_date = new CDate($row['event_end_date']);
			$et_date->setTime(0, 0, 0);
			
			if ($et_date->after($this_day)) { 
				$rows = $n - $i;
			} else {
				$rows = (($et->getHour()*60 + $et->getMinute()) 
				         - ($this_day->getHour()*60 + $this_day->getMinute()))/$inc;
			}
			
			for ($k=$i; $k < ($i + $rows); $k++) {
				$disp_columns[$k]++;
			}
		}
	}
	$this_day->addSeconds(60*$inc);
}
//calculate maximum concurrent events
$disp_max_cols = 1; //need at least one blank column to follow time
foreach($disp_columns as $col_count) {
	$disp_max_cols = (($col_count > $disp_max_cols) ? $col_count : $disp_max_cols);
}

$html  = '<form method="post" name="pickFilter">';
$html .= ($AppUI->_('Event Filter') . ":" 
          . arraySelect($event_filter_list, 'event_filter', 
                        'onchange="javascript:document.pickFilter.submit()" class="text"', 
                        $event_filter, true));
if ($other_users) {
	$html .= ($AppUI->_("Show Events for") . ":" 
	          . '<select name="show_user_events" onchange="javascript:document.pickFilter.submit()"' 
	          . ' class="text">');
	$q = new DBQuery;
	$q->addTable('users', 'u');
	$q->addTable('contacts', 'con');
	$q->addQuery('user_id, user_username, contact_first_name, contact_last_name');
	$q->addWhere('user_contact = contact_id');
			
	if (($rows = $q->loadList())) {
		foreach ($rows as $row) {
			$html .= ('<option value="' . $row['user_id'] . '"' 
					  . (($user_id == $row['user_id']) ? ' selected="selected"' : '')
					  . '>'.htmlspecialchars($row['user_username']) . '</option>');
		}
	}
	$html .= '</select>';
	
}
$html .= '</form>';

$cal = new CMonthCalendar();
$html .= $cal->_drawBirthdays($this_day->format('%Y%m%d'));

$html .= '<table cellspacing="1" cellpadding="2" border="0" width="100%" class="tbl">';

$this_day->setTime($start, 0, 0);
for ($i=0, $n=($end-$start)*60/$inc; $i < $n; $i++) {
	$html .= "\n<tr>";
	
	$tm = $this_day->format($tf);
	$html .= ("\n\t" . '<td width="1%" align="right" nowrap="nowrap">' 
			  . ($this_day->getMinute() ? $tm : ('<b>' . $tm . '</b>')) . '</td>');

	$timeStamp = $this_day->format('%H%M%S');
	if (@$events2[$timeStamp]) {
		$count = count($events2[$timeStamp]);
		for ($j = 0; $j < $count; $j++) {
			$row = $events2[$timeStamp][$j];
			
			$et = new CDate($row['event_end_date']);
			$et_date = new CDate($row['event_end_date']);
			$et_date->setTime(0, 0, 0);
			
			if ($et_date->after($this_day)) { 
				$rows = $n - $i;
			} else {
				$rows = (($et->getHour()*60 + $et->getMinute()) 
				         - ($this_day->getHour()*60 + $this_day->getMinute()))/$inc;
			}
			
			$href = "?m=calendar&a=view&event_id=".$row['event_id'];
			$alt = $row['event_description'];

			$html .= "\n\t" .'<td class="event" rowspan="' . $rows . '" valign="top">';

			$html .= "\n" . '<table cellspacing="0" cellpadding="0" border="0"><tr>';
			$html .= ("\n<td>" . dPshowImage(dPfindImage('event'.$row['event_type'].'.png', 
			                                             'calendar'), 16, 16, ''));
			$html .= ("</td>\n<td>&nbsp;<b>" . $AppUI->_($types[$row['event_type']]) 
					  . '</b></td></tr></table>');


			$html .= (($href) 
					  ? ("\n\t\t" . '<a href="' . htmlspecialchars($href) 
			             . '" class="event" title="' . htmlspecialchars($alt) . '">') 
					  : '');
			$html .= "\n\t\t" . htmlspecialchars($row['event_title']);
			$html .= (($href) ? "\n\t\t</a>" : '');
			$html .= "\n\t</td>";
		}
	}
	
	for ($j = 0; $j < ($disp_max_cols - $disp_columns[$i]); $j++) {
		$html .= "\n\t" . '<td></td>';
	}
	
	$html .= "\n</tr>";

	$this_day->addSeconds(60*$inc);
}


$html .= '</table>';
echo $html;
?>

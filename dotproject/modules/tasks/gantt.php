<?php /* TASKS $Id$ */

/*
 * Gantt.php - by J. Christopher Pereira
 * TASKS $Id$
 */

include ("{$dPconfig['root_dir']}/lib/jpgraph/src/jpgraph.php");
include ("{$dPconfig['root_dir']}/lib/jpgraph/src/jpgraph_gantt.php");

$project_id = defVal( @$_REQUEST['project_id'], 0 );
$f = defVal( @$_REQUEST['f'], 0 );
global $showLabels;
global $showWork;
global $locale_char_set;

$showLabels = dPgetParam($_REQUEST, 'showLabels', false);
// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

require_once $AppUI->getModuleClass('projects');
$project =& new CProject;
$allowedProjects = $project->getAllowedRecords($AppUI->user_id, 'project_id, project_name');
$criticalTasks = ($project_id > 0) ? $project->getCriticalTasks($project_id) : NULL;
// pull valid projects and their percent complete information
$psql = "
SELECT project_id, project_color_identifier, project_name, project_start_date, project_end_date
FROM permissions, projects
LEFT JOIN tasks t1 ON projects.project_id = t1.task_project
WHERE project_active <> 0
" . (count($allowedProjects) ? "AND project_id IN (" . implode(',', array_keys($allowedProjects)) . ')' : '') ."
GROUP BY project_id
ORDER BY project_name
";
// echo "<pre>$psql</pre>";
$prc = db_exec( $psql );
echo db_error();
$pnums = db_num_rows( $prc );

$projects = array();
for ($x=0; $x < $pnums; $x++) {
	$z = db_fetch_assoc( $prc );
	$projects[$z["project_id"]] = $z;
}

// get any specifically denied tasks
$task =& new CTask;
$deny = $task->getDeniedRecords($AppUI->user_id);

// pull tasks

$select = "
tasks.task_id, task_parent, task_name, task_start_date, task_end_date, task_duration, task_duration_type,
task_priority, task_percent_complete, task_order, task_project, task_milestone, 
project_name, task_dynamic
";

$from = "tasks";
$join = "LEFT JOIN projects ON project_id = task_project";
$where = "project_active <> 0".($project_id ? "\nAND task_project = $project_id" : '');

switch ($f) {
	case 'all':
		$where .= "\nAND task_status > -1";
		break;
	case 'myproj':
		$where .= "\nAND task_status > -1\n	AND project_owner = $AppUI->user_id";
		break;
	case 'mycomp':
		$where .= "\nAND task_status > -1\n	AND project_company = $AppUI->user_company";
		break;
	case 'myinact':
		$from .= ", user_tasks";
		$where .= "
	AND task_project = projects.project_id
	AND user_tasks.user_id = $AppUI->user_id
	AND user_tasks.task_id = tasks.task_id
";
		break;
	default:
		$from .= ", user_tasks";
		$where .= "
	AND task_status > -1
	AND task_project = projects.project_id
	AND user_tasks.user_id = $AppUI->user_id
	AND user_tasks.task_id = tasks.task_id
";
		break;
}

$tsql = "SELECT $select FROM $from $join WHERE $where ORDER BY project_id, task_start_date";
##echo "<pre>$tsql</pre>".mysql_error();##

$ptrc = db_exec( $tsql );
$nums = db_num_rows( $ptrc );
echo db_error();
$orrarr[] = array("task_id"=>0, "order_up"=>0, "order"=>"");

//pull the tasks into an array
for ($x=0; $x < $nums; $x++) {
	$row = db_fetch_assoc( $ptrc );
	
	if($row["task_start_date"] == "0000-00-00 00:00:00"){
		$row["task_start_date"] = date("Y-m-d H:i:s");
	}

	// calculate or set blank task_end_date if unset
	if($row["task_end_date"] == "0000-00-00 00:00:00") {
		if($row["task_duration"]) {
			$row["task_end_date"] = db_unix2dateTime ( db_dateTime2unix( $row["task_start_date"] ) + SECONDS_PER_DAY * convert2days( $row["task_duration"], $row["task_duration_type"] ) );
		} else {
			$row["task_end_date"] = "";
		}
	}
		
	$projects[$row['task_project']]['tasks'][] = $row;
}

$width      = dPgetParam( $_GET, 'width', 600 );
//consider critical (concerning end date) tasks as well
$project_end = ($projects[$project_id]["project_end_date"] > $criticalTasks[0]['task_end_date']) ? $projects[$project_id]["project_end_date"] : $criticalTasks[0]['task_end_date'];
$start_date = dPgetParam( $_GET, 'start_date', $projects[$project_id]["project_start_date"] );
$end_date   = dPgetParam( $_GET, 'end_date', $project_end );

$count = 0;


$graph = new GanttGraph($width);
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
//$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY);

$graph->SetFrame(false);
$graph->SetBox(true, array(0,0,0), 2);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
//$graph->scale->day->SetStyle(DAYSTYLE_SHORTDATE2);

// This configuration variable is obsolete
$jpLocale = dPgetConfig( 'jpLocale' );
if ($jpLocale) {
	$graph->scale->SetDateLocale( $jpLocale );
}
//$graph->scale->SetDateLocale( $AppUI->user_locale );

if ($start_date && $end_date) {
	$graph->SetDateRange( $start_date, $end_date );
}
if (is_file( TTF_DIR."arialbd.ttf" )){
	$graph->scale->actinfo->SetFont(FF_ARIAL);
}
$graph->scale->actinfo->vgrid->SetColor('gray');
$graph->scale->actinfo->SetColor('darkgray');
if ($showWork=='1') {
	$graph->scale->actinfo->SetColTitles(array( $AppUI->_('Task name', UI_OUTPUT_RAW), $AppUI->_('Work', UI_OUTPUT_RAW), $AppUI->_('Start', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW)),array(230,16, 60,60));
} else {
	$graph->scale->actinfo->SetColTitles(array( $AppUI->_('Task name', UI_OUTPUT_RAW), $AppUI->_('Dur.', UI_OUTPUT_RAW), $AppUI->_('Start', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW)),array(230,16, 60,60));
}

$graph->scale->tableTitle->Set($projects[$project_id]["project_name"]);

// Use TTF font if it exists
// try commenting out the following two lines if gantt charts do not display
if (is_file( TTF_DIR."arialbd.ttf" ))
	$graph->scale->tableTitle->SetFont(FF_ARIAL,FS_BOLD,12);
$graph->scale->SetTableTitleBackground("#".$projects[$project_id]["project_color_identifier"]);
$graph->scale->tableTitle->Show(true);

//-----------------------------------------
// nice Gantt image
// if diff(end_date,start_date) > 90 days it shows only
//week number
// if diff(end_date,start_date) > 240 days it shows only
//month number
//-----------------------------------------
if ($start_date && $end_date){
        $min_d_start = new CDate($start_date);
        $max_d_end = new CDate($end_date);
        $graph->SetDateRange( $start_date, $end_date );
} else {
        // find out DateRange from gant_arr
        $d_start = new CDate();
        $d_end = new CDate();
        for($i = 0; $i < count(@$gantt_arr); $i++ ){
                $a = $gantt_arr[$i][0];
                $start = substr($a["task_start_date"], 0, 10);
                $end = substr($a["task_end_date"], 0, 10);

                $d_start->Date($start);
                $d_end->Date($end);

                if ($i == 0){
                        $min_d_start = $d_start;
                        $max_d_end = $d_end;
                } else {
                        if (Date::compare($min_d_start,$d_start)>0){
                                $min_d_start = $d_start;
                        }
                        if (Date::compare($max_d_end,$d_end)<0){
                                $max_d_end = $d_end;
                        }
                }
        }
}

// check day_diff and modify Headers
$day_diff = $min_d_start->dateDiff($max_d_end);

if ($day_diff > 240){
        //more than 240 days
        $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);
} else if ($day_diff > 90){
        //more than 90 days and less of 241
        $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HWEEK );
        $graph->scale->week->SetStyle(WEEKSTYLE_WNBR);
}


//This kludgy function echos children tasks as threads

function showgtask( &$a, $level=0 ) {
	/* Add tasks to gantt chart */

	global $gantt_arr;

	$gantt_arr[] = array($a, $level);	

}

function findgchild( &$tarr, $parent, $level=0 ){
	GLOBAL $projects;
	$level = $level+1;
	$n = count( $tarr );
	for ($x=0; $x < $n; $x++) {
		if($tarr[$x]["task_parent"] == $parent && $tarr[$x]["task_parent"] != $tarr[$x]["task_id"]){
			showgtask( $tarr[$x], $level );
			findgchild( $tarr, $tarr[$x]["task_id"], $level);
		}
	}
}

reset($projects);
$p = &$projects[$project_id];
$tnums = count( $p['tasks'] );

for ($i=0; $i < $tnums; $i++) {
	$t = $p['tasks'][$i];
	if ($t["task_parent"] == $t["task_id"]) {
		showgtask( $t );
		findgchild( $p['tasks'], $t["task_id"] );
	}
}

$hide_task_groups = false;

if($hide_task_groups) {
	for($i = 0; $i < count($gantt_arr); $i ++ ) {
		// remove task groups
		if($i != count($gantt_arr)-1 && $gantt_arr[$i + 1][1] > $gantt_arr[$i][1]) {
			// it's not a leaf => remove
			array_splice($gantt_arr, $i, 1);
			continue;
		}
	}
}

$row = 0;
for($i = 0; $i < count(@$gantt_arr); $i ++ ) {

	$a     = $gantt_arr[$i][0];
	$level = $gantt_arr[$i][1];

	if($hide_task_groups) $level = 0;

	$name = $a["task_name"];
	if ( $locale_char_set=='utf-8' && function_exists("utf8_decode") ) {
		$name = utf8_decode($name);
	}
	$name = strlen( $name ) > 34 ? substr( $name, 0, 33 ).'.' : $name ;	
	$name = str_repeat(" ", $level).$name;
    
	//using new jpGraph determines using Date object instead of string
	$start = $a["task_start_date"];
	$end_date = $a["task_end_date"];

	$end_date = new CDate($end_date);
//	$end->addDays(0);
	$end = $end_date->getDate();

	$start = new CDate($start);
//	$start->addDays(0);
	$start = $start->getDate();
	
	$progress = $a["task_percent_complete"];
	$flags    = ($a["task_milestone"] ? "m" : "");

	$cap = "";
	if(!$start || $start == "0000-00-00"){
		$start = !$end ? date("Y-m-d") : $end;
		$cap .= "(no start date)";
	}
	
	if(!$end) {
		$end = $start;
		$cap .= " (no end date)";
	} else {
		$cap = "";
	}

	$caption = "";
	if ($showLabels=='1') {
		$sql = "select ut.task_id, u.user_username, ut.perc_assignment from user_tasks ut, users u where u.user_id = ut.user_id and ut.task_id = ".$a["task_id"];
		$res = db_exec( $sql );
		while ($rw = db_fetch_row( $res )) {
			switch ($rw[2]) {
				case 100:
					$caption = $caption."".$rw[1].";";
					break;
				default:
					$caption = $caption."".$rw[1]."[".$rw[2]."%];";
					break;
			}
		}
		$caption = substr($caption, 0, strlen($caption)-1);
	}	
	
	if($flags == "m") {		
		$start = new CDate($start);
		$start->addDays(0);
		$s = $start->format($df);//
		$bar = new MileStone($row++, array($name, "", substr($s, 0, 10), substr($s, 0, 10)), $s, $s);		
		//caption of milestone shoud be date
		if ($showLabels=='1') {			
			$caption = $start->format($df);		
		}
	} else {
		$type = $a["task_duration_type"];
		$dur = $a["task_duration"];
		if ($type == 24) {
			$dur *= $dPconfig['daily_working_hours'];
		} 
		
		if ($showWork=='1') {
			$work_hours = 0;
			$_days_sql  = "SELECT ROUND(SUM(t.task_duration*u.perc_assignment/100),2) FROM tasks t left join user_tasks u on t.task_id = u.task_id WHERE t.task_id = ".$a['task_id']." AND t.task_duration_type = 24 AND t.task_milestone  ='0' AND t.task_dynamic = 0";
			$_hours_sql = "SELECT ROUND(SUM(t.task_duration*u.perc_assignment/100),2) FROM tasks t left join user_tasks u on t.task_id = u.task_id WHERE t.task_id = ".$a['task_id']." AND t.task_duration_type = 1 AND t.task_milestone  ='0' AND t.task_dynamic = 0";
			$work_hours = db_loadResult($_days_sql) * $dPconfig['daily_working_hours'];
			$work_hours += db_loadResult($_hours_sql);
			//due to the round above, we don't want to print decimals unless they really exist
			//$work_hours = rtrim($work_hours, "0");
			$dur = $work_hours;

			/*
			$handle = fopen ( 'c:\a.txt', 'a+');
			fwrite($handle, $_days_sql);
			fclose($handle);
			*/
		}
		

		$dur .= " h";
		$enddate = new CDate($end);
		$startdate = new CDate($start);
		$bar = new GanttBar($row++, array($name, $dur, $startdate->format($df), $enddate->format($df)), substr($start, 2, 8), substr($end, 2, 8), $cap, $a["task_dynamic"] == 1 ? 0.1 : 0.6);
		$bar->progress->Set($progress/100);
		if (is_file( TTF_DIR."arialbd.ttf" )) {
			$bar->title->SetFont(FF_ARIAL,FS_NORMAL,8);
		}
	    if($a["task_dynamic"] == 1){
	    	if (is_file( TTF_DIR."arialbd.ttf" )){
	        	$bar->title->SetFont(FF_ARIAL,FS_BOLD, 8);
		}
    		$bar->rightMark->Show();
            $bar->rightMark->SetType(MARK_RIGHTTRIANGLE);
            $bar->rightMark->SetWidth(3);
            $bar->rightMark->SetColor('black');
            $bar->rightMark->SetFillColor('black');

            $bar->leftMark->Show();
            $bar->leftMark->SetType(MARK_LEFTTRIANGLE);
            $bar->leftMark->SetWidth(3);
            $bar->leftMark->SetColor('black');
            $bar->leftMark->SetFillColor('black');
            
            $bar->SetPattern(BAND_SOLID,'black');
	    }
	}
	//adding captions
	$bar->caption = new TextProperty($caption);
	$bar->caption->Align("left","center");

        // show tasks which are both finished and past in (dark)gray
        if ($progress >= 100 && $end_date->isPast() && get_class($bar) == "ganttbar") {
                $bar->caption->SetColor('darkgray');
                $bar->title->SetColor('darkgray');
                $bar->setColor('darkgray');
                $bar->SetFillColor('darkgray');
                $bar->SetPattern(BAND_SOLID,'gray');
                $bar->progress->SetFillColor('darkgray');
                $bar->progress->SetPattern(BAND_SOLID,'gray',98);
        }
	$q = new DBQuery;
	$q->addTable('task_dependencies');
	$q->addQuery('dependencies_task_id');
	$q->addWhere('dependencies_req_task_id=' . $a["task_id"]);
	$query = $q->loadHashList(1);

	foreach($query as $dep) {
		// find row num of dependencies
		for($d = 0; $d < count($gantt_arr); $d++ ) {
			if($gantt_arr[$d][0]["task_id"] == $dep["dependencies_task_id"]) {
				$bar->SetConstrain($d, CONSTRAIN_ENDSTART);
			}
		}
	}
	$q->clear();
	$graph->Add($bar);
}
$today = date("y-m-d");
$vline = new GanttVLine($today, $AppUI->_('Today', UI_OUTPUT_RAW));
if (is_file( TTF_DIR."arialbd.ttf" )) {
	$vline->title->SetFont(FF_ARIAL,FS_BOLD,10);
}
$graph->Add($vline);
$graph->Stroke();
?>

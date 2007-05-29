<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}


/*
 * Gantt.php - by J. Christopher Pereira
 * TASKS $Id$
 */


include ($AppUI->getLibraryClass( 'jpgraph/src/jpgraph'));
include ($AppUI->getLibraryClass( 'jpgraph/src/jpgraph_gantt'));

global $caller, $locale_char_set, $showLabels, $showWork, $sortByName, $showLabels, $showPinned, $showArcProjs, $showHoldProjs, $showDynTasks, $showLowTasks, $user_id;


$sortByName = dPgetParam( $_REQUEST, 'sortByName', false );
$project_id = defVal( @$_REQUEST['project_id'], 0 );
$f = defVal( @$_REQUEST['f'], 0 );

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

require_once $AppUI->getModuleClass('projects');
$project =& new CProject;
$criticalTasks = ($project_id > 0) ? $project->getCriticalTasks($project_id) : NULL;

// pull valid projects and their percent complete information

$q = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, project_color_identifier, project_name, project_start_date, project_end_date');
$q->addJoin('tasks', 't1', 'projects.project_id = t1.task_project');
$q->addWhere('project_status != 7');
$q->addGroup('project_id');
$q->addOrder('project_name');
$project->setAllowedSQL($AppUI->user_id, $q);
$projects = $q->loadHashList('project_id');
$q->clear();

##############################################
/* gantt is called now by the todo page, too.
** there is a different filter approach in todo
** so we have to tweak a little bit,
** also we do not have a special project available
*/
$caller = defVal( @$_REQUEST['caller'], null );
if ($caller == 'todo') {
	$user_id = defVal( @$_REQUEST['user_id'], 0 );

	$projects[$project_id]['project_name'] = $AppUI->_('Todo for').' '.dPgetUsername($user_id);
	$projects[$project_id]['project_color_identifier'] = 'ff6000';

	$showLabels = dPgetParam($_REQUEST, 'showLabels', false);
	$showPinned = dPgetParam( $_REQUEST, 'showPinned', false );
	$showArcProjs = dPgetParam( $_REQUEST, 'showArcProjs', false );
	$showHoldProjs = dPgetParam( $_REQUEST, 'showHoldProjs', false );
	$showDynTasks = dPgetParam( $_REQUEST, 'showDynTasks', false );
	$showLowTasks = dPgetParam( $_REQUEST, 'showLowTasks', true);

	$q = new DBQuery;
	$q->addQuery('ta.*');
	$q->addQuery('project_name, project_id, project_color_identifier');
	$q->addQuery('tp.task_pinned');
	$q->addTable('projects', 'pr');
	$q->addTable('tasks', 'ta');
	$q->addTable('user_tasks', 'ut');
	$q->leftJoin('user_task_pin', 'tp', 'tp.task_id = ta.task_id and tp.user_id = ' . $user_id);

	$q->addWhere('ut.task_id = ta.task_id');
	$q->addWhere("ut.user_id = '$user_id'");
	$q->addWhere('( ta.task_percent_complete < 100 or ta.task_percent_complete is null)');
	$q->addWhere("ta.task_status = '0'");
	$q->addWhere("pr.project_id = ta.task_project");
	if (!$showArcProjs)
		$q->addWhere('project_status <> 7');
	if (!$showLowTasks)
		$q->addWhere('task_priority >= 0');
	if (!$showHoldProjs)
		$q->addWhere('project_status != 4');
	if (!$showDynTasks)
		$q->addWhere('task_dynamic != 1');
	if ($showPinned)
		$q->addWhere('task_pinned = 1');
	
	$q->addGroup('ta.task_id');

	if ($sortByName) {
		$q->addOrder('ta.task_name, ta.task_end_date');
		$q->addOrder('task_priority DESC');
	} else {
			$q->addOrder('ta.task_end_date');
			$q->addOrder('task_priority DESC');
	}
##############################################################
} else {
	// pull tasks
	$q = new DBQuery;
	$q->addTable('tasks', 't');
	$q->addQuery('t.task_id, task_parent, task_name, task_start_date, task_end_date, task_duration, task_duration_type, task_priority, task_percent_complete, task_order, task_project, task_milestone, project_name, task_dynamic');
	$q->addJoin('projects', 'p', 'project_id = t.task_project');
	$q->addWhere('project_status != 7');

	if ($sortByName)
		$q->addOrder('project_id, t.task_name, task_start_date');
	else 	
		$q->addOrder('project_id, task_start_date');

	if ($project_id) {
	        $q->addWhere('task_project = '.$project_id);
	}
	switch ($f) {
	        case 'all':
	                $q->addWhere('task_status > -1');
	                break;
	        case 'myproj':
	                $q->addWhere('task_status > -1');
	                $q->addWhere('project_owner = '.$AppUI->user_id);
	                break;
	        case 'mycomp':
	                $q->addWhere('task_status > -1');
	                $q->addWhere('project_company = '.$AppUI->user_company);
	                break;
	        case 'myinact':
	                $q->addTable('user_tasks', 'ut');
	                $q->addWhere('task_project = p.project_id');
	                $q->addWhere('ut.user_id = '.$AppUI->user_id);
	                $q->addWhere('ut.task_id = t.task_id');
	                break;
	        default:
	                $q->addTable('user_tasks', 'ut');
	                $q->addWhere('task_status > -1');
	                $q->addWhere('task_project = p.project_id');
	                $q->addWhere('ut.user_id = '.$AppUI->user_id);
	                $q->addWhere('ut.task_id = t.task_id');
	                break;
	}

}

// get any specifically denied tasks
$task =& new CTask;
$task->setAllowedSQL($AppUI->user_id, $q);

$proTasks = $q->loadHashList('task_id');
$orrarr[] = array('task_id'=>0, 'order_up'=>0, 'order'=>'');

$end_max = '0000-00-00 00:00:00';
$start_min = date('Y-m-d H:i:s');

//pull the tasks into an array
foreach ($proTasks as $row) {

        if($row['task_start_date'] == '0000-00-00 00:00:00'){
                $row['task_start_date'] = date('Y-m-d H:i:s');
        }

		$tsd = new CDate($row['task_start_date']);

		if ($tsd->before(new CDate($start_min)))
			$start_min = $row['task_start_date'];

        // calculate or set blank task_end_date if unset
        if($row['task_end_date'] == '0000-00-00 00:00:00') {
                if($row['task_duration']) {
                        $row['task_end_date'] = db_unix2dateTime ( db_dateTime2unix( $row['task_start_date'] ) + SECONDS_PER_DAY * convert2days( $row['task_duration'], $row['task_duration_type'] ) );
                } else {
                        $row['task_end_date'] = '';
                }
        }
		
		$ted = new CDate($row['task_end_date']);

		if ($ted->after(new CDate($end_max)))
			$end_max = $row['task_end_date'];

        $projects[$row['task_project']]['tasks'][] = $row;
}
$q->clear();
$width      = dPgetParam( $_GET, 'width', 600 );
//consider critical (concerning end date) tasks as well
if ($caller != 'todo') {
	$start_min = $projects[$project_id]['project_start_date'];
	$end_max = ($projects[$project_id]['project_end_date'] > $criticalTasks[0]['task_end_date']) ? $projects[$project_id]['project_end_date'] : $criticalTasks[0]['task_end_date'];
}
$start_date = dPgetParam( $_GET, 'start_date', $start_min );
$end_date   = dPgetParam( $_GET, 'end_date', $end_max );

$count = 0;


$graph = new GanttGraph($width);
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
//$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY);

$graph->SetFrame(false);
$graph->SetBox(true, array(0,0,0), 2);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
//$graph->scale->day->SetStyle(DAYSTYLE_SHORTDATE2);

$pLocale = setlocale(LC_TIME, 0); // get current locale for LC_TIME
$res = @setlocale(LC_TIME, $AppUI->user_lang[2]);
if ($res) { // Setting locale doesn't fail
	$graph->scale->SetDateLocale( $AppUI->user_lang[2] );
}
setlocale(LC_TIME, $pLocale);

if ($start_date && $end_date) {
        $graph->SetDateRange( $start_date, $end_date );
}
if (is_file( TTF_DIR.'arialbd.ttf' )){
        $graph->scale->actinfo->SetFont(FF_ARIAL);
}
$graph->scale->actinfo->vgrid->SetColor('gray');
$graph->scale->actinfo->SetColor('darkgray');

if ($caller == 'todo') {
	if ($showWork=='1') {
	        $graph->scale->actinfo->SetColTitles(array( $AppUI->_('Task name', UI_OUTPUT_RAW), $AppUI->_('Project name', UI_OUTPUT_RAW), $AppUI->_('Work', UI_OUTPUT_RAW), $AppUI->_('Start', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW)),array(180,50, 60, 60,60));
	} else {
	        $graph->scale->actinfo->SetColTitles(array( $AppUI->_('Task name', UI_OUTPUT_RAW), $AppUI->_('Project name', UI_OUTPUT_RAW), $AppUI->_('Dur.', UI_OUTPUT_RAW), $AppUI->_('Start', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW)),array(180,50, 60, 60,60));
	}
} else {
	if ($showWork=='1') {
	        $graph->scale->actinfo->SetColTitles(array( $AppUI->_('Task name', UI_OUTPUT_RAW), $AppUI->_('Work', UI_OUTPUT_RAW), $AppUI->_('Start', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW)),array(230,60, 60,60));
	} else {
	        $graph->scale->actinfo->SetColTitles(array( $AppUI->_('Task name', UI_OUTPUT_RAW), $AppUI->_('Dur.', UI_OUTPUT_RAW), $AppUI->_('Start', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW)),array(230,60, 60,60));
	}

}
$graph->scale->tableTitle->Set($projects[$project_id]['project_name']);

// Use TTF font if it exists
// try commenting out the following two lines if gantt charts do not display
if (is_file( TTF_DIR.'arialbd.ttf' ))
        $graph->scale->tableTitle->SetFont(FF_ARIAL,FS_BOLD,12);
$graph->scale->SetTableTitleBackground('#'.$projects[$project_id]['project_color_identifier']);
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
                $start = substr($a['task_start_date'], 0, 10);
                $end = substr($a['task_end_date'], 0, 10);

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
                if($tarr[$x]['task_parent'] == $parent && $tarr[$x]['task_parent'] != $tarr[$x]['task_id']){
                        showgtask( $tarr[$x], $level );
                        findgchild( $tarr, $tarr[$x]['task_id'], $level);
                }
        }
}

reset($projects);
//$p = &$projects[$project_id];
foreach ($projects as $p) {
	$tnums = count( $p['tasks'] );

	for ($i=0; $i < $tnums; $i++) {
	        $t = $p['tasks'][$i];
	        if ($t['task_parent'] == $t['task_id']) {
	                showgtask( $t );
	                findgchild( $p['tasks'], $t['task_id'] );
	        }
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

        $name = $a['task_name'];
        if ( $locale_char_set=='utf-8' && function_exists('utf8_decode') ) {
                $name = utf8_decode($name);
        }
        $name = strlen( $name ) > 34 ? substr( $name, 0, 33 ).'.' : $name ;
        $name = str_repeat(' ', $level).$name;
		
		if ($caller == 'todo') { 
			$pname = $a['project_name'];
	        if ( $locale_char_set=='utf-8' && function_exists('utf8_decode') ) {
	                $pname = utf8_decode($pname);
	        }
	        $pname = strlen( $pname ) > 14 ? substr( $pname, 0, 5 ).'...'.substr( $pname, -5, 5 ): $pname ;
		}
        //using new jpGraph determines using Date object instead of string
        $start = $a['task_start_date'];
        $end_date = $a['task_end_date'];

        $end_date = new CDate($end_date);
//        $end->addDays(0);
        $end = $end_date->getDate();

        $start = new CDate($start);
//        $start->addDays(0);
        $start = $start->getDate();

        $progress = $a['task_percent_complete'] + 0;
	
	if ($progress > 100) 
		$progress = 100;
	elseif ($progress < 0)
		$progress = 0;

        $flags    = ($a['task_milestone'] ? 'm' : '');

        $cap = '';
        if(!$start || $start == '0000-00-00'){
                $start = !$end ? date('Y-m-d') : $end;
                $cap .= '(no start date)';
        }

        if(!$end) {
                $end = $start;
                $cap .= ' (no end date)';
        } else {
                $cap = '';
        }

        $caption = '';
        if ($showLabels=='1') {
                $q = new DBQuery;
                $q->addTable('user_tasks', 'ut');
                $q->addTable('users', 'u');
                $q->addQuery('ut.task_id, u.user_username, ut.perc_assignment');
                $q->addWhere('u.user_id = ut.user_id');
                $q->addWhere('ut.task_id = '.$a['task_id']);
                $res = $q->loadList();
                foreach ($res as $rw) {
                        switch ($rw['perc_assignment']) {
                                case 100:
                                        $caption = $caption.''.$rw['user_username'].';';
                                        break;
                                default:
                                        $caption = $caption.''.$rw['user_username'].'['.$rw['perc_assignment'].'%];';
                                        break;
                        }
                }
                $q->clear();
                $caption = substr($caption, 0, strlen($caption)-1);
        }

        if($flags == 'm') {
                $start = new CDate($start);
                $start->addDays(0);
                $s = $start->format($df);
				if ($caller == 'todo')
                	$bar  = new MileStone ($row++,array($name, $pname, '', substr($s, 0, 10), substr($s, 0, 10)) , $a['task_start_date'], $s);
				else 
					$bar  = new MileStone ($row++,array($name, '', substr($s, 0, 10), substr($s, 0, 10)) , $a['task_start_date'], $s);
                if (is_file( TTF_DIR.'arial.ttf' )) {
									$bar->title->SetFont(FF_ARIAL,FS_NORMAL,8);
								}
                //caption of milestone should be date
                if ($showLabels=='1') {
                        $caption = $start->format($df);
                }
                $bar->title->SetColor('#CC0000');
                $graph->Add($bar);
        } else {
                $type = $a['task_duration_type'];
                $dur = $a['task_duration'];
                if ($type == 24) {
                        $dur *= $dPconfig['daily_working_hours'];
                }

                if ($showWork=='1') {
                        $work_hours = 0;
                        $q = new DBQuery;
                        $q->addTable('tasks', 't');
                        $q->addJoin('user_tasks', 'u', 't.task_id = u.task_id');
                        $q->addQuery('ROUND(SUM(t.task_duration*u.perc_assignment/100),2) AS wh');
                        $q->addWhere('t.task_duration_type = 24');
                        $q->addWhere('t.task_id = '.$a['task_id']);

                        $wh = $q->loadResult();
                        $work_hours = $wh * $dPconfig['daily_working_hours'];
                        $q->clear();

                        $q = new DBQuery;
                        $q->addTable('tasks', 't');
                        $q->addJoin('user_tasks', 'u', 't.task_id = u.task_id');
                        $q->addQuery('ROUND(SUM(t.task_duration*u.perc_assignment/100),2) AS wh');
                        $q->addWhere('t.task_duration_type = 1');
                        $q->addWhere('t.task_id = '.$a['task_id']);

                        $wh2 = $q->loadResult();
                        $work_hours += $wh2;
                        $q->clear();
                        //due to the round above, we don't want to print decimals unless they really exist
                        //$work_hours = rtrim($work_hours, '0');
                        $dur = $work_hours;

                        /*
                        $handle = fopen ( 'c:\a.txt', 'a+');
                        fwrite($handle, $_days_sql);
                        fclose($handle);
                        */
                }


                $dur .= ' h';
                $enddate = new CDate($end);
                $startdate = new CDate($start);
                if ($caller == 'todo')
					$bar = new GanttBar($row++, array($name, $pname, $dur, $startdate->format($df), $enddate->format($df)), substr($start, 2, 8), substr($end, 2, 8), $cap, $a['task_dynamic'] == 1 ? 0.1 : 0.6);
				else
					$bar = new GanttBar($row++, array($name, $dur, $startdate->format($df), $enddate->format($df)), substr($start, 2, 8), substr($end, 2, 8), $cap, $a['task_dynamic'] == 1 ? 0.1 : 0.6);
                $bar->progress->Set(min(($progress/100),1));
                if (is_file( TTF_DIR.'arial.ttf' )) {
                        $bar->title->SetFont(FF_ARIAL,FS_NORMAL,8);
                }
            if($a['task_dynamic'] == 1){
                    if (is_file( TTF_DIR.'arialbd.ttf' )){
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
        $bar->caption->Align('left','center');

        // show tasks which are both finished and past in (dark)gray
        if ($progress >= 100 && $end_date->isPast() && get_class($bar) == 'ganttbar') {
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
        $q->addWhere('dependencies_req_task_id=' . $a['task_id']);
        $query = $q->loadList();

        foreach($query as $dep) {
                // find row num of dependencies
                for($d = 0; $d < count($gantt_arr); $d++ ) {
                        if($gantt_arr[$d][0]['task_id'] == $dep['dependencies_task_id']) {
                                $bar->SetConstrain($d, CONSTRAIN_ENDSTART);
                        }
                }
        }
        $q->clear();
        $graph->Add($bar);
}
$today = date('y-m-d');
$vline = new GanttVLine($today, $AppUI->_('Today', UI_OUTPUT_RAW));
if (is_file( TTF_DIR.'arialbd.ttf' )) {
        $vline->title->SetFont(FF_ARIAL,FS_BOLD,10);
}
$graph->Add($vline);
$graph->Stroke();
?>

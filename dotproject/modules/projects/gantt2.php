<?php /* TASKS $Id$ */
include ("{$dPconfig['root_dir']}/lib/jpgraph/src/jpgraph.php");
include ("{$dPconfig['root_dir']}/lib/jpgraph/src/jpgraph_gantt.php");



// orginal: global $company_id, $dept_ids, $department, $locale_char_set, $proFilter, $projectStatus, $showInactive, $showLabels;
global $company_id, $dept_ids, $department, $locale_char_set, $proFilter, $projectStatus, $showInactive, $showLabels, $showAllGantt; // $showAllGantt == Gantt with tasks and users


// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

$filter1 = array();
$projectStatus = dPgetSysVal( 'ProjectStatus' );
$projectStatus = arrayMerge( array( '-2' => $AppUI->_('All w/o in progress')), $projectStatus);
$proFilter = dPgetParam($_REQUEST, 'proFilter', '-1');

if ($proFilter == '-2'){
        $filter1[] = "project_status != 3 ";
} else if ($proFilter != '-1') {
        $filter1[] = "project_status = $proFilter ";
}
if ($company_id != 0) {
        $filter1[] = "project_company = $company_id ";
}
//$filter1 = ($proFilter == '-1') ? '' : " AND project_status = $proFilter ";
if ($showInactive != '1')
	$filter1[] = "project_active <> 0 ";
$pjobj =& new CProject;
$allowed_projects = $pjobj->getAllowedSQL($AppUI->user_id);
$where = array_merge($filter1, $allowed_projects);

// pull valid projects and their percent complete information
// changed "ti.task_end_date AS project_actual_end_date" to  "max(t1.task_end_date) AS project_actual_end_date" -- max()
$sql = "
SELECT u.user_username user_name, t.task_name task_name, t.task_start_date task_start_date, t.task_milestone task_milestone, ut.perc_assignment perc_assignment,
t.task_end_date task_end_date, p.project_color_identifier project_color_identifier, p.project_name project_name
FROM tasks t 
LEFT JOIN user_tasks ut ON t.task_id = ut.task_id
LEFT JOIN users u ON u.user_id = ut.user_id
LEFT JOIN  projects p ON p.project_id = t.task_project
LEFT JOIN companies c ON p.project_company = c.company_id
ORDER BY 1, 2, 5, 4 
";
$q  = new DBQuery;
$q->addTable('tasks', 't');
$q->addQuery('u.user_username user_name, t.task_name task_name, t.task_start_date task_start_date, t.task_milestone
		task_milestone, ut.perc_assignment perc_assignment, t.task_end_date task_end_date, p.project_color_identifier
		project_color_identifier, p.project_name project_name');
$q->addJoin('user_tasks', 'ut', 't.task_id = ut.task_id');
$q->addJoin('users', 'u', 'u.user_id = ut.user_id');
$q->addJoin('projects', 'p', 'p.project_id = t.task_project');
$q->addJoin('companies', 'c', 'p.project_company = c.company_id');
$q->addOrder('1, 2, 5, 4');
$tasks = $q->loadList();

$sqlMinMax = "
SELECT min(t.task_start_date) task_min_date, max(t.task_end_date) task_max_date
FROM users u, tasks t, user_tasks ut
WHERE u.user_id = ut.user_id AND ut.task_id = t.task_id
";
$q  = new DBQuery;
$q->addTable('users', 'u');
$q->addTable('tasks', 't');
$q->addTable('user_tasks', 'ut');
$q->addQuery('min(t.task_start_date) task_min_date, max(t.task_end_date) task_max_date');
$q->addWhere('u.user_id = ut.user_id AND ut.task_id = t.task_id');
$taskMinMax = $q->loadList();

$width      = dPgetParam( $_GET, 'width', 600 );
$start_date = dPgetParam( $_GET, 'start_date', 0 );
$end_date   = dPgetParam( $_GET, 'end_date', 0 );
$showTaskGantt = dPgetParam( $_GET, 'showTaskGantt', 0 );


$graph2 = new GanttGraph($width);
$graph2->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);

$graph2->SetFrame(false);
$graph2->SetBox(true, array(0,0,0), 2);
$graph2->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

$jpLocale = dPgetConfig( 'jpLocale' );
if ($jpLocale) {
	$graph2->scale->SetDateLocale( $jpLocale );
}

if ($start_date && $end_date) {
	$graph2->SetDateRange( $start_date, $end_date );
}

//$graph2->scale->actinfo->SetFont(FF_ARIAL);
$graph2->scale->actinfo->vgrid->SetColor('gray');
$graph2->scale->actinfo->SetColor('darkgray');
$graph2->scale->actinfo->SetColTitles(array( $AppUI->_('User Name', UI_OUTPUT_RAW), $AppUI->_('Start Date', UI_OUTPUT_RAW), $AppUI->_('Finish', UI_OUTPUT_RAW), $AppUI->_(' ')),array(160,10, 70,70));


$tableTitle = ($proFilter == '-1') ? $AppUI->_('All Tasks By Users') : $projectStatus[$proFilter];
$graph2->scale->tableTitle->Set($tableTitle);

// Use TTF font if it exists
// try commenting out the following two lines if gantt charts do not display
if (is_file( TTF_DIR."arialbd.ttf" ))
	$graph2->scale->tableTitle->SetFont(FF_ARIAL,FS_BOLD,12);
$graph2->scale->SetTableTitleBackground("#eeeeee");
$graph2->scale->tableTitle->Show(true);

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
        $graph2->SetDateRange( $start_date, $end_date );
} else {
        // find out DateRange from gant_arr
        $d_start = new CDate();
        $d_end = new CDate();
        for($i = 0; $i < count(@$taskMinMax); $i++ ){
                $start = substr($taskMinMax["task_min_date"], 0, 10);
                $end = substr($taskMinMax["task_max_date"], 0, 10);

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
        $graph2->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);
} else if ($day_diff > 90){
        //more than 90 days and less of 241
        $graph2->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HWEEK );
        $graph2->scale->week->SetStyle(WEEKSTYLE_WNBR);
}

$row = 0;

if (!is_array($tasks) || sizeof($tasks) == 0) {
 $d = new CDate();
 $bar = new GanttBar($row++, array(' '.$AppUI->_('No tasks found'),  ' ', ' ', ' '), $d->getDate(), $d->getDate(), ' ', 0.6);
 $bar->title->SetCOlor('red');
 $graph2->Add($bar);
}



if (is_array($tasks)) {
$nameUser = "";
foreach($tasks as $t) {
	
	if($nameUser != $t['user_name']	)
	{
		$row++;
		$barTmp = new GanttBar($row++, array($t["user_name"], "", ""," "), "0", "0;" , 0.6);						
		$barTmp->title->SetColor("#".$t['project_color_identifier']);
		$barTmp->SetFillColor("#".$t['project_color_identifier']);
		$barTmp->title ->SetFont(FF_FONT1, FF_BOLD);		
		$graph2->Add($barTmp);
	}
		
	if ( $locale_char_set=='utf-8' && function_exists("utf_decode") ) {
		$name = strlen( utf8_decode($t["task_name"]) ) > 25 ? substr( utf8_decode($t["task_name"]), 0, 22 ).'...' : utf8_decode($t["task_name"]) ;
		$nameUser = $t['user_name'];
	} else {
		//while using charset different than UTF-8 we need not to use utf8_deocde
		$name = strlen( $t["task_name"] ) > 25 ? substr( $t["task_name"], 0, 22 ).'...' : $t["task_name"] ;
		$nameUser = $t['user_name'];		
	}

	//using new jpGraph determines using Date object instead of string
	$start = ($t["task_start_date"] > "0000-00-00 00:00:00") ? $t["task_start_date"] : date("Y-m-d H:i:s");
	$end_date   = $t["task_end_date"];
        $actual_end = $t["task_end_date"] ? $t["task_end_date"] : " ";


	$end_date = new CDate($end_date);
//	$end->addDays(0);
	$end = $end_date->getDate();

	$start = new CDate($start);
//	$start->addDays(0);
	$start = $start->getDate();

	//$progress = $p['project_percent_complete'];

	$caption = "";
	if(!$start || $start == "0000-00-00"){
		$start = !$end ? date("Y-m-d") : $end;
		$caption .= "(no start date)";
	}

	if(!$end) {
		$end = $start;
		$caption .= " (no end date)";
	} else {
		$cap = "";
	}

       if ($showLabels){
                $caption .= $t['project_name']." (".$t['perc_assignment']."%)";
//                $caption .= $p['project_active'] <> 0 ? $AppUI->_('active') : $AppUI->_('inactive');
        }

	


	if ($t["task_milestone"] != 1)
	{
	$enddate = new CDate($end);
	$startdate = new CDate($start);
        $bar = new GanttBar($row++, array($name, $startdate->format($df), $enddate->format($df), /*substr($actual_end, 0, 10))*/" "), $start, $actual_end, $cap, 0.6);
   //     $bar->progress->Set($progress/100);
        $bar->title->SetFont(FF_FONT1,FS_NORMAL,10);
        $bar->SetFillColor("#".$t['project_color_identifier']);
        $bar->SetPattern(BAND_SOLID,"#".$t['project_color_identifier']);

		//adding captions
		$bar->caption = new TextProperty($caption);
		$bar->caption->Align("left","center");
		
	}
	else
	{
		$bar  = new MileStone ($row++, $name, $start, (substr($start, 0, 10)));
		$bar->title->SetColor("#CC0000");		

	}

	$graph2->Add($bar);
	
	// If showAllGant checkbox is checked 
}
} // End of check for valid projects array.

$today = date("y-m-d");
$vline = new GanttVLine($today, $AppUI->_('Today', UI_OUTPUT_RAW));
$graph->Add($vline);
$graph2->Stroke();
?>
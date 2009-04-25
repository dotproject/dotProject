<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

// Output the PDF
// make the PDF file
if ($project_id != 0)
{
	$sql = "SELECT project_name FROM projects WHERE project_id=$project_id";
	$pname = db_loadResult($sql);
}
else
	$pname = $AppUI->_('All Projects');

$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';

require($AppUI->getLibraryClass('ezpdf/class.ezpdf'));

$pdf =& new Cezpdf($paper='A4',$orientation='landscape');
$pdf->ezSetCmMargins(1, 2, 1.5, 1.5);
$pdf->selectFont("$font_dir/Helvetica.afm");

$pdf->ezText(safe_utf8_decode(dPgetConfig('company_name')), 12);

$date = new CDate();
$pdf->ezText("\n" . $date->format($df) , 8);
$next_week = new CDate($date);
$next_week->addSpan(new Date_Span(array(7,0,0,0)));

$pdf->selectFont("$font_dir/Helvetica-Bold.afm");
$pdf->ezText("\n" . safe_utf8_decode($AppUI->_('Project Upcoming Task Report')), 12);
$pdf->ezText("$pname", 15);
$pdf->ezText(safe_utf8_decode($AppUI->_('Tasks Due to be Completed By')) . " " . $next_week->format($df) , 10);
$pdf->ezText("\n");
$pdf->selectFont("$font_dir/Helvetica.afm");
$title = null;
$options = array(
	'showLines' => 2,
	'showHeadings' => 1,
	'fontSize' => 9,
	'rowGap' => 4,
	'colGap' => 5,
	'xPos' => 50,
	'xOrientation' => 'right',
	'width'=>'750',
	'shaded'=> 0,
	'cols'=>array(
	 	0=>array('justification'=>'left','width'=>250),
		1=>array('justification'=>'left','width'=>95),
		2=>array('justification'=>'center','width'=>75),
		3=>array('justification'=>'center','width'=>75),
		4=>array('justification'=>'center','width'=>75))
);

$hasResources = $AppUI->isActiveModule('resources');
if ($hasResources)
	$hasResources = getPermission('resources', 'view');
// Build the data to go into the table.
$pdfdata = array();
$columns = array();
$columns[] = "<b>" . safe_utf8_decode($AppUI->_('Task Name')). "</b>";
$columns[] = "<b>" . safe_utf8_decode($AppUI->_('Owner')) . "</b>";
$columns[] = "<b>" . safe_utf8_decode($AppUI->_('Assigned Users')) . "</b>";
if ($hasResources)
	$columns[] = "<b>" . safe_utf8_decode($AppUI->_('Assigned Resources')) . "</b>";
$columns[] = "<b>" . safe_utf8_decode($AppUI->_('Finish Date')) . "</b>";

// Grab the completed items in the last week
$q =& new DBQuery;
$q->addQuery('a.*');
$q->addQuery('b.user_username');
$q->addTable('tasks', 'a');
$q->leftJoin('users', 'b', 'a.task_owner = b.user_id');
$q->addWhere('task_percent_complete < 100');
if ($project_id != 0)
	$q->addWhere('task_project = ' . $project_id);
$q->addWhere("task_end_date between '" . $date->format(FMT_DATETIME_MYSQL) . "' and '" . $next_week->format(FMT_DATETIME_MYSQL) . "'");
$tasks = $q->loadHashList('task_id');

if ($err = db_error()) {
	$AppUI->setMsg($err, UI_MSG_ERROR);
	$AppUI->redirect();
}
// Now grab the resources allocated to the tasks.
$task_list = array_keys($tasks);
$assigned_users = array();
// Build the array
foreach ($task_list as $tid)
	$assigned_users[$tid] = array();

if (count($tasks)) {
	$q->clear();
	$q->addQuery('a.task_id, a.perc_assignment, b.*, c.*');
	$q->addTable('user_tasks', 'a');
	$q->leftJoin('users', 'b', 'a.user_id = b.user_id');
	$q->leftJoin('contacts', 'c', 'b.user_contact = c.contact_id');
	$q->addWhere('a.task_id in (' . implode(',', $task_list) . ')');
	$res = $q->exec();
	if (! $res) {
		$AppUI->setMsg(db_error(), UI_MSG_ERROR);
		$q->clear();
		$AppUI->redirect();
	}
	while ($row = db_fetch_assoc($res)) {
		$assigned_users[$row['task_id']][$row['user_id']] 
		= safe_utf8_decode("$row[contact_first_name] $row[contact_last_name] [$row[perc_assignment]%]");
	}
	$q->clear();
}

$resources = array();
if ($hasResources && count($tasks)) {
	foreach ($task_list as $tid) {
		$resources[$tid] = array();
	}
	$q->clear();
	$q->addQuery('a.*, b.resource_name');
	$q->addTable('resource_tasks', 'a');
	$q->leftJoin('resources', 'b', 'a.resource_id = b.resource_id');
	$q->addWhere('a.task_id in (' . implode(',', $task_list) . ')');
	$res = $q->exec();
	if (! $res) {
		$AppUI->setMsg(db_error(), UI_MSG_ERROR);
		$AppUI->redirect();
	}
	while ($row = db_fetch_assoc($res)) {
		$resources[$row['task_id']][$row['resource_id']] 
		= safe_utf8_decode($row['resource_name']) . " [" . $row['percent_allocated'] . "%]";
	}
}

// Build the data columns
foreach ($tasks as $task_id => $detail) {
	$row =& $pdfdata[];
	$row[] = safe_utf8_decode($detail['task_name']);
	$row[] = safe_utf8_decode($detail['user_username']);
	$row[] = implode("\n",$assigned_users[$task_id]);
	if ($hasResources)
		$row[] = implode("\n", $resources[$task_id]);
	$end_date = new CDate($detail['task_end_date']);
	$row[] = $end_date->format($df);
}

$pdf->ezTable($pdfdata, $columns, $title, $options);

$pdf->ezStream();
?>

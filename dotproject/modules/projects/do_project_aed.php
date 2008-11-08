<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$obj = new CProject();
$msg = '';

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

require_once($AppUI->getSystemClass('CustomFields'));
// convert dates to SQL format first
if ($obj->project_start_date) {
	$date = new CDate($obj->project_start_date);
	$obj->project_start_date = $date->format(FMT_DATETIME_MYSQL);
}
if ($obj->project_end_date) {
	$date = new CDate($obj->project_end_date);
	$date->setTime(23, 59, 59);
	$obj->project_end_date = $date->format(FMT_DATETIME_MYSQL);
}
if ($obj->project_actual_end_date) {
	$date = new CDate($obj->project_actual_end_date);
	$obj->project_actual_end_date = $date->format(FMT_DATETIME_MYSQL);
}

// let's check if there are some assigned departments to project
if(!dPgetParam($_POST, "project_departments", 0)){
	$obj->project_departments = implode(",", dPgetParam($_POST, "dept_ids", array()));
}

$del = dPgetParam($_POST, 'del', 0);

// prepare (and translate) the module name ready for the suffix
if ($del) {
	$project_id = dPgetParam($_POST, 'project_id', 0);
	$canDelete = $obj->canDelete($msg, $project_id);
	if (!$canDelete) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	}
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		$AppUI->setMsg("Project deleted", UI_MSG_ALERT);
		$AppUI->redirect("m=projects");
	}
} 
else {
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$isNotNew = @$_POST['project_id'];
		
		if ($importTask_projectId = dPgetParam($_POST, 'import_tasks_from', '0')) {
			$scale_project = dPgetParam($_POST, 'scale_project', '0');
			$obj->importTasks($importTask_projectId, $scale_project);
		}
		$AppUI->setMsg($isNotNew ? 'Project updated' : 'Project inserted', UI_MSG_OK, true);

 		$custom_fields = New CustomFields($m, 'addedit', $obj->project_id, 'edit');
 		$custom_fields->bind($_POST);
 		$sql = $custom_fields->store($obj->project_id); // Store Custom Fields


	}
	$AppUI->redirect();
}
?>

<?php //$Id$
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$del = isset($_POST['del']) ? $_POST['del'] : 0;
$rm = isset($_POST['rm']) ? $_POST['rm'] : 0;
$hassign = @$_POST['hassign'];
$htasks = @$_POST['htasks'];
$store = dPgetParam($_POST, 'store', 0);
$chUTP = dPgetParam($_POST, 'chUTP', 0);
$percentage_assignment = dPgetParam($_POST, 'percentage_assignment');
$user_task_priority = dPgetParam($_POST, 'user_task_priority');
$user_id = @$_POST['user_id'];

// prepare the percentage of assignment per user as required by CTask::updateAssigned()
$hperc_assign_ar = array();
if (isset($hassign)) {
	$tarr = explode(',', $hassign);
	foreach ($tarr as $uid) {
		if (intval($uid) > 0) {
			$hperc_assign_ar[$uid] = $percentage_assignment;
		}
	}
}

// prepare a list of tasks to process
$htasks_ar = array();
if (isset($htasks)) {
	$tarr = explode(',', $htasks);
	foreach ($tarr as $tid) {
		if (intval($tid) > 0) {
			$htasks_ar[] = $tid;
		}
	}
}

$sizeof = sizeof($htasks_ar);
for ($i=0; $i <= $sizeof; $i++) {
	
	$_POST['task_id'] = $htasks_ar[$i];
	
	// verify that task_id is not NULL
	if ($_POST['task_id'] > 0) {
		$obj = new CTask();
		
		if (!$obj->bind($_POST)) {
			$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
			$AppUI->redirect();
		}
		
		if ($rm && $del) {
			$overAssignment = $obj->updateAssigned($hassign , $hperc_assign_ar, true, true);
			if ($overAssignment) {
				$AppUI->setMsg('Some Users could not be unassigned from Task', UI_MSG_ERROR);
			} else {
				// Don't do anything because we might have other tasks to change
				// $AppUI->setMsg('User(s) unassigned from Task', UI_MSG_OK);
				// $AppUI->redirect();
			}
		} else if (($rm || $del)) {
			if (($msg = $obj->removeAssigned($user_id))) {
				$AppUI->setMsg($msg, UI_MSG_ERROR);
				
			} else {
				$AppUI->setMsg('User unassigned from Task', UI_MSG_OK);
			}
		}
		if (isset($hassign) && ! $del == 1) {
			$overAssignment = $obj->updateAssigned($hassign , $hperc_assign_ar, false, false);
			//check if OverAssignment occured, database has not been updated in this case
			if ($overAssignment) {
				$AppUI->setMsg(('The following Users have not been assigned in order to prevent' 
				                . ' from Over-Assignment:'), UI_MSG_ERROR);
				$AppUI->setMsg('<br />'.$overAssignment, UI_MSG_ERROR, true);
			} else {
				$AppUI->setMsg('User(s) assigned to Task', UI_MSG_OK);
			}
		}
		// process the user specific task priority
		if ($chUTP == 1) {
			$obj->updateUserSpecificTaskPriority($user_task_priority, $user_id);
			$AppUI->setMsg('User specific Task Priority updated', UI_MSG_OK, true);
		}
		
		if ($store == 1) {
			if (($msg = $obj->store())) {
				$AppUI->setMsg($msg, UI_MSG_ERROR, true);
				
			} else {
				$AppUI->setMsg('Task(s) updated', UI_MSG_OK, true);
			}
		}
	}
}
if ($rm && $del) {
	$AppUI->setMsg('User(s) unassigned from Task', UI_MSG_OK);
}
$AppUI->redirect();
?>

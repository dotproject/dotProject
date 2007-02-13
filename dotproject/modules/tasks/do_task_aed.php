<?php /* TASKS $Id$ */

function setItem($item_name, $defval = null) {
	if (isset($_POST[$item_name]))
		return $_POST[$item_name];
	return $defval;
}
$adjustStartDate = setItem('set_task_start_date');
$del = isset($_POST['del']) ? $_POST['del'] : 0;
$task_id = setItem('task_id', 0);
$hassign = setItem('hassign');
$hperc_assign = setItem('hperc_assign');
$hdependencies = setItem('hdependencies');
$notify = setItem('task_notify', 0);
$comment = setItem('email_comment','');
$sub_form = isset($_POST['sub_form']) ? $_POST['sub_form'] : 0;

if ($sub_form) {
	// in add-edit, so set it to what it should be
	$AppUI->setState('TaskAeTabIdx', $_POST['newTab']);
	if (isset($_POST['subform_processor'])) {
		$root = $dPconfig['root_dir'];
		if (isset($_POST['subform_module']))
			$mod = $AppUI->checkFileName($_POST['subform_module']);
		else
			$mod = 'tasks';
		$proc = $AppUI->checkFileName($_POST['subform_processor']);
		include "$root/modules/$mod/$proc.php";
	} 
} else {

	// Include any files for handling module-specific requirements
	foreach (findTabModules('tasks', 'addedit') as $mod) {
		$fname = dPgetConfig('root_dir') . "/modules/$mod/tasks_dosql.addedit.php";
		dprint(__FILE__, __LINE__, 3, "checking for $fname");
		if (file_exists($fname))
			require_once $fname;
	}

	$obj = new CTask();

	// If we have an array of pre_save functions, perform them in turn.
	if (isset($pre_save)) {
		foreach ($pre_save as $pre_save_function)
			$pre_save_function();
	} else {
		dprint(__FILE__, __LINE__, 1, "No pre_save functions.");
	}

	// Find the task if we are set
	$task_end_date = null;
	if ($task_id) {
		$obj->load($task_id);
		$task_end_date = new CDate($obj->task_end_date);
	}

	if ( isset($_POST)) {
		$obj->bind($_POST);
	}

	if (! $obj->task_owner)
		$obj->task_owner = $AppUI->user_id;

	if (!$obj->bind( $_POST )) {
		$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
		$AppUI->redirect();
	}
		
	// Check to see if the task_project has changed
	if (isset($_POST['new_task_project']) && $_POST['new_task_project'] && ($obj->task_project != $_POST['new_task_project'])) {
		$obj->task_project = $_POST['new_task_project'];
		$obj->task_parent  = $obj->task_id;
  }

	// Map task_dynamic checkboxes to task_dynamic values for task dependencies.
	if ( $obj->task_dynamic != 1 ) {
		$task_dynamic_delay = setItem("task_dynamic_nodelay", '0');
		if (in_array($obj->task_dynamic, $tracking_dynamics)) {
			$obj->task_dynamic = $task_dynamic_delay ? 21 : 31;
		} else {
			$obj->task_dynamic = $task_dynamic_delay ? 11 : 0;
		}
	}
    
	// Let's check if task_dynamic is unchecked
	if( !array_key_exists("task_dynamic", $_POST) ){
		$obj->task_dynamic = false;
	}
	
	// Make sure task milestone is set or reset as appropriate
	if (! isset($_POST['task_milestone']))
		$obj->task_milestone = false;

	//format hperc_assign user_id=percentage_assignment;user_id=percentage_assignment;user_id=percentage_assignment;
	$tmp_ar = explode(";", $hperc_assign);
	$hperc_assign_ar = array();
	for ($i = 0; $i < sizeof($tmp_ar); $i++) {
		$tmp = explode("=", $tmp_ar[$i]);
		if (count($tmp) > 1)
			$hperc_assign_ar[$tmp[0]] = $tmp[1];
		else
			$hperc_assign_ar[$tmp[0]] = 100;
	}

	// let's check if there are some assigned departments to task
	$obj->task_departments = implode(",", setItem("dept_ids", array()));

	// convert dates to SQL format first
	if ($obj->task_start_date) {
		$date = new CDate( $obj->task_start_date );
		$obj->task_start_date = $date->format( FMT_DATETIME_MYSQL );
	}
	$end_date = null;
	if ($obj->task_end_date) {
		$end_date = new CDate( $obj->task_end_date );
		$obj->task_end_date = $end_date->format( FMT_DATETIME_MYSQL );
	}


	require_once("./classes/CustomFields.class.php");

	// prepare (and translate) the module name ready for the suffix
	if ($del) {
		if (($msg = $obj->delete())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		} else {
			$AppUI->setMsg( 'Task deleted');
			$AppUI->redirect( '', -1 );
		}
	} 
    else {
		if (($msg = $obj->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect(); // Store failed don't continue?
		} else {
			$custom_fields = New CustomFields( $m, 'addedit', $obj->task_id, "edit" );
 			$custom_fields->bind( $_POST );
 			$sql = $custom_fields->store( $obj->task_id ); // Store Custom Fields

			// Now add any task reminders
			// If there wasn't a task, but there is one now, and
			// that task date is set, we need to set a reminder.
			if (empty($task_end_date) || (! empty($end_date) && $task_end_date->dateDiff($end_date)) )
				$obj->addReminder();
			$AppUI->setMsg( $task_id ? 'Task updated' : 'Task added', UI_MSG_OK);
		}

		if (isset($hassign)) {
			$obj->updateAssigned( $hassign , $hperc_assign_ar);
		}
		
		if (isset($hassign)) {
			$obj->updateAssigned( $hassign , $hperc_assign_ar);
		}
		
		if (isset($hdependencies) && !empty($hdependencies)) {
			// there are dependencies set!
			
			// backup initial start and end dates
			$tsd = new CDate ($obj->task_start_date);
			$ted = new CDate ($obj->task_end_date);

			// updating the table recording the 
			// dependency relations with this task
			$obj->updateDependencies( $hdependencies );
			
			// we will reset the task's start date based upon dependencies
			// and shift the end date appropriately
			if ($adjustStartDate && !is_null($hdependencies)) {

							// load already stored task data for this task
							$tempTask = new CTask();
							$tempTask->load( $obj->task_id );

							// shift new start date to the last dependency end date
							$nsd = new CDate ($tempTask->get_deps_max_end_date( $tempTask ) );
							
							// prefer Wed 8:00 over Tue 16:00 as start date
							$nsd = $nsd->next_working_day();
							 
							// prepare the creation of the end date
							$ned = new CDate();
							$ned->copy($nsd);
							
							if (empty($obj->task_start_date)) {
								// appropriately calculated end date via start+duration
								$ned->addDuration($obj->task_duration, $obj->task_duration_type);		
								
							} else { 			
								// calc task time span start - end
								$d = $tsd->calcDuration($ted);
	
								// Re-add (keep) task time span for end date.
								// This is independent from $obj->task_duration.
								// The value returned by Date::Duration() is always in hours ('1') 
								$ned->addDuration($d, '1');
	
							}
							
							// prefer tue 16:00 over wed 8:00 as an end date
							$ned = $ned->prev_working_day();
							
							$obj->task_start_date = $nsd->format( FMT_DATETIME_MYSQL );
							$obj->task_end_date = $ned->format( FMT_DATETIME_MYSQL );						
							
							$q = new DBQuery;
					    $q->addTable('tasks', 't');							
							$q->addUpdate('task_start_date', $obj->task_start_date);	
              $q->addUpdate('task_end_date', $obj->task_end_date);
              $q->addWhere('task_id = '.$obj->task_id);
              $q->addWhere('task_dynamic != 1');
              $q->exec();
              $q->clear();
						}

		}
		// If there is a set of post_save functions, then we process them

		if (isset($post_save)) {
			foreach ($post_save as $post_save_function) {
				$post_save_function();
			}
		}

		if ($notify) {
			if ($msg = $obj->notify($comment)) {
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			}
		}
		
		$AppUI->redirect();
	}

} // end of if subform
?>

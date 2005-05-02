<?php /* TASKS $Id$ */

function setItem($item_name, $defval = null) {
	if (isset($_POST[$item_name]))
		return $_POST[$item_name];
	return $defval;
}

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
	if ($task_id)
		$obj->load($task_id);

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
	if (isset($_POST['new_task_project']) && $_POST['new_task_project'])
		$obj->task_project = $_POST['new_task_project'];

		
    // Let's check if task_dynamic is unchecked
    if( !array_key_exists("task_dynamic", $_POST) ){
        $obj->task_dynamic = false;
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
	if ($obj->task_end_date) {
		$date = new CDate( $obj->task_end_date );
		$obj->task_end_date = $date->format( FMT_DATETIME_MYSQL );
	}


	require_once("./classes/CustomFields.class.php");
	//echo '<pre>';print_r( $hassign );echo '</pre>';die;
	// prepare (and translate) the module name ready for the suffix
	if ($del) {
		if (($msg = $obj->delete())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		} else {
			$AppUI->setMsg( 'Task deleted');
			$AppUI->redirect( '', -1 );
		}
	} else {
		if (($msg = $obj->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect(); // Store failed don't continue?
		} else {
			$custom_fields = New CustomFields( $m, 'addedit', $obj->task_id, "edit" );
 			$custom_fields->bind( $_POST );
 			$sql = $custom_fields->store( $obj->task_id ); // Store Custom Fields

			$AppUI->setMsg( $task_id ? 'Task updated' : 'Task added', UI_MSG_OK);
		}

		if (isset($hassign)) {
			$obj->updateAssigned( $hassign , $hperc_assign_ar);
		}
		
		if (isset($hdependencies)) {
			$obj->updateDependencies( $hdependencies );
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

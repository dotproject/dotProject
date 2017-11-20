<?php /* PROJECTDESIGNER $Id: do_task_bulk_aed.php,v 1.2 2007/06/19 11:43:04 pedroix Exp $ */
global $AppUI;
$project_id = (int)dPgetParam( $_POST, 'project_id', 0 );
$selected = dPgetCleanParam( $_POST, 'bulk_selected_task', 0 );
$bulk_task_project = dPgetCleanParam( $_POST, 'bulk_task_project', '' );
$bulk_task_parent = dPgetCleanParam( $_POST, 'bulk_task_parent', '' );
$bulk_task_dependency = dPgetCleanParam( $_POST, 'bulk_task_dependency', '' );
$bulk_task_priority = dPgetCleanParam( $_POST, 'bulk_task_priority', '' );
$bulk_task_access = dPgetCleanParam( $_POST, 'bulk_task_access', '' );
$bulk_task_assign = dPgetCleanParam( $_POST, 'bulk_task_assign', '' );
$bulk_task_assign_perc = dPgetCleanParam( $_POST, 'bulk_task_assign_perc', '' );
$bulk_task_unassign = dPgetCleanParam( $_POST, 'bulk_task_unassign', '' );
$bulk_task_other = dPgetCleanParam( $_POST, 'bulk_task_other', '' );
$bulk_task_owner = dPgetCleanParam( $_POST, 'bulk_task_owner', '' );
$bulk_task_type = dPgetCleanParam( $_POST, 'bulk_task_type', '' );
$bulk_task_duration = dPgetCleanParam( $_POST, 'bulk_task_duration', '' );
$bulk_task_durntype = dPgetCleanParam( $_POST, 'bulk_task_durntype', '');
$bulk_task_start_date = dPgetCleanParam( $_POST, 'add_task_bulk_start_date', '' );
if ($bulk_task_start_date) {
   $start_date = new CDate($bulk_task_start_date);
   $bulk_start_date = $start_date->format(FMT_DATETIME_MYSQL);
}
$bulk_task_end_date = dPgetCleanParam( $_POST, 'add_task_bulk_end_date', '' );
if ($bulk_task_end_date) {
   $end_date = new CDate($bulk_task_end_date);
   $bulk_end_date = $end_date->format(FMT_DATETIME_MYSQL);
}
$bulk_move_date = intval(dPgetParam( $_POST, 'bulk_move_date', '' ));
$bulk_task_percent_complete = dPgetCleanParam( $_POST, 'bulk_task_percent_complete', '' );

//Lets store the panels view options of the user:
$pdo = new CProjectDesignerOptions();
$pdo->pd_option_user = $AppUI->user_id;
$pdo->pd_option_view_project = (int)dPgetParam( $_POST, 'opt_view_project', 0 );
$pdo->pd_option_view_gantt = (int)dPgetParam( $_POST, 'opt_view_gantt', 0 );
$pdo->pd_option_view_tasks = (int)dPgetParam( $_POST, 'opt_view_tasks', 0 );
$pdo->pd_option_view_actions = (int)dPgetParam( $_POST, 'opt_view_actions', 0 );
$pdo->pd_option_view_addtasks = (int)dPgetParam( $_POST, 'opt_view_addtsks', 0 );
$pdo->pd_option_view_files = (int)dPgetParam( $_POST, 'opt_view_files', 0 );
$pdo->store();

if (is_array($selected) && count( $selected )) {
	$upd_task = new CTask();
	foreach ($selected as $key => $val) {
		if ($key) {
			$upd_task->load($key);
		}

//Action: Modify Percent Complete
		if ( isset($_POST['bulk_task_percent_complete']) && $bulk_task_percent_complete!='' && $bulk_task_percent_complete) {
			if ($upd_task->task_id) {
				$upd_task->task_percent_complete = $bulk_task_percent_complete;
				$upd_task->store();
			}
		}

//Action: Move Task Date
		if ( isset($_POST['bulk_move_date']) && $bulk_move_date!='' && $bulk_move_date) {
			if ($upd_task->task_id && (intval($upd_task->task_dynamic)!=1 && !$upd_task->getDependencies($upd_task->task_id))) {
			      $offSet = $bulk_move_date;
                        $start_date = new CDate($upd_task->task_start_date);
                        $start_date->addDays($offSet);
                        $upd_task->task_start_date = $start_date->format(FMT_DATETIME_MYSQL);
                        $end_date = new CDate($upd_task->task_end_date);
                        $end_date->addDays($offSet);
                        $upd_task->task_end_date = $end_date->format(FMT_DATETIME_MYSQL);
				$upd_task->store();
                        $upd_task->shiftDependentTasks();
			}
		}

//Action: Modify Start Date
		if ( isset($_POST['add_task_bulk_start_date']) && $bulk_task_start_date!='' && $bulk_start_date) {
			if ($upd_task->task_id) {
				$upd_task->task_start_date = $bulk_start_date;
				$upd_task->store();
			}
		}

//Action: Modify End Date
		if ( isset($_POST['add_task_bulk_end_date']) && $bulk_task_end_date!='' && $bulk_end_date) {
			if ($upd_task->task_id) {
				$upd_task->task_end_date = $bulk_end_date;
				$upd_task->store();
			}
		}

//Action: Modify Duration
		if ( isset($_POST['bulk_task_duration']) && $bulk_task_duration!='' && is_numeric($bulk_task_duration)) {
			if ($upd_task->task_id) {
				$upd_task->task_duration = $bulk_task_duration;
                        //set duration type to hours (1)
				$upd_task->task_duration_type = $bulk_task_durntype ? $bulk_task_durntype : 1 ;
				$upd_task->store();
			}
		}

//Action: Modify Task Owner
		if ( isset($_POST['bulk_task_owner']) && $bulk_task_owner!='' && $bulk_task_owner) {
			if ($upd_task->task_id) {
				$upd_task->task_owner = $bulk_task_owner;
				$upd_task->store();
			}
		}

//Action: Move to Project
		if ( isset($_POST['bulk_task_project']) && $bulk_task_project!='' && $bulk_task_project) {
			if ($upd_task->task_id) {
				$upd_task->task_project = $bulk_task_project;
				//Set parent to self task
				$upd_task->task_parent = $key;
				$upd_task->store();
			}
		}

//Action: Change parent
		if ( isset($_POST['bulk_task_parent']) && $bulk_task_parent!='') {
			if ($upd_task->task_id) {
				//If parent is self task
				if ($bulk_task_parent=='0'){
					$upd_task->task_parent = $key;
					$upd_task->store();
				//if not, then the task will be child to the selected parent
				} else {
					$upd_task->task_parent = $bulk_task_parent;
					$upd_task->store();
				}
			}
		}

//Action: Change dependency
		if ( isset($_POST['bulk_task_dependency']) && $bulk_task_dependency!='') {
			if ($upd_task->task_id) {
				//If parent is self task
				//print_r($bulk_task_dependency);die;
				if ($bulk_task_dependency=='0'){
					$upd_task->task_dynamic = 0;
					$upd_task->store();
                              $q = new DBQuery;
                              $q->setDelete('task_dependencies');
                              $q->addWhere('dependencies_task_id='.$upd_task->task_id);
                              $q->exec();
				} elseif (!($bulk_task_dependency==$upd_task->task_id)){
					$upd_task->task_dynamic = 31;
					$upd_task->store();
                              $q = new DBQuery;
                              $q->addTable('task_dependencies');
                              $q->addReplace('dependencies_task_id',$upd_task->task_id);
                              $q->addReplace('dependencies_req_task_id',$bulk_task_dependency);
                              $q->exec();
            			//Lets recalc the dependency
            			$dep_task = new CTask();
            			$dep_task->load($bulk_task_dependency);
      			      if ($dep_task->task_id) {
      			            $dep_task->shiftDependentTasks();
      			      }
				}
			}
		}

//Action: Modify priority
		if ( isset($_POST['bulk_task_priority']) && $bulk_task_priority!='') {
			if ($upd_task->task_id) {
				$upd_task->task_priority = $bulk_task_priority;
				$upd_task->store();
			}
		}

//Action: Modify Access
		if ( isset($_POST['bulk_task_access']) && $bulk_task_access!='') {
			if ($upd_task->task_id) {
				$upd_task->task_access = $bulk_task_access;
				$upd_task->store();
			}
		}

//Action: Modify Type
		if ( isset($_POST['bulk_task_type']) && $bulk_task_type!='') {
			if ($upd_task->task_id) {
				$upd_task->task_type = $bulk_task_type;
				$upd_task->store();
			}
		}

//Action: Assign User
		if ( isset($_POST['bulk_task_assign']) && $bulk_task_assign!='') {
		    $upd_task = new CTask();
		    $upd_task->load($key);
		    if ($upd_task->task_id)
                       $upd_task->updateAssigned($bulk_task_assign,array($bulk_task_assign=>$bulk_task_assign_perc),false,false);
		    if ($upd_task->task_project && $upd_task->task_id && $upd_task->task_notify)
		    	     $upd_task->notify();
		}

//Action: Unassign User
		if ( isset($_POST['bulk_task_unassign']) && $bulk_task_unassign!='') {
		    $upd_task = new CTask();
		    $upd_task->load($key);
			if ($upd_task->task_id)
				$upd_task->removeAssigned($bulk_task_unassign);

		}

//Action: Other Actions
		if ( isset($_POST['bulk_task_other']) && $bulk_task_other!='') {

		    if ($upd_task->task_id) {
		    	//Option 1 - Mark as finished
				if ($bulk_task_other=='1') {
					$upd_task->task_percent_complete = 100;
					if (!$upd_task->task_end_date || $upd_task->task_end_date=='0000-00-00 00:00:00') {
						$end_date = null;
						$end_date = new CDate();
						$upd_task->task_end_date = $end_date->format( FMT_DATETIME_MYSQL );
					}
					$upd_task->store();
		    	//Option 2 - Mark as milestone
				} elseif ($bulk_task_other=='2') {
					$upd_task->task_milestone = 1;
					$upd_task->store();
		    	//Option 3 - Mark as non milestone
				} elseif ($bulk_task_other=='3') {
					$upd_task->task_milestone = 0;
					$upd_task->store();
		    	//Option 4 - Mark as dynamic
				} elseif ($bulk_task_other=='4') {
					$upd_task->task_dynamic = 1;
					$upd_task->store();
		    	//Option 5 - Mark as non dynamic
				} elseif ($bulk_task_other=='5') {
					$upd_task->task_dynamic = 0;
					$upd_task->store();
		    	//Option 6 - Add Task Reminder
				} elseif ($bulk_task_other=='6') {
					$upd_task->addReminder();
		    	//Option 7 - Mark as non dynamic
				} elseif ($bulk_task_other=='7') {
					$upd_task->clearReminder(true);
		    	//Option 8 - Mark as active
				} elseif ($bulk_task_other=='8') {
					$upd_task->task_status = '0';
					$upd_task->store();
		    	//Option 9 - Mark as inactive
				} elseif ($bulk_task_other=='9') {
					$upd_task->task_status = '-1';
					$upd_task->store();
		    	//Option 99 (always at the bottom) - Delete
				} elseif ($bulk_task_other=='99') {
		    		$upd_task->delete();
				}
		    }
		}
		echo db_error();
	}
}
$AppUI->redirect('m=projectdesigner&project_id='.$project_id);
?>

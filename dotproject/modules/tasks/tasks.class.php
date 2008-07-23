<?php /* TASKS $Id$ */

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

require_once($AppUI->getSystemClass('libmail'));
require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('projects'));
require_once($AppUI->getSystemClass('event_queue'));
require_once($AppUI->getSystemClass('date'));

// user based access
$task_access = array('0'=>'Public',
					 '4'=>'Privileged',
					 '2'=>'Participant',
					 '1'=>'Protected',
					 '3'=>'Private');

/*
 * TASK DYNAMIC VALUE:
 * 0  = default(OFF), no dep tracking of others, others do track
 * 1  = dynamic, umbrella task, no dep tracking, others do track
 * 11 = OFF, no dep tracking, others do not track
 * 21 = FEATURE, dep tracking, others do not track
 * 31 = ON, dep tracking, others do track
 */

// When calculating a task's start date only consider
// end dates of tasks with these dynamic values.
$tracked_dynamics = array('0' => '0',
						  '1' => '1',
						  '2' => '31');
// Tasks with these dynamics have their dates updated when
// one of their dependencies changes. (They track dependencies)
$tracking_dynamics = array('0' => '21',
						   '1' => '31');

/*
 * CTask Class
 */
class CTask extends CDpObject 
{
	/** @var int */
	var $task_id = NULL;
	/** @var string */
	var $task_name = NULL;
	/** @var int */
	var $task_parent = NULL;
	var $task_milestone = NULL;
	var $task_project = NULL;
	var $task_owner = NULL;
	var $task_start_date = NULL;
	var $task_duration = NULL;
	var $task_duration_type = NULL;
	/** @deprecated */
	var $task_hours_worked = NULL;
	var $task_end_date = NULL;
	var $task_status = NULL;
	var $task_priority = NULL;
	var $task_percent_complete = NULL;
	var $task_description = NULL;
	var $task_target_budget = NULL;
	var $task_related_url = NULL;
	var $task_creator = NULL;
	
	var $task_order = NULL;
	var $task_client_publish = NULL;
	var $task_dynamic = NULL;
	var $task_access = NULL;
	var $task_notify = NULL;
	var $task_departments = NULL;
	var $task_contacts = NULL;
	var $task_custom = NULL;
	var $task_type	 = NULL;
	
	
	function CTask() {
		$this->CDpObject('tasks', 'task_id');
	}
	
	function __toString() {
		return $this -> link . '/' . $this -> type . '/' . $this -> length;
	}
	
	// overload check
	function check() {
		global $AppUI;
		
		if ($this->task_id === NULL) {
			return 'task id is NULL';
		}
		// ensure changes to checkboxes are honoured
		$this->task_milestone = intval($this->task_milestone);
		$this->task_dynamic	  = intval($this->task_dynamic);
		
		$this->task_percent_complete = intval($this->task_percent_complete);
		
		if ($this->task_milestone) {
			$this->task_duration = '0';
		} else if (!($this->task_duration)) {
			$this->task_duration = '1';
		}
		if (!$this->task_creator) {
			$this->task_creator = $AppUI->user_id;
		}
		if (!$this->task_duration_type) {
			$this->task_duration_type = 1;
		}
		if (!$this->task_related_url) {
			$this->task_related_url = '';
		}
		if (!$this->task_notify) {
			$this->task_notify = 0;
		}
		
		/*
		 * Check for bad or circular task relationships (dep or child-parent).
		 * These checks are definately not exhaustive it is still quite possible
		 * to get things in a knot.
		 * Note: some of these checks may be problematic and might have to be removed
		 */
		static $addedit;
		if (!isset($addedit)) {
			$addedit = dPgetParam($_POST, 'dosql', '') == 'do_task_aed' ? true : false;
		}
		$this_dependencies = array();
		
		/*
		 * If we are called from addedit then we want to use the incoming
		 * list of dependencies and attempt to stop bad deps from being created
		 */
		if ($addedit) {
			$hdependencies = dPgetParam($_POST, 'hdependencies', '0');
			if ($hdependencies) {
				$this_dependencies = explode(',', $hdependencies);
			}
		} else {
			$this_dependencies = explode(',', $this->getDependencies());
		}
		// Set to false for recursive updateDynamic calls etc.
		$addedit = false;
		
		// Have deps
		if (array_sum($this_dependencies)) {
			if ($this->task_dynamic == 1) {
				return 'BadDep_DynNoDep';
			}
			
			$this_dependents = $this->task_id ? explode(',', $this->dependentTasks()) : array();
			$more_dependents = array();
			// If the dependents' have parents add them to list of dependents
			foreach ($this_dependents as $dependent) {
				$dependent_task = new CTask();
				$dependent_task->load($dependent);
				if ($dependent_task->task_id != $dependent_task->task_parent) {
					$more_dependents = explode(',', 
					                           $this->dependentTasks($dependent_task->task_parent));
				}
			}
			$this_dependents = array_merge($this_dependents, $more_dependents);
			
			// Task dependencies can not be dependent on this task
			$intersect = array_intersect($this_dependencies, $this_dependents);
			if (array_sum($intersect)) {
				$ids = '(' . implode(',', $intersect) . ')';
				return array('BadDep_CircularDep', $ids);
			}
		}
		
		// Has a parent
		if ($this->task_id && $this->task_id != $this->task_parent) {
			$this_children = $this->getChildren();
			$this_parent = new CTask();
			$this_parent->load($this->task_parent);
			$parents_dependents = explode(',', $this_parent->dependentTasks());
			
			if (in_array($this_parent->task_id, $this_dependencies)) {
				return 'BadDep_CannotDependOnParent';
			}
			// Task parent cannot be child of this task
			if (in_array($this_parent->task_id, $this_children)) {
				return 'BadParent_CircularParent';
			}
			
			if ($this_parent->task_parent != $this_parent->task_id) {
				// ... or parent's parent, cannot be child of this task. Could go on ...
				if (in_array($this_parent->task_parent, $this_children)) {
					return array('BadParent_CircularGrandParent'
								 , '(' . $this_parent->task_parent . ')');
				}
				// parent's parent cannot be one of this task's dependencies
				if (in_array($this_parent->task_parent, $this_dependencies)) {
					return array('BadDep_CircularGrandParent'
								 , '(' . $this_parent->task_parent . ')');
				}
			} // grand parent
			
			if ($this_parent->task_dynamic == 1) {
				$intersect = array_intersect($this_dependencies, $parents_dependents);
				if (array_sum($intersect)) {
					$ids = '(' . implode(',', $intersect) . ')';
					return array('BadDep_CircularDepOnParentDependent', $ids);
				}
			}
			if ($this->task_dynamic == 1) {
				// then task's children can not be dependent on parent
				$intersect = array_intersect($this_children, $parents_dependents);
				if (array_sum($intersect)) {
					return 'BadParent_ChildDepOnParent';
				}
			}
		} // parent
		
		//Is dynamic and no child
		if (dPgetConfig('check_task_empty_dynamic') && $this->task_dynamic == 1) {
			$children_of_dynamic = $this->getChildren();
			if (empty($children_of_dynamic)) {
				return 'BadDyn_NoChild';
			}
		}
		
		return NULL;
	}
	
	
	/*
	 * overload the load function
	 * We need to update dynamic tasks of type '1' on each load process!
	 * @param int $oid optional argument, if not specifed then the value of current key is used
	 * @return any result from the database operation
	 */
	
	function load($oid=null, $strip=false, $skipUpdate=false) {
		// use parent function to load the given object
		$loaded = parent::load($oid, $strip);
		
		/*
		 ** Update the values of a dynamic task from
		 ** the children's properties each time the
		 ** dynamic task is loaded.
		 ** Additionally store the values in the db.
		 ** Only treat umbrella tasks of dynamics '1'.
		 */
		if ($this->task_dynamic == 1 && !($skipUpdate)) {
			// update task from children
			$this->htmlDecode();
			$this->updateDynamics(true);
			
			/*
			 ** Use parent function to store the updated values in the db
			 ** instead of store function of this object in order to
			 ** prevent from infinite loops.
			 */
			parent::store();
			$loaded = parent::load($oid, $strip);
		}
		
		// return whether the object load process has been successful or not
		return $loaded;
	}
	
	/*
	 * call the load function but don't update dynamics
	 */
	function peek($oid=null, $strip=false) {
		$loadme = $this->load($oid, $strip, true);
		return $loadme;
	}
	
	function updateDynamics($fromChildren = false) {
		//Has a parent or children, we will check if it is dynamic so that it's info is updated also
		$q = new DBQuery;
		$modified_task = new CTask();
		
		if ($fromChildren){
			if (version_compare(phpversion(), '5.0.0', '>=')) {
				$modified_task = $this;
			} else {
				$modified_task =& $this;
			}
		} else {
			$modified_task->load($this->task_parent);
			$modified_task->htmlDecode();
		}
		
		if ($modified_task->task_dynamic == '1') {
			//Update allocated hours based on children with duration type of 'hours'
			$q->addTable($this->_tbl);
			$q->addQuery('SUM(task_duration * task_duration_type)');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND task_duration_type = 1 ');
			$q->addGroup('task_parent');
			$sql = $q->prepare();
			$q->clear();
			$children_allocated_hours1 = (float) db_loadResult($sql);
			
			/*
			 * Update allocated hours based on children with duration type of 'days'
			 * use the daily working hours instead of the full 24 hours to calculate 
			 * dynamic task duration!
			 */
			$q->addTable($this->_tbl);
			$q->addQuery(' SUM(task_duration * ' . dPgetConfig('daily_working_hours') . ')');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND task_duration_type <> 1 ');
			$q->addGroup('task_parent');
			$sql = $q->prepare();
			$q->clear();
			$children_allocated_hours2 = (float) db_loadResult($sql);
			
			// sum up the two distinct duration values for the children with duration type 'hrs'
			// and for those with the duration type 'day'
			$children_allocated_hours = $children_allocated_hours1 + $children_allocated_hours2;
			
			if ($modified_task->task_duration_type == 1) {
				$modified_task->task_duration = round($children_allocated_hours, 2);
			} else {
				$modified_task->task_duration = round($children_allocated_hours 
													  / dPgetConfig('daily_working_hours'), 2);
			}
			
			//Update worked hours based on children
			$q->addTable('tasks', 't');
			$q->innerJoin('task_log', 'tl', 't.task_id = tl.task_log_task');
			$q->addQuery('SUM(task_log_hours)');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND task_dynamic <> 1 ');
			$sql = $q->prepare();
			$q->clear();
			$children_hours_worked = (float) db_loadResult($sql);
			
			
			//Update worked hours based on dynamic children tasks
			$q->addTable('tasks');
			$q->addQuery('SUM(task_hours_worked)');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND task_dynamic = 1 ');
			$sql = $q->prepare();
			$q->clear();
			$children_hours_worked += (float) db_loadResult($sql);
			
			$modified_task->task_hours_worked = $children_hours_worked;
			
			//Update percent complete
			//hours
			$q->addTable('tasks');
			$q->addQuery('SUM(task_percent_complete * task_duration * task_duration_type)');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND task_duration_type = 1 ');
			$sql = $q->prepare();
			$q->clear();
			$real_children_hours_worked = (float) db_loadResult($sql);
			
			//"days"
			$q->addTable('tasks');
			$q->addQuery('SUM(task_percent_complete * task_duration * ' 
						 . dPgetConfig('daily_working_hours') . ')');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND task_duration_type <> 1 ');
			$sql = $q->prepare();
			$q->clear();
			$real_children_hours_worked += (float) db_loadResult($sql);
			
			$total_hours_allocated = (float)($modified_task->task_duration 
											 * (($modified_task->task_duration_type > 1) 
												? dPgetConfig('daily_working_hours') : 1));
			if ($total_hours_allocated > 0) {
				$modified_task->task_percent_complete = ceil($real_children_hours_worked 
															 / $total_hours_allocated);
			} else {
				$q->addTable('tasks');
				$q->addQuery('AVG(task_percent_complete)');
				$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
							 . $modified_task->task_id);
				$sql = $q->prepare();
				$q->clear();
				$modified_task->task_percent_complete = db_loadResult($sql);
			}
			
			
			//Update start date
			$q->addTable('tasks');
			$q->addQuery('MIN(task_start_date)');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND ! isnull(task_start_date)' 
						 . " AND task_start_date <>	'0000-00-00 00:00:00'");
			$sql = $q->prepare();
			$q->clear();
			$d = db_loadResult($sql);
			if ($d) {
				$modified_task->task_start_date = $d;
			} else {
				$modified_task->task_start_date = '0000-00-00 00:00:00';
			}
			
			//Update end date
			$q->addTable('tasks');
			$q->addQuery('MAX(task_end_date)');
			$q->addWhere('task_parent = ' . $modified_task->task_id . ' AND task_id <> ' 
						 . $modified_task->task_id . ' AND ! isnull(task_end_date)');
			$sql = $q->prepare();
			$q->clear();
			$modified_task->task_end_date = db_loadResult($sql);
			
			//If we are updating a dynamic task from its children we don't want to store() it
			//when the method exists the next line in the store calling function will do that
			if ($fromChildren == false) {
				$modified_task->store();
			}
		}
	}
	
	/*
	 * Copy the current task
	 *
	 * @author handco <handco@users.sourceforge.net>
	 * @param int id of the destination project
	 * @return object The new record object or null if error
	 */
	function copy($destProject_id = 0, $destTask_id = -1) {
		
		$newObj = $this->duplicate();
		// Copy this task to another project if it's specified
		if ($destProject_id != 0) {
			$newObj->task_project = $destProject_id;
		}
		
		if ($destTask_id == 0) {
			$newObj->task_parent = $newObj->task_id;
		} else if ($destTask_id > 0) {
			$newObj->task_parent = $destTask_id;
		}
		
		if ($newObj->task_parent == $this->task_id) {
			$newObj->task_parent = '';
		}
		$newObj->store();
		
		// Copy assigned users as well
		$q = new DBQuery();
		$q->addQuery('user_id, user_type, perc_assignment, user_task_priority');
		$q->addTable('user_tasks');
		$q->addWhere('task_id = ' . $this->task_id);
		$users = $q->loadList();
		
		$q->setDelete('user_tasks');
		$q->addWhere('task_id = ' . $newObj->task_id);
		$q->exec();
		$q->clear();
		
		$fields = array('user_id', 'user_type', 'perc_assignment', 'user_task_priority', 'task_id');
		foreach ($users as $user) {
			$user['task_id'] = $newObj->task_id;
			$values = array_values($user);
			
			$q->addTable('user_tasks');
			$q->addInsert($fields, $values, true);
			$q->exec();
			$q->clear();
		}
		//copy dependancies
		$dep_list = $this->getDependencies();
		$newObj->updateDependencies($dep_list);
		
		return $newObj;
	}// end of copy()
	
	function deepCopy($destProject_id = 0, $destTask_id = 0) {
		$children = $this->getChildren();
		$newObj = $this->copy($destProject_id, $destTask_id);
		if (!empty($children)) {
			$tempTask = new CTask();
			$new_child_ids = array();
			foreach ($children as $child) {
				$tempTask->peek($child);
				$tempTask->htmlDecode($child);
				$newChild = $tempTask->deepCopy($destProject_id, $newObj->task_id);
				$newChild->store();
				
				//old id to new id translation table
				$old_id = $tempTask->task_id;
				$new_child_ids[$old_id] = $newChild->task_id;
			}
			/*
			 * We cannot update beyond the new child id without complicating matters
			 * by mapping "old" id's to new in an array that would be accessible in 
			 * *every* level of recursive call and get executed just before returning 
			 * from a given call. Also we may not want to do this as there could be 
			 * good reasons for keeping some of the old non-child dependancy ids anyway
			 */
			//update dependancies on old child ids to new child id
			$dep_list = $newObj->getDependencies();
			if ($dep_list) {
				$dep_array = explode(',', $dep_list);
				foreach ($dep_array as $key => $dep_id) {
					if ($new_child_ids[$dep_id]) {
						$dep_array[$key] = $new_child_ids[$dep_id];
					}
				}
				$dep_list = implode(',', $dep_array);
				$newObj->updateDependencies($dep_list);
			}
		}
		$newObj->store();
		
		return $newObj;
	}
	
	function move($destProject_id = 0, $destTask_id = -1) {
		if ($destProject_id) {
			$this->task_project = $destProject_id;
		}
		if ($destTask_id >= 0) {
			$this->task_parent = (($destTask_id) ? $destTask_id : $this->task_id);
		}
		$this->store();
	}
	
	function deepMove($destProject_id = 0, $destTask_id = 0) {
		$children = $this->getChildren();
		$this->move($destProject_id, $destTask_id);
		if (!empty($children)) {
			foreach ($children as $child) {
				$tempChild = new CTask();
				$tempChild->peek($child);
				$tempChild->htmlDecode($child);
				$tempChild->deepMove($destProject_id, $this->task_id);
				$tempChild->store();
			}
		}
		$this->store();
	}
	
	/**
	 * @todo Parent store could be partially used
	 */
	function store() {
		GLOBAL $AppUI;
		$q = new DBQuery;
		
		$this->dPTrimAll();
		
		$importing_tasks = false;
		$msg = $this->check();
		if ($msg) {
			$return_msg = array(get_class($this) . '::store-check',	 'failed',	'-');
			if (is_array($msg)) {
				return array_merge($return_msg, $msg);
			} else {
				array_push($return_msg, $msg);
				return $return_msg;
			}
		}
		if ($this->task_id) {
			addHistory('tasks', $this->task_id, 'update', $this->task_name, $this->task_project);
			$this->_action = 'updated';
			
			// Load and globalize the old, not yet updated task object	
			// e.g. we need some info later to calculate the shifting time for depending tasks	
			// see function update_dep_dates
			GLOBAL $oTsk;
			$oTsk = new CTask();
			$oTsk->peek($this->task_id);
			
			// if task_status changed, then update subtasks
			if ($this->task_status != $oTsk->task_status) {
				$this->updateSubTasksStatus($this->task_status);
			}
			
			// Moving this task to another project?
			if ($this->task_project != $oTsk->task_project) {
				$this->updateSubTasksProject($this->task_project);
			}
			
			if ($this->task_dynamic == 1) {
				$this->updateDynamics(true);
			}
			
			// shiftDependentTasks needs this done first
			$this->check();
			$ret = db_updateObject('tasks', $this, 'task_id', false);

			// Milestone or task end date, or dynamic status has changed,
			// shift the dates of the tasks that depend on this task
			if (($this->task_end_date != $oTsk->task_end_date) 
				|| ($this->task_dynamic != $oTsk->task_dynamic) 
				|| ($this->task_milestone == '1')) {
				$this->shiftDependentTasks();
			}
		} else {
			$this->_action = 'added';
			if ($this->task_start_date == '')
				$this->task_start_date = '0000-00-00 00:00:00';
			if ($this->task_end_date == '')
				$this->task_end_date = '0000-00-00 00:00:00';

			$ret = db_insertObject('tasks', $this, 'task_id');
			addHistory('tasks', $this->task_id, 'add', $this->task_name, $this->task_project);
			
			if (!$this->task_parent) {
				$q->addTable('tasks');
				$q->addUpdate('task_parent', $this->task_id);
				$q->addWhere('task_id = ' . $this->task_id);
				$q->exec();
				$q->clear();
			} else {
				// importing tasks do not update dynamics
				$importing_tasks = true;
			}
			
			// insert entry in user tasks
			$q->addTable('user_tasks');
			$q->addInsert('user_id', $AppUI->user_id);
			$q->addInsert('task_id', $this->task_id);
			$q->addInsert('user_type', '0');
			$q->exec();
			$q->clear();
		}
		
		//split out related departments and store them seperatly.
		$q->setDelete('task_departments');
		$q->addWhere('task_id=' . $this->task_id);
		$q->exec();
		$q->clear();
		// print_r($this->task_departments);
		if (!empty($this->task_departments)){
			$departments = explode(',', $this->task_departments);
			foreach ($departments as $department) {
				$q->addTable('task_departments');
				$q->addInsert('task_id', $this->task_id);
				$q->addInsert('department_id', $department);
				$q->exec();
				$q->clear();
			}
		}
		
		//split out related contacts and store them seperatly.
		$q->setDelete('task_contacts');
		$q->addWhere('task_id=' . $this->task_id);
		$q->exec();
		$q->clear();
		if (!empty($this->task_contacts)){
			$contacts = explode(',', $this->task_contacts);
			foreach ($contacts as $contact) {
				$q->addTable('task_contacts');
				$q->addInsert('task_id', $this->task_id);
				$q->addInsert('contact_id', $contact);
				$q->exec();
				$q->clear();
			}
		}
		
		// if is child update parent task
		if ($this->task_parent != $this->task_id) {
			
			if (!$importing_tasks) {
				$this->updateDynamics(true);
			}
			
			$pTask = new CTask();
			$pTask->load($this->task_parent);
			$pTask->updateDynamics();
			
			if ($oTsk->task_parent != $this->task_parent) {
				$old_parent = new CTask();
				$old_parent->load($oTsk->task_parent);
				$old_parent->updateDynamics();
			}
		}
		
		// update dependencies
		if (!empty($this->task_id)) {
			$this->updateDependencies($this->getDependencies());
		} else {
			// print_r($this);
		}
		
		if (!$ret) {
			return get_class($this) . '::store failed <br />' . db_error();
		} else {
			return NULL;
		}
	}
	
	/**
	 * @todo Parent store could be partially used
	 * @todo Can't delete a task with children
	 */
	function delete() {
		$q = new DBQuery;
		if (!($this->task_id)) {
			return 'invalid task id';
		}
		
		//load task first because we need info on it to update the parent tasks later
		$task = new CTask();
		$task->load($this->task_id);
		//get child tasks so we can delete them too (no orphans)
		$childrenlist = $task->getDeepChildren();
		
		
		//delete task (if we're actually allowed to delete this task)
		$err_msg = parent::delete($task->task_id, $task->task_name, $task->task_project);
		if ($err_msg) {
			return $err_msg;
		}
		$this->_action = 'deleted';
		
		if ($task->task_parent != $task->task_id){
			//Has parent, run the update sequence, this child will no longer be in the database
			$this->updateDynamics();
		}
		$q->clear();
		
		//delete children
		if (!empty($childrenlist)) {
			foreach ($childrenlist as $child_id) {
				$ctask = new CTask();
				$ctask->load($child_id);
				//ignore permissions on child tasks by deleteing task directly from the database
				$q->setDelete('tasks');
				$q->addWhere('task_id=' . $ctask->task_id);
				if (!($q->exec())) {
					return db_error();
				}
				$q->clear();
				addHistory('tasks', $ctask->task_id, 'delete', 
				           $ctask->task_name, $ctask->task_project);
				
				$this->updateDynamics(); //to update after children are deleted (see above)
			}
			$this->_action = 'deleted with children';
		}
		
		//delete affiliated task_logs (overrides any task_log permissions)
		$q->setDelete('task_log');
		if (!empty($childrenlist)) {
			$q->addWhere('task_log_task IN (' . implode(', ', $childrenlist) 
						 . ', ' . $this->task_id . ')');
		} else {
			$q->addWhere('task_log_task=' . $this->task_id);
		}
		
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		
		//delete affiliated task_dependencies
		$q->setDelete('task_dependencies');
		if (!empty($childrenlist)) {
			$q->addWhere('dependencies_task_id IN (' . implode(', ', $childrenlist) 
						 . ', ' . $task->task_id . ')');
		} else {
			$q->addWhere('dependencies_task_id=' . $task->task_id);
		}
		
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		
		// delete linked user tasks
		$q->setDelete('user_tasks');
		if (!empty($childrenlist)) {
			$q->addWhere('task_id IN (' . implode(', ', $childrenlist) 
						 . ', ' . $task->task_id . ')');
		} else {
			$q->addWhere('task_id=' . $task->task_id);
		}
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		
		return NULL;
	}
	
	function updateDependencies($cslist) {
		$q = new DBQuery;
		// delete all current entries
		$q->setDelete('task_dependencies');
		$q->addWhere('dependencies_task_id=' . $this->task_id);
		$q->exec();
		$q->clear();
		
		// process dependencies
		$tarr = explode(',', $cslist);
		foreach ($tarr as $task_id) {
			if (intval($task_id) > 0) {
				$q->addTable('task_dependencies');
				$q->addReplace('dependencies_task_id', $this->task_id);
				$q->addReplace('dependencies_req_task_id', $task_id);
				$q->exec();
				$q->clear();
			}
		}
	}
	
	/**
	 *		  Retrieve the tasks dependencies
	 *
	 *		  @author		 handco		   <handco@users.sourceforge.net>
	 *		  @return		 string		   comma delimited list of tasks id's
	 **/
	function getDependencies() {
		// Call the static method for this object
		$result = $this->staticGetDependencies ($this->task_id);
		return $result;
	} // end of getDependencies ()
	
	
	/**
	 *		  Retrieve the tasks dependencies
	 *
	 *		  @author		 handco		   <handco@users.sourceforge.net>
	 *		  @param		integer		   ID of the task we want dependencies
	 *		  @return		 string		   comma delimited list of tasks id's
	 **/
	function staticGetDependencies($taskId) {
		$q = new DBQuery;
		if (empty($taskId)) {
			return '';
		}
		$q->addTable('task_dependencies', 'td');
		$q->addQuery('dependencies_req_task_id');
		$q->addWhere('td.dependencies_task_id = ' . $taskId);
		$sql = $q->prepare();
		$q->clear();
		$list = db_loadColumn ($sql);
		$result = $list ? implode (',', $list) : '';
		
		return $result;
	} // end of staticGetDependencies ()
	
	
	function notifyOwner() {
		$q = new DBQuery;
		GLOBAL $AppUI, $locale_char_set;
		
		$q->addTable('projects');
		$q->addQuery('project_name');
		$q->addWhere('project_id=' . $this->task_project);
		$sql = $q->prepare();
		$q->clear();
		$projname = htmlspecialchars_decode(db_loadResult($sql));
		$mail = new Mail;
		
		$mail->Subject($projname . '::' . $this->task_name . ' ' 
					   . $AppUI->_($this->_action, UI_OUTPUT_RAW), $locale_char_set);
		
		// c = creator
		// a = assignee
		// o = owner
		$q->addTable('tasks', 't');
		$q->leftJoin('user_tasks', 'u', 'u.task_id = t.task_id');
		$q->leftJoin('users', 'o', 'o.user_id = t.task_owner');
		$q->leftJoin('contacts', 'oc', 'oc.contact_id = o.user_contact');
		$q->leftJoin('users', 'c', 'c.user_id = t.task_creator');
		$q->leftJoin('contacts', 'cc', 'cc.contact_id = c.user_contact');
		$q->leftJoin('users', 'a', 'a.user_id = u.user_id');
		$q->leftJoin('contacts', 'ac', 'ac.contact_id = a.user_contact');
		$q->addQuery('t.task_id, cc.contact_email as creator_email' 
					 . ', cc.contact_first_name as creator_first_name' 
					 . ', cc.contact_last_name as creator_last_name' 
					 . ', oc.contact_email as owner_email' 
					 . ', oc.contact_first_name as owner_first_name' 
					 . ', oc.contact_last_name as owner_last_name' 
					 . ', a.user_id as assignee_id, ac.contact_email as assignee_email' 
					 . ', ac.contact_first_name as assignee_first_name' 
					 . ', ac.contact_last_name as assignee_last_name');
		$q->addWhere(' t.task_id = ' . $this->task_id);
		$sql = $q->prepare();
		$q->clear();
		$users = db_loadList($sql);
		
		if (count($users)) {
			$body = ($AppUI->_('Project', UI_OUTPUT_RAW) . ': ' . $projname . "\n" 
					 . $AppUI->_('Task', UI_OUTPUT_RAW) . ':	' . $this->task_name . "\n" 
					 . $AppUI->_('URL', UI_OUTPUT_RAW) . ': ' . DP_BASE_URL 
					 . '/index.php?m=tasks&a=view&task_id=' . $this->task_id . "\n\n" 
					 . $AppUI->_('Description', UI_OUTPUT_RAW) . ': ' . "\n" 
					 . $this->task_description . "\n\n" 
					 . $AppUI->_('Creator', UI_OUTPUT_RAW) . ': ' . $AppUI->user_first_name . ' ' 
					 . $AppUI->user_last_name . "\n\n" 
					 . $AppUI->_('Progress', UI_OUTPUT_RAW) . ': '  
					 . $this->task_percent_complete . '%' . "\n\n" 
					 . dPgetParam($_POST, 'task_log_description'));
			
			
			$mail->Body($body, isset($GLOBALS['locale_char_set']) 
						? $GLOBALS['locale_char_set'] 
						: '');
			$mail->From ('"' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name
						  . '" <' . $AppUI->user_email . '>');
		}
		
		if ($mail->ValidEmail($users[0]['owner_email'])) {
			$mail->To($users[0]['owner_email'], true);
			$mail->Send();
		}
		
		return '';
	}
	
	//additional comment will be included in email body
	function notify($comment = '') {
		$q = new DBQuery;
		GLOBAL $AppUI, $locale_char_set;
		$df = $AppUI->getPref('SHDATEFORMAT');
		$df .= ' ' . $AppUI->getPref('TIMEFORMAT');
		
		$sql = 'SELECT project_name FROM projects WHERE project_id=' . $this->task_project;
		$projname = htmlspecialchars_decode(db_loadResult($sql));
		
		$mail = new Mail;
		
		$mail->Subject($projname . '::' . $this->task_name . ' '  
				.$AppUI->_($this->_action, UI_OUTPUT_RAW), $locale_char_set);
		
		// c = creator
		// a = assignee
		// o = owner
		$q->addTable('tasks', 't');
		$q->leftJoin('user_tasks', 'u', 'u.task_id = t.task_id');
		$q->leftJoin('users', 'o', 'o.user_id = t.task_owner');
		$q->leftJoin('contacts', 'oc', 'oc.contact_id = o.user_contact');
		$q->leftJoin('users', 'c', 'c.user_id = t.task_creator');
		$q->leftJoin('contacts', 'cc', 'cc.contact_id = c.user_contact');
		$q->leftJoin('users', 'a', 'a.user_id = u.user_id');
		$q->leftJoin('contacts', 'ac', 'ac.contact_id = a.user_contact');
		$q->addQuery('t.task_id, cc.contact_email as creator_email' 
					 . ', cc.contact_first_name as creator_first_name' 
					 . ', cc.contact_last_name as creator_last_name' 
					 . ', oc.contact_email as owner_email' 
					 . ', oc.contact_first_name as owner_first_name' 
					 . ', oc.contact_last_name as owner_last_name' 
					 . ', a.user_id as assignee_id, ac.contact_email as assignee_email' 
					 . ', ac.contact_first_name as assignee_first_name' 
					 . ', ac.contact_last_name as assignee_last_name');
		$q->addWhere(' t.task_id = ' . $this->task_id);
		$sql = $q->prepare();
		$q->clear();
		$users = db_loadList($sql);
		
		if (count($users)) {
			$task_start_date = new CDate($this->task_start_date);
			$task_finish_date = new CDate($this->task_end_date);
			
			$body = ($AppUI->_('Project', UI_OUTPUT_RAW) . ': ' . $projname . "\n" 
					 . $AppUI->_('Task', UI_OUTPUT_RAW) . ':	 ' . $this->task_name);
			//Priority not working for some reason, will wait till later
			//$body .= "\n".$AppUI->_('Priority'). ': ' . $this->task_priority;
			$body .= ("\n" . $AppUI->_('Start Date', UI_OUTPUT_RAW) . ': ' 
					  . $task_start_date->format($df) . "\n" 
					  . $AppUI->_('Finish Date', UI_OUTPUT_RAW) . ': ' 
					  . ($this->task_end_date != '' ? $task_finish_date->format($df) : '') . "\n" 
					  . $AppUI->_('URL', UI_OUTPUT_RAW) . ': ' . DP_BASE_URL 
					  . '/index.php?m=tasks&a=view&task_id=' . $this->task_id . "\n\n" 
					  . $AppUI->_('Description', UI_OUTPUT_RAW) . ': ' . "\n" 
					  . $this->task_description);
			if ($users[0]['creator_email']) {
				$body .= ("\n\n" . $AppUI->_('Creator', UI_OUTPUT_RAW). ':' . "\n"  
						  . $users[0]['creator_first_name'] . ' ' . $users[0]['creator_last_name' ] 
						  . ', ' . $users[0]['creator_email']);
			}
			$body .= ("\n\n" . $AppUI->_('Owner', UI_OUTPUT_RAW).':' . "\n"  
					  . $users[0]['owner_first_name'] . ' ' . $users[0]['owner_last_name' ]  
					  . ', ' . $users[0]['owner_email']);
			
			if ($comment != '') {
				$body .= "\n\n".$comment;
			}
			$mail->Body($body, (isset($GLOBALS['locale_char_set']) 
								? $GLOBALS['locale_char_set'] : ''));
			$mail->From ('"' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name 
						 . '" <' . $AppUI->user_email . '>');
		}
		
		$mail_owner = $AppUI->getPref('MAILALL');
		
		foreach ($users as $row) {
			if ($mail_owner || $row['assignee_id'] != $AppUI->user_id) {
				if ($mail->ValidEmail($row['assignee_email'])) {
					$mail->To($row['assignee_email'], true);
					$mail->Send();
				}
			}
		}
		return '';
	}
	
	/**
	 * Email the task log to assignees, task contacts, project contacts, and others
	 * based upon the information supplied by the user.
	 */
	function email_log(&$log, $assignees, $task_contacts, $project_contacts, $others, $extras) {
		global $AppUI, $locale_char_set, $dPconfig;
		
		$mail_recipients = array();
		$q = new DBQuery;
		if (isset($assignees) && $assignees == 'on') {
			$q->addTable('user_tasks', 'ut');
			$q->leftJoin('users', 'ua', 'ua.user_id = ut.user_id');
			$q->leftJoin('contacts', 'c', 'c.contact_id = ua.user_contact');
			$q->addQuery('c.contact_email, c.contact_first_name, c.contact_last_name');
			$q->addWhere('ut.task_id = ' . $this->task_id);
			if (! $AppUI->getPref('MAILALL')) {
				$q->addWhere('ua.user_id <>' . $AppUI->user_id);
			}
			$req =& $q->exec(QUERY_STYLE_NUM);
			for ($req; ! $req->EOF; $req->MoveNext()) {
				list($email, $first, $last) = $req->fields;
				if (! isset($mail_recipients[$email])) {
					$mail_recipients[$email] = trim($first) . ' ' . trim($last);
				}
			}
			$q->clear();
		}
		if (isset($task_contacts) && $task_contacts == 'on') {
			$q->addTable('task_contacts', 'tc');
			$q->leftJoin('contacts', 'c', 'c.contact_id = tc.contact_id');
			$q->addQuery('c.contact_email, c.contact_first_name, c.contact_last_name');
			$q->addWhere('tc.task_id = ' . $this->task_id);
			$req =& $q->exec(QUERY_STYLE_NUM);
			for ($req; ! $req->EOF; $req->MoveNext()) {
				list($email, $first, $last) = $req->fields;
				if (! isset($mail_recipients[$email])) {
					$mail_recipients[$email] = $first . ' ' . $last;
				}
			}
			$q->clear();
		}
		if (isset($project_contacts) && $project_contacts == 'on') {
			$q->addTable('project_contacts', 'pc');
			$q->leftJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
			$q->addQuery('c.contact_email, c.contact_first_name, c.contact_last_name');
			$q->addWhere('pc.project_id = ' . $this->task_project);
			$req =& $q->exec(QUERY_STYLE_NUM);
			for ($req; ! $req->EOF; $req->MoveNext()) {
				list($email, $first, $last) = $req->fields;
				if (! isset($mail_recipients[$email])) {
					$mail_recipients[$email] = $first . ' ' . $last;
				}
			}
			$q->clear();
		}
		if (isset($others)) {
			$others = trim($others, " \r\n\t,"); // get rid of empty elements.
			if (strlen($others) > 0) {
				$q->addTable('contacts', 'c');
				$q->addQuery('c.contact_email, c.contact_first_name, c.contact_last_name');
				$q->addWhere('c.contact_id in (' . $others . ')');
				$req =& $q->exec(QUERY_STYLE_NUM);
				for ($req; ! $req->EOF; $req->MoveNext()) {
					list($email, $first, $last) = $req->fields;
					if (! isset($mail_recipients[$email])) {
						$mail_recipients[$email] = $first . ' ' . $last;
					}
				}
				$q->clear();
			}
		}
		if (isset($extras) && $extras) {
			// Search for semi-colons, commas or spaces and allow any to be separators
			$extra_list = preg_split('/[\s,;]+/', $extras);
			foreach ($extra_list as $email) {
				if ($email && ! isset($mail_recipients[$email])) {
					$mail_recipients[$email] = $email;
				}
			}
		}
		$q->clear(); // Reset to the default state.
		if (count($mail_recipients) == 0) {
			return false;
		}
		
		// Build the email and send it out.
		$char_set = isset($locale_char_set) ? $locale_char_set : '';
		$mail = new Mail;
		// Grab the subject from user preferences
		$prefix = $AppUI->getPref('TASKLOGSUBJ');
		$mail->Subject($prefix .  ' ' . $log->task_log_name, $char_set);
		
		$q->addTable('projects');
		$q->addQuery('project_name');
		$q->addWhere('project_id=' . $this->task_project);
		$sql = $q->prepare();
		$q->clear();
		$projname = htmlspecialchars_decode(db_loadResult($sql));
		
		$body = $AppUI->_('Project', UI_OUTPUT_RAW) . ': ' . $projname . "\n";
		if ($this->task_parent != $this->task_id) {
			$q->addTable('tasks');
			$q->addQuery('task_name');
			$q->addWhere('task_id = ' . $this->task_parent);
			$req =& $q->exec(QUERY_STYLE_NUM);
			if ($req) {
				$body .= $AppUI->_('Parent Task', UI_OUTPUT_RAW) . ': ' 
				  . htmlspecialchars_decode($req->fields[0]) . "\n";
			}
			$q->clear();
		}
		$body .= $AppUI->_('Task', UI_OUTPUT_RAW) . ': ' . $this->task_name . "\n";
		$task_types = dPgetSysVal('TaskType');
		$body .= $AppUI->_('Task Type', UI_OUTPUT_RAW) . ':' . $task_types[$this->task_type] . "\n";
		$body .= $AppUI->_('URL', UI_OUTPUT_RAW) 
			. ': ' . DP_BASE_URL . '/index.php?m=tasks&a=view&task_id=' . $this->task_id . "\n\n";
		$body .= $AppUI->_('Summary', UI_OUTPUT_RAW) . ': ' . $log->task_log_name . "\n\n";
		$body .= $log->task_log_description;
		
		// Append the user signature to the email - if it exists.
		$q->addTable('users');
		$q->addQuery('user_signature');
		$q->addWhere('user_id = ' . $AppUI->user_id);
		if ($res = $q->exec()) {
			if ($res->fields['user_signature']) {
				$body .= "\n--\n" . $res->fields['user_signature'];
			}
		}
		$q->clear();
		
		$mail->Body($body, $char_set);
		$mail->From($AppUI->user_first_name . ' ' . $AppUI->user_last_name . ' <' 
					. $AppUI->user_email . '>');
		
		$recipient_list = '';
		foreach ($mail_recipients as $email => $name) {
			if ($mail->ValidEmail($email)) {
				$mail->To($email);
				$recipient_list .= $email . ' (' . $name . ")\n";
			} else {
				$recipient_list .= "Invalid email address '{$email}' for {$name}, not sent\n";
			}
		}
		$mail->Send();
		// Now update the log
		$save_email = @$AppUI->getPref('TASKLOGNOTE');
		if ($save_email) {
			$log->task_log_description .= "\nEmailed " . date('d/m/Y H:i:s') 
				. " to:\n{$recipient_list}";
			return true;
		}
		
		return false; // No update needed.
	}
	
	/**
	 * @param Date Start date of the period
	 * @param Date End date of the period
	 * @param integer The target company
	 */
	function getTasksForPeriod($start_date, $end_date, $company_id=0, $user_id=null, 
	                           $filter_proj_archived=false, $filter_proj_completed=false) {
		GLOBAL $AppUI;
		$q = new DBQuery;
		// convert to default db time stamp
		$db_start = $start_date->format(FMT_DATETIME_MYSQL);
		$db_end = $end_date->format(FMT_DATETIME_MYSQL);
		
		// Allow for possible passing of user_id 0 to stop user filtering
		if(!isset($user_id)){
			$user_id = $AppUI->user_id;
		}
 
		// filter tasks for not allowed projects
		$tasks_filter = '';
		// check permissions on projects
		$proj = new CProject();
		$task_filter_where = $proj->getAllowedSQL($AppUI->user_id, 't.task_project');
		// exclude read denied projects
		$deny = $proj->getDeniedRecords($AppUI->user_id);
		// check permissions on tasks
		$obj = new CTask();
		$allow = $obj->getAllowedSQL($AppUI->user_id, 't.task_id');
		$parent_task_allow = $obj->getAllowedSQL($AppUI->user_id, 't.task_parent');
		
		$q->addTable('tasks', 't');
		if ($user_id) {
			$q->innerJoin('user_tasks', 'ut', 't.task_id=ut.task_id');
		}
		$q->innerJoin('projects', 'p', 't.task_project = p.project_id');
		$q->addQuery('DISTINCT t.task_id, t.task_name, t.task_start_date, t.task_end_date' 
		             . ', t.task_duration, t.task_duration_type' 
		             . ', p.project_color_identifier AS color, p.project_name');
		$q->addWhere('task_status > -1' 
					 . " AND (task_start_date <= '{$db_end}' AND (task_end_date >= '{$db_start}'" 
					 . " OR  task_end_date = '0000-00-00 00:00:00' OR task_end_date = NULL))");
		if ($user_id) {
			$q->addWhere("ut.user_id = '$user_id'");
		}

		if ($company_id) {
			$q->addWhere('p.project_company = ' . $company_id);
		}
		if (count($task_filter_where) > 0) {
			$q->addWhere('(' . implode(' AND ', $task_filter_where) . ')');
		}
		if (count($deny) > 0) {
			$q->addWhere('(t.task_project NOT IN (' . implode(', ', $deny) . '))');
		}
		if (count($allow) > 0) {
			$q->addWhere('(' . implode(' AND ', $allow) . ')');
		}
		if (count($parent_task_allow) > 0) {
			$q->addWhere('(' . implode(' AND ', $parent_task_allow) . ')');
		}
		if ($filter_proj_archived) {
			$q->addWhere('p.project_status <> 7');
		}
		if ($filter_proj_archived) {
			$q->addWhere('p.project_status <> 5');
		}
		$q->addOrder('t.task_start_date');
		
		// assemble query
		$sql = $q->prepare();
		$q->clear();
		//echo "<pre>$sql</pre>";
		// execute and return
		return db_loadList($sql);
	}
	
	function canAccess($user_id) {
		$q = new DBQuery;
		
		//check whether we are explicitly denied at task level
		$denied_tasks = $this->getDeniedRecords($user_id);
		if (in_array($this->task_id, $denied_tasks)) {
			return false;
		}
		
		switch ($this->task_access) {
		case 0:
			//public
			$retval = true;
			$proj_obj = new CProject();
			$denied_projects = $proj_obj->getDeniedRecords($user_id);
			if (in_array($this->task_project, $denied_projects)) {
				$retval = false;
			}
			
			break;
		case 1:
			//protected
			$q->addTable('users', 'u');
			$q->innerJoin('contacts', 'c', 'c.contact_id=u.user_contact');
			$q->addQuery('c.contact_company');
			$q->addWhere('u.user_id=' . $user_id . ' OR u.user_id=' . $this->task_owner);
			$sql = $q->prepare();
			$q->clear();
			$user_owner_companies = db_loadColumn($sql);
			$company_match = true;
			foreach ($user_owner_companies as $current_company) {
				$company_match = $company_match && ((!(isset($last_company))) 
													|| $last_company == $current_company);
				$last_company = $current_company;
			}
			
		case 2:
			//participant
			$company_match = ((isset($company_match)) ? $company_match : true);
			$q->addTable('user_tasks', 'ut');
			$q->addQuery('COUNT(*)');
			$q->addWhere('ut.user_id=' . $user_id . ' AND ut.task_id=' . $this->task_id);
			$sql = $q->prepare();
			$q->clear();
			$count = db_loadResult($sql);
			$retval = (($company_match && $count > 0) || $this->task_owner == $user_id);
			break;
		case 3:
			//private
			$retval = ($this->task_owner == $user_id);
			break;
		case 4:
			//privileged
			$retval = true;
			if ($this->task_project != '') {
				$q->clear();
				
				$q->addTable('users', 'u');
				$q->innerJoin('contacts', 'c', 'c.contact_id=u.user_contact');
				$q->addQuery('c.contact_company');
				$q->addWhere('u.user_id = ' . $user_id); 
				$user_company = $q->loadResult();
				$q->clear();
				
				$q->addTable('projects', 'p');
				$q->addQuery('p.project_company');
				$q->addWhere('p.project_id = ' . $this->task_project);
				$project_company = $q->loadResult();
				$q->clear();
				
				$q->addTable('user_tasks', 'ut');
				$q->addQuery('COUNT(ut.*) AS user_task_count');
				$q->addWhere('ut.user_id = ' . $user_id . ' AND ut.task_id = ' . $this->task_id);
				$count = $q->loadResult();
				$q->clear();
				
				$retval = (($user_company == $project_company) || $this->task_owner == $user_id 
				           || $count);
			}
			break;
		default:
			$retval = false;
			break;
		}
		
		return $retval;
	}
	
	/**
	 *		 retrieve tasks are dependent of another.
	 *		 @param	 integer		 ID of the master task
	 *		 @param	 boolean		 true if is a dep call (recurse call)
	 *		 @param	 boolean		 false for no recursion (needed for calc_end_date)
	 **/
	function dependentTasks ($taskId = false, $isDep = false, $recurse = true) {
		$q = new DBQuery;
		static $aDeps = false;
		// Initialize the dependencies array
		if (($taskId == false) && ($isDep == false)) {
			$aDeps = array();
		}
		// retrieve dependents tasks
		if (!$taskId) {
			$taskId = $this->task_id;
		}
		if (empty($taskId)) {
			return '';
		}
		$q->addTable('task_dependencies', 'td');
		$q->innerJoin('tasks', 't', 'td.dependencies_task_id = t.task_id');
		$q->addQuery('dependencies_task_id');
		$q->addWhere('td.dependencies_req_task_id = ' . $taskId);
		$sql = $q->prepare();
		$q->clear();
		$aBuf = db_loadColumn($sql);
		$aBuf = !empty($aBuf) ? $aBuf : array();
		//$aBuf = array_values(db_loadColumn ($sql));
		
		if ($recurse) {
			// recurse to find sub dependents
			foreach ($aBuf as $depId) {
				// work around for infinite loop
				if (!in_array($depId, $aDeps)) {
					$aDeps[] = $depId;
					$this->dependentTasks ($depId, true);
				}
			}
			
		} else {
			$aDeps = $aBuf;
		}
		
		// return if we are in a dependency call
		if ($isDep) {
			return;
		}
		
		return implode (',', $aDeps);
		
	} // end of dependentTasks()
	
   /*
	*		 shift dependents tasks dates
	*		 @return void
	*/
	function shiftDependentTasks () {
		// Get tasks that depend on this task
		$csDeps = explode(',', $this->dependentTasks('', '', false));
		
		if ($csDeps[0] == '') {
			return;
		}
		
		// Stage 1: Update dependent task dates
		foreach ($csDeps as $task_id) {
			$this->update_dep_dates($task_id);
		}
		
		// Stage 2: Now shift the dependent tasks' dependents
		foreach ($csDeps as $task_id) {
			$newTask = new CTask();
			$newTask->load($task_id);
			$newTask->shiftDependentTasks();
		}
		
		return;
	} // end of shiftDependentTasks()
	
   /*
	*		  Update this task's dates in the DB.
	*		  start date:		  based on latest end date of dependencies
	*		  end date:			  based on start date + appropriate task time span
	*		   
	*		  @param				integer task_id of task to update
	*/
	function update_dep_dates($task_id) {
		GLOBAL $tracking_dynamics;
		$q = new DBQuery;
		
		$newTask = new CTask();
		$newTask->load($task_id);
	
		// Do not update tasks that are not tracking dependencies
		if (!in_array($newTask->task_dynamic, $tracking_dynamics)) {
			return;
		}
 
		// load original task dates and calculate task time span
		$tsd = new CDate($newTask->task_start_date);
		$ted = new CDate($newTask->task_end_date);
		$duration = $tsd->calcDuration($ted);
		
		// reset start date
		$nsd = new CDate ($newTask->get_deps_max_end_date($newTask));
		
		// prefer Wed 8:00 over Tue 16:00 as start date
		$nsd = $nsd->next_working_day();
		$new_start_date = $nsd->format(FMT_DATETIME_MYSQL);
		
		// Add task time span to End Date again
		$ned = new CDate();
		$ned->copy($nsd);
		$ned->addDuration($duration, '1');
		
		// make sure one didn't land on a non-working day
		$ned = $ned->next_working_day(true);

		// prefer tue 16:00 over wed 8:00 as an end date
		$ned = $ned->prev_working_day();
		
		$new_end_date = $ned->format(FMT_DATETIME_MYSQL);		
	
		// update the db
		$q->addTable('tasks');
		$q->addUpdate('task_start_date', $new_start_date);
		$q->addUpdate('task_end_date', $new_end_date);
		$q->addWhere('task_dynamic <> 1 AND task_id = ' . $task_id);
		$q->exec();
		$q->clear();
		
		if ($newTask->task_parent != $newTask->task_id) {
			$newTask->updateDynamics();
		}
		
		return;
	}
	
	
	/* 
	 ** Time related calculations have been moved to /classes/date.class.php
	 ** some have been replaced with more _robust_ functions
	 ** 
			** Affected functions:
	 ** prev_working_day()
	 ** next_working_day()
	 ** calc_task_end_date()	renamed to addDuration()
	 ** calc_end_date()	renamed to calcDuration()
	 **
	 ** @date	20050525
	 ** @responsible gregorerhardt
	 ** @purpose	reusability, consistence
	 */ 
	
	
	/*
	 
	Get the last end date of all of this task's dependencies
	
	@param Task object
	returns FMT_DATETIME_MYSQL date
	
	*/
	
	function get_deps_max_end_date($taskObj) {
		global $tracked_dynamics;
		$q = new DBQuery;
		
		$deps = $taskObj->getDependencies();
		$obj = new CTask();
		
		$last_end_date = false;
		// Don't respect end dates of excluded tasks
		if ($tracked_dynamics && !empty($deps)) {
			$track_these = implode(',', $tracked_dynamics);
			$q->addTable('tasks');
			$q->addQuery('MAX(task_end_date)');
			$q->addWhere('task_id IN (' . $deps . ') AND task_dynamic IN (' . $track_these . ')');
			$sql = $q->prepare();
			$q->clear();
			$last_end_date = db_loadResult($sql);
		}
		
		if (!$last_end_date) {
			// Set to project start date
			$id = $taskObj->task_project;
			$q->addTable('projects');
			$q->addQuery('project_start_date');
			$q->addWhere('project_id = ' . $id);
			$sql = $q->prepare();
			$q->clear();
			$last_end_date = db_loadResult($sql);
		}
		
		return $last_end_date;
	}
	
	
	/**
	 * Function that returns the amount of hours this
	 * task consumes per user each day
	 */
	function getTaskDurationPerDay($use_percent_assigned = false){
		$duration = $this->task_duration * ($this->task_duration_type == 24 
											? dPgetConfig('daily_working_hours') 
											: $this->task_duration_type);
		$task_start_date = new CDate($this->task_start_date);
		$task_finish_date = new CDate($this->task_end_date);
		$assigned_users = $this->getAssignedUsers();
		if ($use_percent_assigned) {
			$number_assigned_users = 0;
			foreach ($assigned_users as $u) {
				$number_assigned_users += ($u['perc_assignment'] / 100);
			}
		} else {
			$number_assigned_users = count($assigned_users);
		}
		
		$day_diff = $task_finish_date->dateDiff($task_start_date);
		$number_of_days_worked = 0;
		$actual_date = $task_start_date;
		
		for ($i=0; $i<=$day_diff; $i++) {
			if ($actual_date->isWorkingDay()) {
				$number_of_days_worked++;
			}
			$actual_date->addDays(1);
		}
		// May be it was a Sunday task
		if ($number_of_days_worked == 0) {
			$number_of_days_worked = 1;
		}
		if ($number_assigned_users == 0) {
			$number_assigned_users = 1;
		}
		return ($duration/$number_assigned_users) / $number_of_days_worked;
	}
	
	
	/**
	 * Function that returns the amount of hours this
	 * task consumes per user each week
	 */
	function getTaskDurationPerWeek($use_percent_assigned = false) {
		$duration = ($this->task_duration_type == 24 ? dPgetConfig('daily_working_hours') 
		             : $this->task_duration_type) * $this->task_duration;
		$task_start_date = new CDate($this->task_start_date);
		$task_finish_date = new CDate($this->task_end_date);
		$assigned_users = $this->getAssignedUsers();
		if ($use_percent_assigned) {
			$number_assigned_users = 0;
			foreach ($assigned_users as $u) {
				$number_assigned_users += ($u['perc_assignment'] / 100);
			}
		} else {
			$number_assigned_users = count($assigned_users);
		}
		
		$number_of_weeks_worked = $task_finish_date->workingDaysInSpan($task_start_date) 
			/ count(explode(',', dPgetConfig('cal_working_days')));	
		$number_of_weeks_worked = (($number_of_weeks_worked < 1) 
								   ? ceil($number_of_weeks_worked) : $number_of_weeks_worked);
		
		// zero adjustment
		if ($number_of_weeks_worked == 0) {
			$number_of_weeks_worked = 1;
		}
		if ($number_assigned_users == 0) {
			$number_assigned_users = 1;
		}
		return ($duration/$number_assigned_users) / $number_of_weeks_worked;
	}
	
	
	// unassign a user from task
	function removeAssigned($user_id) {
		$q = new DBQuery;
		// delete all current entries
		$q->setDelete('user_tasks');
		$q->addWhere('task_id = ' . $this->task_id . ' AND user_id = ' . $user_id);
		$q->exec();
		$q->clear();
	}
	
	//using user allocation percentage ($perc_assign)
	// @return returns the Names of the over-assigned users (if any), otherwise false
	function updateAssigned($cslist, $perc_assign, $del=true, $rmUsers=false) {
		$q = new DBQuery;
		
		// process assignees
		$tarr = explode(',', $cslist);
		
		// delete all current entries from $cslist
		if ($del == true && $rmUsers == true) {
			foreach ($tarr as $user_id) {
				$user_id = (int)$user_id;
				if (!empty($user_id)) {
					$this->removeAssigned($user_id);
				}
			}
			
			return false;
			
		} else if ($del == true) { // delete all users assigned to this task (to properly update)
			$q->setDelete('user_tasks');
			$q->addWhere('task_id = ' . $this->task_id);
			$q->exec();
			$q->clear();
		}
		
		// get Allocation info in order to check if overAssignment occurs
		$alloc = $this->getAllocation('user_id');
		$overAssignment = false;
		
		foreach ($tarr as $user_id) {
			if (intval($user_id) > 0) {
				$perc = $perc_assign[$user_id];
				if (dPgetConfig('check_overallocation') 
					&& $perc > $alloc[$user_id]['freeCapacity']) {
					// add Username of the overAssigned User
					$overAssignment .= ' ' . $alloc[$user_id]['userFC'];
				} 
				else {
					$q->addTable('user_tasks');
					$q->addReplace('user_id', $user_id);
					$q->addReplace('task_id', $this->task_id);
					$q->addReplace('perc_assignment', $perc);
					$q->exec();
					$q->clear();
				}
			}
		}
		return $overAssignment;
	}
	
	function getAssignedUsers(){
		$q = new DBQuery;
		$q->addTable('users', 'u');
		$q->innerJoin('user_tasks', 'ut', 'ut.user_id = u.user_id');
		$q->leftJoin('contacts', 'co', ' co.contact_id = u.user_contact');
		$q->addQuery('u.*, ut.perc_assignment, ut.user_task_priority' 
		             . ', co.contact_first_name, co.contact_last_name');
		$q->addWhere('ut.task_id = ' . $this->task_id);
		$sql = $q->prepare();
		$q->clear();
		return db_loadHashList($sql, 'user_id');
	}
	
	/**
	 *	Calculate the extent of utilization of user assignments
	 *	@param string hash	 a hash for the returned hashList
	 *	@param array users	 an array of user_ids calculating their assignment capacity
	 *	@return array		 returns hashList of extent of utilization for assignment of the users
	 */
	function getAllocation($hash = NULL, $users = NULL) {
		if (! dPgetConfig('check_overallocation') ) {
			return array();
		}
		$q = new DBQuery;
		// retrieve the systemwide default preference for the assignment maximum
		$q->addTable('user_preferences');
		$q->addQuery('pref_value');
		$q->addWhere("pref_user = 0 AND pref_name = 'TASKASSIGNMAX'");
		$sql = $q->prepare();
		$q->clear();
		$result = db_loadHash($sql, $sysChargeMax);
		if (! $result) {
			$scm = 0;
		} else {
			$scm = $sysChargeMax['pref_value'];
		}
		
		/*
		 * provide actual assignment charge, individual chargeMax 
		 * and freeCapacity of users' assignments to tasks
		*/
		$q->addTable('users', 'u');
		$q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
		$q->leftJoin('user_tasks', 'ut', 'ut.user_id = u.user_id');
		$q->leftJoin('user_preferences', 'up', 'up.pref_user = u.user_id');
		$q->addQuery("u.user_id, CONCAT(CONCAT_WS(' [', CONCAT_WS(' '" 
					 . ', contact_first_name, contact_last_name), IF(IFNULL((IFNULL(up.pref_value' 
					 . ', ' . $scm . ') - SUM(ut.perc_assignment)), up.pref_value) > 0' 
					 . ', IFNULL((IFNULL(up.pref_value, ' . $scm . ') - SUM(ut.perc_assignment))' 
					 . ', up.pref_value), 0)), ' . "'%]')" . ' AS userFC' 
					 . ', IFNULL(SUM(ut.perc_assignment), 0) AS charge, u.user_username' 
					 . ', IFNULL(up.pref_value,' . $scm . ') AS chargeMax' 
					 . ', IF(IFNULL((IFNULL(up.pref_value, ' . $scm . ') ' 
					 . '- SUM(ut.perc_assignment)), up.pref_value) > 0' 
					 . ', IFNULL((IFNULL(up.pref_value, ' . $scm . ') - SUM(ut.perc_assignment))' 
					 . ', up.pref_value), 0) AS freeCapacity' );
		if (!empty($users)) { // use userlist if available otherwise pull data for all users
			$q->addWhere('u.user_id IN (' . implode(',', $users) . ')');
		}
		$q->addGroup('u.user_id');
		$q->addOrder('contact_last_name, contact_first_name');
		$sql = $q->prepare();
		$q->clear();
		//echo "<pre>$sql</pre>";
		return db_loadHashList($sql, $hash);
	}
	
	function getUserSpecificTaskPriority($user_id = 0, $task_id = NULL) {
		$q = new DBQuery;
		// use task_id of given object if the optional parameter task_id is empty
		$task_id = empty($task_id) ? $this->task_id : $task_id;
		
		$q->addTable('user_tasks');
		$q->addQuery('user_task_priority');
		$q->addWhere('user_id = ' . $user_id . ' AND task_id = ' . $task_id);
		$sql = $q->prepare();
		$q->clear();
		$prio = db_loadHash($sql, $priority);
		return $prio ? $priority['user_task_priority'] : NULL;
	}
	
	function updateUserSpecificTaskPriority($user_task_priority = 0, $user_id = 0
											, $task_id = NULL) {
		$q = new DBQuery;
		// use task_id of given object if the optional parameter task_id is empty
		$task_id = empty($task_id) ? $this->task_id : $task_id;
		
		$q->addTable('user_tasks');
		$q->addReplace('user_id', $user_id);
		$q->addReplace('task_id', $task_id);
		$q->addReplace('user_task_priority', $user_task_priority);
		$q->exec();
		$q->clear();
	}
	
	function getProject() {
		$q = new DBQuery;
		
		$q->addTable('projects');
		$q->addQuery('project_name, project_short_name, project_color_identifier');
		$q->addWhere("project_id = '" . $this->task_project ."'");
		$sql = $q->prepare();
		$q->clear();
		$proj = db_loadHash($sql, $projects);
		return $projects;
	}
	
	//Returns task children IDs
	function getChildren() {
		$q = new DBQuery;
		
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere("task_id <> '" . $this->task_id . "' AND task_parent = '" . $this->task_id ."'");
		$sql = $q->prepare();
		$q->clear();
		
		return db_loadColumn($sql);
	}
	
	// Returns task deep children IDs
	function getDeepChildren() {
		$q = new DBQuery;
		
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere("task_id <> '" . $this->task_id . "' AND task_parent = '" . $this->task_id ."'");
		$sql = $q->prepare();
		$q->clear();
		$children = db_loadColumn($sql);
		
		if ($children) {
			$deep_children = array();
			$tempTask = new CTask();
			foreach ($children as $child) {
				$tempTask->peek($child);
				$deep_children = array_merge($deep_children, $tempTask->getDeepChildren());
			}
			
			return array_merge($children, $deep_children);
		}
		return array();
	}
	
	/**
	 * This function, recursively, updates all tasks status
	 * to the one passed as parameter
	 */
	function updateSubTasksStatus($new_status, $task_id = null){
		$q = new DBQuery;
		
		if (is_null($task_id)) {
			$task_id = $this->task_id;
		}
		
		// get children
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere("task_parent = '" . $task_id . "'");
		$sql = $q->prepare();
		$q->clear();
		$tasks_id = db_loadColumn($sql);
		if (count($tasks_id) == 0) {
			return true;
		}
		
		// update status of children
		$q->addTable('tasks');
		$q->addUpdate('task_status', $new_status);
		$q->addWhere("task_parent = '" . $task_id . "'");
		$q->exec();
		$q->clear();
		
		// update status of children's children
		foreach ($tasks_id as $id) {
			if ($id != $task_id) {
				$this->updateSubTasksStatus($new_status, $id);
			}
		}
	}
	
	/**
	 * This function recursively updates all tasks project
	 * to the one passed as parameter
	 */
	function updateSubTasksProject($new_project , $task_id = null){
		$q = new DBQuery;
		
		if (is_null($task_id)) {
			$task_id = $this->task_id;
		}
		
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere("task_parent = '" . $task_id . "'");
		$sql = $q->prepare();
		$q->clear();
		$tasks_id = db_loadColumn($sql);
		
		if (count($tasks_id) == 0) {
			return true;
		}
		
		// update project of children
		$q->addTable('tasks');
		$q->addUpdate('task_project', $new_project);
		$q->addWhere("task_parent = '" . $task_id . "'");
		$q->exec();
		$q->clear();
		
		foreach ($tasks_id as $id) {
			if ($id != $task_id) {
				$this->updateSubTasksProject($new_project, $id);
			}
		}
	}
	
	function canUserEditTimeInformation(){
		global $AppUI;
		
		$project = new CProject();
		$project->load($this->task_project);
		
		// Code to see if the current user is
		// enabled to change time information related to task
		$can_edit_time_information = false;
		// Let's see if all users are able to edit task time information
		if (dPgetConfig('restrict_task_time_editing') == true && $this->task_id > 0) {
			
			// Am I the task owner?
			if ($this->task_owner == $AppUI->user_id) {
				$can_edit_time_information = true;
			}
			
			// Am I the project owner?
			if ($project->project_owner == $AppUI->user_id) {
				$can_edit_time_information = true;
			}
			
			// Am I sys admin?
			if (getPermission('admin', 'edit')) {
				$can_edit_time_information = true;
			}
			
		} else if (dPgetConfig('restrict_task_time_editing') == false || $this->task_id == 0) { 
			// If all users are able, then don't check anything
			$can_edit_time_information = true;
		}
		
		return $can_edit_time_information;
	}
	
	/**
	 * Injects a reminder event into the event queue.
	 * Repeat interval is one day, repeat count
	 * and days to trigger before event overdue is
	 * set in the system config.
	 */
	function addReminder() {
		$day = 86400;
		
		if (!dPgetConfig('task_reminder_control')) {
			return;
		}
		
		if (! $this->task_end_date) { // No end date, can't do anything.
			return $this->clearReminder(true); // Also no point if it is changed to null
		}
		
		if ($this->task_percent_complete >= 100) {
			return $this->clearReminder(true);
		}
		
		$eq = new EventQueue;
		$pre_charge = dPgetConfig('task_reminder_days_before', 1);
		$repeat = dPgetConfig('task_reminder_repeat', 100);
		
		/*
		 * If we don't need any arguments (and we don't) then we set this to null. 
		 * We can't just put null in the call to add as it is passed by reference.
		 */
		$args = null;
		
		// Find if we have a reminder on this task already
		$old_reminders = $eq->find('tasks', 'remind', $this->task_id);
		if (count($old_reminders)) {
			/* 
			 * It shouldn't be possible to have more than one reminder, 
			 * but if we do, we may as well clean them up now.
			 */
			foreach ($old_reminders as $old_id => $old_data) {
				$eq->remove($old_id);
			}
		}
		
		// Find the end date of this task, then subtract the required number of days.
		$date = new CDate($this->task_end_date);
		$today = new CDate(date('Y-m-d'));
		if (CDate::compare($date, $today) < 0) {
			$start_day = time();
		} else {
			$start_day = $date->getDate(DATE_FORMAT_UNIXTIME);
			$start_day -= ($day * $pre_charge);
		}
		
		$eq->add(array($this, 'remind'), $args, 'tasks', false, $this->task_id, 'remind', 
				 $start_day, $day, $repeat);
	}
	
	/**
	 * Called by the Event Queue processor to process a reminder
	 * on a task.
	 * @access		  public
	 * @param		 string		   $module		  Module name (not used)
	 * @param		 string		   $type Type of event (not used)
	 * @param		 integer		$id ID of task being reminded
	 * @param		 integer		$owner		  Originator of event
	 * @param		 mixed		  $args event-specific arguments.
	 * @return		  mixed		   true, dequeue event, false, event stays in queue.
	 -1, event is destroyed.
	*/
	function remind($module, $type, $id, $owner, &$args) {
		global $locale_char_set, $AppUI;
		$q = new DBQuery;
		
		$df = $AppUI->getPref('SHDATEFORMAT');
		$tf = $AppUI->getPref('TIMEFORMAT');
		// If we don't have preferences set for these, use ISO defaults.
		if (! $df) {
			$df = '%Y-%m-%d';
		}
		if (! $tf) {
			$tf = '%H:%m';
		}
		$df .= ' ' . $tf;
		
		// At this stage we won't have an object yet
		if (! $this->load($id)) {
			return -1; // No point it trying again later.
		}
		$this->htmlDecode();
		
		// Only remind on working days.
		$today = new CDate();
		if (! $today->isWorkingDay()) {
			return true;
		}
		
		// Check if the task is completed
		if ($this->task_percent_complete == 100) {
			return -1;
		}
		
		// Grab the assignee list
		$q->addTable('user_tasks', 'ut');
		$q->leftJoin('users', 'u', 'u.user_id = ut.user_id');
		$q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
		$q->addQuery('c.contact_id, contact_first_name, contact_last_name, contact_email');
		$q->addWhere('ut.task_id = ' . $id);
		$contacts = $q->loadHashList('contact_id');
		$q->clear();
		
		// Now we also check the owner of the task, as we will need
		// to notify them as well.
		$owner_is_not_assignee = false;
		$q->addTable('users', 'u');
		$q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
		$q->addQuery('c.contact_id, contact_first_name, contact_last_name, contact_email');
		$q->addWhere('u.user_id = ' . $this->task_owner);
		if ($q->exec(ADODB_FETCH_NUM)) {
			list($owner_contact, $owner_first_name, $owner_last_name, $owner_email) = $q->fetchRow();
			if (! isset($contacts[$owner_contact])) {
				$owner_is_not_assignee = true;
				$contacts[$owner_contact] = array(
												  'contact_id' => $owner_contact,
												  'contact_first_name' => $owner_first_name,
												  'contact_last_name' => $owner_last_name,
												  'contact_email' => $owner_email
												 );
			}
		}
		$q->clear();
		
		// build the subject line, based on how soon the
		// task will be overdue.
		$starts = new CDate($this->task_start_date);
		$expires = new CDate($this->task_end_date);
		$now = new CDate();
		$diff = $expires->dateDiff($now);
		$diff *= CDate::compare($expires, $now);
		$prefix = $AppUI->_('Task Due', UI_OUTPUT_RAW);
		if ($diff == 0) {
			$msg = $AppUI->_('TODAY', UI_OUTPUT_RAW);
		} else if ($diff == 1) {
			$msg = $AppUI->_('TOMORROW', UI_OUTPUT_RAW);
		} else if ($diff < 0) {
			$msg = $AppUI->_(array('OVERDUE', abs($diff), 'DAYS'));
			$prefix = $AppUI->_('Task', UI_OUTPUT_RAW);
		} else {
			$msg = $AppUI->_(array($diff, 'DAYS'));
		}
		
		$q->addTable('projects');
		$q->addQuery('project_name');
		$q->addWhere('project_id = ' . $this->task_project);
		$project_name = htmlspecialchars_decode($q->loadResult());
		$q->clear();
		
		$subject = $prefix . ' ' .$msg . ' ' . $this->task_name . '::' . $project_name;
		
		$body = ($AppUI->_('Task Due', UI_OUTPUT_RAW) . ': ' . $msg . "\n" 
				 . $AppUI->_('Project', UI_OUTPUT_RAW) . ': ' . $project_name . "\n" 
				 . $AppUI->_('Task', UI_OUTPUT_RAW) . ': ' . $this->task_name . "\n" 
				 . $AppUI->_('Start Date', UI_OUTPUT_RAW) . ': ' . $starts->format($df) . "\n" 
				 . $AppUI->_('Finish Date', UI_OUTPUT_RAW) . ': ' . $expires->format($df) . "\n" 
				 . $AppUI->_('URL', UI_OUTPUT_RAW) . ': ' . DP_BASE_URL 
				 . '/index.php?m=tasks&a=view&task_id=' . $this->task_id . '&reminded=1' . "\n\n" 
				 . $AppUI->_('Resources', UI_OUTPUT_RAW) . ":\n");
		foreach ($contacts as $contact) {
			if ($owner_is_not_assignee || $contact['contact_id'] != $owner_contact) {
				$body .= ($contact['contact_first_name'] . ' ' . $contact['contact_last_name'] 
						  . ' <' . $contact['contact_email'] . ">\n");
			}
		}
		$body .= ("\n" . $AppUI->_('Description', UI_OUTPUT_RAW) . ":\n" 
				  . $this->task_description . "\n");
		
		$mail = new Mail;
		foreach ($contacts as $contact) {
			if ($mail->ValidEmail($contact['contact_email'])) {
				$mail->To($contact['contact_email']);
			}
		}
		$mail->From($owner_email);
		$mail->Subject($subject, $locale_char_set);
		$mail->Body($body, $locale_char_set);
		return $mail->Send();
	}
	
	/**
	 *
	 */
	function clearReminder($dont_check = false) {
		$ev = new EventQueue;
		
		$event_list = $ev->find('tasks', 'remind', $this->task_id);
		if (count($event_list)) {
			foreach ($event_list as $id => $data) {
				if ($dont_check || $this->task_percent_complete >= 100) {
					$ev->remove($id);
				}
			}
		}
	}
	
}


/**
 * CTaskLog Class
 */
class CTaskLog extends CDpObject 
{
	var $task_log_id = NULL;
	var $task_log_task = NULL;
	var $task_log_name = NULL;
	var $task_log_description = NULL;
	var $task_log_creator = NULL;
	var $task_log_hours = NULL;
	var $task_log_date = NULL;
	var $task_log_costcode = NULL;
	var $task_log_problem = NULL;
	var $task_log_reference = NULL;
	var $task_log_related_url = NULL;
	
	
	function CTaskLog() {
		$this->CDpObject('task_log', 'task_log_id');
		
		// ensure changes to checkboxes are honoured
		$this->task_log_problem = intval($this->task_log_problem);
	}
	
	function dPTrimAll() {
		$spacedDescription = $this->task_log_description;
		parent::dPTrimAll();
		$this->task_log_description = $spacedDescription;
	}
	
	// overload check method
	function check() {
		$this->task_log_hours = (float) $this->task_log_hours;
		return NULL;
	}
}

function openClosedTask($task){
	global $tasks_opened;
	global $tasks_closed;
	$tasks_closed = (($tasks_closed) ? $tasks_closed : array());
	$tasks_opened = (($tasks_opened) ? $tasks_opened : array());
	$to_open_task = new CTask();
	
	if (is_array($task) || $task > 0) {
		if (is_array($task)) {
			$task_dynamic = $task['task_dynamic'];
			$task_id = $task['task_id'];

		} else {
			$to_open_task->peek($task);
			$task_dynamic = $to_open_task->task_dynamic;
			$task_id = $task;
		}
		// don't "open" non-dynamic tasks
		if ($task_dynamic == 1) {
			// only unset that which is set
			$index = array_search($task_id, $tasks_closed);
			if ($index !== false) {
				unset($tasks_closed[$index]);
			}
			
			//don't double open or we can't close properly
			if (!in_array($task_id, $tasks_opened)) { 
			$tasks_opened[] = $task_id;
			}
		}
	}
}

function openClosedTaskRecursive($task_id) {
	global $tasks_opened;
	global $tasks_closed;
	$tasks_closed = (($tasks_closed) ? $tasks_closed : array());
	$tasks_opened = (($tasks_opened) ? $tasks_opened : array());
	$open_task = new CTask();
	
	if ($task_id > 0) {
		openClosedTask($task_id);
		
		$open_task->peek($task_id) ;
		$children_to_open = $open_task->getChildren();
		foreach ($children_to_open as $to_open) {
			openClosedTaskRecursive($to);
		}
	}
}

function closeOpenedTask($task){
	global $tasks_opened;
	global $tasks_closed;
	$tasks_closed = (($tasks_closed) ? $tasks_closed : array());
	$tasks_opened = (($tasks_opened) ? $tasks_opened : array());
	$to_close_task = new CTask();
	
	if (is_array($task) || $task > 0) {	
		if (is_array($task)) {
			$task_id = $task['task_id'];
			$task_dynamic = $task['task_dynamic'];
		} else {
			$to_close_task->peek($task);
			$task_id = $task;
			$task_dynamic = $to_close_task->task_dynamic;
		}
		// don't "close" non-dynamic tasks
		if ($task_dynamic == 1) {
			// only unset that which is set
			$index = array_search($task_id, $tasks_opened);
			if ($index !== false) {
				unset($tasks_opened[$index]);
			}
			
			//don't double close or we can't open properly
			if (!in_array($task_id, $tasks_closed)) {
				$tasks_closed[] = $task_id;
			}
		}
	}
}

function closeOpenedTaskRecursive($task_id){
	global $tasks_opened;
	global $tasks_closed;
	$tasks_closed = (($tasks_closed) ? $tasks_closed : array());
	$tasks_opened = (($tasks_opened) ? $tasks_opened : array());
	$close_task = new CTask();
	
	if ($task_id > 0) {
		closeOpenedTask($task_id);
		
		$close_task->peek($task_id) ;
		$children_to_close = $close_task->getChildren();
		foreach ($children_to_close as $to_close) {
		  closeOpenedTaskRecursive($to_close);
		}
		
	}
}

//This kludgy function echos children tasks as threads

function showtask(&$a, $level=0, $is_opened = true, $today_view = false, $hideOpenCloseLink=false
				  , $allowRepeat = false) {
	global $AppUI, $done, $query_string, $durnTypes, $userAlloc, $showEditCheckbox;
	global $tasks_opened, $tasks_closed, $user_id;
	
	$tasks_closed = (($tasks_closed) ? $tasks_closed : array());
	$tasks_opened = (($tasks_opened) ? $tasks_opened : array());
	$done = (($done) ? $done : array());
	
	$now = new CDate();
	$df = $AppUI->getPref('SHDATEFORMAT');
	$df .= ' ' . $AppUI->getPref('TIMEFORMAT');
	$perms =& $AppUI->acl();
	$show_all_assignees = dPgetConfig('show_all_task_assignees', false);
	
	if (!isset($done[$a['task_id']]))	{ 
		$done[$a['task_id']] = 1;
	} else if (!($allowRepeat)) {
		//by default, we shouldn't allow repeat displays of the same task
		return;
	}
	
	$task_obj = new CTask();
	$task_obj->peek($a['task_id']);
	if (!($task_obj->canAccess((($user_id) ? $user_id : $AppUI->user_id)))) {
		//don't show tasks that we can't access
		return;
	}
	
	if ($is_opened) {
		openClosedTask($a);
	} else {
		closeOpenedTask($a);
	}
	
	$start_date = intval($a['task_start_date']) ? new CDate($a['task_start_date']) : null;
	$end_date = intval($a['task_end_date']) ? new CDate($a['task_end_date']) : null;
	$last_update = ((isset($a['last_update']) && intval($a['last_update'])) 
					? new CDate($a['last_update']) : null);
	
	// prepare coloured highlight of task time information
	$sign = 1;
	$style = '';
	if ($start_date) {
		
		if ($now->after($start_date) && $a['task_percent_complete'] == 0) {
			$style = 'background-color:#ffeebb';
		} else if ($now->after($start_date) && $a['task_percent_complete'] < 100) {
			$style = 'background-color:#e6eedd';
		}
		
		if (!empty($end_date) && $now->after($end_date)) {
			$sign = -1;
			$style = 'background-color:#cc6666;color:#ffffff';
		}

		if (!$end_date) {
			/*
			 ** end date calc has been moved to calcEndByStartAndDuration()-function
			 ** called from array_csort and tasks.php 
			 ** perhaps this fallback if-clause could be deleted in the future, 
			 ** didn't want to remove it shortly before the 2.0.2
			 */ 
			$end_date = new CDate('0000-00-00 00:00:00');
		}
		
		if ($a['task_percent_complete'] == 100){
			$style = 'background-color:#aaddaa; color:#00000';
		}
		
		$days = $now->dateDiff($end_date) * $sign;
	}
	
	$s = "\n<tr>";
	// edit icon
	$s .= "\n\t<td>";
	$canEdit = getPermission('tasks', 'edit', $a['task_id']);
	$canViewLog = $perms->checkModuleItem('task_log', 'view', $a['task_id']);
	if ($canEdit) {
		$s .= ("\n\t\t".'<a href="?m=tasks&a=addedit&task_id=' . $a['task_id'] . '">'
			   . "\n\t\t\t".'<img src="./images/icons/pencil.gif" alt="' . $AppUI->_('Edit Task') 
			   . '" border="0" width="12" height="12">' . "\n\t\t</a>");
	}
	$s .= "\n\t</td>";
	// pinned
	$pin_prefix = $a['task_pinned'] ? '' : 'un';
	$s .= ("\n\t<td>\n\t\t" . '<a href="?m=tasks&pin=' . ($a['task_pinned']?0:1) 
		   . '&task_id=' . $a['task_id'] . '">'
		   . "\n\t\t\t".'<img src="./images/icons/' . $pin_prefix . 'pin.gif" alt="'
		   . $AppUI->_($pin_prefix . 'pin Task') . '" border="0" width="12" height="12">'
		   . "\n\t\t</a>\n\t</td>");
	// New Log
	$s .= ("\n\t" . '<td align="center">');
	if ($canViewLog && $a['task_dynamic'] != 1) {
		$s .= ('<a href="?m=tasks&a=view&task_id=' . $a['task_id'] . '&tab=1">' 
			   . $AppUI->_('Log') . '</a>');
	} else {
		$s .= $AppUI->_('-');
	}
	$s .= ('</td>');
	// percent complete and priority
	$s .= ("\n\t" . '<td align="right">' . intval($a['task_percent_complete']) . '%</td>' 
		   . "\n\t" . '<td align="center" nowrap="nowrap">');
	if (@$a['task_log_problem']>0) {
		$s .= ('<a href="?m=tasks&a=view&task_id=' 
			   . $a['task_id'] . '&tab=0&problem=1">' 
			   . dPshowImage('./images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem!') 
			   . '</a>');
	} else if ($a['task_priority'] != 0) {
		$s .= "\n\t\t" . dPshowImage(('./images/icons/priority' . (($a['task_priority'] > 0) 
																   ? '+' : '-') 
									  . abs($a['task_priority']) . '.gif'), 13, 16, '', '');
	}
	$s .= ((@$a['file_count'] > 0) ? '<img src="./images/clip.png" alt="F">' : '') . '</td>';
	// dots
	$s .= '<td width="' . (($today_view) ? '50%' : '90%') . '">';
	//level
	if ($level == -1) {
		$s .= '...';
	}
	for ($y=0; $y < $level; $y++) {  
		$s .= ('<img src="' . (($y+1 == $level) ? './images/corner-dots.gif' : './images/shim.gif') 
			   . '" width="16" height="12" border="0">');
	}
	// name link
	/*
	$alt = ((strlen($a['task_description']) > 80) 
			? (substr($a['task_description'], 0, 80) . '...') : $a['task_description']);
	// instead of the statement below
	$alt = str_replace('"', '&quot;', $alt);
	$alt = htmlspecialchars($alt);
	$alt = str_replace("\r", ' ', $alt);
	$alt = str_replace("\n", ' ', $alt);
	*/
	$alt = ((!empty($a['task_description']))
			? ('onmouseover="return overlib(' . "'" 
			   . htmlspecialchars('<div><p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', 
														   addslashes($a['task_description']))
								  , ENT_QUOTES) . '</p></div>' . "', CAPTION, '" 
			   . $AppUI->_('Description') . "'" . ', CENTER);" onmouseout="nd();"')
			: ' ');
	
	if ($a['task_milestone'] > 0) {
		$s .= ('&nbsp;<a href="./index.php?m=tasks&a=view&task_id=' . $a['task_id'] . '" ' 
			   . $alt . '>' . '<b>' . $a['task_name'] . '</b></a>' 
			   . '<img src="./images/icons/milestone.gif" border="0"></td>');
	} else if ($a['task_dynamic'] == 1){
		if (! ($today_view || $hideOpenCloseLink)) {
			$s .= ('<a href="index.php' . $query_string 
				   . (($is_opened) 
					  ? ('&close_task_id='.$a['task_id'] 
						 . '"><img src="images/icons/collapse.gif" align="center"') 
					  : ('&open_task_id='.$a['task_id'] . '"><img src="images/icons/expand.gif"')) 
				   . ' border="0" /></a>');
		}
		$s .= ('&nbsp;<a href="./index.php?m=tasks&a=view&task_id=' . $a['task_id'] . '" ' 
			   . $alt . '><b><i>' . $a['task_name'] . '</i></b></a></td>');
	} else {
	  $s .= ('&nbsp;<a href="./index.php?m=tasks&a=view&task_id=' . $a['task_id'] . '" ' 
			 . $alt . '>' . $a['task_name'] . '</a></td>');
	}
	
	if ($today_view) { // Show the project name
		$s .= ('<td width="50%"><a href="./index.php?m=projects&a=view&project_id=' 
			   . $a['task_project'] . '">' . '<span style="padding:2px;background-color:#' 
			   . $a['project_color_identifier'] . ';color:' 
			   . bestColor($a['project_color_identifier']) . '">' . $a['project_name'] . '</span>' 
			   . '</a></td>');
	}
	// task owner
	if (! $today_view) {
		$s .= ('<td nowrap="nowrap" align="center">' . '<a href="?m=admin&a=viewuser&user_id=' 
			   . $a['user_id'] . '">' . $a['user_username'] . '</a>' . '</td>');
	}
	// $s .= '<td nowrap="nowrap" align="center">' . $a['user_username'] . '</td>';
	if (isset($a['task_assigned_users']) && ($assigned_users = $a['task_assigned_users'])) {
		$a_u_tmp_array = array();
		if ($show_all_assignees) {
			$s .= '<td align="center">';
			foreach ($assigned_users as $val) {
				/*
				$a_u_tmp_array[] = ('<a href="mailto:' . $val['user_email'] . '">' 
									. $val['user_username'] . '</a>'); 
				*/
				$a_u_tmp_array[] = ('<a href="?m=admin&a=viewuser&user_id=' . $val['user_id'] . '"' 
						  . 'title="' . $AppUI->_('Extent of Assignment') . ':' 
						  . $userAlloc[$val['user_id']]['charge'] . '%; ' 
						  . $AppUI->_('Free Capacity') . ':' 
						  . $userAlloc[$val['user_id']]['freeCapacity'] . '%' . '">' 
						  . $val['user_username'] . ' (' . $val['perc_assignment'] . '%)</a>');
			}
			$s .= join (', ', $a_u_tmp_array) . '</td>';
		} else {
			$s .= ('<td align="center" nowrap="nowrap">'
				   .'<a href="?m=admin&a=viewuser&user_id=' . $assigned_users[0]['user_id'] 
				   . '" title="' . $AppUI->_('Extent of Assignment') . ':' 
				   . $userAlloc[$assigned_users[0]['user_id']]['charge']. '%; ' 
				   . $AppUI->_('Free Capacity') . ':' 
				   . $userAlloc[$assigned_users[0]['user_id']]['freeCapacity'] . '%">' 
				   . $assigned_users[0]['user_username'] 
				   .' (' . $assigned_users[0]['perc_assignment'] .'%)</a>');
			if ($a['assignee_count'] > 1) {
				$s .= (' <a href="javascript: void(0);" onClick="toggle_users(' 
					   . "'users_" . $a['task_id'] . "'" . ');" title="' 
					   . join (', ', $a_u_tmp_array) .'">(+' . ($a['assignee_count'] - 1) . ')</a>'
					   . '<span style="display: none" id="users_' . $a['task_id'] . '">');
				$a_u_tmp_array[] = $assigned_users[0]['user_username'];
				for ($i = 1, $xi = count($assigned_users); $i < $xi; $i++) {
					$a_u_tmp_array[] = $assigned_users[$i]['user_username'];
					$s .= ('<br /><a href="?m=admin&a=viewuser&user_id=' 
						   . $assigned_users[$i]['user_id'] . '" title="' 
						   . $AppUI->_('Extent of Assignment') . ':' 
						   . $userAlloc[$assigned_users[$i]['user_id']]['charge'] . '%; ' 
						   . $AppUI->_('Free Capacity') . ':' 
						   . $userAlloc[$assigned_users[$i]['user_id']]['freeCapacity'] . '%">' 
						   . $assigned_users[$i]['user_username'] . ' (' 
						   . $assigned_users[$i]['perc_assignment'] . '%)</a>');
				}
				$s .= '</span>';
			}
			$s .= '</td>';
		}
	} else if (! $today_view) {
		// No users asigned to task
		$s .= '<td align="center">-</td>';
	}
	// duration or milestone
	$s .= ('<td nowrap="nowrap" align="center" style="' . $style . '">' 
		   . ($start_date ? $start_date->format($df) : '-') . '</td>' 
		   . '<td align="center" nowrap="nowrap" style="' . $style . '">' . $a['task_duration'] 
		   . ' ' . $AppUI->_($durnTypes[$a['task_duration_type']]) . '</td>' 
		   . '<td nowrap="nowrap" align="center" style="' . $style . '">' 
		   . ($end_date ? $end_date->format($df) : '-') . '</td>');
	if ($today_view) {
		$s .= ('<td nowrap="nowrap" align="center" style="' . $style . '">' 
			   . $a['task_due_in'] . '</td>');
	} else if ($AppUI->isActiveModule('history') && getPermission('history', 'view')) {
		$s .= ('<td nowrap="nowrap" align="center" style="' . $style.'">' 
			   . ($last_update ? $last_update->format($df) : '-') . '</td>');
	}
	
	// Assignment checkbox
	if ($showEditCheckbox) {
		$s .= ("\n\t" . '<td align="center">' . '<input type="checkbox" name="selected_task[' 
			   . $a['task_id'] . ']" value="' . $a['task_id'] . '"/></td>');
	}
	$s .= '</tr>';
	echo $s;
}

function findchild(&$tarr, $parent, $level=0) {
	global $tasks_opened, $tasks_closed, $tasks_filtered, $children_of;
	$tasks_closed = (($tasks_closed) ? $tasks_closed : array());
	$tasks_opened = (($tasks_opened) ? $tasks_opened : array());
	
	$level = $level+1;
	
	foreach ($tarr as $x => $task) {
		if ($task['task_parent'] == $parent && $task['task_parent'] != $task['task_id']) {
			$is_opened = (!($task['task_dynamic']) || !(in_array($task['task_id'], $tasks_closed)));
			
			//check for child
			$no_children = empty($children_of[$task['task_id']]);
			
			showtask($task, $level, $is_opened, false, $no_children);
			if ($is_opened && !($no_children)) {
				/*
				 * Yes, this is stupid, but there was previously a bug where if you had
				 * two dynamic tasks at the same level and the child of a dynamic task,
				 * they would only both display if the first one was closed.  The moment
				 * you opened the first one, the second would disappear.
				 * 
				 * There is something screwy happening in this function in the pass by
				 * reference.  I suspect it's a PHP4 vs PHP5 oddity.
				 */
				$tmp = $tarr;
				findchild($tmp, $task['task_id'], $level);
			}
		}
	}
}

/*
array_csort($data_array [,$col, $order, $type [, $col, $order, $type [...]]]);

$data_array - multi-dimensional array of query results to sort
$col - data table "column" to sort by
$order - SORT_ASC or SORT_DESC flag values
$type - SORT_REGULAR, SORT_NUMERIC, or SORT_STRING flag values

...any number of column, order, and type values can be passed 
but they must be specified in that order and all sets except 
the last must be defined fully

Examples - 
valid:
array_csort($data_array,$col, $order, $type, $col2, $order2, $type2);
array_csort($data_array,$col, $order, $type, $col2, $order2,);
array_csort($data_array,$col, $order, $type, $col2, $type2);
array_csort($data_array);

invalid:
array_csort($data_array,$col, $type, $col2, $order2, $type2);

*/
function array_csort() {   

	$args = func_get_args();
	$marray = array_shift($args);
	
	if (empty($marray)) {
		return array();
	}
	
	$i = 0;
	$msortline = 'return(array_multisort(';
	$sortarr = array();
	foreach ($args as $arg) {
		if ($i % 3) {
			$msortline .= $arg . ', ';
		} else {
			foreach ($marray as $j => $item) {
				
				/* we have to calculate the end_date via start_date+duration for 
				 ** end='0000-00-00 00:00:00' before sorting, see mantis #1509:
				 
				 ** Task definition writes the following to the DB:
				 ** A without start date: start = end = NULL
				 ** B with start date and empty end date: start = startdate, 
				                                          end = '0000-00-00 00:00:00'
				 ** C start + end date: start= startdate, end = end date
				 
				 ** A the end_date for the middle task (B) is ('dynamically') calculated on display 
				 ** via start_date+duration, it may be that the order gets wrong due to the fact 
				 ** that sorting has taken place _before_.
				 */
				if ($item['task_end_date'] == '0000-00-00 00:00:00') {
					$item['task_end_date'] = calcEndByStartAndDuration($marray[$j]);
				}
				$sortarr[$i][$j] = $marray[$j][$arg];
			}
			$msortline .= '$sortarr[' . $i . '], ';
		}
		$i++;
	}
	$msortline .= '$marray));';
	eval($msortline);
	
	return $marray;
}

/*
 ** Calc End Date via Startdate + Duration
 ** @param array task	A DB row from the earlier fetched tasklist
 ** @return string	Return calculated end date in MySQL-TIMESTAMP format	
 */

function calcEndByStartAndDuration($task) {
	$end_date = new CDate($task['task_start_date']);
	$end_date->addSeconds(@$task['task_duration'] * $task['task_duration_type'] * SEC_HOUR);
	return $end_date->format(FMT_DATETIME_MYSQL);
}

function sort_by_item_title($title, $item_name, $item_type) {
	global $AppUI, $project_id, $task_id, $min_view;
	global $task_sort_item1, $task_sort_type1, $task_sort_order1;
	global $task_sort_item2, $task_sort_type2, $task_sort_order2;
	
	
	if ($task_sort_item2 == $item_name) {
		$item_order = $task_sort_order2;
	}
	if ($task_sort_item1 == $item_name) {
		$item_order = $task_sort_order1;
	}
	//Hack for Problem Log/Priority Sorting
	if ($item_name == 'task_log_problem_priority' && $task_sort_item2 == 'task_priority') {
		$item_order = $task_sort_order2;
	}
	
	if (isset($item_order)) {
		echo ('<img src="./images/arrow-' . (($item_order == SORT_ASC) ? 'up' : 'down') 
			  . '.gif" width="11" height="11">');
	} else {
		$item_order = SORT_DESC;
	}
	
	/* flip the sort order for the link */
	$item_order = ($item_order == SORT_ASC) ? SORT_DESC : SORT_ASC;
	
	echo ('<a href="./index.php?');
	foreach ($_GET as $var => $val) {
		if (!(in_array($var, array('task_sort_item1', 'task_sort_type1', 'task_sort_order1', 
		                           'task_sort_item2', 'task_sort_type2', 'task_sort_order2')))) {
			echo ((($not_first) ? '&' : '') . $var . '=' . $val);
			$not_first = 1;
		}
	}
	
	if ($item_name == 'task_log_problem_priority') {
		echo '&task_sort_item1=task_log_problem';
		echo '&task_sort_type1=' . $item_type;
		echo '&task_sort_order1=' . SORT_DESC;
		echo '&task_sort_item2=task_priority';
		echo '&task_sort_type2=' . $item_type;
		echo '&task_sort_order2=' . $item_order;
	} else {
		echo '&task_sort_item1=' . $item_name;
		echo '&task_sort_type1=' . $item_type;
		echo '&task_sort_order1=' . $item_order;
		
		if ((($task_sort_item1 && $task_sort_item1 != $item_name) 
			 || $task_sort_item2) && $task_sort_item2 != 'task_priority')  {
			$item_num = (($task_sort_item1 == $item_name) ? '2' : '1');
			echo '&task_sort_item2=' . ${'task_sort_item' . $item_num};
			echo '&task_sort_type2=' . ${'task_sort_type' . $item_num};
			echo '&task_sort_order2=' . ${'task_sort_order' . $item_num};
		}
	}
	echo '" class="hdr">';
	
	echo $AppUI->_($title);
	
	echo '</a>';
}

?>

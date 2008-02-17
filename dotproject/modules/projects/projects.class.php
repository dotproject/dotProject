<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision$
*/

require_once ($AppUI->getSystemClass ('dp'));
require_once ($AppUI->getLibraryClass('PEAR/Date'));
require_once ($AppUI->getModuleClass('tasks'));
require_once ($AppUI->getModuleClass('companies'));
require_once ($AppUI->getModuleClass('departments'));

/**
 * The Project Class
 */
class CProject extends CDpObject {
	var $project_id = NULL;
	var $project_company = NULL;
	var $project_company_internal = NULL;
	var $project_department = NULL;
	var $project_name = NULL;
	var $project_short_name = NULL;
	var $project_owner = NULL;
	var $project_url = NULL;
	var $project_demo_url = NULL;
	var $project_start_date = NULL;
	var $project_end_date = NULL;
	var $project_actual_end_date = NULL;
	var $project_status = NULL;
	var $project_percent_complete = NULL;
	var $project_color_identifier = NULL;
	var $project_description = NULL;
	var $project_target_budget = NULL;
	var $project_actual_budget = NULL;
	var $project_creator = NULL;
	var $project_private = NULL;
	var $project_departments= NULL;
	var $project_contacts = NULL;
	var $project_priority = NULL;
	var $project_type = NULL;
	
	function CProject() {
		$this->CDpObject('projects', 'project_id');
	}
	
	function check() {
		// ensure changes of state in checkboxes is captured
		$this->project_private = intval($this->project_private);
		// Make sure project_short_name is the right size (issue with encoded characters)
		if (strlen($this->project_short_name) > 10) {
			$this->project_short_name = substr($this->project_short_name, 0, 10);
		}
		// Make sure empty dates are nulled.  Cannot save an empty date.
		if (empty($this->project_end_date)) {
			$this->project_end_date = null;
		}
		
		return null; // object is ok
	}
	
	function load($oid=null, $strip = true) {
		$result = parent::load($oid, $strip);
		if ($result && $oid) {
			$working_hours = ((dPgetConfig('daily_working_hours')) 
			                  ? dPgetConfig('daily_working_hours'):8);
			
			$q = new DBQuery;
			$q->addTable('projects');
			$q->addQuery(' SUM(t1.task_duration * t1.task_percent_complete' 
						 . ' * IF(t1.task_duration_type = 24, ' . $working_hours 
						 . ', t1.task_duration_type)) / SUM(t1.task_duration' 
						 . ' * IF(t1.task_duration_type = 24, ' . $working_hours 
						 . ', t1.task_duration_type)) AS project_percent_complete');
			$q->addJoin('tasks', 't1', 'projects.project_id = t1.task_project');
			$q->addWhere('project_id = ' . $oid . ' AND t1.task_id = t1.task_parent');
			$this->project_percent_complete = $q->loadResult();
		}
		return $result;
	}
	
	// overload canDelete
	function canDelete(&$msg, $oid=null) {
		// TODO: check if user permissions are considered when deleting a project
		global $AppUI;
		$perms =& $AppUI->acl();
		
		return $perms->checkModuleItem('projects', 'delete', $oid);
		
		// NOTE: I uncommented the dependencies check since it is
		// very anoying having to delete all tasks before being able
		// to delete a project.
		
		/*
		$tables[] = array('label' => 'Tasks', 'name' => 'tasks', 'idfield' => 'task_id', 
		                  'joinfield' => 'task_project');
		// call the parent class method to assign the oid
		return CDpObject::canDelete($msg, $oid, $tables);
		*/
	}
	
	function delete() {
		$this->load($this->project_id);
		addHistory('projects', $this->project_id, 'delete', $this->project_name, 
		           $this->project_id);
		$q = new DBQuery;
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere('task_project = ' . $this->project_id);
		$sql = $q->prepare();
		$q->clear();
		$tasks_to_delete = db_loadColumn ($sql);
		foreach ($tasks_to_delete as $task_id) {
			$q->setDelete('user_tasks');
			$q->addWhere('task_id =' . $task_id);
			$q->exec();
			$q->clear();
			$q->setDelete('task_dependencies');
			$q->addWhere('dependencies_req_task_id =' . $task_id);
			$q->exec();
			$q->clear();
		}
		$q->setDelete('tasks');
		$q->addWhere('task_project =' . $this->project_id);
		$q->exec();
		$q->clear();
		
		// remove the project-contacts and project-departments map
		$q->setDelete('project_contacts');
		$q->addWhere('project_id =' . $this->project_id);
		$q->exec();
		$q->clear();
		$q->setDelete('project_departments');
		$q->addWhere('project_id =' . $this->project_id);
		$q->exec();
		$q->clear();
		$q->setDelete('projects');
		$q->addWhere('project_id =' . $this->project_id);
		
		$result = ((!$q->exec()) ? db_error() : NULL);
		$q->clear();
		return $result;
	}
	
	/**	Import tasks from another project
	*
	*	@param	int		Project ID of the tasks come from.
	*	@return	bool	
	**/
	function importTasks ($from_project_id) {
		
		// Load the original
		$origProject = new CProject ();
		$origProject->load($from_project_id);
		$q = new DBQuery;
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere('task_project =' . $from_project_id);
		//$q->addWhere('task_id = task_parent');
		$sql = $q->prepare();
		$q->clear();
		$tasks = array_flip(db_loadColumn ($sql));
		
		$origDate = new CDate($origProject->project_start_date);
		$destDate = new CDate ($this->project_start_date);
		$timeOffset = $destDate->getTime() - $origDate->getTime();
		
		// Old dependencies array
		$deps = array();
		// New dependencies array
		$newDeps = array();
		// Old2New task ID array
		$taskXref = array();
		// New task ID to old parent array
		$nid2op = array();
		
		// Copy each task into this project and get their deps
		foreach ($tasks as $orig => $void) {
			$objTask = new CTask();
			$objTask->load ($orig);
			
			// Grab the old parent id
			$oldParent = (integer)$objTask->task_parent;
			
			$deps[$orig] = $objTask->getDependencies ();
			$destTask = $objTask->copy($this->project_id, 0);
			$nid2op[$destTask->task_id] = $oldParent;
			$tasks[$orig] = $destTask;
			$taskXref[$orig] = (integer)$destTask->task_id;
		}
		
		// Build new dependencies array
		foreach($deps as $odkey => $od) {
			$ndt = '';
			$ndkey = $taskXref[$odkey];
			$odep = explode(',', $od);
			foreach($odep as $odt) {
				$ndt = $ndt . $taskXref[$odt] . ',';
			}
			$ndt = rtrim($ndt, ',');
			$newDeps[$ndkey] = $ndt;
		}
		
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere('task_project =' . $this->project_id);
		$tasks = $q->loadColumn();
		
		// Update dates based on new project's start date. 
		$newTask = new CTask();
		foreach ($tasks as $task_id) {
			$newTask->load($task_id);
			// Fix task start date from project start date offset
			$origDate->setDate ($newTask->task_start_date);
			$destDate->setDate ($origDate->getTime() + $timeOffset, DATE_FORMAT_UNIXTIME); 
			$destDate = $destDate->next_working_day();
			$newTask->task_start_date = $destDate->format(FMT_DATETIME_MYSQL);   
			
			// Fix task end date from start date + work duration
			//$newTask->calc_task_end_date();
			if (!empty($newTask->task_end_date) 
			    && $newTask->task_end_date != '0000-00-00 00:00:00') {
				$origDate->setDate ($newTask->task_end_date);
				$destDate->setDate (($origDate->getTime() + $timeOffset), DATE_FORMAT_UNIXTIME); 
				$destDate = $destDate->next_working_day();
				$newTask->task_end_date = $destDate->format(FMT_DATETIME_MYSQL);
			}
			
			$newTask->task_parent = $taskXref[$nid2op[$newTask->task_id]];
			
			$newTask->store();
			$newTask->updateDependencies($newDeps[$task_id]);
		} // end Fix record integrity	
		
	} // end of importTasks
	
	/**
	**	Overload of the dpObject::getAllowedRecords 
	**	to ensure that the allowed projects are owned by allowed companies.
	**
	**	@author	handco <handco@sourceforge.net>
	**	@see	dpObject::getAllowedRecords
	**/

	function getAllowedRecords($uid, $fields='*', $orderby='', $index=null, $extra=null) {
		$oCpy = new CCompany ();
		
		$aCpies = $oCpy->getAllowedRecords ($uid, 'company_id, company_name');
		
		$buffer = ((count($aCpies)) 
		           ? ('(project_company IN (' . implode(',', array_keys($aCpies)) . '))') 
		           : '1 = 0');
		$extra['where'] = ((($extra['where'] != '' ) ? ($extra['where'] . ' AND ') : '') 
		                   . $buffer);

		return parent::getAllowedRecords ($uid, $fields, $orderby, $index, $extra);
				
	}
	
	function getAllowedSQL($uid, $index = null) {
		$oCpy = new CCompany ();
		
		$where = $oCpy->getAllowedSQL ($uid, 'project_company');
		$project_where = parent::getAllowedSQL($uid, $index);
		return array_merge($where, $project_where);
	}
	
	function setAllowedSQL($uid, &$query, $index = null, $key = null) {
		$oCpy = new CCompany;
		parent::setAllowedSQL($uid, $query, $index, $key);
		$oCpy->setAllowedSQL($uid, $query, 'project_company');
	}
	
	/**
	 *	Overload of the dpObject::getDeniedRecords 
	 *	to ensure that the projects owned by denied companies are denied.
	 *
	 *	@author	handco <handco@sourceforge.net>
	 *	@see	dpObject::getAllowedRecords
	 */
	function getDeniedRecords($uid) {
		$aBuf1 = parent::getDeniedRecords ($uid);
		
		$oCpy = new CCompany ();
		// Retrieve which projects are allowed due to the company rules 
		$aCpiesAllowed = $oCpy->getAllowedRecords ($uid, 'company_id,company_name');
		
		$q = new DBQuery;
		$q->addTable('projects');
		$q->addQuery('project_id');
		if (count($aCpiesAllowed)) {
			$q->addWhere('NOT (project_company IN (' . implode (',', array_keys($aCpiesAllowed)) 
			             . '))');
		}
		$sql = $q->prepare();
		$q->clear();
		$aBuf2 = db_loadColumn ($sql);
		
		return array_merge ($aBuf1, $aBuf2); 
		
	}
	
	function getAllowedProjectsInRows($userId) {
		$q = new DBQuery;
		$q->addQuery('project_id, project_status, project_name, project_description' 
		             . ', project_short_name');
		$q->addTable('projects');                     
		$q->addOrder('project_short_name');
		$this->setAllowedSQL($userId, $q);
		$allowedProjectRows = $q->exec();
		
		return $allowedProjectRows;
	}
	
	function getAssignedProjectsInRows($userId) {
		$q = new DBQuery;
		$q->addQuery('project_id, project_status, project_name, project_description' 
		             . ', project_short_name');
		$q->addTable('projects');
		$q->addJoin('tasks', 't', 't.task_project = project_id');
		$q->addJoin('user_tasks', 'ut', 'ut.task_id = t.task_id');
		$q->addWhere('ut.user_id = ' . $userId);
		$q->addGroup('project_id');                     
		$q->addOrder('project_name');
		$this->setAllowedSQL($userId, $q);
		$allowedProjectRows = $q->exec();
		
		return $allowedProjectRows;
	}
	
	/* Retrieve tasks with latest task_end_dates within given project
	 * @param int Project_id
	 * @param int SQL-limit to limit the number of returned tasks
	 * @return array List of criticalTasks
	 */
	function getCriticalTasks($project_id = NULL, $limit = 1) {
		$project_id = !empty($project_id) ? $project_id : $this->project_id;
		$q = new DBQuery;
		$q->addTable('tasks');
		if ($project_id) {
			$q->addWhere('task_project = ' . $project_id);
		}
		$q->addWhere("!isnull(task_end_date) AND task_end_date !=  '0000-00-00 00:00:00'");
		$q->addOrder('task_end_date DESC');
		$q->setLimit($limit);
		
		return $q->loadList();
	}
	
	function store() {
		$this->dPTrimAll();
        
		$msg = $this->check();
		if($msg) {
			return get_class($this) . '::store-check failed - ' . $msg;
		}
		
		if($this->project_id) {
			$ret = db_updateObject('projects', $this, 'project_id', false);
			addHistory('projects', $this->project_id, 'update', $this->project_name, 
			           $this->project_id);
		} else {
			$ret = db_insertObject('projects', $this, 'project_id');
			addHistory('projects', $this->project_id, 'add', $this->project_name, 
			           $this->project_id);
		}
		
		//split out related departments and store them seperatly.
		$q = new DBQuery;
		$q->setDelete('project_departments');
		$q->addWhere('project_id=' . $this->project_id);
		$q->exec();
		$q->clear();
		if ($this->project_departments) {
			$departments = explode(',',$this->project_departments);
			foreach ($departments as $department) {
				$q->addTable('project_departments');
				$q->addInsert('project_id', $this->project_id);
				$q->addInsert('department_id', $department);
				$q->exec();
				$q->clear();
			}
		}
		
		//split out related contacts and store them seperatly.
		$q->setDelete('project_contacts');
		$q->addWhere('project_id=' . $this->project_id);
		$q->exec();
		$q->clear();
		if ($this->project_contacts) {
			$contacts = explode(',',$this->project_contacts);
			foreach($contacts as $contact) {
				if ($contact) {
					$q->addTable('project_contacts');
					$q->addInsert('project_id', $this->project_id);
					$q->addInsert('contact_id', $contact);
					$q->exec();
					$q->clear();
				}
			}
		}
		
		return ((!$ret) ? (get_class($this) . '::store failed <br />' . db_error()) : NULL);
	}
}

/* The next lines of code have resided in projects/index.php before 
** and have been moved into this 'encapsulated' function
** for reusability of that central code.
**
** @date 20060225
** @responsible gregorerhardt
**
** E.g. this code is used as well in a tab for the admin/viewuser site
**
** @mixed user_id 	userId as filter for tasks/projects that are shown, if nothing is specified, 
			current viewing user $AppUI->user_id is used.
*/

function projects_list_data($user_id = false) {
	global $AppUI, $addPwOiD, $buffer, $company, $company_id, $company_prefix, $deny, $department;
	global $dept_ids, $dPconfig, $orderby, $orderdir, $projects, $tasks_critical, $tasks_problems;
	global $tasks_sum, $tasks_summy, $tasks_total, $owner, $projectTypeId, $project_status;
	global $currentTabId;
	$addProjectsWithAssignedTasks = (($AppUI->getState('addProjWithTasks')) 
	                                 ? $AppUI->getState('addProjWithTasks') : 0);
	
	// get any records denied from viewing
	$obj_project = new CProject();
	$deny = $obj_project->getDeniedRecords($AppUI->user_id);
	
	
	// Let's delete temproary tables
	$q  = new DBQuery;
	$q->dropTemp('tasks_sum, tasks_total, tasks_summy, tasks_critical, tasks_problems' 
	             . ', tasks_users');
	$q->exec();
	$q->clear();

	// Task sum table
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003

	$working_hours = ($dPconfig['daily_working_hours']?$dPconfig['daily_working_hours']:8);

	// GJB: Note that we have to special case duration type 24 
	// and this refers to the hours in a day, NOT 24 hours
	$q->createTemp('tasks_sum');
	$q->addTable('tasks', 't');
	$q->addQuery('t.task_project, SUM(t.task_duration * t.task_percent_complete' 
	             . ' * IF(t.task_duration_type = 24, ' . $working_hours 
	             . ', t.task_duration_type)) / SUM(t.task_duration' 
	             . ' * IF(t.task_duration_type = 24, ' . $working_hours 
	             . ', t.task_duration_type)) AS project_percent_complete, SUM(t.task_duration' 
	             . ' * IF(t.task_duration_type = 24, ' . $working_hours 
	             . ', t.task_duration_type)) AS project_duration');
	if ($user_id) {
		$q->addJoin('user_tasks', 'ut', 'ut.task_id = t.task_id');
		$q->addWhere('ut.user_id = ' . $user_id);
	}
	if (count($deny)) {
		$q->addWhere('NOT (t.task_project IN (' . implode (',', $deny) . '))');
	}
	$q->addWhere('t.task_id = t.task_parent');
	
	$q->addGroup('t.task_project');
	$tasks_sum = $q->exec();
	$q->clear();
	
	// At this stage tasks_sum contains the project id, and the total of tasks as percentage complate and project duration.
	// I.e. one record per project
    
	// Task total table
	$q->createTemp('tasks_total');
	$q->addTable('tasks', 't');
	$q->addQuery('t.task_project, COUNT(distinct t.task_id) AS total_tasks');
	if ($user_id) {
		$q->addJoin('user_tasks', 'ut', 'ut.task_id = t.task_id');
		$q->addWhere('ut.user_id = ' . $user_id);
	}
	if (count($deny)) {
		$q->addWhere('NOT (t.task_project IN (' . implode (',', $deny) . '))');
	}
	$q->addGroup('t.task_project');
	$tasks_total = $q->exec();
	$q->clear();

	// tasks_total contains the total number of tasks for each project.
    
	// temporary My Tasks
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003
	$q->createTemp('tasks_summy');
	$q->addTable('tasks', 't');
	$q->addQuery('t.task_project, COUNT(DISTINCT t.task_id) AS my_tasks');
	$q->addWhere('t.task_owner = ' . (($user_id) ? $user_id : $AppUI->user_id));
	if (count($deny)) {
		$q->addWhere('NOT (task_project IN (' . implode (',', $deny) . '))');
	}
	$q->addGroup('t.task_project');
	$tasks_summy = $q->exec();
	$q->clear();
	
	// tasks_summy contains total count of tasks for each project that I own.

	// temporary critical tasks
	$q->createTemp('tasks_critical');
	$q->addTable('tasks', 't');
	$q->addQuery('t.task_project, t.task_id AS critical_task' 
	             . ', MAX(t.task_end_date) AS project_actual_end_date');
	// MerlinYoda: we don't join tables if we don't get anything out of the process
	// $q->addJoin('projects', 'p', 'p.project_id = t.task_project');
	if (count($deny)) {
		$q->addWhere('NOT (t.task_project IN (' . implode (',', $deny) . '))');
	}
	$q->addOrder('t.task_end_date DESC');
	$q->addGroup('t.task_project');
	$tasks_critical = $q->exec();
	$q->clear();

	// tasks_critical contains the latest ending task and its end date.
	
	// temporary task problem logs
	$q->createTemp('tasks_problems');
	$q->addTable('tasks', 't');
	$q->addQuery('t.task_project, tl.task_log_problem');
	$q->addJoin('task_log', 'tl', 'tl.task_log_task = t.task_id');
	$q->addWhere("tl.task_log_problem > '0'");
	$q->addGroup('t.task_project');
	$tasks_problems = $q->exec();
	$q->clear();

	// tasks_problems contains an indication of any projects that have task logs set to problem.
	
	if ($addProjectsWithAssignedTasks) {
		// temporary users tasks
		$q->createTemp('tasks_users');
		$q->addTable('tasks', 't');
		$q->addQuery('t.task_project');
		$q->addQuery('ut.user_id');
		$q->addJoin('user_tasks', 'ut', 'ut.task_id = t.task_id');
		if ($user_id) {
			$q->addWhere('ut.user_id = ' . $user_id);
		}
		$q->addOrder('t.task_end_date DESC');
		$q->addGroup('t.task_project');
		$tasks_users = $q->exec();
		$q->clear();
	}

	// tasks_users contains all projects with tasks that have user assignments. (isn't this getting pointless?)
	
	// add Projects where the Project Owner is in the given department
	if ($addPwOiD && isset($department)) {
		$owner_ids = array();
		$q->addTable('users', 'u');
		$q->addQuery('u.user_id');
		$q->addJoin('contacts', 'c', 'c.contact_id = u.user_contact');
		$q->addWhere('c.contact_department = ' . $department);
		$owner_ids = $q->loadColumn();	
		$q->clear();
	}

	if (isset($department)) {
		/*
		 * If a department is specified, we want to display projects from the department 
		 * and all departments under that, so we need to build that list of departments 
		 */
		$dept_ids = array();
		$q->addTable('departments');
		$q->addQuery('dept_id, dept_parent');
		$q->addOrder('dept_parent,dept_name');
		$rows = $q->loadList();
		addDeptId($rows, $department);
		$dept_ids[] = $department;
	}
	$q->clear();
	
	
	
	$q->addTable('projects', 'p');
	$q->addQuery('p.project_id, p.project_status, p.project_color_identifier, p.project_type' 
	             . ', p.project_name, p.project_description, p.project_start_date' 
	             . ', p.project_end_date, p.project_color_identifier, p.project_company' 
	             . ', p.project_status, p.project_priority, com.company_name' 
	             . ', com.company_description, tc.critical_task, tc.project_actual_end_date' 
	             . ', tp.task_log_problem, tt.total_tasks, tsy.my_tasks' 
	             . ', ts.project_percent_complete, ts.project_duration, u.user_username');
	$q->addJoin('companies', 'com', 'p.project_company = com.company_id');
	$q->addJoin('users', 'u', 'p.project_owner = u.user_id');
	$q->addJoin('tasks_critical', 'tc', 'p.project_id = tc.task_project');
	$q->addJoin('tasks_problems', 'tp', 'p.project_id = tp.task_project');
	$q->addJoin('tasks_sum', 'ts', 'p.project_id = ts.task_project');
	$q->addJoin('tasks_total', 'tt', 'p.project_id = tt.task_project');
	$q->addJoin('tasks_summy', 'tsy', 'p.project_id = tsy.task_project');	
	if ($addProjectsWithAssignedTasks) {
		$q->addJoin('tasks_users', 'tu', 'p.project_id = tu.task_project');
	}
	if (isset($project_status) && $currentTabId != 500) {
		$q->addWhere('p.project_status = '.$project_status);
	}
	if (isset($department)) {
		$q->addJoin('project_departments', 'pd', 'pd.project_id = p.project_id');
		if (!$addPwOiD) {
			$q->addWhere('pd.department_id in (' . implode(',',$dept_ids) . ')');
		}
	} else if ($company_id &&!$addPwOiD) {
		$q->addWhere('p.project_company = ' . $company_id);
	}
	
	if ($projectTypeId > -1) {
		$q->addWhere('p.project_type = ' . $projectTypeId);
	}
	
	if ($user_id && $addProjectsWithAssignedTasks) {
		$q->addWhere('(tu.user_id = ' . $user_id . ' OR p.project_owner = ' . $user_id . ')');
	} else if ($user_id) {
		$q->addWhere('p.project_owner = ' . $user_id);
	}
	
	if ($owner > 0) {
		$q->addWhere('p.project_owner = ' . $owner);
	}
	
	// Show Projects where the Project Owner is in the given department
	if ($addPwOiD && !empty($owner_ids)) {
		$q->addWhere('p.project_owner IN (' . implode(',', $owner_ids) . ')');
	}
	
	$q->addGroup('p.project_id');
	$q->addOrder($orderby . ' ' . $orderdir);
	$obj_project->setAllowedSQL($AppUI->user_id, $q, null, 'p');
	$projects = $q->loadList();
	
	
	
	// retrieve list of records
	// modified for speed
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003
	// get the list of permitted companies
	$obj_company = new CCompany();
	$companies = $obj_company->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 
	                                             'company_name');
	if(count($companies) == 0) { 
		$companies = array(0);
	}
	
	// get the list of permitted companies
	$companies = arrayMerge(array('0' => $AppUI->_('All')), $companies);
	
	//get list of all departments, filtered by the list of permitted companies.
	$q->clear();
	$q->addTable('companies', 'c');
	$q->addQuery('c.company_id, c.company_name, dep.*');
	$q->addJoin('departments', 'dep', 'c.company_id = dep.dept_company');
	$q->addOrder('c.company_name, dep.dept_parent, dep.dept_name');
	$obj_company->setAllowedSQL($AppUI->user_id, $q);
	$rows = $q->loadList();
	
	//display the select list
	$buffer = '<select name="department" onChange="document.pickCompany.submit()" class="text">';
	$buffer .= ('<option value="company_0" style="font-weight:bold;">' . $AppUI->_('All') 
	            . '</option>'."\n");
	$company = '';
	foreach ($rows as $row) {
		if ($row['dept_parent'] == 0) {
			if($company != $row['company_id']) {
				$buffer .= ('<option value="' . $company_prefix . $row['company_id'] 
							. '" style="font-weight:bold;"' 
							. (($company_id == $row['company_id']) ? 'selected="selected"' : '') 
							. '>' . $row['company_name'] . '</option>' . "\n");
				$company = $row['company_id'];
			}
			if ($row['dept_parent'] != null) {
				showchilddept($row);
				findchilddept($rows, $row['dept_id']);
			}
		}
	}
	$buffer .= '</select>';
	
}
?>

<?php /* PROJECTS $Id$ */
/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision$
*/

require_once( $AppUI->getSystemClass ('dp' ) );
require_once( $AppUI->getLibraryClass( 'PEAR/Date' ) );
require_once( $AppUI->getModuleClass( 'tasks' ) );
require_once( $AppUI->getModuleClass( 'companies' ) );

/**
 * The Project Class
 */
class CProject extends CDpObject {
	var $project_id = NULL;
	var $project_company = NULL;
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
	var $project_active = NULL;
	var $project_private = NULL;
	var $project_departments= NULL;
	var $project_contacts = NULL;
	var $project_priority = NULL;
	var $project_type = NULL;

	function CProject() {
		$this->CDpObject( 'projects', 'project_id' );
	}

	function check() {
	// ensure changes of state in checkboxes is captured
		$this->project_active = intval( $this->project_active );
		$this->project_private = intval( $this->project_private );

		return NULL; // object is ok
	}

        function load($oid=null , $strip = true) {
                $result = parent::load($oid, $strip);
                if ($result && $oid)
                {
			$q = new DBQuery;
			$q->addTable('projects');
			$q->addQuery('SUM(t1.task_duration*t1.task_duration_type*t1.task_percent_complete) / 
                                        SUM(t1.task_duration*t1.task_duration_type) 
                                        AS project_percent_complete');
			$q->addJoin('tasks', 't1', 'projects.project_id = t1.task_project');
			$q->addWhere(" project_id = $oid");
                        $this->project_percent_complete = $q->loadResult();
                }
                return $result;
        }
// overload canDelete
	function canDelete( &$msg, $oid=null ) {
		// TODO: check if user permissions are considered when deleting a project
		global $AppUI;
		$perms =& $AppUI->acl();

		return $perms->checkModuleItem('projects', 'delete', $oid);
		
		// NOTE: I uncommented the dependencies check since it is
		// very anoying having to delete all tasks before being able
		// to delete a project.
		
		/*		
		$tables[] = array( 'label' => 'Tasks', 'name' => 'tasks', 'idfield' => 'task_id', 'joinfield' => 'task_project' );
		// call the parent class method to assign the oid
		return CDpObject::canDelete( $msg, $oid, $tables );
		*/
	}

	function delete() {
                $this->load($this->project_id);
		addHistory('projects', $this->project_id, 'delete', $this->project_name, $this->project_id);
		$q = new DBQuery;
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere("task_project = $this->project_id");
		$sql = $q->prepare();
		$q->clear();
		$tasks_to_delete = db_loadColumn ( $sql );
		foreach ( $tasks_to_delete as $task_id ) {
			$q->setDelete('user_tasks');
			$q->addWhere('task_id ='.$task_id);
			$q->exec();
			$q->clear();
			$q->setDelete('task_dependencies');
			$q->addWhere('dependencies_req_task_id ='.$task_id);
			$q->exec();
			$q->clear();
		}
		$q->setDelete('tasks');
		$q->addWhere('task_project ='.$this->project_id);
		$q->exec();
		$q->clear();

		// remove the project-contacts and project-departments map
		$q->setDelete('project_contacts');
		$q->addWhere('project_id ='.$this->project_id);
		$q->exec();
		$q->clear();
		$q->setDelete('project_departments');
		$q->addWhere('project_id ='.$this->project_id);
		$q->exec();
		$q->clear();
		$q->setDelete('projects');
		$q->addWhere('project_id ='.$this->project_id);
		
                if (!$q->exec()) {
			$result = db_error();
		} else {
			$result =  NULL;
		}
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
		$origProject->load ($from_project_id);
		$q = new DBQuery;
		$q->addTable('tasks');
		$q->addQuery('task_id');
		$q->addWhere('task_project ='.$from_project_id);
		$sql = $q->prepare();
		$q->clear();
		$tasks = array_flip(db_loadColumn ($sql));

		$origDate = new CDate( $origProject->project_start_date );

		$destDate = new CDate ($this->project_start_date);

		$timeOffset = $destDate->getTime() - $origDate->getTime();

		
		// Dependencies array
		$deps = array();
		
		// Copy each task into this project and get their deps
		foreach ($tasks as $orig => $void) {
			$objTask = new CTask();
			$objTask->load ($orig);
			$destTask = $objTask->copy($this->project_id);
			$tasks[$orig] = $destTask;
			$deps[$orig] = $objTask->getDependencies ();
		}

		// Fix record integrity 
		foreach ($tasks as $old_id => $newTask) {

			// Fix parent Task
			// This task had a parent task, adjust it to new parent task_id
			if ($newTask->task_id != $newTask->task_parent)
				$newTask->task_parent = $tasks[$newTask->task_parent]->task_id;

			// Fix task start date from project start date offset
			$origDate->setDate ($newTask->task_start_date);
			$destDate->setDate ($origDate->getTime() + $timeOffset , DATE_FORMAT_UNIXTIME ); 
			$destDate = $destDate->next_working_day( );
			$newTask->task_start_date = $destDate->format(FMT_DATETIME_MYSQL);   
			
			// Fix task end date from start date + work duration
			//$newTask->calc_task_end_date();
			if (!empty($newTask->task_end_date) && $newTask->task_end_date != '0000-00-00 00:00:00')
			{
				$origDate->setDate ($newTask->task_end_date);
				$destDate->setDate ($origDate->getTime() + $timeOffset , DATE_FORMAT_UNIXTIME ); 
				$destDate = $destDate->next_working_day();
				$newTask->task_end_date = $destDate->format(FMT_DATETIME_MYSQL);
			}
			
			// Dependencies
			if (!empty($deps[$old_id])) {
				$oldDeps = explode (',', $deps[$old_id]);
				// New dependencies array
				$newDeps = array();
				foreach ($oldDeps as $dep) 
					$newDeps[] = $tasks[$dep]->task_id;
					
				// Update the new task dependencies
				$csList = implode (',', $newDeps);
				$newTask->updateDependencies ($csList);
			} // end of update dependencies 

			$newTask->store();

		} // end Fix record integrity	

			
	} // end of importTasks

	/**
	**	Overload of the dpObject::getAllowedRecords 
	**	to ensure that the allowed projects are owned by allowed companies.
	**
	**	@author	handco <handco@sourceforge.net>
	**	@see	dpObject::getAllowedRecords
	**/

	function getAllowedRecords( $uid, $fields='*', $orderby='', $index=null, $extra=null ){
		$oCpy = new CCompany ();
		
		$aCpies = $oCpy->getAllowedRecords ($uid, "company_id, company_name");
		if (count($aCpies)) {
		  $buffer = '(project_company IN (' . 
				  implode(',' , array_keys($aCpies)) . 
				  '))'; 

		  if ($extra['where'] != "") 
			  $extra['where'] = $extra['where'] . ' AND ' . $buffer;
		  else
			  $extra['where'] = $buffer; 
		} else {
		  // There are no allowed companies, so don't allow projects.
		  if ($extra['where'] != '')
		    $extra['where'] = $extra['where'] . ' AND 1 = 0 ';
		  else
		    $extra['where'] = '1 = 0';
		}

		return parent::getAllowedRecords ($uid, $fields, $orderby, $index, $extra);
				
	}
	
	function getAllowedSQL($uid, $index = null) {
		$oCpy = new CCompany ();
		
		$where = $oCpy->getAllowedSQL ($uid, "project_company");
		$project_where = parent::getAllowedSQL($uid, $index);
		return array_merge($where, $project_where);
	}

	function setAllowedSQL($uid, &$query, $index = null) {
		$oCpy = new CCompany;
		parent::setAllowedSQL($uid, $query, $index);
		$oCpy->setAllowedSQL($uid, $query, "project_company");
	}
	
	/**
	 *	Overload of the dpObject::getDeniedRecords 
	 *	to ensure that the projects owned by denied companies are denied.
	 *
	 *	@author	handco <handco@sourceforge.net>
	 *	@see	dpObject::getAllowedRecords
	 */
	function getDeniedRecords( $uid ) {
		$aBuf1 = parent::getDeniedRecords ($uid);
		
		$oCpy = new CCompany ();
		// Retrieve which projects are allowed due to the company rules 
		$aCpiesAllowed = $oCpy->getAllowedRecords ($uid, "company_id,company_name");
		
		$q = new DBQuery;
		$q->addTable('projects');
		$q->addQuery('project_id');
		If (count($aCpiesAllowed))
			$q->addWhere("NOT (project_company IN (" . implode (',', array_keys($aCpiesAllowed)) . '))');
		$sql = $q->prepare();
		$q->clear();
		$aBuf2 = db_loadColumn ($sql);
		
		return array_merge ($aBuf1, $aBuf2); 
		
	}

        /** Retrieve tasks with latest task_end_dates within given project
        * @param int Project_id
        * @param int SQL-limit to limit the number of returned tasks
        * @return array List of criticalTasks
        */
        function getCriticalTasks($project_id = NULL, $limit = 1) {
                $project_id = !empty($project_id) ? $project_id : $this->project_id;
		$q = new DBQuery;
		$q->addTable('tasks');
		$q->addWhere("task_project = $project_id AND !isnull( task_end_date ) AND task_end_date !=  '0000-00-00 00:00:00'");
		$q->addOrder('task_end_date DESC');
		$q->setLimit($limit);

                return $q->loadList();
        }

	function store() {
        
        $this->dPTrimAll();
        
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed - $msg";
		}

		if( $this->project_id ) {
			$ret = db_updateObject( 'projects', $this, 'project_id', false );
        		addHistory('projects', $this->project_id, 'update', $this->project_name, $this->project_id);
		} else {
			$ret = db_insertObject( 'projects', $this, 'project_id' );
		        addHistory('projects', $this->project_id, 'add', $this->project_name, $this->project_id);
		}
		
		//split out related departments and store them seperatly.
		$q = new DBQuery;
		$q->setDelete('project_departments');
		$q->addWhere('project_id='.$this->project_id);
		$q->exec();
		$q->clear();
                if ($this->project_departments)
                {
        		$departments = explode(',',$this->project_departments);
        		foreach($departments as $department){
				$q->addTable('project_departments');
				$q->addInsert('project_id', $this->project_id);
				$q->addInsert('department_id', $department);
				$q->exec();
				$q->clear();
        		}
                }
		
		//split out related contacts and store them seperatly.
		$q->setDelete('project_contacts');
		$q->addWhere('project_id='.$this->project_id);
		$q->exec();
		$q->clear();
                if ($this->project_contacts)
                {
        		$contacts = explode(',',$this->project_contacts);
        		foreach($contacts as $contact){
							if ($contact) {
								$q->addTable('project_contacts');
								$q->addInsert('project_id', $this->project_id);
								$q->addInsert('contact_id', $contact);
								$q->exec();
								$q->clear();
							}
        		}
                }

		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		} else {
			return NULL;
		}

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
	global $AppUI, $buffer, $company, $company_id, $company_prefix, $deny, $department, $dept_ids, $dPconfig, $orderby, $orderdir, $projects, $tasks_critical, $tasks_problems, $tasks_sum, $tasks_summy;

	$addProjectsWithAssignedTasks = $AppUI->getState( 'addProjWithTasks' ) ? $AppUI->getState( 'addProjWithTasks' ) : 0;

	// get any records denied from viewing
	$obj = new CProject();
	$deny = $obj->getDeniedRecords( $AppUI->user_id );

	// Let's delete temproary tables
	$q  = new DBQuery;
	$q->dropTemp('tasks_sum, tasks_summy, tasks_critical, tasks_problems, tasks_users');
	$q->exec();
	$q->clear();

	// Task sum table
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003

	$working_hours = $dPconfig['daily_working_hours'];

	// GJB: Note that we have to special case duration type 24 and this refers to the hours in a day, NOT 24 hours
	$q->createTemp('tasks_sum');
	$q->addTable('tasks');
	$q->addQuery("task_project, COUNT(distinct tasks.task_id) AS total_tasks, 
			SUM(task_duration * task_percent_complete * IF(task_duration_type = 24, ".$working_hours.", task_duration_type))/
			SUM(task_duration * IF(task_duration_type = 24, ".$working_hours.", task_duration_type)) AS project_percent_complete, SUM(task_duration * IF(task_duration_type = 24, ".$working_hours.", task_duration_type)) AS project_duration");
	if ($user_id) {
		$q->addJoin('user_tasks', 'ut', 'ut.task_id = tasks.task_id');
		$q->addWhere('ut.user_id = '.$user_id);
	}
	$q->addGroup('task_project');
	$tasks_sum = $q->exec();
	$q->clear();

	// temporary My Tasks
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003
	$q->createTemp('tasks_summy');
	$q->addTable('tasks');
	$q->addQuery('task_project, COUNT(distinct task_id) AS my_tasks');
	if ($user_id) {
		$q->addWhere('task_owner = '.$user_id);
	} else {
		$q->addWhere('task_owner = '.$AppUI->user_id);
	}
	$q->addGroup('task_project');
	$tasks_summy = $q->exec();
	$q->clear();

	// temporary critical tasks
	$q->createTemp('tasks_critical');
	$q->addTable('tasks');
	$q->addQuery('task_project, task_id AS critical_task, MAX(task_end_date) AS project_actual_end_date');
	$q->addJoin('projects', 'p', 'p.project_id = task_project');
	$q->addOrder("task_end_date DESC");
	$q->addGroup('task_project');
	$tasks_critical = $q->exec();
	$q->clear();

	// temporary task problem logs
	$q->createTemp('tasks_problems');
	$q->addTable('tasks');
	$q->addQuery('task_project, task_log_problem');
	$q->addJoin('task_log', 'tl', 'tl.task_log_task = task_id');
	$q->addWhere("task_log_problem > '0'");
	$q->addGroup('task_project');
	$tasks_problems = $q->exec();
	$q->clear();

	if ($addProjectsWithAssignedTasks) {
	// temporary users tasks
	$q->createTemp('tasks_users');
	$q->addTable('tasks');
	$q->addQuery('task_project');
	$q->addQuery('ut.user_id');
	$q->addJoin('user_tasks', 'ut', 'ut.task_id = tasks.task_id');
	if ($user_id) {
		$q->addWhere('ut.user_id = '.$user_id);
	}
	$q->addOrder("task_end_date DESC");
	$q->addGroup('task_project');
	$tasks_users = $q->exec();
	$q->clear();
	}

	if(isset($department)){
		//If a department is specified, we want to display projects from the department, and all departments under that, so we need to build that list of departments
		$dept_ids = array();
		$q->addTable('departments');
		$q->addQuery('dept_id, dept_parent');
		$q->addOrder('dept_parent,dept_name');
		$rows = $q->loadList();
		addDeptId($rows, $department);
		$dept_ids[] = $department;
	}
	$q->clear();

	// retrieve list of records
	// modified for speed
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003
	// get the list of permitted companies
	$obj = new CCompany();
	$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
	if(count($companies) == 0) $companies = array(0);


	$q->addTable('projects');
	$q->addQuery('projects.project_id, project_active, project_status, project_color_identifier, project_name, project_description, project_duration,
		project_start_date, project_end_date, project_color_identifier, project_company, company_name, company_description, project_status,
		project_priority, tc.critical_task, tc.project_actual_end_date, tp.task_log_problem, ts.total_tasks, tsy.my_tasks,
		ts.project_percent_complete, user_username');
	$q->addJoin('companies', 'com', 'projects.project_company = company_id');
	$q->addJoin('users', 'u', 'projects.project_owner = u.user_id');
	$q->addJoin('tasks_critical', 'tc', 'projects.project_id = tc.task_project');
	$q->addJoin('tasks_problems', 'tp', 'projects.project_id = tp.task_project');
	$q->addJoin('tasks_sum', 'ts', 'projects.project_id = ts.task_project');
	$q->addJoin('tasks_summy', 'tsy', 'projects.project_id = tsy.task_project');
	if ($addProjectsWithAssignedTasks)
		$q->addJoin('tasks_users', 'tu', 'projects.project_id = tu.task_project');
	// DO we have to include the above DENY WHERE restriction, too?
	//$q->addJoin('', '', '');
	if (isset($department)) {
		$q->addJoin('project_departments', 'pd', 'pd.project_id = projects.project_id');
	}
	if (!isset($department) && $company_id) {
		$q->addWhere("projects.project_company = '$company_id'");
	}
	if (isset($department)) {
		$q->addWhere("pd.department_id in ( ".implode(',',$dept_ids)." )");
	}
	if ($user_id && $addProjectsWithAssignedTasks) {
		$q->addWhere('(tu.user_id = '.$user_id.' OR projects.project_owner = '.$user_id.' )');
	} elseif ($user_id) {
		$q->addWhere('projects.project_owner = '.$user_id);
	}
	$q->addGroup('projects.project_id');
	$q->addOrder("$orderby $orderdir");
	$obj->setAllowedSQL($AppUI->user_id, $q);
	$projects = $q->loadList();

	// get the list of permitted companies
	$companies = arrayMerge( array( '0'=>$AppUI->_('All') ), $companies );

	//get list of all departments, filtered by the list of permitted companies.
	$q->clear();
	$q->addTable('companies');
	$q->addQuery('company_id, company_name, dep.*');
	$q->addJoin('departments', 'dep', 'companies.company_id = dep.dept_company');
	$q->addOrder('company_name,dept_parent,dept_name');
	$obj->setAllowedSQL($AppUI->user_id, $q);
	$rows = $q->loadList();

	//display the select list
	$buffer = '<select name="department" onChange="document.pickCompany.submit()" class="text">';
	$buffer .= '<option value="company_0" style="font-weight:bold;">'.$AppUI->_('All').'</option>'."\n";
	$company = '';
	foreach ($rows as $row) {
		if ($row["dept_parent"] == 0) {
			if($company!=$row['company_id']){
				$buffer .= '<option value="'.$company_prefix.$row['company_id'].'" style="font-weight:bold;"'.($company_id==$row['company_id']?'selected="selected"':'').'>'.$row['company_name'].'</option>'."\n";
				$company=$row['company_id'];
			}
			if($row["dept_parent"]!=null){
				showchilddept( $row );
				findchilddept( $rows, $row["dept_id"] );
			}
		}
	}
	$buffer .= '</select>';

}


//writes out a single <option> element for display of departments
function showchilddept( &$a, $level=1 ) {
	Global $buffer, $department;
	$s = '<option value="'.$a["dept_id"].'"'.(isset($department)&&$department==$a["dept_id"]?'selected="selected"':'').'>';

	for ($y=0; $y < $level; $y++) {
		if ($y+1 == $level) {
			$s .= '';
		} else {
			$s .= '&nbsp;&nbsp;';
		}
	}

	$s .= '&nbsp;&nbsp;'.$a["dept_name"]."</option>\n";
	$buffer .= $s;

//	echo $s;
}

//recursive function to display children departments.
function findchilddept( &$tarr, $parent, $level=1 ){
	$level = $level+1;
	$n = count( $tarr );
	for ($x=0; $x < $n; $x++) {
		if($tarr[$x]["dept_parent"] == $parent && $tarr[$x]["dept_parent"] != $tarr[$x]["dept_id"]){
			showchilddept( $tarr[$x], $level );
			findchilddept( $tarr, $tarr[$x]["dept_id"], $level);
		}
	}
}

function addDeptId($dataset, $parent){
	Global $dept_ids;
	foreach ($dataset as $data){
		if($data['dept_parent']==$parent){
			$dept_ids[] = $data['dept_id'];
			addDeptId($dataset, $data['dept_id']);
		}
	}
}
?>

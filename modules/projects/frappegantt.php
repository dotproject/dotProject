<?php
require_once DP_BASE_DIR . '/classes/query.class.php';
require_once DP_BASE_DIR . '/modules/projects/projects.class.php';


/**
 * Frappe Gantt renderer for dotProject
 * 
 * @author Matt Bell (2Pi Software) 2019
 */
class Gantt {
    const ListProjects = 1;
    const ListProjectTasks = 2;
    private static $headerWritten = false;
    private $taskClickURL = "";
    private $viewID = null;


    /**
     * Create a gantt for projects
     * 
     * @return Gantt
     */
    public static function Projects() {
        return new Gantt(Gantt::ListProjects);
    }

    /**
     * Create a gantt for tasks within the specified project
     * 
     * @param int $projectid ID of the project
     * @return Gantt
     */
    public static function ProjectTasks($projectid) {
        return new Gantt(Gantt::ListProjectTasks, ["projectid"=>$projectid]);
    }

    /**
     * Write the header required to display the gantt chart
     */
    public static function WriteHeader() {
        if (!Gantt::$headerWritten) {
            echo '<link rel="stylesheet" href="lib/frappe-gantt/frappe-gantt.css">
            <script src="lib/frappe-gantt/frappe-gantt.js"></script>';
            Gantt::$headerWritten = true;
        }
    }

    /**
     * Create a Gantt Chart
     */
    private function __construct($type, $params = []) {
        $this->getFilters();
        switch ($type) {
            case Gantt::ListProjects:
                $this->getProjects();
                $this->taskClickURL = "index.php?m=projects&a=view&project_id=%id%";
                $this->viewID = 'projects';
            break;
            case Gantt::ListProjectTasks:
                $this->getProjectTasks($params["projectid"]);
                $this->taskClickURL = "index.php?m=tasks&a=view&task_id=%id%";
                $this->viewID = 'projectTasks'.$params["projectid"];
            break;
        }
    }

    /**
     * List of tasks to render
     */
    private $tasks = [];

    /**
     * List of filters
     */
    private $filters = [];

    /**
     * Render the gantt chart
     */
    public function render() {
        global $AppUI;
        if (!Gantt::$headerWritten) {
            Gantt::WriteHeader();
        }
        $json = json_encode($this->tasks);
        //render html+json for frappe
        include "frappegantt.view.php";
    }

    /**
     * Get the options chosen from the project filter pane
     */
    private function getFilters() {
        global $AppUI;
        $display_option = dPgetCleanParam($_POST, 'display_option', 'this_month');

        $this->filters = array(
            "project_id" => intval(dPgetParam($_REQUEST, 'project_id'), null),
            "user_id" => intval(dPgetParam($_REQUEST, 'user_id', $AppUI->user_id)),
            "proFilter" => (int)dPgetParam($_REQUEST, 'proFilter', '-1'),
            "company_id" => intval(dPgetParam($_REQUEST, 'company_id', 0)),
            "department" => intval(dPgetParam($_REQUEST, 'department', 0)),
            "showLabels" => (int)dPgetParam($_REQUEST, 'showLabels', 0),
            "showInactive" => (int)dPgetParam($_REQUEST, 'showInactive', 0),
            "sortTasksByName" => (int)dPgetParam($_REQUEST, 'sortTasksByName', 0),
            "addPwOiD" => (int)dPgetParam($_REQUEST, 'addPwOiD', 0),
            "m_orig" => dPgetCleanParam($_REQUEST, 'm_orig', $m),
            "a_orig" => dPgetCleanParam($_REQUEST, 'a_orig', $a),
            "sdate" => 0,
            "edate" => 0 
        );

        if ($display_option === 'this_month') {
            $firstOfMonth = strToTime(date('Ym01'));
            $this->filters = array_merge($this->filters, array(
                "sdate" => date("Ymd", $firstOfMonth),
                "edate" => date("Ymd", strtotime("+1 month", $firstOfMonth))
            ));
            return;
        }

        if ($display_option !== 'all') {
            $this->filters = array_merge($this->filters, array(
                "sdate" => dPgetCleanParam($_REQUEST, 'sdate', 0),
                "edate" => dPgetCleanParam($_REQUEST, 'edate', 0)
            ));
            return;
        }
    }

    /**
     * Get the projects from the db and format them for a gantt chart
     */
    private function getProjects() {
        $q = new DBQuery;
        $pjobj = new CProject;
        global $dPconfig;
        $working_hours = $dPconfig['daily_working_hours'];
        $owner_ids = array();
        if ($this->filters['addPwOiD'] && $this->filters["department"] > 0) {
            $q->addTable('users');
            $q->addQuery('user_id');
            $q->addJoin('contacts', 'c', 'c.contact_id = user_contact');
            $q->addWhere('c.contact_department = '.$this->filters["department"]);
            $owner_ids = $q->loadColumn();	
            $q->clear();
        }
        $q->addTable('projects', 'p');
        $q->addQuery('DISTINCT p.project_id, project_color_identifier, project_name, project_start_date' 
                     . ', project_end_date, max(t1.task_end_date) AS project_actual_end_date' 
                     . ', SUM(task_duration * task_percent_complete * IF(task_duration_type = 24, ' 
                     . $working_hours . ', task_duration_type))' 
                     . ' / SUM(task_duration * IF(task_duration_type = 24, ' 
                     . $working_hours . ', task_duration_type)) AS project_percent_complete' 
                     . ', project_status');
        $q->addJoin('tasks', 't1', 'p.project_id = t1.task_project');
        $q->addJoin('companies', 'c1', 'p.project_company = c1.company_id');
        if ($this->filters["department"] > 0) {
            $q->addJoin('project_departments', 'pd', 'pd.project_id = p.project_id');
            
            if (!$this->filters["addPwOiD"]) {
                $q->addWhere('pd.department_id = ' . $this->filters["department"]);
            } else {
                // Show Projects where the Project Owner is in the given department
                $q->addWhere('p.project_owner IN (' 
                             . ((!empty($owner_ids)) ? implode(',', $owner_ids) : 0) . ')');
            }
        } else if ($this->filters["company_id"] != 0 && !$this->filters["addPwOiD"]) {
            $q->addWhere('project_company = ' . $this->filters["company_id"]);
        }
        
        if ($this->filters["proFilter"] == '-4') {
            $q->addWhere('project_status != 7');
        } else if ($this->filters["proFilter"] == '-3') {
            $q->addWhere('project_owner = ' . $this->filters["user_id"]);
        } else if ($this->filters["proFilter"] == '-2') {
            $q->addWhere('project_status != 3');
        } else if ($this->filters["proFilter"] != '-1') {
            $q->addWhere('project_status = ' . $this->filters["proFilter"]);
        }

        if ($this->filters["sdate"] != 0 && $this->filters["edate"] != 0) {
            $sdate = (new CDate($this->filters["sdate"]))->format(FMT_DATETIME_MYSQL);
            $edate = (new CDate($this->filters["edate"]))->format(FMT_DATETIME_MYSQL);
            $q->addWhere("project_start_date <= '$edate'");
            $q->addWhere("project_end_date >= '$sdate'");
        }
        
        if ($this->filters["user_id"] && $this->filters["m_orig"] == 'admin' && $this->filters["a_orig"] == 'viewuser') {
            $q->addWhere('project_owner = ' . $this->filters["user_id"]);
        }
        
        if ($this->filters["showInactive"] != '1') {
            $q->addWhere('project_status != 7');
        }
        //$pjobj->setAllowedSQL($AppUI->user_id, $q, null, 'p');
        $q->addGroup('p.project_id');
        $q->addOrder('project_name, task_end_date DESC');
        
        $projects = $q->loadList();
        if ($projects === false) {
            return array();
        }
        $q->clear();
        foreach ($projects as $project) {
            if ($project["project_start_date"] == null || $project["project_end_date"] == null) {
                continue;
            }
            array_push($this->tasks, array(
                "id" => $project["project_id"],
                "name" => $project["project_name"],
                "start" => $project["project_start_date"],
                "end" => $project["project_end_date"],
                "progress" => $project["project_percent_complete"]
            ));
        }
    }

    /**
     * Get the project tasks from the db and format them for a gantt chart
     */
    private function getProjectTasks($projectID = null) {
        global $AppUI;
	$f = defVal( @$_REQUEST['f'], 0 );

        $q = new DBQuery;
        $q->addTable('tasks', 't');
        $q->addQuery('t.task_id, task_parent, task_name, task_start_date, task_end_date, task_duration, task_duration_type, task_priority, task_percent_complete, task_order, task_project, task_milestone, project_name, task_dynamic');
        $q->addJoin('projects', 'p', 'project_id = t.task_project');
        $q->addWhere('project_status != 7');
        if ($projectID == null) {
            $projectID = $this->filters["project_id"];
        }
        if ($projectID != null) {
            $q->addWhere("task_project = $projectID");
        }
        if ($this->filters["sdate"] != 0 && $this->filters["edate"] != 0) {
            $sdate = (new CDate($this->filters["sdate"]))->format(FMT_DATETIME_MYSQL);
            $edate = (new CDate($this->filters["edate"]))->format(FMT_DATETIME_MYSQL);
            $q->addWhere("task_start_date <= '$edate'");
            $q->addWhere("task_end_date >= '$sdate'");
        }

        switch ($f) {
            case 'all':
                    $q->addWhere('task_status > -1');
                    break;
            case 'myproj':
                    $q->addWhere('task_status > -1');
                    $q->addWhere('project_owner = '.$AppUI->user_id);
                    break;
            case 'mycomp':
                    $q->addWhere('task_status > -1');
                    $q->addWhere('project_company = '.$AppUI->user_company);
                    break;
            case 'myinact':
                    $q->addTable('user_tasks', 'ut');
                    $q->addWhere('task_project = p.project_id');
                    $q->addWhere('ut.user_id = '.$AppUI->user_id);
                    $q->addWhere('ut.task_id = t.task_id');
                    break;
            default:
                    $q->addTable('user_tasks', 'ut');
                    $q->addWhere('task_status > -1');
                    $q->addWhere('task_project = p.project_id');
                    $q->addWhere('ut.user_id = '.$AppUI->user_id);
                    $q->addWhere('ut.task_id = t.task_id');
                    break;
        }

        $q->addOrder('project_id, task_start_date');

        $task = new CTask;
        $task->setAllowedSQL($AppUI->user_id, $q);

        $proTasks = $q->loadHashList('task_id');
	$q->clear();

        foreach ($proTasks as $task) {
            if ($task["task_start_date"] == null || $task["task_end_date"] == null) {
                continue;
            }

	    $q->addTable('task_dependencies', 'td');
	    $q->addQuery('td.dependencies_req_task_id');
	    $q->addWhere('td.dependencies_task_id = ' . $task['task_id']);
	    $dependency = $q->loadResult();
	    $q->clear();

            array_push($this->tasks, array(
                "id" => $task["task_id"],
                "name" => $task["task_name"],
                "start" => $task["task_start_date"],
                "end" => $task["task_end_date"],
                "progress" => $task["task_percent_complete"],
		"dependencies" => strval($dependency)
            ));
        }
    }
}


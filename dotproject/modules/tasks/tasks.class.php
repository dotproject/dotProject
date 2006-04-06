<?php /* TASKS $Id$ */

require_once( $AppUI->getSystemClass( 'libmail' ) );
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getModuleClass( 'projects' ) );

// user based access
$task_access = array(
        '0'=>'Public',
        '1'=>'Protected',
        '2'=>'Participant',
        '3'=>'Private'
);

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
$tracked_dynamics = array(
        '0' => '0',
        '1' => '1',
        '2' => '31'
);
// Tasks with these dynamics have their dates updated when
// one of their dependencies changes. (They track dependencies)
$tracking_dynamics = array(
        '0' => '21',
        '1' => '31'
);

/*
* CTask Class
*/
class CTask extends CDpObject {
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
        var $task_type   = NULL;


        function CTask() {
                $this->CDpObject( 'tasks', 'task_id' );
        }

// overload check
        function check() {
                global $AppUI;

                if ($this->task_id === NULL)
                        return 'task id is NULL';

        // ensure changes to checkboxes are honoured
                $this->task_milestone = intval( $this->task_milestone );
                $this->task_dynamic   = intval( $this->task_dynamic );

                $this->task_percent_complete = intval( $this->task_percent_complete );

                if (!$this->task_duration) {
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
                if (!isset($addedit))
                        $addedit = dPgetParam($_POST, 'dosql', '') == 'do_task_aed' ? true : false;
                $this_dependencies = array();

                /*
                 * If we are called from addedit then we want to use the incoming
                 * list of dependencies and attempt to stop bad deps from being created
                 */
                if ($addedit) {
                        $hdependencies = dPgetParam($_POST, 'hdependencies', '0');
                        if ($hdependencies)
                                $this_dependencies = explode(',', $hdependencies);
                } else {
                        $this_dependencies = explode(',', $this->getDependencies());
                }
                // Set to false for recursive updateDynamic calls etc.
                $addedit = false;

                // Have deps
                if (array_sum($this_dependencies)) {

                        if ( $this->task_dynamic == '1')
                                return 'BadDep_DynNoDep';

                        $this_dependents = $this->task_id ? explode(',', $this->dependentTasks()) : array();
                        $more_dependents = array();
                        // If the dependents' have parents add them to list of dependents
                        foreach ($this_dependents as $dependent) {
                                $dependent_task = new CTask();
                                $dependent_task->load($dependent);
                                if ( $dependent_task->task_id != $dependent_task->task_parent )
                                        $more_dependents = explode(',', $this->dependentTasks($dependent_task->task_parent));
                        }
                        $this_dependents = array_merge($this_dependents, $more_dependents);

                        // Task dependencies can not be dependent on this task
                        $intersect = array_intersect( $this_dependencies, $this_dependents );
                        if (array_sum($intersect)) {
                                $ids = "(".implode(',', $intersect).")";
                                return array('BadDep_CircularDep', $ids);
                        }
                }

                // Has a parent
                if ( $this->task_id && $this->task_id != $this->task_parent ) {
                        $this_children = $this->getChildren();
                        $this_parent = new CTask();
                        $this_parent->load($this->task_parent);
                        $parents_dependents = explode(',', $this_parent->dependentTasks());

                        if (in_array($this_parent->task_id, $this_dependencies))
                                return 'BadDep_CannotDependOnParent';

                        // Task parent cannot be child of this task
                        if (in_array($this_parent->task_id, $this_children))
                                return 'BadParent_CircularParent';

                        if ( $this_parent->task_parent != $this_parent->task_id ) {

                                // ... or parent's parent, cannot be child of this task. Could go on ...
                                if (in_array($this_parent->task_parent, $this_children))
                                        return array('BadParent_CircularGrandParent', '('.$this_parent->task_parent.')');

                                // parent's parent cannot be one of this task's dependencies
                                if (in_array($this_parent->task_parent, $this_dependencies))
                                        return array('BadDep_CircularGrandParent', '('.$this_parent->task_parent.')');

                        } // grand parent

                        if ( $this_parent->task_dynamic == '1' ) {
                                $intersect = array_intersect( $this_dependencies, $parents_dependents );
                                if (array_sum($intersect)) {
                                        $ids = "(".implode(',', $intersect).")";
                                        return array('BadDep_CircularDepOnParentDependent', $ids);
                                }
                        }

                        if ( $this->task_dynamic == '1' ) {
                                // then task's children can not be dependent on parent
                                $intersect = array_intersect( $this_children, $parents_dependents );
                                if (array_sum($intersect))
                                        return 'BadParent_ChildDepOnParent';
                        }
                } // parent

                return NULL;
        }


/*
 *        overload the load function
 *        We need to update dynamic tasks of type '1' on each load process!
 *        @param int $oid optional argument, if not specifed then the value of current key is used
 *        @return any result from the database operation
*/

        function load($oid=null,$strip=false) {
                // use parent function to load the given object
                $loaded = parent::load($oid,$strip);

                /*
                ** Update the values of a dynamic task from
                ** the children's properties each time the
                ** dynamic task is loaded.
                ** Additionally store the values in the db.
                ** Only treat umbrella tasks of dynamics '1'.
                */
                if ($this->task_dynamic == '1') {
                        // update task from children
                        $this->updateDynamics(true);

                        /*
                        ** Use parent function to store the updated values in the db
                        ** instead of store function of this object in order to
                        ** prevent from infinite loops.
                        */
                        parent::store();
                }

                // return whether the object load process has been successful or not
                return $loaded;
        }


        function updateDynamics( $fromChildren = false ) {
                GLOBAL $dPconfig;
                //Has a parent or children, we will check if it is dynamic so that it's info is updated also

                $modified_task = new CTask();

                if ( $fromChildren ){
                        $modified_task = &$this;
                } else {
                        $modified_task->load($this->task_parent);
                }

                if ( $modified_task->task_dynamic == '1' ) {
                        //Update allocated hours based on children with duration type of 'hours'
                        $sql1 = "SELECT SUM( task_duration * task_duration_type ) from " . $this->_tbl . " WHERE task_parent = " . $modified_task->task_id .
                                        " and task_id <> " . $modified_task->task_id .
                                        " AND task_duration_type = 1 " .
                                        " GROUP BY task_parent;";
                        $children_allocated_hours1 = (float) db_loadResult( $sql1 );

                        //Update allocated hours based on children with duration type of 'days'
                        // use here the daily working hours instead of the full 24 hours to calculate dynamic task duration!
                        $sql2 = "SELECT SUM( task_duration * ".$dPconfig['daily_working_hours']." ) from " . $this->_tbl . " WHERE task_parent = " . $modified_task->task_id .
                                        " and task_id <> " . $modified_task->task_id .
                                        " AND task_duration_type > 1" .
                                        " GROUP BY task_parent;";
                        $children_allocated_hours2 = (float) db_loadResult( $sql2 );

                        // sum up the two distinct duration values for the children with duration type 'hrs'
                        // and for those with the duration type 'day'
                        $children_allocated_hours = $children_allocated_hours1 + $children_allocated_hours2;

                        if ( $modified_task->task_duration_type == 1 ) {
                                $modified_task->task_duration = round($children_allocated_hours,2);
                        } else {
                                $modified_task->task_duration = round($children_allocated_hours / $dPconfig['daily_working_hours'], 2);
                        }

                        //Update worked hours based on children
                        $sql = "SELECT sum( task_log_hours ) FROM tasks, task_log
                                        WHERE task_id = task_log_task AND task_parent = " . $modified_task->task_id .
                                        " AND task_id <> " . $modified_task->task_id .
                                        " AND task_dynamic <> 1";
                        $children_hours_worked = (float) db_loadResult( $sql );


                        //Update worked hours based on dynamic children tasks
                        $sql = "SELECT sum( task_hours_worked ) FROM tasks
                                        WHERE task_dynamic = 1 AND task_parent = " . $modified_task->task_id .
                                        " AND task_id <> " . $modified_task->task_id;
                        $children_hours_worked += (float) db_loadResult( $sql );

                        $modified_task->task_hours_worked = $children_hours_worked;

                        //Update percent complete
                        $sql = "SELECT sum(task_percent_complete * task_duration * task_duration_type )
                                        FROM tasks WHERE task_parent = " . $modified_task->task_id .
                                        " AND task_id <> " . $modified_task->task_id . 
															" AND task_duration_type = 1 ";
                        $real_children_hours_worked = (float) db_loadResult( $sql );

                        $sql = "SELECT sum(task_percent_complete * task_duration *" . dPconfig('daily_working_hours') . ")
                                        FROM tasks WHERE task_parent = " . $modified_task->task_id .
                                        " AND task_id <> " . $modified_task->task_id . 
															" AND task_duration_type != 1 ";
                        $real_children_hours_worked += (float) db_loadResult( $sql );

                        $total_hours_allocated = (float)($modified_task->task_duration * ($modified_task->task_duration_type > 1?dPconfig('daily_working_hours'):1));
                        if($total_hours_allocated > 0){
                            $modified_task->task_percent_complete = $real_children_hours_worked / $total_hours_allocated;
                        } else {
                            $sql = "SELECT avg(task_percent_complete)
                                            FROM tasks WHERE task_parent = " . $modified_task->task_id .
                                            " AND task_id <> " . $modified_task->task_id;
                            $modified_task->task_percent_complete = db_loadResult($sql);
                        }


                        //Update start date
                        $sql = "SELECT min( task_start_date ) FROM tasks
                                        WHERE task_parent = " . $modified_task->task_id .
                                        " AND task_id <> " . $modified_task->task_id .
                                        " AND ! isnull( task_start_date ) AND task_start_date <>  '0000-00-00 00:00:00'";
												$d = db_loadResult( $sql );
												if ($d)
                        	$modified_task->task_start_date = $d;
												else
													$modified_task->task_start_date = '0000-00-00 00:00:00';												$d = db_loadResult( $sql );

                        //Update end date
                        $sql = "SELECT max( task_end_date ) FROM tasks
                                        WHERE task_parent = " . $modified_task->task_id .
                                        " AND task_id <> " . $modified_task->task_id .
                                        " AND ! isnull( task_end_date ) ";
                        $modified_task->task_end_date = db_loadResult( $sql );

                        //If we are updating a dynamic task from its children we don't want to store() it
                        //when the method exists the next line in the store calling function will do that
                        if ( $fromChildren == false ) $modified_task->store();
                }
        }

/**
*        Copy the current task
*
*        @author        handco <handco@users.sourceforge.net>
*        @param        int                id of the destination project
*        @return        object        The new record object or null if error
**/
        function copy($destProject_id = 0, $destTask_id = -1) {
                $newObj = $this->duplicate();

                // Copy this task to another project if it's specified
                if ($destProject_id != 0)
                        $newObj->task_project = $destProject_id;

                if ($destTask_id == 0)
                        $newObj->task_parent = $newObj->task_id;
                else if ($destTask_id > 0)
                        $newObj->task_parent = $destTask_id;
                if ($newObj->task_parent == $this->task_id)
                        $newObj->task_parent = '';

                $newObj->store();

                return $newObj;
        }// end of copy()

        function deepCopy($destProject_id = 0, $destTask_id = 0) {
                $newObj = $this->copy($destProject_id, $destTask_id);
                $new_id = $newObj->task_id;
                $children = $this->getChildren();
                if (!empty($children))
                {
                        $tempTask = & new CTask();
                        foreach ($children as $child)
                        {
                                $tempTask->load($child);
                                $newChild = $tempTask->deepCopy($destProject_id, $new_id);
                                $newChild->store();
                        }
                }

                return $newObj;
        }

        function move($destProject_id = 0, $destTask_id = -1) {
                if ($destProject_id != 0)
                        $this->task_project = $destProject_id;
                if ($destTask_id == 0)
                        $this->task_parent = $this->task_id;
                else if ($destTask_id > 0)
                        $this->task_parent = $destTask_id;
        }

        function deepMove($destProject_id = 0, $destTask_id = 0) {
                $this->move($destProject_id, destTask_id);
                $children = $this->getDeepChildren();
                if (!empty($children))
                {
                        $tempChild = & new CTask();
                        foreach ($children as $child)
                        {
                                $tempChild->load($child);
                                $tempChild->move($destProject_id);
                                $tempChild->store();
                        }
                }
        }
/**
* @todo Parent store could be partially used
*/
        function store() {
                GLOBAL $AppUI;

                $importing_tasks = false;
                $msg = $this->check();
                if( $msg ) {
                        $return_msg = array(get_class($this) . '::store-check',  'failed',  '-');
                        if (is_array($msg))
                                return array_merge($return_msg, $msg);
                        else {
                                array_push($return_msg, $msg);
                                return $return_msg;
                        }
                }
                if( $this->task_id ) {
                        addHistory('tasks', $this->task_id, 'update', $this->task_name, $this->task_project);
                        $this->_action = 'updated';
                        // Load the old task from disk
                        $oTsk = new CTask();
                        $oTsk->load ($this->task_id);

                        // if task_status changed, then update subtasks
                        if ($this->task_status != $oTsk->task_status)
                                $this->updateSubTasksStatus($this->task_status);

                        // Moving this task to another project?
                        if ($this->task_project != $oTsk->task_project)
                                $this->updateSubTasksProject($this->task_project);

                        if ( $this->task_dynamic == '1' )
                                $this->updateDynamics(true);

                        // shiftDependentTasks needs this done first
                        $ret = db_updateObject( 'tasks', $this, 'task_id', false );

                        // Milestone or task end date, or dynamic status has changed,
                        // shift the dates of the tasks that depend on this task
                        if (($this->task_end_date != $oTsk->task_end_date) ||
                            ($this->task_dynamic != $oTsk->task_dynamic)   ||
                            ($this->task_milestone == '1')) {
                                $this->shiftDependentTasks();
                        }
                } else {
                        $this->_action = 'added';
                        $ret = db_insertObject( 'tasks', $this, 'task_id' );
                        addHistory('tasks', $this->task_id, 'add', $this->task_name, $this->task_project);

                        if (!$this->task_parent) {
                                $sql = "UPDATE tasks SET task_parent = $this->task_id WHERE task_id = $this->task_id";
                                db_exec( $sql );
                        } else {
                                // importing tasks do not update dynamics
                                $importing_tasks = true;
                        }

                        // insert entry in user tasks
                        $sql = "INSERT INTO user_tasks (user_id, task_id, user_type) VALUES ($AppUI->user_id, $this->task_id, 0)";
                        db_exec( $sql );
                }

                //split out related departments and store them seperatly.
                $sql = 'DELETE FROM task_departments WHERE task_id='.$this->task_id;
                db_exec( $sql );
                // print_r($this->task_departments);
                if(!empty($this->task_departments)){
                  $departments = explode(',',$this->task_departments);
              foreach($departments as $department){
                       $sql = 'INSERT INTO task_departments (task_id, department_id) values ('.$this->task_id.', '.$department.')';
                       db_exec( $sql );
              }
                }

                //split out related contacts and store them seperatly.
                $sql = 'DELETE FROM task_contacts WHERE task_id='.$this->task_id;
                db_exec( $sql );
                if(!empty($this->task_contacts)){
                    $contacts = explode(',',$this->task_contacts);
                    foreach($contacts as $contact){
                            $sql = 'INSERT INTO task_contacts (task_id, contact_id) values ('.$this->task_id.', '.$contact.')';
                            db_exec( $sql );
                    }
                }

                if ( !$importing_tasks && $this->task_parent != $this->task_id )
                        $this->updateDynamics(true);

                // if is child update parent task
                if ( $this->task_parent != $this->task_id ) {
                             $pTask = new CTask();
                        $pTask->load($this->task_parent);
                        $pTask->updateDynamics();
												
												if ($oTsk->task_parent != $this->task_parent)
												{
													$old_parent = new CTask();
													$old_parent->load($oTsk->task_parent);
													$old_parent->updateDynamics();
												}
                }

                // update dependencies
                if (!empty($this->task_id))
                        $this->updateDependencies($this->getDependencies());
                else
                        print_r($this);

                if( !$ret ) {
                        return get_class( $this )."::store failed <br />" . db_error();
                } else {
                        return NULL;
                }
        }

/**
* @todo Parent store could be partially used
* @todo Can't delete a task with children
*/
        function delete() {
                $this->_action = 'deleted';
        // delete linked user tasks
                $sql = "DELETE FROM user_tasks WHERE task_id = $this->task_id";
                if (!db_exec( $sql )) {
                        return db_error();
                }

                //load it before deleting it because we need info on it to update the parents later on
                $this->load($this->task_id);
                addHistory('tasks', $this->task_id, 'delete', $this->task_name, $this->task_project);

                // delete the tasks...what about orphans?
                // delete task with parent is this task
                $childrenlist = $this->getDeepChildren();

                $sql = "DELETE FROM tasks WHERE task_id = $this->task_id";
                if (!db_exec( $sql )) {
                        return db_error();
                } else {
                        if ( $this->task_parent != $this->task_id ){
                                // Has parent, run the update sequence, this child will no longer be in the
                                // database
                                $this->updateDynamics();
                        }
                }

                // delete children
                $sql = "SELECT * FROM tasks WHERE task_parent = $this->task_id";

                if (!empty($childrenlist))
                {
                        $sql = "DELETE FROM tasks WHERE task_parent IN (" . implode(', ', $childrenlist) . ", $this->task_id)";
                        if (!db_exec( $sql ))
                                return db_error();
                        else
                        {
                                $this->updateDynamics(); // to update after children are deleted (see above)
                                $this->_action ='deleted with children'; // always overriden?
                        }
                }

                // delete affiliated task_logs
                $sql = "DELETE FROM task_log WHERE task_log_task";
                if (!empty($childrenlist))
                        $sql .= " IN (" . implode(', ', $childrenlist) . ", $this->task_id)";
                else
                        $sql .= "=$this->task_id";

                if (!db_exec( $sql ))
                        return db_error();
                else
                        $this->_action ='deleted';

                 return NULL;
        }

        function updateDependencies( $cslist ) {
        // delete all current entries
                $sql = "DELETE FROM task_dependencies WHERE dependencies_task_id = $this->task_id";
                db_exec( $sql );

        // process dependencies
                $tarr = explode( ",", $cslist );
                foreach ($tarr as $task_id) {
                        if (intval( $task_id ) > 0) {
                                $sql = "REPLACE INTO task_dependencies (dependencies_task_id, dependencies_req_task_id) VALUES ($this->task_id, $task_id)";
                                db_exec($sql);
                        }
                }
        }

        /**
        *        Retrieve the tasks dependencies
        *
        *        @author        handco        <handco@users.sourceforge.net>
        *        @return        string        comma delimited list of tasks id's
        **/
        function getDependencies () {
                // Call the static method for this object
                $result = $this->staticGetDependencies ($this->task_id);
                return $result;
        } // end of getDependencies ()

        //}}}

        //{{{ staticGetDependencies ()
        /**
        *        Retrieve the tasks dependencies
        *
        *        @author        handco        <handco@users.sourceforge.net>
        *        @param        integer        ID of the task we want dependencies
        *        @return        string        comma delimited list of tasks id's
        **/
        function staticGetDependencies ($taskId) {
                if (empty($taskId))
                        return '';
                $sql = "
            SELECT dependencies_req_task_id
            FROM task_dependencies td
            WHERE td.dependencies_task_id = $taskId
                ";
                $list = db_loadColumn ($sql);
                $result = $list ? implode (',', $list) : '';

                return $result;
        } // end of staticGetDependencies ()

        //}}}

        function notifyOwner() {
                GLOBAL $AppUI, $dPconfig, $locale_char_set;

                $sql = "SELECT project_name FROM projects WHERE project_id=$this->task_project";
                $projname = db_loadResult( $sql );

                $mail = new Mail;

                $mail->Subject( dPgetConfig('email_prefix') . " $projname::$this->task_name ".$AppUI->_($this->_action, UI_OUTPUT_RAW), $locale_char_set);

        // c = creator
        // a = assignee
        // o = owner
                $sql = "SELECT t.task_id,"
                ."\ncc.contact_email as creator_email,"
                ."\ncc.contact_first_name as creator_first_name,"
                ."\ncc.contact_last_name as creator_last_name,"
                ."\noc.contact_email as owner_email,"
                ."\noc.contact_first_name as owner_first_name,"
                ."\noc.contact_last_name as owner_last_name,"
                ."\na.user_id as assignee_id,"
                ."\nac.contact_email as assignee_email,"
                ."\nac.contact_first_name as assignee_first_name,"
                ."\nac.contact_last_name as assignee_last_name"
                ."\nFROM tasks t"
                ."\nLEFT JOIN user_tasks u ON u.task_id = t.task_id"
                ."\nLEFT JOIN users o ON o.user_id = t.task_owner"
                ."\nLEFT JOIN contacts oc ON oc.contact_id = o.user_contact"
                ."\nLEFT JOIN users c ON c.user_id = t.task_creator"
                ."\nLEFT JOIN contacts cc ON cc.contact_id = c.user_contact"
                ."\nLEFT JOIN users a ON a.user_id = u.user_id"
                ."\nLEFT JOIN contacts ac ON ac.contact_id = a.user_contact"
                ."\nWHERE t.task_id = $this->task_id";
                $users = db_loadList( $sql );

                if (count( $users )) {
                        $body = $AppUI->_('Project', UI_OUTPUT_RAW).": $projname";
                        $body .= "\n".$AppUI->_('Task', UI_OUTPUT_RAW).":    $this->task_name";
                        $body .= "\n".$AppUI->_('URL', UI_OUTPUT_RAW).":     {$dPconfig['base_url']}/index.php?m=tasks&a=view&task_id=$this->task_id";
                        $body .= "\n\n" . $AppUI->_('Description', UI_OUTPUT_RAW) . ":"
                                . "\n$this->task_description";
                        $body .= "\n\n" . $AppUI->_('Creator', UI_OUTPUT_RAW).":" . $AppUI->user_first_name . " " . $AppUI->user_last_name;

                        $body .= "\n\n" . $AppUI->_('Progress', UI_OUTPUT_RAW) . ": " . $this->task_percent_complete . "%";
                        $body .= "\n\n" . dPgetParam($_POST, "task_log_description");


                        $mail->Body( $body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "" );
                        $mail->From ( '"' . $AppUI->user_first_name . " " . $AppUI->user_last_name
                                . '" <' . $AppUI->user_email . '>'
                        );
                }

                if ($mail->ValidEmail($users[0]['owner_email'])) {
                        $mail->To( $users[0]['owner_email'], true );
                        $mail->Send();
                }

                return '';
        }

        //additional comment will be included in email body
        function notify( $comment = '' ) {
                GLOBAL $AppUI, $dPconfig, $locale_char_set;
                $df = $AppUI->getPref('SHDATEFORMAT');
                $df .= " " . $AppUI->getPref('TIMEFORMAT');

                $sql = "SELECT project_name FROM projects WHERE project_id=$this->task_project";
                $projname = db_loadResult( $sql );

                $mail = new Mail;

                $mail->Subject( "$projname::$this->task_name ".$AppUI->_($this->_action, UI_OUTPUT_RAW), $locale_char_set);

        // c = creator
        // a = assignee
        // o = owner
                $sql = "SELECT t.task_id,"
                ."\ncc.contact_email as creator_email,"
                ."\ncc.contact_first_name as creator_first_name,"
                ."\ncc.contact_last_name as creator_last_name,"
                ."\noc.contact_email as owner_email,"
                ."\noc.contact_first_name as owner_first_name,"
                ."\noc.contact_last_name as owner_last_name,"
                ."\na.user_id as assignee_id,"
                ."\nac.contact_email as assignee_email,"
                ."\nac.contact_first_name as assignee_first_name,"
                ."\nac.contact_last_name as assignee_last_name"
                ."\nFROM tasks t"
                ."\nLEFT JOIN user_tasks u ON u.task_id = t.task_id"
                ."\nLEFT JOIN users o ON o.user_id = t.task_owner"
                ."\nLEFT JOIN contacts oc ON oc.contact_id = o.user_contact"
                ."\nLEFT JOIN users c ON c.user_id = t.task_creator"
                ."\nLEFT JOIN contacts cc ON cc.contact_id = c.user_contact"
                ."\nLEFT JOIN users a ON a.user_id = u.user_id"
                ."\nLEFT JOIN contacts ac ON ac.contact_id = a.user_contact"
                ."\nWHERE t.task_id = $this->task_id";
                $users = db_loadList( $sql );

                if (count( $users )) {
                        $task_start_date       = new CDate($this->task_start_date);
                        $task_finish_date      = new CDate($this->task_end_date);

                        $body = $AppUI->_('Project', UI_OUTPUT_RAW).": $projname";
                        $body .= "\n".$AppUI->_('Task', UI_OUTPUT_RAW).":    $this->task_name";
                        //Priority not working for some reason, will wait till later
                        //$body .= "\n".$AppUI->_('Priority'). ": $this->task_priority";
                        $body .= "\n".$AppUI->_('Start Date', UI_OUTPUT_RAW) . ": " . $task_start_date->format( $df );
                        $body .= "\n".$AppUI->_('Finish Date', UI_OUTPUT_RAW) . ": " . ($this->task_end_date != "" ? $task_finish_date->format( $df ) : "");
                        $body .= "\n".$AppUI->_('URL', UI_OUTPUT_RAW).":     {$dPconfig['base_url']}/index.php?m=tasks&a=view&task_id=$this->task_id";
                        $body .= "\n\n" . $AppUI->_('Description', UI_OUTPUT_RAW) . ":"
                                . "\n$this->task_description";
                        if ($users[0]['creator_email']) {
                                $body .= "\n\n" . $AppUI->_('Creator', UI_OUTPUT_RAW).":"
                                        . "\n" . $users[0]['creator_first_name'] . " " . $users[0]['creator_last_name' ]
                                        . ", " . $users[0]['creator_email'];
                        }
                        $body .= "\n\n" . $AppUI->_('Owner', UI_OUTPUT_RAW).":"
                                . "\n" . $users[0]['owner_first_name'] . " " . $users[0]['owner_last_name' ]
                                . ", " . $users[0]['owner_email'];

                        if ($comment != '') {
                                $body .= "\n\n".$comment;
                        }
                        $mail->Body( $body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "" );
                        $mail->From ( '"' . $AppUI->user_first_name . " " . $AppUI->user_last_name
                                . '" <' . $AppUI->user_email . '>'
                        );
                }

                $mail_owner = $AppUI->getPref('MAILALL');

                foreach ($users as $row) {
                        if ($mail_owner || $row['assignee_id'] != $AppUI->user_id) {
                                if ($mail->ValidEmail($row['assignee_email'])) {
                                        $mail->To( $row['assignee_email'], true );
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
        function email_log(&$log, $assignees, $task_contacts, $project_contacts, $others, $extras)
        {
                global $AppUI, $locale_char_set, $dPconfig;

                $mail_recipients = array();
                $q =& new DBQuery;
                if (isset($assignees) && $assignees == 'on') {
                        $q->clear();
                        $q->addTable('user_tasks', 'ut');
                        $q->addWhere('ut.task_id = ' . $this->task_id);
                        $q->leftJoin('users', 'ua', 'ua.user_id = ut.user_id');
                        $q->leftJoin('contacts', 'c', 'c.contact_id = ua.user_contact');
                        $q->addQuery('c.contact_email');
                        $q->addQuery('c.contact_first_name');
                        $q->addQuery('c.contact_last_name');
                        $req =& $q->exec(QUERY_STYLE_NUM);
                        for($req; ! $req->EOF; $req->MoveNext()) {
                                list($email, $first, $last) = $req->fields;
                                if (! isset($mail_recipients[$email]))
                                        $mail_recipients[$email] = trim($first) . ' ' . trim($last);
                        }
                }
                if (isset($task_contacts) && $task_contacts == 'on') {
                        $q->clear();
                        $q->addTable('task_contacts', 'tc');
                        $q->addWhere('tc.task_id = ' . $this->task_id);
                        $q->leftJoin('contacts', 'c', 'c.contact_id = tc.contact_id');
                        $q->addQuery('c.contact_email');
                        $q->addQuery('c.contact_first_name');
                        $q->addQuery('c.contact_last_name');
                        $req =& $q->exec(QUERY_STYLE_NUM);
                        for ($req; ! $req->EOF; $req->MoveNext()) {
                                list($email, $first, $last) = $req->fields;
                                if (! isset($mail_recipients[$email]))
                                        $mail_recipients[$email] = $first . ' ' . $last;
                        }
                }
                if (isset($project_contacts) && $project_contacts == 'on') {
                        $q->clear();
                        $q->addTable('project_contacts', 'pc');
                        $q->addWhere('pc.project_id = ' . $this->task_project);
                        $q->leftJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
                        $q->addQuery('c.contact_email');
                        $q->addQuery('c.contact_first_name');
                        $q->addQuery('c.contact_last_name');
                        $req =& $q->exec(QUERY_STYLE_NUM);
                        for ($req; ! $req->EOF; $req->MoveNext()) {
                                list($email, $first, $last) = $req->fields;
                                if (! isset($mail_recipients[$email]))
                                        $mail_recipients[$email] = $first . ' ' . $last;
                        }
                }
                if (isset($others)) {
                        $others = trim($others, " \r\n\t,"); // get rid of empty elements.
                        if (strlen($others) > 0) {
                                $q->clear();
                                $q->addTable('contacts', 'c');
                                $q->addWhere('c.contact_id in (' . $others . ')');
                                $q->addQuery('c.contact_email');
                                $q->addQuery('c.contact_first_name');
                                $q->addQuery('c.contact_last_name');
                                $req =& $q->exec(QUERY_STYLE_NUM);
                                for ($req; ! $req->EOF; $req->MoveNext()) {
                                        list($email, $first, $last) = $req->fields;
                                        if (! isset($mail_recipients[$email]))
                                                $mail_recipients[$email] = $first . ' ' . $last;
                                }
                        }
                }
                if (isset($extras) && $extras) {
                        // Search for semi-colons, commas or spaces and allow any to be separators
                        $extra_list = preg_split('/[\s,;]+/', $extras);
                        foreach ($extra_list as $email) {
                                if ($email && ! isset($mail_recipients[$email]) )
                                        $mail_recipients[$email] = $email;
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
                $mail->Subject( $prefix .  ' ' . $log->task_log_name, $char_set);

                $sql = "SELECT project_name FROM projects WHERE project_id=$this->task_project";
                $projname = db_loadResult( $sql );

                $body = $AppUI->_('Project', UI_OUTPUT_RAW) . ": $projname\n";
                if ($this->task_parent != $this->task_id) {
                        $q->clear();
                        $q->addTable('tasks');
                        $q->addQuery('task_name');
                        $q->addWhere('task_id = ' . $this->task_parent);
                        $req =& $q->exec(QUERY_STYLE_NUM);
                        if ($req) {
                                $body .= $AppUI->_('Parent Task', UI_OUTPUT_RAW) . ': ' . $req->fields[0] . "\n";
                        }
                }
                $q->clear();
                $body .= $AppUI->_('Task') . ": $this->task_name\n";
                $task_types = dPgetSysVal("TaskType");
                $body .= $AppUI->_('Task Type', UI_OUTPUT_RAW) . ':' . $task_types[$this->task_type] . "\n";
                $body .= $AppUI->_('URL', UI_OUTPUT_RAW) . ": {$dPconfig['base_url']}/index.php?m=tasks&a=view&task_id=$this->task_id\n\n";
                $body .= $AppUI->_('Summary', UI_OUTPUT_RAW) . ": $log->task_log_name\n\n";
                $body .= $log->task_log_description;

                // Append the user signature to the email - if it exists.
                $q->addQuery('user_signature');
                $q->addWhere('user_id = ' . $AppUI->user_id);
                $q->addTable('users');
                if ($res = $q->exec()) {
                        if ($res->fields['user_signature'])
                                $body .= "\n--\n" . $res->fields['user_signature'];
                }
                $q->clear();

                $mail->Body( $body, $char_set);
                $mail->From( "$AppUI->user_first_name $AppUI->user_last_name <$AppUI->user_email>");

                $recipient_list = "";
                foreach ($mail_recipients as $email => $name) {
                        if ($mail->ValidEmail($email)) {
                                $mail->To($email);
                                $recipient_list .= "$email ($name)\n";
                        } else {
                                $recipient_list .= "Invalid email address '$email' for $name, not sent\n";
                        }
                }
                $mail->Send();
                // Now update the log
                $save_email = @$AppUI->getPref('TASKLOGNOTE');
                if ($save_email) {
                  $log->task_log_description .= "\nEmailed " . date('d/m/Y H:i:s') . " to:\n$recipient_list";
                        return true;
                }

                return false; // No update needed.
        }

/**
* @param Date Start date of the period
* @param Date End date of the period
* @param integer The target company
*/
        function getTasksForPeriod( $start_date, $end_date, $company_id=0 ) {
                GLOBAL $AppUI;
        // convert to default db time stamp
                $db_start = $start_date->format( FMT_DATETIME_MYSQL );
                $db_end = $end_date->format( FMT_DATETIME_MYSQL );

                // filter tasks for not allowed projects
                $tasks_filter = '';
                $proj =& new CProject;
                $task_filter_where = $proj->getAllowedSQL($AppUI->user_id, 'task_project');
                if (count($task_filter_where))
                  $tasks_filter = ' AND (' . implode(' AND ', $task_filter_where) . ")";


        // assemble where clause
                $where = "task_project = project_id"
                        . "\n\tAND task_status > -1"
                        . "\n\tAND ("
                        . "\n\t\t(task_start_date <= '$db_end' AND task_end_date >= '$db_start')"
                        . "\n\t\tOR task_start_date BETWEEN '$db_start' AND '$db_end'"
                        . "\n\t)"
                    . "\n\t$tasks_filter";
        /*
                        OR
                        task_end_date BETWEEN '$db_start' AND '$db_end'
                        OR
                        (DATE_ADD(task_start_date, INTERVAL task_duration HOUR)) BETWEEN '$db_start' AND '$db_end'
                        OR
                        (DATE_ADD(task_start_date, INTERVAL task_duration DAY)) BETWEEN '$db_start' AND '$db_end'
        */
                $where .= $company_id ? "\n\tAND project_company = '$company_id'" : '';

        // exclude read denied projects
                $obj = new CProject();
                $deny = $obj->getDeniedRecords( $AppUI->user_id );

                $where .= count($deny) > 0 ? "\n\tAND task_project NOT IN (" . implode( ',', $deny ) . ')' : '';

        // get any specifically denied tasks
                $obj = new CTask();
                $allow = $obj->getAllowedSQL( $AppUI->user_id );

                $where .= count($allow) > 0 ? "\n\tAND " . implode( ' AND ', $allow ) : '';

        // assemble query
                $sql = "SELECT DISTINCT task_id, task_name, task_start_date, task_end_date,"
                        . "\n\ttask_duration, task_duration_type,"
                        . "\n\tproject_color_identifier AS color,"
                        . "\n\tproject_name"
                        . "\nFROM tasks,projects,companies"
                        . "\nWHERE $where"
                        . "\nORDER BY task_start_date";

//echo "<pre>$sql</pre>";
        // execute and return
                return db_loadList( $sql );
        }

        function canAccess( $user_id ) {
                //echo intval($this->task_access);
                // Let's see if this user has admin privileges
                if(!getDenyRead("admin")){
                        return true;
                }

                switch ($this->task_access) {
                        case 0:
                                // public
                                return true;
                                break;
                        case 1:
                                // protected
                                $sql = "SELECT user_company FROM users WHERE user_id=$user_id";
                                $user_company = db_loadResult( $sql );
                                $sql = "SELECT user_company FROM users WHERE user_id=$this->task_owner";
                                $owner_company = db_loadResult( $sql );
                                //echo "$user_company,$owner_company";die;

                                $sql = "SELECT COUNT(*) FROM user_tasks WHERE user_id=$user_id AND task_id=$this->task_id";
                                $count = db_loadResult( $sql );
                                return (($owner_company == $user_company && $count > 0) || $this->task_owner == $user_id);
                                break;
                        case 2:
                                // participant
                                $sql = "SELECT COUNT(*) FROM user_tasks WHERE user_id=$user_id AND task_id=$this->task_id";
                                $count = db_loadResult( $sql );
                                return ($count > 0 || $this->task_owner == $user_id);
                                break;
                        case 3:
                                // private
                                return ($this->task_owner == $user_id);
                                break;
                }
        }

        /**
        *       retrieve tasks are dependent of another.
        *       @param  integer         ID of the master task
        *       @param  boolean         true if is a dep call (recurse call)
        *       @param  boolean         false for no recursion (needed for calc_end_date)
        **/
        function dependentTasks ($taskId = false, $isDep = false, $recurse = true) {
                static $aDeps = false;
                // Initialize the dependencies array
                if (($taskId == false) && ($isDep == false))
                        $aDeps = array();

                // retrieve dependents tasks
                if (!$taskId)
                        $taskId = $this->task_id;

                if (empty($taskId))
                        return '';
                $sql = "
                        SELECT dependencies_task_id
                        FROM task_dependencies AS td, tasks AS t
                        WHERE td.dependencies_req_task_id = $taskId
                        AND td.dependencies_task_id = t.task_id
                ";
                // AND t.task_dynamic != 1   dynamics are not updated but they are considered
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
                if ($isDep)
                        return;

                return implode (',', $aDeps);

        } // end of dependentTasks()

        /*
         *       shift dependents tasks dates
         *       @param  integer         time offset in seconds
         *       @return void
         */
        function shiftDependentTasks () {
                // Get tasks that depend on this task
                $csDeps = explode( ",", $this->dependentTasks('','',false));

                if ($csDeps[0] == '')
                        return;

                // Stage 1: Update dependent task dates (accounting for working hours)
                foreach( $csDeps as $task_id )
                        $this->update_dep_dates( $task_id );

                // Stage 2: Now shift the dependent tasks' dependents
                foreach( $csDeps as $task_id ) {
                        $newTask = new CTask();
                        $newTask->load($task_id);
                        $newTask->shiftDependentTasks();
                }
                return;

        } // end of shiftDependentTasks()

        /*
         *        Update this task's dates in the DB.
         *        start date:         based on max dependency end date
         *        end date:           based on start date + working duration
         *
         *        @param                integer task_id of task to update
         */
        function update_dep_dates( $task_id ) {
                GLOBAL $tracking_dynamics;

                $destDate = new CDate();
                $newTask = new CTask();

                $newTask->load($task_id);

                // Do not update tasks that are not tracking dependencies
                if (!in_array($newTask->task_dynamic, $tracking_dynamics))
                        return;

                // start date, based on maximal dep end date
                $destDate->setDate( $this->get_deps_max_end_date( $newTask ) );
                $destDate = $this->next_working_day( $destDate );
                $new_start_date = $destDate->format( FMT_DATETIME_MYSQL );

                // end date, based on start date and work duration
                $newTask->task_start_date = $new_start_date;
                $newTask->calc_task_end_date();
                $new_end_date = $newTask->task_end_date;

                $sql = "UPDATE tasks
                SET
                                task_start_date = '$new_start_date',
                                task_end_date = '$new_end_date'
                        WHERE         task_dynamic <> '1' AND task_id = $task_id
                ";

                db_exec( $sql );

                if ( $newTask->task_parent != $newTask->task_id )
                        $newTask->updateDynamics();
                return;
        }

        // Return date obj for the start of next working day
        function next_working_day( $dateObj ) {
                global $AppUI;
                $end = intval(dPgetConfig('cal_day_end'));
                $start = intval(dPgetConfig('cal_day_start'));
                while ( ! $dateObj->isWorkingDay() || $dateObj->getHour() >= $end ) {
                        $dateObj->addDays(1);
                        $dateObj->setTime($start, '0', '0');
                }
                return $dateObj;
        }
        // Return date obj for the end of the previous working day
        function prev_working_day( $dateObj ) {
                global $AppUI;
                $end = intval(dPgetConfig('cal_day_end'));
                $start = intval(dPgetConfig('cal_day_start'));
                while ( ! $dateObj->isWorkingDay() || ( $dateObj->getHour() < $start ) ||
                              ( $dateObj->getHour() == $start && $dateObj->getMinute() == '0' ) ) {
                        $dateObj->addDays(-1);
                        $dateObj->setTime($end, '0', '0');
                }
                return $dateObj;
        }

        /*

         Get the last end date of all of this task's dependencies

         @param Task object
         returns FMT_DATETIME_MYSQL date

         */

        function get_deps_max_end_date( $taskObj ) {
                global $tracked_dynamics;

                $deps = $taskObj->getDependencies();
                $obj = new CTask();

                // Don't respect end dates of excluded tasks
                if ($tracked_dynamics) {
                        $track_these = implode(',', $tracked_dynamics);
                        $sql = "SELECT MAX(task_end_date) FROM tasks
                                WHERE task_id IN ($deps) AND task_dynamic IN ($track_these)";
                }

                $last_end_date = db_loadResult( $sql );

                if ( !$last_end_date ) {
                        // Set to project start date
                        $id = $taskObj->task_project;
                        $sql = "SELECT project_start_date FROM projects
                                WHERE project_id = $id";
                        $last_end_date = db_loadResult( $sql );
                }

                return $last_end_date;
        }

        /*
        * Calculate this task obj's end date. Based on start date
        * and the task duration and duration type.
        */
        function calc_task_end_date() {
                $e = $this->calc_end_date( $this->task_start_date, $this->task_duration, $this->task_duration_type );
                $this->task_end_date = $e->format( FMT_DATETIME_MYSQL );
        }

        /*

         Calculate end date given start date and work time.
         Accounting for (non)working days and working hours.

         @param date obj or mysql time - start date
         @param int - number
         @param int - durnType 24=days, 1=hours
         returns date obj

        */

        function calc_end_date( $start_date=null, $durn='8', $durnType='1' ) {
                GLOBAL $AppUI;

                $cal_day_start = intval(dPgetConfig( 'cal_day_start' ));
                $cal_day_end = intval(dPgetConfig( 'cal_day_end' ));
                $daily_working_hours = intval(dPgetConfig( 'daily_working_hours' ));

                $s = new CDate( $start_date );
                $e = $s;
                $inc = $durn;
                $full_working_days = 0;
                $hours_to_add_to_last_day = 0;
                $hours_to_add_to_first_day = $durn;

                // Calc the end date
                if ( $durnType == 24 ) { // Units are full days

                        $full_working_days = ceil($durn);
                        for ( $i = 0 ; $i < $full_working_days ; $i++ ) {
                                $e->addDays(1);
                                $e->setTime($cal_day_start, '0', '0');
                                if ( !$e->isWorkingDay() )
                                        $full_working_days++;
                        }
                        $e->setHour( $s->getHour() );

                } else {  // Units are hours

                        // First partial day
                        if (( $s->getHour() + $inc ) > $cal_day_end ) {
                                // Account hours for partial work day
                                $hours_to_add_to_first_day = $cal_day_end - $s->getHour();
                                if ( $hours_to_add_to_first_day > $daily_working_hours )
                                        $hours_to_add_to_first_day = $daily_working_hours;
                                $inc -= $hours_to_add_to_first_day;
                                $hours_to_add_to_last_day = $inc % $daily_working_hours;
                                // number of full working days remaining
                                $full_working_days = round(($inc - $hours_to_add_to_last_day) / $daily_working_hours);

                                if ( $hours_to_add_to_first_day != 0 ) {
                                        while (1) {
                                                // Move on to the next workday
                                                $e->addDays(1);
                                                $e->setTime($cal_day_start, '0', '0');
                                                if ( $e->isWorkingDay() )
                                                        break;
                                        }
                                }
                        } else {
                                // less than one day's work, update the hour and be done..
                                $e->setHour( $e->getHour() + $hours_to_add_to_first_day );
                        }

                        // Full days
                        for ( $i = 0 ; $i < $full_working_days ; $i++ ) {
                                $e->addDays(1);
                                $e->setTime($cal_day_start, '0', '0');
                                if ( !$e->isWorkingDay() )
                                        $full_working_days++;
                        }
                        // Last partial day
                        if ( !($full_working_days == 0 && $hours_to_add_to_last_day == 0) )
                                $e->setHour( $cal_day_start + $hours_to_add_to_last_day );

                }
                // Go to start of prev work day if current work day hasn't begun
                if ( $durn != 0 )
                        $e = $this->prev_working_day( $e );

                return $e;

        } // End of calc_end_date

        /**
        * Function that returns the amount of hours this
        * task consumes per user each day
        */
        function getTaskDurationPerDay($use_percent_assigned = false){
                $duration              = $this->task_duration*($this->task_duration_type == 24?dPgetConfig('daily_working_hours'):$this->task_duration_type);
                $task_start_date       = new CDate($this->task_start_date);
                $task_finish_date      = new CDate($this->task_end_date);
                $assigned_users        = $this->getAssignedUsers();
                if ($use_percent_assigned) {
                        $number_assigned_users = 0;
                        foreach ($assigned_users as $u) {
                                $number_assigned_users += ( $u['perc_assignment'] / 100 );
                        }
                } else {
                  $number_assigned_users = count($assigned_users);
                }

                $day_diff              = $task_finish_date->dateDiff($task_start_date);
                $number_of_days_worked = 0;
                $actual_date           = $task_start_date;

                for($i=0; $i<=$day_diff; $i++){
                        if($actual_date->isWorkingDay()){
                                $number_of_days_worked++;
                        }
                        $actual_date->addDays(1);
                }
                // May be it was a Sunday task
                if($number_of_days_worked == 0) $number_of_days_worked = 1;
                if($number_assigned_users == 0) $number_assigned_users = 1;
                return ($duration/$number_assigned_users) / $number_of_days_worked;
        }

         // unassign a user from task
        function removeAssigned( $user_id ) {
        // delete all current entries
                $sql = "DELETE FROM user_tasks WHERE task_id = $this->task_id AND user_id = $user_id";
                db_exec( $sql );

        }

        //using user allocation percentage ($perc_assign)
        // @return      returns the Names of the concerned Users if there occured an overAssignment, otherwise false
        function updateAssigned( $cslist, $perc_assign, $del=true, $rmUsers=false ) {

        // process assignees
                $tarr = explode( ",", $cslist );

                // delete all current entries from $cslist
                if ($del == true && $rmUsers == true) {
                        foreach ($tarr as $user_id) {
                                if ($user_id > '') {
                                        $sql = "DELETE FROM user_tasks WHERE task_id = $this->task_id
                                                AND user_id = $user_id";
                                        db_exec( $sql );
                                }
                        }

                         return false;

                } else if ($del == true) {      // delete all on this task for a hand-over of the task
                        $sql = "DELETE FROM user_tasks WHERE task_id = $this->task_id";
                        db_exec( $sql );
                }


                // get Allocation info in order to check if overAssignment occurs
                $alloc = $this->getAllocation("user_id");
                $overAssignment = false;


                foreach ($tarr as $user_id) {
                        if (intval( $user_id ) > 0) {
                                $perc = $perc_assign[$user_id];
                                if (dPgetConfig("check_overallocation") && $perc > $alloc[$user_id]['freeCapacity']) {
                                        // add Username of the overAssigned User
                                        $overAssignment .= " ".$alloc[$user_id]['userFC'];
                                } else {
                                        $sql = "REPLACE INTO user_tasks (user_id, task_id, perc_assignment) VALUES ($user_id, $this->task_id, $perc)";
                                        db_exec( $sql );
                                }
                        }
                }
                return $overAssignment;
        }

        function getAssignedUsers(){
                $sql = "select u.*, ut.perc_assignment, ut.user_task_priority, co.contact_last_name
                        from users as u, user_tasks as ut
            LEFT JOIN contacts as co ON co.contact_id = u.user_contact
                        where ut.task_id = '$this->task_id'
                              and ut.user_id = u.user_id";
                return db_loadHashList($sql, "user_id");
        }

        /**
        *  Calculate the extent of utilization of user assignments
        *  @param string hash   a hash for the returned hashList
        *  @param array users   an array of user_ids calculating their assignment capacity
        *  @return array        returns hashList of extent of utilization for assignment of the users
        */
        function getAllocation( $hash = NULL, $users = NULL ) {
                // use userlist if available otherwise pull data for all users
                $where = !empty($users) ? 'WHERE u.user_id IN ('.implode(",", $users).') ' : '';
                // retrieve the systemwide default preference for the assignment maximum
                $sql = "SELECT pref_value FROM user_preferences WHERE pref_user = 0 AND pref_name = 'TASKASSIGNMAX'";
                $result = db_loadHash($sql, $sysChargeMax);
                                                                if (! $result)
                                                                        $scm = 0;
                                                                else
                                                                        $scm = $sysChargeMax['pref_value'];
                // provide actual assignment charge, individual chargeMax and freeCapacity of users' assignments to tasks
                $sql = "SELECT u.user_id,
                        CONCAT(CONCAT_WS(' [', CONCAT_WS(' ',contact_first_name,contact_last_name), IF(IFNULL((IFNULL(up.pref_value,$scm)-SUM(ut.perc_assignment)),up.pref_value)>0,IFNULL((IFNULL(up.pref_value,$scm)-SUM(ut.perc_assignment)),up.pref_value),0)), '%]') AS userFC,
                        IFNULL(SUM(ut.perc_assignment),0) AS charge, u.user_username,
                        IFNULL(up.pref_value,$scm) AS chargeMax,
                        IF(IFNULL((IFNULL(up.pref_value,$scm)-SUM(ut.perc_assignment)),up.pref_value)>0,IFNULL((IFNULL(up.pref_value,$scm)-SUM(ut.perc_assignment)),up.pref_value),0) AS freeCapacity
                        FROM users u
                        LEFT JOIN contacts ON contact_id = user_contact
                        LEFT JOIN user_tasks ut ON ut.user_id = u.user_id
                        LEFT JOIN user_preferences up ON (up.pref_user = u.user_id AND up.pref_name = 'TASKASSIGNMAX')".$where."
                        GROUP BY u.user_id
                        ORDER BY contact_last_name, contact_first_name";
//               echo "<pre>$sql</pre>";
                return db_loadHashList($sql, $hash);
        }

         function getUserSpecificTaskPriority( $user_id = 0, $task_id = NULL ) {
                // use task_id of given object if the optional parameter task_id is empty
                $task_id = empty($task_id) ? $this->task_id : $task_id;
                $sql = "SELECT user_task_priority FROM user_tasks WHERE user_id = $user_id AND task_id = $task_id";
                $prio = db_loadHash($sql, $priority);
                return $prio ? $priority['user_task_priority'] : NULL;
        }

        function updateUserSpecificTaskPriority( $user_task_priority = 0, $user_id = 0, $task_id = NULL ) {
                // use task_id of given object if the optional parameter task_id is empty
                $task_id = empty($task_id) ? $this->task_id : $task_id;
                $sql = "REPLACE INTO user_tasks (user_id, task_id, user_task_priority) VALUES ($user_id, $task_id, $user_task_priority)";
                db_exec( $sql );
        }

    function getProject() {
     $sql = "SELECT project_name, project_short_name, project_color_identifier FROM projects WHERE project_id = '$this->task_project'";
     $proj = db_loadHash($sql, $projects);
     return $projects;
    }

        //Returns task children IDs
        function getChildren() {
                $sql = "select task_id from tasks where task_id <> '$this->task_id'
                                and task_parent = '$this->task_id'";
                return db_loadColumn($sql);
        }

        // Returns task deep children IDs
        function getDeepChildren()
        {
                $children = db_loadColumn( "SELECT task_id FROM tasks WHERE task_parent = $this->task_id" );
                if ($children)
                {
                        $deep_children = array();
                        $tempTask = &new CTask();
                        foreach ($children as $child)
                        {
                                $tempTask->load($child);
                                $deep_children = array_merge($deep_children, $this->getChildren());
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
                if(is_null($task_id)){
                        $task_id = $this->task_id;
                }

                // get children
                $sql = "select task_id
                        from tasks
                        where task_parent = '$task_id'";

                $tasks_id = db_loadColumn($sql);
                if(count($tasks_id) == 0) return true;

                // update status of children
                $sql = "update tasks set task_status = '$new_status' where task_parent = '$task_id'";

                db_exec($sql);
                // update status of children's children
                foreach($tasks_id as $id){
                        if($id != $task_id){
                                $this->updateSubTasksStatus($new_status, $id);
                        }
                }
        }

        /**
        * This function recursively updates all tasks project
        * to the one passed as parameter
        */
        function updateSubTasksProject($new_project , $task_id = null){
                if(is_null($task_id)){
                        $task_id = $this->task_id;
                }
                $sql = "select task_id
                        from tasks
                        where task_parent = '$task_id'";

                $tasks_id = db_loadColumn($sql);
                if(count($tasks_id) == 0) return true;

                $sql = "update tasks set task_project = '$new_project' where task_parent = '$task_id'";
                db_exec($sql);

                foreach($tasks_id as $id){
                        if($id != $task_id){
                                $this->updateSubTasksProject($new_project, $id);
                        }
                }
        }

        function canUserEditTimeInformation(){
                global $dPconfig, $AppUI;

                $project = new CProject();
                $project->load( $this->task_project );

                // Code to see if the current user is
                // enabled to change time information related to task
                $can_edit_time_information = false;
                // Let's see if all users are able to edit task time information
                if(isset($dPconfig['restrict_task_time_editing']) && $dPconfig['restrict_task_time_editing']==true && $this->task_id > 0){

                        // Am I the task owner?
                        if($this->task_owner == $AppUI->user_id){
                                $can_edit_time_information = true;
                        }

                        // Am I the project owner?
                        if($project->project_owner == $AppUI->user_id){
                                $can_edit_time_information = true;
                        }

                        // Am I sys admin?
                        if(!getDenyEdit("admin")){
                                $can_edit_time_information = true;
                        }

                } else if (!isset($dPconfig['restrict_task_time_editing']) || $dPconfig['restrict_task_time_editing']==false || $this->task_id == 0) { // If all users are able, then don't check anything
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
        function addReminder()
        {
          global $dPconfig;
          $day = 86400;

          if (! isset($dPconfig['task_reminder_control'])
          || ! $dPconfig['task_reminder_control'])
            return;

          if (! $this->task_end_date) // No end date, can't do anything.
            return $this->clearReminder(true); // Also no point if it is changed to null

          if ($this->task_percent_complete >= 100)
            return $this->clearReminder(true);

          $eq = new EventQueue;
          $pre_charge = isset($dPconfig['task_reminder_days_before']) ?
            $dPconfig['task_reminder_days_before'] : 1;
          $repeat = isset($dPconfig['task_reminder_repeat'])
            ? $dPconfig['task_reminder_repeat'] : 100;

          // If we don't need any arguments (and we don't)
          // then we set this to null.  We can't just put null in the
          // call to add as it is passed by reference.
          $args = null;

          // Find if we have a reminder on this task already
          $old_reminders = $eq->find('tasks', 'remind', $this->task_id);
          if (count($old_reminders)) {
            // It shouldn't be possible to have more than one reminder,
            // but if we do, we may as well clean them up now.
            foreach ($old_reminders as $old_id => $old_data)
              $eq->remove($old_id);
          }

          // Find the end date of this task, then subtract the
          // required number of days.
          $date = new CDate($this->task_end_date);
          $today = new CDate(date('Y-m-d'));
          if (CDate::compare($date, $today) < 0) {
            $start_day = time();
          } else {
            $start_day = $date->getDate(DATE_FORMAT_UNIXTIME);
            $start_day -= ($day * $pre_charge);
          }

          $eq->add(array($this, 'remind'), $args, 'tasks', false, $this->task_id, 'remind', $start_day, $day, $repeat);
        }

        /**
         * Called by the Event Queue processor to process a reminder
         * on a task.
         * @access        public
         * @param        string        $module        Module name (not used)
         * @param        string        $type Type of event (not used)
         * @param        integer        $id ID of task being reminded
         * @param        integer        $owner        Originator of event
         * @param        mixed        $args event-specific arguments.
         * @return        mixed        true, dequeue event, false, event stays in queue.
          -1, event is destroyed.
         */
        function remind($module, $type, $id, $owner, &$args)
        {
          global $locale_char_set, $AppUI, $baseUrl;
          $df = $AppUI->getPref('SHDATEFORMAT');
          $tf = $AppUI->getPref('TIMEFORMAT');
          // If we don't have preferences set for these, use ISO defaults.
          if (! $df)
            $df = '%Y-%m-%d';
          if (! $tf)
            $tf = '%H:%m';
          $df .= ' ' . $tf;

          // At this stage we won't have an object yet
          if (! $this->load($id))
            return -1; // No point it trying again later.

          // Only remind on working days.
          $today = new CDate();
          if (! $today->isWorkingDay())
            return true;

          // Check if the task is completed
          if ($this->task_percent_complete == 100)
            return -1;

          // Grab the assignee list
          $q = new DBQuery;
          $q->addQuery('c.contact_id, contact_first_name, contact_last_name');
          $q->addQuery('contact_email');
          $q->addTable('user_tasks', 'ut');
          $q->leftJoin('users', 'u', 'u.user_id = ut.user_id');
          $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
          $q->addWhere('ut.task_id = ' . $id);
          $contacts = $q->loadHashList('contact_id');

          // Now we also check the owner of the task, as we will need
          // to notify them as well.
          $owner_is_not_assignee = false;
          $q->clear();
          $q->addQuery('c.contact_id, contact_first_name, contact_last_name');
          $q->addQuery('contact_email');
          $q->addTable('users', 'u');
          $q->leftJoin('contacts', 'c', 'c.contact_id = u.user_contact');
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
            $msg = $AppUI->_(array('OVERDUE',abs($diff), 'DAYS'));
            $prefix = $AppUI->_('Task', UI_OUTPUT_RAW);
          } else {
            $msg = $AppUI->_(array($diff, 'DAYS'));
          }

          $q->clear();
          $q->addTable('projects');
          $q->addQuery('project_name');
          $q->addWhere('project_id = ' . $this->task_project);
          $project_name = $q->loadResult();

          $subject = $prefix . ' ' .$msg . ' ' . $this->task_name . '::' . $project_name;

          $body = $AppUI->_('Task Due', UI_OUTPUT_RAW) . ': ' . $msg . "\n";
          $body .= $AppUI->_('Project', UI_OUTPUT_RAW) . ': ' . $project_name . "\n";
          $body .= $AppUI->_('Task', UI_OUTPUT_RAW) . ': ' . $this->task_name . "\n";

          $starts = new CDate($this->task_start_date);
          $body .= $AppUI->_('Start Date', UI_OUTPUT_RAW) . ': ' . $starts->format($df) . "\n";
          $body .= $AppUI->_('Finish Date', UI_OUTPUT_RAW) . ': ' . $expires->format($df) . "\n";
          $body .= $AppUI->_('URL', UI_OUTPUT_RAW) . ': ' . $baseUrl . '/index.php?m=tasks&a=view&task_id=' . $this->task_id . '&reminded=1' . "\n";
          $body .= "\n" . $AppUI->_('Resources', UI_OUTPUT_RAW) . ":\n";
          foreach ($contacts as $contact) {
            if ( ! $owner_is_assignee
            || $contact['contact_id'] != $owner_contact) {
              $body .= $contact['contact_first_name'] . ' ' . $contact['contact_last_name'] . '<' . $contact['contact_email'] . ">\n";
            }
          }
          $body .= "\n" . $AppUI->_('Description', UI_OUTPUT_RAW) . ":\n";
          $body .= $this->task_description . "\n";

          $mail = new Mail;
          foreach ($contacts as $contact) {
            if ($mail->ValidEmail($contact['contact_email']))
              $mail->To($contact['contact_email']);
          }
          $mail->From($owner_email);
          $mail->Subject($subject, $locale_char_set);
          $mail->Body($body, $locale_char_set);
          return $mail->Send();
        }

        /**
         *
         */
        function clearReminder($dont_check = false)
        {
          $ev = new EventQueue;

          $event_list = $ev->find('tasks', 'remind', $this->id);
          if (count($event_list)) {
            foreach ($event_list as $id => $data) {
              if ($dont_check || $this->task_percent_complete >= 100)
                $ev->remove($id);
            }
          }
        }
				
}


/**
* CTask Class
*/
class CTaskLog extends CDpObject {
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
                $this->CDpObject( 'task_log', 'task_log_id' );

                // ensure changes to checkboxes are honoured
                $this->task_log_problem = intval( $this->task_log_problem );
        }

// overload check method
        function check() {
                $this->task_log_hours = (float) $this->task_log_hours;
                return NULL;
        }

function canDelete( &$msg, $oid=null, $joins=null ) {
                global $AppUI;

                // First things first.  Are we allowed to delete?
                $acl =& $AppUI->acl();
                if ( ! $acl->checkModuleItem('task_log', "delete", $oid)) {
                  $msg = $AppUI->_( "noDeletePermission" );
                  return false;
                }

                $k = $this->_tbl_key;
                if ($oid) {
                        $this->$k = intval( $oid );
                }
                if (is_array( $joins )) {
                        $select = "$k";
                        $join = "";
                        foreach( $joins as $table ) {
                                $select .= ",\nCOUNT(DISTINCT {$table['idfield']}) AS {$table['idfield']}";
                                $join .= "\nLEFT JOIN {$table['name']} ON {$table['joinfield']} = $k";
                        }
                        $sql = "SELECT $select\nFROM $this->_tbl\n$join\nWHERE $k = ".$this->$k." GROUP BY $k";

                        $obj = null;
                        if (!db_loadObject( $sql, $obj )) {
                                $msg = db_error();
                                return false;
                        }
                        $msg = array();
                        foreach( $joins as $table ) {
                                $k = $table['idfield'];
                                if ($obj->$k) {
                                        $msg[] = $AppUI->_( $table['label'] );
                                }
                        }

                        if (count( $msg )) {
                                $msg = $AppUI->_( "noDeleteRecord" ) . ": " . implode( ', ', $msg );
                                return false;
                        } else {
                                return true;
                        }
                }

                return true;
        }
}

function closeOpenedTask($task_id){
    global $tasks_opened;
    global $tasks_closed;

    unset($tasks_opened[array_search($task_id, $tasks_opened)]);
    $tasks_closed[] = $task_id;
}

//This kludgy function echos children tasks as threads

function showtask( &$a, $level=0, $is_opened = true, $today_view = false) {
        global $AppUI, $dPconfig, $done, $query_string, $durnTypes, $userAlloc, $showEditCheckbox;

        $now = new CDate();
        $df = $AppUI->getPref('SHDATEFORMAT');
        $df .= " " . $AppUI->getPref('TIMEFORMAT');
        $perms =& $AppUI->acl();
        $show_all_assignees = @$dPconfig['show_all_task_assignees'] ? true : false;

        $done[] = $a['task_id'];

        $start_date = intval( $a["task_start_date"] ) ? new CDate( $a["task_start_date"] ) : null;
        $end_date = intval( $a["task_end_date"] ) ? new CDate( $a["task_end_date"] ) : null;
        $last_update = isset($a['last_update']) && intval( $a['last_update'] ) ? new CDate( $a['last_update'] ) : null;

        // prepare coloured highlight of task time information
        $sign = 1;
        $style = "";
        if ($start_date) {
                if (!$end_date) {
                       	/*
			** end date calc has been moved to calcEndByStartAndDuration()-function
			** called from array_csort and tasks.php 
			** perhaps this fallback if-clause could be deleted in the future, 
			** didn't want to remove it shortly before the 2.0.2

			*/ 
			$end_date = new CDate('0000-00-00 00:00:00');
                }

                if ($now->after( $start_date ) && $a["task_percent_complete"] == 0) {
                        $style = 'background-color:#ffeebb';
                } else if ($now->after( $start_date ) && $a["task_percent_complete"] < 100) {
                        $style = 'background-color:#e6eedd';
                }

                if ($now->after( $end_date )) {
                        $sign = -1;
                        $style = 'background-color:#cc6666;color:#ffffff';
                }
                if ($a["task_percent_complete"] == 100){
                        $style = 'background-color:#aaddaa; color:#00000';
                }

                $days = $now->dateDiff( $end_date ) * $sign;
        }

        $s = "\n<tr>";
// edit icon
        $s .= "\n\t<td>";
        $canEdit = !getDenyEdit( 'tasks', $a["task_id"] );
        $canViewLog = $perms->checkModuleItem('task_log', 'view', $a['task_id']);
        if ($canEdit) {
                $s .= "\n\t\t<a href=\"?m=tasks&a=addedit&task_id={$a['task_id']}\">"
                        . "\n\t\t\t".'<img src="./images/icons/pencil.gif" alt="'.$AppUI->_( 'Edit Task' ).'" border="0" width="12" height="12">'
                        . "\n\t\t</a>";
        }
        $s .= "\n\t</td>";
// pinned
        $pin_prefix = $a['task_pinned']?'':'un';
        $s .= "\n\t<td>";
        $s .= "\n\t\t<a href=\"?m=tasks&pin=" . ($a['task_pinned']?0:1) . "&task_id={$a['task_id']}\">"
                . "\n\t\t\t".'<img src="./images/icons/' . $pin_prefix . 'pin.gif" alt="'.$AppUI->_( $pin_prefix . 'pin Task' ).'" border="0" width="12" height="12">'
                . "\n\t\t</a>";
        $s .= "\n\t</td>";
// New Log
        if (@$a['task_log_problem']>0) {
                $s .= '<td align="center" valign="middle"><a href="?m=tasks&a=view&task_id='.$a['task_id'].'&tab=0&problem=1">';
                $s .= dPshowImage( './images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem!' );
                $s .='</a></td>';
        } else if ($canViewLog) {
                $s .= "\n\t<td><a href=\"?m=tasks&a=view&task_id=" . $a['task_id'] . '&tab=1">' . $AppUI->_('Log') . '</a></td>';
        } else {
                $s .= "\n\t<td></td>";
                                }
// percent complete
        $s .= "\n\t<td align=\"right\">".intval( $a["task_percent_complete"] ).'%</td>';
// priority
        $s .= "\n\t<td align='center' nowrap='nowrap'>";
        if ($a["task_priority"] < 0 ) {
                $s .= "\n\t\t<img src=\"./images/icons/priority-". -$a["task_priority"] .'.gif" width=13 height=16>';
        } else if ($a["task_priority"] > 0) {
                $s .= "\n\t\t<img src=\"./images/icons/priority+". $a["task_priority"] .'.gif" width=13 height=16>';
        }
        $s .= @$a["file_count"] > 0 ? "<img src=\"./images/clip.png\" alt=\"F\">" : "";
        $s .= "</td>";
// dots
        if ($today_view)
                $s .= '<td width="50%">';
        else
                $s .= '<td width="90%">';
        for ($y=0; $y < $level; $y++) {
                if ($y+1 == $level) {
                        $s .= '<img src="./images/corner-dots.gif" width="16" height="12" border="0">';
                } else {
                        $s .= '<img src="./images/shim.gif" width="16" height="12"  border="0">';
                }
        }
// name link
        $alt = strlen($a['task_description']) > 80 ? substr($a["task_description"],0,80) . '...' : $a['task_description'];
        // instead of the statement below
        $alt = str_replace("\"", "&quot;", $alt);
//        $alt = htmlspecialchars($alt);
        $alt = str_replace("\r", ' ', $alt);
        $alt = str_replace("\n", ' ', $alt);

        $open_link = $is_opened ? "<a href='index.php$query_string&close_task_id=".$a["task_id"]."'><img src='images/icons/collapse.gif' border='0' align='center' /></a>" : "<a href='index.php$query_string&open_task_id=".$a["task_id"]."'><img src='images/icons/expand.gif' border='0' /></a>";
        if ($a["task_milestone"] > 0 ) {
                $s .= '&nbsp;<a href="./index.php?m=tasks&a=view&task_id=' . $a["task_id"] . '" title="' . $alt . '"><b>' . $a["task_name"] . '</b></a> <img src="./images/icons/milestone.gif" border="0"></td>';
        } else if ($a["task_dynamic"] == '1'){
                if (! $today_view)
                        $s .= $open_link;

                $s .= '&nbsp;<a href="./index.php?m=tasks&a=view&task_id=' . $a["task_id"] . '" title="' . $alt . '"><b><i>' . $a["task_name"] . '</i></b></a></td>';
        } else {
                $s .= '&nbsp;<a href="./index.php?m=tasks&a=view&task_id=' . $a["task_id"] . '" title="' . $alt . '">' . $a["task_name"] . '</a></td>';
        }
        if ($today_view) { // Show the project name
                $s .= '<td width="50%">';
                $s .= '<a href="./index.php?m=projects&a=view&project_id=' . $a['task_project'] . '">';
                $s .= '<span style="padding:2px;background-color:#' . $a['project_color_identifier'] . ';color:' . bestColor($a['project_color_identifier']) . '">' . $a['project_name'] . '</span>';
                $s .= '</a></td>';
        }
// task owner
        if (! $today_view) {
                $s .= '<td nowrap="nowrap" align="center">'."<a href='?m=admin&a=viewuser&user_id=".$a['user_id']."'>".$a['user_username']."</a>".'</td>';
        }
//        $s .= '<td nowrap="nowrap" align="center">'. $a["user_username"] .'</td>';
        if ( isset($a['task_assigned_users']) && ($assigned_users = $a['task_assigned_users'])) {
                $a_u_tmp_array = array();
                if($show_all_assignees){
                        $s .= '<td align="center">';
                        foreach ( $assigned_users as $val) {
                                //$a_u_tmp_array[] = "<A href='mailto:".$val['user_email']."'>".$val['user_username']."</A>";
                                $aInfo = "<a href='?m=admin&a=viewuser&user_id=".$val['user_id']."'";
                                $aInfo .= 'title="'.$AppUI->_('Extent of Assignment').':'.$userAlloc[$val['user_id']]['charge'].'%; '.$AppUI->_('Free Capacity').':'.$userAlloc[$val['user_id']]['freeCapacity'].'%'.'">';
                                $aInfo .= $val['user_username']." (".$val['perc_assignment']."%)</a>";
                                $a_u_tmp_array[] = $aInfo;
                        }
                        $s .= join ( ', ', $a_u_tmp_array );
                        $s .= '</td>';
                } else {
                        $s .= '<td align="center" nowrap="nowrap">';
//                        $s .= $a['assignee_username'];
                        $s .= "<a href='?m=admin&a=viewuser&user_id=".$assigned_users[0]['user_id']."'";
                        $s .= 'title="'.$AppUI->_('Extent of Assignment').':'.$userAlloc[$assigned_users[0]['user_id']]['charge'].'%; '.$AppUI->_('Free Capacity').':'.$userAlloc[$assigned_users[0]['user_id']]['freeCapacity'].'%'.'">';
                        $s .= $assigned_users[0]['user_username'] .' (' . $assigned_users[0]['perc_assignment'] .'%)</a>';
                        if($a['assignee_count']>1){
                        $id = $a['task_id'];
                        $s .= " <a href=\"javascript: void(0);\"  onClick=\"toggle_users('users_$id');\" title=\"" . join ( ', ', $a_u_tmp_array ) ."\">(+". ($a['assignee_count']-1) .")</a>";

                        $s .= '<span style="display: none" id="users_' . $id . '">';

                                $a_u_tmp_array[] = $assigned_users[0]['user_username'];
                                for ( $i = 1; $i < count( $assigned_users ); $i++) {
                                        $a_u_tmp_array[] = $assigned_users[$i]['user_username'];
                                        $s .= '<br /><a href="?m=admin&a=viewuser&user_id=';
                                        $s .=  $assigned_users[$i]['user_id'] . '" title="'.$AppUI->_('Extent of Assignment').':'.$userAlloc[$assigned_users[$i]['user_id']]['charge'].'%; '.$AppUI->_('Free Capacity').':'.$userAlloc[$assigned_users[$i]['user_id']]['freeCapacity'].'%'.'">';
                                        $s .= $assigned_users[$i]['user_username'] .' (' . $assigned_users[$i]['perc_assignment'] .'%)</a>';
                                }
                        $s .= '</span>';
                        }
                        $s .= '</td>';
                }
        } else if (! $today_view) {
                // No users asigned to task
                $s .= '<td align="center">-</td>';
        }

        $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($start_date ? $start_date->format( $df ) : '-').'</td>';
// duration or milestone
        $s .= '<td align="center" nowrap="nowrap" style="'.$style.'">';
        $s .= $a['task_duration'] . ' ' . $AppUI->_( $durnTypes[$a['task_duration_type']] );
        $s .= '</td>';
        $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($end_date ? $end_date->format( $df ) : '-').'</td>';
        if ($today_view) {
                $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'. $a['task_due_in'].'</td>';
        } else {
                $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($last_update ? $last_update->format( $df ) : '-').'</td>';
        }

// Assignment checkbox
        if ($showEditCheckbox) {
                $s .= "\n\t<td align='center'><input type=\"checkbox\" name=\"selected_task[{$a['task_id']}]\" value=\"{$a['task_id']}\"/></td>";
        }
        $s .= '</tr>';
        echo $s;
}

function findchild( &$tarr, $parent, $level=0){
        GLOBAL $projects;
        global $tasks_opened;

        $level = $level+1;
        $n = count( $tarr );

        for ($x=0; $x < $n; $x++) {
                if($tarr[$x]["task_parent"] == $parent && $tarr[$x]["task_parent"] != $tarr[$x]["task_id"]){
                    $is_opened = in_array($tarr[$x]["task_id"], $tasks_opened);
                        showtask( $tarr[$x], $level, $is_opened );
                        if($is_opened || !$tarr[$x]["task_dynamic"]){
                            findchild( $tarr, $tarr[$x]["task_id"], $level);
                        }
                }
        }
}

/* please throw this in an include file somewhere, its very useful */

function array_csort()   //coded by Ichier2003
{
    $args = func_get_args();
    $marray = array_shift($args);
	
        if ( empty( $marray )) return array();

        $i = 0;
    $msortline = "return(array_multisort(";
        $sortarr = array();
    foreach ($args as $arg) {
        $i++;
        if (is_string($arg)) {
	for ($j=0; $j < count($marray); $j++) {

		/* we have to calculate the end_date via start_date+duration for 
		** end='0000-00-00 00:00:00' before sorting, see mantis #1509:
		
		** Task definition writes the following to the DB:
		** A without start date: start = end = NULL
		** B with start date and empty end date: start = startdate, end = "0000-00-00 00:00:00"
		** C start + end date: start= startdate, end = end date

		** A the end_date for the middle task (B) is ('dynamically') calculated on display 
		** via start_date+duration, it may be that the order gets wrong due to the fact 
		** that sorting has taken place _before_.
		*/
		if ( $marray[$j]['task_end_date'] == '0000-00-00 00:00:00') {
			
			$marray[$j]['task_end_date'] = calcEndByStartAndDuration($marray[$j]);
		}
                $sortarr[$i][] = $marray[$j][$arg];
            }
        } else {
            $sortarr[$i] = $arg;
        }
        $msortline .= "\$sortarr[".$i."],";
    }
    $msortline .= "\$marray));";

    eval($msortline);
//var_export($marray);
    return $marray;
}

/*
** Calc End Date via Startdate + Duration
** @param array task	A DB row from the earlier fetched tasklist
** @return string	Return calculated end date in MySQL-TIMESTAMP format	
*/

function calcEndByStartAndDuration( $task ) {
	$end_date = new CDate($task['task_start_date']);
        $end_date->addSeconds( @$task['task_duration']*$task['task_duration_type']*SEC_HOUR );
	return $end_date->format(FMT_DATETIME_MYSQL);
}

function sort_by_item_title( $title, $item_name, $item_type )
{
        global $AppUI,$project_id,$task_id,$min_view,$m;
        global $task_sort_item1,$task_sort_type1,$task_sort_order1;
        global $task_sort_item2,$task_sort_type2,$task_sort_order2;

        if ( $task_sort_item2 == $item_name ) $item_order = $task_sort_order2;
        if ( $task_sort_item1 == $item_name ) $item_order = $task_sort_order1;

        if ( isset( $item_order ) ) {
                if ( $item_order == SORT_ASC )
                        echo '<img src="./images/arrow-up.gif" width=11 height=11>';
                else
                        echo '<img src="./images/arrow-down.gif" width=11 height=11>';
        } else
                $item_order = SORT_DESC;

        /* flip the sort order for the link */
        $item_order = ( $item_order == SORT_ASC ) ? SORT_DESC : SORT_ASC;
        if ( $m == 'tasks' )
        {
                if ( $task_id > 0 )
                {
                        echo '<a href="./index.php?m=tasks&a=view&task_id='.$task_id;
                }
                else
                {
                        echo '<a href="./index.php?m=tasks';
                }
        }
        else
        {
                if ( $project_id > 0 )
                {
                        echo '<a href="./index.php?m=projects&a=view&project_id='.$project_id;
                }
                else
                {
                        echo '<a href="./index.php?m=projects';
                }
        }
        echo '&task_sort_item1='.$item_name;
        echo '&task_sort_type1='.$item_type;
        echo '&task_sort_order1='.$item_order;
        if ( $task_sort_item1 == $item_name ) {
                echo '&task_sort_item2='.$task_sort_item2;
                echo '&task_sort_type2='.$task_sort_type2;
                echo '&task_sort_order2='.$task_sort_order2;
        } else {
                echo '&task_sort_item2='.$task_sort_item1;
                echo '&task_sort_type2='.$task_sort_type1;
                echo '&task_sort_order2='.$task_sort_order1;
        }
        echo '" class="hdr">';

        echo $AppUI->_($title);

        echo '</a>';
}


?>

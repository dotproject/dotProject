<?php /* PROJECTDESIGNER $Id: printproject.php,v 1.2 2007/07/06 11:38:13 pedroix Exp $ */
global $AppUI, $dPconfig;
// check permissions for this module
$perms =& $AppUI->acl();
$canView = $perms->checkModule( $m, 'view' );
$canAddProject = $perms->checkModuleItem( 'projects', 'view', $project_id );

if (!$canView) {
	$AppUI->redirect( "m=public&a=access_denied" );
}
$project_id = intval( dPgetParam( $_REQUEST, "project_id", 0 ) );
$project = new CProject();
$projects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, company_name');
$q->addJoin("companies",'co','co.company_id = project_company');
$idx_companies = $q->loadHashList();
$q->clear();
foreach ($projects as $prj_id => $prj_name) {
      $projects[$prj_id] = $idx_companies[$prj_id].': '.$prj_name;
}
asort($projects);
$projects = arrayMerge( array( '0'=>$AppUI->_('(None)', UI_OUTPUT_RAW) ), $projects );

$task = new CTask();
$tasks = $task->getAllowedRecords( $AppUI->user_id, 'task_id,task_name', 'task_name', null, $extra );
$tasks = arrayMerge( array( '0'=>$AppUI->_('(None)', UI_OUTPUT_RAW) ), $tasks );
      // check permissions for this record
      $canReadProject = $perms->checkModuleItem( 'projects', 'view', $project_id );
      $canEditProject = $perms->checkModuleItem( 'projects', 'edit', $project_id );
      $canViewTasks = $perms->checkModule( 'tasks', 'view');
      $canAddTasks = $perms->checkModule( 'tasks', 'add');
      $canEditTasks = $perms->checkModule( 'tasks', 'edit');
      $canDeleteTasks = $perms->checkModule( 'tasks', 'delete');
      
      if (!$canReadProject) {
      	$AppUI->redirect( "m=public&a=access_denied" );
      }

      // check if this record has dependencies to prevent deletion
      $msg = '';
      $obj = new CProject();
      // Now check if the project is editable/viewable.
      $denied = $obj->getDeniedRecords($AppUI->user_id);
      if (in_array($project_id, $denied)) {
      	$AppUI->redirect( "m=public&a=access_denied" );
      }

      $canDeleteProject = $obj->canDelete( $msg, $project_id );

      // get critical tasks (criteria: task_end_date)
      $criticalTasks = ($project_id > 0) ? $obj->getCriticalTasks($project_id) : NULL;
      
      // get ProjectPriority from sysvals
      $projectPriority = dPgetSysVal( 'ProjectPriority' );
      $projectPriorityColor = dPgetSysVal( 'ProjectPriorityColor' );
      $pstatus = dPgetSysVal( 'ProjectStatus' );
      $ptype = dPgetSysVal( 'ProjectType' );
      
      $working_hours = ($dPconfig['daily_working_hours']?$dPconfig['daily_working_hours']:8);
      
      $q  = new DBQuery;
      //check that project has tasks; otherwise run seperate query
      $q->addTable('tasks', 't');
      $q->addQuery("COUNT(distinct t.task_id) AS total_tasks");
      $q->addWhere('task_project = '.$project_id);
      $hasTasks = $q->loadResult();
      $q->clear();

      // load the record data
      // GJB: Note that we have to special case duration type 24 and this refers to the hours in a day, NOT 24 hours
      if ($hasTasks) { 
          $q->addTable('projects','pr');
          $q->addQuery("company_name, CONCAT_WS(' ',contact_first_name,contact_last_name) user_name, pr.*,"
                       ." SUM(t1.task_duration * t1.task_percent_complete"
                       ." * IF(t1.task_duration_type = 24, {$working_hours}, t1.task_duration_type))"
                       ." / SUM(t1.task_duration * IF(t1.task_duration_type = 24, {$working_hours}, t1.task_duration_type))"
                       ." AS project_percent_complete");
          $q->addJoin('companies', 'com', 'company_id = project_company');
          $q->addJoin('users', 'u', 'user_id = project_owner');
          $q->addJoin('contacts', 'con', 'contact_id = user_contact');
          $q->addJoin('tasks', 't1', 'pr.project_id = t1.task_project');
          $q->addWhere('project_id = '.$project_id .' AND t1.task_id = t1.task_parent');
          $q->addGroup('project_id');
          $sql = $q->prepare();
      } else {
          $q->addTable('projects','pr');
          $q->addQuery("company_name, CONCAT_WS(' ',contact_first_name,contact_last_name) user_name, pr.*, "
                       ."(0.0) AS project_percent_complete");
          $q->addJoin('companies', 'com', 'company_id = project_company');
          $q->addJoin('users', 'u', 'user_id = project_owner');
          $q->addJoin('contacts', 'con', 'contact_id = user_contact');
          $q->addWhere('project_id = '.$project_id);
          $q->addGroup('project_id');
          $sql = $q->prepare();
      }
      $q->clear();

      $obj = null;
      if (!db_loadObject( $sql, $obj )) {
      	$AppUI->setMsg( 'Project' );
      	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
      	$AppUI->redirect();
      } else {
      	$AppUI->savePlace();
      }


      // worked hours
      // now milestones are summed up, too, for consistence with the tasks duration sum
      // the sums have to be rounded to prevent the sum form having many (unwanted) decimals because of the mysql floating point issue
      // more info on http://www.mysql.com/doc/en/Problems_with_float.html
      if($hasTasks) {
          $q->addTable('task_log');
          $q->addTable('tasks');
          $q->addQuery('ROUND(SUM(task_log_hours),2)');
          $q->addWhere("task_log_task = task_id AND task_project = $project_id");
          $sql = $q->prepare();
          $q->clear();
          $worked_hours = db_loadResult($sql);
          $worked_hours = rtrim($worked_hours, '.');
          
          // total hours
          // same milestone comment as above, also applies to dynamic tasks
          $q->addTable('tasks');
          $q->addQuery('ROUND(SUM(task_duration),2)');
          $q->addWhere("task_project = $project_id AND task_duration_type = 24 AND task_dynamic != 1");
          $sql = $q->prepare();
          $q->clear();
          $days = db_loadResult($sql);
          
          $q->addTable('tasks');
          $q->addQuery('ROUND(SUM(task_duration),2)');
          $q->addWhere("task_project = $project_id AND task_duration_type = 1 AND task_dynamic != 1");
          $sql = $q->prepare();
          $q->clear();
          $hours = db_loadResult($sql);
          $total_hours = $days * $dPconfig['daily_working_hours'] + $hours;
          
          $total_project_hours = 0;
          
          $q->addTable('tasks', 't');
          $q->addQuery('ROUND(SUM(t.task_duration*u.perc_assignment/100),2)');
          $q->addJoin('user_tasks', 'u', 't.task_id = u.task_id');
          $q->addWhere("t.task_project = $project_id AND t.task_duration_type = 24 AND t.task_dynamic != 1");
          $total_project_days_sql = $q->prepare();
          $q->clear();
          
          $q->addTable('tasks', 't');
          $q->addQuery('ROUND(SUM(t.task_duration*u.perc_assignment/100),2)');
          $q->addJoin('user_tasks', 'u', 't.task_id = u.task_id');
          $q->addWhere("t.task_project = $project_id AND t.task_duration_type = 1 AND t.task_dynamic != 1");
          $total_project_hours_sql = $q->prepare();
          $q->clear();
          
          $total_project_hours = db_loadResult($total_project_days_sql) * $dPconfig['daily_working_hours'] 
              + db_loadResult($total_project_hours_sql);
          //due to the round above, we don't want to print decimals unless they really exist
          //$total_project_hours = rtrim($total_project_hours, "0");
      }
      else { //no tasks in project so "fake" project data
          $worked_hours = $total_hours = $total_project_hours = 0.00;
      }
      

?>

<?php 
$priorities = dPgetsysval('TaskPriority');
$types = dPgetsysval('TaskType');
include_once( $AppUI->getModuleClass( 'tasks' ) );
global $task_access;
$extra = array(
       0=>'(none)',
       1=>'Milestone',
       2=>'Dynamic Task',
       3=>'Inactive Task'
);
?>
<SCRIPT language="JavaScript">
var check_task_dates = <?php
  if (isset($dPconfig['check_task_dates']) && $dPconfig['check_task_dates'])
    echo 'true';
  else
    echo 'false';
?>;
var can_edit_time_information = <?php echo $can_edit_time_information ? 'true' : 'false'; ?>;

var task_name_msg = "<?php echo $AppUI->_('taskName');?>";
var task_start_msg = "<?php echo $AppUI->_('taskValidStartDate');?>";
var task_end_msg = "<?php echo $AppUI->_('taskValidEndDate');?>";

var workHours = <?php echo dPgetConfig( 'daily_working_hours' );?>;
//working days array from config.php
var working_days = new Array(<?php echo dPgetConfig( 'cal_working_days' );?>);
var cal_day_start = <?php echo intval(dPgetConfig( 'cal_day_start' ));?>;
var cal_day_end = <?php echo intval(dPgetConfig( 'cal_day_end' ));?>;
var daily_working_hours = <?php echo intval(dPgetConfig('daily_working_hours')); ?>;
</script>

<style type="text/css">
/* Standard table 'spreadsheet' style */
TABLE.prjprint {
	background: #ffffff;
}

TABLE.prjprint TH {
	background-color: #ffffff;
	color: black;
	list-style-type: disc;
	list-style-position: inside;
	border:solid 1px;
	font-weight: normal;
	font-size:15px;
}

TABLE.prjprint TD {
	background-color: #ffffff;
	font-size:14px;
}

TABLE.prjprint TR {
	padding:5px;
}
	
</style>
<table class="prjprint">
<form name="frmDelete" action="./index.php?m=projects" method="post">
	<input type="hidden" name="dosql" value="do_project_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
</form>

<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">  
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="prjprint">	
            <tr>
            	<td width="22">
            	&nbsp;
            	</td>
            	<td align="center"  colspan="1">
            	<?php
            		echo '<strong> Client Report <strong>';
            	?>
            	</td>
        <!--	    <td width="22" align="right">
				<a href="#" onclick="var img=document.getElementById('imghd'); img.style.display='none'; window.print(); window.close();">
      			<img id="imghd" src="./modules/projectdesigner/images/printer.png" border="0" width="22" height="22" alt="print project" title="print project"/>
      			</a>
      			</td>-->  
      	</tr>
      	</table>
	</td>
</tr>
<tr>
	<?php
            if ($canReadProject) {
               require(dPgetConfig('root_dir')."/modules/projectdesigner/vw_projecttask.php");
            } else {
                  echo $AppUI->_('You do not have permission to view tasks');
            }
	?>
</tr>
</table>

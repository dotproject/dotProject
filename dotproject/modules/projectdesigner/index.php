<?php /* PROJECTDESIGNER $Id: index.php,v 1.5 2008/10/04 15:38:32 theideaman Exp $ */
/*  Copyright (c) 2007 Pedro A. (dotProject Development Team Member)
    THIS MODULE WAS SPONSORED BY DUSTIN OF PURYEAR-IT.COM

    This file is part of the dotProject ProjectDesigner module.

    The ProjectDesigner module is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version, as long as you keep this copyright notice as well as
    the sponsor.txt file which is also part of this module.

    The Project Designer module is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with dotProject; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}
global $AppUI, $dPconfig;
// check permissions for this module
$perms =& $AppUI->acl();
$canView = $perms->checkModule( $m, 'view' );
$canAddProject = $perms->checkModuleItem( 'projects', 'view', $project_id );

if (!$canView) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$base = DP_BASE_URL;
if ( substr($base, -1) != '/') {
      $base .= '/';
}
echo"<style type=\"text/css\">@import url({$base}modules/$m/jscalendar/calendar-win2k-1.css);</style>";
echo "<script type=\"text/javascript\" src=\"{$base}modules/$m/jscalendar/calendar.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"{$base}modules/$m/jscalendar/lang/calendar-en.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"{$base}modules/$m/jscalendar/calendar-setup.js\"></script>\n";

$today = new CDate();
$df = $AppUI->getPref( 'SHDATEFORMAT' );
$tf = $AppUI->getPref( 'TIMEFORMAT' );
$cf = $df.' '.$tf;
$cal_df = $cf;
$cal_df = str_replace('p','a', $cal_df);
$cal_df = str_replace('%I','%hh', $cal_df);
$cal_df = str_replace('%M','%mm', $cal_df);
$cal_df = str_replace('%m','%MM', $cal_df);
$cal_df = str_replace('%MMm','%mm', $cal_df);
$cal_df = str_replace('%d','%dd', $cal_df);
$cal_df = str_replace('%b','%NNN', $cal_df);
$cal_df = str_replace('%','', $cal_df);

//Lets load the users panel viewing options
$q  = new DBQuery;
$q->addTable('project_designer_options','pdo');
$q->addQuery('pdo.*');
$q->addWhere('pdo.pd_option_user = '.$AppUI->user_id);
$view_options = $q->loadList();

$project_id = intval( dPgetParam( $_REQUEST, "project_id", 0 ) );
$extra = array('where'=>'project_status<>7');
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

$extra = array();
$task = new CTask();
$tasks = $task->getAllowedRecords( $AppUI->user_id, 'task_id,task_name', 'task_name', null, $extra );
$tasks = arrayMerge( array( '0'=>$AppUI->_('(None)', UI_OUTPUT_RAW) ), $tasks );

if (!$project_id) {
            // setup the title block
            $ttl = "ProjectDesigner";
            $titleBlock = new CTitleBlock( $ttl, 'projectdesigner.png', $m, "$m.$a" );
            $titleBlock->addCrumb( "?m=projects", "projects list" );
      	$titleBlock->addCell();
            if ($canAddProject) {
            	$titleBlock->addCell(
            		'<input type="submit" class="button" value="'.$AppUI->_('new project').'">', '',
            		'<form action="?m=projects&a=addedit" method="post">', '</form>'
            	);
            }
            $titleBlock->show();
?>

            <script language="javascript">
            function submitIt() {
            	var f = document.prjFrm;
            	var msg ='';
                  if (f.project_id.value == 0) {
                  	msg += "\n<?php echo $AppUI->_('You must select a project first', UI_OUTPUT_JS);?>";
                  	f.project_id.focus();
                  }

                  if (msg.length < 1) {
                  	f.submit();
                  } else {
                  	alert(msg);
                  }
            }
            </script>

            <table border="1" cellpadding="4" cellspacing="0" width="100%" class="std">
            <form name="prjFrm" action="?m=projectdesigner" method="post">
            <tr>
            	<td nowrap="nowrap" style="border: outset #eeeeee 1px;background-color:#fffff" >
            		<font color="<?php echo bestColor('#ffffff'); ?>">
            			<strong><?php echo $AppUI->_('Project'); ?>: <?php echo arraySelect( $projects, 'project_id','onchange="submitIt()" class="text" style="width:500px"', 0  );?></strong>
            		</font>
            	</td>
            </tr>            
            </form>
            </table>
<?php
} else {
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
          $q->addTable('projects', 'pr');
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
          $q->addTable('projects', 'pr');
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
      
      // create Date objects from the datetime fields
      $start_date = intval( $obj->project_start_date ) ? new CDate( $obj->project_start_date ) : null;
      $end_date = intval( $obj->project_end_date ) ? new CDate( $obj->project_end_date ) : null;
      $actual_end_date = intval( $criticalTasks[0]['task_end_date'] ) ? new CDate( $criticalTasks[0]['task_end_date'] ) : null;
      $style = (( $actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

      // setup the title block
      $ttl = "ProjectDesigner";
      $titleBlock = new CTitleBlock( $ttl, 'projectdesigner.png', $m, "$m.$a" );
          $titleBlock->addCrumb( "?m=projects", "projects list" );
     	$titleBlock->addCrumb( "?m=$m", "select another project" );
     	$titleBlock->addCrumb( "?m=projects&a=view&project_id=$project_id", "normal view project" );

      if ($canAddProject) {
      	$titleBlock->addCell();
      	$titleBlock->addCell(
      		'<input type="submit" class="button" value="'.$AppUI->_('new project').'">', '',
      		'<form action="?m=projects&a=addedit" method="post">', '</form>'
      	);
      }

      if ($canAddTask) {
      	$titleBlock->addCell();
      	$titleBlock->addCell(
      		'<input type="submit" class="button" value="'.$AppUI->_('new task').'">', '',
      		'<form action="?m=tasks&a=addedit&task_project=' . $project_id . '" method="post">', '</form>'
      	);
      }
      if ($canEditProject) {
      	$titleBlock->addCell();
      	$titleBlock->addCell(
      		'<input type="submit" class="button" value="'.$AppUI->_('new event').'">', '',
      		'<form action="?m=calendar&a=addedit&event_project=' . $project_id . '" method="post">', '</form>'
      	);
      
      	$titleBlock->addCell();
      	$titleBlock->addCell(
      		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
      		'<form action="?m=files&a=addedit&project_id=' . $project_id . '" method="post">', '</form>'
      	);
      	$titleBlock->addCrumb( "?m=projects&a=addedit&project_id=$project_id", "edit this project" );
      	if ($canDeleteProject) {
      		$titleBlock->addCrumbDelete( 'delete project', $canDelete, $msg );
      	}
      }
     	$titleBlock->addCell();
      $titleBlock->addCell("<a href=\"#\" onclick =\"window.open('index.php?m=projectdesigner&a=printproject&dialog=1&suppressHeaders=1&project_id=$project_id', 'printproject','width=1200, height=600, menubar=1, scrollbars=1')\">
      		<img src=\"./modules/projectdesigner/images/printer.png\" border=\"0\" width=\"22\" height=\"22\" alt=\"print project\" title=\"print project\"/>
      		</a>
      		"
      	);
//      $titleBlock->addCell("<a href=\"#\" onclick =\"window.open('index.php?m=projectdesigner&a=printproject&dialog=1&suppressHeaders=1&project_id=$project_id', 'printproject','width=1200, height=600, menubar=1, scrollbars=1')\">client report</a>");
      $titleBlock->addCell("<a href=\"#\" onclick =\"expandAll()\">
      		<img src=\"./modules/projectdesigner/images/down.png\" border=\"0\" width=\"22\" height=\"22\" alt=\"expand all panels\" title=\"expand all panels\"/>
      		</a>
      		"
      	);
      $titleBlock->addCell("<a href=\"#\" onclick =\"collapseAll()\">
      		<img src=\"./modules/projectdesigner/images/up.png\" border=\"0\" width=\"22\" height=\"22\" alt=\"collapse all panels\" title=\"collpase all panels\"/>
      		</a>
      		"
      	);
      $titleBlock->addCell("<a href=\"#\" onclick =\"document.frmWorkspace.submit()\">
      		<img src=\"./modules/projectdesigner/images/filesave.png\" border=\"0\" width=\"22\" height=\"22\" alt=\"save your workspace\" title=\"save your workspace\"/>
      		</a>
      		"
      	);
     	$titleBlock->addCell();
      $titleBlock->show();
?>
<form name="frmWorkspace" action="?m=<?php echo $m;?>" method="post">
	<input type="hidden" name="dosql" value="do_projectdesigner_aed" />
	<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
      <input type="hidden" name="opt_view_project" value="<?php echo (isset($view_options[0]['pd_option_view_project']) ? $view_options[0]['pd_option_view_project'] : 1);?>" />
      <input type="hidden" name="opt_view_gantt" value="<?php echo (isset($view_options[0]['pd_option_view_gantt']) ? $view_options[0]['pd_option_view_gantt'] : 1);?>" />
      <input type="hidden" name="opt_view_tasks" value="<?php echo (isset($view_options[0]['pd_option_view_tasks']) ? $view_options[0]['pd_option_view_tasks'] : 1);?>" />
      <input type="hidden" name="opt_view_actions" value="<?php echo (isset($view_options[0]['pd_option_view_actions']) ? $view_options[0]['pd_option_view_actions'] : 1);?>" />
      <input type="hidden" name="opt_view_addtsks" value="<?php echo (isset($view_options[0]['pd_option_view_addtasks']) ? $view_options[0]['pd_option_view_addtasks'] : 1);?>" />
      <input type="hidden" name="opt_view_files" value="<?php echo (isset($view_options[0]['pd_option_view_files']) ? $view_options[0]['pd_option_view_files'] : 1);?>" />
</form>

<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function delIt() {
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Project', UI_OUTPUT_JS).'?';?>" )) {
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<script language="javascript">
//dP Related
// ------------------------------------------------------------------
// parseDate( date_string [, prefer_euro_format] )
//
// This function takes a date string and tries to match it to a
// number of possible date formats to get the value. It will try to
// match against the following international formats, in this order:
// y-M-d   MMM d, y   MMM d,y   y-MMM-d   d-MMM-y  MMM d
// M/d/y   M-d-y      M.d.y     MMM-d     M/d      M-d
// d/M/y   d-M-y      d.M.y     d-MMM     d/M      d-M
// A second argument may be passed to instruct the method to search
// for formats like d/M/y (european format) before M/d/y (American).
// Returns a Date object or null if no patterns match.
// ------------------------------------------------------------------
function parseDate(val) {
	var preferEuro=(arguments.length==2)?arguments[1]:false;
	generalFormats=new Array('yyyyMMddHHmm', '<?php echo $cal_df ?>');
	monthFirst=new Array();
      dateFirst =new Array();
//	generalFormats=new Array('yyyyMMddHHmm', 'NNN/dd/yyyy h:m a', 'NNN/dd/yyyy H:m', 'NNN/dd/yyyy hh:mm a', 'NNN/dd/yyyy HH:mm a', 'y-M-d','MMM d, y','MMM d,y','y-MMM-d','d-MMM-y','MMM d');
//	monthFirst=new Array('M/d/y h:m','M-d-y h:m','M.d.y h:m','M/d/y hh:mm','M-d-y hh:mm','M.d.y hh:mm','M/d/y','M-d-y','M.d.y','MMM-d','M/d','M-d');
//	dateFirst =new Array('d/M/y hh:mm','d-M-y hh:mm','d.M.y hh:mm','d/M/y h:m','d-M-y h:m','d.M.y h:m','d-MMM','d/M','d-M','d/M/y','d-M-y','d.M.y','d-MMM','d/M','d-M');
	var checkList=new Array('generalFormats',preferEuro?'dateFirst':'monthFirst',preferEuro?'monthFirst':'dateFirst');
	var d=null;
	for (var i=0; i<checkList.length; i++) {
		var l=window[checkList[i]];
		for (var j=0; j<l.length; j++) {
			d=getDateFromFormat(val,l[j]);
			if (d!=0) { return new Date(d); }
			}
		}
	return null;
	}

function setDate( frm_name, f_date ) {
	fld_date = eval( "document." + frm_name + "." + f_date );
	fld_task_date = eval( "document." + frm_name + "." + "add_task_" + f_date );
	if (fld_date.value.length>0) {
	      if ((parseDate(fld_date.value))==null) {
	            alert('The Date/Time you typed does not match your prefered format, please retype.');
	            fld_task_date.value = '';
	            fld_date.style.backgroundColor = 'red';
            } else {
            	fld_task_date.value = formatDate(parseDate(fld_date.value), "yyyyMMddHHmm");
            	fld_date.value = formatDate(parseDate(fld_date.value), "<?php echo $cal_df ?>");
	            fld_date.style.backgroundColor = '';
	            if (frm_name.indexOf('editFrm')>-1) {
	               if (f_date.indexOf('start_date')>-1) {
	                  start_date = fld_task_date;
	                  end_date = eval( "document." + frm_name + "." + "add_task_" + f_date.replace("start_date","end_date") );
	                  duration_fld = eval( "document." + frm_name + "." + "add_task_" + f_date.replace("start_date","duration") );
	                  durntype_fld = eval( "document." + frm_name + "." + "add_task_" + f_date.replace("start_date","durntype") );
                     } else {
	                  end_date = fld_task_date;
	                  start_date = eval( "document." + frm_name + "." + "add_task_" + f_date.replace("end_date","start_date") );
	                  duration_fld = eval( "document." + frm_name + "." + "add_task_" + f_date.replace("end_date","duration") );
	                  durntype_fld = eval( "document." + frm_name + "." + "add_task_" + f_date.replace("end_date","durntype") );
                     }
	               calcDuration(document.editFrm, start_date, end_date, duration_fld, durntype_fld);
                  }
      	}
	} else {
      	fld_task_date.value = "";
	}
}
</script>
<?php
$priorities = dPgetsysval('TaskPriority');
$types = dPgetsysval('TaskType');
$durntype = dPgetSysVal('TaskDurationType');
include_once( $AppUI->getModuleClass( 'tasks' ) );
global $task_access;
$extra = array(
       0=>'(none)',
       1=>'Milestone',
       2=>'Dynamic Task',
       3=>'Inactive Task'
);
$sel_priorities = arraySelect( $priorities, 'add_task_priority0', 'style="width:80px" class="text"', '' );
$sel_types = arraySelect( $types, 'add_task_type0', 'style="width:80px" class="text"', '' );
$sel_access = arraySelect( $task_access, 'add_task_access0', 'style="width:80px" class="text"', '' );
$sel_extra = arraySelect( $extra, 'add_task_extra0', 'style="width:80px" class="text"', '' );
$sel_durntype = arraySelect( $durntype, 'add_task_durntype0', 'style="width:80px" class="text"', '', true );
?>
<script language="javascript">
var sel_priorities = "<?php echo str_replace(chr(10),'', str_replace('"', "'", $sel_priorities)); ?>";
var sel_types = "<?php echo str_replace(chr(10),'', str_replace('"', "'", $sel_types)); ?>";
var sel_access = "<?php echo str_replace(chr(10),'', str_replace('"', "'", $sel_access)); ?>";
var sel_extra = "<?php echo str_replace(chr(10),'', str_replace('"', "'", $sel_extra)); ?>";
var sel_durntype = "<?php echo str_replace(chr(10), '', str_replace('"', "'", $sel_durntype)); ?>";

function addComponent() {
      var form = document.editFrm;
      var li = parseInt(form.nrcomponents.value);
      var line_nr = li+1;

      var ni = document.getElementById('tcomponents');
      var li = li+1;

      priorities = sel_priorities.replace('priority0','priority_'+line_nr);
      priorities = priorities.replace('priority0','priority_'+line_nr);
      types = sel_types.replace('type0','type_'+line_nr);
      types = types.replace('type0','type_'+line_nr);
      access = sel_access.replace('access0','access_'+line_nr);
      access = access.replace('access0','access_'+line_nr);
      extra = sel_extra.replace('extra0','extra_'+line_nr);
      extra = extra.replace('extra0','extra_'+line_nr);
	 durntype = sel_durntype.replace('durntype0', 'durntype_'+line_nr);
	 durntype = durntype.replace('durntype0', 'durntype_'+line_nr);

      eval('oldType_'+line_nr+'=""');

      var trIdName = 'component'+li+'_';
      var newtr = document.createElement('tr');
      var htmltxt = "";
      newtr.setAttribute("id",trIdName);
      oCell = document.createElement("td");
      oCell.setAttribute ("align","left");
      oCell.setAttribute ("width","5");
      htmltxt = "";
      htmltxt +="<a href='#bottom' onClick='removeComponent(\"component"+line_nr+"_\")'><img src='./modules/projectdesigner/images/remove.png' width='16' height='16' title='<?php echo $AppUI->_('Remove');?>' alt='<?php echo $AppUI->_('Remove');?>' border='0' /></a>";
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +="<input type='hidden' id='add_task_line_"+line_nr+"' name='add_task_line_"+line_nr+"' value='"+line_nr+"' />";
	htmltxt +="<input type='text' class='text' style='width:200px;' name='add_task_name_"+line_nr+"' value='' />";
	htmltxt +="&nbsp;<a href='#component"+li+"_desc' onClick=\"expand_colapse('component"+li+"_desc', 'tblProjects')\"><img id='component"+li+"_desc_expand' src='./images/icons/expand.gif' title='<?php echo $AppUI->_('Edit Task Description');?>' alt='<?php echo $AppUI->_('Edit Task Description');?>' width='12' height='12' border='0'><img id='component"+li+"_desc_collapse' src='./images/icons/collapse.gif' width='12' height='12' border='0' style='display:none'></a>";
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +="<input type='hidden' id='add_task_start_date_"+line_nr+"' name='add_task_start_date_"+line_nr+"' value='<?php echo $today->format( FMT_TIMESTAMP );?>' />";
	htmltxt +="<input type='text' onChange=\"setDate('editFrm', 'start_date_"+line_nr+"');\" class='text' style='width:130px;' id='start_date_"+line_nr+"' name='start_date_"+line_nr+"' value='<?php echo $today->format( $cf );?>' />";
	htmltxt +="<a href='#' onClick=\"return showCalendar('start_date_"+line_nr+"', '<?php echo $cf ?>', 'editFrm', '<?php echo (strpos($cf,'%p')!==false ? '12' : '24') ?>', true)\" >";
	htmltxt +="&nbsp;<img src='./images/calendar.gif' width='24' height='12' alt='<?php echo $AppUI->_('Calendar');?>' border='0' />";
	htmltxt +="</a>";
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      oCell = document.createElement("td");
      htmltxt = "";
//	htmltxt +="<input type='hidden' id='add_task_duration_"+line_nr+"' name='add_task_duration_"+line_nr+"' value='' />";
	htmltxt +="<input type='hidden' id='add_task_end_date_"+line_nr+"' name='add_task_end_date_"+line_nr+"' value='<?php $today->setDate( $today->getTime() + 60 * 60, DATE_FORMAT_UNIXTIME); echo $today->format( FMT_TIMESTAMP );?>' />";
	htmltxt +="<input type='text' onChange=\"setDate('editFrm', 'end_date_"+line_nr+"');\" class='text' style='width:130px;' id='end_date_"+line_nr+"' name='end_date_"+line_nr+"' value='<?php echo $today->format( $cf );?>' />";
	htmltxt +="<a href='#' onClick=\"return showCalendar('end_date_"+line_nr+"', '<?php echo $cf ?>', 'editFrm', '<?php echo (strpos($cf,'%p')!==false ? '12' : '24') ?>', true)\" >";
	htmltxt +="&nbsp;<img src='./images/calendar.gif' width='24' height='12' alt='<?php echo $AppUI->_('Calendar');?>' border='0' />";
	htmltxt +="</a>";
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +="<input type='text' class='text' style='width:40px;text-align:right;' id='add_task_duration_"+line_nr+"' name='add_task_duration_"+line_nr+"' value='1' />";
	htmltxt += "&nbsp;"+durntype ;
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      ni.appendChild(newtr);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +=priorities;
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      ni.appendChild(newtr);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +=types;
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +=access;
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      oCell = document.createElement("td");
      htmltxt = "";
	htmltxt +=extra;
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      ni.appendChild(newtr);
      var trIdName = 'component'+li+'_desc';
      var newtr = document.createElement('tr');
      newtr.setAttribute ("valign","top");
      newtr.style.display = "none";
      var htmltxt = "";
      newtr.setAttribute("id",trIdName);
      oCell = document.createElement("td");
      oCell.setAttribute ("align","left");
      oCell.colSpan = 5;
      oCell.setAttribute ("valign","top");
      htmltxt = "";
      htmltxt +="<b><?php echo $AppUI->_('Task Description');?></b>:<br />";
      htmltxt +="<textarea cols='80' rows='8' id='add_task_description_"+line_nr+"' name='add_task_description_"+line_nr+"' /></textarea>";
	oCell.innerHTML =htmltxt;
      newtr.appendChild(oCell);
      ni.appendChild(newtr);
      form.nrcomponents.value = li;
      end_date = eval( "document.editFrm.add_task_end_date_"+line_nr );
      start_date = eval( "document.editFrm.add_task_start_date_"+line_nr );
      duration_fld = eval( "document.editFrm.add_task_duration_"+line_nr );
      durntype_fld = eval( "document.editFrm.add_task_durntype_"+line_nr );
      calcDuration(document.editFrm, start_date, end_date, duration_fld, durntype_fld);
}

function removeComponent(tr_id) {
    var table_row = document.getElementById(tr_id);
    var table_row_description = document.getElementById(tr_id+'desc');
    table = table_row.parentNode;
    table.removeChild(table_row);
    table.removeChild(table_row_description);
}
</script>
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
var oldProj = "<?php echo $obj->project_name.':';?>";
</script>


<table border="0" cellpadding="0" cellspacing="0" width="100%" class="std">

<form name="frmDelete" action="./index.php?m=projects" method="post">
	<input type="hidden" name="dosql" value="do_project_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
</form>

<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">
            <table border="0" cellpadding="4" cellspacing="0" width="100%">
            <tr>
            	<td style="background-color:#<?php echo $obj->project_color_identifier;?>" colspan="1">
           	<?php
                  echo '<a href="#fp" name="fp" style="display:block" onClick="expand_colapse(\'project\', \'tblProjects\')">'
           	?>
            	<?php
            		echo '<font color="' . bestColor( $obj->project_color_identifier ) . '"><strong>'
            			. $AppUI->_('Project') . ': ' . $obj->project_name .'<strong></font>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            	<td width="12" style="background-color:#<?php echo $obj->project_color_identifier;?>" align="right" colspan="1">
           	<?php
                  echo '<a href="#fp" name="fp" style="display:block" onClick="expand_colapse(\'project\', \'tblProjects\')">'
           	?>
            	<?php
                        echo '<img id="project_expand" src="./images/icons/expand.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_project']) ? ($view_options[0]['pd_option_view_project'] ? 'style="display:none"' : 'style="display:"') : 'style="display:none"').'><img id="project_collapse" src="./images/icons/collapse.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_project']) ? ($view_options[0]['pd_option_view_project'] ? 'style="display:"' : 'style="display:none"') : 'style="display:"').'>';
            	?>
           	<?php
                  echo '</a>'
           	?>
      	</tr>
      	</table>
	</td>
</tr>
<tr id="project" <?php echo (isset($view_options[0]['pd_option_view_project']) ? ($view_options[0]['pd_option_view_project'] ? 'style="visibility:visible;display:"' : 'style="visibility:collapse;display:none"') : 'style="visibility:visible;display:"');?>>
	<?php
            if ($canReadProject) {
               require(dPgetConfig('root_dir')."/modules/projectdesigner/vw_project.php");
            } else {
                  echo $AppUI->_('You do not have permission to view tasks');
            }
	?>
</tr>
</table>
<br />
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
            	<td colspan="1">
           	<?php
                  echo '<a href="#fg" name="fg" style="display:block" onClick="expand_colapse(\'gantt\', \'tblProjects\')">'
           	?>
            	<?php
            		echo '<strong>'. $AppUI->_('Gantt Chart') .'<strong></font>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            	<td width="12" align="right" colspan="1">
           	<?php
                  echo '<a href="#fg" name="fg" style="display:block" onClick="expand_colapse(\'gantt\', \'tblProjects\')">'
           	?>
            	<?php
                        echo '<img id="gantt_expand" src="./images/icons/expand.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_gantt']) ? ($view_options[0]['pd_option_view_gantt'] ? 'style="display:none"' : 'style="display:"') : 'style="display:none"').'><img id="gantt_collapse" src="./images/icons/collapse.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_gantt']) ? ($view_options[0]['pd_option_view_gantt'] ? 'style="display:"' : 'style="display:none"') : 'style="display:"').'></a>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            </tr>
      	</table>
	</td>
</tr>
<tr id="gantt" <?php echo (isset($view_options[0]['pd_option_view_gantt']) ? ($view_options[0]['pd_option_view_gantt'] ? 'style="visibility:visible;display:"' : 'style="visibility:collapse;display:none"') : 'style="visibility:visible;display:"');?>>
	<td colspan="2" class="hilite">
	<?php
            if ($canViewTasks) {
               require(dPgetConfig('root_dir')."/modules/projectdesigner/vw_gantt.php");
            } else {
                  echo $AppUI->_('You do not have permission to view tasks');
            }
	?>
	</td>
</tr>
</table>
<br />
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
            	<td colspan="1">
           	<?php
                  echo '<a href="#ft" name="ft" style="display:block" onClick="expand_colapse(\'tasks\', \'tblProjects\')">'
           	?>
            	<?php
            		echo '<strong>'. $AppUI->_('Tasks') .'<strong></font>';
            	?>
            	</td>
           	<?php
                  echo '</a>'
           	?>
            	<td width="12" align="right" colspan="1">
           	<?php
                  echo '<a href="#ft" name="ft" style="display:block" onClick="expand_colapse(\'tasks\', \'tblProjects\')">'
           	?>
            	<?php
                        echo '<img id="tasks_expand" src="./images/icons/expand.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_tasks']) ? ($view_options[0]['pd_option_view_tasks'] ? 'style="display:none"' : 'style="display:"') : 'style="display:none"').'><img id="tasks_collapse" src="./images/icons/collapse.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_tasks']) ? ($view_options[0]['pd_option_view_tasks'] ? 'style="display:"' : 'style="display:none"') : 'style="display:"').'></a>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            </tr>
      	</table>
	</td>
</tr>
<tr id="tasks" <?php echo (isset($view_options[0]['pd_option_view_tasks']) ? ($view_options[0]['pd_option_view_tasks'] ? 'style="visibility:visible;display:"' : 'style="visibility:collapse;display:none"') : 'style="visibility:visible;display:"');?>>
	<td colspan="2" class="hilite">
	<?php
            if ($canViewTasks) {
                  require(dPgetConfig('root_dir')."/modules/projectdesigner/vw_tasks.php");
            } else {
                  echo $AppUI->_('You do not have permission to view tasks');
            }
	?>
	</td>
</tr>
</table>
<br />
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
            	<td colspan="1">
           	<?php
                  echo '<a href="#fa" name="fa" style="display:block" onClick="expand_colapse(\'actions\', \'tblProjects\')">'
           	?>
            	<?php
            		echo '<strong>'. $AppUI->_('Actions') .'<strong></font>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            	<td width="12" align="right" colspan="1">
           	<?php
                  echo '<a href="#fa" name="fa" style="display:block" onClick="expand_colapse(\'actions\', \'tblProjects\')">'
           	?>
            	<?php
                        echo '<img id="actions_expand" src="./images/icons/expand.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_actions']) ? ($view_options[0]['pd_option_view_actions'] ? 'style="display:none"' : 'style="display:"') : 'style="display:none"').'><img id="actions_collapse" src="./images/icons/collapse.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_actions']) ? ($view_options[0]['pd_option_view_actions'] ? 'style="display:"' : 'style="display:none"') : 'style="display:"').'></a>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            </tr>
      	</table>
	</td>
</tr>
<tr id="actions" <?php echo (isset($view_options[0]['pd_option_view_actions']) ? ($view_options[0]['pd_option_view_actions'] ? 'style="visibility:visible;display:"' : 'style="visibility:collapse;display:none"') : 'style="visibility:visible;display:"');?>>
	<td colspan="2" class="hilite">
	<?php
            if ($canEditTasks) {
                 require dPgetConfig('root_dir')."/modules/projectdesigner/vw_actions.php";
            } else {
                  echo $AppUI->_('You do not have permission to edit tasks');
            }
	?>
	</td>
</tr>
</table>
<br />
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
            	<td colspan="1">
           	<?php
                  echo '<a href="#fat" name="fat" style="display:block" onClick="expand_colapse(\'addtsks\', \'tblProjects\')">'
           	?>
            	<?php
            		echo '<strong>'. $AppUI->_('Add Tasks') .'<strong></font>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            	<td width="12" align="right" colspan="1">
           	<?php
                  echo '<a href="#fat" name="fat" style="display:block" onClick="expand_colapse(\'addtsks\', \'tblProjects\')">'
           	?>
            	<?php
                        echo '<img id="addtsks_expand" src="./images/icons/expand.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_addtasks']) ? ($view_options[0]['pd_option_view_addtasks'] ? 'style="display:none"' : 'style="display:"') : 'style="display:none"').'><img id="addtsks_collapse" src="./images/icons/collapse.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_addtasks']) ? ($view_options[0]['pd_option_view_addtasks'] ? 'style="display:"' : 'style="display:none"') : 'style="display:"').'></a>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            </tr>
      	</table>
	</td>
</tr>
<tr id="addtsks" <?php echo (isset($view_options[0]['pd_option_view_addtasks']) ? ($view_options[0]['pd_option_view_addtasks'] ? 'style="visibility:visible;display:"' : 'style="visibility:collapse;display:none"') : 'style="visibility:visible;display:"');?>>
	<td colspan="2" class="hilite">
	<?php
            if ($canAddTasks) {
                 require dPgetConfig('root_dir')."/modules/projectdesigner/vw_addtasks.php";
            } else {
                  echo $AppUI->_('You do not have permission to add tasks');
            }
	?>
	</td>
</tr>
</table>
<br />
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td style="border: outset #d1d1cd 1px;" colspan="2">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
            	<td colspan="1">
           	<?php
                  echo '<a href="#fbt" name="fbt" style="display:block" onClick="expand_colapse(\'files\', \'tblProjects\')">'
           	?>
            	<?php
            		echo '<strong>'. $AppUI->_('Files') .'<strong></font>';
            	?>
           	<?php
                  echo '</a>'
           	?>
            	</td>
            	<td width="12" align="right" colspan="1">
           	<?php
                  echo '<a href="#fbt" name="fbt" style="display:block" onClick="expand_colapse(\'files\', \'tblProjects\')">'
           	?>
            	<?php
                        echo '<img id="files_expand" src="./images/icons/expand.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_files']) ? ($view_options[0]['pd_option_view_files'] ? 'style="display:none"' : 'style="display:"') : 'style="display:none"').'><img id="files_collapse" src="./images/icons/collapse.gif" width="12" height="12" border="0" '.(isset($view_options[0]['pd_option_view_files']) ? ($view_options[0]['pd_option_view_files'] ? 'style="display:"' : 'style="display:none"') : 'style="display:"').'></a>';
            	?>
            	</td>
            </tr>
           	<?php
                  echo '</a>'
           	?>
      	</table>
	</td>
</tr>
<tr id="files" <?php echo (isset($view_options[0]['pd_option_view_files']) ? ($view_options[0]['pd_option_view_files'] ? 'style="visibility:visible;display:"' : 'style="visibility:collapse;display:none"') : 'style="visibility:visible;display:"');?>>
	<td colspan="2" class="hilite">
	<?php
	      //Permission check here
            $canViewFiles = $perms->checkModule( 'files', 'view');
            if ($canViewFiles) {
                 require dPgetConfig('root_dir')."/modules/projectdesigner/vw_files.php";
            } else {
                  echo $AppUI->_('You do not have permission to view files');
            }
	?>
	</td>
</tr>
</table>
<div style="display:none;">
<table class="tbl">
<tr><td id="td_sample">&nbsp;</td></tr>
</table>
</div>
<script language="javascript">
var original_bgc = getStyle('td_sample', 'background-color', 'backgroundColor');
</script>
<?php
}
?>

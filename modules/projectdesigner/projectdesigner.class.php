<?php /* PROJECTDESIGNER $Id: projectdesigner.class.php,v 1.2 2008/10/17 19:12:21 theideaman Exp $ */

//Lets require the main classes needed
include_once("./modules/projectdesigner/config.php");
require_once( $AppUI->getSystemClass( 'libmail' ) );
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getModuleClass( 'companies' ) );
require_once( $AppUI->getModuleClass( 'projects' ) );
require_once( $AppUI->getModuleClass( 'tasks' ) );

/**
* CProjectDesignerOptions Class
*/
class CProjectDesignerOptions extends CDpObject {
        var $pd_option_id = NULL;
        var $pd_option_user = NULL;
        var $pd_option_view_project = NULL;
        var $pd_option_view_gantt = NULL;
        var $pd_option_view_tasks = NULL;
        var $pd_option_view_actions = NULL;
        var $pd_option_view_addtasks = NULL;
        var $pd_option_view_files = NULL;

        function __construct() {
                parent::__construct( 'project_designer_options', 'pd_option_id' );
        }                    

        function store($updateNulls = FALSE) {
                  $q = new DBQuery;
                  $q->addTable('project_designer_options');
                  $q->addReplace('pd_option_user',$this->pd_option_user);
                  $q->addReplace('pd_option_view_project',$this->pd_option_view_project);
                  $q->addReplace('pd_option_view_gantt',$this->pd_option_view_gantt);
                  $q->addReplace('pd_option_view_tasks',$this->pd_option_view_tasks);
                  $q->addReplace('pd_option_view_actions',$this->pd_option_view_actions);
                  $q->addReplace('pd_option_view_addtasks',$this->pd_option_view_addtasks);
                  $q->addReplace('pd_option_view_files',$this->pd_option_view_files);
                  $q->addWhere('pd_option_user = '.$this->pd_option_user);
                  $q->exec();
        }                    
}

/** Retrieve tasks with first task_end_dates within given project
* @param int Project_id
* @param int SQL-limit to limit the number of returned tasks
* @return array List of criticalTasks
*/
function getCriticalTasksInverted($project_id = NULL, $limit = 1) {

      if (!$project_id) {
            $result = array();
            $result[0]['task_end_date'] = '0000-00-00 00:00:00';
            return $result;
      } else {
            $q = new DBQuery;
            $q->addTable('tasks');
            $q->addWhere("task_project = $project_id AND !isnull( task_end_date ) AND task_end_date !=  '0000-00-00 00:00:00'");
            $q->addOrder('task_start_date ASC');
            $q->setLimit($limit);
            
                return $q->loadList();
      }
}

function taskstyle_pd($task) {
	$now = new CDate();
	$start_date = intval( $task["task_start_date"] ) ? new CDate( $task["task_start_date"] ) : null;
	$end_date = intval( $task["task_end_date"] ) ? new CDate( $task["task_end_date"] ) : null;
    
	if ($start_date && !$end_date) {
        $end_date = $start_date;
        $end_date->addSeconds( @$task["task_duration"]*$task["task_duration_type"]*SEC_HOUR );
	}
	else if (!$start_date){
		return '';
    }
    
    $style = 'class=';
    if ($task['task_percent_complete'] == 0) {
        $style .= (($now->before( $start_date ))?'"task_future"':'"task_notstarted"');
    }
    else if ($task['task_percent_complete'] == 100) {
		$t = new CTask();
		$t->load($task['task_id']);
		$actual_end_date = new CDate(get_actual_end_date_pd($t->task_id,$t));
        $style .= (($actual_end_date->after($end_date))?'"task_late"':'"task_done"');
	}
    else {
        $style .= (($now->after( $end_date ))?'"task_overdue"':'"task_started"');
    }
    return $style;
}

function get_actual_end_date_pd($task_id, $task) {
  global $AppUI;
  $q = new DBQuery;
  $mods = $AppUI->getActiveModules();
  
  if (!empty($mods['history']) && !getDenyRead('history')) {
      $q->addQuery('MAX(history_date) as actual_end_date');
      $q->addTable('history');
      $q->addWhere('history_table=\'tasks\' AND history_item='.$task_id);
  }
  else {
      $q->addQuery('MAX(task_log_date) AS actual_end_date');
      $q->addTable('task_log');
      $q->addWhere('task_log_task = '.$task_id);
  }
  
  $task_log_end_date = $q->loadResult();
  
  $edate = $task_log_end_date;
  
  $edate = ($edate > $task->task_end_date || $task->task_percent_complete == 100)?$edate:$task->task_end_date;
  
  return $edate;
}

//This kludgy function echos children tasks as threads on project designer (_pd)

function showtask_pd( &$a, $level=0, $is_opened = true, $today_view = false) {
        global $AppUI, $dPconfig, $done, $query_string, $durnTypes, $userAlloc, $showEditCheckbox;
        global $task_access, $task_priority, $PROJDESIGN_CONFIG;
      
        $types = dPgetsysval('TaskType');

        $now = new CDate();
        $tf = $AppUI->getPref('TIMEFORMAT');
        $df = $AppUI->getPref('SHDATEFORMAT');
        $fdf = $df . " " . $tf;
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

        $s = "\n<tr id=\"row".$a['task_id']."\" onmouseover=\"highlight_tds(this, true, ".$a['task_id'].")\" onmouseout=\"highlight_tds(this, false, ".$a['task_id'].")\" onclick=\"select_box('selected_task', ".$a['task_id'].",'frm_tasks')\">"; // edit icon
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
/*        $pin_prefix = $a['task_pinned']?'':'un';
        $s .= "\n\t<td>";
        $s .= "\n\t\t<a href=\"?m=tasks&pin=" . ($a['task_pinned']?0:1) . "&task_id={$a['task_id']}\">"
                . "\n\t\t\t".'<img src="./images/icons/' . $pin_prefix . 'pin.gif" alt="'.$AppUI->_( $pin_prefix . 'pin Task' ).'" border="0" width="12" height="12">'
                . "\n\t\t</a>";
        $s .= "\n\t</td>";*/
// New Log
/*        if (@$a['task_log_problem']>0) {
                $s .= '<td align="center" valign="middle"><a href="?m=tasks&a=view&task_id='.$a['task_id'].'&tab=0&problem=1">';
                $s .= dPshowImage( './images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem!' );
                $s .='</a></td>';
        } else if ($canViewLog) {
                $s .= "\n\t<td><a href=\"?m=tasks&a=view&task_id=" . $a['task_id'] . '&tab=1">' . $AppUI->_('Log') . '</a></td>';
        } else {
                $s .= "\n\t<td></td>";
                                }*/
// percent complete
        $s .= "\n\t<td align=\"right\">".intval( $a["task_percent_complete"] ).'%</td>';
// priority
        $s .= "\n\t<td align='center' nowrap='nowrap'>";
	$s .= "\n\t\t<img src=\"./images/icons/priority" . ($a["task_priority"] < 0 ? '_down_' : '_up_')
	   . abs($a["task_priority"]) . '.gif" width=13 height=16>';
        $s .= @$a["file_count"] > 0 ? "<img src=\"./images/clip.png\" alt=\"F\">" : "";
        $s .= "</td>";
// access
        $s .= "\n\t<td nowrap='nowrap'>";
        $s .= '<abbr title="'.$task_access[$a['task_access']].'">'.substr($task_access[$a["task_access"]],0,3).'</abbr>';
        $s .= "</td>";
// type
        $s .= "\n\t<td nowrap='nowrap'>";
        $s .= '<abbr title="'.$types[$a['task_type']].'">'.substr($types[$a["task_type"]],0,3).'</abbr>';
        $s .= "</td>";
// type
        $s .= "\n\t<td nowrap='nowrap'>";
        $s .= $a["queue_id"] ? 'Yes' : '';
        $s .= "</td>";
// inactive
        $s .= "\n\t<td nowrap='nowrap'>";
        $s .= $a["task_status"]=='-1' ? 'Yes' : '';
        $s .= "</td>";
// add log
        $s .= "\n\t<td align='center' nowrap='nowrap'>";
        if ($a['task_dynamic'] != 1) {
              $s .= "\n\t\t<a href=\"?m=tasks&a=view&tab=1&project_id={$a['task_project']}&task_id={$a['task_id']}\">"
                  . "\n\t\t\t".'<img src="./modules/projectdesigner/images/add.png" alt="'.$AppUI->_( 'Add Work Log' ).'" title="'.$AppUI->_( 'Add Work Log' ).'" border="0" width="16" height="16">'
                  . "\n\t\t</a>";
        }
        $s .= "</td>";
// dots
        if ($today_view)
                $s .= '<td>';
        else
                $s .= '<td width="20%">';
        for ($y=0; $y < $level; $y++) {
                if ($y+1 == $level) {
                        $s .= '<img src="./images/corner-dots.gif" width="16" height="12" border="0">';
                } else {
                        $s .= '<img src="./images/shim.gif" width="16" height="12"  border="0">';
                }
        }
// name link
        $alt = strlen($a['task_description']) > intval($PROJDESIGN_CONFIG['chars_task_descriptions']) ? substr($a["task_description"],0,intval($PROJDESIGN_CONFIG['chars_task_descriptions'])) . '...' : $a['task_description'];
        // instead of the statement below
        $alt = str_replace("\"", "&quot;", $alt);
//        $alt = htmlspecialchars($alt);
        $alt = str_replace("\r", ' ', $alt);
        $alt = str_replace("\n", ' ', $alt);

        $open_link = $is_opened ? "<!--<a href='index.php$query_string&close_task_id=".$a["task_id"]."'>--><img src='images/icons/collapse.gif' border='0' align='center' /><!--</a>-->" : "<!--<a href='index.php$query_string&open_task_id=".$a["task_id"]."'>--><img src='images/icons/expand.gif' border='0' /><!--</a>-->";
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
                $s .= '<td>';
                $s .= '<a href="./index.php?m=projects&a=view&project_id=' . $a['task_project'] . '">';
                $s .= '<span style="padding:2px;background-color:' . $a['project_color_identifier'] . ';color:' . bestColor($a['project_color_identifier']) . '">' . $a['project_name'] . '</span>';
                $s .= '</a></td>';
        }
// task description
        if ($PROJDESIGN_CONFIG['show_task_descriptions']) {
                $s .= '<td align="justified">'.$a['task_description'].'</td>';
        }
// task owner
        $s .= '<td align="center">'."<a href='?m=admin&a=viewuser&user_id=".$a['user_id']."'>".$a['contact_first_name'].' '.$a['contact_last_name']."</a>".'</td>';
        if (! $today_view) {
		        $s .= '<td  id="ignore_td_'.$a['task_id'].'" nowrap="nowrap" align="center" style="'.$style.'">'.($start_date ? $start_date->format( $df.' '.$tf ) : '-').'</td>';
//		        $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($start_date ? $start_date->format( $tf ) : '-').'</td>';
        }
// duration or milestone
        $s .= '<td  id="ignore_td_'.$a['task_id'].'" align="center" nowrap="nowrap" style="'.$style.'">';
        $s .= $a['task_duration'] . ' ' . $AppUI->_( $durnTypes[$a['task_duration_type']] );
        $s .= '</td>';
        $s .= '<td id="ignore_td_'.$a['task_id'].'" nowrap="nowrap" align="center" style="'.$style.'">'.($end_date ? $end_date->format( $df.' '.$tf ) : '-').'</td>';
        if ( isset($a['task_assigned_users']) && ($assigned_users = $a['task_assigned_users'])) {
                $a_u_tmp_array = array();
                if($show_all_assignees){
                        $s .= '<td align="center">';
                        foreach ( $assigned_users as $val) {
                                //$a_u_tmp_array[] = "<A href='mailto:".$val['user_email']."'>".$val['user_username']."</A>";
                                $aInfo = "<a href='?m=admin&a=viewuser&user_id=".$val['user_id']."'";
                                $aInfo .= 'title="'.$AppUI->_('Extent of Assignment').':'.$userAlloc[$val['user_id']]['charge'].'%; '.$AppUI->_('Free Capacity').':'.$userAlloc[$val['user_id']]['freeCapacity'].'%'.'">';
                                $aInfo .= $val['contact_first_name'].' '.$val['contact_last_name']." (".$val['perc_assignment']."%)</a>";
                                $a_u_tmp_array[] = $aInfo;
                        }
                        $s .= join ( ', ', $a_u_tmp_array );
                        $s .= '</td>';
                } else {
                        $s .= '<td align="center" nowrap="nowrap">';
                        $s .= "<a href='?m=admin&a=viewuser&user_id=".$assigned_users[0]['user_id']."'";
                        $s .= 'title="'.$AppUI->_('Extent of Assignment').':'.$userAlloc[$assigned_users[0]['user_id']]['charge'].'%; '.$AppUI->_('Free Capacity').':'.$userAlloc[$assigned_users[0]['user_id']]['freeCapacity'].'%'.'">';
                        $s .= $assigned_users[0]['contact_first_name'] . ' ' . $assigned_users[0]['contact_last_name'] .' (' . $assigned_users[0]['perc_assignment'] .'%)</a>';
                        if($a['assignee_count']>1){
                        $id = $a['task_id'];
                        $s .= " <a href=\"javascript: void(0);\"  onClick=\"toggle_users('users_$id');\" title=\"" . join ( ', ', $a_u_tmp_array ) ."\">(+". ($a['assignee_count']-1) .")</a>";

                        $s .= '<span style="display: none" id="users_' . $id . '">';

                                $a_u_tmp_array[] = $assigned_users[0]['user_username'];
                                for ( $i = 1; $i < count( $assigned_users ); $i++) {
                                        $a_u_tmp_array[] = $assigned_users[$i]['user_username'];
                                        $s .= '<br /><a href="?m=admin&a=viewuser&user_id=';
                                        $s .=  $assigned_users[$i]['user_id'] . '" title="'.$AppUI->_('Extent of Assignment').':'.$userAlloc[$assigned_users[$i]['user_id']]['charge'].'%; '.$AppUI->_('Free Capacity').':'.$userAlloc[$assigned_users[$i]['user_id']]['freeCapacity'].'%'.'">';
                                        $s .= $assigned_users[$i]['contact_first_name'] . ' ' . $assigned_users[$i]['contact_last_name'] .' (' . $assigned_users[$i]['perc_assignment'] .'%)</a>';
                                }
                        $s .= '</span>';
                        }
                        $s .= '</td>';
                }
        } else if (! $today_view) {
                // No users asigned to task
                $s .= '<td align="center">-</td>';
        }

// Assignment checkbox
        if ($showEditCheckbox || $perms->checkModule( 'admin', 'view')) {
                $s .= "\n\t<td align='center'><input type=\"checkbox\" onclick=\"select_box('selected_task', ".$a['task_id'].",'frm_tasks')\" onfocus=\"is_check=true;\" onblur=\"is_check=false;\" id=\"selected_task_{$a['task_id']}\" name=\"selected_task[{$a['task_id']}]\" value=\"{$a['task_id']}\"/></td>";
        }
        $s .= '</tr>';
        echo $s;
}

function findchild_pd( &$tarr, $parent, $level=0){
        GLOBAL $projects;
        global $tasks_opened;

        $level = $level+1;
        $n = count( $tarr );

        for ($x=0; $x < $n; $x++) {
                if($tarr[$x]["task_parent"] == $parent && $tarr[$x]["task_parent"] != $tarr[$x]["task_id"]){
                    $is_opened = !empty($tasks_opened[$tarr[$x]["task_id"]]);
                        showtask_pd( $tarr[$x], $level, $is_opened );
                        if($is_opened || !$tarr[$x]["task_dynamic"]){
                            findchild_pd( $tarr, $tarr[$x]["task_id"], $level);
                        }
                }
        }
}

function get_dependencies_pd($task_id){
      // Pull tasks dependencies
      $q = new DBQuery;
      $q->addTable('tasks','t');
      $q->addTable('task_dependencies','td');
      $q->addQuery('t.task_id, t.task_name');
      $q->addWhere("td.dependencies_task_id = $task_id");
      $q->addWhere('t.task_id = td.dependencies_req_task_id');
      $sql = $q->prepare();
      $taskDep = db_loadHashList( $sql );
}

function showtask_pr( &$a, $level=0, $is_opened = true, $today_view = false) {
        global $AppUI, $dPconfig, $done, $query_string, $durnTypes, $userAlloc, $showEditCheckbox;
        global $task_access, $task_priority;
      
        $types = dPgetsysval('TaskType');

        $now = new CDate();
        $tf = $AppUI->getPref('TIMEFORMAT');
        $df = $AppUI->getPref('SHDATEFORMAT');
        $fdf = $df . " " . $tf;
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

                $days = $now->dateDiff( $end_date ) * $sign;
        }

        $s = "\n<tr>";

// dots
        if ($today_view)
                $s .= '<td nowrap>';
        else
                $s .= '<td nowrap width="20%">';
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
        $alt = str_replace("\r", ' ', $alt);
        $alt = str_replace("\n", ' ', $alt);

        $open_link = $is_opened ? "<!--<a href='index.php$query_string&close_task_id=".$a["task_id"]."'>--><img src='images/icons/collapse.gif' border='0' align='center' /><!--</a>-->" : "<!--<a href='index.php$query_string&open_task_id=".$a["task_id"]."'>--><img src='images/icons/expand.gif' border='0' /><!--</a>-->";
        if ($a["task_milestone"] > 0 ) {
                $s .= '&nbsp;<!--<a href="./index.php?m=tasks&a=view&task_id=' . $a["task_id"] . '" title="' . $alt . '">--><b>' . $a["task_name"] . '</b><!--</a>--> <img src="./images/icons/milestone.gif" border="0"></td>';
        } else if ($a["task_dynamic"] == '1'){
                if (! $today_view)
                        $s .= $open_link;

                $s .=  $a["task_name"];
        } else {
                $s .= $a["task_name"];
        }
// percent complete
        $s .= "\n\t<td align=\"right\">".intval( $a["task_percent_complete"] ).'%</td>';
        if ($today_view) { // Show the project name
                $s .= '<td>';
                $s .= '<a href="./index.php?m=projects&a=view&project_id=' . $a['task_project'] . '">';
                $s .= '<span style="padding:2px;background-color:' . $a['project_color_identifier'] . ';color:' . bestColor($a['project_color_identifier']) . '">' . $a['project_name'] . '</span>';
                $s .= '</a></td>';
        }
        if (! $today_view) {
		        $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($start_date ? $start_date->format( $df.' '.$tf ) : '-').'</td>';
        }
        $s .= '</td>';
        $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($end_date ? $end_date->format( $df.' '.$tf ) : '-').'</td>';
        $s .= '</td>';
        $s .= '<td nowrap="nowrap" align="center" style="'.$style.'">'.($last_update ? $last_update->format( $df.' '.$tf ) : '-').'</td>';
        echo $s;
}

function findchild_pr( &$tarr, $parent, $level=0){
        GLOBAL $projects;
        global $tasks_opened;

        $level = $level+1;
        $n = count( $tarr );

        for ($x=0; $x < $n; $x++) {
                if($tarr[$x]["task_parent"] == $parent && $tarr[$x]["task_parent"] != $tarr[$x]["task_id"]){
                    $is_opened = !empty($tasks_opened[$tarr[$x]["task_id"]]);
                        showtask_pr( $tarr[$x], $level, $is_opened );
                        if($is_opened || !$tarr[$x]["task_dynamic"]){
                            findchild_pr( $tarr, $tarr[$x]["task_id"], $level);
                        }
                }
        }
}
?>

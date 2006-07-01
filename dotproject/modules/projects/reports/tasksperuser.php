<?php

$do_report 		    = dPgetParam( $_POST, "do_report", 0 );
$log_start_date 	= dPgetParam( $_POST, "log_start_date", 0 );
$log_end_date 	    = dPgetParam( $_POST, "log_end_date", 0 );
$log_all		    = dPgetParam($_POST["log_all"], 0);
$use_period			= dPgetParam($_POST,"use_period",0); 
$display_week_hours	= dPgetParam($_POST,"display_week_hours",0); 
$max_levels        	= dPgetParam($_POST,"max_levels","max"); 
$log_userfilter		= dPgetParam($_POST,"log_userfilter","");


$table_header = "";
$table_rows="";

// create Date objects from the datetime fields
$start_date = intval( $log_start_date ) ? new CDate( $log_start_date ) : new CDate();
$end_date   = intval( $log_end_date )   ? new CDate( $log_end_date ) : new CDate();

if (!$log_start_date) {
	$start_date->subtractSpan( new Date_Span( "14,0,0,0" ) );
}
$end_date->setTime( 23, 59, 59 );
?>

<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scollbars=false' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.log_' + calendarField );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}
</script>

<form name="editFrm" action="index.php?m=projects&a=reports" method="post">
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">

<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_start_date" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format( $df );?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td nowrap="nowrap">
        <SELECT NAME="log_userfilter" CLASS="text" STYLE="width: 200px">
  
	   	 	<?php
   		   	  $usersql = "
   		   	  SELECT user_id, user_username, contact_first_name, contact_last_name
   		   	  FROM users
                        LEFT JOIN contacts ON contact_id = user_contact
			  ORDER by contact_last_name,contact_first_name
   		   	  ";
 			 
   		   	  if ( $log_userfilter == 0 ) echo '<OPTION VALUE="0" SELECTED>'.$AppUI->_('All users' );
   		   	  else echo '<OPTION VALUE="0">All users';
			 
   		   	  if (($log_userfilter_users = db_loadList( $usersql, NULL )))
   		   	  {
   		   	      foreach ($log_userfilter_users as $row)
   		   	      {
					  $selected="";
   		   	          if ( $log_userfilter == $row["user_id"]) { $selected=" SELECTED"; }
					  echo "<OPTION VALUE='".$row["user_id"]."'$selected>".
                                    $row["contact_first_name"]." ".$row["contact_last_name"];
   		   	      }
   		   	  }
		
		    ?>
	
   	     </SELECT>

	</td>

	<td nowrap="nowrap">
		<input type="checkbox" name="use_period" <?php if ($use_period) echo "checked" ?> >
		<?php echo $AppUI->_( 'Use the period' );?>
		</input>
		<br>
		<input type="checkbox" name="display_week_hours" <?php if ($display_week_hours) echo "checked" ?> >
		<?php echo $AppUI->_( 'Display allocated hours/week' );?>
		</input>
		<br> 

	</td> 
	
	<td align="right" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to:');?></td>
	<td>
		<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td>
		<?php echo $AppUI->_( 'Levels to display' ); ?>
		<input type="text" name="max_levels" size="10" maxlength="3" <?php $max_levels ?> >
		</input>
	</td>

</tr>

</table>
</form>

<?php
if($do_report){

	// Let's figure out which users we have
	$sql = "SELECT  u.user_id,
	 				u.user_username, 
					contact_first_name, 

					contact_last_name
	        FROM users AS u
                LEFT JOIN contacts ON contact_id = user_contact";

	if ($log_userfilter!=0) {
			$sql.=" WHERE user_id=".
						  $log_userfilter
					      ;//$log_userfilter_users[$log_userfilter]["user_id"];
	}
	$sql.=" ORDER by contact_last_name, contact_first_name";
	
	$user_list = db_loadHashList($sql, "user_id");

	$ss="'".$start_date->format( FMT_DATETIME_MYSQL )."'";
	$se="'".$end_date->format( FMT_DATETIME_MYSQL )."'";

	$and=false;
	$where=false;

	$sql = 	 "SELECT t.* "
			."FROM tasks AS t "
			."LEFT JOIN projects on project_id = task_project ";

	if ($use_period) {
		if (!$where) { $sql.=" WHERE ";$where=true; }
		$sql.=" ( "
			."  ( task_start_date >= $ss AND task_start_date <= $se ) "
			." OR "
			."  ( task_end_date <= $se AND task_end_date >= $ss ) "
			." ) ";
		$and=true;
	}
	        //AND !isnull(task_end_date) AND task_end_date != '0000-00-00 00:00:00'
	        //AND !isnull(task_start_date) AND task_start_date != '0000-00-00 00:00:00';
	        //AND task_dynamic   ='0'
	        //AND task_milestone = '0'
	        //AND task_duration  > 0";
			//;

	if($project_id != 0){
		if (!$where) { $sql.=" WHERE ";$where=true; }
		if ($and) {
			$sql .= " AND ";
		}
		$sql.=" task_project='$project_id' ";
		$and = true;
	}

	$proj =& new CProject;
	$obj =& new CTask;
	$allowedProjects = $proj->getAllowedSQL($AppUI->user_id, 'task_project');
	$allowedTasks = $obj->getAllowedSQL($AppUI->user_id);

	if (count($allowedProjects)) {
		if (!$where) { $sql.=" WHERE ";$where=true; }
		if ($and) $sql .= " AND ";
		$and = true;
		$sql .= implode(" AND ", $allowedProjects);
	}

	if (count($allowedTasks)) {
		if (!$where) { $sql.=" WHERE ";$where=true; }
		if ($and) $sql .= " AND ";
		$and = true;
		$sql .= implode(" AND ", $allowedTasks);
 	}
 
	$sql .= " ORDER BY task_end_date;";

	$task_list_hash 	 = db_loadHashList($sql, "task_id");
	$task_list      	 = array();
	$task_assigned_users = array();
	$i = 0;
	foreach($task_list_hash as $task_id => $task_data){
		$task = new CTask();
		$task->bind($task_data);
		$task_list[$i] = $task;
		$task_assigned_users[$i] = $task->getAssignedUsers();
		$i+=1;
	}
	$Ntasks=$i;

	//for($i=0;$i<$Ntasks;$i++) {
		//print $task_list[$i]->task_name."<br>\n";
	//}
	
	$user_usage            = array();
	$task_dates            = array();
	
	$actual_date = $start_date;
	$days_header = ""; // we will save days title here

	if (strtolower($max_levels)=="max") {
		$max_levels=-1;
	}
	elseif ($max_levels=="") {
		$max_levels=-1;
	}
	else {
		$max_levels=atoi($max_levels);
	}
	if ($max_levels==0) { $max_levels=1; }
	if ($max_levels<0) { $max_levels=-1; }
	
	if ( count($task_list) == 0 ) {
		echo "<p>" . $AppUI->_( 'No data available' ) ."</p>";
	} else {

		$sss=$ss;$sse=$se;
		if (!$use_period) {	$sss=-1; $sse=-1; }
		if ($display_week_hours and !$use_period) { 
			foreach($task_list as $t) {
				if ($sss==-1) {
					$sss=$t->task_start_date;
					$sse=$t->task_end_date;
				}
				else {
					if ($t->task_start_date<$sss) { $sss=$t->task_start_date; }
					if ($t->task_end_date>$sse) { $sse=$t->task_end_date; }
				}
			}
		}
	
		$table_header = '
			<tr>
				<td nowrap="nowrap" bgcolor="#A0A0A0">
				<font color="black"><b>'.$AppUI->_('Task').'</b></font> </td>'.
				( $project_id == 0 ? '<td nowrap="nowrap" bgcolor="#A0A0A0"><font color="black"><b>'.$AppUI->_('Project').'</b></font></td>' : '' ) . '
				<td nowrap="nowrap" bgcolor="#A0A0A0"><font color="black"><b>'.$AppUI->_('Start Date').'</b></font></td>
				<td nowrap="nowrap" bgcolor="#A0A0A0"><font color="black"><b>'.$AppUI->_('End Date').'</b></font></td>'.
		weekDates($display_week_hours,$sss,$sse).'
			</tr>';
		$table_rows = '';
		
		foreach($user_list as $user_id => $user_data){

			$tmpuser= "<tr><td align='left' nowrap='nowrap' bgcolor='#D0D0D0'><font color='black'><B>"
					  .$user_data["contact_first_name"]
				      ." "
					  .$user_data['contact_last_name']
					  .'</b></font>
	</td>';
		    for($w=0;$w<=(1 + ($project_id == 0 ? 1 : 0) + weekCells($display_week_hours,$sss,$sse));$w++) {
				 $tmpuser.='<td bgcolor="#D0D0D0">&nbsp;</td>';
			}
			$tmpuser.='</tr>';

			$tmptasks="";
			$actual_date = $start_date;
			foreach($task_list as $task) {
				if (!isChildTask($task)) {
					if (isMemberOfTask($task_list,$task_assigned_users,$Ntasks,$user_id,$task)) {
						$tmptasks.=displayTask($task_list,$task,0,$display_week_hours,$sss,$sse, !$project_id);
						// Get children
						$tmptasks.=doChildren($task_list,$task_assigned_users,$Ntasks,
											  $task->task_id,$user_id,
											  1,$max_levels,$display_week_hours,$sss,$sse, !$project_id);
					}
				}
			}
			if ($tmptasks != "") {
				$table_rows.=$tmpuser;
				$table_rows.=$tmptasks;
			}
		}
	}
}

function doChildren($list,$Lusers,$N,$id,$uid,$level,$maxlevels,$display_week_hours,$ss,$se, $log_all_projects = false) {
	$tmp="";
	if ($maxlevels==-1 || $level<$maxlevels) {
		for($c=0;$c<$N;$c++) {
			$task=$list[$c];
			if (($task->task_parent==$id) and isChildTask($task)) {
				// we have a child, do we have the user as a member?
				if (isMemberOfTask($list,$Lusers,$N,$uid,$task)) {
					$tmp.=displayTask($list,$task,$level,$display_week_hours,$ss,$se, $log_all_projects);
					$tmp.=doChildren($list,$Lusers,$N,$task->task_id,
                                     $uid,$level+1,$maxlevels,
                                     $display_week_hours,$ss,$se, $log_all_projects);
				}
			}
		}
	}
return $tmp;
}

function isMemberOfTask($list,$Lusers,$N,$user_id,$task) {

	for($i=0;$i<$N && $list[$i]->task_id!=$task->task_id;$i++);
	$users=$Lusers[$i];

	//$users=$Lusers[$task->getAssignedUsers();
	foreach($users as $task_user_id => $user_data) {
		if ($task_user_id==$user_id) { return true; }
	}

	// check child tasks if any

	for($c=0;$c<$N;$c++) {
		$ntask=$list[$c];
		if (($ntask->task_parent==$task->task_id) and isChildTask($ntask)) {
			// we have a child task
			if (isMemberOfTask($list,$Lusers,$N,$user_id,$ntask)) {
				return true;
			}
		}
	}
return false;
}

function displayTask($list,$task,$level,$display_week_hours,$fromPeriod,$toPeriod, $log_all_projects = false) {
	$tmp="";
	$tmp.="<tr><td nowrap=\"nowrap\">&#160&#160&#160";
	for($i=0;$i<$level;$i++) {
		$tmp.="&#160&#160&#160";
	}
	if ($level==0) { $tmp.="<B>"; }
	elseif ($level==1) { $tmp.="<I>"; }
	$tmp.=$task->task_name;
	if ($level==0) { $tmp.="</B>"; }
	elseif ($level==1) { $tmp.="</I>"; }
	$tmp.="&#160&#160&#160</td>";
	if ( $log_all_projects ) {	
		//Show project name when we are logging all projects
		$project = $task->getProject();
		$tmp .= "<td nowrap=\"nowrap\">";
		if ( !isChildTask($task) ) {
			//However only show the name on parent tasks and not the children to make it a bit cleaner
			$tmp.= $project["project_name"];
		}
		$tmp .= "</td>";
	}
	$tmp.="<td nowrap=\"nowrap\">";
	$dt=new CDate($task->task_start_date);
	$tmp.=$dt->format("%d-%m-%Y");
	$tmp.="&#160&#160&#160</td>";
	$tmp.="<td nowrap=\"nowrap\">";
	$dt=new CDate($task->task_end_date);
	$tmp.=$dt->format("%d-%m-%Y");
	$tmp.="</td>";
	if ($display_week_hours) {
		$tmp.=displayWeeks($list,$task,$level,$fromPeriod,$toPeriod);
	}
	$tmp.="</tr>\n";
return $tmp;
}

function isChildTask($task) {
	return $task->task_id!=$task->task_parent;
}

function atoi($a) {
	return $a+0;
}

function weekDates($display_allocated_hours,$fromPeriod,$toPeriod) {
	if ($fromPeriod==-1) { return ""; }
	if (!$display_allocated_hours) { return ""; }

	$s=new CDate($fromPeriod);
	$e=new CDate($toPeriod);
	$sw=getBeginWeek($s);
	$ew=getEndWeek($e); //intval($e->Format("%U"));

	$row="";
	for($i=$sw;$i<=$ew;$i++) {
		$row.="<td nowrap=\"nowrap\" bgcolor='#A0A0A0'><font color='black'><B>".$s->format("%d-%m")."</B></font></td>";
		$s->addSeconds(168*3600);	// + one week
	}
return $row;
}

function weekCells($display_allocated_hours,$fromPeriod,$toPeriod) {

	if ($fromPeriod==-1) { return 0; }
	if (!$display_allocated_hours) { return 0; }
	
	
	$s=new CDate($fromPeriod);
	$e=new CDate($toPeriod);
	$sw=getBeginWeek($s); //intval($s->Format("%U"));
	$ew=getEndWeek($e); //intval($e->Format("%U"));

return $ew-$sw+1;
}



// Look for a user when he/she has been allocated
// to this task and when. Report this in weeks
// This function is called within 'displayTask()'
function displayWeeks($list,$task,$level,$fromPeriod,$toPeriod) {

	if ($fromPeriod==-1) { return ""; }

	$s=new CDate($fromPeriod);
	$e=new CDate($toPeriod);
	$sw=getBeginWeek($s); 	//intval($s->Format("%U"));
	$ew=getEndWeek($e); //intval($e->Format("%U"));

	$st=new CDate($task->task_start_date);
	$et=new CDate($task->task_end_date);
	$stw=getBeginWeek($st); //intval($st->Format("%U"));
	$etw=getEndWeek($et); //intval($et->Format("%U"));

	//print "week from: $stw, to: $etw<br>\n";

	$row="";
	for($i=$sw;$i<=$ew;$i++) {
		if ($i>=$stw and $i<$etw) {
			$color="blue";
			if ($level==0 and hasChildren($list,$task)) { $color="#C0C0FF"; }
			else if ($level==1 and hasChildren($list,$task)) { $color="#9090FF"; }
			$row.="<td  nowrap=\"nowrap\" bgcolor=\"$color\">";
		}
		else {
			$row.="<td nowrap=\"nowrap\">";
		}
		$row.="&#160&#160</td>";
	}

return $row;
}

function getBeginWeek($d) {
	$dn=intval($d->Format("%w"));
	$dd=new CDate($d);
	$dd->subtractSeconds($dn*24*3600);
	return intval($dd->Format("%U"));
}

function getEndWeek($d) {
	$dn=intval($d->Format("%w"));
	if ($dn>0) { $dn=7-$dn; }
	$dd=new CDate($d);
	$dd->addSeconds($dn*24*3600);
	return intval($dd->Format("%U"));
}

function hasChildren($list,$task) {
	foreach($list as $t) {
		if ($t->task_parent==$task->task_id) { return true; }
	}
return false;
}

?>

<center>
	<table class="std">
		<?php echo $table_header . $table_rows; ?>
	</table>
</center>


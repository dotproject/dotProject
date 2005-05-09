<?php /* PROJECTS $Id$ */
$project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );

// check permissions for this record
$perms =& $AppUI->acl();
$canRead = $perms->checkModuleItem( $m, 'view', $project_id );
$canEdit = $perms->checkModuleItem( $m, 'edit', $project_id );

if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// retrieve any state parameters
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'ProjVwTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'ProjVwTab' ) !== NULL ? $AppUI->getState( 'ProjVwTab' ) : 0;

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CProject();
// Now check if the proect is editable/viewable.
$denied = $obj->getDeniedRecords($AppUI->user_id);
if (in_array($project_id, $denied)) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$canDelete = $obj->canDelete( $msg, $project_id );

// get critical tasks (criteria: task_end_date)
$criticalTasks = ($project_id > 0) ? $obj->getCriticalTasks($project_id) : NULL;

// get ProjectPriority from sysvals
$projectPriority = dPgetSysVal( 'ProjectPriority' );
$projectPriorityColor = dPgetSysVal( 'ProjectPriorityColor' );

$working_hours = $dPconfig['daily_working_hours'];

// load the record data
// GJB: Note that we have to special case duration type 24 and this refers to the hours in a day, NOT 24 hours
$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery("company_name,
	CONCAT_WS(' ',contact_first_name,contact_last_name) user_name,
	projects.*,
	SUM(t1.task_duration * t1.task_percent_complete * IF(t1.task_duration_type = 24, ".$working_hours.", t1.task_duration_type))/
		SUM(t1.task_duration * IF(t1.task_duration_type = 24, ".$working_hours.", t1.task_duration_type)) AS project_percent_complete");
$q->addJoin('companies', 'com', 'company_id = project_company');
$q->addJoin('users', 'u', 'user_id = project_owner');
$q->addJoin('contacts', 'con', 'contact_id = user_contact');
$q->addJoin('tasks', 't1', 'projects.project_id = t1.task_project');
$q->addWhere('project_id = '.$project_id);
$q->addGroup('project_id');
$sql = $q->prepare();
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
$q->addTable('task_log');
$q->addTable('tasks');
$q->addQuery('ROUND(SUM(task_log_hours),2)');
$q->addWhere("task_log_task = task_id AND task_project = $project_id");
$sql = $q->prepare();
$q->clear();
$worked_hours = db_loadResult($sql);
$worked_hours = rtrim($worked_hours, "0");

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

$total_project_hours = db_loadResult($total_project_days_sql) * $dPconfig['daily_working_hours'] + db_loadResult($total_project_hours_sql);
//due to the round above, we don't want to print decimals unless they really exist
//$total_project_hours = rtrim($total_project_hours, "0");

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

// create Date objects from the datetime fields
$start_date = intval( $obj->project_start_date ) ? new CDate( $obj->project_start_date ) : null;
$end_date = intval( $obj->project_end_date ) ? new CDate( $obj->project_end_date ) : null;
$actual_end_date = intval( $criticalTasks[0]['task_end_date'] ) ? new CDate( $criticalTasks[0]['task_end_date'] ) : null;
$style = (( $actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

// setup the title block
$titleBlock = new CTitleBlock( 'View Project', 'applet3-48.png', $m, "$m.$a" );

// patch 2.12.04 text to search entry box
if (isset( $_POST['searchtext'] )) {
	$AppUI->setState( 'searchtext', $_POST['searchtext']);
}

$search_text = $AppUI->getState('searchtext') ? $AppUI->getState('searchtext'):'';
$titleBlock->addCell( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AppUI->_('Search') . ':' );
$titleBlock->addCell(
	'<input type="text" class="text" SIZE="10" name="searchtext" onChange="document.searchfilter.submit();" value=' . "'$search_text'" .
	'title="'. $AppUI->_('Search in name and description fields') . '"/>
       	<!--<input type="submit" class="button" value=">" title="'. $AppUI->_('Search in name and description fields') . '"/>-->', '',
	'<form action="?m=projects&a=view&project_id='.$project_id.'" method="post" id="searchfilter">', '</form>'
);

if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new task').'">', '',
		'<form action="?m=tasks&a=addedit&task_project=' . $project_id . '" method="post">', '</form>'
	);
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
		'<form action="?m=files&a=addedit&project_id=' . $project_id . '" method="post">', '</form>'
	);
}
$titleBlock->addCrumb( "?m=projects", "projects list" );
if ($canEdit) {
	$titleBlock->addCrumb( "?m=projects&a=addedit&project_id=$project_id", "edit this project" );
	if ($canDelete) {
		$titleBlock->addCrumbDelete( 'delete project', $canDelete, $msg );
	}
	$titleBlock->addCrumb("?m=tasks&a=organize&project_id=$project_id", "organize tasks");
}
$titleBlock->addCrumb( "?m=projects&a=reports&project_id=$project_id", "reports" );
$titleBlock->show();
?>
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

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<form name="frmDelete" action="./index.php?m=projects" method="post">
	<input type="hidden" name="dosql" value="do_project_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
</form>

<tr>
	<td style="border: outset #d1d1cd 1px;background-color:#<?php echo $obj->project_color_identifier;?>" colspan="2">
	<?php
		echo '<font color="' . bestColor( $obj->project_color_identifier ) . '"><strong>'
			. $obj->project_name .'<strong></font>';
	?>
	</td>
</tr>

<tr>
	<td width="50%" valign="top">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Company');?>:</td>
			<td class="hilite" width="100%"><?php echo htmlspecialchars( $obj->company_name, ENT_QUOTES) ;?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Short Name');?>:</td>
			<td class="hilite"><?php echo htmlspecialchars( @$obj->project_short_name, ENT_QUOTES) ;?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Start Date');?>:</td>
			<td class="hilite"><?php echo $start_date ? $start_date->format( $df ) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Target End Date');?>:</td>
			<td class="hilite"><?php echo $end_date ? $end_date->format( $df ) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Actual End Date');?>:</td>
			<td class="hilite">
                                 <?php if ($project_id > 0) { ?>
                                        <?php echo $actual_end_date ? '<a href="?m=tasks&a=view&task_id='.$criticalTasks[0]['task_id'].'">' : '';?>
                                        <?php echo $actual_end_date ? '<span '. $style.'>'.$actual_end_date->format( $df ).'</span>' : '-';?>
                                        <?php echo $actual_end_date ? '</a>' : '';?>
                                 <?php } else { echo $AppUI->_('Dynamically calculated');} ?>
                        </td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Target Budget');?>:</td>
			<td class="hilite"><?php echo $dPconfig['currency_symbol'] ?><?php echo @$obj->project_target_budget;?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Project Owner');?>:</td>
			<td class="hilite"><?php echo $obj->user_name; ?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('URL');?>:</td>
			<td class="hilite"><a href="<?php echo @$obj->project_url;?>" target="_new"><?php echo @$obj->project_url;?></A></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Staging URL');?>:</td>
			<td class="hilite"><a href="<?php echo @$obj->project_demo_url;?>" target="_new"><?php echo @$obj->project_demo_url;?></a></td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
				require_once("./classes/CustomFields.class.php");
				$custom_fields = New CustomFields( $m, $a, $obj->project_id, "view" );
				$custom_fields->printHTML();
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<strong><?php echo $AppUI->_('Description');?></strong><br />
			<table cellspacing="0" cellpadding="2" border="0" width="100%">
			<tr>
				<td class="hilite">
					<?php echo str_replace( chr(10), "<br>", $obj->project_description) ; ?>&nbsp;
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
	</td>
	<td width="50%" rowspan="9" valign="top">
		<strong><?php echo $AppUI->_('Summary');?></strong><br />
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Status');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($pstatus[$obj->project_status]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Priority');?>:</td>
			<td class="hilite" width="100%" style="background-color:<?php echo $projectPriorityColor[$obj->project_priority]?>"><?php echo $AppUI->_($projectPriority[$obj->project_priority]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Type');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($ptype[$obj->project_type]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Progress');?>:</td>
			<td class="hilite" width="100%"><?php printf( "%.1f%%", $obj->project_percent_complete );?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Active');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->project_active ? $AppUI->_('Yes') : $AppUI->_('No');?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Worked Hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $worked_hours ?></td>
		</tr>	
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Scheduled Hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $total_hours ?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Project Hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $total_project_hours ?></td>
		</tr>				
		<?php
		$q  = new DBQuery;
		$q->addTable('departments', 'a');
		$q->addTable('project_departments', 'b');
		$q->addQuery('a.dept_id, a.dept_name, a.dept_phone');
		$q->addWhere("a.dept_id = b.department_id and b.project_id = $project_id");
		$depts = $q->loadHashList("dept_id");
		if (count($depts) > 0) {
		?>
		    <tr>
		    	<td><strong><?php echo $AppUI->_("Departments"); ?></strong></td>
		    </tr>
		    <tr>
		    	<td colspan='3' class="hilite">
		    		<?php
		    			foreach($depts as $dept_id => $dept_info){
		    				echo "<div>".$dept_info["dept_name"];
		    				if($dept_info["dept_phone"] != ""){
		    					echo "( ".$dept_info["dept_phone"]." )";
		    				}
		    				echo "</div>";
		    			}
		    		?>
		    	</td>
		    </tr>
	 		<?php
		}
		
			$q  = new DBQuery;
			$q->addTable('contacts', 'a');
			$q->addTable('project_contacts', 'b');
			$q->addQuery('a.contact_id, a.contact_first_name, a.contact_last_name,
					a.contact_email, a.contact_phone, a.contact_department');
			$q->addWhere("a.contact_id = b.contact_id and b.project_id = $project_id
					and (contact_owner = '$AppUI->user_id' or contact_private='0')");

			$contacts = $q->loadHashList("contact_id");
			if(count($contacts)>0){
				?>
			    <tr>
			    	<td><strong><?php echo $AppUI->_("Contacts"); ?></strong></td>
			    </tr>
			    <tr>
			    	<td colspan='3' class="hilite">
			    		<?php
			    			echo "<table cellspacing='1' cellpadding='2' border='0' width='100%' bgcolor='black'>";
			    			echo "<tr><th>".$AppUI->_("Name")."</th><th>".$AppUI->_("Email")."</th><th>".$AppUI->_("Phone")."</th><th>".$AppUI->_("Department")."</th></tr>";
			    			foreach($contacts as $contact_id => $contact_data){
			    				echo "<tr>";
			    				echo "<td class='hilite'>";
							$canEdit = $perms->checkModuleItem('contacts', 'edit', $contact_id);
							if ($canEdit)
								echo "<a href='index.php?m=contacts&a=view&contact_id=$contact_id'>";
							echo $contact_data["contact_first_name"]." ".$contact_data["contact_last_name"];
							if ($canEdit)
								echo "</a>";
							echo "</td>";
			    				echo "<td class='hilite'><a href='mailto: ".$contact_data["contact_email"]."'>".$contact_data["contact_email"]."</a></td>";
			    				echo "<td class='hilite'>".$contact_data["contact_phone"]."</td>";
			    				echo "<td class='hilite'>".$contact_data["contact_department"]."</td>";
			    				echo "</tr>";
			    			}
			    			echo "</table>";
			    		?>
			    	</td>
			    </tr>
			    <tr>
			    	<td>
		 <?php
		}?>
		</table>
	</td>
</table>

<?php
$tabBox = new CTabBox( "?m=projects&a=view&project_id=$project_id", "", $tab );
$query_string = "?m=projects&a=view&project_id=$project_id";
// tabbed information boxes
// Note that we now control these based upon module requirements.
$canViewTask = $perms->checkModule('tasks', 'view');
if ($canViewTask) {
	$tabBox->add( dPgetConfig('root_dir')."/modules/tasks/tasks", 'Tasks' );
	$tabBox->add( dPgetConfig('root_dir')."/modules/tasks/tasks", 'Tasks (Inactive)' );
}
if ($perms->checkModule('forums', 'view'))
	$tabBox->add( dPgetConfig('root_dir')."/modules/projects/vw_forums", 'Forums' );
//if ($perms->checkModule('files', 'view'))
//	$tabBox->add( dPgetConfig('root_dir')."/modules/projects/vw_files", 'Files' );
if ($canViewTask) {
	$tabBox->add( dPgetConfig('root_dir')."/modules/tasks/viewgantt", 'Gantt Chart' );
	$tabBox->add( dPgetConfig('root_dir')."/modules/projects/vw_logs", 'Task Logs' );
}
$tabBox->loadExtras($m);
$f = 'all';
$min_view = true;

$tabBox->show();
?>

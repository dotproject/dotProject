<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
* Generates a report of the task logs for given dates
*/
//error_reporting( E_ALL );
$do_report = dPgetParam( $_POST, "do_report", 0 );
$log_all = dPgetParam( $_POST, 'log_all', 0 );
$log_pdf = dPgetParam( $_POST, 'log_pdf', 0 );
$log_ignore = dPgetParam( $_POST, 'log_ignore', 0 );
$days = dPgetParam( $_POST, 'days', 30 );

$list_start_date = dPgetParam( $_POST, "list_start_date", 0 );
$list_end_date = dPgetParam( $_POST, "list_end_date", 0 );

$period = dPgetParam($_POST, "period", 0);
$period_value = dPgetParam($_POST, "pvalue", 1);
if ($period)
{
  $today = new CDate();
  $ts = $today->format(FMT_TIMESTAMP_DATE);
        if (strtok($period, " ") == $AppUI->_("Next"))
                $sign = +1;
        else //if(...)
                $sign = -1;

        $day_word = strtok(" ");
        if ($day_word == $AppUI->_("Day"))
                $days = $period_value;
        else if ($day_word == $AppUI->_("Week"))
                $days = 7*$period_value;
        else if ($day_word == $AppUI->_("Month"))
                $days = 30*$period_value;

        $start_date = new CDate($ts);
        $end_date = new CDate($ts);

        if ($sign > 0)
                $end_date->addSpan( new Date_Span("$days,0,0,0") );
        else
                $start_date->subtractSpan( new Date_Span("$days,0,0,0") );

        $do_report = 1;
        
}
else
{
// create Date objects from the datetime fields
        $start_date = intval( $list_start_date ) ? new CDate( $list_start_date ) : new CDate();
        $end_date = intval( $list_end_date ) ? new CDate( $list_end_date ) : new CDate();
}


if (!$list_start_date) {
	$start_date->subtractSpan( new Date_Span( "14,0,0,0" ) );
}
$end_date->setTime( 23, 59, 59 );

?>
<script language="javascript">

var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.list_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.list_' + calendarField );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

</script>

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">

<form name="editFrm" action="index.php?m=projects&a=reports" method="post">
	<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
	<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<tr>
        <td align="right"><?php echo $AppUI->_('Default Actions'); ?>:</td>
        <td nowrap="nowrap" colspan="2">
          <input class="button" type="submit" name="period" value="<?php echo $AppUI->_('Previous Month'); ?>" />
          <input class="button" type="submit" name="period" value="<?php echo $AppUI->_('Previous Week'); ?>" />
          <input class="button" type="submit" name="period" value="<?php echo $AppUI->_('Previous Day'); ?>" />
        </td>
        <td nowrap="nowrap">
          <input class="button" type="submit" name="period" value="<?php echo $AppUI->_('Next Day'); ?>" />
          <input class="button" type="submit" name="period" value="<?php echo $AppUI->_('Next Week'); ?>" />
          <input class="button" type="submit" name="period" value="<?php echo $AppUI->_('Next Month'); ?>" />
          </td>
        <td colspan="3"><input class="text" type="field" size="2" name="pvalue" value="1" /> - value for the previous buttons</td>
<!--
        <td><input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('Previous Month'); ?>" onClick="set(-30)" /></td>
        <td><input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('Previous Week'); ?>" onClick="set(-7)" /></td>
        <td><input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('Next Week'); ?>" onClick="set(7)" /></td>
        <td><input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('Next Month'); ?>" onClick="set(30)" /></td>
-->
</tr>
<tr>

	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="list_start_date" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format( $df );?>" class="text" disabled="disabled" />
		<a href="#" onclick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to');?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="list_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" />
		<a href="#" onclick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>

	<td nowrap="nowrap">
		<input type="checkbox" name="log_all" id="log_all" <?php if ($log_all) echo 'checked="checked"' ?> />
		<label for="log_all"><?php echo $AppUI->_( 'Log All' );?></label>
	</td>
	<td nowrap="nowrap">
		<input type="checkbox" name="log_pdf" id="log_pdf" <?php if ($log_pdf) echo 'checked="checked"' ?> />
		<label for="log_pdf"><?php echo $AppUI->_( 'Make PDF' );?></label>
	</td>

	<td align="right" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>
</form>
</table>

<?php

if ($do_report) {
	
	if ($project_id == 0){ 
		$q = new DBQuery;
		$q->addTable('tasks', 'a');
		$q->addTable('projects', 'b');
		$q->addQuery('a.*, b.project_name');
		$q->addWhere('a.task_project = b.project_id');
	} else {
		$q = new DBQuery;
		$q->addTable('tasks', 'a');
		$q->addWhere('task_project ='.$project_id);
	}
	if (!$log_all) {
		$q->addWhere("task_start_date >= '".$start_date->format( FMT_DATETIME_MYSQL )."'");
		$q->addWhere("task_start_date <= '".$end_date->format( FMT_DATETIME_MYSQL )."'");
	}

	$obj =& new CTask;
	$allowedTasks = $obj->getAllowedSQL($AppUI->user_id);
	if (count($allowedTasks)) {
		$obj->getAllowedSQL($AppUI->user_id, $q);
	}
	$q->addOrder('project_start_date', 'task_project', 'task_parent', 'task_start_date');
	$Task_List = $q->exec();

	echo "<table cellspacing=\"1\" cellpadding=\"4\" border=\"0\" class=\"tbl\">";
	if ($project_id==0) { echo "<tr><th>Project Name</th><th>Task Name</th>";} else {echo "<tr><th>Task Name</th>";}
	echo "<th width=400>Task Description</th>";
	echo "<th>Assigned To</th>";
	echo "<th>Task Start Date</th>";
	echo "<th>Task End Date</th>";
	echo "<th>Completion</th></tr>";
	
	$pdfdata = array();
	$columns = array(	
	"<b>".$AppUI->_('Task Name')."</b>",
	"<b>".$AppUI->_('Task Description')."</b>",
	"<b>".$AppUI->_('Assigned To')."</b>",
	"<b>".$AppUI->_('Task Start Date')."</b>",
	"<b>".$AppUI->_('Task End Date')."</b>",
	"<b>".$AppUI->_('Completion')."</b>"
	);
	if ($project_id==0) { array_unshift($columns, "<b>".$AppUI->_('Project Name')."</b>");}		
	while ($Tasks = db_fetch_assoc($Task_List)){
		$start_date = intval($Tasks['task_start_date']) ? new CDate( $Tasks['task_start_date'] ) : ' ';
		$end_date = intval($Tasks['task_end_date']) ? new CDate( $Tasks['task_end_date'] ) : ' ';
		$task_id = $Tasks['task_id'];
		
		$q = new DBQuery;
		$q->addTable('user_tasks');
		$q->addWhere('task_id = '.$task_id);
		$sql_user = $q->exec();
		
		
		$users = null;
		while ($Task_User = db_fetch_assoc($sql_user)){
			//$current_user = $Task_User['user_id'];
			if ($users!=null){
				$users.=", ";
			}

			$q->clear();
			$q = new DBQuery;
			$q->addTable('users', 'u');
			$q->addTable('contacts', 'c');
			$q->addQuery('contact_first_name, contact_last_name');
			$q->addWhere('u.user_contact = c.contact_id');
			$q->addWhere('user_id = '.$Task_User['user_id']);

			$sql_user_array = $q->exec();
			$q->clear();
			$user_list = db_fetch_assoc($sql_user_array);
			$users .= $user_list['contact_first_name']." ".$user_list['contact_last_name'];
		}
		$str =  "<tr>";
		if ($project_id==0) {$str .= "<td>".$Tasks['project_name']."</td>";}
		$str .= "<td><a href='?m=tasks&a=view&task_id=".$Tasks['task_id']. "'>".$Tasks['task_name']."</a></td>";
		$str .= "<td>".$Tasks['task_description']."</td>";
		$str .= "<td>".$users."</td>";
		$str .= "<td>";
		($start_date != ' ') ? $str .= $start_date->format( $df )."</td>" : $str .= ' '."</td>";			
		$str .= "<td>";		
		($end_date != ' ') ? $str .= $end_date->format( $df )."</td>" : $str .= ' '."</td>";
		$str .= "<td align=\"center\">".$Tasks['task_percent_complete']."%</td>";
		$str .= "</tr>";
		echo $str;
		if ($project_id==0) {	
		$pdfdata[] = array(
			$Tasks['project_name'],
			$Tasks['task_name'],
			$Tasks['task_description'],
			$users,
			(($start_date != ' ') ? $start_date->format( $df ) : ' '),
			(($end_date != ' ') ? $end_date->format( $df ) : ' '),
			$Tasks['task_percent_complete']."%",);
			}
		else {
			$pdfdata[] = array(
			$Tasks['task_name'],
			$Tasks['task_description'],
			$users,
			(($start_date != ' ') ? $start_date->format( $df ) : ' '),
			(($end_date != ' ') ? $end_date->format( $df ) : ' '),
			$Tasks['task_percent_complete']."%",
			);
		}		
	}
	echo "</table>";
if ($log_pdf) {
	// make the PDF file
		$q = new DBQuery;
		$q->addTable('projects');
		$q->addQuery('project_name');
		$q->addWhere('project_id='.(int)$project_id);
		$pname = $q->loadResult();

		$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
		$temp_dir = DP_BASE_DIR.'/files/temp';
		
		require( $AppUI->getLibraryClass( 'ezpdf/class.ezpdf' ) );

		$pdf =& new Cezpdf($paper='A4',$orientation='landscape');
		$pdf->ezSetCmMargins( 1, 2, 1.5, 1.5 );
		$pdf->selectFont( "$font_dir/Helvetica.afm" );

		$pdf->ezText( dPgetConfig( 'company_name' ), 12 );
		// $pdf->ezText( dPgetConfig( 'company_name' ).' :: '.dPgetConfig( 'page_title' ), 12 );		

		$date = new CDate();
		$pdf->ezText( "\n" . $date->format( $df ) , 8 );

		$pdf->selectFont( "$font_dir/Helvetica-Bold.afm" );
		$pdf->ezText( "\n" . $AppUI->_('Project Task Report'), 12 );
		if ($project_id != 0) {$pdf->ezText( $pname, 15 );}
		if ($log_all) {
			$pdf->ezText( "All task entries", 9 );
		} else {		
			if($end_date != ' ') {$pdf->ezText( "Task entries from ".$start_date->format( $df ).' to '.$end_date->format( $df ), 9 );} else {$pdf->ezText( "Task entries from ".$start_date->format( $df ),9);}
		}
		$pdf->ezText( "\n" );
		$pdf->selectFont( "$font_dir/Helvetica.afm" );
		//$columns = null; This is already defined above... :)
		$title = null;
		$options = array(
			'showLines' => 2,
			'showHeadings' => 1,
			'fontSize' => 9,
			'rowGap' => 4,
			'colGap' => 5,
			'xPos' => 50,
			'xOrientation' => 'right',
			'width'=>'750',
			'shaded'=> 0,
			'cols'=>array(0=>array('justification'=>'left','width'=>150),
					2=>array('justification'=>'left','width'=>95),
					3=>array('justification'=>'center','width'=>75),
					4=>array('justification'=>'center','width'=>75),
					5=>array('justification'=>'center','width'=>75))
		);

		$pdf->ezTable( $pdfdata, $columns, $title, $options );

		if ($fp = fopen( $temp_dir.'/temp'.$AppUI->user_id.'.pdf', 'wb' )) {
			fwrite( $fp, $pdf->ezOutput() );
			fclose( $fp );
			echo '<a href="'.DP_BASE_URL.'/files/temp/temp'.$AppUI->user_id.'.pdf" target="pdf">';
			echo $AppUI->_( "View PDF File" );
			echo "</a>";
		} else {
			echo "Could not open file to save PDF.  ";
			if (!is_writable( $temp_dir )) {
				"The files/temp directory is not writable.  Check your file system permissions.";
			}
		}
	}
}
?>
</table>

<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
* Generates a report of the task logs for given dates
*/
$do_report = dPgetParam( $_POST, "do_report", 0 );
$log_pdf = dPgetParam( $_POST, 'log_pdf', 0 );

$log_start_date = dPgetParam( $_POST, "log_start_date", 0 );
$log_end_date = dPgetParam( $_POST, "log_end_date", 0 );
$log_all = dPgetParam( $_POST, 'log_all', 0 );

// create Date objects from the datetime fields
$start_date = intval( $log_start_date ) ? new CDate( $log_start_date ) : new CDate();
$end_date = intval( $log_end_date ) ? new CDate( $log_end_date ) : new CDate();

if (!$log_start_date) {
	$start_date->subtractSpan( new Date_Span( "14,0,0,0" ) );
}
$end_date->setTime( 23, 59, 59 );

$fullaccess = ($AppUI->user_type == 1);
?>
<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=250, height=220, scrollbars=no' );
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

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">

<form name="editFrm" action="index.php?m=projects&a=reports" method="post">
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_start_date" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format( $df );?>" class="text" disabled="disabled" style="width: 80px" />
		<a href="#" onClick="popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to');?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" style="width: 80px"/>
		<a href="#" onClick="popCalendar('end_date')">
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
$allpdfdata = array();
function showcompany($company, $restricted = false)
{
	global $AppUI, $allpdfdata, $log_start_date, $log_end_date, $log_all;
       /* $sql="
        SELECT
                billingcode_id,
                billingcode_name,
                billingcode_value
        FROM billingcode
        WHERE company_id=$company
        ORDER BY billingcode_name ASC
        ";
                                                                                                                  
        $company_billingcodes=NULL;
        $ptrc=db_exec($sql);
        $nums=db_num_rows($ptrc);
        echo db_error();
                                                                                                                         
        for ($x=0; $x < $nums; $x++) {
                $row=db_fetch_assoc($ptrc);
                $company_billingcodes[$row['billingcode_id']]=$row['billingcode_name'];
        }
*/
	$sql = "SELECT project_id, project_name
		FROM projects
		WHERE project_company = $company";

	$projects = db_loadHashList($sql);
  
	$sql = "SELECT company_name
		FROM companies
		WHERE company_id = $company";
	$company_name = db_loadResult($sql);                                                                                                                       

        $table = '<h2>Company: ' . $company_name . '</h2>
        <table cellspacing="1" cellpadding="4" border="0" class="tbl">';
	$project_row = '
        <tr>
                <th>' . $AppUI->_('Project') . '</th>';
                
		$pdfth[] = $AppUI->_('Project');
/*		if (isset($company_billingcodes))
	                foreach ($company_billingcodes as $code)
			{
        	                $project_row .= '<th>' . $code . ' ' . $AppUI->_('Hours') . '</th>';
				$pdfth[] = $code;
			}
  */              
        $project_row .= '<th>' . $AppUI->_('Total') . '</th></tr>';
	$pdfth[] = $AppUI->_('Total');
	$pdfdata[] = $pdfth;
        
        $hours = 0.0;
	$table .= $project_row;

        foreach ($projects as $project => $name)
        {
		$pdfproject = array();
		$pdfproject[] = $name;
		$project_hours = 0;
		$project_row = "<tr><td>$name</td>";
		$sql = "SELECT task_log_costcode, sum(task_log_hours) as hours
			FROM projects, tasks, task_log
			WHERE project_id = $project";
		if ($log_start_date != 0 && !$log_all)
			$sql .= " AND task_log_date >= $log_start_date";
		if ($log_end_date != 0 && !$log_all)
			$sql .= " AND task_log_date <= $log_end_date";
		if ($restricted)
			$sql .= " AND task_log_creator = '" . $AppUI->user_id . "'";
			
		$sql .= " AND project_id = task_project
			AND task_id = task_log_task
			GROUP BY project_id"; //task_log_costcode";

		$task_logs = db_loadHashList($sql);

/*		if (isset($company_billingcodes))
		foreach($company_billingcodes as $code => $name)
		{
			if (isset($task_logs[$code]))
			{
				$value = sprintf( "%.2f", $task_logs[$code] );
				$project_row .= '<td>' . $value . '</td>';
				$project_hours += $task_logs[$code];
				$pdfproject[] = $value;
			}
			else
			{
				$project_row .= '<td>&nbsp;</td>';
				$pdfproject[] = 0;
			}
		}
*/
                foreach($task_logs as $task_log)
                        $project_hours += $task_log;
		$project_row .= '<td>' . round($project_hours, 2) . '</td></tr>';
		$pdfproject[]=round($project_hours, 2);
		$hours += $project_hours;
		if ($project_hours > 0)
		{
			$table .= $project_row;
			$pdfdata[] = $pdfproject;
		}
        }
	if ($hours > 0)
	{
		$allpdfdata[$company_name] = $pdfdata;
	
		echo $table;
		echo '<tr><td>Total</td><td>' . round($hours, 2) . '</td></tr></table>';
	}


	return $hours;
}

if ($do_report) {

	$total = 0;

if ($fullaccess)
	$sql = "SELECT company_id FROM companies";
else
	$sql = "SELECT company_id FROM companies WHERE company_owner='" . $AppUI->user_id . "'";

$companies = db_loadColumn($sql);

if (!empty($companies))	
	foreach ($companies as $company)
		$total += showcompany($company);
else
{
	$sql = "SELECT company_id FROM companies";
	foreach(db_loadColumn($sql) as $company)
		$total += showcompany($company, true);
}

	

echo '<h2>' . $AppUI->_('Total Hours') . ":"; 
printf( "%.2f", $total );
echo '</h2>';


if ($log_pdf) {
	// make the PDF file

		$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
		$temp_dir = DP_BASE_DIR.'/files/temp';
		
		require( $AppUI->getLibraryClass( 'ezpdf/class.ezpdf' ) );

		$pdf =& new Cezpdf();
		$pdf->ezSetCmMargins( 1, 2, 1.5, 1.5 );
		$pdf->selectFont( "$font_dir/Helvetica.afm" );

		$pdf->ezText( dPgetConfig( 'company_name' ), 12 );
		// $pdf->ezText( dPgetConfig( 'company_name' ).' :: '.$AppUI->getConfig( 'page_title' ), 12 );		

		if ($log_all)
		{
			$date = new CDate();
			$pdf->ezText( "\nAll hours as of " . $date->format( $df ) , 8 );
		}
		else
		{
			$sdate = new CDate($log_start_date);
			$edate = new CDate($log_end_date);
			$pdf->ezText( "\nHours from " . $sdate->format( $df ) .  " to " . $edate->format( $df ), 8);
		}

		$pdf->selectFont( "$font_dir/Helvetica-Bold.afm" );
		$pdf->ezText( "\n" . $AppUI->_('Overall Report'), 12 );

	foreach($allpdfdata as $company => $data)
	{
		$title = $company;
		$options = array(
			'showLines' => 1,
			'showHeadings' => 0,
			'fontSize' => 8,
			'rowGap' => 2,
			'colGap' => 5,
			'xPos' => 50,
			'xOrientation' => 'right',
			'width'=>'500'
		);

		$pdf->ezTable( $data, NULL, $title, $options );
	}
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

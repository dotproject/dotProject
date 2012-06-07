<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
* Generates a report of the task logs for given dates
*/
if (!(getPermission('task_log', 'view'))) {
	redirect('m=public&a=access_denied');
}	
$do_report = dPgetParam($_GET, "do_report", '');
$log_all = (int)dPgetParam($_GET, 'log_all', 0);
$log_pdf = (int)dPgetParam($_GET, 'log_pdf', 0);
$log_ignore = (int)dPgetParam($_GET, 'log_ignore', 0);
$log_userfilter = (int)dPgetParam($_GET, 'log_userfilter', '0');

$log_start_date = dPgetCleanParam($_GET, "log_start_date", 0);
$log_end_date = dPgetCleanParam($_GET, "log_end_date", 0);

// create Date objects from the datetime fields
$start_date = intval($log_start_date) ? new CDate($log_start_date) : new CDate();
$end_date = intval($log_end_date) ? new CDate($log_end_date) : new CDate();

if (!$log_start_date) {
	$start_date->subtractSpan(new Date_Span("14,0,0,0"));
}
$end_date->setTime(23, 59, 59);

?>
<script language="javascript">
var calendarField = '';

function popCalendar(field) {
	calendarField = field;
	idate = eval('document.editFrm.log_' + field + '.value');
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no, status=no');
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar(idate, fdate) {
	fld_date = eval('document.editFrm.log_' + calendarField);
	fld_fdate = eval('document.editFrm.' + calendarField);
	fld_date.value = idate;
	fld_fdate.value = fdate;
}
</script>

<form name="editFrm" action="" method="GET">
<input type="hidden" name="m" value="projects" />
<input type="hidden" name="a" value="reports" />
<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
<input type="hidden" name="report_type" value="<?php echo $report_type;?>" />

<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('For period');?>:</td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_start_date" value="<?php echo $start_date->format(FMT_TIMESTAMP_DATE);?>" />
		<input type="text" name="start_date" value="<?php echo $start_date->format($df);?>" class="text" disabled="disabled" style="width: 80px" />
		<a href="#" onclick="javascript:popCalendar('start_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('to');?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : '';?>" />
		<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format($df) : '';?>" class="text" disabled="disabled" style="width: 80px"/>
		<a href="#" onclick="popCalendar('end_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>

	<td nowrap="nowrap">
		<?php echo $AppUI->_('User');?>:
		<select name="log_userfilter" class="text" style="width: 80px">

	<?php
		$q = new DBQuery;
		$q->addQuery('user_id, user_username, contact_first_name, contact_last_name');
		$q->addTable('users', 'u');
		$q->leftJoin('contacts', 'c', 'u.user_contact = c.contact_id');
		$q->addOrder('u.user_username');

		if ($log_userfilter == 0) {
			echo '<option value="0" selected="selected">'.$AppUI->_('All users');
		} else {
			echo '<option value="0">All users';
		}

		if (($rows = $q->loadList())) {
			foreach ($rows as $row) {
				if ($log_userfilter == $row["user_id"])
					echo "<option value='".$row["user_id"]."' selected='selected'>".$row["user_username"];
				else
					echo "<option value='".$row["user_id"]."'>".$row["user_username"];
			}
		}

	?>

		</select>
	</td>

	<td nowrap="nowrap">
		<input type="checkbox" name="log_all" id="log_all" <?php if ($log_all) echo 'checked="checked"' ?> />
		<label for="log_all"><?php echo $AppUI->_('Log All');?></label>
	</td>

	<td nowrap="nowrap">
		<input type="checkbox" name="log_pdf" id="log_pdf" <?php if ($log_pdf) echo 'checked="checked"' ?> />
		<label for="log_pdf"><?php echo $AppUI->_('Make PDF');?></label>
	</td>

	<td nowrap="nowrap">
		<input type="checkbox" name="log_ignore" id="log_ignore" />
		<label for="log_ignore"><?php echo $AppUI->_('Ignore 0 hours');?></label>
	</td>

	<td align="right" width="50%" nowrap="nowrap">
		<input class="button" type="submit" name="do_report" value="<?php echo $AppUI->_('submit');?>" />
	</td>
</tr>
</table>
</form>

<?php
if ($do_report) {

	$q = new DBQuery;
	$q->addQuery('p.project_id, p.project_name, t.*, 
		CONCAT_WS(\' \',contact_first_name,contact_last_name) AS creator,
		if (bc.billingcode_name is null, \'\', bc.billingcode_name) as billingcode_name');
	$q->addTable('task_log', 't');
	$q->leftJoin('billingcode','bc', 'bc.billingcode_id = t.task_log_costcode');
	$q->leftJoin('users', 'u', 'user_id = task_log_creator');
	$q->leftJoin('contacts', 'c', 'u.user_contact = contact_id');
	$q->innerJoin('tasks', 'tsk', 't.task_log_task = tsk.task_id');
	$q->leftJoin('projects', 'p', 'p.project_id = task_project');
	if ($project_id != 0) {
		$q->addWhere('task_project = ' . (int)$project_id);
	}
	if (!$log_all) {
		$q->addWhere('task_log_date >= \''.$start_date->format(FMT_DATETIME_MYSQL).'\'');
		$q->addWhere('task_log_date <= \''.$end_date->format(FMT_DATETIME_MYSQL)."'");
	}
	if ($log_ignore) {
		$q->addWhere('task_log_hours > 0');
	}
	if ($log_userfilter) {
		$q->addWhere('task_log_creator = ' . (int)$log_userfilter);
	}

	$proj = new CProject;
	$allowedProjects = $proj->getAllowedSQL($AppUI->user_id, 'task_project');
	if (count($allowedProjects)) {
		$q->addWhere($allowedProjects);
	}

	$obj = new CTask;
	$allowedTasks = $obj->getAllowedSQL($AppUI->user_id, 'tsk.task_id');
	if (count($allowedTasks)) {
		$q->addWhere($allowedTasks);
	}
	$allowedChildrenTasks = $obj->getAllowedSQL($AppUI->user_id, 'tsk.task_parent');
	if (count($allowedChildrenTasks)) {
		$q->addWhere($allowedChildrenTasks);
	}

	$q->addOrder('task_log_date');

	//echo "<pre>$sql</pre>";

	$logs = $q->loadList();
	echo db_error();
?>
	<table cellspacing="1" cellpadding="4" border="0" class="tbl" summary"project task summary">
	<tr>
		<th nowrap="nowrap"><?php echo $AppUI->_('Created by');?></th>
		<?php if ($project_id == 0) { ?>
			<th><?php echo $AppUI->_('Project');?></th>
		<?php } ?>
		<th><?php echo $AppUI->_('Summary');?></th>
		<th><?php echo $AppUI->_('Description');?></th>
		<th><?php echo $AppUI->_('Date');?></th>
		<th><?php echo $AppUI->_('Hours');?></th>
		<th><?php echo $AppUI->_('Cost Code');?></th>
	</tr>
<?php
	$hours = 0.0;
	$pdfdata = array();

        foreach ($logs as $log) {
		$date = new CDate($log['task_log_date']);
		$hours += $log['task_log_hours'];

		$pdfdata[] = array(
			safe_utf8_decode($log['creator']),
			safe_utf8_decode($log['task_log_name']),
			safe_utf8_decode($log['task_log_description']),
			$date->format($df),
			sprintf("%.2f", $log['task_log_hours']),
			safe_utf8_decode($log['billingcode_name']),
		);
?>
	<tr>
		<td><?php echo $log['creator'];?></td>
		<?php if ($project_id == 0) { ?>
			<td><a href="?m=projects&amp;a=view&amp;project_id=<?php echo $log['project_id']; ?>"><?php echo $log['project_name'] ?></a></td>
		<?php } ?>
		<td>
			<a href="?m=tasks&amp;a=view&amp;tab=1&amp;task_id=<?php echo $log['task_log_task'];?>&amp;task_log_id=<?php echo $log['task_log_id'];?>"><?php echo $log['task_log_name'];?></a>
		</td>
		<td><?php
      $transbrk = "\n[translation]\n";
			$descrip = str_replace("\n", "<br />", $log['task_log_description']);
			$tranpos = mb_strpos($descrip, str_replace("\n", "<br />", $transbrk));
			if ($tranpos === false) {
				echo $descrip;
			} else {
				$descrip = mb_substr($descrip, 0, $tranpos);
				$tranpos = mb_strpos($log['task_log_description'], $transbrk);
				$transla = mb_substr($log['task_log_description'], $tranpos + mb_strlen($transbrk));
				$transla = trim(str_replace("'", '"', $transla));
				echo $descrip."<div style='font-weight: bold; text-align: right'><a title='$transla' class='hilite'>[".$AppUI->_("translation")."]</a></div>";
			}
// dylan_cuthbert; auto-translation end
			?></td>
		<td><?php echo $date->format($df);?></td>
		<td align="right"><?php printf("%.2f", $log['task_log_hours']);?></td>
		<td><?php echo $log['billingcode_name'];?></td>
	</tr>
<?php
	}
	$pdfdata[] = array(
		'',
		'',
		'',
		safe_utf8_decode($AppUI->_('Total Hours')).':',
		sprintf("%.2f", $hours),
		'',
	);
?>
	<tr>
		<?php if ($project_id == 0) { ?>
			<td></td>
		<?php } ?>
		<td align="right" colspan="4"><?php echo $AppUI->_('Total Hours');?>:</td>
		<td align="right"><?php printf("%.2f", $hours);?></td>
	</tr>
	</table>
<?php
	if ($log_pdf) {
	// make the PDF file
		if ($project_id != 0) {
			$q = new DBQuery;
			$q->addTable('projects');
			$q->addQuery('project_name');
			$q->addWhere('project_id=' . $project_id);
			$pname = $q->loadResult();
		} else {
			$pname = "All Projects";
		}
		echo db_error();

		$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
		$temp_dir = DP_BASE_DIR.'/files/temp';
		
		require($AppUI->getLibraryClass('ezpdf/class.ezpdf'));

		$pdf = new Cezpdf();
		$pdf->ezSetCmMargins(1, 2, 1.5, 1.5);
		$pdf->selectFont("$font_dir/Helvetica.afm");

		$pdf->ezText(safe_utf8_decode(dPgetConfig('company_name')), 12);

		$date = new CDate();
		$pdf->ezText("\n" . $date->format($df) , 8);

		$pdf->selectFont("$font_dir/Helvetica-Bold.afm");
		$pdf->ezText("\n" . safe_utf8_decode($AppUI->_('Task Log Report')), 12);
		$pdf->ezText(safe_utf8_decode($pname), 15);
		if ($log_all) {
			$pdf->ezText("All task log entries", 9);
		} else {
			$pdf->ezText("Task log entries from ".$start_date->format($df).' to '.$end_date->format($df), 9);
		}
		$pdf->ezText("\n\n");

		$title = 'Task Logs';

	        $pdfheaders = array(
		        safe_utf8_decode($AppUI->_('Created by')),
        		safe_utf8_decode($AppUI->_('Summary')),
        		safe_utf8_decode($AppUI->_('Description')),
        		safe_utf8_decode($AppUI->_('Date')),
        		safe_utf8_decode($AppUI->_('Hours')),
	        	safe_utf8_decode($AppUI->_('Cost Code'))
        	);

        	$options = array(
			'showLines' => 0,
			'fontSize' => 12,
			'rowGap' => 2,
			'colGap' => 5,
			'xPos' => 50,
			'xOrientation' => 'right',
			'width' => 500,
			);
	        $pdfheaderdata[] = array (
	                                  '',
	                                  '',
	                                  '',
	                                  '',
	                                  '',
	                                  '',
	                                );
		$pdf->ezTable($pdfheaderdata,$pdfheaders,$title,$options);
			 
	        $options['col_options'] = array(
			                        2 => array('width' => 250),
			                        3 => array('width' => 55),
			                        4 => array('width' => 30),
			                        5 => array('width' => 30),
			                      );
	        $options['showHeadings'] = 0;
	        $options['showLines'] = 1;
	        $options['fontSize'] = 8;
	        
		$pdf->ezTable($pdfdata,'','',$options);

		if ($fp = fopen($temp_dir.'/temp'.$AppUI->user_id.'.pdf', 'wb')) {
			fwrite($fp, $pdf->ezOutput());
			fclose($fp);
			echo '<a href="'.DP_BASE_URL.'/files/temp/temp'.$AppUI->user_id.'.pdf" target="pdf">';
			echo $AppUI->_("View PDF File");
			echo "</a>";
		} else {
			echo "Could not open file to save PDF.  ";
			if (!is_writable($temp_dir)) {
				"The files/temp directory is not writable.  Check your file system permissions.";
			}
		}
	}
}
?>

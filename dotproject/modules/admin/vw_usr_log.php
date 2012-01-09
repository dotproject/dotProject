<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

?>
<script language="javascript" type="text/javascript">
var calendarField = '';
var calWin = null;


function popCalendar(field) {
	calendarField = field;
	idate = eval('document.frmDate.log_' + field + '.value');
	window.open('?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scrollbars=no, status=no');
}

function setCalendar(idate, fdate) {
	fld_date = eval('document.frmDate.log_' + calendarField);
	fld_fdate = eval('document.frmDate.' + calendarField);
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function checkDate() {
           if (document.frmDate.log_start_date.value == "" || document.frmDate.log_end_date.value== "") {
                alert("<?php echo $AppUI->_('You must fill fields', UI_OUTPUT_JS) ?>");
                return false;
           } 
           return true;
}
</script>

<?php
$date_reg = date("Y-m-d");
$start_date = intval($date_reg) ? new CDate(dPgetCleanParam($_POST, "log_start_date", date("Y-m-d"))) : null;
$end_date = intval($date_reg) ? new CDate(dPgetCleanParam($_POST, "log_end_date", date("Y-m-d"))) : null;

$df = $AppUI->getPref('SHDATEFORMAT');
global $currentTabId;
if ($a = dPgetCleanParam($_REQUEST, "a", "") == "") {
    $a = "&tab={$currentTabId}&amp;showdetails=1";
} else {
    $user_id = intval(dPgetParam($_REQUEST, "user_id", 0));
    $a = "&amp;a=viewuser&amp;user_id={$user_id}&amp;tab={$currentTabId}&amp;showdetails=1";
}

?>

<table align="center">
	<tr>
		<td>
			<h1><?php echo $AppUI->_('User Log');?></h1>
		</td>
	</tr>
</table>

<form action="?m=admin<?php echo $a; ?>" method="post" name="frmDate">
<table align="center" width="100%">
	<tr align="center">
		<td align="right" width="45%" ><?php echo $AppUI->_('Start Date');?></td>
			<td width="55%" align="left">
				<input type="hidden" name="log_start_date" value="<?php echo $start_date ? $start_date->format(FMT_TIMESTAMP_DATE) : "" ;?>" />
				<input type="text" name="start_date" value="<?php echo $start_date ? $start_date->format($df) : "" ;?>" class="text" readonly="readonly" disabled="disabled" />
				<a href="#" onclick="javascript:popCalendar('start_date')">
				<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" ></a>
			</td>
	</tr>
	<tr align="center">
		<td align="right" width="45%"><?php echo $AppUI->_('End Date');?></td>
			<td width="55%" align="left">
				<input type="hidden" name="log_end_date" value="<?php echo $end_date ? $end_date->format(FMT_TIMESTAMP_DATE) : '';?>" />
				<input type="text" name="end_date" value="<?php echo $end_date ? $end_date->format($df) : '';?>" class="text" readonly="readonly" disabled="disabled" />
				<a href="#" onclick="javascript:popCalendar('end_date')">
				<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0"></a>
		</td>
	</tr>
</table>
<table align="center">
	<tr align="center">
		<td><input type="submit" class="button" value="<?php echo $AppUI->_('Submit');?>" onclick="javascript:return checkDate('start','end')"></td>
	</tr>
</table>
</form>

<?php 
if (dPgetParam($_REQUEST, "showdetails", 0) == 1) {  
    $start_date = date("Y-m-d", strtotime(dPgetCleanParam($_POST, "log_start_date", date("Y-m-d"))));
    $end_date   = date("Y-m-d 23:59:59", strtotime(dPgetCleanParam($_POST, "log_end_date", date("Y-m-d"))));
    
    	$q  = new DBQuery;
	$q->addTable('user_access_log', 'ual');
	$q->addTable('users', 'u');
	$q->addTable('contacts', 'c');
	$q->addQuery('ual.*, u.*, c.*');
	$q->addWhere('ual.user_id = u.user_id');
	$q->addWhere('user_contact = contact_id ');
	if ($user_id != 0) { $q->addWhere("ual.user_id='$user_id'"); }
	$q->addWhere("ual.date_time_in >='$start_date'");
	$q->addWhere("ual.date_time_out <='$end_date'");
	$q->addGroup('ual.date_time_last_action DESC');
	$logs = $q->loadList();
?>
<table align="center" class="tbl" width="50%">
<tr>
	<th nowrap="nowrap" ><?php echo $AppUI->_('Name(s)');?></th>
	<th nowrap="nowrap" ><?php echo $AppUI->_('Last Name');?></th>
	<th nowrap="nowrap" ><?php echo $AppUI->_('Internet Address');?></th>
	<th nowrap="nowrap" ><?php echo $AppUI->_('Date Time IN');?></th>
	<th nowrap="nowrap" ><?php echo $AppUI->_('Date Time OUT');?></th>
</tr>
<?php foreach ($logs as $detail) {?>
	<tr>
		<td align="center"><?php echo $detail["contact_first_name"];?></td>
		<td align="center"><?php echo $detail["contact_last_name"];?></td>
		<td align="center"><?php echo $detail["user_ip"];?></td>
		<td align="center"><?php echo $detail["date_time_in"];?></td>
		<td align="center"><?php echo $detail["date_time_out"];?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>


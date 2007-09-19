<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

global $AppUI,$company_id, $dept_ids, $department, $min_view, $m, $a, $user_id, $tab;
ini_set('memory_limit', $dPconfig['reset_memory_limit']);

$min_view = defVal( @$min_view, false);
$project_id = defVal( @$_GET['project_id'], 0);
$user_id = defVal( @$_GET['user_id'], $AppUI->user_id);
// sdate and edate passed as unix time stamps
$sdate = dPgetParam( $_POST, 'sdate', 0 );
$edate = dPgetParam( $_POST, 'edate', 0 );
$showInactive = dPgetParam( $_POST, 'showInactive', '0' );
$showLabels = dPgetParam( $_POST, 'showLabels', '0' );
$sortTasksByName = dPgetParam( $_POST, 'sortTasksByName', '0' );
$showAllGantt = dPgetParam( $_POST, 'showAllGantt', '0' );
$showTaskGantt = dPgetParam( $_POST, 'showTaskGantt', '0' );
$addPwOiD = dPgetParam($_POST, 'add_pwoid', isset($addPwOiD) ? $addPwOiD : 0);

//if set GantChart includes user labels as captions of every GantBar
if ($showLabels!='0') {
	$showLabels='1';
}
if ($showInactive!='0') {
	$showInactive='1';
}

if ($showAllGantt!='0') {
	$showAllGantt='1';
}

$projectStatus = dPgetSysVal( 'ProjectStatus' );

if (isset(  $_POST['proFilter'] )) {
	$AppUI->setState( 'ProjectIdxFilter',  $_POST['proFilter'] );
}
$proFilter = $AppUI->getState( 'ProjectIdxFilter' ) !== NULL ? $AppUI->getState( 'ProjectIdxFilter' ) : '-1';

$projFilter = arrayMerge( array('-1' => 'All Projects'), $projectStatus);
$projFilter = arrayMerge( array( '-2' => 'All w/o in progress'), $projFilter);
if ($AppUI->user_id == $user_id) {
	$projFilter = arrayMerge( array( '-3' => 'My projects'), $projFilter);
} else {
	$projFilter = arrayMerge( array( '-3' => 'User\'s projects'), $projFilter);
}

natsort($projFilter);


// months to scroll
$scroll_date = 1;

$display_option = dPgetParam( $_POST, 'display_option', 'this_month' );

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

if ($display_option == 'custom') {
	// custom dates
	$start_date = intval( $sdate ) ? new CDate( $sdate ) : new CDate();
	$end_date = intval( $edate ) ? new CDate( $edate ) : new CDate();
} else {
	// month
	$start_date = new CDate();
	$start_date->day = 1;
   	$end_date = new CDate($start_date);
    $end_date->addMonths( $scroll_date );
}

// setup the title block
if (!@$min_view) {
	$titleBlock = new CTitleBlock( 'Gantt Chart', 'applet-48.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=$m", "projects list" );
	$titleBlock->show();
}

?>

<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.' + calendarField );
	fld_fdate = eval( 'document.editFrm.show_' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function scrollPrev() {
	f = document.editFrm;
<?php
	$new_start = new CDate($start_date);	
	$new_start->day = 1;
	$new_end = new CDate($end_date);
	$new_start->addMonths( -$scroll_date );
	$new_end->addMonths( -$scroll_date );

	echo "f.sdate.value='".$new_start->format( FMT_TIMESTAMP_DATE )."';";
	echo "f.edate.value='".$new_end->format( FMT_TIMESTAMP_DATE )."';";
?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function scrollNext() {
	f = document.editFrm;
<?php
	$new_start = new CDate($start_date);
	$new_start->day = 1;
	$new_end = new CDate($end_date);	
	$new_start->addMonths( $scroll_date );
	$new_end->addMonths( $scroll_date );
	echo "f.sdate.value='" . $new_start->format( FMT_TIMESTAMP_DATE ) . "';";
	echo "f.edate.value='" . $new_end->format( FMT_TIMESTAMP_DATE ) . "';";
?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function showThisMonth() {
	document.editFrm.display_option.value = "this_month";
	document.editFrm.submit();
}

function showFullProject() {
	document.editFrm.display_option.value = "all";
	document.editFrm.submit();
}

</script>
<table class="tbl" width="100%" border="0" cellpadding="4" cellspacing="0">
<tr>
        <td>
                <table border="0" cellpadding="4" cellspacing="0" class="tbl">

                <form name="editFrm" method="post" action="?<?php echo 'm='.$m.'&a='.$a. (isset($user_id) ? '&user_id='.$user_id : '').'&tab='.$tab;?>">
                <input type="hidden" name="display_option" value="<?php echo $display_option;?>" />

                <tr>
                        <td align="left" valign="top" width="20">
                <?php if ($display_option != "all") { ?>
                                <a href="javascript:scrollPrev()">
                                        <img src="./images/prev.gif" width="16" height="16" alt="<?php echo $AppUI->_( 'previous' );?>" border="0">
                                </a>
                <?php } ?>
                        </td>

                        <td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'From' );?>:</td>
                        <td align="left" nowrap="nowrap">
                                <input type="hidden" name="sdate" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
                                <input type="text" class="text" name="show_sdate" value="<?php echo $start_date->format( $df );?>" size="12" disabled="disabled" />
                                <a href="javascript:popCalendar('sdate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0"></a>
                        </td>

                        <td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'To' );?>:</td>
                        <td align="left" nowrap="nowrap">
                                <input type="hidden" name="edate" value="<?php echo $end_date->format( FMT_TIMESTAMP_DATE );?>" />
                                <input type="text" class="text" name="show_edate" value="<?php echo $end_date->format( $df );?>" size="12" disabled="disabled" />
                                <a href="javascript:popCalendar('edate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0"></a>
                        <td valign="top">
                                <?php echo arraySelect( $projFilter, 'proFilter', 'size=1 class=text', $proFilter, true );?>
                        </td>
                        <td valign="top">
                                <input type="checkbox" name="showLabels" id="showLabels" value='1' <?php echo (($showLabels==1) ? 'checked="checked"' : "");?> /><label for="showLabels"><?php echo $AppUI->_( 'Show captions' );?></label>
                        </td>
                        <td valign="top">
                                <input type="checkbox" value='1' name="showInactive" id="showInactive" <?php echo (($showInactive==1) ? 'checked="checked"' : "");?> /><label for="showInactive"><?php echo $AppUI->_( 'Show Archived' );?></label>
                        </td>
                        <td valign="top">
                                <input type="checkbox" value='1' name="showAllGantt" id="showAllGantt" <?php echo (($showAllGantt==1) ? 'checked="checked"' : "");?> /><label for="showAllGantt"><?php echo $AppUI->_( 'Show Tasks' );?></label>
                        </td>
												<td valign="top">
                                <input type="checkbox" value='1' name="sortTasksByName" id="sortTasksByName" <?php echo (($sortTasksByName==1) ? 'checked="checked"' : "");?> /><label for="sortTasksByName"><?php echo $AppUI->_( 'Sort Tasks By Name' );?></label>
                        </td>
                        <td align="left">
                                <input type="button" class="button" value="<?php echo $AppUI->_( 'submit' );?>" onclick='document.editFrm.display_option.value="custom";submit();'>
                        </td>

                        <td align="right" valign="top" width="20">
                <?php if ($display_option != "all") { ?>
                        <a href="javascript:scrollNext()">
                                <img src="./images/next.gif" width="16" height="16" alt="<?php echo $AppUI->_( 'next' );?>" border="0">
                        </a>
                <?php } ?>
                        </td>
                </tr>

                </form>

                <tr>
                        <td align="center" valign="bottom" colspan="12">
                                <?php echo "<a href='javascript:showThisMonth()'>".$AppUI->_('show this month')."</a> : <a href='javascript:showFullProject()'>".$AppUI->_('show all')."</a><br>"; ?>
                        </td>
                </tr>

                </table>

                <table cellspacing="0" cellpadding="0" border="1" align="center" class="tbl">
                <tr>
                        <td>
                <?php
                $src = "?m=projects&a=gantt&suppressHeaders=1" .
                ( $display_option == 'all' ? '' :
                  '&start_date=' . $start_date->format( "%Y-%m-%d" ) . '&end_date=' . $end_date->format( "%Y-%m-%d" ) ) .
                "&width=' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '&showLabels=$showLabels&sortTasksByName=$sortTasksByName&proFilter=$proFilter&showInactive=$showInactive&company_id=$company_id&department=$department&dept_ids=$dept_ids&showAllGantt=$showAllGantt&user_id=$user_id&addPwOiD=$addPwOiD";
                echo "<script>document.write('<img src=\"$src\">')</script>";
                ?>
                        </td>
                </tr>
                </table>
        </td>
</tr>
</table>
<?php ini_restore('memory_limit');?>

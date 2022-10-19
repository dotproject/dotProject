<?php /* TASKS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

GLOBAL $min_view, $m, $a, $user_id, $tab, $tasks;

$min_view = defVal(@$min_view, false);

$project_id = defVal(@$_GET['project_id'], 0);

// sdate and edate passed as unix time stamps
$sdate = dPgetCleanParam($_POST, 'sdate', 0);
$edate = dPgetCleanParam($_POST, 'edate', 0);



//if set GantChart includes user labels as captions of every GantBar
$showLabels = (int)dPgetParam($_POST, 'showLabels', '0');
$showLabels = (($showLabels != '0') ? '1' : $showLabels);

$showWork = (int)dPgetParam($_POST, 'showWork', '0');
$showWork = (($showWork != '0') ? '1' : $showWork);

$sortByName = (int)dPgetParam($_POST, 'sortByName', '0');
$sortByName = (($sortByName != '0') ? '1' : $sortByName);

if ($a == 'todo') {
	if (isset($_POST['show_form'])) {
		$AppUI->setState('TaskDayShowArc', (int)dPgetParam($_POST, 'showArcProjs', 0));
		$AppUI->setState('TaskDayShowLow', (int)dPgetParam($_POST, 'showLowTasks', 0));
		$AppUI->setState('TaskDayShowHold', (int)dPgetParam($_POST, 'showHoldProjs', 0));
		$AppUI->setState('TaskDayShowDyn', (int)dPgetParam($_POST, 'showDynTasks', 0));
		$AppUI->setState('TaskDayShowPin', (int)dPgetParam($_POST, 'showPinned', 0));
	}
	$showArcProjs = $AppUI->getState('TaskDayShowArc', 0);
	$showLowTasks = $AppUI->getState('TaskDayShowLow', 1);
	$showHoldProjs = $AppUI->getState('TaskDayShowHold', 0);
	$showDynTasks = $AppUI->getState('TaskDayShowDyn', 0);
	$showPinned = $AppUI->getState('TaskDayShowPin', 0);

} else {
	$showPinned = (int)dPgetParam($_POST, 'showPinned', '0');
	$showPinned = (($showPinned != '0') ? '1' : $showPinned);
	$showArcProjs = (int)dPgetParam($_POST, 'showArcProjs', '0');
	$showArcProjs = (($showArcProjs != '0') ? '1' : $showArcProjs);
	$showHoldProjs = (int)dPgetParam($_POST, 'showHoldProjs', '0');
	$showHoldProjs = (($showHoldProjs != '0') ? '1' : $showHoldProjs);
	$showDynTasks = (int)dPgetParam($_POST, 'showDynTasks', '0');
	$showDynTasks = (($showDynTasks != '0') ? '1' : $showDynTasks);
	$showLowTasks = (int)dPgetParam($_POST, 'showLowTasks', '0');
	$showLowTasks = (($showLowTasks != '0') ? '1' : $showLowTasks);

}

// months to scroll
$scroll_date = 1;

$display_option = dPgetCleanParam($_POST, 'display_option', 'this_month');

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

if ($display_option == 'custom') {
	// custom dates
	$start_date = ((intval($sdate)) ? new CDate($sdate) : new CDate());
	$end_date = ((intval($edate)) ? new CDate($edate) : new CDate());
} else {
	// month
	$start_date = new CDate();
	$start_date->day = 1;
   	$end_date = new CDate($start_date);
	$end_date->addMonths($scroll_date);
}

// setup the title block
if (!@$min_view) {
	$titleBlock = new CTitleBlock('Gantt Chart', 'applet-48.png', $m, "$m.$a");
	$titleBlock->addCrumb('?m=tasks', 'tasks list');
	$titleBlock->addCrumb(('?m=projects&amp;a=view&amp;project_id=' . $project_id), 'view this project');
	$titleBlock->show();
}
?>
<script language="javascript">
function scrollPrev() {
	f = document.editFrm;
<?php
	$new_start = new CDate($start_date);
	$new_start->day = 1;
	$new_end = new CDate($end_date);
	$new_start->addMonths(-$scroll_date);
	$new_end->addMonths(-$scroll_date);
	echo ('f.sdate.value="' . $new_start->format(FMT_TIMESTAMP_DATE) . '";');
	echo ('f.edate.value="' . $new_end->format(FMT_TIMESTAMP_DATE) . '";');
?>
	document.editFrm.display_option.value = "custom";
	f.submit()
}

function scrollNext() {
	f = document.editFrm;
<?php
	$new_start = new CDate($start_date);
	$new_start->day = 1;
	$new_end = new CDate($end_date);
	$new_start->addMonths($scroll_date);
	$new_end->addMonths($scroll_date);
	echo ('f.sdate.value="' . $new_start->format(FMT_TIMESTAMP_DATE) . '";');
	echo ('f.edate.value="' . $new_end->format(FMT_TIMESTAMP_DATE) . '";');
?>
	document.editFrm.display_option.value = "custom";
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

<form name="editFrm" method="post" action="?<?php
echo ('m=' . $m . '&amp;a=' . $a . '&amp=tab=' . $tab . '&amp;project_id=' . $project_id); ?>">
<input type="hidden" name="display_option" value="<?php echo $display_option;?>" />

<table border="0" cellpadding="4" cellspacing="0">
<tr>
	<td align="left" valign="top" width="20">
<?php if ($display_option != "all") { ?>
		<a href="javascript:scrollPrev()">
			<img src="./images/prev.gif" width="16" height="16" alt="<?php
	echo $AppUI->_('previous');?>" border="0">
		</a>
<?php } ?>
	</td>

	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('From');?>:</td>
	<td align="left" nowrap="nowrap">
		<input type="date" name="sdate" value="<?php
echo $start_date->format(FMT_DATE_HTML5);?>" class="text dpDateField">
	</td>

	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('To');?>:</td>
	<td align="left" nowrap="nowrap">
		<input type="date" name="edate" value="<?php
echo $end_date->format(FMT_DATE_HTML5);?>" class="text dpDateField">
	</td>
	<td valign="top">
		<input type="checkbox" name="showLabels" id="showLabels" <?php
echo (($showLabels == 1) ? 'checked="checked"' : ''); ?> /><label for="showLabels"><?php
echo $AppUI->_('Show captions'); ?></label>
	</td>
	<td valign="top">
		<input type="checkbox" name="showWork" id="showWork" <?php
echo (($showWork == 1) ? 'checked="checked"' : ''); ?> /><label for="showWork"><?php
echo $AppUI->_('Show work instead of duration'); ?></label>
	</td>
	<td valign="top">
		<input type="checkbox" name="sortByName" id="sortByName" <?php
echo (($sortByName == 1) ? 'checked="checked"' : ''); ?> /><label for="sortByName"><?php
echo $AppUI->_('Sort by Task Name'); ?></label>
	</td>
	<td align="left">
		<input type="button" class="button" value="<?php
echo $AppUI->_('submit');?>" onclick='javascript:document.editFrm.display_option.value="custom";submit();'>
	</td>

	<td align="right" valign="top" width="20">
<?php if ($display_option != "all") { ?>
	  <a href="javascript:scrollNext()">
	  	<img src="./images/next.gif" width="16" height="16" alt="<?php
echo $AppUI->_('next');?>" border="0" />
	  </a>
<?php } ?>
	</td>
</tr>
<?php if ($a == 'todo') { ?>
<tr>
	<td align="center" valign="bottom" nowrap="nowrap" colspan="7">
		<input type="hidden" name="show_form" value="1" />
		<table width="100%" border="0" cellpadding="1" cellspacing="0">
			<tr>
			<td align="center" valign="bottom" nowrap="nowrap">
				<input type="checkbox" name="showPinned" id="showPinned" <?php
echo $showPinned ? 'checked="checked"' : ''; ?> />
				<label for="showPinned"><?php echo $AppUI->_('Pinned Only'); ?></label>
			</td>
			<td align="center" valign="bottom" nowrap="nowrap">
				<input type="checkbox" name="showArcProjs" id="showArcProjs" <?php
echo $showArcProjs ? 'checked="checked"' : ''; ?> />
				<label for="showArcProjs"><?php echo $AppUI->_('Archived Projects'); ?></label>
			</td>
			<td align="center" valign="bottom" nowrap="nowrap">
				<input type="checkbox" name="showHoldProjs" id="showHoldProjs" <?php
echo $showHoldProjs ? 'checked="checked"' : ''; ?> />
				<label for="showHoldProjs"><?php echo $AppUI->_('Projects on Hold'); ?></label>
			</td>
			<td align="center" valign="bottom" nowrap="nowrap">
				<input type="checkbox" name="showDynTasks" id="showDynTasks" <?php
echo $showDynTasks ? 'checked="checked"' : ''; ?> />
				<label for="showDynTasks"><?php echo $AppUI->_('Dynamic Tasks'); ?></label>
			</td>
			<td align="center" valign="bottom" nowrap="nowrap">
				<input type="checkbox" name="showLowTasks" id="showLowTasks" <?php
echo $showLowTasks ? 'checked="checked"' : ''; ?> />
				<label for="showLowTasks"><?php
echo $AppUI->_('Low Priority Tasks'); ?></label>
			</td>
			</tr>
		</table>
	</td>
</tr>
<?php } ?>
<tr>
	<td align="center" valign="bottom" colspan="7">
		<a href='javascript:showThisMonth()'><?php echo $AppUI->_('show this month'); ?></a> :
		<a href='javascript:showFullProject()'><?php
echo (($a == 'todo') ? $AppUI->_('show all') : $AppUI->_('show full project')); ?></a>
	</td>
</tr>

</table>
</form>

<table cellspacing="0" cellpadding="0" border="1" align="center">
<tr>
	<td>
<?php
if ($a != 'todo') {
	$q = new DBQuery;
	$q->addTable('tasks');
	$q->addQuery('COUNT(*) AS N');
	$q->addWhere('task_project=' . $project_id);
	$cnt = $q->loadList();
	$q->clear();
} else {
	$cnt[0]['N'] = ((empty($tasks)) ? 0 : 1);
}
if ($cnt[0]['N'] > 0) {
	$src = ('?m=tasks&amp;a=gantt&amp;suppressHeaders=1&amp;project_id=' . $project_id
	        . (($display_option == 'all') ? ''
	           : ('&amp;start_date=' . $start_date->format('%Y-%m-%d')
	              . '&amp;end_date=' . $end_date->format('%Y-%m-%d'))) . "&width='"
			. "+((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95)"
			. "+'&amp;showLabels=" . $showLabels . '&amp;showWork=' . $showWork
	        . '&amp;sortByName=' . $sortByName . '&amp;showPinned=' . $showPinned
	        . '&amp;showArcProjs=' . $showArcProjs . '&amp;showHoldProjs=' . $showHoldProjs
	        . '&amp;showDynTasks=' . $showDynTasks . '&amp;showLowTasks=' . $showLowTasks
	        . '&amp;caller=' . $a . '&amp;user_id=' . $user_id);
?>
	<script>document.write('<img src="<?php echo $src; ?>" alt="" />')</script>
<?php
	//If we have a problem displaying this we need to display a warning.
	//Put it at the bottom just in case
	if (! dPcheckMem(32*1024*1024)) {
?>
	</td>
</tr>
<tr>
	<td>
		<span style="color: red; font-weight: bold;"><?php
		echo $AppUI->_('invalid memory config'); ?></span>
<?php
	}
} else {
	echo $AppUI->_('No tasks to display');
}
?>
	</td>
</tr>
</table>
<br />

<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $a, $addPwOiD, $addPwT, $AppUI, $cBuffer, $company_id, $department, $min_view, $m, $priority;
global $projects, $tab, $user_id, $orderdir, $orderby, $dept_ids;

$department = isset($_GET['dept_id']) ? $_GET['dept_id'] : (isset($department) ? $department : 0);

$df = $AppUI->getPref('SHDATEFORMAT');

$pstatus =  dPgetSysVal('ProjectStatus');

if (isset($_POST['proFilter'])) {
	$AppUI->setState('DeptProjectIdxFilter',  $_POST['proFilter']);
}
$proFilter = (($AppUI->getState('DeptProjectIdxFilter') !== NULL) 
              ? $AppUI->getState('DeptProjectIdxFilter') : '-3');

$projFilter = arrayMerge(array('-1' => 'All Projects'), $pstatus);
$projFilter = arrayMerge(array('-2' => 'All w/o in progress'), $projFilter);
$projFilter = arrayMerge(array('-3' => 'All w/o archived'), $projFilter);
natsort($projFilter);

// load the companies class to retrieved denied companies
require_once($AppUI->getModuleClass('companies'));

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('DeptProjIdxTab', $_GET['tab']);
}

$valid_ordering = array(
	'project_color_identifier',
	'company_name',
	'project_name',
	'project_start_date',
	'project_duration',
	'project_end_date',
	'project_actual_end_date',
	'task_log_problem',
	'user_username',
	'total_tasks',
	'my_tasks',
	'project_status',
);
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
    $orderdir = $AppUI->getState('DeptProjIdxOrderDir') ? ($AppUI->getState('DeptProjIdxOrderDir')== 'asc' ? 'desc' : 'asc') : 'desc';    
    $AppUI->setState('DeptProjIdxOrderBy', $_GET['orderby']);
    $AppUI->setState('DeptProjIdxOrderDir', $orderdir);
}
$orderby  = $AppUI->getState('DeptProjIdxOrderBy') ? $AppUI->getState('DeptProjIdxOrderBy') : 'project_end_date';
$orderdir = $AppUI->getState('DeptProjIdxOrderDir') ? $AppUI->getState('DeptProjIdxOrderDir') : 'asc';

if (isset($_POST['show_form'])) {
	$AppUI->setState('addProjWithTasks',  dPgetParam($_POST, 'add_pwt', 0));
	$AppUI->setState('addProjWithOwnerInDep',  dPgetParam($_POST, 'add_pwoid', 0));
}
$addPwT = $AppUI->getState('addProjWithTasks', 0);
$addPwOiD = $AppUI->getState('addProjWithOwnerInDep', 0);

$extraGet = '&amp;user_id='.$user_id;

require(DP_BASE_DIR.'/functions/projects_func.php');
require_once($AppUI->getModuleClass('projects'));

// collect the full projects list data via function in projects.class.php
projects_list_data($user_id);
?>

<form action="?m=departments<?php echo (isset($a) ? '&amp;a='.$a : ''); ?>&amp;tab=<?php 
echo $tab; ?>" method="post" name="form_cb">
<input type="hidden" name="show_form" value="1" />
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<td align="right" width="65" nowrap="nowrap">
		<?php echo $AppUI->_('sort by');?>:
	</td>
	<td align="center" width="100%" nowrap="nowrap">&nbsp;</td>
	<td align="right" nowrap="nowrap">
		<input type="checkbox" name="add_pwoid" id="add_pwoid" onclick="document.form_cb.submit()" <?php 
echo ($addPwOiD ? 'checked="checked"' : ''); ?> />
		<label for="add_pwoid"><?php 
echo $AppUI->_('Show Projects whose Owner is Member of the Dep.'); ?></label>
	</td>
	<td align="right" nowrap="nowrap">
		<input type="checkbox" name="add_pwt" id="add_pwt" onclick="document.form_cb.submit()" <?php 
echo $addPwT ? 'checked="checked"' : ''; ?> />
		<label for="add_pwt"><?php echo $AppUI->_('Show Projects with assigned Tasks'); ?></label>
	</td>
	<td align="right" nowrap="nowrap">
		<?php 
echo arraySelect($projFilter, 'proFilter', 
                 'size=1 class=text onChange="document.form_cb.submit()"', $proFilter, true); 
?>
	</td>
</tr>
</table>
</form>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=project_color_identifier" class="hdr"><?php 
echo $AppUI->_('Color'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=company_name" class="hdr"><?php 
echo $AppUI->_('Company'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=project_name" class="hdr"><?php 
echo $AppUI->_('Project Name'); ?></a>
	</th>
          <th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=project_start_date" class="hdr"><?php 
echo $AppUI->_('Start'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=project_duration" class="hdr"><?php 
echo $AppUI->_('Duration'); ?></a>
	</th>
        <th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=project_end_date" class="hdr"><?php 
echo $AppUI->_('Due Date'); ?></a>
	</th>
        <th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; 
?>&amp;orderby=project_actual_end_date" class="hdr"><?php 
echo $AppUI->_('Actual'); ?></a>
	</th>
        <th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=task_log_problem" class="hdr"><?php 
echo $AppUI->_('P'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=user_username" class="hdr"><?php 
echo $AppUI->_('Owner'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=total_tasks" class="hdr"><?php 
echo $AppUI->_('Tasks'); ?></a>
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=my_tasks" class="hdr">(<?php 
echo $AppUI->_('My'); ?>)</a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=<?php echo $m; ?><?php echo (isset($a) ? '&amp;a='.$a : '') ; ?><?php 
echo (isset($extraGet) ? $extraGet : '') ; ?>&amp;orderby=project_status" class="hdr"><?php 
echo $AppUI->_('Status'); ?></a>
	</th>
</tr>

<?php
$CR = "\n";
$CT = "\n\t";
$none = true;
foreach ($projects as $row) {
	if (!(getPermission('projects', 'view', $row['project_id']))) {
		continue;
	}
	// We dont check the percent_completed == 100 because some projects
	// were being categorized as completed because not all the tasks
	// have been created (for new projects)
	if ($proFilter == -1 || $row['project_status'] == $proFilter 
	    || ($proFilter == -2 && $row['project_status'] != 3) 
	    || ($proFilter == -3 && $row['project_status'] != 7)) {
		$none = false;
		$start_date = ((intval(@$row['project_start_date'])) 
		               ? new CDate($row['project_start_date']) : null);
		$end_date = ((intval(@$row['project_end_date'])) 
		             ? new CDate($row['project_end_date']) : null);
		$actual_end_date = ((intval(@$row['project_actual_end_date'])) 
		                    ? new CDate($row['project_actual_end_date']) : null);
		$style = (($actual_end_date > $end_date && !(empty($end_date))) 
		          ? 'style="color:red; font-weight:bold"' : '');
?>
<tr>
	<td width="65" align="center" style="border: outset #eeeeee 2px;background-color:#<?php 
echo ($row['project_color_identifier']); ?>">
		<span style="color:<?php echo bestColor($row['project_color_identifier']); ?>">
			<?php echo sprintf("%.1f%%", $row['project_percent_complete']); ?>
		</span>
	</td>
	<td width="30%">
<?php 
		if (getPermission('companies', 'view', $row['project_company'])) {
?>
		<a href="?m=companies&amp;a=view&amp;company_id=<?php 
echo $row['project_company']; ?>" title="<?php echo htmlspecialchars($row['company_description'], ENT_QUOTES); ?> ">
<?php 
		}
		
		echo htmlspecialchars($row['company_name'], ENT_QUOTES);
		
		if (getPermission('companies', 'view', $row['project_company'])) {
?>
		</a>
<?php 
		}
?>
	</td>
	<td width="100%">
		<a href="?m=projects&amp;a=view&amp;project_id=<?php 
echo ($row['project_id']); ?>" onmouseover="return overlib('<?php 
echo htmlspecialchars(('<div><p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', 
                                                addslashes($row['project_description'])) 
                       . '</p></div>'), ENT_QUOTES); 
?>', CAPTION, '<?php echo $AppUI->_('Description'); ?>', CENTER);" onmouseout="nd();">
		<?php echo htmlspecialchars($row['project_name'], ENT_QUOTES); ?>
		</a>
	</td>
	<td align="center"><?php 
echo htmlspecialchars(($start_date ? $start_date->format($df) : '-')); ?></td>
	<td align="center"><?php 
echo htmlspecialchars((($row['project_duration'] > 0) 
                       ? ($row['project_duration'] . $AppUI->_('h')) : '-')); ?></td>
	<td align="center"nowrap="nowrap" style="background-color:<?php echo ($priority[$row['project_priority']]['color']); ?>"><?php 
echo htmlspecialchars(($end_date ? $end_date->format($df) : '-')); ?></td>
	<td align="center">
<?php 
		if (($actual_end_date)) {
?>
		<a href="?m=tasks&amp;a=view&amp;task_id=<?php echo ($row['critical_task']); ?>" <?php 
echo ($style); ?>><?php echo htmlspecialchars($actual_end_date->format($df)); ?></a>
<?php 
		} else {
?>
		-
<?php 
		}
?>
	</td>
	<td align="center">
<?php 
		if ($row['task_log_problem']) {
?>
		<a href="?m=tasks&amp;a=index&amp;f=all&amp;project_id=<?php echo ($row['project_id']); ?>">
		<?php dPshowImage('./images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem!'); ?>
		</a>
<?php 
		} else if ($row['project_priority'] != 0) {
			echo dPshowImage(('./images/icons/priority' 
			                  . (($row['project_priority'] > 0) ? '+' : '-') 
			                  . abs($row['project_priority']) . '.gif'), 13, 16, '', '');
		} else {
?>
		&nbsp;
<?php 
		}
?>
	</td>
	<td align="center" nowrap="nowrap"><?php 
echo htmlspecialchars($row['user_username'], ENT_QUOTES); ?>
	</td>
	<td align="center" nowrap="nowrap"><?php 
echo htmlspecialchars($row['total_tasks'] . ($row['my_tasks'] ? ' ('.$row['my_tasks'].')' : '')); ?>
	</td>
	<td align="center" nowrap="nowrap"><?php 
echo $AppUI->_($pstatus[$row['project_status']]); ?>
	</td>
</tr>
<?php 
	}
}
if ($none) {
?>
<tr><td colspan="11"><?php echo $AppUI->_('No projects available'); ?> </td></tr>
<?php 
}
?>
<tr>
	<td colspan="11">&nbsp;</td>
</tr>
</table>

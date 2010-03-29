<?php /* FILES $Id$ */

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

// modified later by Pablo Roca (proca) in 18 August 2003 - added page support
// Files modules: index page re-usable sub-table
GLOBAL $AppUI, $m, $canRead, $canEdit, $canAdmin;
GLOBAL $company_id, $project_id, $task_id;
GLOBAL $currentTabId, $currentTabName, $tabbed;

// ****************************************************************************
// Page numbering variables
// Pablo Roca (pabloroca@Xmvps.org) (Remove the X)
// 19 August 2003
//
// $tab				- file category
// $page			- actual page to show
// $xpg_pagesize	- max rows per page
// $xpg_min			- initial record in the SELECT LIMIT
// $xpg_totalrecs	- total rows selected
// $xpg_sqlrecs		- total rows from SELECT LIMIT
// $xpg_total_pages - total pages
// $xpg_next_page	- next pagenumber
// $xpg_prev_page	- previous pagenumber
// $xpg_break		- stop showing page numbered list?
// $xpg_sqlcount	- SELECT for the COUNT total
// $xpg_sqlquery	- SELECT for the SELECT LIMIT
// $xpg_result		- pointer to results from SELECT LIMIT

$tab = ((!$company_id && !$project_id && !$task_id) || $m=='files') ? $currentTabId : 0;
$page = dPgetParam($_GET, "page", 1);
if (!isset($project_id)) {
	$project_id = dPgetParam($_REQUEST, 'project_id', 0);
}
if (!isset($showProject)) {
	$showProject = true;
}
$xpg_pagesize = 30; //TODO?: Set by System Config Value ...
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from

// load the following classes to retrieved denied records
include_once $AppUI->getModuleClass('projects');
include_once $AppUI->getModuleClass('tasks');
require_once $AppUI->getSystemClass('query');
require_once $AppUI->getModuleClass('files');

$canAdmin = getPermission('system', 'edit');

$project = new CProject();
$task = new CTask();
$cfObj = new CFileFolder();
$compObj = new CCompany();
$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

$file_types = dPgetSysVal("FileType");
if (($company_id || $project_id || $task_id) && !($m=='files')) {
	$category_filter = false;
} else if ($tabbed) {
	$category_filter = (($tab <= 0) ? false : ("file_category = " . --$tab)) ;
} else {
	$category_filter = (($tab < 0) ? false : ("file_category = " . $tab)) ;
}

// Fetch permissions once for all queries
$allowedProjects = $project->getAllowedSQL($AppUI->user_id, 'f.file_project');
$allowedTasks = $task->getAllowedSQL($AppUI->user_id, 'f.file_task');
$allowedFolders = $cfObj->getAllowedSQL($AppUI->user_id, 'f.file_folder');
$allowedCompanies = $compObj->getAllowedSQL($AppUI->user_id);

// SQL text for count the total recs from the selected option
$q = new DBQuery;
$r = new DBQuery;

$q->addQuery('count(file_id)');
$q->addTable('files', 'f');
$q->addJoin('projects', 'p', 'p.project_id = file_project');
$q->addJoin('tasks', 't', 't.task_id = f.file_task');
if (count ($allowedProjects)) {
	$q->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
}
if (count ($allowedTasks)) {
	$q->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
}
if (count($allowedFolders)) {
	$q->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
}
if ($category_filter) {
	$q->addWhere($category_filter);
}
if ($company_id) {
	$q->addWhere('p.project_company = ' . $company_id);
}
if ($project_id) {
	$q->addWhere('f.file_project = '. $project_id);
}
if ($task_id) {
	$q->addWhere('f.file_task = '. $task_id);
}


// most recent version info per file_project and file_version_id
$r->createTemp('files_count_max');
$r->addTable('files', 'f');
$r->addQuery('DISTINCT count(f.file_id) as file_versions' 
             . ', max(f.file_version) as file_lastversion' 
             . ', f.file_version_id, f.file_project');
$r->addJoin('projects', 'p', 'p.project_id = f.file_project');
$r->addJoin('tasks', 't', 't.task_id = f.file_task');
$r->addJoin('file_folders', 'ff', 'ff.file_folder_id = f.file_folder');

if (count ($allowedProjects)) {
	$r->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
}
if (count ($allowedTasks)) {
	$r->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
}
if (count($allowedFolders)) {
	$r->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
}
if ($company_id) {
	$r->innerJoin('companies', 'co', 'co.company_id = p.project_company');
	$r->addWhere('co.company_id = '. $company_id);
	$r->addWhere($allowedCompanies);
}

$r->addGroup('f.file_project');
$r->addGroup('f.file_version_id');
$file_version_max_counts = $r->exec();
$r->clear();

// SETUP FOR FILE LIST
$q2 = new DBQuery;
$q2->addQuery('SQL_CALC_FOUND_ROWS f.*, f.file_id as latest_id'
              . ', fmc.file_versions , round(fmc.file_lastversion, 2) as file_lastversion');
$q2->addQuery('ff.*');
$q2->addTable('files', 'f');
$q2->addJoin('files_count_max', 'fmc', 
             '(fmc.file_lastversion = f.file_version AND fmc.file_version_id = f.file_version_id' 
             . ' AND fmc.file_project = f.file_project)', 'inner');
$q2->addJoin('file_folders', 'ff', 'ff.file_folder_id = f.file_folder');
$q2->addJoin('projects', 'p', 'p.project_id = f.file_project');
$q2->addJoin('tasks', 't', 't.task_id = f.file_task');
if (count ($allowedProjects)) {
	$q2->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
}
if (count ($allowedTasks)) {
	$q2->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
}
if (count($allowedFolders)) {
	$q2->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
}
if ($category_filter) {
	$q2->addWhere($category_filter);
}
if ($company_id) {
	$q2->addWhere('p.project_company = '. $company_id);
}
if ($project_id) {
	$q2->addWhere('f.file_project = '. $project_id);
}
if ($task_id) {
	$q2->addWhere('f.file_task = '. $task_id);
}
$q2->setLimit($xpg_pagesize, $xpg_min);
// Adding an Order by that is different to a group by can cause
// performance issues. It is far better to rearrange the group
// by to get the correct ordering.
$q2->addGroup('p.project_id');
$q2->addGroup('f.file_version_id DESC');


$q3 = new DBQuery;
$q3->addQuery('f.file_id, f.file_version, f.file_version_id, f.file_project, f.file_name' 
              . ', f.file_task, t.task_name, f.file_description, f.file_checkout, f.file_co_reason' 
              . ', u.user_username as file_owner, f.file_size, f.file_category, f.file_type' 
              . ', f.file_date, cu.user_username as co_user, p.project_name' 
              . ', p.project_color_identifier, p.project_owner, con.contact_first_name' 
              . ', con.contact_last_name, co.contact_first_name as co_contact_first_name' 
              . ', co.contact_last_name as co_contact_last_name ');
$q3->addQuery('ff.*');
$q3->addTable('files', 'f');
$q3->addJoin('users', 'u', 'u.user_id = file_owner');
$q3->addJoin('contacts', 'con', 'con.contact_id = u.user_contact');
$q3->addJoin('file_folders','ff','ff.file_folder_id = f.file_folder');
$q3->addJoin('projects', 'p', 'p.project_id = f.file_project');
$q3->addJoin('tasks', 't', 't.task_id = f.file_task');
$q3->leftJoin('users', 'cu', 'cu.user_id = f.file_checkout');
$q3->leftJoin('contacts', 'co', 'co.contact_id = cu.user_contact');
if (count ($allowedProjects)) {
	$q3->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
}
if (count ($allowedTasks)) {
	$q3->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
}
if (count($allowedFolders)) {
	$q3->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
}
if ($category_filter) {
	$q3->addWhere($category_filter);
}
if ($company_id) {
	$q3->addWhere('p.project_company = '. $company_id);
}
if ($project_id) {
	$q3->addWhere('f.file_project = '. $project_id);
}
if ($task_id) {
	$q3->addWhere('f.file_task = '. $task_id);
}

$files = array();
$file_versions = array();
if ($canRead) {
	
	$q2->includeCount();
	$files = $q2->loadList();
	$xpg_totalrecs = $q2->foundRows();
	$file_versions = $q3->loadHashList('file_id');
}

$r->dropTemp('files_count_max');
$r->exec();

// How many pages are we dealing with here ??
$xpg_total_pages = ($xpg_totalrecs > $xpg_pagesize) ? ceil($xpg_totalrecs / $xpg_pagesize) : 1;

shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page);

?>
<script type="text/JavaScript">
function expand(id) {
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "block" : "none";
}
</script>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">&nbsp;</th>
	<th nowrap="nowrap"><?php echo $AppUI->_('co'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Checkout Reason'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('File Name'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Description'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Versions'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Category'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Folder'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Task Name'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Owner'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Size'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Type'); ?></a></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Date'); ?></th>
</tr><?php
$fp=-1;
$file_date = new CDate();

$id = 0;
foreach ($files as $file_row) {
	$latest_file = $file_versions[$file_row['latest_id']];
	$file_date = new CDate($latest_file['file_date']);
	
	if ($fp != $latest_file["file_project"]) {
		if (!$latest_file["file_project"]) {
			$latest_file["project_name"] = $AppUI->_('Not associated to projects');
			$latest_file["project_color_identifier"] = 'f4efe3';
		}
		if ($showProject) {
			$style = ("background-color:#$latest_file[project_color_identifier];color:" 
			          . bestColor($latest_file["project_color_identifier"]));
?>
<tr>
	<td colspan="20" style="border: outset 2px #eeeeee;<?php echo $style; ?>">
		<a href="?m=projects&amp;a=view&amp;project_id=<?php echo $latest_file['file_project']; ?>">
		<span style="<?php echo $style; ?>"><?php echo $latest_file['project_name']; ?></span>
		</a>
	</td>
</tr><?php
		}
	}
	$fp = $latest_file["file_project"];
?>
<tr>
	<td nowrap="nowrap" width="20">
		<?php 
	if ($canEdit && (empty($latest_file['file_checkout']) 
	                 || ($latest_file['file_checkout'] == 'final' 
	                     && ($canAdmin || $latest_file['project_owner'] == $AppUI->user_id)))) {
		echo ('<a href="./index.php?m=files&amp;a=addedit&amp;file_id=' . $latest_file['file_id'] 
		      . '">');
		echo (dPshowImage(DP_BASE_URL . '/modules/files/images/kedit.png', '16', '16', 'edit file', 
		                  'edit file'));
		echo '</a>';
	}
?>
	</td>
	<td nowrap="nowrap"><?php 
	if ($canEdit && empty($latest_file['file_checkout'])) {
?>
		<a href="?m=files&amp;a=co&amp;file_id=<?php echo $latest_file['file_id']; ?>"><?php 
		echo dPshowImage((DP_BASE_URL . '/modules/files/images/co.png'), '16', '16', 'checkout', 
		                 'checkout file');
?>
		</a><?php
	} else if ($latest_file['file_checkout'] == $AppUI->user_id) { 
?>
		<a href="?m=files&amp;a=addedit&amp;ci=1&amp;file_id=<?php echo $latest_file['file_id']; ?>">
		<?php 
		echo dPshowImage((DP_BASE_URL . '/modules/files/images/ci.png'), '16', '16', 'checkin', 
		                 'checkin file');
?>
		</a><?php
	} else if ($latest_file['file_checkout'] == 'final') {
		echo $AppUI->_('final');
	} else {
		echo ($AppUI->___('	  ' . $latest_file['co_contact_first_name'] . ' ' 
		                  . $latest_file['co_contact_last_name']) . '<br />'
			  . $AppUI->___('(' . $latest_file['co_user'] . ')'));
	}
?>
	</td>
	<td width="10%">
		<?php echo ($latest_file['file_co_reason']); ?> <?php 
	if (!(empty($latest_file['file_checkout'])) 
	    && ($latest_file['file_checkout'] == $AppUI->user_id 
	        || ($canEdit && ($canAdmin || $latest_file['project_owner'] == $AppUI->user_id)))) {
?>
		<a href="?m=files&amp;a=co&amp;co_cancel=1&amp;file_id=<?php 
		echo $latest_file['file_id']; ?>">
		<?php
		echo dPshowImage((DP_BASE_URL . '/images/icons/stock_cancel-16.png'), '16', '16', 
		                 'cancel checkout', 'cancel file checkout'); 
?>
		</a><?php
} ?>
	</td>
	<td nowrap="8%">
		<?php 
	$fnamelen = 32;
	$filename = $latest_file['file_name'];
	if (mb_strlen($latest_file['file_name']) > $fnamelen+9) {
		$ext = mb_substr($filename, mb_strrpos($filename, '.')+1);
		$filename = mb_substr($filename, 0, $fnamelen);
		$filename .= '[...].' . $ext;
	}
	$file_icon = getIcon($file_row['file_type']);
?>
		<a href="./fileviewer.php?file_id=<?php 
	echo $latest_file['file_id']; ?>" title="<?php echo $latest_file['file_description']; ?>">
		<?php
	echo (dPshowImage((DP_BASE_URL . '/modules/files/images/' . $file_icon), '16', '16') . "\n" 
	      . '&nbsp;' . $filename);
?>
	  </a>
	</td>
	<td width="20%"><?php echo $latest_file['file_description']; ?></td>
	<td width="5%" nowrap="nowrap" align="center">
		<?php 
	echo $file_row['file_lastversion'];
	if ($file_row['file_versions'] > 1) {
?>
		<a href="#" onClick="expand('versions_<?php echo $latest_file['file_id']; ?>');">
		(<?php echo $file_row['file_versions']; ?>)
		</a><?php 
	}
?>
	</td>
	<td width="10%" nowrap="nowrap" align="center">
		<?php echo $file_types[$latest_file["file_category"]]; ?>
	</td>
	<td width="10%" nowrap="nowrap" align="center">
		<?php
	if ($file_row['file_folder_name'] != '') {
		$file_folder_url = (DP_BASE_URL . '/index.php?m=files&amp;tab=' . (count($file_types)+1) 
		                    . '&amp;folder=' . $file_row['file_folder_id']);
?>
		<a href="<?php echo $file_folder_url; ?>">
		<?php 
		echo dPshowImage((DP_BASE_URL . '/modules/files/images/folder5_small.png'), 
		                 '16', '16', 'folder icon', 'show only this folder');
?> 
		<?php echo  $file_row['file_folder_name']; ?>
		</a> <?php
	} else {
		echo $AppUI->_('Root');
	}
?>
	</td>
	<td width="5%" align="center">
		<a href="./index.php?m=tasks&amp;a=view&amp;task_id=<?php 
	echo $latest_file['file_task']; ?>">
		<?php echo $latest_file["task_name"]; ?>
		</a>
	</td>
	<td width="15%" nowrap="nowrap">
		<?php 
	echo ($latest_file["contact_first_name"] . ' ' . $latest_file["contact_last_name"]); 
?>
	</td>
	<td width="5%" nowrap="nowrap" align="right">
		<?php echo file_size(intval($latest_file["file_size"])); ?>
	</td>
	<td nowrap="nowrap">
		<?php 
	echo $AppUI->_(mb_substr($latest_file['file_type'], 
	                         mb_strpos($latest_file['file_type'], '/') + 1)); 
?>
	</td>
	<td width="15%" nowrap="nowrap" align="right">
		<?php echo $file_date->format($df.' '.$tf); ?>
	</td>
</tr>
<?php
	if ($file_row['file_versions'] > 1) {
?>
<tr>
	<td colspan="20">
	<table style="display: none" id="versions_<?php 
		echo $latest_file['file_id']; 
?>" width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
	<tr>
		<th nowrap="nowrap">&nbsp;</th>
		<th nowrap="nowrap"><?php echo $AppUI->_('File Name'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Description'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Versions'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Category'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Folder'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Task Name'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Owner'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Size'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Type'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Date'); ?></th>
	</tr>
<?php
		foreach ($file_versions as $file) {
			if ($file['file_version_id'] == $latest_file['file_version_id']) {
				$file_icon = getIcon($file['file_type']);
				$hdate = new Date($file['file_date']);
?>
	<tr>
		<td nowrap="nowrap" width="20">&nbsp;<?php
				if ($canEdit && $dPconfig['files_show_versions_edit']) {
?>
			<a href="./index.php?m=files&amp;a=addedit&amp;file_id=<?php echo $file['file_id']; ?>">
			<?php
					echo dPshowImage((DP_BASE_URL . '/modules/files/images/kedit.png'), '16', '16', 
				                 'edit file', 'edit file');
?>
			</a><?php
				}
?>
		</td>
		<td nowrap="8%">
			<a href="./fileviewer.php?file_id=<?php echo $file['file_id']; ?>" title="<?php 
				echo $file['file_description']; ?>">
			<?php 
				echo dPshowImage((DP_BASE_URL . '/modules/files/images/' . $file_icon), '16', '16');
?>
			<?php echo $file['file_name']; ?> 
			</a>
		</td>
		<td width="20%"><?php echo $file['file_description']; ?></td>
		<td width="5%" nowrap="nowrap" align="center"><?php echo $file['file_version']; ?></td>
		<td width="10%" nowrap="nowrap" align="center">
			<?php echo $file_types[$file['file_category']]; ?>
		</td>
		<td width="10%" nowrap="nowrap" align="center">
			<?php
				if ($file['file_folder_name'] != '') {
					$file_folder_url = (DP_BASE_URL . '/index.php?m=files&amp;tab=' 
					                    . (count($file_types)+1) . '&amp;folder=' 
					                    . $file['file_folder_id']);
?>
			<a href="<?php echo $file_folder_url; ?>">
			<?php 
					echo dPshowImage((DP_BASE_URL . '/modules/files/images/folder5_small.png'), 
					                 '16', '16', 'folder icon', 'show only this folder');
?> 
			<?php echo  $file['file_folder_name']; ?>
			</a><?php
				} else {
					echo $AppUI->_('Root');
				}
?>
		</td>
		<td width="5%" align="center">
			<a href="./index.php?m=tasks&amp;a=view&amp;task_id=<?php echo $file['file_task']; ?>">
			<?php echo $file['task_name']; ?>
			</a>
		</td>
		<td width="15%" nowrap="nowrap">
			<?php echo ($file["contact_first_name"] . ' ' . $file["contact_last_name"]); ?>
		</td>
		<td width="5%" nowrap="nowrap" align="right">
			<?php echo file_size(intval($file['file_size'])); ?>
		</td>
		<td nowrap="nowrap">
			<?php echo mb_substr($file['file_type'], mb_strpos($file['file_type'], '/')+1) ?>
		</td>
		<td width="15%" nowrap="nowrap" align="right">
			<?php echo $hdate->format("$df $tf"); ?>
		</td>
		</tr>
<?php
			}
		}
?>
		</table>
	</td>
</tr>
<?php
	} 
}
?>
</table>
<?php
shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page);
?>

<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI, $m, $a, $tab;
global $canAccess, $canRead, $canEdit, $canAuthor, $canDelete, $canAdmin;
global $company_id, $project_id, $task_id, $folder, $page, $current_uri;
global $currentTabId, $currentTabName, $tabbed, $showProject;
global $cfObj;

// add to allow for returning to other modules besides Files
$current_uriArray = parse_url($_SERVER['REQUEST_URI']);
$current_uri = $current_uriArray['query'] . (isset($current_uriArray['fragment']) ?? "");

$folder = (int)dPgetParam($_GET, 'folder', 0);
$page = (int)dPgetParam($_GET, 'page', 1);


global $canAccess_folders, $canRead_folders, $canEdit_folders;
global $canAuthor_folders, $canDelete_folders;

$canAccess_folders = getPermission('file_folders', 'access');
$canRead_folders = getPermission('file_folders', 'view');
$canEdit_folders = getPermission('file_folders', 'edit');
$canAuthor_folders = getPermission('file_folders', 'add');
$canDelete_folders = getPermission('file_folders', 'delete');

$canAdmin = getPermission('system', 'edit');

// load the following classes to retrieved denied records
include_once($AppUI->getModuleClass('projects'));
include_once($AppUI->getModuleClass('tasks'));

if (!isset($project_id)) {
	$project_id = (int)dPgetParam($_REQUEST, 'project_id', 0);
}
if (!$project_id) {
	$showProject = true;
}

global $allowedCompanies, $allowedProjects, $allowedTasks, $allowedFolders;

$company = new CCompany();
$allowedCompanies = $company->getAllowedSQL($AppUI->user_id, 'co.company_id');

$project = new CProject();
$allowedProjects = $project->getAllowedSQL($AppUI->user_id, 'f.file_project');

$task = new CTask();
$allowedTasks = $task->getAllowedSQL($AppUI->user_id, 'f.file_task');

$cfObj = new CFileFolder();
$allowedFolderIDs = $cfObj->getAllowedSQL($AppUI->user_id, 'ff.file_folder_id');
$allowedFolders = $cfObj->getAllowedSQL($AppUI->user_id, 'f.file_folder');

/**
 * Display a specific folder, level by level
 *
 * @param integer $parent_id is the parent of the children we want to see
 * @param integer $level is increased when we go deeper into the tree, used to display a nice indented tree
 * @return void
 **/
function displayFolders($folder_id=0, $level=0) {
	global $AppUI, $m, $a, $tab;
	global $current_uri;

	global $canAccess_folders, $canRead_folders, $canEdit_folders;
	global $canAuthor_folders, $canDelete_folders;

	global $company_id, $project_id, $task_id;
	global $allowedCompanies, $allowedProjects, $allowedTasks, $allowedFolders;

	$q = new DBQuery();
	$folders = array();
	// retrieve all info of $folder_id
	if (!empty($folder_id)) {
	  $q->addTable('file_folders', 'ff');
		$q->addQuery('ff.*');
		$q->addWhere('ff.file_folder_id = ' . $folder_id);
		$folder_sql = $q->prepare();
		$q->clear();
		$folders = db_loadList($folder_sql);
	} else {
		$folders[0]['file_folder_name'] = $AppUI->_('Root');
		$folders[0]['file_folder_description'] = '';
		$folder_id = 0;
	}

	//get file count for folder
	$file_count = countFiles($folder_id);

	//check permissions
	$canAccess_this = getPermission('file_folders', 'access', $folder_id);
	$canRead_this = getPermission('file_folders', 'view', $folder_id);
	$canEdit_this = getPermission('file_folders', 'edit', $folder_id);
	$canAuthor_this = getPermission('file_folders', 'add', $folder_id);
	$canDelete_this = getPermission('file_folders', 'delete', $folder_id);

	if (!($canRead_this) && $folder_id) {
		return;
	}

	foreach ($folders as $row) {

		// "loop" through one folder
		if ($canRead_this && $level) {
			// indent and display the title
			echo ('<table width="100%" summary="files folders"><tr>' . "\n");
			echo ('<td>' . "\n");
		}

		echo ('<span class="folder-name' . ((!($folder_id && $level))
		                                    ? '-current':'') . '">' . "\n");

		echo ((($m=='files')
		       ? ('<a' . (($folder_id)
		                  ? (' href="?m=' . $m . '&a=' . $a . '&amp;tab=' . $tab
		                     . '&amp;folder=' . $folder_id . '"') : '') . ' name="ff' . $folder_id
		          . '">') : '') . "\n");
		echo (dPshowImage(DP_BASE_URL . '/modules/files/images/folder5_small.png', '16', '16',
		                  'folder icon', $AppUI->_('show only this folder'))
		      . $AppUI->___($row['file_folder_name']) . "\n");
		echo ((($m=='files') ? '</a>' : ''). "\n");

		if ($file_count > 0) {
			echo ('<a href="#ff' . $folder_id . '" onclick="javascript:expand(' . "'files_"
			      . $folder_id . "'" . ')" class="has-files">(' . $file_count
			      . ' files) +</a>'. "\n");
		}

		echo ("</span>\n" . (($level)? "</td>\n" : ''));

		if ($row['file_folder_description'] && !($folder_id && $level)) {
			echo $AppUI->___('<p>' . $row['file_folder_description'] . '</p>');
		} else if ($level) {

			if ($folder_id) {
				echo ('<form id="frm_remove_folder_' . $folder_id
				      . '" name="frm_remove_folder_' . $folder_id
				      . '" action="?m=files" method="post">' . "\n"
				      . '<input type="hidden" name="dosql" value="do_folder_aed" />' . "\n"
				      . '<input type="hidden" name="del" value="1" />' . "\n"
				      . '<input type="hidden" name="file_folder_id" value="' . $folder_id
				      . '" />' . "\n"
				      . '<input type="hidden" name="redirect" value="' . $current_uri . '" />'
				      . "</form>\n");
				echo ('<td align="right" width="64" nowrap="nowrap">' . "\n");
				//edit folder
				if ($canEdit_this) {
					echo ('<a href="?m=files&amp;a=addedit_folder&amp;folder=' . $folder_id
					      . '">' . dPshowImage(DP_BASE_URL . '/modules/files/images/kedit.png',
					                           '16', '16', 'edit icon', 'edit this folder')
					      . '</a>');
				}
				//add folder
				if ($canAuthor_this) {
					echo ('<a href="?m=files&amp;a=addedit_folder&amp;file_folder_parent='
					      . $folder_id . '&amp;folder=0">'
					      . dPshowImage(DP_BASE_URL . '/modules/files/images/edit_add.png', '16',
					                    '16', 'new folder', $AppUI->_('add a new subfolder'))
					      . '</a>');
				}
				if ($canDelete_this) {
					//remove folder
					echo ('<a href="#" onclick="javascript:delCheck('  . "'" . $folder_id . "'"
					      . ')">' .dPshowImage(DP_BASE_URL . '/modules/files/images/remove.png',
					                           '16', '16', 'delete icon',
					                           $AppUI->_('delete this folder'))
					      . '</a>');
				}
				//add file to folder
				echo ('<a href="?m=files&amp;a=addedit&amp;folder=' . $folder_id
				      . '&amp;project_id=' . $project_id . '&amp;file_id=0">'
				      . dPshowImage(DP_BASE_URL . '/modules/files/images/folder_new.png', '16',
				                    '16', 'new file', $AppUI->_('add new file to this folder'))
				      . '</a>');
				echo ("</td>\n");
				echo ("</tr></table>\n");
			}
		}

		if ($file_count > 0) {
			echo ('<div class="files-list" id="files_' . $folder_id . '" style="display:'
			      . ((empty($level) || empty($open_folder)) ? 'none' : 'block') . ';">');
			displayFiles($folder_id);
			echo '</div>';
		} else if (!empty($folder) && !($folder_id && $level)) {
			echo $AppUI->_('No Result(s)');
		}
	}

	// retrieve all children of $folder_id
	$q->addTable('file_folders', 'ff');
	$q->addQuery('ff.*');
	$q->addWhere('ff.file_folder_parent = ' . $folder_id);
	if (!empty($allowedFolderIDs)) {
		$q->addWhere($allowedFolderIDs);
	}
	$q->addOrder('ff.file_folder_name');
	$folder_children_sql = $q->prepare();
	$q->clear();
	$folders_children = db_loadList($folder_children_sql);
	foreach ($folders_children as $kid_row) {
		// call this function again to its children
		echo ('<ul><li>');
		displayFolders($kid_row['file_folder_id'], $level+1);
		echo ('</li></ul>');
	}
}

/**
 * Counts number of files in the specified directory
 *
 * @param integer $folder_id
 * @return integer or mixed
 **/
function countFiles($folder_id) {
  if (empty($folder_id)) {  // should not really happen
    dprint(__FILE__, __LINE__, 11, "[WARN]: got called with empty folder id (should not happen)");
    return 0;
  }

	global $company_id, $project_id, $task_id;
	global $allowedCompanies, $allowedProjects, $allowedTasks, $allowedFolders;

	$q = new DBQuery();

	//get file count for folder
	$q->addTable('files', 'f');
	$q->addJoin('projects', 'p', 'p.project_id = f.file_project');
	$q->addJoin('tasks', 't', 't.task_id = f.file_task');
	$q->addJoin('file_folders', 'ff', 'ff.file_folder_id = f.file_folder');
	$q->addQuery('count(f.file_id)', 'file_in_folder');
	$q->addWhere('f.file_folder = '. $folder_id);
	if (count($allowedFolders)) {
		$q->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
	}
	if (count($allowedProjects)) {
		$q->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
	}
	if (count($allowedTasks)) {
		$q->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR file_task = 0)');
	}
	if ($project_id) {
		$q->addWhere('f.file_project = '. $project_id);
	}
	if ($task_id) {
		$q->addWhere('f.file_task = '. $task_id);
	}
	if ($company_id) {
		$q->innerJoin('companies', 'co', 'co.company_id = p.project_company');
		$q->addWhere('co.company_id = '. $company_id);
		if (count($allowedCompanies)) {
			$q->addWhere('(' . implode(' AND ', $allowedCompanies) . ')');
		}
	}

	$sql = $q->prepare();
	$q->clear();

	return db_loadResult($sql);
}

/**
 * Displays files in the specified directory
 *
 * @param integer $folder_id
 * @return integer or mixed
 **/
function displayFiles($folder_id) {
  if (empty($folder_id)) {  // should not really happen
    dprint(__FILE__, __LINE__, 11, "[WARN]: got called with empty folder id (should not happen)");
    return 0;
  }

	global $AppUI, $m, $a, $tab, $page;
	global $current_uri;

	global $canAccess, $canRead, $canEdit, $canAuthor, $canDelete;
	global $canAccess_folders, $canRead_folders, $canEdit_folders;
	global $canAuthor_folders, $canDelete_folders;

	global $company_id, $project_id, $task_id;
	global $allowedCompanies, $allowedProjects, $allowedTasks, $allowedFolders;

	global $showProject, $cfObj, $dPconfig;


	$df = $AppUI->getPref('SHDATEFORMAT');
	$tf = $AppUI->getPref('TIMEFORMAT');

	$file_types = dPgetSysVal('FileType');

	$xpg_pagesize = 30; //TODO?: Set by System Config Value ...
	$xpg_totalrecs = countFiles($folder_id); //get file count for folder
	$xpg_total_pages = ($xpg_totalrecs > $xpg_pagesize) ? ceil($xpg_totalrecs / $xpg_pagesize) : 1;

	$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from

	$q = new DBQuery();

	// most recent version info per file_project and file_version_id
	$q->createTemp('files_count_max' . $folder_id);
	$q->addTable('files', 'f');
	$q->addQuery('DISTINCT count(f.file_id) as file_versions'
	             . ', max(f.file_version) as file_lastversion'
	             . ', file_version_id, f.file_project');
	$q->addJoin('projects', 'p', 'p.project_id = f.file_project');
	$q->addJoin('tasks', 't', 't.task_id = f.file_task');
	$q->addJoin('file_folders', 'ff', 'ff.file_folder_id = f.file_folder');

	$q->addWhere('f.file_folder = '. $folder_id);
	if (count ($allowedProjects)) {
		$q->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
	}
	if (count ($allowedTasks)) {
		$q->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
	}
	if (count($allowedFolders)) {
		$q->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
	}
	if ($company_id) {
		$q->innerJoin('companies', 'co', 'co.company_id = p.project_company');
		$q->addWhere('co.company_id = '. $company_id);
		if (count($allowedCompanies)) {
			$q->addWhere('(' . implode(' AND ', $allowedCompanies) . ')');
		}
	}

	$q->addGroup('f.file_version_id');
	$q->addGroup('f.file_project');
	$file_version_max_counts = $q->exec();
	$q->clear();

	// most recent version
	$q->addTable('files', 'f');
	$q->addQuery('f.*, fmc.file_versions, round(fmc.file_lastversion, 2) as file_lastversion'
				 . ', u.user_username as file_owner, ff.file_folder_name'
	             . ', ff.file_folder_id, ff.file_folder_name, p.project_name'
	             . ', p.project_color_identifier, p.project_owner, c.contact_first_name'
	             . ', c.contact_last_name, t.task_name, u.user_username as file_owner'
	             . ', cc.contact_first_name as checkout_first_name'
	             . ', cc.contact_last_name as checkout_last_name');
	$q->addJoin('files_count_max' . $folder_id, 'fmc',
				'(fmc.file_lastversion=f.file_version AND fmc.file_version_id=f.file_version_id'
				. ' AND fmc.file_project=f.file_project)', 'inner');
	$q->addJoin('projects', 'p', 'p.project_id = f.file_project');
	$q->addJoin('users', 'u', 'u.user_id = f.file_owner');
	$q->addJoin('contacts', 'c', 'c.contact_id = u.user_contact');
	$q->addJoin('tasks', 't', 't.task_id = f.file_task');
	$q->addJoin('file_folders', 'ff', 'ff.file_folder_id = f.file_folder');
	$q->leftJoin('users', 'cu', 'cu.user_id = f.file_checkout');
	$q->leftJoin('contacts', 'cc', 'cc.contact_id = cu.user_contact');

	$q->addWhere('f.file_folder = '. $folder_id);
	if (count ($allowedProjects)) {
		$q->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
	}
	if (count ($allowedTasks)) {
		$q->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
	}
	if (count($allowedFolders)) {
		$q->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
	}
	if ($project_id) {
		$q->addWhere('f.file_project = '. $project_id);
	}
	if ($task_id) {
		$q->addWhere('f.file_task = '. $task_id);
	}
	if ($company_id) {
		$q->innerJoin('companies', 'co', 'co.company_id = p.project_company');
		$q->addWhere('co.company_id = '. $company_id);
		if (count($allowedCompanies)) {
			$q->addWhere('(' . implode(' AND ', $allowedCompanies) . ')');
		}
	}

	$q->addOrder('p.project_name');
	$q->setLimit($xpg_pagesize, $xpg_min);

	$files_sql = $q->prepare();
	$q->clear();

	// all versions
	$q->addTable('files', 'f');
	$q->addQuery('f.*, ff.file_folder_id, ff.file_folder_name, p.project_name'
	             . ', p.project_color_identifier, p.project_owner, c.contact_first_name'
	             . ', c.contact_last_name, t.task_name, u.user_username as file_owner');
	$q->addJoin('projects', 'p', 'p.project_id = f.file_project');
	$q->addJoin('users', 'u', 'u.user_id = f.file_owner');
	$q->addJoin('contacts', 'c', 'c.contact_id = u.user_contact');
	$q->addJoin('tasks', 't', 't.task_id = f.file_task');
	$q->addJoin('file_folders', 'ff', 'ff.file_folder_id = f.file_folder');

	$q->addWhere('f.file_folder = '. $folder_id);
	if (count ($allowedProjects)) {
		$q->addWhere('((' . implode(' AND ', $allowedProjects) . ') OR f.file_project = 0)');
	}
	if (count ($allowedTasks)) {
		$q->addWhere('((' . implode(' AND ', $allowedTasks) . ') OR f.file_task = 0)');
	}
	if (count($allowedFolders)) {
		$q->addWhere('((' . implode(' AND ', $allowedFolders) . ') OR f.file_folder = 0)');
	}
	if ($project_id) {
		$q->addWhere('f.file_project = '. $project_id);
	}
	if ($task_id) {
		$q->addWhere('f.file_task = '. $task_id);
	}
	if ($company_id) {
		$q->innerJoin('companies', 'co', 'co.company_id = p.project_company');
		$q->addWhere('co.company_id = '. $company_id);
		if (count($allowedCompanies)) {
			$q->addWhere('(' . implode(' AND ', $allowedCompanies) . ')');
		}
	}

	$file_versions_sql = $q->prepare();
	$q->clear();

	//file arrays
	$files = array();
	$file_versions = array();
	if ($canRead) {
		$files = db_loadList($files_sql);
		$file_versions = db_loadHashList($file_versions_sql, 'file_id');
	}
	$q->dropTemp('files_count_max' . $folder_id);
	$q->exec();
	if ($files == array()) {
		return;
	}
?>
	<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl" summary="folders table">
	<tr>
		<th nowrap="nowrap"><?php echo $AppUI->_('File Name'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Description'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Versions'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Category'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Task Name'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Owner'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Size'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Type'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Date'); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('Checkout Reason') ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_('co') ?></th>
		<th nowrap width="1"></th>
		<th nowrap width="1"></th>
	</tr><?php
	$fp=-1;
	$file_date = new CDate();

	$id = 0;
	foreach ($files as $row) {
		$file_date = new CDate($row['file_date']);

		$canEdit_file = getPermission('files', 'edit', $row['file_id']); //single file

		if ($fp != $row['file_project']) {
			if (!$row['file_project']) {
				$row['project_name'] = $AppUI->_('Not associated to projects');
				$row['project_color_identifier'] = '#f4efe3';
			}
			if ($showProject) {
				$style = ('background-color:' . $row['project_color_identifier']
						  . ';color:' . bestColor($row['project_color_identifier']));
?>
<tr>
	<td colspan="20" style="border: outset 2px #eeeeee;<?php echo $style; ?>">
	<a href="?m=projects&amp;a=view&amp;project_id=<?php echo $row['file_project']; ?>">
	<span style="<?php echo $style; ?>"><?php echo $row['project_name']; ?></span></a>
	</td>
</tr><?php
			}
		}
		$fp = $row['file_project'];
?>
	<tr>
		<td nowrap="8%">
	<form name="frm_remove_file_<?php echo $row['file_id']; ?>" action="?m=files" method="post">
	<input type="hidden" name="dosql" value="do_file_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="file_id" value="<?php echo $row['file_id']; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
	</form>
	<form name="frm_duplicate_file_<?php echo $row['file_id']; ?>" action="?m=files" method="post">
	<input type="hidden" name="dosql" value="do_file_aed" />
	<input type="hidden" name="duplicate" value="1" />
	<input type="hidden" name="file_id" value="<?php echo $row['file_id']; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
	</form>
<?php
		$file_icon = getIcon($row['file_type']);
?>
			<a href="./fileviewer.php?file_id=<?php echo $row['file_id']; ?>" title="<?php
		echo $AppUI->___($row['file_description']); ?>">
			<?php
		echo dPshowImage((DP_BASE_URL . '/modules/files/images/' . $file_icon), '16', '16');
?>
			&nbsp;<?php echo $row['file_name']; ?>
			</a>
		</td>
		<td width="20%"><?php echo $AppUI->___($row['file_description']);?></td>
		<td width="5%" nowrap="nowrap" align="center">
			<?php
		$hidden_table = '';
		echo $row['file_lastversion'];
		if ($row['file_versions'] > 1) {
?>
			<a href="#" onclick="javascript:expand('versions_<?php echo $row['file_id']; ?>');">
			(<?php echo $row['file_versions']; ?>)
			</a><?php
		}
?>
		</td>
		<td width="10%" nowrap="nowrap" align="center">
			<?php echo $file_types[$row['file_category']]; ?>
		</td>
		<td width="5%" align="center">
			<a href="?m=tasks&amp;a=view&amp;task_id=<?php echo $row['file_task']; ?>">
			<?php echo $row['task_name']; ?>
			</a>
		</td>
		<td width="15%" nowrap="nowrap">
			<?php echo $AppUI->___($row["contact_first_name"] . ' ' . $row["contact_last_name"]); ?>
		</td>
		<td width="5%" nowrap="nowrap" align="right">
			<?php echo file_size(intval($row['file_size'])); ?>
		</td>
		<td nowrap="nowrap">
			<?php echo ($row['file_type']); ?>
		</td>
		<td width="10%" nowrap="nowrap" align="right">
			<?php echo $file_date->format($df . ' ' . $tf); ?>
		</td>
		<td width="15%">
			<?php echo $AppUI->___($row['file_co_reason']); ?> <?php
	if (!(empty($row['file_checkout']))
	    && ($row['file_checkout'] == $AppUI->user_id
	        || ($canEdit && ($canAdmin || $row['project_owner'] == $AppUI->user_id)))) {
?>
			<a href="?m=files&amp;a=co&amp;co_cancel=1&amp;file_id=<?php
		echo $row['file_id']; ?>">
			<?php
		echo dPshowImage((DP_BASE_URL . '/images/icons/stock_cancel-16.png'), '16', '16',
		                 'cancel checkout', 'cancel file checkout');
?>
			</a><?php
} ?>
		</td>
		<td nowrap="nowrap" align="center">
			<?php
		if ($canEdit && empty($row['file_checkout'])) {
?>
			<a href="?m=files&amp;a=co&amp;file_id=<?php echo $row['file_id']; ?>">
			<?php
			echo dPshowImage(DP_BASE_URL . '/modules/files/images/co.png', '16', '16',
			                 'checkout','checkout file'); ?>
			</a><?php
		} else if ($row['file_checkout'] == $AppUI->user_id) {
?>
			<a href="?m=files&amp;a=addedit&amp;ci=1&amp;file_id=<?php echo $row['file_id']; ?>">
			<?php
			echo dPshowImage(DP_BASE_URL . '/modules/files/images/ci.png', '16', '16',
			                 'checkin','checkin file'); ?>
			</a><?php
		} else if ($file['file_checkout'] == 'final') {
			echo $AppUI->_('final');
		} else {
			echo ($AppUI->___('	  ' . $file['co_contact_first_name'] . ' '
			                  . $file['co_contact_last_name']) . '<br />'
				  . $AppUI->___('(' . $file['co_user'] . ')'));
			}
?>
		</td>
		<td nowrap="nowrap" align="right" width="48">
			<?php
		if (empty($row['file_checkout']) || $row['file_checkout'] == 'final') {
			// Edit File
			if ($canEdit && (empty($row['file_checkout'])
			                 || ($row['file_checkout'] == 'final'
			                     && ($canAdmin || $row['project_owner'] == $AppUI->user_id)))) {
?>
			<a href="?m=files&amp;a=addedit&amp;file_id=<?php echo $row['file_id']; ?>">
			<?php
				echo (dPshowImage(DP_BASE_URL . '/modules/files/images/kedit.png', '16', '16',
				                  'edit file', 'edit file'));
?>
			</a><?php
			}
			// Duplicate File
			if ($canAuthor || $row['project_owner'] == $AppUI->user_id) {
?>
			<a href="#"  onclick="javascript:document.frm_duplicate_file_<?php
				echo $row['file_id']; ?>.submit()">
			<?php
				echo (dPshowImage(DP_BASE_URL . '/modules/files/images/duplicate.png', '16', '16',
				                  'duplicate file', 'duplicate file'));
?>
			</a><?php
			}
			// Delete File
			if ($canDelete || $row['project_owner'] == $AppUI->user_id) {
?>
			<a href="#" onclick="if (confirm('Are you sure you want to delete this file?')) {javascript:document.frm_remove_file_<?php
				echo $row['file_id']; ?>.submit()}">
			<?php
				echo (dPshowImage(DP_BASE_URL . '/modules/files/images/remove.png', '16', '16',
				                  'delete file', 'delete file'));
?>
			</a><?php
			}
		}
?>
		</td>
		<td nowrap="nowrap" align="center" width="1"><?php
		if ((empty($row['file_checkout']) || $row['file_checkout'] == 'final')
		    && ($canEdit || $row['project_owner'] == $AppUI->user_id)) {
			$bulk_op = ('onchange="javascript:(this.checked) ? addBulkComponent(' . $row['file_id']
						. ') : removeBulkComponent(' . $row['file_id'] . ')"');
?>
			<input type="checkbox" <?php echo $bulk_op; ?> name="chk_sub_sel_file_<?php
			echo $file_row['file_id'] ?? ""; ?>" /><?php
		}
?>
		</td>
	</tr><?php
		if ($row['file_versions'] > 1) {
?>
	<tr>
		<td colspan="20">
		<table style="display: none" id="versions_<?php echo $row['file_id'];
?>" width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
		<tr>
			<th nowrap="nowrap"><?php echo $AppUI->_('File Name'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Description'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Versions'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Category'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Task Name'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Owner'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Size'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Type'); ?></th>
			<th nowrap="nowrap"><?php echo $AppUI->_('Date'); ?></th>
			<th nowrap="nowrap"width="1">&nbsp;</th>
			<th nowrap="nowrap"width="1">&nbsp;</th>
		</tr><?php
			foreach ($file_versions as $file) {
				if ($file['file_version_id'] == $row['file_version_id']) {
					$file_icon = getIcon($file['file_type']);
					$file_version_date = new Date($file['file_date']);
?>
		<tr>
			<td nowrap="8%">
		<form name="frm_delete_sub_file_<?php echo $file['file_id'];
?>" action="?m=files" method="post">
		<input type="hidden" name="dosql" value="do_file_aed" />
		<input type="hidden" name="del" value="1" />
		<input type="hidden" name="file_id" value="<?php echo $file['file_id']; ?>" />
		<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
		</form>
		<form name="frm_duplicate_sub_file_<?php echo $file['file_id'];
?>" action="?m=files" method="post">
		<input type="hidden" name="dosql" value="do_file_aed" />
		<input type="hidden" name="duplicate" value="1" />
		<input type="hidden" name="file_id" value="<?php echo $file['file_id']; ?>" />
		<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
		</form>
				<a href="./fileviewer.php?file_id=<?php echo $file['file_id'];
?>" title="<?php echo $file['file_description']; ?>">
				<?php
					echo dPshowImage((DP_BASE_URL . '/modules/files/images/' . $file_icon), '16',
					                 '16');
?>
				<?php echo $AppUI->___($file['file_name']); ?>
				</a>
			</td>
			<td width="20%"><?php echo $AppUI->___($file['file_description']); ?></td>
			<td width="5%" nowrap="nowrap" align="center"><?php echo $file['file_version']; ?></td>
			<td width="10%" nowrap="nowrap" align="center">
				<?php echo $file_types[$file['file_category']]; ?>
			</td>
			<td width="5%" align="center">
				<a href="?m=tasks&amp;a=view&amp;task_id=<?php echo $file['file_task']; ?>">
				<?php echo $AppUI->___($file['task_name']); ?>
				</a>
			</td>
			<td width="15%" nowrap="nowrap">
				<?php echo $AppUI->___($file["contact_first_name"] . ' ' . $file["contact_last_name"]); ?>
			</td>
			<td width="5%" nowrap="nowrap" align="right">
			  <?php echo file_size(intval($file['file_size'])); ?>
			</td>
			<td nowrap="nowrap">
				<?php echo ($row['file_type']); ?>
			</td>
			<td width="15%" nowrap="nowrap" align="right">
				<?php echo $file_version_date->format($df . ' ' . $tf); ?>
			</td>

			<td nowrap="nowrap" align="right" width="48"><?php
					if (empty($file['file_checkout']) || $file['file_checkout'] == 'final') {
						// Edit File
						if ($canEdit && $dPconfig['files_show_versions_edit']
						    && ($canAdmin || $file['project_owner'] == $AppUI->user_id
						        || $file['file_co_reason'] == '')) {
?>
				<a href="?m=files&amp;a=addedit&amp;file_id=<?php echo $row['file_id'];?>">
				<?php
							echo (dPshowImage(DP_BASE_URL . '/modules/files/images/kedit.png',
							                  '16', '16', 'edit file', 'edit file'));
?>
				</a><?php
						}
						// Duplicate File
						if ($canAuthor) {
?>
				<a href="#" onclick="javascript:document.frm_duplicate_file_<?php echo $row['file_id'];
?>.submit()">
				<?php
							echo (dPshowImage(DP_BASE_URL . '/modules/files/images/duplicate.png',
							                  '16', '16', 'duplicate file', 'duplicate file'));
?>
				</a><?php
						}
						// Delete File
						if ($canDelete && $dPconfig['files_show_versions_edit']
						    && ($canAdmin || $file['project_owner'] == $AppUI->user_id
						        || $file['file_co_reason'] == '')) {
?>
				<a href="#" onclick="if (confirm('<?php
							echo $AppUI->_('Are you sure you want to delete this file?');
?>')) {javascript:document.frm_remove_file_<?php echo $row['file_id']; ?>.submit()}">
				<?php
							echo (dPshowImage(DP_BASE_URL . '/modules/files/images/remove.png',
							                  '16', '16', 'delete file', $AppUI->_('delete file')));
?>
				</a><?php
						}
					}
?>
			</td>
			<td nowrap="nowrap" align="center" width="1"><?php
					if ((empty($row['file_checkout'])
					     || $row['file_checkout'] == 'final')
						&& ($canEdit || $row['project_owner'] == $AppUI->user_id)) {
						$bulk_op = ('onchange="javascript:(this.checked) ? addBulkComponent('
						            . $row['file_id'] . ') : removeBulkComponent('
						            . $row['file_id'] . ')"');
?>
				<input type="checkbox" <?php echo $bulk_op; ?> name="chk_sub_sel_file_<?php
						echo $file_row['file_id']; ?>" /><?php
					}
?>
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
	shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page, $folder_id);
	echo "<br />";
}

/**** Main Program ****/

$canAccess_this_folder = getPermission('file_folders', 'access', $folder);
$canRead_this_folder = getPermission('file_folders', 'view', $folder);
$canEdit_this_folder = getPermission('file_folders', 'edit', $folder);

if (!($canAccess_folders || $canAccess_this_folder)) {
	$AppUI->redirect("m=public&a=access_denied");
}

if (!empty($folder) && $folder > 0) {
	$cfObj->load($folder);
	$msg = '';
	$canDelete = $cfObj->canDelete($msg, $folder);
}
?>

<script language="javascript">
function expand(id) {
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "block" : "none";
}
function addBulkComponent(li) {
	var form = document.frm_bulk;
	var ni = document.getElementById('tbl_bulk');
	var newitem = document.createElement('input');

	if (document.all || navigator.appName == "Microsoft Internet Explorer") { //IE
		var htmltxt = "";
		newitem.id = 'bulk_selected_file['+li+']';
		newitem.name = 'bulk_selected_file['+li+']';
		newitem.type = 'hidden';
	} else { //Non IE
		newitem.setAttribute("id",'bulk_selected_file['+li+']');
		newitem.setAttribute("name",'bulk_selected_file['+li+']');
		newitem.setAttribute("type",'hidden');
	}

	ni.appendChild(newitem);
}

function delCheck(ffid) {
<?php
$trans_del =  $AppUI->_('Are you sure you want to delete this folder?');
?>
	if (confirm('<?php echo $trans_del ; ?>')) {
		var submit_me = document.getElementById('frm_remove_folder_'+ffid);
		submit_me.submit();
	}
}

function goCheck() {
<?php
$trans_go =  $AppUI->_('Are you sure you wish to apply the options on the selected files?');
?>
	if (confirm('<?php echo $trans_go ; ?>')) {
		document.frm_bulk.submit();
	}
}


function removeBulkComponent(li) {
      var t = document.getElementById('tbl_bulk');
      var old = document.getElementById('bulk_selected_file['+li+']');
      t.removeChild(old);
}
</script>
<?php
if (!empty($folder)) {
?>
<table border="0" cellpadding="4" cellspacing="0" width="100%">
	<tr>
		<td nowrap="nowrap">
<?php
	echo ("\t\t\t" . '<a href="?m=' . $m . '&amp;a=' . $a . '&amp;tab=' . $tab . '&amp;folder=0">'
	      . "\n");
	echo ("\t\t\t" . dPshowImage(DP_BASE_URL . '/modules/files/images/home.png', '22', '22',
	                             'folder icon', $AppUI->_('back to root folder')) . "\n");
	echo ("\t\t\t" . '</a>' . "\n");

	$canRead_parent = (!empty($folder)
	                   ? getPermission('file_folders', 'view', $cfObj->file_folder_parent) : true);
	if ($canRead_parent) {
		echo ("\t\t\t" . '<a href="?m=' . $m . '&amp;a=' . $a . '&amp;tab=' . $tab . '&amp;folder='
		      . $cfObj->file_folder_parent . '">' . "\n");
		echo ("\t\t\t" . dPshowImage(DP_BASE_URL . '/modules/files/images/back.png', '22', '22',
		                             'folder icon', $AppUI->_('back to parent folder')) . "\n");
		echo ("\t\t\t" . '</a>' . "\n");
	}

	if ($canEdit_this_folder) {
		echo ("\t\t\t" . '<a href="?m=' . $m . '&amp;a=addedit_folder&amp;tab=' . $tab
		      . '&folder=' . $cfObj->file_folder_id . '" title="' . $AppUI->_('edit folder')
		      . ': '. $cfObj->file_folder_name . '">' . "\n");
		echo ("\t\t\t" . dPshowImage(DP_BASE_URL . '/modules/files/images/filesaveas.png',
		                             '22', '22', 'folder icon', $AppUI->_('edit folder')) . "\n");
		echo ("\t\t\t" . '</a>' . "\n");
	}
?>
		</td>
	</tr>
</table>
<?php
}
?>

<div id="folder-list">
<?php
displayFolders($folder);
?>
</div>

<hr />

<table border="0" cellpadding="4" cellspacing="0" width="100%">
<?php
/*
 * Add drop-downs for "bulk" changes
 * Used 'O' (uppercase letter) instead of 0 (number)
 * for "header option" ids so things would print right
 */


//project drop-down: allowed Projects only
//get list of allowed projects
$project = new CProject();
$projects_list = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name',
                                             'project_name', null, $extra);
//getting company names (to go with project name in drop-down)
$q = new DBQuery;
$q->addTable('projects', 'p');
$q->addJoin('companies', 'co', 'co.company_id = p.project_company');
$q->addQuery('p.project_id, co.company_name');
$q->addWhere('p.project_id IN (0' . ((count($projects_list))
                                     ? (',' . implode(',', array_keys($projects_list)))
                                     : '') . ')');
$proj_companies = $q->loadHashList();
$q->clear();

//folder drop-down: allowed Folders only
$folders_list = getFolderSelectList();
$folders_list = arrayMerge(array('O' => array('O', ('(' . $AppUI->_('Move to Folder') . ')'), -1)),
                           $folders_list);

foreach ($projects_list as $prj_id => $prj_name) {
	$projects_list[$prj_id] = $proj_companies[$prj_id].': '.$prj_name;
}
asort($projects_list);

$projects_list = arrayMerge(array('O' => ('(' . $AppUI->_('Move to Project') . ')'),
                                  '0' => ('(' . $AppUI->_('No Project Association') . ')')),
							$projects_list);
?>
	<tr>
		<td colspan="50" align="right">
			<form name='frm_bulk' method='POST' action='?m=files&amp;a=do_files_bulk_aed'>
			<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
			<table id="tbl_bulk" name="tbl_bulk">
				<tr>
					<td>
<?php
echo ("\t\t\t\t\t\t" . arraySelect($projects_list, 'bulk_file_project',
                                   'style="width:180px" class="text"', 'O'));
?>
					</td>
					<td>
<?php
echo ("\t\t\t\t\t\t" . arraySelectTree($folders_list, 'bulk_file_folder',
                                       'style="width:180px;" class="text"', 'O'));
?>
					</td>
					<td align="right">
						<input type="button" class="button" value="<?php echo $AppUI->_('Go');?>"
						 onclick="javascript:goCheck();" />
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>

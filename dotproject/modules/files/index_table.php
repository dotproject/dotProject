<?php
/* FILES $Id$ */
// modified later by Pablo Roca (proca) in 18 August 2003 - added page support
// Files modules: index page re-usable sub-table
GLOBAL $AppUI, $deny1, $canRead, $canEdit, $canAdmin;
global $company_id, $project_id, $task_id;

global $currentTabId;
global $currentTabName;
global $tabbed;

//require_once( dPgetConfig( 'root_dir' )."/modules/files/index_table.lib.php");

// ****************************************************************************
// Page numbering variables
// Pablo Roca (pabloroca@Xmvps.org) (Remove the X)
// 19 August 2003
//
// $tab             - file category
// $page            - actual page to show
// $xpg_pagesize    - max rows per page
// $xpg_min         - initial record in the SELECT LIMIT
// $xpg_totalrecs   - total rows selected
// $xpg_sqlrecs     - total rows from SELECT LIMIT
// $xpg_total_pages - total pages
// $xpg_next_page   - next pagenumber
// $xpg_prev_page   - previous pagenumber
// $xpg_break       - stop showing page numbered list?
// $xpg_sqlcount    - SELECT for the COUNT total
// $xpg_sqlquery    - SELECT for the SELECT LIMIT
// $xpg_result      - pointer to results from SELECT LIMIT

$tab = (!$company_id && !$project_id && !$task_id) ? $currentTabId : 0;
$page = dPgetParam( $_GET, "page", 1);
if (!isset($project_id))
        $project_id = dPgetParam( $_REQUEST, 'project_id', 0);
if (!isset($showProject))
        $showProject = true;

$xpg_pagesize = 30;
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from

// load the following classes to retrieved denied records
include_once $AppUI->getModuleClass( 'projects' );
include_once $AppUI->getModuleClass( 'tasks' );
require_once $AppUI->getSystemClass( 'query' );
require_once $AppUI->getModuleClass( 'files' );

$project = new CProject();
$task = new CTask();

$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

$file_types = dPgetSysVal("FileType");
if ($tabbed) {
      if ($tab <= 0)
              $catsql = false;
      else
              $catsql = "file_category = " . --$tab ;
} elseif ($company_id || $project_id || $task_id) {
      $catsql = false;
} else {
      if ($tab < 0)
              $catsql = false;
      else
              $catsql = "file_category = " . $tab ;
}
// SQL text for count the total recs from the selected option
$q = new DBQuery;
$q->addQuery('count(file_id)');
$q->addTable('files', 'f');
if ($catsql) $q->addWhere($catsql);
if ($company_id) $q->addWhere("project_company = $company_id");
if ($project_id) $q->addWhere("file_project = $project_id");
if ($task_id) $q->addWhere("file_task = $task_id");
$q->addGroup("file_version_id");
$project->setAllowedSQL($AppUI->user_id, $q, 'file_project');
$task->setAllowedSQL($AppUI->user_id, $q, 'file_task');

// SETUP FOR FILE LIST
$q2 = new DBQuery;
$q2->addQuery(array ('f.*',
	'max(f.file_id) as  latest_id',
	'count(f.file_version) as file_versions',
	'round(max(f.file_version),2) as file_lastversion'//,
//	'project_name',
//	'project_color_identifier',
//	'cont.contact_first_name',
//	'cont.contact_last_name',
//	'task_name',
//	'task_id',
//	'cu.user_username as co_user'
));
$q2->addTable('files', 'f');
//$q2->leftJoin('users', 'cu', 'cu.user_id = f.file_checkout');
//$q2->leftJoin('users', 'u', 'u.user_id = f.file_owner');
//$q2->leftJoin('contacts', 'cont', 'cont.contact_id = u.user_contact');
$project->setAllowedSQL($AppUI->user_id, $q2, 'file_project');
$task->setAllowedSQL($AppUI->user_id, $q2, 'file_task');
if ($catsql) $q2->addWhere($catsql);
if ($company_id) $q2->addWhere("project_company = $company_id");
if ($project_id) $q2->addWhere("file_project = $project_id");
if ($task_id) $q2->addWhere("file_task = $task_id");
$q2->setLimit($xpg_pagesize, $xpg_min);
// Adding an Order by that is different to a group by can cause
// performance issues. It is far better to rearrange the group
// by to get the correct ordering.
$q2->addGroup('project_id');
$q2->addGroup('file_version_id DESC');

$q3 = new DBQuery;
$q3->addQuery("file_id, file_version, file_version_id, file_project, file_name, file_task, task_name, file_description, file_checkout, file_co_reason, u.user_username as file_owner, file_size, file_category, file_type, file_date, cu.user_username as co_user, project_name, project_color_identifier, project_owner, contact_first_name, contact_last_name");
$q3->addTable('files');
$q3->leftJoin('users', 'cu', 'cu.user_id = file_checkout');
$q3->leftJoin('users', 'u', 'u.user_id = file_owner');
$q3->leftJoin('contacts', 'con', 'con.contact_id = u.user_contact');
//$q3->leftJoin('tasks', 't', 't.task_id = file_task');
//$q3->leftJoin('projects', 'p', 'p.project_id = file_project');
$project->setAllowedSQL($AppUI->user_id, $q3, 'file_project');
$task->setAllowedSQL($AppUI->user_id, $q3, 'file_task');
if ($project_id) $q3->addWhere("file_project = $project_id");
if ($task_id) $q3->addWhere("file_task = $task_id");

$files = array();
$file_versions = array();
if ($canRead) {
	
	$files = $q2->loadList();
	$file_versions = $q3->loadHashList('file_id');
}
// counts total recs from selection
$xpg_totalrecs = count($q->loadList());

// How many pages are we dealing with here ??
$xpg_total_pages = ($xpg_totalrecs > $xpg_pagesize) ? ceil($xpg_totalrecs / $xpg_pagesize) : 1;

shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page);

?>
<script type="text/JavaScript">
function expand(id){
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "block" : "none";
}
</script>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">&nbsp;</th>
        <th nowrap="nowrap"><?php echo $AppUI->_('co') ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_('Checkout Reason') ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'File Name' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Description' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Versions' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Task Name' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Owner' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Size' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Type' );?></a></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Date' );?></th>
</tr>
<?php
$fp=-1;
$file_date = new CDate();

$id = 0;
foreach ($files as $file_row) {
        $latest_file = $file_versions[$file_row['latest_id']];
	$file_date = new CDate( $latest_file['file_date'] );

	if ($fp != $latest_file["file_project"]) {
		if (!$latest_file["project_name"]) {
			$latest_file["project_name"] = $AppUI->_('All Projects');
			$latest_file["project_color_identifier"] = 'f4efe3';
		}
		if ($showProject) {
			$style = "background-color:#$latest_file[project_color_identifier];color:" . bestColor($latest_file["project_color_identifier"]);
			$s = '<tr>';
			$s .= '<td colspan="12" style="border: outset 2px #eeeeee;' . $style . '">';
			$s .= '<a href="?m=projects&a=view&project_id=' . $latest_file['file_project'] . '">';
			$s .= '<span style="' . $style . '">' . $latest_file["project_name"] . '</span></a>';
			$s .= '</td></tr>';
			echo $s;
		}
	}
	$fp = $latest_file["file_project"];
//        if ($row['file_versions'] > 1)
//                $file = last_file($file_versions, $row['file_name'], $row['file_project']);
//        else 
//                $file = $latest_file;
?>
<tr>
	<td nowrap="nowrap" width="20">
	<?php if ($canEdit && ( empty($latest_file['file_checkout']) || ( $latest_file['file_checkout'] == 'final' && ($canEdit || $latest_file['project_owner'] == $AppUI->user_id) ))) {
		echo "\n".'<a href="./index.php?m=files&a=addedit&file_id=' . $latest_file["file_id"] . '">';
		echo dPshowImage( './images/icons/stock_edit-16.png', '16', '16' );
		echo "\n</a>";
	}
	?>
	</td>
        <td nowrap="nowrap">
        <?php if ($canEdit && empty($latest_file['file_checkout']) ) {
        ?>
                <a href="?m=files&a=co&file_id=<?php echo $latest_file['file_id']; ?>"><?php echo dPshowImage( './images/icons/co.png', '20', '20' ); ?></a>
        <?php }
        else if ($latest_file['file_checkout'] == $AppUI->user_id) { ?>
                <a href="?m=files&a=addedit&ci=1&file_id=<?php echo $latest_file['file_id']; ?>"><?php echo dPshowImage( './images/icons/ci.png', '20','20'); ?></a>
        <?php }
        else { 
                if ($latest_file['file_checkout'] == 'final'){
                        echo 'final';
                } else {
						$q4 = new DBQuery;
						$q4->addQuery("file_id, file_checkout, user_username as co_user, contact_first_name, contact_last_name");
						$q4->addTable('files');
						$q4->leftJoin('users', 'cu', 'cu.user_id = file_checkout');
						$q4->leftJoin('contacts', 'co', 'co.contact_id = cu.user_contact');
						$q4->addWhere('file_id = '.$latest_file['file_id']);
						$co_user = array();
						$co_user = $q4->loadList();
						$co_user = $co_user[0];
						$q4->clear();
                        echo $co_user['contact_first_name'].' '.$co_user['contact_last_name'].'<br>('.$co_user['co_user'].')'; 
		}
        }
        ?>
                
        </td>
        <td width="10%"><?php echo $latest_file['file_co_reason']; ?></td>
	<td nowrap="8%">
		<?php 
$fnamelen = 32;
$filename = $latest_file['file_name'];
if (strlen($latest_file['file_name']) > $fnamelen+9)
{
	$ext = substr($filename, strrpos($filename, '.')+1);
	$filename = substr($filename, 0, $fnamelen);
	$filename .= '[...].' . $ext;
}
echo "
<a href=\"./fileviewer.php?file_id={$latest_file['file_id']}\" title=\"{$latest_file['file_description']}\">$filename</a>";
//	{$latest_file['file_name']}
		?>
	</td>
	<td width="20%"><?php echo $latest_file['file_description'];?></td>
	<td width="5%" nowrap="nowrap" align="center">
        <?php
                $hidden_table = '';
                echo $file_row['file_lastversion'];
                if ($file_row['file_versions'] > 1)
                {
                 echo ' <a href="#" onClick="expand(\'versions_' . ++$id . '\'); ">(' . $file_row['file_versions'] . ')</a>';
                 $hidden_table = '<tr><td colspan="12">
<table style="display: none" id="versions_' . $id++ . '" width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
        <th nowrap="nowrap">&nbsp;</th>
        <th nowrap="nowrap">' . $AppUI->_( 'File Name' ) . '</th>
        <th nowrap="nowrap">' . $AppUI->_( 'Description' ) . '</th>
        <th nowrap="nowrap">' . $AppUI->_( 'Versions' ) . '</th>
        <th nowrap="nowrap">' . $AppUI->_( 'Task Name' ) . '</th>
        <th nowrap="nowrap">' . $AppUI->_( 'Owner' ) . '</th>
        <th nowrap="nowrap">' . $AppUI->_( 'Size' ) . '</th>
        <th nowrap="nowrap">' . $AppUI->_( 'Type' ) . '</a></th>
        <th nowrap="nowrap">' . $AppUI->_( 'Date' ) . '</th>
</tr>
';
                foreach($file_versions as $file)
                        if ($file['file_version_id'] == $latest_file['file_version_id'])
                        {
				$hdate = new Date($file['file_date']);
                                $hidden_table .= '
        <tr>
                <td nowrap="nowrap" width="20">&nbsp;';
                                if ($canEdit && $dPconfig['files_show_versions_edit'])
                                {
                                        $hidden_table .= '
                <a href="./index.php?m=files&a=addedit&file_id=' . $file["file_id"] . '">' . dPshowImage( './images/icons/stock_edit-16.png', '16', '16' ) . "\n</a>";
                                }
                                $hidden_table .= '
                </td>
                <td nowrap="8%"><a href="./fileviewer.php?file_id=' . $file['file_id'] . '" 
                        title="' . $file['file_description'] . '">' . 
                        $file['file_name'] . '
                </a></td>
                <td width="20%">' . $file['file_description'] . '</td>
                <td width="5%" nowrap="nowrap" align="center">' . $file['file_version'] . '</td>
                <td width="5%" align="center"><a href="./index.php?m=tasks&a=view&task_id=' . $file['file_task'] . '">' . $file['task_name'] . '</a></td>
                <td width="15%" nowrap="nowrap">' . $file["contact_first_name"].' '.$file["contact_last_name"] . '</td>
                <td width="5%" nowrap="nowrap" align="right">' . file_size(intval($file['file_size'])) . '</td>
                <td nowrap="nowrap">' . substr($file['file_type'], strpos($file['file_type'], '/')+1) . '</td>
                <td width="15%" nowrap="nowrap" align="right">' . $hdate->format("$df $tf") . '</td>
        </tr>';
                        }
                $hidden_table .= '</table>';
                //$hidden_table .= '</span>';
                }
        ?>
        </td>
	<td width="5%" align="center"><a href="./index.php?m=tasks&a=view&task_id=<?php echo $latest_file["file_task"];?>"><?php echo $latest_file["task_name"];?></a></td>
	<td width="15%" nowrap="nowrap"><?php echo $latest_file["contact_first_name"].' '.$latest_file["contact_last_name"];?></td>
	<td width="5%" nowrap="nowrap" align="right"><?php echo file_size(intval($latest_file["file_size"]));?></td>
	<td nowrap="nowrap"><?php echo $AppUI->_(substr($latest_file['file_type'], strpos($latest_file['file_type'], '/')+1));?></td>
	<td width="15%" nowrap="nowrap" align="right"><?php echo $file_date->format( "$df $tf" );?></td>
</tr>
<?php 
	echo $hidden_table; 
        $hidden_table = ''; 
}?>
</table>
<?php
shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page);
?>

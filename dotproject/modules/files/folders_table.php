<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

/* FILES $Id$ */
// modified later by Pablo Roca (proca) in 18 August 2003 - added page support
// Files modules: index page re-usable sub-table

// add to allow for returning to other modules besides Files
$current_uriArray = parse_url($_SERVER['REQUEST_URI']);
$current_uri = $current_uriArray['query'] . $current_uriArray['fragment'];

function showfnavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page, $folder)
{

	global $AppUI, $tab, $m, $a;
	$xpg_break = false;
	$xpg_prev_page = $xpg_next_page = 1;
	
	echo "\t<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";

	if ($xpg_totalrecs > $xpg_pagesize) {
		$xpg_prev_page = $page - 1;
		$xpg_next_page = $page + 1;
		// left buttoms
		if ($xpg_prev_page > 0) {
			echo "<td align='left' width='15%'>";
			echo '<a href="./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&amp;folder='.$folder.'&amp;page=1">';
			echo '<img src="images/navfirst.gif" border="0" Alt="First Page"></a>&nbsp;&nbsp;';
			echo '<a href="./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&amp;folder='.$folder.'&amp;page=' . $xpg_prev_page . '">';
			echo "<img src=\"images/navleft.gif\" border=\"0\" Alt=\"Previous page ($xpg_prev_page)\"></a></td>";
		} else {
			echo "<td width='15%'>&nbsp;</td>\n";
		} 
		
		// central text (files, total pages, ...)
		echo "<td align='center' width='70%'>";
		//echo "$xpg_totalrecs " . $AppUI->_('File(s)') . " ($xpg_total_pages " . $AppUI->_('Page(s)') . ")";
		echo "$xpg_totalrecs " . $AppUI->_('File(s)') . " " . $AppUI->_('Pages') . ":";
		echo " [ ";
		
		// begin page numbers
		for($n = $page > 16 ? $page-16 : 1; $n <= $xpg_total_pages; $n++) {
			if ($n == $page) {
				echo "<b>$n</b></a>";
			} else {
				echo "<a href='./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&amp;folder={$folder}&amp;page=$n'>";
				echo $n . "</a>";
			} 
			if ($n >= 30+$page-15) {
				$xpg_break = true;
				break;
			} else if ($n < $xpg_total_pages) {
				echo " | ";
			} 
		} 
	
		if (!isset($xpg_break)) { // are we supposed to break ?
			if ($n == $page) {
				echo "<" . $n . "</a>";
			} else {
				echo "<a href='./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&amp;page=$xpg_total_pages'>";
				echo $n . "</a>";
			} 
		} 
		echo " ] ";
		// end page numbers
		
		echo "</td>";

		// right buttoms
		if ($xpg_next_page <= $xpg_total_pages) {
			echo "<td align='right' width='15%'>";
			echo '<a href="./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&amp;folder='.$folder.'&amp;page='.$xpg_next_page.'">';
			echo '<img src="images/navright.gif" border="0" Alt="Next Page ('.$xpg_next_page.')"></a>&nbsp;&nbsp;';
			echo '<a href="./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&amp;folder='.$folder.'&amp;page=' . $xpg_total_pages . '">';
			echo '<img src="images/navlast.gif" border="0" Alt="Last Page"></a></td>';
		} else {
			echo "<td width='15%'>&nbsp;</td></tr>\n";
		}
		// Page numbered list, up to 30 pages
		//echo "<tr><td colspan=\"3\" align=\"center\">";
// was page numbers
		//echo "</td></tr>";
	} else { // or we dont have any files..
		echo "<td align='center'>";
		if ($xpg_next_page > $xpg_total_pages) {
		echo $xpg_sqlrecs . " " . "Files" . " ";
		}
		echo "</td></tr>";
	} 
	echo "</table>";
}

global $AppUI, $deny1, $canRead, $canEdit, $allowed_folders_ary, $denied_folders_ary, $tab, $folder, $cfObj, $m, $a, $company_id, $allowed_companies, $showProject;

//require_once( DP_BASE_DIR."/modules/files/index_table.lib.php");

// ****************************************************************************
// Page numbering variables
// Pablo Roca (pabloroca@Xmvps.org) (Remove the X)
// 19 August 2003
//
// $folder          - current folder
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

$page = dPgetParam( $_GET, "page", 1);

if (!isset($project_id)) {
        $project_id = dPgetParam( $_REQUEST, 'project_id', 0);
}
if (!$project_id) {
        $showProject = true;
}

// get company to filter files by
//if (isset( $_POST['company_id'] )) {
//	$AppUI->setState( 'FileIdxCompany', intval( $_POST['company_id'] ) );
//}
//$company_id = $AppUI->getState( 'FileIdxCompany' ) !== NULL ? $AppUI->getState( 'FileIdxCompany' ) : $AppUI->user_company;
if (!isset($company_id)) {
        $company_id = dPgetParam( $_REQUEST, 'company_id', 0);
}

$obj = new CCompany();
$allowed_companies_ary = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$allowed_companies = implode( ",", array_keys($allowed_companies_ary) );

if (!isset($task_id)) {
        $task_id = dPgetParam( $_REQUEST, 'task_id', 0);
}

global $xpg_min, $xpg_pagesize;
$xpg_pagesize = 30;
$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from

// load the following classes to retrieved denied records
include_once( $AppUI->getModuleClass( 'projects' ) );
include_once( $AppUI->getModuleClass( 'tasks' ) );

$project = new CProject();
$deny1 = $project->getDeniedRecords( $AppUI->user_id );

$task = new CTask();
$deny2 = $task->getDeniedRecords( $AppUI->user_id );

global $file_types;
$file_types = dPgetSysVal("FileType");

$folder = $folder ? $folder : 0;

// SQL text for count the total recs from the selected option
$q = new DBQuery();
$q->addTable('files');
$q->addQuery('count(files.file_id)');
$q->addJoin('projects','p','p.project_id = file_project');
$q->addJoin('users','u','u.user_id = file_owner');
$q->addJoin('tasks','t','t.task_id = file_task');
$q->addJoin('file_folders','ff','ff.file_folder_id = file_folder');
$q->addWhere('file_folder = '. $folder);
if (count( $deny1 ) > 0)
	$q->addWhere('file_project NOT IN (' . implode( ',', $deny1 ) . ')');
if (count( $deny2 ) > 0)
	$q->addWhere('file_task NOT IN (' . implode( ',', $deny2 ) . ')');
if ($project_id)
	$q->addWhere('file_project = '. $project_id);
if ($task_id)
	$q->addWhere('file_task = '. $task_id);
if ($company_id) {
	$q->innerJoin('companies','co','co.company_id = p.project_company');
	$q->addWhere('company_id = '. $company_id);
	$q->addWhere('company_id IN (' . $allowed_companies  . ')');
}
	
$q->addGroup('file_folder_name');
$q->addGroup('project_name');
$q->addGroup('file_name');

$xpg_sqlcount = $q->prepare();
$q->clear();

// counts total recs from selection
$xpg_totalrecs = count(db_loadList($xpg_sqlcount));

// How many pages are we dealing with here ??
$xpg_total_pages = ($xpg_totalrecs > $xpg_pagesize) ? ceil($xpg_totalrecs / $xpg_pagesize) : 1;

//shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page, $folder);

?>
<script type="text/JavaScript">
function expand(id){
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "block" : "none";
}
function addBulkComponent(li) {
//IE
  if (document.all || navigator.appName == "Microsoft Internet Explorer") {
	var form = document.frm_bulk;
      var ni = document.getElementById('tbl_bulk');
      var newitem = document.createElement('input');
      var htmltxt = "";
      newitem.id = 'bulk_selected_file['+li+']';
      newitem.name = 'bulk_selected_file['+li+']';
      newitem.type = 'hidden';
      ni.appendChild(newitem);
  } else {
//Non IE
	var form = document.frm_bulk;
      var ni = document.getElementById('tbl_bulk');
      var newitem = document.createElement('input');
      newitem.setAttribute("id",'bulk_selected_file['+li+']');
      newitem.setAttribute("name",'bulk_selected_file['+li+']');
      newitem.setAttribute("type",'hidden');
      ni.appendChild(newitem);
  }
}

function removeBulkComponent(li) {
      var t = document.getElementById('tbl_bulk');
      var old = document.getElementById('bulk_selected_file['+li+']');
      t.removeChild(old);
}
</script>
<style>
#folder-list {
/*  margin-left: -25px;*/
}
#folder-list ul {
  padding: 0;
  margin: 0;
}
#folder-list ul li {
  list-style: none;
  margin-top: -1px;
  margin-bottom: 0px;
  border: 0px solid #CCC;
}
#folder-list ul li ul li {
  margin-left: 25px;
}

.folder-name {
  display: block;
  height: 16px;
  padding-top: 0px;
  background: white;
  border-bottom: 1px solid #333;
  border-right: 1px solid #333;
  margin-bottom: 0px;
}

.folder-name-current {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  border-bottom: 1px solid #333;
}

.has-files {
  font-weight: bold;
}

#folder-list .tbl {
  margin-top: 2px;
}
#folder-list .tbl th {
  border: none;
}

#folder-list p {
  padding: 3px 5px;
  margin-top: -5px;
  margin-left: 25px;
  margin-right: 25px;
  border: 1px solid #CCC;
  border-top: none;
  background: #F9F9F9;
}
</style>

<?php
// $parent is the parent of the children we want to see
// $level is increased when we go deeper into the tree,
//        used to display a nice indented tree
function getFolders($parent, $level=0) {
   global $AppUI, $allowed_folders_ary, $denied_folders_ary, $tab, $m, $a, $company_id, $allowed_companies, $project_id, $task_id, $current_uri, $file_types;
   // retrieve all children of $parent

   $folder_where = "file_folder_parent='$parent'";
//   $folder_where .= (count($denied_folders_ary) > 0) ? "\nAND file_folder_id NOT IN (" . implode(",", $denied_folders_ary) . ")" : "";

	$q = new DBQuery();
	$q->addTable('file_folders');
	$q->addQuery('*');
	$q->addWhere($folder_where);
	$q->addOrder('file_folder_name');
				
	$folder_sql = $q->prepare();
	$q->clear();

   $folders = db_loadList($folder_sql);

   // display each child
   foreach ($folders as $row) {
	if (array_key_exists($row['file_folder_id'], $allowed_folders_ary) OR array_key_exists($parent, $allowed_folders_ary)) {
		// indent and display the title of this child
		$file_count = countFiles($row['file_folder_id']);
			echo '<ul><li><table width="100%"><tr><td><span class="folder-name">';
			if ($m=='files')
				 echo '<a href="./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&folder='.$row['file_folder_id'].'" name="ff'.$row['file_folder_id'].'">';
				 
			echo dPshowImage( './modules/files/images/folder5_small.png', '16', '16', 'folder icon', 'show only this folder' );
			if ($m=='files') {
				 '</a>' .
				 '<a href="./index.php?m='.$m.'&amp;a='.$a.'&amp;tab='.$tab.'&folder='.$row['file_folder_id'].'" name="ff'.$row['file_folder_id'].'">';
			}
			echo $row['file_folder_name'];
			if ($m=='files') 
				  '</a>';
		if ( $file_count > 0 ) {
			echo ' <a href="#ff'.$row['file_folder_id'].'" onClick="expand(\'files_'.$row['file_folder_id'].'\')" class="has-files">('.$file_count.' files) +</a>';
		}
		echo "</td>\n";
		echo '	<form name="frm_remove_folder_'.$row['file_folder_id'].'" action="?m=files" method="post">
				<input type="hidden" name="dosql" value="do_folder_aed" />
				<input type="hidden" name="del" value="1" />
				<input type="hidden" name="file_folder_id" value="'.$row['file_folder_id'].'" />
				<input type="hidden" name="redirect" value="'.$current_uri.'" />
				</form>';		
		echo "<td align='right' width='64' nowrap>\n";
		echo '<a href="./index.php?m=files&amp;a=addedit_folder&amp;folder='.$row['file_folder_id'].'">' .
				 dPshowImage( './modules/files/images/filesaveas.png', '16', '16', 'edit icon', 'edit this folder' ) .
				 '</a>' .
				 '<a href="./index.php?m=files&amp;a=addedit_folder&amp;file_folder_parent='.$row['file_folder_id'].'&amp;file_folder_id=0">' .
				 dPshowImage( './modules/files/images/edit_add.png', '', '', 'new folder', 'add a new subfolder' ) .
				 '</a>' .
				 '<a href="#" onclick="if (confirm(\'Are you sure you want to delete this folder?\')) {document.frm_remove_folder_'.$row['file_folder_id'].'.submit()}">' .
				 dPshowImage( './modules/files/images/remove.png', '', '', 'delete icon', 'delete this folder' ) .
				 '</a>' .
				 '<a href="./index.php?m=files&amp;a=addedit&amp;folder='.$row['file_folder_id'].'&amp;project_id='.$project_id.'&amp;file_id=0">' .
				 dPshowImage( './modules/files/images/folder_new.png', '', '', 'new file', 'add new file to this folder' ) .
				 '</a>';
		echo "</td></tr></table></span>\n";
		if ($file_count > 0) {
			echo '<div class="files-list" id="files_'.$row['file_folder_id'].'" style="display: none;">';
			displayFiles($row['file_folder_id']);
			echo "</div>";
		}
	}
	// call this function again to display this
	// child's children
	if (!getFolders($row['file_folder_id'], $level+1)) {
		echo "</li>";
	} else {
       		echo "</li></ul>";
	}
   }
   return true;
}

function countFiles($folder) {
	global $AppUI, $company_id, $allowed_companies, $tab;
	global $deny1, $deny2, $project_id, $task_id, $showProject, $file_types;

	$q = new DBQuery();
	$q->addTable('files');
	$q->addQuery('count(files.file_id)','file_in_folder');
	$q->addJoin('projects','p','p.project_id = file_project');
	$q->addJoin('users','u','u.user_id = file_owner');
	$q->addJoin('tasks','t','t.task_id = file_task');
	$q->addJoin('file_folders','ff','ff.file_folder_id = file_folder');
	$q->addWhere('file_folder = '. $folder);
	if (count( $deny1 ) > 0)
		$q->addWhere('file_project NOT IN (' . implode( ',', $deny1 ) . ')');
	if (count( $deny2 ) > 0)
		$q->addWhere('file_task NOT IN (' . implode( ',', $deny2 ) . ')');
	if ($project_id)
		$q->addWhere('file_project = '. $project_id);
	if ($task_id)
		$q->addWhere('file_task = '. $task_id);
	if ($company_id) {
		$q->innerJoin('companies','co','co.company_id = p.project_company');
		$q->addWhere('company_id = '. $company_id);
		$q->addWhere('company_id IN (' . $allowed_companies  . ')');
	}
				
	$sql = $q->prepare();
	$q->clear();
	
	$files_in_folder = db_loadResult($sql);
	return $files_in_folder;
}

function displayFiles($folder) {
	global $m, $a, $tab, $AppUI, $xpg_min, $xpg_pagesize;
	global $deny1, $deny2, $project_id, $task_id, $showProject, $file_types, $cfObj;
	global $xpg_totalrecs, $xpg_total_pages, $page;
	global $company_id, $allowed_companies, $current_uri, $dPconfig;
	
	$canEdit = !getDenyEdit( $m, $folder );
	$canRead = !getDenyRead( $m, $folder );
	
      $df = $AppUI->getPref('SHDATEFORMAT');
      $tf = $AppUI->getPref('TIMEFORMAT');
      
	// SETUP FOR FILE LIST
	$q = new DBQuery();
	$q->addTable('files');
	$q->addQuery('files.*,count(file_version) as file_versions,round(max(file_version), 2) as file_lastversion,file_folder_id, file_folder_name,project_name, project_color_identifier,contact_first_name, contact_last_name,task_name,task_id');
	$q->addJoin('projects','p','p.project_id = file_project');
	$q->addJoin('users','u','u.user_id = file_owner');
	$q->addJoin('contacts','c','c.contact_id = u.user_contact');
	$q->addJoin('tasks','t','t.task_id = file_task');
	$q->addJoin('file_folders','ff','ff.file_folder_id = file_folder');
	$q->addWhere('file_folder = '. $folder);
	if (count( $deny1 ) > 0)
		$q->addWhere('file_project NOT IN (' . implode( ',', $deny1 ) . ')');
	if (count( $deny2 ) > 0)
		$q->addWhere('file_task NOT IN (' . implode( ',', $deny2 ) . ')');
	if ($project_id)
		$q->addWhere('file_project = '. $project_id);
	if ($task_id)
		$q->addWhere('file_task = '. $task_id);
	if ($company_id) {
		$q->innerJoin('companies','co','co.company_id = p.project_company');
		$q->addWhere('company_id = '. $company_id);
		$q->addWhere('company_id IN (' . $allowed_companies  . ')');
	}
				
	$q->addGroup('file_folder');
	$q->addGroup('project_name');
	$q->addGroup('file_name');

	$q->addOrder('file_folder');
	$q->addOrder('project_name');
	$q->addOrder('file_name');
	
	$q->setLimit($xpg_pagesize, $xpg_min);

	$files_sql = $q->prepare();
	$q->clear();

	$q = new DBQuery();
	$q->addTable('files');
	$q->addQuery('files.file_id, file_version, file_project, file_name, file_task, file_description, user_username as file_owner, file_size, file_category, file_type, file_date, file_folder_name');
	$q->addJoin('projects','p','p.project_id = file_project');
	$q->addJoin('users','u','u.user_id = file_owner');
	$q->addJoin('tasks','t','t.task_id = file_task');
	$q->addJoin('file_folders','ff','ff.file_folder_id = file_folder');
	$q->addWhere('file_folder = '. $folder);
	if ($project_id)
		$q->addWhere('file_project = '. $project_id);
	if ($task_id)
		$q->addWhere('file_task = '. $task_id);
	if ($company_id) {
		$q->innerJoin('companies','co','co.company_id = p.project_company');
		$q->addWhere('company_id = '. $company_id);
		$q->addWhere('company_id IN (' . $allowed_companies  . ')');
	}
				
	$file_versions_sql = $q->prepare();
	$q->clear();

	$files = array();
	$file_versions = array();
	if ($canRead) {
		$files = db_loadList( $files_sql );
		$file_versions = db_loadList($file_versions_sql);
	}
	if ($files === array()) {
		return 0;	
	}
?>
	<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
	<tr>
		<th nowrap="nowrap"><?php echo $AppUI->_( 'File Name' ); ?></th>
		<th><?php echo $AppUI->_( 'Description' ); ?></th>
		<th><?php echo $AppUI->_( 'Versions' ); ?></th>
	    <th><?php echo $AppUI->_( 'Category' ); ?></th>
		<th nowrap="nowrap"><?php echo $AppUI->_( 'Task Name' ); ?></th>
		<th><?php echo $AppUI->_( 'Owner' ); ?></th>
		<th><?php echo $AppUI->_( 'Size' ); ?></th>
		<th><?php echo $AppUI->_( 'Type' ); ?></a></th>
		<th><?php echo $AppUI->_( 'Date' ); ?></th>
    	<th nowrap="nowrap"><?php echo $AppUI->_('co Reason') ?></th>
    	<th><?php echo $AppUI->_('co') ?></th>
		<th nowrap width="1"></th>
		<th nowrap width="1"></th>
	</tr>
<?php
	$fp=-1;
	$file_date = new CDate();
	
	$id = 0;
	foreach ($files as $row) {
		$file_date = new CDate( $row['file_date'] );

		if ($fp != $row["file_project"]) {
			if (!$row["project_name"]) {
				$row["project_name"] = $AppUI->_('All Projects');
				$row["project_color_identifier"] = 'f4efe3';
			}
			if ($showProject) {
				$s = '<tr>';
				$s .= '<td colspan="20" style="background-color:#'.$row["project_color_identifier"].'">';
				$s .= '<font color="' . bestColor( $row["project_color_identifier"] ) . '">';
				if ($row['file_project'] > 0) {
					$href = './index.php?m=projects&a=view&project_id=' . $row['file_project'];
				} else {
					$href = './index.php?m=projects';	
				}
				$s .= '<a href="'. $href .'">' . $row["project_name"] . '</a>';
				$s .= '</font></td></tr>';
				echo $s;
			}
		}
		$fp = $row["file_project"];
	        if ($row['file_versions'] > 1)
	                $file = last_file($file_versions, $row['file_name'], $row['file_project']);
	        else 
	                $file = $row;
	?>
	<form name="frm_remove_file_<?php echo $file['file_id']; ?>" action="?m=files" method="post">
	<input type="hidden" name="dosql" value="do_file_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="file_id" value="<?php echo $file['file_id']; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
	</form>		
	<form name="frm_duplicate_file_<?php echo $file['file_id']; ?>" action="?m=files" method="post">
	<input type="hidden" name="dosql" value="do_file_aed" />
	<input type="hidden" name="duplicate" value="1" />
	<input type="hidden" name="file_id" value="<?php echo $file['file_id']; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
	</form>		
	<tr>
		<td nowrap="8%">
			<?php 
			      $file_icon = getIcon($row['file_type']);
                        echo "<a href=\"./fileviewer.php?file_id={$file['file_id']}\" title=\"{$file['file_description']}\"><img border=\"0\" width=\"16\" heigth=\"16\" src=\"".DP_BASE_URL."/modules/files/images/$file_icon\" />&nbsp;{$row['file_name']}</a>"; ?>
		</td>
		<td width="20%"><?php echo $file['file_description'];?></td>
		<td width="5%" nowrap="nowrap" align="center">
	        <?php
	                $hidden_table = '';
	                echo $row['file_lastversion'];
	                if ($row['file_versions'] > 1)
	                {
	                 echo ' <a href="#" onClick="expand(\'versions_' . $file['file_id'] . '\'); ">(' . $row['file_versions'] . ')</a>';
	                 $hidden_table = '<tr><td colspan="20">
	<table style="display: none" id="versions_' . $file['file_id'] . '" width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
	<tr>
	        <th nowrap="nowrap">' . $AppUI->_( 'File Name' ) . '</th>
	        <th>' . $AppUI->_( 'Description' ) . '</th>
	        <th>' . $AppUI->_( 'Versions' ) . '</th>
	        <th>' . $AppUI->_( 'Category' ) . '</th>
	        <th nowrap="nowrap">' . $AppUI->_( 'Task Name' ) . '</th>
	        <th>' . $AppUI->_( 'Owner' ) . '</th>
	        <th>' . $AppUI->_( 'Size' ) . '</th>
	        <th>' . $AppUI->_( 'Type' ) . '</a></th>
	        <th>' . $AppUI->_( 'Date' ) . '</th>
    		<th nowrap="nowrap">' . $AppUI->_('co Reason') . '</th>
    		<th>' . $AppUI->_('co') . '</th>
	        <th nowrap width="1"></th>
	        <th nowrap width="1"></th>
	</tr>
	';
	                foreach($file_versions as $file_row)
	                        if ($file_row['file_name'] == $row['file_name'] && $file_row['file_project'] == $row['file_project'])
	                        {
                              $file_icon = getIcon($file_row['file_type']);
		                  $file_date = new CDate( $file_row['file_date'] );
					$hidden_table .= '	
			<form name="frm_delete_sub_file_'.$file_row['file_id'].'" action="?m=files" method="post">
			<input type="hidden" name="dosql" value="do_file_aed" />
			<input type="hidden" name="del" value="1" />
			<input type="hidden" name="file_id" value="'.$file_row['file_id'].'" />
			<input type="hidden" name="redirect" value="'.$current_uri.'" />
			</form>';		
					$hidden_table .= '	
			<form name="frm_duplicate_sub_file_'.$file_row['file_id'].'" action="?m=files" method="post">
			<input type="hidden" name="dosql" value="do_file_aed" />
			<input type="hidden" name="duplicate" value="1" />
			<input type="hidden" name="file_id" value="'.$file_row['file_id'].'" />
			<input type="hidden" name="redirect" value="'.$current_uri.'" />
			</form>';		
	                $hidden_table .= '
	        <tr>
	                <td nowrap="8%"><a href="./fileviewer.php?file_id=' . $file_row['file_id'] . '" 
	                        title="' . $file_row['file_description'] . '">' . 
                              "<img border=\"0\" width=\"16\" heigth=\"16\" src=\"".DP_BASE_URL."/modules/files/images/$file_icon\" />&nbsp;" . 
	                        $file_row['file_name'] . '
	                </a></td>
	                <td width="20%">' . $file_row['file_description'] . '</td>
	                <td width="5%" nowrap="nowrap" align="center">' . $file_row['file_version'] . '</td>
	                <td width="10%" nowrap="nowrap" align="center"><a href="./index.php?m='.$m.'&a='.$a.'&tab='.($file_row['file_category']+1).'">' . $file_types[$file_row['file_category']+1] . '</a></td>
	                <td width="5%" align="center"><a href="./index.php?m=tasks&a=view&task_id='.$file_row["file_task"].'">'.$row["task_name"].'</a></td>
	                <td width="15%" nowrap="nowrap">' . $row["contact_first_name"].' '.$row["contact_last_name"] . '</td>
	                <td width="5%" nowrap="nowrap" align="right">' . intval($file_row['file_size']/1024) . 'kb </td>
	                <td width="15%" nowrap="nowrap">' . $file_row['file_type'] . '</td>
	                <td width="15%" nowrap="nowrap" align="right">' . $file_date->format( "$df $tf" ) . '</td>
        			<td width="10%">' . $row['file_co_reason'] . '</td>
        			<td nowrap="nowrap" align="center">';
	                if ($canEdit && empty($file_row['file_checkout']) ) {
			                $hidden_table .='<a href="?m=files&a=co&file_id='.$file_row['file_id'].'">'.dPshowImage( './modules/files/images/up.png', '16', '16','checkout','checkout file' ).'</a>';
	                } else if ($row['file_checkout'] == $AppUI->user_id) {
			                $hidden_table .='<a href="?m=files&a=addedit&ci=1&file_id='.$file_row['file_id'].'">'.dPshowImage( './modules/files/images/down.png', '16','16','checkin','checkin file').'</a>';
	                } else { 
			                if ($file_row['file_checkout'] == 'final'){
			                        $hidden_table .= 'final';
			                } else {
									$q4 = new DBQuery;
									$q4->addQuery("file_id, file_checkout, user_username as co_user, contact_first_name, contact_last_name");
									$q4->addTable('files');
									$q4->leftJoin('users', 'cu', 'cu.user_id = file_checkout');
									$q4->leftJoin('contacts', 'co', 'co.contact_id = cu.user_contact');
									$q4->addWhere('file_id = '.$file_row['file_id']);
									$co_user = array();
									$co_user = $q4->loadList();
									$co_user = $co_user[0];
									$q4->clear();
			                        $hidden_table .= $co_user['contact_first_name'].' '.$co_user['contact_last_name'].'<br>('.$co_user['co_user'].')'; 
							}
			        }
			        $hidden_table .= '</td>';        
	                $hidden_table .= '<td nowrap="nowrap" align="right" width="48">';
							 if ($canEdit && ( empty($file_row['file_checkout']) || ( $file_row['file_checkout'] == 'final' && ($canEdit || $row['project_owner'] == $AppUI->user_id) ))) 
	                                {
	                                        $hidden_table .= '<a href="./index.php?m=files&a=addedit&file_id=' . $file_row["file_id"] . '">' . dPshowImage( './modules/files/images/kedit.png', '16', '16', 'edit file', 'edit file' ) . "</a>".
											'<a href="#" onclick="document.frm_duplicate_sub_file_'.$file_row['file_id'].'.submit()">' . dPshowImage( './modules/files/images/duplicate.png', '16', '16', 'duplicate file', 'duplicate file' ) . "</a>".
											'<a href="#" onclick="if (confirm(\'Are you sure you want to delete this file?\')) {document.frm_delete_sub_file_'.$file_row['file_id'].'.submit()}">' . dPshowImage( './modules/files/images/remove.png', '16', '16', 'delete file', 'delete file' ) . "</a>";
	                                }
	                                $hidden_table .= '</td>';
	                $hidden_table .= '<td nowrap="nowrap" align="right" width="1">';
					if ($canEdit && ( empty($row['file_checkout']) || ( $row['file_checkout'] == 'final' && ($canEdit || $row['project_owner'] == $AppUI->user_id) ))) {
            			$bulk_op = 'onchange="(this.checked) ? addBulkComponent('.$file_row['file_id'].') : removeBulkComponent('.$file_row['file_id'].')"';
						$hidden_table .= '<input type="checkbox" '.$bulk_op.' name="chk_sub_sel_file_'.$file_row['file_id'].'" />';		
					}
	                $hidden_table .= '</td>';
	                $hidden_table .= '</tr>';
	                        }
	                $hidden_table .= '</table>';
	                //$hidden_table .= '</span>';
	                }
	        ?>
	        </td>
	        <td width="10%" nowrap="nowrap" align="center"><a href="./index.php?m=<?php echo $m; ?>&a=<?php echo $a; ?>&view=categories&tab=<?php echo ($file['file_category']); ?>"><?php echo $file_types[$file["file_category"]]; ?></a></td> 
		<td width="5%" align="center"><a href="./index.php?m=tasks&a=view&task_id=<?php echo $file["task_id"]; ?>"><?php echo $file["task_name"];?></a></td>
		<td width="15%" nowrap="nowrap"><?php echo $file["contact_first_name"].' '.$file["contact_last_name"]; ?></td>
		<td width="5%" nowrap="nowrap" align="right"><?php echo intval($file["file_size"] / 1024); ?> kb</td>
		<td width="15%" nowrap="nowrap"><?php echo $file["file_type"]; ?></td>
		<td width="15%" nowrap="nowrap" align="right"><?php echo $file_date->format( "$df $tf" ); ?></td>
        <td width="10%"><?php echo $file['file_co_reason']; ?></td>
        <td nowrap="nowrap" align="center">
        <?php if ($canEdit && empty($row['file_checkout']) ) {
        ?>
                <a href="?m=files&a=co&file_id=<?php echo $file['file_id']; ?>"><?php echo dPshowImage( './modules/files/images/up.png', '16', '16','checkout','checkout file' ); ?></a>
        <?php }
        else if ($row['file_checkout'] == $AppUI->user_id) { ?>
                <a href="?m=files&a=addedit&ci=1&file_id=<?php echo $file['file_id']; ?>"><?php echo dPshowImage( './modules/files/images/down.png', '16','16','checkin','checkin file'); ?></a>
        <?php }
        else { 
                if ($file['file_checkout'] == 'final'){
                        echo 'final';
                } else {
						$q4 = new DBQuery;
						$q4->addQuery("file_id, file_checkout, user_username as co_user, contact_first_name, contact_last_name");
						$q4->addTable('files');
						$q4->leftJoin('users', 'cu', 'cu.user_id = file_checkout');
						$q4->leftJoin('contacts', 'co', 'co.contact_id = cu.user_contact');
						$q4->addWhere('file_id = '.$file['file_id']);
						$co_user = array();
						$co_user = $q4->loadList();
						$co_user = $co_user[0];
						$q4->clear();
                        echo $co_user['contact_first_name'].' '.$co_user['contact_last_name'].'<br>('.$co_user['co_user'].')'; 
				}
        }
        ?>                
        </td>
		<td nowrap="nowrap" align="center" width="48">
		<?php if ($canEdit && ( empty($file['file_checkout']) || ( $file['file_checkout'] == 'final' && ($canEdit || $file['project_owner'] == $AppUI->user_id) ))) {
			echo '<a href="./index.php?m=files&a=addedit&file_id=' . $file["file_id"] . '">';
			echo dPshowImage( './modules/files/images/kedit.png', '16', '16', 'edit file', 'edit file' );
			echo "</a>";
			echo '<a href="#" onclick="document.frm_duplicate_file_'.$file['file_id'].'.submit()">' . dPshowImage( './modules/files/images/duplicate.png', '16', '16', 'duplicate file', 'duplicate file' ) . '</a>';
			echo '<a href="#" onclick="if (confirm(\'Are you sure you want to delete this file?\')) {document.frm_remove_file_'.$file['file_id'].'.submit()}">' . dPshowImage( './modules/files/images/remove.png', '16', '16', 'delete file', 'delete file' ) . '</a>';		
		}
		?>
		<td nowrap="nowrap" align="center" width="1">
		<?php if ($canEdit && ( empty($file['file_checkout']) || ( $file['file_checkout'] == 'final' && ($canEdit || $file['project_owner'] == $AppUI->user_id) ))) {
            $bulk_op = 'onchange="(this.checked) ? addBulkComponent('.$file['file_id'].') : removeBulkComponent('.$file['file_id'].')"';
			echo '<input type="checkbox" '.$bulk_op.' name="chk_sel_file_'.$file['file_id'].'" />';		
		}
		?>		
		</td>
	</tr>
	<?php echo $hidden_table; ?>
	<?php 
	        $hidden_table = ''; 
	}?>
	</table>
	<?php
	if ($xpg_totalrecs > $xpg_pagesize) {
		showfnavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page, $folder);
	}
	echo "<br />";
}

/**** Main Program ****/
$canEdit = !getDenyEdit( $m, $folder );
$canRead = !getDenyRead( $m, $folder );
//echo $folder . ":" . $canEdit . ":" . $canRead;
//if (!$canEdit && !$canRead) {
//	$AppUI->redirect( "m=public&a=access_denied" );
//}

if ($folder > 0) {
	$cfObj->load($folder);
	$msg = '';
	$canDelete = $cfObj->canDelete( $msg, $folder );
}

if ($folder){ ?>
<table border="0" cellpadding="4" cellspacing="0" width="100%">
<tr>
  <td nowrap="nowrap">
	<a href="./index.php?m=<?php echo $m; ?>&amp;&a=<?php echo $a; ?>&amp;tab=<?php echo $tab; ?>&folder=0"><?php echo dPshowImage( './modules/files/images/home.png', '22', '22', 'folder icon', 'back to root folder' ); ?></a>
	<?php if (array_key_exists($cfObj->file_folder_parent, $allowed_folders_ary) ): ?>
	<a href="./index.php?m=<?php echo $m; ?>&amp;&a=<?php echo $a; ?>&amp;tab=<?php echo $tab; ?>&folder=<?php echo $cfObj->file_folder_parent; ?>"><?php echo dPshowImage( './modules/files/images/back.png', '22', '22', 'folder icon', 'back to parent folder' ); ?></a>
	<?php endif;
		  //if ($allowed_folders_ary[$folder] == -1): ?>
	<a href="./index.php?m=<?php echo $m; ?>&amp;tab=<?php echo $tab; ?>&a=addedit_folder&folder=<?php echo $cfObj->file_folder_id; ?>" title="edit the <?php echo $cfObj->file_folder_name; ?> folder"><?php echo dPshowImage( './modules/files/images/filesaveas.png', '22', '22', 'folder icon', 'edit folder' ); ?></a>
	<?php //endif; ?>
  </td>
</tr>
</table>
<?php
}

?>
   
<div id="folder-list" style="background-color:white;layer-background-color:white;">
<?php
//	if (!$limited OR $folder != 0):
?>
		<span class="folder-name-current">
			<?php
			echo dPshowImage( './modules/files/images/folder5_small.png', '16', '16');
			echo ($cfObj->file_folder_name) ? $cfObj->file_folder_name : "Root";
			?>
		</span>
<?php
//	endif;
	if ($cfObj->file_folder_description != ''): ?>
		<p><?php echo $cfObj->file_folder_description; ?></p>
<?php
	endif;
		if( countFiles( $folder ) > 0 ) {
			displayfiles( $folder );
		} elseif (!$limited OR $folder != 0) {
			echo "no files";
		}
		getFolders($folder);
?>
</div>

<hr />

<table border="0" cellpadding="4" cellspacing="0" width="100%">
<?php
//Lets add our bulk form
	$folders_avail = getFolderSelectList();
	//used O (uppercase 0)instead of 0 (zero) to keep things in place
	$folders = array('-1' => Array ( 0 => 'O', 1 => '(Move to Folder)', 2 => -1 )) + array('0' => Array ( 0 => 0, 1 => 'Root', 2 => -1 )) + $folders_avail;

	$project = new CProject();
    $sprojects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
    $q  = new DBQuery;
    $q->addTable('projects');
    $q->addQuery('project_id, company_name');
    $q->addJoin("companies",'co','co.company_id = project_company');
    $idx_companies = $q->loadHashList();
    $q->clear();
    foreach ($sprojects as $prj_id => $prj_name) {
          $sprojects[$prj_id] = $idx_companies[$prj_id].': '.$prj_name;
    }
    asort($sprojects);
    $sprojects = array( 'O'=>'('.$AppUI->_('Move to Project', UI_OUTPUT_RAW).')') + array( '0'=>'('.$AppUI->_('All Projects', UI_OUTPUT_RAW).')') + $sprojects ;
?>
                <tr>
                        <td colspan="50" align="right">
                              <form name='frm_bulk' method='POST' action='?m=files&a=do_files_bulk_aed'>
							  <input type="hidden" name="redirect" value="<?php echo $current_uri; ?>" />
                              <table id="tbl_bulk" name="tbl_bulk">
                              <tr>
                                    <td><?php echo arraySelect( $sprojects, 'bulk_file_project', 'style="width:180px" class="text"', 'O' ); ?></td>
                                    <td><?php echo arraySelectTree( $folders, 'bulk_file_folder', 'style="width:180px;" class="text"', 'O' ); ?></td>
                                    <td align="right"><input type="button" class="button" value="<?php echo $AppUI->_('Go');?>" onclick="if (confirm('Are you sure you wish to apply the options on the selected files?')) document.frm_bulk.submit();" /></td>
                              </tr>                                
                              </table>
                              </form>
                        </td>
                </tr>
</table>

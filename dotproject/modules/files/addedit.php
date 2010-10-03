<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$folder = intval(dPgetParam($_GET, 'folder', 0));
$file_id = intval(dPgetParam($_GET, 'file_id', 0));
$ci = dPgetParam($_GET, 'ci', 0) == 1 ? true : false;
$preserve = $dPconfig['files_ci_preserve_attr'];

// check permissions for this record
$canEdit = getPermission($m, 'edit', $file_id);
if (!($canEdit)) {
	$AppUI->redirect('m=public&amp;a=access_denied');
}
if (file_exists(DP_BASE_DIR . '/modules/helpdesk/config.php')) {
	include (DP_BASE_DIR . '/modules/helpdesk/config.php');
}
$canAdmin = getPermission('system', 'edit');

// add to allow for returning to other modules besides Files
$referrerArray = parse_url($_SERVER['HTTP_REFERER']);
$referrer = $referrerArray['query'] . $referrerArray['fragment'];

// load the companies class to retrieved denied companies
require_once($AppUI->getModuleClass('companies'));
require_once($AppUI->getModuleClass('projects'));
require_once $AppUI->getModuleClass('tasks');

$file_task = intval(dPgetParam($_GET, 'file_task', 0));
$file_parent = intval(dPgetParam($_GET, 'file_parent', 0));
$file_project = intval(dPgetParam($_GET, 'project_id', 0));
$file_helpdesk_item = intval(dPgetParam($_GET, 'file_helpdesk_item', 0));

$q = new DBQuery;

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CFile();
$canDelete = $obj->canDelete($msg, $file_id);

// load the record data
// $obj = null;
if ($file_id > 0) {
	if (!($obj->load($file_id))) {
		$AppUI->setMsg('File');
		$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
		$AppUI->redirect();
	}
	// Check to see if the task or the project is allowed.
	if (($obj->file_task && !(getPermission('tasks', 'view', $obj->file_task))) 
	    || ($obj->file_project 
	        && !(getPermission('projects', 'view', $obj->file_project)))) {
		$AppUI->redirect('m=public&a=access_denied');
	}
}

if ($obj->file_checkout != $AppUI->user_id) {
	$ci = false;
}

if (!($canAdmin)) {
	$canAdmin = $obj->canAdmin();
}
if ($obj->file_checkout == 'final' && !($canAdmin)) {
	$AppUI->redirect('m=public&amp;a=access_denied');
}

// setup the title block
$ttl = (($file_id) ? 'Edit File' : 'Add File');
$ttl = (($ci) ? 'Checking in' : $ttl);
$titleBlock = new CTitleBlock($ttl, 'folder5.png', $m, "$m.$a");
$titleBlock->addCrumb('?m=files', 'files list');
if ($canDelete && $file_id > 0 && !($ci)) {
	$titleBlock->addCrumbDelete('delete file', $canDelete, $msg);
}
$titleBlock->show();

//Clear the file id if checking out so a new version is created.
if ($ci) {
	$file_id = 0;
}

if ($obj->file_project) {
	$file_project = $obj->file_project;
}
if ($obj->file_task) {
	$file_task = $obj->file_task;
	$task_name = $obj->getTaskName();
} else if ($file_task) {
	$q  = new DBQuery;
	$q->addTable('tasks');
	$q->addQuery('task_name');
	$q->addWhere("task_id=$file_task");
	$sql = $q->prepare();
	$q->clear();
	$task_name = db_loadResult($sql);
} else {
	$task_name = '';
}
if ($obj->file_helpdesk_item) {
	$file_helpdesk_item = $obj->file_helpdesk_item;	
}

$extra = array(
	'where'=>'project_status <> 7'
);
$project = new CProject();
$projects = $project->getAllowedRecords($AppUI->user_id, 'project_id,project_name', 'project_name', 
                                        null, $extra);
$projects = arrayMerge(array('0'=>$AppUI->_('None', UI_OUTPUT_RAW)), $projects);

$folders = getFolderSelectList();
?>
<script language="javascript" type="text/javascript">
function submitIt() {
	var f = document.uploadFrm;
	f.submit();
}
function delIt() {
	if (confirm("<?php echo $AppUI->_('filesDelete', UI_OUTPUT_JS); ?>")) {
		var f = document.uploadFrm;
		f.del.value='1';
		f.submit();
	}
}
function popTask() {
    var f = document.uploadFrm;
    if (f.file_project.selectedIndex == 0) {
        alert("<?php echo $AppUI->_('Please select a project first!', UI_OUTPUT_JS); ?>");
    } else {
        window.open('?m=public&amp;a=selector&amp;dialog=1&amp;callback=setTask&amp;table=tasks&amp;task_project='
		            + f.file_project.options[f.file_project.selectedIndex].value, 'task','left=50,top=50,height=250,width=400,resizable')
    }
}

function finalCI() {
	var f = document.uploadFrm;
	if (f.final_ci.checked == true) {
		f.file_checkout.value = 'final';
		f.file_co_reason.value = 'Final Version';
	} else {
		f.file_checkout.value = '';
		f.file_co_reason.value = '';
	}
}

// Callback function for the generic selector
function setTask(key, val) {
    var f = document.uploadFrm;
    if (val != '') {
        f.file_task.value = key;
        f.task_name.value = val;
    } else {
        f.file_task.value = '0';
        f.task_name.value = '';
    }
}
</script>

<form name="uploadFrm" action="?m=files" enctype="multipart/form-data" method="post">
	<input type="hidden" name="dosql" value="do_file_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="file_id" value="<?php echo $file_id; ?>" />
	<input type="hidden" name="file_version_id" value="<?php echo $obj->file_version_id; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $referrer; ?>" />
	<input type="hidden" name="file_helpdesk_item" value="<?php echo $file_helpdesk_item; ?>" />

<table width="100%" border="0" cellpadding="3" cellspacing="3" class="std">
<tr>
	<td width="100%" valign="top" align="center">
		<table cellspacing="1" cellpadding="2" width="60%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Folder'); ?>:</td>
			<td align="left"><?php 
echo arraySelectTree($folders, 'file_folder', 'style="width:175px;" class="text"', 
                     (($file_helpdesk_item) ? getHelpdeskFolder() 
                      : (($file_id == 0 && !$ci) ? $folder : $obj->file_folder))); ?></td>
		</tr><?php 
if ($file_id) { ?>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('File Name'); ?>:</td>
			<td align="left" class="hilite"><?php 
	echo mb_strlen($obj->file_name)== 0 ? "n/a" : $obj->file_name; ?></td>
			<td>
				<a href="./fileviewer.php?file_id=<?php echo $obj->file_id; ?>"><?php 
	echo dPshowImage(DP_BASE_URL . '/modules/files/images/filesaveas.png', '16', '16', 
	                 'download icon', 'download'); ?></a>
			</td>
		</tr>
		<tr valign="top">
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Type'); ?>:</td>
			<td align="left" class="hilite"><?php echo $obj->file_type; ?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Size'); ?>:</td>
			<td align="left" class="hilite"><?php echo $obj->file_size; ?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Uploaded By'); ?>:</td>
			<td align="left" class="hilite"><?php echo $obj->getOwner(); ?></td>
		</tr><?php 
} 
echo file_show_attr(); ?>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Description'); ?>:</td>
			<td align="left">
				<textarea name="file_description" class="textarea" style="width:270px"><?php 
echo $obj->file_description; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
				// TODO - Add custom fields to files, doesnt make sense without a detail view.
				/*
				require_once($AppUI->getSystemClass('CustomFields'));
				$custom_fields = New CustomFields($m, $a, $obj->file_id, "edit");
				$custom_fields->printHTML();
				*/
			?>
			</td>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Upload File'); ?>:</td>
			<td align="left"><input type="File" class="button" name="formfile" style="width:270px" /></td>
		</tr>
		<?php 
if (!($file_id) || $ci || ($canAdmin && $obj->file_checkout == 'final')) { ?>
		<tr>
			<td align="right" nowrap="nowrap">&nbsp;</td>
			<td align="left">
				<input type="checkbox" name="final_ci" id="final_ci" onclick="javascript:finalCI()" />
				<label for="final_ci"><?php echo $AppUI->_('Final Version'); ?></label>
			</td>		
		</tr><?php 
} ?>
		<tr>
			<td align="right" nowrap="nowrap">&nbsp;</td>
			<td align="left">
				<input type="checkbox" name="notify" id="notify" checked="checked" />
				<label for="notify"><?php 
	echo $AppUI->_('Notify Assignees of Task or Project Owner by Email'); ?></label>
			</td>		
		</tr>
		<tr>
			<td align="right" nowrap="nowrap">&nbsp;</td>
			<td align="left">
				<input type="checkbox" name="notify_contacts" id="notify_contacts" checked="checked" />
				<label for="notify_contacts"><?php 
echo $AppUI->_('Notify Project and Task Contacts'); ?></label>
			</td>		
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<input class="button" type="button" name="cancel" value="<?php 
echo $AppUI->_('cancel'); ?>" onclick="javascript:if (confirm('<?php 
echo $AppUI->_('Are you sure you want to cancel?', UI_OUTPUT_JS); ?>')) {location.href = '?<?php 
echo $AppUI->getPlace(); ?>'; }" />
	</td>
	<td align="right">
		<input type="button" class="button" value="<?php 
echo $AppUI->_('submit'); ?>" onclick="javascript:submitIt()" />
	</td>
</tr>
</table>
</form>

<?php 
function file_show_attr() {
	global $AppUI, $obj, $ci, $canAdmin, $projects,
	$file_project, $file_task, $task_name, $preserve, $file_helpdesk_item;
	
	
	if ($ci) {
		$str_out = ('<tr><td align="right" nowrap="nowrap">' . $AppUI->_('Minor Revision') 
					. '</td><td>' 
		            . '<input type="Radio" name="revision_type" value="minor" checked="checked" />' 
		            . '</td></tr>' 
		            . '<tr><td align="right" nowrap="nowrap">' . $AppUI->_('Major Revision') 
		            . '</td><td>' . '<input type="Radio" name="revision_type" value="major" />' 
		            . '</td></tr>');
	} else {
		$str_out = '<tr><td align="right" nowrap="nowrap">' . $AppUI->_('Version') . ':</td>';
	}
	
	$str_out .= '<td align="left">';
      
	if (!($file_id) || $ci || ($canAdmin && $obj->file_checkout == 'final')) {
		$str_out .= ('<input type="hidden" name="file_checkout" value="" />' 
		             . '<input type="hidden" name="file_co_reason" value="" />');
	}
	
	if ($ci) {
		$the_value = (mb_strlen($obj->file_version) > 0 ? $obj->file_version+0.01 : "1");
		$str_out .= '<input type="hidden" name="file_version" value="' . $the_value . '" />';
	} else {
		$the_value = (mb_strlen($obj->file_version) > 0 ? $obj->file_version : "1");
		$str_out .= ('<input type="text" name="file_version" maxlength="10" size="5" ' 
		             . 'value="' . $the_value . '" />');
	}
    
	$str_out .= '</td>';
    
    
	$select_disabled=' ';  
	$onclick_task=' onclick="javascript:popTask()" ';
	if ($ci && $preserve) {
		$select_disabled=' disabled ';  
        $onclick_task=' ';
        // need because when a html is disabled, it's value it's not sent in submit
        $str_out .= '<input type="hidden" name="file_project" value="' .  $file_project . '" />';
        $str_out .= ('<input type="hidden" name="file_category" value="' .  $obj->file_category 
		             . '" />');
	}
	
    
	// Category
	$str_out .= ('<tr>' . '<td align="right" nowrap="nowrap">' . $AppUI->_('Category') . ':</td>');
	$str_out .= ('<td align="left">' 
	             . arraySelect(dPgetSysVal('FileType'), 'file_category', (string) $select_disabled, 
	                           $obj->file_category, true) . '<td>');
                              
	//TODO: export helpdesk code if possible...
	if ($file_helpdesk_item) {
		$hd_item = new CHelpDeskItem();
		$hd_item->load($file_helpdesk_item);
		//Helpdesk Item
		$str_out .= ('<tr><td align="right" nowrap="nowrap">' . $AppUI->_('Helpdesk Item') 
		             . ':</td>');
		$str_out .= ('<td align="left"><b>' 
		             . $hd_item->item_id . ' - ' . $hd_item->item_title . '</b></td></tr>');
		// Project
		$str_out .= '<input type="hidden" name="file_project" value="' .  $file_project . '" />';
        
		// Task 
		$str_out .= '<input type="hidden" name="file_task" value="0" />';
	} else {
		// Project
		$str_out .= '<tr><td align="right" nowrap="nowrap">' . $AppUI->_('Project') . ':</td>';
		$str_out .= ('<td align="left">' 
		             . projectSelectWithOptGroup($AppUI->user_id, 'file_project', 
		                                         ('size="1" class="text" style="width:270px"' 
		                                          . $select_disabled), $file_project) 
		             . '</td></tr>');
            
		
		// Task 
		$str_out .= ('<tr><td align="right" nowrap="nowrap">' . $AppUI->_('Task') . ':</td>' 
		             . '<td align="left" colspan="2" valign="top">' 
		             . '<input type="hidden" name="file_task" value="' . $file_task . '" />' 
		             . '<input type="text" class="text" name="task_name" value="' . $task_name 
		             . '" size="40" disabled />' 
		             . '<input type="button" class="button" value="' . $AppUI->_('select task') 
		             . '..."' . $onclick_task . '/>' . '</td></tr>');
	}
	
    
	return ($str_out);
}


//TODO: export helpdesk code if possible...
function getHelpdeskFolder() {
	$q = new DBQuery();
	$q->addTable('file_folders', 'ff');
	$q->addQuery('file_folder_id');
	$q->addWhere('ff.file_folder_name = "Helpdesk"');
	$ffid = $q->loadResult();
	$q->clear();
	return intval($ffid);
}

?>

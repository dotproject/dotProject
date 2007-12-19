<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$file_folder_parent = intval(dPgetParam($_GET, 'file_folder_parent', 0));
$folder = intval(dPgetParam($_GET, 'folder', 0));

// add to allow for returning to other modules besides Files
$referrerArray = parse_url($_SERVER['HTTP_REFERER']);
$referrer = $referrerArray['query'] . $referrerArray['fragment'];

$obj = new CFileFolder();
// load the record data
if ($folder && !$obj->load($folder)) {
	$AppUI->setMsg('File Folder');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
}

// check permissions for this record
if ($folder) {
	$canRead_folder = getPermission('file_folders', 'view', $folder);
	$canEdit_folder = getPermission('file_folders', 'edit', $folder);
} else {
	$canAuthor_folder = getPermission('file_folders', 'add', $folder);
}
if (($folder && !($canEdit_folder && $canRead_folder)) || (!($folder) && !($canAuthor_folder))) {
	$AppUI->redirect("m=public&a=access_denied");
}

$msg = '';
// check if this record has dependancies to prevent deletion
if ($folder > 0) {
	$canDelete_folder = $obj->canDelete($msg, $folder);
}

$folders = getFolderSelectList();
// setup the title block
$ttl = $folder ? "Edit File Folder" : "Add File Folder";
$titleBlock = new CTitleBlock($ttl, 'folder5.png', $m, $m . '.' . $a);
$titleBlock->addCrumb("?m=files", "files list");
if ($canDelete_folder) {
	$titleBlock->addCrumbDelete('delete file folder', $canDelete_folder, $msg);
}
$titleBlock->show();

?>
<script language="javascript">
function submitIt() {
	var f = document.folderFrm;
	var msg = '';
	if (f.file_folder_name.value.length < 1) {
		msg += "\n<?php echo $AppUI->_('Folder Name'); ?>";
		f.file_folder_name.focus();
	}
	if(msg.length > 0) {
		alert('<?php echo $AppUI->_('Please type'); ?>:' + msg);
	} else {
		f.submit();
	}
}
function delIt() {
	if (confirm("<?php echo $AppUI->_('Delete Folder');?>")) {
		var f = document.folderFrm;
		f.del.value='1';
		f.submit();
	}
}
</script>
<table width="100%" border="0" cellpadding="3" cellspacing="3" class="std">

<form name="folderFrm" action="?m=files" enctype="multipart/form-data" method="post">
	<input type="hidden" name="dosql" value="do_folder_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="file_folder_id" value="<?php echo $folder; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $referrer; ?>" />

<tr>
	<td width="100%" valign="top" align="center">
		<table cellspacing="1" cellpadding="2" width="60%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Subfolder of');?>:</td>
			<td align="left">
			<?php
			$parent_folder = ($folder > 0) ? $obj->file_folder_parent : $file_folder_parent;
			echo arraySelectTree($folders, 'file_folder_parent', 'style="width:175px;" class="text"', $parent_folder); ?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Folder Name');?>:</td>
			<td align="left"><input type="text" class="text" id="ffn" name="file_folder_name"
                              value="<?php echo $obj->file_folder_name; ?>" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right" valign="top" nowrap="nowrap"><?php echo $AppUI->_('Description');?>:</td>
			<td align="left">
				<textarea name="file_folder_description" class="textarea" rows="4" style="width:270px"><?php echo $obj->file_folder_description; ?></textarea>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel');?>" onClick="javascript:if(confirm('<?php echo $AppUI->_('Are you sure you want to cancel?'); ?>')){location.href = '?<?php echo $referrer; ?>';}" />
	</td>
	<td align="right">
		<input type="button" class="button" value="<?php echo $AppUI->_('submit');?>" onclick="submitIt()" />
	</td>
</tr>
</form>
</table>

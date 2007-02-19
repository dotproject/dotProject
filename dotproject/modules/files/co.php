<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

$file_id = intval( dPgetParam( $_GET, 'file_id', 0 ) );
// check permissions for this record
$perms =& $AppUI->acl();

$canEdit = $perms->checkModuleItem( $m, "edit", $file_id );
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}
$canAdmin = $perms->checkModule('system', 'edit');

// load the companies class to retrieved denied companies
require_once( $AppUI->getModuleClass( 'projects' ) );

$file_parent = intval( dPgetParam( $_GET, 'file_parent', 0 ) );

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CFile();

// load the record data
if ( $file_id > 0 && ! $obj->load($file_id) ) {
	$AppUI->setMsg( 'File' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

// setup the title block
$titleBlock = new CTitleBlock( 'Checkout', 'folder5.png', $m, "$m.$a" );
$titleBlock->addCrumb( '?m=files', 'files list' );
$titleBlock->show();

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
	$task_name = db_loadResult( $sql );
} else {
	$task_name = '';
}

$extra = array(
	'where'=>'project_status<>7'
);
$project = new CProject();
$projects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name', null, $extra );
$projects = arrayMerge( array( '0'=>$AppUI->_('All') ), $projects );
?>

<script language='javascript'>
function popFile( params ) {
    fileloader = window.open("fileviewer.php?"+params,"mywindow","location=1,status=1,scrollbars=0,width=80,height=80");
    fileloader.moveTo(0,0);
}
</script>

<table width="100%" border="0" cellpadding="3" cellspacing="3" class="std">

<form name="coFrm" action="?m=files" method="post">
	<input type="hidden" name="dosql" value="do_file_co" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="file_id" value="<?php echo $file_id;?>" />
    <input type="hidden" name="file_checkout" value="<?php echo $AppUI->user_id; ?>">
    <input type="hidden" name="file_version_id" value="<?php echo $obj->file_version_id; ?>">
        

<tr>
	<td width="100%" valign="top" align="center">
		<table cellspacing="1" cellpadding="2" width="60%">
	<?php if ($file_id) { ?>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'File Name' );?>:</td>
			<td align="left" class="hilite"><?php echo strlen($obj->file_name)== 0 ? "n/a" : $obj->file_name;?></td>
		</tr>
		<tr valign="top">
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Type' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->file_type;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Size' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->file_size;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Uploaded By' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->getOwner();?></td>
		</tr>
	<?php } ?>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'CO Reason' );?>:</td>
			<td align="left">
				<textarea name="file_co_reason" class="textarea" rows="4" style="width:270px"><?php echo $obj->file_co_reason;?></textarea>
			</td>
		</tr>

		<tr>
			<td align="right" nowrap="nowrap">&nbsp;</td>
			<td align="left"><input type="checkbox" name="notify" checked="checked"><?php echo $AppUI->_('Notify Assignees of Task or Project Owner by Email'); ?></td>		
		</tr>

		<tr>
			<td align="right" nowrap="nowrap">&nbsp;</td>
			<td align="left"><input type="checkbox" name="notify_contacts" checked="checked"><?php echo $AppUI->_('Notify Project and Task Contacts'); ?></td>		
		</tr>
		
		</table>
	</td>
</tr>
<tr>
	<td>
		<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel');?>" onClick="javascript:if(confirm('<?php echo $AppUI->_('Are you sure you want to cancel?', UI_OUTPUT_JS); ?>')){location.href = './index.php?m=files';}" />
	</td>
	<td align="right">
		<input type="submit" class="button" value="<?php echo $AppUI->_( 'submit' );?>" />
	</td>
</tr>
</form>
</table>

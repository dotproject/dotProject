<?php /* FILES $Id$ */
//addfile sql
$file_id = intval( dPgetParam( $_POST, 'file_id', 0 ) );
$del = intval( dPgetParam( $_POST, 'del', 0 ) );
global $db;

$not = dPgetParam( $_POST, 'notify', '0' );
if ($not!='0') $not='1';

$obj = new CFile();
if ($file_id) { 
	$obj->_message = 'updated';
	$oldObj = new CFile();
	$oldObj->load( $file_id );

} else {
	$obj->_message = 'added';
}
$obj->file_category = intval( dPgetParam( $_POST, 'file_category', 0 ) );

$version = dPgetParam( $_POST, 'file_version', 0 );
$revision_type   = dPgetParam( $_POST, 'revision_type', 0 );

if ( strcasecmp('major', $revision_type) == 0 )
{
  $major_num = strtok($version, ".") + 1;
  $_POST['file_version']= $major_num;
}

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'File' );
// delete the file
if ($del) {
	$obj->load( $file_id );
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		if ($not=='1') $obj->notify();
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( "m=files" );
	}
}

set_time_limit( 600 );
ignore_user_abort( 1 );

//echo "<pre>";print_r($_POST);echo "</pre>";die;

$upload = null;
if (isset( $_FILES['formfile'] )) {
	$upload = $_FILES['formfile'];

	if ($upload['size'] < 1) {
		if (!$file_id) {
			$AppUI->setMsg( 'Upload file size is zero. Process aborted.', UI_MSG_ERROR );
			$AppUI->redirect();
		}
	} else {

	// store file with a unique name
		$obj->file_name = $upload['name'];
		$obj->file_type = $upload['type'];
		$obj->file_size = $upload['size'];
		$obj->file_date = str_replace("'", '', $db->DBTimeStamp(time()));
		$obj->file_real_filename = uniqid( rand() );

		$res = $obj->moveTemp( $upload );
		if (!$res) {
		    $AppUI->setMsg( 'File could not be written', UI_MSG_ERROR );
		    $AppUI->redirect();
		}

	}
}

// move the file on filesystem if the affiliated project was changed
if ($file_id && ($obj->file_project != $oldObj->file_project) ) {
	$res = $obj->moveFile( $oldObj->file_project, $oldObj->file_real_filename );
	if (!$res) {
		$AppUI->setMsg( 'File could not be moved', UI_MSG_ERROR );
		$AppUI->redirect();
	}
}

if (!$file_id) {
	$obj->file_owner = $AppUI->user_id;
	if (! $obj->file_version_id)
	{
		$q  = new DBQuery;
		$q->addTable('files');
		$q->addQuery('file_version_id');
		$q->addOrder('file_version_id DESC');
		$q->setLimit(1);
		$sql = $q->prepare();
		$q->clear();
		$latest_file_version = db_loadResult($sql);
		$obj->file_version_id = $latest_file_version + 1;
	} else {
		$q  = new DBQuery;
		$q->addTable('files');
		$q->addUpdate('file_checkout', '');
		$q->addWhere("file_version_id = $obj->file_version_id");
		$q->exec();
		$q->clear();
	}
}

if (($msg = $obj->store())) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR );
} else {
	$obj->load($obj->file_id);
	if ($not=='1') $obj->notify();
	$AppUI->setMsg( $file_id ? 'updated' : 'added', UI_MSG_OK, true );
	/* Workaround for indexing large files:
	** Based on the value defined in config data,
	** files with file_size greater than specified limit
	** are not indexed for searching.
	** Negative value :<=> no filesize limit
	*/
	$index_max_file_size = dPgetConfig('index_max_file_size', 0);
	if ($index_max_file_size < 0 || $obj->file_size <= $index_max_file_size*1024) {
		$obj->indexStrings();
		$AppUI->setMsg('; ' . $indexed . ' words indexed', UI_MSG_OK, true);
	}
}
$AppUI->redirect();
?>

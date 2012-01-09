<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//addfile sql
$file_id = intval(dPgetParam($_POST, 'file_id', 0));
$del = intval(dPgetParam($_POST, 'del', 0));
$duplicate = intval(dPgetParam($_POST, 'duplicate', 0));
$redirect = dPgetCleanParam($_POST, 'redirect', '');
global $db;

$not = (bool)dPgetParam($_POST, 'notify', '0');
$notcont = (bool)dPgetParam($_POST, 'notify_contacts', '0');

$obj = new CFile();
if ($file_id) { 
	$obj->_message = 'updated';
	$oldObj = new CFile();
	$oldObj->load($file_id);
} else {
	/*
	 ** @date 		20070309
	 ** @author 	gregorerhardt
	 **
	 ** 1. it must be (cf. #1932):
	 ** 	if ($del) instead of if (!$del)
	 ** 2. commented all out, because delete permissions shouldn't be module-centric, 
	 ** but file object-centric. In the CFile::delete() method there is an object-centric check 
	 ** for permission.
			
	if ($del) {
		$acl =& $AppUI->acl();
		if (! $acl->checkModule('files', 'delete')) {
			$AppUI->setMsg($AppUI->_('noDeletePermission'));
			$AppUI->redirect('m=public&a=access_denied');
		}
	}
	*/
	$obj->_message = 'added';
}
$obj->file_category = intval(dPgetParam($_POST, 'file_category', 0));

$version = dPgetCleanParam($_POST, 'file_version', 0);
$revision_type   = dPgetCleanParam($_POST, 'revision_type', 0);

if (strcasecmp('major', $revision_type) == 0) {
	$major_num = strtok($version, '.') + 1;
	$_POST['file_version']= $major_num;
}

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect($redirect);
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('File');
// duplicate a file
if ($duplicate) {
	$obj->load($file_id);
	$new_file = new CFile();
	$new_file = $obj->duplicate();
	if (!($dup_realname = $obj->duplicateFile($obj->file_project, $obj->file_real_filename))) {
		$AppUI->setMsg('Could not duplicate file, check file permissions', UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		$new_file->file_real_filename = $dup_realname;
		$new_file->file_date = str_replace("'", '', $db->DBTimeStamp(time()));
		$new_file->file_version_id = getNextVersionID();
		
		if ($msg = $new_file->store()) {
			$AppUI->setMsg($msg, UI_MSG_ERROR);
		} else {
			$AppUI->setMsg('duplicated', UI_MSG_OK, true);
		}
		$AppUI->redirect($redirect);
	}
}
// delete the file
if ($del) {
	$obj->load($file_id);
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		if ($not) {
			$obj->notify();
		}
		if ($notcont) {
			$obj->notifyContacts();
		}
		$AppUI->setMsg('deleted', UI_MSG_OK, true);
		$AppUI->redirect($redirect);
	}
}



if (!(ini_get('safe_mode'))) {
	set_time_limit(600);
}
ignore_user_abort(1);

//echo '<pre>';print_r($_POST);echo '</pre>';die;

$upload = null;
if (isset($_FILES['formfile'])) {
	$upload = $_FILES['formfile'];

	if ($upload['size'] < 1) {
		if (!$file_id) {
			$AppUI->setMsg('Upload file size is zero. Process aborted.', UI_MSG_ERROR);
			$AppUI->redirect($redirect);
		}
	} else {
		// store file with a unique name
		$obj->file_name = $upload['name'];
		$obj->file_type = $upload['type'];
		$obj->file_size = $upload['size'];
		$obj->file_date = str_replace("'", '', $db->DBTimeStamp(time()));
		$obj->file_real_filename = uniqid(rand());
		
		$res = $obj->moveTemp($upload);
		if (!$res) {
			$AppUI->setMsg('File could not be written', UI_MSG_ERROR);
		    $AppUI->redirect($redirect);
		}
	}
}

// move the file on filesystem if the affiliated project was changed
if ($file_id && ($obj->file_project != $oldObj->file_project)) {
	$res = $obj->moveFile($oldObj->file_project, $oldObj->file_real_filename);
	if (!$res) {
		$AppUI->setMsg('File could not be moved', UI_MSG_ERROR);
		$AppUI->redirect($redirect);
	}
}

if (!$file_id) {
	$obj->file_owner = $AppUI->user_id;
	if (! $obj->file_version_id) {
		$obj->file_version_id = getNextVersionID();
	} else {
		$q  = new DBQuery;
		$q->addTable('files');
		$q->addUpdate('file_checkout', '');
		$q->addWhere('file_version_id = ' . $obj->file_version_id);
		$q->exec();
		$q->clear();
	}
}
//print_r($obj);die;
if (($msg = $obj->store())) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
	// Notification
	$obj->load($obj->file_id);
	if ($not) {
		$obj->notify();
	}
	if ($notcont) {
		$obj->notifyContacts();
	}
	
	// Delete the existing (old) file in case of file replacement 
	// (through addedit not through c/o-versions)
	if (($file_id) && ($upload['size'] > 0)) {
		if (($oldObj->deleteFile())) {
			$AppUI->setMsg('replaced', UI_MSG_OK, true);
		} else {
			$AppUI->setMsg($file_id ? 'updated' : 'added' . '; unable to delete existing file', 
			               UI_MSG_OK, true);
		}
	} else {
		$AppUI->setMsg($file_id ? 'updated' : 'added', UI_MSG_OK, true);
	}

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
$AppUI->redirect($redirect);
?>

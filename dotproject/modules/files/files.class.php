<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

require_once ($AppUI->getSystemClass('libmail'));
require_once ($AppUI->getSystemClass('dp'));
require_once ($AppUI->getSystemClass('date'));
require_once ($AppUI->getModuleClass('tasks'));
require_once ($AppUI->getModuleClass('projects'));
global $helpdesk_available;

/** The helpdesk module seems to no longer have files support (at least in the dotmods CVS)
so this breaks if helpdesk is available.  This is NOT the way to build co-operating modules.
if ($helpdesk_available = $AppUI->isActiveModule('helpdesk')) {
	require_once($AppUI->getModuleClass('helpdesk'));
}
*/
$helpdesk_available = false;
/**
* File Class
*/
class CFile extends CDpObject {
	
	var $file_id = NULL;
	var $file_version_id = NULL;
	var $file_project = NULL;
	var $file_real_filename = NULL;
	var $file_task = NULL;
	var $file_name = NULL;
	var $file_parent = NULL;
	var $file_description = NULL;
	var $file_type = NULL;
	var $file_owner = NULL;
	var $file_date = NULL;
	var $file_size = NULL;
	var $file_version = NULL;
	var $file_category = NULL;
	var $file_folder = NULL;
	var $file_checkout = NULL;
	var $file_co_reason = NULL;
	
	var $_message = NULL;
	
	// This "breaks" check-in/upload if helpdesk is not present.
	// class variable needs to be added "dymanically"
	//var $file_helpdesk_item = NULL;
	
	
	function CFile() {
		global $AppUI, $helpdesk_available;
		if ($helpdesk_available) {
			$this->file_helpdesk_item = NULL;
		}
		$this->CDpObject('files', 'file_id');
	}
	
	function store() {
		global $helpdesk_available;
		if ($helpdesk_available && $this->file_helpdesk_item != 0) {
			$this->addHelpDeskTaskLog();
		}
		parent::store();
	}
	
	function addHelpDeskTaskLog() {
		global $AppUI, $helpdesk_available;
		if ($helpdesk_available && $this->file_helpdesk_item != 0) {
			
			// create task log with information about the file that was uploaded
			$task_log = new CHDTaskLog();
			$task_log->task_log_help_desk_id = $this->_hditem->item_id;
			if ($this->_message != 'deleted') {
				$task_log->task_log_name = 'File ' . $this->file_name .' uploaded';
			} else {
				$task_log->task_log_name = 'File ' . $this->file_name .' deleted';
			}
			$task_log->task_log_description = $this->file_description;
			$task_log->task_log_creator = $AppUI->user_id;
			$date = new CDate();
			$task_log->task_log_date = $date->format(FMT_DATETIME_MYSQL);
			if ($msg = $task_log->store()) {
				$AppUI->setMsg($msg, UI_MSG_ERROR);
			}
		}
		return NULL;
	}
	
	function canAdmin() {
		global $AppUI;
		
		if (! $this->file_project) {
			return false;
		}
		if (! $this->file_id) {
			return false;
		}
		
		$result = false;
		$this->_query->clear();
		$this->_query->addTable('projects');
		$this->_query->addQuery('project_owner');
		$this->_query->addWhere('project_id = ' . $this->file_project);
		$res = $this->_query->exec();
		if ($res && $row = db_fetch_assoc($res)) {
			if ($row['project_owner'] == $AppUI->user_id)
				$result = true;
		}
		$this->_query->clear();
		return $result;
	}
	
	function check() {
		// ensure the integrity of some variables
		$this->file_id = intval($this->file_id);
		$this->file_version_id = intval($this->file_version_id);
		$this->file_parent = intval($this->file_parent);
		$this->file_task = intval($this->file_task);
		$this->file_project = intval($this->file_project);
		
		return NULL; // object is ok
	}
	
	function checkout($userId, $coReason) {
		global $AppUI;
		if (! $this->file_id) {
			return $AppUI->_('fileIdError', UI_OUTPUT_RAW);
		}
		$this->file_checkout = $userId;
		$this->file_co_reason = $coReason;
		
		return NULL;
	}

	function delete() {
		global $helpdesk_available;
		
		// delete the main table reference
		$message = parent :: delete($this->file_id, $this->file_name, $this->file_project);
		if ($message) {
			return $message;
		}
		
		// remove the file from the file system
		$this->deleteFile();
		
		// delete any index entries
		$this->_query->clear();
		$this->_query->setDelete('files_index');
		$this->_query->addQuery('*');
		$this->_query->addWhere('file_id = ' . $this->file_id);
		if (!$this->_query->exec()) {
			$this->_query->clear();
			return db_error();
		}
		$this->_query->clear();
		
		$this->_message = 'deleted';
		
		if ($helpdesk_available && $this->file_helpdesk_item != 0) {
			$this->addHelpDeskTaskLog();
		}
		
		return null;
	}

	// delete File from File System
	function deleteFile() {
		global $dPconfig;
		return @unlink(DP_BASE_DIR . '/files/' . $this->file_project . '/' 
		               . $this->file_real_filename);
	}

	// move the file if the affiliated project was changed
	function moveFile($oldProj, $realname) {
		global $AppUI, $dPconfig;
		if (!is_dir(DP_BASE_DIR . '/files/' . $this->file_project)) {
			$res = mkdir(DP_BASE_DIR . '/files/' . $this->file_project, 0777);
			if (!$res) {
				$AppUI->setMsg('Upload folder not setup to accept uploads' 
				               . ' - change permission on files/ directory.', UI_MSG_ALLERT);
				return false;
			}
		}
		$res = rename(DP_BASE_DIR . '/files/' . $oldProj . '/' . $realname, 
		              DP_BASE_DIR . '/files/' . $this->file_project . '/' . $realname);
		
		return $res;
	}

	// duplicate a file into root
	function duplicateFile($oldProj, $realname) {
		global $AppUI, $dPconfig;
		if (!is_dir(DP_BASE_DIR.'/files/0')) {
			$res = mkdir(DP_BASE_DIR.'/files/0', 0777);
			if (!$res) {
				$AppUI->setMsg('Upload folder not setup to accept uploads.' 
				               . ' Change permission on files/ directory.', UI_MSG_ALLERT);
				return false;
			}
		}
		$dest_realname = uniqid(rand());
		$res = copy(DP_BASE_DIR . '/files/' . $oldProj . '/' . $realname, 
		            DP_BASE_DIR . '/files/0/' . $dest_realname);
		
		return ((!$res) ? false : $dest_realname);
	}
	
	// move a file from a temporary (uploaded) location to the file system
	function moveTemp($upload) {
		global $AppUI, $dPconfig;
		// check that directories are created
		if (!is_dir(DP_BASE_DIR.'/files')) {
			$res = mkdir(DP_BASE_DIR.'/files', 0777);
		    if (!$res) {
				return false;
			}
		}
		if (!is_dir(DP_BASE_DIR.'/files/'.$this->file_project)) {
			$res = mkdir(DP_BASE_DIR.'/files/'.$this->file_project, 0777);
			if (!$res) {
				$AppUI->setMsg('Upload folder not setup to accept uploads' 
				               . ' - change permission on files/ directory.', UI_MSG_ALLERT);
				return false;
			}
		}
		
		
		$filepath = DP_BASE_DIR.'/files/'.$this->file_project.'/'.$this->file_real_filename;
		// move it
		$res = move_uploaded_file($upload['tmp_name'], $filepath);
		return $res;
	}

	// parse file for indexing
	function indexStrings() {
		GLOBAL $AppUI, $dPconfig;
		// get the parser application
		$parser = @$dPconfig['parser_' . $this->file_type];
		if (!$parser) {
			$parser = $dPconfig['parser_default'];
		}
		if (!$parser) {
			return false;
		}
		// buffer the file
		$filepath = (DP_BASE_DIR . '/files/' . $this->file_project . '/' 
		             . $this->file_real_filename);
		$fp = fopen($filepath, 'rb');
		$x = fread($fp, $this->file_size);
		fclose($fp);
		
		// parse it
		$parser = $parser . ' ' . $filepath;
		$pos = mb_strpos($parser, '/pdf');
		$x = (($pos !== false) ? `$parser -` : `$parser`);
		
		// if nothing, return
		if (mb_strlen($x) < 1) {
			return 0;
		}
		// remove punctuation and parse the strings
		$x = str_replace(array('.', ',', '!', '@', '(', ')'), ' ', $x);
		$warr = mb_split('[[:space:]]', $x);
		
		$wordarr = array();
		$nwords = count($warr);
		for ($x=0; $x < $nwords; $x++) {
			$newword = $warr[$x];
			if (!ereg('[[:punct:]]', $newword) && !ereg('[[:digit:]]', $newword) 
			    && mb_strlen(trim($newword)) > 2) {
				$wordarr[] = array('word' => $newword, 'wordplace' => $x);
			}
		}
		db_exec('LOCK TABLES files_index WRITE');
		// filter out common strings
		$ignore = array();
		include_once (DP_BASE_DIR . '/modules/files/file_index_ignore.php');
		foreach ($ignore as $w) {
			unset($wordarr[$w]);
		}
		// insert the strings into the table
		while (list($key, $val) = each($wordarr)) {
			$this->_query->clear();
			$this->_query->addTable('files_index');
			$this->_query->addReplace('file_id', $this->file_id);
			$this->_query->addReplace('word', $wordarr[$key]['word']);
			$this->_query->addReplace('word_placement', $wordarr[$key]['wordplace']);
			$this->_query->exec();
			$this->_query->clear();
		}
		
		db_exec('UNLOCK TABLES;');
		return nwords;
	}
	
	//function notifies about file changing
	function notify() {	
		GLOBAL $AppUI, $dPconfig, $locale_char_set, $helpdesk_available;
		
		// if helpdesk_item is available send notification to assigned users
		if ($helpdesk_available && $this->file_helpdesk_item != 0) {
			$this->_hditem = new CHelpDeskItem();
			$this->_hditem->load($this->file_helpdesk_item);
			
			$task_log = new CHDTaskLog();
			$task_log_help_desk_id = $this->_hditem->item_id;
			// send notifcation about new log entry
			// 2 = TASK_LOG
			$this->_hditem->notify(2, $task_log->task_log_id);
			
		}
		
		//if no project specified than we will not do anything
		if (intval($this->file_project) != 0) {
			$project = new CProject();
			$project->load($this->file_project);
			$mail = new Mail;		
			
			if (intval($this->file_task) != 0) {
				//notify all assigned users
				$task = new CTask();
				$task->load($this->file_task);
				$mail->Subject($project->project_name . '::' . $task->task_name . '::' 
				               . $this->file_name, $locale_char_set);
			} else {
				//notify project owner
				$mail->Subject($project->project_name . '::' . $this->file_name, $locale_char_set);
			}
			
			$body = $AppUI->_('Project').': '.$project->project_name;
			$body .= ("\n" . $AppUI->_('URL') . ': ' . DP_BASE_URL 
			          . '/index.php?m=projects&amp;a=view&amp;project_id=' . $this->file_project);
			
			$users = array();
			if (intval($this->file_task) != 0) {
				$body .= "\n\n" . $AppUI->_('Task') . ': ' . $task->task_name;
				$body .= ("\n" . $AppUI->_('URL') . ': ' . DP_BASE_URL 
				          . '/index.php?m=tasks&amp;a=view&amp;task_id=' . $this->file_task);
				$body .= ("\n" . $AppUI->_('Description') . ': ' . "\n" . $task->task_description);
				
				//preparing users array
				$this->_query->clear();
				$this->_query->addTable('tasks', 't');
				$this->_query->addJoin('user_tasks', 'u', 'u.task_id = t.task_id');
				$this->_query->addJoin('users', 'a', 'a.user_id = u.user_id');
				$this->_query->addJoin('contacts', 'ac', 'a.user_contact = ac.contact_id');
				$this->_query->addQuery('a.user_id as assignee_id' 
				                        . ', ac.contact_email as assignee_email' 
				                        . ', ac.contact_first_name as assignee_first_name' 
				                        . ', ac.contact_last_name as assignee_last_name');
				$this->_query->addWhere('t.task_id = ' . $this->file_task);
				$users = $this->_query->loadList();
			} else {
				//find project owner and notify him about new or modified file
				$this->_query->clear();
				$this->_query->addTable('users', 'u');
				$this->_query->addJoin('contacts', 'uc', 'uc.contact_id = u.user_contact');
				$this->_query->addQuery('u.user_id as owner_id' 
				                        . ', uc.contact_first_name as owner_first_name' 
				                        . ', uc.contact_last_name as owner_last_name' 
				                        . ', uc.contact_email as owner_email');
				$this->_query->addWhere('u.user_id = ' . $project->project_owner);
				$users = $this->_query->loadList();
			}
			$this->_query->clear();
			
			$body .= ("\n\nFile " . $this->file_name . ' was ' . $this->_message . ' by ' 
			          . $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
			if ($this->_message != 'deleted') {
				$body .= ("\n" . $AppUI->_('URL') . ': ' . DP_BASE_URL 
				          . '/fileviewer.php?file_id=' . $this->file_id);
				$body .= "\n" . $AppUI->_('Description') . ':' . "\n" . $this->file_description;
				if ($this->file_co_reason != '') {
					$body .= ("\n" . $AppUI->_('Checkout Reason') . ':' . "\n" 
					          . $this->file_co_reason);
				}
			}
			
			//send mail			
			$mail->Body($body, 
			            (isset($GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : ''));
			$mail->From ('"' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name . '" <' 
			             . $AppUI->user_email . '>');
			
			if (intval($this->file_task) != 0) {
				foreach ($users as $row) {
					if ($row['assignee_id'] != $AppUI->user_id 
					    && $mail->ValidEmail($row['assignee_email'])) {
						//send e-mails
						$mail->To($row['assignee_email'], true);
						$mail->Send();
					}
				}
			} else { 
				foreach ($users as $row) {
					if ($row['owner_id'] != $AppUI->user_id) {
						if ($mail->ValidEmail($row['owner_email'])) {
						    //sending mail to project owner (there should be only one) 
							$mail->To($row['owner_email'], true);
							$mail->Send();
						}
					}
				}
			}
		}
	}
	
	function notifyContacts() {
		GLOBAL $AppUI, $dPconfig, $locale_char_set;
		//if no project specified than we will not do anything
		if ($this->file_project != 0) {
			$project = new CProject();
			$project->load($this->file_project);
			$mail = new Mail;
			
			if (intval($this->file_task) != 0) {
				//notify task contacts
				$task = new CTask();
				$task->load($this->file_task);
				$mail->Subject($project->project_name . '::' . $task->task_name . '::' 
				               . $this->file_name, $locale_char_set);
			} else {
				//notify project contacts
				$mail->Subject($project->project_name . '::' . $this->file_name, $locale_char_set);
			}
			
			$body = $AppUI->_('Project') . ': ' . $project->project_name;
			$body .= ("\n" . $AppUI->_('URL') . ': ' . DP_BASE_URL 
			          . '/index.php?m=projects&amp;a=view&amp;project_id=' . $this->file_project);
			
			$users = array();
			if (intval($this->file_task) != 0) {
				$body .= "\n\n" . $AppUI->_('Task') . ': ' . $task->task_name;
				$body .= ("\n" . $AppUI->_('URL') . ': ' . DP_BASE_URL 
				          . '/index.php?m=tasks&amp;a=view&amp;task_id=' . $this->file_task);
				$body .= "\n" . $AppUI->_('Description') . ":\n" . $task->task_description;
				$this->_query->clear();
				$this->_query->addTable('project_contacts', 'pc');
				$this->_query->addJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
				$this->_query->addQuery('c.contact_email as contact_email' 
				                        . ', c.contact_first_name as contact_first_name' 
				                        . ', c.contact_last_name as contact_last_name');
				$this->_query->addWhere('pc.project_id = ' . $this->file_project);
				$pc_users = $this->_query->loadList();
				$this->_query->clear();   
				
				$this->_query->addTable('task_contacts', 'tc');
				$this->_query->addJoin('contacts', 'c', 'c.contact_id = tc.contact_id');
				$this->_query->addQuery('c.contact_email as contact_email' 
				                        . ', c.contact_first_name as contact_first_name' 
				                        . ', c.contact_last_name as contact_last_name');
				$this->_query->addWhere('tc.task_id = ' . $this->file_task);
				$tc_users = $this->_query->loadList();
				$this->_query->clear();
				
  				$users = array_merge($pc_users, $tc_users);
			} else {
				$this->_query->addTable('project_contacts', 'pc');
				$this->_query->addJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
				$this->_query->addQuery('c.contact_email as contact_email' 
				                        . ', c.contact_first_name as contact_first_name' 
				                        . ', c.contact_last_name as contact_last_name');
				$this->_query->addWhere('pc.project_id = ' . $this->file_project);
				$users = $this->_query->loadList();
				$this->_query->clear();
			}
			
			$body .= ("\n\nFile " . $this->file_name . ' was ' . $this->_message . ' by ' 
			          . $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
			if ($this->_message != 'deleted') {
				$body .= ("\n" . $AppUI->_('URL') . ': ' . DP_BASE_URL 
				          . '/fileviewer.php?file_id=' . $this->file_id);
				$body .= "\n" . $AppUI->_('Description') . ":\n" . $this->file_description;
				if ($this->file_co_reason != '') {
					$body .= ("\n" . $AppUI->_('Checkout Reason') . ':' . "\n" 
					          . $this->file_co_reason);
				}
			}
			
			// send mail
			$mail->Body($body, 
			            (isset($GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : ''));
			$mail->From ('"' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name . '" <' 
			             . $AppUI->user_email . '>');
			
			
			foreach ($users as $row) {
				if ($mail->ValidEmail($row['contact_email'])) {
					$mail->To($row['contact_email'], true);
					$mail->Send();
				}
			}
		}
		return '';
	}

	function getOwner() {
		if (!($this->file_owner)) {
			return '';
		}
		$this->_query->clear();
		$this->_query->addTable('users', 'a');
		$this->_query->leftJoin('contacts', 'b', 'b.contact_id = a.user_contact');
		$this->_query->addQuery('contact_first_name, contact_last_name');
		$this->_query->addWhere('a.user_id = ' . $this->file_owner);
		if (!$this->_query->exec()) {
			$this->_query->clear();
			return db_error();
		}
		$row = $this->_query->fetchRow();
		$this->_query->clear();
		
		return ($row['contact_first_name'] . ' ' . $row['contact_last_name']);
	}
	
	function getTaskName() {
		if (!($this->file_task)) {
			return '';
		}
		$this->_query->clear();
		$this->_query->addTable('tasks');
		$this->_query->addQuery('task_name');
		$this->_query->addWhere('task_id = ' . $this->file_task);
		if (!$this->_query->exec()) {
			$this->_query->clear();
			return db_error();
		}
		$row = $this->_query->fetchRow();
		$this->_query->clear();
		
		return $row['task_name'];
	}
	
}

/**
 * File Folder Class
 */
class CFileFolder extends CDpObject {
	/** @param int file_folder_id **/
	var $file_folder_id = null;
	/** @param int file_folder_parent The id of the parent folder **/
	var $file_folder_parent = null;
	/** @param string file_folder_name The folder's name **/
	var $file_folder_name = null;
	/** @param string file_folder_description The folder's description **/
	var $file_folder_description = null;
	
	function CFileFolder() {
		$this->CDpObject('file_folders', 'file_folder_id');
	}
	
	function check() {
		$this->file_folder_id = intval($this->file_folder_id);
		$this->file_folder_parent = intval($this->file_folder_parent);
		return null;
	}
	
	function delete($oid=null) {
		$oid = intval(($oid ? $oid : $this->file_folder_id));
	    return parent :: delete($oid);
	}
	
	function canDelete(&$msg, $oid=null, $joins=null) {
		global $AppUI;
		
		$oid = intval(($oid ? $oid : $this->file_folder_id));
		
		if (!(parent::canDelete($msg, $oid, $joins))) {
			return false;
		}
		$this->_query->clear();
      	$this->_query->addTable($this->_tbl);
      	$this->_query->addQuery('COUNT(DISTINCT file_folder_id) AS num_of_subfolders');
      	$this->_query->addWhere('file_folder_parent=' . $oid);
      	$sql1 = $this->_query->prepare();
      	$this->_query->clear();
		
      	$this->_query->addTable('files');
      	$this->_query->addQuery('COUNT(DISTINCT file_id) AS num_of_files');
      	$this->_query->addWhere('file_folder=' . $oid);
      	$sql2 = $this->_query->prepare();
      	$this->_query->clear();
		
		if (db_loadResult($sql1) > 0 || db_loadResult($sql2) > 0) {
			$msg = $AppUI->_('Can not delete folder, it has files and/or subfolders.');
			return false;
		}
		
		return true;
	}
	
	/** @return string Returns the name of the parent folder or null if no parent was found **/
	function getParentFolderName() {
		$this->_query->clear();
      	$this->_query->addTable($this->_tbl);
      	$this->_query->addQuery('file_folder_name');
      	$this->_query->addWhere('file_folder_id=' . $this->file_folder_parent);
      	$sql = $this->_query->prepare();
		return db_loadResult($sql);
	}
	
	function countFolders() {
		$this->_query->clear();
      	$this->_query->addTable($this->_tbl);
      	$this->_query->addQuery('COUNT(*)');
      	$sql = $this->_query->prepare();
		$result = db_loadResult($sql);
		return $result;
	}
}


function file_size($size) {
  
	$size_measurments = array(0 => 'B', 1 => 'KiB', 2 => 'MiB', 3 => 'GiB', 4 => 'TiB');
	$size_length = sizeof($size_measurments) - 1;
	
	for ($i = 0; $i < $size_length; $i++) {
		if ($size < pow(2, (10 * ($i + 1)))) {
			break;
		}
	}
	
	return (round(($size / pow(2, 10 * ($i))), 2) . ' ' . $size_measurments[$i]);
}

function last_file($file_versions, $file_name, $file_project) {
	$latest = NULL;
	
	if (isset($file_versions)) {
		foreach ($file_versions as $file_version) {
			if ($file_version['file_name'] == $file_name 
			    && $file_version['file_project'] == $file_project 
			    && ($latest == NULL || $latest['file_version'] < $file_version['file_version'])) {
				$latest = $file_version;
			}
		}
	}
	
	return $latest;
}

function getIcon($file_type) {
	global $dPconfig;
	$result = '';
	$mime = str_replace('/','-',$file_type);
	$icon = 'gnome-mime-' . $mime;
	if (is_file(DP_BASE_DIR . '/modules/files/images/icons/' . $icon . '.png')) {
		$result = 'icons/' . $icon . '.png';
	} else {
		$mime = mb_split('/', $file_type);
		switch ($mime[0]) {
		case 'audio' : 
			$result = 'icons/wav.png';
			break;
		case 'image' :
			$result = 'icons/image.png';
			break;
		case 'text' :
			$result = 'icons/text.png';
			break;
		case 'video' :
			$result = 'icons/video.png';
			break;
		}
		if ($mime[0] == 'application') {
			switch($mime[1]) {
			case 'vnd.ms-excel' : 
				$result = 'icons/spreadsheet.png';
            	break;
			case 'vnd.ms-powerpoint' :
				$result = 'icons/quicktime.png';
            	break;
			case 'octet-stream' :
				$result = 'icons/source_c.png';
            	break;
			default :
				$result = 'icons/documents.png';
			}
		}
	}
	
	if ($result == '') {
		switch ($obj->$file_category) {
		default: // no idea what's going on
			$result = 'icons/unknown.png';
      	}
	}
	return $result;      
}

function getNextVersionID() {
	
	$q = new DBQuery;
	$q->addTable('files', 'f');
	$q->addQuery('MAX(f.file_version_id) AS max_version_id');
	$latest_file_version = intval($q->loadResult());
	$q->clear();
	
	return ($latest_file_version + 1);
}

function getFolderSelectList() {
	global $AppUI;
	
	$folder = new CFileFolder();
	$allowed_folders = array();
	$allowed_folders_pre = $folder->getAllowedRecords($AppUI->user_id, 
													  ('file_folder_id, file_folder_name' 
													   . ', file_folder_parent'), 
													  'file_folder_name', 'file_folder_id');
	//get array in proper "format" for tree
	foreach ($allowed_folders_pre as $results) {
		$folder_id = $results['file_folder_id'];
		$allowed_folders[$folder_id] = array($results['file_folder_id'], 
											 $results['file_folder_name'], 
											 $results['file_folder_parent']);
	}
	
	$folders = arrayMerge(array(array(0, $AppUI->_('Root'), -1)), 
	                      $allowed_folders);
	return $folders;
}
?>

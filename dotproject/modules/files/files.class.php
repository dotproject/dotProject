<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

require_once( $AppUI->getSystemClass( 'libmail' ) );
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getSystemClass( 'date' ) );
require_once( $AppUI->getModuleClass( 'tasks' ) );
require_once( $AppUI->getModuleClass( 'projects' ) );
global $helpdesk_available;

if (($helpdesk_available = $AppUI->isActiveModule('helpdesk')) {
	require_once( $AppUI->getModuleClass( 'helpdesk' ) );
}
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
      var $file_folder = null;
	var $file_checkout = NULL;
	var $file_co_reason = NULL;
	var $file_helpdesk_item = NULL;

	
	function CFile() {
		global $AppUI;
		$this->CDpObject( 'files', 'file_id' );
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
			if ($this->_message != "deleted") {
				$task_log->task_log_name = "File ". $this->file_name ." uploaded";
			} else {
				$task_log->task_log_name = "File ". $this->file_name ." deleted";
			}
			$task_log->task_log_description = $this->file_description;
			$task_log->task_log_creator = $AppUI->user_id;
			$date = new CDate();
			$task_log->task_log_date = $date->format( FMT_DATETIME_MYSQL );
			if ($msg = $task_log->store()) {
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			}
		}
		return NULL;
	}

	function canAdmin() {
		global $AppUI;

		if (! $this->file_project)
			return false;
		if (! $this->file_id)
			return false;

		$result = false;
		$this->_query->clear();
		$this->_query->addTable('projects');
		$this->_query->addQuery('project_owner');
		$this->_query->addWhere('project_id = ' . $this->file_project);
		$res = $this->_query->exec();
		if ($res && $row = db_fetch_assoc($res)) {
			if ($row['project_owner'] == $AppUI->user_id)
				$result =  true;
		} 
		$this->_query->clear();
		return $result;
	}

	function check() {
	// ensure the integrity of some variables
		$this->file_id = intval( $this->file_id );
		$this->file_version_id = intval($this->file_version_id);
		$this->file_parent = intval( $this->file_parent );
		// $this->file_category = intval( $this->file_category );
		$this->file_task = intval( $this->file_task );
		$this->file_project = intval( $this->file_project );

		return NULL; // object is ok
	}

	function delete() {
		global $helpdesk_available;
		if (!$this->canDelete( $msg ))
			return $msg;
		$this->_message = "deleted";
		addHistory('files', $this->file_id, 'delete',  $this->file_name, $this->file_project);
	// remove the file from the file system
		$this->deleteFile();
	// delete any index entries
		$q  = new DBQuery;
		$q->setDelete('files_index');
		$q->addQuery('*');
		$q->addWhere("file_id = $this->file_id");
		if (!$q->exec()) {
			$q->clear();
			return db_error();
		}
	// delete the main table reference
		$q->clear();
		$q->setDelete('files');
		$q->addQuery('*');
		$q->addWhere("file_id = $this->file_id");
		if (!$q->exec()) {
			$q->clear();
			return db_error();
		}
		$q->clear();
		
		if ($helpdesk_available && $this->file_helpdesk_item != 0) {
			$this->addHelpDeskTaskLog();
		}
		return NULL;
	}

	// delete File from File System
	function deleteFile() {
		global $dPconfig;
		return @unlink( $dPconfig['root_dir'].'/files/'.$this->file_project.'/'.$this->file_real_filename );
	}

	// move the file if the affiliated project was changed
	function moveFile( $oldProj, $realname ) {
		global $AppUI, $dPconfig;
		if (!is_dir("{$dPconfig['root_dir']}/files/$this->file_project")) {
		    $res = mkdir( "{$dPconfig['root_dir']}/files/$this->file_project", 0777 );
			 if (!$res) {
                                $AppUI->setMsg( "Upload folder not setup to accept uploads - change permission on files/ directory.", UI_MSG_ALLERT );
			     return false;
			 }
		}
		$res = rename("{$dPconfig['root_dir']}/files/$oldProj/$realname", "{$dPconfig['root_dir']}/files/$this->file_project/$realname");

		if (!$res) {
		    return false;
		}
		return true;
	}

	// duplicate a file into root
	function duplicateFile( $oldProj, $realname ) {
		global $AppUI, $dPconfig;
		if (!is_dir("{$dPconfig['root_dir']}/files/0")) {
		    $res = mkdir( "{$dPconfig['root_dir']}/files/0", 0777 );
			 if (!$res) {
                                $AppUI->setMsg( "Upload folder not setup to accept uploads - change permission on files/ directory.", UI_MSG_ALLERT );
			     return false;
			 }
		}
		$dest_realname = uniqid( rand() );
		$res = copy("{$dPconfig['root_dir']}/files/$oldProj/$realname", "{$dPconfig['root_dir']}/files/0/$dest_realname");

		if (!$res) {
		    return false;
		}
		return $dest_realname;
	}

// move a file from a temporary (uploaded) location to the file system
	function moveTemp( $upload ) {
		global $AppUI, $dPconfig;
	// check that directories are created
		if (!is_dir("{$dPconfig['root_dir']}/files")) {
		    $res = mkdir( "{$dPconfig['root_dir']}/files", 0777 );
		    if (!$res) {
			     return false;
			 }
		}
		if (!is_dir("{$dPconfig['root_dir']}/files/$this->file_project")) {
		    $res = mkdir( "{$dPconfig['root_dir']}/files/$this->file_project", 0777 );
			 if (!$res) {
                                $AppUI->setMsg( "Upload folder not setup to accept uploads - change permission on files/ directory.", UI_MSG_ALLERT );
			     return false;
			 }
		}


		$this->_filepath = "{$dPconfig['root_dir']}/files/$this->file_project/$this->file_real_filename";
	// move it
		$res = move_uploaded_file( $upload['tmp_name'], $this->_filepath );
		if (!$res) {
		    return false;
		}
		return true;
	}

// parse file for indexing
	function indexStrings() {
		GLOBAL $AppUI, $dPconfig;
	// get the parser application
		$parser = @$dPconfig['parser_' . $this->file_type];
		if (!$parser)
			$parser = $dPconfig['parser_default'];
		if (!$parser) 
			return false;
	// buffer the file
		$this->_filepath = "{$dPconfig['root_dir']}/files/$this->file_project/$this->file_real_filename";
		$fp = fopen( $this->_filepath, "rb" );
		$x = fread( $fp, $this->file_size );
		fclose( $fp );
	// parse it
		$parser = $parser . " " . $this->_filepath;
		$pos = strpos( $parser, '/pdf' );
		if (false !== $pos) {
			$x = `$parser -`;
		} else {
			$x = `$parser`;
		}
	// if nothing, return
		if (strlen( $x ) < 1) {
			return 0;
		}
	// remove punctuation and parse the strings
		$x = str_replace( array( ".", ",", "!", "@", "(", ")" ), " ", $x );
		$warr = split( "[[:space:]]", $x );

		$wordarr = array();
		$nwords = count( $warr );
		for ($x=0; $x < $nwords; $x++) {
			$newword = $warr[$x];
			if (!ereg( "[[:punct:]]", $newword )
				&& strlen( trim( $newword ) ) > 2
				&& !ereg( "[[:digit:]]", $newword )) {
				$wordarr[] = array( "word" => $newword, "wordplace" => $x );
			}
		}
		db_exec( "LOCK TABLES files_index WRITE" );
	// filter out common strings
		$ignore = array();
		include "{$dPconfig['root_dir']}/modules/files/file_index_ignore.php";
		foreach ($ignore as $w) {
			unset( $wordarr[$w] );
		}
	// insert the strings into the table
		while (list( $key, $val ) = each( $wordarr )) {
			$q  = new DBQuery;
			$q->addTable('files_index');

			$q->addReplace("file_id", $this->file_id);
			$q->addReplace("word", $wordarr[$key]['word']);
			$q->addReplace("word_placement", $wordarr[$key]['wordplace']);
			$q->exec();
			$q->clear();
		}

		db_exec( "UNLOCK TABLES;" );
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
			$this->_hditem->notify( 2, $task_log->task_log_id );
			
		}
		//if no project specified than we will not do anything
		if ($this->file_project != 0) {
			$this->_project = new CProject();
			$this->_project->load($this->file_project);
			$mail = new Mail;		

			if ($this->file_task == 0) {//notify all developers
				$mail->Subject( $this->_project->project_name."::".$this->file_name, $locale_char_set);
			} else { //notify all assigned users			
				$this->_task = new CTask();
				$this->_task->load($this->file_task);
				$mail->Subject( $this->_project->project_name."::".$this->_task->task_name."::".$this->file_name, $locale_char_set);
			}
			
			$body = $AppUI->_('Project').": ".$this->_project->project_name;
			$body .= "\n".$AppUI->_('URL').":     {$dPconfig['base_url']}/index.php?m=projects&a=view&project_id=".$this->_project->project_id;
			
			if (intval($this->_task->task_id) != 0) {
				$body .= "\n\n".$AppUI->_('Task').":    ".$this->_task->task_name;
				$body .= "\n".$AppUI->_('URL').":     {$dPconfig['base_url']}/index.php?m=tasks&a=view&task_id=".$this->_task->task_id;
				$body .= "\n" . $AppUI->_('Description') . ":" . "\n".$this->_task->task_description;
				
				//preparing users array
				$q  = new DBQuery;
				$q->addTable('tasks', 't');
				$q->addQuery('t.task_id, cc.contact_email as creator_email, cc.contact_first_name as
						 creator_first_name, cc.contact_last_name as creator_last_name,
						 oc.contact_email as owner_email, oc.contact_first_name as owner_first_name,
						 oc.contact_last_name as owner_last_name, a.user_id as assignee_id, 
						 ac.contact_email as assignee_email, ac.contact_first_name as
						 assignee_first_name, ac.contact_last_name as assignee_last_name');
				$q->addJoin('user_tasks', 'u', 'u.task_id = t.task_id');
				$q->addJoin('users', 'o', 'o.user_id = t.task_owner');
				$q->addJoin('contacts', 'oc', 'o.user_contact = oc.contact_id');
				$q->addJoin('users', 'c', 'c.user_id = t.task_creator');
				$q->addJoin('contacts', 'cc', 'c.user_contact = cc.contact_id');
				$q->addJoin('users', 'a', 'a.user_id = u.user_id');
				$q->addJoin('contacts', 'ac', 'a.user_contact = ac.contact_id');
				$q->addWhere('t.task_id = '.$this->_task->task_id);
				$this->_users = $q->loadList();
			} else {
				//find project owner and notify him about new or modified file
				$q  = new DBQuery;
				$q->addTable('users', 'u');
				$q->addTable('projects', 'p');
				$q->addQuery('u.*');
				$q->addWhere('p.project_owner = u.user_id');
				$q->addWhere('p.project_id = '.$this->file_project);
				$this->_users = $q->loadList();
			}
			$body .= "\n\nFile ".$this->file_name." was ".$this->_message." by ".$AppUI->user_first_name . " " . $AppUI->user_last_name;
			if ($this->_message != "deleted") {
				$body .= "\n".$AppUI->_('URL').":     {$dPconfig['base_url']}/fileviewer.php?file_id=".$this->file_id;
				$body .= "\n" . $AppUI->_('Description') . ":" . "\n".$this->file_description;	
			}
			
			//send mail			
			$mail->Body( $body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "" );
			$mail->From ( '"' . $AppUI->user_first_name . " " . $AppUI->user_last_name . '" <' . $AppUI->user_email . '>');
			
			if (intval($this->_task->task_id) != 0) {
				foreach ($this->_users as $row) {
					if ($row['assignee_id'] != $AppUI->user_id) {
						if ($mail->ValidEmail($row['assignee_email'])) {
							$mail->To( $row['assignee_email'], true );
							$mail->Send();
						}
					}
				}
			} else { //sending mail to project owner
				foreach ($this->_users as $row) { //there should be only one row
					if ($row['user_id'] != $AppUI->user_id) {
						if ($mail->ValidEmail($row['user_email'])) {
							$mail->To( $row['user_email'], true );
							$mail->Send();
						}
					}
				}				
			}
		}
	}//notify

	function notifyContacts() {
		GLOBAL $AppUI, $dPconfig, $locale_char_set;
		//if no project specified than we will not do anything
		if ($this->file_project != 0) {
			$this->_project = new CProject();
			$this->_project->load($this->file_project);
			$mail = new Mail;		

			if ($this->file_task == 0) {//notify all developers
				$mail->Subject( $AppUI->_('Project').": ".$this->_project->project_name."::".$this->file_name, $locale_char_set);
			} else { //notify all assigned users			
				$this->_task = new CTask();
				$this->_task->load($this->file_task);
				$mail->Subject( $AppUI->_('Project').": ".$this->_project->project_name."::".$this->_task->task_name."::".$this->file_name, $locale_char_set);
			}

			$body = $AppUI->_('Project').": ".$this->_project->project_name;
			$body .= "\n".$AppUI->_('URL').":     {$dPconfig['base_url']}/index.php?m=projects&a=view&project_id=".$this->_project->project_id;
			
			if (intval($this->_task->task_id) != 0) {
				$body .= "\n\n".$AppUI->_('Task').":    ".$this->_task->task_name;
				$body .= "\n".$AppUI->_('URL').":     {$dPconfig['base_url']}/index.php?m=tasks&a=view&task_id=".$this->_task->task_id;
				$body .= "\n" . $AppUI->_('Description') . ": " . "\n".$this->_task->task_description;

				$q = new DBQuery;
				$q->addTable('project_contacts', 'pc');
  	    		      $q->addQuery('c.contact_email as contact_email, c.contact_first_name as contact_first_name, c.contact_last_name as contact_last_name');
				$q->addJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
				$q->addWhere('pc.project_id = '.$this->_project->project_id);
                        $sql = '('.$q->prepare().')';
                        $q->clear();
				//$sql = "(SELECT contacts.contact_last_name, contacts.contact_email, contacts.contact_first_name FROM project_contacts INNER JOIN contacts ON (project_contacts.contact_id = contacts.contact_id) WHERE (project_contacts.project_id = ".$this->_project->project_id.")) ";				
				$sql .= " UNION ";				
				$q->addTable('task_contacts', 'tc');
  	    		      $q->addQuery('c.contact_email as contact_email, c.contact_first_name as contact_first_name, c.contact_last_name as contact_last_name');
				$q->addJoin('contacts', 'c', 'c.contact_id = tc.contact_id');
				$q->addWhere('tc.task_id = '.$this->_task->task_id);
                        $sql .= '('.$q->prepare().')';
                        $q->clear();
				//$sql .= "(SELECT contacts.contact_last_name, contacts.contact_email, contacts.contact_first_name FROM task_contacts INNER JOIN contacts ON (task_contacts.contact_id = contacts.contact_id) WHERE (task_contacts.task_id = ".$this->_task->task_id."));";				
  				$this->_users = db_loadList($sql);
			} else {			
				$q = new DBQuery;
				$q->addTable('project_contacts', 'pc');
				$q->addQuery('pc.project_id, pc.contact_id');
  	    		      $q->addQuery('c.contact_email as contact_email, c.contact_first_name as contact_first_name, c.contact_last_name as contact_last_name');
				$q->addJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
				$q->addWhere('pc.project_id = '.$this->file_project);

				$this->_users = $q->loadList();
				$q->clear();
			}
			
			$body .= "\n\nFile ".$this->file_name." was ".$this->_message." by ".$AppUI->user_first_name . " " . $AppUI->user_last_name;
			if ($this->_message != "deleted") {
				$body .= "\n".$AppUI->_('URL').":     {$dPconfig['base_url']}/fileviewer.php?file_id=".$this->file_id;
				$body .= "\n" . $AppUI->_('Description') . ":" . "\n".$this->file_description;	
			}
			
			//send mail			
			$mail->Body( $body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "" );
			$mail->From ( '"' . $AppUI->user_first_name . " " . $AppUI->user_last_name . '" <' . $AppUI->user_email . '>');



			foreach ($this->_users as $row) {
				
				if ($mail->ValidEmail($row['contact_email'])) {
					$mail->To( $row['contact_email'], true );
					$mail->Send();
				}
			}		
			return '';
		}
	}

	function getOwner()
	{
		$owner = '';
		if (! $this->file_owner)
			return $owner;

		$this->_query->clear();
		$this->_query->addTable('users', 'a');
		$this->_query->leftJoin('contacts', 'b', 'b.contact_id = a.user_contact');
		$this->_query->addQuery('contact_first_name, contact_last_name');
		$this->_query->addWhere('a.user_id = ' . $this->file_owner);
		if ($qid =& $this->_query->exec())
			$owner = $qid->fields['contact_first_name'] . ' ' . $qid->fields['contact_last_name'];
		$this->_query->clear();
		
		return $owner;
	}

	function getTaskName()
	{
		$taskname = '';
		if (! $this->file_task)
			return $taskname;

		$this->_query->clear();
		$this->_query->addTable('tasks');
		$this->_query->addQuery('task_name');
		$this->_query->addWhere('task_id = ' . $this->file_task);
		if ($qid =& $this->_query->exec()) {
			if ($qid->fields['task_name'])
				$taskname = $qid->fields['task_name'];
			else
				$taskname = $qid->fields[0];
		}
		$this->_query->clear();
		return $taskname;
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
		$this->CDpObject( 'file_folders', 'file_folder_id' );
	}
	
	function getAllowedRecords($uid) {
		$q = new DBQuery();
      	$q->addTable('file_folders');
      	$q->addQuery('*');
      	$q->addOrder('file_folder_parent');
      	$q->addOrder('file_folder_name');
      	return $q->loadHashList();
	}
      	
	function check() {
		$this->file_folder_id = intval( $this->file_folder_id );
		$this->file_folder_parent = intval( $this->file_folder_parent );
		return null;
	}
	
	function delete( $oid=null ) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		if (!$this->canDelete( $msg, ($oid ? $oid : $this->file_folder_id) )) {
			return $msg;
		}
		$this->$k = $this->$k ? $this->$k : intval( ($oid ? $oid : $this->file_folder_id) );

		$q = new DBQuery();
		$q->setDelete($this->_tbl);
		$q->addWhere("{$this->_tbl_key} = {$this->$k}");
		$sql=$q->prepare();
		$q->clear();
//		$sql = "DELETE FROM $this->_tbl WHERE $this->_tbl_key = '".$this->$k."'";
		if (!db_exec( $sql )) {
			return db_error();
		} else {
			return NULL;
		}
	}
	
	function canDelete(&$msg, $oid) {
		global $AppUI;
		$q = new DBQuery();
      	$q->addTable('file_folders');
      	$q->addQuery('COUNT(DISTINCT file_folder_id) AS num_of_subfolders');
      	$q->addWhere("file_folder_parent=$oid");
      	$sql1 = $q->prepare();
      	$q->clear();

      	$q = new DBQuery();
      	$q->addTable('files');
      	$q->addQuery('COUNT(DISTINCT file_id) AS num_of_files');
      	$q->addWhere("file_folder=$oid");
      	$sql2 = $q->prepare();
      	$q->clear();
//		$sql = "SELECT COUNT(DISTINCT file_folder_id) AS num_of_subfolders FROM file_folders WHERE file_folder_parent = {$oid}";
		if (db_loadResult($sql1) > 0 || db_loadResult($sql2) > 0) {
			$msg[] = 'File Folders';
			$msg = $AppUI->_( "Can't delete folder, it has files and/or subfolders." ) . ": " . implode( ', ', $msg );
			return false;	
		}
		
		return true;	
		//$joins[] = array('label'=>'Files','name'=>'files','idfield'=>'file_id','joinfield'=>'file_folder');
		//return parent::canDelete(&$msg, $oid, $joins );
	}
	
	/** @return string Returns the name of the parent folder or null if no parent was found **/
	function getParentFolderName() {
		$q = new DBQuery();
      	$q->addTable('file_folders');
      	$q->addQuery('file_folder_name');
      	$q->addWhere("file_folder_id=$this->file_folder_parent");
      	$sql = $q->prepare();
/*		$sql = "SELECT file_folder_name" .
				"FROM file_folders" .
				"WHERE file_folder_id = $this->file_folder_parent";*/
		return db_loadResult($sql);
	}

	function countFolders() {
		$q = new DBQuery();
      	$q->addTable($this->_tbl);
      	$q->addQuery('COUNT(*)');
      	$sql = $q->prepare();
//		$sql = "SELECT COUNT(*) FROM $this->_tbl;";
		$result = db_loadResult($sql);
		return $result;
	}
}

function shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page)
{

	GLOBAL $AppUI;
	$xpg_break = false;
        $xpg_prev_page = $xpg_next_page = 1;
	
	echo "\t<table width='100%' cellspacing='0' cellpadding='0' border=0><tr>";

	if ($xpg_totalrecs > $xpg_pagesize) {
		$xpg_prev_page = $page - 1;
		$xpg_next_page = $page + 1;
		// left buttoms
		if ($xpg_prev_page > 0) {
			echo "<td align='left' width='15%'>";
			echo '<a href="./index.php?m=files&amp;page=1">';
			echo '<img src="images/navfirst.gif" border="0" Alt="First Page"></a>&nbsp;&nbsp;';
			echo '<a href="./index.php?m=files&amp;page=' . $xpg_prev_page . '">';
			echo "<img src=\"images/navleft.gif\" border=\"0\" Alt=\"Previous page ($xpg_prev_page)\"></a></td>";
		} else {
			echo "<td width='15%'>&nbsp;</td>\n";
		} 
		
		// central text (files, total pages, ...)
		echo "<td align='center' width='70%'>";
		echo "$xpg_totalrecs " . $AppUI->_('File(s)') . " ($xpg_total_pages " . $AppUI->_('Page(s)') . ")";
		echo "</td>";

		// right buttoms
		if ($xpg_next_page <= $xpg_total_pages) {
			echo "<td align='right' width='15%'>";
			echo '<a href="./index.php?m=files&amp;page='.$xpg_next_page.'">';
			echo '<img src="images/navright.gif" border="0" Alt="Next Page ('.$xpg_next_page.')"></a>&nbsp;&nbsp;';
			echo '<a href="./index.php?m=files&amp;page=' . $xpg_total_pages . '">';
			echo '<img src="images/navlast.gif" border="0" Alt="Last Page"></a></td>';
		} else {
			echo "<td width='15%'>&nbsp;</td></tr>\n";
		}
		// Page numbered list, up to 30 pages
		echo "<tr><td colspan=\"3\" align=\"center\">";
		echo " [ ";
	
		for($n = $page > 16 ? $page-16 : 1; $n <= $xpg_total_pages; $n++) {
			if ($n == $page) {
				echo "<b>$n</b></a>";
			} else {
				echo "<a href='./index.php?m=files&amp;page=$n'>";
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
				echo "<a href='./index.php?m=files&amp;page=$xpg_total_pages'>";
				echo $n . "</a>";
			} 
		} 
		echo " ] ";
		echo "</td></tr>";
	} else { // or we dont have any files..
		echo "<td align='center'>";
		if ($xpg_next_page > $xpg_total_pages) {
		echo $xpg_sqlrecs . " " . "Files" . " ";
		}
		echo "</td></tr>";
	} 
	echo "</table>";
}

function file_size($size)
{
        if ($size > 1024*1024*1024)
                return round($size / 1024 / 1024 / 1024, 2) . ' Gb';
        if ($size > 1024*1024)
                return round($size / 1024 / 1024, 2) . ' Mb';
        if ($size > 1024)
                return round($size / 1024, 2) . ' Kb';
        return $size . ' B';
}

function last_file($file_versions, $file_name, $file_project)
{
        $latest = NULL;
        //global $file_versions;
        if (isset($file_versions))
        foreach ($file_versions as $file_version)
                if ($file_version['file_name'] == $file_name && $file_version['file_project'] == $file_project)
                        if ($latest == NULL || $latest['file_version'] < $file_version['file_version'])
                                $latest = $file_version;

        return $latest;
}

function getIcon($file_type) {
      global $dPconfig;
      $result = '';
      $mime = str_replace('/','-',$file_type);
      $icon = 'gnome-mime-'.$mime;
      if (is_file($dPconfig['root_dir'].'/modules/files/images/icons/'.$icon.'.png')) {
    		$result = "icons/$icon.png";
      } else {
            $mime = split("/", $file_type);
            switch($mime[0]){
            	case "audio" : 
            		$result = "icons/wav.png";
            		break;
            	case "image" :
            		$result = "icons/image.png";
            		break;
            	case "text" :
            		$result = "icons/text.png";
            		break;
            	case "video" :
            		$result = "icons/video.png";
            		break;
            }
            if ($mime[0] == "application"){
            		switch($mime[1]){
            			case "vnd.ms-excel" : 
            				$result = "icons/spreadsheet.png";
            				break;
            			case "vnd.ms-powerpoint" :
            				$result = "icons/quicktime.png";
            				break;
            			case "octet-stream" :
            				$result = "icons/source_c.png";
            				break;
            			default :
            				$result = "icons/documents.png";
            		}
            }
      }      
      
      if ($result == ''){
      	switch($obj->$file_category){
      		default : // no idea what's going on
      			$result = "icons/unknown.png";
      	}
      }
      return $result;      
}
?>

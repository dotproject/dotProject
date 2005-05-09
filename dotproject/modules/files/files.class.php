<?php /* FILES $Id$ */
require_once( $AppUI->getSystemClass( 'libmail' ) );
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getModuleClass( 'tasks' ) );
require_once( $AppUI->getModuleClass( 'projects' ) );
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
	var $file_checkout = NULL;
	var $file_co_reason = NULL;

	
	function CFile() {
		$this->CDpObject( 'files', 'file_id' );
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
		global $dPconfig;
		if (!$this->canDelete( $msg ))
			return $msg;
		$this->_message = "deleted";
		addHistory('files', $this->file_id, 'delete',  $this->file_name, $this->file_project);
	// remove the file from the file system
		@unlink( "{$dPconfig['root_dir']}/files/$this->file_project/$this->file_real_filename" );
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
		return NULL;
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

			$q->addInsert("file_id", $this->file_id);
			$q->addInsert("word", $wordarr[$key]['word']);
			$q->addInsert("word_placement", $wordarr[$key]['wordplace']);
			$q->exec();
			$q->clear();
		}

		db_exec( "UNLOCK TABLES;" );
		return nwords;
	}
	
	//function notifies about file changing
	function notify() {	
		GLOBAL $AppUI, $dPconfig, $locale_char_set;
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

?>

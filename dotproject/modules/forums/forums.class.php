<?php /* FORUMS $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}


require_once( $AppUI->getSystemClass( 'libmail' ) );

class CForum extends CDpObject {
	var $forum_id = NULL;
	var $forum_project = NULL;
	var $forum_status = NULL;
	var $forum_owner = NULL;
	var $forum_name = NULL;
	var $forum_create_date = NULL;
	var $forum_last_date = NULL;
	var $forum_last_id = NULL;
	var $forum_message_count = NULL;
	var $forum_description = NULL;
	var $forum_moderated = NULL;

	function CForum() {
		// empty constructor
		parent::CDpObject('forums', 'forum_id');
	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return "CForum::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
		if ($this->forum_id === NULL) {
			return 'forum_id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return "CForum::store-check failed<br />$msg";
		}
		if( $this->forum_id ) {
			$ret = db_updateObject( 'forums', $this, 'forum_id', false ); // ! Don't update null values
			if($this->forum_name) {
				// when adding messages, this functon is called without first setting 'forum_name'
				addHistory('forums', $this->forum_id, 'update', $this->forum_name);
			}
		} else {
			$this->forum_create_date = db_datetime( time() );
			$ret = db_insertObject( 'forums', $this, 'forum_id' );
			addHistory('forums', $this->forum_id, 'add', $this->forum_name);
		}
		if( !$ret ) {
			return "CForum::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete() {
		$q  = new DBQuery;
		$q->setDelete('forum_visits');
		$q->addWhere('visit_forum = '.$this->forum_id);
		$q->exec(); // No error if this fails, it is not important.
		$q->clear();
		
		$q->setDelete('forums');
		$q->addWhere('forum_id = '.$this->forum_id);
		if (!$q->exec()) {
			$q->clear();
			return db_error();
		}
		// $sql = "DELETE FROM forum_messages WHERE message_forum = $this->forum_id";
		$q->clear();
		$q->setDelete('forum_messages');
		$q->addWhere('message_forum = '.$this->forum_id);
		if (!$q->exec()) {
			$result =  db_error();
		} else {
			addHistory('forums', $this->forum_id, 'delete', $this->forum_name);
			$result =  NULL;
		}
		$q->clear();
		return $result;
	}
}

class CForumMessage {
	var $message_id = NULL;
	var $message_forum = NULL;
	var $message_parent = NULL;
	var $message_author = NULL;
	var $message_editor = NULL;
	var $message_title = NULL;
	var $message_date = NULL;
	var $message_body = NULL;
	var $message_published = NULL;

	function CForumMessage() {
		// empty constructor
	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return "CForumMessage::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
		if ($this->message_id === NULL) {
			return 'message_id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return "CForumMessage::store-check failed<br />$msg";
		}
		$q  = new DBQuery;
		if( $this->message_id ) {
			// First we need to remove any forum visits for this message
			// otherwise nobody will see that it has changed.
			$q->setDelete('forum_visits');
			$q->addWhere('visit_message = '.$this->message_id);
			$q->exec(); // No error if this fails, it is not important.
			$ret = db_updateObject( 'forum_messages', $this, 'message_id', false ); // ! Don't update null values
			$q->clear();
		} else {
			$this->message_date = db_datetime( time() );
			$new_id = db_insertObject( 'forum_messages', $this, 'message_id' ); ## TODO handle error now
			echo db_error(); ## TODO handle error better

			$q->addTable('forum_messages');
			$q->addQuery('count(message_id), MAX(message_date)');
			$q->addWhere('message_forum = '.$this->message_forum);

			$res = $q->exec();
			echo db_error(); ## TODO handle error better
			$reply = db_fetch_row( $res );
			$q->clear();

			//update forum descriptor
			$forum = new CForum();
			$forum->forum_id = $this->message_forum;
			$forum->forum_message_count = $reply[0];
			$forum->forum_last_date = $reply[1];
			$forum->forum_last_id = $this->message_id;

			$forum->store(); ## TODO handle error now

			return $this->sendWatchMail( false );
		}

		if( !$ret ) {
			return "CForumMessage::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete() {
		$q  = new DBQuery;
		$q->setDelete('forum_visits');
		$q->addWhere('visit_message = '.$this->message_id);
		$q->exec(); // No error if this fails, it is not important.
		$q->clear();
		
		$q->setDelete('forum_messages');
		$q->addWhere('message_id = '.$this->message_id);
		if (!$q->exec()) {
			$result = db_error();
		} else {
			$result = NULL;
		}
		$q->clear();
		return $result;
	}

	function sendWatchMail( $debug=false ) {
		GLOBAL $AppUI, $debug, $dPconfig;
		$subj_prefix = $AppUI->_('forumEmailSubj', UI_OUTPUT_RAW);
		$body_msg = $AppUI->_('forumEmailBody', UI_OUTPUT_RAW);
		
		// Get the message from details.
		$q  = new DBQuery;
		$q->addTable('users', 'u');
		$q->addQuery('contact_email, contact_first_name, contact_last_name');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');
		$q->addWhere("user_id = '{$this->message_author}'");
		$res = $q->exec();
		if ($row = db_fetch_assoc($res)) {
		  $message_from = "$row[contact_first_name] $row[contact_last_name] <$row[contact_email]>";
		} else {
		  $message_from = "Unknown user";
		}
		// Get the forum name;
		$q->clear();
		$q->addTable('forums');
		$q->addQuery('forum_name');
		$q->addWhere("forum_id = '{$this->message_forum}'");
		$res = $q->exec();
		if ($row = db_fetch_assoc($res)) {
		  $forum_name = $row['forum_name'];
		} else {
		  $forum_name = 'Unknown';
		}

		// SQL-Query to check if the message should be delivered to all users (forced)
		// In positive case there will be a (0,0,0) row in the forum_watch table
		$q->clear();
		$q->addTable('forum_watch');
		$q->addQuery('*');
		$q->addWhere('watch_user = 0 AND watch_forum = 0 AND watch_topic = 0');
		$resAll = $q->exec();
		$AllCount = db_num_rows($resAll);

		$q->clear();
		$q->addTable('users');
		$q->addQuery('DISTINCT contact_email, user_id, contact_first_name, contact_last_name');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');

		if ($AllCount < 1)		//message is only delivered to users that checked the forum watch
		{	
			$q->addTable('forum_watch');
			$q->addWhere("user_id = watch_user
				AND (watch_forum = $this->message_forum OR watch_topic = $this->message_parent)");
		}

		if (!($res = $q->exec())) {
			$q->clear();
			return;
		}
		if (db_num_rows( $res ) < 1) {
			return;
		}

		$mail = new Mail;
		$mail->Subject( "$subj_prefix $this->message_title", isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "");

		$body = "$body_msg";

		$body .= "\n\n" . $AppUI->_('Forum', UI_OUTPUT_RAW) . ": $forum_name";
		$body .= "\n" . $AppUI->_('Subject', UI_OUTPUT_RAW) . ": {$this->message_title}";
		$body .= "\n" . $AppUI->_('Message From', UI_OUTPUT_RAW) . ": $message_from";
		$body .= "\n\n".DP_BASE_URL.'/index.php?m=forums&a=viewer&forum_id='.$this->message_forum;
		$body .= "\n\n$this->message_body";
 
		$mail->Body( $body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : ""  );
		$mail->From( $AppUI->_('forumEmailFrom', UI_OUTPUT_RAW) );

		while ($row = db_fetch_assoc( $res )) {
			if ($mail->ValidEmail( $row['contact_email'] )) {
				$mail->To( $row['contact_email'], true );
				$mail->Send();
			}
		}
		$q->clear();
		return;
	}
}
?>

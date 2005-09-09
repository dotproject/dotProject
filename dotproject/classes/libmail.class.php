<?php /* CLASSES $Id$ */
/**
 *	@package dotproject
 *	@subpackage utilites
*/

/**
 *	This class encapsulates the PHP mail() function.
 *
 *	implements CC, Bcc, Priority headers
 *	@version	1.3
 *	<ul>
 *	<li>added ReplyTo( $address ) method
 *	<li>added Receipt() method - to add a mail receipt
 *	<li>added optionnal charset parameter to Body() method. this should fix charset problem on some mail clients
 *	</ul>
 *	Example
 *	<code>
 *	include "libmail.php";
 *
 *	$m= new Mail; // create the mail
 *	$m->From( "leo@isp.com" );
 *	$m->To( "destination@somewhere.fr" );
 *	$m->Subject( "the subject of the mail" );
 *
 *	$message= "Hello world!\nthis is a test of the Mail class\nplease ignore\nThanks.";
 *	$m->Body( $message);	// set the body
 *	$m->Cc( "someone@somewhere.fr");
 *	$m->Bcc( "someoneelse@somewhere.fr");
 *	$m->Priority(4) ;	// set the priority to Low
 *	$m->Attach( "/home/leo/toto.gif", "image/gif" ) ;	// attach a file of type image/gif
 *	$m->Send();	// send the mail
 *	echo "the mail below has been sent:<br><pre>", $m->Get(), "</pre>";
 *	</code>

LASTMOD
	Fri Oct  6 15:46:12 UTC 2000

 *	@author	Leo West - lwest@free.fr
 */
class Mail
{
/**
 *	list of To addresses
 *	@var	array
 */
	var $sendto = array();
/**
 *	@var	array
*/
	var $acc = array();
/**
 *	@var	array
*/
	var $abcc = array();
/**
 *	paths of attached files
 *	@var array
*/
	var $aattach = array();
/**
 *	list of message headers
 *	@var array
	*/
	var $xheaders = array();
	/**
 *	message priorities referential
 *	@var array
	*/
	var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	/**
 *	character set of message
 *	@var string
	*/
	var $charset = "us-ascii";
	var $ctencoding = "7bit";
	var $receipt = 0;

	var $useRawAddress = true;

	var $host;
	var $port;
	var $sasl;
	var $username;
	var $password;
	var $transport;
	var $defer;

/**
 *	Mail constructor
*/
function Mail()
{
	global $dPconfig;

	$this->autoCheck( true );
	$this->boundary= "--" . md5( uniqid("myboundary") );
	// Grab the current mail handling options
	$this->transport = isset($dPconfig['mail_transport']) ? $dPconfig['mail_transport'] : 'php';
	$this->host = isset($dPconfig['mail_host']) ? $dPconfig['mail_host'] : 'localhost';
	$this->port = isset($dPconfig['mail_port']) ? $dPconfig['mail_port'] : '25';
	$this->sasl = isset($dPconfig['mail_auth']) ? $dPconfig['mail_auth'] : false;
	$this->username = @$dPconfig['mail_user'];
	$this->password = @$dPconfig['mail_pass'];
	$this->defer = @$dPconfig['mail_defer'];
	$this->timeout = isset($dPconfig['mail_timeout']) ? $dPconfig['mail_timeout'] : 0;
}


/**
 *	activate or desactivate the email addresses validator
 *
 *	ex: autoCheck( true ) turn the validator on
 *	by default autoCheck feature is on

 *	@param boolean	$bool set to true to turn on the auto validation
 *	@access public
*/
function autoCheck( $bool )
{
	if( $bool ) {
		$this->checkAddress = true;
	} else {
		$this->checkAddress = false;
	}
}


/**
 *	Define the subject line of the email
 *	@param string $subject any monoline string
 *	@param string $charset encoding to be used for Quoted-Printable encoding of the subject 
*/
function Subject( $subject, $charset='' ) {
	global $dPconfig;

	if( isset($charset) && $charset != "" ) {
		$this->charset = strtolower($charset);
	}
	
	global $AppUI;
	
	if ( ( $AppUI->user_locale != 'en' || ( $this->charset && $this->charset != 'us-ascii' && $this->charset != 'utf-8') ) && function_exists('imap_8bit')) {
		$subject = "=?".$this->charset."?Q?".
			str_replace("=\r\n","",imap_8bit($subject))."?=";		
	}
	$this->xheaders['Subject'] = $dPconfig['email_prefix'].' '.strtr( $subject, "\r\n" , "  " );
}


/**
 *	set the sender of the mail
 *	@param string $from should be an email address

*/

function From( $from ) {
	if( ! is_string($from) ) {
		echo "Class Mail: error, From is not a string";
		exit;
	}
	$this->xheaders['From'] = $from;
}

/**
 *	set the Reply-to header
 *	@param string $email should be an email address
*/
function ReplyTo( $address ) {
	if (!is_string($address)) {
		return false;
	}
	$this->xheaders["Reply-To"] = $address;
}

/**
 *	add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined)
 *	when the receiver opens the message.
 *	@warning this functionality is *not* a standard, thus only some mail clients are compliants.
*/

function Receipt() {
	$this->receipt = 1;
}

/**
 *	set the mail recipient
 *
 *	The optional reset parameter is useful when looping through records to send individual mails.
 *	This prevents the 'to' array being continually stacked with additional addresses.
 *
 *	@param string $to email address, accept both a single address or an array of addresses
 *	@param boolean $reset resets the current array
*/

function To( $to, $reset=false ) {

	// TODO : test validité sur to
	if( is_array( $to ) ) {
		$this->sendto = $to;
	} else {
		if ($this->useRawAddress) {
		   if( preg_match( "/^(.*)\<(.+)\>$/", $to, $regs ) ) {
			  $to = $regs[2];
		   }
		}
		if ($reset) {
			unset( $this->sendto );
			$this->sendto = array();
		}
		$this->sendto[] = $to;
	}

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->sendto );

}


/**
 *	Cc()
 *	set the CC headers ( carbon copy )
 *	$cc : email address(es), accept both array and string
 */

function Cc( $cc ) {
	if( is_array($cc) )
		$this->acc= $cc;
	else
		$this->acc= explode(',', $cc);

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->acc );

}

/**
 *	set the Bcc headers ( blank carbon copy ).
 *	$bcc : email address(es), accept both array and string
 */

function Bcc( $bcc ) {
	if( is_array($bcc) ) {
		$this->abcc = $bcc;
	} else {
		$this->abcc[]= $bcc;
	}

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->abcc );
}

/**
 *		set the body (message) of the mail
 *		define the charset if the message contains extended characters (accents)
 *		default to us-ascii
 *		$mail->Body( "mél en français avec des accents", "iso-8859-1" );
 */
function Body( $body, $charset="" ) {
	$this->body = $body;

	if( isset($charset) && $charset != "" ) {
		$this->charset = strtolower($charset);
		if( $this->charset != "us-ascii" )
			$this->ctencoding = "8bit";
	}
}

/**
 *		set the Organization header
 */
function Organization( $org ) {
	if( trim( $org != "" )  )
		$this->xheaders['Organization'] = $org;
}

/**
 *		set the mail priority
 *		$priority : integer taken between 1 (highest) and 5 ( lowest )
 *		ex: $mail->Priority(1) ; => Highest
 */

function Priority( $priority ) {
	if( ! intval( $priority ) )
		return false;

	if( ! isset( $this->priorities[$priority-1]) )
		return false;

	$this->xheaders["X-Priority"] = $this->priorities[$priority-1];

	return true;
}

/**
 *	Attach a file to the mail
 *
 *	@param string $filename : path of the file to attach
 *	@param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
 *	@param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 */
function Attach( $filename, $filetype = "", $disposition = "inline" ) {
	// TODO : si filetype="", alors chercher dans un tablo de MT connus / extension du fichier
	if( $filetype == "" )
		$filetype = "application/x-unknown-content-type";

	$this->aattach[] = $filename;
	$this->actype[] = $filetype;
	$this->adispo[] = $disposition;
}

/**
 *	Build the email message
 *	@access protected
*/
function BuildMail() {
// build the headers
	global $AppUI;

	$this->headers = "";
//	$this->xheaders['To'] = implode( ", ", $this->sendto );

	if( count($this->acc) > 0 ) {
		$this->xheaders['CC'] = implode( ", ", $this->acc );
	}
	if( count($this->abcc) > 0 ) {
		$this->xheaders['BCC'] = implode( ", ", $this->abcc );
	}

	if( $this->receipt ) {
		if( isset($this->xheaders["Reply-To"] ) ) {
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
		} else {
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
		}
	}

	if( $this->charset != "" ) {
		$this->xheaders["Mime-Version"] = "1.0";
		$this->xheaders["Content-Type"] = "text/plain; charset=$this->charset";
		$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
	}

	$this->xheaders["X-Mailer"] = "dotProject v" . $AppUI->getVersion();

	// include attached files
	if( count( $this->aattach ) > 0 ) {
		$this->_build_attachement();
	} else {
		$sep = "\r\n";
		$arr = preg_split("/(\r?\n)|\r/", $this->body);
		$this->fullBody = implode($sep, $arr);
	}

	reset($this->xheaders);
	while( list( $hdr,$value ) = each( $this->xheaders )  ) {
		if( $hdr != "Subject" )
			$this->headers .= "$hdr: $value\r\n";
	}
}

/**
 *	format and send the mail
 *	@access public
*/
function Send() {
	$this->BuildMail();

	$this->strTo = implode( ", ", $this->sendto );

	if ($this->defer)
		return $this->QueueMail();
	else if ($this->transport == 'smtp')
		return $this->SMTPSend( $this->sendto, $this->xheaders['Subject'], $this->fullBody, $this->xheaders );
	else
		return @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );
}

/**
 * Send email via an SMTP connection.
 *
 * Work based loosly on that of Bugs Genie, which appears to be in turn based on something from 'Ninebirds'
 *
 * @access public
 */
function SMTPSend($to, $subject, $body, &$headers) {
	global $AppUI, $dPconfig;

	// Start the connection to the server
	$error_number = 0;
	$error_message = '';
	$this->socket = fsockopen($this->host, $this->port, $error_number, $error_message, $this->timeout);
	if (! $this->socket) {
		dprint(__FILE__, __LINE__, 1, "Error on connecting to host {$this->host} at port {$this->port}: $error_message ($error_number)");
		$AppUI->setMsg("Cannot connect to SMTP Host: $error_message ($error_number)");
		return false;
	}
	// Read the opening stuff;
	$this->socketRead();
	// Send the protocol start
	$this->socketSend("HELO " . $this->getHostName());
	if ($this->sasl && $this->username) {
		$this->socketSend("AUTH LOGIN");
		$this->socketSend(base64_encode($this->username));
		$rcv = $this->socketSend(base64_encode($this->password));
		if (strpos($rcv, '235') !== 0) {
			dprint(__FILE__, __LINE__, 1, "Authentication failed on server: $rcv");
			$AppUI->setMsg("Failed to login to SMTP server: $rcv");
			fclose($this->socket);
			return false;
		}
	}
	// Determine the mail from address.
	if ( ! isset($headers['From'])) {
		$from = $dPconfig['admin_user'] . '@' . $dPconfig['site_domain'];
	} else {
		// Search for the parts of the email address
		if (preg_match('/.*<([^@]+@[a-z0-9\._-]+)>/i', $headers['From'], $matches))
			$from = $matches[1];
		else
			$from = $headers['From'];
	}
	$rcv = $this->socketSend("MAIL FROM: <$from>");
	if (substr($rcv,0,1) != '2') {
		$AppUI->setMsg("Failed to send email: $rcv", UI_MSG_ERROR);
		return false;
	}
	foreach ($to as $to_address) {
		if (strpos($to_address, '<') !== false) {
			preg_match('/^.*<([^@]+\@[a-z0-9\._-]+)>/i', $to_address, $matches);
			if (isset($matches[1]))
				$to_address = $matches[1];
		}
		$rcv = $this->socketSend("RCPT TO: <$to_address>");
		if (substr($rcv,0,1) != '2') {
			$AppUI->setMsg("Failed to send email: $rcv", UI_MSG_ERROR);
			return false;
		}
	}
	$this->socketSend("DATA");
	foreach ($headers as $hdr =>$val) {
		$this->socketSend("$hdr: $val", false);
	}
	// Now build the To Headers as well.
	$this->socketSend("To: " . implode(', ', $to), false);
	$this->socketSend("Date: " . date('r'), false);
	$this->socketSend("", false);
	$this->socketSend($body, false);
	$result = $this->socketSend(".\r\nQUIT");
	if (strpos($result, '250') === 0)
		return true;
	else {
		dprint(__FILE__, __LINE__, 1, "Failed to send email from $from to $to_address: $result");
		$AppUI->setMsg("Failed to send email: $result");
		return false;
	}
}

function socketRead()
{
	$result = fgets($this->socket, 4096);
	dprint(__FILE__, __LINE__, 12, "server said: $result");
	return $result;
}

function socketSend($msg, $rcv = true)
{
	dprint(__FILE__, __LINE__, 12, "sending: $msg");
	$sent = fputs($this->socket, $msg . "\r\n");
	if ($rcv)
		return $this->socketRead();
	else
		return $sent;
}

function getHostName()
{
  // Grab the server address, return a hostname for it.
  if ($host = gethostbyname($_SERVER['SERVER_ADDR']))
    return $host;
  else
    return '[' . $_SERVER['SERVER_ADDR'] . ']';
}

/**
 * Queue mail to allow the queue manager to trigger
 * the email transfer.
 *
 * @access private
 */
function QueueMail() {
	global $AppUI;

	require_once $AppUI->getSystemClass('event_queue');
	$ec = new EventQueue;
	$vars = get_object_vars($this);
	return $ec->add(array('Mail', 'SendQueuedMail'), $vars, 'libmail', true);
}

/**
 * Dequeue the email and transfer it.  Called from the queue manager.
 *
 * @access private
 */
function SendQueuedMail($mod, $type, $originator, $owner, &$args) {
	extract($args);
	if ($this->transport == 'smtp') {
		return $this->SMTPSend($sendto, $xheaders['Subject'], $fullBody, $xheaders);
	} else {
		return @mail( $strTo, $xheaders['Subject'], $fullBody, $headers );
	}
}

/**
 *	Returns the whole e-mail , headers + message
 *
 *	can be used for displaying the message in plain text or logging it
 *
 *	@return string
 */
function Get() {
	$this->BuildMail();
	$mail = "To: " . $this->strTo . "\r\n";
	$mail .= $this->headers . "\r\n";
	$mail .= $this->fullBody;
	return $mail;
}

/**
 *	check an email address validity
 *	@access public
 *	@param string $address : email address to check
 *	@return true if email adress is ok
 */
function ValidEmail($address) {
   if( preg_match( "/^(.*)\<(.+)\>$/", $address, $regs ) ) {
      $address = $regs[2];
   }
   if( preg_match( "/^[^@ ]+@([a-zA-Z0-9\-.]+)$/",$address) ) {
      return true;
   } else {
      return false;
   }
}

/**
 *	check validity of email addresses
 *	@param	array $aad -
 *	@return if unvalid, output an error message and exit, this may -should- be customized
 */

function CheckAdresses( $aad ) {
	for($i=0;$i< count( $aad); $i++ ) {
		if( ! $this->ValidEmail( $aad[$i]) ) {
			echo "Class Mail, method Mail : invalid address $aad[$i]";
			exit;
		}
	}
}

/**
 *	check and encode attach file(s) . internal use only
 *	@access private
*/
function _build_attachement() {
	$this->xheaders["Content-Type"] = "multipart/mixed;\r\n boundary=\"$this->boundary\"";

	$this->fullBody = "This is a multi-part message in MIME format.\r\n--$this->boundary\r\n";
	$this->fullBody .= "Content-Type: text/plain; charset=$this->charset\r\nContent-Transfer-Encoding: $this->ctencoding\r\n\r\n";

	$sep= "\r\n";
	$body = preg_split("/\r?\n/", $this->body);
	$this->fullBody .= implode($sep, $body) ."\r\n";

	$ata= array();
	$k=0;

	// for each attached file, do...
	for( $i=0; $i < count( $this->aattach); $i++ ) {
		$filename = $this->aattach[$i];
		$basename = basename($filename);
		$ctype = $this->actype[$i];	// content-type
		$disposition = $this->adispo[$i];

		if( ! file_exists( $filename) ) {
			echo "Class Mail, method attach : file $filename can't be found"; exit;
		}
		$subhdr= "--$this->boundary\r\nContent-type: $ctype;\r\n name=\"$basename\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: $disposition;\r\n  filename=\"$basename\"\r\n";
		$ata[$k++] = $subhdr;
		// non encoded line length
		$linesz= filesize( $filename)+1;
		$fp= fopen( $filename, 'r' );
		$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
		fclose($fp);
	}
	$this->fullBody .= implode($sep, $ata);
}

} // class Mail


?>

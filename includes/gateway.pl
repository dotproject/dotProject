#!c:\programme\perl\bin\perl.exe -w
# You may have to edit the above line to reflect your system
# E.g. the typical UNIX/Linux system will require #!/usr/bin/perl

# $Id$ #

# send email report upon receipt (1 = yes, 0 = no)
$send_email_report = 1;

# Send aknowlegment back to lodger (1 = yes, 0 = no)
$send_acknowledge = 1;

# Save attachments as files in project 0 (1 = yes, 0 = no, just mark them as removed)
$save_attachments = 0;

# Skip non-MIME component of MIME emails (usually a warning about non-MIME compliant readers)
# Deprecated - leave at 0 unless you know what you are doing!
$skip_mime_preface = 0; 

# NOTE:  Email addresses should escape the @ symbol as it is
# a PERL array identifier and will cause this script to break.
# Alternatively change the double quotes to single quotes, which
# also escapes the string.

# NOTE 2: If your dotProject PHP environment is correctly set up
# you don't need to add the @ and domain, it will get it from
# dPconfig[site_domain] key.

# address to send report to
$report_to_address = 'admin';

# report from address
$report_from_address = 'support';

# location of sendmail
$mailprog = "/usr/sbin/sendmail";

# location of mimencode, some systems call this mmencode
$mime_encoder = "/usr/bin/mimencode";

# debugging - if set it will report what it finds, but will not add anything
# to the database
$debug = 0;

######################## </CONFIGURATION SECTION> ##############################

## First phase, check to see we can configure ourselves based upon
## the PHP environment.
die ("Gateway.pl requires the full path to the dotproject config.php file as its only argument") if ($#ARGV != 0);
%config = ();
&check_config($ARGV[0]);

# Shortcuts for the email code
$app_root = $config{'base_url'};
$dp_root = $config{'root_dir'};

# Check that the relevant files exist
@sendmail_st = stat($mailprog);
if (! @sendmail_st) {
	if ( $send_email_report || $send_acknowledge ) {
		die("You have requested email functions, but your mailer does not exist");
	} else {
		print "No mailer defined, or mailer not found - will not be able to email error reports\n";
		print "Continuing anyway\n";
	}
}

@mmstat = stat($mime_encoder);
if (! @mmstat) {
	if ($save_attachments) {
		print "You have requested to save attachments, but the mime encoder could not be found\n";
		print "Continuing, but not saving attachments\n";
		$save_attachments = 0;
	}
}

# If no domain portion, add the domain from the configuration file.
if ($report_to_address !~ /\@/) {
	$report_to_address .= '@' . $config{'site_domain'};
}
if ($report_from_address !~ /\@/) {
	$report_from_address .= '@' . $config{'site_domain'};
}

# database bindings
use DBI;

# read in message
while (<STDIN>) {
	push @message, $_;
}

# main program
&get_headers();
$attach_count = 0;
$mime_alternative = 0;
&check_attachments($attachment_info, $first_message_line, $#message);
&get_body();
&insert_message();
&insert_attachments() if ($save_attachments);
&mail_report() if ($send_email_report);
&mail_acknowledgement() if ($send_acknowledge);

exit();

################################################################################

sub check_config() {
	$dp_conf = $_[0];
	open (PHPCONFIG, "<$dp_conf") or die ("Cannot find dotProject configuration file!");
	while (<PHPCONFIG>) {
		if (/^\s*\$dpconfig\[/i) {
			s/\s*;.*$//;
			# Now split the conf line up.
			@confs = split /\s*=\s*/;
			# First part is the name
			$confs[0] =~ s/^.*\[['"](.*)['"]\]/$1/;
			$confs[1] =~ s/['"\r\n]//g;
			# add to the config array
			$config{$confs[0]} = $confs[1];
		}
	}
}

sub get_headers {

	# read in headers
	# First pass, fix up split headers.
	$first_message_line = 0;
	foreach (@message) {
		last if (/^\s$/ || /^$/);
		if (/^[\s\t]+/) {
			$last_hdr = pop @headers;
			$last_hdr =~ s/[\s\t]*$//;
			s/[\s\t]*//;
			$last_hdr .= " " . $_;
			push @headers, $last_hdr;
		} else {
			push @headers, $_;
		}
		$first_message_line++;
	}
	# Second pass, split out the required headers
	$attachment = 0;
	foreach (@headers) {
		if (/content-type:\s+multipart/i) {
			$attachment_info = $_;
			if ($save_attachments) {
				$attachment = 2;
			} else {
				$attachment = 1;
			}
		}
		$_ =~ s/:\s/:/g;
		if (/:/) {
			@vars = split(':', $_, 2);
			if (@vars) {
				chop($header{$vars[0]} = $vars[1]);
			}
		}
	}
	
	# strip out Re:'s in subject
	$header{'Subject'} =~ s/\s*Re:\s*//gi;
	
	# put a nice Re: back in
	$header{'Subject'} =~ s/(\[\#\d+\])(.*)/$1 Re: $2/;
	
	# initialize Cc: header
	$header{'Cc'} = "" if (!$header{'Cc'});
	
	# Allow the use of Reply-To to insert tickets on behalf of another
	if ($header{'Reply-To'}) {
		$header{'From'} = $header{'Reply-To'};
	}
	
	# fix quoting in email headers
	$header{'From'} =~ s/"/\"/g;
	$header{'Cc'} =~ s/"/\"/g;
	
	# determine ticket number
	$parent = $header{'Subject'};
	if ($parent =~ /\[\#(\d+)\]/) {
		$parent =~ s/.*\[\#(\d+)\].*/$1/;
		$ticket = $parent;
	}
	else {
		$parent = 0;
	}
	
	if ($debug) {
		print "parent=$parent\n";
		print "attachments=$attachment\n";
		print "\nHeaders:\n";
		while (($key, $val) = each(%header)) {
			print "$key: $val\n";
		}
	}
}

sub mail_error() {
	my $msg = $_[0];
	open(MAIL, "|$mailprog -t");
	print MAIL "From: $report_from_address\n";
	print MAIL "To: $report_to_address\n";
	print MAIL "Subject: Error in processing ticket mail\n\n";
	print MAIL "An error occurred in processing a ticket mail.\n";
	print MAIL "The error message was:\n";
	print MAIL "$msg\n";
	print MAIL "\nMessage Headers:\n";
	while (($key, $val) = each(%header)) {
		print MAIL "   $key: $val\n";
	}
	close(MAIL);
	die($msg);
}

################################################################################

sub check_attachments($) {
	
	my $att = $_[0];
	my $offset = $_[1];
	my $end = $_[2];
	my $ctype = "";
	my $boundary = "";
	my $subtype = "";
	my %option = ();
	my $i;
	
	# check for attachment
	return if (!$att);
	
	# determine attachment delimiter
	($ctype, $subtype, $options) = ($att =~ m/content-type:\s*([_a-z0-9]+)\/([_a-z0-9]+);?\s(.*)$/i);
	
	# split the options out
	while ($options =~ m/([_a-z0-9]+)=["']?([^;"']+)["';]?/g) {
		$name = $1;
		$name =~ tr/A-Z/a-z/;
		$option{$name} = $2;
		if ($debug) {
			print "option[$name] = $2\n";
		}
	}
	$boundary = $option{'boundary'};
	if ($debug) {
		print "\nAttachment Info\n";
		print "Original MIME content header is $att\n";
		print "Content type is $ctype\n";
		print "Subtype is $subtype\n";
		print "Boundary is $boundary\n";
		print "Option list is $options\n";
		print "Checking from $offset to $end\n";
	}

	# The subtype should let us know if we are 
	return if (!$boundary);
	if ($subtype =~ /alternative/i) {
		$mime_alternative = 1;
	}
	# pull out attachments
	my $in_attach_hdrs = 0;
	for ($i = $offset; $i <= $end; $i++) {
		if ($message[$i] =~ /--$boundary/) {
			if ($debug) {
			print "$attach_count attachment boundary $boundary found at line $i\n";
			}
			$in_attach_hdrs = 1;
			$boundary_lines[$attach_count] = $i;
			$attach_disposition[$attach_count] = "";
			$attach_type[$attach_count] = "text/plain";
			$attach_encoding[$attach_count] = "7bit";
			$attach_realname[$attach_count] = "";
			$attach_content_header[$attach_count] = "content-type: text/plain";
			if ($attach_count > 0 && ! $boundary_end[$attach_count]) {
				$boundary_end[$attach_count] = $i-1;
			}
			$attach_count += 1;
		} else {
			if ($in_attach_hdrs) {
				if ($message[$i] =~ /^\s*$/) {
					$boundary_lines[$attach_count-1] = $i;
					# push @boundary_end, $last;
					$in_attach_hdrs = 0;
				} else {
					#  In the header section, find the details
					@attach_hdr = split(/[:;]/, $message[$i]);
					if ($attach_hdr[0] =~ m/content-disposition/i) {
						$attach_disposition[$attach_count-1] = $attach_hdr[1];
					}
					if ($attach_hdr[0] =~ m/content-type/i) {
						$attach_type[$attach_count-1] = $attach_hdr[1];
						$attach_content_header[$attach_count-1] = $message[$i];
					}
					if ($attach_hdr[0] =~ m/boundary/i) {
						$attach_content_header[$attach_count-1] .= "; " . $attach_hdr[0];
					}
					if ($attach_hdr[0] =~ m/content-transfer-encoding/i) {
						$attach_encoding[$attach_count-1] = $attach_hdr[1];
					}
					if ($message[$i] =~ m/name=/i) {
						($x, $f) = split(/"/, $message[$i]);
						$x = "";
						$attach_realname[$attach_count-1] = $f;
					}
				}
			}
		}
	}
	$boundary_end[$attach_count] = $end;
	# push @boundary_end, $end;
}

################################################################################

sub get_body {
	
	my $i;
	my $body_lines = 0;
	
	if ($debug) {
		print "Attachcount=$attach_count\n";
	}
	# read in message body
	if (!$attachment_info) {
		for ($i = $first_message_line + 1; $i <= $#message; $i++) {
			$body .= $message[$i];
			$body_lines += 1;
		}
	}
	else {
		# Check that the attachments are not in themselves multipart
		for ($i = 0; $i < $attach_count; $i++) {
			if ($attach_type[$i] =~ /multipart\//i) {
				&check_attachments($attach_content_header[$i], $boundary_lines[$i], $boundary_end[$i+1]);
			}
		}
		#$boundary_end[$attach_count] = $#message;
		# Look for the attachment that doesn't have a disposition
		if ($skip_mime_preface) {
			$i = 1;
		} else {
			$i = 0;
		}
		for (; $i < $attach_count; $i++) {
			if ($debug) {
				print "$i: mimealt=$mime_alternative, type=$attach_type[$i], disp=$attach_disposition[$i] name=$attach_realname[$i] start=$boundary_lines[$i], end=$boundary_end[$i+1]\n";
			}
			if (($mime_alternative == 1 && $attach_type[$i] =~ /text\/plain/i) || ($mime_alternative == 0 && $attach_type[$i] =~ /text\//i && $attach_disposition[$i] =~ /^$/ )) {
				if ($debug) {
					print "Found suitable body text in attachment $i\n";
				}
				for ($j = $boundary_lines[$i] + 1; $j < $boundary_end[$i+1]; $j++) {
					$body .= $message[$j];
					$body_lines += 1;
				}
				# Fix for RFC2046 compliance.
				if (($i+1) < $attach_count && $message[$j] !~ /^\s*$/) {
					$body .= $message[$j];
					$body_lines += 1;
				}
			}
		}
	}
	if (! $body_lines) {
		&mail_error("No suituable body text found in email");
	}
	$body =~ s/^\n//;
	$body =~ s/\r\n$/\n/;
	if ($debug) {
		print "\nBody:\n";
		print $body;
	}
}

################################################################################

sub insert_message {
	
	if ($debug) {
		print "insert_message not run, parent = $parent\n";
		print "author=" . $header{'From'} . "\n";
		print "subject=" . $header{'Subject'} . "\n";
		print "cc=" . $header{'Cc'} . "\n";
		$author = $header{'From'};
		$subject = $header{'Subject'};
		$cc = $header{'Cc'};
		return;
	}
	# connect to database
	$dbh = DBI->connect("DBI:mysql:$config{'dbname'}:$config{'dbhost'}", $config{'dbuser'}, $config{'dbpass'});

	# update parent activity
	if ($parent) {
		$activity_query = "UPDATE tickets SET type = 'Open', activity = UNIX_TIMESTAMP() WHERE ticket = '$parent'";
		$sth = $dbh->prepare($activity_query);
		$sth->execute();
		$sth->finish();
		$type = "Client Followup";
		$assignment = "9999";
	}
	else {
		$type = "Open";
		$assignment = "0";
	}

	# quote all fields
	$db_parent = $dbh->quote($parent);
	$attachment = $dbh->quote($attachment);
	$author = $dbh->quote($header{'From'});
	$subject = $dbh->quote($header{'Subject'});
	$body = $dbh->quote($body);
	$type = $dbh->quote($type);
	$cc = $dbh->quote($header{'Cc'});
	$assignment = $dbh->quote($assignment);

	# do insertion
	$insert_query = "INSERT INTO tickets (parent, attachment, timestamp, author, subject, body, type, cc, assignment) ";
	$insert_query .= "VALUES ($db_parent, $attachment, UNIX_TIMESTAMP(), $author, $subject, $body, $type, $cc, $assignment)";
	$sth = $dbh->prepare($insert_query);
	$sth->execute();
	if (not $parent) {
	   $ticket = $sth->{'mysql_insertid'};
	}
	$sth->finish();
	$dbh->disconnect();
	
}

sub insert_attachments {
	return if (!$attachment_info);
	
	if (!$debug) {
	  $dbh = DBI->connect("DBI:mysql:$config{'dbname'}:$config{'dbhost'}", $config{'dbuser'}, $config{'dbpass'});
	}
	if ($skip_mime_preface) {
		$i = 1;
	} else {
		$i = 0;
	}
	for ($i = 0; $i < $attach_count; $i++) {
		if (($mime_alternative == 0 && $attach_disposition[$i] !~ /^$/) || ($mime_alternative == 1 && $attach_type[$i] !~ /text\/plain/i && $attach_type[$i] !~ /multipart/i) ) {
			if ($debug) {
			  insert_attachment($i, 0);
			} else {
			  insert_attachment($i, $dbh);
			}
		}
	}
	if (! $debug) {
		$dbh->disconnect();
	}
}

sub insert_attachment($) {
	
	$att = $_[0];
	$dbh = $_[1];
	
	if ($debug) {
		print "insert_attachment called with att=$att\n";
	}
	
	# Check that we can write to the required directory and that we know who the
	# web owner is.
	if (! $debug) {
	$files_dir = $dp_root . "/files";
	$file_repository = $files_dir . "/0";
	
	@st = stat $files_dir or die ("Cannot find file repository");
	$web_owner = $st[4];
	
	# If the repository doesn't exist, create it.
	if (! stat $file_repository) {
		mkdir $file_repository, 0777;
		# If a umask is set, the mkdir will not correctly set
		# the modes on the file repository.
		chmod 0777, $file_repository;
	}
	
	# Extract the file using mimencode if necessary.
	$fid = sprintf("%x_%d", time(), $att);
	# If content encoding is not 7bit, try and determine what it is
	$fname = $file_repository . "/" . $fid;
	$freal = ">";
	$freal = "| " . $mime_encoder . " -u -o " if ($attach_encoding[$att] =~ m/base64/i);
	$freal = "| " . $mime_encoder . " -u -q -o " if ($attach_encoding[$att] =~ m/quoted/i);
	$fout = $freal . $fname;
	open(FH, $fout) or &mail_error("Attached file " . $attach_realname[$att] . " could not be saved!\nThis was probably due to a system error in running the command:\n\t'" . $fout . "'" );
	}
	for ($j = $boundary_lines[$att] + 1; $j < $boundary_end[$att+1]; $j++) {
		if ($debug) {
		  print $message[$j];
		} else {
		  print FH $message[$j];
		}
	}
	if ($debug) {
		return;
	} else {
		close(FH);
	}
	
	# Determine the files size
	open(FH, $fname) or &mail_error("File " . $attach_realname[$att] . " was not created correctly\nThis may be due to permissions errors");
	seek FH, 0, 2;
	$filesize = tell FH;
	close(FH);
	if ($filesize <= 0) {
		&mail_error("Attached file " . $attach_realname[$att] . " has length " . $filesize );
	}
	
	# Change ownership to the web server owner - assumes the files directory is correctly owned
	chown  $fname, $web_owner or chmod 0666, $fname;
	
	# Grab last file version id, and update it.
	$sql_stmt = "SELECT file_version_id FROM files ORDER BY file_version_id DESC LIMIT 1";
	@file_version = $dbh->selectrow_array($sql_stmt) or @file_version = (0);
	
	$file_version_id = $file_version[0] + 1;
	
	
	# insert the file as user Admin (id=1), Project = 0
	$sql_stmt = "INSERT into files (file_real_filename, file_name, file_type, file_size, file_date, file_description, file_task, file_version, file_version_id)  values (";
	$sql_stmt .= " '" . $fid . "',";
	$sql_stmt .= " '" . $attach_realname[$att] . "',";
	$sql_stmt .= " '" . $attach_type[$att] . "', ";
	$sql_stmt .= sprintf("%d", $filesize);
	$sql_stmt .= ", NOW() , ";
	$desc = "File attachment from: " . $header{'From'} . "\nTicket #" . $ticket . "\nSubject: " . $header{'Subject'};
	$sql_stmt .= $dbh->quote($desc);
	$sql_stmt .= ", ";
	$sql_stmt .= $ticket;
	$sql_stmt .= ", '1', '";
	$sql_stmt .= $file_version_id;
	$sql_stmt .= "' )";
	$sth = $dbh->prepare($sql_stmt);
	$sth->execute() or &mail_error("Failed to insert message in database - error was:\n" . $sth->errstr);
	$sth->finish();
}


################################################################################

sub mail_report {
	
	# unquote necessary fields
	$author =~ s/^\'(.*)\'$/$1/;
	$author =~ s/\\\'/'/g;
	$subject =~ s/^\'(.*)\'$/$1/;
	$subject =~ s/\\\'/'/g;
	
	# try to strip off \r
	$author =~ s/\\r//g;
	$subject =~ s/\\r//g;
	
	# remove ticket number
	$subject =~ s/\[\#\d+\](.*)/$1/;
	$boundary = "_lkqwkASDHASK89271893712893"; 
	
	# check for possible mail loops
	if ( $report_to_address eq $report_from_address || $author eq $report_from_address ) {
		print("Mail loop detected, not sending report\n");
		return;
	}
	# mail the report
	if ($debug) {
		print "\nReport Mail:\n";
		return;
		# open(MAIL, "|cat");
	} else {
		open(MAIL, "|$mailprog -t");
	}
	print MAIL "To: $report_to_address\n";
	print MAIL "From: $report_from_address\n";
	if ($parent) {
		print MAIL "Subject: Client followup to trouble ticket #$ticket\n";
	} else {
		print MAIL "Subject: New support ticket #$ticket\n";
	}
	print MAIL "Content-type: multipart/alternative; boundary=\"$boundary\"\n";
	print MAIL "Mime-Version: 1.0\n\n";
	print MAIL "--$boundary\n";
	print MAIL "Content-disposition: inline\n";
	print MAIL "Content-type: text/plain\n\n";
	if ($parent) {
		print MAIL "Followup Trouble ticket to ticket #$ticket\n\n";
	} else {
		print MAIL "New Trouble Ticket\n\n";
	}
	print MAIL "Ticket ID: $ticket\n";
	print MAIL "Author   : $author\n";
	print MAIL "Subject  : $subject\n";
	print MAIL "View     : $app_root/index.php?m=ticketsmith&amp;a=view&amp;ticket=$ticket\n";
	print MAIL "\n--$boundary\n";
	print MAIL "Content-disposition: inline\n";
	print MAIL "Content-type: text/html\n\n";
	print MAIL "<html>\n";
	print MAIL "<head>\n";
	print MAIL "<style>\n";
	print MAIL ".title {\n";
	print MAIL "    FONT-SIZE: 18pt; SIZE: 18pt;\n";
	print MAIL "}\n";
	print MAIL ".td {\n";
	print MAIL "    font: 9pt arial, san-serif;\n";
	print MAIL "}\n";
	print MAIL "</style>\n";
	if ($parent) {
		print MAIL "<title>Followup Trouble ticket to ticket #$ticket</title>\n";
	} else {
		print MAIL "<title>New Trouble ticket</title>\n";
	}
	print MAIL "</head>\n";
	print MAIL "<body>\n";
	print MAIL "\n";
	print MAIL "<table \"border=0\" cellpadding=\"4\" cellspacing=\"1\">\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td nowrap><span class=\"title\">Trouble Ticket Management</span></td>\n";
	print MAIL "        <td valign=\"top\" align=\"right\" width=\"100%\">&nbsp;</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "</table>\n";
	print MAIL "<table width=\"600\" border=\"0\" cellpadding=\"4\" cellspacing=\"1\" bgcolor=\"#878676\">\n";
	print MAIL "    <tr>\n";
	if ($parent) {
		print MAIL "        <td colspan=\"2\"><font face=\"arial,san-serif\" size=\"2\" color=\"white\">Followup Ticket Entered</font></td>\n";
	} else {
		print MAIL "        <td colspan=\"2\"><font face=\"arial,san-serif\" size=\"2\" color=\"white\">New Ticket Entered</font></td>\n";
	}
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">Ticket ID:</td>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">$ticket</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">Author:</td>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">$author</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">Subject:</td>\n";
	print MAIL "        <td bgcolor=\"white\"><font face=\"arial,san-serif\" size=\"2\">$subject</font></td>";
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">View:</td>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\"><a href=\"$app_root/index.php?m=ticketsmith&amp;a=view&amp;ticket=$ticket\">$app_root/index.php?m=ticketsmith&amp;a=view&amp;ticket=$ticket</a></td>\n";
	print MAIL "    </tr>\n";
	print MAIL "</table>\n";
	print MAIL "</body>\n";
	print MAIL "</html>\n";
	print MAIL "\n--$boundary--\n";
	close(MAIL);
	
}

################################################################################

sub mail_acknowledgement {
	
	# unquote necessary fields
	$author =~ s/^\'(.*)\'$/$1/;
	$author =~ s/\\\'/'/g;
	$subject =~ s/^\'(.*)\'$/$1/;
	$subject =~ s/\\\'/'/g;
	
	# remove ticket number
	$subject =~ s/\[\#\d+\](.*)/$1/;
	$boundary = "_lkqwkASDHASK89271893712893"; 
	
	# Check for mail loops.
	if ( $author eq $report_to_address || $author eq $report_from_address) {
		print("Detected mail loop, not sending acknowledgment\n");
		return;
	}
	
	# mail the report
	if ($debug) {
		print "\nAcknowledge Mail:\n";
		return;
		# open(MAIL, "|cat");
	}
	else {
		open(MAIL, "|$mailprog -t");
	}
	print MAIL "To: $author\n";
	print MAIL "From: $report_from_address\n";
	if ($parent) {
		print MAIL "Subject: [#$ticket] Response to Ticket $ticket received\n";
	}
	else {
		print MAIL "Subject: [#$ticket] Your Support Request\n";
	}
	print MAIL "Content-type: multipart/alternative; boundary=\"$boundary\"\n";
	print MAIL "Mime-Version: 1.0\n\n";
	print MAIL "--$boundary\n";
	print MAIL "Content-disposition: inline\n";
	print MAIL "Content-type: text/plain\n\n";
	if ($parent) {
		print MAIL "This is an acknowledgement that your response to\n";
		print MAIL "Ticket ID $ticket has been received\n";
	}
	else {
		print MAIL "This is an acknowledgement that your support request has been logged\n";
		print MAIL "by an automated support tracking system. It will be assigned to a\n";
		print MAIL "support representative who will be in touch in due course.\n\n";
	}
	print MAIL "Details of support request:\n";
	print MAIL "Ticket ID: $ticket\n";
	print MAIL "Author   : $author\n";
	print MAIL "Subject  : $subject\n";
	print MAIL "\n--$boundary\n";
	print MAIL "Content-disposition: inline\n";
	print MAIL "Content-type: text/html\n\n";
	print MAIL "<html>\n";
	print MAIL "<head>\n";
	print MAIL "<style>\n";
	print MAIL ".title {\n";
	print MAIL "    font-size: 18pt; size: 18pt;\n";
	print MAIL "}\n";
	print MAIL ".td {\n";
	print MAIL "	font: 9pt arial, san-serif;\n";
	print MAIL "}\n";
	print MAIL "</style>\n";
	print MAIL "<title>Your Support Request</title>\n";
	print MAIL "</head>\n";
	print MAIL "<body>\n";
	print MAIL "\n";
	print MAIL "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\">\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td nowrap><span class=\"title\">Trouble Ticket Management</span></td>\n";
	print MAIL "        <td valign=\"top\" align=\"right\" width=\"100%\">&nbsp;</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "</table>\n";
	print MAIL "<table width=\"600\" border=\"0\" cellpadding=\"4\" cellspacing=\"1\" bgcolor=\"#878676\">\n";
	print MAIL "    <tr>\n";
	if ($parent) {
		print MAIL "        <td colspan=\"2\"><font face=\"arial,san-serif\" size=\"2\" color=\"white\">Response received</font></td>\n";
	}
	else {
		print MAIL "        <td colspan=\"2\"><font face=\"arial,san-serif\" size=\"2\" color=\"white\">New Ticket Entered</font></td>\n";
	}
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">Ticket ID:</td>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">$ticket</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">Author:</td>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">$author</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">Subject:</td>\n";
	print MAIL "        <td bgcolor=\"white\" class=\"td\">$subject</td>\n";
	print MAIL "    </tr>\n";
	print MAIL "    <tr>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">&nbsp;</td>\n";
	print MAIL "        <td bgcolor=\"white\" nowrap class=\"td\">\n";
	if ($parent) {
		print MAIL "This is an acknowledgement that your response to\n";
		print MAIL "Ticket ID $ticket has been received\n";
	}
	else {
		print MAIL "This is an acknowledgement that your support request has been logged<br />\n";
		print MAIL "by an automated support tracking system. It will be assigned to a<br />\n";
		print MAIL "support representative who will be in touch in due course.\n";
	}
	print MAIL "            </font></td>\n";
	print MAIL "	</tr>\n";
	print MAIL "</table>\n";
	print MAIL "</body>\n";
	print MAIL "</html>\n";
	print MAIL "\n--$boundary--\n";
	close(MAIL);
	
}



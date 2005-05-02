<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'syslog');

function dplink_user_main()
{
	$url = trim(pnModGetVar('dplink', 'url'));
	$window = pnModGetVar('dplink', 'use_window');
	$wrap = pnModGetVar('dplink', 'use_postwrap');

	$user_data = array();
	$home = pnGetBaseURL();
  $home .= "user.php?op=loginscreen&module=NS-User";
	if (!pnUserLoggedIn()) {
		pnRedirect($home);
	}
	// We need to get the user password string from the database
	$uid = pnUserGetVar('uid');
	list($dbconn) = pnDBGetConn();
	$pntables = pnDBGetTables();
	$usertable = $pntables['users'];
	$usercol =& $pntables['users_column'];
	$sql = "SELECT $usercol[uname], 
	  $usercol[pass],
		$usercol[name],
		$usercol[email]
	  FROM $usertable
		WHERE $usercol[uid] = $uid";
	$result = $dbconn->Execute($sql);
	if ($dbconn->ErrorNo() != 0)
		die("Could not get user details");
	if ($result->EOF)
		die("Could not get user detail");
	list($uname, $password, $user_name, $user_email) = $result->fields;
	$result->Close();
	$user_data['login'] = $uname;
	$user_data['passwd'] = $password;
	$user_data['name'] = $user_name;
	$user_data['email'] = $user_email;
	$parm = serialize($user_data);
	$check = md5($parm);
	$cparm = gzcompress($parm);
	$bparm = urlencode(base64_encode($cparm));
	if ( $window ) {
		$url .= "/index.php?login=pn&userdata=$bparm&check=$check";
		header("Location: $url");
	} else {
		$url .= "/index.php?login=pn%26userdata=$bparm%26check=$check";
		if ($wrap) {
			header("Location: modules.php?op=modload&name=PostWrap&file=index&page=$url");
		} else {
			header("Location: modules.php?op=modload&name=dplink&file=index&url=$url");
		}
	}
	exit;
}

?>

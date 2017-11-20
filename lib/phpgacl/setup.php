<?php

require_once('./admin/gacl_admin.inc.php');
require_once(ADODB_DIR .'/adodb-xmlschema.inc.php');

$db_table_prefix = $gacl->_db_table_prefix;
$db_type = $gacl->_db_type;
$db_name = $gacl->_db_name;
$db_host = $gacl->_db_host;
$db_user = $gacl->_db_user;
$db_password = $gacl->_db_password;
$db_name = $gacl->_db_name;

$failed = 0;

echo '<h1>phpGACL Database Setup</h1>
<p><b>Configuration:</b><br/>
driver = <b>'.$db_type.'</b>,<br/>
host = <b>'.$db_host.'</b>,<br/>
user = <b>'.$db_user.'</b>,<br/>
database = <b>'.$db_name.'</b>,<br/>
table prefix = <b>'.$db_table_prefix.'</b></p>';

function echo_success($text) {
	echo '<font color="green"><b>Success!</b></font> '.$text."<br/>\n";
}

function echo_failed($text) {
	global $failed;
	echo '<font color="red"><b>Failed!</b></font> '.$text."<br/>\n";
	$failed++;
}

function echo_normal($text) {
	echo $text."<br/>\n";
}

/*
 * Test database connection
 */
echo '<hr/><h2>Testing database connection...</h2>'."\n";

if (is_resource($db->_connectionID)) {
	echo_success('Connected to &quot;<b>'.$db_type.'</b>&quot; database on &quot;<b>'.$db_host.'</b>&quot;.');
} else {
	echo_failed('<b>ERROR</b> connecting to database,<br/>
			are you sure you specified the proper host, user name, password, and database in <b>admin/gacl_admin.inc.php</b>?<br/>
			Did you create the database, and give read/write permissions to &quot;<b>'.$db_user.'</b>&quot; already?');
	exit;
}

/*
 * Do database specific stuff.
 */
echo '<hr/><h2>Testing database type...</h2>'."\n";

switch ($db_type) {
	case mysql:
		echo_success("Compatible database type \"<b>$db_type</b>\" detected!");
		echo_normal("Making sure database \"<b>$db_name</b>\" exists...");

		$databases = $db->GetCol("show databases");

		if (in_array($db_name, $databases) ) {
			echo_success("Good, database \"<b>$db_name</b>\" already exists!");
		} else {
			echo_normal("Database \"<b>$db_name</b>\" does not exist!");
			echo_normal("Lets try to create it...");

			if (!$db->Execute("create database $db_name") ) {
				echo_failed("Database \"<b>$db_name</b>\" could not be created, please do so manually.");
			} else {
				echo_success("Good, database \"<b>$db_name</b>\" has been created!!");

				//Reconnect. Hrmm, this is kinda weird.
				$db->Connect($db_host, $db_user, $db_password, $db_name);
			}
		}

		break;
	case postgres7:
		echo_success("Compatible database type \"<b>$db_type</b>\" detected!");

		echo_normal("Making sure database \"<b>$db_name</b>\" exists...");

		$databases = $db->GetCol("select datname from pg_database");

		if (in_array($db_name, $databases) ) {
			echo_success("Good, database \"<b>$db_name</b>\" already exists!");
		} else {
			echo_normal("Database \"<b>$db_name</b>\" does not exist!");
			echo_normal("Lets try to create it...");

			if (!$db->Execute("create database $db_name") ) {
				echo_failed("Database \"<b>$db_name</b>\" could not be created, please do so manually.");
			} else {
				echo_success("Good, database \"<b>$db_name</b>\" has been created!!");

				//Reconnect. Hrmm, this is kinda weird.
				$db->Connect($db_host, $db_user, $db_password, $db_name);
			}
		}

		break;

	case oci8-po:
		echo_success("Compatible database type \"<b>$db_type</b>\" detected!");

		echo_normal("Making sure database \"<b>$db_name</b>\" exists...");

		$databases = $db->GetCol("select '$db_name' from dual");

		if (in_array($db_name, $databases) ) {
				echo_success("Good, database \"<b>$db_name</b>\" already exists!");
		} else {
				echo_normal("Database \"<b>$db_name</b>\" does not exist!");
				echo_normal("Lets try to create it...");

				if (!$db->Execute("create database $db_name") ) {
						echo_failed("Database \"<b>$db_name</b>\" could not be created, please do so manually.");
				} else {
						echo_success("Good, database \"<b>$db_name</b>\" has been created!!");

						//Reconnect. Hrmm, this is kinda weird.
						$db->Connect($db_host, $db_user, $db_password, $db_name);
				}
		}

		break;
	default:
		echo_normal("Sorry, <b>setup.php</b> currently does not fully support \"<b>$db_type</b>\" databases.
					<br>I'm assuming you've already created the database \"$db_name\", attempting to create tables.
					<br> Please email <b>$author_email</b> code to detect if a database is created or not so full support for \"<b>$db_type</b>\" can be added.");
}


/*
 * Attempt to create tables
 */
// Create the schema object and build the query array.
$schema = new adoSchema($db);
$schema->SetPrefix($db_table_prefix);

// Build the SQL array
$schema->ParseSchema('schema.xml');

// maybe display this if $gacl->debug is true?
if ($gacl->_debug) {
	print "Here's the SQL to do the build:<br />\n<code>";
	print $schema->getSQL('html');
	print "</code>\n";
	// exit;
}

// Execute the SQL on the database
#ADODB's xmlschema is being lame, continue on error.
$schema->ContinueOnError(TRUE);
$result = $schema->ExecuteSchema();

if ($result != 2) {
  echo_failed('Failed creating tables. Please enable DEBUG mode (set it to TRUE in $gacl_options near top of admin/gacl_admin.inc.php) to see the error and try again. You will most likely need to delete any tables already created.');
}

if ( $failed <= 0 ) {
	echo_success('
Installation Successful!!!
<div align="center">
<font color="red"><b>*IMPORTANT*</b></font><br/>
<p>Please make sure you create the <b>&lt;phpGACL root&gt;/admin/templates_c</b> directory,
and give it <b>write permissions</b> for the user your web server runs as.</p>
<p>Please read the manual, and example.php to familiarize yourself with phpGACL.</p>
<a href="admin/about.php?first_run=1"><b>Let\'s get started!</b></a>
</div>
');
} else {
	echo_failed('Please fix the above errors and try again.');
}
?>

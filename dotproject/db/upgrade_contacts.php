<?php

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly. Instead, run the Installer in install/index.php.');
}

dPmsg('Converting users to contacts');
$q = New DBQuery;
$q->addTable( "users" );
$q->addQuery( "*" );
$users = db_loadList( $q->prepare(true) );
$flds = array(
	'contact_first_name',
	'contact_last_name',
	'contact_birthday',
	'contact_company',
	'contact_department',
	'contact_email',
	'contact_phone',
	'contact_phone2',
	'contact_mobile',
	'contact_address1',
	'contact_address2',
	'contact_city',
	'contact_state',
	'contact_zip',
	'contact_country',
	'contact_icq',
	'contact_icon',
	'contact_owner'
	);
foreach ($users as $user) {
        $vals = array(
                $user['user_first_name'], 
                $user['user_last_name'],
                $user['user_birthday'],
                (int)dPgetParam($user, 'user_company', 0),
                (int)dPgetParam($user, 'user_department', 0),
                $user['user_email'],
                $user['user_phone'],
                $user['user_home_phone'],
                $user['user_mobile'],
                $user['user_address1'],
                $user['user_address2'],
                $user['user_city'],
                $user['user_state'],
                $user['user_zip'],
                $user['user_country'],
                $user['user_icq'],
                $user['user_pic'],
                $user['user_owner'],
                );

				$q->addTable('contacts');
				$q->addInsert($flds, $vals, true);

                db_exec( $q->prepareInsert() );
				$q->clear();
                $msg =  db_error();
                $vals = array($user['user_id'], 'USERFORMAT', 'user');
                $q->addTable('user_preferences');
                db_exec($q->addInsert($flds, $vals, true));
				$q->clear();
                $msg =  db_error();
				if ($msg) {
				  dPmsg($msg);
				}
				$q->addTable('users');
				$q->addUpdate('user_contact=LAST_INSERT_ID()');
				$q->addWhere('user_id = '.$user['user_id']);
                db_exec($q->prepare(true));
                $msg =  db_error();
				if ($msg) {
				  dPmsg($msg);
				}
}

?>

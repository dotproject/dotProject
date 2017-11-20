<?php
/**
* @package dotproject
* @subpackage core
* @license http://opensource.org/licenses/bsd-license.php BSD License
*/

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
require_once($AppUI->getSystemClass('libmail'));

//
// New password code based oncode from Mambo Open Source Core
// www.mamboserver.com | mosforge.net
//
function sendNewPass() {
 global $AppUI;

 $_live_site = dPgetConfig('base_url');
 $_sitename = dPgetConfig('company_name');

 // ensure no malicous sql gets past
 $checkusername = trim(dPgetParam($_POST, 'checkusername', ''));
 $checkusername = db_escape($checkusername);
 $confirmEmail = trim(dPgetParam($_POST, 'checkemail', ''));
 $confirmEmail = mb_strtolower(db_escape($confirmEmail));

 $q = new DBQuery;
 $q->addTable('users', 'u');
 $q->addQuery('u.user_id');
 $q->addWhere('user_username=\''.$checkusername.'\' AND LOWER(contact_email)=\''.$confirmEmail.'\'');
 $q->leftJoin('contacts', 'c', 'u.user_contact = c.contact_id');
 if (!($user_id = $q->loadResult()) || !$checkusername || !$confirmEmail) {
  $AppUI->setMsg('Invalid username or email.', UI_MSG_ERROR);
  $AppUI->redirect();
 }
 
 $newpass = makePass();
 $message = $AppUI->_('sendpass0', 	UI_OUTPUT_RAW) . ' ' . $checkusername . ' ' 
   . $AppUI->_('sendpass1', UI_OUTPUT_RAW) . ' ' . $_live_site . ' '
   . $AppUI->_('sendpass2', UI_OUTPUT_RAW) . ' ' . $newpass . ' ' 
   . $AppUI->_('sendpass3', UI_OUTPUT_RAW);
 $subject = "$_sitename :: ".$AppUI->_('sendpass4', UI_OUTPUT_RAW)." - $checkusername";
 
 $m= new Mail; // create the mail
 $m->From("dotProject@" . dPgetConfig('site_domain'));
 $m->To($confirmEmail);
 $m->Subject($subject);
 $m->Body($message, isset($GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "");	// set the body
 $m->Send();	// send the mail

 $newpass = md5($newpass);
 $q->clear();
 $q->addTable('users');
 $q->addUpdate('user_password', $newpass, true);
 $q->addWhere('user_id=\''.$user_id . '\'');
 $cur = $q->exec();
 if (!$cur) {
  die('SQL error' . $database->stderr(true));
 } else {
  $AppUI->setMsg('New User Password created and emailed to you');
  $AppUI->redirect();
 }
}

function makePass() {
 $makepass='';
 $salt = 'abchefghjkmnpqrstuvwxyz0123456789';
 srand((double)microtime()*1000000);
 $i = 0;
 while ($i <= 7) {
  $num = rand() % 33;
  $tmp = mb_substr($salt, $num, 1);
  $makepass = $makepass . $tmp;
  $i++;
 }
 return ($makepass);
}
?>

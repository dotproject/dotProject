<?php
/*
 * phpGACL - Generic Access Control List
 * Copyright (C) 2002 Mike Benoit
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., please join the
 * phpGACL mailing list. http://sourceforge.net/mail/?group_id=57103
 *
 * You may contact the author of phpGACL by e-mail at:
 * ipso@snappymail.ca
 *
 * The latest version of phpGACL can be obtained from:
 * http://phpgacl.sourceforge.net/
 *
 */

// Prefix added to restrict access to these functions to only
// the original dotproject user.
$baseDir = dirname(__FILE__)."/../../..";
require_once "$baseDir/includes/config.php";
require_once  "$baseDir/classes/ui.class.php";
require_once "$baseDir/includes/session.php";
require_once(dirname(__FILE__).'/../gacl.class.php');
require_once(dirname(__FILE__).'/../gacl_api.class.php');

dPsessionStart();

$gacl_options = array(
								'debug' => FALSE,
								'items_per_page' => 100,
								'max_select_box_items' => 100,
								'max_search_return_items' => 200,
								'db_type' => $dPconfig['dbtype'],
								'db_host' => $dPconfig['dbhost'],
								'db_user' => $dPconfig['dbuser'],
								'db_password' => $dPconfig['dbpass'],
								'db_name' => $dPconfig['dbname'],
								'db_table_prefix' => 'gacl_',
								'caching' => FALSE,
								'force_cache_expire' => TRUE,
								'cache_dir' => '/tmp/phpgacl_cache',
								'cache_expire_time' => 600
							);


$gacl_api = new gacl_api($gacl_options);

$gacl = &$gacl_api;

$db = &$gacl->db;

if (! isset($_SESSION['AppUI']))
  die ("You must log into dotProject first");
if ( $_SESSION['AppUI']->user_id != 1 ) {
  // bit of a chicken and egg here, but allow other users to manage acls.
  if (! $gacl->acl_check("application", "access", "user", $_SESSION['AppUI']->user_id, "sys", "acl"))
    die ("You do not have the appropriate permissions for this task");
}
// End of dotproject login check.



/*
 * Configure the Smarty Class for the administration interface ONLY!
 */
$smarty_dir = "$baseDir/lib/smarty"; //NO trailing slash!
$smarty_template_dir = "$smarty_dir/templates"; //NO trailing slash!
$smarty_compile_dir = "$smarty_dir/templates_c"; //NO trailing slash!

//Setup the Smarty Class.
require_once($smarty_dir.'/Smarty.class.php');

$smarty = new Smarty;
$smarty->compile_check = TRUE;
$smarty->template_dir = $smarty_template_dir;
$smarty->compile_dir = $smarty_compile_dir;

/*
 * Email address used in setup.php, please do not change.
 */
$author_email = 'ipso@snappymail.ca';

/*
 * Don't need to show notices, some of them are pretty lame and people get overly worried when they see them.
 * Mean while I will try to fix most of these. ;) Please submit patches if you find any I may have missed.
 */
error_reporting (E_ALL ^ E_NOTICE);

?>

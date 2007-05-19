#
# dotproject.sql Database Schema
#   updated by JRP (08 July 2002)
#   updated by JCP (29 November 2002)
#
# Use this schema for creating your database for
# a new installation of dotProject.
#

#
# TODO
#
# * replace "task_owner" with "task_creator"
#

CREATE TABLE `companies` (
  `company_id` INT(10) NOT NULL auto_increment,
  `company_module` INT(10) NOT NULL default 0,
  `company_name` varchar(100) default '',
  `company_phone1` varchar(30) default '',
  `company_phone2` varchar(30) default '',
  `company_fax` varchar(30) default '',
  `company_address1` varchar(50) default '',
  `company_address2` varchar(50) default '',
  `company_city` varchar(30) default '',
  `company_state` varchar(30) default '',
  `company_zip` varchar(11) default '',
  `company_primary_url` varchar(255) default '',
  `company_owner` int(11) NOT NULL default '0',
  `company_description` text NOT NULL,
  `company_type` int(3) NOT NULL DEFAULT '0',
  `company_email` varchar(255),
  `company_custom` LONGTEXT,
  PRIMARY KEY (`company_id`),
	KEY `idx_cpy1` (`company_owner`)
) TYPE=MyISAM;

#
# New to version 1.0
#
CREATE TABLE `departments` (
  `dept_id` int(10) unsigned NOT NULL auto_increment,
  `dept_parent` int(10) unsigned NOT NULL default '0',
  `dept_company` int(10) unsigned NOT NULL default '0',
  `dept_name` tinytext NOT NULL,
  `dept_phone` varchar(30) default NULL,
  `dept_fax` varchar(30) default NULL,
  `dept_address1` varchar(30) default NULL,
  `dept_address2` varchar(30) default NULL,
  `dept_city` varchar(30) default NULL,
  `dept_state` varchar(30) default NULL,
  `dept_zip` varchar(11) default NULL,
  `dept_url` varchar(25) default NULL,
  `dept_desc` text,
  `dept_owner` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`dept_id`)
) TYPE=MyISAM COMMENT='Department heirarchy under a company';

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL auto_increment,
  `contact_first_name` varchar(30) default NULL,
  `contact_last_name` varchar(30) default NULL,
  `contact_order_by` varchar(30) NOT NULL default '',
  `contact_title` varchar(50) default NULL,
  `contact_birthday` date default NULL,
  `contact_job` varchar(255) default NULL,
  `contact_company` varchar(100) NOT NULL default '',
  `contact_department` TINYTEXT,
  `contact_type` varchar(20) default NULL,
  `contact_email` varchar(255) default NULL,
  `contact_email2` varchar(255) default NULL,
  `contact_url` varchar(255) default NULL,
  `contact_phone` varchar(30) default NULL,
  `contact_phone2` varchar(30) default NULL,
  `contact_fax` varchar(30) default NULL,
  `contact_mobile` varchar(30) default NULL,
  `contact_address1` varchar(60) default NULL,
  `contact_address2` varchar(60) default NULL,
  `contact_city` varchar(30) default NULL,
  `contact_state` varchar(30) default NULL,
  `contact_zip` varchar(11) default NULL,
  `contact_country` varchar(30) default NULL,
  `contact_jabber` varchar(255) default NULL,
  `contact_icq` varchar(20) default NULL,
  `contact_msn` varchar(255) default NULL,
  `contact_yahoo` varchar(255) default NULL,
  `contact_aol` varchar(30) default NULL,
  `contact_notes` text,
  `contact_project` int(11) NOT NULL default '0',
  `contact_icon` varchar(20) default 'obj/contact',
  `contact_owner` int unsigned default '0',
  `contact_private` tinyint unsigned default '0',
  PRIMARY KEY  (`contact_id`),
  KEY `idx_oby` (`contact_order_by`),
  KEY `idx_co` (`contact_company`),
  KEY `idx_prp` (`contact_project`)
) TYPE=MyISAM;

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL auto_increment,
  `event_title` varchar(255) NOT NULL default '',
  `event_start_date` datetime default null,
  `event_end_date` datetime default null,
  `event_parent` int(11) unsigned NOT NULL default '0',
  `event_description` text,
  `event_times_recuring` int(11) unsigned NOT NULL default '0',
  `event_recurs` int(11) unsigned NOT NULL default '0',
  `event_remind` int(10) unsigned NOT NULL default '0',
  `event_icon` varchar(20) default 'obj/event',
  `event_owner` int(11) default '0',
  `event_project` int(11) default '0',
  `event_private` tinyint(3) default '0',
  `event_type` tinyint(3) default '0',
  `event_cwd` tinyint(3) default '0',
	`event_notify` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`event_id`),
  KEY `id_esd` (`event_start_date`),
  KEY `id_eed` (`event_end_date`),
  KEY `id_evp` (`event_parent`),
	KEY `idx_ev1` (`event_owner`),
	KEY `idx_ev2` (`event_project`)
) TYPE=MyISAM;

# 20050303
#
CREATE TABLE `event_queue` (
  `queue_id` int(11) NOT NULL auto_increment,
  `queue_start` int(11) NOT NULL default '0',
  `queue_type` varchar(40) NOT NULL default '',
  `queue_repeat_interval` int(11) NOT NULL default '0',
  `queue_repeat_count` int(11) NOT NULL default '0',
  `queue_data` longblob NOT NULL,
  `queue_callback` varchar(127) NOT NULL default '',
  `queue_owner` int(11) NOT NULL default '0',
  `queue_origin_id` int(11) NOT NULL default '0',
  `queue_module` varchar(40) NOT NULL default '',
  `queue_module_type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`queue_id`),
  KEY `queue_start` (`queue_start`),
  KEY `queue_module` (`queue_module`),
  KEY `queue_type` (`queue_type`),
  KEY `queue_origin_id` (`queue_origin_id`)
) TYPE=MyISAM;


CREATE TABLE `files` (
  `file_id` int(11) NOT NULL auto_increment,
  `file_real_filename` varchar(255) NOT NULL default '',
  `file_folder` int(11) NOT NULL default '0',
  `file_project` int(11) NOT NULL default '0',
  `file_task` int(11) NOT NULL default '0',
  `file_name` varchar(255) NOT NULL default '',
  `file_parent` int(11) default '0',
  `file_description` text,
  `file_type` varchar(100) default NULL,
  `file_owner` int(11) default '0',
  `file_date` datetime default NULL,
  `file_size` int(11) default '0',
  `file_version` float NOT NULL default '0',
  `file_icon` varchar(20) default 'obj/',
  `file_category` int(11) default '0',
	`file_checkout` varchar(255) not null default '',
	`file_co_reason` text,
	`file_version_id` int(11) not null default '0',
  PRIMARY KEY  (`file_id`),
  KEY `idx_file_task` (`file_task`),
  KEY `idx_file_project` (`file_project`),
  KEY `idx_file_parent` (`file_parent`),
	KEY `idx_file_vid` (`file_version_id`)
) TYPE=MyISAM;

CREATE TABLE `files_index` (
  `file_id` int(11) NOT NULL default '0',
  `word` varchar(50) NOT NULL default '',
  `word_placement` int(11) NOT NULL default '0',
  PRIMARY KEY  (`file_id`,`word`, `word_placement`),
  KEY `idx_fwrd` (`word`)
) TYPE=MyISAM;

CREATE TABLE `forum_messages` (
  `message_id` int(11) NOT NULL auto_increment,
  `message_forum` int(11) NOT NULL default '0',
  `message_parent` int(11) NOT NULL default '0',
  `message_author` int(11) NOT NULL default '0',
  `message_editor` int(11) NOT NULL default '0',
  `message_title` varchar(255) NOT NULL default '',
  `message_date` datetime default '0000-00-00 00:00:00',
  `message_body` text,
  `message_published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`message_id`),
  KEY `idx_mparent` (`message_parent`),
  KEY `idx_mdate` (`message_date`),
  KEY `idx_mforum` (`message_forum`)
) TYPE=MyISAM;

#
# new field forum_last_id in Version 1.0
#
CREATE TABLE `forums` (
  `forum_id` int(11) NOT NULL auto_increment,
  `forum_project` int(11) NOT NULL default '0',
  `forum_status` tinyint(4) NOT NULL default '-1',
  `forum_owner` int(11) NOT NULL default '0',
  `forum_name` varchar(50) NOT NULL default '',
  `forum_create_date` datetime default '0000-00-00 00:00:00',
  `forum_last_date` datetime default '0000-00-00 00:00:00',
  `forum_last_id` INT UNSIGNED DEFAULT '0' NOT NULL,
  `forum_message_count` int(11) NOT NULL default '0',
  `forum_description` varchar(255) default NULL,
  `forum_moderated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`forum_id`),
  KEY `idx_fproject` (`forum_project`),
  KEY `idx_fowner` (`forum_owner`),
  KEY `forum_status` (`forum_status`)
) TYPE=MyISAM;

#
# New to Version 1.0
#
CREATE TABLE `forum_watch` (
  `watch_user` int(10) unsigned NOT NULL default '0',
  `watch_forum` int(10) unsigned default NULL,
  `watch_topic` int(10) unsigned default NULL,
	KEY `idx_fw1` (`watch_user`, `watch_forum`),
	KEY `idx_fw2` (`watch_user`, `watch_topic`)
) TYPE=MyISAM COMMENT='Links users to the forums/messages they are watching';

# 20050303
# New to Version 2.0
CREATE TABLE `forum_visits` (
  `visit_user` INT(10) NOT NULL DEFAULT 0,
  `visit_forum` INT(10) NOT NULL DEFAULT 0,
  `visit_message` INT(10) NOT NULL DEFAULT 0,
  `visit_date` TIMESTAMP,
  KEY `idx_fv` (`visit_user`, `visit_forum`, `visit_message`)
) TYPE=MyISAM;

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL auto_increment,
  `permission_user` int(11) NOT NULL default '0',
  `permission_grant_on` varchar(12) NOT NULL default '',
  `permission_item` int(11) NOT NULL default '0',
  `permission_value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`permission_id`),
  UNIQUE KEY `idx_pgrant_on` (`permission_grant_on`,`permission_item`,`permission_user`),
  KEY `idx_puser` (`permission_user`),
  KEY `idx_pvalue` (`permission_value`)
) TYPE=MyISAM;

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL auto_increment,
  `project_company` int(11) NOT NULL default '0',
  `project_department` int(11) NOT NULL default '0',
  `project_name` varchar(255) default NULL,
  `project_short_name` varchar(10) default NULL,
  `project_owner` int(11) default '0',
  `project_url` varchar(255) default NULL,
  `project_demo_url` varchar(255) default NULL,
  `project_start_date` datetime default NULL,
  `project_end_date` datetime default NULL,
  `project_actual_end_date` datetime default NULL,
  `project_status` int(11) default '0',
  `project_percent_complete` tinyint(4) default '0',
  `project_color_identifier` varchar(6) default 'eeeeee',
  `project_description` text,
  `project_target_budget` decimal(10,2) default '0.00',
  `project_actual_budget` decimal(10,2) default '0.00',
  `project_creator` int(11) default '0',
  `project_private` tinyint(3) unsigned default '0',
  `project_departments` CHAR( 100 ) ,
  `project_contacts` CHAR( 100 ) ,
  `project_priority` tinyint(4) default '0',
  `project_type` SMALLINT DEFAULT '0' NOT NULL,
  PRIMARY KEY  (`project_id`),
  KEY `idx_project_owner` (`project_owner`),
  KEY `idx_sdate` (`project_start_date`),
  KEY `idx_edate` (`project_end_date`),
  KEY `project_short_name` (`project_short_name`),
	KEY `idx_proj1` (`project_company`)

) TYPE=MyISAM;

CREATE TABLE `project_contacts` (
  `project_id` INT(10) NOT NULL,
  `contact_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `project_departments` (
  `project_id` INT(10) NOT NULL,
  `department_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `task_log` (
  `task_log_id` INT(11) NOT NULL auto_increment,
  `task_log_task` INT(11) NOT NULL default '0',
  `task_log_name` VARCHAR(255) default NULL,
  `task_log_description` TEXT,
  `task_log_creator` INT(11) NOT NULL default '0',
  `task_log_hours` FLOAT DEFAULT "0" NOT NULL,
  `task_log_date` DATETIME,
  `task_log_costcode` VARCHAR(8) NOT NULL default '',
  `task_log_problem` TINYINT( 1 ) DEFAULT '0',
  `task_log_reference` TINYINT( 4 ) DEFAULT '0',
  `task_log_related_url` VARCHAR( 255 ) DEFAULT NULL,
  PRIMARY KEY  (`task_log_id`),
  KEY `idx_log_task` (`task_log_task`)
) TYPE=MyISAM;

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL auto_increment,
  `task_name` varchar(255) default NULL,
  `task_parent` int(11) default '0',
  `task_milestone` tinyint(1) default '0',
  `task_project` int(11) NOT NULL default '0',
  `task_owner` int(11) NOT NULL default '0',
  `task_start_date` datetime default NULL,
  `task_duration` float unsigned default '0',
  `task_duration_type` int(11) NOT NULL DEFAULT 1,
  `task_hours_worked` float unsigned default '0',
  `task_end_date` datetime default NULL,
  `task_status` int(11) default '0',
  `task_priority` tinyint(4) default '0',
  `task_percent_complete` tinyint(4) default '0',
  `task_description` text,
  `task_target_budget` decimal(10,2) default '0.00',
  `task_related_url` varchar(255) default NULL,
  `task_creator` int(11) NOT NULL default '0',
  `task_order` int(11) NOT NULL default '0',
  `task_client_publish` tinyint(1) NOT NULL default '0',
  `task_dynamic` tinyint(1) NOT NULL default 0,
  `task_access` int(11) NOT NULL default '0',
  `task_notify` int(11) NOT NULL default '0',
  `task_departments` CHAR( 100 ),
  `task_contacts` CHAR( 100 ),
  `task_custom` LONGTEXT,
  `task_type` SMALLINT DEFAULT '0' NOT NULL,
  PRIMARY KEY  (`task_id`),
  KEY `idx_task_parent` (`task_parent`),
  KEY `idx_task_project` (`task_project`),
  KEY `idx_task_owner` (`task_owner`),
  KEY `idx_task_order` (`task_order`),
	KEY `idx_task1` (`task_start_date`),
	KEY `idx_task2` (`task_end_date`)
) TYPE=MyISAM;

CREATE TABLE `task_contacts` (
  `task_id` INT(10) NOT NULL,
  `contact_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `task_departments` (
  `task_id` INT(10) NOT NULL,
  `department_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `tickets` (
  `ticket` int(10) unsigned NOT NULL auto_increment,
  `ticket_company` int(10) NOT NULL default '0',
  `ticket_project` int(10) NOT NULL default '0',
  `author` varchar(100) NOT NULL default '',
  `recipient` varchar(100) NOT NULL default '',
  `subject` varchar(100) NOT NULL default '',
  `attachment` tinyint(1) unsigned NOT NULL default '0',
  `timestamp` int(10) unsigned NOT NULL default '0',
  `type` varchar(15) NOT NULL default '',
  `assignment` int(10) unsigned NOT NULL default '0',
  `parent` int(10) unsigned NOT NULL default '0',
  `activity` int(10) unsigned NOT NULL default '0',
  `priority` tinyint(1) unsigned NOT NULL default '1',
  `cc` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `signature` text,
  PRIMARY KEY  (`ticket`),
  KEY `parent` (`parent`),
  KEY `type` (`type`)
) TYPE=MyISAM;

CREATE TABLE `user_events` (
	`user_id` int(11) NOT NULL default '0',
	`event_id` int(11) NOT NULL default '0',
	KEY `uek1` (`user_id`, `event_id`),
	KEY `uek2` (`event_id`, `user_id`)
) TYPE=MyISAM;

CREATE TABLE `user_tasks` (
  `user_id` int(11) NOT NULL default '0',
  `user_type` tinyint(4) NOT NULL default '0',
  `task_id` int(11) NOT NULL default '0',
  `perc_assignment` int(11) NOT NULL default '100',
  `user_task_priority` tinyint(4) default '0',
  PRIMARY KEY  (`user_id`,`task_id`),
  KEY `user_type` (`user_type`)
) TYPE=MyISAM;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_contact` int(11) NOT NULL default '0',
  `user_username` varchar(255) NOT NULL default '',
  `user_password` varchar(32) NOT NULL default '',
  `user_parent` int(11) NOT NULL default '0',
  `user_type` tinyint(3) not null default '0',
  `user_company` int(11) default '0',
  `user_department` int(11) default '0',
/*  `user_first_name` varchar(50) default '',
  `user_last_name` varchar(50) default '',
  `user_email` varchar(255) default '',
  `user_phone` varchar(30) default '',
  `user_home_phone` varchar(30) default '',
  `user_mobile` varchar(30) default '',
  `user_address1` varchar(30) default '',
  `user_address2` varchar(30) default '',
  `user_city` varchar(30) default '',
  `user_state` varchar(30) default '',
  `user_zip` varchar(11) default '',
  `user_country` varchar(30) default '',
  `user_icq` varchar(20) default '',
  `user_aol` varchar(20) default '',
  `user_birthday` datetime default NULL,
  `user_pic` TEXT,*/
  `user_owner` int(11) NOT NULL default '0',
  `user_signature` TEXT,
  PRIMARY KEY  (`user_id`),
  KEY `idx_uid` (`user_username`),
  KEY `idx_pwd` (`user_password`),
  KEY `idx_user_parent` (`user_parent`)
) TYPE=MyISAM;

CREATE TABLE `task_dependencies` (
    `dependencies_task_id` int(11) NOT NULL,
    `dependencies_req_task_id` int(11) NOT NULL,
    PRIMARY KEY (`dependencies_task_id`, `dependencies_req_task_id`)
);

CREATE TABLE `user_preferences` (
  `pref_user` varchar(12) NOT NULL default '',
  `pref_name` varchar(72) NOT NULL default '',
  `pref_value` varchar(32) NOT NULL default '',
  KEY `pref_user` (`pref_user`,`pref_name`)
) TYPE=MyISAM;

#
# ATTENTION:
# Customize this section for your installation.
# Recommended changes include:
#   New admin username -> replace {admin}
#   New admin password -> replace {passwd]
#   New admin email -> replace {admin@localhost}
#

INSERT INTO `users` VALUES (1,1,'admin',MD5('passwd'),0,1,0,0,0,'');
INSERT INTO `contacts` (contact_id, contact_first_name, contact_last_name, contact_email) 
  VALUES (1,'Admin','Person','admin@localhost');

INSERT INTO `permissions` VALUES (1,1,"all",-1, -1);

INSERT INTO `user_preferences` VALUES("0", "LOCALE", "en");
INSERT INTO `user_preferences` VALUES("0", "TABVIEW", "0");
INSERT INTO `user_preferences` VALUES("0", "SHDATEFORMAT", "%d/%m/%Y");
INSERT INTO `user_preferences` VALUES("0", "TIMEFORMAT", "%I:%M %p");
INSERT INTO `user_preferences` VALUES("0", "UISTYLE", "default");
INSERT INTO `user_preferences` VALUES("0", "TASKASSIGNMAX", "100");
INSERT INTO `user_preferences` VALUES("0", "USERFORMAT", "user");

#
# AJE (24/Jan/2003)
# ---------
# N O T E !
#
# MODULES TABLE IS STILL IN DEVELOPMENT STAGE
#

#
# Table structure for table 'modules'
#
#DROP TABLE modules;
CREATE TABLE `modules` (
  `mod_id` int(11) NOT NULL auto_increment,
  `mod_name` varchar(64) NOT NULL default '',
  `mod_directory` varchar(64) NOT NULL default '',
  `mod_version` varchar(10) NOT NULL default '',
  `mod_setup_class` varchar(64) NOT NULL default '',
  `mod_type` varchar(64) NOT NULL default '',
  `mod_active` int(1) unsigned NOT NULL default '0',
  `mod_ui_name` varchar(20) NOT NULL default '',
  `mod_ui_icon` varchar(64) NOT NULL default '',
  `mod_ui_order` tinyint(3) NOT NULL default '0',
  `mod_ui_active` int(1) unsigned NOT NULL default '0',
  `mod_description` varchar(255) NOT NULL default '',
  `permissions_item_table` CHAR( 100 ),
  `permissions_item_field` CHAR( 100 ),
  `permissions_item_label` CHAR( 100 ),
  PRIMARY KEY  (`mod_id`,`mod_directory`)
) TYPE=MyISAM;

#
# Dumping data for table 'modules'
#
INSERT INTO `modules` VALUES("1", "Companies", "companies", "1.0.0", "", "core", "1", "Companies", "handshake.png", "1", "1", "", "companies", "company_id", "company_name");
INSERT INTO `modules` VALUES("2", "Projects", "projects", "1.0.0", "", "core", "1", "Projects", "applet3-48.png", "2", "1", "", "projects", "project_id", "project_name");
INSERT INTO `modules` VALUES("3", "Tasks", "tasks", "1.0.0", "", "core", "1", "Tasks", "applet-48.png", "3", "1", "", "tasks", "task_id", "task_name");
INSERT INTO `modules` VALUES("4", "Calendar", "calendar", "1.0.0", "", "core", "1", "Calendar", "myevo-appointments.png", "4", "1", "", "", "", "");
INSERT INTO `modules` VALUES("5", "Files", "files", "1.0.0", "", "core", "1", "Files", "folder5.png", "5", "1", "", "files", "file_id", "file_name");
INSERT INTO `modules` VALUES("6", "Contacts", "contacts", "1.0.0", "", "core", "1", "Contacts", "monkeychat-48.png", "6", "1", "", "", "", "");
INSERT INTO `modules` VALUES("7", "Forums", "forums", "1.0.0", "", "core", "1", "Forums", "support.png", "7", "1", "", "forums", "forum_id", "forum_name");
INSERT INTO `modules` VALUES("8", "Tickets", "ticketsmith", "1.0.0", "", "core", "1", "Tickets", "ticketsmith.gif", "8", "1", "", "", "", "");
INSERT INTO `modules` VALUES("9", "User Administration", "admin", "1.0.0", "", "core", "1", "User Admin", "helix-setup-users.png", "9", "1", "", "users", "user_id", "user_username");
INSERT INTO `modules` VALUES("10", "System Administration", "system", "1.0.0", "", "core", "1", "System Admin", "48_my_computer.png", "10", "1", "", "", "", "");
INSERT INTO `modules` VALUES("11", "Departments", "departments", "1.0.0", "", "core", "1", "Departments", "users.gif", "11", "0", "", "", "", "");
INSERT INTO `modules` VALUES("12", "Help", "help", "1.0.0", "", "core", "1", "Help", "dp.gif", "12", "0", "", "", "", "");
INSERT INTO `modules` VALUES("13", "Public", "public", "1.0.0", "", "core", "1", "Public", "users.gif", "13", "0", "", "", "", "");

#
# Table structure for table 'syskeys'
#

DROP TABLE IF EXISTS `syskeys`;
CREATE TABLE `syskeys` (
  `syskey_id` int(10) unsigned NOT NULL auto_increment,
  `syskey_name` varchar(48) NOT NULL default '' unique,
  `syskey_label` varchar(255) NOT NULL default '',
  `syskey_type` int(1) unsigned NOT NULL default '0',
  `syskey_sep1` char(2) default '\n',
  `syskey_sep2` char(2) NOT NULL default '|',
  PRIMARY KEY  (`syskey_id`),
  UNIQUE KEY `idx_syskey_name` (`syskey_id`)
) TYPE=MyISAM;

#
# Table structure for table 'sysvals'
#

DROP TABLE IF EXISTS `sysvals`;
CREATE TABLE `sysvals` (
  `sysval_id` int(10) unsigned NOT NULL auto_increment,
  `sysval_key_id` int(10) unsigned NOT NULL default '0',
  `sysval_title` varchar(48) NOT NULL default '',
  `sysval_value` text NOT NULL,
  PRIMARY KEY  (`sysval_id`)
) TYPE=MyISAM;

#
# Table structure for table 'sysvals'
#

INSERT INTO `syskeys` VALUES (1, "SelectList", "Enter values for list", "0", "\n", "|");
INSERT INTO `syskeys` VALUES (2, 'CustomField', 'Serialized array in the following format:\r\n<KEY>|<SERIALIZED ARRAY>\r\n\r\nSerialized Array:\r\n[type] => text | checkbox | select | textarea | label\r\n[name] => <Field\'s name>\r\n[options] => <html capture options>\r\n[selects] => <options for select and checkbox>', 0, '\n', '|');
INSERT INTO `syskeys` VALUES (3, "ColorSelection", "Hex color values for type=>color association.", "0", "\n", "|");

INSERT INTO `sysvals` (`sysval_key_id`,`sysval_title`,`sysval_value`) VALUES (1, "ProjectStatus", "0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived");
INSERT INTO `sysvals` (`sysval_key_id`,`sysval_title`,`sysval_value`) VALUES (1, "CompanyType", "0|Not Applicable\n1|Client\n2|Vendor\n3|Supplier\n4|Consultant\n5|Government\n6|Internal");
INSERT INTO `sysvals` (`sysval_key_id`,`sysval_title`,`sysval_value`) VALUES (1, "TaskDurationType", "1|hours\n24|days");
INSERT INTO `sysvals` (`sysval_key_id`,`sysval_title`,`sysval_value`) VALUES (1, "EventType", "0|General\n1|Appointment\n2|Meeting\n3|All Day Event\n4|Anniversary\n5|Reminder");
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TaskStatus', '0|Active\n-1|Inactive');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TaskType', '0|Unknown\n1|Administrative\n2|Operative');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'ProjectType', '0|Unknown\n1|Administrative\n2|Operative');
INSERT INTO `sysvals` (`sysval_key_id`,`sysval_title`,`sysval_value`) VALUES(3, "ProjectColors", "Web|FFE0AE\nEngineering|AEFFB2\nHelpDesk|FFFCAE\nSystem Administration|FFAEAE");
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'FileType', '0|Unknown\n1|Document\n2|Application');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TaskPriority', '-1|low\n0|normal\n1|high');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'ProjectPriority', '-1|low\n0|normal\n1|high');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'ProjectPriorityColor', '-1|#E5F7FF\n0|\n1|#FFDCB3');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TaskLogReference', '0|Not Defined\n1|Email\n2|Helpdesk\n3|Phone Call\n4|Fax');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TaskLogReferenceImage', '0| 1|./images/obj/email.gif 2|./modules/helpdesk/images/helpdesk.png 3|./images/obj/phone.gif 4|./images/icons/stock_print-16.png');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'UserType', '0|Default User\r\n1|Administrator\r\n2|CEO\r\n3|Director\r\n4|Branch Manager\r\n5|Manager\r\n6|Supervisor\r\n7|Employee');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'ProjectRequiredFields', 'f.project_name.value.length|<3\r\nf.project_color_identifier.value.length|<3\r\nf.project_company.options[f.project_company.selectedIndex].value|<1' );
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 2, 'TicketNotify', '0|admin@localhost\n1|admin@localhost\n2|admin@localhost\r\n3|admin@localhost\r\n4|admin@localhost');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TicketPriority', '0|Low\n1|Normal\n2|High\n3|Highest\n4|911');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TicketStatus', '0|Open\n1|Closed\n2|Deleted');
#
# Table structure for table 'roles'
#

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `role_name` varchar(24) NOT NULL default '',
  `role_description` varchar(255) NOT NULL default '',
  `role_type` int(3) unsigned NOT NULL default '0',
  `role_module` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`role_id`)
) TYPE=MyISAM;

#
# Table structure for table 'user_roles'
#

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `role_id` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

# Host: localhost
# Database: dotproject
# Table: 'common_notes'
# 
DROP TABLE IF EXISTS `common_notes`;
CREATE TABLE `common_notes` (
  `note_id` int(10) unsigned NOT NULL auto_increment,
  `note_author` int(10) unsigned NOT NULL default '0',
  `note_module` int(10) unsigned NOT NULL default '0',
  `note_record_id` int(10) unsigned NOT NULL default '0',
  `note_category` int(3) unsigned NOT NULL default '0',
  `note_title` varchar(100) NOT NULL default '',
  `note_body` text NOT NULL,
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note_hours` float NOT NULL default '0',
  `note_code` varchar(8) NOT NULL default '',
  `note_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `note_modified` timestamp(14) NOT NULL,
  `note_modified_by` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`note_id`)
) TYPE=MyISAM; 



#20040823
#Added user access log
CREATE TABLE `user_access_log` (
`user_access_log_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`user_ip` VARCHAR( 15 ) NOT NULL ,
`date_time_in` DATETIME DEFAULT '0000-00-00 00:00:00',
`date_time_out` DATETIME DEFAULT '0000-00-00 00:00:00',
`date_time_last_action` DATETIME DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY ( `user_access_log_id` )
) TYPE = MyISAM;

#20040910
#Pinned tasks
DROP TABLE IF EXISTS `user_task_pin`;
CREATE TABLE `user_task_pin` (
`user_id` int(11) NOT NULL default '0',
`task_id` int(10) NOT NULL default '0',
`task_pinned` tinyint(2) NOT NULL default '1',
PRIMARY KEY (`user_id`,`task_id`)
) TYPE=MyISAM;

#
# Table structure for table `config`
#
# Creation: Feb 23, 2005 at 01:26 PM
# Last update: Feb 24, 2005 at 02:15 AM
#

CREATE TABLE `config` (
  `config_id` int(11) NOT NULL auto_increment,
  `config_name` varchar(255) NOT NULL default '',
  `config_value` varchar(255) NOT NULL default '',
  `config_group` varchar(255) NOT NULL default '',
  `config_type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_name` (`config_name`)
) TYPE=MyISAM AUTO_INCREMENT=47 ;

#
# Dumping data for table `config`
#

INSERT INTO `config` VALUES (0, 'host_locale', 'en', '', 'text');
INSERT INTO `config` VALUES (0, 'check_overallocation', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'currency_symbol', '$', '', 'text');
INSERT INTO `config` VALUES (0, 'host_style', 'default', '', 'text');
INSERT INTO `config` VALUES (0, 'company_name', 'My Company', '', 'text');
INSERT INTO `config` VALUES (0, 'page_title', 'dotProject', '', 'text');
INSERT INTO `config` VALUES (0, 'site_domain', 'dotproject.net', '', 'text');
INSERT INTO `config` VALUES (0, 'email_prefix', '[dotProject]', '', 'text');
INSERT INTO `config` VALUES (0, 'admin_username', 'admin', '', 'text');
INSERT INTO `config` VALUES (0, 'username_min_len', '4', '', 'text');
INSERT INTO `config` VALUES (0, 'password_min_len', '4', '', 'text');
INSERT INTO `config` VALUES (0, 'enable_gantt_charts', 'true', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'log_changes', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'check_task_dates', 'true', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'locale_warn', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'locale_alert', '^', '', 'text');
INSERT INTO `config` VALUES (0, 'daily_working_hours', '8.0', '', 'text');
INSERT INTO `config` VALUES (0, 'display_debug', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'link_tickets_kludge', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'show_all_task_assignees', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'direct_edit_assignment', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'restrict_color_selection', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'cal_day_view_show_minical', 'true', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'cal_day_start', '8', '', 'text');
INSERT INTO `config` VALUES (0, 'cal_day_end', '17', '', 'text');
INSERT INTO `config` VALUES (0, 'cal_day_increment', '15', '', 'text');
INSERT INTO `config` VALUES (0, 'cal_working_days', '1,2,3,4,5', '', 'text');
INSERT INTO `config` VALUES (0, 'restrict_task_time_editing', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'default_view_m', 'calendar', '', 'text');
INSERT INTO `config` VALUES (0, 'default_view_a', 'day_view', '', 'text');
INSERT INTO `config` VALUES (0, 'default_view_tab', '1', '', 'text');
INSERT INTO `config` VALUES (0, 'index_max_file_size', '-1', '', 'text');
INSERT INTO `config` VALUES (0, 'session_handling', 'app', 'session', 'select');
INSERT INTO `config` VALUES (0, 'session_idle_time', '2d', 'session', 'text');
INSERT INTO `config` VALUES (0, 'session_max_lifetime', '1m', 'session', 'text');
INSERT INTO `config` VALUES (0, 'debug', '1', '', 'text');
INSERT INTO `config` VALUES (0, 'parser_default', '/usr/bin/strings', '', 'text');
INSERT INTO `config` VALUES (0, 'parser_application/msword', '/usr/bin/strings', '', 'text');
INSERT INTO `config` VALUES (0, 'parser_text/html', '/usr/bin/strings', '', 'text');
INSERT INTO `config` VALUES (0, 'parser_application/pdf', '/usr/bin/pdftotext', '', 'text');

INSERT INTO `config` VALUES (0, 'files_ci_preserve_attr', 'true', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'files_show_versions_edit', 'false', '', 'checkbox');
INSERT INTO `config` VALUES (0, 'reset_memory_limit', '8M', '', 'text');

# 20050302
# ldap system config variables
INSERT INTO `config` VALUES (0, 'auth_method', 'sql', 'auth', 'select'); 
INSERT INTO `config` VALUES (0, 'ldap_host', 'localhost', 'ldap', 'text'); 
INSERT INTO `config` VALUES (0, 'ldap_port', '389', 'ldap', 'text'); 
INSERT INTO `config` VALUES (0, 'ldap_version', '3', 'ldap', 'text'); 
INSERT INTO `config` VALUES (0, 'ldap_base_dn', 'dc=saki,dc=com,dc=au', 'ldap', 'text'); 
INSERT INTO `config` VALUES (0, 'ldap_user_filter', '(uid=%USERNAME%)', 'ldap', 'text'); 

# 20050302
# PostNuke authentication variables
INSERT INTO `config` VALUES (0, 'postnuke_allow_login', 'true', 'auth', 'checkbox');

# 20050302
# New list support for config variables
CREATE TABLE `config_list` (
`config_list_id` integer not null auto_increment,
`config_id` integer not null default 0,
`config_list_name` varchar(30) not null default '',
PRIMARY KEY(`config_list_id`),
KEY(`config_id`)
);

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'sql'
	FROM config
	WHERE config_name = 'auth_method';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'ldap'
	FROM config
	WHERE config_name = 'auth_method';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'pn'
	FROM config
	WHERE config_name = 'auth_method';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'app'
	FROM config
	WHERE config_name = 'session_handling';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'php'
	FROM config
	WHERE config_name = 'session_handling';

# 20050303
# New mail handling options
INSERT INTO `config` VALUES (0, 'mail_transport', 'php', 'mail', 'select');
INSERT INTO `config` VALUES (0, 'mail_host', 'localhost', 'mail', 'text');
INSERT INTO `config` VALUES (0, 'mail_port', '25', 'mail', 'text');
INSERT INTO `config` VALUES (0, 'mail_auth', 'false', 'mail', 'checkbox');
INSERT INTO `config` VALUES (0, 'mail_user', '', 'mail', 'text');
INSERT INTO `config` VALUES (0, 'mail_pass', '', 'mail', 'password');
INSERT INTO `config` VALUES (0, 'mail_defer', 'false', 'mail', 'checkbox');
INSERT INTO `config` VALUES (0, 'mail_timeout', '30', 'mail', 'text');
INSERT INTO `config` VALUES (0, 'task_reminder_control', 'false', 'task_reminder', 'checkbox');
INSERT INTO `config` VALUES (0, 'task_reminder_days_before', '1', 'task_reminder', 'text');
INSERT INTO `config` VALUES (0, 'task_reminder_repeat', '100', 'task_reminder', 'text');


INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'php'
	FROM config
	WHERE config_name = 'mail_transport';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'smtp'
	FROM config
	WHERE config_name = 'mail_transport';

# 20050303
# Queue scanning on garbage collection
INSERT INTO config VALUES (NULL, 'session_gc_scan_queue', 'false', 'session', 'checkbox');

# 20050302
# new custom fields
CREATE TABLE custom_fields_struct (
field_id integer primary key,
field_module varchar(30),
field_page varchar(30),
field_htmltype varchar(20),
field_datatype varchar(20),
field_order integer,
field_name varchar(100),
field_extratags varchar(250),
field_description varchar(250)
);

CREATE TABLE custom_fields_values (
value_id integer,
value_module varchar(30),
value_object_id integer,
value_field_id integer,
value_charvalue varchar(250),
value_intvalue integer,
KEY `idx_cfv_id` (`value_id`)
);

CREATE TABLE custom_fields_lists (
field_id integer,
list_option_id integer,
list_value varchar(250)
);


#20040920
# ACL support.
#
# Table structure for table `gacl_acl`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 02:15 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_acl`;
CREATE TABLE `gacl_acl` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default 'system',
  `allow` int(11) NOT NULL default '0',
  `enabled` int(11) NOT NULL default '0',
  `return_value` longtext,
  `note` longtext,
  `updated_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `gacl_enabled_acl` (`enabled`),
  KEY `gacl_section_value_acl` (`section_value`),
  KEY `gacl_updated_date_acl` (`updated_date`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_acl_sections`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 22, 2004 at 01:04 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_acl_sections`;
CREATE TABLE `gacl_acl_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aco`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 11:23 AM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_aco`;
CREATE TABLE `gacl_aco` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
  KEY `gacl_hidden_aco` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aco_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 02:15 PM
#

DROP TABLE IF EXISTS `gacl_aco_map`;
CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aco_sections`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 23, 2004 at 08:14 AM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_aco_sections`;
CREATE TABLE `gacl_aco_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aro`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 29, 2004 at 11:38 AM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_aro`;
CREATE TABLE `gacl_aro` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  KEY `gacl_hidden_aro` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aro_groups`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 12:12 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_aro_groups`;
CREATE TABLE `gacl_aro_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`id`,`value`),
  KEY `gacl_parent_id_aro_groups` (`parent_id`),
  KEY `gacl_value_aro_groups` (`value`),
  KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aro_groups_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 12:26 PM
#

DROP TABLE IF EXISTS `gacl_aro_groups_map`;
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aro_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 29, 2004 at 11:33 AM
#

DROP TABLE IF EXISTS `gacl_aro_map`;
CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_aro_sections`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 22, 2004 at 03:04 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_aro_sections`;
CREATE TABLE `gacl_aro_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_axo`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 26, 2004 at 06:23 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_axo`;
CREATE TABLE `gacl_axo` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
  KEY `gacl_hidden_axo` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_axo_groups`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 26, 2004 at 11:00 AM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_axo_groups`;
CREATE TABLE `gacl_axo_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`id`,`value`),
  KEY `gacl_parent_id_axo_groups` (`parent_id`),
  KEY `gacl_value_axo_groups` (`value`),
  KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_axo_groups_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 11:24 AM
#

DROP TABLE IF EXISTS `gacl_axo_groups_map`;
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_axo_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 02:15 PM
#

DROP TABLE IF EXISTS `gacl_axo_map`;
CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(80) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_axo_sections`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 23, 2004 at 03:50 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

DROP TABLE IF EXISTS `gacl_axo_sections`;
CREATE TABLE `gacl_axo_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(80) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_groups_aro_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 29, 2004 at 11:38 AM
#

DROP TABLE IF EXISTS `gacl_groups_aro_map`;
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_groups_axo_map`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 26, 2004 at 11:01 AM
#

DROP TABLE IF EXISTS `gacl_groups_axo_map`;
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gacl_phpgacl`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 22, 2004 at 01:03 PM
#

DROP TABLE IF EXISTS `gacl_phpgacl`;
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `billingcode`;
CREATE TABLE `billingcode` (
  `billingcode_id` bigint(20) NOT NULL auto_increment,
  `billingcode_name` varchar(25) NOT NULL default '',
  `billingcode_value` float NOT NULL default '0',
  `billingcode_desc` varchar(255) NOT NULL default '',
  `billingcode_status` int(1) NOT NULL default '0',
  `company_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`billingcode_id`)
) TYPE=MyISAM;

INSERT INTO `gacl_phpgacl` (name, value) VALUES ('version', '3.3.2');
INSERT INTO `gacl_phpgacl` (name, value) VALUES ('schema_version', '2.1');

INSERT INTO `gacl_acl_sections` (id, value, order_value, name) VALUES (1, 'system', 1, 'System');
INSERT INTO `gacl_acl_sections` (id, value, order_value, name) VALUES (2, 'user', 2, 'User');


#
# Table structure for table `sessions`
#

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
	`session_id` varchar(40) NOT NULL default '',
	`session_user` INT DEFAULT '0' NOT NULL,
	`session_data` LONGBLOB,
	`session_updated` TIMESTAMP,
	`session_created` DATETIME NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY (`session_id`),
	KEY (`session_updated`),
	KEY (`session_created`)
) TYPE=MyISAM;

# 20050304
# Version tracking table.  From here on in all updates are done via the installer,
# which uses this table to manage the upgrade process.
CREATE TABLE dpversion (
	code_version varchar(10) not null default '',
	db_version integer not null default '0',
	last_db_update date not null default '0000-00-00',
	last_code_update date not null default '0000-00-00'
);

INSERT INTO dpversion VALUES ('2.1-rc1', 2, '2007-02-17', '2007-02-17');

# 20050307
# Additional LDAP search user and search password fields for Active Directory compatible LDAP authentication
INSERT INTO `config` VALUES (0, 'ldap_search_user', 'Manager', 'ldap', 'text');
INSERT INTO `config` VALUES (0, 'ldap_search_pass', 'secret', 'ldap', 'password');
INSERT INTO `config` VALUES (0, 'ldap_allow_login', 'true', 'ldap', 'checkbox');

# 20070126
#
# Table structure for table `file_folders`
#

DROP TABLE IF EXISTS `file_folders`;
CREATE TABLE `file_folders` (
	`file_folder_id` int(11) NOT NULL auto_increment,
	`file_folder_parent` int(11) NOT NULL default '0',
	`file_folder_name` varchar(255) NOT NULL default '',
	`file_folder_description` text,
	PRIMARY KEY  (`file_folder_id`)
) TYPE=MyISAM;

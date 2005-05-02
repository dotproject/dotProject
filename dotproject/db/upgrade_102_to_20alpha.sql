# $Id$
#
# Upgrade dotProject DB Schema
# From Version 1.0.2 to Current CVS Version
#
# NOTE: This will NOT upgrade older releases to release 1.0.2
#       You must apply older upgrade script first
#
# !                  W A R N I N G                !
# !BACKUP YOU DATABASE BEFORE APPLYING THIS SCRIPT!
# !                  W A R N I N G                !
#
# add task_departments and contacts to projects table
ALTER TABLE `projects` ADD `project_departments` CHAR( 100 ) ;
ALTER TABLE `projects` ADD `project_contacts` CHAR( 100 ) ;
ALTER TABLE `projects` ADD `project_priority` tinyint(4) default '0';
ALTER TABLE `projects` ADD `project_type` SMALLINT DEFAULT '0' NOT NULL;

#
#Add permissions selection criteria for each module.  
#
ALTER TABLE `modules` ADD `permissions_item_table` CHAR( 100 ) ;
ALTER TABLE `modules` ADD `permissions_item_field` CHAR( 100 ) ;
ALTER TABLE `modules` ADD `permissions_item_label` CHAR( 100 ) ;
UPDATE modules SET permissions_item_table='files', permissions_item_field='file_id', permissions_item_label='file_name' WHERE mod_directory='files';
UPDATE modules SET permissions_item_table='users', permissions_item_field='user_id', permissions_item_label='user_username' WHERE mod_directory='users';
UPDATE modules SET permissions_item_table='projects', permissions_item_field='project_id', permissions_item_label='project_name' WHERE mod_directory='projects';
UPDATE modules SET permissions_item_table='tasks', permissions_item_field='task_id', permissions_item_label='task_name' WHERE mod_directory='tasks';
UPDATE modules SET permissions_item_table='companies', permissions_item_field='company_id', permissions_item_label='company_name' WHERE mod_directory='companies';
UPDATE modules SET permissions_item_table='forums', permissions_item_field='forum_id', permissions_item_label='forum_name' WHERE mod_directory='forums';

#
#add percentage resource allocation
#
ALTER TABLE `user_tasks` ADD COLUMN perc_assignment int(11) NOT NULL default '100';

ALTER TABLE `users` ADD `user_contact` int(11) NOT NULL default '0';
ALTER TABLE `contacts` ADD `contact_fax` varchar(30) NOT NULL default '0';
ALTER TABLE `contacts` ADD `contact_aol` varchar(30) NOT NULL default '0';

ALTER TABLE `tasks` ADD `task_type` SMALLINT DEFAULT '0' NOT NULL ;

ALTER TABLE `files` ADD `file_category` int(11) NOT NULL default '0';
INSERT INTO `sysvals` VALUES (null, 1, 'FileType', '0|Unknown\n1|Document\n2|Application');

# Just some TaskTypes examples
INSERT INTO `sysvals` VALUES (null, 1, 'TaskType', '0|Unknown\n1|Administrative\n2|Operative');
INSERT INTO `sysvals` VALUES (null, 1, 'ProjectType', '0|Unknown\n1|Administrative\n2|Operative');
INSERT INTO `syskeys` VALUES (2, 'CustomField', 'Serialized array in the following format:\r\n<KEY>|<SERIALIZED ARRAY>\r\n\r\nSerialized Array:\r\n[type] => text | checkbox | select | textarea | label\r\n[name] => <Field\'s name>\r\n[options] => <html capture options>\r\n[selects] => <options for select and checkbox>', 0, '\n', '|');
INSERT INTO `syskeys` VALUES("3", "ColorSelection", "Hex color values for type=>color association.", "0", "\n", "|");
INSERT INTO `sysvals` (`sysval_key_id`,`sysval_title`,`sysval_value`) VALUES("3", "ProjectColors", "Web|FFE0AE\nEngineering|AEFFB2\nHelpDesk|FFFCAE\nSystem Administration|FFAEAE");

CREATE TABLE `task_contacts` (
  `task_id` INT(10) NOT NULL,
  `contact_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `task_departments` (
  `task_id` INT(10) NOT NULL,
  `department_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `project_contacts` (
  `project_id` INT(10) NOT NULL,
  `contact_id` INT(10) NOT NULL
) TYPE=MyISAM;

CREATE TABLE `project_departments` (
  `project_id` INT(10) NOT NULL,
  `department_id` INT(10) NOT NULL
) TYPE=MyISAM;

# 20040727
# add user specific task priority
#
ALTER TABLE `user_tasks` ADD `user_task_priority` tinyint(4) default '0';

# 20040728
# converted taskstatus to sysvals
#
INSERT INTO `sysvals` VALUES (null, 1, 'TaskStatus', '0|Active\n-1|Inactive');

# 20040808
# do not show events on non-working days
#
ALTER TABLE `events` ADD `events_cwd` tinyint(3) default '0';

# 20040815
# increase various field lengths
#
ALTER TABLE `contacts` CHANGE `contact_address1` `contact_address1` varchar(60) default null ;
ALTER TABLE `contacts` CHANGE `contact_address2` `contact_address2` varchar(60) default null ;
ALTER TABLE `users` CHANGE `user_username` `user_username` varchar(255) default null ;

# 20040819
# invent task assign maximum
#
ALTER TABLE `user_preferences` CHANGE `pref_name` `pref_name` VARCHAR( 72 ) NOT NULL;
INSERT INTO `user_preferences` VALUES("0", "TASKASSIGNMAX", "100");

#20040820
# added ProjectStatus of Template
#
UPDATE `sysvals` SET `sysval_value` = '0|Not Defined\n1|Proposed\n2|In Planning\n3|In Progress\n4|On Hold\n5|Complete\n6|Template' WHERE `sysval_title` = 'ProjectStatus' LIMIT 1 ;

#20040823
# changed over to dynamic project end date
#
ALTER TABLE `projects` DROP `project_actual_end_date`;

#20040823
#Added user access log
CREATE TABLE `user_access_log` (
`user_access_log_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`date_time_in` DATETIME DEFAULT '0000-00-00 00:00:00',
`date_time_out` DATETIME DEFAULT '0000-00-00 00:00:00',
`date_time_last_action` DATETIME DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY ( `user_access_log_id` )
);

#20040823
# Task Priority, Project Priority are now sysvals
#
INSERT INTO `sysvals` ( `sysval_key_id` , `sysval_title` , `sysval_value` )
VALUES ('1', 'TaskPriority', '-1|low\n0|normal\n1|high');
INSERT INTO `sysvals` ( `sysval_key_id` , `sysval_title` , `sysval_value` )
VALUES ('1', 'ProjectPriority', '-1|low\n0|normal\n1|high');
INSERT INTO `sysvals` ( `sysval_key_id` , `sysval_title` , `sysval_value` )
VALUES ('1', 'ProjectPriorityColor', '-1|#E5F7FF\n0|\n1|#FFDCB3');

#20040823
# Task Log is now sysvals, some additional fields
#
INSERT INTO `sysvals` ( `sysval_key_id` , `sysval_title` , `sysval_value` )
VALUES ('1', 'TaskLogReference', '0|Not Defined\n1|Email\n2|Helpdesk\n3|Phone Call\n4|Fax');
INSERT INTO `sysvals` ( `sysval_key_id` , `sysval_title` , `sysval_value` )
VALUES ('1', 'TaskLogReferenceImage', '0| 1|./images/obj/email.gif 2|./modules/helpdesk/images/helpdesk.png 3|./images/obj/phone.gif 4|./images/icons/stock_print-16.png');

ALTER TABLE `task_log` ADD `task_log_problem` TINYINT( 1 ) DEFAULT '0';
ALTER TABLE `task_log` ADD `task_log_reference` TINYINT( 4 ) DEFAULT '0';
ALTER TABLE `task_log` ADD `task_log_related_url` VARCHAR( 255 ) DEFAULT NULL;

#20040910
# Pinned tasks

CREATE TABLE `user_task_pin` (
`user_id` int(11) NOT NULL default '0',
`task_id` int(10) NOT NULL default '0',
`task_pinned` tinyint(2) NOT NULL default '1',
PRIMARY KEY (`user_id`,`task_id`)
) TYPE=MyISAM;

# 20041022
# Permissions, files, resources
#
# Table structure for table `gacl_acl`
#
# Creation: Jul 22, 2004 at 01:00 PM
# Last update: Jul 28, 2004 at 02:15 PM
# Last check: Jul 22, 2004 at 01:00 PM
#

CREATE TABLE `gacl_acl` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default 'system',
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

CREATE TABLE `gacl_acl_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_aco` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
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

CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_aco_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_aro` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
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

CREATE TABLE `gacl_aro_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
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

CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_aro_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_axo` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
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

CREATE TABLE `gacl_axo_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
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

CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_axo_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
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

CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;


INSERT INTO `gacl_phpgacl` (name, value) VALUES ('version', '3.3.2');
INSERT INTO `gacl_phpgacl` (name, value) VALUES ('schema_version', '2.1');

INSERT INTO `gacl_acl_sections` (id, value, order_value, name) VALUES (1, 'system', 1, 'System');
INSERT INTO `gacl_acl_sections` (id, value, order_value, name) VALUES (2, 'user', 2, 'User');

#
# Indexes to speed up collation of data
#
ALTER TABLE `companies` ADD INDEX (`company_owner`);
ALTER TABLE `events` ADD INDEX (`event_owner`);
ALTER TABLE `events` ADD INDEX (`event_project`);
ALTER TABLE `projects` ADD INDEX (`project_company`);
ALTER TABLE `tasks` ADD INDEX (`task_start_date`);
ALTER TABLE `tasks` ADD INDEX (`task_end_date`);

# Changes to support assignment of events to users
ALTER TABLE `events` ADD `event_notify` TINYINT NOT NULL default '0';

CREATE TABLE `user_events` (
  `user_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  KEY `uek1` (`user_id`, `event_id`),
  KEY `uek2` (`event_id`, `user_id`)
) TYPE=MyISAM;


# Changes to handle file checkin/checkout support
ALTER TABLE `files`
  ADD `file_checkout` VARCHAR(255) NOT NULL DEFAULT '',
  ADD `file_co_reason` TEXT,
  ADD `file_version_id` INT NOT NULL DEFAULT 0,
  ADD INDEX (`file_version_id`);

# Move any old files into the new format
UPDATE `files` SET `file_version_id` = `file_id` WHERE `file_version_id` = 0;

# 20041027 cyberhorse
# done to fix double enries in sysvals table
# won't be possible until values are manually pruned first ...
ALTER TABLE `sysvals` ADD UNIQUE (
`sysval_title`
);

ALTER TABLE `syskeys` ADD UNIQUE (
`syskey_name`
);

# 20041103
# fixed naming conevntion for the following
# do not show events on non-working days
# see 20040808
ALTER TABLE `events` DROP `events_cwd`;
ALTER TABLE `events` ADD `event_cwd` tinyint(3) default '0';

# 20041110
# Fix for stripping of decimals in budget figures
#
ALTER TABLE `projects` CHANGE `project_target_budget` `project_target_budget` DECIMAL(10,2) default '0.00';
ALTER TABLE `projects` CHANGE `project_actual_budget` `project_actual_budget` DECIMAL(10,2) default '0.00';
ALTER TABLE `tasks` CHANGE `task_target_budget` `task_target_budget` DECIMAL(10,2) default '0.00';

# 20041204
# Added new fields for contacts
#
ALTER TABLE `contacts` ADD `contact_job` VARCHAR( 255 ) NOT NULL ,
ADD `contact_jabber` VARCHAR( 255 ) NOT NULL ,
ADD `contact_msn` VARCHAR( 255 ) NOT NULL ,
ADD `contact_yahoo` VARCHAR( 255 ) NOT NULL;

# 20041209
# table used for billing.
CREATE TABLE `billingcode` (
  `billingcode_id` bigint(20) NOT NULL auto_increment,
  `billingcode_name` varchar(25) NOT NULL default '',
  `billingcode_value` float NOT NULL default '0',
  `billingcode_desc` varchar(255) NOT NULL default '',
  `billingcode_status` int(1) NOT NULL default '0',
  `company_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`billingcode_id`)
) TYPE=MyISAM;

# 20050125
# Session handling table.
CREATE TABLE `sessions` (
	`session_id` varchar(40) NOT NULL default '',
	`session_data` LONGBLOB,
	`session_updated` TIMESTAMP,
	`session_created` DATETIME NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY (`session_id`),
	KEY (`session_updated`),
	KEY (`session_created`)
) TYPE=MyISAM;

# 20050216
# Added logging the IP of a user
ALTER TABLE `user_access_log` ADD `user_ip` VARCHAR( 15 ) NOT NULL AFTER `user_id` ;

# 20050216
# Added URL for contacts
ALTER TABLE `contacts` ADD `contact_url` VARCHAR( 255 ) NOT NULL AFTER `contact_icq` ;

# 20050222
# moved many config variables from config-php to a new table
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

INSERT INTO `config` VALUES ('', 'check_legacy_password', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'host_locale', 'en', '', 'text');
INSERT INTO `config` VALUES ('', 'check_overallocation', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'currency_symbol', '$', '', 'text');
INSERT INTO `config` VALUES ('', 'host_style', 'default', '', 'text');
INSERT INTO `config` VALUES ('', 'company_name', 'My Company', '', 'text');
INSERT INTO `config` VALUES ('', 'page_title', 'dotProject', '', 'text');
INSERT INTO `config` VALUES ('', 'site_domain', 'dotproject.net', '', 'text');
INSERT INTO `config` VALUES ('', 'email_prefix', '[dotProject]', '', 'text');
INSERT INTO `config` VALUES ('', 'admin_username', 'admin', '', 'text');
INSERT INTO `config` VALUES ('', 'username_min_len', '4', '', 'text');
INSERT INTO `config` VALUES ('', 'password_min_len', '4', '', 'text');
INSERT INTO `config` VALUES ('', 'show_all_tasks', 'true', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'enable_gantt_charts', 'true', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'jpLocale', '', '', 'text');
INSERT INTO `config` VALUES ('', 'log_changes', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'check_tasks_dates', 'true', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'locale_warn', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'locale_alert', '^', '', 'text');
INSERT INTO `config` VALUES ('', 'daily_working_hours', '8.0', '', 'text');
INSERT INTO `config` VALUES ('', 'display_debug', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'link_tickets_kludge', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'show_all_task_assignees', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'direct_edit_assignment', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'restrict_color_selection', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'cal_day_start', '8', '', 'text');
INSERT INTO `config` VALUES ('', 'cal_day_end', '17', '', 'text');
INSERT INTO `config` VALUES ('', 'cal_day_increment', '15', '', 'text');
INSERT INTO `config` VALUES ('', 'cal_working_days', '1,2,3,4,5', '', 'text');
INSERT INTO `config` VALUES ('', 'cal_day_view_show_minical', 'true', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'restrict_task_time_editing', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'default_view_m', 'calendar', '', 'text');
INSERT INTO `config` VALUES ('', 'default_view_a', 'day_view', '', 'text');
INSERT INTO `config` VALUES ('', 'default_view_tab', '1', '', 'text');
INSERT INTO `config` VALUES ('', 'index_max_file_size', '-1', '', 'text');
INSERT INTO `config` VALUES ('', 'session_handling', 'app', '', 'text');
INSERT INTO `config` VALUES ('', 'session_idle_time', '2d', '', 'text');
INSERT INTO `config` VALUES ('', 'session_max_lifetime', '1m', '', 'text');
INSERT INTO `config` VALUES ('', 'debug', '1', '', 'text');
INSERT INTO `config` VALUES ('', 'auto_fields_creation', 'false', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'parser_default', '/usr/bin/strings', '', 'text');
INSERT INTO `config` VALUES ('', 'parser_application/msword', '/usr/bin/strings', '', 'text');
INSERT INTO `config` VALUES ('', 'parser_text/html', '/usr/bin/strings', '', 'text');
INSERT INTO `config` VALUES ('', 'parser_application/pdf', '/usr/bin/pdftotext', '', 'text');


# 20050222
# moved new config variables by cyberhorse from config-php to a new table
INSERT INTO `config` VALUES ('', 'files_ci_preserve_attr', 'true', '', 'checkbox');
INSERT INTO `config` VALUES ('', 'files_show_versions_edit', 'false', '', 'checkbox');

# 20050225
# forum variable
INSERT INTO `config` VALUES('', 'forum_descendent_order', 'true', '', 'checkbox');

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
value_intvalue integer
);

CREATE TABLE custom_fields_lists (
field_id integer,
list_option_id integer,
list_value varchar(250)
);

# 20050302
# ldap system config variables
INSERT INTO config VALUES ('', 'auth_method', 'sql', 'auth', 'select'); 
INSERT INTO config VALUES ('', 'ldap_host', 'localhost', 'ldap', 'text'); 
INSERT INTO config VALUES ('', 'ldap_port', '389', 'ldap', 'text'); 
INSERT INTO config VALUES ('', 'ldap_version', '3', 'ldap', 'text'); 
INSERT INTO config VALUES ('', 'ldap_base_dn', 'dc=saki,dc=com,dc=au', 'ldap', 'text'); 
INSERT INTO config VALUES ('', 'ldap_user_filter', '(uid=%USERNAME%)', 'ldap', 'text'); 

# 20050302
# PostNuke authentication variables
INSERT INTO config VALUES ('', 'postnuke_allow_login', 'true', 'auth', 'checkbox');

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

# change the session management to a list
UPDATE config SET config_group = 'session' WHERE config_name like 'session%';

UPDATE config SET config_type = 'select' WHERE config_name = 'session_handling';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'app'
	FROM config
	WHERE config_name = 'session_handling';

INSERT INTO config_list (`config_id`, `config_list_name`)
  SELECT config_id, 'php'
	FROM config
	WHERE config_name = 'session_handling';

# 20050303
# dropped legacy passwords support
DELETE FROM config WHERE config_name = 'check_legacy_password' LIMIT 1;

# 20050303
# Added new forum indictator
CREATE TABLE `forum_visits` (
  `visit_user` INT(10) NOT NULL DEFAULT 0,
  `visit_forum` INT(10) NOT NULL DEFAULT 0,
  `visit_message` INT(10) NOT NULL DEFAULT 0,
  `visit_date` TIMESTAMP,
  KEY `idx_fv` (`visit_user`, `visit_forum`, `visit_message`)
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


# 20050303
# New mail handling options
INSERT INTO config VALUES (NULL, 'mail_transport', 'php', 'mail', 'select');
INSERT INTO config VALUES (NULL, 'mail_host', 'localhost', 'mail', 'text');
INSERT INTO config VALUES (NULL, 'mail_port', '25', 'mail', 'text');
INSERT INTO config VALUES (NULL, 'mail_auth', 'false', 'mail', 'checkbox');
INSERT INTO config VALUES (NULL, 'mail_user', '', 'mail', 'text');
INSERT INTO config VALUES (NULL, 'mail_pass', '', 'mail', 'text');
INSERT INTO config VALUES (NULL, 'mail_defer', 'false', 'mail', 'checkbox');
INSERT INTO config VALUES (NULL, 'mail_timeout', '30', 'mail', 'text');

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

# 20050303
# Shorten permissions fields to get over MySQL index size problem.
ALTER TABLE `gacl_acl` CHANGE `section_value` `section_value` varchar(80) NOT NULL default 'system';
ALTER TABLE `gacl_acl_sections` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_aco` CHANGE `section_value` `section_value` varchar(80) NOT NULL default '0';
ALTER TABLE `gacl_aco` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_aco_map` CHANGE `section_value` `section_value` varchar(80) NOT NULL default '0';
ALTER TABLE `gacl_aco_map` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_aco_sections` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_aro` CHANGE `section_value` `section_value` varchar(80) NOT NULL default '0';
ALTER TABLE `gacl_aro` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_aro_groups` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_aro_map` CHANGE `section_value` `section_value` varchar(80) NOT NULL default '0';
ALTER TABLE `gacl_aro_sections` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_axo` CHANGE `section_value` `section_value` varchar(80) NOT NULL default '0';
ALTER TABLE `gacl_axo` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_axo_groups` CHANGE `value` `value` varchar(80) NOT NULL default '';
ALTER TABLE `gacl_axo_map` CHANGE `section_value` `section_value` varchar(80) NOT NULL default '0';
ALTER TABLE `gacl_axo_sections` CHANGE `value` `value` varchar(80) NOT NULL default '';

# 20050304
# Version tracking table.  From here on in all updates are done via the installer,
# which uses this table to manage the upgrade process.
CREATE TABLE dpversion (
	code_version varchar(10) not null default '',
	db_version integer not null default '0',
	last_db_update date not null default '0000-00-00',
	last_code_update date not null default '0000-00-00'
);

INSERT INTO dpversion VALUES ('2.0-alpha', 2, '2005-03-04', '2005-03-04');

# 20050307
# Additional LDAP search user and search password fields for Active Directory compatible LDAP authentication
INSERT INTO config VALUES ('', 'ldap_search_user', 'Manager', 'ldap', 'text'); 
INSERT INTO config VALUES ('', 'ldap_search_pass', 'secret', 'ldap', 'text'); 
INSERT INTO config VALUES ('', 'ldap_allow_login', 'true', 'ldap', 'checkbox');

# 20050311
# removed auto fields cfg options
#
DELETE FROM config WHERE config_name = 'auto_fields_creation' LIMIT 1;

# 20050311
# Added indices to forum watch to speed up queries
#
CREATE INDEX `idx_fw1` ON `forum_watch` (`watch_user`, `watch_forum`);
CREATE INDEX `idx_fw2` ON `forum_watch` (`watch_user`, `watch_topic`);

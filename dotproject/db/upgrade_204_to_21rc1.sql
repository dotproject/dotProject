#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

# 20060809
ALTER TABLE `sessions` ADD `session_user` INT DEFAULT '0' NOT NULL AFTER `session_id`;

# 20061119 
# archived status replaces project (in)active flag:
UPDATE `sysvals` SET `sysval_value` = '0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived' WHERE `sysval_title` = 'ProjectStatus' LIMIT 1;
UPDATE `projects` SET `project_status` = 7 WHERE `project_active` = 0;
ALTER TABLE `projects` DROP `project_active`;

# 20061129
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, '1', 'ProjectRequiredFields', 'f.project_name.value.length|<3\r\nf.project_color_identifier.value.length|<3\r\nf.project_company.options[f.project_company.selectedIndex].value|<1' );

# 20070106
# Adding Index to the custom fields value
ALTER TABLE `custom_fields_values` ADD INDEX `idx_cfv_id` ( `value_id` );

# 20070126
ALTER TABLE `files` ADD `file_folder` INT(11) DEFAULT '0' NOT NULL;

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

# 20070210
# Adding the UserType sysval to close issue #1882
INSERT INTO `sysvals` (`sysval_id`, `sysval_key_id`, `sysval_title`, `sysval_value`) VALUES (null, 1, 'UserType', '0|Default User\r\n1|Administrator\r\n2|CEO\r\n3|Director\r\n4|Branch Manager\r\n5|Manager\r\n6|Supervisor\r\n7|Employee');

# 20070412
# Adding the TicketNotify and TicketPriority sysvals to clean up the Ticketsmith module
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TicketNotify', '0|admin@localhost\n1|admin@localhost\n2|admin@localhost\r\n3|admin@localhost\r\n4|admin@localhost');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TicketPriority', '0|Low\n1|Normal\n2|High\n3|Highest\n4|911');
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, 1, 'TicketStatus', '0|Open\n1|Closed\n2|Deleted');
ALTER TABLE `tickets` ADD `ticket_company` INT( 10 ) NOT NULL DEFAULT '0' AFTER `ticket`;
ALTER TABLE `tickets` ADD `ticket_project` INT( 10 ) NOT NULL DEFAULT '0' AFTER `ticket_company` ;
#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

# 20080116
# adding internal company field with departments field to projects view
ALTER TABLE `projects` ADD `project_company_internal` INT( 11 ) NOT NULL AFTER `project_company`;

# 20080415
# adding config value to toggle checking for child tasks when making tasks dynamic
INSERT INTO `config` (`config_id`, `config_name`, `config_value`, `config_group`, `config_type`)
	VALUES (0, 'check_task_empty_dynamic', 'false', '', 'checkbox');

# 20080602
#editing module table, some modules are missing tables, ID fileds, and ID names for permissions
UPDATE `modules` SET `permissions_item_table`='events', `permissions_item_field`='event_id', permissions_item_label='event_title' WHERE mod_directory = 'calendar';
UPDATE `modules` SET `permissions_item_table`='contacts', `permissions_item_field`='contact_id', permissions_item_label='contact_title' WHERE mod_directory = 'contacts';
UPDATE `modules` SET `permissions_item_table`='departments', `permissions_item_field`='dept_id', permissions_item_label='dept_name' WHERE mod_directory = 'departments';
UPDATE `modules` SET `permissions_item_table`='links', `permissions_item_field`='link_id', permissions_item_label='link_name' WHERE mod_directory = 'links';

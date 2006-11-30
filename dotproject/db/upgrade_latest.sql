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
UPDATE `sysvals` SET `sysval_value` = '0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived' WHERE `sysval_id` = 1 LIMIT 1;
UPDATE `projects` SET `project_status` = 7 WHERE `project_active` = 0;
ALTER TABLE `projects` DROP `project_active`;

# 20061129
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, '1', 'TaskRequiredFields', 'task_name.value.length|<3' );
INSERT INTO `sysvals` ( `sysval_id` , `sysval_key_id` , `sysval_title` , `sysval_value` ) VALUES (null, '1', 'ProjectRequiredFields', 'project_name.value.length|<3\r\nproject_color_identifier.value.length|<3\r\nproject_company.options[f.project_company.selectedIndex].value|<1' );


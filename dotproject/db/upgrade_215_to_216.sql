#
# $Id: upgrade_latest.sql 6155 2012-06-05 03:10:32Z ajdonnison $
#
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

# 20110203
# Add indexes to task departments and task contacts
ALTER TABLE `%dbprefix%task_departments` ADD KEY `idx_task_departments` (`task_id`);
ALTER TABLE `%dbprefix%task_contacts` ADD KEY `idx_task_contacts` (`task_id`);

# 20110619
# Add index to optimize tasks list (slowest query)
ALTER TABLE  `%dbprefix%user_tasks` ADD KEY `idx_user_tasks` ( `task_id` );

# 20120128
# Replace stupid defaults with sane ones
UPDATE `%dbprefix%contacts` SET `contact_email` = 'admin@example.com' WHERE `contact_id` = 1 AND `contact_email` = 'admin@localhost';
UPDATE `%dbprefix%config` SET `config_value` = 'example.com' WHERE `config_name` = 'site_domain' AND `config_value` = 'dotproject.net';
UPDATE `%dbprefix%sysvals` SET `sysval_value` = '0|admin@example.com\n1|admin@example.com\n2|admin@example.com\r\n3|admin@example.com\r\n4|admin@example.com' WHERE `sysval_title` = 'TicketNotify' AND `sysval_value` = '0|admin@localhost\n1|admin@localhost\n2|admin@localhost\r\n3|admin@localhost\r\n4|admin@localhost';

# 20120605
# Extend the key size on the sessions table
ALTER TABLE `%dbprefix%sessions` CHANGE `session_id` `session_id` VARCHAR(60) NOT NULL;

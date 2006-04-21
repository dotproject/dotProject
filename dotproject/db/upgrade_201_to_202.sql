#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

#20060311
# Check task dates fix
UPDATE `config` SET `config_name` = 'check_task_dates' WHERE `config_name` = 'check_tasks_dates';

# 20060321
# Backport of task reminders.
INSERT INTO `config` VALUES ('', 'task_reminder_control', 'false', 'task_reminder', 'checkbox');
INSERT INTO `config` VALUES ('', 'task_reminder_days_before', '1', 'task_reminder', 'text');
INSERT INTO `config` VALUES ('', 'task_reminder_repeat', '100', 'task_reminder', 'text');

# 20060421
# Fixups for grouping of configs
UPDATE `config` SET `config_group` = 'ldap' WHERE `config_name` LIKE 'ldap%';


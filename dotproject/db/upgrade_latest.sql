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

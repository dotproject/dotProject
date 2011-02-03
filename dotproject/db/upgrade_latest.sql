#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

# 20110203
# Add indexes to task departments and task contacts
ALTER TABLE `%dbprefix%task_departments` ADD KEY `idx_task_departments` (`task_id`);
ALTER TABLE `%dbprefix%task_contacts` ADD KEY `idx_task_contacts` (`task_id`);

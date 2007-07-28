#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

#20070728
#altered the data type to prevent the 99.99% misrounding issue
ALTER TABLE `tasks` MODIFY `task_percent_complete` tinyint(4) DEFAULT '0';
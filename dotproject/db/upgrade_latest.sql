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

#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#
#20071113
#20080622 - change to allow for a db table prefix
# Remove the NOT NULL clause from company_description to avoid issues on win plaforms
ALTER TABLE `%dbprefix%companies` MODIFY `company_description` text;

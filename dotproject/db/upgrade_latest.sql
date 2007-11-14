#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#
#20071113
# Remove the NOT NULL clause from company_description to avoid issues on win plaforms
ALTER TABLE `companies` MODIFY `company_description` `company_description` text;

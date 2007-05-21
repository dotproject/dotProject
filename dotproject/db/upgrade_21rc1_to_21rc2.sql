#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

#20070513
#removed jpLocale variable - use user locale instead.
DELETE FROM `config` WHERE config_name = 'jpLocale';
#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#
# 20050316
# Remove config elements that are no longer used.
DELETE FROM `config` where `config_name` = 'cal_day_view_show_minical';
DELETE FROM `config` where `config_name` = 'show_all_tasks';

#
# 20050318
# Change indexes on files module to fix bug 616
ALTER TABLE `files_index` DROP INDEX `idx_wcnt`;
ALTER TABLE `files_index` CHANGE `word_placement` `word_placement` INT( 11 ) DEFAULT '0' NOT NULL;
ALTER TABLE `files_index` DROP PRIMARY KEY;
ALTER TABLE `files_index` ADD PRIMARY KEY( `file_id`, `word`, `word_placement`);
#

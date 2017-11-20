#
# $Id: upgrade_latest.sql 6192 2013-01-05 12:31:23Z ajdonnison $
#
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

# 20130105
INSERT INTO `%dbprefix%config` VALUES (0, 'task_reminder_batch', 'false', 'task_reminder', 'checkbox');
ALTER TABLE `%dbprefix%event_queue` DROP `queue_module_type`, ADD `queue_batched` INTEGER NOT NULL DEFAULT '0', DROP KEY `queue_start`;
ALTER TABLE `%dbprefix%event_queue` ADD KEY `queue_start`(`queue_batched`, `queue_start`);

INSERT INTO `%dbprefix%user_preferences` VALUES ('0', 'USEDIGESTS', '0');
#
# Probably need to update all of the config variables
UPDATE `%dbprefix%config` SET `config_group` = 'auth' WHERE `config_name` IN ('username_min_len', 'password_min_len' );
UPDATE `%dbprefix%config` SET `config_group` = 'ui' WHERE `config_name` IN ('host_locale', 'currency_symbol', 'host_style', 'company_name', 'page_title', 'site_domain', 'email_prefix', 'admin_username', 'locale_warn', 'locale_alert', 'display_debug', 'restrict_color_selection', 'default_view_m', 'default_view_a', 'default_view_tab', 'log_changes', 'debug' );
UPDATE `%dbprefix%config` SET `config_group` = 'tasks' WHERE `config_name` IN ('check_overallocation', 'enable_gantt_charts', 'check_task_dates', 'check_task_empty_dynamic', 'daily_working_hours', 'link_tickets_kludge', 'show_all_task_assignees', 'restrict_task_time_editing', 'reset_memory_limit', 'direct_edit_assignment' );
UPDATE `%dbprefix%config` SET `config_group` = 'calendar' WHERE `config_name` IN ( 'cal_day_view_show_minical', 'cal_day_start', 'cal_day_end', 'cal_day_increment', 'cal_working_days');
UPDATE `%dbprefix%config` SET `config_group` = 'file' WHERE `config_name` IN ( 'parser_default', 'parser_application/msword', 'parser_text/html', 'parser_application/pdf', 'index_max_file_size', 'files_ci_preserve_attr', 'files_show_versions_edit' );
#

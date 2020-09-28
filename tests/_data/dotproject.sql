-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2020 at 05:40 PM
-- Server version: 8.0.18
-- PHP Version: 5.6.39

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `dotp420`
--

-- --------------------------------------------------------

--
-- Table structure for table `dotp_billingcode`
--

CREATE TABLE `dotp_billingcode` (
  `billingcode_id` bigint(20) NOT NULL,
  `billingcode_name` varchar(25) NOT NULL DEFAULT '',
  `billingcode_value` float NOT NULL DEFAULT '0',
  `billingcode_desc` varchar(255) NOT NULL DEFAULT '',
  `billingcode_status` int(1) NOT NULL DEFAULT '0',
  `company_id` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_common_notes`
--

CREATE TABLE `dotp_common_notes` (
  `note_id` int(10) UNSIGNED NOT NULL,
  `note_author` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `note_module` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `note_record_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `note_category` int(3) UNSIGNED NOT NULL DEFAULT '0',
  `note_title` varchar(100) NOT NULL DEFAULT '',
  `note_body` text NOT NULL,
  `note_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_hours` float NOT NULL DEFAULT '0',
  `note_code` varchar(8) NOT NULL DEFAULT '',
  `note_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note_modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_companies`
--

CREATE TABLE `dotp_companies` (
  `company_id` int(10) NOT NULL,
  `company_module` int(10) NOT NULL DEFAULT '0',
  `company_name` varchar(100) DEFAULT '',
  `company_phone1` varchar(30) DEFAULT '',
  `company_phone2` varchar(30) DEFAULT '',
  `company_fax` varchar(30) DEFAULT '',
  `company_address1` varchar(50) DEFAULT '',
  `company_address2` varchar(50) DEFAULT '',
  `company_city` varchar(30) DEFAULT '',
  `company_state` varchar(30) DEFAULT '',
  `company_zip` varchar(11) DEFAULT '',
  `company_primary_url` varchar(255) DEFAULT '',
  `company_owner` int(11) NOT NULL DEFAULT '0',
  `company_description` text,
  `company_type` int(3) NOT NULL DEFAULT '0',
  `company_email` varchar(255) DEFAULT NULL,
  `company_custom` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_config`
--

CREATE TABLE `dotp_config` (
  `config_id` int(11) NOT NULL,
  `config_name` varchar(127) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  `config_group` varchar(255) NOT NULL DEFAULT '',
  `config_type` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_config`
--

INSERT INTO `dotp_config` (`config_id`, `config_name`, `config_value`, `config_group`, `config_type`) VALUES
(1, 'host_locale', 'en', 'ui', 'text'),
(2, 'check_overallocation', 'false', 'tasks', 'checkbox'),
(3, 'currency_symbol', '$', 'ui', 'text'),
(4, 'host_style', 'default', 'ui', 'text'),
(5, 'company_name', 'Company', 'ui', 'text'),
(6, 'page_title', 'My Project', 'ui', 'text'),
(7, 'site_domain', 'example.com', 'ui', 'text'),
(8, 'email_prefix', '[dotProject]', 'ui', 'text'),
(9, 'admin_username', 'admin', 'ui', 'text'),
(10, 'username_min_len', '4', 'auth', 'text'),
(11, 'password_min_len', '4', 'auth', 'text'),
(12, 'enable_gantt_charts', 'true', 'tasks', 'checkbox'),
(13, 'log_changes', 'false', '', 'checkbox'),
(14, 'check_task_dates', 'true', 'tasks', 'checkbox'),
(15, 'check_task_empty_dynamic', 'false', 'tasks', 'checkbox'),
(16, 'locale_warn', 'false', 'ui', 'checkbox'),
(17, 'locale_alert', '^', 'ui', 'text'),
(18, 'daily_working_hours', '8.0', 'tasks', 'text'),
(19, 'display_debug', 'false', 'ui', 'checkbox'),
(20, 'link_tickets_kludge', 'false', 'tasks', 'checkbox'),
(21, 'show_all_task_assignees', 'false', 'tasks', 'checkbox'),
(22, 'direct_edit_assignment', 'false', 'tasks', 'checkbox'),
(23, 'restrict_color_selection', 'false', 'ui', 'checkbox'),
(24, 'cal_day_view_show_minical', 'true', 'calendar', 'checkbox'),
(25, 'cal_day_start', '8', 'calendar', 'text'),
(26, 'cal_day_end', '17', 'calendar', 'text'),
(27, 'cal_day_increment', '15', 'calendar', 'text'),
(28, 'cal_working_days', '1,2,3,4,5', 'calendar', 'text'),
(29, 'restrict_task_time_editing', 'false', 'tasks', 'checkbox'),
(30, 'default_view_m', 'calendar', 'ui', 'text'),
(31, 'default_view_a', 'day_view', 'ui', 'text'),
(32, 'default_view_tab', '1', 'ui', 'text'),
(33, 'index_max_file_size', '-1', 'file', 'text'),
(34, 'session_handling', 'app', 'session', 'select'),
(35, 'session_idle_time', '2d', 'session', 'text'),
(36, 'session_max_lifetime', '1m', 'session', 'text'),
(37, 'debug', '1', '', 'text'),
(38, 'parser_default', '/usr/bin/strings', 'file', 'text'),
(39, 'parser_application/msword', '/usr/bin/strings', 'file', 'text'),
(40, 'parser_text/html', '/usr/bin/strings', 'file', 'text'),
(41, 'parser_application/pdf', '/usr/bin/pdftotext', 'file', 'text'),
(42, 'files_ci_preserve_attr', 'true', 'file', 'checkbox'),
(43, 'files_show_versions_edit', 'false', 'file', 'checkbox'),
(44, 'auth_method', 'sql', 'auth', 'select'),
(45, 'ldap_host', 'localhost', 'ldap', 'text'),
(46, 'ldap_port', '389', 'ldap', 'text'),
(47, 'ldap_version', '3', 'ldap', 'text'),
(48, 'ldap_base_dn', 'dc=saki,dc=com,dc=au', 'ldap', 'text'),
(49, 'ldap_user_filter', '(uid=%USERNAME%)', 'ldap', 'text'),
(50, 'postnuke_allow_login', 'true', 'auth', 'checkbox'),
(51, 'reset_memory_limit', '32M', 'tasks', 'text'),
(52, 'mail_transport', 'php', 'mail', 'select'),
(53, 'mail_host', 'localhost', 'mail', 'text'),
(54, 'mail_port', '25', 'mail', 'text'),
(55, 'mail_auth', 'false', 'mail', 'checkbox'),
(56, 'mail_user', '', 'mail', 'text'),
(57, 'mail_pass', '', 'mail', 'password'),
(58, 'mail_defer', 'false', 'mail', 'checkbox'),
(59, 'mail_timeout', '30', 'mail', 'text'),
(60, 'session_gc_scan_queue', 'false', 'session', 'checkbox'),
(61, 'task_reminder_control', 'false', 'task_reminder', 'checkbox'),
(62, 'task_reminder_days_before', '1', 'task_reminder', 'text'),
(63, 'task_reminder_repeat', '100', 'task_reminder', 'text'),
(64, 'gacl_cache', 'false', 'gacl', 'checkbox'),
(65, 'gacl_expire', 'true', 'gacl', 'checkbox'),
(66, 'gacl_cache_dir', '/tmp', 'gacl', 'text'),
(67, 'gacl_timeout', '600', 'gacl', 'text'),
(68, 'mail_smtp_tls', 'false', 'mail', 'checkbox'),
(69, 'ldap_search_user', 'Manager', 'ldap', 'text'),
(70, 'ldap_search_pass', 'secret', 'ldap', 'password'),
(71, 'ldap_allow_login', 'true', 'ldap', 'checkbox'),
(72, 'user_contact_inactivate', 'true', 'auth', 'checkbox'),
(73, 'user_contact_activate', 'false', 'auth', 'checkbox'),
(74, 'task_reminder_batch', 'false', 'task_reminder', 'checkbox');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_config_list`
--

CREATE TABLE `dotp_config_list` (
  `config_list_id` int(11) NOT NULL,
  `config_id` int(11) NOT NULL DEFAULT '0',
  `config_list_name` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_config_list`
--

INSERT INTO `dotp_config_list` (`config_list_id`, `config_id`, `config_list_name`) VALUES
(1, 44, 'sql'),
(2, 44, 'ldap'),
(3, 44, 'pn'),
(4, 34, 'app'),
(5, 34, 'php'),
(6, 52, 'php'),
(7, 52, 'smtp');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_contacts`
--

CREATE TABLE `dotp_contacts` (
  `contact_id` int(11) NOT NULL,
  `contact_first_name` varchar(30) DEFAULT NULL,
  `contact_last_name` varchar(30) DEFAULT NULL,
  `contact_order_by` varchar(30) NOT NULL DEFAULT '',
  `contact_title` varchar(50) DEFAULT NULL,
  `contact_birthday` date DEFAULT NULL,
  `contact_job` varchar(255) DEFAULT NULL,
  `contact_company` varchar(100) NOT NULL DEFAULT '',
  `contact_department` tinytext,
  `contact_type` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_email2` varchar(255) DEFAULT NULL,
  `contact_url` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contact_phone2` varchar(30) DEFAULT NULL,
  `contact_fax` varchar(30) DEFAULT NULL,
  `contact_mobile` varchar(30) DEFAULT NULL,
  `contact_address1` varchar(60) DEFAULT NULL,
  `contact_address2` varchar(60) DEFAULT NULL,
  `contact_city` varchar(30) DEFAULT NULL,
  `contact_state` varchar(30) DEFAULT NULL,
  `contact_zip` varchar(11) DEFAULT NULL,
  `contact_country` varchar(30) DEFAULT NULL,
  `contact_jabber` varchar(255) DEFAULT NULL,
  `contact_icq` varchar(20) DEFAULT NULL,
  `contact_msn` varchar(255) DEFAULT NULL,
  `contact_yahoo` varchar(255) DEFAULT NULL,
  `contact_aol` varchar(30) DEFAULT NULL,
  `contact_notes` text,
  `contact_project` int(11) NOT NULL DEFAULT '0',
  `contact_icon` varchar(20) DEFAULT 'obj/contact',
  `contact_owner` int(10) UNSIGNED DEFAULT '0',
  `contact_private` tinyint(3) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_contacts`
--

INSERT INTO `dotp_contacts` (`contact_id`, `contact_first_name`, `contact_last_name`, `contact_order_by`, `contact_title`, `contact_birthday`, `contact_job`, `contact_company`, `contact_department`, `contact_type`, `contact_email`, `contact_email2`, `contact_url`, `contact_phone`, `contact_phone2`, `contact_fax`, `contact_mobile`, `contact_address1`, `contact_address2`, `contact_city`, `contact_state`, `contact_zip`, `contact_country`, `contact_jabber`, `contact_icq`, `contact_msn`, `contact_yahoo`, `contact_aol`, `contact_notes`, `contact_project`, `contact_icon`, `contact_owner`, `contact_private`) VALUES
(1, 'Admin', 'Person', '', NULL, NULL, NULL, '', NULL, NULL, 'admin@127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'obj/contact', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_custom_fields_lists`
--

CREATE TABLE `dotp_custom_fields_lists` (
  `field_id` int(11) DEFAULT NULL,
  `list_option_id` int(11) DEFAULT NULL,
  `list_value` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_custom_fields_struct`
--

CREATE TABLE `dotp_custom_fields_struct` (
  `field_id` int(11) NOT NULL,
  `field_module` varchar(30) DEFAULT NULL,
  `field_page` varchar(30) DEFAULT NULL,
  `field_htmltype` varchar(20) DEFAULT NULL,
  `field_datatype` varchar(20) DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `field_name` varchar(100) DEFAULT NULL,
  `field_extratags` varchar(250) DEFAULT NULL,
  `field_description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_custom_fields_values`
--

CREATE TABLE `dotp_custom_fields_values` (
  `value_id` int(11) DEFAULT NULL,
  `value_module` varchar(30) DEFAULT NULL,
  `value_object_id` int(11) DEFAULT NULL,
  `value_field_id` int(11) DEFAULT NULL,
  `value_charvalue` varchar(250) DEFAULT NULL,
  `value_intvalue` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_departments`
--

CREATE TABLE `dotp_departments` (
  `dept_id` int(10) UNSIGNED NOT NULL,
  `dept_parent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dept_company` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dept_name` tinytext NOT NULL,
  `dept_phone` varchar(30) DEFAULT NULL,
  `dept_fax` varchar(30) DEFAULT NULL,
  `dept_address1` varchar(30) DEFAULT NULL,
  `dept_address2` varchar(30) DEFAULT NULL,
  `dept_city` varchar(30) DEFAULT NULL,
  `dept_state` varchar(30) DEFAULT NULL,
  `dept_zip` varchar(11) DEFAULT NULL,
  `dept_url` varchar(25) DEFAULT NULL,
  `dept_desc` text,
  `dept_owner` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Department heirarchy under a company';

-- --------------------------------------------------------

--
-- Table structure for table `dotp_dotpermissions`
--

CREATE TABLE `dotp_dotpermissions` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(80) NOT NULL DEFAULT '',
  `section` varchar(80) NOT NULL DEFAULT '',
  `axo` varchar(80) NOT NULL DEFAULT '',
  `permission` varchar(80) NOT NULL DEFAULT '',
  `allow` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_dotpermissions`
--

INSERT INTO `dotp_dotpermissions` (`acl_id`, `user_id`, `section`, `axo`, `permission`, `allow`, `priority`, `enabled`) VALUES
(12, '1', 'sys', 'acl', 'access', 1, 3, 1),
(11, '1', 'app', 'admin', 'access', 1, 4, 1),
(11, '1', 'app', 'calendar', 'access', 1, 4, 1),
(11, '1', 'app', 'events', 'access', 1, 4, 1),
(11, '1', 'app', 'companies', 'access', 1, 4, 1),
(11, '1', 'app', 'contacts', 'access', 1, 4, 1),
(11, '1', 'app', 'departments', 'access', 1, 4, 1),
(11, '1', 'app', 'files', 'access', 1, 4, 1),
(11, '1', 'app', 'file_folders', 'access', 1, 4, 1),
(11, '1', 'app', 'forums', 'access', 1, 4, 1),
(11, '1', 'app', 'help', 'access', 1, 4, 1),
(11, '1', 'app', 'projects', 'access', 1, 4, 1),
(11, '1', 'app', 'system', 'access', 1, 4, 1),
(11, '1', 'app', 'tasks', 'access', 1, 4, 1),
(11, '1', 'app', 'task_log', 'access', 1, 4, 1),
(11, '1', 'app', 'ticketsmith', 'access', 1, 4, 1),
(11, '1', 'app', 'public', 'access', 1, 4, 1),
(11, '1', 'app', 'roles', 'access', 1, 4, 1),
(11, '1', 'app', 'users', 'access', 1, 4, 1),
(11, '1', 'app', 'admin', 'add', 1, 4, 1),
(11, '1', 'app', 'calendar', 'add', 1, 4, 1),
(11, '1', 'app', 'events', 'add', 1, 4, 1),
(11, '1', 'app', 'companies', 'add', 1, 4, 1),
(11, '1', 'app', 'contacts', 'add', 1, 4, 1),
(11, '1', 'app', 'departments', 'add', 1, 4, 1),
(11, '1', 'app', 'files', 'add', 1, 4, 1),
(11, '1', 'app', 'file_folders', 'add', 1, 4, 1),
(11, '1', 'app', 'forums', 'add', 1, 4, 1),
(11, '1', 'app', 'help', 'add', 1, 4, 1),
(11, '1', 'app', 'projects', 'add', 1, 4, 1),
(11, '1', 'app', 'system', 'add', 1, 4, 1),
(11, '1', 'app', 'tasks', 'add', 1, 4, 1),
(11, '1', 'app', 'task_log', 'add', 1, 4, 1),
(11, '1', 'app', 'ticketsmith', 'add', 1, 4, 1),
(11, '1', 'app', 'public', 'add', 1, 4, 1),
(11, '1', 'app', 'roles', 'add', 1, 4, 1),
(11, '1', 'app', 'users', 'add', 1, 4, 1),
(11, '1', 'app', 'admin', 'delete', 1, 4, 1),
(11, '1', 'app', 'calendar', 'delete', 1, 4, 1),
(11, '1', 'app', 'events', 'delete', 1, 4, 1),
(11, '1', 'app', 'companies', 'delete', 1, 4, 1),
(11, '1', 'app', 'contacts', 'delete', 1, 4, 1),
(11, '1', 'app', 'departments', 'delete', 1, 4, 1),
(11, '1', 'app', 'files', 'delete', 1, 4, 1),
(11, '1', 'app', 'file_folders', 'delete', 1, 4, 1),
(11, '1', 'app', 'forums', 'delete', 1, 4, 1),
(11, '1', 'app', 'help', 'delete', 1, 4, 1),
(11, '1', 'app', 'projects', 'delete', 1, 4, 1),
(11, '1', 'app', 'system', 'delete', 1, 4, 1),
(11, '1', 'app', 'tasks', 'delete', 1, 4, 1),
(11, '1', 'app', 'task_log', 'delete', 1, 4, 1),
(11, '1', 'app', 'ticketsmith', 'delete', 1, 4, 1),
(11, '1', 'app', 'public', 'delete', 1, 4, 1),
(11, '1', 'app', 'roles', 'delete', 1, 4, 1),
(11, '1', 'app', 'users', 'delete', 1, 4, 1),
(11, '1', 'app', 'admin', 'edit', 1, 4, 1),
(11, '1', 'app', 'calendar', 'edit', 1, 4, 1),
(11, '1', 'app', 'events', 'edit', 1, 4, 1),
(11, '1', 'app', 'companies', 'edit', 1, 4, 1),
(11, '1', 'app', 'contacts', 'edit', 1, 4, 1),
(11, '1', 'app', 'departments', 'edit', 1, 4, 1),
(11, '1', 'app', 'files', 'edit', 1, 4, 1),
(11, '1', 'app', 'file_folders', 'edit', 1, 4, 1),
(11, '1', 'app', 'forums', 'edit', 1, 4, 1),
(11, '1', 'app', 'help', 'edit', 1, 4, 1),
(11, '1', 'app', 'projects', 'edit', 1, 4, 1),
(11, '1', 'app', 'system', 'edit', 1, 4, 1),
(11, '1', 'app', 'tasks', 'edit', 1, 4, 1),
(11, '1', 'app', 'task_log', 'edit', 1, 4, 1),
(11, '1', 'app', 'ticketsmith', 'edit', 1, 4, 1),
(11, '1', 'app', 'public', 'edit', 1, 4, 1),
(11, '1', 'app', 'roles', 'edit', 1, 4, 1),
(11, '1', 'app', 'users', 'edit', 1, 4, 1),
(11, '1', 'app', 'admin', 'view', 1, 4, 1),
(11, '1', 'app', 'calendar', 'view', 1, 4, 1),
(11, '1', 'app', 'events', 'view', 1, 4, 1),
(11, '1', 'app', 'companies', 'view', 1, 4, 1),
(11, '1', 'app', 'contacts', 'view', 1, 4, 1),
(11, '1', 'app', 'departments', 'view', 1, 4, 1),
(11, '1', 'app', 'files', 'view', 1, 4, 1),
(11, '1', 'app', 'file_folders', 'view', 1, 4, 1),
(11, '1', 'app', 'forums', 'view', 1, 4, 1),
(11, '1', 'app', 'help', 'view', 1, 4, 1),
(11, '1', 'app', 'projects', 'view', 1, 4, 1),
(11, '1', 'app', 'system', 'view', 1, 4, 1),
(11, '1', 'app', 'tasks', 'view', 1, 4, 1),
(11, '1', 'app', 'task_log', 'view', 1, 4, 1),
(11, '1', 'app', 'ticketsmith', 'view', 1, 4, 1),
(11, '1', 'app', 'public', 'view', 1, 4, 1),
(11, '1', 'app', 'roles', 'view', 1, 4, 1),
(11, '1', 'app', 'users', 'view', 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_dpversion`
--

CREATE TABLE `dotp_dpversion` (
  `code_version` varchar(10) NOT NULL DEFAULT '',
  `db_version` int(11) NOT NULL DEFAULT '0',
  `last_db_update` date NOT NULL DEFAULT '0000-00-00',
  `last_code_update` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_dpversion`
--

INSERT INTO `dotp_dpversion` (`code_version`, `db_version`, `last_db_update`, `last_code_update`) VALUES
('2.2.0', 2, '2013-01-05', '2013-07-27');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_events`
--

CREATE TABLE `dotp_events` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL DEFAULT '',
  `event_start_date` datetime DEFAULT NULL,
  `event_end_date` datetime DEFAULT NULL,
  `event_parent` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `event_description` text,
  `event_times_recuring` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `event_recurs` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `event_remind` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `event_icon` varchar(20) DEFAULT 'obj/event',
  `event_owner` int(11) DEFAULT '0',
  `event_project` int(11) DEFAULT '0',
  `event_private` tinyint(3) DEFAULT '0',
  `event_type` tinyint(3) DEFAULT '0',
  `event_cwd` tinyint(3) DEFAULT '0',
  `event_notify` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_event_queue`
--

CREATE TABLE `dotp_event_queue` (
  `queue_id` int(11) NOT NULL,
  `queue_start` int(11) NOT NULL DEFAULT '0',
  `queue_type` varchar(40) NOT NULL DEFAULT '',
  `queue_repeat_interval` int(11) NOT NULL DEFAULT '0',
  `queue_repeat_count` int(11) NOT NULL DEFAULT '0',
  `queue_data` longblob NOT NULL,
  `queue_callback` varchar(127) NOT NULL DEFAULT '',
  `queue_owner` int(11) NOT NULL DEFAULT '0',
  `queue_origin_id` int(11) NOT NULL DEFAULT '0',
  `queue_module` varchar(40) NOT NULL DEFAULT '',
  `queue_batched` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_files`
--

CREATE TABLE `dotp_files` (
  `file_id` int(11) NOT NULL,
  `file_real_filename` varchar(255) NOT NULL DEFAULT '',
  `file_folder` int(11) NOT NULL DEFAULT '0',
  `file_project` int(11) NOT NULL DEFAULT '0',
  `file_task` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_parent` int(11) DEFAULT '0',
  `file_description` text,
  `file_type` varchar(100) DEFAULT NULL,
  `file_owner` int(11) DEFAULT '0',
  `file_date` datetime DEFAULT NULL,
  `file_size` int(11) DEFAULT '0',
  `file_version` float NOT NULL DEFAULT '0',
  `file_icon` varchar(20) DEFAULT 'obj/',
  `file_category` int(11) DEFAULT '0',
  `file_checkout` varchar(255) NOT NULL DEFAULT '',
  `file_co_reason` text,
  `file_version_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_files_index`
--

CREATE TABLE `dotp_files_index` (
  `file_id` int(11) NOT NULL DEFAULT '0',
  `word` varchar(50) NOT NULL DEFAULT '',
  `word_placement` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_file_folders`
--

CREATE TABLE `dotp_file_folders` (
  `file_folder_id` int(11) NOT NULL,
  `file_folder_parent` int(11) NOT NULL DEFAULT '0',
  `file_folder_name` varchar(255) NOT NULL DEFAULT '',
  `file_folder_description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_forums`
--

CREATE TABLE `dotp_forums` (
  `forum_id` int(11) NOT NULL,
  `forum_project` int(11) NOT NULL DEFAULT '0',
  `forum_status` tinyint(4) NOT NULL DEFAULT '-1',
  `forum_owner` int(11) NOT NULL DEFAULT '0',
  `forum_name` varchar(50) NOT NULL DEFAULT '',
  `forum_create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `forum_last_date` datetime DEFAULT '0000-00-00 00:00:00',
  `forum_last_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `forum_message_count` int(11) NOT NULL DEFAULT '0',
  `forum_description` varchar(255) DEFAULT NULL,
  `forum_moderated` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_forum_messages`
--

CREATE TABLE `dotp_forum_messages` (
  `message_id` int(11) NOT NULL,
  `message_forum` int(11) NOT NULL DEFAULT '0',
  `message_parent` int(11) NOT NULL DEFAULT '0',
  `message_author` int(11) NOT NULL DEFAULT '0',
  `message_editor` int(11) NOT NULL DEFAULT '0',
  `message_title` varchar(255) NOT NULL DEFAULT '',
  `message_date` datetime DEFAULT '0000-00-00 00:00:00',
  `message_body` text,
  `message_published` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_forum_visits`
--

CREATE TABLE `dotp_forum_visits` (
  `visit_user` int(10) NOT NULL DEFAULT '0',
  `visit_forum` int(10) NOT NULL DEFAULT '0',
  `visit_message` int(10) NOT NULL DEFAULT '0',
  `visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_forum_watch`
--

CREATE TABLE `dotp_forum_watch` (
  `watch_user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `watch_forum` int(10) UNSIGNED DEFAULT NULL,
  `watch_topic` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Links users to the forums/messages they are watching';

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_acl`
--

CREATE TABLE `dotp_gacl_acl` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT 'system',
  `allow` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0',
  `return_value` longtext,
  `note` longtext,
  `updated_date` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_acl`
--

INSERT INTO `dotp_gacl_acl` (`id`, `section_value`, `allow`, `enabled`, `return_value`, `note`, `updated_date`) VALUES
(10, 'user', 1, 1, NULL, NULL, 1601046635),
(11, 'user', 1, 1, NULL, NULL, 1601046635),
(12, 'user', 1, 1, NULL, NULL, 1601046635),
(13, 'user', 1, 1, NULL, NULL, 1601046635),
(14, 'user', 1, 1, NULL, NULL, 1601046635),
(15, 'user', 1, 1, NULL, NULL, 1601046635),
(16, 'user', 1, 1, NULL, NULL, 1601046635);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_acl_sections`
--

CREATE TABLE `dotp_gacl_acl_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_acl_sections`
--

INSERT INTO `dotp_gacl_acl_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(1, 'system', 1, 'System', 0),
(2, 'user', 2, 'User', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_acl_seq`
--

CREATE TABLE `dotp_gacl_acl_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_acl_seq`
--

INSERT INTO `dotp_gacl_acl_seq` (`id`) VALUES
(16);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aco`
--

CREATE TABLE `dotp_gacl_aco` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aco`
--

INSERT INTO `dotp_gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'system', 'login', 1, 'Login', 0),
(11, 'application', 'access', 1, 'Access', 0),
(12, 'application', 'view', 2, 'View', 0),
(13, 'application', 'add', 3, 'Add', 0),
(14, 'application', 'edit', 4, 'Edit', 0),
(15, 'application', 'delete', 5, 'Delete', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aco_map`
--

CREATE TABLE `dotp_gacl_aco_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aco_map`
--

INSERT INTO `dotp_gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES
(10, 'system', 'login'),
(11, 'application', 'access'),
(11, 'application', 'add'),
(11, 'application', 'delete'),
(11, 'application', 'edit'),
(11, 'application', 'view'),
(12, 'application', 'access'),
(13, 'application', 'access'),
(13, 'application', 'view'),
(14, 'application', 'access'),
(15, 'application', 'access'),
(15, 'application', 'add'),
(15, 'application', 'delete'),
(15, 'application', 'edit'),
(15, 'application', 'view'),
(16, 'application', 'access'),
(16, 'application', 'view');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aco_sections`
--

CREATE TABLE `dotp_gacl_aco_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aco_sections`
--

INSERT INTO `dotp_gacl_aco_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'system', 1, 'System', 0),
(11, 'application', 2, 'Application', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aco_sections_seq`
--

CREATE TABLE `dotp_gacl_aco_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aco_sections_seq`
--

INSERT INTO `dotp_gacl_aco_sections_seq` (`id`) VALUES
(11);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aco_seq`
--

CREATE TABLE `dotp_gacl_aco_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aco_seq`
--

INSERT INTO `dotp_gacl_aco_seq` (`id`) VALUES
(15);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro`
--

CREATE TABLE `dotp_gacl_aro` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro`
--

INSERT INTO `dotp_gacl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'user', '1', 1, 'admin', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_groups`
--

CREATE TABLE `dotp_gacl_aro_groups` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro_groups`
--

INSERT INTO `dotp_gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES
(10, 0, 1, 10, 'Roles', 'role'),
(11, 10, 2, 3, 'Administrator', 'admin'),
(12, 10, 4, 5, 'Anonymous', 'anon'),
(13, 10, 6, 7, 'Guest', 'guest'),
(14, 10, 8, 9, 'Project worker', 'normal');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_groups_id_seq`
--

CREATE TABLE `dotp_gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro_groups_id_seq`
--

INSERT INTO `dotp_gacl_aro_groups_id_seq` (`id`) VALUES
(14);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_groups_map`
--

CREATE TABLE `dotp_gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro_groups_map`
--

INSERT INTO `dotp_gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES
(10, 10),
(11, 11),
(12, 11),
(13, 13),
(14, 12),
(15, 14),
(16, 13),
(16, 14);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_map`
--

CREATE TABLE `dotp_gacl_aro_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_sections`
--

CREATE TABLE `dotp_gacl_aro_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro_sections`
--

INSERT INTO `dotp_gacl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'user', 1, 'Users', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_sections_seq`
--

CREATE TABLE `dotp_gacl_aro_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro_sections_seq`
--

INSERT INTO `dotp_gacl_aro_sections_seq` (`id`) VALUES
(10);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_aro_seq`
--

CREATE TABLE `dotp_gacl_aro_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_aro_seq`
--

INSERT INTO `dotp_gacl_aro_seq` (`id`) VALUES
(10);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo`
--

CREATE TABLE `dotp_gacl_axo` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo`
--

INSERT INTO `dotp_gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'sys', 'acl', 1, 'ACL Administration', 0),
(11, 'app', 'admin', 1, 'User Administration', 0),
(12, 'app', 'calendar', 2, 'Calendar', 0),
(13, 'app', 'events', 2, 'Events', 0),
(14, 'app', 'companies', 3, 'Companies', 0),
(15, 'app', 'contacts', 4, 'Contacts', 0),
(16, 'app', 'departments', 5, 'Departments', 0),
(17, 'app', 'files', 6, 'Files', 0),
(18, 'app', 'file_folders', 6, 'File Folders', 0),
(19, 'app', 'forums', 7, 'Forums', 0),
(20, 'app', 'help', 8, 'Help', 0),
(21, 'app', 'projects', 9, 'Projects', 0),
(22, 'app', 'system', 10, 'System Administration', 0),
(23, 'app', 'tasks', 11, 'Tasks', 0),
(24, 'app', 'task_log', 11, 'Task Logs', 0),
(25, 'app', 'ticketsmith', 12, 'Tickets', 0),
(26, 'app', 'public', 13, 'Public', 0),
(27, 'app', 'roles', 14, 'Roles Administration', 0),
(28, 'app', 'users', 15, 'User Table', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_groups`
--

CREATE TABLE `dotp_gacl_axo_groups` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_groups`
--

INSERT INTO `dotp_gacl_axo_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES
(10, 0, 1, 8, 'Modules', 'mod'),
(11, 10, 2, 3, 'All Modules', 'all'),
(12, 10, 4, 5, 'Admin Modules', 'admin'),
(13, 10, 6, 7, 'Non-Admin Modules', 'non_admin');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_groups_id_seq`
--

CREATE TABLE `dotp_gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_groups_id_seq`
--

INSERT INTO `dotp_gacl_axo_groups_id_seq` (`id`) VALUES
(13);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_groups_map`
--

CREATE TABLE `dotp_gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_groups_map`
--

INSERT INTO `dotp_gacl_axo_groups_map` (`acl_id`, `group_id`) VALUES
(11, 11),
(13, 13),
(14, 13),
(15, 13);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_map`
--

CREATE TABLE `dotp_gacl_axo_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_map`
--

INSERT INTO `dotp_gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES
(12, 'sys', 'acl'),
(16, 'app', 'users');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_sections`
--

CREATE TABLE `dotp_gacl_axo_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_sections`
--

INSERT INTO `dotp_gacl_axo_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'sys', 1, 'System', 0),
(11, 'app', 2, 'Application', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_sections_seq`
--

CREATE TABLE `dotp_gacl_axo_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_sections_seq`
--

INSERT INTO `dotp_gacl_axo_sections_seq` (`id`) VALUES
(11);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_axo_seq`
--

CREATE TABLE `dotp_gacl_axo_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_axo_seq`
--

INSERT INTO `dotp_gacl_axo_seq` (`id`) VALUES
(28);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_groups_aro_map`
--

CREATE TABLE `dotp_gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `aro_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_groups_aro_map`
--

INSERT INTO `dotp_gacl_groups_aro_map` (`group_id`, `aro_id`) VALUES
(11, 10);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_groups_axo_map`
--

CREATE TABLE `dotp_gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `axo_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_groups_axo_map`
--

INSERT INTO `dotp_gacl_groups_axo_map` (`group_id`, `axo_id`) VALUES
(11, 11),
(11, 12),
(11, 13),
(11, 14),
(11, 15),
(11, 16),
(11, 17),
(11, 18),
(11, 19),
(11, 20),
(11, 21),
(11, 22),
(11, 23),
(11, 24),
(11, 25),
(11, 26),
(11, 27),
(11, 28),
(12, 11),
(12, 22),
(12, 27),
(12, 28),
(13, 12),
(13, 13),
(13, 14),
(13, 15),
(13, 16),
(13, 17),
(13, 18),
(13, 19),
(13, 20),
(13, 21),
(13, 23),
(13, 24),
(13, 25),
(13, 26);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_gacl_phpgacl`
--

CREATE TABLE `dotp_gacl_phpgacl` (
  `name` varchar(127) NOT NULL DEFAULT '',
  `value` varchar(230) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_gacl_phpgacl`
--

INSERT INTO `dotp_gacl_phpgacl` (`name`, `value`) VALUES
('schema_version', '2.1'),
('version', '3.3.2');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_modules`
--

CREATE TABLE `dotp_modules` (
  `mod_id` int(11) NOT NULL,
  `mod_name` varchar(64) NOT NULL DEFAULT '',
  `mod_directory` varchar(64) NOT NULL DEFAULT '',
  `mod_version` varchar(10) NOT NULL DEFAULT '',
  `mod_setup_class` varchar(64) NOT NULL DEFAULT '',
  `mod_type` varchar(64) NOT NULL DEFAULT '',
  `mod_active` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `mod_ui_name` varchar(20) NOT NULL DEFAULT '',
  `mod_ui_icon` varchar(64) NOT NULL DEFAULT '',
  `mod_ui_order` tinyint(3) NOT NULL DEFAULT '0',
  `mod_ui_active` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `mod_description` varchar(255) NOT NULL DEFAULT '',
  `permissions_item_table` char(100) DEFAULT NULL,
  `permissions_item_field` char(100) DEFAULT NULL,
  `permissions_item_label` char(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_modules`
--

INSERT INTO `dotp_modules` (`mod_id`, `mod_name`, `mod_directory`, `mod_version`, `mod_setup_class`, `mod_type`, `mod_active`, `mod_ui_name`, `mod_ui_icon`, `mod_ui_order`, `mod_ui_active`, `mod_description`, `permissions_item_table`, `permissions_item_field`, `permissions_item_label`) VALUES
(1, 'Companies', 'companies', '1.0.0', '', 'core', 1, 'Companies', 'handshake.png', 1, 1, '', 'companies', 'company_id', 'company_name'),
(2, 'Projects', 'projects', '1.0.0', '', 'core', 1, 'Projects', 'applet3-48.png', 2, 1, '', 'projects', 'project_id', 'project_name'),
(3, 'Tasks', 'tasks', '1.0.0', '', 'core', 1, 'Tasks', 'applet-48.png', 3, 1, '', 'tasks', 'task_id', 'task_name'),
(4, 'Calendar', 'calendar', '1.0.0', '', 'core', 1, 'Calendar', 'myevo-appointments.png', 4, 1, '', 'events', 'event_id', 'event_title'),
(5, 'Files', 'files', '1.0.0', '', 'core', 1, 'Files', 'folder5.png', 5, 1, '', 'files', 'file_id', 'file_name'),
(6, 'Contacts', 'contacts', '1.0.0', '', 'core', 1, 'Contacts', 'monkeychat-48.png', 6, 1, '', 'contacts', 'contact_id', 'contact_title'),
(7, 'Forums', 'forums', '1.0.0', '', 'core', 1, 'Forums', 'support.png', 7, 1, '', 'forums', 'forum_id', 'forum_name'),
(8, 'Tickets', 'ticketsmith', '1.0.0', '', 'core', 1, 'Tickets', 'ticketsmith.gif', 8, 1, '', '', '', ''),
(9, 'User Administration', 'admin', '1.0.0', '', 'core', 1, 'User Admin', 'helix-setup-users.png', 9, 1, '', 'users', 'user_id', 'user_username'),
(10, 'System Administration', 'system', '1.0.0', '', 'core', 1, 'System Admin', '48_my_computer.png', 10, 1, '', '', '', ''),
(11, 'Departments', 'departments', '1.0.0', '', 'core', 1, 'Departments', 'users.gif', 11, 0, '', 'departments', 'dept_id', 'dept_name'),
(12, 'Help', 'help', '1.0.0', '', 'core', 1, 'Help', 'dp.gif', 12, 0, '', '', '', ''),
(13, 'Public', 'public', '1.0.0', '', 'core', 1, 'Public', 'users.gif', 13, 0, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_permissions`
--

CREATE TABLE `dotp_permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_user` int(11) NOT NULL DEFAULT '0',
  `permission_grant_on` varchar(12) NOT NULL DEFAULT '',
  `permission_item` int(11) NOT NULL DEFAULT '0',
  `permission_value` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_permissions`
--

INSERT INTO `dotp_permissions` (`permission_id`, `permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) VALUES
(1, 1, 'all', -1, -1);

-- --------------------------------------------------------

--
-- Table structure for table `dotp_projects`
--

CREATE TABLE `dotp_projects` (
  `project_id` int(11) NOT NULL,
  `project_company` int(11) NOT NULL DEFAULT '0',
  `project_company_internal` int(11) NOT NULL DEFAULT '0',
  `project_department` int(11) NOT NULL DEFAULT '0',
  `project_name` varchar(255) DEFAULT NULL,
  `project_short_name` varchar(10) DEFAULT NULL,
  `project_owner` int(11) DEFAULT '0',
  `project_url` varchar(255) DEFAULT NULL,
  `project_demo_url` varchar(255) DEFAULT NULL,
  `project_start_date` datetime DEFAULT NULL,
  `project_end_date` datetime DEFAULT NULL,
  `project_status` int(11) DEFAULT '0',
  `project_percent_complete` tinyint(4) DEFAULT '0',
  `project_color_identifier` varchar(7) DEFAULT '#eeeeee',
  `project_description` text,
  `project_target_budget` decimal(10,2) DEFAULT '0.00',
  `project_actual_budget` decimal(10,2) DEFAULT '0.00',
  `project_creator` int(11) DEFAULT '0',
  `project_private` tinyint(3) UNSIGNED DEFAULT '0',
  `project_departments` char(100) DEFAULT NULL,
  `project_contacts` char(100) DEFAULT NULL,
  `project_priority` tinyint(4) DEFAULT '0',
  `project_type` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_project_contacts`
--

CREATE TABLE `dotp_project_contacts` (
  `project_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_project_departments`
--

CREATE TABLE `dotp_project_departments` (
  `project_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_roles`
--

CREATE TABLE `dotp_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(24) NOT NULL DEFAULT '',
  `role_description` varchar(255) NOT NULL DEFAULT '',
  `role_type` int(3) UNSIGNED NOT NULL DEFAULT '0',
  `role_module` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_sessions`
--

CREATE TABLE `dotp_sessions` (
  `session_id` varchar(60) NOT NULL DEFAULT '',
  `session_user` int(11) NOT NULL DEFAULT '0',
  `session_data` longblob,
  `session_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_sessions`
--

INSERT INTO `dotp_sessions` (`session_id`, `session_user`, `session_data`, `session_created`) VALUES
('592g7lr5ajtfvvg5itip5jmln2', 4, 0x4c414e4755414745537c613a353a7b733a353a22656e5f4155223b613a343a7b693a303b733a323a22656e223b693a313b733a31333a22456e676c697368202841757329223b693a323b733a31333a22456e676c697368202841757329223b693a333b733a333a22656e61223b7d733a353a22656e5f4341223b613a343a7b693a303b733a323a22656e223b693a313b733a31333a22456e676c697368202843616e29223b693a323b733a31333a22456e676c697368202843616e29223b693a333b733a333a22656e63223b7d733a353a22656e5f4742223b613a343a7b693a303b733a323a22656e223b693a313b733a31323a22456e676c6973682028474229223b693a323b733a31323a22456e676c6973682028474229223b693a333b733a333a22656e67223b7d733a353a22656e5f4e5a223b613a343a7b693a303b733a323a22656e223b693a313b733a31323a22456e676c69736820284e5a29223b693a323b733a31323a22456e676c69736820284e5a29223b693a333b733a333a22656e7a223b7d733a353a22656e5f5553223b613a353a7b693a303b733a323a22656e223b693a313b733a31323a22456e676c6973682028555329223b693a323b733a31323a22456e676c6973682028555329223b693a333b733a333a22656e75223b693a343b733a31303a2249534f383835392d3135223b7d7d41707055497c4f3a363a22434170705549223a32373a7b733a353a227374617465223b613a31313a7b733a31333a2243616c496478436f6d70616e79223b733a303a22223b733a31323a2243616c49647846696c746572223b733a323a226d79223b733a31333a2243616c44617956696577546162223b733a313a2231223b733a31343a225461736b44617953686f77417263223b693a303b733a31343a225461736b44617953686f774c6f77223b693a313b733a31353a225461736b44617953686f77486f6c64223b693a303b733a31343a225461736b44617953686f7744796e223b693a303b733a31343a225461736b44617953686f7750696e223b693a303b733a32303a225461736b44617953686f77456d70747944617465223b693a303b733a31323a225341564544504c4143452d31223b4e3b733a31303a225341564544504c414345223b733a373a226d3d61646d696e223b7d733a373a22757365725f6964223b733a313a2231223b733a31353a22757365725f66697273745f6e616d65223b733a353a2241646d696e223b733a31343a22757365725f6c6173745f6e616d65223b733a363a22506572736f6e223b733a31323a22757365725f636f6d70616e79223b733a303a22223b733a31353a22757365725f6465706172746d656e74223b693a303b733a31303a22757365725f656d61696c223b733a31353a2261646d696e403132372e302e302e31223b733a393a22757365725f74797065223b733a313a2231223b733a31303a22757365725f7072656673223b613a383a7b733a363a224c4f43414c45223b733a323a22656e223b733a373a2254414256494557223b733a313a2230223b733a31323a22534844415445464f524d4154223b733a383a2225642f256d2f2559223b733a31303a2254494d45464f524d4154223b733a383a2225493a254d202570223b733a373a2255495354594c45223b733a373a2264656661756c74223b733a31333a225441534b41535349474e4d4158223b733a333a22313030223b733a31303a2255534552464f524d4154223b733a343a2275736572223b733a31303a2255534544494745535453223b733a313a2230223b7d733a31323a226461795f73656c6563746564223b4e3b733a31323a2273797374656d5f7072656673223b613a383a7b733a363a224c4f43414c45223b733a323a22656e223b733a373a2254414256494557223b733a313a2230223b733a31323a22534844415445464f524d4154223b733a383a2225642f256d2f2559223b733a31303a2254494d45464f524d4154223b733a383a2225493a254d202570223b733a373a2255495354594c45223b733a373a2264656661756c74223b733a31333a225441534b41535349474e4d4158223b733a333a22313030223b733a31303a2255534552464f524d4154223b733a343a2275736572223b733a31303a2255534544494745535453223b733a313a2230223b7d733a31313a22757365725f6c6f63616c65223b733a323a22656e223b733a393a22757365725f6c616e67223b613a343a7b693a303b733a31313a22656e5f41552e7574662d38223b693a313b733a333a22656e61223b693a323b733a353a22656e5f4155223b693a333b733a323a22656e223b7d733a31313a22626173655f6c6f63616c65223b733a323a22656e223b733a31363a22626173655f646174655f6c6f63616c65223b4e3b733a333a226d7367223b733a303a22223b733a353a226d73674e6f223b693a303b733a31353a2264656661756c745265646972656374223b733a303a22223b733a333a22636667223b613a313a7b733a31313a226c6f63616c655f7761726e223b623a303b7d733a31333a2276657273696f6e5f6d616a6f72223b693a323b733a31333a2276657273696f6e5f6d696e6f72223b693a323b733a31333a2276657273696f6e5f7061746368223b693a303b733a31343a2276657273696f6e5f737472696e67223b733a353a22322e322e30223b733a31343a226c6173745f696e736572745f6964223b693a343b733a333a225f6a73223b613a303a7b7d733a343a225f637373223b613a303a7b7d733a31303a2270726f6a6563745f6964223b693a303b7d616c6c5f746162737c613a323a7b733a383a2263616c656e646172223b613a313a7b693a303b613a333a7b733a343a226e616d65223b733a383a2250726f6a65637473223b733a343a2266696c65223b733a37303a222f4170706c69636174696f6e732f416d7070732f7777772f646f7470726f6a2f6d6f64756c65732f70726f6a656374732f63616c656e6461725f7461622e70726f6a65637473223b733a363a226d6f64756c65223b733a383a2270726f6a65637473223b7d7d733a353a2261646d696e223b613a313a7b733a383a227669657775736572223b613a323a7b693a303b613a333a7b733a343a226e616d65223b733a383a2250726f6a65637473223b733a343a2266696c65223b733a37363a222f4170706c69636174696f6e732f416d7070732f7777772f646f7470726f6a2f6d6f64756c65732f70726f6a656374732f61646d696e5f7461622e76696577757365722e70726f6a65637473223b733a363a226d6f64756c65223b733a383a2270726f6a65637473223b7d693a313b613a333a7b733a343a226e616d65223b733a31343a2250726f6a656374732067616e7474223b733a343a2266696c65223b733a38323a222f4170706c69636174696f6e732f416d7070732f7777772f646f7470726f6a2f6d6f64756c65732f70726f6a656374732f61646d696e5f7461622e76696577757365722e70726f6a656374735f67616e7474223b733a363a226d6f64756c65223b733a383a2270726f6a65637473223b7d7d7d7d, '2020-09-25 17:04:12'),
('d8ef4p7q0ohs7vmelbcf8eb0j3', 2, 0x4c414e4755414745537c613a353a7b733a353a22656e5f4155223b613a343a7b693a303b733a323a22656e223b693a313b733a31333a22456e676c697368202841757329223b693a323b733a31333a22456e676c697368202841757329223b693a333b733a333a22656e61223b7d733a353a22656e5f4341223b613a343a7b693a303b733a323a22656e223b693a313b733a31333a22456e676c697368202843616e29223b693a323b733a31333a22456e676c697368202843616e29223b693a333b733a333a22656e63223b7d733a353a22656e5f4742223b613a343a7b693a303b733a323a22656e223b693a313b733a31323a22456e676c6973682028474229223b693a323b733a31323a22456e676c6973682028474229223b693a333b733a333a22656e67223b7d733a353a22656e5f4e5a223b613a343a7b693a303b733a323a22656e223b693a313b733a31323a22456e676c69736820284e5a29223b693a323b733a31323a22456e676c69736820284e5a29223b693a333b733a333a22656e7a223b7d733a353a22656e5f5553223b613a353a7b693a303b733a323a22656e223b693a313b733a31323a22456e676c6973682028555329223b693a323b733a31323a22456e676c6973682028555329223b693a333b733a333a22656e75223b693a343b733a31303a2249534f383835392d3135223b7d7d41707055497c4f3a363a22434170705549223a32373a7b733a353a227374617465223b613a393a7b733a31333a2243616c496478436f6d70616e79223b733a303a22223b733a31323a2243616c49647846696c746572223b733a323a226d79223b733a31333a2243616c44617956696577546162223b733a313a2231223b733a31343a225461736b44617953686f77417263223b693a303b733a31343a225461736b44617953686f774c6f77223b693a313b733a31353a225461736b44617953686f77486f6c64223b693a303b733a31343a225461736b44617953686f7744796e223b693a303b733a31343a225461736b44617953686f7750696e223b693a303b733a32303a225461736b44617953686f77456d70747944617465223b693a303b7d733a373a22757365725f6964223b733a313a2231223b733a31353a22757365725f66697273745f6e616d65223b733a353a2241646d696e223b733a31343a22757365725f6c6173745f6e616d65223b733a363a22506572736f6e223b733a31323a22757365725f636f6d70616e79223b733a303a22223b733a31353a22757365725f6465706172746d656e74223b693a303b733a31303a22757365725f656d61696c223b733a31353a2261646d696e403132372e302e302e31223b733a393a22757365725f74797065223b733a313a2231223b733a31303a22757365725f7072656673223b613a383a7b733a363a224c4f43414c45223b733a323a22656e223b733a373a2254414256494557223b733a313a2230223b733a31323a22534844415445464f524d4154223b733a383a2225642f256d2f2559223b733a31303a2254494d45464f524d4154223b733a383a2225493a254d202570223b733a373a2255495354594c45223b733a373a2264656661756c74223b733a31333a225441534b41535349474e4d4158223b733a333a22313030223b733a31303a2255534552464f524d4154223b733a343a2275736572223b733a31303a2255534544494745535453223b733a313a2230223b7d733a31323a226461795f73656c6563746564223b4e3b733a31323a2273797374656d5f7072656673223b613a383a7b733a363a224c4f43414c45223b733a323a22656e223b733a373a2254414256494557223b733a313a2230223b733a31323a22534844415445464f524d4154223b733a383a2225642f256d2f2559223b733a31303a2254494d45464f524d4154223b733a383a2225493a254d202570223b733a373a2255495354594c45223b733a373a2264656661756c74223b733a31333a225441534b41535349474e4d4158223b733a333a22313030223b733a31303a2255534552464f524d4154223b733a343a2275736572223b733a31303a2255534544494745535453223b733a313a2230223b7d733a31313a22757365725f6c6f63616c65223b733a323a22656e223b733a393a22757365725f6c616e67223b613a343a7b693a303b733a31313a22656e5f41552e7574662d38223b693a313b733a333a22656e61223b693a323b733a353a22656e5f4155223b693a333b733a323a22656e223b7d733a31313a22626173655f6c6f63616c65223b733a323a22656e223b733a31363a22626173655f646174655f6c6f63616c65223b4e3b733a333a226d7367223b733a303a22223b733a353a226d73674e6f223b693a303b733a31353a2264656661756c745265646972656374223b733a303a22223b733a333a22636667223b613a313a7b733a31313a226c6f63616c655f7761726e223b623a303b7d733a31333a2276657273696f6e5f6d616a6f72223b693a323b733a31333a2276657273696f6e5f6d696e6f72223b693a323b733a31333a2276657273696f6e5f7061746368223b693a303b733a31343a2276657273696f6e5f737472696e67223b733a353a22322e322e30223b733a31343a226c6173745f696e736572745f6964223b693a323b733a333a225f6a73223b613a303a7b7d733a343a225f637373223b613a303a7b7d733a31303a2270726f6a6563745f6964223b693a303b7d616c6c5f746162737c613a313a7b733a383a2263616c656e646172223b613a313a7b693a303b613a333a7b733a343a226e616d65223b733a383a2250726f6a65637473223b733a343a2266696c65223b733a37303a222f4170706c69636174696f6e732f416d7070732f7777772f646f7470726f6a2f6d6f64756c65732f70726f6a656374732f63616c656e6461725f7461622e70726f6a65637473223b733a363a226d6f64756c65223b733a383a2270726f6a65637473223b7d7d7d, '2020-09-25 15:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_syskeys`
--

CREATE TABLE `dotp_syskeys` (
  `syskey_id` int(10) UNSIGNED NOT NULL,
  `syskey_name` varchar(48) NOT NULL DEFAULT '',
  `syskey_label` varchar(255) NOT NULL DEFAULT '',
  `syskey_type` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `syskey_sep1` char(2) DEFAULT '\n',
  `syskey_sep2` char(2) NOT NULL DEFAULT '|'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_syskeys`
--

INSERT INTO `dotp_syskeys` (`syskey_id`, `syskey_name`, `syskey_label`, `syskey_type`, `syskey_sep1`, `syskey_sep2`) VALUES
(1, 'SelectList', 'Enter values for list', 0, '\n', '|'),
(2, 'CustomField', 'Serialized array in the following format:\r\n<KEY>|<SERIALIZED ARRAY>\r\n\r\nSerialized Array:\r\n[type] => text | checkbox | select | textarea | label\r\n[name] => <Field\'s name>\r\n[options] => <html capture options>\r\n[selects] => <options for select and checkbox>', 0, '\n', '|'),
(3, 'ColorSelection', 'Hex color values for type=>color association.', 0, '\n', '|');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_sysvals`
--

CREATE TABLE `dotp_sysvals` (
  `sysval_id` int(10) UNSIGNED NOT NULL,
  `sysval_key_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `sysval_title` varchar(48) NOT NULL DEFAULT '',
  `sysval_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_sysvals`
--

INSERT INTO `dotp_sysvals` (`sysval_id`, `sysval_key_id`, `sysval_title`, `sysval_value`) VALUES
(1, 1, 'ProjectStatus', '0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived'),
(2, 1, 'CompanyType', '0|Not Applicable\n1|Client\n2|Vendor\n3|Supplier\n4|Consultant\n5|Government\n6|Internal'),
(3, 1, 'TaskDurationType', '1|hours\n24|days'),
(4, 1, 'EventType', '0|General\n1|Appointment\n2|Meeting\n3|All Day Event\n4|Anniversary\n5|Reminder'),
(5, 1, 'TaskStatus', '0|Active\n-1|Inactive'),
(6, 1, 'TaskType', '0|Unknown\n1|Administrative\n2|Operative'),
(7, 1, 'ProjectType', '0|Unknown\n1|Administrative\n2|Operative'),
(8, 3, 'ProjectColors', 'Web|FFE0AE\nEngineering|AEFFB2\nHelpDesk|FFFCAE\nSystem Administration|FFAEAE'),
(9, 1, 'FileType', '0|Unknown\n1|Document\n2|Application'),
(10, 1, 'TaskPriority', '-1|low\n0|normal\n1|high'),
(11, 1, 'ProjectPriority', '-1|low\n0|normal\n1|high'),
(12, 1, 'ProjectPriorityColor', '-1|#E5F7FF\n0|\n1|#FFDCB3'),
(13, 1, 'TaskLogReference', '0|Not Defined\n1|Email\n2|Helpdesk\n3|Phone Call\n4|Fax'),
(14, 1, 'TaskLogReferenceImage', '0| 1|./images/obj/email.gif 2|./modules/helpdesk/images/helpdesk.png 3|./images/obj/phone.gif 4|./images/icons/stock_print-16.png'),
(15, 1, 'UserType', '0|Default User\r\n1|Administrator\r\n2|CEO\r\n3|Director\r\n4|Branch Manager\r\n5|Manager\r\n6|Supervisor\r\n7|Employee'),
(16, 1, 'ProjectRequiredFields', 'f.project_name.value.length|<3\r\nf.project_color_identifier.value.length|<3\r\nf.project_company.options[f.project_company.selectedIndex].value|<1'),
(17, 2, 'TicketNotify', '0|admin@127.0.0.1\n1|admin@127.0.0.1\n2|admin@127.0.0.1\r\n3|admin@127.0.0.1\r\n4|admin@127.0.0.1'),
(18, 1, 'TicketPriority', '0|Low\n1|Normal\n2|High\n3|Highest\n4|911'),
(19, 1, 'TicketStatus', '0|Open\n1|Closed\n2|Deleted');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_tasks`
--

CREATE TABLE `dotp_tasks` (
  `task_id` int(11) NOT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `task_parent` int(11) DEFAULT '0',
  `task_milestone` tinyint(1) DEFAULT '0',
  `task_project` int(11) NOT NULL DEFAULT '0',
  `task_owner` int(11) NOT NULL DEFAULT '0',
  `task_start_date` datetime DEFAULT NULL,
  `task_duration` float UNSIGNED DEFAULT '0',
  `task_duration_type` int(11) NOT NULL DEFAULT '1',
  `task_hours_worked` float UNSIGNED DEFAULT '0',
  `task_end_date` datetime DEFAULT NULL,
  `task_status` int(11) DEFAULT '0',
  `task_priority` tinyint(4) DEFAULT '0',
  `task_percent_complete` tinyint(4) DEFAULT '0',
  `task_description` text,
  `task_target_budget` decimal(10,2) DEFAULT '0.00',
  `task_related_url` varchar(255) DEFAULT NULL,
  `task_creator` int(11) NOT NULL DEFAULT '0',
  `task_order` int(11) NOT NULL DEFAULT '0',
  `task_client_publish` tinyint(1) NOT NULL DEFAULT '0',
  `task_dynamic` tinyint(1) NOT NULL DEFAULT '0',
  `task_access` int(11) NOT NULL DEFAULT '0',
  `task_notify` int(11) NOT NULL DEFAULT '0',
  `task_departments` char(100) DEFAULT NULL,
  `task_contacts` char(100) DEFAULT NULL,
  `task_custom` longtext,
  `task_type` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_task_contacts`
--

CREATE TABLE `dotp_task_contacts` (
  `task_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_task_departments`
--

CREATE TABLE `dotp_task_departments` (
  `task_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_task_dependencies`
--

CREATE TABLE `dotp_task_dependencies` (
  `dependencies_task_id` int(11) NOT NULL,
  `dependencies_req_task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_task_log`
--

CREATE TABLE `dotp_task_log` (
  `task_log_id` int(11) NOT NULL,
  `task_log_task` int(11) NOT NULL DEFAULT '0',
  `task_log_name` varchar(255) DEFAULT NULL,
  `task_log_description` text,
  `task_log_creator` int(11) NOT NULL DEFAULT '0',
  `task_log_hours` float NOT NULL DEFAULT '0',
  `task_log_date` datetime DEFAULT NULL,
  `task_log_costcode` varchar(8) NOT NULL DEFAULT '',
  `task_log_problem` tinyint(1) DEFAULT '0',
  `task_log_reference` tinyint(4) DEFAULT '0',
  `task_log_related_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_tickets`
--

CREATE TABLE `dotp_tickets` (
  `ticket` int(10) UNSIGNED NOT NULL,
  `ticket_company` int(10) NOT NULL DEFAULT '0',
  `ticket_project` int(10) NOT NULL DEFAULT '0',
  `author` varchar(100) NOT NULL DEFAULT '',
  `recipient` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `attachment` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL DEFAULT '',
  `assignment` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `parent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `cc` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `signature` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_users`
--

CREATE TABLE `dotp_users` (
  `user_id` int(11) NOT NULL,
  `user_contact` int(11) NOT NULL DEFAULT '0',
  `user_username` varchar(255) NOT NULL DEFAULT '',
  `user_password` varchar(32) NOT NULL DEFAULT '',
  `user_parent` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(3) NOT NULL DEFAULT '0',
  `user_company` int(11) DEFAULT '0',
  `user_department` int(11) DEFAULT '0',
  `user_owner` int(11) NOT NULL DEFAULT '0',
  `user_signature` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_users`
--

INSERT INTO `dotp_users` (`user_id`, `user_contact`, `user_username`, `user_password`, `user_parent`, `user_type`, `user_company`, `user_department`, `user_owner`, `user_signature`) VALUES
(1, 1, 'admin', '1a1dc91c907325c69271ddf0c944bc72', 0, 1, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_user_access_log`
--

CREATE TABLE `dotp_user_access_log` (
  `user_access_log_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_ip` varchar(15) NOT NULL,
  `date_time_in` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_out` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_last_action` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_user_access_log`
--

INSERT INTO `dotp_user_access_log` (`user_access_log_id`, `user_id`, `user_ip`, `date_time_in`, `date_time_out`, `date_time_last_action`) VALUES
(1, 1, '127.0.0.1', '2020-09-25 11:10:48', '2020-09-25 15:23:27', '2020-09-25 15:22:10'),
(2, 1, '127.0.0.1', '2020-09-25 11:23:36', '0000-00-00 00:00:00', '2020-09-25 15:23:36'),
(3, 1, '127.0.0.1', '2020-09-25 12:13:22', '2020-09-25 16:17:13', '2020-09-25 16:17:09'),
(4, 1, '127.0.0.1', '2020-09-25 13:04:12', '0000-00-00 00:00:00', '2020-09-25 17:04:24');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_user_events`
--

CREATE TABLE `dotp_user_events` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_user_preferences`
--

CREATE TABLE `dotp_user_preferences` (
  `pref_user` varchar(12) NOT NULL DEFAULT '',
  `pref_name` varchar(72) NOT NULL DEFAULT '',
  `pref_value` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dotp_user_preferences`
--

INSERT INTO `dotp_user_preferences` (`pref_user`, `pref_name`, `pref_value`) VALUES
('0', 'LOCALE', 'en'),
('0', 'TABVIEW', '0'),
('0', 'SHDATEFORMAT', '%d/%m/%Y'),
('0', 'TIMEFORMAT', '%I:%M %p'),
('0', 'UISTYLE', 'default'),
('0', 'TASKASSIGNMAX', '100'),
('0', 'USERFORMAT', 'user'),
('0', 'USEDIGESTS', '0');

-- --------------------------------------------------------

--
-- Table structure for table `dotp_user_roles`
--

CREATE TABLE `dotp_user_roles` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_user_tasks`
--

CREATE TABLE `dotp_user_tasks` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(4) NOT NULL DEFAULT '0',
  `task_id` int(11) NOT NULL DEFAULT '0',
  `perc_assignment` int(11) NOT NULL DEFAULT '100',
  `user_task_priority` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dotp_user_task_pin`
--

CREATE TABLE `dotp_user_task_pin` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `task_id` int(10) NOT NULL DEFAULT '0',
  `task_pinned` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dotp_billingcode`
--
ALTER TABLE `dotp_billingcode`
  ADD PRIMARY KEY (`billingcode_id`);

--
-- Indexes for table `dotp_common_notes`
--
ALTER TABLE `dotp_common_notes`
  ADD PRIMARY KEY (`note_id`);

--
-- Indexes for table `dotp_companies`
--
ALTER TABLE `dotp_companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `idx_cpy1` (`company_owner`);

--
-- Indexes for table `dotp_config`
--
ALTER TABLE `dotp_config`
  ADD PRIMARY KEY (`config_id`),
  ADD UNIQUE KEY `config_name` (`config_name`);

--
-- Indexes for table `dotp_config_list`
--
ALTER TABLE `dotp_config_list`
  ADD PRIMARY KEY (`config_list_id`),
  ADD KEY `config_id` (`config_id`);

--
-- Indexes for table `dotp_contacts`
--
ALTER TABLE `dotp_contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `idx_oby` (`contact_order_by`),
  ADD KEY `idx_co` (`contact_company`),
  ADD KEY `idx_prp` (`contact_project`);

--
-- Indexes for table `dotp_custom_fields_struct`
--
ALTER TABLE `dotp_custom_fields_struct`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `dotp_custom_fields_values`
--
ALTER TABLE `dotp_custom_fields_values`
  ADD KEY `idx_cfv_id` (`value_id`);

--
-- Indexes for table `dotp_departments`
--
ALTER TABLE `dotp_departments`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `dotp_dotpermissions`
--
ALTER TABLE `dotp_dotpermissions`
  ADD KEY `user_id` (`user_id`,`section`,`permission`,`axo`);

--
-- Indexes for table `dotp_events`
--
ALTER TABLE `dotp_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `id_esd` (`event_start_date`),
  ADD KEY `id_eed` (`event_end_date`),
  ADD KEY `id_evp` (`event_parent`),
  ADD KEY `idx_ev1` (`event_owner`),
  ADD KEY `idx_ev2` (`event_project`);

--
-- Indexes for table `dotp_event_queue`
--
ALTER TABLE `dotp_event_queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `queue_start` (`queue_batched`,`queue_start`),
  ADD KEY `queue_module` (`queue_module`),
  ADD KEY `queue_type` (`queue_type`),
  ADD KEY `queue_origin_id` (`queue_origin_id`);

--
-- Indexes for table `dotp_files`
--
ALTER TABLE `dotp_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `idx_file_task` (`file_task`),
  ADD KEY `idx_file_project` (`file_project`),
  ADD KEY `idx_file_parent` (`file_parent`),
  ADD KEY `idx_file_vid` (`file_version_id`);

--
-- Indexes for table `dotp_files_index`
--
ALTER TABLE `dotp_files_index`
  ADD PRIMARY KEY (`file_id`,`word`,`word_placement`),
  ADD KEY `idx_fwrd` (`word`);

--
-- Indexes for table `dotp_file_folders`
--
ALTER TABLE `dotp_file_folders`
  ADD PRIMARY KEY (`file_folder_id`);

--
-- Indexes for table `dotp_forums`
--
ALTER TABLE `dotp_forums`
  ADD PRIMARY KEY (`forum_id`),
  ADD KEY `idx_fproject` (`forum_project`),
  ADD KEY `idx_fowner` (`forum_owner`),
  ADD KEY `forum_status` (`forum_status`);

--
-- Indexes for table `dotp_forum_messages`
--
ALTER TABLE `dotp_forum_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_mparent` (`message_parent`),
  ADD KEY `idx_mdate` (`message_date`),
  ADD KEY `idx_mforum` (`message_forum`);

--
-- Indexes for table `dotp_forum_visits`
--
ALTER TABLE `dotp_forum_visits`
  ADD KEY `idx_fv` (`visit_user`,`visit_forum`,`visit_message`);

--
-- Indexes for table `dotp_forum_watch`
--
ALTER TABLE `dotp_forum_watch`
  ADD KEY `idx_fw1` (`watch_user`,`watch_forum`),
  ADD KEY `idx_fw2` (`watch_user`,`watch_topic`);

--
-- Indexes for table `dotp_gacl_acl`
--
ALTER TABLE `dotp_gacl_acl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gacl_enabled_acl` (`enabled`),
  ADD KEY `gacl_section_value_acl` (`section_value`),
  ADD KEY `gacl_updated_date_acl` (`updated_date`);

--
-- Indexes for table `dotp_gacl_acl_sections`
--
ALTER TABLE `dotp_gacl_acl_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_value_acl_sections` (`value`),
  ADD KEY `gacl_hidden_acl_sections` (`hidden`);

--
-- Indexes for table `dotp_gacl_aco`
--
ALTER TABLE `dotp_gacl_aco`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
  ADD KEY `gacl_hidden_aco` (`hidden`);

--
-- Indexes for table `dotp_gacl_aco_map`
--
ALTER TABLE `dotp_gacl_aco_map`
  ADD PRIMARY KEY (`acl_id`,`section_value`,`value`);

--
-- Indexes for table `dotp_gacl_aco_sections`
--
ALTER TABLE `dotp_gacl_aco_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_value_aco_sections` (`value`),
  ADD KEY `gacl_hidden_aco_sections` (`hidden`);

--
-- Indexes for table `dotp_gacl_aro`
--
ALTER TABLE `dotp_gacl_aro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  ADD KEY `gacl_hidden_aro` (`hidden`);

--
-- Indexes for table `dotp_gacl_aro_groups`
--
ALTER TABLE `dotp_gacl_aro_groups`
  ADD PRIMARY KEY (`id`,`value`),
  ADD KEY `gacl_parent_id_aro_groups` (`parent_id`),
  ADD KEY `gacl_value_aro_groups` (`value`),
  ADD KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`);

--
-- Indexes for table `dotp_gacl_aro_groups_map`
--
ALTER TABLE `dotp_gacl_aro_groups_map`
  ADD PRIMARY KEY (`acl_id`,`group_id`);

--
-- Indexes for table `dotp_gacl_aro_map`
--
ALTER TABLE `dotp_gacl_aro_map`
  ADD PRIMARY KEY (`acl_id`,`section_value`,`value`);

--
-- Indexes for table `dotp_gacl_aro_sections`
--
ALTER TABLE `dotp_gacl_aro_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_value_aro_sections` (`value`),
  ADD KEY `gacl_hidden_aro_sections` (`hidden`);

--
-- Indexes for table `dotp_gacl_axo`
--
ALTER TABLE `dotp_gacl_axo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
  ADD KEY `gacl_hidden_axo` (`hidden`);

--
-- Indexes for table `dotp_gacl_axo_groups`
--
ALTER TABLE `dotp_gacl_axo_groups`
  ADD PRIMARY KEY (`id`,`value`),
  ADD KEY `gacl_parent_id_axo_groups` (`parent_id`),
  ADD KEY `gacl_value_axo_groups` (`value`),
  ADD KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`);

--
-- Indexes for table `dotp_gacl_axo_groups_map`
--
ALTER TABLE `dotp_gacl_axo_groups_map`
  ADD PRIMARY KEY (`acl_id`,`group_id`);

--
-- Indexes for table `dotp_gacl_axo_map`
--
ALTER TABLE `dotp_gacl_axo_map`
  ADD PRIMARY KEY (`acl_id`,`section_value`,`value`);

--
-- Indexes for table `dotp_gacl_axo_sections`
--
ALTER TABLE `dotp_gacl_axo_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gacl_value_axo_sections` (`value`),
  ADD KEY `gacl_hidden_axo_sections` (`hidden`);

--
-- Indexes for table `dotp_gacl_groups_aro_map`
--
ALTER TABLE `dotp_gacl_groups_aro_map`
  ADD PRIMARY KEY (`group_id`,`aro_id`);

--
-- Indexes for table `dotp_gacl_groups_axo_map`
--
ALTER TABLE `dotp_gacl_groups_axo_map`
  ADD PRIMARY KEY (`group_id`,`axo_id`);

--
-- Indexes for table `dotp_gacl_phpgacl`
--
ALTER TABLE `dotp_gacl_phpgacl`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `dotp_modules`
--
ALTER TABLE `dotp_modules`
  ADD PRIMARY KEY (`mod_id`,`mod_directory`);

--
-- Indexes for table `dotp_permissions`
--
ALTER TABLE `dotp_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `idx_pgrant_on` (`permission_grant_on`,`permission_item`,`permission_user`),
  ADD KEY `idx_puser` (`permission_user`),
  ADD KEY `idx_pvalue` (`permission_value`);

--
-- Indexes for table `dotp_projects`
--
ALTER TABLE `dotp_projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_project_owner` (`project_owner`),
  ADD KEY `idx_sdate` (`project_start_date`),
  ADD KEY `idx_edate` (`project_end_date`),
  ADD KEY `project_short_name` (`project_short_name`),
  ADD KEY `idx_proj1` (`project_company`);

--
-- Indexes for table `dotp_roles`
--
ALTER TABLE `dotp_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `dotp_sessions`
--
ALTER TABLE `dotp_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `session_updated` (`session_updated`),
  ADD KEY `session_created` (`session_created`);

--
-- Indexes for table `dotp_syskeys`
--
ALTER TABLE `dotp_syskeys`
  ADD PRIMARY KEY (`syskey_id`),
  ADD UNIQUE KEY `syskey_name` (`syskey_name`),
  ADD UNIQUE KEY `idx_syskey_name` (`syskey_name`);

--
-- Indexes for table `dotp_sysvals`
--
ALTER TABLE `dotp_sysvals`
  ADD PRIMARY KEY (`sysval_id`),
  ADD UNIQUE KEY `idx_sysval_title` (`sysval_title`);

--
-- Indexes for table `dotp_tasks`
--
ALTER TABLE `dotp_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `idx_task_parent` (`task_parent`),
  ADD KEY `idx_task_project` (`task_project`),
  ADD KEY `idx_task_owner` (`task_owner`),
  ADD KEY `idx_task_order` (`task_order`),
  ADD KEY `idx_task1` (`task_start_date`),
  ADD KEY `idx_task2` (`task_end_date`);

--
-- Indexes for table `dotp_task_contacts`
--
ALTER TABLE `dotp_task_contacts`
  ADD KEY `idx_task_contacts` (`task_id`);

--
-- Indexes for table `dotp_task_departments`
--
ALTER TABLE `dotp_task_departments`
  ADD KEY `idx_task_departments` (`task_id`);

--
-- Indexes for table `dotp_task_dependencies`
--
ALTER TABLE `dotp_task_dependencies`
  ADD PRIMARY KEY (`dependencies_task_id`,`dependencies_req_task_id`);

--
-- Indexes for table `dotp_task_log`
--
ALTER TABLE `dotp_task_log`
  ADD PRIMARY KEY (`task_log_id`),
  ADD KEY `idx_log_task` (`task_log_task`);

--
-- Indexes for table `dotp_tickets`
--
ALTER TABLE `dotp_tickets`
  ADD PRIMARY KEY (`ticket`),
  ADD KEY `parent` (`parent`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `dotp_users`
--
ALTER TABLE `dotp_users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_uid` (`user_username`),
  ADD KEY `idx_pwd` (`user_password`),
  ADD KEY `idx_user_parent` (`user_parent`);

--
-- Indexes for table `dotp_user_access_log`
--
ALTER TABLE `dotp_user_access_log`
  ADD PRIMARY KEY (`user_access_log_id`);

--
-- Indexes for table `dotp_user_events`
--
ALTER TABLE `dotp_user_events`
  ADD KEY `uek1` (`user_id`,`event_id`),
  ADD KEY `uek2` (`event_id`,`user_id`);

--
-- Indexes for table `dotp_user_preferences`
--
ALTER TABLE `dotp_user_preferences`
  ADD KEY `pref_user` (`pref_user`,`pref_name`);

--
-- Indexes for table `dotp_user_tasks`
--
ALTER TABLE `dotp_user_tasks`
  ADD PRIMARY KEY (`user_id`,`task_id`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `idx_user_tasks` (`task_id`);

--
-- Indexes for table `dotp_user_task_pin`
--
ALTER TABLE `dotp_user_task_pin`
  ADD PRIMARY KEY (`user_id`,`task_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dotp_billingcode`
--
ALTER TABLE `dotp_billingcode`
  MODIFY `billingcode_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_common_notes`
--
ALTER TABLE `dotp_common_notes`
  MODIFY `note_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_companies`
--
ALTER TABLE `dotp_companies`
  MODIFY `company_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_config`
--
ALTER TABLE `dotp_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `dotp_config_list`
--
ALTER TABLE `dotp_config_list`
  MODIFY `config_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dotp_contacts`
--
ALTER TABLE `dotp_contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dotp_departments`
--
ALTER TABLE `dotp_departments`
  MODIFY `dept_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_events`
--
ALTER TABLE `dotp_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_event_queue`
--
ALTER TABLE `dotp_event_queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_files`
--
ALTER TABLE `dotp_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_file_folders`
--
ALTER TABLE `dotp_file_folders`
  MODIFY `file_folder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_forums`
--
ALTER TABLE `dotp_forums`
  MODIFY `forum_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_forum_messages`
--
ALTER TABLE `dotp_forum_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_modules`
--
ALTER TABLE `dotp_modules`
  MODIFY `mod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `dotp_permissions`
--
ALTER TABLE `dotp_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dotp_projects`
--
ALTER TABLE `dotp_projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_roles`
--
ALTER TABLE `dotp_roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_syskeys`
--
ALTER TABLE `dotp_syskeys`
  MODIFY `syskey_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dotp_sysvals`
--
ALTER TABLE `dotp_sysvals`
  MODIFY `sysval_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `dotp_tasks`
--
ALTER TABLE `dotp_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_task_log`
--
ALTER TABLE `dotp_task_log`
  MODIFY `task_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_tickets`
--
ALTER TABLE `dotp_tickets`
  MODIFY `ticket` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dotp_users`
--
ALTER TABLE `dotp_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dotp_user_access_log`
--
ALTER TABLE `dotp_user_access_log`
  MODIFY `user_access_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;




CREATE TABLE `files_count_max` (
  `file_lastversion` float NOT NULL DEFAULT '0',
  `file_version_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
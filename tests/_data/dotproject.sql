#
# SQL Export
# Created by Querious (301010)
# Created: June 18, 2022 at 2:29:14 PM CDT
# Encoding: Unicode (UTF-8)
#


SET @ORIG_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

SET @ORIG_UNIQUE_CHECKS = @@UNIQUE_CHECKS;
SET UNIQUE_CHECKS = 0;

SET @ORIG_TIME_ZONE = @@TIME_ZONE;
SET TIME_ZONE = '+00:00';

SET @ORIG_SQL_MODE = @@SQL_MODE;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';



CREATE TABLE `dotp_billingcode` (
  `billingcode_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `billingcode_name` varchar(25) NOT NULL DEFAULT '',
  `billingcode_value` float NOT NULL DEFAULT '0',
  `billingcode_desc` varchar(255) NOT NULL DEFAULT '',
  `billingcode_status` int(1) NOT NULL DEFAULT '0',
  `company_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`billingcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_common_notes` (
  `note_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `note_author` int(10) unsigned NOT NULL DEFAULT '0',
  `note_module` int(10) unsigned NOT NULL DEFAULT '0',
  `note_record_id` int(10) unsigned NOT NULL DEFAULT '0',
  `note_category` int(3) unsigned NOT NULL DEFAULT '0',
  `note_title` varchar(100) NOT NULL DEFAULT '',
  `note_body` text NOT NULL,
  `note_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_hours` float NOT NULL DEFAULT '0',
  `note_code` varchar(8) NOT NULL DEFAULT '',
  `note_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note_modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_companies` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
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
  `company_custom` longtext,
  PRIMARY KEY (`company_id`),
  KEY `idx_cpy1` (`company_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(127) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  `config_group` varchar(255) NOT NULL DEFAULT '',
  `config_type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name` (`config_name`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_config_list` (
  `config_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_id` int(11) NOT NULL DEFAULT '0',
  `config_list_name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_list_id`),
  KEY `config_id` (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `contact_owner` int(10) unsigned DEFAULT '0',
  `contact_private` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`contact_id`),
  KEY `idx_oby` (`contact_order_by`),
  KEY `idx_co` (`contact_company`),
  KEY `idx_prp` (`contact_project`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_custom_fields_lists` (
  `field_id` int(11) DEFAULT NULL,
  `list_option_id` int(11) DEFAULT NULL,
  `list_value` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_custom_fields_struct` (
  `field_id` int(11) NOT NULL,
  `field_module` varchar(30) DEFAULT NULL,
  `field_page` varchar(30) DEFAULT NULL,
  `field_htmltype` varchar(20) DEFAULT NULL,
  `field_datatype` varchar(20) DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `field_name` varchar(100) DEFAULT NULL,
  `field_extratags` varchar(250) DEFAULT NULL,
  `field_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_custom_fields_values` (
  `value_id` int(11) DEFAULT NULL,
  `value_module` varchar(30) DEFAULT NULL,
  `value_object_id` int(11) DEFAULT NULL,
  `value_field_id` int(11) DEFAULT NULL,
  `value_charvalue` varchar(250) DEFAULT NULL,
  `value_intvalue` int(11) DEFAULT NULL,
  KEY `idx_cfv_id` (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_departments` (
  `dept_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dept_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `dept_company` int(10) unsigned NOT NULL DEFAULT '0',
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
  `dept_owner` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Department heirarchy under a company';


CREATE TABLE `dotp_dotpermissions` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(80) NOT NULL DEFAULT '',
  `section` varchar(80) NOT NULL DEFAULT '',
  `axo` varchar(80) NOT NULL DEFAULT '',
  `permission` varchar(80) NOT NULL DEFAULT '',
  `allow` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`,`section`,`permission`,`axo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_dpversion` (
  `code_version` varchar(10) NOT NULL DEFAULT '',
  `db_version` int(11) NOT NULL DEFAULT '0',
  `last_db_update` date NOT NULL DEFAULT '0000-00-00',
  `last_code_update` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_event_queue` (
  `queue_id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_start` int(11) NOT NULL DEFAULT '0',
  `queue_type` varchar(40) NOT NULL DEFAULT '',
  `queue_repeat_interval` int(11) NOT NULL DEFAULT '0',
  `queue_repeat_count` int(11) NOT NULL DEFAULT '0',
  `queue_data` longblob NOT NULL,
  `queue_callback` varchar(127) NOT NULL DEFAULT '',
  `queue_owner` int(11) NOT NULL DEFAULT '0',
  `queue_origin_id` int(11) NOT NULL DEFAULT '0',
  `queue_module` varchar(40) NOT NULL DEFAULT '',
  `queue_batched` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`queue_id`),
  KEY `queue_start` (`queue_batched`,`queue_start`),
  KEY `queue_module` (`queue_module`),
  KEY `queue_type` (`queue_type`),
  KEY `queue_origin_id` (`queue_origin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(255) NOT NULL DEFAULT '',
  `event_start_date` datetime DEFAULT NULL,
  `event_end_date` datetime DEFAULT NULL,
  `event_parent` int(11) unsigned NOT NULL DEFAULT '0',
  `event_description` text,
  `event_times_recuring` int(11) unsigned NOT NULL DEFAULT '0',
  `event_recurs` int(11) unsigned NOT NULL DEFAULT '0',
  `event_remind` int(10) unsigned NOT NULL DEFAULT '0',
  `event_icon` varchar(20) DEFAULT 'obj/event',
  `event_owner` int(11) DEFAULT '0',
  `event_project` int(11) DEFAULT '0',
  `event_private` tinyint(3) DEFAULT '0',
  `event_type` tinyint(3) DEFAULT '0',
  `event_cwd` tinyint(3) DEFAULT '0',
  `event_notify` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  KEY `id_esd` (`event_start_date`),
  KEY `id_eed` (`event_end_date`),
  KEY `id_evp` (`event_parent`),
  KEY `idx_ev1` (`event_owner`),
  KEY `idx_ev2` (`event_project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_file_folders` (
  `file_folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_folder_parent` int(11) NOT NULL DEFAULT '0',
  `file_folder_name` varchar(255) NOT NULL DEFAULT '',
  `file_folder_description` text,
  PRIMARY KEY (`file_folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `file_version_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `idx_file_task` (`file_task`),
  KEY `idx_file_project` (`file_project`),
  KEY `idx_file_parent` (`file_parent`),
  KEY `idx_file_vid` (`file_version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_files_index` (
  `file_id` int(11) NOT NULL DEFAULT '0',
  `word` varchar(50) NOT NULL DEFAULT '',
  `word_placement` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`,`word`,`word_placement`),
  KEY `idx_fwrd` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_forum_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_forum` int(11) NOT NULL DEFAULT '0',
  `message_parent` int(11) NOT NULL DEFAULT '0',
  `message_author` int(11) NOT NULL DEFAULT '0',
  `message_editor` int(11) NOT NULL DEFAULT '0',
  `message_title` varchar(255) NOT NULL DEFAULT '',
  `message_date` datetime DEFAULT '0000-00-00 00:00:00',
  `message_body` text,
  `message_published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`message_id`),
  KEY `idx_mparent` (`message_parent`),
  KEY `idx_mdate` (`message_date`),
  KEY `idx_mforum` (`message_forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_forum_visits` (
  `visit_user` int(10) NOT NULL DEFAULT '0',
  `visit_forum` int(10) NOT NULL DEFAULT '0',
  `visit_message` int(10) NOT NULL DEFAULT '0',
  `visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_fv` (`visit_user`,`visit_forum`,`visit_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_forum_watch` (
  `watch_user` int(10) unsigned NOT NULL DEFAULT '0',
  `watch_forum` int(10) unsigned DEFAULT NULL,
  `watch_topic` int(10) unsigned DEFAULT NULL,
  KEY `idx_fw1` (`watch_user`,`watch_forum`),
  KEY `idx_fw2` (`watch_user`,`watch_topic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Links users to the forums/messages they are watching';


CREATE TABLE `dotp_forums` (
  `forum_id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_project` int(11) NOT NULL DEFAULT '0',
  `forum_status` tinyint(4) NOT NULL DEFAULT '-1',
  `forum_owner` int(11) NOT NULL DEFAULT '0',
  `forum_name` varchar(50) NOT NULL DEFAULT '',
  `forum_create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `forum_last_date` datetime DEFAULT '0000-00-00 00:00:00',
  `forum_last_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_message_count` int(11) NOT NULL DEFAULT '0',
  `forum_description` varchar(255) DEFAULT NULL,
  `forum_moderated` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`),
  KEY `idx_fproject` (`forum_project`),
  KEY `idx_fowner` (`forum_owner`),
  KEY `forum_status` (`forum_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_acl` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT 'system',
  `allow` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0',
  `return_value` longtext,
  `note` longtext,
  `updated_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `gacl_enabled_acl` (`enabled`),
  KEY `gacl_section_value_acl` (`section_value`),
  KEY `gacl_updated_date_acl` (`updated_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_acl_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_acl_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aco` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
  KEY `gacl_hidden_aco` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aco_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aco_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aco_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aco_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  KEY `gacl_hidden_aro` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_groups` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`value`),
  KEY `gacl_parent_id_aro_groups` (`parent_id`),
  KEY `gacl_value_aro_groups` (`value`),
  KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_aro_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
  KEY `gacl_hidden_axo` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_groups` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`value`),
  KEY `gacl_parent_id_axo_groups` (`parent_id`),
  KEY `gacl_value_axo_groups` (`value`),
  KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_axo_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`aro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `axo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`axo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_gacl_phpgacl` (
  `name` varchar(127) NOT NULL DEFAULT '',
  `value` varchar(230) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_modules` (
  `mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_name` varchar(64) NOT NULL DEFAULT '',
  `mod_directory` varchar(64) NOT NULL DEFAULT '',
  `mod_version` varchar(10) NOT NULL DEFAULT '',
  `mod_setup_class` varchar(64) NOT NULL DEFAULT '',
  `mod_type` varchar(64) NOT NULL DEFAULT '',
  `mod_active` int(1) unsigned NOT NULL DEFAULT '0',
  `mod_ui_name` varchar(20) NOT NULL DEFAULT '',
  `mod_ui_icon` varchar(64) NOT NULL DEFAULT '',
  `mod_ui_order` tinyint(3) NOT NULL DEFAULT '0',
  `mod_ui_active` int(1) unsigned NOT NULL DEFAULT '0',
  `mod_description` varchar(255) NOT NULL DEFAULT '',
  `permissions_item_table` char(100) DEFAULT NULL,
  `permissions_item_field` char(100) DEFAULT NULL,
  `permissions_item_label` char(100) DEFAULT NULL,
  PRIMARY KEY (`mod_id`,`mod_directory`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_user` int(11) NOT NULL DEFAULT '0',
  `permission_grant_on` varchar(12) NOT NULL DEFAULT '',
  `permission_item` int(11) NOT NULL DEFAULT '0',
  `permission_value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `idx_pgrant_on` (`permission_grant_on`,`permission_item`,`permission_user`),
  KEY `idx_puser` (`permission_user`),
  KEY `idx_pvalue` (`permission_value`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_project_contacts` (
  `project_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_project_departments` (
  `project_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `project_private` tinyint(3) unsigned DEFAULT '0',
  `project_departments` char(100) DEFAULT NULL,
  `project_contacts` char(100) DEFAULT NULL,
  `project_priority` tinyint(4) DEFAULT '0',
  `project_type` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`),
  KEY `idx_project_owner` (`project_owner`),
  KEY `idx_sdate` (`project_start_date`),
  KEY `idx_edate` (`project_end_date`),
  KEY `project_short_name` (`project_short_name`),
  KEY `idx_proj1` (`project_company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(24) NOT NULL DEFAULT '',
  `role_description` varchar(255) NOT NULL DEFAULT '',
  `role_type` int(3) unsigned NOT NULL DEFAULT '0',
  `role_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_sessions` (
  `session_id` varchar(60) NOT NULL DEFAULT '',
  `session_user` int(11) NOT NULL DEFAULT '0',
  `session_data` longblob,
  `session_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`session_id`),
  KEY `session_updated` (`session_updated`),
  KEY `session_created` (`session_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_syskeys` (
  `syskey_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `syskey_name` varchar(48) NOT NULL DEFAULT '',
  `syskey_label` varchar(255) NOT NULL DEFAULT '',
  `syskey_type` int(1) unsigned NOT NULL DEFAULT '0',
  `syskey_sep1` char(2) DEFAULT '\n',
  `syskey_sep2` char(2) NOT NULL DEFAULT '|',
  PRIMARY KEY (`syskey_id`),
  UNIQUE KEY `syskey_name` (`syskey_name`),
  UNIQUE KEY `idx_syskey_name` (`syskey_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_sysvals` (
  `sysval_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sysval_key_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sysval_title` varchar(48) NOT NULL DEFAULT '',
  `sysval_value` text NOT NULL,
  PRIMARY KEY (`sysval_id`),
  UNIQUE KEY `idx_sysval_title` (`sysval_title`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_task_contacts` (
  `task_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL,
  KEY `idx_task_contacts` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_task_departments` (
  `task_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL,
  KEY `idx_task_departments` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_task_dependencies` (
  `dependencies_task_id` int(11) NOT NULL,
  `dependencies_req_task_id` int(11) NOT NULL,
  PRIMARY KEY (`dependencies_task_id`,`dependencies_req_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_task_log` (
  `task_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_log_task` int(11) NOT NULL DEFAULT '0',
  `task_log_name` varchar(255) DEFAULT NULL,
  `task_log_description` text,
  `task_log_creator` int(11) NOT NULL DEFAULT '0',
  `task_log_hours` float NOT NULL DEFAULT '0',
  `task_log_date` datetime DEFAULT NULL,
  `task_log_costcode` varchar(8) NOT NULL DEFAULT '',
  `task_log_problem` tinyint(1) DEFAULT '0',
  `task_log_reference` tinyint(4) DEFAULT '0',
  `task_log_related_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`task_log_id`),
  KEY `idx_log_task` (`task_log_task`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(255) DEFAULT NULL,
  `task_parent` int(11) DEFAULT '0',
  `task_milestone` tinyint(1) DEFAULT '0',
  `task_project` int(11) NOT NULL DEFAULT '0',
  `task_owner` int(11) NOT NULL DEFAULT '0',
  `task_start_date` datetime DEFAULT NULL,
  `task_duration` float unsigned DEFAULT '0',
  `task_duration_type` int(11) NOT NULL DEFAULT '1',
  `task_hours_worked` float unsigned DEFAULT '0',
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
  `task_type` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `idx_task_parent` (`task_parent`),
  KEY `idx_task_project` (`task_project`),
  KEY `idx_task_owner` (`task_owner`),
  KEY `idx_task_order` (`task_order`),
  KEY `idx_task1` (`task_start_date`),
  KEY `idx_task2` (`task_end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_tickets` (
  `ticket` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_company` int(10) NOT NULL DEFAULT '0',
  `ticket_project` int(10) NOT NULL DEFAULT '0',
  `author` varchar(100) NOT NULL DEFAULT '',
  `recipient` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `attachment` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL DEFAULT '',
  `assignment` int(10) unsigned NOT NULL DEFAULT '0',
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `activity` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `cc` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `signature` text,
  PRIMARY KEY (`ticket`),
  KEY `parent` (`parent`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_user_access_log` (
  `user_access_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_ip` varchar(15) NOT NULL,
  `date_time_in` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_out` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_last_action` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_access_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_user_events` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  KEY `uek1` (`user_id`,`event_id`),
  KEY `uek2` (`event_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_user_preferences` (
  `pref_user` varchar(12) NOT NULL DEFAULT '',
  `pref_name` varchar(72) NOT NULL DEFAULT '',
  `pref_value` varchar(32) NOT NULL DEFAULT '',
  KEY `pref_user` (`pref_user`,`pref_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_user_roles` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_user_task_pin` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `task_id` int(10) NOT NULL DEFAULT '0',
  `task_pinned` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_user_tasks` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(4) NOT NULL DEFAULT '0',
  `task_id` int(11) NOT NULL DEFAULT '0',
  `perc_assignment` int(11) NOT NULL DEFAULT '100',
  `user_task_priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`user_id`,`task_id`),
  KEY `user_type` (`user_type`),
  KEY `idx_user_tasks` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `dotp_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_contact` int(11) NOT NULL DEFAULT '0',
  `user_username` varchar(255) NOT NULL DEFAULT '',
  `user_password` varchar(32) NOT NULL DEFAULT '',
  `user_parent` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(3) NOT NULL DEFAULT '0',
  `user_company` int(11) DEFAULT '0',
  `user_department` int(11) DEFAULT '0',
  `user_owner` int(11) NOT NULL DEFAULT '0',
  `user_signature` text,
  PRIMARY KEY (`user_id`),
  KEY `idx_uid` (`user_username`),
  KEY `idx_pwd` (`user_password`),
  KEY `idx_user_parent` (`user_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;




LOCK TABLES `dotp_billingcode` WRITE;
TRUNCATE `dotp_billingcode`;
UNLOCK TABLES;


LOCK TABLES `dotp_common_notes` WRITE;
TRUNCATE `dotp_common_notes`;
UNLOCK TABLES;


LOCK TABLES `dotp_companies` WRITE;
TRUNCATE `dotp_companies`;
UNLOCK TABLES;


LOCK TABLES `dotp_config` WRITE;
TRUNCATE `dotp_config`;
INSERT INTO `dotp_config` (`config_id`, `config_name`, `config_value`, `config_group`, `config_type`) VALUES 
	(1,'host_locale','en','ui','text'),
	(2,'check_overallocation','false','tasks','checkbox'),
	(3,'currency_symbol','$','ui','text'),
	(4,'host_style','default','ui','text'),
	(5,'company_name','My Company','ui','text'),
	(6,'page_title','dotProject','ui','text'),
	(7,'site_domain','example.com','ui','text'),
	(8,'email_prefix','[dotProject]','ui','text'),
	(9,'admin_username','admin','ui','text'),
	(10,'username_min_len','4','auth','text'),
	(11,'password_min_len','4','auth','text'),
	(12,'enable_gantt_charts','true','tasks','checkbox'),
	(13,'log_changes','false','','checkbox'),
	(14,'check_task_dates','true','tasks','checkbox'),
	(15,'check_task_empty_dynamic','false','tasks','checkbox'),
	(16,'locale_warn','false','ui','checkbox'),
	(17,'locale_alert','^','ui','text'),
	(18,'daily_working_hours','8.0','tasks','text'),
	(19,'display_debug','false','ui','checkbox'),
	(20,'link_tickets_kludge','false','tasks','checkbox'),
	(21,'show_all_task_assignees','false','tasks','checkbox'),
	(22,'direct_edit_assignment','false','tasks','checkbox'),
	(23,'restrict_color_selection','false','ui','checkbox'),
	(24,'cal_day_view_show_minical','true','calendar','checkbox'),
	(25,'cal_day_start','8','calendar','text'),
	(26,'cal_day_end','17','calendar','text'),
	(27,'cal_day_increment','15','calendar','text'),
	(28,'cal_working_days','1,2,3,4,5','calendar','text'),
	(29,'restrict_task_time_editing','false','tasks','checkbox'),
	(30,'default_view_m','calendar','ui','text'),
	(31,'default_view_a','day_view','ui','text'),
	(32,'default_view_tab','1','ui','text'),
	(33,'index_max_file_size','-1','file','text'),
	(34,'session_handling','app','session','select'),
	(35,'session_idle_time','2d','session','text'),
	(36,'session_max_lifetime','1m','session','text'),
	(37,'debug','1','','text'),
	(38,'parser_default','/usr/bin/strings','file','text'),
	(39,'parser_application/msword','/usr/bin/strings','file','text'),
	(40,'parser_text/html','/usr/bin/strings','file','text'),
	(41,'parser_application/pdf','/usr/bin/pdftotext','file','text'),
	(42,'files_ci_preserve_attr','true','file','checkbox'),
	(43,'files_show_versions_edit','false','file','checkbox'),
	(44,'auth_method','sql','auth','select'),
	(45,'ldap_host','localhost','ldap','text'),
	(46,'ldap_port','389','ldap','text'),
	(47,'ldap_version','3','ldap','text'),
	(48,'ldap_base_dn','dc=saki,dc=com,dc=au','ldap','text'),
	(49,'ldap_user_filter','(uid=%USERNAME%)','ldap','text'),
	(50,'postnuke_allow_login','true','auth','checkbox'),
	(51,'reset_memory_limit','32M','tasks','text'),
	(52,'mail_transport','php','mail','select'),
	(53,'mail_host','localhost','mail','text'),
	(54,'mail_port','25','mail','text'),
	(55,'mail_auth','false','mail','checkbox'),
	(56,'mail_user','','mail','text'),
	(57,'mail_pass','','mail','password'),
	(58,'mail_defer','false','mail','checkbox'),
	(59,'mail_timeout','30','mail','text'),
	(60,'session_gc_scan_queue','false','session','checkbox'),
	(61,'task_reminder_control','false','task_reminder','checkbox'),
	(62,'task_reminder_days_before','1','task_reminder','text'),
	(63,'task_reminder_repeat','100','task_reminder','text'),
	(64,'gacl_cache','false','gacl','checkbox'),
	(65,'gacl_expire','true','gacl','checkbox'),
	(66,'gacl_cache_dir','/tmp','gacl','text'),
	(67,'gacl_timeout','600','gacl','text'),
	(68,'mail_smtp_tls','false','mail','checkbox'),
	(69,'ldap_search_user','Manager','ldap','text'),
	(70,'ldap_search_pass','secret','ldap','password'),
	(71,'ldap_allow_login','true','ldap','checkbox'),
	(72,'user_contact_inactivate','true','auth','checkbox'),
	(73,'user_contact_activate','false','auth','checkbox'),
	(74,'task_reminder_batch','false','task_reminder','checkbox');
UNLOCK TABLES;


LOCK TABLES `dotp_config_list` WRITE;
TRUNCATE `dotp_config_list`;
INSERT INTO `dotp_config_list` (`config_list_id`, `config_id`, `config_list_name`) VALUES 
	(1,44,'sql'),
	(2,44,'ldap'),
	(3,44,'pn'),
	(4,34,'app'),
	(5,34,'php'),
	(6,52,'php'),
	(7,52,'smtp');
UNLOCK TABLES;


LOCK TABLES `dotp_contacts` WRITE;
TRUNCATE `dotp_contacts`;
INSERT INTO `dotp_contacts` (`contact_id`, `contact_first_name`, `contact_last_name`, `contact_order_by`, `contact_title`, `contact_birthday`, `contact_job`, `contact_company`, `contact_department`, `contact_type`, `contact_email`, `contact_email2`, `contact_url`, `contact_phone`, `contact_phone2`, `contact_fax`, `contact_mobile`, `contact_address1`, `contact_address2`, `contact_city`, `contact_state`, `contact_zip`, `contact_country`, `contact_jabber`, `contact_icq`, `contact_msn`, `contact_yahoo`, `contact_aol`, `contact_notes`, `contact_project`, `contact_icon`, `contact_owner`, `contact_private`) VALUES 
	(1,'Admin','Person','',NULL,NULL,NULL,'',NULL,NULL,'admin@example.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'obj/contact',0,0);
UNLOCK TABLES;


LOCK TABLES `dotp_custom_fields_lists` WRITE;
TRUNCATE `dotp_custom_fields_lists`;
UNLOCK TABLES;


LOCK TABLES `dotp_custom_fields_struct` WRITE;
TRUNCATE `dotp_custom_fields_struct`;
UNLOCK TABLES;


LOCK TABLES `dotp_custom_fields_values` WRITE;
TRUNCATE `dotp_custom_fields_values`;
UNLOCK TABLES;


LOCK TABLES `dotp_departments` WRITE;
TRUNCATE `dotp_departments`;
UNLOCK TABLES;


LOCK TABLES `dotp_dotpermissions` WRITE;
TRUNCATE `dotp_dotpermissions`;
INSERT INTO `dotp_dotpermissions` (`acl_id`, `user_id`, `section`, `axo`, `permission`, `allow`, `priority`, `enabled`) VALUES 
	(12,'1','sys','acl','access',1,3,1),
	(11,'1','app','admin','access',1,4,1),
	(11,'1','app','calendar','access',1,4,1),
	(11,'1','app','events','access',1,4,1),
	(11,'1','app','companies','access',1,4,1),
	(11,'1','app','contacts','access',1,4,1),
	(11,'1','app','departments','access',1,4,1),
	(11,'1','app','files','access',1,4,1),
	(11,'1','app','file_folders','access',1,4,1),
	(11,'1','app','forums','access',1,4,1),
	(11,'1','app','help','access',1,4,1),
	(11,'1','app','projects','access',1,4,1),
	(11,'1','app','system','access',1,4,1),
	(11,'1','app','tasks','access',1,4,1),
	(11,'1','app','task_log','access',1,4,1),
	(11,'1','app','ticketsmith','access',1,4,1),
	(11,'1','app','public','access',1,4,1),
	(11,'1','app','roles','access',1,4,1),
	(11,'1','app','users','access',1,4,1),
	(11,'1','app','admin','add',1,4,1),
	(11,'1','app','calendar','add',1,4,1),
	(11,'1','app','events','add',1,4,1),
	(11,'1','app','companies','add',1,4,1),
	(11,'1','app','contacts','add',1,4,1),
	(11,'1','app','departments','add',1,4,1),
	(11,'1','app','files','add',1,4,1),
	(11,'1','app','file_folders','add',1,4,1),
	(11,'1','app','forums','add',1,4,1),
	(11,'1','app','help','add',1,4,1),
	(11,'1','app','projects','add',1,4,1),
	(11,'1','app','system','add',1,4,1),
	(11,'1','app','tasks','add',1,4,1),
	(11,'1','app','task_log','add',1,4,1),
	(11,'1','app','ticketsmith','add',1,4,1),
	(11,'1','app','public','add',1,4,1),
	(11,'1','app','roles','add',1,4,1),
	(11,'1','app','users','add',1,4,1),
	(11,'1','app','admin','delete',1,4,1),
	(11,'1','app','calendar','delete',1,4,1),
	(11,'1','app','events','delete',1,4,1),
	(11,'1','app','companies','delete',1,4,1),
	(11,'1','app','contacts','delete',1,4,1),
	(11,'1','app','departments','delete',1,4,1),
	(11,'1','app','files','delete',1,4,1),
	(11,'1','app','file_folders','delete',1,4,1),
	(11,'1','app','forums','delete',1,4,1),
	(11,'1','app','help','delete',1,4,1),
	(11,'1','app','projects','delete',1,4,1),
	(11,'1','app','system','delete',1,4,1),
	(11,'1','app','tasks','delete',1,4,1),
	(11,'1','app','task_log','delete',1,4,1),
	(11,'1','app','ticketsmith','delete',1,4,1),
	(11,'1','app','public','delete',1,4,1),
	(11,'1','app','roles','delete',1,4,1),
	(11,'1','app','users','delete',1,4,1),
	(11,'1','app','admin','edit',1,4,1),
	(11,'1','app','calendar','edit',1,4,1),
	(11,'1','app','events','edit',1,4,1),
	(11,'1','app','companies','edit',1,4,1),
	(11,'1','app','contacts','edit',1,4,1),
	(11,'1','app','departments','edit',1,4,1),
	(11,'1','app','files','edit',1,4,1),
	(11,'1','app','file_folders','edit',1,4,1),
	(11,'1','app','forums','edit',1,4,1),
	(11,'1','app','help','edit',1,4,1),
	(11,'1','app','projects','edit',1,4,1),
	(11,'1','app','system','edit',1,4,1),
	(11,'1','app','tasks','edit',1,4,1),
	(11,'1','app','task_log','edit',1,4,1),
	(11,'1','app','ticketsmith','edit',1,4,1),
	(11,'1','app','public','edit',1,4,1),
	(11,'1','app','roles','edit',1,4,1),
	(11,'1','app','users','edit',1,4,1),
	(11,'1','app','admin','view',1,4,1),
	(11,'1','app','calendar','view',1,4,1),
	(11,'1','app','events','view',1,4,1),
	(11,'1','app','companies','view',1,4,1),
	(11,'1','app','contacts','view',1,4,1),
	(11,'1','app','departments','view',1,4,1),
	(11,'1','app','files','view',1,4,1),
	(11,'1','app','file_folders','view',1,4,1),
	(11,'1','app','forums','view',1,4,1),
	(11,'1','app','help','view',1,4,1),
	(11,'1','app','projects','view',1,4,1),
	(11,'1','app','system','view',1,4,1),
	(11,'1','app','tasks','view',1,4,1),
	(11,'1','app','task_log','view',1,4,1),
	(11,'1','app','ticketsmith','view',1,4,1),
	(11,'1','app','public','view',1,4,1),
	(11,'1','app','roles','view',1,4,1),
	(11,'1','app','users','view',1,4,1);
UNLOCK TABLES;


LOCK TABLES `dotp_dpversion` WRITE;
TRUNCATE `dotp_dpversion`;
INSERT INTO `dotp_dpversion` (`code_version`, `db_version`, `last_db_update`, `last_code_update`) VALUES 
	('2.2.0',2,'0000-00-00','0000-00-00');
UNLOCK TABLES;


LOCK TABLES `dotp_event_queue` WRITE;
TRUNCATE `dotp_event_queue`;
UNLOCK TABLES;


LOCK TABLES `dotp_events` WRITE;
TRUNCATE `dotp_events`;
UNLOCK TABLES;


LOCK TABLES `dotp_file_folders` WRITE;
TRUNCATE `dotp_file_folders`;
UNLOCK TABLES;


LOCK TABLES `dotp_files` WRITE;
TRUNCATE `dotp_files`;
UNLOCK TABLES;


LOCK TABLES `dotp_files_index` WRITE;
TRUNCATE `dotp_files_index`;
UNLOCK TABLES;


LOCK TABLES `dotp_forum_messages` WRITE;
TRUNCATE `dotp_forum_messages`;
UNLOCK TABLES;


LOCK TABLES `dotp_forum_visits` WRITE;
TRUNCATE `dotp_forum_visits`;
UNLOCK TABLES;


LOCK TABLES `dotp_forum_watch` WRITE;
TRUNCATE `dotp_forum_watch`;
UNLOCK TABLES;


LOCK TABLES `dotp_forums` WRITE;
TRUNCATE `dotp_forums`;
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_acl` WRITE;
TRUNCATE `dotp_gacl_acl`;
INSERT INTO `dotp_gacl_acl` (`id`, `section_value`, `allow`, `enabled`, `return_value`, `note`, `updated_date`) VALUES 
	(10,'user',1,1,NULL,NULL,1655580426),
	(11,'user',1,1,NULL,NULL,1655580426),
	(12,'user',1,1,NULL,NULL,1655580426),
	(13,'user',1,1,NULL,NULL,1655580426),
	(14,'user',1,1,NULL,NULL,1655580426),
	(15,'user',1,1,NULL,NULL,1655580426),
	(16,'user',1,1,NULL,NULL,1655580426);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_acl_sections` WRITE;
TRUNCATE `dotp_gacl_acl_sections`;
INSERT INTO `dotp_gacl_acl_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(1,'system',1,'System',0),
	(2,'user',2,'User',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_acl_seq` WRITE;
TRUNCATE `dotp_gacl_acl_seq`;
INSERT INTO `dotp_gacl_acl_seq` (`id`) VALUES 
	(16);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aco` WRITE;
TRUNCATE `dotp_gacl_aco`;
INSERT INTO `dotp_gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(10,'system','login',1,'Login',0),
	(11,'application','access',1,'Access',0),
	(12,'application','view',2,'View',0),
	(13,'application','add',3,'Add',0),
	(14,'application','edit',4,'Edit',0),
	(15,'application','delete',5,'Delete',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aco_map` WRITE;
TRUNCATE `dotp_gacl_aco_map`;
INSERT INTO `dotp_gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES 
	(10,'system','login'),
	(11,'application','access'),
	(11,'application','add'),
	(11,'application','delete'),
	(11,'application','edit'),
	(11,'application','view'),
	(12,'application','access'),
	(13,'application','access'),
	(13,'application','view'),
	(14,'application','access'),
	(15,'application','access'),
	(15,'application','add'),
	(15,'application','delete'),
	(15,'application','edit'),
	(15,'application','view'),
	(16,'application','access'),
	(16,'application','view');
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aco_sections` WRITE;
TRUNCATE `dotp_gacl_aco_sections`;
INSERT INTO `dotp_gacl_aco_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(10,'system',1,'System',0),
	(11,'application',2,'Application',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aco_sections_seq` WRITE;
TRUNCATE `dotp_gacl_aco_sections_seq`;
INSERT INTO `dotp_gacl_aco_sections_seq` (`id`) VALUES 
	(11);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aco_seq` WRITE;
TRUNCATE `dotp_gacl_aco_seq`;
INSERT INTO `dotp_gacl_aco_seq` (`id`) VALUES 
	(15);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro` WRITE;
TRUNCATE `dotp_gacl_aro`;
INSERT INTO `dotp_gacl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(10,'user','1',1,'admin',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_groups` WRITE;
TRUNCATE `dotp_gacl_aro_groups`;
INSERT INTO `dotp_gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES 
	(10,0,1,10,'Roles','role'),
	(11,10,2,3,'Administrator','admin'),
	(12,10,4,5,'Anonymous','anon'),
	(13,10,6,7,'Guest','guest'),
	(14,10,8,9,'Project worker','normal');
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_groups_id_seq` WRITE;
TRUNCATE `dotp_gacl_aro_groups_id_seq`;
INSERT INTO `dotp_gacl_aro_groups_id_seq` (`id`) VALUES 
	(14);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_groups_map` WRITE;
TRUNCATE `dotp_gacl_aro_groups_map`;
INSERT INTO `dotp_gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES 
	(10,10),
	(11,11),
	(12,11),
	(13,13),
	(14,12),
	(15,14),
	(16,13),
	(16,14);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_map` WRITE;
TRUNCATE `dotp_gacl_aro_map`;
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_sections` WRITE;
TRUNCATE `dotp_gacl_aro_sections`;
INSERT INTO `dotp_gacl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(10,'user',1,'Users',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_sections_seq` WRITE;
TRUNCATE `dotp_gacl_aro_sections_seq`;
INSERT INTO `dotp_gacl_aro_sections_seq` (`id`) VALUES 
	(10);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_aro_seq` WRITE;
TRUNCATE `dotp_gacl_aro_seq`;
INSERT INTO `dotp_gacl_aro_seq` (`id`) VALUES 
	(10);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo` WRITE;
TRUNCATE `dotp_gacl_axo`;
INSERT INTO `dotp_gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(10,'sys','acl',1,'ACL Administration',0),
	(11,'app','admin',1,'User Administration',0),
	(12,'app','calendar',2,'Calendar',0),
	(13,'app','events',2,'Events',0),
	(14,'app','companies',3,'Companies',0),
	(15,'app','contacts',4,'Contacts',0),
	(16,'app','departments',5,'Departments',0),
	(17,'app','files',6,'Files',0),
	(18,'app','file_folders',6,'File Folders',0),
	(19,'app','forums',7,'Forums',0),
	(20,'app','help',8,'Help',0),
	(21,'app','projects',9,'Projects',0),
	(22,'app','system',10,'System Administration',0),
	(23,'app','tasks',11,'Tasks',0),
	(24,'app','task_log',11,'Task Logs',0),
	(25,'app','ticketsmith',12,'Tickets',0),
	(26,'app','public',13,'Public',0),
	(27,'app','roles',14,'Roles Administration',0),
	(28,'app','users',15,'User Table',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_groups` WRITE;
TRUNCATE `dotp_gacl_axo_groups`;
INSERT INTO `dotp_gacl_axo_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES 
	(10,0,1,8,'Modules','mod'),
	(11,10,2,3,'All Modules','all'),
	(12,10,4,5,'Admin Modules','admin'),
	(13,10,6,7,'Non-Admin Modules','non_admin');
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_groups_id_seq` WRITE;
TRUNCATE `dotp_gacl_axo_groups_id_seq`;
INSERT INTO `dotp_gacl_axo_groups_id_seq` (`id`) VALUES 
	(13);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_groups_map` WRITE;
TRUNCATE `dotp_gacl_axo_groups_map`;
INSERT INTO `dotp_gacl_axo_groups_map` (`acl_id`, `group_id`) VALUES 
	(11,11),
	(13,13),
	(14,13),
	(15,13);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_map` WRITE;
TRUNCATE `dotp_gacl_axo_map`;
INSERT INTO `dotp_gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES 
	(12,'sys','acl'),
	(16,'app','users');
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_sections` WRITE;
TRUNCATE `dotp_gacl_axo_sections`;
INSERT INTO `dotp_gacl_axo_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES 
	(10,'sys',1,'System',0),
	(11,'app',2,'Application',0);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_sections_seq` WRITE;
TRUNCATE `dotp_gacl_axo_sections_seq`;
INSERT INTO `dotp_gacl_axo_sections_seq` (`id`) VALUES 
	(11);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_axo_seq` WRITE;
TRUNCATE `dotp_gacl_axo_seq`;
INSERT INTO `dotp_gacl_axo_seq` (`id`) VALUES 
	(28);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_groups_aro_map` WRITE;
TRUNCATE `dotp_gacl_groups_aro_map`;
INSERT INTO `dotp_gacl_groups_aro_map` (`group_id`, `aro_id`) VALUES 
	(11,10);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_groups_axo_map` WRITE;
TRUNCATE `dotp_gacl_groups_axo_map`;
INSERT INTO `dotp_gacl_groups_axo_map` (`group_id`, `axo_id`) VALUES 
	(11,11),
	(11,12),
	(11,13),
	(11,14),
	(11,15),
	(11,16),
	(11,17),
	(11,18),
	(11,19),
	(11,20),
	(11,21),
	(11,22),
	(11,23),
	(11,24),
	(11,25),
	(11,26),
	(11,27),
	(11,28),
	(12,11),
	(12,22),
	(12,27),
	(12,28),
	(13,12),
	(13,13),
	(13,14),
	(13,15),
	(13,16),
	(13,17),
	(13,18),
	(13,19),
	(13,20),
	(13,21),
	(13,23),
	(13,24),
	(13,25),
	(13,26);
UNLOCK TABLES;


LOCK TABLES `dotp_gacl_phpgacl` WRITE;
TRUNCATE `dotp_gacl_phpgacl`;
INSERT INTO `dotp_gacl_phpgacl` (`name`, `value`) VALUES 
	('schema_version','2.1'),
	('version','3.3.2');
UNLOCK TABLES;


LOCK TABLES `dotp_modules` WRITE;
TRUNCATE `dotp_modules`;
INSERT INTO `dotp_modules` (`mod_id`, `mod_name`, `mod_directory`, `mod_version`, `mod_setup_class`, `mod_type`, `mod_active`, `mod_ui_name`, `mod_ui_icon`, `mod_ui_order`, `mod_ui_active`, `mod_description`, `permissions_item_table`, `permissions_item_field`, `permissions_item_label`) VALUES 
	(1,'Companies','companies','1.0.0','','core',1,'Companies','handshake.png',1,1,'','companies','company_id','company_name'),
	(2,'Projects','projects','1.0.0','','core',1,'Projects','applet3-48.png',2,1,'','projects','project_id','project_name'),
	(3,'Tasks','tasks','1.0.0','','core',1,'Tasks','applet-48.png',3,1,'','tasks','task_id','task_name'),
	(4,'Calendar','calendar','1.0.0','','core',1,'Calendar','myevo-appointments.png',4,1,'','events','event_id','event_title'),
	(5,'Files','files','1.0.0','','core',1,'Files','folder5.png',5,1,'','files','file_id','file_name'),
	(6,'Contacts','contacts','1.0.0','','core',1,'Contacts','monkeychat-48.png',6,1,'','contacts','contact_id','contact_title'),
	(7,'Forums','forums','1.0.0','','core',1,'Forums','support.png',7,1,'','forums','forum_id','forum_name'),
	(8,'Tickets','ticketsmith','1.0.0','','core',1,'Tickets','ticketsmith.gif',8,1,'','','',''),
	(9,'User Administration','admin','1.0.0','','core',1,'User Admin','helix-setup-users.png',9,1,'','users','user_id','user_username'),
	(10,'System Administration','system','1.0.0','','core',1,'System Admin','48_my_computer.png',10,1,'','','',''),
	(11,'Departments','departments','1.0.0','','core',1,'Departments','users.gif',11,0,'','departments','dept_id','dept_name'),
	(12,'Help','help','1.0.0','','core',1,'Help','dp.gif',12,0,'','','',''),
	(13,'Public','public','1.0.0','','core',1,'Public','users.gif',13,0,'','','','');
UNLOCK TABLES;


LOCK TABLES `dotp_permissions` WRITE;
TRUNCATE `dotp_permissions`;
INSERT INTO `dotp_permissions` (`permission_id`, `permission_user`, `permission_grant_on`, `permission_item`, `permission_value`) VALUES 
	(1,1,'all',-1,-1);
UNLOCK TABLES;


LOCK TABLES `dotp_project_contacts` WRITE;
TRUNCATE `dotp_project_contacts`;
UNLOCK TABLES;


LOCK TABLES `dotp_project_departments` WRITE;
TRUNCATE `dotp_project_departments`;
UNLOCK TABLES;


LOCK TABLES `dotp_projects` WRITE;
TRUNCATE `dotp_projects`;
UNLOCK TABLES;


LOCK TABLES `dotp_roles` WRITE;
TRUNCATE `dotp_roles`;
UNLOCK TABLES;


LOCK TABLES `dotp_sessions` WRITE;
TRUNCATE `dotp_sessions`;
INSERT INTO `dotp_sessions` (`session_id`, `session_user`, `session_data`, `session_updated`, `session_created`) VALUES 
	('cih5n7i819velrl9k160db35ng',1,x'4C414E4755414745537C613A353A7B733A353A22656E5F4155223B613A343A7B693A303B733A323A22656E223B693A313B733A31333A22456E676C697368202841757329223B693A323B733A31333A22456E676C697368202841757329223B693A333B733A333A22656E61223B7D733A353A22656E5F4341223B613A343A7B693A303B733A323A22656E223B693A313B733A31333A22456E676C697368202843616E29223B693A323B733A31333A22456E676C697368202843616E29223B693A333B733A333A22656E63223B7D733A353A22656E5F4742223B613A343A7B693A303B733A323A22656E223B693A313B733A31323A22456E676C6973682028474229223B693A323B733A31323A22456E676C6973682028474229223B693A333B733A333A22656E67223B7D733A353A22656E5F4E5A223B613A343A7B693A303B733A323A22656E223B693A313B733A31323A22456E676C69736820284E5A29223B693A323B733A31323A22456E676C69736820284E5A29223B693A333B733A333A22656E7A223B7D733A353A22656E5F5553223B613A353A7B693A303B733A323A22656E223B693A313B733A31323A22456E676C6973682028555329223B693A323B733A31323A22456E676C6973682028555329223B693A333B733A333A22656E75223B693A343B733A31303A2249534F383835392D3135223B7D7D41707055497C4F3A363A22434170705549223A32373A7B733A353A227374617465223B613A32303A7B733A31333A2243616C496478436F6D70616E79223B733A303A22223B733A31323A2243616C49647846696C746572223B733A323A226D79223B733A31333A2243616C44617956696577546162223B733A313A2231223B733A31343A225461736B44617953686F77417263223B693A303B733A31343A225461736B44617953686F774C6F77223B693A313B733A31353A225461736B44617953686F77486F6C64223B693A303B733A31343A225461736B44617953686F7744796E223B693A303B733A31343A225461736B44617953686F7750696E223B693A303B733A32303A225461736B44617953686F77456D70747944617465223B693A303B733A31323A225341564544504C4143452D31223B733A373A226D3D61646D696E223B733A31303A225341564544504C414345223B733A383A226D3D73797374656D223B733A31343A2250726F6A496478436F6D70616E79223B733A303A22223B733A31353A2250726F6A4964784F72646572446972223B733A333A22617363223B733A31353A226F776E65725F66696C7465725F6964223B733A313A2231223B733A373A22757365725F6964223B733A313A2231223B733A32323A225461736B4C69737453686F77496E636F6D706C657465223B693A303B733A31323A227461736B735F6F70656E6564223B613A303A7B7D733A31303A2246696C65496478546162223B693A303B733A31343A2246696C6549647850726F6A656374223B693A303B733A31323A22436F6E744964785768657265223B733A303A22223B7D733A373A22757365725F6964223B733A313A2231223B733A31353A22757365725F66697273745F6E616D65223B733A353A2241646D696E223B733A31343A22757365725F6C6173745F6E616D65223B733A363A22506572736F6E223B733A31323A22757365725F636F6D70616E79223B733A303A22223B733A31353A22757365725F6465706172746D656E74223B693A303B733A31303A22757365725F656D61696C223B733A31373A2261646D696E406578616D706C652E636F6D223B733A393A22757365725F74797065223B733A313A2231223B733A31303A22757365725F7072656673223B613A383A7B733A363A224C4F43414C45223B733A323A22656E223B733A373A2254414256494557223B733A313A2230223B733A31323A22534844415445464F524D4154223B733A383A2225642F256D2F2559223B733A31303A2254494D45464F524D4154223B733A383A2225493A254D202570223B733A373A2255495354594C45223B733A373A2264656661756C74223B733A31333A225441534B41535349474E4D4158223B733A333A22313030223B733A31303A2255534552464F524D4154223B733A343A2275736572223B733A31303A2255534544494745535453223B733A313A2230223B7D733A31323A226461795F73656C6563746564223B4E3B733A31323A2273797374656D5F7072656673223B613A383A7B733A363A224C4F43414C45223B733A323A22656E223B733A373A2254414256494557223B733A313A2230223B733A31323A22534844415445464F524D4154223B733A383A2225642F256D2F2559223B733A31303A2254494D45464F524D4154223B733A383A2225493A254D202570223B733A373A2255495354594C45223B733A373A2264656661756C74223B733A31333A225441534B41535349474E4D4158223B733A333A22313030223B733A31303A2255534552464F524D4154223B733A343A2275736572223B733A31303A2255534544494745535453223B733A313A2230223B7D733A31313A22757365725F6C6F63616C65223B733A323A22656E223B733A393A22757365725F6C616E67223B613A343A7B693A303B733A31313A22656E5F41552E7574662D38223B693A313B733A333A22656E61223B693A323B733A353A22656E5F4155223B693A333B733A323A22656E223B7D733A31313A22626173655F6C6F63616C65223B733A323A22656E223B733A31363A22626173655F646174655F6C6F63616C65223B4E3B733A333A226D7367223B733A303A22223B733A353A226D73674E6F223B693A303B733A31353A2264656661756C745265646972656374223B733A303A22223B733A333A22636667223B613A313A7B733A31313A226C6F63616C655F7761726E223B623A303B7D733A31333A2276657273696F6E5F6D616A6F72223B693A323B733A31333A2276657273696F6E5F6D696E6F72223B693A323B733A31333A2276657273696F6E5F7061746368223B693A303B733A31343A2276657273696F6E5F737472696E67223B733A353A22322E322E30223B733A31343A226C6173745F696E736572745F6964223B693A313B733A333A225F6A73223B613A303A7B7D733A343A225F637373223B613A303A7B7D733A31303A2270726F6A6563745F6964223B693A303B7D616C6C5F746162737C613A31303A7B733A383A2263616C656E646172223B613A313A7B693A303B613A333A7B733A343A226E616D65223B733A383A2250726F6A65637473223B733A343A2266696C65223B733A38333A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F70726F6A656374732F63616C656E6461725F7461622E70726F6A65637473223B733A363A226D6F64756C65223B733A383A2270726F6A65637473223B7D7D733A383A2270726F6A65637473223B613A323A7B693A303B613A333A7B733A343A226E616D65223B733A363A224576656E7473223B733A343A2266696C65223B733A38313A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F63616C656E6461722F70726F6A656374735F7461622E6576656E7473223B733A363A226D6F64756C65223B733A383A2263616C656E646172223B7D693A313B613A333A7B733A343A226E616D65223B733A353A2246696C6573223B733A343A2266696C65223B733A37373A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F66696C65732F70726F6A656374735F7461622E66696C6573223B733A363A226D6F64756C65223B733A353A2266696C6573223B7D7D733A393A22636F6D70616E696573223B613A323A7B693A303B613A333A7B733A343A226E616D65223B733A353A2246696C6573223B733A343A2266696C65223B733A37383A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F66696C65732F636F6D70616E6965735F7461622E66696C6573223B733A363A226D6F64756C65223B733A353A2266696C6573223B7D733A343A2276696577223B613A313A7B693A303B613A333A7B733A343A226E616D65223B733A31343A2250726F6A656374732067616E7474223B733A343A2266696C65223B733A39353A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F70726F6A656374732F636F6D70616E6965735F7461622E766965772E70726F6A656374735F67616E7474223B733A363A226D6F64756C65223B733A383A2270726F6A65637473223B7D7D7D733A353A227461736B73223B613A323A7B733A343A2276696577223B613A313A7B693A303B613A333A7B733A343A226E616D65223B733A353A2246696C6573223B733A343A2266696C65223B733A37393A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F66696C65732F7461736B735F7461622E766965772E66696C6573223B733A363A226D6F64756C65223B733A353A2266696C6573223B7D7D693A303B613A333A7B733A343A226E616D65223B733A353A2246696C6573223B733A343A2266696C65223B733A37343A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F66696C65732F7461736B735F7461622E66696C6573223B733A363A226D6F64756C65223B733A353A2266696C6573223B7D7D733A353A2266696C6573223B613A303A7B7D733A383A22636F6E7461637473223B613A303A7B7D733A363A22666F72756D73223B613A303A7B7D733A31313A227469636B6574736D697468223B613A303A7B7D733A353A2261646D696E223B613A313A7B733A383A227669657775736572223B613A323A7B693A303B613A333A7B733A343A226E616D65223B733A383A2250726F6A65637473223B733A343A2266696C65223B733A38393A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F70726F6A656374732F61646D696E5F7461622E76696577757365722E70726F6A65637473223B733A363A226D6F64756C65223B733A383A2270726F6A65637473223B7D693A313B613A333A7B733A343A226E616D65223B733A31343A2250726F6A656374732067616E7474223B733A343A2266696C65223B733A39353A222F55736572732F6A6566667068696C6170792F73697465732F616C7461686F73742F646F7470726F6A6563742F6D6F64756C65732F70726F6A656374732F61646D696E5F7461622E76696577757365722E70726F6A656374735F67616E7474223B733A363A226D6F64756C65223B733A383A2270726F6A65637473223B7D7D7D733A363A2273797374656D223B613A303A7B7D7D','2022-06-18 19:28:07','2022-06-18 19:27:49');
UNLOCK TABLES;


LOCK TABLES `dotp_syskeys` WRITE;
TRUNCATE `dotp_syskeys`;
INSERT INTO `dotp_syskeys` (`syskey_id`, `syskey_name`, `syskey_label`, `syskey_type`, `syskey_sep1`, `syskey_sep2`) VALUES 
	(1,'SelectList','Enter values for list',0,'\n','|'),
	(2,'CustomField','Serialized array in the following format:\r\n<KEY>|<SERIALIZED ARRAY>\r\n\r\nSerialized Array:\r\n[type] => text | checkbox | select | textarea | label\r\n[name] => <Field\'s name>\r\n[options] => <html capture options>\r\n[selects] => <options for select and checkbox>',0,'\n','|'),
	(3,'ColorSelection','Hex color values for type=>color association.',0,'\n','|');
UNLOCK TABLES;


LOCK TABLES `dotp_sysvals` WRITE;
TRUNCATE `dotp_sysvals`;
INSERT INTO `dotp_sysvals` (`sysval_id`, `sysval_key_id`, `sysval_title`, `sysval_value`) VALUES 
	(1,1,'ProjectStatus','0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived'),
	(2,1,'CompanyType','0|Not Applicable\n1|Client\n2|Vendor\n3|Supplier\n4|Consultant\n5|Government\n6|Internal'),
	(3,1,'TaskDurationType','1|hours\n24|days'),
	(4,1,'EventType','0|General\n1|Appointment\n2|Meeting\n3|All Day Event\n4|Anniversary\n5|Reminder'),
	(5,1,'TaskStatus','0|Active\n-1|Inactive'),
	(6,1,'TaskType','0|Unknown\n1|Administrative\n2|Operative'),
	(7,1,'ProjectType','0|Unknown\n1|Administrative\n2|Operative'),
	(8,3,'ProjectColors','Web|FFE0AE\nEngineering|AEFFB2\nHelpDesk|FFFCAE\nSystem Administration|FFAEAE'),
	(9,1,'FileType','0|Unknown\n1|Document\n2|Application'),
	(10,1,'TaskPriority','-1|low\n0|normal\n1|high'),
	(11,1,'ProjectPriority','-1|low\n0|normal\n1|high'),
	(12,1,'ProjectPriorityColor','-1|#E5F7FF\n0|\n1|#FFDCB3'),
	(13,1,'TaskLogReference','0|Not Defined\n1|Email\n2|Helpdesk\n3|Phone Call\n4|Fax'),
	(14,1,'TaskLogReferenceImage','0| 1|./images/obj/email.gif 2|./modules/helpdesk/images/helpdesk.png 3|./images/obj/phone.gif 4|./images/icons/stock_print-16.png'),
	(15,1,'UserType','0|Default User\r\n1|Administrator\r\n2|CEO\r\n3|Director\r\n4|Branch Manager\r\n5|Manager\r\n6|Supervisor\r\n7|Employee'),
	(16,1,'ProjectRequiredFields','f.project_name.value.length|<3\r\nf.project_color_identifier.value.length|<3\r\nf.project_company.options[f.project_company.selectedIndex].value|<1'),
	(17,2,'TicketNotify','0|admin@example.com\n1|admin@example.com\n2|admin@example.com\r\n3|admin@example.com\r\n4|admin@example.com'),
	(18,1,'TicketPriority','0|Low\n1|Normal\n2|High\n3|Highest\n4|911'),
	(19,1,'TicketStatus','0|Open\n1|Closed\n2|Deleted');
UNLOCK TABLES;


LOCK TABLES `dotp_task_contacts` WRITE;
TRUNCATE `dotp_task_contacts`;
UNLOCK TABLES;


LOCK TABLES `dotp_task_departments` WRITE;
TRUNCATE `dotp_task_departments`;
UNLOCK TABLES;


LOCK TABLES `dotp_task_dependencies` WRITE;
TRUNCATE `dotp_task_dependencies`;
UNLOCK TABLES;


LOCK TABLES `dotp_task_log` WRITE;
TRUNCATE `dotp_task_log`;
UNLOCK TABLES;


LOCK TABLES `dotp_tasks` WRITE;
TRUNCATE `dotp_tasks`;
UNLOCK TABLES;


LOCK TABLES `dotp_tickets` WRITE;
TRUNCATE `dotp_tickets`;
UNLOCK TABLES;


LOCK TABLES `dotp_user_access_log` WRITE;
TRUNCATE `dotp_user_access_log`;
INSERT INTO `dotp_user_access_log` (`user_access_log_id`, `user_id`, `user_ip`, `date_time_in`, `date_time_out`, `date_time_last_action`) VALUES 
	(1,1,'127.0.0.1','2022-06-18 14:27:49','0000-00-00 00:00:00','2022-06-18 19:28:07');
UNLOCK TABLES;


LOCK TABLES `dotp_user_events` WRITE;
TRUNCATE `dotp_user_events`;
UNLOCK TABLES;


LOCK TABLES `dotp_user_preferences` WRITE;
TRUNCATE `dotp_user_preferences`;
INSERT INTO `dotp_user_preferences` (`pref_user`, `pref_name`, `pref_value`) VALUES 
	('0','LOCALE','en'),
	('0','TABVIEW','0'),
	('0','SHDATEFORMAT','%d/%m/%Y'),
	('0','TIMEFORMAT','%I:%M %p'),
	('0','UISTYLE','default'),
	('0','TASKASSIGNMAX','100'),
	('0','USERFORMAT','user'),
	('0','USEDIGESTS','0');
UNLOCK TABLES;


LOCK TABLES `dotp_user_roles` WRITE;
TRUNCATE `dotp_user_roles`;
UNLOCK TABLES;


LOCK TABLES `dotp_user_task_pin` WRITE;
TRUNCATE `dotp_user_task_pin`;
UNLOCK TABLES;


LOCK TABLES `dotp_user_tasks` WRITE;
TRUNCATE `dotp_user_tasks`;
UNLOCK TABLES;


LOCK TABLES `dotp_users` WRITE;
TRUNCATE `dotp_users`;
INSERT INTO `dotp_users` (`user_id`, `user_contact`, `user_username`, `user_password`, `user_parent`, `user_type`, `user_company`, `user_department`, `user_owner`, `user_signature`) VALUES 
	(1,1,'admin','76a2173be6393254e72ffa4d6df1030a',0,1,0,0,0,'');
UNLOCK TABLES;






SET FOREIGN_KEY_CHECKS = @ORIG_FOREIGN_KEY_CHECKS;

SET UNIQUE_CHECKS = @ORIG_UNIQUE_CHECKS;

SET @ORIG_TIME_ZONE = @@TIME_ZONE;
SET TIME_ZONE = @ORIG_TIME_ZONE;

SET SQL_MODE = @ORIG_SQL_MODE;



# Export Finished: June 18, 2022 at 2:29:14 PM CDT


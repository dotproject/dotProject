/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : dotproject_2_1_7

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-05-15 22:10:11
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `dotp_acquisition_planning`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_acquisition_planning`;
CREATE TABLE `dotp_acquisition_planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `items_to_be_acquired` text,
  `contract_type` text,
  `documents_to_acquisition` text,
  `criteria_for_supplier_selection` text,
  `additional_requirements` text,
  `supplier_management_process` text,
  `acquisition_roles` text,
  PRIMARY KEY (`id`),
  KEY `FK_PROJECT_QUALITY` (`project_id`),
  CONSTRAINT `FK_PROJECT_QUALITY` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_acquisition_planning
-- ----------------------------
INSERT INTO dotp_acquisition_planning VALUES ('7', '2', '<p>Projetor para notebook com entrada de v&iacute;deo VGA e HDMI.</p>', '<p>Pre&ccedil;o fixo.</p>', '<p>Documento para dispen&ccedil;a de licita&ccedil;&atilde;o porque a compra n&atilde;o exeder&aacute; 10% do valor m&aacute;ximo para licita&ccedil;&atilde;o do tipo convite. (R$ 8.000,00)</p>\r\n<p>- Entrada VGA</p>\r\n<p>- Entrada HDMI</p>', '<p>Os forncedores ser&atilde;o selecionados pelo menor pre&ccedil;o (peso 4) e pelo tempo de&nbsp;entrega (peso 1).</p>', '<p>Os equipamentos adquiridos devem possuir ao menos um ano de garantia.</p>', '<p>1.&nbsp;Envio do pedido de compra&nbsp;ao fornecedor.</p>\r\n<p>2.&nbsp;Resposta do&nbsp;fornecedor com sua proposta.</p>\r\n<p>3. Avalia&ccedil;&atilde;o da proposta.</p>\r\n<p>4. Com a proposta aceita, solicita-se a compra.</p>\r\n<p>5. Acompanhamento do prazo da&nbsp;entrega.</p>\r\n<p>6. Ao receber o objeto adquirido, &eacute; verificado se este atende as especifica&ccedil;&otilde;es.</p>', '<p>Contratante: elabora descri&ccedil;&atilde;o do item a ser adquirido e solicita or&ccedil;amento. Tamb&eacute;m aprova a compra e realiza o pagamento.<br />Fornecedor: elabora proposta, e entrega o item solicitado.</p>');
INSERT INTO dotp_acquisition_planning VALUES ('8', '2', '<p>3 Notebooks.</p>', '<p>Pre&ccedil;o fixo.</p>', '<p>Documentos para licita&ccedil;&atilde;o&nbsp;na modalidade&nbsp;do tipo convite.&nbsp;</p>\r\n<p>Especifica&ccedil;&atilde;o:</p>\r\n<p>- suporte ao sistema operacional windows 8.<br />- 8 GB de mem&oacute;ria RAM.<br />- Processador intel core I7<br />-500 GB de HD.<br />- placa de rede gibabit ethernet.</p>', '<p>Os forncedores ser&atilde;o selecionados pelo menor pre&ccedil;o (peso 3) e pelo tempo de garantia (peso 1).</p>', '<p>Os itens adquiridos devem ser entregues at&eacute; 15/03/2013.</p>', '<p>1.&nbsp;Envio do pedido de compra&nbsp;ao fornecedor.</p>\r\n<p>2.&nbsp;Resposta do&nbsp;fornecedor com sua proposta.</p>\r\n<p>3. Avalia&ccedil;&atilde;o da proposta.</p>\r\n<p>4. Com a proposta aceita, solicita-se a compra.</p>\r\n<p>5. Acompanhamento do prazo da&nbsp;entrega.</p>\r\n<p>6. Ao receber o objeto adquirido, &eacute; verificado se este atende as especifica&ccedil;&otilde;es.</p>', '<p>Contratante: elabora descri&ccedil;&atilde;o do item a ser adquirido e solicita or&ccedil;amento. Tamb&eacute;m aprova a compra e realiza o pagamento.</p>\r\n<p>Fornecedor: elabora proposta, e entrega o item solicitado</p>');

-- ----------------------------
-- Table structure for `dotp_billingcode`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_billingcode`;
CREATE TABLE `dotp_billingcode` (
  `billingcode_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `billingcode_name` varchar(25) NOT NULL DEFAULT '',
  `billingcode_value` float NOT NULL DEFAULT '0',
  `billingcode_desc` varchar(255) NOT NULL DEFAULT '',
  `billingcode_status` int(1) NOT NULL DEFAULT '0',
  `company_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`billingcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_billingcode
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_budget`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_budget`;
CREATE TABLE `dotp_budget` (
  `budget_id` int(11) NOT NULL AUTO_INCREMENT,
  `budget_project_id` int(11) NOT NULL,
  `budget_reserve_management` decimal(9,2) NOT NULL,
  `budget_sub_total` decimal(9,2) NOT NULL,
  `budget_total` decimal(9,2) NOT NULL,
  PRIMARY KEY (`budget_id`),
  KEY `budget_project_id` (`budget_project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_budget
-- ----------------------------
INSERT INTO dotp_budget VALUES ('1', '1', '5.00', '11870.00', '12463.50');
INSERT INTO dotp_budget VALUES ('2', '2', '9.00', '133316.00', '145314.00');

-- ----------------------------
-- Table structure for `dotp_budget_reserve`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_budget_reserve`;
CREATE TABLE `dotp_budget_reserve` (
  `budget_reserve_id` int(11) NOT NULL AUTO_INCREMENT,
  `budget_reserve_project_id` int(11) NOT NULL,
  `budget_reserve_risk_id` int(11) NOT NULL,
  `budget_reserve_description` varchar(50) DEFAULT NULL,
  `budget_reserve_financial_impact` int(11) DEFAULT NULL,
  `budget_reserve_inicial_month` datetime DEFAULT NULL,
  `budget_reserve_final_month` datetime DEFAULT NULL,
  `budget_reserve_value_total` decimal(9,2) DEFAULT NULL,
  PRIMARY KEY (`budget_reserve_id`),
  KEY `budget_reserve_project_id` (`budget_reserve_project_id`),
  KEY `budget_reserve_risk_id` (`budget_reserve_risk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_budget_reserve
-- ----------------------------
INSERT INTO dotp_budget_reserve VALUES ('1', '1', '2', 'Technical troubles', '5000', '2013-02-19 00:00:00', '2013-02-19 00:00:00', '5000.00');
INSERT INTO dotp_budget_reserve VALUES ('2', '1', '1', 'Employee request to leave the team', '20000', '2012-12-01 00:00:00', '2012-12-01 00:00:00', '20000.00');
INSERT INTO dotp_budget_reserve VALUES ('4', '2', '3', 'Perda de do analista de testes', '6000', '2013-03-04 00:00:00', '2013-08-01 00:00:00', '30000.00');
INSERT INTO dotp_budget_reserve VALUES ('5', '2', '4', 'Does not match the customer expectations', '7000', '2013-03-04 00:00:00', '2013-08-01 00:00:00', '35000.00');

-- ----------------------------
-- Table structure for `dotp_common_notes`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_common_notes`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_common_notes
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_communication`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_communication`;
CREATE TABLE `dotp_communication` (
  `communication_id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_title` varchar(255) NOT NULL,
  `communication_information` varchar(2000) NOT NULL,
  `communication_frequency_id` int(11) NOT NULL,
  `communication_channel_id` int(11) NOT NULL,
  `communication_project_id` int(11) NOT NULL,
  `communication_restrictions` varchar(2000) NOT NULL,
  `communication_date` varchar(30) NOT NULL,
  `communication_responsible_authorization` varchar(80) NOT NULL,
  PRIMARY KEY (`communication_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_communication
-- ----------------------------
INSERT INTO dotp_communication VALUES ('3', 'Plano do projeto', 'Publicar o plano do projeto para obter o acordo entre todos os stakeholders.', '3', '1', '2', '', '', '7');
INSERT INTO dotp_communication VALUES ('4', 'RelatÃ³rio do progresso do projeto', 'Divulgar relatÃ³rio do progresso do projeto para o cliente.', '5', '1', '2', 'Os relatÃ³rios do projeto serÃ£o divulgados no portal da organizaÃ§Ã£o, o qual o cliente terÃ¡ acesso para acompanhamento.', '', '');
INSERT INTO dotp_communication VALUES ('5', 'RelatÃ³rio de conclusÃ£o do mÃ³dulo web', 'Informar conclusÃ£o do mÃ³dulo web ao cliente.', '3', '1', '2', 'O relatÃ³rio serÃ¡ emitido somente apÃ³s a realizaÃ§Ã£o de testes de sistema pelo analista de testes.', '', '');
INSERT INTO dotp_communication VALUES ('6', 'RelatÃ³rio de conclusÃ£o do mÃ³dulo mobile', 'Comunicar a conclusÃ£o do desenvolvimento do mÃ³dulo mobile.', '3', '1', '2', 'Esta comunicaÃ§Ã£o serÃ¡ liberada somente com a autorizaÃ§Ã£o do analista de testes apÃ³s passar pelos testes de sistema.', '', '6');
INSERT INTO dotp_communication VALUES ('7', '', 'ZxXzXZ', '0', '0', '5', '', '', '');

-- ----------------------------
-- Table structure for `dotp_communication_channel`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_communication_channel`;
CREATE TABLE `dotp_communication_channel` (
  `communication_channel_id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_channel` varchar(255) NOT NULL,
  PRIMARY KEY (`communication_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_communication_channel
-- ----------------------------
INSERT INTO dotp_communication_channel VALUES ('1', 'e-mail');
INSERT INTO dotp_communication_channel VALUES ('2', '');

-- ----------------------------
-- Table structure for `dotp_communication_frequency`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_communication_frequency`;
CREATE TABLE `dotp_communication_frequency` (
  `communication_frequency_id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_frequency` varchar(255) NOT NULL,
  `communication_frequency_hasdate` char(3) DEFAULT 'Nao',
  PRIMARY KEY (`communication_frequency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_communication_frequency
-- ----------------------------
INSERT INTO dotp_communication_frequency VALUES ('3', 'Ãšnica vez', 'Nao');
INSERT INTO dotp_communication_frequency VALUES ('4', 'semanalmente', 'Nao');
INSERT INTO dotp_communication_frequency VALUES ('5', 'Cada duas semanas', 'Nao');
INSERT INTO dotp_communication_frequency VALUES ('6', '', 'Nao');

-- ----------------------------
-- Table structure for `dotp_communication_issuing`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_communication_issuing`;
CREATE TABLE `dotp_communication_issuing` (
  `communication_issuing_id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_id` int(11) NOT NULL,
  `communication_stakeholder_id` int(11) NOT NULL,
  PRIMARY KEY (`communication_issuing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_communication_issuing
-- ----------------------------
INSERT INTO dotp_communication_issuing VALUES ('4', '3', '7');
INSERT INTO dotp_communication_issuing VALUES ('5', '5', '5');
INSERT INTO dotp_communication_issuing VALUES ('6', '6', '5');
INSERT INTO dotp_communication_issuing VALUES ('7', '4', '7');

-- ----------------------------
-- Table structure for `dotp_communication_receptor`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_communication_receptor`;
CREATE TABLE `dotp_communication_receptor` (
  `communication_receptor_id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_id` int(11) NOT NULL,
  `communication_stakeholder_id` int(11) NOT NULL,
  PRIMARY KEY (`communication_receptor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_communication_receptor
-- ----------------------------
INSERT INTO dotp_communication_receptor VALUES ('3', '3', '5');
INSERT INTO dotp_communication_receptor VALUES ('4', '3', '6');
INSERT INTO dotp_communication_receptor VALUES ('5', '5', '7');
INSERT INTO dotp_communication_receptor VALUES ('6', '5', '6');
INSERT INTO dotp_communication_receptor VALUES ('7', '6', '6');
INSERT INTO dotp_communication_receptor VALUES ('8', '3', '8');
INSERT INTO dotp_communication_receptor VALUES ('9', '4', '8');
INSERT INTO dotp_communication_receptor VALUES ('10', '5', '8');

-- ----------------------------
-- Table structure for `dotp_companies`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_companies`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_companies
-- ----------------------------
INSERT INTO dotp_companies VALUES ('2', '0', 'Grupo de qualidade de software', '', '', '', '', '', '', '', '', '', '1', null, '0', null, null);

-- ----------------------------
-- Table structure for `dotp_company_policies`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_company_policies`;
CREATE TABLE `dotp_company_policies` (
  `company_policies_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_policies_recognition` text,
  `company_policies_policy` text,
  `company_policies_safety` text,
  `company_policies_company_id` int(11) NOT NULL,
  PRIMARY KEY (`company_policies_id`),
  KEY `dotp_company_policies_ibfk_1` (`company_policies_company_id`),
  CONSTRAINT `dotp_company_policies_ibfk_1` FOREIGN KEY (`company_policies_company_id`) REFERENCES `dotp_companies` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_company_policies
-- ----------------------------
INSERT INTO dotp_company_policies VALUES ('2', 'A organizaÃ§Ã£o prove recompensa pelos projetos concluÃ­dos dentro das restriÃ§Ãµes de custo, tempo, e escopo. Esta recompensa limita-se a 10% do valor do projeto dividido em partes iguais entre os membros da equipe, seguido de dois dias de folga.', 'A organizaÃ§Ã£o possui um processo definido para o desenvolvimento de seus projetos. Logo, todo atividade realizada dentro de algum projeto deve seguir as prÃ¡ticas estabelecidas pela organizaÃ§Ã£o, e isto inclui: templetes para elaboraÃ§Ã£o de documentos, regras de testes e inspeÃ§Ãµes, ferramentas CASE, regras de cumprimento de horÃ¡rios e registros de horas.', 'Para garantir a seguranÃ§a dos membros da equipe, do cliente, e dos projetos como um todo, a organizaÃ§Ã£o exige que todas as informaÃ§Ãµes sobre os projetos sejam confidencias, e nÃ£o permite que nenhuma cÃ³pia de documento ou componente de software seja retirada da organizaÃ§Ã£o sem autorizaÃ§Ã£o prÃ©via.', '2');

-- ----------------------------
-- Table structure for `dotp_company_role`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_company_role`;
CREATE TABLE `dotp_company_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `role_name` text,
  `identation` text,
  PRIMARY KEY (`id`),
  KEY `fk_role_company` (`company_id`),
  CONSTRAINT `fk_role_company` FOREIGN KEY (`company_id`) REFERENCES `dotp_companies` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_company_role
-- ----------------------------
INSERT INTO dotp_company_role VALUES ('4', '2', '0', 'Gerente de projetos', '');
INSERT INTO dotp_company_role VALUES ('5', '2', '1', 'Analista de sistemas', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_company_role VALUES ('6', '2', '2', 'Analista de teste', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_company_role VALUES ('7', '2', '3', 'Programador', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_company_role VALUES ('8', '2', '4', 'Gerente de qualidade', '');

-- ----------------------------
-- Table structure for `dotp_config`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_config`;
CREATE TABLE `dotp_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  `config_group` varchar(255) NOT NULL DEFAULT '',
  `config_type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name` (`config_name`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_config
-- ----------------------------
INSERT INTO dotp_config VALUES ('1', 'host_locale', 'en', '', 'text');
INSERT INTO dotp_config VALUES ('2', 'check_overallocation', 'true', '', 'checkbox');
INSERT INTO dotp_config VALUES ('3', 'currency_symbol', '$', '', 'text');
INSERT INTO dotp_config VALUES ('4', 'host_style', 'default', '', 'text');
INSERT INTO dotp_config VALUES ('5', 'company_name', 'UFSC - INE5427', '', 'text');
INSERT INTO dotp_config VALUES ('6', 'page_title', 'dotProject', '', 'text');
INSERT INTO dotp_config VALUES ('7', 'site_domain', 'example.com', '', 'text');
INSERT INTO dotp_config VALUES ('8', 'email_prefix', '[dotProject]', '', 'text');
INSERT INTO dotp_config VALUES ('9', 'admin_username', 'admin', '', 'text');
INSERT INTO dotp_config VALUES ('10', 'username_min_len', '4', '', 'text');
INSERT INTO dotp_config VALUES ('11', 'password_min_len', '4', '', 'text');
INSERT INTO dotp_config VALUES ('12', 'enable_gantt_charts', 'true', '', 'checkbox');
INSERT INTO dotp_config VALUES ('13', 'log_changes', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('14', 'check_task_dates', 'true', '', 'checkbox');
INSERT INTO dotp_config VALUES ('15', 'check_task_empty_dynamic', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('16', 'locale_warn', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('17', 'locale_alert', '^', '', 'text');
INSERT INTO dotp_config VALUES ('18', 'daily_working_hours', '8', '', 'text');
INSERT INTO dotp_config VALUES ('19', 'display_debug', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('20', 'link_tickets_kludge', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('21', 'show_all_task_assignees', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('22', 'direct_edit_assignment', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('23', 'restrict_color_selection', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('24', 'cal_day_view_show_minical', 'true', '', 'checkbox');
INSERT INTO dotp_config VALUES ('25', 'cal_day_start', '8', '', 'text');
INSERT INTO dotp_config VALUES ('26', 'cal_day_end', '17', '', 'text');
INSERT INTO dotp_config VALUES ('27', 'cal_day_increment', '15', '', 'text');
INSERT INTO dotp_config VALUES ('28', 'cal_working_days', '1,2,3,4,5', '', 'text');
INSERT INTO dotp_config VALUES ('29', 'restrict_task_time_editing', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('30', 'default_view_m', 'calendar', '', 'text');
INSERT INTO dotp_config VALUES ('31', 'default_view_a', 'day_view', '', 'text');
INSERT INTO dotp_config VALUES ('32', 'default_view_tab', '1', '', 'text');
INSERT INTO dotp_config VALUES ('33', 'index_max_file_size', '-1', '', 'text');
INSERT INTO dotp_config VALUES ('34', 'session_handling', 'app', 'session', 'select');
INSERT INTO dotp_config VALUES ('35', 'session_idle_time', '2d', 'session', 'text');
INSERT INTO dotp_config VALUES ('36', 'session_max_lifetime', '1m', 'session', 'text');
INSERT INTO dotp_config VALUES ('37', 'debug', '1', '', 'text');
INSERT INTO dotp_config VALUES ('38', 'parser_default', '/usr/bin/strings', '', 'text');
INSERT INTO dotp_config VALUES ('39', 'parser_application/msword', '/usr/bin/strings', '', 'text');
INSERT INTO dotp_config VALUES ('40', 'parser_text/html', '/usr/bin/strings', '', 'text');
INSERT INTO dotp_config VALUES ('41', 'parser_application/pdf', '/usr/bin/pdftotext', '', 'text');
INSERT INTO dotp_config VALUES ('42', 'files_ci_preserve_attr', 'true', '', 'checkbox');
INSERT INTO dotp_config VALUES ('43', 'files_show_versions_edit', 'false', '', 'checkbox');
INSERT INTO dotp_config VALUES ('44', 'auth_method', 'sql', 'auth', 'select');
INSERT INTO dotp_config VALUES ('45', 'ldap_host', 'localhost', 'ldap', 'text');
INSERT INTO dotp_config VALUES ('46', 'ldap_port', '389', 'ldap', 'text');
INSERT INTO dotp_config VALUES ('47', 'ldap_version', '3', 'ldap', 'text');
INSERT INTO dotp_config VALUES ('48', 'ldap_base_dn', 'dc=saki,dc=com,dc=au', 'ldap', 'text');
INSERT INTO dotp_config VALUES ('49', 'ldap_user_filter', '(uid=%USERNAME%)', 'ldap', 'text');
INSERT INTO dotp_config VALUES ('50', 'postnuke_allow_login', 'true', 'auth', 'checkbox');
INSERT INTO dotp_config VALUES ('51', 'reset_memory_limit', '32M', '', 'text');
INSERT INTO dotp_config VALUES ('52', 'mail_transport', 'php', 'mail', 'select');
INSERT INTO dotp_config VALUES ('53', 'mail_host', 'localhost', 'mail', 'text');
INSERT INTO dotp_config VALUES ('54', 'mail_port', '25', 'mail', 'text');
INSERT INTO dotp_config VALUES ('55', 'mail_auth', 'false', 'mail', 'checkbox');
INSERT INTO dotp_config VALUES ('56', 'mail_user', '', 'mail', 'text');
INSERT INTO dotp_config VALUES ('57', 'mail_pass', '', 'mail', 'password');
INSERT INTO dotp_config VALUES ('58', 'mail_defer', 'false', 'mail', 'checkbox');
INSERT INTO dotp_config VALUES ('59', 'mail_timeout', '30', 'mail', 'text');
INSERT INTO dotp_config VALUES ('60', 'session_gc_scan_queue', 'false', 'session', 'checkbox');
INSERT INTO dotp_config VALUES ('61', 'task_reminder_control', 'false', 'task_reminder', 'checkbox');
INSERT INTO dotp_config VALUES ('62', 'task_reminder_days_before', '1', 'task_reminder', 'text');
INSERT INTO dotp_config VALUES ('63', 'task_reminder_repeat', '100', 'task_reminder', 'text');
INSERT INTO dotp_config VALUES ('64', 'gacl_cache', 'false', 'gacl', 'checkbox');
INSERT INTO dotp_config VALUES ('65', 'gacl_expire', 'true', 'gacl', 'checkbox');
INSERT INTO dotp_config VALUES ('66', 'gacl_cache_dir', '/tmp', 'gacl', 'text');
INSERT INTO dotp_config VALUES ('67', 'gacl_timeout', '600', 'gacl', 'text');
INSERT INTO dotp_config VALUES ('68', 'mail_smtp_tls', 'false', 'mail', 'checkbox');
INSERT INTO dotp_config VALUES ('69', 'ldap_search_user', 'Manager', 'ldap', 'text');
INSERT INTO dotp_config VALUES ('70', 'ldap_search_pass', 'secret', 'ldap', 'password');
INSERT INTO dotp_config VALUES ('71', 'ldap_allow_login', 'true', 'ldap', 'checkbox');
INSERT INTO dotp_config VALUES ('72', 'user_contact_inactivate', 'true', 'auth', 'checkbox');
INSERT INTO dotp_config VALUES ('73', 'user_contact_activate', 'false', 'auth', 'checkbox');

-- ----------------------------
-- Table structure for `dotp_config_list`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_config_list`;
CREATE TABLE `dotp_config_list` (
  `config_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_id` int(11) NOT NULL DEFAULT '0',
  `config_list_name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_list_id`),
  KEY `config_id` (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_config_list
-- ----------------------------
INSERT INTO dotp_config_list VALUES ('1', '44', 'sql');
INSERT INTO dotp_config_list VALUES ('2', '44', 'ldap');
INSERT INTO dotp_config_list VALUES ('3', '44', 'pn');
INSERT INTO dotp_config_list VALUES ('4', '34', 'app');
INSERT INTO dotp_config_list VALUES ('5', '34', 'php');
INSERT INTO dotp_config_list VALUES ('6', '52', 'php');
INSERT INTO dotp_config_list VALUES ('7', '52', 'smtp');

-- ----------------------------
-- Table structure for `dotp_contacts`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_contacts`;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_contacts
-- ----------------------------
INSERT INTO dotp_contacts VALUES ('1', 'Admin', 'Person', '', null, null, null, '', null, null, 'admin@example.com', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'obj/contact', '0', '0');
INSERT INTO dotp_contacts VALUES ('5', 'Person', 'A', 'Roberto Pereira', '', '0000-00-00', '', '2', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', 'obj/contact', '1', '0');
INSERT INTO dotp_contacts VALUES ('6', 'Person', 'B', 'Fernando Bauer', '', '0000-00-00', '', '2', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', 'obj/contact', '1', '0');
INSERT INTO dotp_contacts VALUES ('7', 'Person', 'C', '', null, null, null, '2', null, null, 'gresse@gmail.com', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'obj/contact', '1', '0');
INSERT INTO dotp_contacts VALUES ('8', 'Tio', 'Chico', 'Chico, Tio', null, null, null, '0', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'obj/contact', '1', '0');
INSERT INTO dotp_contacts VALUES ('9', 'Person', 'D', '', null, null, null, '2', null, null, 'paulo@telemedicina.br', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'obj/contact', '1', '0');
INSERT INTO dotp_contacts VALUES ('10', 'Person', 'E', '', null, null, null, '2', null, null, 'marcelo@dotproject.com', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'obj/contact', '1', '0');
INSERT INTO dotp_contacts VALUES ('11', 'teste', '1234', '', null, null, null, '0', null, null, 'teste', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'obj/contact', '1', '0');

-- ----------------------------
-- Table structure for `dotp_costs`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_costs`;
CREATE TABLE `dotp_costs` (
  `cost_id` int(11) NOT NULL AUTO_INCREMENT,
  `cost_type_id` int(11) NOT NULL,
  `cost_project_id` int(11) NOT NULL,
  `cost_description` varchar(150) NOT NULL,
  `cost_quantity` int(11) DEFAULT NULL,
  `cost_date_begin` datetime DEFAULT NULL,
  `cost_date_end` datetime DEFAULT NULL,
  `cost_value_unitary` decimal(9,2) DEFAULT NULL,
  `cost_value_total` decimal(9,2) DEFAULT NULL,
  PRIMARY KEY (`cost_id`),
  KEY `cost_project_id` (`cost_project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_costs
-- ----------------------------
INSERT INTO dotp_costs VALUES ('27', '0', '2', 'Person A - System analyst', '80', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '17.00', '5440.00');
INSERT INTO dotp_costs VALUES ('28', '0', '2', 'Person B - Test analyst', '80', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '18.00', '5760.00');
INSERT INTO dotp_costs VALUES ('29', '0', '2', 'Person C - Project manager', '176', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '18.00', '12672.00');
INSERT INTO dotp_costs VALUES ('26', '0', '2', 'Person A - Programer', '100', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '17.00', '6800.00');
INSERT INTO dotp_costs VALUES ('23', '1', '2', 'Application server for tests', '1', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '5000.00', '5000.00');
INSERT INTO dotp_costs VALUES ('24', '1', '2', 'Meeting room', '1', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '3200.00', '3200.00');
INSERT INTO dotp_costs VALUES ('25', '1', '2', 'Wireless internet (IEEE 802.11n)', '1', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '1900.00', '1900.00');
INSERT INTO dotp_costs VALUES ('30', '0', '2', 'Person D - Quality manager', '72', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '18.00', '5184.00');
INSERT INTO dotp_costs VALUES ('31', '0', '2', 'Person E - Programer', '130', '2013-04-02 00:00:00', '2013-08-12 00:00:00', '18.00', '9360.00');

-- ----------------------------
-- Table structure for `dotp_custom_fields_lists`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_custom_fields_lists`;
CREATE TABLE `dotp_custom_fields_lists` (
  `field_id` int(11) DEFAULT NULL,
  `list_option_id` int(11) DEFAULT NULL,
  `list_value` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_custom_fields_lists
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_custom_fields_struct`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_custom_fields_struct`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_custom_fields_struct
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_custom_fields_values`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_custom_fields_values`;
CREATE TABLE `dotp_custom_fields_values` (
  `value_id` int(11) DEFAULT NULL,
  `value_module` varchar(30) DEFAULT NULL,
  `value_object_id` int(11) DEFAULT NULL,
  `value_field_id` int(11) DEFAULT NULL,
  `value_charvalue` varchar(250) DEFAULT NULL,
  `value_intvalue` int(11) DEFAULT NULL,
  KEY `idx_cfv_id` (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_custom_fields_values
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_departments`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_departments`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Department heirarchy under a company';

-- ----------------------------
-- Records of dotp_departments
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_dotpermissions`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_dotpermissions`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_dotpermissions
-- ----------------------------
INSERT INTO dotp_dotpermissions VALUES ('18', '1', 'app', 'admin', 'access', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('18', '1', 'app', 'admin', 'add', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('18', '1', 'app', 'admin', 'delete', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('18', '1', 'app', 'admin', 'edit', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('18', '1', 'app', 'admin', 'view', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('20', '1', 'app', 'initiating', 'access', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('20', '1', 'app', 'initiating', 'add', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('20', '1', 'app', 'initiating', 'delete', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('20', '1', 'app', 'initiating', 'edit', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('20', '1', 'app', 'initiating', 'view', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('21', '1', 'app', 'stakeholder', 'access', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('21', '1', 'app', 'stakeholder', 'add', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('21', '1', 'app', 'stakeholder', 'delete', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('21', '1', 'app', 'stakeholder', 'edit', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('21', '1', 'app', 'stakeholder', 'view', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('22', '7', 'app', 'initiating', 'access', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('22', '7', 'app', 'initiating', 'add', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('22', '7', 'app', 'initiating', 'delete', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('22', '7', 'app', 'initiating', 'edit', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('22', '7', 'app', 'initiating', 'view', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('23', '7', 'app', 'stakeholder', 'access', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('23', '7', 'app', 'stakeholder', 'add', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('23', '7', 'app', 'stakeholder', 'delete', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('23', '7', 'app', 'stakeholder', 'edit', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('23', '7', 'app', 'stakeholder', 'view', '1', '1', '1');
INSERT INTO dotp_dotpermissions VALUES ('12', '1', 'sys', 'acl', 'access', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('12', '4', 'sys', 'acl', 'access', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('12', '5', 'sys', 'acl', 'access', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('12', '6', 'sys', 'acl', 'access', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('12', '7', 'sys', 'acl', 'access', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('16', '8', 'app', 'users', 'access', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('16', '8', 'app', 'users', 'view', '1', '3', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'admin', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'calendar', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'events', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'companies', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'contacts', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'departments', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'files', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'file_folders', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'forums', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'help', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'projects', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'system', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'tasks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'task_log', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'ticketsmith', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'public', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'roles', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'users', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'human_resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'costs', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'risks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'communication', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'timeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'scopeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'admin', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'calendar', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'events', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'companies', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'contacts', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'departments', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'files', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'file_folders', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'forums', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'help', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'projects', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'system', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'tasks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'task_log', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'ticketsmith', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'public', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'roles', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'users', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'human_resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'costs', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'risks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'communication', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'timeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'scopeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'admin', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'calendar', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'events', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'companies', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'contacts', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'departments', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'files', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'file_folders', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'forums', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'help', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'projects', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'system', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'tasks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'task_log', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'ticketsmith', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'public', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'roles', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'users', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'human_resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'costs', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'risks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'communication', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'timeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'scopeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'admin', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'calendar', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'events', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'companies', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'contacts', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'departments', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'files', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'file_folders', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'forums', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'help', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'projects', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'system', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'tasks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'task_log', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'ticketsmith', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'public', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'roles', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'users', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'human_resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'costs', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'risks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'communication', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'timeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'scopeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'admin', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'calendar', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'events', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'companies', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'contacts', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'departments', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'files', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'file_folders', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'forums', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'help', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'projects', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'system', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'tasks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'task_log', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'ticketsmith', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'public', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'roles', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'users', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'human_resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'costs', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'risks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'communication', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'timeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '1', 'app', 'scopeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'admin', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'calendar', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'events', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'companies', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'contacts', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'departments', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'files', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'file_folders', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'forums', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'help', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'projects', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'system', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'tasks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'task_log', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'ticketsmith', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'public', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'roles', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'users', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'human_resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'costs', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'risks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'communication', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'timeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'scopeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'admin', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'calendar', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'events', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'companies', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'contacts', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'departments', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'files', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'file_folders', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'forums', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'help', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'projects', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'system', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'tasks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'task_log', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'ticketsmith', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'public', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'roles', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'users', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'human_resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'costs', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'risks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'communication', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'timeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'scopeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'admin', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'calendar', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'events', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'companies', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'contacts', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'departments', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'files', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'file_folders', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'forums', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'help', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'projects', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'system', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'tasks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'task_log', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'ticketsmith', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'public', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'roles', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'users', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'human_resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'costs', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'risks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'communication', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'timeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'scopeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'admin', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'calendar', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'events', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'companies', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'contacts', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'departments', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'files', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'file_folders', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'forums', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'help', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'projects', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'system', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'tasks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'task_log', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'ticketsmith', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'public', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'roles', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'users', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'human_resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'costs', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'risks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'communication', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'timeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'scopeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'admin', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'calendar', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'events', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'companies', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'contacts', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'departments', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'files', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'file_folders', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'forums', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'help', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'projects', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'system', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'tasks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'task_log', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'ticketsmith', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'public', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'roles', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'users', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'human_resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'costs', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'risks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'communication', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'timeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '4', 'app', 'scopeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'admin', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'calendar', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'events', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'companies', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'contacts', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'departments', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'files', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'file_folders', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'forums', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'help', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'projects', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'system', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'tasks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'task_log', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'ticketsmith', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'public', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'roles', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'users', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'human_resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'costs', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'risks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'communication', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'timeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'scopeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'admin', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'calendar', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'events', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'companies', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'contacts', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'departments', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'files', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'file_folders', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'forums', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'help', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'projects', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'system', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'tasks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'task_log', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'ticketsmith', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'public', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'roles', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'users', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'human_resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'costs', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'risks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'communication', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'timeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'scopeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'admin', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'calendar', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'events', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'companies', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'contacts', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'departments', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'files', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'file_folders', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'forums', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'help', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'projects', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'system', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'tasks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'task_log', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'ticketsmith', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'public', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'roles', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'users', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'human_resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'costs', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'risks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'communication', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'timeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'scopeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'admin', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'calendar', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'events', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'companies', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'contacts', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'departments', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'files', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'file_folders', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'forums', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'help', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'projects', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'system', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'tasks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'task_log', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'ticketsmith', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'public', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'roles', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'users', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'human_resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'costs', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'risks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'communication', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'timeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'scopeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'admin', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'calendar', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'events', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'companies', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'contacts', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'departments', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'files', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'file_folders', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'forums', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'help', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'projects', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'system', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'tasks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'task_log', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'ticketsmith', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'public', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'roles', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'users', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'human_resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'costs', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'risks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'communication', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'timeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '5', 'app', 'scopeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'admin', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'calendar', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'events', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'companies', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'contacts', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'departments', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'files', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'file_folders', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'forums', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'help', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'projects', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'system', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'tasks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'task_log', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'ticketsmith', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'public', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'roles', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'users', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'human_resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'costs', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'risks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'communication', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'timeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'scopeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'admin', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'calendar', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'events', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'companies', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'contacts', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'departments', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'files', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'file_folders', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'forums', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'help', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'projects', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'system', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'tasks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'task_log', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'ticketsmith', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'public', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'roles', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'users', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'human_resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'costs', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'risks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'communication', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'timeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'scopeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'admin', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'calendar', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'events', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'companies', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'contacts', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'departments', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'files', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'file_folders', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'forums', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'help', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'projects', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'system', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'tasks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'task_log', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'ticketsmith', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'public', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'roles', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'users', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'human_resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'costs', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'risks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'communication', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'timeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'scopeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'admin', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'calendar', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'events', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'companies', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'contacts', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'departments', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'files', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'file_folders', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'forums', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'help', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'projects', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'system', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'tasks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'task_log', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'ticketsmith', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'public', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'roles', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'users', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'human_resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'costs', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'risks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'communication', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'timeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'scopeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'admin', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'calendar', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'events', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'companies', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'contacts', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'departments', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'files', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'file_folders', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'forums', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'help', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'projects', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'system', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'tasks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'task_log', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'ticketsmith', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'public', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'roles', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'users', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'human_resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'costs', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'risks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'communication', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'timeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '6', 'app', 'scopeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'admin', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'calendar', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'events', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'companies', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'contacts', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'departments', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'files', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'file_folders', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'forums', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'help', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'projects', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'system', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'tasks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'task_log', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'ticketsmith', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'public', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'roles', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'users', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'human_resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'costs', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'risks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'communication', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'timeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'scopeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'admin', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'calendar', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'events', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'companies', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'contacts', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'departments', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'files', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'file_folders', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'forums', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'help', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'projects', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'system', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'tasks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'task_log', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'ticketsmith', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'public', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'roles', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'users', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'human_resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'costs', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'risks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'communication', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'timeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'scopeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'admin', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'calendar', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'events', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'companies', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'contacts', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'departments', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'files', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'file_folders', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'forums', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'help', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'projects', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'system', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'tasks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'task_log', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'ticketsmith', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'public', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'roles', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'users', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'human_resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'costs', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'risks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'communication', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'timeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'scopeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'admin', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'calendar', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'events', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'companies', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'contacts', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'departments', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'files', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'file_folders', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'forums', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'help', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'projects', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'system', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'tasks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'task_log', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'ticketsmith', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'public', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'roles', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'users', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'human_resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'costs', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'risks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'communication', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'timeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'scopeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'admin', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'calendar', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'events', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'companies', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'contacts', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'departments', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'files', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'file_folders', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'forums', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'help', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'projects', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'system', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'tasks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'task_log', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'ticketsmith', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'public', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'roles', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'users', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'human_resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'costs', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'risks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'communication', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'timeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('11', '7', 'app', 'scopeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'calendar', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'events', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'companies', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'contacts', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'departments', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'files', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'file_folders', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'forums', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'help', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'projects', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'tasks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'task_log', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'ticketsmith', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'public', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'human_resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'resources', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'costs', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'risks', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'communication', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'timeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'scopeplanning', 'access', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'calendar', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'events', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'companies', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'contacts', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'departments', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'files', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'file_folders', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'forums', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'help', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'projects', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'tasks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'task_log', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'ticketsmith', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'public', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'human_resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'resources', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'costs', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'risks', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'communication', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'timeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'scopeplanning', 'add', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'calendar', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'events', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'companies', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'contacts', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'departments', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'files', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'file_folders', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'forums', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'help', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'projects', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'tasks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'task_log', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'ticketsmith', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'public', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'human_resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'resources', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'costs', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'risks', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'communication', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'timeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'scopeplanning', 'delete', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'calendar', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'events', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'companies', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'contacts', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'departments', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'files', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'file_folders', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'forums', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'help', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'projects', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'tasks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'task_log', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'ticketsmith', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'public', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'human_resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'resources', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'costs', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'risks', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'communication', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'timeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'scopeplanning', 'edit', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'calendar', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'events', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'companies', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'contacts', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'departments', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'files', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'file_folders', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'forums', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'help', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'projects', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'tasks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'task_log', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'ticketsmith', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'public', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'human_resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'resources', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'costs', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'risks', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'communication', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'timeplanning', 'view', '1', '4', '1');
INSERT INTO dotp_dotpermissions VALUES ('15', '8', 'app', 'scopeplanning', 'view', '1', '4', '1');

-- ----------------------------
-- Table structure for `dotp_dpversion`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_dpversion`;
CREATE TABLE `dotp_dpversion` (
  `code_version` varchar(10) NOT NULL DEFAULT '',
  `db_version` int(11) NOT NULL DEFAULT '0',
  `last_db_update` date NOT NULL DEFAULT '0000-00-00',
  `last_code_update` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_dpversion
-- ----------------------------
INSERT INTO dotp_dpversion VALUES ('2.1.7', '2', '2012-08-14', '2012-11-15');

-- ----------------------------
-- Table structure for `dotp_eap_item_estimations`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_eap_item_estimations`;
CREATE TABLE `dotp_eap_item_estimations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eap_item_id` int(11) DEFAULT NULL,
  `size` float DEFAULT NULL,
  `size_unit` text,
  PRIMARY KEY (`id`),
  KEY `fk_estimation_eap_item` (`eap_item_id`),
  CONSTRAINT `fk_estimation_eap_item` FOREIGN KEY (`eap_item_id`) REFERENCES `dotp_project_eap_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_eap_item_estimations
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_events`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_events`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_events
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_event_queue`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_event_queue`;
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
  `queue_module_type` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`queue_id`),
  KEY `queue_start` (`queue_start`),
  KEY `queue_module` (`queue_module`),
  KEY `queue_type` (`queue_type`),
  KEY `queue_origin_id` (`queue_origin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_event_queue
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_files`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_files`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_files
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_files_index`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_files_index`;
CREATE TABLE `dotp_files_index` (
  `file_id` int(11) NOT NULL DEFAULT '0',
  `word` varchar(50) NOT NULL DEFAULT '',
  `word_placement` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`,`word`,`word_placement`),
  KEY `idx_fwrd` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_files_index
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_file_folders`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_file_folders`;
CREATE TABLE `dotp_file_folders` (
  `file_folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_folder_parent` int(11) NOT NULL DEFAULT '0',
  `file_folder_name` varchar(255) NOT NULL DEFAULT '',
  `file_folder_description` text,
  PRIMARY KEY (`file_folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_file_folders
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_forums`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_forums`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_forums
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_forum_messages`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_forum_messages`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_forum_messages
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_forum_visits`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_forum_visits`;
CREATE TABLE `dotp_forum_visits` (
  `visit_user` int(10) NOT NULL DEFAULT '0',
  `visit_forum` int(10) NOT NULL DEFAULT '0',
  `visit_message` int(10) NOT NULL DEFAULT '0',
  `visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_fv` (`visit_user`,`visit_forum`,`visit_message`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_forum_visits
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_forum_watch`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_forum_watch`;
CREATE TABLE `dotp_forum_watch` (
  `watch_user` int(10) unsigned NOT NULL DEFAULT '0',
  `watch_forum` int(10) unsigned DEFAULT NULL,
  `watch_topic` int(10) unsigned DEFAULT NULL,
  KEY `idx_fw1` (`watch_user`,`watch_forum`),
  KEY `idx_fw2` (`watch_user`,`watch_topic`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links users to the forums/messages they are watching';

-- ----------------------------
-- Records of dotp_forum_watch
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_gacl_acl`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_acl`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_acl
-- ----------------------------
INSERT INTO dotp_gacl_acl VALUES ('10', 'user', '1', '1', null, null, '1357696089');
INSERT INTO dotp_gacl_acl VALUES ('11', 'user', '1', '1', null, null, '1357696089');
INSERT INTO dotp_gacl_acl VALUES ('12', 'user', '1', '1', null, null, '1357696090');
INSERT INTO dotp_gacl_acl VALUES ('13', 'user', '1', '1', null, null, '1357696090');
INSERT INTO dotp_gacl_acl VALUES ('14', 'user', '1', '1', null, null, '1357696090');
INSERT INTO dotp_gacl_acl VALUES ('15', 'user', '1', '1', null, null, '1357696090');
INSERT INTO dotp_gacl_acl VALUES ('16', 'user', '1', '1', null, null, '1357696091');
INSERT INTO dotp_gacl_acl VALUES ('18', 'user', '1', '1', null, null, '1362686264');
INSERT INTO dotp_gacl_acl VALUES ('20', 'user', '1', '1', null, null, '1362687098');
INSERT INTO dotp_gacl_acl VALUES ('21', 'user', '1', '1', null, null, '1362688452');
INSERT INTO dotp_gacl_acl VALUES ('22', 'user', '1', '1', null, null, '1363182876');
INSERT INTO dotp_gacl_acl VALUES ('23', 'user', '1', '1', null, null, '1363182894');
INSERT INTO dotp_gacl_acl VALUES ('24', 'user', '1', '1', null, null, '1364857074');
INSERT INTO dotp_gacl_acl VALUES ('25', 'user', '1', '1', null, null, '1364857092');
INSERT INTO dotp_gacl_acl VALUES ('26', 'user', '1', '1', null, null, '1364857104');

-- ----------------------------
-- Table structure for `dotp_gacl_acl_sections`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_acl_sections`;
CREATE TABLE `dotp_gacl_acl_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_acl_sections
-- ----------------------------
INSERT INTO dotp_gacl_acl_sections VALUES ('1', 'system', '1', 'System', '0');
INSERT INTO dotp_gacl_acl_sections VALUES ('2', 'user', '2', 'User', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_acl_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_acl_seq`;
CREATE TABLE `dotp_gacl_acl_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_acl_seq
-- ----------------------------
INSERT INTO dotp_gacl_acl_seq VALUES ('26');

-- ----------------------------
-- Table structure for `dotp_gacl_aco`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aco`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aco
-- ----------------------------
INSERT INTO dotp_gacl_aco VALUES ('10', 'system', 'login', '1', 'Login', '0');
INSERT INTO dotp_gacl_aco VALUES ('11', 'application', 'access', '1', 'Access', '0');
INSERT INTO dotp_gacl_aco VALUES ('12', 'application', 'view', '2', 'View', '0');
INSERT INTO dotp_gacl_aco VALUES ('13', 'application', 'add', '3', 'Add', '0');
INSERT INTO dotp_gacl_aco VALUES ('14', 'application', 'edit', '4', 'Edit', '0');
INSERT INTO dotp_gacl_aco VALUES ('15', 'application', 'delete', '5', 'Delete', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_aco_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aco_map`;
CREATE TABLE `dotp_gacl_aco_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aco_map
-- ----------------------------
INSERT INTO dotp_gacl_aco_map VALUES ('10', 'system', 'login');
INSERT INTO dotp_gacl_aco_map VALUES ('11', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('11', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('11', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('11', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('11', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('12', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('13', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('13', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('14', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('15', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('15', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('15', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('15', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('15', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('16', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('16', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('18', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('18', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('18', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('18', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('18', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('20', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('20', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('20', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('20', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('20', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('21', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('21', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('21', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('21', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('21', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('22', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('22', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('22', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('22', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('22', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('23', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('23', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('23', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('23', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('23', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('24', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('24', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('24', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('24', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('24', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('25', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('25', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('25', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('25', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('25', 'application', 'view');
INSERT INTO dotp_gacl_aco_map VALUES ('26', 'application', 'access');
INSERT INTO dotp_gacl_aco_map VALUES ('26', 'application', 'add');
INSERT INTO dotp_gacl_aco_map VALUES ('26', 'application', 'delete');
INSERT INTO dotp_gacl_aco_map VALUES ('26', 'application', 'edit');
INSERT INTO dotp_gacl_aco_map VALUES ('26', 'application', 'view');

-- ----------------------------
-- Table structure for `dotp_gacl_aco_sections`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aco_sections`;
CREATE TABLE `dotp_gacl_aco_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aco_sections
-- ----------------------------
INSERT INTO dotp_gacl_aco_sections VALUES ('10', 'system', '1', 'System', '0');
INSERT INTO dotp_gacl_aco_sections VALUES ('11', 'application', '2', 'Application', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_aco_sections_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aco_sections_seq`;
CREATE TABLE `dotp_gacl_aco_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aco_sections_seq
-- ----------------------------
INSERT INTO dotp_gacl_aco_sections_seq VALUES ('11');

-- ----------------------------
-- Table structure for `dotp_gacl_aco_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aco_seq`;
CREATE TABLE `dotp_gacl_aco_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aco_seq
-- ----------------------------
INSERT INTO dotp_gacl_aco_seq VALUES ('15');

-- ----------------------------
-- Table structure for `dotp_gacl_aro`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro
-- ----------------------------
INSERT INTO dotp_gacl_aro VALUES ('10', 'user', '1', '1', 'admin', '0');
INSERT INTO dotp_gacl_aro VALUES ('13', 'user', '4', '1', 'rafael queiroz goncalves', '0');
INSERT INTO dotp_gacl_aro VALUES ('14', 'user', '5', '1', 'andre marques pereira', '0');
INSERT INTO dotp_gacl_aro VALUES ('15', 'user', '6', '1', 'christiane gresse von wangenheim', '0');
INSERT INTO dotp_gacl_aro VALUES ('16', 'user', '7', '1', 'paulo', '0');
INSERT INTO dotp_gacl_aro VALUES ('17', 'user', '8', '1', 'marcelo silva', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_groups`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_groups`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_groups
-- ----------------------------
INSERT INTO dotp_gacl_aro_groups VALUES ('10', '0', '1', '10', 'Roles', 'role');
INSERT INTO dotp_gacl_aro_groups VALUES ('11', '10', '2', '3', 'Administrator', 'admin');
INSERT INTO dotp_gacl_aro_groups VALUES ('12', '10', '4', '5', 'Anonymous', 'anon');
INSERT INTO dotp_gacl_aro_groups VALUES ('13', '10', '6', '7', 'Guest', 'guest');
INSERT INTO dotp_gacl_aro_groups VALUES ('14', '10', '8', '9', 'Project worker', 'normal');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_groups_id_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_groups_id_seq`;
CREATE TABLE `dotp_gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_groups_id_seq
-- ----------------------------
INSERT INTO dotp_gacl_aro_groups_id_seq VALUES ('14');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_groups_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_groups_map`;
CREATE TABLE `dotp_gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_groups_map
-- ----------------------------
INSERT INTO dotp_gacl_aro_groups_map VALUES ('10', '10');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('11', '11');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('12', '11');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('13', '13');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('14', '12');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('15', '14');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('16', '13');
INSERT INTO dotp_gacl_aro_groups_map VALUES ('16', '14');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_map`;
CREATE TABLE `dotp_gacl_aro_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_map
-- ----------------------------
INSERT INTO dotp_gacl_aro_map VALUES ('18', 'user', '1');
INSERT INTO dotp_gacl_aro_map VALUES ('20', 'user', '1');
INSERT INTO dotp_gacl_aro_map VALUES ('21', 'user', '1');
INSERT INTO dotp_gacl_aro_map VALUES ('22', 'user', '7');
INSERT INTO dotp_gacl_aro_map VALUES ('23', 'user', '7');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_sections`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_sections`;
CREATE TABLE `dotp_gacl_aro_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_sections
-- ----------------------------
INSERT INTO dotp_gacl_aro_sections VALUES ('10', 'user', '1', 'Users', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_sections_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_sections_seq`;
CREATE TABLE `dotp_gacl_aro_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_sections_seq
-- ----------------------------
INSERT INTO dotp_gacl_aro_sections_seq VALUES ('10');

-- ----------------------------
-- Table structure for `dotp_gacl_aro_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_aro_seq`;
CREATE TABLE `dotp_gacl_aro_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_aro_seq
-- ----------------------------
INSERT INTO dotp_gacl_aro_seq VALUES ('19');

-- ----------------------------
-- Table structure for `dotp_gacl_axo`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo
-- ----------------------------
INSERT INTO dotp_gacl_axo VALUES ('10', 'sys', 'acl', '1', 'ACL Administration', '0');
INSERT INTO dotp_gacl_axo VALUES ('11', 'app', 'admin', '1', 'User Administration', '0');
INSERT INTO dotp_gacl_axo VALUES ('12', 'app', 'calendar', '2', 'Calendar', '0');
INSERT INTO dotp_gacl_axo VALUES ('13', 'app', 'events', '2', 'Events', '0');
INSERT INTO dotp_gacl_axo VALUES ('14', 'app', 'companies', '3', 'Companies', '0');
INSERT INTO dotp_gacl_axo VALUES ('15', 'app', 'contacts', '4', 'Contacts', '0');
INSERT INTO dotp_gacl_axo VALUES ('16', 'app', 'departments', '5', 'Departments', '0');
INSERT INTO dotp_gacl_axo VALUES ('17', 'app', 'files', '6', 'Files', '0');
INSERT INTO dotp_gacl_axo VALUES ('18', 'app', 'file_folders', '6', 'File Folders', '0');
INSERT INTO dotp_gacl_axo VALUES ('19', 'app', 'forums', '7', 'Forums', '0');
INSERT INTO dotp_gacl_axo VALUES ('20', 'app', 'help', '8', 'Help', '0');
INSERT INTO dotp_gacl_axo VALUES ('21', 'app', 'projects', '9', 'Projects', '0');
INSERT INTO dotp_gacl_axo VALUES ('22', 'app', 'system', '10', 'System Administration', '0');
INSERT INTO dotp_gacl_axo VALUES ('23', 'app', 'tasks', '11', 'Tasks', '0');
INSERT INTO dotp_gacl_axo VALUES ('24', 'app', 'task_log', '11', 'Task Logs', '0');
INSERT INTO dotp_gacl_axo VALUES ('25', 'app', 'ticketsmith', '12', 'Tickets', '0');
INSERT INTO dotp_gacl_axo VALUES ('26', 'app', 'public', '13', 'Public', '0');
INSERT INTO dotp_gacl_axo VALUES ('27', 'app', 'roles', '14', 'Roles Administration', '0');
INSERT INTO dotp_gacl_axo VALUES ('28', 'app', 'users', '15', 'User Table', '0');
INSERT INTO dotp_gacl_axo VALUES ('44', 'app', 'human_resources', '1', 'Human Resources', '0');
INSERT INTO dotp_gacl_axo VALUES ('47', 'app', 'resources', '1', 'Resources', '0');
INSERT INTO dotp_gacl_axo VALUES ('61', 'app', 'costs', '1', 'Costs', '0');
INSERT INTO dotp_gacl_axo VALUES ('63', 'app', 'risks', '1', 'Risks', '0');
INSERT INTO dotp_gacl_axo VALUES ('64', 'app', 'communication', '1', 'Communication', '0');
INSERT INTO dotp_gacl_axo VALUES ('69', 'app', 'initiating', '1', 'Initiating', '0');
INSERT INTO dotp_gacl_axo VALUES ('70', 'app', 'stakeholder', '1', 'Stakeholder', '0');
INSERT INTO dotp_gacl_axo VALUES ('74', 'app', 'timeplanning', '1', 'Time Planning', '0');
INSERT INTO dotp_gacl_axo VALUES ('84', 'app', 'scopeplanning', '1', 'Planejamento do Escopo', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_groups`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_groups`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_groups
-- ----------------------------
INSERT INTO dotp_gacl_axo_groups VALUES ('10', '0', '1', '8', 'Modules', 'mod');
INSERT INTO dotp_gacl_axo_groups VALUES ('11', '10', '2', '3', 'All Modules', 'all');
INSERT INTO dotp_gacl_axo_groups VALUES ('12', '10', '4', '5', 'Admin Modules', 'admin');
INSERT INTO dotp_gacl_axo_groups VALUES ('13', '10', '6', '7', 'Non-Admin Modules', 'non_admin');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_groups_id_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_groups_id_seq`;
CREATE TABLE `dotp_gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_groups_id_seq
-- ----------------------------
INSERT INTO dotp_gacl_axo_groups_id_seq VALUES ('13');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_groups_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_groups_map`;
CREATE TABLE `dotp_gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_groups_map
-- ----------------------------
INSERT INTO dotp_gacl_axo_groups_map VALUES ('11', '11');
INSERT INTO dotp_gacl_axo_groups_map VALUES ('13', '13');
INSERT INTO dotp_gacl_axo_groups_map VALUES ('14', '13');
INSERT INTO dotp_gacl_axo_groups_map VALUES ('15', '13');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_map`;
CREATE TABLE `dotp_gacl_axo_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_map
-- ----------------------------
INSERT INTO dotp_gacl_axo_map VALUES ('12', 'sys', 'acl');
INSERT INTO dotp_gacl_axo_map VALUES ('16', 'app', 'users');
INSERT INTO dotp_gacl_axo_map VALUES ('18', 'app', 'admin');
INSERT INTO dotp_gacl_axo_map VALUES ('20', 'app', 'initiating');
INSERT INTO dotp_gacl_axo_map VALUES ('21', 'app', 'stakeholder');
INSERT INTO dotp_gacl_axo_map VALUES ('22', 'app', 'initiating');
INSERT INTO dotp_gacl_axo_map VALUES ('23', 'app', 'stakeholder');
INSERT INTO dotp_gacl_axo_map VALUES ('24', 'app', 'admin');
INSERT INTO dotp_gacl_axo_map VALUES ('25', 'app', 'initiating');
INSERT INTO dotp_gacl_axo_map VALUES ('26', 'app', 'stakeholder');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_sections`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_sections`;
CREATE TABLE `dotp_gacl_axo_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(80) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_sections
-- ----------------------------
INSERT INTO dotp_gacl_axo_sections VALUES ('10', 'sys', '1', 'System', '0');
INSERT INTO dotp_gacl_axo_sections VALUES ('11', 'app', '2', 'Application', '0');
INSERT INTO dotp_gacl_axo_sections VALUES ('12', 'resources', '0', 'Resources Record', '0');
INSERT INTO dotp_gacl_axo_sections VALUES ('13', 'costs', '0', 'Costs Record', '0');
INSERT INTO dotp_gacl_axo_sections VALUES ('14', 'communication', '0', 'Communication Record', '0');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_sections_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_sections_seq`;
CREATE TABLE `dotp_gacl_axo_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_sections_seq
-- ----------------------------
INSERT INTO dotp_gacl_axo_sections_seq VALUES ('14');

-- ----------------------------
-- Table structure for `dotp_gacl_axo_seq`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_axo_seq`;
CREATE TABLE `dotp_gacl_axo_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_axo_seq
-- ----------------------------
INSERT INTO dotp_gacl_axo_seq VALUES ('84');

-- ----------------------------
-- Table structure for `dotp_gacl_groups_aro_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_groups_aro_map`;
CREATE TABLE `dotp_gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`aro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_groups_aro_map
-- ----------------------------
INSERT INTO dotp_gacl_groups_aro_map VALUES ('11', '10');
INSERT INTO dotp_gacl_groups_aro_map VALUES ('11', '13');
INSERT INTO dotp_gacl_groups_aro_map VALUES ('11', '14');
INSERT INTO dotp_gacl_groups_aro_map VALUES ('11', '15');
INSERT INTO dotp_gacl_groups_aro_map VALUES ('11', '16');
INSERT INTO dotp_gacl_groups_aro_map VALUES ('14', '17');

-- ----------------------------
-- Table structure for `dotp_gacl_groups_axo_map`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_groups_axo_map`;
CREATE TABLE `dotp_gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `axo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`axo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_groups_axo_map
-- ----------------------------
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '11');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '12');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '13');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '14');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '15');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '16');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '17');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '18');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '19');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '20');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '21');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '22');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '23');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '24');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '25');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '26');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '27');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '28');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '44');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '47');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '61');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '63');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '64');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '74');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('11', '84');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('12', '11');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('12', '22');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('12', '27');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('12', '28');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '12');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '13');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '14');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '15');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '16');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '17');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '18');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '19');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '20');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '21');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '23');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '24');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '25');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '26');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '44');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '47');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '61');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '63');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '64');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '74');
INSERT INTO dotp_gacl_groups_axo_map VALUES ('13', '84');

-- ----------------------------
-- Table structure for `dotp_gacl_phpgacl`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_gacl_phpgacl`;
CREATE TABLE `dotp_gacl_phpgacl` (
  `name` varchar(230) NOT NULL DEFAULT '',
  `value` varchar(230) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_gacl_phpgacl
-- ----------------------------
INSERT INTO dotp_gacl_phpgacl VALUES ('schema_version', '2.1');
INSERT INTO dotp_gacl_phpgacl VALUES ('version', '3.3.2');

-- ----------------------------
-- Table structure for `dotp_human_resource`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_human_resource`;
CREATE TABLE `dotp_human_resource` (
  `human_resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `human_resource_user_id` int(11) NOT NULL,
  `human_resource_lattes_url` text,
  `human_resource_mon` int(11) DEFAULT NULL,
  `human_resource_tue` int(11) DEFAULT NULL,
  `human_resource_wed` int(11) DEFAULT NULL,
  `human_resource_thu` int(11) DEFAULT NULL,
  `human_resource_fri` int(11) DEFAULT NULL,
  `human_resource_sat` int(11) DEFAULT NULL,
  `human_resource_sun` int(11) DEFAULT NULL,
  PRIMARY KEY (`human_resource_id`),
  KEY `dotp_human_resource_ibfk_1` (`human_resource_user_id`),
  CONSTRAINT `dotp_human_resource_ibfk_1` FOREIGN KEY (`human_resource_user_id`) REFERENCES `dotp_users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_human_resource
-- ----------------------------
INSERT INTO dotp_human_resource VALUES ('3', '6', 'http://lattes.cnpq.br/', '4', '4', '4', '4', '4', '0', '0');
INSERT INTO dotp_human_resource VALUES ('4', '5', 'http://lattes.cnpq.br/', '6', '6', '6', '6', '6', '0', '0');
INSERT INTO dotp_human_resource VALUES ('5', '4', 'http://lattes.cnpq.br/', '8', '8', '8', '8', '8', '0', '0');
INSERT INTO dotp_human_resource VALUES ('6', '7', 'http://lattes.cnpq.br/', '4', '4', '4', '4', '4', '0', '0');
INSERT INTO dotp_human_resource VALUES ('7', '8', 'http://lattes.cnpq.br/', '5', '5', '5', '5', '5', '0', '0');

-- ----------------------------
-- Table structure for `dotp_human_resources_role`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_human_resources_role`;
CREATE TABLE `dotp_human_resources_role` (
  `human_resources_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `human_resources_role_name` text NOT NULL,
  `human_resources_role_authority` text,
  `human_resources_role_responsability` text,
  `human_resources_role_competence` text,
  `human_resources_role_company_id` int(11) NOT NULL,
  PRIMARY KEY (`human_resources_role_id`),
  KEY `dotp_human_resources_role_ibfk_1` (`human_resources_role_company_id`),
  CONSTRAINT `dotp_human_resources_role_ibfk_1` FOREIGN KEY (`human_resources_role_company_id`) REFERENCES `dotp_companies` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_human_resources_role
-- ----------------------------
INSERT INTO dotp_human_resources_role VALUES ('4', 'Project manager', 'Pode alocar os membros da equipe nas atividades que desejar, e tambÃ©m pode solicitar aquisiÃ§Ã£o dos materiais e serviÃ§os essenciais para o trabalho.', 'Elaborar o termo de abertura do projeto.\r\nElaborar o plano do projeto.\r\nExecutar o plano do projeto.\r\nMonitorar e controlar o projeto.\r\nEncerrar formalmente o projeto.', 'Deve possuir treinamento na Ã¡rea de gerÃªncia de projetos e preferencialmente ser certificado PMP.', '2');
INSERT INTO dotp_human_resources_role VALUES ('5', 'Programer', 'Requisitar as ferramentas de software e hardware necessÃ¡rias para execuÃ§Ã£o de seu trabalho.\r\nSolicitar treinamento quando alocado para uma atividade a qual nÃ£o possuem competÃªncia.', 'Executar as atividades do projeto as quais foi alocado. \r\nRealizar testes de unidade e integraÃ§Ã£o nos componentes de software os quais estÃ¡ trabalhando.', 'FormaÃ§Ã£o completa em cursos superior de CiÃªncia da ComputaÃ§Ã£o ou \r\nSistemas de InformaÃ§Ã£o. Possuir certificaÃ§Ãµes nas linguagens de programaÃ§Ã£o as quais foi alocado.', '2');
INSERT INTO dotp_human_resources_role VALUES ('6', 'Test analyst', 'Exigir a execuÃ§Ã£o dos testes antes de qualquer entrega.', 'Escrever os casos de teste. Executar os testes de sistema, e acompanhar o cliente na execuÃ§Ã£o dos testes de aceite.', 'FormaÃ§Ã£o em curso superior em CiÃªncia da ComputaÃ§Ã£o ou Sistemas da InformaÃ§Ã£o.', '2');
INSERT INTO dotp_human_resources_role VALUES ('7', 'System analyst', 'Agendar reuniÃµes com o cliente sempre que necessÃ¡rio. Definir tecnologias apropriadas para construÃ§Ã£o do produto, respeitando as restriÃ§Ãµes impostas pelo cliente.', 'Realizar reuniÃµes junto aos clientes para reconhecimento do negÃ³cio. Modelar o negÃ³cio do cliente e identificar requisitos de software que suportem uma soluÃ§Ã£o apropriada. Modelar a soluÃ§Ã£o e apresenta-la Ã  equipe e ao cliente. Validar requisitos e modelos de sistema junto ao cliente.', 'FormaÃ§Ã£o em CiÃªncia da ComputaÃ§Ã£o ou Sistemas da InformaÃ§Ã£o. Entendimento de modelagem de sistemas. CerificaÃ§Ã£o em UML Ã© desejÃ¡vel.', '2');
INSERT INTO dotp_human_resources_role VALUES ('8', 'Quality manager', 'Denunciar nÃ£o conformidades do processo ou do produto ao gerente de projetos.', 'Executar auditorias internas de acordo com as especificaÃ§Ãµes das normas adotadas pela organizaÃ§Ã£o. Buscar discrepÃ¢ncias entre o processo executado e o processo definido. \r\nRealiza inspeÃ§Ãµes nos componentes de software desenvolvidos pela organizaÃ§Ã£o, e confere seu suporte/desempenho em relaÃ§Ã£o Ã  especificaÃ§Ã£o.', 'GraduaÃ§Ã£o de nÃ­vel superior em alguma Ã¡rea da tecnologia da informaÃ§Ã£o. \r\nConhecimento sobre os modelos de maturidade e capacidade de software.', '2');

-- ----------------------------
-- Table structure for `dotp_human_resource_allocation`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_human_resource_allocation`;
CREATE TABLE `dotp_human_resource_allocation` (
  `human_resource_allocation_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_tasks_estimated_roles_id` bigint(20) NOT NULL,
  `human_resource_id` int(11) NOT NULL,
  PRIMARY KEY (`human_resource_allocation_id`),
  KEY `dotp_human_resource_allocation_ibfk_2` (`project_tasks_estimated_roles_id`),
  KEY `dotp_human_resource_allocation_ibfk_1` (`human_resource_id`),
  CONSTRAINT `dotp_human_resource_allocation_ibfk_1` FOREIGN KEY (`human_resource_id`) REFERENCES `dotp_human_resource` (`human_resource_id`) ON DELETE CASCADE,
  CONSTRAINT `dotp_human_resource_allocation_ibfk_2` FOREIGN KEY (`project_tasks_estimated_roles_id`) REFERENCES `dotp_project_tasks_estimated_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_human_resource_allocation
-- ----------------------------
INSERT INTO dotp_human_resource_allocation VALUES ('18', '575', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('19', '561', '4');
INSERT INTO dotp_human_resource_allocation VALUES ('20', '553', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('21', '554', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('22', '551', '4');
INSERT INTO dotp_human_resource_allocation VALUES ('23', '552', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('24', '557', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('25', '559', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('26', '560', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('27', '562', '4');
INSERT INTO dotp_human_resource_allocation VALUES ('28', '564', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('29', '550', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('30', '565', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('31', '570', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('32', '571', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('33', '548', '7');
INSERT INTO dotp_human_resource_allocation VALUES ('34', '548', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('35', '573', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('36', '546', '4');
INSERT INTO dotp_human_resource_allocation VALUES ('37', '547', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('38', '549', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('39', '556', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('41', '572', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('42', '563', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('43', '555', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('44', '558', '4');
INSERT INTO dotp_human_resource_allocation VALUES ('45', '574', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('46', '566', '3');
INSERT INTO dotp_human_resource_allocation VALUES ('47', '567', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('48', '568', '4');
INSERT INTO dotp_human_resource_allocation VALUES ('49', '569', '5');
INSERT INTO dotp_human_resource_allocation VALUES ('50', '636', '6');
INSERT INTO dotp_human_resource_allocation VALUES ('51', '637', '6');
INSERT INTO dotp_human_resource_allocation VALUES ('52', '638', '6');

-- ----------------------------
-- Table structure for `dotp_human_resource_roles`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_human_resource_roles`;
CREATE TABLE `dotp_human_resource_roles` (
  `human_resource_roles_id` int(11) NOT NULL AUTO_INCREMENT,
  `human_resources_role_id` int(11) NOT NULL,
  `human_resource_id` int(11) NOT NULL,
  PRIMARY KEY (`human_resource_roles_id`),
  KEY `dotp_human_resource_roles_ibfk_1` (`human_resources_role_id`),
  KEY `dotp_human_resource_roles_ibfk_2` (`human_resource_id`),
  CONSTRAINT `dotp_human_resource_roles_ibfk_1` FOREIGN KEY (`human_resources_role_id`) REFERENCES `dotp_human_resources_role` (`human_resources_role_id`) ON DELETE CASCADE,
  CONSTRAINT `dotp_human_resource_roles_ibfk_2` FOREIGN KEY (`human_resource_id`) REFERENCES `dotp_human_resource` (`human_resource_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_human_resource_roles
-- ----------------------------
INSERT INTO dotp_human_resource_roles VALUES ('6', '5', '5');
INSERT INTO dotp_human_resource_roles VALUES ('7', '7', '5');
INSERT INTO dotp_human_resource_roles VALUES ('9', '6', '4');
INSERT INTO dotp_human_resource_roles VALUES ('12', '4', '3');
INSERT INTO dotp_human_resource_roles VALUES ('18', '5', '7');
INSERT INTO dotp_human_resource_roles VALUES ('25', '8', '6');

-- ----------------------------
-- Table structure for `dotp_initiating`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_initiating`;
CREATE TABLE `dotp_initiating` (
  `initiating_id` int(11) NOT NULL AUTO_INCREMENT,
  `initiating_title` varchar(255) NOT NULL,
  `initiating_manager` int(11) NOT NULL,
  `initiating_create_by` int(11) NOT NULL,
  `initiating_date_create` datetime NOT NULL,
  `initiating_justification` varchar(2000) DEFAULT NULL,
  `initiating_objective` varchar(2000) DEFAULT NULL,
  `initiating_expected_result` varchar(2000) DEFAULT NULL,
  `initiating_premise` varchar(2000) DEFAULT NULL,
  `initiating_restrictions` varchar(2000) DEFAULT NULL,
  `initiating_budget` varchar(2000) DEFAULT NULL,
  `initiating_start_date` date DEFAULT NULL,
  `initiating_end_date` date DEFAULT NULL,
  `initiating_milestone` varchar(2000) DEFAULT NULL,
  `initiating_success` varchar(2000) DEFAULT NULL,
  `initiating_approved` int(1) DEFAULT '0',
  `initiating_authorized` int(1) DEFAULT '0',
  `initiating_completed` int(1) NOT NULL DEFAULT '0',
  `initiating_approved_comments` varchar(2000) DEFAULT NULL,
  `initiating_authorized_comments` varchar(2000) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`initiating_id`),
  KEY `initiation_project_fk` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_initiating
-- ----------------------------
INSERT INTO dotp_initiating VALUES ('2', 'Sistema de encomendas online para pizzaria', '6', '1', '2013-03-12 18:32:21', 'O dono da pizzaria solicitou este projeto para auxiliar a empresa no cumprimento dos seus objetivos estratÃ©gicos. Atualmente a pizzaria oferece a entrega em domicÃ­lio, via ligaÃ§Ãµes telefÃ´nicas. Para ampliar o seu negÃ³cio, ele quer possibilitar que seus clientes, por meio da Internet, possam encomendar pizzas no site do seu estabelecimento. Estas informaÃ§Ãµes serÃ£o processadas por dois de seus atendentes, que precisarÃ£o ser treinados, visto que atualmente tÃªm pouco conhecimento em TI.', 'Adicionar suporte para encomendas de pizzas no site da pizzaria. O sistema deverÃ¡ possuir o cadastro dos clientes, para que possibilite que o cliente faÃ§a login no site, e utilize os dados cadastrados para determinar o local de entrega. O sistema deverÃ¡ possuir a funcionalidade de compra de pizza, em que serÃ£o descriminados os sabores, tamanho, e forma de pagamento. O sistema suportarÃ¡ pagamento por meio de cartÃµes de crÃ©dito e dÃ©bito.', '- MÃ³dulo de login;\r\n- MÃ³dulo de cadastro de cliente;\r\n- MÃ³dulo de compra;\r\n- MÃ³dulo de pagamento via cartÃµes de crÃ©dito e dÃ©bito;\r\n- Manual de instalaÃ§Ã£o do sistema de encomenda;\r\n- CapacitaÃ§Ã£o dos atendentes.', 'O sistema pode ser desenvolvido com base em frameworks existentes para lojas online.', 'O sistema precisa ser integrado no site existente da pizzaria e seguir os padrÃµes da empresa e design.', 'O custo total do projeto estÃ¡ estimado em R$ 30.000,00. A maior parte dos custos referem-se ao esforÃ§o de trabalhos internos.', '2013-03-12', '2013-12-31', '- Release da versÃ£o 1.0 do sistema em 01/10/2013;\r\n- Sistema instalado em 10/12/2013;\r\n- CapacitaÃ§Ã£o dos atendentes em 13/12/2013.', 'Release da versÃ£o 1.0 com todas as funcionalidades requisitadas instaladas e testadas com um custo total que nÃ£o ultrapasse os R$ 30.000,00. Atendentes utilizando o sistema em no mÃ¡ximo 15 dias apÃ³s a instalaÃ§Ã£o.', '1', '1', '1', 'As principais entregas, as premissas e restriÃ§Ãµes, assim como o orÃ§amento estÃ£o adequados.', 'O inÃ­cio do projeto deve ocorrer no dia 12/03/2013, assim como consta na data planejada.', '2');
INSERT INTO dotp_initiating VALUES ('3', 'cccccc', '1', '1', '2013-05-04 14:48:47', null, null, null, null, null, null, null, null, null, null, '0', '0', '0', null, null, null);
INSERT INTO dotp_initiating VALUES ('4', 'blablabla', '1', '1', '2013-05-07 15:33:12', null, null, null, null, null, null, null, null, null, null, '0', '0', '0', null, null, null);

-- ----------------------------
-- Table structure for `dotp_initiating_stakeholder`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_initiating_stakeholder`;
CREATE TABLE `dotp_initiating_stakeholder` (
  `initiating_stakeholder_id` int(11) NOT NULL AUTO_INCREMENT,
  `initiating_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `stakeholder_responsibility` varchar(100) DEFAULT NULL,
  `stakeholder_interest` varchar(100) DEFAULT NULL,
  `stakeholder_power` varchar(100) DEFAULT NULL,
  `stakeholder_strategy` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`initiating_stakeholder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_initiating_stakeholder
-- ----------------------------
INSERT INTO dotp_initiating_stakeholder VALUES ('3', '2', '8', 'Financiar o projeto. Participar das reuniÃµes de elicitaÃ§Ã£o de requisitos. Validar os mÃ³dulos de', '1', '1', 'Gerencie perto.');
INSERT INTO dotp_initiating_stakeholder VALUES ('4', '2', '5', 'Programar os mÃ³dulos web e mobile.', '1', '2', 'Mantenha informado.');
INSERT INTO dotp_initiating_stakeholder VALUES ('5', '2', '7', 'Elaborar o plano de projeto e executÃ¡-lo. Acompanhar a execuÃ§Ã£o do projeto para assegurar que seu', '1', '1', 'Gerencie perto.');
INSERT INTO dotp_initiating_stakeholder VALUES ('6', '2', '6', 'Analisar as necessidades dos funcionÃ¡rios da pizzaria e construir os modelos do sistema. Realizar o', '2', '1', 'Mantenha satisfeito');

-- ----------------------------
-- Table structure for `dotp_modules`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_modules`;
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
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_modules
-- ----------------------------
INSERT INTO dotp_modules VALUES ('1', 'Companies', 'companies', '1.0.0', '', 'core', '1', 'Companies', 'handshake.png', '1', '1', '', 'companies', 'company_id', 'company_name');
INSERT INTO dotp_modules VALUES ('2', 'Projects', 'projects', '1.0.0', '', 'core', '1', 'Projects', 'applet3-48.png', '2', '1', '', 'projects', 'project_id', 'project_name');
INSERT INTO dotp_modules VALUES ('3', 'Tasks', 'tasks', '1.0.0', '', 'core', '1', 'Tasks', 'applet-48.png', '3', '1', '', 'tasks', 'task_id', 'task_name');
INSERT INTO dotp_modules VALUES ('4', 'Calendar', 'calendar', '1.0.0', '', 'core', '0', 'Calendar', 'myevo-appointments.png', '4', '0', '', 'events', 'event_id', 'event_title');
INSERT INTO dotp_modules VALUES ('5', 'Files', 'files', '1.0.0', '', 'core', '0', 'Files', 'folder5.png', '5', '1', '', 'files', 'file_id', 'file_name');
INSERT INTO dotp_modules VALUES ('6', 'Contacts', 'contacts', '1.0.0', '', 'core', '1', 'Contacts', 'monkeychat-48.png', '6', '1', '', 'contacts', 'contact_id', 'contact_title');
INSERT INTO dotp_modules VALUES ('7', 'Forums', 'forums', '1.0.0', '', 'core', '0', 'Forums', 'support.png', '7', '0', '', 'forums', 'forum_id', 'forum_name');
INSERT INTO dotp_modules VALUES ('8', 'Tickets', 'ticketsmith', '1.0.0', '', 'core', '0', 'Tickets', 'ticketsmith.gif', '8', '0', '', '', '', '');
INSERT INTO dotp_modules VALUES ('9', 'User Administration', 'admin', '1.0.0', '', 'core', '1', 'User Admin', 'helix-setup-users.png', '9', '1', '', 'users', 'user_id', 'user_username');
INSERT INTO dotp_modules VALUES ('10', 'System Administration', 'system', '1.0.0', '', 'core', '1', 'System Admin', '48_my_computer.png', '10', '1', '', '', '', '');
INSERT INTO dotp_modules VALUES ('11', 'Departments', 'departments', '1.0.0', '', 'core', '1', 'Departments', 'users.gif', '11', '0', '', 'departments', 'dept_id', 'dept_name');
INSERT INTO dotp_modules VALUES ('12', 'Help', 'help', '1.0.0', '', 'core', '1', 'Help', 'dp.gif', '12', '0', '', '', '', '');
INSERT INTO dotp_modules VALUES ('13', 'Public', 'public', '1.0.0', '', 'core', '1', 'Public', 'users.gif', '13', '0', '', '', '', '');
INSERT INTO dotp_modules VALUES ('29', 'Human Resources', 'human_resources', '1.0', 'SHumanResources', 'user', '1', 'Human Resources', 'applet3-48.png', '14', '1', '', null, null, null);
INSERT INTO dotp_modules VALUES ('32', 'Resources', 'resources', '1.0.1', 'SResource', 'user', '1', 'Resources', 'helpdesk.png', '16', '1', '', 'resources', 'resource_id', 'resource_name');
INSERT INTO dotp_modules VALUES ('46', 'Costs', 'costs', '1.0.1', 'SSetupCosts', 'user', '1', 'Costs', 'costs.png', '18', '1', 'Costs Plan', 'costs', 'cost_id', 'cost_name');
INSERT INTO dotp_modules VALUES ('48', 'Risks', 'risks', '1.0', 'CSetupRisks', 'user', '1', 'Risks', 'risks.png', '20', '1', 'Risks Plan', null, 'risk_id', 'risk_name');
INSERT INTO dotp_modules VALUES ('49', 'Communication', 'communication', '1.0', 'CSetupCommunication', 'user', '1', 'Communication', 'applet3-48.png', '21', '1', 'Communications Planning', 'communication', 'communication_id', 'communication_name');
INSERT INTO dotp_modules VALUES ('54', 'Initiating', 'initiating', '1.0', 'CSetupInitiating', 'user', '1', 'Initiating', 'applet3-48.png', '22', '1', 'Initiating process group implementation', null, null, null);
INSERT INTO dotp_modules VALUES ('55', 'Stakeholder', 'stakeholder', '1.0', 'CSetupStakeholder', 'user', '1', 'Stakeholder', 'applet3-48.png', '23', '1', 'Initiating process group implementation', null, null, null);
INSERT INTO dotp_modules VALUES ('59', 'Time Planning', 'timeplanning', '2.0', 'CSetup_TimePlanning', 'user', '1', 'Time Planning', 'applet3-48.png', '24', '0', 'Time planning', null, null, null);
INSERT INTO dotp_modules VALUES ('69', 'Planejamento do Escopo', 'scopeplanning', '1.0', 'CSetup_ScopePlanning', 'user', '1', 'Escopo', 'scope.png', '25', '1', 'Scope Planning module', null, null, null);

-- ----------------------------
-- Table structure for `dotp_monitoring_baseline`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_baseline`;
CREATE TABLE `dotp_monitoring_baseline` (
  `baseline_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `baseline_name` varchar(255) DEFAULT NULL,
  `baseline_version` varchar(255) DEFAULT NULL,
  `baseline_observation` text,
  `user_id` int(11) NOT NULL,
  `baseline_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`baseline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_baseline
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_baseline_task`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_baseline_task`;
CREATE TABLE `dotp_monitoring_baseline_task` (
  `baseline_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `baseline_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_start_date` datetime DEFAULT NULL,
  `task_duration` float unsigned DEFAULT '0',
  `task_duration_type` int(11) NOT NULL DEFAULT '1',
  `task_hours_worked` float unsigned DEFAULT '0',
  `task_end_date` datetime DEFAULT NULL,
  `task_percent_complete` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`baseline_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_baseline_task
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_baseline_task_log`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_baseline_task_log`;
CREATE TABLE `dotp_monitoring_baseline_task_log` (
  `baseline_task_id_log` int(11) NOT NULL AUTO_INCREMENT,
  `baseline_task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_log_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_log_creator` int(11) NOT NULL DEFAULT '0',
  `task_log_hours` float NOT NULL DEFAULT '0',
  `task_log_date` datetime DEFAULT NULL,
  PRIMARY KEY (`baseline_task_id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_baseline_task_log
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_baseline_user_cost`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_baseline_user_cost`;
CREATE TABLE `dotp_monitoring_baseline_user_cost` (
  `baseline_cost_id` int(11) NOT NULL AUTO_INCREMENT,
  `baseline_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cost_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL,
  `cost_value` decimal(10,2) DEFAULT '0.00',
  `cost_per_use` decimal(11,0) DEFAULT NULL,
  `cost_dt_begin` datetime NOT NULL,
  `cost_dt_end` datetime DEFAULT NULL,
  PRIMARY KEY (`baseline_cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_baseline_user_cost
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_change_request`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_change_request`;
CREATE TABLE `dotp_monitoring_change_request` (
  `change_id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) DEFAULT '0',
  `change_impact` int(11) NOT NULL DEFAULT '0',
  `change_status` int(11) DEFAULT NULL,
  `change_description` text NOT NULL,
  `change_cause` text NOT NULL,
  `change_request` text NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `change_date_limit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `meeting_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) NOT NULL,
  PRIMARY KEY (`change_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_change_request
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting`;
CREATE TABLE `dotp_monitoring_meeting` (
  `meeting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `dt_meeting_begin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ds_title` text NOT NULL,
  `ds_subject` text NOT NULL,
  `dt_meeting_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `meeting_type_id` int(10) NOT NULL,
  PRIMARY KEY (`meeting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting_item`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting_item`;
CREATE TABLE `dotp_monitoring_meeting_item` (
  `meeting_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_item_description` text,
  PRIMARY KEY (`meeting_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting_item
-- ----------------------------
INSERT INTO dotp_monitoring_meeting_item VALUES ('1', 'Is the use and communication of data following the plan?');
INSERT INTO dotp_monitoring_meeting_item VALUES ('2', 'Is the schedule being carried out according to plan?');
INSERT INTO dotp_monitoring_meeting_item VALUES ('3', 'Is the stakeholder involvement following the plan?');
INSERT INTO dotp_monitoring_meeting_item VALUES ('4', 'Were there changes in the risks?');
INSERT INTO dotp_monitoring_meeting_item VALUES ('5', 'Were there risks?');
INSERT INTO dotp_monitoring_meeting_item VALUES ('6', 'Are the costs being carried out according to plan?');

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting_item_select`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting_item_select`;
CREATE TABLE `dotp_monitoring_meeting_item_select` (
  `meeting_item_select_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `meeting_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`meeting_item_select_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting_item_select
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting_item_senior`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting_item_senior`;
CREATE TABLE `dotp_monitoring_meeting_item_senior` (
  `meeting_item_senior_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_percentual` decimal(10,2) DEFAULT '0.00',
  `meeting_size` int(10) unsigned NOT NULL DEFAULT '0',
  `meeting_idc` decimal(10,2) DEFAULT '0.00',
  `meeting_idp` decimal(10,2) DEFAULT '0.00',
  `meeting_vp` decimal(10,2) DEFAULT '0.00',
  `meeting_va` decimal(10,2) DEFAULT '0.00',
  `meeting_cr` decimal(10,2) DEFAULT '0.00',
  `meeting_baseline` int(10) unsigned NOT NULL DEFAULT '0',
  `meeting_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`meeting_item_senior_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting_item_senior
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting_item_tasks_delivered`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting_item_tasks_delivered`;
CREATE TABLE `dotp_monitoring_meeting_item_tasks_delivered` (
  `meeting_item_taks_delivered_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `meeting_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`meeting_item_taks_delivered_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting_item_tasks_delivered
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting_type`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting_type`;
CREATE TABLE `dotp_monitoring_meeting_type` (
  `meeting_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_type_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meeting_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting_type
-- ----------------------------
INSERT INTO dotp_monitoring_meeting_type VALUES ('1', 'Standard');
INSERT INTO dotp_monitoring_meeting_type VALUES ('2', 'Delivery');
INSERT INTO dotp_monitoring_meeting_type VALUES ('3', 'Monitoring');
INSERT INTO dotp_monitoring_meeting_type VALUES ('4', 'Status Report');
INSERT INTO dotp_monitoring_meeting_type VALUES ('5', 'Monitoring / Status Report');

-- ----------------------------
-- Table structure for `dotp_monitoring_meeting_user`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_meeting_user`;
CREATE TABLE `dotp_monitoring_meeting_user` (
  `meeting_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `meeting_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`meeting_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_meeting_user
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_quality`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_quality`;
CREATE TABLE `dotp_monitoring_quality` (
  `quality_id` int(11) NOT NULL AUTO_INCREMENT,
  `quality_type_id` int(11) NOT NULL,
  `quality_description` text,
  `user_id` int(11) DEFAULT NULL,
  `quality_status_id` int(11) NOT NULL,
  `quality_date_end` datetime DEFAULT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`quality_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_quality
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_quality_status`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_quality_status`;
CREATE TABLE `dotp_monitoring_quality_status` (
  `quality_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `quality_status_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`quality_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_quality_status
-- ----------------------------
INSERT INTO dotp_monitoring_quality_status VALUES ('1', 'Pending');
INSERT INTO dotp_monitoring_quality_status VALUES ('2', 'Concluded');
INSERT INTO dotp_monitoring_quality_status VALUES ('3', 'Canceled');

-- ----------------------------
-- Table structure for `dotp_monitoring_quality_type`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_quality_type`;
CREATE TABLE `dotp_monitoring_quality_type` (
  `quality_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `quality_type_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`quality_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_quality_type
-- ----------------------------
INSERT INTO dotp_monitoring_quality_type VALUES ('1', 'Logical Error');
INSERT INTO dotp_monitoring_quality_type VALUES ('2', 'Business Error');
INSERT INTO dotp_monitoring_quality_type VALUES ('3', 'Analysis Error');

-- ----------------------------
-- Table structure for `dotp_monitoring_responsibility_matriz`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_responsibility_matriz`;
CREATE TABLE `dotp_monitoring_responsibility_matriz` (
  `responsibility_id` int(11) NOT NULL AUTO_INCREMENT,
  `responsibility_description` varchar(255) DEFAULT NULL,
  `responsibility_user_id_consultation` int(11) DEFAULT NULL,
  `responsibility_user_id_execut` int(11) DEFAULT NULL,
  `responsibility_user_id_support` int(11) DEFAULT NULL,
  `responsibility_user_id_approve` int(11) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`responsibility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_responsibility_matriz
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_monitoring_user_cost`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_monitoring_user_cost`;
CREATE TABLE `dotp_monitoring_user_cost` (
  `cost_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cost_value` decimal(10,2) DEFAULT '0.00',
  `cost_per_use` decimal(11,0) DEFAULT NULL,
  `cost_dt_begin` datetime NOT NULL,
  `cost_dt_end` datetime DEFAULT NULL,
  PRIMARY KEY (`cost_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_monitoring_user_cost
-- ----------------------------
INSERT INTO dotp_monitoring_user_cost VALUES ('1', '3', '25.00', null, '2013-02-01 00:00:00', '2013-02-28 00:00:00');
INSERT INTO dotp_monitoring_user_cost VALUES ('2', '2', '20.00', null, '2012-02-11 00:00:00', '2013-02-23 00:00:00');
INSERT INTO dotp_monitoring_user_cost VALUES ('3', '6', '18.00', null, '2013-02-01 00:00:00', '2013-12-31 00:00:00');
INSERT INTO dotp_monitoring_user_cost VALUES ('4', '5', '18.00', null, '2013-02-01 00:00:00', '2014-12-31 00:00:00');
INSERT INTO dotp_monitoring_user_cost VALUES ('5', '4', '17.00', null, '2013-02-01 00:00:00', '2013-12-31 00:00:00');
INSERT INTO dotp_monitoring_user_cost VALUES ('6', '7', '18.00', null, '2013-01-01 00:00:00', '2014-07-31 00:00:00');
INSERT INTO dotp_monitoring_user_cost VALUES ('7', '8', '18.00', null, '2013-03-01 00:00:00', '2014-07-31 00:00:00');

-- ----------------------------
-- Table structure for `dotp_need_for_training`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_need_for_training`;
CREATE TABLE `dotp_need_for_training` (
  `project_id` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`project_id`),
  CONSTRAINT `FK_PROJECT_NEED_FOR_TRAINING` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_need_for_training
-- ----------------------------
INSERT INTO dotp_need_for_training VALUES ('2', '<p>Para o projeto da pizzaria do tio Chico ser&aacute; necess&aacute;rio o treinamento dos membros da equipe no que se refere ao desenvolvimento para dispositivos m&oacute;veis. Os membros da equipe n&atilde;o possuem experi&ecirc;ncia pr&eacute;via neste tipo de desenvolvimento. Em&nbsp;decorr&ecirc;ncia do m&oacute;dulo para dispositivo m&oacute;vel ser de essencial import&acirc;ncia para o sucesso do projeto, parte do or&ccedil;amento deve ser destinada para realiza&ccedil;&atilde;o do treinamento. Ser&aacute; realizado um treinamento de 40 horas, com dura&ccedil;&atilde;o de uma semana,&nbsp;pela empresa MobileDeveloperCoach. Todos os programadores envolvidos no projeto devem participar deste treinamento.</p>');

-- ----------------------------
-- Table structure for `dotp_permissions`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_permissions`;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_permissions
-- ----------------------------
INSERT INTO dotp_permissions VALUES ('1', '1', 'all', '-1', '-1');

-- ----------------------------
-- Table structure for `dotp_projects`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_projects`;
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
  `project_color_identifier` varchar(6) DEFAULT 'eeeeee',
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_projects
-- ----------------------------
INSERT INTO dotp_projects VALUES ('2', '2', '0', '0', 'Sistema de pizzaria online', 'Sistema de', '6', '', '', '2013-03-01 00:00:00', '2013-08-08 23:59:59', '2', '0', 'FFFFFF', 'Este projeto deve auxiliar a empresa no cumprimento dos seus objetivos estratÃ©gicos. Para ampliar o negÃ³cio, este projeto deve possibilitar que seus clientes, por meio da Internet, possam encomendar pizzas no site da pizzaria. Estas informaÃ§Ãµes serÃ£o processadas pelos atendentes. Este projeto inclui a realizaÃ§Ã£o de treinamento aos atendentes da pizzaria, visto que atualmente eles tÃªm pouco conhecimento em TI. \r\nUma parte deste projeto envolve adicionar suporte para encomendas de pizzas no site da pizzaria. O sistema deverÃ¡ possuir o cadastro dos clientes, para que possibilite que o cliente faÃ§a login no site, e utilize os dados cadastrados para determinar o local de entrega. O sistema deverÃ¡ possuir a funcionalidade de compra de pizza, em que serÃ£o descriminados os sabores, tamanho, e forma de pagamento. O sistema suportarÃ¡ pagamento por meio de cartÃµes de crÃ©dito e dÃ©bito.  Uma segunda parte do sistema Ã© um mÃ³dulo para dispositivos mÃ³veis, que serÃ¡ utilizada pelos entregadores para consultar detalhes dos dados referentes aos pedidos em aberto.', '30000.00', '30000.00', '1', '0', '0', '', '0', '0');
INSERT INTO dotp_projects VALUES ('5', '2', '0', '0', 'Projeto A', 'Projeto A', '1', null, null, '2013-05-12 00:00:00', null, '0', '0', 'FFFFFF', null, '0.00', '0.00', '1', '0', null, null, '0', '0');
INSERT INTO dotp_projects VALUES ('6', '2', '0', '0', 'Projeto B', 'Projeto B', '1', null, null, '2013-05-12 00:00:00', null, '0', '0', 'FFFFFF', null, '0.00', '0.00', '1', '0', null, null, '0', '0');

-- ----------------------------
-- Table structure for `dotp_project_contacts`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_project_contacts`;
CREATE TABLE `dotp_project_contacts` (
  `project_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_project_contacts
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_project_departments`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_project_departments`;
CREATE TABLE `dotp_project_departments` (
  `project_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_project_departments
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_project_eap_items`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_project_eap_items`;
CREATE TABLE `dotp_project_eap_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `item_name` text,
  `number` text,
  `is_leaf` text,
  `identation` text,
  PRIMARY KEY (`id`),
  KEY `fk_eap_item_project` (`project_id`),
  CONSTRAINT `fk_eap_item_project` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_project_eap_items
-- ----------------------------
INSERT INTO dotp_project_eap_items VALUES ('4', '2', '0', 'Pizzaria Online', '1', '0', '');
INSERT INTO dotp_project_eap_items VALUES ('20', '2', '1', 'Gerenciamento do projeto', '1.1', '0', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('21', '2', '2', 'Plano de gerenc. do projeto', '1.1.1', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('22', '2', '3', 'Plano de gerenciamento do escopo', '1.1.1.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('23', '2', '4', 'Plano de gerenciamento de requisitos', '1.1.1.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('24', '2', '5', 'Plano de gerenciamento do tempo', '1.1.1.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('25', '2', '6', 'Plano de gerenciamento de custos', '1.1.1.4', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('26', '2', '7', 'Plano de gerenciamento de RH', '1.1.1.5', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('27', '2', '8', 'Plano de gerenciamento de comunicaÃ§Ãµes', '1.1.1.6', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('28', '2', '9', 'Plano de gerenciamento de riscos', '1.1.1.7', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('29', '2', '10', 'Plano de gerenciamento de aquisiÃ§Ãµes', '1.1.1.8', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('30', '2', '11', 'Plano de gerenciamento de mudanÃ§as', '1.1.1.9', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('31', '2', '12', 'Monitoramento e controle', '1.1.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('32', '2', '13', 'ReuniÃµes de acompanhamento', '1.1.2.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('33', '2', '14', 'RelatÃ³rios de progresso', '1.1.2.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('34', '2', '15', 'Auditorias da qualidade do processo', '1.1.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('35', '2', '16', 'InspeÃ§Ã£o da qualidade do produto', '1.1.4', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('36', '2', '17', 'Core do sistema', '1.2', '0', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('37', '2', '18', 'Sistema de cadastro de clientes', '1.2.1', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('38', '2', '19', 'ImplementaÃ§Ã£o', '1.2.1.1', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('39', '2', '20', 'Banco de dados de clientes', '1.2.1.1.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('40', '2', '21', 'ServiÃ§os para manter o cadastro de clientes', '1.2.1.1.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('41', '2', '22', 'Testes', '1.2.1.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('42', '2', '23', 'Testes unitÃ¡rios', '1.2.1.2.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('43', '2', '24', 'Sistema de cadastro de pedidos', '1.2.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('44', '2', '25', 'ImplementaÃ§Ã£o', '1.2.2.1', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('45', '2', '26', 'Banco de dados de pedidos', '1.2.2.1.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('46', '2', '27', 'ServiÃ§os para manter o cadastro de pedidos', '1.2.2.1.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('47', '2', '28', 'Testes', '1.2.2.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('48', '2', '29', 'Testes unitÃ¡rios', '1.2.2.2.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('49', '2', '30', 'Interface Web', '1.3', '0', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('50', '2', '31', 'ProtÃ³tipos da interface Web', '1.3.1', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('51', '2', '32', 'ProtÃ³tipo da interface web para os fregueses (cadastro de clientes e pedidos)', '1.3.1.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('52', '2', '33', 'ProtÃ³tipo da interface web para operadores (relatÃ³rio dos pedidos)', '1.3.1.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('53', '2', '34', 'Interface Web (fregueses)', '1.3.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('54', '2', '35', 'CriaÃ§Ã£o da interface', '1.3.2.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('55', '2', '36', 'Testes', '1.3.2.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('56', '2', '37', 'Testes de integraÃ§Ã£o', '1.3.2.2.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('57', '2', '38', 'Testes de sistema', '1.3.2.2.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('58', '2', '39', 'Testes de aceite', '1.3.2.2.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('59', '2', '40', 'Interface Web (operaÃ§Ã£o)', '1.3.3', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('60', '2', '41', 'Interface grÃ¡fica para apresentaÃ§Ã£o dos relatÃ³rios', '1.3.3.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('61', '2', '42', 'Cliente para consumir os serviÃ§os de consulta de pedidos para o relatÃ³rio', '1.3.3.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('62', '2', '43', 'Testes', '1.3.3.3', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('63', '2', '44', 'Testes de integraÃ§Ã£o', '1.3.3.3.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('64', '2', '45', 'Testes de sistema', '1.3.3.3.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('65', '2', '46', 'Testes de aceite', '1.3.3.3.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('66', '2', '47', 'MÃ³dulo smartphone', '1.4', '0', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('67', '2', '48', 'ImplementaÃ§Ã£o', '1.4.1', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('68', '2', '49', 'Interface grÃ¡fica para consulta de pedidos', '1.4.1.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('69', '2', '50', 'Cliente para consumir os serviÃ§os de consulta de pedidos', '1.4.1.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('70', '2', '51', 'Testes', '1.4.2', '0', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('71', '2', '52', 'Testes de integraÃ§Ã£o', '1.4.2.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('72', '2', '53', 'Testes de sistema', '1.4.2.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('73', '2', '54', 'Testes de aceite', '1.4.2.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('74', '2', '55', 'ImplantaÃ§Ã£o', '1.5', '0', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('75', '2', '56', 'Sistema implantado em servidor produtivo', '1.5.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('76', '2', '57', 'Site da pizzaria atualizado', '1.5.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('77', '2', '58', 'Apps instalados nos smartphones utilizados pelos entregadores', '1.5.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('78', '2', '59', 'Encerramento', '1.6', '0', '&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('79', '2', '60', 'Encerramento do contrato', '1.6.1', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('80', '2', '61', 'LiÃ§Ãµes aprendidas', '1.6.2', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
INSERT INTO dotp_project_eap_items VALUES ('81', '2', '62', 'Encerramento do projeto', '1.6.3', '1', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

-- ----------------------------
-- Table structure for `dotp_project_minutes`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_project_minutes`;
CREATE TABLE `dotp_project_minutes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `minute_date` datetime DEFAULT NULL,
  `description` text,
  `isEffort` int(11) DEFAULT '0',
  `isDuration` int(11) DEFAULT '0',
  `isResource` int(11) DEFAULT '0',
  `isSize` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_minute_project` (`project_id`),
  CONSTRAINT `fk_minute_project` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_project_minutes
-- ----------------------------
INSERT INTO dotp_project_minutes VALUES ('2', '2', '2013-02-06 00:00:00', '<ul>\r\n<li>Estimada a dura&ccedil;&atilde;o e esfor&ccedil;o para&nbsp;elabora&ccedil;&atilde;o do plano do projeto.</li>\r\n<li>Foi atodata a t&eacute;cnica de estimativa an&aacute;loga, pois projetos simulares foram realizadas recentemente.</li>\r\n</ul>', '1', '1', '1', '1');
INSERT INTO dotp_project_minutes VALUES ('3', '2', '2013-02-18 00:00:00', '<ul>\r\n<li>Realizadas as estimativas para as atividades relacionadas ao desenvolvimento do m&oacute;dulo web.</li>\r\n<li>Utilizada t&eacute;cnica de opni&ccedil;&atilde;o especializada por meio do planning poker.</li>\r\n</ul>', '1', '1', '1', '1');
INSERT INTO dotp_project_minutes VALUES ('4', '2', '2013-02-14 00:00:00', '<ul>\r\n<li>Realizada estimativas para as atividades do m&oacute;dulo mobile.</li>\r\n<li>Utilizada t&eacute;cnica de opni&atilde;o especializada, wideband delphi, porque os membros da equipe n&atilde;o poderam estar presencialmente reunidos.</li>\r\n</ul>', '1', '1', '1', '1');

-- ----------------------------
-- Table structure for `dotp_project_tasks_estimated_roles`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_project_tasks_estimated_roles`;
CREATE TABLE `dotp_project_tasks_estimated_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) DEFAULT NULL,
  `role_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=641 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_project_tasks_estimated_roles
-- ----------------------------
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('535', '1', '1');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('536', '2', '2');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('537', '3', '2');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('538', '4', '1');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('539', '5', '3');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('540', '6', '1');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('541', '7', '1');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('542', '7', '3');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('543', '8', '2');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('544', '8', '3');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('546', '10', '6');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('547', '11', '5');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('548', '12', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('549', '13', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('550', '14', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('551', '15', '6');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('552', '16', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('553', '17', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('554', '18', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('555', '19', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('556', '20', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('557', '21', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('558', '22', '6');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('559', '23', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('560', '24', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('561', '25', '6');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('562', '26', '6');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('563', '27', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('564', '28', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('565', '29', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('566', '30', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('567', '30', '5');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('568', '30', '6');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('569', '30', '7');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('570', '31', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('571', '32', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('572', '33', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('573', '34', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('574', '35', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('575', '36', '4');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('636', '37', '8');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('637', '38', '8');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('638', '39', '8');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('639', '9', '5');
INSERT INTO dotp_project_tasks_estimated_roles VALUES ('640', '9', '5');

-- ----------------------------
-- Table structure for `dotp_project_tasks_estimations`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_project_tasks_estimations`;
CREATE TABLE `dotp_project_tasks_estimations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `effort` float DEFAULT NULL,
  `effort_unit` text,
  `duration` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_estimation_task_attributes` (`task_id`),
  CONSTRAINT `fk_estimation_task_attributes` FOREIGN KEY (`task_id`) REFERENCES `dotp_tasks` (`task_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_project_tasks_estimations
-- ----------------------------
INSERT INTO dotp_project_tasks_estimations VALUES ('9', '9', '11', '2', '11');
INSERT INTO dotp_project_tasks_estimations VALUES ('10', '10', '13', '2', '13');
INSERT INTO dotp_project_tasks_estimations VALUES ('11', '11', '10', '2', '10');
INSERT INTO dotp_project_tasks_estimations VALUES ('12', '12', '15', '2', '15');
INSERT INTO dotp_project_tasks_estimations VALUES ('13', '13', '4', '2', '4');
INSERT INTO dotp_project_tasks_estimations VALUES ('14', '14', '10', '2', '10');
INSERT INTO dotp_project_tasks_estimations VALUES ('15', '15', '10', '2', '10');
INSERT INTO dotp_project_tasks_estimations VALUES ('16', '16', '8', '2', '8');
INSERT INTO dotp_project_tasks_estimations VALUES ('17', '17', '16', '2', '16');
INSERT INTO dotp_project_tasks_estimations VALUES ('18', '18', '7', '2', '7');
INSERT INTO dotp_project_tasks_estimations VALUES ('19', '19', '31', '2', '31');
INSERT INTO dotp_project_tasks_estimations VALUES ('20', '20', '24', '2', '25');
INSERT INTO dotp_project_tasks_estimations VALUES ('21', '21', '3', '2', '4');
INSERT INTO dotp_project_tasks_estimations VALUES ('22', '22', '8', '2', '9');
INSERT INTO dotp_project_tasks_estimations VALUES ('23', '23', '32', '2', '28');
INSERT INTO dotp_project_tasks_estimations VALUES ('24', '24', '16', '2', '18');
INSERT INTO dotp_project_tasks_estimations VALUES ('25', '25', '5', '2', '8');
INSERT INTO dotp_project_tasks_estimations VALUES ('26', '26', '5', '2', '5');
INSERT INTO dotp_project_tasks_estimations VALUES ('27', '27', '14', '2', '14');
INSERT INTO dotp_project_tasks_estimations VALUES ('28', '28', '15', '2', '15');
INSERT INTO dotp_project_tasks_estimations VALUES ('29', '29', '11', '2', '11');
INSERT INTO dotp_project_tasks_estimations VALUES ('30', '30', '17', '2', '17');
INSERT INTO dotp_project_tasks_estimations VALUES ('31', '31', '3', '2', '3');
INSERT INTO dotp_project_tasks_estimations VALUES ('32', '32', '10', '2', '10');
INSERT INTO dotp_project_tasks_estimations VALUES ('33', '33', '6', '2', '5');
INSERT INTO dotp_project_tasks_estimations VALUES ('34', '34', '8', '2', '8');
INSERT INTO dotp_project_tasks_estimations VALUES ('35', '35', '6', '2', '7');
INSERT INTO dotp_project_tasks_estimations VALUES ('36', '36', '5', '2', '6');
INSERT INTO dotp_project_tasks_estimations VALUES ('37', '37', '3', '0', '4');
INSERT INTO dotp_project_tasks_estimations VALUES ('38', '38', '4', '2', '4');
INSERT INTO dotp_project_tasks_estimations VALUES ('39', '39', '5', '2', '5');

-- ----------------------------
-- Table structure for `dotp_quality_planning`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_quality_planning`;
CREATE TABLE `dotp_quality_planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `quality_controlling` text,
  `quality_assurance` text,
  `quality_policies` text,
  PRIMARY KEY (`id`),
  KEY `FK_PROJECT_ACQUISITION` (`project_id`),
  CONSTRAINT `FK_PROJECT_ACQUISITION` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_quality_planning
-- ----------------------------
INSERT INTO dotp_quality_planning VALUES ('2', '2', '<p><span style=\"font-size: x-small; font-family: arial,helvetica,sans-serif;\">\r\n<p class=\"MsoNormal\" style=\"text-align: justify; margin: 0cm 0cm 10pt; line-height: normal;\"><span style=\"font-size: small;\">O controle da qualidade dentro da organiza&ccedil;&atilde;o define que todos os componentes de software desenvolvidos devem seguir as etapas de testes previstas no &ldquo;modelo V&rdquo;: unidade, integra&ccedil;&atilde;o, sistema, e aceite. Os testes de unidade e de integra&ccedil;&atilde;o devem ser desenvolvidos pelo pr&oacute;prio programador durante as atividades de programa&ccedil;&atilde;o. Estes testes s&atilde;o realizados utilizando a ferramenta JUnit para projetos desenvolvidos na linguagem JAVA ou SimpleTest para projetos desenvolvidos na linguagem PHP. Os testes de sistema devem ser realizados pelo analista de teste, que ir&aacute; testar o sistema de acordo com a especifica&ccedil;&atilde;o definida no planejamento de escopo. Os testes de aceite s&atilde;o realizados pelo usu&aacute;rio final/cliente acompanhado pelo gerente de qualidade.</span></p>\r\n</span></p>\r\n<p class=\"MsoNormal\" style=\"text-align: justify; margin: 0cm 0cm 10pt; line-height: normal;\"><span style=\"font-size: small;\">A norma &ldquo;alimenta&ccedil;&atilde;o segura&rdquo;, uma vez ao ano (juntamente com a auditoria externa) realiza inspe&ccedil;&otilde;es nos produtos de software desenvolvidos. As inspe&ccedil;&otilde;es buscam verificar se as exig&ecirc;ncias de rastreabilidade entre clientes e alimentos adquiridos est&atilde;o sendo suportadas. As inspe&ccedil;&otilde;es tamb&eacute;m comparam os relat&oacute;rios de testes com o software produzido, a fim de verificar a consist&ecirc;ncia dos resultados documentados.</span></p>', '<p><span style=\"font-size: small;\">Os projetos desenvolvidos para o setor aliment&iacute;cio devem seguir os processos da norma &ldquo;alimenta&ccedil;&atilde;o segura&rdquo;. Estes processos definem que o projeto deve incluir etapas para modelagem dos dados, para o planejamento de testes, e que seja mantido o relat&oacute;rio da execu&ccedil;&atilde;o dos testes. O processo deve tratar as solicita&ccedil;&otilde;es de mudan&ccedil;a de escopo, mantendo o registro das mudan&ccedil;as solicitadas e do resultado da an&aacute;lise de impacto. Os registros das solicita&ccedil;&otilde;es de mudan&ccedil;a devem seguir o template &ldquo;alimentacao_segura_mudanca.docx\".</span></p>\r\n<p><span style=\"font-size: small;\">&nbsp;</span></p>\r\n<p><span style=\"font-size: small;\">Como abordagem da garantia da qualidade, a cada 3 meses &eacute; realizada uma auditoria interna para verificar se os processos est&atilde;o sendo seguidos. As auditorias internas s&atilde;o realizadas pelo gerente da qualidade. Durante as auditorias &eacute; realizada a revis&atilde;o estruturada dos resultados esperados. Os custos com as auditorias de qualidade s&atilde;o financiados pela pr&oacute;pria organiza&ccedil;&atilde;o, sendo dilu&iacute;dos pelo lucro dos demais projetos. Uma vez ao ano ocorre uma auditoria externa, por avaliadores oficiais da norma \"alimenta&ccedil;&atilde;o segura\", que elegem aleatoriamente dois projetos da organiza&ccedil;&atilde;o para serem auditados.</span></p>', '<p><span style=\"font-size: small;\">O projeto dever ser desenvolvido de acordo com a norma &ldquo;alimenta&ccedil;&atilde;o segura&rdquo;. Esta norma busca garantir que todos os alimentos vendidos por meio do sistema, possuam uma rela&ccedil;&atilde;o de rastreabilidade com o cliente que o adquiriu. A norma tamb&eacute;m exige que seja mantido o registro de data e hora de todas as compras. Para manter o selo da norma &ldquo;alimenta&ccedil;&atilde;o segura&rdquo; atrelado &agrave; organiza&ccedil;&atilde;o, anualmente s&atilde;o realizadas inspe&ccedil;&otilde;es formais sobre produtos desenvolvidos e auditorias sobre o processo utilizado durante o desenvolvimento.</span></p>\r\n<p><span style=\"font-size: small;\">&nbsp;</span></p>\r\n<p><span style=\"font-size: small;\">A pol&iacute;tica organizacional define que todos os projetos desenvolvidos para o setor aliment&iacute;cio devem seguir a norma \"alimenta&ccedil;&atilde;o segura\", e que todos os membros da equipe do projeto devem estar cientes que o projeto segue os padr&otilde;es estabelecidos pela norma.</span></p>');

-- ----------------------------
-- Table structure for `dotp_resources`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_resources`;
CREATE TABLE `dotp_resources` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) NOT NULL DEFAULT '',
  `resource_key` varchar(64) NOT NULL DEFAULT '',
  `resource_type` int(11) NOT NULL DEFAULT '0',
  `resource_note` text NOT NULL,
  `resource_max_allocation` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`resource_id`),
  KEY `resource_name` (`resource_name`),
  KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_resources
-- ----------------------------
INSERT INTO dotp_resources VALUES ('3', 'Meeting room', 'GTYJ', '0', '', '100');
INSERT INTO dotp_resources VALUES ('4', 'Projector HDMI', 'JUYTJ', '0', '', '100');
INSERT INTO dotp_resources VALUES ('5', 'Application server for tests', 'HTHK', '0', '', '100');
INSERT INTO dotp_resources VALUES ('6', 'Wireless internet (IEEE 802.11n)', 'HTGK', '0', '', '100');

-- ----------------------------
-- Table structure for `dotp_resource_tasks`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_resource_tasks`;
CREATE TABLE `dotp_resource_tasks` (
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `task_id` int(11) NOT NULL DEFAULT '0',
  `percent_allocated` int(11) NOT NULL DEFAULT '100',
  KEY `resource_id` (`resource_id`),
  KEY `task_id` (`task_id`,`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_resource_tasks
-- ----------------------------
INSERT INTO dotp_resource_tasks VALUES ('1', '1', '100');
INSERT INTO dotp_resource_tasks VALUES ('2', '1', '100');
INSERT INTO dotp_resource_tasks VALUES ('3', '27', '100');
INSERT INTO dotp_resource_tasks VALUES ('2', '27', '100');
INSERT INTO dotp_resource_tasks VALUES ('6', '13', '100');
INSERT INTO dotp_resource_tasks VALUES ('5', '13', '100');

-- ----------------------------
-- Table structure for `dotp_resource_types`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_resource_types`;
CREATE TABLE `dotp_resource_types` (
  `resource_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type_name` varchar(255) NOT NULL DEFAULT '',
  `resource_type_note` text,
  PRIMARY KEY (`resource_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_resource_types
-- ----------------------------
INSERT INTO dotp_resource_types VALUES ('1', 'Equipment', null);
INSERT INTO dotp_resource_types VALUES ('2', 'Tool', null);
INSERT INTO dotp_resource_types VALUES ('3', 'Venue', null);

-- ----------------------------
-- Table structure for `dotp_risks`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_risks`;
CREATE TABLE `dotp_risks` (
  `risk_id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_name` varchar(255) NOT NULL,
  `risk_responsible` int(11) NOT NULL,
  `risk_description` varchar(2000) DEFAULT NULL,
  `risk_probability` varchar(15) NOT NULL,
  `risk_impact` varchar(15) NOT NULL,
  `risk_answer_to_risk` varchar(2000) DEFAULT NULL,
  `risk_status` varchar(15) NOT NULL,
  `risk_project` int(11) DEFAULT NULL,
  `risk_task` int(11) DEFAULT NULL,
  `risk_notes` varchar(2000) DEFAULT NULL,
  `risk_potential_other_projects` varchar(2) NOT NULL,
  `risk_lessons_learned` varchar(2000) DEFAULT NULL,
  `risk_priority` varchar(15) NOT NULL,
  `risk_active` int(11) NOT NULL,
  `risk_strategy` int(11) NOT NULL,
  `risk_prevention_actions` varchar(2000) DEFAULT NULL,
  `risk_contingency_plan` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`risk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dotp_risks
-- ----------------------------
INSERT INTO dotp_risks VALUES ('3', 'Perda de do analista de testes', '6', 'O analista de testes estÃ¡ prestes a se mudar de cidade.', '3', '3', null, '0', '2', '0', null, '0', 'Buscar redundÃ¢ncias entre os recursos.', '2', '0', '2', 'Oferecer ao analista de testes uma bonificaÃ§Ã£o financeira ao tÃ©rmino do projeto, para que ele postergue a mudanÃ§a para depois do projeto.', 'Contratar um novo analista de testes.');
INSERT INTO dotp_risks VALUES ('4', 'NÃ£o atender as expectativas do cliente quanto ao aplicativo em dispositivo mÃ³vel.', '6', 'Devido a pouca experiÃªncia no uso de recursos avanÃ§ados para os dispositivos mÃ³veis, o resultado produzido pode nÃ£o atender as expectativas.', '3', '3', null, '0', '2', '0', null, '0', 'Buscar aperfeiÃ§oamento dos membros da equipe nas tecnologias adotadas nos projetos.', '2', '0', '2', 'Demonstrar protÃ³tipos o mais cedo possÃ­vel nos projeto evitar expectativas divergentes.', 'Terceirizar o desenvolvimento com uma organizaÃ§Ã£o mais experiente.');
INSERT INTO dotp_risks VALUES ('5', 'O cliente pode declarar falhÃªncia durante o projeto.', '6', 'Devido a crise financeira o cliente estÃ¡ com dificuldades de manter seu negÃ³cio. O investimento busca melhorar as vendas. Entretanto a parte final do pagamento serÃ¡ liberada apenas ao tÃ©rmino do projeto.', '2', '4', null, '0', '2', '0', '', '0', '', '2', '0', '0', '', 'Buscar novos clientes para vender o produto.');
INSERT INTO dotp_risks VALUES ('6', 'Problemas de disponibilidade do aplicativo servidor quando o dispositivo mÃ³vel estiver em campo.', '6', 'O serviÃ§o oferecido pelo aplicativo ao dispositivo mÃ³vel deve ter alta disponibilidade.', '2', '4', null, '0', '2', '0', '', '0', '', '2', '0', '3', 'SerÃ¡ delegado ao cliente a contrataÃ§Ã£o de um provedor de servidor de aplicativos para hospedagem do aplicativo. A disponibilidade do aplicativo serÃ¡ de responsabilidade deste provedor.', '');

-- ----------------------------
-- Table structure for `dotp_roles`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_roles`;
CREATE TABLE `dotp_roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(24) NOT NULL DEFAULT '',
  `role_description` varchar(255) NOT NULL DEFAULT '',
  `role_type` int(3) unsigned NOT NULL DEFAULT '0',
  `role_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_roles
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_scope_requirements`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_scope_requirements`;
CREATE TABLE `dotp_scope_requirements` (
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `req_idname` varchar(6) NOT NULL,
  `req_description` text NOT NULL,
  `req_source` varchar(60) NOT NULL,
  `req_owner` varchar(60) NOT NULL,
  `req_categ_prefix_id` varchar(3) NOT NULL,
  `req_priority_id` varchar(20) NOT NULL,
  `req_status_id` varchar(20) NOT NULL,
  `req_version` varchar(20) DEFAULT NULL,
  `req_inclusiondate` date NOT NULL,
  `req_conclusiondate` date DEFAULT NULL,
  `eapitem_id` int(11) DEFAULT NULL,
  `req_testcase` text,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`req_id`),
  KEY `req_categ_prefix_id` (`req_categ_prefix_id`),
  KEY `req_priority_id` (`req_priority_id`),
  KEY `req_status_id` (`req_status_id`),
  KEY `eapitem_id` (`eapitem_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `dotp_scope_requirements_ibfk_1` FOREIGN KEY (`req_categ_prefix_id`) REFERENCES `dotp_scope_requirement_categories` (`req_categ_prefix_id`),
  CONSTRAINT `dotp_scope_requirements_ibfk_2` FOREIGN KEY (`req_priority_id`) REFERENCES `dotp_scope_requirement_priorities` (`req_priority_id`),
  CONSTRAINT `dotp_scope_requirements_ibfk_3` FOREIGN KEY (`req_status_id`) REFERENCES `dotp_scope_requirement_status` (`req_status_id`),
  CONSTRAINT `dotp_scope_requirements_ibfk_4` FOREIGN KEY (`eapitem_id`) REFERENCES `dotp_project_eap_items` (`id`),
  CONSTRAINT `dotp_scope_requirements_ibfk_5` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_requirements
-- ----------------------------
INSERT INTO dotp_scope_requirements VALUES ('1', 'R01', 'ImplantaÃ§Ã£o de um novo serviÃ§o de encomendas pela internet', 'Termo de abertura do projeto', 'Patrocinador', 'RNF', 'High', 'Active', '1', '2013-02-10', null, '24', '', '2');
INSERT INTO dotp_scope_requirements VALUES ('2', 'R02', 'O valor total da implantaÃ§Ã£o nÃ£o deve ultrapassar R$ 15.000,00', 'Contrato', 'Patrocinador', 'RNF', 'High', 'Active', '1', '2013-02-15', null, null, null, '2');
INSERT INTO dotp_scope_requirements VALUES ('3', 'R03', 'O sistema deve ser colocado em operaÃ§Ã£o em no mÃ¡ximo trÃªs meses apÃ³s o inÃ­cio formal do projeto', 'Termo de abertura do projeto', 'Patrocinador', 'RNF', 'High', 'Active', '1', '2013-02-16', null, null, null, '2');
INSERT INTO dotp_scope_requirements VALUES ('4', 'R04', 'O sistema deve ter um mÃ³dulo para smartphones Android', 'Termo de abertura do projeto', 'Patrocinador', 'RNF', 'High', 'Active', '1', '2013-02-16', null, null, null, '2');
INSERT INTO dotp_scope_requirements VALUES ('5', 'R05', 'O sistema deve permitir o pagamento online com cartÃµes de crÃ©dito, cartÃµes de dÃ©bito e dÃ©bito em conta', 'Entrevista', 'Patrocinador', 'RF', 'Normal', 'Added', '1', '2013-03-20', null, null, null, '2');
INSERT INTO dotp_scope_requirements VALUES ('6', 'R06', 'O mÃ³dulo para smartphones deve ser desenvolvido para sistema Android 4.0', 'Entrevista', 'Gerente do projeto', 'RNF', 'Normal', 'Active', '1', '2013-02-20', null, null, null, '2');
INSERT INTO dotp_scope_requirements VALUES ('7', 'R07', 'O mÃ³dulo para smartphones deve permitir a consulta do endereÃ§o, do telefone do cliente e dos dados do pedido', 'Entrevista', 'Gerente do projeto', 'RF', 'Normal', 'Added', '1', '2013-02-20', null, null, null, '2');
INSERT INTO dotp_scope_requirements VALUES ('8', 'R08', 'Os atendentes da pizzaria deverÃ£o ser treinados para operar apropriadamente o sistema online', 'Entrevista', 'Gerente do projeto', 'RNF', 'Low', 'Added', '1', '2013-02-22', null, null, null, '2');

-- ----------------------------
-- Table structure for `dotp_scope_requirements_managplan`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_scope_requirements_managplan`;
CREATE TABLE `dotp_scope_requirements_managplan` (
  `req_managplan_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `req_managplan_collect_descr` text,
  `req_managplan_reqcategories` text,
  `req_managplan_reqprioritization` text,
  `req_managplan_trac_descr` text,
  `req_managplan_config_descr` text,
  `req_managplan_verif_descr` text,
  PRIMARY KEY (`req_managplan_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `dotp_scope_requirements_managplan_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_requirements_managplan
-- ----------------------------
INSERT INTO dotp_scope_requirements_managplan VALUES ('3', '2', 'Os requisitos serÃ£o coletados observando-se primeiramente o Termo de Abertura do Projeto e o contrato de prestaÃ§Ã£o de serviÃ§o entre as partes. SerÃ£o utilizadas tambÃ©m entrevistas formais e informais com os envolvidos no projeto e observaÃ§Ãµes sobre o ambiente de trabalho nos quesitos de registro e entrega de pedidos.', 'Os requisitos serÃ£o classificados nas categorias Funcional,  NÃ£o-Funcional e de SeguranÃ§a. \r\na) Categoria Funcional: requisitos que dizem respeito Ã s funÃ§Ãµes que deverÃ¡ ter o produto ao final do projeto.\r\nb) Categoria NÃ£o-funcional: requisitos que determinam caracterÃ­sticas gerais do produto e de sua operaÃ§Ã£o, ou que estÃ£o relacionados Ã  implantaÃ§Ã£o do mesmo.\r\nc) Categoria SeguranÃ§a: requisitos que dizem respeito Ã  seguranÃ§a mÃ­nima que o produto deve prover durante as transaÃ§Ãµes online.', 'Os requisitos serÃ£o priorizados na seguinte ordem: Requisitos de SeguranÃ§a, Requisitos Funcionais e Requisitos de NÃ£o-funcionais.', 'Os requisitos serÃ£o rastreados pelo nÃºmero do pacote de trabalho correspondente na EAP, e pelo seu respectivo caso de teste.', 'Qualquer alteraÃ§Ã£o nos requisitos deverÃ¡ ser solicitada pelo cliente ou pelo gerente do projeto mediante a sua respectiva justificativa. Requisitos sÃ³ poderÃ£o ser alterados, excluÃ­dos ou acrescentados apÃ³s a aprovaÃ§Ã£o de ambas as partes e apÃ³s a anÃ¡lise dos termos do contrato e da viabilidade do requisito.', 'A mediÃ§Ã£o dos requisitos serÃ¡ efetuada atravÃ©s de casos de teste para as funcionalidades previstas.');

-- ----------------------------
-- Table structure for `dotp_scope_requirement_categories`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_scope_requirement_categories`;
CREATE TABLE `dotp_scope_requirement_categories` (
  `req_categ_prefix_id` varchar(3) NOT NULL,
  `req_categ_description` text,
  `req_categ_name` varchar(20) NOT NULL,
  `req_categ_priority` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`req_categ_prefix_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_requirement_categories
-- ----------------------------
INSERT INTO dotp_scope_requirement_categories VALUES ('RF', null, 'Functional', null);
INSERT INTO dotp_scope_requirement_categories VALUES ('RNF', null, 'Non-functional', null);

-- ----------------------------
-- Table structure for `dotp_scope_requirement_priorities`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_scope_requirement_priorities`;
CREATE TABLE `dotp_scope_requirement_priorities` (
  `req_priority_id` varchar(20) NOT NULL,
  PRIMARY KEY (`req_priority_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_requirement_priorities
-- ----------------------------
INSERT INTO dotp_scope_requirement_priorities VALUES ('High');
INSERT INTO dotp_scope_requirement_priorities VALUES ('Low');
INSERT INTO dotp_scope_requirement_priorities VALUES ('Normal');

-- ----------------------------
-- Table structure for `dotp_scope_requirement_status`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_scope_requirement_status`;
CREATE TABLE `dotp_scope_requirement_status` (
  `req_status_id` varchar(20) NOT NULL,
  PRIMARY KEY (`req_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_requirement_status
-- ----------------------------
INSERT INTO dotp_scope_requirement_status VALUES ('Active');
INSERT INTO dotp_scope_requirement_status VALUES ('Added');
INSERT INTO dotp_scope_requirement_status VALUES ('Cancelled');
INSERT INTO dotp_scope_requirement_status VALUES ('Finished');
INSERT INTO dotp_scope_requirement_status VALUES ('Inactive');

-- ----------------------------
-- Table structure for `dotp_scope_statement`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_scope_statement`;
CREATE TABLE `dotp_scope_statement` (
  `scope_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `scope_goal` text,
  `scope_description` text,
  `scope_acceptancecriteria` text,
  `scope_deliverables` text,
  `scope_exclusions` text,
  `scope_constraints` text,
  `scope_assumptions` text,
  PRIMARY KEY (`scope_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `dotp_scope_statement_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `dotp_projects` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_statement
-- ----------------------------
INSERT INTO dotp_scope_statement VALUES ('2', '2', 'O objetivo do projeto Ã© desenvolver um sistema para uma pizzaria de modo a permitir o pedido online pelos fregueses, alÃ©m da atual opÃ§Ã£o de pedidos por telefone. O sistema deve ter um mÃ³dulo para smartphones Android que permita ao entregador a consulta dos dados do pedido como tipo e quantidade de produtos pedidos, o valor de cada item e o valor total do pedido, o endereÃ§o de entrega e telefone do freguÃªs.', 'O projeto inclui o desenvolvimento do sistema para acesso pela web a partir de um PC comum e de um mÃ³dulo que acessarÃ¡ o serviÃ§o web a partir de smartphones Android. O sistema serÃ¡ desenvolvido com a arquitetura cliente-servidor. O servidor serÃ¡ desenvolvido utilizando-se a linguagem Java e a tecnologia EJB 3.0. O cliente deverÃ¡ ter duas interfaces especÃ­ficas: uma que permita a operaÃ§Ã£o pelos funcionÃ¡rios da pizzaria e outra para acesso pelos fregueses da pizzaria. O mÃ³dulo cliente da aplicaÃ§Ã£o serÃ¡ desenvolvido utilizando-se a tecnologia JSF. O mÃ³dulo cliente para smartphone serÃ¡ desenvolvido para o sistema Android utilizando-se o framework Java prÃ³prio para Android. O sistema deverÃ¡ utilizar banco de dados MySQL para guardar os dados relativos aos clientes e aos pedidos. O sistema deverÃ¡ ser validado para o SO Windows e para o browser Mozilla Firefox\r\n\r\nDeverÃ¡ ser desenvolvido um mÃ³dulo cliente para a operaÃ§Ã£o pelos funcionÃ¡rios que permita visualizar os pedidos efetuados pelos clientes pela Web. Este mÃ³dulo deverÃ¡ permitir a identificaÃ§Ã£o dos pedidos entregues e dos pedidos em fila para entrega.\r\n\r\nDeverÃ¡ ser desenvolvido um mÃ³dulo cliente para os fregueses da pizzaria. Este mÃ³dulo deverÃ¡ exigir que o freguÃªs cadastre-se para que possa efetuar pedidos online. O cadastro deve exigir dados de identificaÃ§Ã£o do freguÃªs, endereÃ§o e dados para contato e o endereÃ§o de entrega. O sistema deverÃ¡ apresentar todas as opÃ§Ãµes de produtos da pizzaria. Para que o freguÃªs faÃ§a um pedido deverÃ¡ ser necessÃ¡rio informar quais produtos deseja, sua quantidade e o endereÃ§o de entrega. DeverÃ¡ ser possÃ­vel o pagamento atravÃ©s de cartÃ£o de crÃ©dito, cartÃ£o de dÃ©bito e dÃ©bito em conta. O pedido sÃ³ deverÃ¡ ser finalizado apÃ³s o pagamento do mesmo, quando entrarÃ¡ na fila de pedidos da pizzaria.\r\n\r\nDeve ser desenvolvido um cliente para smartphone Android para uso dos entregadores da pizzaria. Este cliente deverÃ¡ permitir o acesso aos detalhes do pedido (produtos, quantidades e valores), endereÃ§o de entrega e o telefone de contato do freguÃªs.', 'O sistema deverÃ¡ ser entregue em pleno funcionamento com todos os requisitos aprovados implementados.\r\nDeverÃ£o ser executados testes unitÃ¡rios para todas as funÃ§Ãµes do sistema.\r\nDeverÃ£o ser executados testes de verificaÃ§Ã£o e de validaÃ§Ã£o das interfaces Web.\r\nAs interfaces Web e para Android a serem desenvolvidas deverÃ£o seguir as premissas de usabilidade de software de modo a facilitar o uso e proporcionar uma boa experiÃªncia para o usuÃ¡rio.\r\nO sistema deverÃ¡ ser carregado e executar as aÃ§Ãµes de forma rÃ¡pida e fluida.\r\nTodas as entregas do projeto deverÃ£o ser aprovadas pelo gerente do projeto e pelo cliente, sÃ³ entÃ£o Ã© considerada finalizada a entrega.', 'As partes servidor e cliente (interface Web para operaÃ§Ã£o por funcionÃ¡rios e para fregueses) do sistema serÃ£o desenvolvidas paralelamente e de forma incremental. A parte do mÃ³dulo cliente para smartphones serÃ¡ desenvolvida apÃ³s o tÃ©rmino dos mÃ³dulos principais. O projeto serÃ¡ dividido nas seguintes entregas:\r\n1.	ProtÃ³tipos de interface Web para operaÃ§Ã£o e para fregueses;\r\n2.	Sistema de cadastro de fregueses (cliente e servidor);\r\n3.	Sistema de pedidos, controle e pagamentos (cliente e servidor);\r\n4.	Pizzaria virtual completa (interface Web para fregueses);\r\n5.	OperaÃ§Ã£o e controle de pedidos (interface Web para operaÃ§Ã£o);\r\n6.	MÃ³dulo para smartphones;', 'O projeto nÃ£o inclui a validaÃ§Ã£o do sistema em outros sistemas operacionais ou browsers, exceto o explicitamente citados neste documento.\r\nO sistema nÃ£o irÃ¡ incluir quaisquer outras formas de pagamento exceto cartÃ£o de crÃ©dito, cartÃ£o de dÃ©bito e dÃ©bito em conta.', 'O custo do projeto (sistema completo, implantaÃ§Ã£o efetiva e treinamento para operaÃ§Ã£o) estÃ¡ limitado a R$ 15.000,00.\r\nO sistema deverÃ¡ entrar em operaÃ§Ã£o apÃ³s trÃªs meses contados a partir da aprovaÃ§Ã£o formal do projeto.\r\nO sistema deverÃ¡ utilizar tecnologia Java e banco de dados sem custo de licenÃ§a.', 'Deve-se conhecer e entender o negÃ³cio do cliente, incluindo o processo de pedidos e de seu registro, e de controle de atendimento e entrega dos mesmos.');
INSERT INTO dotp_scope_statement VALUES ('4', '6', 'aaaaaaaaaaaaaaaaaa', 'bbbbbbbbbbbbbbbbbb', null, null, null, null, null);

-- ----------------------------
-- Table structure for `dotp_sessions`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_sessions`;
CREATE TABLE `dotp_sessions` (
  `session_id` varchar(60) NOT NULL DEFAULT '',
  `session_user` int(11) NOT NULL DEFAULT '0',
  `session_data` longblob,
  `session_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`session_id`),
  KEY `session_updated` (`session_updated`),
  KEY `session_created` (`session_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_sessions
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_syskeys`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_syskeys`;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_syskeys
-- ----------------------------
INSERT INTO dotp_syskeys VALUES ('1', 'SelectList', 'Enter values for list', '0', '\n', '|');
INSERT INTO dotp_syskeys VALUES ('2', 'CustomField', 'Serialized array in the following format:\r\n<KEY>|<SERIALIZED ARRAY>\r\n\r\nSerialized Array:\r\n[type] => text | checkbox | select | textarea | label\r\n[name] => <Field\'s name>\r\n[options] => <html capture options>\r\n[selects] => <options for select and checkbox>', '0', '\n', '|');
INSERT INTO dotp_syskeys VALUES ('3', 'ColorSelection', 'Hex color values for type=>color association.', '0', '\n', '|');

-- ----------------------------
-- Table structure for `dotp_sysvals`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_sysvals`;
CREATE TABLE `dotp_sysvals` (
  `sysval_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sysval_key_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sysval_title` varchar(48) NOT NULL DEFAULT '',
  `sysval_value` text NOT NULL,
  PRIMARY KEY (`sysval_id`),
  UNIQUE KEY `idx_sysval_title` (`sysval_title`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_sysvals
-- ----------------------------
INSERT INTO dotp_sysvals VALUES ('1', '1', 'ProjectStatus', '0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived');
INSERT INTO dotp_sysvals VALUES ('2', '1', 'CompanyType', '0|Not Applicable\n1|Client\n2|Vendor\n3|Supplier\n4|Consultant\n5|Government\n6|Internal');
INSERT INTO dotp_sysvals VALUES ('3', '1', 'TaskDurationType', '1|hours\n24|days');
INSERT INTO dotp_sysvals VALUES ('4', '1', 'EventType', '0|General\n1|Appointment\n2|Meeting\n3|All Day Event\n4|Anniversary\n5|Reminder');
INSERT INTO dotp_sysvals VALUES ('5', '1', 'TaskStatus', '0|Active\n-1|Inactive');
INSERT INTO dotp_sysvals VALUES ('6', '1', 'TaskType', '0|Unknown\n1|Administrative\n2|Operative');
INSERT INTO dotp_sysvals VALUES ('7', '1', 'ProjectType', '0|Unknown\n1|Administrative\n2|Operative');
INSERT INTO dotp_sysvals VALUES ('8', '3', 'ProjectColors', 'Web|FFE0AE\nEngineering|AEFFB2\nHelpDesk|FFFCAE\nSystem Administration|FFAEAE');
INSERT INTO dotp_sysvals VALUES ('9', '1', 'FileType', '0|Unknown\n1|Document\n2|Application');
INSERT INTO dotp_sysvals VALUES ('10', '1', 'TaskPriority', '-1|low\n0|normal\n1|high');
INSERT INTO dotp_sysvals VALUES ('11', '1', 'ProjectPriority', '-1|low\n0|normal\n1|high');
INSERT INTO dotp_sysvals VALUES ('12', '1', 'ProjectPriorityColor', '-1|#E5F7FF\n0|\n1|#FFDCB3');
INSERT INTO dotp_sysvals VALUES ('13', '1', 'TaskLogReference', '0|Not Defined\n1|Email\n2|Helpdesk\n3|Phone Call\n4|Fax');
INSERT INTO dotp_sysvals VALUES ('14', '1', 'TaskLogReferenceImage', '0| 1|./images/obj/email.gif 2|./modules/helpdesk/images/helpdesk.png 3|./images/obj/phone.gif 4|./images/icons/stock_print-16.png');
INSERT INTO dotp_sysvals VALUES ('15', '1', 'UserType', '0|Default User\r\n1|Administrator\r\n2|CEO\r\n3|Director\r\n4|Branch Manager\r\n5|Manager\r\n6|Supervisor\r\n7|Employee');
INSERT INTO dotp_sysvals VALUES ('16', '1', 'ProjectRequiredFields', 'f.project_name.value.length|<3\r\nf.project_color_identifier.value.length|<3\r\nf.project_company.options[f.project_company.selectedIndex].value|<1');
INSERT INTO dotp_sysvals VALUES ('17', '2', 'TicketNotify', '0|admin@example.com\n1|admin@example.com\n2|admin@example.com\r\n3|admin@example.com\r\n4|admin@example.com');
INSERT INTO dotp_sysvals VALUES ('18', '1', 'TicketPriority', '0|Low\n1|Normal\n2|High\n3|Highest\n4|911');
INSERT INTO dotp_sysvals VALUES ('19', '1', 'TicketStatus', '0|Open\n1|Closed\n2|Deleted');
INSERT INTO dotp_sysvals VALUES ('20', '1', 'RiskImpact', '0|LBL_SUPER_LOW_M\n1|LBL_LOW_M\n2|LBL_MEDIUM_M\n3|LBL_HIGH_M\n4|LBL_SUPER_HIGH_M');
INSERT INTO dotp_sysvals VALUES ('21', '1', 'RiskProbability', '0|LBL_SUPER_LOW_F\n1|LBL_LOW_F\n2|LBL_MEDIUM_F\n3|LBL_HIGH_F\n4|LBL_SUPER_HIGH_F');
INSERT INTO dotp_sysvals VALUES ('22', '1', 'RiskStatus', '0|LBL_OPEN\n1|LBL_CLOSED\n2|LBL_NOT_APLICABLE');
INSERT INTO dotp_sysvals VALUES ('23', '1', 'RiskPotential', '0|LBL_NO\n1|LBL_YES');
INSERT INTO dotp_sysvals VALUES ('24', '1', 'RiskPriority', '0|LBL_LOW_F\n1|LBL_MEDIUM_F\n2|LBL_HIGH_F');
INSERT INTO dotp_sysvals VALUES ('25', '1', 'RiskActive', '0|LBL_YES\n1|LBL_NO');
INSERT INTO dotp_sysvals VALUES ('26', '1', 'RiskStrategy', '0|LBL_ACCEPT\n1|LBL_ELIMINATE\n2|LBL_MITIGATE\n3|LBL_TRANSFER');

-- ----------------------------
-- Table structure for `dotp_tasks`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_tasks`;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_tasks
-- ----------------------------
INSERT INTO dotp_tasks VALUES ('9', 'Realizar o design grÃ¡fico da tela de realizaÃ§Ã£o de pedidos.', '9', '0', '2', '0', '2013-06-11 00:00:00', '11', '24', '0', '2013-06-21 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('10', 'Realizar a anÃ¡lise da usabilidade da tela de realizaÃ§Ã£o de pedidos.', '10', '0', '2', '0', '2013-07-15 00:00:00', '13', '24', '0', '2013-07-27 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('11', 'Criar schema de banco de dados para armazenar os pedidos.', '11', '0', '2', '0', '2013-06-01 00:00:00', '10', '24', '0', '2013-06-10 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('12', 'Programar funcionalidade de armazenar as requisiÃ§Ãµes.', '12', '0', '2', '0', '2013-07-01 00:00:00', '15', '24', '0', '2013-07-15 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('13', 'Realizar teste de sistema sobre o cadastro de pedidos.', '13', '0', '2', '3', '2013-08-02 00:00:00', '4', '24', '0', '2013-08-05 00:00:00', '0', '0', '0', '', '0.00', '', '1', '0', '0', '0', '0', '0', '', '', null, '0');
INSERT INTO dotp_tasks VALUES ('14', 'Programar tela de cadastro de cliente e seu design grÃ¡fico.', '14', '0', '2', '0', '2014-05-16 00:00:00', '10', '24', '0', '2014-05-25 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('15', 'Realizar anÃ¡lise e ajustes de usabilidade na tela de cadastro de cliente.', '15', '0', '2', '0', '2014-05-06 00:00:00', '10', '24', '0', '2014-05-15 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('16', 'Configurar o esquema de banco de dados para armazenar os clientes.', '16', '0', '2', '0', '2014-04-28 00:00:00', '8', '24', '0', '2014-05-05 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('17', 'Programar funcionalidade de armazenar os clientes.', '17', '0', '2', '0', '2014-04-10 00:00:00', '16', '24', '0', '2014-04-25 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('18', 'Realizar teste de sistema sobre o cadastro de clientes.', '18', '0', '2', '0', '2014-06-01 00:00:00', '7', '24', '0', '2014-06-07 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('19', 'Programar a funcionalidade de consultar pedidos no banco de dados.', '19', '0', '2', '0', '2013-10-20 00:00:00', '31', '24', '0', '2013-11-19 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('20', 'Programar a tela de consulta de pedidos.', '20', '0', '2', '0', '2013-09-15 00:00:00', '25', '24', '0', '2013-10-09 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('21', 'Integrar a tela de consulta com o banco de dados.', '21', '0', '2', '0', '2013-10-10 00:00:00', '4', '24', '0', '2013-10-13 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('22', 'Realizar teste de sistema sobre a consulta de pedidos.', '22', '0', '2', '0', '2013-11-20 00:00:00', '9', '24', '0', '2013-11-28 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('23', 'Programar tela de listagem de pedidos em aberto.', '23', '0', '2', '0', '2014-02-01 00:00:00', '28', '24', '0', '2014-02-28 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('24', 'Integrar tela com a funcionalidade de consultar os pedidos no banco de dados.', '24', '0', '2', '0', '2014-03-01 00:00:00', '18', '24', '0', '2014-03-18 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('25', 'Realizar teste de sistema sobre a consulta de pedidos em aberto.', '25', '0', '2', '0', '2014-04-01 00:00:00', '8', '24', '0', '2014-04-08 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('26', 'Realizar teste de aceite sobre a consulta de pedidos em aberto.', '26', '0', '2', '0', '2014-04-06 00:00:00', '5', '24', '0', '2014-04-10 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('27', 'Idenficar os stakeholders do projeto.', '27', '0', '2', '3', '2013-02-16 00:00:00', '14', '24', '0', '2013-03-01 00:00:00', '0', '0', '0', '', '0.00', '', '1', '0', '0', '0', '0', '0', '', '', null, '0');
INSERT INTO dotp_tasks VALUES ('28', 'Elaborar o termo de abertura do projeto.', '28', '0', '2', '0', '2013-02-01 00:00:00', '15', '24', '0', '2013-02-15 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('29', 'Elaborar o plano de escopo', '29', '0', '2', '0', '2013-03-05 00:00:00', '11', '24', '0', '2013-03-15 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('30', 'Elaborar o plano de tempo', '30', '0', '2', '0', '2013-03-16 00:00:00', '17', '24', '0', '2013-04-01 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('31', 'Elaborar o plano de custos', '31', '0', '2', '0', '2013-04-06 00:00:00', '3', '24', '0', '2013-04-08 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('32', 'Elaborar o plano de riscos', '32', '0', '2', '0', '2013-04-09 00:00:00', '10', '24', '0', '2013-04-18 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('33', 'Elaborar o plano de recursos humanos', '33', '0', '2', '0', '2013-04-01 00:00:00', '5', '24', '0', '2013-04-05 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('34', 'Elaborar o plano de qualidade', '34', '0', '2', '0', '2013-05-01 00:00:00', '8', '24', '0', '2013-05-08 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('35', 'Elaborar o plano de comunicaÃ§Ã£o', '35', '0', '2', '0', '2013-05-09 00:00:00', '7', '24', '0', '2013-05-15 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('36', 'Elaborar o plano de aquisiÃ§Ãµes', '36', '0', '2', '7', '2013-05-16 00:00:00', '6', '24', '0', '2013-05-21 00:00:00', '0', '0', '0', '', '0.00', '', '1', '0', '0', '0', '0', '0', '', '', null, '0');
INSERT INTO dotp_tasks VALUES ('37', 'Solicitar teste de aceite sobre o cadastro de pedidos.', '37', '0', '2', '0', '2013-08-05 00:00:00', '4', '24', '0', '2013-08-08 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('38', 'Solicitar teste de aceite sobre o cadastro de clientes.', '38', '0', '2', '0', '2014-06-07 00:00:00', '4', '24', '0', '2014-06-10 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');
INSERT INTO dotp_tasks VALUES ('39', 'Realizar teste de aceite sobre a consulta de pedidos.', '39', '0', '2', '0', '2013-12-01 00:00:00', '5', '24', '0', '2013-12-05 00:00:00', '0', '0', '0', null, '0.00', '', '1', '0', '0', '0', '0', '0', null, null, null, '0');

-- ----------------------------
-- Table structure for `dotp_tasks_mdp`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_tasks_mdp`;
CREATE TABLE `dotp_tasks_mdp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `pos_x` int(11) DEFAULT NULL,
  `pos_y` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mdp_task` (`task_id`),
  CONSTRAINT `fk_mdp_task` FOREIGN KEY (`task_id`) REFERENCES `dotp_tasks` (`task_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_tasks_mdp
-- ----------------------------
INSERT INTO dotp_tasks_mdp VALUES ('9', '9', '37', '1019');
INSERT INTO dotp_tasks_mdp VALUES ('10', '10', '52', '718');
INSERT INTO dotp_tasks_mdp VALUES ('11', '11', '550', '1020');
INSERT INTO dotp_tasks_mdp VALUES ('12', '12', '42', '634');
INSERT INTO dotp_tasks_mdp VALUES ('13', '13', '51', '796');
INSERT INTO dotp_tasks_mdp VALUES ('14', '14', '602', '851');
INSERT INTO dotp_tasks_mdp VALUES ('15', '15', '602', '779');
INSERT INTO dotp_tasks_mdp VALUES ('16', '16', '599', '696');
INSERT INTO dotp_tasks_mdp VALUES ('17', '17', '608', '626');
INSERT INTO dotp_tasks_mdp VALUES ('18', '18', '605', '916');
INSERT INTO dotp_tasks_mdp VALUES ('19', '19', '218', '778');
INSERT INTO dotp_tasks_mdp VALUES ('20', '20', '200', '644');
INSERT INTO dotp_tasks_mdp VALUES ('21', '21', '199', '714');
INSERT INTO dotp_tasks_mdp VALUES ('22', '22', '211', '865');
INSERT INTO dotp_tasks_mdp VALUES ('23', '23', '386', '630');
INSERT INTO dotp_tasks_mdp VALUES ('24', '24', '404', '730');
INSERT INTO dotp_tasks_mdp VALUES ('25', '25', '391', '811');
INSERT INTO dotp_tasks_mdp VALUES ('26', '26', '407', '900');
INSERT INTO dotp_tasks_mdp VALUES ('27', '27', '211', '578');
INSERT INTO dotp_tasks_mdp VALUES ('28', '28', '27', '577');
INSERT INTO dotp_tasks_mdp VALUES ('29', '29', '938', '584');
INSERT INTO dotp_tasks_mdp VALUES ('30', '30', '1158', '584');
INSERT INTO dotp_tasks_mdp VALUES ('31', '31', '1160', '737');
INSERT INTO dotp_tasks_mdp VALUES ('32', '32', '1157', '804');
INSERT INTO dotp_tasks_mdp VALUES ('33', '33', '1158', '660');
INSERT INTO dotp_tasks_mdp VALUES ('34', '34', '1147', '880');
INSERT INTO dotp_tasks_mdp VALUES ('35', '35', '1133', '962');
INSERT INTO dotp_tasks_mdp VALUES ('36', '36', '1114', '1024');
INSERT INTO dotp_tasks_mdp VALUES ('37', '37', '38', '903');
INSERT INTO dotp_tasks_mdp VALUES ('38', '38', '716', '966');
INSERT INTO dotp_tasks_mdp VALUES ('39', '39', '202', '926');

-- ----------------------------
-- Table structure for `dotp_tasks_workpackages`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_tasks_workpackages`;
CREATE TABLE `dotp_tasks_workpackages` (
  `task_id` int(11) NOT NULL DEFAULT '0',
  `eap_item_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  CONSTRAINT `fk_task_eap_item` FOREIGN KEY (`task_id`) REFERENCES `dotp_tasks` (`task_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_tasks_workpackages
-- ----------------------------
INSERT INTO dotp_tasks_workpackages VALUES ('9', '6');
INSERT INTO dotp_tasks_workpackages VALUES ('10', '6');
INSERT INTO dotp_tasks_workpackages VALUES ('11', '6');
INSERT INTO dotp_tasks_workpackages VALUES ('12', '6');
INSERT INTO dotp_tasks_workpackages VALUES ('13', '6');
INSERT INTO dotp_tasks_workpackages VALUES ('14', '7');
INSERT INTO dotp_tasks_workpackages VALUES ('15', '7');
INSERT INTO dotp_tasks_workpackages VALUES ('16', '7');
INSERT INTO dotp_tasks_workpackages VALUES ('17', '7');
INSERT INTO dotp_tasks_workpackages VALUES ('18', '7');
INSERT INTO dotp_tasks_workpackages VALUES ('19', '8');
INSERT INTO dotp_tasks_workpackages VALUES ('20', '8');
INSERT INTO dotp_tasks_workpackages VALUES ('21', '8');
INSERT INTO dotp_tasks_workpackages VALUES ('22', '8');
INSERT INTO dotp_tasks_workpackages VALUES ('23', '10');
INSERT INTO dotp_tasks_workpackages VALUES ('24', '10');
INSERT INTO dotp_tasks_workpackages VALUES ('25', '10');
INSERT INTO dotp_tasks_workpackages VALUES ('26', '10');
INSERT INTO dotp_tasks_workpackages VALUES ('27', '12');
INSERT INTO dotp_tasks_workpackages VALUES ('28', '12');
INSERT INTO dotp_tasks_workpackages VALUES ('29', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('30', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('31', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('32', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('33', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('34', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('35', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('36', '13');
INSERT INTO dotp_tasks_workpackages VALUES ('37', '6');
INSERT INTO dotp_tasks_workpackages VALUES ('38', '7');
INSERT INTO dotp_tasks_workpackages VALUES ('39', '8');

-- ----------------------------
-- Table structure for `dotp_task_contacts`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_task_contacts`;
CREATE TABLE `dotp_task_contacts` (
  `task_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL,
  KEY `idx_task_contacts` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_task_contacts
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_task_departments`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_task_departments`;
CREATE TABLE `dotp_task_departments` (
  `task_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL,
  KEY `idx_task_departments` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_task_departments
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_task_dependencies`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_task_dependencies`;
CREATE TABLE `dotp_task_dependencies` (
  `dependencies_task_id` int(11) NOT NULL,
  `dependencies_req_task_id` int(11) NOT NULL,
  PRIMARY KEY (`dependencies_task_id`,`dependencies_req_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_task_dependencies
-- ----------------------------
INSERT INTO dotp_task_dependencies VALUES ('9', '11');
INSERT INTO dotp_task_dependencies VALUES ('10', '12');
INSERT INTO dotp_task_dependencies VALUES ('11', '36');
INSERT INTO dotp_task_dependencies VALUES ('12', '9');
INSERT INTO dotp_task_dependencies VALUES ('13', '10');
INSERT INTO dotp_task_dependencies VALUES ('14', '15');
INSERT INTO dotp_task_dependencies VALUES ('15', '16');
INSERT INTO dotp_task_dependencies VALUES ('16', '17');
INSERT INTO dotp_task_dependencies VALUES ('17', '26');
INSERT INTO dotp_task_dependencies VALUES ('18', '14');
INSERT INTO dotp_task_dependencies VALUES ('19', '21');
INSERT INTO dotp_task_dependencies VALUES ('20', '37');
INSERT INTO dotp_task_dependencies VALUES ('21', '20');
INSERT INTO dotp_task_dependencies VALUES ('22', '19');
INSERT INTO dotp_task_dependencies VALUES ('23', '39');
INSERT INTO dotp_task_dependencies VALUES ('24', '23');
INSERT INTO dotp_task_dependencies VALUES ('25', '24');
INSERT INTO dotp_task_dependencies VALUES ('26', '25');
INSERT INTO dotp_task_dependencies VALUES ('27', '28');
INSERT INTO dotp_task_dependencies VALUES ('29', '27');
INSERT INTO dotp_task_dependencies VALUES ('30', '29');
INSERT INTO dotp_task_dependencies VALUES ('31', '33');
INSERT INTO dotp_task_dependencies VALUES ('32', '31');
INSERT INTO dotp_task_dependencies VALUES ('33', '30');
INSERT INTO dotp_task_dependencies VALUES ('34', '32');
INSERT INTO dotp_task_dependencies VALUES ('35', '34');
INSERT INTO dotp_task_dependencies VALUES ('36', '35');
INSERT INTO dotp_task_dependencies VALUES ('37', '13');
INSERT INTO dotp_task_dependencies VALUES ('38', '18');
INSERT INTO dotp_task_dependencies VALUES ('39', '22');

-- ----------------------------
-- Table structure for `dotp_task_log`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_task_log`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_task_log
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_task_minute_members`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_task_minute_members`;
CREATE TABLE `dotp_task_minute_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `task_minute_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_minute_partipant_task` (`task_minute_id`),
  KEY `fk_task_minute_partipant_user` (`user_id`),
  CONSTRAINT `fk_task_minute_partipant_task` FOREIGN KEY (`task_minute_id`) REFERENCES `dotp_project_minutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_minute_partipant_user` FOREIGN KEY (`user_id`) REFERENCES `dotp_users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_task_minute_members
-- ----------------------------
INSERT INTO dotp_task_minute_members VALUES ('13', '6', '2');
INSERT INTO dotp_task_minute_members VALUES ('14', '4', '2');
INSERT INTO dotp_task_minute_members VALUES ('15', '5', '2');
INSERT INTO dotp_task_minute_members VALUES ('16', '5', '3');
INSERT INTO dotp_task_minute_members VALUES ('17', '4', '3');
INSERT INTO dotp_task_minute_members VALUES ('18', '6', '3');
INSERT INTO dotp_task_minute_members VALUES ('19', '5', '4');
INSERT INTO dotp_task_minute_members VALUES ('20', '6', '4');
INSERT INTO dotp_task_minute_members VALUES ('21', '4', '4');

-- ----------------------------
-- Table structure for `dotp_tickets`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_tickets`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_tickets
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_users`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_users`;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_users
-- ----------------------------
INSERT INTO dotp_users VALUES ('1', '1', 'admin', '76a2173be6393254e72ffa4d6df1030a', '0', '1', '0', '0', '0', '');
INSERT INTO dotp_users VALUES ('4', '5', 'Person A', '81dc9bdb52d04dc20036dbd8313ed055', '0', '0', '0', '0', '0', null);
INSERT INTO dotp_users VALUES ('5', '6', 'Person B', '81dc9bdb52d04dc20036dbd8313ed055', '0', '0', '0', '0', '0', null);
INSERT INTO dotp_users VALUES ('6', '7', 'Person C', '81dc9bdb52d04dc20036dbd8313ed055', '0', '0', '0', '0', '0', null);
INSERT INTO dotp_users VALUES ('7', '9', 'Person D', '81dc9bdb52d04dc20036dbd8313ed055', '0', '4', '0', '0', '0', null);
INSERT INTO dotp_users VALUES ('8', '10', 'Person E', '81dc9bdb52d04dc20036dbd8313ed055', '0', '0', '0', '0', '0', null);

-- ----------------------------
-- Table structure for `dotp_user_access_log`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_user_access_log`;
CREATE TABLE `dotp_user_access_log` (
  `user_access_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_ip` varchar(15) NOT NULL,
  `date_time_in` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_out` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_last_action` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_access_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=599 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_user_access_log
-- ----------------------------
INSERT INTO dotp_user_access_log VALUES ('1', '1', '127.0.0.1', '2013-01-08 23:48:29', '2013-01-09 02:59:34', '2013-01-09 02:59:26');
INSERT INTO dotp_user_access_log VALUES ('2', '1', '127.0.0.1', '2013-01-08 23:59:40', '2013-01-14 01:35:21', '2013-01-09 03:02:45');
INSERT INTO dotp_user_access_log VALUES ('3', '1', '127.0.0.1', '2013-01-09 19:59:58', '2013-01-14 00:46:40', '2013-01-11 20:25:43');
INSERT INTO dotp_user_access_log VALUES ('4', '1', '127.0.0.1', '2013-01-13 21:46:52', '2013-01-14 01:34:46', '2013-01-14 01:27:08');
INSERT INTO dotp_user_access_log VALUES ('5', '1', '127.0.0.1', '2013-01-13 22:34:54', '2013-01-17 20:07:27', '2013-01-17 20:07:23');
INSERT INTO dotp_user_access_log VALUES ('6', '1', '127.0.0.1', '2013-01-17 17:07:36', '2013-01-17 20:09:19', '2013-01-17 20:09:14');
INSERT INTO dotp_user_access_log VALUES ('7', '1', '127.0.0.1', '2013-01-17 17:09:26', '2013-01-21 19:12:15', '2013-01-18 13:42:31');
INSERT INTO dotp_user_access_log VALUES ('8', '1', '127.0.0.1', '2013-01-21 16:12:47', '2013-01-21 20:57:22', '2013-01-21 20:57:01');
INSERT INTO dotp_user_access_log VALUES ('9', '1', '127.0.0.1', '2013-01-21 17:57:27', '2013-01-21 21:08:32', '2013-01-21 21:08:24');
INSERT INTO dotp_user_access_log VALUES ('10', '1', '127.0.0.1', '2013-01-21 18:10:11', '2013-01-21 21:12:30', '2013-01-21 21:10:36');
INSERT INTO dotp_user_access_log VALUES ('11', '1', '127.0.0.1', '2013-01-21 18:12:39', '2013-01-26 22:31:35', '2013-01-23 19:02:03');
INSERT INTO dotp_user_access_log VALUES ('12', '1', '127.0.0.1', '2013-01-26 19:31:59', '2013-01-26 22:40:55', '2013-01-26 22:40:35');
INSERT INTO dotp_user_access_log VALUES ('13', '1', '127.0.0.1', '2013-01-26 19:41:02', '2013-01-28 01:19:00', '2013-01-28 01:18:54');
INSERT INTO dotp_user_access_log VALUES ('14', '1', '127.0.0.1', '2013-01-27 22:19:08', '2013-01-28 01:22:17', '2013-01-28 01:22:12');
INSERT INTO dotp_user_access_log VALUES ('15', '1', '127.0.0.1', '2013-01-27 22:22:24', '2013-01-28 01:30:20', '2013-01-28 01:30:16');
INSERT INTO dotp_user_access_log VALUES ('16', '1', '127.0.0.1', '2013-01-27 22:30:28', '2013-01-28 01:36:49', '2013-01-28 01:36:40');
INSERT INTO dotp_user_access_log VALUES ('17', '1', '127.0.0.1', '2013-01-27 22:36:56', '2013-01-28 18:52:03', '2013-01-28 18:50:55');
INSERT INTO dotp_user_access_log VALUES ('18', '1', '127.0.0.1', '2013-01-28 15:52:13', '2013-01-28 18:55:35', '2013-01-28 18:52:42');
INSERT INTO dotp_user_access_log VALUES ('19', '1', '127.0.0.1', '2013-01-28 15:55:47', '2013-01-28 18:57:55', '2013-01-28 18:57:53');
INSERT INTO dotp_user_access_log VALUES ('20', '1', '127.0.0.1', '2013-01-28 15:58:02', '2013-01-28 18:58:46', '2013-01-28 18:58:43');
INSERT INTO dotp_user_access_log VALUES ('21', '1', '127.0.0.1', '2013-01-28 15:58:53', '2013-01-28 18:59:59', '2013-01-28 18:59:56');
INSERT INTO dotp_user_access_log VALUES ('22', '1', '127.0.0.1', '2013-01-28 16:00:09', '2013-01-29 14:37:56', '2013-01-29 14:37:48');
INSERT INTO dotp_user_access_log VALUES ('23', '1', '127.0.0.1', '2013-01-29 11:38:03', '2013-01-29 14:45:36', '2013-01-29 14:45:18');
INSERT INTO dotp_user_access_log VALUES ('24', '1', '127.0.0.1', '2013-01-29 11:45:43', '2013-02-02 18:44:41', '2013-01-29 14:46:32');
INSERT INTO dotp_user_access_log VALUES ('25', '1', '127.0.0.1', '2013-02-02 15:44:48', '2013-02-14 19:16:13', '2013-02-06 20:28:13');
INSERT INTO dotp_user_access_log VALUES ('26', '1', '127.0.0.1', '2013-02-13 09:36:50', '2013-02-17 19:27:18', '2013-02-15 20:35:24');
INSERT INTO dotp_user_access_log VALUES ('27', '1', '127.0.0.1', '2013-02-17 15:27:57', '2013-02-19 17:58:36', '2013-02-19 17:58:28');
INSERT INTO dotp_user_access_log VALUES ('28', '1', '127.0.0.1', '2013-02-19 13:58:41', '2013-02-19 18:19:20', '2013-02-19 18:19:07');
INSERT INTO dotp_user_access_log VALUES ('29', '1', '127.0.0.1', '2013-02-19 14:19:28', '2013-02-20 17:34:47', '2013-02-20 17:34:40');
INSERT INTO dotp_user_access_log VALUES ('30', '1', '127.0.0.1', '2013-02-20 13:34:55', '2013-02-23 19:19:08', '2013-02-20 18:51:21');
INSERT INTO dotp_user_access_log VALUES ('31', '1', '127.0.0.1', '2013-02-23 15:20:24', '2013-02-27 00:24:22', '2013-02-27 00:24:18');
INSERT INTO dotp_user_access_log VALUES ('32', '1', '127.0.0.1', '2013-02-26 20:24:28', '2013-02-28 20:05:04', '2013-02-28 20:04:59');
INSERT INTO dotp_user_access_log VALUES ('33', '1', '127.0.0.1', '2013-02-28 16:10:30', '2013-03-03 18:08:13', '2013-03-03 18:08:07');
INSERT INTO dotp_user_access_log VALUES ('34', '1', '127.0.0.1', '2013-03-03 14:08:23', '2013-03-03 18:18:46', '2013-03-03 18:18:42');
INSERT INTO dotp_user_access_log VALUES ('35', '1', '127.0.0.1', '2013-03-03 14:18:56', '2013-03-03 18:21:18', '2013-03-03 18:21:06');
INSERT INTO dotp_user_access_log VALUES ('36', '1', '127.0.0.1', '2013-03-03 14:21:36', '2013-03-11 22:22:39', '2013-03-07 23:25:15');
INSERT INTO dotp_user_access_log VALUES ('37', '1', '127.0.0.1', '2013-03-11 18:22:50', '2013-03-13 13:51:44', '2013-03-13 13:51:41');
INSERT INTO dotp_user_access_log VALUES ('38', '1', '127.0.0.1', '2013-03-13 09:51:52', '2013-03-13 14:49:14', '2013-03-13 14:48:13');
INSERT INTO dotp_user_access_log VALUES ('39', '1', '127.0.0.1', '2013-03-13 10:49:21', '2013-03-13 14:53:17', '2013-03-13 14:53:12');
INSERT INTO dotp_user_access_log VALUES ('40', '7', '127.0.0.1', '2013-03-13 10:53:23', '2013-03-13 21:54:39', '2013-03-13 21:53:40');
INSERT INTO dotp_user_access_log VALUES ('41', '1', '127.0.0.1', '2013-03-13 17:54:55', '2013-03-18 19:47:54', '2013-03-15 01:56:29');
INSERT INTO dotp_user_access_log VALUES ('42', '1', '127.0.0.1', '2013-03-15 11:14:04', '0000-00-00 00:00:00', '2013-03-15 15:14:10');
INSERT INTO dotp_user_access_log VALUES ('43', '1', '127.0.0.1', '2013-03-15 11:14:10', '2013-03-15 18:40:07', '2013-03-15 18:40:03');
INSERT INTO dotp_user_access_log VALUES ('44', '1', '127.0.0.1', '2013-03-15 14:40:14', '2013-03-16 21:31:24', '2013-03-16 21:30:56');
INSERT INTO dotp_user_access_log VALUES ('45', '1', '127.0.0.1', '2013-03-16 17:31:29', '2013-03-16 21:57:15', '2013-03-16 21:57:08');
INSERT INTO dotp_user_access_log VALUES ('46', '1', '127.0.0.1', '2013-03-16 17:57:21', '2013-03-16 22:06:22', '2013-03-16 22:02:10');
INSERT INTO dotp_user_access_log VALUES ('47', '1', '127.0.0.1', '2013-03-16 18:06:29', '2013-03-16 22:12:04', '2013-03-16 22:08:16');
INSERT INTO dotp_user_access_log VALUES ('48', '1', '127.0.0.1', '2013-03-16 18:12:12', '2013-03-16 22:19:15', '2013-03-16 22:19:05');
INSERT INTO dotp_user_access_log VALUES ('49', '1', '127.0.0.1', '2013-03-16 18:21:48', '2013-03-16 22:22:55', '2013-03-16 22:22:51');
INSERT INTO dotp_user_access_log VALUES ('50', '1', '127.0.0.1', '2013-03-16 18:23:06', '2013-03-22 21:41:41', '2013-03-20 21:08:50');
INSERT INTO dotp_user_access_log VALUES ('51', '1', '127.0.0.1', '2013-03-22 17:41:53', '2013-03-30 17:56:05', '2013-03-22 22:04:53');
INSERT INTO dotp_user_access_log VALUES ('52', '1', '127.0.0.1', '2013-03-30 13:56:16', '2013-03-30 18:03:30', '2013-03-30 18:03:25');
INSERT INTO dotp_user_access_log VALUES ('53', '1', '127.0.0.1', '2013-03-30 14:03:35', '2013-03-30 18:04:35', '2013-03-30 18:04:31');
INSERT INTO dotp_user_access_log VALUES ('54', '1', '127.0.0.1', '2013-03-30 14:04:41', '2013-04-02 00:52:25', '2013-03-30 18:07:53');
INSERT INTO dotp_user_access_log VALUES ('55', '1', '127.0.0.1', '2013-04-01 19:52:32', '2013-04-02 00:53:41', '2013-04-02 00:53:34');
INSERT INTO dotp_user_access_log VALUES ('56', '9', '127.0.0.1', '2013-04-01 19:53:47', '2013-04-02 00:53:58', '2013-04-02 00:53:48');
INSERT INTO dotp_user_access_log VALUES ('57', '1', '127.0.0.1', '2013-04-01 19:54:04', '2013-04-02 19:23:30', '2013-04-02 19:22:34');
INSERT INTO dotp_user_access_log VALUES ('58', '1', '127.0.0.1', '2013-04-02 14:23:43', '2013-04-02 19:24:21', '2013-04-02 19:24:16');
INSERT INTO dotp_user_access_log VALUES ('59', '1', '127.0.0.1', '2013-04-02 14:24:27', '2013-04-02 19:29:00', '2013-04-02 19:28:55');
INSERT INTO dotp_user_access_log VALUES ('60', '1', '127.0.0.1', '2013-04-02 14:29:10', '2013-04-02 19:32:06', '2013-04-02 19:32:02');
INSERT INTO dotp_user_access_log VALUES ('61', '1', '127.0.0.1', '2013-04-02 14:32:15', '2013-04-02 19:36:39', '2013-04-02 19:36:35');
INSERT INTO dotp_user_access_log VALUES ('62', '1', '127.0.0.1', '2013-04-02 14:36:45', '2013-04-02 20:13:59', '2013-04-02 20:13:53');
INSERT INTO dotp_user_access_log VALUES ('63', '1', '127.0.0.1', '2013-04-02 15:14:14', '2013-04-02 20:15:43', '2013-04-02 20:15:38');
INSERT INTO dotp_user_access_log VALUES ('64', '1', '127.0.0.1', '2013-04-02 15:15:49', '2013-04-02 20:34:41', '2013-04-02 20:33:06');
INSERT INTO dotp_user_access_log VALUES ('65', '1', '127.0.0.1', '2013-04-02 15:34:49', '2013-04-02 20:36:52', '2013-04-02 20:36:47');
INSERT INTO dotp_user_access_log VALUES ('66', '1', '127.0.0.1', '2013-04-02 15:34:51', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO dotp_user_access_log VALUES ('67', '1', '127.0.0.1', '2013-04-02 15:37:00', '2013-04-02 20:37:41', '2013-04-02 20:37:35');
INSERT INTO dotp_user_access_log VALUES ('68', '1', '127.0.0.1', '2013-04-02 15:37:47', '2013-04-08 15:26:17', '2013-04-03 04:01:36');
INSERT INTO dotp_user_access_log VALUES ('69', '1', '127.0.0.1', '2013-04-08 10:26:24', '2013-05-01 23:47:03', '2013-04-08 15:26:45');
INSERT INTO dotp_user_access_log VALUES ('70', '1', '127.0.0.1', '2013-04-24 15:52:56', '0000-00-00 00:00:00', '2013-04-24 19:00:16');
INSERT INTO dotp_user_access_log VALUES ('71', '1', '127.0.0.1', '2013-04-24 16:00:16', '2013-04-25 20:52:28', '2013-04-25 20:52:20');
INSERT INTO dotp_user_access_log VALUES ('72', '1', '127.0.0.1', '2013-04-25 17:53:59', '2013-04-25 20:55:26', '2013-04-25 20:54:21');
INSERT INTO dotp_user_access_log VALUES ('73', '1', '127.0.0.1', '2013-04-25 17:56:09', '2013-04-28 18:00:47', '2013-04-25 21:01:51');
INSERT INTO dotp_user_access_log VALUES ('74', '1', '127.0.0.1', '2013-04-28 15:04:42', '2013-05-01 02:01:57', '2013-04-28 18:05:03');
INSERT INTO dotp_user_access_log VALUES ('75', '1', '127.0.0.1', '2013-04-28 16:41:29', '2013-05-01 23:47:03', '2013-04-28 19:41:41');
INSERT INTO dotp_user_access_log VALUES ('76', '1', '127.0.0.1', '2013-04-28 17:20:51', '2013-05-01 23:47:03', '2013-04-28 23:39:15');
INSERT INTO dotp_user_access_log VALUES ('77', '1', '127.0.0.1', '2013-04-28 18:05:12', '2013-05-01 23:47:03', '2013-04-28 21:05:13');
INSERT INTO dotp_user_access_log VALUES ('78', '1', '127.0.0.1', '2013-04-30 23:02:12', '2013-05-01 02:23:54', '2013-05-01 02:15:55');
INSERT INTO dotp_user_access_log VALUES ('79', '1', '127.0.0.1', '2013-04-30 23:24:01', '2013-05-01 02:27:29', '2013-05-01 02:24:16');
INSERT INTO dotp_user_access_log VALUES ('80', '1', '127.0.0.1', '2013-04-30 23:27:34', '2013-05-01 02:35:20', '2013-05-01 02:32:55');
INSERT INTO dotp_user_access_log VALUES ('81', '1', '127.0.0.1', '2013-04-30 23:35:27', '2013-05-01 02:40:23', '2013-05-01 02:38:37');
INSERT INTO dotp_user_access_log VALUES ('82', '1', '127.0.0.1', '2013-04-30 23:40:28', '2013-05-01 02:45:28', '2013-05-01 02:40:33');
INSERT INTO dotp_user_access_log VALUES ('83', '1', '127.0.0.1', '2013-04-30 23:45:34', '2013-05-01 02:48:02', '2013-05-01 02:47:19');
INSERT INTO dotp_user_access_log VALUES ('84', '1', '127.0.0.1', '2013-04-30 23:48:11', '2013-05-01 02:50:46', '2013-05-01 02:48:38');
INSERT INTO dotp_user_access_log VALUES ('85', '1', '127.0.0.1', '2013-04-30 23:50:52', '2013-05-01 02:52:45', '2013-05-01 02:51:04');
INSERT INTO dotp_user_access_log VALUES ('86', '1', '127.0.0.1', '2013-04-30 23:52:49', '2013-05-01 02:53:50', '2013-05-01 02:52:58');
INSERT INTO dotp_user_access_log VALUES ('87', '1', '127.0.0.1', '2013-04-30 23:53:56', '2013-05-01 02:55:29', '2013-05-01 02:55:06');
INSERT INTO dotp_user_access_log VALUES ('88', '1', '127.0.0.1', '2013-04-30 23:55:33', '2013-05-01 03:20:41', '2013-05-01 03:12:03');
INSERT INTO dotp_user_access_log VALUES ('89', '1', '127.0.0.1', '2013-05-01 00:20:47', '2013-05-01 03:23:27', '2013-05-01 03:21:20');
INSERT INTO dotp_user_access_log VALUES ('90', '1', '127.0.0.1', '2013-05-01 00:24:01', '2013-05-01 03:24:27', '2013-05-01 03:24:07');
INSERT INTO dotp_user_access_log VALUES ('91', '1', '127.0.0.1', '2013-05-01 00:25:55', '2013-05-01 03:27:46', '2013-05-01 03:26:01');
INSERT INTO dotp_user_access_log VALUES ('92', '1', '127.0.0.1', '2013-05-01 00:28:07', '2013-05-01 19:31:36', '2013-05-01 03:28:12');
INSERT INTO dotp_user_access_log VALUES ('93', '1', '127.0.0.1', '2013-05-01 16:31:47', '2013-05-01 19:45:13', '2013-05-01 19:32:04');
INSERT INTO dotp_user_access_log VALUES ('94', '1', '127.0.0.1', '2013-05-01 16:45:22', '2013-05-01 19:49:02', '2013-05-01 19:45:51');
INSERT INTO dotp_user_access_log VALUES ('95', '1', '127.0.0.1', '2013-05-01 16:49:06', '2013-05-01 20:10:48', '2013-05-01 19:50:20');
INSERT INTO dotp_user_access_log VALUES ('96', '1', '127.0.0.1', '2013-05-01 17:10:53', '2013-05-01 20:14:17', '2013-05-01 20:12:11');
INSERT INTO dotp_user_access_log VALUES ('97', '1', '127.0.0.1', '2013-05-01 17:14:24', '2013-05-01 20:29:06', '2013-05-01 20:19:31');
INSERT INTO dotp_user_access_log VALUES ('98', '1', '127.0.0.1', '2013-05-01 17:29:11', '2013-05-01 21:57:46', '2013-05-01 21:51:22');
INSERT INTO dotp_user_access_log VALUES ('99', '1', '127.0.0.1', '2013-05-01 18:57:51', '2013-05-01 22:01:46', '2013-05-01 21:57:58');
INSERT INTO dotp_user_access_log VALUES ('100', '1', '127.0.0.1', '2013-05-01 19:01:51', '2013-05-01 22:02:10', '2013-05-01 22:01:57');
INSERT INTO dotp_user_access_log VALUES ('101', '1', '127.0.0.1', '2013-05-01 19:02:31', '2013-05-01 22:31:47', '2013-05-01 22:24:03');
INSERT INTO dotp_user_access_log VALUES ('102', '1', '127.0.0.1', '2013-05-01 19:31:52', '2013-05-01 22:32:24', '2013-05-01 22:32:13');
INSERT INTO dotp_user_access_log VALUES ('103', '1', '127.0.0.1', '2013-05-01 20:13:59', '2013-05-01 23:33:04', '2013-05-01 23:20:26');
INSERT INTO dotp_user_access_log VALUES ('104', '1', '127.0.0.1', '2013-05-01 20:33:09', '2013-05-01 23:37:39', '2013-05-01 23:36:19');
INSERT INTO dotp_user_access_log VALUES ('105', '1', '127.0.0.1', '2013-05-01 20:38:03', '2013-05-01 23:38:45', '2013-05-01 23:38:08');
INSERT INTO dotp_user_access_log VALUES ('106', '1', '127.0.0.1', '2013-05-01 20:39:34', '2013-05-01 23:42:34', '2013-05-01 23:40:18');
INSERT INTO dotp_user_access_log VALUES ('107', '1', '127.0.0.1', '2013-05-01 20:42:56', '2013-05-01 23:45:07', '2013-05-01 23:43:25');
INSERT INTO dotp_user_access_log VALUES ('108', '1', '127.0.0.1', '2013-05-01 20:45:37', '2013-05-02 00:02:54', '2013-05-01 23:57:11');
INSERT INTO dotp_user_access_log VALUES ('109', '1', '127.0.0.1', '2013-05-01 21:02:59', '2013-05-02 00:12:39', '2013-05-02 00:03:49');
INSERT INTO dotp_user_access_log VALUES ('110', '1', '127.0.0.1', '2013-05-01 21:12:43', '2013-05-02 02:22:54', '2013-05-02 02:13:27');
INSERT INTO dotp_user_access_log VALUES ('111', '1', '127.0.0.1', '2013-05-01 23:23:00', '2013-05-02 02:23:37', '2013-05-02 02:23:18');
INSERT INTO dotp_user_access_log VALUES ('112', '1', '127.0.0.1', '2013-05-01 23:23:53', '2013-05-02 02:24:17', '2013-05-02 02:24:03');
INSERT INTO dotp_user_access_log VALUES ('113', '1', '127.0.0.1', '2013-05-01 23:24:22', '2013-05-02 02:36:28', '2013-05-02 02:24:35');
INSERT INTO dotp_user_access_log VALUES ('114', '1', '127.0.0.1', '2013-05-01 23:36:29', '2013-05-02 02:36:51', '2013-05-02 02:36:35');
INSERT INTO dotp_user_access_log VALUES ('115', '1', '127.0.0.1', '2013-05-01 23:37:34', '2013-05-02 03:00:33', '2013-05-02 02:47:53');
INSERT INTO dotp_user_access_log VALUES ('116', '1', '127.0.0.1', '2013-05-02 00:00:35', '2013-05-02 03:01:05', '2013-05-02 03:00:47');
INSERT INTO dotp_user_access_log VALUES ('117', '1', '127.0.0.1', '2013-05-02 00:02:15', '2013-05-02 03:20:44', '2013-05-02 03:07:46');
INSERT INTO dotp_user_access_log VALUES ('118', '1', '127.0.0.1', '2013-05-02 00:20:46', '2013-05-02 03:35:11', '2013-05-02 03:30:15');
INSERT INTO dotp_user_access_log VALUES ('119', '1', '127.0.0.1', '2013-05-02 00:35:12', '2013-05-02 03:37:13', '2013-05-02 03:37:02');
INSERT INTO dotp_user_access_log VALUES ('120', '1', '127.0.0.1', '2013-05-02 00:43:24', '2013-05-02 03:43:53', '2013-05-02 03:43:38');
INSERT INTO dotp_user_access_log VALUES ('121', '1', '127.0.0.1', '2013-05-02 00:45:08', '2013-05-02 03:45:53', '2013-05-02 03:45:15');
INSERT INTO dotp_user_access_log VALUES ('122', '1', '127.0.0.1', '2013-05-02 12:50:22', '2013-05-02 18:14:07', '2013-05-02 18:09:40');
INSERT INTO dotp_user_access_log VALUES ('123', '1', '127.0.0.1', '2013-05-02 15:14:11', '2013-05-02 18:16:53', '2013-05-02 18:15:02');
INSERT INTO dotp_user_access_log VALUES ('124', '1', '127.0.0.1', '2013-05-02 15:16:55', '2013-05-02 18:45:18', '2013-05-02 18:17:06');
INSERT INTO dotp_user_access_log VALUES ('125', '1', '127.0.0.1', '2013-05-02 15:45:20', '2013-05-02 18:48:01', '2013-05-02 18:45:28');
INSERT INTO dotp_user_access_log VALUES ('126', '1', '127.0.0.1', '2013-05-02 15:48:02', '2013-05-02 19:01:49', '2013-05-02 18:48:22');
INSERT INTO dotp_user_access_log VALUES ('127', '1', '127.0.0.1', '2013-05-02 16:01:51', '2013-05-02 19:13:43', '2013-05-02 19:10:03');
INSERT INTO dotp_user_access_log VALUES ('128', '1', '127.0.0.1', '2013-05-02 16:13:45', '2013-05-02 19:57:11', '2013-05-02 19:16:31');
INSERT INTO dotp_user_access_log VALUES ('129', '1', '127.0.0.1', '2013-05-02 16:57:12', '2013-05-02 20:02:55', '2013-05-02 19:57:46');
INSERT INTO dotp_user_access_log VALUES ('130', '1', '127.0.0.1', '2013-05-02 17:02:57', '2013-05-02 20:15:13', '2013-05-02 20:03:36');
INSERT INTO dotp_user_access_log VALUES ('131', '1', '127.0.0.1', '2013-05-02 17:15:14', '2013-05-02 20:17:07', '2013-05-02 20:16:50');
INSERT INTO dotp_user_access_log VALUES ('132', '1', '127.0.0.1', '2013-05-02 17:17:31', '2013-05-02 20:20:38', '2013-05-02 20:18:00');
INSERT INTO dotp_user_access_log VALUES ('133', '1', '127.0.0.1', '2013-05-02 17:21:38', '2013-05-02 20:23:57', '2013-05-02 20:21:55');
INSERT INTO dotp_user_access_log VALUES ('134', '1', '127.0.0.1', '2013-05-02 17:23:58', '2013-05-02 20:31:06', '2013-05-02 20:30:15');
INSERT INTO dotp_user_access_log VALUES ('135', '1', '127.0.0.1', '2013-05-02 17:31:07', '2013-05-02 20:32:10', '2013-05-02 20:31:21');
INSERT INTO dotp_user_access_log VALUES ('136', '1', '127.0.0.1', '2013-05-02 17:32:11', '2013-05-02 20:41:17', '2013-05-02 20:36:07');
INSERT INTO dotp_user_access_log VALUES ('137', '1', '127.0.0.1', '2013-05-02 17:41:18', '2013-05-02 20:43:04', '2013-05-02 20:41:39');
INSERT INTO dotp_user_access_log VALUES ('138', '1', '127.0.0.1', '2013-05-02 17:43:10', '2013-05-02 20:49:04', '2013-05-02 20:43:30');
INSERT INTO dotp_user_access_log VALUES ('139', '1', '127.0.0.1', '2013-05-02 17:49:05', '2013-05-02 20:49:55', '2013-05-02 20:49:22');
INSERT INTO dotp_user_access_log VALUES ('140', '1', '127.0.0.1', '2013-05-02 17:49:56', '2013-05-02 20:50:31', '2013-05-02 20:50:06');
INSERT INTO dotp_user_access_log VALUES ('141', '1', '127.0.0.1', '2013-05-02 17:50:48', '2013-05-02 20:53:02', '2013-05-02 20:50:58');
INSERT INTO dotp_user_access_log VALUES ('142', '1', '127.0.0.1', '2013-05-02 17:53:04', '2013-05-02 21:01:39', '2013-05-02 20:54:01');
INSERT INTO dotp_user_access_log VALUES ('143', '1', '127.0.0.1', '2013-05-02 18:01:40', '2013-05-02 21:03:41', '2013-05-02 21:01:52');
INSERT INTO dotp_user_access_log VALUES ('144', '1', '127.0.0.1', '2013-05-02 18:04:11', '2013-05-02 21:07:50', '2013-05-02 21:04:24');
INSERT INTO dotp_user_access_log VALUES ('145', '1', '127.0.0.1', '2013-05-02 18:07:51', '2013-05-02 21:09:28', '2013-05-02 21:08:02');
INSERT INTO dotp_user_access_log VALUES ('146', '1', '127.0.0.1', '2013-05-02 18:09:29', '2013-05-02 21:15:30', '2013-05-02 21:09:41');
INSERT INTO dotp_user_access_log VALUES ('147', '1', '127.0.0.1', '2013-05-02 18:15:33', '2013-05-02 21:17:34', '2013-05-02 21:15:51');
INSERT INTO dotp_user_access_log VALUES ('148', '1', '127.0.0.1', '2013-05-02 18:17:47', '2013-05-02 23:28:21', '2013-05-02 21:18:00');
INSERT INTO dotp_user_access_log VALUES ('149', '1', '127.0.0.1', '2013-05-02 20:28:57', '2013-05-03 00:12:51', '2013-05-03 00:11:41');
INSERT INTO dotp_user_access_log VALUES ('150', '1', '127.0.0.1', '2013-05-02 21:12:53', '2013-05-03 00:15:26', '2013-05-03 00:13:11');
INSERT INTO dotp_user_access_log VALUES ('151', '1', '127.0.0.1', '2013-05-02 21:15:29', '2013-05-03 00:18:28', '2013-05-03 00:15:38');
INSERT INTO dotp_user_access_log VALUES ('152', '1', '127.0.0.1', '2013-05-02 21:18:45', '2013-05-03 02:33:24', '2013-05-03 00:18:54');
INSERT INTO dotp_user_access_log VALUES ('153', '1', '127.0.0.1', '2013-05-02 23:33:32', '2013-05-03 03:01:31', '2013-05-03 02:34:43');
INSERT INTO dotp_user_access_log VALUES ('154', '1', '127.0.0.1', '2013-05-03 00:01:34', '2013-05-03 14:48:22', '2013-05-03 03:01:47');
INSERT INTO dotp_user_access_log VALUES ('155', '1', '127.0.0.1', '2013-05-03 11:51:07', '2013-05-03 14:58:42', '2013-05-03 14:51:17');
INSERT INTO dotp_user_access_log VALUES ('156', '1', '127.0.0.1', '2013-05-03 11:58:44', '2013-05-03 15:00:45', '2013-05-03 14:58:52');
INSERT INTO dotp_user_access_log VALUES ('157', '1', '127.0.0.1', '2013-05-03 12:00:47', '2013-05-03 15:01:44', '2013-05-03 15:00:54');
INSERT INTO dotp_user_access_log VALUES ('158', '1', '127.0.0.1', '2013-05-03 12:01:45', '2013-05-03 15:03:06', '2013-05-03 15:02:00');
INSERT INTO dotp_user_access_log VALUES ('159', '1', '127.0.0.1', '2013-05-03 12:03:08', '2013-05-03 15:04:22', '2013-05-03 15:03:16');
INSERT INTO dotp_user_access_log VALUES ('160', '1', '127.0.0.1', '2013-05-03 12:04:44', '2013-05-03 15:15:06', '2013-05-03 15:05:02');
INSERT INTO dotp_user_access_log VALUES ('161', '1', '127.0.0.1', '2013-05-03 12:15:12', '2013-05-03 15:20:31', '2013-05-03 15:17:32');
INSERT INTO dotp_user_access_log VALUES ('162', '1', '127.0.0.1', '2013-05-03 12:20:36', '2013-05-03 15:23:20', '2013-05-03 15:20:59');
INSERT INTO dotp_user_access_log VALUES ('163', '1', '127.0.0.1', '2013-05-03 12:23:22', '2013-05-03 15:26:49', '2013-05-03 15:23:53');
INSERT INTO dotp_user_access_log VALUES ('164', '1', '127.0.0.1', '2013-05-03 12:26:57', '2013-05-03 15:40:15', '2013-05-03 15:27:04');
INSERT INTO dotp_user_access_log VALUES ('165', '1', '127.0.0.1', '2013-05-03 12:40:18', '2013-05-03 15:58:54', '2013-05-03 15:40:25');
INSERT INTO dotp_user_access_log VALUES ('166', '1', '127.0.0.1', '2013-05-03 12:58:56', '2013-05-03 15:59:58', '2013-05-03 15:59:31');
INSERT INTO dotp_user_access_log VALUES ('167', '1', '127.0.0.1', '2013-05-03 13:00:48', '2013-05-03 16:01:45', '2013-05-03 16:01:16');
INSERT INTO dotp_user_access_log VALUES ('168', '1', '127.0.0.1', '2013-05-03 13:01:58', '2013-05-06 22:08:05', '2013-05-03 16:02:40');
INSERT INTO dotp_user_access_log VALUES ('169', '1', '127.0.0.1', '2013-05-03 13:13:35', '2013-05-03 16:22:51', '2013-05-03 16:17:03');
INSERT INTO dotp_user_access_log VALUES ('170', '1', '127.0.0.1', '2013-05-03 13:22:52', '2013-05-03 16:23:26', '2013-05-03 16:23:05');
INSERT INTO dotp_user_access_log VALUES ('171', '1', '127.0.0.1', '2013-05-03 13:24:12', '2013-05-03 16:35:03', '2013-05-03 16:24:18');
INSERT INTO dotp_user_access_log VALUES ('172', '1', '127.0.0.1', '2013-05-03 13:35:05', '2013-05-03 16:35:38', '2013-05-03 16:35:12');
INSERT INTO dotp_user_access_log VALUES ('173', '1', '127.0.0.1', '2013-05-03 13:39:08', '2013-05-03 16:42:06', '2013-05-03 16:39:15');
INSERT INTO dotp_user_access_log VALUES ('174', '1', '127.0.0.1', '2013-05-03 13:42:36', '2013-05-03 17:34:13', '2013-05-03 17:27:45');
INSERT INTO dotp_user_access_log VALUES ('175', '1', '127.0.0.1', '2013-05-03 14:34:15', '2013-05-03 17:34:55', '2013-05-03 17:34:31');
INSERT INTO dotp_user_access_log VALUES ('176', '1', '127.0.0.1', '2013-05-03 14:36:38', '2013-05-03 17:37:28', '2013-05-03 17:37:11');
INSERT INTO dotp_user_access_log VALUES ('177', '1', '127.0.0.1', '2013-05-03 14:45:32', '2013-05-03 17:47:16', '2013-05-03 17:46:46');
INSERT INTO dotp_user_access_log VALUES ('178', '1', '127.0.0.1', '2013-05-03 14:48:46', '2013-05-03 17:50:11', '2013-05-03 17:49:49');
INSERT INTO dotp_user_access_log VALUES ('179', '1', '127.0.0.1', '2013-05-03 14:57:40', '2013-05-06 22:08:05', '2013-05-03 18:02:09');
INSERT INTO dotp_user_access_log VALUES ('180', '1', '127.0.0.1', '2013-05-03 15:05:52', '2013-05-03 18:06:24', '2013-05-03 18:06:21');
INSERT INTO dotp_user_access_log VALUES ('181', '1', '127.0.0.1', '2013-05-03 15:06:27', '2013-05-03 18:08:25', '2013-05-03 18:06:41');
INSERT INTO dotp_user_access_log VALUES ('182', '1', '127.0.0.1', '2013-05-03 15:08:27', '2013-05-03 18:09:01', '2013-05-03 18:08:45');
INSERT INTO dotp_user_access_log VALUES ('183', '1', '127.0.0.1', '2013-05-03 15:09:17', '2013-05-06 22:08:05', '2013-05-03 18:09:41');
INSERT INTO dotp_user_access_log VALUES ('184', '1', '127.0.0.1', '2013-05-03 15:20:30', '2013-05-03 18:21:20', '2013-05-03 18:20:37');
INSERT INTO dotp_user_access_log VALUES ('185', '1', '127.0.0.1', '2013-05-03 15:21:47', '2013-05-03 18:26:08', '2013-05-03 18:22:01');
INSERT INTO dotp_user_access_log VALUES ('186', '1', '127.0.0.1', '2013-05-03 15:27:01', '2013-05-03 18:32:19', '2013-05-03 18:30:58');
INSERT INTO dotp_user_access_log VALUES ('187', '1', '127.0.0.1', '2013-05-03 15:32:41', '2013-05-03 18:34:28', '2013-05-03 18:32:50');
INSERT INTO dotp_user_access_log VALUES ('188', '1', '127.0.0.1', '2013-05-03 15:34:42', '2013-05-03 18:36:59', '2013-05-03 18:35:37');
INSERT INTO dotp_user_access_log VALUES ('189', '1', '127.0.0.1', '2013-05-03 15:37:18', '2013-05-03 19:06:49', '2013-05-03 18:46:08');
INSERT INTO dotp_user_access_log VALUES ('190', '1', '127.0.0.1', '2013-05-03 16:06:51', '2013-05-03 19:08:36', '2013-05-03 19:06:58');
INSERT INTO dotp_user_access_log VALUES ('191', '1', '127.0.0.1', '2013-05-03 16:08:54', '2013-05-03 19:12:29', '2013-05-03 19:09:04');
INSERT INTO dotp_user_access_log VALUES ('192', '1', '127.0.0.1', '2013-05-03 16:13:55', '2013-05-03 19:41:49', '2013-05-03 19:15:56');
INSERT INTO dotp_user_access_log VALUES ('193', '1', '127.0.0.1', '2013-05-03 16:41:58', '2013-05-03 19:42:32', '2013-05-03 19:42:06');
INSERT INTO dotp_user_access_log VALUES ('194', '1', '127.0.0.1', '2013-05-03 16:51:55', '2013-05-03 20:11:37', '2013-05-03 19:52:58');
INSERT INTO dotp_user_access_log VALUES ('195', '1', '127.0.0.1', '2013-05-03 17:11:38', '2013-05-03 20:28:05', '2013-05-03 20:11:43');
INSERT INTO dotp_user_access_log VALUES ('196', '1', '127.0.0.1', '2013-05-03 17:29:00', '2013-05-04 14:32:50', '2013-05-03 23:54:39');
INSERT INTO dotp_user_access_log VALUES ('197', '1', '127.0.0.1', '2013-05-04 11:32:51', '2013-05-04 14:35:08', '2013-05-04 14:33:06');
INSERT INTO dotp_user_access_log VALUES ('198', '1', '127.0.0.1', '2013-05-04 11:35:50', '2013-05-04 14:39:34', '2013-05-04 14:36:12');
INSERT INTO dotp_user_access_log VALUES ('199', '1', '127.0.0.1', '2013-05-04 11:39:36', '2013-05-04 15:04:51', '2013-05-04 14:49:42');
INSERT INTO dotp_user_access_log VALUES ('200', '1', '127.0.0.1', '2013-05-04 12:04:53', '2013-05-04 15:13:26', '2013-05-04 15:09:51');
INSERT INTO dotp_user_access_log VALUES ('201', '1', '127.0.0.1', '2013-05-04 12:13:28', '2013-05-04 15:21:44', '2013-05-04 15:18:33');
INSERT INTO dotp_user_access_log VALUES ('202', '1', '127.0.0.1', '2013-05-04 12:22:02', '2013-05-04 15:26:53', '2013-05-04 15:22:07');
INSERT INTO dotp_user_access_log VALUES ('203', '1', '127.0.0.1', '2013-05-04 12:26:55', '2013-05-04 15:31:02', '2013-05-04 15:27:03');
INSERT INTO dotp_user_access_log VALUES ('204', '1', '127.0.0.1', '2013-05-04 12:31:04', '2013-05-04 15:32:37', '2013-05-04 15:31:11');
INSERT INTO dotp_user_access_log VALUES ('205', '1', '127.0.0.1', '2013-05-04 12:32:38', '2013-05-04 15:34:53', '2013-05-04 15:32:42');
INSERT INTO dotp_user_access_log VALUES ('206', '1', '127.0.0.1', '2013-05-04 12:34:54', '2013-05-04 15:37:21', '2013-05-04 15:34:58');
INSERT INTO dotp_user_access_log VALUES ('207', '1', '127.0.0.1', '2013-05-04 12:37:22', '2013-05-04 15:38:21', '2013-05-04 15:37:27');
INSERT INTO dotp_user_access_log VALUES ('208', '1', '127.0.0.1', '2013-05-04 12:38:22', '2013-05-04 15:39:25', '2013-05-04 15:38:32');
INSERT INTO dotp_user_access_log VALUES ('209', '1', '127.0.0.1', '2013-05-04 12:39:27', '2013-05-04 15:40:28', '2013-05-04 15:39:35');
INSERT INTO dotp_user_access_log VALUES ('210', '1', '127.0.0.1', '2013-05-04 12:40:29', '2013-05-04 15:46:44', '2013-05-04 15:40:36');
INSERT INTO dotp_user_access_log VALUES ('211', '1', '127.0.0.1', '2013-05-04 12:46:46', '2013-05-04 16:19:19', '2013-05-04 15:46:53');
INSERT INTO dotp_user_access_log VALUES ('212', '1', '127.0.0.1', '2013-05-04 13:19:23', '2013-05-04 20:27:30', '2013-05-04 20:14:16');
INSERT INTO dotp_user_access_log VALUES ('213', '1', '127.0.0.1', '2013-05-04 17:27:32', '2013-05-04 21:08:03', '2013-05-04 20:29:19');
INSERT INTO dotp_user_access_log VALUES ('214', '1', '127.0.0.1', '2013-05-04 18:08:06', '2013-05-04 21:11:19', '2013-05-04 21:08:12');
INSERT INTO dotp_user_access_log VALUES ('215', '1', '127.0.0.1', '2013-05-04 18:11:21', '2013-05-04 21:59:38', '2013-05-04 21:11:28');
INSERT INTO dotp_user_access_log VALUES ('216', '1', '127.0.0.1', '2013-05-04 18:59:40', '2013-05-04 22:00:34', '2013-05-04 21:59:54');
INSERT INTO dotp_user_access_log VALUES ('217', '1', '127.0.0.1', '2013-05-04 19:01:17', '2013-05-05 03:37:40', '2013-05-04 22:54:05');
INSERT INTO dotp_user_access_log VALUES ('218', '1', '127.0.0.1', '2013-05-05 00:37:41', '2013-05-05 03:41:52', '2013-05-05 03:37:45');
INSERT INTO dotp_user_access_log VALUES ('219', '1', '127.0.0.1', '2013-05-05 00:41:53', '2013-05-05 04:18:25', '2013-05-05 03:49:48');
INSERT INTO dotp_user_access_log VALUES ('220', '1', '127.0.0.1', '2013-05-05 01:18:27', '2013-05-05 04:25:40', '2013-05-05 04:19:00');
INSERT INTO dotp_user_access_log VALUES ('221', '1', '127.0.0.1', '2013-05-05 01:25:42', '2013-05-05 15:37:24', '2013-05-05 14:44:03');
INSERT INTO dotp_user_access_log VALUES ('222', '1', '127.0.0.1', '2013-05-05 12:37:26', '2013-05-05 15:38:54', '2013-05-05 15:37:37');
INSERT INTO dotp_user_access_log VALUES ('223', '1', '127.0.0.1', '2013-05-05 12:39:15', '2013-05-05 23:06:55', '2013-05-05 22:29:26');
INSERT INTO dotp_user_access_log VALUES ('224', '1', '127.0.0.1', '2013-05-05 20:06:58', '2013-05-06 00:31:37', '2013-05-05 23:07:20');
INSERT INTO dotp_user_access_log VALUES ('225', '1', '127.0.0.1', '2013-05-05 21:31:40', '2013-05-06 00:58:16', '2013-05-06 00:50:34');
INSERT INTO dotp_user_access_log VALUES ('226', '1', '127.0.0.1', '2013-05-05 21:58:18', '2013-05-06 01:02:32', '2013-05-06 01:00:27');
INSERT INTO dotp_user_access_log VALUES ('227', '1', '127.0.0.1', '2013-05-05 22:02:33', '2013-05-06 01:03:28', '2013-05-06 01:02:41');
INSERT INTO dotp_user_access_log VALUES ('228', '1', '127.0.0.1', '2013-05-05 22:03:29', '2013-05-06 01:18:42', '2013-05-06 01:18:39');
INSERT INTO dotp_user_access_log VALUES ('229', '1', '127.0.0.1', '2013-05-05 22:18:46', '2013-05-06 01:20:41', '2013-05-06 01:18:55');
INSERT INTO dotp_user_access_log VALUES ('230', '1', '127.0.0.1', '2013-05-05 22:24:17', '2013-05-06 01:46:43', '2013-05-06 01:46:40');
INSERT INTO dotp_user_access_log VALUES ('231', '1', '127.0.0.1', '2013-05-05 22:46:45', '2013-05-06 01:51:28', '2013-05-06 01:47:27');
INSERT INTO dotp_user_access_log VALUES ('232', '1', '127.0.0.1', '2013-05-05 22:51:29', '2013-05-06 01:53:06', '2013-05-06 01:51:47');
INSERT INTO dotp_user_access_log VALUES ('233', '1', '127.0.0.1', '2013-05-05 22:53:08', '2013-05-06 01:56:23', '2013-05-06 01:54:01');
INSERT INTO dotp_user_access_log VALUES ('234', '1', '127.0.0.1', '2013-05-05 22:56:25', '2013-05-06 01:56:35', '2013-05-06 01:56:32');
INSERT INTO dotp_user_access_log VALUES ('235', '1', '127.0.0.1', '2013-05-05 22:56:37', '2013-05-06 02:45:20', '2013-05-06 02:36:27');
INSERT INTO dotp_user_access_log VALUES ('236', '1', '127.0.0.1', '2013-05-05 23:45:21', '2013-05-06 02:47:11', '2013-05-06 02:45:34');
INSERT INTO dotp_user_access_log VALUES ('237', '1', '127.0.0.1', '2013-05-05 23:47:12', '2013-05-06 02:53:06', '2013-05-06 02:51:15');
INSERT INTO dotp_user_access_log VALUES ('238', '1', '127.0.0.1', '2013-05-05 23:53:07', '2013-05-06 02:58:02', '2013-05-06 02:54:18');
INSERT INTO dotp_user_access_log VALUES ('239', '1', '127.0.0.1', '2013-05-05 23:58:03', '2013-05-06 03:01:08', '2013-05-06 02:58:11');
INSERT INTO dotp_user_access_log VALUES ('240', '1', '127.0.0.1', '2013-05-06 00:01:10', '2013-05-06 03:02:50', '2013-05-06 03:01:39');
INSERT INTO dotp_user_access_log VALUES ('241', '1', '127.0.0.1', '2013-05-06 00:02:51', '2013-05-06 03:13:26', '2013-05-06 03:07:45');
INSERT INTO dotp_user_access_log VALUES ('242', '1', '127.0.0.1', '2013-05-06 00:13:27', '2013-05-06 03:43:33', '2013-05-06 03:14:26');
INSERT INTO dotp_user_access_log VALUES ('243', '1', '127.0.0.1', '2013-05-06 00:43:35', '2013-05-06 03:43:52', '2013-05-06 03:43:41');
INSERT INTO dotp_user_access_log VALUES ('244', '1', '127.0.0.1', '2013-05-06 00:44:32', '2013-05-06 15:18:31', '2013-05-06 04:24:28');
INSERT INTO dotp_user_access_log VALUES ('245', '1', '127.0.0.1', '2013-05-06 12:18:36', '2013-05-06 15:25:27', '2013-05-06 15:19:00');
INSERT INTO dotp_user_access_log VALUES ('246', '1', '127.0.0.1', '2013-05-06 12:25:29', '2013-05-06 15:26:42', '2013-05-06 15:26:40');
INSERT INTO dotp_user_access_log VALUES ('247', '1', '127.0.0.1', '2013-05-06 12:26:45', '2013-05-06 16:04:29', '2013-05-06 15:48:28');
INSERT INTO dotp_user_access_log VALUES ('248', '1', '127.0.0.1', '2013-05-06 13:04:31', '2013-05-06 16:05:50', '2013-05-06 16:05:46');
INSERT INTO dotp_user_access_log VALUES ('249', '1', '127.0.0.1', '2013-05-06 13:05:52', '2013-05-06 16:07:07', '2013-05-06 16:06:15');
INSERT INTO dotp_user_access_log VALUES ('250', '1', '127.0.0.1', '2013-05-06 13:07:25', '2013-05-06 16:09:19', '2013-05-06 16:07:39');
INSERT INTO dotp_user_access_log VALUES ('251', '1', '127.0.0.1', '2013-05-06 13:09:23', '2013-05-06 16:42:14', '2013-05-06 16:09:35');
INSERT INTO dotp_user_access_log VALUES ('252', '1', '127.0.0.1', '2013-05-06 13:42:18', '2013-05-06 16:46:10', '2013-05-06 16:42:36');
INSERT INTO dotp_user_access_log VALUES ('253', '1', '127.0.0.1', '2013-05-06 13:46:12', '2013-05-06 16:53:43', '2013-05-06 16:46:51');
INSERT INTO dotp_user_access_log VALUES ('254', '1', '127.0.0.1', '2013-05-06 13:53:44', '2013-05-06 16:54:47', '2013-05-06 16:53:52');
INSERT INTO dotp_user_access_log VALUES ('255', '1', '127.0.0.1', '2013-05-06 13:55:15', '2013-05-06 16:58:27', '2013-05-06 16:55:21');
INSERT INTO dotp_user_access_log VALUES ('256', '1', '127.0.0.1', '2013-05-06 13:58:29', '2013-05-06 16:58:58', '2013-05-06 16:58:48');
INSERT INTO dotp_user_access_log VALUES ('257', '1', '127.0.0.1', '2013-05-06 14:00:33', '2013-05-06 17:05:29', '2013-05-06 17:03:05');
INSERT INTO dotp_user_access_log VALUES ('258', '1', '127.0.0.1', '2013-05-06 14:05:30', '2013-05-06 17:08:07', '2013-05-06 17:05:39');
INSERT INTO dotp_user_access_log VALUES ('259', '1', '127.0.0.1', '2013-05-06 14:08:10', '2013-05-06 17:10:18', '2013-05-06 17:08:21');
INSERT INTO dotp_user_access_log VALUES ('260', '1', '127.0.0.1', '2013-05-06 14:10:20', '2013-05-06 17:11:49', '2013-05-06 17:10:28');
INSERT INTO dotp_user_access_log VALUES ('261', '1', '127.0.0.1', '2013-05-06 14:11:50', '2013-05-06 17:13:30', '2013-05-06 17:11:56');
INSERT INTO dotp_user_access_log VALUES ('262', '1', '127.0.0.1', '2013-05-06 14:13:32', '2013-05-06 17:21:50', '2013-05-06 17:13:40');
INSERT INTO dotp_user_access_log VALUES ('263', '1', '127.0.0.1', '2013-05-06 14:21:51', '2013-05-06 17:22:58', '2013-05-06 17:21:59');
INSERT INTO dotp_user_access_log VALUES ('264', '1', '127.0.0.1', '2013-05-06 14:23:17', '2013-05-06 17:29:54', '2013-05-06 17:23:38');
INSERT INTO dotp_user_access_log VALUES ('265', '1', '127.0.0.1', '2013-05-06 14:29:56', '2013-05-06 17:31:35', '2013-05-06 17:30:04');
INSERT INTO dotp_user_access_log VALUES ('266', '1', '127.0.0.1', '2013-05-06 14:31:50', '2013-05-06 17:34:21', '2013-05-06 17:31:57');
INSERT INTO dotp_user_access_log VALUES ('267', '1', '127.0.0.1', '2013-05-06 14:34:22', '2013-05-06 17:34:55', '2013-05-06 17:34:29');
INSERT INTO dotp_user_access_log VALUES ('268', '1', '127.0.0.1', '2013-05-06 14:34:55', '2013-05-06 17:39:02', '2013-05-06 17:35:03');
INSERT INTO dotp_user_access_log VALUES ('269', '1', '127.0.0.1', '2013-05-06 14:39:03', '2013-05-06 17:40:20', '2013-05-06 17:39:09');
INSERT INTO dotp_user_access_log VALUES ('270', '1', '127.0.0.1', '2013-05-06 14:40:21', '2013-05-06 17:41:49', '2013-05-06 17:40:33');
INSERT INTO dotp_user_access_log VALUES ('271', '1', '127.0.0.1', '2013-05-06 14:41:50', '2013-05-06 17:43:08', '2013-05-06 17:41:59');
INSERT INTO dotp_user_access_log VALUES ('272', '1', '127.0.0.1', '2013-05-06 14:43:09', '2013-05-06 17:58:50', '2013-05-06 17:45:20');
INSERT INTO dotp_user_access_log VALUES ('273', '1', '127.0.0.1', '2013-05-06 14:59:14', '2013-05-06 18:02:35', '2013-05-06 18:01:08');
INSERT INTO dotp_user_access_log VALUES ('274', '1', '127.0.0.1', '2013-05-06 15:03:23', '2013-05-06 18:11:10', '2013-05-06 18:05:35');
INSERT INTO dotp_user_access_log VALUES ('275', '1', '127.0.0.1', '2013-05-06 15:11:12', '2013-05-06 19:10:32', '2013-05-06 19:10:25');
INSERT INTO dotp_user_access_log VALUES ('276', '1', '127.0.0.1', '2013-05-06 16:10:34', '2013-05-06 19:31:34', '2013-05-06 19:19:24');
INSERT INTO dotp_user_access_log VALUES ('277', '1', '127.0.0.1', '2013-05-06 16:31:36', '2013-05-06 19:32:57', '2013-05-06 19:32:20');
INSERT INTO dotp_user_access_log VALUES ('278', '1', '127.0.0.1', '2013-05-06 16:33:18', '2013-05-06 19:39:42', '2013-05-06 19:38:09');
INSERT INTO dotp_user_access_log VALUES ('279', '1', '127.0.0.1', '2013-05-06 16:39:43', '2013-05-06 19:44:53', '2013-05-06 19:42:16');
INSERT INTO dotp_user_access_log VALUES ('280', '1', '127.0.0.1', '2013-05-06 16:44:55', '2013-05-06 19:46:07', '2013-05-06 19:45:08');
INSERT INTO dotp_user_access_log VALUES ('281', '1', '127.0.0.1', '2013-05-06 16:46:09', '2013-05-06 19:48:02', '2013-05-06 19:46:16');
INSERT INTO dotp_user_access_log VALUES ('282', '1', '127.0.0.1', '2013-05-06 16:48:03', '2013-05-06 20:01:59', '2013-05-06 19:48:48');
INSERT INTO dotp_user_access_log VALUES ('283', '1', '127.0.0.1', '2013-05-06 17:02:01', '2013-05-06 20:06:26', '2013-05-06 20:02:10');
INSERT INTO dotp_user_access_log VALUES ('284', '1', '127.0.0.1', '2013-05-06 17:06:28', '2013-05-06 20:09:49', '2013-05-06 20:06:37');
INSERT INTO dotp_user_access_log VALUES ('285', '1', '127.0.0.1', '2013-05-06 17:09:50', '2013-05-06 20:15:23', '2013-05-06 20:13:52');
INSERT INTO dotp_user_access_log VALUES ('286', '1', '127.0.0.1', '2013-05-06 17:15:24', '2013-05-06 20:16:04', '2013-05-06 20:16:02');
INSERT INTO dotp_user_access_log VALUES ('287', '1', '127.0.0.1', '2013-05-06 17:16:06', '2013-05-06 20:16:20', '2013-05-06 20:16:09');
INSERT INTO dotp_user_access_log VALUES ('288', '1', '127.0.0.1', '2013-05-06 17:16:34', '2013-05-06 20:17:50', '2013-05-06 20:16:37');
INSERT INTO dotp_user_access_log VALUES ('289', '1', '127.0.0.1', '2013-05-06 17:17:52', '2013-05-06 21:31:30', '2013-05-06 21:22:08');
INSERT INTO dotp_user_access_log VALUES ('290', '1', '127.0.0.1', '2013-05-06 18:31:32', '2013-05-06 21:51:11', '2013-05-06 21:31:50');
INSERT INTO dotp_user_access_log VALUES ('291', '1', '127.0.0.1', '2013-05-06 18:51:13', '2013-05-06 21:56:38', '2013-05-06 21:51:25');
INSERT INTO dotp_user_access_log VALUES ('292', '1', '127.0.0.1', '2013-05-06 18:56:40', '2013-05-06 22:00:15', '2013-05-06 21:57:54');
INSERT INTO dotp_user_access_log VALUES ('293', '1', '127.0.0.1', '2013-05-06 19:00:16', '2013-05-06 22:01:09', '2013-05-06 22:00:21');
INSERT INTO dotp_user_access_log VALUES ('294', '1', '127.0.0.1', '2013-05-06 19:01:11', '2013-05-06 22:02:16', '2013-05-06 22:01:14');
INSERT INTO dotp_user_access_log VALUES ('295', '1', '127.0.0.1', '2013-05-06 19:02:17', '2013-05-06 22:08:05', '2013-05-06 22:02:26');
INSERT INTO dotp_user_access_log VALUES ('296', '1', '127.0.0.1', '2013-05-06 19:08:06', '2013-05-06 22:13:19', '2013-05-06 22:08:09');
INSERT INTO dotp_user_access_log VALUES ('297', '1', '127.0.0.1', '2013-05-06 19:13:20', '2013-05-06 22:14:11', '2013-05-06 22:13:31');
INSERT INTO dotp_user_access_log VALUES ('298', '1', '127.0.0.1', '2013-05-06 19:14:12', '2013-05-06 22:21:30', '2013-05-06 22:19:55');
INSERT INTO dotp_user_access_log VALUES ('299', '1', '127.0.0.1', '2013-05-06 19:21:32', '2013-05-06 22:23:59', '2013-05-06 22:23:07');
INSERT INTO dotp_user_access_log VALUES ('300', '1', '127.0.0.1', '2013-05-06 19:24:02', '2013-05-06 22:33:00', '2013-05-06 22:24:07');
INSERT INTO dotp_user_access_log VALUES ('301', '1', '127.0.0.1', '2013-05-06 19:33:02', '2013-05-06 22:33:44', '2013-05-06 22:33:15');
INSERT INTO dotp_user_access_log VALUES ('302', '1', '127.0.0.1', '2013-05-06 19:33:46', '2013-05-07 00:27:34', '2013-05-06 22:35:34');
INSERT INTO dotp_user_access_log VALUES ('303', '1', '127.0.0.1', '2013-05-06 21:27:36', '2013-05-07 00:33:11', '2013-05-07 00:27:52');
INSERT INTO dotp_user_access_log VALUES ('304', '1', '127.0.0.1', '2013-05-06 21:33:14', '2013-05-07 00:50:03', '2013-05-07 00:45:48');
INSERT INTO dotp_user_access_log VALUES ('305', '1', '127.0.0.1', '2013-05-06 21:50:04', '2013-05-07 00:59:20', '2013-05-07 00:50:21');
INSERT INTO dotp_user_access_log VALUES ('306', '1', '127.0.0.1', '2013-05-06 21:59:21', '2013-05-07 01:01:52', '2013-05-07 01:00:33');
INSERT INTO dotp_user_access_log VALUES ('307', '1', '127.0.0.1', '2013-05-06 22:02:11', '2013-05-07 01:08:01', '2013-05-07 01:04:18');
INSERT INTO dotp_user_access_log VALUES ('308', '1', '127.0.0.1', '2013-05-06 22:08:02', '2013-05-07 01:12:00', '2013-05-07 01:08:57');
INSERT INTO dotp_user_access_log VALUES ('309', '1', '127.0.0.1', '2013-05-06 22:12:01', '2013-05-07 01:12:25', '2013-05-07 01:12:15');
INSERT INTO dotp_user_access_log VALUES ('310', '1', '127.0.0.1', '2013-05-06 22:16:33', '2013-05-07 01:18:04', '2013-05-07 01:16:56');
INSERT INTO dotp_user_access_log VALUES ('311', '1', '127.0.0.1', '2013-05-06 22:18:05', '2013-05-07 01:19:49', '2013-05-07 01:18:15');
INSERT INTO dotp_user_access_log VALUES ('312', '1', '127.0.0.1', '2013-05-06 22:20:16', '2013-05-07 01:21:45', '2013-05-07 01:20:47');
INSERT INTO dotp_user_access_log VALUES ('313', '1', '127.0.0.1', '2013-05-06 22:21:46', '2013-05-07 01:23:01', '2013-05-07 01:22:40');
INSERT INTO dotp_user_access_log VALUES ('314', '1', '127.0.0.1', '2013-05-06 22:23:03', '2013-05-07 01:29:28', '2013-05-07 01:24:23');
INSERT INTO dotp_user_access_log VALUES ('315', '1', '127.0.0.1', '2013-05-06 22:29:29', '2013-05-07 01:50:40', '2013-05-07 01:47:13');
INSERT INTO dotp_user_access_log VALUES ('316', '1', '127.0.0.1', '2013-05-06 22:50:42', '2013-05-07 01:54:32', '2013-05-07 01:51:32');
INSERT INTO dotp_user_access_log VALUES ('317', '1', '127.0.0.1', '2013-05-06 22:54:34', '2013-05-07 01:55:16', '2013-05-07 01:54:43');
INSERT INTO dotp_user_access_log VALUES ('318', '1', '127.0.0.1', '2013-05-06 22:56:12', '2013-05-07 02:04:54', '2013-05-07 02:00:15');
INSERT INTO dotp_user_access_log VALUES ('319', '1', '127.0.0.1', '2013-05-06 23:04:55', '2013-05-07 02:06:22', '2013-05-07 02:06:18');
INSERT INTO dotp_user_access_log VALUES ('320', '1', '127.0.0.1', '2013-05-06 23:06:46', '2013-05-07 03:01:43', '2013-05-07 02:33:24');
INSERT INTO dotp_user_access_log VALUES ('321', '1', '127.0.0.1', '2013-05-07 00:01:45', '2013-05-07 03:02:50', '2013-05-07 03:01:52');
INSERT INTO dotp_user_access_log VALUES ('322', '1', '127.0.0.1', '2013-05-07 00:02:51', '2013-05-07 03:09:56', '2013-05-07 03:06:52');
INSERT INTO dotp_user_access_log VALUES ('323', '1', '127.0.0.1', '2013-05-07 00:09:57', '2013-05-07 03:15:47', '2013-05-07 03:10:19');
INSERT INTO dotp_user_access_log VALUES ('324', '1', '127.0.0.1', '2013-05-07 00:15:49', '2013-05-07 03:22:59', '2013-05-07 03:17:41');
INSERT INTO dotp_user_access_log VALUES ('325', '1', '127.0.0.1', '2013-05-07 00:23:01', '2013-05-07 03:28:07', '2013-05-07 03:23:38');
INSERT INTO dotp_user_access_log VALUES ('326', '1', '127.0.0.1', '2013-05-07 00:28:08', '2013-05-07 03:34:36', '2013-05-07 03:31:15');
INSERT INTO dotp_user_access_log VALUES ('327', '1', '127.0.0.1', '2013-05-07 00:34:38', '2013-05-07 03:38:31', '2013-05-07 03:34:44');
INSERT INTO dotp_user_access_log VALUES ('328', '1', '127.0.0.1', '2013-05-07 00:38:33', '2013-05-07 03:51:22', '2013-05-07 03:46:42');
INSERT INTO dotp_user_access_log VALUES ('329', '1', '127.0.0.1', '2013-05-07 00:51:24', '2013-05-07 03:58:58', '2013-05-07 03:52:14');
INSERT INTO dotp_user_access_log VALUES ('330', '1', '127.0.0.1', '2013-05-07 00:58:59', '2013-05-07 04:06:43', '2013-05-07 03:59:47');
INSERT INTO dotp_user_access_log VALUES ('331', '1', '127.0.0.1', '2013-05-07 01:06:45', '2013-05-07 04:10:29', '2013-05-07 04:06:55');
INSERT INTO dotp_user_access_log VALUES ('332', '1', '127.0.0.1', '2013-05-07 01:10:31', '2013-05-07 04:10:55', '2013-05-07 04:10:37');
INSERT INTO dotp_user_access_log VALUES ('333', '1', '127.0.0.1', '2013-05-07 01:11:16', '2013-05-07 15:01:00', '2013-05-07 14:52:50');
INSERT INTO dotp_user_access_log VALUES ('334', '1', '127.0.0.1', '2013-05-07 12:01:03', '2013-05-07 15:07:02', '2013-05-07 15:01:55');
INSERT INTO dotp_user_access_log VALUES ('335', '1', '127.0.0.1', '2013-05-07 12:07:04', '2013-05-07 15:17:08', '2013-05-07 15:13:07');
INSERT INTO dotp_user_access_log VALUES ('336', '1', '127.0.0.1', '2013-05-07 12:17:11', '2013-05-07 15:18:45', '2013-05-07 15:17:27');
INSERT INTO dotp_user_access_log VALUES ('337', '1', '127.0.0.1', '2013-05-07 12:18:47', '2013-05-07 15:24:36', '2013-05-07 15:18:53');
INSERT INTO dotp_user_access_log VALUES ('338', '1', '127.0.0.1', '2013-05-07 12:24:37', '2013-05-07 15:31:58', '2013-05-07 15:24:50');
INSERT INTO dotp_user_access_log VALUES ('339', '1', '127.0.0.1', '2013-05-07 12:32:00', '2013-05-07 16:03:01', '2013-05-07 15:59:15');
INSERT INTO dotp_user_access_log VALUES ('340', '1', '127.0.0.1', '2013-05-07 13:03:03', '2013-05-07 18:14:50', '2013-05-07 16:03:23');
INSERT INTO dotp_user_access_log VALUES ('341', '1', '127.0.0.1', '2013-05-07 15:14:51', '2013-05-07 19:07:41', '2013-05-07 19:06:39');
INSERT INTO dotp_user_access_log VALUES ('342', '1', '127.0.0.1', '2013-05-07 16:07:44', '2013-05-07 19:14:21', '2013-05-07 19:14:20');
INSERT INTO dotp_user_access_log VALUES ('343', '1', '127.0.0.1', '2013-05-07 16:14:23', '2013-05-07 19:14:43', '2013-05-07 19:14:37');
INSERT INTO dotp_user_access_log VALUES ('344', '1', '127.0.0.1', '2013-05-07 16:15:11', '2013-05-07 19:18:32', '2013-05-07 19:15:44');
INSERT INTO dotp_user_access_log VALUES ('345', '1', '127.0.0.1', '2013-05-07 16:18:33', '2013-05-07 19:21:28', '2013-05-07 19:21:14');
INSERT INTO dotp_user_access_log VALUES ('346', '1', '127.0.0.1', '2013-05-07 16:21:29', '2013-05-07 19:22:37', '2013-05-07 19:21:42');
INSERT INTO dotp_user_access_log VALUES ('347', '1', '127.0.0.1', '2013-05-07 16:22:39', '2013-05-07 19:22:59', '2013-05-07 19:22:46');
INSERT INTO dotp_user_access_log VALUES ('348', '1', '127.0.0.1', '2013-05-07 16:28:03', '2013-05-07 19:35:10', '2013-05-07 19:29:17');
INSERT INTO dotp_user_access_log VALUES ('349', '1', '127.0.0.1', '2013-05-07 16:35:12', '2013-05-07 19:35:49', '2013-05-07 19:35:41');
INSERT INTO dotp_user_access_log VALUES ('350', '1', '127.0.0.1', '2013-05-07 16:36:22', '2013-05-07 19:37:13', '2013-05-07 19:37:08');
INSERT INTO dotp_user_access_log VALUES ('351', '1', '127.0.0.1', '2013-05-07 16:37:15', '2013-05-07 19:37:45', '2013-05-07 19:37:32');
INSERT INTO dotp_user_access_log VALUES ('352', '1', '127.0.0.1', '2013-05-07 16:37:46', '2013-05-07 19:45:24', '2013-05-07 19:37:46');
INSERT INTO dotp_user_access_log VALUES ('353', '1', '127.0.0.1', '2013-05-07 16:47:01', '2013-05-07 19:47:31', '2013-05-07 19:47:18');
INSERT INTO dotp_user_access_log VALUES ('354', '1', '127.0.0.1', '2013-05-07 16:47:50', '2013-05-07 19:51:49', '2013-05-07 19:50:33');
INSERT INTO dotp_user_access_log VALUES ('355', '1', '127.0.0.1', '2013-05-07 16:51:50', '2013-05-07 19:52:11', '2013-05-07 19:52:03');
INSERT INTO dotp_user_access_log VALUES ('356', '1', '127.0.0.1', '2013-05-07 16:55:15', '2013-05-07 19:55:19', '2013-05-07 19:55:15');
INSERT INTO dotp_user_access_log VALUES ('357', '1', '127.0.0.1', '2013-05-07 16:55:22', '2013-05-07 20:04:45', '2013-05-07 19:56:26');
INSERT INTO dotp_user_access_log VALUES ('358', '1', '127.0.0.1', '2013-05-07 17:04:47', '2013-05-07 20:06:37', '2013-05-07 20:05:20');
INSERT INTO dotp_user_access_log VALUES ('359', '1', '127.0.0.1', '2013-05-07 17:06:39', '2013-05-07 20:07:52', '2013-05-07 20:07:04');
INSERT INTO dotp_user_access_log VALUES ('360', '1', '127.0.0.1', '2013-05-07 17:07:53', '2013-05-07 20:09:38', '2013-05-07 20:08:22');
INSERT INTO dotp_user_access_log VALUES ('361', '1', '127.0.0.1', '2013-05-07 17:09:39', '2013-05-07 20:11:42', '2013-05-07 20:10:29');
INSERT INTO dotp_user_access_log VALUES ('362', '1', '127.0.0.1', '2013-05-07 17:11:44', '2013-05-07 20:14:36', '2013-05-07 20:12:17');
INSERT INTO dotp_user_access_log VALUES ('363', '1', '127.0.0.1', '2013-05-07 17:14:37', '2013-05-07 20:17:52', '2013-05-07 20:17:49');
INSERT INTO dotp_user_access_log VALUES ('364', '1', '127.0.0.1', '2013-05-07 17:17:53', '2013-05-07 20:20:13', '2013-05-07 20:18:48');
INSERT INTO dotp_user_access_log VALUES ('365', '1', '127.0.0.1', '2013-05-07 17:20:34', '2013-05-07 20:28:43', '2013-05-07 20:21:20');
INSERT INTO dotp_user_access_log VALUES ('366', '1', '127.0.0.1', '2013-05-07 17:28:46', '2013-05-07 20:30:19', '2013-05-07 20:29:27');
INSERT INTO dotp_user_access_log VALUES ('367', '1', '127.0.0.1', '2013-05-07 17:30:21', '2013-05-07 23:59:41', '2013-05-07 20:32:07');
INSERT INTO dotp_user_access_log VALUES ('368', '1', '127.0.0.1', '2013-05-07 20:59:42', '2013-05-08 00:01:40', '2013-05-08 00:00:19');
INSERT INTO dotp_user_access_log VALUES ('369', '1', '127.0.0.1', '2013-05-07 21:01:41', '2013-05-08 00:30:43', '2013-05-08 00:16:44');
INSERT INTO dotp_user_access_log VALUES ('370', '1', '127.0.0.1', '2013-05-07 21:30:46', '2013-05-08 00:48:15', '2013-05-08 00:48:09');
INSERT INTO dotp_user_access_log VALUES ('371', '1', '127.0.0.1', '2013-05-07 21:48:16', '2013-05-08 00:55:47', '2013-05-08 00:49:11');
INSERT INTO dotp_user_access_log VALUES ('372', '1', '127.0.0.1', '2013-05-07 21:55:48', '2013-05-08 01:11:57', '2013-05-08 01:01:32');
INSERT INTO dotp_user_access_log VALUES ('373', '1', '127.0.0.1', '2013-05-07 22:11:59', '2013-05-08 01:37:50', '2013-05-08 01:20:56');
INSERT INTO dotp_user_access_log VALUES ('374', '1', '127.0.0.1', '2013-05-07 22:37:51', '2013-05-08 01:51:12', '2013-05-08 01:38:56');
INSERT INTO dotp_user_access_log VALUES ('375', '1', '127.0.0.1', '2013-05-07 22:51:14', '2013-05-08 01:52:37', '2013-05-08 01:51:17');
INSERT INTO dotp_user_access_log VALUES ('376', '1', '127.0.0.1', '2013-05-07 22:52:38', '2013-05-08 01:55:03', '2013-05-08 01:52:44');
INSERT INTO dotp_user_access_log VALUES ('377', '1', '127.0.0.1', '2013-05-07 22:55:04', '2013-05-08 02:09:35', '2013-05-08 01:58:25');
INSERT INTO dotp_user_access_log VALUES ('378', '1', '127.0.0.1', '2013-05-07 23:09:37', '2013-05-08 02:10:16', '2013-05-08 02:09:50');
INSERT INTO dotp_user_access_log VALUES ('379', '1', '127.0.0.1', '2013-05-07 23:10:18', '2013-05-08 02:10:29', '2013-05-08 02:10:18');
INSERT INTO dotp_user_access_log VALUES ('380', '1', '127.0.0.1', '2013-05-07 23:10:50', '2013-05-08 02:12:29', '2013-05-08 02:11:02');
INSERT INTO dotp_user_access_log VALUES ('381', '1', '127.0.0.1', '2013-05-07 23:12:30', '2013-05-08 02:14:37', '2013-05-08 02:13:07');
INSERT INTO dotp_user_access_log VALUES ('382', '1', '127.0.0.1', '2013-05-07 23:14:38', '2013-05-08 02:21:10', '2013-05-08 02:15:18');
INSERT INTO dotp_user_access_log VALUES ('383', '1', '127.0.0.1', '2013-05-07 23:21:11', '2013-05-08 02:23:51', '2013-05-08 02:21:40');
INSERT INTO dotp_user_access_log VALUES ('384', '1', '127.0.0.1', '2013-05-07 23:23:52', '2013-05-08 02:38:40', '2013-05-08 02:32:33');
INSERT INTO dotp_user_access_log VALUES ('385', '1', '127.0.0.1', '2013-05-07 23:38:42', '2013-05-08 02:43:03', '2013-05-08 02:41:29');
INSERT INTO dotp_user_access_log VALUES ('386', '1', '127.0.0.1', '2013-05-07 23:43:05', '2013-05-08 02:48:28', '2013-05-08 02:45:02');
INSERT INTO dotp_user_access_log VALUES ('387', '1', '127.0.0.1', '2013-05-07 23:48:29', '2013-05-08 03:06:40', '2013-05-08 03:00:41');
INSERT INTO dotp_user_access_log VALUES ('388', '1', '127.0.0.1', '2013-05-08 00:06:42', '2013-05-08 03:07:39', '2013-05-08 03:07:03');
INSERT INTO dotp_user_access_log VALUES ('389', '1', '127.0.0.1', '2013-05-08 00:07:40', '2013-05-08 03:08:24', '2013-05-08 03:07:51');
INSERT INTO dotp_user_access_log VALUES ('390', '1', '127.0.0.1', '2013-05-08 00:08:25', '2013-05-08 03:09:44', '2013-05-08 03:08:38');
INSERT INTO dotp_user_access_log VALUES ('391', '1', '127.0.0.1', '2013-05-08 00:09:47', '2013-05-08 03:23:03', '2013-05-08 03:11:19');
INSERT INTO dotp_user_access_log VALUES ('392', '1', '127.0.0.1', '2013-05-08 00:23:08', '2013-05-08 03:27:42', '2013-05-08 03:23:43');
INSERT INTO dotp_user_access_log VALUES ('393', '1', '127.0.0.1', '2013-05-08 00:27:44', '2013-05-08 03:28:11', '2013-05-08 03:28:08');
INSERT INTO dotp_user_access_log VALUES ('394', '1', '127.0.0.1', '2013-05-08 00:28:52', '2013-05-08 03:31:48', '2013-05-08 03:29:04');
INSERT INTO dotp_user_access_log VALUES ('395', '1', '127.0.0.1', '2013-05-08 00:32:03', '2013-05-08 03:32:30', '2013-05-08 03:32:23');
INSERT INTO dotp_user_access_log VALUES ('396', '1', '127.0.0.1', '2013-05-08 00:34:00', '2013-05-08 03:45:57', '2013-05-08 03:42:00');
INSERT INTO dotp_user_access_log VALUES ('397', '1', '127.0.0.1', '2013-05-08 00:45:59', '2013-05-08 03:49:55', '2013-05-08 03:46:17');
INSERT INTO dotp_user_access_log VALUES ('398', '1', '127.0.0.1', '2013-05-08 00:49:56', '2013-05-08 03:52:22', '2013-05-08 03:50:58');
INSERT INTO dotp_user_access_log VALUES ('399', '1', '127.0.0.1', '2013-05-08 00:52:23', '2013-05-08 03:54:07', '2013-05-08 03:52:43');
INSERT INTO dotp_user_access_log VALUES ('400', '1', '127.0.0.1', '2013-05-08 00:54:08', '2013-05-08 04:02:01', '2013-05-08 03:56:35');
INSERT INTO dotp_user_access_log VALUES ('401', '1', '127.0.0.1', '2013-05-08 01:02:02', '2013-05-08 04:03:59', '2013-05-08 04:02:09');
INSERT INTO dotp_user_access_log VALUES ('402', '1', '127.0.0.1', '2013-05-08 01:04:00', '2013-05-08 04:07:22', '2013-05-08 04:04:05');
INSERT INTO dotp_user_access_log VALUES ('403', '1', '127.0.0.1', '2013-05-08 01:07:24', '2013-05-08 04:08:57', '2013-05-08 04:07:27');
INSERT INTO dotp_user_access_log VALUES ('404', '1', '127.0.0.1', '2013-05-08 01:08:58', '2013-05-08 04:09:55', '2013-05-08 04:09:03');
INSERT INTO dotp_user_access_log VALUES ('405', '1', '127.0.0.1', '2013-05-08 01:09:56', '2013-05-08 04:12:57', '2013-05-08 04:10:37');
INSERT INTO dotp_user_access_log VALUES ('406', '1', '127.0.0.1', '2013-05-08 01:12:58', '2013-05-08 04:16:27', '2013-05-08 04:13:07');
INSERT INTO dotp_user_access_log VALUES ('407', '1', '127.0.0.1', '2013-05-08 01:16:29', '2013-05-08 04:52:41', '2013-05-08 04:26:05');
INSERT INTO dotp_user_access_log VALUES ('408', '1', '127.0.0.1', '2013-05-08 01:52:42', '2013-05-08 04:53:32', '2013-05-08 04:52:47');
INSERT INTO dotp_user_access_log VALUES ('409', '1', '127.0.0.1', '2013-05-08 01:53:33', '2013-05-08 05:00:11', '2013-05-08 04:53:37');
INSERT INTO dotp_user_access_log VALUES ('410', '1', '127.0.0.1', '2013-05-08 02:00:12', '2013-05-08 05:01:34', '2013-05-08 05:00:16');
INSERT INTO dotp_user_access_log VALUES ('411', '1', '127.0.0.1', '2013-05-08 02:01:35', '2013-05-08 14:49:52', '2013-05-08 05:01:38');
INSERT INTO dotp_user_access_log VALUES ('412', '1', '127.0.0.1', '2013-05-08 11:50:02', '2013-05-08 14:56:04', '2013-05-08 14:50:18');
INSERT INTO dotp_user_access_log VALUES ('413', '1', '127.0.0.1', '2013-05-08 11:56:06', '2013-05-08 14:58:11', '2013-05-08 14:56:20');
INSERT INTO dotp_user_access_log VALUES ('414', '1', '127.0.0.1', '2013-05-08 11:58:13', '2013-05-08 14:58:56', '2013-05-08 14:58:19');
INSERT INTO dotp_user_access_log VALUES ('415', '1', '127.0.0.1', '2013-05-08 11:58:58', '2013-05-08 14:59:59', '2013-05-08 14:59:04');
INSERT INTO dotp_user_access_log VALUES ('416', '1', '127.0.0.1', '2013-05-08 12:00:00', '2013-05-08 15:06:58', '2013-05-08 15:00:08');
INSERT INTO dotp_user_access_log VALUES ('417', '1', '127.0.0.1', '2013-05-08 12:06:59', '2013-05-08 15:08:07', '2013-05-08 15:07:05');
INSERT INTO dotp_user_access_log VALUES ('418', '1', '127.0.0.1', '2013-05-08 12:08:09', '2013-05-08 15:11:31', '2013-05-08 15:08:13');
INSERT INTO dotp_user_access_log VALUES ('419', '1', '127.0.0.1', '2013-05-08 12:11:33', '2013-05-08 15:12:33', '2013-05-08 15:11:37');
INSERT INTO dotp_user_access_log VALUES ('420', '1', '127.0.0.1', '2013-05-08 12:12:34', '2013-05-08 15:13:18', '2013-05-08 15:13:16');
INSERT INTO dotp_user_access_log VALUES ('421', '1', '127.0.0.1', '2013-05-08 12:13:19', '2013-05-08 15:18:24', '2013-05-08 15:13:24');
INSERT INTO dotp_user_access_log VALUES ('422', '1', '127.0.0.1', '2013-05-08 12:18:26', '2013-05-08 15:44:30', '2013-05-08 15:38:54');
INSERT INTO dotp_user_access_log VALUES ('423', '1', '127.0.0.1', '2013-05-08 12:44:31', '2013-05-08 15:44:44', '2013-05-08 15:44:32');
INSERT INTO dotp_user_access_log VALUES ('424', '1', '127.0.0.1', '2013-05-08 12:44:46', '2013-05-08 16:11:28', '2013-05-08 15:50:18');
INSERT INTO dotp_user_access_log VALUES ('425', '1', '127.0.0.1', '2013-05-08 13:11:31', '2013-05-08 16:15:46', '2013-05-08 16:15:35');
INSERT INTO dotp_user_access_log VALUES ('426', '1', '127.0.0.1', '2013-05-08 13:15:48', '2013-05-08 16:17:40', '2013-05-08 16:17:35');
INSERT INTO dotp_user_access_log VALUES ('427', '1', '127.0.0.1', '2013-05-08 13:17:42', '2013-05-08 16:18:41', '2013-05-08 16:17:57');
INSERT INTO dotp_user_access_log VALUES ('428', '1', '127.0.0.1', '2013-05-08 13:19:34', '2013-05-08 16:20:09', '2013-05-08 16:20:05');
INSERT INTO dotp_user_access_log VALUES ('429', '1', '127.0.0.1', '2013-05-08 13:20:10', '2013-05-08 16:31:07', '2013-05-08 16:22:07');
INSERT INTO dotp_user_access_log VALUES ('430', '1', '127.0.0.1', '2013-05-08 13:32:06', '2013-05-08 17:10:29', '2013-05-08 16:58:40');
INSERT INTO dotp_user_access_log VALUES ('431', '1', '127.0.0.1', '2013-05-08 14:10:30', '2013-05-08 17:11:43', '2013-05-08 17:11:02');
INSERT INTO dotp_user_access_log VALUES ('432', '1', '127.0.0.1', '2013-05-08 14:11:44', '2013-05-08 17:16:06', '2013-05-08 17:11:56');
INSERT INTO dotp_user_access_log VALUES ('433', '1', '127.0.0.1', '2013-05-08 14:16:08', '2013-05-08 17:18:30', '2013-05-08 17:16:25');
INSERT INTO dotp_user_access_log VALUES ('434', '1', '127.0.0.1', '2013-05-08 14:18:31', '2013-05-08 17:30:44', '2013-05-08 17:27:07');
INSERT INTO dotp_user_access_log VALUES ('435', '1', '127.0.0.1', '2013-05-08 14:30:45', '2013-05-08 18:51:39', '2013-05-08 18:43:18');
INSERT INTO dotp_user_access_log VALUES ('436', '1', '127.0.0.1', '2013-05-08 15:51:40', '2013-05-08 18:52:46', '2013-05-08 18:51:52');
INSERT INTO dotp_user_access_log VALUES ('437', '1', '127.0.0.1', '2013-05-08 15:52:47', '2013-05-08 18:54:31', '2013-05-08 18:52:56');
INSERT INTO dotp_user_access_log VALUES ('438', '1', '127.0.0.1', '2013-05-08 15:54:32', '2013-05-08 19:58:04', '2013-05-08 18:54:41');
INSERT INTO dotp_user_access_log VALUES ('439', '1', '127.0.0.1', '2013-05-08 16:58:05', '2013-05-08 21:43:55', '2013-05-08 21:25:42');
INSERT INTO dotp_user_access_log VALUES ('440', '1', '127.0.0.1', '2013-05-08 18:43:57', '2013-05-08 21:45:37', '2013-05-08 21:44:11');
INSERT INTO dotp_user_access_log VALUES ('441', '1', '127.0.0.1', '2013-05-08 18:45:39', '2013-05-08 21:52:10', '2013-05-08 21:50:51');
INSERT INTO dotp_user_access_log VALUES ('442', '1', '127.0.0.1', '2013-05-08 18:52:12', '2013-05-08 21:54:43', '2013-05-08 21:52:22');
INSERT INTO dotp_user_access_log VALUES ('443', '1', '127.0.0.1', '2013-05-08 18:54:44', '2013-05-08 22:12:24', '2013-05-08 21:54:55');
INSERT INTO dotp_user_access_log VALUES ('444', '1', '127.0.0.1', '2013-05-08 19:12:26', '2013-05-08 22:14:11', '2013-05-08 22:12:42');
INSERT INTO dotp_user_access_log VALUES ('445', '1', '127.0.0.1', '2013-05-08 19:14:13', '2013-05-08 22:53:44', '2013-05-08 22:43:29');
INSERT INTO dotp_user_access_log VALUES ('446', '1', '127.0.0.1', '2013-05-08 19:53:45', '2013-05-08 23:31:24', '2013-05-08 22:53:56');
INSERT INTO dotp_user_access_log VALUES ('447', '1', '127.0.0.1', '2013-05-08 20:31:25', '2013-05-08 23:34:13', '2013-05-08 23:31:30');
INSERT INTO dotp_user_access_log VALUES ('448', '1', '127.0.0.1', '2013-05-08 20:34:15', '2013-05-08 23:50:26', '2013-05-08 23:34:21');
INSERT INTO dotp_user_access_log VALUES ('449', '1', '127.0.0.1', '2013-05-08 20:50:27', '2013-05-08 23:53:56', '2013-05-08 23:50:32');
INSERT INTO dotp_user_access_log VALUES ('450', '1', '127.0.0.1', '2013-05-08 20:53:58', '2013-05-09 00:03:28', '2013-05-08 23:54:05');
INSERT INTO dotp_user_access_log VALUES ('451', '1', '127.0.0.1', '2013-05-08 21:03:29', '2013-05-09 00:16:03', '2013-05-09 00:07:05');
INSERT INTO dotp_user_access_log VALUES ('452', '1', '127.0.0.1', '2013-05-08 21:16:05', '2013-05-09 00:28:06', '2013-05-09 00:16:14');
INSERT INTO dotp_user_access_log VALUES ('453', '1', '127.0.0.1', '2013-05-08 21:28:08', '2013-05-09 00:34:49', '2013-05-09 00:28:15');
INSERT INTO dotp_user_access_log VALUES ('454', '1', '127.0.0.1', '2013-05-08 21:34:50', '2013-05-09 00:49:03', '2013-05-09 00:37:45');
INSERT INTO dotp_user_access_log VALUES ('455', '1', '127.0.0.1', '2013-05-08 21:49:03', '2013-05-09 00:52:02', '2013-05-09 00:49:12');
INSERT INTO dotp_user_access_log VALUES ('456', '1', '127.0.0.1', '2013-05-08 21:52:03', '2013-05-09 00:53:26', '2013-05-09 00:52:10');
INSERT INTO dotp_user_access_log VALUES ('457', '1', '127.0.0.1', '2013-05-08 21:53:27', '2013-05-09 00:55:30', '2013-05-09 00:53:34');
INSERT INTO dotp_user_access_log VALUES ('458', '1', '127.0.0.1', '2013-05-08 21:55:32', '2013-05-09 00:58:26', '2013-05-09 00:55:38');
INSERT INTO dotp_user_access_log VALUES ('459', '1', '127.0.0.1', '2013-05-08 21:58:26', '2013-05-09 01:42:08', '2013-05-09 01:38:06');
INSERT INTO dotp_user_access_log VALUES ('460', '1', '127.0.0.1', '2013-05-08 22:42:09', '2013-05-09 01:54:29', '2013-05-09 01:51:15');
INSERT INTO dotp_user_access_log VALUES ('461', '1', '127.0.0.1', '2013-05-08 22:54:32', '2013-05-09 01:57:19', '2013-05-09 01:54:40');
INSERT INTO dotp_user_access_log VALUES ('462', '1', '127.0.0.1', '2013-05-08 22:57:21', '2013-05-09 02:18:53', '2013-05-09 02:13:13');
INSERT INTO dotp_user_access_log VALUES ('463', '1', '127.0.0.1', '2013-05-08 23:18:58', '2013-05-09 02:20:22', '2013-05-09 02:19:07');
INSERT INTO dotp_user_access_log VALUES ('464', '1', '127.0.0.1', '2013-05-08 23:20:23', '2013-05-09 02:22:00', '2013-05-09 02:20:34');
INSERT INTO dotp_user_access_log VALUES ('465', '1', '127.0.0.1', '2013-05-08 23:22:01', '2013-05-09 02:23:00', '2013-05-09 02:22:11');
INSERT INTO dotp_user_access_log VALUES ('466', '1', '127.0.0.1', '2013-05-08 23:23:01', '2013-05-09 02:23:30', '2013-05-09 02:23:28');
INSERT INTO dotp_user_access_log VALUES ('467', '1', '127.0.0.1', '2013-05-08 23:24:27', '2013-05-09 02:42:05', '2013-05-09 02:41:45');
INSERT INTO dotp_user_access_log VALUES ('468', '1', '127.0.0.1', '2013-05-08 23:42:26', '2013-05-09 02:42:43', '2013-05-09 02:42:26');
INSERT INTO dotp_user_access_log VALUES ('469', '1', '127.0.0.1', '2013-05-08 23:42:44', '2013-05-09 02:52:53', '2013-05-09 02:51:40');
INSERT INTO dotp_user_access_log VALUES ('470', '1', '127.0.0.1', '2013-05-08 23:52:54', '2013-05-09 03:12:42', '2013-05-09 03:09:54');
INSERT INTO dotp_user_access_log VALUES ('471', '1', '127.0.0.1', '2013-05-09 00:12:44', '2013-05-09 03:17:54', '2013-05-09 03:12:49');
INSERT INTO dotp_user_access_log VALUES ('472', '1', '127.0.0.1', '2013-05-09 00:17:58', '2013-05-09 03:35:50', '2013-05-09 03:26:30');
INSERT INTO dotp_user_access_log VALUES ('473', '1', '127.0.0.1', '2013-05-09 00:35:52', '2013-05-09 03:40:12', '2013-05-09 03:38:59');
INSERT INTO dotp_user_access_log VALUES ('474', '1', '127.0.0.1', '2013-05-09 00:40:13', '2013-05-09 03:42:00', '2013-05-09 03:40:20');
INSERT INTO dotp_user_access_log VALUES ('475', '1', '127.0.0.1', '2013-05-09 00:42:01', '2013-05-09 03:43:09', '2013-05-09 03:42:10');
INSERT INTO dotp_user_access_log VALUES ('476', '1', '127.0.0.1', '2013-05-09 00:43:10', '2013-05-09 04:29:18', '2013-05-09 04:25:57');
INSERT INTO dotp_user_access_log VALUES ('477', '1', '127.0.0.1', '2013-05-09 01:29:19', '2013-05-09 04:36:19', '2013-05-09 04:34:32');
INSERT INTO dotp_user_access_log VALUES ('478', '1', '127.0.0.1', '2013-05-09 01:37:09', '2013-05-09 04:41:06', '2013-05-09 04:38:31');
INSERT INTO dotp_user_access_log VALUES ('479', '1', '127.0.0.1', '2013-05-09 01:41:08', '2013-05-09 04:44:48', '2013-05-09 04:42:37');
INSERT INTO dotp_user_access_log VALUES ('480', '1', '127.0.0.1', '2013-05-09 01:44:49', '2013-05-09 15:24:38', '2013-05-09 15:23:29');
INSERT INTO dotp_user_access_log VALUES ('481', '1', '127.0.0.1', '2013-05-09 12:24:40', '2013-05-09 15:28:43', '2013-05-09 15:27:08');
INSERT INTO dotp_user_access_log VALUES ('482', '1', '127.0.0.1', '2013-05-09 12:28:44', '2013-05-09 15:30:18', '2013-05-09 15:29:33');
INSERT INTO dotp_user_access_log VALUES ('483', '1', '127.0.0.1', '2013-05-09 12:30:19', '2013-05-09 15:35:12', '2013-05-09 15:30:59');
INSERT INTO dotp_user_access_log VALUES ('484', '1', '127.0.0.1', '2013-05-09 12:35:13', '2013-05-09 15:41:42', '2013-05-09 15:37:11');
INSERT INTO dotp_user_access_log VALUES ('485', '1', '127.0.0.1', '2013-05-09 12:41:44', '2013-05-09 15:50:54', '2013-05-09 15:43:25');
INSERT INTO dotp_user_access_log VALUES ('486', '1', '127.0.0.1', '2013-05-09 12:50:55', '2013-05-09 15:51:45', '2013-05-09 15:51:04');
INSERT INTO dotp_user_access_log VALUES ('487', '1', '127.0.0.1', '2013-05-09 12:51:46', '2013-05-09 15:52:57', '2013-05-09 15:52:02');
INSERT INTO dotp_user_access_log VALUES ('488', '1', '127.0.0.1', '2013-05-09 12:52:58', '2013-05-09 15:53:30', '2013-05-09 15:53:06');
INSERT INTO dotp_user_access_log VALUES ('489', '1', '127.0.0.1', '2013-05-09 12:53:47', '2013-05-09 16:13:01', '2013-05-09 15:58:34');
INSERT INTO dotp_user_access_log VALUES ('490', '1', '127.0.0.1', '2013-05-09 13:13:03', '2013-05-09 16:24:59', '2013-05-09 16:14:22');
INSERT INTO dotp_user_access_log VALUES ('491', '1', '127.0.0.1', '2013-05-09 13:25:02', '2013-05-09 16:27:58', '2013-05-09 16:25:55');
INSERT INTO dotp_user_access_log VALUES ('492', '1', '127.0.0.1', '2013-05-09 13:27:59', '2013-05-09 16:34:59', '2013-05-09 16:28:09');
INSERT INTO dotp_user_access_log VALUES ('493', '1', '127.0.0.1', '2013-05-09 13:35:00', '2013-05-09 16:35:30', '2013-05-09 16:35:20');
INSERT INTO dotp_user_access_log VALUES ('494', '1', '127.0.0.1', '2013-05-09 13:35:31', '2013-05-09 16:44:24', '2013-05-09 16:39:17');
INSERT INTO dotp_user_access_log VALUES ('495', '1', '127.0.0.1', '2013-05-09 13:44:25', '2013-05-09 16:47:45', '2013-05-09 16:44:50');
INSERT INTO dotp_user_access_log VALUES ('496', '1', '127.0.0.1', '2013-05-09 13:47:46', '2013-05-09 16:52:03', '2013-05-09 16:48:13');
INSERT INTO dotp_user_access_log VALUES ('497', '1', '127.0.0.1', '2013-05-09 13:52:23', '2013-05-09 16:55:05', '2013-05-09 16:53:18');
INSERT INTO dotp_user_access_log VALUES ('498', '1', '127.0.0.1', '2013-05-09 13:55:06', '2013-05-09 16:56:32', '2013-05-09 16:55:13');
INSERT INTO dotp_user_access_log VALUES ('499', '1', '127.0.0.1', '2013-05-09 13:56:33', '2013-05-09 17:00:45', '2013-05-09 16:57:22');
INSERT INTO dotp_user_access_log VALUES ('500', '1', '127.0.0.1', '2013-05-09 14:02:30', '2013-05-09 17:07:21', '2013-05-09 17:06:48');
INSERT INTO dotp_user_access_log VALUES ('501', '1', '127.0.0.1', '2013-05-09 14:07:22', '2013-05-09 17:11:56', '2013-05-09 17:07:44');
INSERT INTO dotp_user_access_log VALUES ('502', '1', '127.0.0.1', '2013-05-09 14:11:57', '2013-05-09 17:13:45', '2013-05-09 17:12:11');
INSERT INTO dotp_user_access_log VALUES ('503', '1', '127.0.0.1', '2013-05-09 14:13:46', '2013-05-09 17:17:50', '2013-05-09 17:15:29');
INSERT INTO dotp_user_access_log VALUES ('504', '1', '127.0.0.1', '2013-05-09 14:17:52', '2013-05-09 17:27:14', '2013-05-09 17:19:31');
INSERT INTO dotp_user_access_log VALUES ('505', '1', '127.0.0.1', '2013-05-09 14:27:16', '2013-05-09 17:35:11', '2013-05-09 17:28:21');
INSERT INTO dotp_user_access_log VALUES ('506', '1', '127.0.0.1', '2013-05-09 14:35:13', '2013-05-09 17:37:42', '2013-05-09 17:37:01');
INSERT INTO dotp_user_access_log VALUES ('507', '1', '127.0.0.1', '2013-05-09 14:37:43', '2013-05-09 17:38:44', '2013-05-09 17:37:52');
INSERT INTO dotp_user_access_log VALUES ('508', '1', '127.0.0.1', '2013-05-09 14:38:45', '2013-05-09 17:39:32', '2013-05-09 17:38:56');
INSERT INTO dotp_user_access_log VALUES ('509', '1', '127.0.0.1', '2013-05-09 14:39:33', '2013-05-09 17:41:00', '2013-05-09 17:39:52');
INSERT INTO dotp_user_access_log VALUES ('510', '1', '127.0.0.1', '2013-05-09 14:41:01', '2013-05-09 17:41:38', '2013-05-09 17:41:11');
INSERT INTO dotp_user_access_log VALUES ('511', '1', '127.0.0.1', '2013-05-09 14:44:31', '2013-05-09 17:45:26', '2013-05-09 17:44:41');
INSERT INTO dotp_user_access_log VALUES ('512', '1', '127.0.0.1', '2013-05-09 14:45:27', '2013-05-09 17:52:08', '2013-05-09 17:50:32');
INSERT INTO dotp_user_access_log VALUES ('513', '1', '127.0.0.1', '2013-05-09 14:52:09', '2013-05-09 17:57:54', '2013-05-09 17:52:39');
INSERT INTO dotp_user_access_log VALUES ('514', '1', '127.0.0.1', '2013-05-09 14:58:48', '2013-05-09 18:16:35', '2013-05-09 18:15:21');
INSERT INTO dotp_user_access_log VALUES ('515', '1', '127.0.0.1', '2013-05-09 15:16:36', '2013-05-09 23:05:09', '2013-05-09 23:04:55');
INSERT INTO dotp_user_access_log VALUES ('516', '1', '127.0.0.1', '2013-05-09 20:10:03', '2013-05-10 01:50:07', '2013-05-10 01:45:21');
INSERT INTO dotp_user_access_log VALUES ('517', '1', '127.0.0.1', '2013-05-09 22:50:10', '2013-05-10 01:54:22', '2013-05-10 01:50:25');
INSERT INTO dotp_user_access_log VALUES ('518', '1', '127.0.0.1', '2013-05-09 22:54:23', '2013-05-10 02:04:47', '2013-05-10 01:56:47');
INSERT INTO dotp_user_access_log VALUES ('519', '1', '127.0.0.1', '2013-05-09 23:04:48', '2013-05-10 02:08:16', '2013-05-10 02:07:16');
INSERT INTO dotp_user_access_log VALUES ('520', '1', '127.0.0.1', '2013-05-09 23:08:17', '2013-05-10 02:09:32', '2013-05-10 02:09:24');
INSERT INTO dotp_user_access_log VALUES ('521', '1', '127.0.0.1', '2013-05-09 23:09:53', '2013-05-10 02:15:09', '2013-05-10 02:10:11');
INSERT INTO dotp_user_access_log VALUES ('522', '1', '127.0.0.1', '2013-05-09 23:15:10', '2013-05-10 02:16:08', '2013-05-10 02:15:21');
INSERT INTO dotp_user_access_log VALUES ('523', '1', '127.0.0.1', '2013-05-09 23:16:10', '2013-05-10 02:18:04', '2013-05-10 02:18:00');
INSERT INTO dotp_user_access_log VALUES ('524', '1', '127.0.0.1', '2013-05-09 23:18:05', '2013-05-10 02:19:29', '2013-05-10 02:18:32');
INSERT INTO dotp_user_access_log VALUES ('525', '1', '127.0.0.1', '2013-05-09 23:19:30', '2013-05-10 02:20:32', '2013-05-10 02:19:49');
INSERT INTO dotp_user_access_log VALUES ('526', '1', '127.0.0.1', '2013-05-09 23:20:33', '2013-05-10 02:26:25', '2013-05-10 02:20:49');
INSERT INTO dotp_user_access_log VALUES ('527', '1', '127.0.0.1', '2013-05-09 23:26:28', '2013-05-10 03:56:49', '2013-05-10 03:31:15');
INSERT INTO dotp_user_access_log VALUES ('528', '1', '127.0.0.1', '2013-05-10 00:56:51', '2013-05-10 03:59:13', '2013-05-10 03:58:20');
INSERT INTO dotp_user_access_log VALUES ('529', '1', '127.0.0.1', '2013-05-10 01:03:26', '2013-05-10 04:14:23', '2013-05-10 04:10:37');
INSERT INTO dotp_user_access_log VALUES ('530', '1', '127.0.0.1', '2013-05-10 01:14:24', '2013-05-10 04:15:37', '2013-05-10 04:14:30');
INSERT INTO dotp_user_access_log VALUES ('531', '1', '127.0.0.1', '2013-05-10 01:15:38', '2013-05-10 04:21:55', '2013-05-10 04:16:27');
INSERT INTO dotp_user_access_log VALUES ('532', '1', '127.0.0.1', '2013-05-10 01:21:56', '2013-05-10 04:26:10', '2013-05-10 04:25:02');
INSERT INTO dotp_user_access_log VALUES ('533', '1', '127.0.0.1', '2013-05-10 01:26:11', '2013-05-10 04:27:11', '2013-05-10 04:26:18');
INSERT INTO dotp_user_access_log VALUES ('534', '1', '127.0.0.1', '2013-05-10 01:27:12', '2013-05-10 04:28:00', '2013-05-10 04:27:17');
INSERT INTO dotp_user_access_log VALUES ('535', '1', '127.0.0.1', '2013-05-10 01:36:45', '2013-05-10 04:37:25', '2013-05-10 04:36:50');
INSERT INTO dotp_user_access_log VALUES ('536', '1', '127.0.0.1', '2013-05-10 01:37:26', '2013-05-10 04:38:56', '2013-05-10 04:38:49');
INSERT INTO dotp_user_access_log VALUES ('537', '1', '127.0.0.1', '2013-05-10 01:38:57', '2013-05-10 04:56:15', '2013-05-10 04:40:06');
INSERT INTO dotp_user_access_log VALUES ('538', '1', '127.0.0.1', '2013-05-10 01:56:16', '2013-05-10 04:57:39', '2013-05-10 04:56:38');
INSERT INTO dotp_user_access_log VALUES ('539', '1', '127.0.0.1', '2013-05-10 01:57:41', '2013-05-10 05:04:01', '2013-05-10 04:58:38');
INSERT INTO dotp_user_access_log VALUES ('540', '1', '127.0.0.1', '2013-05-10 02:04:02', '2013-05-10 05:04:22', '2013-05-10 05:04:12');
INSERT INTO dotp_user_access_log VALUES ('541', '1', '127.0.0.1', '2013-05-10 02:05:08', '2013-05-10 05:05:28', '2013-05-10 05:05:14');
INSERT INTO dotp_user_access_log VALUES ('542', '1', '127.0.0.1', '2013-05-10 20:01:37', '2013-05-10 23:10:43', '2013-05-10 23:10:41');
INSERT INTO dotp_user_access_log VALUES ('543', '1', '127.0.0.1', '2013-05-10 20:19:36', '2013-05-11 03:30:04', '2013-05-11 02:37:44');
INSERT INTO dotp_user_access_log VALUES ('544', '1', '127.0.0.1', '2013-05-11 00:30:06', '2013-05-11 03:37:03', '2013-05-11 03:33:09');
INSERT INTO dotp_user_access_log VALUES ('545', '1', '127.0.0.1', '2013-05-11 00:37:04', '2013-05-11 03:41:29', '2013-05-11 03:39:25');
INSERT INTO dotp_user_access_log VALUES ('546', '1', '127.0.0.1', '2013-05-11 00:41:30', '2013-05-11 03:44:21', '2013-05-11 03:42:02');
INSERT INTO dotp_user_access_log VALUES ('547', '1', '127.0.0.1', '2013-05-11 00:44:23', '2013-05-11 21:50:23', '2013-05-11 21:49:24');
INSERT INTO dotp_user_access_log VALUES ('548', '1', '127.0.0.1', '2013-05-11 18:50:24', '2013-05-11 21:50:43', '2013-05-11 21:50:36');
INSERT INTO dotp_user_access_log VALUES ('549', '1', '127.0.0.1', '2013-05-11 18:57:39', '2013-05-11 22:08:17', '2013-05-11 22:07:44');
INSERT INTO dotp_user_access_log VALUES ('550', '1', '127.0.0.1', '2013-05-11 19:08:20', '2013-05-11 22:22:13', '2013-05-11 22:09:21');
INSERT INTO dotp_user_access_log VALUES ('551', '1', '127.0.0.1', '2013-05-11 19:22:36', '2013-05-11 22:27:50', '2013-05-11 22:27:45');
INSERT INTO dotp_user_access_log VALUES ('552', '1', '127.0.0.1', '2013-05-11 19:54:15', '2013-05-12 00:06:01', '2013-05-11 22:55:30');
INSERT INTO dotp_user_access_log VALUES ('553', '1', '127.0.0.1', '2013-05-11 21:06:04', '2013-05-12 00:06:43', '2013-05-12 00:06:21');
INSERT INTO dotp_user_access_log VALUES ('554', '1', '127.0.0.1', '2013-05-11 21:06:45', '2013-05-12 00:14:20', '2013-05-12 00:14:05');
INSERT INTO dotp_user_access_log VALUES ('555', '1', '127.0.0.1', '2013-05-11 21:14:21', '2013-05-12 00:16:53', '2013-05-12 00:16:49');
INSERT INTO dotp_user_access_log VALUES ('556', '1', '127.0.0.1', '2013-05-11 21:17:22', '2013-05-12 02:07:25', '2013-05-12 01:59:11');
INSERT INTO dotp_user_access_log VALUES ('557', '1', '127.0.0.1', '2013-05-11 23:07:28', '2013-05-12 02:22:15', '2013-05-12 02:22:11');
INSERT INTO dotp_user_access_log VALUES ('558', '1', '127.0.0.1', '2013-05-11 23:22:16', '2013-05-12 02:23:53', '2013-05-12 02:22:31');
INSERT INTO dotp_user_access_log VALUES ('559', '1', '127.0.0.1', '2013-05-11 23:23:55', '2013-05-12 02:26:30', '2013-05-12 02:24:04');
INSERT INTO dotp_user_access_log VALUES ('560', '1', '127.0.0.1', '2013-05-11 23:26:32', '2013-05-12 02:29:52', '2013-05-12 02:26:44');
INSERT INTO dotp_user_access_log VALUES ('561', '1', '127.0.0.1', '2013-05-11 23:29:55', '2013-05-12 02:30:34', '2013-05-12 02:30:03');
INSERT INTO dotp_user_access_log VALUES ('562', '1', '127.0.0.1', '2013-05-12 00:00:17', '2013-05-12 17:03:48', '2013-05-12 17:00:35');
INSERT INTO dotp_user_access_log VALUES ('563', '1', '127.0.0.1', '2013-05-12 14:03:53', '2013-05-12 18:46:17', '2013-05-12 18:44:30');
INSERT INTO dotp_user_access_log VALUES ('564', '1', '127.0.0.1', '2013-05-12 15:46:19', '2013-05-12 18:47:48', '2013-05-12 18:46:26');
INSERT INTO dotp_user_access_log VALUES ('565', '1', '127.0.0.1', '2013-05-12 15:47:50', '2013-05-12 18:48:17', '2013-05-12 18:48:00');
INSERT INTO dotp_user_access_log VALUES ('566', '1', '127.0.0.1', '2013-05-12 15:50:27', '2013-05-12 18:55:49', '2013-05-12 18:50:39');
INSERT INTO dotp_user_access_log VALUES ('567', '1', '127.0.0.1', '2013-05-12 15:56:09', '2013-05-12 18:58:00', '2013-05-12 18:57:12');
INSERT INTO dotp_user_access_log VALUES ('568', '1', '127.0.0.1', '2013-05-12 15:58:02', '2013-05-12 18:58:22', '2013-05-12 18:58:12');
INSERT INTO dotp_user_access_log VALUES ('569', '1', '127.0.0.1', '2013-05-12 15:58:23', '2013-05-12 18:59:49', '2013-05-12 18:58:31');
INSERT INTO dotp_user_access_log VALUES ('570', '1', '127.0.0.1', '2013-05-12 15:59:50', '2013-05-12 19:00:44', '2013-05-12 18:59:58');
INSERT INTO dotp_user_access_log VALUES ('571', '1', '127.0.0.1', '2013-05-12 16:00:46', '2013-05-12 19:01:41', '2013-05-12 19:00:53');
INSERT INTO dotp_user_access_log VALUES ('572', '1', '127.0.0.1', '2013-05-12 16:01:43', '2013-05-12 19:13:47', '2013-05-12 19:08:36');
INSERT INTO dotp_user_access_log VALUES ('573', '1', '127.0.0.1', '2013-05-12 16:13:48', '2013-05-12 20:20:18', '2013-05-12 19:20:01');
INSERT INTO dotp_user_access_log VALUES ('574', '1', '127.0.0.1', '2013-05-12 17:20:20', '2013-05-12 20:25:20', '2013-05-12 20:21:32');
INSERT INTO dotp_user_access_log VALUES ('575', '1', '127.0.0.1', '2013-05-12 17:25:20', '2013-05-12 20:40:09', '2013-05-12 20:40:02');
INSERT INTO dotp_user_access_log VALUES ('576', '1', '127.0.0.1', '2013-05-12 17:41:54', '2013-05-12 20:42:42', '2013-05-12 20:41:54');
INSERT INTO dotp_user_access_log VALUES ('577', '1', '127.0.0.1', '2013-05-12 17:42:43', '2013-05-12 20:51:41', '2013-05-12 20:51:01');
INSERT INTO dotp_user_access_log VALUES ('578', '1', '127.0.0.1', '2013-05-12 17:51:42', '2013-05-12 21:01:56', '2013-05-12 20:52:52');
INSERT INTO dotp_user_access_log VALUES ('579', '1', '127.0.0.1', '2013-05-12 18:01:57', '2013-05-12 21:23:55', '2013-05-12 21:03:01');
INSERT INTO dotp_user_access_log VALUES ('580', '1', '127.0.0.1', '2013-05-12 18:23:57', '2013-05-12 21:28:23', '2013-05-12 21:25:02');
INSERT INTO dotp_user_access_log VALUES ('581', '1', '127.0.0.1', '2013-05-12 18:28:25', '2013-05-12 21:31:15', '2013-05-12 21:30:55');
INSERT INTO dotp_user_access_log VALUES ('582', '1', '127.0.0.1', '2013-05-12 18:31:16', '2013-05-12 21:32:55', '2013-05-12 21:32:53');
INSERT INTO dotp_user_access_log VALUES ('583', '1', '127.0.0.1', '2013-05-12 18:33:14', '2013-05-12 21:34:36', '2013-05-12 21:33:32');
INSERT INTO dotp_user_access_log VALUES ('584', '1', '127.0.0.1', '2013-05-12 18:34:37', '2013-05-12 21:39:25', '2013-05-12 21:34:52');
INSERT INTO dotp_user_access_log VALUES ('585', '1', '127.0.0.1', '2013-05-12 18:39:26', '2013-05-15 00:17:34', '2013-05-15 00:17:05');
INSERT INTO dotp_user_access_log VALUES ('586', '1', '127.0.0.1', '2013-05-14 21:17:36', '2013-05-15 00:18:41', '2013-05-15 00:18:36');
INSERT INTO dotp_user_access_log VALUES ('587', '1', '127.0.0.1', '2013-05-14 21:18:42', '2013-05-15 00:19:54', '2013-05-15 00:18:49');
INSERT INTO dotp_user_access_log VALUES ('588', '1', '127.0.0.1', '2013-05-14 21:21:00', '2013-05-15 00:21:26', '2013-05-15 00:21:10');
INSERT INTO dotp_user_access_log VALUES ('589', '1', '127.0.0.1', '2013-05-14 21:21:28', '2013-05-15 00:21:36', '2013-05-15 00:21:28');
INSERT INTO dotp_user_access_log VALUES ('590', '1', '127.0.0.1', '2013-05-14 21:21:37', '2013-05-15 04:38:10', '2013-05-15 04:37:44');
INSERT INTO dotp_user_access_log VALUES ('591', '1', '127.0.0.1', '2013-05-15 01:39:35', '2013-05-15 04:45:47', '2013-05-15 04:41:55');
INSERT INTO dotp_user_access_log VALUES ('592', '1', '127.0.0.1', '2013-05-15 01:45:49', '2013-05-15 04:50:54', '2013-05-15 04:49:46');
INSERT INTO dotp_user_access_log VALUES ('593', '1', '127.0.0.1', '2013-05-15 01:50:56', '2013-05-15 05:00:29', '2013-05-15 04:51:09');
INSERT INTO dotp_user_access_log VALUES ('594', '1', '127.0.0.1', '2013-05-15 02:00:30', '2013-05-15 05:01:45', '2013-05-15 05:01:01');
INSERT INTO dotp_user_access_log VALUES ('595', '1', '127.0.0.1', '2013-05-15 02:03:33', '2013-05-15 16:14:40', '2013-05-15 05:05:09');
INSERT INTO dotp_user_access_log VALUES ('596', '1', '127.0.0.1', '2013-05-15 13:14:52', '2013-05-15 16:22:07', '2013-05-15 16:17:11');
INSERT INTO dotp_user_access_log VALUES ('597', '1', '127.0.0.1', '2013-05-15 13:22:11', '2013-05-15 16:54:09', '2013-05-15 16:37:08');
INSERT INTO dotp_user_access_log VALUES ('598', '1', '127.0.0.1', '2013-05-15 13:54:12', '2013-05-16 00:10:11', '2013-05-16 00:10:00');

-- ----------------------------
-- Table structure for `dotp_user_events`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_user_events`;
CREATE TABLE `dotp_user_events` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  KEY `uek1` (`user_id`,`event_id`),
  KEY `uek2` (`event_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_user_events
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_user_preferences`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_user_preferences`;
CREATE TABLE `dotp_user_preferences` (
  `pref_user` varchar(12) NOT NULL DEFAULT '',
  `pref_name` varchar(72) NOT NULL DEFAULT '',
  `pref_value` varchar(32) NOT NULL DEFAULT '',
  KEY `pref_user` (`pref_user`,`pref_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_user_preferences
-- ----------------------------
INSERT INTO dotp_user_preferences VALUES ('0', 'USERFORMAT', 'user');
INSERT INTO dotp_user_preferences VALUES ('0', 'LOCALE', 'pt_br');
INSERT INTO dotp_user_preferences VALUES ('0', 'TABVIEW', '0');
INSERT INTO dotp_user_preferences VALUES ('0', 'SHDATEFORMAT', '%d/%m/%Y');
INSERT INTO dotp_user_preferences VALUES ('0', 'TIMEFORMAT', '%I:%M %p');
INSERT INTO dotp_user_preferences VALUES ('0', 'CURRENCYFORM', 'pt_br');
INSERT INTO dotp_user_preferences VALUES ('0', 'UISTYLE', 'default');
INSERT INTO dotp_user_preferences VALUES ('0', 'TASKASSIGNMAX', '100');
INSERT INTO dotp_user_preferences VALUES ('0', 'EVENTFILTER', 'my');
INSERT INTO dotp_user_preferences VALUES ('0', 'MAILALL', '0');
INSERT INTO dotp_user_preferences VALUES ('0', 'TASKLOGEMAIL', '0');
INSERT INTO dotp_user_preferences VALUES ('0', 'TASKLOGSUBJ', '');
INSERT INTO dotp_user_preferences VALUES ('0', 'TASKLOGNOTE', '0');

-- ----------------------------
-- Table structure for `dotp_user_roles`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_user_roles`;
CREATE TABLE `dotp_user_roles` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_user_roles
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_user_tasks`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_user_tasks`;
CREATE TABLE `dotp_user_tasks` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(4) NOT NULL DEFAULT '0',
  `task_id` int(11) NOT NULL DEFAULT '0',
  `perc_assignment` int(11) NOT NULL DEFAULT '100',
  `user_task_priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`user_id`,`task_id`),
  KEY `user_type` (`user_type`),
  KEY `idx_user_tasks` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_user_tasks
-- ----------------------------
INSERT INTO dotp_user_tasks VALUES ('4', '0', '9', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '11', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '12', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '13', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '14', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '16', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '17', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '18', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '19', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '20', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '21', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '23', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '24', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('4', '0', '30', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('5', '0', '10', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('5', '0', '15', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('5', '0', '22', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('5', '0', '25', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('5', '0', '26', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('5', '0', '30', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '27', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '28', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '29', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '30', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '31', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '32', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '33', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '34', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '35', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('6', '0', '36', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('7', '0', '9', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('7', '0', '37', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('7', '0', '38', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('7', '0', '39', '100', '0');
INSERT INTO dotp_user_tasks VALUES ('8', '0', '12', '100', '0');

-- ----------------------------
-- Table structure for `dotp_user_task_pin`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_user_task_pin`;
CREATE TABLE `dotp_user_task_pin` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `task_id` int(10) NOT NULL DEFAULT '0',
  `task_pinned` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_user_task_pin
-- ----------------------------

-- ----------------------------
-- Table structure for `dotp_wbs_dictionary`
-- ----------------------------
DROP TABLE IF EXISTS `dotp_wbs_dictionary`;
CREATE TABLE `dotp_wbs_dictionary` (
  `wbs_item_id` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`wbs_item_id`),
  CONSTRAINT `FK_WBS_ITEM_DICTIONARY` FOREIGN KEY (`wbs_item_id`) REFERENCES `dotp_project_eap_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_wbs_dictionary
-- ----------------------------
INSERT INTO dotp_wbs_dictionary VALUES ('4', 'O sistema deve proporcionar que clientes possam realizar pedidos online, e que os entregadores possam consultar os dados dos pedidos por meio de dispositivos mÃ³veis. ');

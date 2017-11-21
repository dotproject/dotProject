/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : dotproject_2_1_7

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-05-10 20:40:38
*/

SET FOREIGN_KEY_CHECKS=0;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_requirements
-- ----------------------------

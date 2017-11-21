/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : dotproject_2_1_7

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-05-10 20:41:47
*/

SET FOREIGN_KEY_CHECKS=0;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotp_scope_statement
-- ----------------------------

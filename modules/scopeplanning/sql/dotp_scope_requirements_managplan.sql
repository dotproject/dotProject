/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : dotproject_2_1_7

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-05-15 13:19:13
*/

SET FOREIGN_KEY_CHECKS=0;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
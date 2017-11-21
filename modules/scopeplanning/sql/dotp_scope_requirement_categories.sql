/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : dotproject_2_1_7

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-05-10 20:41:09
*/

SET FOREIGN_KEY_CHECKS=0;
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
INSERT INTO dotp_scope_requirement_categories VALUES ('RF', '', 'Functional', null);
INSERT INTO dotp_scope_requirement_categories VALUES ('RNF', null, 'Non-functional', null);

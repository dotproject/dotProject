/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : dotproject_2_1_7

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-05-10 20:41:25
*/

SET FOREIGN_KEY_CHECKS=0;
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

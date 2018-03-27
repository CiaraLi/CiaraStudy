/*
Navicat MySQL Data Transfer

Source Server         : dev-rallinone
Source Server Version : 50505
Source Host           : 172.16.101.1:3306
Source Database       : rallinone

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-02-11 17:49:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for apps
-- ----------------------------
DROP TABLE IF EXISTS `apps`;
CREATE TABLE `apps` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT,
  `app_auth` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `app_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `app_added_at` int(11) NOT NULL,
  `app_last_time` datetime NOT NULL,
  `app_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `app_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `app_icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `app_status` int(1) NOT NULL,
  `app_chid` int(11) NOT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of apps
-- ----------------------------
INSERT INTO `apps` VALUES ('1', '7znZt2oJ', 'pPn3pGRICHoWgfA1PLU1xC70gzg4AoepZCpTkPSWV0sxTzk3EQU3', '1518160246', '0000-00-00 00:00:00', 'B2B', 'https://my.test.com/', null, '1', '0');

/*
Navicat MySQL Data Transfer

Source Server         : 172.16.51.190
Source Server Version : 50505
Source Host           : 172.16.51.190:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-12-27 10:31:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `isbn` varchar(20) NOT NULL,
  `quantity` int(8) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;
-- ----------------------------
-- Records of books
-- ----------------------------
INSERT INTO `books` VALUES ('1', 'book', '1234567', '2', 'book','','2016-12-27 03:28:16', '2016-12-27 03:28:16');
INSERT INTO `books` VALUES ('2', 'vilo', '11222333', '2', 'vilo', '', '2016-12-27 03:28:16', '2016-12-27 03:28:16');

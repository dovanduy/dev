/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50616
Source Host           : localhost:3306
Source Database       : dev2

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2016-06-02 16:05:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `hash_password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `birthday` int(11) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT '0' COMMENT '0: undefined; 1: male; 2: femail',
  `country_code` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `passport` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identify` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device` int(11) NOT NULL DEFAULT '0' COMMENT '0:undefined;bit{0:web;1:ios;2:android;3:window phone;4:blackberry}',
  `last_login` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `image_id` int(11) DEFAULT NULL,
  `website_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `IX_email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES ('1', '9ef06c325cefb48cca510919', 'root@gmail.com', '$2y$10$d1d5blRROHU4MmVsWGU2du.uxkej8/Ec17ZDRvWSuu3nWTek11WnK', 'wWynTQ8u82elXe6w', 'root', 'Root', 'Administrator', '1291734000', '1', 'VN', 'VN-SG', 'VN.HC.QA', 'Số 6, Nguyễn Thị Minh Khai, P. đa kao', '7785858585', '0098766676', '0', '1464751199', null, '1464751199', '1', '41', '1');

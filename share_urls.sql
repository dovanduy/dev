/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50616
Source Host           : localhost:3306
Source Database       : dev2

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2016-06-03 00:17:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for share_urls
-- ----------------------------
DROP TABLE IF EXISTS `share_urls`;
CREATE TABLE `share_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `website_id` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `shared` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COMMENT='ユーザFacebookテーブル';

-- ----------------------------
-- Records of share_urls
-- ----------------------------
INSERT INTO `share_urls` VALUES ('24', '1', 'http://vuongquocbalo.com/tui-ipad-deo-cheo-thoi-trang-vzid25775', null, '1464882648', '1464886483');
INSERT INTO `share_urls` VALUES ('25', '1', 'http://vuongquocbalo.com/tui-deo-cheo-cnt-06-lich-lam-vzid24540', null, '1464882695', '1464886484');
INSERT INTO `share_urls` VALUES ('26', '1', 'http://vuongquocbalo.com/tui-ipad-deo-cheo-hieu-cnt-16-vzid25412', null, '1464882715', '1464886484');
INSERT INTO `share_urls` VALUES ('27', '1', 'http://vuongquocbalo.com/tui-deo-cheo-da-nam-nang-dong-vzid38876', null, '1464882725', '1464886484');
INSERT INTO `share_urls` VALUES ('28', '1', 'http://vuongquocbalo.com/tui-xach-nu-theu-chi-khoa-xoay-vzid39965', null, '1464882728', '1464886485');
INSERT INTO `share_urls` VALUES ('29', '1', 'http://vuongquocbalo.com/balo-mini-cnt-ca-tinh-vzid24497', null, '1464885952', '1464886485');
INSERT INTO `share_urls` VALUES ('30', '1', 'http://vuongquocbalo.com/tui-rut-tuzki-vbdct100', null, '1464886054', '1464886486');
INSERT INTO `share_urls` VALUES ('31', '1', 'http://vuongquocbalo.com/tui-rut-tho-deo-phao-vbdct86', null, '1464886081', '1464886487');
INSERT INTO `share_urls` VALUES ('32', '1', 'http://vuongquocbalo.com/tui-rut-tho-trang-vbdct80', null, '1464886121', '1464886487');
INSERT INTO `share_urls` VALUES ('33', '1', 'http://vuongquocbalo.com/tui-rut-couple-for-you-vbdcp23a', null, '1464886219', '1464886488');
INSERT INTO `share_urls` VALUES ('34', '1', 'http://vuongquocbalo.com/tui-rut-vpop-son-tung-m-tp-vbdvp155', null, '1464886259', '1464886488');
INSERT INTO `share_urls` VALUES ('35', '1', 'http://vuongquocbalo.com/tui-rut-vpop-son-tung-m-tp-vbdvp161', null, '1464886290', '1464886489');
INSERT INTO `share_urls` VALUES ('36', '1', 'http://vuongquocbalo.com/tui-rut-vpop-hoai-lam-vbdvp150', null, '1464886292', '1464887660');
INSERT INTO `share_urls` VALUES ('37', '1', 'http://vuongquocbalo.dev/tui-rut-winner-vbdkp207', null, '1464887621', '1464887621');
INSERT INTO `share_urls` VALUES ('38', '1', 'http://vuongquocbalo.com/tui-rut-winner-vbdkp207', null, '1464887630', '1464887630');
INSERT INTO `share_urls` VALUES ('39', '1', 'http://vuongquocbalo.com/tui-rut-vpop-365-band-vbdvp165', null, '1464887648', '1464887648');
INSERT INTO `share_urls` VALUES ('41', '1', 'http://vuongquocbalo.com/tui-rut-vpop-khoi-my-kelv-khanh-vbdvp154', null, '1464887669', '1464887669');
INSERT INTO `share_urls` VALUES ('42', '1', 'http://vuongquocbalo.com/tui-rut-vpop-khoi-my-kevil-khanh-vbdvp169', null, '1464887687', '1464887687');
INSERT INTO `share_urls` VALUES ('43', '1', 'http://vuongquocbalo.com/tui-rut-vpop-khoi-my-vbdvp163', null, '1464887690', '1464887690');
INSERT INTO `share_urls` VALUES ('44', '1', 'http://vuongquocbalo.com/tui-rut-vpop-khoi-my-vbdvp167', null, '1464887692', '1464887692');
INSERT INTO `share_urls` VALUES ('45', '1', 'http://vuongquocbalo.com/tui-rut-vpop-ngo-kien-huy-vbdvp151', null, '1464887694', '1464887694');
INSERT INTO `share_urls` VALUES ('46', '1', 'http://vuongquocbalo.com/tui-rut-vpop-khoi-my-kevil-khanh-vbdvp171', null, '1464887697', '1464887697');
INSERT INTO `share_urls` VALUES ('47', '1', 'http://vuongquocbalo.com/tui-rut-vpop-khoi-my-vbdvp159', null, '1464887698', '1464887698');
INSERT INTO `share_urls` VALUES ('48', '1', 'http://vuongquocbalo.com/tui-rut-vpop-son-tung-m-tp-vbdvp120', null, '1464887701', '1464887701');
INSERT INTO `share_urls` VALUES ('49', '1', 'http://vuongquocbalo.com/tui-rut-vpop-son-tung-m-tp-vbdvp10', null, '1464887703', '1464887703');
INSERT INTO `share_urls` VALUES ('50', '1', 'http://vuongquocbalo.com/tui-rut-son-tung-vbdvp153', null, '1464887711', '1464887711');
INSERT INTO `share_urls` VALUES ('51', '1', 'http://vuongquocbalo.com/tui-rut-tien-cookie-tam-su-voi-nguoi-la-vbdvp176', null, '1464887713', '1464887713');
DROP TRIGGER IF EXISTS `before_insert_user_facebook_informations_copy2`;
DELIMITER ;;
CREATE TRIGGER `before_insert_user_facebook_informations_copy2` BEFORE INSERT ON `share_urls` FOR EACH ROW SET 
	new.created = UNIX_TIMESTAMP(),
	new.updated = UNIX_TIMESTAMP()
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `before_update_user_facebook_informations_copy2`;
DELIMITER ;;
CREATE TRIGGER `before_update_user_facebook_informations_copy2` BEFORE UPDATE ON `share_urls` FOR EACH ROW SET 
	new.updated = UNIX_TIMESTAMP()
;;
DELIMITER ;

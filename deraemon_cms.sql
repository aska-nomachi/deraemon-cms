-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- サーバのバージョン： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `emontest`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(10) unsigned DEFAULT NULL,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`id`, `division_id`, `segment`, `name`, `description`, `order`) VALUES
(1, 2, 'aaaaa', 'カテゴリーA', NULL, 0),
(2, 2, 'bbbbb', 'カテゴリーB', NULL, 0),
(3, 2, 'ccccc', 'カテゴリーC', NULL, 0),
(4, 2, 'ddddd', 'カテゴリーD', NULL, 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `details`
--

CREATE TABLE IF NOT EXISTS `details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `detail_rules`
--

CREATE TABLE IF NOT EXISTS `detail_rules` (
  `id` int(10) unsigned NOT NULL,
  `detail_id` int(10) DEFAULT NULL,
  `callback` text,
  `param` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `divisions`
--

CREATE TABLE IF NOT EXISTS `divisions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wrapper_id` int(10) unsigned NOT NULL,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- テーブルのデータのダンプ `divisions`
--

INSERT INTO `divisions` (`id`, `wrapper_id`, `segment`, `name`) VALUES
(1, 1, 'page', 'PAGE'),
(2, 1, 'products', 'PRODUCTS'),
(3, 1, 'news', 'NEWS');

-- --------------------------------------------------------

--
-- テーブルの構造 `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `receive_subject` varchar(200) DEFAULT NULL,
  `receive_email_type` varchar(45) DEFAULT NULL,
  `user_name_field` varchar(45) DEFAULT NULL,
  `user_address_field` varchar(45) DEFAULT NULL,
  `confirm_subject` varchar(200) DEFAULT NULL,
  `confirm_email_type` varchar(200) DEFAULT NULL,
  `admin_name` varchar(200) DEFAULT NULL,
  `admin_address` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- テーブルのデータのダンプ `emails`
--

INSERT INTO `emails` (`id`, `segment`, `name`, `description`, `receive_subject`, `receive_email_type`, `user_name_field`, `user_address_field`, `confirm_subject`, `confirm_email_type`, `admin_name`, `admin_address`) VALUES
(1, 'contact', 'CONTACT', '', 'contact from {{name}}', 'text/plain', 'name', 'email', 'thank you for your contact', 'text/plain', 'administrator', 'kohei.0728@gmail.com');

-- --------------------------------------------------------

--
-- テーブルの構造 `email_rules`
--

CREATE TABLE IF NOT EXISTS `email_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(10) unsigned DEFAULT NULL,
  `field` varchar(200) DEFAULT NULL,
  `callback` text,
  `param` varchar(200) DEFAULT NULL,
  `label` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- テーブルのデータのダンプ `email_rules`
--

INSERT INTO `email_rules` (`id`, `email_id`, `field`, `callback`, `param`, `label`) VALUES
(1, 1, 'email', 'not_empty', '', 'email'),
(2, 1, 'name', 'not_empty', '', 'name');

-- --------------------------------------------------------

--
-- テーブルの構造 `fields`
--

CREATE TABLE IF NOT EXISTS `fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(10) DEFAULT NULL,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- テーブルのデータのダンプ `fields`
--

INSERT INTO `fields` (`id`, `division_id`, `segment`, `name`, `order`) VALUES
(1, 2, 'type', 'TYPE', 3),
(2, 2, 'price', 'PRICE', 4),
(3, 1, 'text1', 'TEXT1', 1),
(4, 1, 'text2', 'TEXT2', 2);

-- --------------------------------------------------------

--
-- テーブルの構造 `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned DEFAULT NULL,
  `segment` varchar(200) DEFAULT NULL,
  `ext` varchar(10) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `order` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- テーブルのデータのダンプ `images`
--

INSERT INTO `images` (`id`, `item_id`, `segment`, `ext`, `name`, `description`, `order`) VALUES
(1, 1, 'home_main', '.png', 'HOME MAIN', '', 0),
(2, 12, 'news1_main', '.png', 'NEWS1 MAIN', '', 0),
(3, 2, 'about_us_main', '.png', 'ABOUT US MAIN', '', 0),
(4, 14, 'product1_1', '.png', 'PRODUCT1-1', '', 0),
(5, 15, 'product2', '.png', 'PRODUCT2', '', 0),
(6, 16, 'product3', '.png', 'PRODUCT3', '', 0),
(7, 17, 'product4', '.png', 'PRODUCT4', '', 0),
(8, 18, 'product5', '.png', 'PRODUCT5', '', 0),
(9, 19, 'product6', '.png', 'PRODUCT6', '', 0),
(10, 20, 'product7', '.png', 'PRODUCT7', '', 0),
(11, 21, 'product8', '.png', 'PRODUCT8', '', 0),
(12, 25, 'news5_main', '.png', 'NEWS5  MAIN', '', 0),
(13, 24, 'news4_main', '.png', 'NEWS4 MAIN', '', 0),
(14, 22, 'news2_main', '.png', 'NEWS2 MAIN', '', 0),
(15, 23, 'news3_main', '.png', 'NEWS3 MAIN', '', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(10) unsigned NOT NULL,
  `shape_segment` varchar(100) DEFAULT NULL,
  `image_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `segment` varchar(100) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `catch` varchar(200) DEFAULT NULL,
  `keywords` varchar(200) DEFAULT NULL,
  `description` text,
  `summary` text,
  `order` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `issued` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `send_comment_is_on` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- テーブルのデータのダンプ `items`
--

INSERT INTO `items` (`id`, `division_id`, `shape_segment`, `image_id`, `user_id`, `parent_id`, `segment`, `title`, `catch`, `keywords`, `description`, `summary`, `order`, `is_active`, `issued`, `created`, `send_comment_is_on`) VALUES
(1, 1, NULL, 1, 1, 0, 'home', 'HOME', 'CMS for web designer.', 'cms, deraemon', 'CMS for web designer.', 'CMS for web designer.', 1, 1, '2015-12-21 12:49:00', '2015-12-21 12:49:28', 0),
(2, 1, NULL, 3, 1, 0, 'about_us', 'ABOUT US', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX \nXXXXX XXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 2, 1, '2015-12-21 12:49:00', '2015-12-21 12:50:11', 0),
(3, 1, NULL, NULL, 1, 0, 'puroducts', 'PURODUCTS', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 3, 1, '2015-12-21 12:50:00', '2015-12-21 12:50:40', 0),
(4, 1, NULL, NULL, 1, 0, 'news', 'NEWS', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 4, 1, '2015-12-21 12:50:00', '2015-12-21 12:50:59', 0),
(5, 1, NULL, NULL, 1, 0, 'contact', 'CONTACT', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXX,XXXXXX,XXXXXX\nXXXXX,XXXXXX,XXXXXX\nXXXXX,XXXXXX,XXXXXX\nXXXXX,XXXXXX,XXXXXX', 'XXXXX,XXXXXX,XXXXXX\nXXXXX,XXXXXX,XXXXXX\nXXXXX,XXXXXX,XXXXXX\nXXXXX,XXXXXX,XXXXXX', 5, 1, '2015-12-23 03:52:00', '2015-12-23 15:52:50', 0),
(12, 3, NULL, 2, 1, 0, 'news1', 'NEWS1', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\n', 0, 1, '2015-12-23 05:31:00', '2015-12-23 17:31:58', 1),
(14, 2, 'product', 4, 1, 3, 'product1', 'PRODUCT1', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 06:02:00', '2015-12-23 18:02:11', 0),
(15, 2, 'product', 5, 1, 0, 'product2', 'PRODUCT2', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 09:57:00', '2015-12-23 21:57:44', 0),
(16, 2, 'product', 6, 1, 0, 'product3', 'PRODUCT3', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 0, '2015-12-23 09:57:00', '2015-12-23 21:57:54', 0),
(17, 2, 'product', 7, 1, 0, 'product4', 'PRODUCT4', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 09:58:00', '2015-12-23 21:58:10', 0),
(18, 2, 'product', 8, 1, 0, 'product5', 'PRODUCT5', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 09:58:00', '2015-12-23 21:58:21', 0),
(19, 2, 'product', 9, 1, 0, 'product6', 'PRODUCT6', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 09:58:00', '2015-12-23 21:58:32', 0),
(20, 2, 'product', 10, 1, 0, 'product7', 'PRODUCT7', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 09:58:00', '2015-12-23 21:58:41', 0),
(21, 2, 'product', 11, 1, 0, 'product8', 'PRODUCT8', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXX,XXXXXX,XXXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-23 09:58:00', '2015-12-23 21:58:49', 1),
(22, 3, NULL, 14, 1, 0, 'news2', 'NEWS2', '', '', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-25 11:58:00', '2015-12-25 11:58:04', 1),
(23, 3, NULL, 15, 1, 0, 'news3', 'NEWS3', '', '', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-25 11:58:00', '2015-12-25 11:58:16', 1),
(24, 3, NULL, 13, 1, 0, 'news4', 'NEWS4', '', '', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-25 11:58:00', '2015-12-25 11:58:47', 1),
(25, 3, NULL, 12, 1, 0, 'news5', 'NEWS5', '', '', 'XXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 'XXXXXXXXX XXXXX\nXXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXXXXXXXXXXX XXXXX XXXXXXXX XXXXX\nXXXXXXXXX XXXXX XXXXXXXX XXXXX', 0, 1, '2015-12-25 11:58:00', '2015-12-25 11:58:57', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `items_categories`
--

CREATE TABLE IF NOT EXISTS `items_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- テーブルのデータのダンプ `items_categories`
--

INSERT INTO `items_categories` (`id`, `item_id`, `category_id`) VALUES
(2, 21, 1),
(3, 20, 1),
(5, 19, 3),
(6, 18, 4),
(7, 17, 3),
(9, 16, 1),
(10, 15, 2),
(11, 14, 2);

-- --------------------------------------------------------

--
-- テーブルの構造 `items_fields`
--

CREATE TABLE IF NOT EXISTS `items_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned DEFAULT NULL,
  `field_id` int(10) unsigned DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- テーブルのデータのダンプ `items_fields`
--

INSERT INTO `items_fields` (`id`, `item_id`, `field_id`, `value`) VALUES
(11, 14, 1, 'A123'),
(12, 14, 2, '1000'),
(13, 15, 1, NULL),
(14, 15, 2, NULL),
(15, 16, 1, NULL),
(16, 16, 2, NULL),
(17, 17, 1, NULL),
(18, 17, 2, NULL),
(19, 18, 1, NULL),
(20, 18, 2, NULL),
(21, 19, 1, NULL),
(22, 19, 2, NULL),
(23, 20, 1, NULL),
(24, 20, 2, NULL),
(25, 21, 1, NULL),
(26, 21, 2, NULL),
(27, 1, 3, 'xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx\nxxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx\nxxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx'),
(28, 2, 3, NULL),
(29, 3, 3, NULL),
(30, 4, 3, NULL),
(31, 5, 3, NULL),
(32, 1, 4, 'xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx\nxxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx\nxxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx xxxx xxx xxxxx xxx xxxxx xxx'),
(33, 2, 4, NULL),
(34, 3, 4, NULL),
(35, 4, 4, NULL),
(36, 5, 4, NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `items_tags`
--

CREATE TABLE IF NOT EXISTS `items_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned DEFAULT NULL,
  `tag_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `received_comments`
--

CREATE TABLE IF NOT EXISTS `received_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `replay_id` int(10) unsigned DEFAULT NULL,
  `display_name` varchar(200) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `content` text,
  `created` datetime DEFAULT NULL,
  `is_accept` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `received_emails`
--

CREATE TABLE IF NOT EXISTS `received_emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_segment` varchar(200) NOT NULL,
  `json` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- テーブルのデータのダンプ `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'edit', 'Administrative user, has access to everything.'),
(3, 'admin', ''),
(4, 'direct', '');

-- --------------------------------------------------------

--
-- テーブルの構造 `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_users_ibfk_1_idx` (`user_id`),
  KEY `roles_users_ibfk_2_idx` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(24) NOT NULL,
  `last_active` int(10) unsigned NOT NULL,
  `contents` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_active` (`last_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `key` varchar(200) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

--
-- テーブルのデータのダンプ `settings`
--

INSERT INTO `settings` (`id`, `name`, `key`, `value`) VALUES
(1, 'direct key', 'direct_key', '********************************'),
(2, 'temp prefix', 'temp_pre', 'temp'),
(3, 'temp directory', 'temp_dir', 'contents/temp'),
(4, 'tpl function class name', 'tpl_func', 'Cms_Functions'),
(5, 'item directory', 'item_dir', 'contents/items'),
(6, 'image directory', 'image_dir', 'contents/images'),
(7, 'backend template directory', 'back_tpl_dir', 'contents/backend2'),
(8, 'frontend template directory', 'front_tpl_dir', 'contents/frontend/'),
(9, 'front_theme', 'front_theme', 'simple'),
(11, 'backend name', 'backend_name', 'admin'),
(12, 'lang', 'lang', 'ja'),
(13, 'backend language', 'backend_lang', 'ja'),
(14, 'timezoon', 'timezoon', 'Asia/Tokyo'),
(15, 'home page', 'home_page', 'home'),
(16, 'site title', 'site_title', '***site_title***'),
(17, 'site email address', 'site_email_address', 'info@example.com'),
(18, 'site details', 'site_details', 'address: ----------\ntel: ----------\nfax: ----------\nemail: ----------\nurl: ----------'),
(21, 'paginate items per page for items', 'paginate_items_per_page_for_items', '10'),
(22, 'paginate follow for items', 'paginate_items_follow_for_items', '2'),
(23, 'paginate items per page for users', 'paginate_items_per_page_for_users', '10'),
(24, 'paginate follow for users', 'paginate_items_follow_for_users', '1'),
(25, 'paginate items per page for received emails', 'paginate_items_per_page_for_received_emails', '4'),
(26, 'paginate items follow for received emails', 'paginate_items_follow_for_received_emails', '3'),
(27, 'paginate items per page for received comments', 'paginate_items_per_page_for_received_comments', '10'),
(28, 'paginate items follow for received comments', 'paginate_items_follow_for_received_comments', '1'),
(31, 'image size vertical', 'image_v', '600, 800'),
(32, 'image size horizontal', 'image_h', '800, 600'),
(33, 'image size square', 'image_s', '600, 600'),
(41, 'smtp hostname', 'smtp_hostname', '***smtp_hostname***'),
(42, 'smtp port', 'smtp_port', '***smtp_port***'),
(43, 'smtp username', 'smtp_username', '***smtp_username***'),
(44, 'smtp password', 'smtp_password', '***smtp_password***'),
(51, 'send email is on', 'send_email_is_on', '1'),
(52, 'send_email save is on', 'send_email_save_is_on', '1'),
(53, 'send email confirm is on', 'send_email_confirm_is_on', '1'),
(54, 'send email allowable tags', 'send_email_allowable_tags', '<p><a><img>'),
(55, 'send email defult receive subject', 'send_email_defult_receive_subject', 'contact from user'),
(56, 'send email defult user name', 'send_email_defult_user_name', 'anonymity'),
(57, 'send email defult user address', 'send_email_defult_user_address', 'info@example.com'),
(58, 'send email defult confirm subject', 'send_email_defult_confirm_subject', 'thank you for your contact'),
(59, 'send email defult admin name', 'send_email_defult_admin_name', 'administrator'),
(60, 'send email defult admin address', 'send_email_defult_admin_address', 'info@example.com'),
(70, 'author login is on', 'author_login_is_on', '1'),
(71, 'author register is on', 'author_register_is_on', '1'),
(72, 'author register activate is on', 'author_register_activate_is_on', '1'),
(73, 'author register activate subject', 'author_register_activate_subject', 'thank you for your register'),
(74, 'author register activate email type', 'author_register_activate_email_type', 'text/plain'),
(75, 'author register activate from address', 'author_register_activate_from_address', 'info@example.com'),
(76, 'author register activate from name', 'author_register_activate_from_name', '***author_register_activate_from_name***'),
(77, 'author register activate access key', 'author_register_activate_access_key', '***author_register_activate_access_key***'),
(78, 'author register activate key delimiter', 'author_register_activate_key_delimiter', '/////'),
(79, 'author register default is block', 'author_register_default_is_block', '0'),
(81, 'author password forgot is on', 'author_password_forgot_is_on', '1'),
(82, 'author password reset subject', 'author_password_reset_subject', '***author_password_reset_subject***'),
(83, 'author password reset email type', 'author_password_reset_email_type', 'text/plain'),
(84, 'author password reset from address', 'author_password_reset_from_address', 'info@example.com'),
(85, 'author password reset from name', 'author_password_reset_from_name', '***author_password_reset_from_name***'),
(86, 'author password reset key delimiter', 'author_password_reset_key_delimiter', '/////'),
(87, 'author password is on', 'author_password_is_on', '1'),
(88, 'author account is on', 'author_account_is_on', '1'),
(89, 'author detail is on', 'author_detail_is_on', '1'),
(91, 'send comment is on', 'send_comment_is_on', '1'),
(92, 'send comment is user only', 'send_comment_is_user_only', '0'),
(93, 'send comment is on default', 'send_comment_is_on_default', '1'),
(94, 'send comment is accept default', 'send_comment_is_accept_default', '1'),
(95, 'send comment allowable tags', 'send_comment_allowable_tags', '<img>'),
(101, 'encrypt mode', 'encrypt_mode', 'nofb'),
(102, 'encrypt cipher', 'encrypt_cipher', 'rijndael-128'),
(103, 'encrypt key', 'encrypt_key', '********************************'),
(104, 'cooki salt', 'cooki_salt', '********************************'),
(105, 'cooki expiration', 'cooki_expiration', 'week'),
(106, 'session name', 'session_name', 'deraemon_session'),
(107, 'auth hash method', 'auth_hash_method', 'sha256'),
(108, 'auth hash key', 'auth_hash_key', '********************************'),
(109, 'auth lifetime', 'auth_lifetime', '2week'),
(110, 'auth session key', 'auth_session_key', '********************************');

-- --------------------------------------------------------

--
-- テーブルの構造 `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `reset_key` varchar(32) DEFAULT NULL,
  `ext` varchar(10) DEFAULT NULL,
  `is_block` int(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `users_details`
--

CREATE TABLE IF NOT EXISTS `users_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `detail_id` int(10) unsigned DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `wrappers`
--

CREATE TABLE IF NOT EXISTS `wrappers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segment` varchar(200) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `content_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- テーブルのデータのダンプ `wrappers`
--

INSERT INTO `wrappers` (`id`, `segment`, `name`, `content_type`) VALUES
(1, 'html', 'HTML', 'text/html');

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
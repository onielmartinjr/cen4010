-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2016 at 09:54 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `breed`
--

CREATE TABLE IF NOT EXISTS `breed` (
  `breed_wk` int(11) NOT NULL AUTO_INCREMENT,
  `pet_type_wk` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`breed_wk`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `color`
--

CREATE TABLE IF NOT EXISTS `color` (
  `color_wk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`color_wk`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment_wk`
--

CREATE TABLE IF NOT EXISTS `comment_wk` (
  `comment_wk` int(11) NOT NULL AUTO_INCREMENT,
  `user_wk` int(11) NOT NULL,
  `pet_wk` int(11) NOT NULL,
  `input_wk` text NOT NULL,
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `image_wk` int(11) NOT NULL AUTO_INCREMENT,
  `pet_wk` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_wk`),
  UNIQUE KEY `file_name` (`file_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `log_wk` int(11) NOT NULL AUTO_INCREMENT,
  `user_wk` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE IF NOT EXISTS `pet` (
  `pet_wk` int(11) NOT NULL AUTO_INCREMENT,
  `pet_type_wk` int(11) NOT NULL,
  `breed_wk` int(11) NOT NULL,
  `color_wk` int(11) NOT NULL,
  `status_wk` int(11) NOT NULL,
  `image_wk` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `age` int(2) NOT NULL,
  `weight` float NOT NULL,
  `acquired_dt` datetime NOT NULL,
  `is_rescued` tinyint(1) NOT NULL,
  `last_update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pet_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pet_to_vaccination`
--

CREATE TABLE IF NOT EXISTS `pet_to_vaccination` (
  `pet_to_vaccination_wk` int(11) NOT NULL AUTO_INCREMENT,
  `pet_wk` int(11) NOT NULL,
  `vaccination_wk` int(11) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pet_to_vaccination_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pet_type`
--

CREATE TABLE IF NOT EXISTS `pet_type` (
  `pet_type_wk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pet_type_wk`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pet_wish_list`
--

CREATE TABLE IF NOT EXISTS `pet_wish_list` (
  `pet_wish_list_wk` int(11) NOT NULL AUTO_INCREMENT,
  `user_wk` int(11) NOT NULL,
  `pet_wk` int(11) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pet_wish_list_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reset_password`
--

CREATE TABLE IF NOT EXISTS `reset_password` (
  `reset_password_wk` int(11) NOT NULL AUTO_INCREMENT,
  `user_wk` int(11) NOT NULL,
  `random_key` int(11) NOT NULL,
  `request_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_reset` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reset_password_wk`),
  UNIQUE KEY `random_key` (`random_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `role_wk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_wk`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `setting_wk` int(11) NOT NULL AUTO_INCREMENT,
  `variable_name` varchar(50) NOT NULL,
  `variable_value` varchar(255) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`setting_wk`),
  UNIQUE KEY `variable_name` (`variable_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `status_wk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_wk`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_wk` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email_address` varchar(50) NOT NULL,
  `hashed_password` varchar(40) NOT NULL,
  `role_wk` int(11) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_dt` datetime DEFAULT NULL,
  `last_update_dt` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(15) NOT NULL,
  `is_notifications_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `phone_number` int(10) NOT NULL,
  PRIMARY KEY (`user_wk`),
  UNIQUE KEY `email_address` (`email_address`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vaccination`
--

CREATE TABLE IF NOT EXISTS `vaccination` (
  `vaccination_wk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vaccination_wk`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `watch_list`
--

CREATE TABLE IF NOT EXISTS `watch_list` (
  `watch_list_wk` int(11) NOT NULL AUTO_INCREMENT,
  `user_wk` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `last_update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`watch_list_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `watch_list_column_entry`
--

CREATE TABLE IF NOT EXISTS `watch_list_column_entry` (
  `watch_list_column_entry_wk` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(20) NOT NULL,
  `column_name` varchar(40) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`watch_list_column_entry_wk`),
  UNIQUE KEY `display_name` (`display_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `watch_list_detail`
--

CREATE TABLE IF NOT EXISTS `watch_list_detail` (
  `watch_list_detail_wk` int(11) NOT NULL AUTO_INCREMENT,
  `watch_list_wk` int(11) NOT NULL,
  `watch_list_column_entry_wk` int(11) NOT NULL,
  `value1` varchar(20) NOT NULL,
  `value2` int(11) DEFAULT NULL,
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`watch_list_detail_wk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

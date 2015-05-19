-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2014 at 01:57 AM
-- Server version: 5.5.23
-- PHP Version: 5.5.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dot_kernel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `firstName` varchar(150) NOT NULL,
  `lastName` varchar(150)  NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `firstName`, `lastName`, `dateCreated`, `isActive`) VALUES
(1, 'admin', '$2y$10$kC0ztOWgfx4i6e/6NeLvOejOMFhdv4tWVuhwfqmnEB0qMRkpmSivC', 'team@dotkernel.com', 'Default', 'Account', '2010-03-15 03:05:43', '1');

-- --------------------------------------------------------

--
-- Table structure for table `adminLogin`
--

CREATE TABLE IF NOT EXISTS `adminLogin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `adminId` int(11) unsigned NOT NULL,
  `referer` text NOT NULL,
  `userAgent` text NOT NULL,
  `dateLogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `adminId` (`adminId`),
  CONSTRAINT `fk_adminLogin_admin` FOREIGN KEY (`adminId`) REFERENCES `admin` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `statisticVisit`
--

CREATE TABLE IF NOT EXISTS `statisticVisit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `proxyIp` varchar(150) NOT NULL,
  `carrier` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  `accept` text NOT NULL,
  `acceptLanguage` text NOT NULL,
  `acceptEncoding` text NOT NULL,
  `acceptCharset` text NOT NULL,
  `userAgent` text NOT NULL,
  `cacheControl` text NOT NULL,
  `cookie` text NOT NULL,
  `xWapProfile` text NOT NULL,
  `xForwardedFor` text NOT NULL,
  `xForwardedHost` text NOT NULL,
  `xForwardedServer` text NOT NULL,
  `referer` text NOT NULL,
  `dateHit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `statisticVisitMobile`
--

CREATE TABLE IF NOT EXISTS `statisticVisitMobile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `visitId` int(11) unsigned NOT NULL,
  `fallBack` varchar(100) NOT NULL,
  `brandName` varchar(100) NOT NULL,
  `modelName` varchar(100) NOT NULL,
  `browserName` varchar(100) NOT NULL,
  `browserVersion` varchar(100) NOT NULL,
  `deviceOs` varchar(100) NOT NULL,
  `deviceOsVersion` varchar(100) NOT NULL,
  `screenWidth` int(6) NOT NULL,
  `screenHeight` int(6) NOT NULL,
  `isTablet` enum('0','1') NOT NULL DEFAULT '0',
  `isMobile` enum('0','1') NOT NULL DEFAULT '0',
  `isSmartphone` enum('0','1') NOT NULL DEFAULT '0',
  `isIphone` enum('0','1') NOT NULL DEFAULT '0',
  `isAndroid` enum('0','1') NOT NULL DEFAULT '0',
  `isBlackberry` enum('0','1') NOT NULL DEFAULT '0',
  `isSymbian` enum('0','1') NOT NULL DEFAULT '0',
  `isWindowsMobile` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `visitId` (`visitId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- RELATIONS FOR TABLE `statisticVisitMobile`:
--   `visitId`
--       `statisticvisit` -> `id`

--
-- Constraints for table `statisticVisitMobile`
--
ALTER TABLE `statisticVisitMobile`
  ADD CONSTRAINT `statisticVisitMobile_ibfk_1` FOREIGN KEY (`visitId`) REFERENCES `statisticVisit` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- --------------------------------------------------------


--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(150) NOT NULL,
  `value` text NOT NULL,
  `title` text NOT NULL,
  `comment` text NOT NULL,
  `isEditable` enum('1','0') NOT NULL DEFAULT '0',
  `type` enum('radio','option','textarea') NOT NULL DEFAULT 'textarea',
  `possibleValues` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `key`, `value`, `title`, `comment`, `isEditable`, `type`, `possibleValues`) VALUES
(1, 'siteEmail', 'contact@dotkernel.com', 'Site Email Address', 'The email address that recieves all contact emails from the site.\r\nAlso used as Sender Email for ''forgot password''.', '1', 'textarea', ''),
(2, 'devEmails', 'team@dotkernel.com', 'Developer Emails', 'developer emails, for debug purpose, separated by comma', '0', 'textarea', ''),
(3, 'timeFormatShort', '%d %b %Y', 'Short Date/Time Format.', '%d - day of the month as a decimal number (range 01 to 31) %b - abbreviated month name according to the current locale %B - full month name according to the current locale %m - month as a decimal number (range 01 to 12) %y - year as a decimal number without a century (range 00 to 99) %Y - year as a decimal number including the century', '1', 'option', '%d %b %Y;%d %B %Y;%d %B %y;%d %m %Y;%d %m %y;%B %d, %Y;%b %d, %Y'),
(4, 'timeFormatLong', '%b %d, %Y, %H:%M', 'Long Date/Time Format.', 'Date/time format, including hours, minutes and seconds', '1', 'option', '%d %b %Y, %H:%M;%d %B %Y, %H:%M;%d %B %y, %H:%M;%d %m %Y, %H:%M;%d %m %y, %H:%M;%B %d, %Y, %H:%M;%b %d, %Y, %H:%M'),
(7, 'resultsPerPage', '5', 'Default results per page', 'How many records will be on every page, if is not specified otherwise by a specific configuration value', '1', 'option', '5;10;20;30;40;50'),
(8, 'whoisUrl', 'http://whois.domaintools.com', '', 'Whois lookup and Domain name search', '0', 'textarea', ''),
(9, 'paginationStep', '3', 'Pagination Step', 'The maximum number of pages that are shown on either side of the current page in the pagination header.', '1', 'option', '3;4;5;6;7;8;9;10');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `firstName` varchar(150) NOT NULL,
  `lastName` varchar(150) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `userLogin`
--

CREATE TABLE IF NOT EXISTS `userLogin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `country` varchar(150) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `referer` text NOT NULL,
  `userAgent` text NOT NULL,
  `dateLogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `adminId` (`userId`),
  CONSTRAINT `fk_userLogin_user` FOREIGN KEY (`userId`) REFERENCES `user` (`id`)
  	ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

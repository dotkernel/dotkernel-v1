-- phpMyAdmin SQL Dump
-- version 3.3.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2010 at 01:50 AM
-- Server version: 5.1.36
-- PHP Version: 5.3.2

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
  `username` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `firstName`, `lastName`, `dateCreated`, `isActive`) VALUES
(1, 'admin', '497fa091cb2c62a6b61e5d50f9079b71', 'team@dotkernel.com', 'Default', 'Account', '2010-03-15 03:05:43', '1');

-- --------------------------------------------------------

--
-- Table structure for table `emailTransporter`
--

CREATE TABLE IF NOT EXISTS `emailTransporter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `server` varchar(100) NOT NULL DEFAULT 'smtp.gmail.com',
  `port` int(5) NOT NULL DEFAULT '465',
  `ssl` enum('tls','ssl') NOT NULL DEFAULT 'tls',
  `capacity` int(11) NOT NULL DEFAULT '2000',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `counter` int(11) NOT NULL DEFAULT '0',
  `isActive` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `emailTransporter`
--


-- --------------------------------------------------------


--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `title` text NOT NULL,
  `comment` text NOT NULL,
  `isEditable` enum('1','0') NOT NULL DEFAULT '0',
  `type` enum('radio','option','textarea') NOT NULL DEFAULT 'textarea',
  `possibleValues` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `key`, `value`, `title`, `comment`, `isEditable`, `type`, `possibleValues`) VALUES
(1, 'defaultPageTitle', 'DotKernel', 'The Site Title.', 'The Site Title', '1', 'textarea', ''),
(2, 'defaultMetaDescription', 'Enterprise Level PHP Solutions, based on Zend Framework and DotKernel , build by DotBoost Technologies Inc.', '', '', '0', 'textarea', ''),
(3, 'defaultMetaKeywords', 'DotKernel, PHP, Zend Framework, ', 'Default Meta Keywords.', 'The default meta keywords.', '1', 'textarea', ''),
(4, 'siteName', 'DotKernel ', 'Site Name', 'Site Name', '1', 'textarea', ''),
(5, 'siteEmail', 'contact@dotkernel.com', 'Site Email Address', 'The email address that recieves all contact emails from the site.\r\nAlso used as Sender Email for ''forgot password''.', '1', 'textarea', ''),
(6, 'devEmails', 'team@dotkernel.com', 'Developer Emails', 'developer emails, for debug purpose, separated by comma', '0', 'textarea', ''),
(7, 'timeFormat', '%d %b %Y', 'Short Date/Time Format.', '%d - day of the month as a decimal number (range 01 to 31) %b - abbreviated month name according to the current locale %B - full month name according to the current locale %m - month as a decimal number (range 01 to 12) %y - year as a decimal number without a century (range 00 to 99) %Y - year as a decimal number including the century', '1', 'option', '%d %b %Y;%d %B %Y;%d %B %y;%d %m %Y;%d %m %y;%B %d, %Y;%b %d, %Y'),
(8, 'timeFormatLong', '%b %d, %Y, %H:%M', 'Long Date/Time Format.', 'Date/time format, including hours, minutes and seconds', '1', 'option', '%d %b %Y, %H:%M;%d %B %Y, %H:%M;%d %B %y, %H:%M;%d %m %Y, %H:%M;%d %m %y, %H:%M;%B %d, %Y, %H:%M;%b %d, %Y, %H:%M'),
(9, 'smtpAddresses', 'aol.com;aim.com;comcast.net;hotmail.com;earthlink.net;juno.com;juno.net;bellsouth.net;cox.net;roadrunner.com;sbcglobal.net', 'Email servers where we need to use external SMTP in order to send emails.', '', '0', 'textarea', ''),
(10, 'smtpActive', '0', 'Use external SMTP servers', 'If we use or not external SMTP servers.', '1', 'radio', '1;0'),
(11, 'resultsPerPage', '5', 'Default results per page', 'How many records will be on every page, if is not specified otherwise by a specific configuration value', '1', 'option', '5;10;20;30;40;50');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user`
--


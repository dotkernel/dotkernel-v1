-- phpMyAdmin SQL Dump
-- version 3.2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 02, 2009 at 11:51 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dot_kernel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `active` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `variable` varchar(63) NOT NULL default '',
  `title` text NOT NULL,
  `comment` text NOT NULL,
  `value` text NOT NULL,
  `editable` enum('Y','N') NOT NULL default 'N',
  `type` enum('radio','option','textarea') NOT NULL default 'textarea',
  `posible_values` text NOT NULL,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`variable`, `title`, `comment`, `value`, `editable`, `type`, `posible_values`) VALUES
('meta_keywords', 'Default Meta Keywords.', 'The default meta keywords.', 'DotKernel, DotKernel, php, Zend Framework, ', 'Y', 'textarea', ''),
('time_format', 'General Date Format.', '%d - day of the month as a decimal number (range 01 to 31) %b - abbreviated month name according to the current locale %B - full month name according to the current locale %m - month as a decimal number (range 01 to 12) %y - year as a decimal number without a century (range 00 to 99) %Y - year as a decimal number including the century', '%d %b %Y', 'Y', 'option', '%d %b %Y;%d %B %Y;%d %B %y;%d %m %Y;%d %m %y;%B %d, %Y;%b %d, %Y'),
('search_engine_bots', '', 'Not Used = Use this section to list which search engine user-agents you''d like to be treated as a search engine visit along with a ''human'' name for the online list in a bot=Human Name format.(one per line)', 'googlebot=Google.com\r\nYahoo!=Yahoo Bot\r\njeeves=Ask Jeeves Teoma\r\ninfoseek=Infoseek\r\nia_archiver=Archive.org\r\nmsnbot=MSN\r\nFAST=Alltheweb\r\ncrawler@alexa.com=Alexa\r\nYahooSeeker=Yahoo Shopping\r\ncurl=CURL\r\nwget=WGET', 'N', 'textarea', ''),
('page_title', 'The Site Title.', 'The Site Title', 'DotKernel Framework', 'Y', 'textarea', ''),
('url_rewrite_mode', 'Url Rewrite Mode.', 'The State of the rewrite mode', 'N', 'Y', 'radio', 'Y;N'),
('contact_recipient', 'Contact us email.', 'The email address that recieves all contact emails from the site.', 'contact@dotkernel.com', 'Y', 'textarea', ''),
('admin_link_name_to_site', '', '', 'DotKernel', 'N', 'textarea', ''),
('admin_link_to_site', '', '', 'http://localhost/DotKernel/', 'N', 'textarea', ''),
('admin_email', 'Admin Email.', 'The "From" field of an email sent by the script.', 'admin@dotkernel.com', 'Y', 'textarea', ''),
('site_name', '', 'Used in admin panel.', 'DotKernel', 'N', 'textarea', ''),
('company_link', '', '', 'http://localhost/DotKernel/', 'N', 'textarea', ''),
('license_url', '', '', 'http://www.dotkernel.com/', 'N', 'textarea', ''),
('license_name', '', '', 'DotKernel', 'N', 'textarea', ''),
('server_time', '', '', '0', 'N', 'textarea', ''),
('time_format_short', '', '', '%d %b %Y', 'N', 'textarea', ''),
('time_format_long', 'Long Time Format.', 'General time format.', '%b %d, %Y, %H:%M', 'Y', 'option', '%d %b %Y, %H:%M;%d %B %Y, %H:%M;%d %B %y, %H:%M;%d %m %Y, %H:%M;%d %m %y, %H:%M;%B %d, %Y, %H:%M;%b %d, %Y, %H:%M'),
('security_image_code_length', 'Security image code lenght', 'The number o characters in the security code.', '4', 'Y', 'option', '3;4;5;6;7;8;9;10'),
('smtp_addresses', '', '', 'aol.com;aim.com;comcast.net;hotmail.com;earthlink.net;juno.com;juno.net;bellsouth.net;cox.net;roadrunner.com;sbcglobal.net', 'N', 'textarea', ''),
('smtp_username', '', '', '********@gmail.com', 'N', 'textarea', ''),
('smtp_password', '', '', 'p4ssword', 'N', 'textarea', ''),
('smtp_email', '', '', '********@gmail.com', 'N', 'textarea', ''),
('smtp_server', '', '', 'smtp.gmail.com', 'N', 'textarea', ''),
('dev_emails', '', '', 'team@dotkernel.com', 'N', 'textarea', ''),
('meta_description', '', '', 'Enterprise Level PHP Solutions, based on Zend Framework and DotKernel , build by DotBoost Technologies Inc.', 'N', 'textarea', ''),
('results_per_page', '', 'How many records will be on every page', '10', 'Y', 'option', '10;20;30;40;50');
('recaptcha_public_key', 'Recaptcha Public Key.', 'Use this in the JavaScript code that is served to your users. http://recaptcha.net/', '123XXX', 'Y', 'textarea', ''), 
('recaptcha_private_key', 'Recaptcha Private Key.', 'Use this when communicating between your server and our server. Because this key is a global key, it is OK if the private key is distributed to multiple users. http://recaptcha.net/', '123XXX', 'Y', 'textarea', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `active` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


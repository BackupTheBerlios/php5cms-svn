# phpMyAdmin SQL Dump
# version 2.5.6
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Apr 16, 2004 at 03:05 PM
# Server version: 3.23.57
# PHP Version: 4.3.3
#
# Database : `junk`
#
DROP DATABASE IF EXISTS `junk`;
CREATE DATABASE `junk`;
USE junk;

# --------------------------------------------------------

#
# Table structure for table `user_details`
#

DROP TABLE IF EXISTS `user_details`;
CREATE TABLE `user_details` (
  `UserID` bigint(20) NOT NULL default '0',
  `FirstName` varchar(100) NOT NULL default '',
  `LastName` varchar(100) NOT NULL default '',
  KEY `UserID` (`UserID`)
) TYPE=BerkeleyDB;

# --------------------------------------------------------

#
# Table structure for table `users`
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserID` bigint(20) NOT NULL auto_increment,
  `UserName` varchar(100) NOT NULL default '',
  `UserPassword` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`UserID`),
  KEY `UserName` (`UserName`)
) TYPE=BerkeleyDB AUTO_INCREMENT=1 ;

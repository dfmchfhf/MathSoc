-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2014 at 10:06 PM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mathsoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `total` int(11) NOT NULL DEFAULT 1,
  `stock` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `checkouts`
--

CREATE TABLE IF NOT EXISTS `checkouts` (
  `checkout_id` int(11) NOT NULL AUTO_INCREMENT,
  `uwID` varchar(8) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `checkout` datetime NOT NULL,
  `checkin` datetime DEFAULT NULL,
  PRIMARY KEY (`checkout_id`),
  KEY `uwID` (`uwID`),
  KEY `asset_id` (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `uwID` varchar(8) NOT NULL,
  `name` varchar(127) NOT NULL,
  PRIMARY KEY (`uwID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `candy`
--
CREATE TABLE IF NOT EXISTS `candy` (
  `candy_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `cost` Decimal(11) NOT NULL DEFAULT 0,
  `total_time` bigint(32) NOT NULL DEFAULT 0,
  `times_out` int(11) NOT NULL DEFAULT 0,
  `current_run` bigint(32) NOT NULL DEFAULT 0,
  KEY `name` (`name`),
  PRIMARY KEY (`candy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD CONSTRAINT `checkouts_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`asset_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `checkouts_ibfk_1` FOREIGN KEY (`uwID`) REFERENCES `customers` (`uwID`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

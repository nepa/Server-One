-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. Februar 2012 um 19:53
-- Server Version: 5.0.51
-- PHP-Version: 5.2.6-1+lenny13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `netpan_s1`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `metadata`
--

CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(11) NOT NULL auto_increment,
  `latitude` decimal(8,5) NOT NULL,
  `longitude` decimal(7,5) NOT NULL,
  `title` varchar(255) default NULL,
  `timestamp` varchar(80) NOT NULL,
  `description` text,
  `sampleID` varchar(80) NOT NULL,
  `fileType` varchar(5) NOT NULL,
  `reportedAt` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reportedBy` char(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `noiseLevels`
--

CREATE TABLE IF NOT EXISTS `noiseLevels` (
  `id` int(11) NOT NULL auto_increment,
  `longitude` decimal(8,5) NOT NULL,
  `latitude` decimal(7,5) NOT NULL,
  `timestamp` varchar(80) NOT NULL,
  `zipCode` varchar(10) default NULL,
  `noiseLevel` int(3) NOT NULL,
  `reportedAt` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reportedBy` char(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `zipCode` (`zipCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

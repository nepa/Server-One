-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 20. Januar 2012 um 21:46
-- Server Version: 5.1.37
-- PHP-Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `s1`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `metadata`
--

CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `latitude` decimal(8,5) NOT NULL,
  `longitude` decimal(7,5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `timestamp` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `sampleID` varchar(80) NOT NULL,
  `fileType` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `noiseLevels`
--

CREATE TABLE IF NOT EXISTS `noiseLevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `longitude` decimal(8,5) NOT NULL,
  `latitude` decimal(7,5) NOT NULL,
  `zipCode` varchar(10) DEFAULT NULL,
  `noiseLevel` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zipCode` (`zipCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

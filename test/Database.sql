-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 15. Januar 2012 um 04:04
-- Server Version: 5.1.37
-- PHP-Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

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
  `description` text NOT NULL,
  `fileName` varchar(80) NOT NULL,
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

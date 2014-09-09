-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 09. September 2014 um 14:05
-- Server Version: 5.1.47
-- PHP-Version: 5.3.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `chat`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `dialog_hash_fk` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `offlinemessage`
--

CREATE TABLE IF NOT EXISTS `offlinemessage` (
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_hash_fk` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `operator`
--

CREATE TABLE IF NOT EXISTS `operator` (
  `operator_hash_pk` varchar(32) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0=off; 1=on',
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`operator_hash_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_hash_pk` varchar(32) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `visit_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_hash_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_operator_commitment`
--

CREATE TABLE IF NOT EXISTS `user_operator_commitment` (
  `dialog_hash_pk` varchar(32) NOT NULL,
  `operator_hash_fk` varchar(32) NOT NULL DEFAULT 'waiting for operator',
  `user_hash_fk` varchar(32) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1 = user chat request; 2= accepted by op; 3= finished by user or operator',
  PRIMARY KEY (`dialog_hash_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

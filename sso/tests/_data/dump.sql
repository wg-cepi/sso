/* Replace this file with actual dump of your database */

-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `tokens`;
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(1024) COLLATE utf8_bin NOT NULL,
  `used` tinyint(1) NOT NULL,
  `expires` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text COLLATE utf8_bin NOT NULL,
  `password` varchar(512) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `cookie` varchar(256) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `user_login_facebook`;
CREATE TABLE `user_login_facebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_id` varchar(64) COLLATE utf8_bin NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `user_login_google`;
CREATE TABLE `user_login_google` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `google_id` varchar(64) COLLATE utf8_bin NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `cookie`) VALUES
  (1,	'joe@example.com',	'$1$k84.dI3.$9/qnahwUbk3047whNEojD/',	'Joe',	'Satriani',	'26ccbe440ff6f04144d0ef78dfa252fa:c67e09386ae1834b9dffcc157bcadfeb'),
  (2,	'bob@example.com',	'$1$Js2.eI3.$HFmO/0rNJp9Yts/HU99YQ1',	'Bob',	'Jackson',	'296292edf0553ca52e51f2fb284cf731:d0e7c070617b13fcdfe8340479d72437'),
  (3,	'testsso@wgz.cz',	'',	'',	'',	'e659afb8dff710c3ba7cfe665453cc21:baa52ff7190baa9f574ff802778e0ddf');

INSERT INTO `domains` (`id`, `name`, `user_id`) VALUES
  (1,	'domain1.local',	NULL),
  (2,	'domain2.local',	NULL),
  (3,	'sub1.domain1.local',	NULL);

INSERT INTO `user_login_facebook` (`id`, `user_id`, `facebook_id`, `created`) VALUES
  (1,	3,	'107712179619654',	1456008427);

INSERT INTO `user_login_google` (`id`, `user_id`, `google_id`, `created`) VALUES
  (1,	3,	'106440411057598425368',	1456008298);